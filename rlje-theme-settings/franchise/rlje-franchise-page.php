<?php

require_once 'rlje-season-page.php';
require_once 'rlje-episode-page.php';
require_once 'rlje-trailer-page.php';

class RLJE_Franchise_Page {

	protected $franchise;
	protected $franchise_id;
	protected $season_id;
	protected $episode_id;
	protected $session_cookie;
	protected $nonce = 'rlje-franchise-token-nonce';

	public function __construct() {
		add_action( 'wp', array( $this, 'get_pagename' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'template_redirect', array( $this, 'franchise_template_redirect' ), 20 );
		add_action( 'wp_ajax_add', array( $this, 'add_to_watchlist' ) );
		add_action( 'wp_ajax_nopriv_add', array( $this, 'add_to_watchlist' ) );
		add_action( 'wp_ajax_remove', array( $this, 'remove_from_watchlist' ) );
		add_action( 'wp_ajax_nopriv_remove', array( $this, 'remove_from_watchlist' ) );

		add_filter( 'body_class', array( $this, 'franchise_body_class' ) );
		add_filter( 'rlje_json_ld_header', array( $this, 'add_franchise_json_ld_to_header' ) );
	}

	public function get_pagename() {
		global $wp_query;

		$pagename = $wp_query->query['pagename'];
		// var_dump($pagename,explode( '/', $pagename ));
		list( $this->franchise_id, $this->season_id, $this->episode_id ) = array_pad( explode( '/', $pagename ), 3, '' );
		// var_dump($this->franchise_id, $this->season_id, $this->episode_id);
		$this->franchise = ( ! empty( $this->franchise_id ) ) ? rljeApiWP_getFranchiseById( $this->franchise_id ) : false;
	}

	public function enqueue_scripts() {
		if ( ! $this->is_current_franchise() ) {
			return;
		}

		if ( is_ssl() ) {
			$bc_admin_js = 'https://sadmin.brightcove.com/';
		} else {
			$bc_admin_js = 'http://admin.brightcove.com/';
		}

		// Register script for later usages on Episode, and Trailer pages
		wp_register_script( 'brightcove', $bc_admin_js . 'js/BrightcoveExperiences.js', array(), false, true );

		$css_ver = date( 'ymd-Gis', filemtime( plugin_dir_path( __FILE__ ) . 'css/franchise.css' ) );
		$js_ver  = date( 'ymd-Gis', filemtime( plugin_dir_path( __FILE__ ) . 'js/franchise.js' ) );

		wp_enqueue_style( 'rlje-franchise', plugins_url( 'css/franchise.css', __FILE__ ), array( 'main_style_css' ), $css_ver );
		wp_enqueue_script( 'rlje-franchise', plugins_url( 'js/franchise.js', __FILE__ ), array( 'main-js' ), $js_ver, true );
		$franchise_object = [
			'ajax_url'     => admin_url( 'admin-ajax.php' ),
			'franchise_id' => $this->franchise_id,
			'nonce'        => wp_create_nonce( $this->nonce ),
		];
		wp_localize_script( 'rlje-franchise', 'franchise_object', $franchise_object );
	}

	public function franchise_template_redirect() {
		if ( $this->is_franchise() ) {
			global $wp_query;

			// Prevent internal 404 on custome search page because of template_redirect hook.
			$wp_query->is_404  = false;
			$wp_query->is_page = true;
			status_header( 200 );
			// set_query_var( 'franchise_id', $this->franchise_id );
			$franchise_id = $this->franchise_id;
			$franchise    = rljeApiWP_getFranchiseById( $franchise_id );

			$stream_positions = $this->get_stream_positions( $franchise_id );
			$total_episodes   = $this->get_total_episodes( $franchise );

			$template_path = apply_filters( 'rlje_franchise_page_template_path', plugin_dir_path( __FILE__ ) . 'templates/franchise.php' );
			ob_start();
			require_once $template_path;
			$html = ob_get_clean();
			echo $html;

			exit();
		}
	}

	public function add_to_watchlist() {
		if ( ! wp_verify_nonce( $_POST['nonce'], $this->nonce ) ) {
			die( 'Action Not Allow!' );
		}

		if ( ! isset( $_COOKIE['ATVSessionCookie'] ) || ! rljeApiWP_isUserActive( $_COOKIE['ATVSessionCookie'] ) ) {
			die( 'Action Not Allow!' );
		}

		$franchise_id = ( ! empty( $_POST['franchise_id'] ) ) ? $_POST['franchise_id'] : '';
		if ( empty( $franchise_id ) ) {
			wp_send_json_error( array( 'message' => 'Please provide franchise ID.' ) );
		}

		$data = [
			'message' => rljeApiWP_addToWatchlist( $franchise_id, $_COOKIE['ATVSessionCookie'] ),
		];
		wp_send_json_success( $data );
	}

	public function remove_from_watchlist() {
		if ( ! wp_verify_nonce( $_POST['nonce'], $this->nonce ) ) {
			die( 'Action Not Allow!' );
		}

		if ( ! isset( $_COOKIE['ATVSessionCookie'] ) || ! rljeApiWP_isUserActive( $_COOKIE['ATVSessionCookie'] ) ) {
			die( 'Action Not Allow!' );
		}

		$franchise_id = ( ! empty( $_POST['franchise_id'] ) ) ? $_POST['franchise_id'] : '';
		if ( empty( $franchise_id ) ) {
			wp_send_json_error( array( 'message' => 'Please provide franchise ID.' ) );
		}

		$data = [
			'message' => rljeApiWP_removeFromWatchlist( $franchise_id, $_COOKIE['ATVSessionCookie'] ),
		];
		wp_send_json_success( $data );
	}

	public function franchise_body_class( $classes ) {
		if ( $this->is_current_franchise() ) {
			$classes[] = 'franchise-page';
			$classes[] = $this->franchise_id;
			$classes[] = 'page-' . $this->franchise_id;
		}

		return $classes;
	}

	public function add_franchise_json_ld_to_header( $json_ld ) {
		if ( $this->is_current_franchise() ) {
			if ( ! $this->franchise ) {
				return $json_ld;
			}
			$season         = ( ! empty( $this->franchise->seasons[0] ) ) ? $this->franchise->seasons[0] : [];
			$episode        = ( ! empty( $season->episodes[0] ) ) ? $season->episodes[0] : [];
			$franchise_type = ( ! empty( $episode->type ) ) ? ucfirst( strtolower( $episode->type ) ) : 'TVSeries';
			$type           = ( 'Movie' === $franchise_type ) ? $franchise_type : 'TVSeries';
			$url            = trailingslashit( home_url( $this->franchise_id ) );
			$image          = ( ! empty( $episode->image ) ) ? rljeApiWP_getImageUrlFromServices( $episode->image ) : '';
			$name           = ( ! empty( $this->franchise->name ) ) ? $this->franchise->name : '';
			$actors         = ( ! empty( $this->franchise->actors ) ) ? $this->franchise->actors : [];
			$actor          = [];
			foreach ( $actors as $actor_person ) {
				$actor[] = [
					'@type' => 'Person',
					'name'  => $actor_person,
				];
			}
			$directors = ( ! empty( $this->franchise->director ) ) ? $this->franchise->director : [];
			$director  = [];
			foreach ( $directors as $director_person ) {
				$director[] = [
					'@type' => 'Person',
					'name'  => $director_person,
				];
			}
			$date_created        = date( 'Y-m-d', ( $this->franchise->createdDate / 1000 ) );
			$description         = $this->franchise->longDescription;
			$trailer_info        = ( ! empty( $this->franchise->episodes[0] ) ) ? $this->franchise->episodes[0] : [];
			$trailer_name        = ( ! empty( $trailer_info->name ) ) ? $trailer_info->name : '';
			$trailer_description = ( ! empty( $trailer_info->shortDescription ) ) ? $trailer_info->shortDescription : $trailer_info->longDescription;
			$trailer_image       = rljeApiWP_getImageUrlFromServices( $trailer_info->image );
			$trailer_upload_date = ( ! empty( $trailer_info->startDate ) ) ? date( 'Y-m-d', ( $trailer_info->startDate / 1000 ) ) : strval( $trailer_info->year );
			$trailer             = [
				'@type'        => 'VideoObject',
				'name'         => $trailer_name,
				'description'  => $trailer_description,
				'thumbnail'    => [
					'@type'      => 'ImageObject',
					'contentUrl' => $trailer_image,
				],
				'thumbnailUrl' => $trailer_image,
				'uploadDate'   => $trailer_upload_date,
			];
			$json_ld             = [
				'@context'    => 'http://schema.org',
				'@type'       => $type,
				'url'         => $url,
				'name'        => $name,
				'image'       => $image,
				'dateCreated' => $date_created,
				// 'genre' => [
				// 'Action',
				// 'Adventure',
				// 'Fantasy',
				// 'Sci-Fi'
				// ],
				'actor'       => $actor,
				'director'    => $director,
				'description' => $description,
				// 'datePublished' => $season['episode']->year,
				// 'keywords' => 'atlantis,dc extended universe,dc cinematic universe,based on comic,dc comics,superhero,one word title,based on comic book,character name in title,aquaman character,army,super villain,supernatural power,ocean,underwater',
				// 'trailer' => $trailer
			];

			if ( 'Movie' === $type ) {
				if ( ! empty( $episode->length ) ) {
					$json_ld['duration'] = $this->iso8601_duration( $episode->length );
				}
			}

			if ( ! empty( $trailer_name ) ) {
				$json_ld['trailer'] = $trailer;
			}
		}

		return $json_ld;
	}

	public function get_stream_positions( $franchise_id ) {
		$stream_positions = [];
		if ( ! $this->is_user_active() ) {
			return $stream_positions;
		}

		$get_stream_positions = rljeApiWP_getStreamPositionsByFranchise( $franchise_id, $_COOKIE['ATVSessionCookie'] );
		if ( isset( $get_stream_positions->streamPositions ) ) {
			$count_positions = 1;
			foreach ( $get_stream_positions->streamPositions as $stream_position ) {
				$stream_positions[ $stream_position->EpisodeID ] = [
					'Position'      => $stream_position->Position,
					'EpisodeLength' => $stream_position->EpisodeLength,
					'Counter'       => $count_positions,
				];
				$count_positions++;
			}
		}

		return $stream_positions;
	}

	public function get_total_episodes( $franchise ) {
		$total_episodes = 0;
		foreach ( $franchise->seasons as $season_item ) {
			$total_episodes += count( $season_item->episodes );
		}

		return $total_episodes;
	}

	protected function get_available_franchise_list() {
		$country    = ( ! empty( rljeApiWP_getCountryCode() ) ) ? rljeApiWP_getCountryCode() : 'US';
		$futuredate = ( ! empty( rljeApiWP_getFutureDate() ) ) ? rljeApiWP_getFutureDate() : 'today';

		$transient_key = 'rlje_franchise_list_' . strtolower( $country ) . '_on_' . strtolower( $futuredate );
		$current_country_available_franchises = get_transient( $transient_key );
		if ( false === $current_country_available_franchises ) {
			$response = wp_remote_get( esc_url_raw( CONTENT_BASE_URL . '/' . $futuredate . '/web/franchiselist?country=' . $country ) );

			if ( is_wp_error( $response ) ) {
				return array();
			}
			$body                                 = wp_remote_retrieve_body( $response );
			$current_country_available_franchises = json_decode( $body, true );
			// var_dump($current_country_available_franchises);
			set_transient( $transient_key, $current_country_available_franchises, 15 * MINUTE_IN_SECONDS );
		}

		$franchises = array();
		if ( empty( $current_country_available_franchises[ $country ] ) ) {
			return array();
		}

		$available_franchises = $current_country_available_franchises[ $country ];
		foreach ( $available_franchises as $franchise_id => $franchise_info ) {
			$franchise_name                          = $franchise_info['name'];
			$franchises[ $country ][ $franchise_id ] = $franchise_name;
		}

		$available_franchise_list = ( ! empty( $franchises[ $country ] ) ) ? $franchises[ $country ] : array();

		return ( ! empty( $available_franchise_list ) ) ? $available_franchise_list : array();
	}

	protected function is_franchise() {
		if ( is_page() ) {
			return false;
		}

		if ( ! is_404() ) {
			return false;
		}

		if ( ! $this->is_current_franchise() ) {
			return false;
		}

		if ( ! empty( $this->season_id ) || ! empty( $this->episode_id ) ) {
			return false;
		}

		return true;
	}

	// Returns true if we are dealing with a franchise page and not an episode or season
	protected function is_current_franchise() {
		if ( empty( $this->franchise_id ) ) {
			return false;
		}

		$available_franchise_list = $this->get_available_franchise_list();
		if ( empty( $available_franchise_list ) ) {
			// We should return 404 for not supported country
			return false;
		}

		if ( empty( $available_franchise_list[ $this->franchise_id ] ) ) {
			// We should return not available for your country template
			return false;
		}

		if ( ! empty( $this->season_id ) || ! empty( $this->episode_id ) ) {
			return false;
		}

		return true;
	}

	protected function is_user_active() {
		if ( isset( $_COOKIE['ATVSessionCookie'] ) && rljeApiWP_isUserActive( $_COOKIE['ATVSessionCookie'] ) ) {
			$this->session_cookie = $_COOKIE['ATVSessionCookie'];
			return true;
		}

		return false;
	}

	protected function is_trailer_available( $franchise ) {
		if ( isset( $franchise->episodes[0], $franchise->episodes[0]->id ) && ( ! empty( $franchise->episodes[0]->id ) ) ) {
			return true;
		}

		return false;
	}

	protected function iso8601_duration( $seconds ) {
		$intervals = [
			'D' => 60 * 60 * 24,
			'H' => 60 * 60,
			'M' => 60,
			'S' => 1,
		];

		$pt     = 'P';
		$result = '';
		foreach ( $intervals as $tag => $divisor ) {
			$qty = floor( $seconds / $divisor );
			if ( ! $qty && '' === $result ) {
				$pt = 'T';
				continue;
			}

			$seconds -= $qty * $divisor;
			$result  .= "$qty$tag";
		}
		if ( '' === $result ) {
			$result = '0S';
		}

		return "$pt$result";
	}

}

$rlje_franchise_page = new RLJE_Franchise_Page();

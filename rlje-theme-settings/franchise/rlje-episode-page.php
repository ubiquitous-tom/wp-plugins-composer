<?php

class RLJE_Episode_Page extends RLJE_Season_Page {

	protected $episodes;
	protected $brightcove;
	protected $nonce = 'atv#episodePlayer@token_nonce';

	public function __construct() {
		add_action( 'wp', array( $this, 'get_pagename' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_debugger_scripts' ) );
		add_action( 'wp_ajax_is_user_active', array( $this, 'ajax_is_user_active' ) );
		add_action( 'wp_ajax_nopriv_is_user_active', array( $this, 'ajax_is_user_active' ) );
		add_action( 'wp_ajax_streamposition', array( $this, 'ajax_set_stream_position' ) );
		add_action( 'wp_ajax_nopriv_streamposition', array( $this, 'ajax_set_stream_position' ) );
		add_action( 'template_redirect', array( $this, 'episode_template_redirect' ) );

		add_filter( 'body_class', array( $this, 'episode_body_class' ) );
		add_filter( 'rlje_json_ld_header', array( $this,'add_episode_json_ld_to_header' ),15 );
	}

	public function get_pagename() {
		global $wp_query;

		$pagename = $wp_query->query['pagename'];
		// var_dump($pagename,explode( '/', $pagename ));
		list( $this->franchise_id, $this->season_id, $this->episode_id ) = array_pad( explode( '/', $pagename ), 3, '' );
		// var_dump($this->franchise_id, $this->season_id, $this->episode_id);
		$this->get_current_franchise_season_episodes();
	}

	public function enqueue_scripts() {
		if ( ! $this->is_episode() ) {
			return;
		}

		$brightcove = $this->get_brightcove_player_settings();
		$bc_account_id = $brightcove['bc_account_id'];
		$bc_player_id = $brightcove['bc_player_id'];

		$bc_url = '//players.brightcove.net/' . $bc_account_id . '/' . $bc_player_id . '_default/index.js';
		$css_ver = date( 'ymd-Gis', filemtime( plugin_dir_path( __FILE__ ) . 'css/episode.css' ) );
		$js_ver = date( 'ymd-Gis', filemtime( plugin_dir_path( __FILE__ ) . 'js/episode.js' ) );

		wp_enqueue_style( 'rlje-episode', plugins_url( 'css/episode.css', __FILE__ ), array( 'main_style_css' ), $css_ver );
		wp_enqueue_script( 'rlje-brightcove', $bc_url, array( 'jquery', 'main-js' ), false, true );
		wp_enqueue_script( 'rlje-episode', plugins_url( 'js/episode.js', __FILE__ ), array( 'rlje-brightcove' ), $js_ver, true );

		wp_localize_script( 'rlje-episode', 'episode_object', array(
			// 'ajax_url' => home_url( 'ajax_atv' ),
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'token' => wp_create_nonce( $this->nonce ),
			'episode_id' => $this->episode_id,
		) );
	}

	public function enqueue_debugger_scripts() {
		if ( ! $this->is_episode() ) {
			return;
		}

		$is_video_debugger_on = rljeApiWP_isVideoDebuggerOn();
		if ( ! $is_video_debugger_on ) {
			return;
		}

		wp_enqueue_style( 'brightcove-debugger', '//solutions.brightcove.com/marguin/debugger/css/brightcove-player-debugger.css', array(), false );
		wp_enqueue_script( 'brightcove-debugger', '//solutions.brightcove.com/marguin/debugger/js/brightcove-player-debugger.min.js', array( 'brightcove' ), false, true );

		// var options = {"debugAds":false, "logClasses":true, "showProgress":true, "useLineNums":true, "verbose":true};
		$debugger_options = array(
			'debugAds' => false,
			'logClasses' => true,
			'showProgress' => true,
			'useLineNums' => true,
			'verbose' => true,
		);

		$bc_debugger_js_ver = date( 'ymd-Gis', filemtime( plugin_dir_path( __FILE__ ) . 'js/brightcove-debugger.js' ) );
		wp_enqueue_script( 'rlje-brightcove-debugger', plugins_url( 'js/brightcove-debugger.js', __FILE__ ), array( 'brightcove-debugger' ), $bc_debugger_js_ver, true );
		wp_localize_script( 'rlje-brightcove-debugger', 'brightcove_debugger_object', array(
			'options' => $debugger_options,
		));
	}

	public function ajax_is_user_active() {
		if ( ! wp_verify_nonce( $_POST['token'], $this->nonce ) ) {
			die( 'Action Not Allow!' );
		}

		$content = ( ! empty( $_POST['content'] ) ) ? $_POST['content'] : null;
		$page = ( ! empty( $_POST['page'] ) ) ? $_POST['page'] : null;

		$data = array(
			'isActive' => rljeApiWP_isUserActive( $_COOKIE['ATVSessionCookie'] ),
		);
		wp_send_json_success( $data );
	}

	public function ajax_set_stream_position() {
		if ( ! wp_verify_nonce( $_POST['token'], $this->nonce ) ) {
			die( 'Action Not Allow!' );
		}

		if ( ! rljeApiWP_isUserActive( $_COOKIE['ATVSessionCookie'] ) ) {
			die( wp_get_server_protocol() . ' 401 Unauthorized' );
		}

		$episode_id = ( ! empty( $_POST['EpisodeID'] ) ) ? $_POST['EpisodeID'] : null;
		$session_id = ( ! empty( $_COOKIE['ATVSessionCookie'] ) ) ? $_COOKIE['ATVSessionCookie'] : null;
		$position = ( is_numeric( $_POST['Position'] ) ) ? intval( $_POST['Position'] ) : null;
		$last_known_action = ( ! empty( $_POST['LastKnownAction'] ) ) ? $_POST['LastKnownAction'] : null;

		if ( empty( $episode_id ) ) {
			// die( wp_get_server_protocol() . ' 401 Unauthorized' );
			wp_send_json_error( [ 'message' => wp_get_server_protocol() . ' 401 Unauthorized' ] );
		}

		if ( empty( $session_id ) ) {
			// die( wp_get_server_protocol() . ' 401 Unauthorized' );
			wp_send_json_error( [ 'message' => wp_get_server_protocol() . ' 401 Unauthorized' ] );
		}

		// 0 (zero) is an acceptable stream position
		if ( ! is_int( $position ) ) {
			// die( wp_get_server_protocol() . ' 401 Unauthorized' );
			wp_send_json_error( [ 'message' => wp_get_server_protocol() . ' 401 Unauthorized' ] );
		}

		if ( empty( $last_known_action ) ) {
			// die( wp_get_server_protocol() . ' 401 Unauthorized' );
			wp_send_json_error( [ 'message' => wp_get_server_protocol() . ' 401 Unauthorized' ] );
		}

		$response = rljeApiWP_addStreamPosition( $episode_id, $session_id, $position, $last_known_action );
		if ( is_null( $response ) ) {
			wp_send_json_error();
		} else {
			wp_send_json_success( $response );
		}
	}

	public function episode_template_redirect() {
		if ( ! $this->is_episode() ) {
			return false;
		}

		$brightcove = $this->get_brightcove_player_settings();
		$bc_account_id = $brightcove['bc_account_id'];
		$bc_player_id = $brightcove['bc_player_id'];

		global $wp_query;

		// Prevent internal 404 on custome search page because of template_redirect hook.
		$wp_query->is_404  = false;
		$wp_query->is_page = true;
		status_header( 200 );
		// set_query_var( 'franchise_id', $this->franchise_id );
		// set_query_var( 'season_name', $this->season_id );
		// set_query_var( 'episode_name', $this->episode_id );
		$franchise_id     = $this->franchise_id;
		$season_name_url  = $this->season_id;
		$episode_name_url = $this->episode_id;

		ob_start();
		require_once plugin_dir_path( __FILE__ ) . 'templates/episode.php';
		$html = ob_get_clean();
		echo $html;

		exit();
	}

	public function episode_body_class( $classes ) {
		if ( $this->is_episode() ) {
			$classes[] = 'episode';
			$classes[] = 'episode-page';
			$classes[] = $this->episode_id;
			$classes[] = 'page-' . $this->episode_id;
		}

		return $classes;
	}

	public function add_episode_json_ld_to_header( $json_ld ) {
		if ( $this->is_episode() ) {
			$url = trailingslashit( home_url( join( '/', [ $this->franchise_id, $this->season_id, $this->episode_id ] ) ) );
			$image = ( ! empty( $this->episodes->image ) ) ? rljeApiWP_getImageUrlFromServices( $this->episodes->image ) : '';
			$description = ( ! empty( $this->episodes->shortDescription ) ) ? $this->episodes->shortDescription : $this->episodes->longDescription;
			$date_created = date( 'Y-m-d' , ( $this->episodes->startDate / 1000 ) );
			$duration = $this->iso8601_duration( $this->episodes->length );
			$json_ld['@type'] = 'TVEpisode';
			$json_ld['url'] = $url;
			$json_ld['name'] = $this->episodes->name;
			$json_ld['image'] = $image;
			$json_ld['description'] = $description;
			$json_ld['dateCreated'] = $date_created;
			$json_ld['timeRequired'] = $duration;

			if ( ! empty( $this->episodes->actors ) ) {
				$actors = $this->episodes->actors;
				$actor = [];
				foreach ( $actors as $actor_person ) {
					$actor[] = [
						'@type' => 'Person',
						'name' => $actor_person,
					];
				}
				$json_ld['actor'] = $actor;
			}

			if ( ! empty( $this->episodes->director ) ) {
				$directors = $this->episodes->director;
				$director = [];
				foreach ( $directors as $director_person ) {
					$director[] = [
						'@type' => 'Person',
						'name' => $director_person,
					];
				}
				$json_ld['director'] = $director;
			}
		}

		return $json_ld;
	}

	protected function is_episode() {
		if ( empty( $this->episode_id ) ) {
			return false;
		}

		if ( empty( $this->episodes ) ) {
			return false;
		}

		return true;
	}

	protected function get_current_franchise_season_episodes() {
		if ( empty( $this->episode_id ) ) {
			$this->episodes = false;
		} else {
			$this->episodes = rljeApiWP_getCurrentEpisode( $this->franchise_id, $this->season_id, $this->episode_id );
		}
	}

	protected function get_brightcove_player_settings() {
		$brightcove = [];

		// UMC FREE ACCOUNT
		// $bc_account_id = '3392051363001';
		// $bc_player_id = '0066661d-8f08-4e7b-a5b4-8d48755a3057';
		// UMC PAYWALL ACCOUNT
		// $bc_account_id = '3392051362001';
		// $bc_player_id = 'e148573c-29cd-4ede-a267-a3947918ea4a';

		if ( rljeApiWP_isUserActive( $_COOKIE['ATVSessionCookie'] ) ) {
			$this->brightcove = get_option( 'rlje_theme_brightcove_restricted_settings' );
			$brightcove['bc_account_id'] = $this->brightcove['restricted_account_id'];
			$brightcove['bc_player_id'] = $this->brightcove['restricted_player_id'];
		} else {
			$this->brightcove = get_option( 'rlje_theme_brightcove_shared_settings' );
			$brightcove['bc_account_id'] = $this->brightcove['shared_account_id'];
			$brightcove['bc_player_id'] = $this->brightcove['shared_player_id'];
		}

		if ( empty( $this->brightcove ) ) {
			$brightcove['bc_account_id'] = '';
			$brightcove['bc_player_id'] = '';

			return false;
		}

		return $brightcove;
	}
}

$rlje_episode_page = new RLJE_Episode_Page();

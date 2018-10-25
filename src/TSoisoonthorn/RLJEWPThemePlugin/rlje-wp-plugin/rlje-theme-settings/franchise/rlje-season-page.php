<?php

class RLJE_Season_Page extends RLJE_Franchise_Page {

	protected $seasons;

	public function __construct() {
		add_action( 'wp', array( $this, 'get_pagename' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'template_redirect', array( $this, 'season_template_redirect' ), 15 );

		add_filter( 'body_class', array( $this, 'season_body_class' ) );
	}

	public function get_pagename() {
		global $wp_query;

		$pagename = $wp_query->query['pagename'];
		// var_dump($pagename,explode( '/', $pagename ));
		list( $this->franchise_id, $this->season_id, $this->episode_id ) = array_pad( explode( '/', $pagename ), 3 , '' );
		// var_dump($this->franchise_id, $this->season_id, $this->episode_id);
		$this->get_current_franchise_season();
	}

	public function enqueue_scripts() {
		if ( ! $this->is_season() ) {
			return;
		}

		$css_ver = date( 'ymd-Gis', filemtime( plugin_dir_path( __FILE__ ) . 'css/season.css' ) );
		wp_enqueue_style( 'rlje-season', plugins_url( 'css/season.css', __FILE__ ), array( 'main_style_css' ), $css_ver );
	}

	public function season_template_redirect() {
		if ( ! $this->is_season() ) {
			return false;
		}

		global $wp_query;

		// Prevent internal 404 on custome search page because of template_redirect hook.
		$wp_query->is_404  = false;
		$wp_query->is_page = true;
		status_header( 200 );
		// set_query_var( 'franchise_id', $this->franchise_id );
		// set_query_var( 'season_name', $this->season_id );
		$franchise_id    = $this->franchise_id;
		$season_name_url = $this->season_id;

		ob_start();
		require_once plugin_dir_path( __FILE__ ) . 'templates/season.php';
		$html = ob_get_clean();
		echo $html;

		exit();
	}

	public function season_body_class( $classes ) {
		if ( $this->is_season() ) {
			$classes[] = 'season';
			$classes[] = 'season-page';
			$classes[] = $this->season_id;
			$classes[] = 'page-' . $this->season_id;
		}

		return $classes;
	}

	protected function is_season() {
		if ( empty( $this->season_id ) ) {
			return false;
		}

		if ( empty( $this->seasons ) ) {
			return false;
		}

		if ( ! empty( $this->episode ) ) {
			return false;
		}

		return true;
	}

	protected function get_current_franchise_season() {
		if ( empty( $this->season_id ) ) {
			$this->seasons = false;
		} else {
			$this->seasons = rljeApiWP_getCurrentSeason( $this->franchise_id, $this->season_id );
		}
	}
}

$rlje_season_page = new RLJE_Season_Page();

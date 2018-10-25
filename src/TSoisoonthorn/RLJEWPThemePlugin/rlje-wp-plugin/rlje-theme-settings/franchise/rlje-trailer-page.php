<?php

class RLJE_Trailer_Page extends RLJE_Franchise_Page {

	protected $brightcove;

	public function __construct() {
		add_action( 'init', array( $this, 'trailer_rewrite_rules' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'template_redirect', array( $this, 'trailer_template_redirect' ), 15 );

		add_filter( 'body_class', array( $this, 'trailer_body_class' ) );
	}

	public function trailer_rewrite_rules() {
		add_rewrite_rule( '([^/]*)/trailer/?', 'index.php?pagename=trailer&franchise_id=$matches[1]', 'top' );
	}

	public function enqueue_scripts() {
		if ( ! $this->is_trailer() ) {
			return;
		}

		// We need all these brightcove stuff because this page uses share account only.
		$this->brightcove = get_option( 'rlje_theme_brightcove_shared_settings' );
		$bc_account_id = $this->brightcove['shared_account_id'];
		$bc_player_id = $this->brightcove['shared_player_id'];

		// if ( is_ssl() ) {
		// 	$bc_admin_js = 'https://sadmin.brightcove.com/';
		// } else {
		// 	$bc_admin_js = 'http://admin.brightcove.com/';
		// }

		// wp_enqueue_script( 'brightcove', $bc_admin_js . 'js/BrightcoveExperiences.js', array(), false, true );

		$bc_url = '//players.brightcove.net/' . $bc_account_id . '/' . $bc_player_id . '_default/index.js';
		$css_ver = date( 'ymd-Gis', filemtime( plugin_dir_path( __FILE__ ) . 'css/trailer.css' ) );
		$js_ver = date( 'ymd-Gis', filemtime( plugin_dir_path( __FILE__ ) . 'js/trailer.js' ) );

		wp_enqueue_style( 'rlje-trailer', plugins_url( 'css/trailer.css', __FILE__ ), array( 'main_style_css' ), $css_ver );
		wp_enqueue_script( 'rlje-brightcove', $bc_url, array( 'jquery', 'main-js' ), false, true );
		wp_enqueue_script( 'rlje-trailer', plugins_url( 'js/trailer.js', __FILE__ ), array( 'rlje-brightcove' ), $js_ver, true );

		// wp_localize_script( 'rlje-episode', 'atv_player_object', array(
		// 	'ajax_url' => home_url( 'ajax_atv' ),
		// 	'token' => wp_create_nonce( 'atv#episodePlayer@token_nonce' )
		// ));
	}

	public function trailer_template_redirect() {
		if ( ! $this->is_trailer() ) {
			return false;
		}

		global $wp_query;

		// Prevent internal 404 on custome search page because of template_redirect hook.
		$wp_query->is_404  = false;
		$wp_query->is_page = true;
		status_header( 200 );
		// set_query_var( 'franchise_id', $this->franchise_id );
		$franchise_id = $this->franchise_id;

		ob_start();
		require_once plugin_dir_path( __FILE__ ) . 'templates/trailer.php';
		$html = ob_get_clean();
		echo $html;

		exit();
	}

	public function trailer_body_class( $classes ) {
		if ( $this->is_trailer() ) {
			$classes[] = 'trailer';
			$classes[] = 'page-trailer';
			$classes[] = 'page-trailer-' . $this->franchise_id;
		}

		return $classes;
	}

	protected function is_trailer() {
		$pagename = get_query_var( 'pagename' );
		if ( 'trailer' === $pagename ) {
			$this->franchise_id = get_query_var( 'franchise_id' );

			return true;
		}

		return false;
	}
}

$rlje_trailer_page = new RLJE_Trailer_Page();

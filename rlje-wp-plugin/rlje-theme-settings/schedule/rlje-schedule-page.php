<?php

class RLJE_Schedule_Page {

	protected $nonce         = 'atv#contentPage@token_nonce';
	protected $list_sections = array(
		'featured'    => 'Recently Added',
		'comingsoon'  => 'Coming Soon',
		'leavingsoon' => 'Leaving Soon',
	);

	public function __construct() {
		add_action( 'init', array( $this, 'add_schedule_rewrite_rules' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_ajax_paginate', array( $this, 'ajax_carousel_pagination' ) );
		add_action( 'wp_ajax_nopriv_paginate', array( $this, 'ajax_carousel_pagination' ) );
		add_action( 'template_redirect', array( $this, 'schedule_template_redirect' ) );

		add_filter( 'body_class', array( $this, 'schedule_body_class' ) );
	}

	public function add_schedule_rewrite_rules() {
		add_rewrite_rule( 'schedule([^/]+)/?', 'index.php?pagename=schedule&section=featurerd', 'top' );
		add_rewrite_rule( 'schedule/([^/]+)/?', 'index.php?pagename=schedule&section=$matches[1]', 'top' );
	}

	public function enqueue_scripts() {
		$pagename = get_query_var( 'pagename' );
		if ( 'schedule' !== $pagename ) {
			return;
		}

		$this->brightcove = get_option( 'rlje_theme_brightcove_shared_settings' );
		if ( empty( $this->brightcove ) ) {
			return;
		}

		// UMC FREE ACCOUNT
		// $bc_account_id = '3392051363001';
		// $bc_player_id = '0066661d-8f08-4e7b-a5b4-8d48755a3057';
		// UMC PAYWALL ACCOUNT
		// $bc_account_id = '3392051362001';
		// $bc_player_id = 'e148573c-29cd-4ede-a267-a3947918ea4a';

		$bc_account_id = $this->brightcove['shared_account_id'];
		$bc_player_id  = $this->brightcove['shared_player_id'];
		$bc_url        = '//players.brightcove.net/' . $bc_account_id . '/' . $bc_player_id . '_default/index.js';
		$css_ver       = date( 'ymd-Gis', filemtime( plugin_dir_path( __FILE__ ) . 'css/schedule.css' ) );
		$js_ver        = date( 'ymd-Gis', filemtime( plugin_dir_path( __FILE__ ) . 'js/schedule.js' ) );

		// if ( is_ssl() ) {
		// 	$bc_admin_js = 'https://sadmin.brightcove.com/';
		// } else {
		// 	$bc_admin_js = 'http://admin.brightcove.com/';
		// }

		wp_enqueue_style( 'rlje-schedule', plugins_url( 'css/schedule.css', __FILE__ ), array( 'main_style_css' ), $css_ver );
		// wp_enqueue_script( 'brightcove', $bc_admin_js . 'js/BrightcoveExperiences.js', array(), false, true );
		wp_enqueue_script( 'rlje-brightcove', $bc_url, array( 'jquery', 'main-js' ), false, true );
		wp_enqueue_script( 'rlje-schedule', plugins_url( 'js/schedule.js', __FILE__ ), array( 'rlje-brightcove' ), $js_ver, true );
	}

	public function ajax_carousel_pagination() {
		if ( ! wp_verify_nonce( $_POST['token'], $this->nonce ) ) {
			die( 'Action Not Allow!' );
		}

		$content = ( ! empty( $_POST['content'] ) ) ? $_POST['content'] : null;
		$page    = ( ! empty( $_POST['page'] ) ) ? $_POST['page'] : null;

		$data = rljeApiWP_getContentPageItems( $content, $page );
		wp_send_json_success( $data );
	}

	public function schedule_template_redirect() {
		$pagename = get_query_var( 'pagename' );
		if ( 'schedule' !== $pagename ) {
			return;
		}

		// For backward rewrite rules compatibility.
		$active_section = get_query_var( 'section' );
		switch ( $active_section ) {
			case 'comingsoon':
			case 'leavingsoon':
			case 'featured':
				break;
			case '':
				// set_query_var( 'section', 'featured' );
				$active_section = 'featured';
				break;
			default:
				// Not schedule page
				return;
		}

		$this->brightcove = get_option( 'rlje_theme_brightcove_shared_settings' );
		if ( empty( $this->brightcove ) ) {
			return;
		}

		// UMC FREE ACCOUNT
		// $bc_account_id = '3392051363001';
		// $bc_player_id = '0066661d-8f08-4e7b-a5b4-8d48755a3057';
		// UMC PAYWALL ACCOUNT
		// $bc_account_id = '3392051362001';
		// $bc_player_id = 'e148573c-29cd-4ede-a267-a3947918ea4a';
		$bc_account_id = $this->brightcove['shared_account_id'];
		$bc_player_id  = $this->brightcove['shared_player_id'];

		global $wp_query;

		// Prevent internal 404 on custome search page because of template_redirect hook.
		$wp_query->is_404  = false;
		$wp_query->is_page = true;
		status_header( 200 );

		ob_start();
		require_once plugin_dir_path( __FILE__ ) . 'templates/schedule.php';
		$html = ob_get_clean();
		echo $html;
		exit();
	}

	public function schedule_body_class( $classes ) {
		$pagename = get_query_var( 'pagename' );
		if ( 'schedule' === $pagename ) {
			// $classes[] = $pagename;
			$classes[] = 'page-' . $pagename;

			$section = get_query_var( 'section' );
			if ( ! empty( $section ) ) {
				$classes[] = $pagename . '-' . $section;
				$classes[] = 'page-' . $section;
				// $classes[] = $section;
			}
		}

		return $classes;
	}
}

$rlje_schedule_page = new RLJE_Schedule_Page();

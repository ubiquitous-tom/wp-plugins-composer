<?php

class RLJE_Theme_Search_Settings {

	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'init', array( $this, 'add_search_rewrite_rules' ) );
		add_action( 'template_redirect', array( $this, 'search_template_redirect' ) );
	}

	public function enqueue_scripts() {
		wp_enqueue_style( 'rlje-search', plugins_url( 'search/css/style.css', __DIR__ ), array( 'main_style_css' ), '1.0.0' );
		wp_enqueue_script( 'rlje-search', plugins_url( 'search/js/script.js', __DIR__ ), array( 'jquery' ), '1.0.0', true );
	}

	public function add_search_rewrite_rules() {
		add_rewrite_rule( '^search/([^/]+)/?', 'index.php?pagename=search&s=$matches[1]', 'top' );
	}

	public function search_template_redirect() {
		// First, redirect from ?s=text to /search/text.
		if ( is_search() and false === strpos( $_SERVER['REQUEST_URI'], '/search/' ) ) {
			wp_safe_redirect( trailingslashit( site_url( '/search/' ) . rawurlencode( get_query_var( 's' ) ) ) );
			exit();
		}

		$pagename     = get_query_var( 'pagename' );
		$search_query = get_query_var( 's' );

		// Then, using new search rewrite rule to load new search template.
		if ( 'search' === $pagename || ! empty( $search_query ) ) {
			global $wp_query;

			// Prevent internal 404 on custome search page because of template_redirect hook.
			$wp_query->is_search = true;
			$wp_query->is_404    = false;
			status_header( 200 );

			ob_start();
			require_once plugin_dir_path( __FILE__ ) . 'templates/search.php';
			$html = ob_get_clean();
			echo $html;

			exit();
		}
	}

}

$rlje_theme_search_settings = new RLJE_Theme_Search_Settings();

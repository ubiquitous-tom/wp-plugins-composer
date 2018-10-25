<?php

class RLJE_ACORN_Theme {

	protected $theme = 'acorn';

	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_filter( 'rlje_theme_header_logo', array( $this, 'theme_header_logo' ), 11 );
		add_filter( 'atv_add_img_and_href', array( $this, 'acorn_add_img_and_href' ) );
	}

	public function enqueue_scripts( $hook ) {
		$css_ver = date( 'ymd-Gis', filemtime( plugin_dir_path( __FILE__ ) . 'css/style.css' ) );
		wp_enqueue_style( 'rlje-acorn-theme', plugins_url( 'css/style.css', __FILE__ ), array( 'main_style_css' ), $css_ver );
	}

	public function theme_header_logo( $logo_url ) {
		$logo_url = plugin_dir_url( __FILE__ ) . 'img/logo.png';

		return $logo_url;
	}

	public function acorn_add_img_and_href( $item ) {
		if ( ! isset( $item->href ) ) {
			$id = ( isset( $item->id ) ) ? $item->id: $item->franchiseID;
			$item->href = $id;
		}

		if ( ! isset( $item->img ) ) {
			$img = ( isset( $item->image ) ) ? $item->image : $item->href . '_avatar';
			$item->img = rljeApiWP_getImageUrlFromServices( $img );
		}

		return $item;
	}
}

// $rlje_acorn_theme = new RLJE_ACORN_Theme();

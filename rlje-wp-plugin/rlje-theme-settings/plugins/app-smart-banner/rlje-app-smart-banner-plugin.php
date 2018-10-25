<?php

class RLJE_App_Smart_Banner {

	public $smart_banner;

	public function __construct() {
		add_action( 'init', array( $this, 'init_smart_banner' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_head', array( $this, 'smart_app_banner_header_meta' ), 0 );
	}

	public function init_smart_banner() {
		$smart_app_banner   = get_option( 'rlje_smart_app_banner_settings' );
		$this->smart_banner = [
			'status'               => $smart_app_banner['status'],
			'title'               => $smart_app_banner['title'],
			'author'              => $smart_app_banner['author'],
			'price'               => $smart_app_banner['price'],
			'price-suffix-apple'  => $smart_app_banner['price_apple'],
			'price-suffix-google' => $smart_app_banner['price_google'],
			'icon-apple'          => $smart_app_banner['icon_apple'],
			'icon-google'         => $smart_app_banner['icon_google'],
			'button'              => $smart_app_banner['button_text'],
			'button-url-apple'    => $smart_app_banner['button_url_apple'],
			'button-url-google'   => $smart_app_banner['button_url_google'],
			'enabled-platforms'   => $smart_app_banner['enabled_platforms'],
			// 'custom-design-modifier' => $smart_app_banner['custom_design_modifier'],
			'hide-ttl'            => $smart_app_banner['hide_ttl'],
			// 'disable-positioning' => $smart_app_banner['disable_positioning'],
		];
	}

	public function enqueue_scripts() {
		if ( empty( $this->smart_banner ) ) {
			return;
		}

		if ( absint( $this->smart_banner['status'] ) ) {
			wp_enqueue_style( 'smartbanner', plugins_url( 'css/smartbanner.min.css', __FILE__ ), array(), '1.10.0' );
			wp_enqueue_script( 'smartbanner', plugins_url( 'js/smartbanner.js', __FILE__ ), array(), '1.10.0', true );
		}
	}

	public function smart_app_banner_header_meta() {
		if ( empty( $this->smart_banner ) ) {
			return;
		}

		if ( absint( $this->smart_banner['status'] ) ) {
			foreach ( $this->smart_banner as $name => $content ) {
				echo '<meta name="smartbanner:' . $name . '" content="' . $content . '">' . "\n";
			}
		}
	}
}

$rlje_app_smart_banner = new RLJE_App_Smart_Banner();

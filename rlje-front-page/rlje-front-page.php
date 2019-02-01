<?php

class RLJE_Front_page {
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_init', [ $this, 'register_homepage_settings' ] );
		add_action( 'admin_menu', array( $this, 'register_homepage_menu' ) );

		add_filter( 'rlje_redis_api_cache_groups', array( $this, 'add_country_list_cache_table_list' ) );
		require_once 'rlje-hero.php';
		require_once 'rlje-section-position.php';
		require_once 'rlje-text-part.php';
	}

	public function enqueue_scripts( $hook ) {
		if ( 'toplevel_page_rlje-front-page' === $hook ) {
			wp_enqueue_style( 'rlje-bootstrap', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css', array(), '3.3.4' );
			wp_enqueue_style( 'rlje-front-page', plugins_url( 'css/rlje-front-page.css', __FILE__ ), array(), filemtime( plugin_dir_path( __FILE__ ) . 'css/rlje-front-page.css' ) );

			wp_enqueue_script( 'rlje-bootstrap', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js', array( 'jquery' ), '3.3.4', true );
			wp_enqueue_script( 'rlje-front-page', plugins_url( 'js/rlje-front-page.js', __FILE__ ), array( 'rlje-bootstrap' ), filemtime( plugin_dir_path( __FILE__ ) . 'js/rlje-front-page.js' ), true );
			wp_enqueue_script( 'rlje-bc-swipe', plugins_url( 'js/bc-swipe.js', __FILE__ ), array( 'jquery' ), filemtime( plugin_dir_path( __FILE__ ) . 'js/bc-swipe.js' ), true );
		}
	}

	public function register_homepage_settings() {
		register_setting( 'rlje-front-page', 'rlje_front_page_homepage', array( $this, 'sanitize_callback' ) );
	}

	public function register_homepage_menu() {
		add_menu_page(
			'Homepage Settings',
			'Homepage',
			'manage_options',
			'rlje-front-page',
			null,
			'dashicons-admin-home',
			5
		);
	}

	public function add_country_list_cache_table_list( $cache_list ) {
		$cache_list[] = 'rlje_country_code_list';

		return $cache_list;
	}

	public function sanitize_callback( $data ) {
		if ( ! empty( $_POST['submit'] ) && ( 'Delete Hero Cache' === $_POST['submit'] ) ) {
			$data = apply_filters( 'rlje_front_page_homepage_sanitizer', $data );
		}

		if ( ! empty( $_POST['submit'] ) && ( 'Go to this country' === $_POST['submit'] ) ) {
			$go_to_country = $data['display_country'];
			unset( $data['go_to_country'] );

			$query_args = array(
				'page'    => 'rlje-front-page',
				'country' => $go_to_country,
			);
			wp_safe_redirect( add_query_arg( $query_args, admin_url( 'admin.php' ) ) );
			exit();
		}

		$rlje_front_page_homepage = get_option( 'rlje_front_page_homepage' );
		$key                      = $data['display_country'];
		$new_data[ $key ]         = $data;
		$data                     = array_merge( $rlje_front_page_homepage, $new_data );

		return $data;
	}

	protected function get_current_country() {
		$country = ( ! empty( $_GET['country'] ) ) ? esc_attr( $_GET['country'] ) : 'us';
		// $country = ( ! empty( rljeApiWP_getCountryFilter() ) ) ? rljeApiWP_getCountryFilter() : 'US';
		$countries = $this->get_countries();
		$country   = $countries[ strtoupper( $country ) ];

		return $country;
	}

	protected function get_countries() {
		// https://acorn.dev/wp-admin/admin.php?page=rlje-front-page&country=mx
		// wp_safe_redirect( add_query_arg( array( 'page' => 'rlje-front-page', 'country' => 'mx' ), admin_url( 'admin.php' ) ) );
		$transient_key = 'rlje_country_code_list';
		// delete_transient( $transient_key );
		$countries = get_transient( $transient_key );
		if ( false !== $countries ) {
			return $countries;
		} else {
			$countries     = array();
			$response      = wp_remote_get( 'https://api.rlje.net/cms/admin/countrycode' );
			$body          = wp_remote_retrieve_body( $response );
			$response_body = json_decode( $body );
			foreach ( $response_body as $country ) {
				$countries[ $country->CountryCode ]['timezone'] = $country->TimeZone;
				$countries[ $country->CountryCode ]['name']     = $country->CountryName;
				$countries[ $country->CountryCode ]['code']     = $country->CountryCode;
			}
			$updated = set_transient( $transient_key, $countries, 30 * MINUTE_IN_SECONDS );
			if ( $updated ) {
				return $countries;
			}
		}
	}
}

$rlje_front_page = new RLJE_Front_page();

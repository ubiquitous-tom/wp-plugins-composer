<?php

class RLJE_Front_page {

	protected $prefix          = 'rlje_front_page';
	protected $homepage        = array();
	protected $current_country = '';

	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_init', array( $this, 'register_admin_page' ) );
		add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );

		// add_filter( 'rlje_front_page_homepage_sanitizer', array( $this, 'load_new_country' ) );
		add_filter( 'rlje_redis_api_cache_groups', array( $this, 'add_country_list_cache_table_list' ) );
		require_once 'rlje-section-position.php';
		require_once 'rlje-hero.php';
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

	public function register_admin_page() {
		register_setting( 'rlje_front_page', 'rlje_front_page_homepage', array( $this, 'sanitize_callback' ) );

		add_settings_section( 'homepage_section', 'Hero Settings', array( $this, 'homepage_settings' ), 'rlje-front-page' );
		add_settings_field( 'country_listing', 'Available Countries', array( $this, 'homepage_country_listing' ), 'rlje-front-page', 'homepage_section' );
	}

	public function register_admin_menu() {
		add_menu_page(
			'Main Landing Page',
			'Homepage',
			'manage_options',
			'rlje-front-page',
			array( $this, 'add_menu_page' ),
			'dashicons-admin-home',
			5
		);
	}

	public function add_menu_page() {
		?>
		<div class="wrap" id="rlje-front-page">
			<h1>Homepage Hero Carousel Settings</h1>
			<?php settings_errors(); ?>
			<form method="post" action="options.php">
			<?php
				settings_fields( $this->prefix );
				do_settings_sections( 'rlje-front-page' );
				submit_button();
			?>
			</form>
		</div>
		<?php
	}

	public function homepage_settings( $section ) {
		$rlje_front_page_homepage = get_option( 'rlje_front_page_homepage' );
		$this->current_country    = $this->get_current_country();
		$country_code             = strtoupper( $this->current_country['code'] );
		$this->homepage           = ( array_key_exists( $country_code, $rlje_front_page_homepage ) ) ? $rlje_front_page_homepage[ $country_code ] : array();
		// var_dump( $rlje_front_page_homepage, $country_code, $this->homepage );
		?>
		<p>Currently displaying <strong><?php echo esc_html( $this->current_country['name'] ); ?></strong> Hero Settings</p>
		<?php
	}

	public function homepage_country_listing() {
		// echo home_url( add_query_arg( null, null ) );
		// $this->current_country = ( ! empty( $this->homepage['display_country'] ) ) ? $this->homepage['display_country'] : 'US';
		$countries = $this->get_countries();
		?>
		<select name="rlje_front_page_homepage[display_country]" id="display_country">
			<?php foreach ( $countries as $country ) : ?>
			<option value="<?php echo esc_attr( $country['code'] ); ?>" <?php selected( $country['code'], strtoupper( $this->current_country['code'] ) ); ?>>
				<?php echo esc_html( $country['name'] ); ?>
			</option>
			<?php endforeach; ?>
		</select>
		<!-- <input type="hidden" name="rlje_front_page_homepage[go_to_country]" value="<?php echo esc_attr( $this->current_country['code'] ); ?>"> -->
		<input type="submit" name="submit" id="submit" class="button button-primary" value="Go to this country">
		<p class="description">Currently display hero carousel from <?php echo esc_html( $countries[ $this->current_country['code'] ]['name'] ); ?></p>
		<?php
	}

	// public function load_new_country( $data ) {
	// if ( ! empty( $data['go_to_country'] ) ) {
	// $go_to_country =  $data['display_country'];
	// unset( $data['go_to_country'] );
	// wp_safe_redirect( add_query_arg( array( 'page' => 'rlje-front-page', 'country' => $go_to_country ), admin_url( 'admin.php' ) ) );
	// }
	// return $data;
	// }

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

	public function add_country_list_cache_table_list( $cache_list ) {
		$cache_list[] = 'rlje_country_code_list';

		return $cache_list;
	}

	protected function get_current_country() {
		$country = ( ! empty( $_GET['country'] ) ) ? esc_attr( $_GET['country'] ) : 'us';
		// $country = ( ! empty( rljeApiWP_getCountryFilter() ) ) ? rljeApiWP_getCountryFilter() : 'US';
		$countries = $this->get_countries();
		$country   = $countries[ strtoupper( $country ) ];

		return $country;
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
}

$rlje_front_page = new RLJE_Front_page();

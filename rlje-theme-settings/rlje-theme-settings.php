<?php

class RLJE_Theme_Settings {

	protected $theme_settings         = array();
	protected $rlje_redis_table;

	public function __construct() {
		$this->theme_settings_include_files();

		add_action( 'plugins_loaded', [ $this, 'plugins_loaded' ], 0 );

		add_action( 'admin_init', [ $this, 'display_options' ] );
		add_action( 'admin_menu', [ $this, 'add_rlje_settings_menu' ] );
		add_action( 'admin_menu', [ $this, 'add_rlje_theme_submenu' ] );

		// CORE THEME PAGES.
		require_once 'header/rlje-header.php';
		require_once 'footer/rlje-footer.php';
		require_once 'widgets/rlje-widget.php';
		require_once 'navigations/rlje-theme-menu-settings.php';
		require_once 'search/rlje-theme-search-settings.php';
		require_once 'index/rlje-index-page.php';
		require_once 'schedule/rlje-schedule-page.php';
		require_once 'franchise/rlje-franchise-page.php';

		// CORE THEME PLUGINS.
		require_once 'plugins/country/rlje-country-plugin.php';
		require_once 'plugins/futuredate/rlje-futuredate-plugin.php';
		require_once 'plugins/videodebugger/rlje-videodebugger-plugin.php';
		require_once 'plugins/app-smart-banner/rlje-app-smart-banner-plugin.php';

		// CORE THEME SETTINGS PAGES.
		require_once 'rlje-theme-environment-settings.php';
		require_once 'rlje-theme-settings-3rd-party-tab.php';
		require_once 'rlje-theme-brightcove-settings.php';
		require_once 'rlje-theme-redis-settings.php';

	}

	public function display_options() {
		$this->rlje_redis_table = new RLJE_Redis_Table();

		register_setting( 'rlje_theme_section', 'rlje_theme_settings', array( $this, 'sanitize_callback' ) );

		add_settings_section( 'rlje_theme_section', 'Theme Options', array( $this, 'display_rlje_theme_options_content' ), 'rlje-theme-settings' );
		add_settings_field( 'theme_switcher', 'Current Theme', array( $this, 'display_theme_switcher' ), 'rlje-theme-settings', 'rlje_theme_section' );
	}

	public function add_rlje_settings_menu() {
		add_menu_page(
			'RLJE Theme Settings',
			'RLJE Settings',
			'manage_network',
			'rlje-theme-settings',
			'',
			'',
			100
		);
	}

	public function add_rlje_theme_submenu() {
		add_submenu_page(
			'rlje-theme-settings',
			'RLJE Theme Options',
			'Theme Options',
			'manage_network',
			'rlje-theme-settings',
			[ $this, 'rlje_theme_settings_page' ]
		);
	}

	public function rlje_theme_settings_page() {
		?>
		<div class="wrap">
			<div id="icon-options-general" class="icon32"></div>
			<h1>RLJE Theme Options</h1>
			<?php settings_errors(); ?>
			<form method="post" action="options.php">
				<?php
					// Add_settings_section callback is displayed here. For every new section we need to call settings_fields.
					settings_fields( 'rlje-theme-settings' );

					// all the add_settings_field callbacks is displayed here.
					do_settings_sections( 'rlje-theme-settings' );

					// Add the submit button to serialize the options.
					submit_button();
				?>
			</form>
		</div>
		<?php
	}

	public function display_rlje_theme_options_content() {
		echo 'Load all the specific theme stylesheet and plugins';
		$this->theme_settings = get_option( 'rlje_theme_settings' );
		var_dump( $this->theme_settings );
	}

	public function display_theme_switcher() {
		$current_theme = ( ! empty( $this->theme_settings['current_theme'] ) ) ? $this->theme_settings['current_theme'] : 'acorn';
		?>
		<select name="rlje_theme_settings[current_theme]" id="current-theme" class="regular-text">
			<option value="acorn" <?php selected( $current_theme, 'acorn' ); ?>>Acorn</option>
			<option value="umc" <?php selected( $current_theme, 'umc' ); ?>>UMC</option>
		</select>
		<?php
	}

	public function sanitize_options( $data = null ) {
		$message = 'Data can not be empty';
		$type    = 'error';

		if ( null !== $data ) {
			if ( false === get_option( 'rlje_theme_settings' ) ) {
				add_option( 'rlje_theme_settings', $data );
				$type    = 'updated';
				$message = __( 'Successfully saved', 'my-text-domain' );
			} else {
				update_option( 'rlje_theme_settings', $data );
				$type    = 'updated';
				$message = __( 'Successfully updated', 'my-text-domain' );
			}
		}

		add_settings_error(
			'rlje-theme-setting-notice',
			esc_attr( 'settings_updated' ),
			$message,
			$type
		);
	}

	public function plugins_loaded() {
		$this->theme_settings             = get_option( 'rlje_theme_settings' );
		$this->theme_environment_settings = get_option( 'rlje_theme_environment_settings' );
		$this->sailthru                   = get_option( 'rlje_sailthru_settings' );
		$this->rightsline                 = get_option( 'rlje_rightsline_settings' );

		// For RLJE API call.
		define( 'API_TIMEOUT_SECS', '30' );

		// $env_type = ( ! empty( $this->theme_environment_settings['environment_type'] ) ) ? $this->theme_environment_settings['environment_type'] : 'DEV';
		// if ( ! empty( $env_type ) ) {
		// 	define( 'ENVIRONMENT', $env_type );
		// 	if ( 'DEV' === $env_type ) {
		// 		define( 'JETPACK_DEV_DEBUG', true );
		// 	}
		// }

		$rlje_base_url = ( ! empty( $this->theme_environment_settings['rlje_base_url'] ) ) ? $this->theme_environment_settings['rlje_base_url'] : 'https://dev-api.rlje.net/acorn';
		if ( ! empty( $rlje_base_url ) ) {
			define( 'RLJE_BASE_URL', $rlje_base_url );
		}

		$content_base_url = ( ! empty( $this->theme_environment_settings['content_base_url'] ) ) ? $this->theme_environment_settings['content_base_url'] : 'https://dev-api.rlje.net/cms/acorn';
		if ( ! empty( $content_base_url ) ) {
			define( 'CONTENT_BASE_URL', $content_base_url );
		}

		$sailthru_customer_id = ( ! empty( $this->sailthru['customer_id'] ) ) ? $this->sailthru['customer_id'] : '';
		if ( ! empty( $sailthru_customer_id ) ) {
			define( 'SAILTHRU_CUSTOMER_ID', $sailthru_customer_id );
		}

		$rightsline_base_url = ( ! empty( $this->rightsline['base_url'] ) ) ? $this->rightsline['base_url'] : 'http://api.rightsline.com';
		if ( ! empty( $rightsline_base_url ) ) {
			define( 'RIGHTSLINE_BASE_URL', $rightsline_base_url );
		}

		$rightsline_auth_type   = ( ! empty( $this->rightsline['auth_type'] ) ) ? $this->rightsline['auth_type'] : '';
		$rightsline_auth_header = ( ! empty( $this->rightsline['auth_header'] ) ) ? $this->rightsline['auth_header'] : '';
		if ( ! empty( $rightsline_auth_type ) && ! empty( $rightsline_auth_header ) ) {
			define( 'RIGHTSLINE_AUTH_HEADER', $rightsline_auth_type . ' ' . $rightsline_auth_header );
		}

		// THIS NEEDS TO BE IN wp-config.php IN ORDER FOR IT TO WORK!!
		// IT IS TOO LATE TO CALL INSIDE OF THIS FILE
		// define( 'WP_DEBUG_LOG', true );
		// define( 'WP_DEBUG_DISPLAY', false );
		// define( 'SCRIPT_DEBUG', true );
		// // For query-monitor plugin.
		// define( 'WP_LOCAL_DEV', true );
		// define( 'WP_REDIS_HOST', 'redis' );
		// define( 'WP_REDIS_PORT', '6379' );
		// define( 'WP_REDIS_CLIENT', 'pecl' );
		// define( 'AWS_ACCESS_KEY_ID', $_SERVER['AWS_ACCESS_KEY_ID'] );
		// define( 'AWS_SECRET_ACCESS_KEY', $_SERVER['AWS_SECRET_KEY'] );
		// define( 'GLOBAL_SMTP_HOST', $_SERVER['GLOBAL_SMTP_HOST'] );
		// define( 'GLOBAL_SMTP_USER', $_SERVER['GLOBAL_SMTP_USER'] );
		// define( 'GLOBAL_SMTP_PASSWORD', $_SERVER['GLOBAL_SMTP_PASSWORD'] );
		// define( 'GLOBAL_SMTP_PORT', $_SERVER['GLOBAL_SMTP_PORT'] );
	}

	protected function theme_settings_include_files() {
		$this->theme_settings = get_option( 'rlje_theme_settings' );
		$current_theme        = ( ! empty( $this->theme_settings['current_theme'] ) ) ? $this->theme_settings['current_theme'] : 'acorn';
		switch ( $current_theme ) {
			case 'umc':
				require_once 'themes/umc/rlje-umc-theme.php';
				break;
			case 'acorn':
			default:
				require_once 'themes/acorn/rlje-acorn-theme.php';
		}
	}

	public function sanitize_callback( $data ) {
		$rlje_theme_settings = get_option( 'rlje_theme_settings' );
		$current_theme       = ( ! empty( $rlje_theme_settings['current_theme'] ) ) ? $rlje_theme_settings['current_theme'] : '';
		if ( $current_theme !== $data['current_theme'] ) {
			$clear_caches = array();
			$caches       = $this->rlje_redis_table->get_redis_caches();
			foreach ( $caches as $cache_key => $cache_value ) {
				$clear_caches[] = $cache_key;
			}

			$is_deleted = $this->rlje_redis_table->delete_redis_caches( $clear_caches );
		}

		add_settings_error( 'rlje-theme-settings', 'settings_updated', 'Successfully updated', 'updated' );

		return $data;
	}

}

$rlje_theme_settings = new RLJE_Theme_Settings();

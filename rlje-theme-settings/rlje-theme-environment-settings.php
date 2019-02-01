<?php

class RLJE_Theme_Environment_Settings {

	protected $theme_environment_settings = array();
	protected $rlje_redis_table;

	public function __construct() {
		add_action( 'admin_init', array( $this, 'display_options' ) );
		add_action( 'admin_menu', array( $this, 'add_theme_environment_settings_submenu' ) );
	}

	public function display_options() {
		$this->rlje_redis_table = new RLJE_Redis_Table();

		register_setting( 'rlje_theme_environment_section', 'rlje_theme_environment_settings', array( $this, 'sanitize_callback' ) );

		// Section name, display name, callback to print description of section, page to which section is attached.
		add_settings_section( 'rlje_theme_environment_section', 'Environment Options', array( $this, 'display_rlje_environment_options_content' ), 'rlje-theme-environment-settings' );
		// Setting name, display name, callback to print form element, page in which field is displayed, section to which it belongs.
		// Last field section is optional.
		// add_settings_field( 'environment_type', 'Environment Type', array( $this, 'display_environment_type' ), 'rlje-theme-environment-settings', 'rlje_theme_environment_section' );
		add_settings_field( 'rlje_base_url', 'RLJE Base URL', array( $this, 'display_rlje_base_url' ), 'rlje-theme-environment-settings', 'rlje_theme_environment_section' );
		add_settings_field( 'content_base_url', 'Content Base URL', array( $this, 'display_content_base_url' ), 'rlje-theme-environment-settings', 'rlje_theme_environment_section' );
	}

	public function add_theme_environment_settings_submenu() {
		add_submenu_page(
			'rlje-theme-settings',
			'Environment Settings',
			'Theme Environment',
			'manage_sites',
			'rlje-theme-environment-settings',
			array( $this, 'rlje_theme_environment_settings_page' )
		);
	}

	public function rlje_theme_environment_settings_page() {
		$active_fields = 'rlje_theme_environment_section';
		?>
		<div class="wrap">
			<div id="icon-options-general" class="icon32"></div>
			<h1>Environment Options</h1>
			<?php settings_errors(); ?>
			<form method="post" action="options.php">
				<?php
					// Add_settings_section callback is displayed here. For every new section we need to call settings_fields.
					settings_fields( $active_fields );

					// all the add_settings_field callbacks is displayed here.
					do_settings_sections( 'rlje-theme-environment-settings' );

					// Add the submit button to serialize the options.
					submit_button();
				?>
			</form>
		</div>
		<?php
	}

	public function display_rlje_environment_options_content() {
		echo 'Specific environment for the theme';
		$this->theme_environment_settings = get_option( 'rlje_theme_environment_settings' );
		//var_dump( $this->theme_environment_settings );
	}

	public function display_environment_type() {
		$env_type = ( ! empty( $this->theme_environment_settings['environment_type'] ) ) ? $this->theme_environment_settings['environment_type'] : 'DEV';
		?>
		<select name="rlje_theme_environment_settings[environment_type]" id="environmen-type" class="regular-text">
			<option value="DEV" <?php selected( $env_type, 'DEV' ); ?>>Development</option>
			<option value="QA" <?php selected( $env_type, 'QA' ); ?>>QA</option>
			<option value="PROD" <?php selected( $env_type, 'PROD' ); ?>>PROD</option>
		</select>
		<p class="description">This environment type is for loading "Jetpack DEV Mode" when set to "Development"</p>
		<?php
	}

	public function display_rlje_base_url() {
		$rlje_base_url = ( ! empty( $this->theme_environment_settings['rlje_base_url'] ) ) ? $this->theme_environment_settings['rlje_base_url'] : 'https://dev-api.rlje.net/acorn';
		?>
		<input type="url" name="rlje_theme_environment_settings[rlje_base_url]" id="rlje-base-url" class="regular-text" value="<?php echo esc_url( $rlje_base_url ); ?>" placeholder="RLEJ base URL" pattern="^(?:(?:https?|HTTPS?|ftp|FTP):\/\/)(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-zA-Z\u00a1-\uffff0-9]-*)*[a-zA-Z\u00a1-\uffff0-9]+)(?:\.(?:[a-zA-Z\u00a1-\uffff0-9]-*)*[a-zA-Z\u00a1-\uffff0-9]+)*)(?::\d{2,})?(?:[\/?#]\S*)?$" required>
		<p class="description">URL for the main RLJE site (https://dev-api.rlje.net/acorn) - no trailing slash</p>
		<?php
	}

	public function display_content_base_url() {
		$content_base_url = ( ! empty( $this->theme_environment_settings['content_base_url'] ) ) ? $this->theme_environment_settings['content_base_url'] : 'https://dev-api.rlje.net/cms/acorn';
		?>
		<input type="url" name="rlje_theme_environment_settings[content_base_url]" id="content-base-url" class="regular-text" value="<?php echo esc_url( $content_base_url ); ?>" placeholder="RLJE base content URL" pattern="^(?:(?:https?|HTTPS?|ftp|FTP):\/\/)(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-zA-Z\u00a1-\uffff0-9]-*)*[a-zA-Z\u00a1-\uffff0-9]+)(?:\.(?:[a-zA-Z\u00a1-\uffff0-9]-*)*[a-zA-Z\u00a1-\uffff0-9]+)*)(?::\d{2,})?(?:[\/?#]\S*)?$" required>
		<p class="description">URL for the site content from RLJE API (https://dev-api.rlje.net/cms/acorn) - no trailing slash</p>
		<?php
	}

	public function sanitize_callback( $data ) {
		$rlje_theme_environment_settings = get_option( 'rlje_theme_environment_settings' );
		$rlje_base_url                   = ( ! empty( $rlje_theme_environment_settings['rlje_base_url'] ) ) ? $rlje_theme_environment_settings['rlje_base_url'] : '';
		$content_base_url                = ( ! empty( $rlje_theme_environment_settings['content_base_url'] ) ) ? $rlje_theme_environment_settings['content_base_url'] : '';
		if ( $rlje_base_url !== $data['rlje_base_url'] || $content_base_url !== $data['content_base_url'] ) {
			$clear_caches = array();
			$caches       = $this->rlje_redis_table->get_redis_caches();
			foreach ( $caches as $cache_key => $cache_value ) {
				$clear_caches[] = $cache_key;
			}

			$is_deleted = $this->rlje_redis_table->delete_redis_caches( $clear_caches );
		}

		add_settings_error( 'rlje-theme-environment-settings', 'settings_updated', 'Successfully updated', 'updated' );

		return $data;
	}

}

$rjle_theme_environment_settings = new RLJE_Theme_Environment_Settings();

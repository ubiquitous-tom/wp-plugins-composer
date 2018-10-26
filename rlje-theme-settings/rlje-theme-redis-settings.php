<?php

class RLJE_Theme_Redis_Settings {

	protected $theme_settings       = array();
	protected $theme_redis_settings = array();
	protected $rlje_redis_table;

	public function __construct() {
		add_action( 'admin_init', array( $this, 'display_options' ) );
		add_action( 'admin_menu', array( $this, 'add_theme_redis_settings_submenu' ) );

		require_once 'settings/redis/rlje-redis-table.php';
		require_once 'settings/redis/rlje-redis-table-submenu.php';
	}

	public function display_options() {
		$this->rlje_redis_table = new RLJE_Redis_Table();

		register_setting( 'rlje_theme_redis_section', 'rlje_theme_redis_settings', array( $this, 'sanitize_callback' ) );

		// Section name, display name, callback to print description of section, page to which section is attached.
		add_settings_section( 'rlje_theme_redis_section', 'Redis Cache Options', array( $this, 'display_rlje_redis_options_content' ), 'rlje-theme-redis-settings' );
		// Setting name, display name, callback to print form element, page in which field is displayed, section to which it belongs.
		// Last field section is optional.
		add_settings_field( 'redis_cache_time', 'Cache Time', array( $this, 'display_redis_cache_time' ), 'rlje-theme-redis-settings', 'rlje_theme_redis_section' );
		add_settings_field( 'redis_server_info', 'Server Info', array( $this, 'display_redis_server_info' ), 'rlje-theme-redis-settings', 'rlje_theme_redis_section' );
		add_settings_field( 'redis_cache_clear', 'Clear All API Cache', array( $this, 'display_clear_all_redis_cache' ), 'rlje-theme-redis-settings', 'rlje_theme_redis_section' );
	}

	public function add_theme_redis_settings_submenu() {
		add_submenu_page(
			'rlje-theme-settings',
			'API Cache',
			'API Cache Management',
			'manage_sites',
			'rlje-theme-redis-settings',
			array( $this, 'rlje_theme_redis_settings_page' )
		);
	}

	public function rlje_theme_redis_settings_page() {
		$active_fields = 'rlje_theme_redis_section';
		?>
		<div class="wrap">
			<div id="icon-options-general" class="icon32"></div>
			<h1>Redis Cache Options</h1>
			<?php settings_errors(); ?>
			<form method="post" action="options.php">
				<?php
					// Add_settings_section callback is displayed here. For every new section we need to call settings_fields.
					settings_fields( $active_fields );

					// all the add_settings_field callbacks is displayed here.
					do_settings_sections( 'rlje-theme-redis-settings' );

					// Add the submit button to serialize the options.
					submit_button();
				?>
			</form>
		</div>
		<?php
	}

	public function display_rlje_redis_options_content() {
		echo 'Redis cache information for the theme';
		$this->theme_settings       = get_option( 'rlje_theme_settings' );
		$this->theme_redis_settings = get_option( 'rlje_theme_redis_settings' );
		var_dump( $this->theme_redis_settings );
	}

	public function display_redis_cache_time() {
		$default_ttl = ( defined( 'RLJE_API_PLUGIN__TIME_REFRESH_CACHE ' ) ) ? RLJE_API_PLUGIN__TIME_REFRESH_CACHE : 900;
		$ttl         = ( ! empty( $this->theme_redis_settings['ttl'] ) ) ? $this->theme_redis_settings['ttl'] : $default_ttl;
		?>
		<input type="number" name="rlje_theme_redis_settings[ttl]" id="rlje-redis-cache-time" class="regular-text" value="<?php echo absint( $ttl ); ?>">
		<p class="description">Please enter time in <strong>Seconds</strong>. (Default: 900 seconds)</p>
		<?php
	}

	public function display_redis_server_info() {
		?>
		<p>
			For more information on Redis server please go here.<br>
			<a href="<?php echo esc_url( admin_url( 'network/settings.php?page=redis-cache' ) ); ?>" target="_blank">Redis Object Cache</a>
		</p>
		<p class="description">You can also flush all Redis Cache within the link also.</p>
		<?php
	}

	public function display_clear_all_redis_cache() {
		$text             = 'Clear Cache';
		$type             = 'delete';
		$name             = 'clear_cache';
		$wrap             = false;
		$other_attributes = null;
		submit_button( $text, $type, $name, $wrap, $other_attributes );
		?>
		<p class="description">Clear all the API caches that was saved in Redis sever.</p>
		<?php
	}

	public function sanitize_callback( $data ) {
		if ( ! empty( $_POST['clear_cache'] ) ) {
			$clear_caches = array();
			$caches       = $this->rlje_redis_table->get_redis_caches();
			foreach ( $caches as $cache_key => $cache_value ) {
				$clear_caches[] = $cache_key;
			}

			$is_deleted = $this->rlje_redis_table->delete_redis_caches( $clear_caches );
		}

		add_settings_error( 'rlje-theme-redis-settings', 'settings_updated', 'Successfully updated', 'updated' );
		// add_settings_error( 'hasNumberError', 'validation_error', 'Data can not be empty', 'error' );

		return $data;
	}
}

$rlje_theme_redis_settings = new RLJE_Theme_Redis_Settings();

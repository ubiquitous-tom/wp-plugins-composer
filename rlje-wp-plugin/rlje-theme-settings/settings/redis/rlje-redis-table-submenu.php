<?php

class RLJE_Theme_Redis_Cache_list_Settings {

	protected $rlje_redis_table;

	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_init', array( $this, 'display_options' ) );
		add_action( 'admin_menu', array( $this, 'add_theme_redis_cache_list_settings_submenu' ) );

		// For table bulk action
		add_action( 'admin_post_-1', array( $this, 'redirect_back') );
		add_action( 'admin_post_delete', array( $this, 'remove_selected_redis_cache_files') );
	}

	public function enqueue_scripts( $hook ) {
		if ( 'theme-settings_page_rlje-theme-redis-cache-list-settings' === $hook ) {
			$css_ver = date( 'ymd-Gis', filemtime( plugin_dir_path( __FILE__ ) . 'css/style.css' ) );
			wp_enqueue_style( 'rlje-theme-redis-setting', plugins_url( 'css/style.css', __FILE__ ), array(), $css_ver );
		}
	}

	public function display_options() {
		$this->rlje_redis_table = new RLJE_Redis_Table();

		register_setting( 'rlje_theme_redis_cache_list_section', 'rlje_theme_redis_cache_list_settings' );

		add_settings_section( 'rlje_theme_redis_section', 'Redis Cache List Table', array( $this, 'display_rlje_redis_options_content' ), 'rlje-theme-redis-cache-list-settings' );
		add_settings_field( 'redis_cache_info', 'Current Caches', array( $this, 'display_redis_cache_info_table' ), 'rlje-theme-redis-cache-list-settings', 'rlje_theme_redis_section' );
	}

	public function add_theme_redis_cache_list_settings_submenu() {
		add_submenu_page(
			'rlje-theme-settings',
			'Redis Cache List',
			'Redis Cache List',
			'manage_sites',
			'rlje-theme-redis-cache-list-settings',
			array( $this, 'rlje_theme_redis_cache_list_settings_page' )
		);
	}

	public function rlje_theme_redis_cache_list_settings_page() {
		$active_fields = 'rlje_theme_redis_cache_list_section';
		?>
		<div class="wrap">
			<div id="icon-options-general" class="icon32"></div>
			<h1>Redis Cache list</h1>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="rlje_action" value="delete_selected_redis_caches">
				<?php
					// Add_settings_section callback is displayed here. For every new section we need to call settings_fields.
					settings_fields( $active_fields );

					// all the add_settings_field callbacks is displayed here.
					do_settings_sections( 'rlje-theme-redis-cache-list-settings' );

					// Add the submit button to serialize the options.
					// submit_button();
				?>
			</form>
		</div>
		<?php
	}

	public function display_rlje_redis_options_content() {
		echo 'Currently available redis caches on the theme';
		$this->theme_settings = get_option( 'rlje_theme_settings' );
		$this->theme_redis_cache_list_settings = get_option( 'rlje_theme_redis_cache_list_settings' );
		var_dump( $this->theme_redis_cache_list_settings );
	}

	public function display_redis_cache_info_table() {
		$this->rlje_redis_table->prepare_items();
		$this->rlje_redis_table->display();
	}

	public function redirect_back() {
		/**
		 * Redirect back to the settings page that was submitted
		 */
		$goback = wp_get_referer();
		wp_redirect( $goback );
		exit;
	}

	public function remove_selected_redis_cache_files() {
		/**
		 * Redirect back to the settings page that was submitted
		 */
		$rlje_action = $_POST['rlje_action'];
		$action = $_POST['action'];
		$action2 = $_POST['action2'];
		$caches = $_POST['rlje_theme_delete_redis_cache'];
		// $paged = $_POST['paged'];

		if ( 'delete_selected_redis_caches' === $_POST['rlje_action'] ) {
			if ( ( 'delete' === $_POST['action'] ) && ( -1 === intval( $_POST['action2'] ) ) ) {
				$this->rlje_redis_table->delete_redis_caches( $caches );
			}
		}


		$query_args = array(
		// 	'rlje_action' => $rlje_action,
		// 	'action' => $action,
		// 	'action2' => $action2,
		// 	'paged' => $paged,
			'settings-updated' => 'true',
		);
		$goback = add_query_arg( $query_args, wp_get_referer() );
		wp_redirect( $goback );
		exit;
	}

}

$rlje_theme_redis_cache_listsettings = new RLJE_Theme_Redis_Cache_list_Settings();

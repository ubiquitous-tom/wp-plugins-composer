<?php

require plugin_dir_path( __FILE__ ) . '../../../franchise/rlje-franchise-page.php';
class UMC_Franchise_page extends RLJE_Franchise_Page {

	protected $umc_theme_settings;

	public function __construct() {
		parent::__construct();
		$this->umc_theme_settings = get_option( 'rlje_umc_theme_plugins_settings' );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_init', array( $this, 'display_options' ), 20 );

		add_filter( 'rlje_franchise_page_template_path', array( $this, 'umc_franchise_page_template_path' ) );
	}

	public function enqueue_scripts() {
		if ( ! $this->is_franchise_page() ) {
			return;
		}

		if ( ! $this->is_current_franchise() ) {
			return;
		}

		$franchise_css_ver = date( 'ymd-Gis', filemtime( plugin_dir_path( __FILE__ ) . 'css/umc-franchise.css' ) );
		wp_enqueue_style( 'rlje-umc-franchise-theme', plugins_url( 'css/umc-franchise.css', __FILE__ ), array( 'rlje-umc-theme' ), $franchise_css_ver );
	}


	public function display_options() {
		register_setting( 'rlje_theme_section', 'rlje_umc_theme_plugins_settings' );
		add_settings_section( 'rlje_umc_theme_section', 'UMC Theme Options', array( $this, 'display_rlje_umc_theme_options_content' ), 'rlje-theme-settings' );
		add_settings_field( 'umc_franchise_page', 'UMC Franchise Page', array( $this, 'display_umc_theme_franchise_page' ), 'rlje-theme-settings', 'rlje_umc_theme_section' );
	}

	public function display_rlje_umc_theme_options_content() {
		echo 'UMC Theme plugins';
		// $this->umc_theme_settings = get_option( 'rlje_umc_theme_plugins_settings' );
		var_dump( $this->umc_theme_settings );
	}

	public function display_umc_theme_franchise_page() {
		$franchise_page = ( ! empty( $this->umc_theme_settings['franchise_page'] ) ) ? absint( $this->umc_theme_settings['franchise_page']) : 0;
		?>
		<input type="radio" name="rlje_umc_theme_plugins_settings[franchise_page]" id="umc-franchise-page-on" value="1" <?php checked( $franchise_page, 1); ?>>
		<label for="umc-franchise-page-on">ON</label>
		<br>
		<input type="radio" name="rlje_umc_theme_plugins_settings[franchise_page]" id="umc-franchise-page-off" value="0" <?php checked( $franchise_page, 0); ?>>
		<label for="umc-franchise-page-off">Off</label>
		<?php
	}

	public function umc_franchise_page_template_path( $template_path ) {
		if ( $this->is_franchise_page() ) {
			$template_path = plugin_dir_path( __FILE__ ) . 'templates/franchise.php';
		}

		return $template_path;
	}

	protected function is_franchise_page() {
		$is_activated = ( ! empty( $this->umc_theme_settings['franchise_page'] ) ) ? intval( $this->umc_theme_settings['franchise_page']) : 0;
		if ( $is_activated ) {
			return true;
		}

		return false;
	}
}

$umc_franchise_page = new UMC_Franchise_page();

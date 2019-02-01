<?php

class RLJE_Theme_Settings_3rd_Party_Tab {

	protected $apple      = [];
	protected $google     = [];
	protected $sailthru   = [];
	protected $rightsline = [];
	protected $tealium    = [];
	protected $stunning   = [];

	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_init', array( $this, 'display_options' ) );
		add_action( 'admin_menu', [ $this, 'third_party_submenu' ] );
	}

	public function enqueue_scripts( $hook ) {
		if ( 'rlje-settings_page_3rd-party-options' === $hook ) {
			wp_enqueue_script( 'rlje-theme-setting', plugins_url( 'settings/3rd-party-tab/js/script.js', __FILE__ ) );
		}
	}

	public function third_party_submenu() {
		add_submenu_page(
			'rlje-theme-settings',
			'Third party services settings',
			'3rd Party Options',
			'manage_network',
			'3rd-party-options',
			[ $this, 'third_party_settings_page' ]
		);
	}

	public function third_party_settings_page() {
		?>
		<div class="wrap">
			<div id="icon-options-general" class="icon32"></div>
			<h1>3rd Party Options</h1>
			<p>You can access and update third party services settings used by the site on this page</p>
			<?php settings_errors(); ?>
			<form method="post" action="options.php">
				<?php
					// Add_settings_section callback is displayed here. For every new section we need to call settings_fields.
					settings_fields( 'rlje_3rd_party_section' );

					// all the add_settings_field callbacks is displayed here.
					do_settings_sections( '3rd-party-options' );

					// Add the submit button to serialize the options.
					submit_button();
				?>
			</form>
		</div>
		<?php
	}

	public function display_options() {
		register_setting( 'rlje_3rd_party_section', 'rlje_sailthru_settings' );
		register_setting( 'rlje_3rd_party_section', 'rlje_rightsline_settings' );
		register_setting( 'rlje_3rd_party_section', 'rlje_google_settings' );
		register_setting( 'rlje_3rd_party_section', 'rlje_apple_settings' );
		register_setting( 'rlje_3rd_party_section', 'rlje_tealium_settings' );
		register_setting( 'rlje_3rd_party_section', 'rlje_stunning_settings' );
		register_setting( 'rlje_3rd_party_section', 'rlje_smart_app_banner_settings' );

		add_settings_section( 'rlje_apple_section', 'Apple Options', array( $this, 'display_apple_options_content' ), '3rd-party-options' );
		add_settings_field( 'apple_itunes_app_id', 'Apple Itunes App', array( $this, 'display_apple_itunes_app_settings' ), '3rd-party-options', 'rlje_apple_section' );

		add_settings_section( 'rlje_google_section', 'Google Options', array( $this, 'display_google_options_content' ), '3rd-party-options' );
		add_settings_field( 'google_analytics', 'Google Analytics', array( $this, 'display_google_analytics_settings' ), '3rd-party-options', 'rlje_google_section' );
		add_settings_field( 'google_site_verification', 'Google Site Verification', array( $this, 'display_google_site_verification_settings' ), '3rd-party-options', 'rlje_google_section' );
		add_settings_field( 'google_play_app_id', 'Google Play App', array( $this, 'display_google_play_app_settings' ), '3rd-party-options', 'rlje_google_section' );

		add_settings_section( 'rlje_sailthru_section', 'Sailthru Options', array( $this, 'display_sailthru_options_content' ), '3rd-party-options' );
		add_settings_field( 'sailthru', 'Sailthru Customer ID', array( $this, 'display_sailthru_settings' ), '3rd-party-options', 'rlje_sailthru_section' );

		add_settings_section( 'rlje_rightsline_section', 'Rightsline Options', array( $this, 'display_rightsline_options_content' ), '3rd-party-options' );
		add_settings_field( 'rightsline_base_url', 'Base URL', array( $this, 'display_rightsline_base_url' ), '3rd-party-options', 'rlje_rightsline_section' );
		add_settings_field( 'rightsline_auth_type', 'Auth Type', array( $this, 'display_rightsline_auth_type' ), '3rd-party-options', 'rlje_rightsline_section' );
		add_settings_field( 'rightsline_auth_header', 'Auth Header', array( $this, 'display_rightsline_auth_header' ), '3rd-party-options', 'rlje_rightsline_section' );

		add_settings_section( 'rlje_tealium_section', 'Tealium Options', array( $this, 'display_tealium_options_content' ), '3rd-party-options' );
		add_settings_field( 'tealium_id', 'Tealium Tag', array( $this, 'display_tealium_settings' ), '3rd-party-options', 'rlje_tealium_section' );

		add_settings_section( 'rlje_stunning_section', 'Stunning Options', array( $this, 'display_stunning_options_content' ), '3rd-party-options' );
		add_settings_field( 'stunning_id', 'Stunning ID', array( $this, 'display_stunning_settings' ), '3rd-party-options', 'rlje_stunning_section' );

		add_settings_section( 'rlje_smart_app_banner_section', 'Smart App Banner Options', array( $this, 'display_smart_app_banner_options_content' ), '3rd-party-options' );
		add_settings_field( 'smart_app_banner_status', 'Smart App Banner On/Off', array( $this, 'display_smart_app_banner_status' ), '3rd-party-options', 'rlje_smart_app_banner_section' );
		add_settings_field( 'smart_app_banner_title', 'Smart App Banner title', array( $this, 'display_smart_app_banner_title' ), '3rd-party-options', 'rlje_smart_app_banner_section' );
		add_settings_field( 'smart_app_banner_author', 'Smart App Banner Author', array( $this, 'display_smart_app_banner_author' ), '3rd-party-options', 'rlje_smart_app_banner_section' );
		add_settings_field( 'smart_app_banner_price', 'Smart App Banner Price', array( $this, 'display_smart_app_banner_price' ), '3rd-party-options', 'rlje_smart_app_banner_section' );
		add_settings_field( 'smart_app_banner_price_suffix_apple', 'Smart App Banner Price for iOS', array( $this, 'display_smart_app_banner_price_suffix_apple' ), '3rd-party-options', 'rlje_smart_app_banner_section' );
		add_settings_field( 'smart_app_banner_price_suffix_google', 'Smart App Banner Price for Android', array( $this, 'display_smart_app_banner_price_suffix_google' ), '3rd-party-options', 'rlje_smart_app_banner_section' );
		add_settings_field( 'smart_app_banner_icon_apple', 'Smart App Banner iOS Icon', array( $this, 'display_smart_app_banner_icon_apple' ), '3rd-party-options', 'rlje_smart_app_banner_section' );
		add_settings_field( 'smart_app_banner_icon_google', 'Smart App Banner Android Icon', array( $this, 'display_smart_app_banner_icon_google' ), '3rd-party-options', 'rlje_smart_app_banner_section' );
		add_settings_field( 'smart_app_banner_button', 'Smart App Banner Button Text', array( $this, 'display_smart_app_banner_button_text' ), '3rd-party-options', 'rlje_smart_app_banner_section' );
		add_settings_field( 'smart_app_banner_button_url_apple', 'Smart App Banner App URL for iOS', array( $this, 'display_smart_app_banner_button_url_apple' ), '3rd-party-options', 'rlje_smart_app_banner_section' );
		add_settings_field( 'smart_app_banner_button_url_google', 'Smart App Banner App URL for Android', array( $this, 'display_smart_app_banner_button_url_google' ), '3rd-party-options', 'rlje_smart_app_banner_section' );
		add_settings_field( 'smart_app_banner_enabled_platforms', 'Smart App Banner Supported Platforms', array( $this, 'display_smart_app_banner_enabled_platforms' ), '3rd-party-options', 'rlje_smart_app_banner_section' );
		// add_settings_field( 'smart_app_banner_custom_design_modifier', 'Smart App Banner title', array( $this, 'display_smart_app_banner_custom_design_modifier' ), 'rlje-theme-settings', 'rlje_smart_app_banner_section' );
		add_settings_field( 'smart_app_banner_hide_ttl', 'Smart App Banner Cookie Time', array( $this, 'display_smart_app_banner_hide_ttl' ), '3rd-party-options', 'rlje_smart_app_banner_section' );
	}

	public function display_apple_options_content() {
		$this->apple = get_option( 'rlje_apple_settings' );
	}

	public function display_apple_itunes_app_settings() {
		$apple_itunes_app_id = ( ! empty( $this->apple['apple_itunes_app_id'] ) ) ? $this->apple['apple_itunes_app_id'] : '';
		?>
		<input type="text" name="rlje_apple_settings[apple_itunes_app_id]" id="apple-itunes-app-id" class="regular-text" value="<?php echo esc_attr( $apple_itunes_app_id ); ?>" placeholder="Apple Itunes APP ID">
		<p class="description">Please enter your Apple Itunes APP ID. (e.g., app-id=896014310)</p>
		<?php
	}

	public function display_google_options_content() {
		$this->google = get_option( 'rlje_google_settings' );
	}

	public function display_google_analytics_settings() {
		$google_analytics_id = ( ! empty( $this->google['google_analytics_id'] ) ) ? $this->google['google_analytics_id'] : '';
		?>
		<input type="text" name="rlje_google_settings[google_analytics_id]" id="ga-id" class="regular-text" value="<?php echo esc_attr( $google_analytics_id ); ?>" placeholder="Google Analytics ID">
		<p class="description">Please enter your Google Analytics ID. (e.g., GTM-5C5NK6)</p>
		<?php
	}

	public function display_google_site_verification_settings() {
		$google_site_verification_id = ( ! empty( $this->google['google_site_verification_id'] ) ) ? $this->google['google_site_verification_id'] : '';
		?>
		<input type="text" name="rlje_google_settings[google_site_verification_id]" id="google-site-verification-id" class="regular-text" value="<?php echo esc_attr( $google_site_verification_id ); ?>" placeholder="Google Site Verification ID">
		<p class="description">Please enter your Google Site Verification ID. (e.g., QCrNnLN11eCtEq_RIVjUQEXRabEJewu4tPwxbjJHHj4)</p>
		<?php
	}

	public function display_google_play_app_settings() {
		$google_play_app_id = ( ! empty( $this->google['google_play_app_id'] ) ) ? $this->google['google_play_app_id'] : '';
		?>
		<input type="text" name="rlje_google_settings[google_play_app_id]" id="google-play-app-id" class="regular-text" value="<?php echo esc_attr( $google_play_app_id ); ?>" placeholder="Google Play APP ID">
		<p class="description">Please enter your Google Play APP ID. (e.g., app-id=com.acorn.tv)</p>
		<?php
	}

	public function display_sailthru_options_content() {
		$this->sailthru = get_option( 'rlje_sailthru_settings', array() );
	}

	public function display_sailthru_settings() {
		$sailthru_customer_id = ( ! empty( $this->sailthru['customer_id'] ) ) ? $this->sailthru['customer_id'] : '';
		?>
		<input type="text" name="rlje_sailthru_settings[customer_id]" id="sailthru-customer-id" class="regular-text" value="<?php echo esc_attr( $sailthru_customer_id ); ?>" placeholder="Sailthru Customer ID">
		<p class="description">Please enter your sailthru customer id</p>
		<?php
	}

	public function display_rightsline_options_content() {
		$this->rightsline = get_option( 'rlje_rightsline_settings' );
	}

	public function display_rightsline_base_url() {
		$rightsline_base_url = ( ! empty( $this->rightsline['base_url'] ) ) ? $this->rightsline['base_url'] : 'http://api.rightsline.com';
		?>
		<input type="text" name="rlje_rightsline_settings[base_url]" id="rightsline-base-url" class="regular-text" value="<?php echo esc_url( $rightsline_base_url ); ?>" placeholder="Rightsline API URL">
		<p class="description">URL for the Rightsline API (http://api.rightsline.com) - no trailing slash</p>
		<?php
	}

	public function display_rightsline_auth_type() {
		$rightsline_auth_type = ( ! empty( $this->rightsline['auth_type'] ) ) ? $this->rightsline['auth_type'] : '';
		?>
		<select name="rlje_rightsline_settings[auth_type]" id="rightsline-auth-type">
			<option value="Basic">Basic</option>
			<option value="Bearer">Bearer</option>
			<option value="AWS4-HMAC-SHA256">AWS Signature Version 4</option>
		</select>
		<p data-auth-type="Basic" class="auth-type description hidden">Basic Example - Authorization: Basic Ym9zY236Ym9zY28=</p>
		<p data-auth-type="Bearer" class="auth-type description hidden">Bearer Example - Authorization: Bearer AbCdEf123456</p>
		<p data-auth-type="AWS4-HMAC-SHA256" class="auth-type description hidden">AWS Example - Authorization: AWS4-HMAC-SHA256<br>
			Credential=AKIAIOSFODNN7EXAMPLE/20130524/us-east-1/s3/aws4_request,<br>
			SignedHeaders=host;range;x-amz-date,<br>
			Signature=fe5f80f77d5fa3beca038a248ff027d0445342fe2855ddc963176630326f1024</p>
		<?php
	}

	public function display_rightsline_auth_header() {
		$rightsline_auth_header = ( ! empty( $this->rightsline['auth_header'] ) ) ? $this->rightsline['auth_header'] : '';
		?>
		<input type="text" name="rlje_rightsline_settings[auth_header]" id="rightsline-auth-header" class="regular-text" value="<?php echo esc_attr( $rightsline_auth_header ); ?>" placeholder="Rightsline Auth Header">
		<p class="description">Please enter your Rightsline auth header</p>
		<?php
	}

	public function display_tealium_options_content() {
		$this->tealium = get_option( 'rlje_tealium_settings' );
	}

	public function display_tealium_settings() {
		$tealium_id = ( ! empty( $this->tealium['tealium_id'] ) ) ? $this->tealium['tealium_id'] : '';
		?>
		<input type="text" name="rlje_tealium_settings[tealium_id]" id="tealium-id" class="regular-text" value="<?php echo esc_attr( $tealium_id ); ?>" placeholder="Tealium ID">
		<p class="description">Please enter your Tealium ID. (e.g., 6377)</p>
		<?php
	}

	public function display_stunning_options_content() {
		$this->stunning = get_option( 'rlje_stunning_settings' );
	}

	public function display_stunning_settings() {
		$stunning_id = ( ! empty( $this->stunning['stunning_id'] ) ) ? $this->stunning['stunning_id'] : '';
		?>
		<input type="text" name="rlje_stunning_settings[stunning_id]" id="stunning-id" class="regular-text" value="<?php echo esc_attr( $stunning_id ); ?>" placeholder="2047dxrbmvipxdnrcfbetfxoe">
		<?php
	}

	public function display_smart_app_banner_options_content() {
		$this->smart_app_banner = get_option( 'rlje_smart_app_banner_settings' );
	}

	public function display_smart_app_banner_status() {
		$status = ( ! empty( $this->smart_app_banner['status'] ) ) ? absint( $this->smart_app_banner['status'] ) : 0;
		?>
		<label for="smart-app-banner-status-on">
			<input type="radio" name="rlje_smart_app_banner_settings[status]" id="smart-app-banner-status-on" value="1" <?php checked( $status, 1 ); ?>> ON
		</label>
		<br>
		<label for="smart-app-banner-status-off">
			<input type="radio" name="rlje_smart_app_banner_settings[status]" id="smart-app-banner-status-off" value="0" <?php checked( $status, 0 ); ?>> OFF
		</label>
		<p class="description">Turn On/Off Smart App Banner. Please turn this off if you want to use `Apple Itunes App ID or Google Play App ID`</p>
		<?php
	}

	public function display_smart_app_banner_title() {
		$title = ( ! empty( $this->smart_app_banner['title'] ) ) ? $this->smart_app_banner['title'] : 'Acorn TV - The Best British TV';
		?>
		<input type="text" name="rlje_smart_app_banner_settings[title]" id="smart-app-banner-title" class="regular-text" value="<?php echo esc_attr( $title ); ?>" placeholder="Acorn TV - The Best British TV">
		<p class="description">App Title</p>
		<?php
	}

	public function display_smart_app_banner_author() {
		$author = ( ! empty( $this->smart_app_banner['author'] ) ) ? $this->smart_app_banner['author'] : 'RLJ Entertainment, Inc.';
		?>
		<input type="text" name="rlje_smart_app_banner_settings[author]" id="smart-app-banner-author" class="regular-text" value="<?php echo esc_attr( $author ); ?>" placeholder="RLJ Entertainment, Inc.">
		<p class="description">App Author. (Default: RLJ Entertainment, Inc.)</p>
		<?php
	}

	public function display_smart_app_banner_price() {
		$price = ( ! empty( $this->smart_app_banner['price'] ) ) ? $this->smart_app_banner['price'] : 'FREE';
		?>
		<input type="text" name="rlje_smart_app_banner_settings[price]" id="smart-app-banner-price" class="regular-text" value="<?php echo esc_attr( $price ); ?>" placeholder="FREE">
		<p class="description">App Price. (Default: FREE)</p>
		<?php
	}

	public function display_smart_app_banner_price_suffix_apple() {
		$price_apple = ( ! empty( $this->smart_app_banner['price_apple'] ) ) ? $this->smart_app_banner['price_apple'] : ' - On the App Store';
		?>
		<input type="text" name="rlje_smart_app_banner_settings[price_apple]" id="smart-app-banner-price-apple" class="regular-text" value="<?php echo esc_attr( $price_apple ); ?>" placeholder=" - On the App Store">
		<p class="description">App Price. (Default: - On the App Store)</p>
		<?php
	}

	public function display_smart_app_banner_price_suffix_google() {
		$price_google = ( ! empty( $this->smart_app_banner['price_google'] ) ) ? $this->smart_app_banner['price_google'] : ' - In Google Play';
		?>
		<input type="text" name="rlje_smart_app_banner_settings[price_google]" id="smart-app-banner-price-google" class="regular-text" value="<?php echo esc_attr( $price_google ); ?>" placeholder=" - In Google Play">
		<p class="description">App Price. (Default: - In Google Play)</p>
		<?php
	}

	public function display_smart_app_banner_icon_apple() {
		$icon_apple = ( ! empty( $this->smart_app_banner['icon_apple'] ) ) ? $this->smart_app_banner['icon_apple'] : plugins_url( 'img/atv-app-mobile.png', __FILE__ );
		?>
		<input type="text" name="rlje_smart_app_banner_settings[icon_apple]" id="smart-app-banner-icon-apple" class="regular-text" value="<?php echo esc_url( $icon_apple ); ?>" placeholder="<?php echo esc_url( plugins_url( 'img/atv-app-mobile.png', __FILE__ ) ); ?>">
		<p class="description">App Icon for iOS. (Default: - Website Logo)</p>
		<?php
	}

	public function display_smart_app_banner_icon_google() {
		$icon_google = ( ! empty( $this->smart_app_banner['icon_google'] ) ) ? $this->smart_app_banner['icon_google'] : plugins_url( 'img/atv-app-mobile.png', __FILE__ );
		?>
		<input type="text" name="rlje_smart_app_banner_settings[icon_google]" id="smart-app-banner-icon-google" class="regular-text" value="<?php echo esc_url( $icon_google ); ?>" placeholder="<?php echo esc_url( plugins_url( 'img/atv-app-mobile.png', __FILE__ ) ); ?>">
		<p class="description">App Icon for Android. (Default: Website Logo)</p>
		<?php
	}

	public function display_smart_app_banner_button_text() {
		$button_text = ( ! empty( $this->smart_app_banner['button_text'] ) ) ? $this->smart_app_banner['button_text'] : 'VIEW';
		?>
		<input type="text" name="rlje_smart_app_banner_settings[button_text]" id="smart-app-banner-button-text" class="regular-text" value="<?php echo esc_attr( $button_text ); ?>" placeholder="VIEW">
		<p class="description">App Button Text. (Default: - VIEW)</p>
		<?php
	}

	public function display_smart_app_banner_button_url_apple() {
		$button_url_apple = ( ! empty( $this->smart_app_banner['button_url_apple'] ) ) ? $this->smart_app_banner['button_url_apple'] : '';
		?>
		<input type="text" name="rlje_smart_app_banner_settings[button_url_apple]" id="smart-app-banner-button-url-apple" class="regular-text" value="<?php echo esc_url( $button_url_apple ); ?>" placeholder="https://ios/application-url">
		<p class="description">App URL for iOS. (Default: https://ios/application-url)</p>
		<?php
	}

	public function display_smart_app_banner_button_url_google() {
		$button_url_google = ( ! empty( $this->smart_app_banner['button_url_google'] ) ) ? $this->smart_app_banner['button_url_google'] : '';
		?>
		<input type="text" name="rlje_smart_app_banner_settings[button_url_google]" id="smart-app-banner-button-url-google" class="regular-text" value="<?php echo esc_url( $button_url_google ); ?>" placeholder="https://android/application-url">
		<p class="description">App URL for Android. (Default: https://android/application-url)</p>
		<?php
	}

	public function display_smart_app_banner_enabled_platforms() {
		$enabled_platforms = ( ! empty( $this->smart_app_banner['enabled_platforms'] ) ) ? $this->smart_app_banner['enabled_platforms'] : 'android,ios';
		?>
		<input type="text" name="rlje_smart_app_banner_settings[enabled_platforms]" id="smart-app-banner-enabled-platforms" class="regular-text" value="<?php echo esc_attr( $enabled_platforms ); ?>" placeholder="android,ios">
		<p class="description">App platfroms we want the banner to show on. (Default: android,ios)</p>
		<?php
	}

	public function display_smart_app_banner_hide_ttl() {
		$hide_ttl = ( ! empty( $this->smart_app_banner['hide_ttl'] ) ) ? $this->smart_app_banner['hide_ttl'] : '2592000000';
		?>
		<input type="text" name="rlje_smart_app_banner_settings[hide_ttl]" id="smart-app-banner-button-hide-ttl" class="regular-text" value="<?php echo esc_attr( $hide_ttl ); ?>" placeholder="2592000000">
		<p class="description">Cookie time for the smart banner to reappear again after close. (Default: 2592000000 - 1 month in milliseconds)</p>
		<?php
	}
}

$rlje_theme_settings_3rd_party_tab = new RLJE_Theme_Settings_3rd_Party_Tab();

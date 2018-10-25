<?php

class RLJE_Theme_Settings {

	protected $theme_settings         = array();
	protected $theme_text_settings    = array();
	protected $theme_plugins_settings = array();
	protected $signup_promo_settings   = array();
	protected $rlje_redis_table;

	public function __construct() {
		$this->theme_settings_include_files();

		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ), 0 );

		add_action( 'admin_init', array( $this, 'display_options' ) );
		add_action( 'admin_menu', array( $this, 'add_theme_settings_menu' ) );

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
		require_once 'rlje-theme-settings-3rd-party-tab.php';
		require_once 'rlje-theme-environment-settings.php';
		require_once 'rlje-theme-brightcove-settings.php';
		require_once 'rlje-theme-redis-settings.php';

	}

	public function display_options() {
		$this->rlje_redis_table = new RLJE_Redis_Table();

		register_setting( 'rlje_theme_section', 'rlje_theme_settings', array( $this, 'sanitize_callback' ) );
		register_setting( 'rlje_theme_section', 'rlje_theme_text_settings' );
		register_setting( 'rlje_theme_section', 'rlje_signup_promo_settings' );
		register_setting( 'rlje_theme_section', 'rlje_theme_plugins_settings' );

		// Here we display the sections and options in the settings page based on the active tab.
		$tab = ( ! empty( $_GET['tab'] ) ) ? $_GET['tab'] : '';
		if ( empty( $tab ) || ( 'main-options' === $tab ) ) {
			add_settings_section( 'rlje_theme_section', 'Theme Options', array( $this, 'display_rlje_theme_options_content' ), 'rlje-theme-settings' );
			add_settings_field( 'theme_switcher', 'Current Theme', array( $this, 'display_theme_switcher' ), 'rlje-theme-settings', 'rlje_theme_section' );

			add_settings_section( 'rlje_theme_text_section', 'Theme Text Options', array( $this, 'display_rlje_theme_text_options_content' ), 'rlje-theme-settings' );
			add_settings_field( 'navigation_signup_text', 'Navigation Sign Up Text', array( $this, 'display_navigation_signup_text' ), 'rlje-theme-settings', 'rlje_theme_text_section' );
			add_settings_field( 'navigation_login_text', 'Navigation Log In Text', array( $this, 'display_navigation_login_text' ), 'rlje-theme-settings', 'rlje_theme_text_section' );
			add_settings_field( 'navigation_free_trial_text', 'Navigation Free Trial Text', array( $this, 'display_navigation_free_trial_text' ), 'rlje-theme-settings', 'rlje_theme_text_section' );
			add_settings_field( 'home_callout_left_section_text', 'Home Callout Left Section Text', array( $this, 'display_home_callout_one_text' ), 'rlje-theme-settings', 'rlje_theme_text_section' );
			add_settings_field( 'home_callout_right_section_text', 'Home Callout Right Section Text', array( $this, 'display_home_callout_two_text' ), 'rlje-theme-settings', 'rlje_theme_text_section' );
			add_settings_field( 'brightcove_video_placeholder_text', 'Episode/Trailer Text', array( $this, 'display_brightcove_video_placeholder_text' ), 'rlje-theme-settings', 'rlje_theme_text_section' );

			add_settings_section( 'signup_promo_section', 'Signup Promo Section', [ $this, 'display_signup_promo_section' ], 'rlje-theme-settings' );
			add_settings_field( 'signup_promo_section_enable', 'Enable', [ $this, 'signup_promo_section_activation' ], 'rlje-theme-settings', 'signup_promo_section' );
			add_settings_field( 'signup_promo_section_video_id', 'Sales video being shown', [ $this, 'signup_promo_section_video_id' ], 'rlje-theme-settings', 'signup_promo_section' );
			add_settings_field( 'signup_promo_section_pitch', 'Sales pitch being shown', [ $this, 'signup_promo_section_pitch' ], 'rlje-theme-settings', 'signup_promo_section' );

			add_settings_section( 'rlje_theme_plugins_section', 'Plugins Options', array( $this, 'display_rlje_theme_plugins_content' ), 'rlje-theme-settings' );
			add_settings_field( 'theme_plugins_front_page', 'Home Page', array( $this, 'display_theme_plugins_front_page' ), 'rlje-theme-settings', 'rlje_theme_plugins_section' );
			add_settings_field( 'theme_plugins_landing_page', 'Landing Pages', array( $this, 'display_theme_plugins_landing_page' ), 'rlje-theme-settings', 'rlje_theme_plugins_section' );
			add_settings_field( 'theme_plugins_news_and_reviews', 'News And Reviews', array( $this, 'display_theme_plugins_news_and_reviews' ), 'rlje-theme-settings', 'rlje_theme_plugins_section' );
			add_settings_field( 'theme_plugins_home_callout', 'Home Callout', array( $this, 'display_theme_home_callout' ), 'rlje-theme-settings', 'rlje_theme_plugins_section' );
		}
	}

	public function add_theme_settings_menu() {
		// This needs to be updated to `manage_network` so only Super Admin can edit this.
		add_menu_page(
			'RLJE Theme Settings', // Required. Text in browser title bar when the page associated with this menu item is displayed.
			'Theme Settings', // Required. Text to be displayed in the menu.
			'manage_sites', // Required. The required capability of users to access this menu item.
			'rlje-theme-settings', // Required. A unique identifier to identify this menu item.
			array( $this, 'rlje_theme_settings_page' ), // Optional. This callback outputs the content of the page associated with this menu item.
			'', // Optional. The URL to the menu item icon.
			100 // Optional. Position of the menu item in the menu.
		);
	}

	public function rlje_theme_settings_page() {
		?>
		<div class="wrap">
			<div id="icon-options-general" class="icon32"></div>
			<h1>RLJE Theme Options</h1>
			<?php
			// We check if the page is visited by click on the tabs or on the menu button.
			// Then we get the active tab.
			$tab = ( ! empty( $_GET['tab'] ) ) ? $_GET['tab'] : '';
			switch ( $tab ) {
				case '3rd-party-options':
					$active_tab    = '3rd-party-options';
					$active_fields = 'rlje_3rd_party_section';
					break;
				default:
					$active_tab    = 'main-options';
					$active_fields = 'rlje_theme_section';
			}
			?>

			<!-- WordPress provides the styling for tabs. -->
			<h2 class="nav-tab-wrapper">
				<!-- when tab buttons are clicked we jump back to the same page but with a new parameter that represents the clicked tab. accordingly we make it active -->
				<a href="?page=rlje-theme-settings&tab=main-options" class="nav-tab
				<?php
				if ( 'main-options' === $active_tab ) {
					echo 'nav-tab-active'; }
				?>
				">Theme Options</a>
				<a href="?page=rlje-theme-settings&tab=3rd-party-options" class="nav-tab
				<?php
				if ( '3rd-party-options' === $active_tab ) {
					echo 'nav-tab-active'; }
				?>
				">3rd Party Options</a>
			</h2>
			<?php settings_errors(); ?>
			<form method="post" action="options.php">
				<?php
					// Add_settings_section callback is displayed here. For every new section we need to call settings_fields.
					settings_fields( $active_fields );

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

	public function display_rlje_theme_text_options_content() {
		echo 'Text to display on the theme navigation';
		$this->theme_text_settings = get_option( 'rlje_theme_text_settings' );
		var_dump( $this->theme_text_settings );
	}

	public function display_navigation_signup_text() {
		$signup_text = ( ! empty( $this->theme_text_settings['signup'] ) ) ? $this->theme_text_settings['signup'] : 'Sign Up';
		?>
		<input type="text" name="rlje_theme_text_settings[signup]" class="regular-text" id="signup-text" value="<?php echo esc_attr( $signup_text ); ?>" placeholder="Sign Up">
		<?php
	}

	public function display_navigation_login_text() {
		$login_text = ( ! empty( $this->theme_text_settings['login'] ) ) ? $this->theme_text_settings['login'] : 'Log In';
		?>
		<input type="text" name="rlje_theme_text_settings[login]" class="regular-text" id="login-text" value="<?php echo esc_attr( $login_text ); ?>" placeholder="Log In">
		<?php
	}

	public function display_navigation_free_trial_text() {
		$free_trial_text = ( ! empty( $this->theme_text_settings['free_trial'] ) ) ? $this->theme_text_settings['free_trial'] : 'Start Free Trial';
		?>
		<input type="text" name="rlje_theme_text_settings[free_trial]" class="regular-text" id="free-trial-text" value="<?php echo esc_attr( $free_trial_text ); ?>" placeholder="Start Free Trial">
		<?php
	}

	public function display_home_callout_one_text() {
		$callout = ( ! empty( $this->theme_text_settings['callout']['one'] ) ) ? $this->theme_text_settings['callout']['one'] : array();
		$callout_text = ( ! empty( $callout['text'] ) ) ? $callout['text'] : 'Available on Roku, Apple TV, Samsung Smart TV, iPhone, iPad, web and more.';
		$callout_link = ( ! empty( $callout['link'] ) ) ? $callout['link'] : home_url( '/' );
		$callout_link_text = ( ! empty( $callout['link_text'] ) ) ? $callout['link_text'] : 'Learn More';
		?>
		<p>
			<label for="callout-one-text"><strong>Main Text:</strong></label><br>
			<textarea type="text" name="rlje_theme_text_settings[callout][one][text]" class="widefat" id="callout-one-text" placeholder="Insert Text Here" rows="5"><?php echo esc_html( $callout_text ); ?></textarea>
		</p>
		<p class="description">Right home callout section main text</p>
		<hr>
		<p>
			<label for="callout-one-link"><strong>Button URL:</strong></label><br>
			<input type="text" name="rlje_theme_text_settings[callout][one][link]" class="widefat" id="callout-one-link" value="<?php echo esc_attr( $callout_link ); ?>" placeholder="<?php echo esc_url( home_url( '/' ) ); ?>">
		</p>
		<p class="description">Right home callout section button url</p>
		<hr>
		<p>
			<label for="callout-one-link-text"><strong>Button Text:</strong></label><br>
			<input type="text" name="rlje_theme_text_settings[callout][one][link_text]" class="regular-text" id="callout-one-link-text" value="<?php echo esc_attr( $callout_link_text ); ?>" placeholder="Learn More">
		</p>
		<p class="description">Right home callout section button url</p>
		<hr>
		<?php
	}

	public function display_home_callout_two_text() {
		$callout = ( ! empty( $this->theme_text_settings['callout']['two'] ) ) ? $this->theme_text_settings['callout']['two'] : array();
		$callout_text = ( ! empty( $callout['text'] ) ) ? $callout['text'] : 'Over 1,800 hours of programming, including 60 shows you won\'t find anywhere else.';
		$callout_link = ( ! empty( $callout['link'] ) ) ? $callout['link'] : home_url( '/' );
		$callout_link_text = ( ! empty( $callout['link_text'] ) ) ? $callout['link_text'] : 'Start Your Free Trial';
		?>
		<p>
			<label for="callout-two-text"><strong>Main Text:</strong></label><br>
			<textarea type="text" name="rlje_theme_text_settings[callout][two][text]" class="widefat" id="callout-two-text" placeholder="Insert Text Here" rows="5"><?php echo esc_html( $callout_text ); ?></textarea>
		</p>
		<p class="description">Left home callout section main text</p>
		<hr>
		<p>
			<label for="callout-two-link"><strong>Button URL:</strong></label><br>
			<input type="text" name="rlje_theme_text_settings[callout][two][link]" class="widefat" id="callout-two-link" value="<?php echo esc_attr( $callout_link ); ?>" placeholder="<?php echo esc_url( home_url( '/' ) ); ?>">
		</p>
		<p class="description">Left home callout section button url</p>
		<hr>
		<p>
			<label for="callout-two-link-text"><strong>Button Text:</strong></label><br>
			<input type="text" name="rlje_theme_text_settings[callout][two][link_text]" class="regular-text" id="callout-two-link-text" value="<?php echo esc_attr( $callout_link_text ); ?>" placeholder="Start Your Free Trial">
		</p>
		<p class="description">Left home callout section button url</p>
		<hr>
		<?php
	}

	public function display_brightcove_video_placeholder_text() {
		$video_placeholder = ( ! empty( $this->theme_text_settings['video_placeholder'] ) ) ? $this->theme_text_settings['video_placeholder'] : array();
		$video_placeholder_title_text = ( ! empty( $video_placeholder['title_text'] ) ) ? $video_placeholder['title_text'] : 'Watch world-class TV from Britain and beyond';
		$video_placeholder_text = ( ! empty( $video_placeholder['text'] ) ) ? $video_placeholder['text'] : 'Always available, always commercial free';
		?>
		<p>
			<label for="video-placeholder-title-text"><strong>Title Text:</strong></label><br>
			<input type="text" name="rlje_theme_text_settings[video_placeholder][title_text]" class="regular-text" id="video-placeholder-title-text" value="<?php echo esc_attr( $video_placeholder_title_text ); ?>" placeholder="Start Your Free Trial">
		</p>
		<p class="description">Episode/Trailer Brightcove placeholder title text if the video is not available</p>
		<hr>
		<p>
			<label for="video-placeholder-text"><strong>Main Text:</strong></label><br>
			<textarea type="text" name="rlje_theme_text_settings[video_placeholder][text]" class="widefat" id="video-placeholder-text" placeholder="Insert Text Here" rows="5"><?php echo esc_html( $video_placeholder_text ); ?></textarea>
		</p>
		<p class="description">Episode/Trailer Brightcove placeholder main text if the video is not available</p>
		<hr>
		<?php
	}

	public function display_signup_promo_section() {
		$this->signup_promo_settings = get_option( 'rlje_signup_promo_settings' );
		var_dump( $this->signup_promo_settings );
		echo 'Options to control signup promotion section seen on the homepage';
	}

	public function signup_promo_section_activation() {
		$promo_enabled = ( ! empty( $this->signup_promo_settings['enable'] ) ) ? boolval( $this->signup_promo_settings['enable'] ) : false;
		?>
		<input type="radio" name="rlje_signup_promo_settings[enable]" id="rlje-signup-promo-on" class="regular-text" value="1" <?php checked( $promo_enabled, true ); ?>>
		<label for="rlje-signup-promo-on">On</label>
		<br>
		<input type="radio" name="rlje_signup_promo_settings[enable]" id="rlje-signup-promo-off" class="regular-text" value="0" <?php checked( $promo_enabled, false ); ?>>
		<label for="rlje-signup-promo-off">Off</label>
		<?php
	}

	public function signup_promo_section_video_id() {
		$promo_video_id = ( ! empty( $this->signup_promo_settings['video_id'] ) ) ? $this->signup_promo_settings['video_id'] : '5180867444001';
		?>
		<input name="rlje_signup_promo_settings[video_id]" class="regular-text" placeholder="5180867444001" value="<?php echo esc_attr( $promo_video_id ); ?>">
		<?php
	}

	public function signup_promo_section_pitch() {
		$promo_pitch = ( ! empty( $this->signup_promo_settings['pitch'] ) ) ? $this->signup_promo_settings['pitch'] : 'Start your FREE 7-day trial to watch the best in Black film & television with new and exclusive content added weekly! Download UMC on your favorite Apple and Android mobile devices or stream on Roku or Amazon Prime Video Channels. Drama, romance, comedy and much more - it’s all on UMC!';
		?>
		<textarea name="rlje_signup_promo_settings[pitch]" class="widefat" placeholder="Some text to intrigue users to signup" rows="5"><?php echo esc_html( $promo_pitch ); ?></textarea>
		<?php
	}

	public function display_rlje_theme_plugins_content() {
		echo 'Toggle for other RLJE plugins for the theme';
		$this->theme_plugins_settings = get_option( 'rlje_theme_plugins_settings' );
		var_dump( $this->theme_plugins_settings );
	}

	public function display_theme_plugins_front_page() {
		$front_page = ( ! intval( $this->theme_plugins_settings['front_page'] ) ) ? intval( $this->theme_plugins_settings['front_page'] ) : 1;
		?>
		<input type="radio" name="rlje_theme_plugins_settings[front_page]" id="rlje-plugins-front-page-on" class="regular-text" value="1" <?php checked( $front_page, 1 ); ?>>
		<label for="rlje-plugins-front-page-on">On</label>
		<br>
		<input type="radio" name="rlje_theme_plugins_settings[front_page]" id="rlje-plugins-front-page-off" class="regular-text" value="0" <?php checked( $front_page, 0 ); ?>>
		<label for="rlje-plugins-front-page-off">Off</label>
		<p class="description">For activating Homepage Hero</p>
		<?php
	}

	public function display_theme_plugins_landing_page() {
		$landing_pages = ( ! intval( $this->theme_plugins_settings['landing_pages'] ) ) ? intval( $this->theme_plugins_settings['landing_pages'] ) : 1;
		?>
		<input type="radio" name="rlje_theme_plugins_settings[landing_pages]" id="rlje-plugins-landing-page-on" class="regular-text" value="1" <?php checked( $landing_pages, 1 ); ?>>
		<label for="rlje-plugins-landing-page-on">On</label>
		<br>
		<input type="radio" name="rlje_theme_plugins_settings[landing_pages]" id="rlje-plugins-landing-page-off" class="regular-text" value="0" <?php checked( $landing_pages, 0 ); ?>>
		<label for="rlje-plugins-landing-page-off">Off</label>
		<p class="description">For activating Franchise landing page</p>
		<?php
	}

	public function display_theme_plugins_news_and_reviews() {
		$news_and_reviews = ( ! intval( $this->theme_plugins_settings['news_and_reviews'] ) ) ? intval( $this->theme_plugins_settings['news_and_reviews'] ) : 1;
		?>
		<input type="radio" name="rlje_theme_plugins_settings[news_and_reviews]" id="rlje-plugins-news-and-reviews-on" class="regular-text" value="1" <?php checked( $news_and_reviews, 1 ); ?>>
		<label for="rlje-plugins-news-and-reviews-on">On</label>
		<br>
		<input type="radio" name="rlje_theme_plugins_settings[news_and_reviews]" id="rlje-plugins-news-and-reviews-off" class="regular-text" value="0" <?php checked( $news_and_reviews, 0 ); ?>>
		<label for="rlje-plugins-news-and-reviews-off">Off</label>
		<p class="description">For activating Homepage section</p>
		<?php
	}

	public function display_theme_home_callout() {
		$home_callout = ( ! intval( $this->theme_plugins_settings['home_callout'] ) ) ? intval( $this->theme_plugins_settings['home_callout'] ) : 1;
		?>
		<input type="radio" name="rlje_theme_plugins_settings[home_callout]" id="rlje-plugins-home-callout-on" class="regular-text" value="1" <?php checked( $home_callout, 1 ); ?>>
		<label for="rlje-plugins-home-callout-on">On</label>
		<br>
		<input type="radio" name="rlje_theme_plugins_settings[home_callout]" id="rlje-plugins-home-callout-off" class="regular-text" value="0" <?php checked( $home_callout, 0 ); ?>>
		<label for="rlje-plugins-home-callout-off">Off</label>
		<p class="description">For activating Homepage Callout section</p>
		<p class="description">* Please insert all the needed text and links before activating this section.</p>
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

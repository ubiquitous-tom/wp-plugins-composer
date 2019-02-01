<?php

class RLJE_text_part {

	public function __construct() {
        add_action( 'admin_init', [ $this, 'register_settings' ] );
        add_action( 'admin_init', [ $this, 'register_sections' ] );
        add_action( 'admin_menu', [ $this, 'homepage_text_menu' ] );
    }

    public function register_settings() {
        register_setting( 'rlje-homepage-text', 'rlje_theme_text_settings' );
		register_setting( 'rlje-homepage-text', 'rlje_signup_promo_settings' );
		register_setting( 'rlje-homepage-text', 'rlje_theme_plugins_settings' );
    }

    public function homepage_text_menu() {
		add_submenu_page(
			'rlje-front-page',
			'Homepage Text and sections Settings',
			'Text and parts',
			'manage_options',
			'rlje-homepage-text',
			[ $this, 'render_homepage_text_settings' ]
		);
    }

    public function render_homepage_text_settings() {
		?>
		<div class="wrap" id="rlje-front-page">
			<?php settings_errors(); ?>
			<form method="post" action="options.php">
			<?php
				settings_fields( 'rlje-homepage-text' );
				do_settings_sections( 'rlje-homepage-text' );
				submit_button();
			?>
			</form>
		</div>
		<?php
    }

    public function register_sections() {
		add_settings_section( 'rlje_theme_text_section', 'Theme Text Options', array( $this, 'display_rlje_theme_text_options_content' ), 'rlje-homepage-text' );
		add_settings_field( 'navigation_signup_text', 'Navigation Sign Up Text', array( $this, 'display_navigation_signup_text' ), 'rlje-homepage-text', 'rlje_theme_text_section' );
		add_settings_field( 'navigation_login_text', 'Navigation Log In Text', array( $this, 'display_navigation_login_text' ), 'rlje-homepage-text', 'rlje_theme_text_section' );
		add_settings_field( 'navigation_free_trial_text', 'Navigation Free Trial Text', array( $this, 'display_navigation_free_trial_text' ), 'rlje-homepage-text', 'rlje_theme_text_section' );
		add_settings_field( 'home_callout_left_section_text', 'Home Callout Left Section Text', array( $this, 'display_home_callout_one_text' ), 'rlje-homepage-text', 'rlje_theme_text_section' );
		add_settings_field( 'home_callout_right_section_text', 'Home Callout Right Section Text', array( $this, 'display_home_callout_two_text' ), 'rlje-homepage-text', 'rlje_theme_text_section' );
		add_settings_field( 'brightcove_video_placeholder_text', 'Episode/Trailer Text', array( $this, 'display_brightcove_video_placeholder_text' ), 'rlje-homepage-text', 'rlje_theme_text_section' );

		add_settings_section( 'signup_promo_section', 'Signup Promo Section', [ $this, 'display_signup_promo_section' ], 'rlje-homepage-text' );
		add_settings_field( 'signup_promo_section_enable', 'Enable', [ $this, 'signup_promo_section_activation' ], 'rlje-homepage-text', 'signup_promo_section' );
		add_settings_field( 'signup_promo_section_video_id', 'Sales video being shown', [ $this, 'signup_promo_section_video_id' ], 'rlje-homepage-text', 'signup_promo_section' );
		add_settings_field( 'signup_promo_section_pitch', 'Sales pitch being shown', [ $this, 'signup_promo_section_pitch' ], 'rlje-homepage-text', 'signup_promo_section' );

		add_settings_section( 'rlje_theme_plugins_section', 'Plugins Options', array( $this, 'display_rlje_theme_plugins_content' ), 'rlje-homepage-text' );
		add_settings_field( 'theme_plugins_front_page', 'Hero carousel', array( $this, 'display_theme_plugins_front_page' ), 'rlje-homepage-text', 'rlje_theme_plugins_section' );
		add_settings_field( 'theme_plugins_landing_page', 'Landing Pages', array( $this, 'display_theme_plugins_landing_page' ), 'rlje-homepage-text', 'rlje_theme_plugins_section' );
		add_settings_field( 'theme_plugins_news_and_reviews', 'News And Reviews', array( $this, 'display_theme_plugins_news_and_reviews' ), 'rlje-homepage-text', 'rlje_theme_plugins_section' );
		add_settings_field( 'theme_plugins_home_callout', 'Home Callout', array( $this, 'display_theme_home_callout' ), 'rlje-homepage-text', 'rlje_theme_plugins_section' );
    }

    public function display_rlje_theme_text_options_content() {
		echo 'Text to display on the theme navigation';
	}

	public function display_navigation_signup_text() {
		$theme_text_settings = get_option( 'rlje_theme_text_settings' );
		$signup_text = ( ! empty( $theme_text_settings['signup'] ) ) ? $theme_text_settings['signup'] : 'Sign Up';
		?>
		<input type="text" name="rlje_theme_text_settings[signup]" class="regular-text" id="signup-text" value="<?php echo esc_attr( $signup_text ); ?>" placeholder="Sign Up">
		<?php
	}

	public function display_navigation_login_text() {
		$theme_text_settings = get_option( 'rlje_theme_text_settings' );
		$login_text = ( ! empty( $theme_text_settings['login'] ) ) ? $theme_text_settings['login'] : 'Log In';
		?>
		<input type="text" name="rlje_theme_text_settings[login]" class="regular-text" id="login-text" value="<?php echo esc_attr( $login_text ); ?>" placeholder="Log In">
		<?php
	}

	public function display_navigation_free_trial_text() {
		$theme_text_settings = get_option( 'rlje_theme_text_settings' );
		$free_trial_text = ( ! empty( $theme_text_settings['free_trial'] ) ) ? $theme_text_settings['free_trial'] : 'Start Free Trial';
		?>
		<input type="text" name="rlje_theme_text_settings[free_trial]" class="regular-text" id="free-trial-text" value="<?php echo esc_attr( $free_trial_text ); ?>" placeholder="Start Free Trial">
		<?php
	}

	public function display_home_callout_one_text() {
		$theme_text_settings = get_option( 'rlje_theme_text_settings' );
		$callout = ( ! empty( $theme_text_settings['callout']['one'] ) ) ? $theme_text_settings['callout']['one'] : array();
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
		$theme_text_settings = get_option( 'rlje_theme_text_settings' );
		$callout = ( ! empty( $theme_text_settings['callout']['two'] ) ) ? $theme_text_settings['callout']['two'] : array();
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
		$theme_text_settings = get_option( 'rlje_theme_text_settings' );
		$video_placeholder = ( ! empty( $theme_text_settings['video_placeholder'] ) ) ? $theme_text_settings['video_placeholder'] : array();
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
		echo 'Options to control signup promotion section seen on the homepage';
	}

	public function signup_promo_section_activation() {
		$signup_promo_settings = get_option( 'rlje_signup_promo_settings' );
		$promo_enabled = ( ! empty( $signup_promo_settings['enable'] ) ) ? boolval( $signup_promo_settings['enable'] ) : false;
		?>
		<input type="radio" name="rlje_signup_promo_settings[enable]" id="rlje-signup-promo-on" value="1" <?php checked( $promo_enabled, true ); ?>>
		<label for="rlje-signup-promo-on">On</label>
		<br>
		<input type="radio" name="rlje_signup_promo_settings[enable]" id="rlje-signup-promo-off" value="0" <?php checked( $promo_enabled, false ); ?>>
		<label for="rlje-signup-promo-off">Off</label>
		<?php
	}

	public function signup_promo_section_video_id() {
		$signup_promo_settings = get_option( 'rlje_signup_promo_settings' );
		$promo_video_id = ( ! empty( $signup_promo_settings['video_id'] ) ) ? $signup_promo_settings['video_id'] : '5180867444001';
		?>
		<input name="rlje_signup_promo_settings[video_id]" class="regular-text" placeholder="5180867444001" value="<?php echo esc_attr( $promo_video_id ); ?>">
		<?php
	}

	public function signup_promo_section_pitch() {
		$signup_promo_settings = get_option( 'rlje_signup_promo_settings' );
		$promo_pitch = ( ! empty( $signup_promo_settings['pitch'] ) ) ? $signup_promo_settings['pitch'] : 'Start your FREE 7-day trial to watch the best in Black film & television with new and exclusive content added weekly! Download UMC on your favorite Apple and Android mobile devices or stream on Roku or Amazon Prime Video Channels. Drama, romance, comedy and much more - it’s all on UMC!';
		?>
		<textarea name="rlje_signup_promo_settings[pitch]" class="widefat" placeholder="Some text to intrigue users to signup" rows="5"><?php echo esc_html( $promo_pitch ); ?></textarea>
		<?php
	}

	public function display_rlje_theme_plugins_content() {
		echo 'Toggle for other RLJE plugins for the theme';
	}

	public function display_theme_plugins_front_page() {
		$theme_plugins_settings = get_option( 'rlje_theme_plugins_settings' );
		$front_page = ( ! intval( $theme_plugins_settings['front_page'] ) ) ? intval( $theme_plugins_settings['front_page'] ) : 1;
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
		$theme_plugins_settings = get_option( 'rlje_theme_plugins_settings' );
		$landing_pages = ( ! intval( $theme_plugins_settings['landing_pages'] ) ) ? intval( $theme_plugins_settings['landing_pages'] ) : 1;
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
		$theme_plugins_settings = get_option( 'rlje_theme_plugins_settings' );
		$news_and_reviews = ( ! intval( $theme_plugins_settings['news_and_reviews'] ) ) ? intval( $theme_plugins_settings['news_and_reviews'] ) : 1;
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
		$theme_plugins_settings = get_option( 'rlje_theme_plugins_settings' );
		$home_callout = ( ! intval( $theme_plugins_settings['home_callout'] ) ) ? intval( $theme_plugins_settings['home_callout'] ) : 1;
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

}

$rlje_txt_part = new RLJE_text_part();
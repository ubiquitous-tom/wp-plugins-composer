<?php

class RLJE_News_And_Reviews_Settings {

	protected $transient_key = 'rlje_news_and_review_';
	protected $news_and_reviews_settings;

	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'admin_init', array( $this, 'create_display_type_settings' ) );
		add_action( 'admin_menu', array( $this, 'register_news_and_reviews_display_type_page' ) );
	}

	public function admin_enqueue_scripts( $hook ) {
		if ( 'news-reviews_page_rlje-news-and-reviews-settings' === $hook ) {
			// Versioning for cachebuster.
			// $settings_js_version = date( 'ymd-Gis', filemtime( plugin_dir_path( __FILE__ ) . 'js/admin-news-and-reviews-settings.js' ) );
			$settings_css_verion = date( 'ymd-Gis', filemtime( plugin_dir_path( __FILE__ ) . 'css/admin-news-and-reviews-settings.css' ) );
			wp_enqueue_style( 'rlje-admin-news-and-reviews-settings', plugins_url( 'css/admin-news-and-reviews-settings.css', __FILE__ ), array(), $settings_css_verion );
			// wp_enqueue_script( 'rlje-admin-news-and-reviews-settings', plugins_url( 'js/admin-news-and-reviews-settings.js', __FILE__ ), array( 'jquery-ui-core' ), $settings_js_version, true );
		}
	}

	public function create_display_type_settings() {
		register_setting( 'rlje_news_and_reviews_news_settings_section', 'rlje_news_and_reviews_settings', array( $this, 'rlje_settings_sanitize_callback' ) );

		add_settings_section( 'rlje_news_and_reviews_news_display_type_section', 'Display Type', array( $this, 'display_news_and_reviews_display_type_options' ), 'rlje-news-and-reviews-settings' );
		add_settings_field( 'display_type', 'Display Combinations', array( $this, 'news_and_reviews_display_type' ), 'rlje-news-and-reviews-settings', 'rlje_news_and_reviews_news_display_type_section' );

		add_settings_section( 'rlje_news_and_reviews_news_display_section', 'Display Section', array( $this, 'display_news_and_reviews_display_section_options' ), 'rlje-news-and-reviews-settings' );
		add_settings_field( 'news_and_reviews_left_display_section', 'Left Display Section', array( $this, 'news_and_reviews_left_display_section' ), 'rlje-news-and-reviews-settings', 'rlje_news_and_reviews_news_display_section' );
		add_settings_field( 'news_and_reviews_right_display_section', 'Right Display Section', array( $this, 'news_and_reviews_right_display_section' ), 'rlje-news-and-reviews-settings', 'rlje_news_and_reviews_news_display_section' );
	}

	public function display_news_and_reviews_display_type_options() {
		$this->news_and_reviews_settings = get_option( 'rlje_news_and_reviews_settings' );
		var_dump( $this->news_and_reviews_settings );
		print '<p>The combinations of News and Reviews to display</p>';
	}

	public function news_and_reviews_display_type() {
		$left_section  = ( ! empty( $this->news_and_reviews_settings['left_section'] ) ) ? intval( $this->news_and_reviews_settings['left_section'] ) : 1;
		$right_section = ( ! empty( $this->news_and_reviews_settings['right_section'] ) ) ? intval( $this->news_and_reviews_settings['right_section'] ) : 0;
		switch ( $left_section ) {
			case 2:
				$left_type = 'News (Group 2)';
				break;
			case 1:
			default:
				$left_type = 'News (Group 1)';
		}

		switch ( $right_section ) {
			case 2:
				$right_type = 'News (Group 2)';
				break;
			case 1:
				$right_type = 'News (Group 1)';
				break;
			default:
				$right_type = 'Reviews';
		}
		?>
		<div class="display-type">
			<div class="display-type-1"
				<label for="display-type-1">Current Display Type</label>
				<table>
					<tbody>
						<tr>
							<td><?php echo esc_html( $left_type ); ?></td>
							<td><?php echo esc_html( $right_type ); ?></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<?php
	}

	public function display_news_and_reviews_display_section_options() {
		// $this->news_and_reviews_settings = get_option( 'rlje_news_and_reviews_settings' );
		// var_dump($this->news_and_reviews_display_type);
		print '<p>Assing what to display in News and Reviews on homepage</p>';
	}

	public function news_and_reviews_left_display_section() {
		var_dump( $this->news_and_reviews_settings['left_section'] );
		$news_group = ( intval( $this->news_and_reviews_settings['left_section'] ) ) ? intval( $this->news_and_reviews_settings['left_section'] ) : 1;
		?>
		<select name="rlje_news_and_reviews_settings[left_section]" id="left-section" class="regular-text">
			<option value="1" <?php selected( $news_group, 1 ); ?>>News (Group 1)</option>
			<option value="2" <?php selected( $news_group, 2 ); ?>>News (Group 2)</option>
		</select>
		<?php
	}

	public function news_and_reviews_right_display_section() {
		var_dump( $this->news_and_reviews_settings['right_section'] );
		$news_group = ( intval( $this->news_and_reviews_settings['right_section'] ) ) ? intval( $this->news_and_reviews_settings['right_section'] ) : 0;
		?>
		<select name="rlje_news_and_reviews_settings[right_section]" id="right-section" class="regular-text">
			<option value="0" <?php selected( $news_group, 0 ); ?>>Reviews</option>
			<option value="1" <?php selected( $news_group, 1 ); ?>>News (Group 1)</option>
			<option value="2" <?php selected( $news_group, 2 ); ?>>News (Group 2)</option>
		</select>
		<?php
	}

	public function register_news_and_reviews_display_type_page() {
		add_submenu_page(
			'rlje-news-and-reviews',
			'News & Reviews Settings',
			'Settings',
			'manage_options',
			'rlje-news-and-reviews-settings',
			array( $this, 'acorntv_news_and_reviews_settings_page' )
		);
	}

	public function acorntv_news_and_reviews_settings_page() {
		?>
		<div class="wrap" id="news_reviews">
			<h1>News & Reviews Settings</h1>
			<form method="post" action="options.php">
		<?php

		settings_fields( 'rlje_news_and_reviews_news_settings_section' );

		do_settings_sections( 'rlje-news-and-reviews-settings' );

		submit_button();

		?>
			</form>
		</div>
		<?php
	}

	public function rlje_settings_sanitize_callback( $data ) {
		$country_code  = ( ! empty( rljeApiWP_getCountryCode() ) ) ? rljeApiWP_getCountryCode() : 'US';
		$transient_key = $this->transient_key . strtolower( $country_code );
		delete_transient( $transient_key );

		return $data;
	}
}

$rlje_news_and_reviews_settings = new RLJE_News_And_Reviews_Settings();

<?php

class RLJE_Reviews_Logo_Settings {

	private $prefix = 'acorntv_';
	protected $acorntv_news_options;

	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'admin_init', array( $this, 'reviews_logo_init_setup' ) );
		add_action( 'admin_init', array( $this, 'create_reviews_logo_settings' ) );
		add_action( 'admin_menu', array( $this, 'register_reviews_logo_settings_page' ) );
	}

	public function admin_enqueue_scripts( $hook ) {
		if ( 'news-reviews_page_rlje-reviews-logo-settings' === $hook ) {
			// wp_enqueue_style( 'jquery-ui-theme-css', plugin_url( '/lib/jquery/ui/jquery-ui.theme.min.css', __FILE__ ) );
			// wp_enqueue_script( 'jquery-ui-js', get_template_directory_uri() . '/lib/jquery/ui/jquery-ui.min.js' );
			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'jquery-ui-draggable' );
			wp_enqueue_media();

			// Versioning for cachebuster.
			$logo_js_version = date( 'ymd-Gis', filemtime( plugin_dir_path( __FILE__ ) . 'js/admin-reviews-logo-setting.js' ) );
			$logo_css_verion = date( 'ymd-Gis', filemtime( plugin_dir_path( __FILE__ ) . 'css/admin-reviews-logo-setting.css' ) );
			wp_enqueue_style( 'rlje-admin-reviews-logo-setting', plugins_url( 'css/admin-reviews-logo-setting.css', __FILE__ ), array(), $logo_css_verion );
			wp_enqueue_script( 'rlje-admin-reviews-logo-setting', plugins_url( 'js/admin-reviews-logo-setting.js', __FILE__ ), array( 'jquery-ui-core' ), $logo_js_version, true );
		}
	}

	public function reviews_logo_init_setup() {
		$this->acorntv_news_options = get_option( 'acorntv_news_options' );
		if ( empty( $this->acorntv_news_options ) ) {
			// Set default values to NewsOptions
			$news_options = array(
				array(
					'title' => 'Variety',
					'image' => plugins_url( 'img/variety.png', __FILE__ ),
				),
				array(
					'title' => 'Wall Street Journal',
					'image' => plugins_url( 'img/wsj_v4.png', __FILE__ ),
				),
				array(
					'title' => 'NY Daily News',
					'image' => plugins_url( 'img/dailynews_v1.png', __FILE__ ),
				),
				array(
					'title' => 'Hollywood Reporter',
					'image' => plugins_url( 'img/hollywoodreporter.png', __FILE__ ),
				),
				array(
					'title' => 'NPR',
					'image' => plugins_url( 'img/npr.png', __FILE__ ),
				),
				array(
					'title' => 'NY Times',
					'image' => plugins_url( 'img/nytimes_v4.png', __FILE__ ),
				),
			);
			update_option( 'acorntv_news_options', $news_options );
		}
	}

	public function create_reviews_logo_settings() {
		register_setting(
			$this->prefix . 'reviews_logo_settings',
			$this->prefix . 'news_options',
			null
		);

		add_settings_section(
			$this->prefix . 'news_options',
			'Reviews Logo Options',
			function() {
				print '<p>Add or Edit the Reviews Logo.</p><button id="addReviewOption">Add New Review Logo</button>';
			},
			'rlje-reviews-logo-settings'
		);

		// $news_opts = get_option( $this->prefix . 'news_options' );
		$news_opts = $this->acorntv_news_options;

		if ( is_array( $news_opts ) ) {
			foreach ( $news_opts as $key => $opt ) {
				add_settings_field(
					'news_option_fields_' . $key,
					'Review Logo <br/>Title and Image:',
					array( $this, 'acorntv_news_option_fields' ),
					'rlje-reviews-logo-settings',
					$this->prefix . 'news_options',
					array( $key )
				);
			}
		}
	}

	/**
	 * News options template.
	 *
	 * @param Array $keyParam Array parameters with a key to identify each field.
	 */
	public function acorntv_news_option_fields( $key_param ) {
		$key       = absint( $key_param[0] );
		$opt_name  = $this->prefix . 'news_options';
		$opt_value = get_option( $opt_name );
		$title     = ( ! empty( $opt_value[ $key ]['title'] ) ) ? esc_attr( $opt_value[ $key ]['title'] ) : '';
		$image     = ( ! empty( $opt_value[ $key ]['image'] ) ) ? esc_attr( $opt_value[ $key ]['image'] ) : '';
		?>
		<input class="rwsOptionTitle" name="acorntv_news_options[<?php echo esc_attr( $key ); ?>][title]" type="text" size="20" placeholder="Title..." value="<?php echo esc_attr( $title ); ?>" />
		<input class="rwsOptionSrc uploadImage" name="acorntv_news_options[<?php echo esc_attr( $key ); ?>][image]" type="text" size="70" placeholder="http://..." value="<?php echo esc_attr( $image ); ?>" />
		<button class="uploadBtn">Upload Image</button>
		<?php if ( $key > 0 ) : ?>
		<button class="removeReviewOption">Remove</button>
			<?php
		endif;
	}

	public function register_reviews_logo_settings_page() {
		add_submenu_page(
			'rlje-news-and-reviews',
			'Reviews Logo Options',
			'Logo Settings',
			'manage_options',
			'rlje-reviews-logo-settings',
			array( $this, 'acorntv_reviews_logo_settings_page' )
		);
	}

	/**
	 * Admin Review Logo Settings page template.
	 */
	public function acorntv_reviews_logo_settings_page() {
		?>
		<div class="wrap" id="reviews_logo_settings">
			<h1>Reviews Logo Settings</h1>
			<form method="post" action="options.php">
			<?php

			settings_fields( $this->prefix . 'reviews_logo_settings' );

			do_settings_sections( 'rlje-reviews-logo-settings' );

			submit_button();

			?>
			</form>
			<p class="notice">
				* After to save any change, you need go to <a href="<?php echo esc_url( get_admin_url() . 'admin.php?page=rlje-news-and-reviews' ); ?>">News and Reviews</a> and update the proper Review Logo fields then clicks on <b>Save Changes</b> to apply it.
			</p>
		</div>
		<?php
	}
}

$rlje_reviews_logo_settings = new RLJE_Reviews_Logo_Settings();

<?php
/*
 * News & Reviews Section - WP Administrator
 */

class RLJE_News_And_Reviews {

	private $transient_key = 'rlje_news_and_review_';
	protected $rlje_news;
	protected $rlje_reviews;

	public function __construct() {
		// add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
		// add_action( 'admin_init', array( $this, 'news_and_reviews_init_setup' ) );
		add_action( 'admin_menu', array( $this, 'register_news_and_reviews_page' ) );
		// add_action( 'rlje_homepage_middle_section_content', array( $this, 'display_news_and_reviews' ) );

		add_filter( 'rlje_redis_api_cache_groups', array( $this, 'add_news_and_review_cache_table_list' ) );

		require_once 'rlje-news-tab.php';
		require_once 'rlje-reviews-tab.php';
		require_once 'rlje-reviews-logo-settings.php';
		require_once 'rlje-news-and-reviews-settings.php';
	}

	public function admin_enqueue_scripts( $hook ) {
		if ( 'toplevel_page_rlje-news-and-reviews' === $hook ) {
			// wp_enqueue_style( 'jquery-ui-theme-css', plugin_url( '/lib/jquery/ui/jquery-ui.theme.min.css', __FILE__ ) );
			// wp_enqueue_script( 'jquery-ui-js', get_template_directory_uri() . '/lib/jquery/ui/jquery-ui.min.js' );
			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'jquery-ui-draggable' );
			wp_enqueue_media();

			// Versioning for cachebuster.
			$news_js_version = date( 'ymd-Gis', filemtime( plugin_dir_path( __FILE__ ) . 'js/admin-news-and-reviews.js' ) );
			$news_css_verion = date( 'ymd-Gis', filemtime( plugin_dir_path( __FILE__ ) . 'css/admin-news-and-reviews.css' ) );
			wp_enqueue_style( 'rlje-admin-news-and-reviews', plugins_url( 'css/admin-news-and-reviews.css', __FILE__ ), array(), $news_css_verion );
			wp_enqueue_script( 'rlje-admin-news-and-reviews', plugins_url( 'js/admin-news-and-reviews.js', __FILE__ ), array( 'jquery-ui-core', 'brightcove-public-player' ), $news_js_version, true );
		}
	}

	public function wp_enqueue_scripts() {
		if ( ! is_home() ) {
			return;
		}

		$news_js_version = date( 'ymd-Gis', filemtime( plugin_dir_path( __FILE__ ) . 'js/news-and-reviews.js' ) );
		$news_css_verion = date( 'ymd-Gis', filemtime( plugin_dir_path( __FILE__ ) . 'css/news-and-reviews.css' ) );

		wp_enqueue_style( 'rlje-news-and-reviews', plugins_url( 'css/news-and-reviews.css', __FILE__ ), array(), $news_css_verion );
		wp_enqueue_script( 'rlje-news-and-reviews', plugins_url( 'js/news-and-reviews.js', __FILE__ ), array( 'brightcove-public-player' ), $news_js_version, true );
	}

	public function add_news_and_review_cache_table_list( $cache_list ) {
		$cache_list[] = $this->transient_key;

		return $cache_list;
	}

	/**
	 * Register news and review page in admin menu page.
	 */
	public function register_news_and_reviews_page() {
		add_menu_page(
			'News & Reviews',
			'News & Reviews',
			'manage_options',
			'rlje-news-and-reviews',
			array( $this, 'news_and_reviews_page' ),
			'dashicons-feedback',
			6
		);
	}

	/**
	 * Admin News and Review page template.
	 */
	public function news_and_reviews_page() {
		?>
		<div class="wrap" id="news_reviews">
			<h1>News & Reviews</h1>
			<?php
			// We check if the page is visited by click on the tabs or on the menu button.
			// Then we get the active tab.
			$tab = ( ! empty( $_GET['tab'] ) ) ? $_GET['tab'] : '';
			switch ( $tab ) {
				case 'reviews-section':
					$active_tab    = 'reviews-section';
					$active_fields = 'rlje_news_and_reviews_reviews_section';
					break;
				default:
					$active_tab    = 'news-section';
					$active_fields = 'rlje_news_and_reviews_news_section';
			}
			?>

			<?php settings_errors(); ?>
			<!-- WordPress provides the styling for tabs. -->
			<h2 class="nav-tab-wrapper">
				<!-- when tab buttons are clicked we jump back to the same page but with a new parameter that represents the clicked tab. accordingly we make it active -->
				<a href="?page=rlje-news-and-reviews&tab=news-section" class="nav-tab
				<?php
				if ( 'news-section' === $active_tab ) {
					echo 'nav-tab-active'; }
				?>
				">News Section</a>
				<a href="?page=rlje-news-and-reviews&tab=reviews-section" class="nav-tab
				<?php
				if ( 'reviews-section' === $active_tab ) {
					echo 'nav-tab-active'; }
				?>
				">Reviews Section</a>
			</h2>
			<form method="post" action="options.php">
			<?php

			settings_fields( $active_fields );

			do_settings_sections( 'rlje-news-and-reviews' );

			submit_button();

			?>
			</form>
		</div>
		<?php
	}

	public function display_news_and_reviews() {
		if ( rljeApiWP_getCountryCode() ) {
			return;
		}

		$country_code  = ( ! empty( rljeApiWP_getCountryCode() ) ) ? rljeApiWP_getCountryCode() : 'US';
		$transient_key = $this->transient_key . strtolower( $country_code );
		$html          = get_transient( $transient_key );
		if ( false === $html ) :
			ob_start();

			$news_and_reviews_settings = get_option( 'rlje_news_and_reviews_settings' );
			$left_section              = ( intval( $news_and_reviews_settings['left_section'] ) ) ? intval( $news_and_reviews_settings['left_section'] ) : 1;
			$right_section             = ( intval( $news_and_reviews_settings['right_section'] ) ) ? intval( $news_and_reviews_settings['right_section'] ) : 0;
			$left    = $this->display_section( $left_section, false );
			$right = $this->display_section( $right_section, false );
		?>
		<section class="home-middle hidden-xs hidden-sm">
			<div class="container">
				<div class="row">
					<h4 class="subnav">News &amp; Reviews</h4>
					<!-- MARKETING PLACEHOLDER -->
					<?php // get_template_part( 'partials/homepage-section-marketing-placeholder' ); ?>
					<?php echo $left;// $this->display_news(); ?>

					<!-- LATEST NEWS -->
					<?php // get_template_part( 'partials/homepage-section-lastnews' ); ?>
					<?php echo $right; //$this->display_reviews(); ?>
				</div>
			</div>
		</section>
		<?php
			$html = ob_get_clean();
			set_transient( $transient_key, $html, 15 * MINUTE_IN_SECONDS );
		endif;
		echo $html;
	}

	public function display_section( $section = 1, $echo = true ) {
		switch ( intval( $section ) ) {
			case 2:
				$html = $this->get_news_html( $section );
				break;
			case 1:
				$html = $this->get_news_html( $section );
				break;
			default:
				$html = $this->get_reviews_html();
		}


		if ( (boolean) $echo ) {
			echo $html;
		} else {
			return $html;
		}
	}

	public function get_news_html( $news_group = 1 ) {
		$marketing_places = $this->get_display_carousels( $news_group );

		ob_start();
		require plugin_dir_path( __FILE__ ) . 'templates/carousel.php';
		$html = ob_get_clean();

		return $html;
	}

	public function get_reviews_html() {
		$html    = '';
		$reviews = get_option( 'rlje_news_and_reviews_reviews' );
		if ( false !== $reviews ) {
			ob_start();
			require plugin_dir_path( __FILE__ ) . 'templates/reviews.php';
			$html = ob_get_clean();
		}

		return $html;
	}

	protected function get_display_carousels( $news_group ) {
		$display_carousels = [];
		$news              = get_option( 'rlje_news_and_reviews_news' );
		if ( false !== $news ) {
			$carousels = $news[ $news_group ];
			foreach ( $carousels as $carousel ) {
				if ( ! empty( $carousel['src'] ) ) {
					$display_carousels[] = $carousel;
				}
			}
		}

		return $display_carousels;
	}

}

$rlje_news_and_reviews = new RLJE_News_And_Reviews();

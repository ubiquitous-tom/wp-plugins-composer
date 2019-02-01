<?php

class RLJE_Index_Page {

	protected $theme_text_settings;
	protected $theme_plugins_settings;
	protected $signup_promo_settings;
	protected $categories_home;
	protected $categories_items;
	protected $spotlight_name;
	protected $browse_id_list_availables;
	protected $home_sections;
	protected $nonce = 'atv#contentPage@token_nonce';

	public function __construct() {
		add_action( 'init', array( $this, 'initialize_index' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_ajax_paginate', array( $this, 'ajax_carousel_pagination' ) );
		add_action( 'wp_ajax_nopriv_paginate', array( $this, 'ajax_carousel_pagination' ) );
		add_action( 'rlje_homepage_middle_section_content', array( $this, 'display_signup_promotion' ) );
		add_action( 'rlje_homepage_middle_section_content', array( $this, 'display_home_loggedin_featured' ) );
		add_action( 'rlje_homepage_middle_section_content', array( $this, 'display_home_sections_by_index' ) );
		// add_action( 'rlje_homepage_middle_section_content', array( $this, 'display_home_sections' ) );
		// add_action( 'rlje_homepage_middle_section_content', array( $this, 'display_home_featured' ) );
		// add_action( 'rlje_homepage_middle_section_content', array( $this, 'display_home_spotlights' ) );
		add_action( 'rlje_homepage_bottom_section_content', array( $this, 'display_callout' ) );
	}

	public function initialize_index() {
		$this->theme_text_settings       = get_option( 'rlje_theme_text_settings' );
		$this->theme_plugins_settings    = get_option( 'rlje_theme_plugins_settings' );
		$this->signup_promo_settings     = get_option( 'rlje_signup_promo_settings' );
		$this->rlje_front_page_section   = get_option( 'rlje_front_page_section' );
		$this->categories_home           = rljeApiWP_getHomeItems( 'categories' );
		$this->categories_items          = ( isset( $this->categories_home->options ) ) ? $this->categories_home->options : array();
		$this->browse_id_list_availables = apply_filters( 'atv_get_browse_genres_availables', '' );

		// Default News And Reviews position to after 2 content stipes.
		$section_position_index = ( ! empty( $this->rlje_front_page_section['section_position_index'] ) ) ? $this->rlje_front_page_section['section_position_index'] : 2;

		// Add News And Reviews to the list.
		$section_position       = $this->categories_items;
		$news_and_reviews       = new stdClass();
		$news_and_reviews->id   = 'news-and-reviews';
		$news_and_reviews->name = 'News And Reviews';
		$news_and_reviews->type = 'news-and-reviews';

		// Create a placeholder array.
		$placeholder = array( 'placeholder' => 'Placeholder' );
		// Splice placeholder to the array then add News And Reviews.
		array_splice( $section_position, $section_position_index, 0, $placeholder );
		$section_position[ $section_position_index ] = $news_and_reviews;
		$this->home_sections['section_position']     = $section_position;

	}

	public function enqueue_scripts() {
		if ( is_home() || is_front_page() || 'browse' == get_query_var( 'pagename' ) ) {
			// $orderby_js_ver = date( 'ymd-Gis', filemtime( plugin_dir_path( __FILE__ ) . 'js/orderby.js' ) );
			$pagination_js_ver = date( 'ymd-Gis', filemtime( plugin_dir_path( __FILE__ ) . 'js/carousel-pagination.js' ) );

			// wp_enqueue_script( 'browse-orderby-js', plugins_url( 'js/orderby.js', __FILE__ ), array( 'jquery' ), $js_ver, true );
			// Special js hook to update carousel pagination image url to use the right one for umc.
			wp_enqueue_script( 'rlje-carousel-pagination-js', plugins_url( '/js/carousel-pagination.js', __FILE__ ), array( 'jquery' ), $pagination_js_ver, true );

			$browse_object = array(
				'ajax_url'  => admin_url( 'admin-ajax.php' ),
				'home_url'  => home_url(),
				'image_url' => rljeApiWP_getImageUrlFromServices( '' ),
				'token'     => wp_create_nonce( $this->nonce ),
			);
			wp_localize_script( 'rlje-carousel-pagination-js', 'carousel_pagination_object', $browse_object );
		}
	}

	public function ajax_carousel_pagination() {
		if ( ! wp_verify_nonce( $_POST['token'], $this->nonce ) ) {
			die( 'Action Not Allow!' );
		}

		$content = ( ! empty( $_POST['content'] ) ) ? $_POST['content'] : null;
		$page    = ( ! empty( $_POST['page'] ) ) ? $_POST['page'] : null;

		$data = rljeApiWP_getContentPageItems( $content, $page );
		wp_send_json_success( $data );
	}

	public function display_signup_promotion() {
		if ( ! empty( $_COOKIE['ATVSessionCookie'] ) && rljeApiWP_isUserActive( $_COOKIE['ATVSessionCookie'] ) ) {
			return;
		}

		$is_activated = ( ! empty( $this->signup_promo_settings['enable'] ) ) ? boolval( $this->signup_promo_settings['enable'] ) : false;
		if ( $is_activated ) {
			$pitch          = ( ! empty( $this->signup_promo_settings['pitch'] ) ) ? $this->signup_promo_settings['pitch'] : 'Start your FREE 7-day trial to watch the best in Black film & television with new and exclusive content added weekly! Download UMC on your favorite Apple and Android mobile devices or stream on Roku or Amazon Prime Video Channels. Drama, romance, comedy and much more - it’s all on UMC!';
			$about_video_id = ( ! empty( $this->signup_promo_settings['video_id'] ) ) ? $this->signup_promo_settings['video_id'] : '5180867444001';
			ob_start();
			require plugin_dir_path( __FILE__ ) . 'partials/section-signup-promotion.php';
			$html = ob_get_clean();
			echo $html;
		}
	}

	public function display_home_loggedin_featured() {
		if ( is_home() || is_front_page() ) :

			if ( ! empty( $_COOKIE['ATVSessionCookie'] ) && rljeApiWP_isUserActive( $_COOKIE['ATVSessionCookie'] ) ) :

				ob_start();

				$watch_spotlight_items = apply_filters( 'atv_get_watch_spotlight_items', 'recentlyWatched' );
				if ( 0 < count( $watch_spotlight_items ) ) :
					?>
		<section class="home-featured">
			<div class="container">
				<!-- RECENTLY WATCHED || WATCHLIST SPOTLIGHT-->
				<div class="col-md-12">
					<?php
					set_query_var( 'carousel-items', $watch_spotlight_items );
					// get_template_part( 'partials/section-generic-carousel' );
					require plugin_dir_path( __FILE__ ) . 'partials/section-generic-carousel.php';
					?>
				</div>
					<?php
				endif;

				$watchlist_spotlight_items = apply_filters( 'atv_get_watch_spotlight_items', 'watchlist' );
				if ( 0 < count( $watchlist_spotlight_items ) ) :
					?>
				<!-- RECENTLY WATCHED || WATCHLIST SPOTLIGHT-->
				<div class="col-md-12">
					<?php
					set_query_var( 'carousel-items', $watchlist_spotlight_items );
					// get_template_part( 'partials/section-generic-carousel' );
					require plugin_dir_path( __FILE__ ) . 'partials/section-generic-carousel.php';
					?>
				</div>
					<?php
				endif;
				?>
			</div>
		</section>
				<?php
				$html = ob_get_clean();
				echo $html;

			endif;

		endif;
	}

	public function display_home_sections_by_index() {
		if ( is_home() || is_front_page() ) {
			if ( ! empty( $this->home_sections['section_position'] ) ) {
				$news_and_reviews_displayed = false;
				foreach ( $this->home_sections['section_position'] as $section_position ) {
					if ( 'news-and-reviews' === $section_position->id ) {
						$this->display_home_news_and_reviews_section();
						$news_and_reviews_displayed = true;
					} elseif ( ! $news_and_reviews_displayed ) {
						$this->display_home_featured_section( $section_position );
					} else {
						$this->display_home_spotlight_section( $section_position );
					}
				}
			} else { // FALLBACK
				$this->display_home_featured();

				if ( class_exists( 'RLJE_News_And_Reviews' ) ) {
					global $rlje_news_and_reviews;
					$rlje_news_and_reviews->display_news_and_reviews();
				}

				$this->display_home_spotlights();
			}
		}
	}

	public function display_home_sections() {
		if ( is_home() || is_front_page() ) {
			if ( ! empty( $this->home_sections['section_position'] ) ) {
				foreach ( $this->home_sections['section_position'] as $section_position ) {
					switch ( $section_position->section_type ) {
						case 'news-and-reviews':
							$this->display_home_news_and_reviews_section();
							break;
						case 'home-featured':
							$this->display_home_featured_section( $section_position );
							break;
						case 'home-spotlight':
							$this->display_home_spotlight_section( $section_position );
							break;
						default:
							// Do nothing.
					}
				}
			} else { // FALLBACK
				$this->display_home_featured();

				if ( class_exists( 'RLJE_News_And_Reviews' ) ) {
					global $rlje_news_and_reviews;
					$rlje_news_and_reviews->display_news_and_reviews();
				}

				$this->display_home_spotlights();
			}
		}
	}

	public function display_home_news_and_reviews_section() {
		if ( class_exists( 'RLJE_News_And_Reviews' ) ) {
			global $rlje_news_and_reviews;
			// $rlje_news_and_reviews = new RLJE_News_And_Reviews();
			$rlje_news_and_reviews->display_news_and_reviews();
		}
	}

	public function display_home_featured_section( $home_featured ) {
		if ( is_home() || is_front_page() ) :

			ob_start();
			?>
		<section class="home-featured <?php echo esc_attr( $home_featured->id ); ?>">
			<div class="container">
				<?php
					$spotlight            = $home_featured;
					$this->spotlight_name = ( ! empty( $spotlight->name ) ) ? $spotlight->name : '';
				?>
				<!-- <?php echo strtoupper( $this->spotlight_name ); ?> SPOTLIGHT-->
				<div class="col-md-12">
				<?php
					set_query_var(
						'carousel-section', array(
							'title'           => $this->spotlight_name,
							'categoryObj'     => $spotlight,
							'showViewAllLink' => ( isset( $this->browse_id_list_availables[ $spotlight->id ] ) ),
						)
					);
					// get_template_part( 'partials/section-carousel-pagination' );
					require plugin_dir_path( __FILE__ ) . 'partials/section-carousel-pagination.php';
				?>
				</div>
			</div>
		</section>
			<?php
			$html = ob_get_clean();
			echo $html;

		endif;
	}

	public function display_home_spotlight_section( $home_spotlight ) {
		if ( is_home() || is_front_page() ) :

			ob_start();
			?>
		<section class="home-spotlights <?php echo esc_attr( $home_spotlight->id ); ?>">
			<div class="container">
				<?php
					$spotlight            = $home_spotlight;
					$this->spotlight_name = ( ! empty( $spotlight->name ) ) ? $spotlight->name : '';
				?>
				<!-- <?php echo strtoupper( $this->spotlight_name ); ?> SPOTLIGHT -->
				<div class="col-md-12">
					<?php
						set_query_var(
							'carousel-section', array(
								'title'           => $this->spotlight_name,
								'categoryObj'     => $spotlight,
								'showViewAllLink' => ( isset( $this->browse_id_list_availables[ $spotlight->id ] ) ),
							)
						);
					// get_template_part( 'partials/section-carousel-pagination' );
					require plugin_dir_path( __FILE__ ) . 'partials/section-carousel-pagination.php';
					?>
				</div>
			</div>
		</section>
			<?php
			$html = ob_get_clean();
			echo $html;
		endif;
	}

	// FALLBACK FOR HOME SECTION POSITIONING - HOME FEATURED
	public function display_home_featured() {
		if ( is_home() || is_front_page() ) :
			// if ( empty( $this->home_sections['section_position'] ) ) {
			// return;
			// }
			$home_featured = [];
			// foreach ( $this->home_sections['section_position'] as $section_position ) {
			// if ( 'news-and-reviews' !== $section_position->id ) {
			// $home_featured[] = $section_position;
			// } else {
			// break;
			// }
			// }
			$categories_items = $this->categories_items;
			$home_featureds   = array_splice( $categories_items, 0, 2 );

			ob_start();
			?>
		<section class="home-featured">
			<div class="container">
			<?php
			// for ( $i = 0; $i < 2 && isset( $home_featured[ $i ] ); $i++ ) :
			foreach ( $home_featureds as $home_featured ) :
				// $spotlight            = $home_featured[ $i ];
				$spotlight            = $home_featured;
				$this->spotlight_name = ( ! empty( $spotlight->name ) ) ? $spotlight->name : '';
				?>
				<!-- <?php echo strtoupper( $this->spotlight_name ); ?> SPOTLIGHT-->
				<div class="col-md-12">
				<?php
					set_query_var(
						'carousel-section', array(
							'title'           => $this->spotlight_name,
							'categoryObj'     => $spotlight,
							'showViewAllLink' => ( isset( $this->browse_id_list_availables[ $spotlight->id ] ) ),
						)
					);
					// get_template_part( 'partials/section-carousel-pagination' );
					require plugin_dir_path( __FILE__ ) . 'partials/section-carousel-pagination.php';
				?>
				</div>
			<?php endforeach; // endfor; ?>
			</div>
		</section>
			<?php
			$html = ob_get_clean();
			echo $html;
		endif;
	}

	// FALLBACK FOR HOME SECTION POSITIONING - HOME SPOTLIGHTS
	public function display_home_spotlights() {
		if ( is_home() || is_front_page() ) :
			// if ( empty( $this->home_sections['section_position'] ) ) {
			// return;
			// }
			$home_spotlights = [];
			// $is_found        = false;
			// foreach ( $this->home_sections['section_position'] as $section_position ) {
			// if ( 'news-and-reviews' === $section_position->id || $is_found ) {
			// $is_found          = true;
			// $home_spotlights[] = $section_position;
			// }
			// }
			$categories_items = $this->categories_items;
			$home_spotlights  = array_splice( $categories_items, 2 );

			ob_start();
			?>
		<section class="home-spotlights">
			<div class="container">
			<?php
			// for ( $i = 2; $i < count( $home_spotlights ); $i++ ) :
			foreach ( $home_spotlights as $home_spotlight ) :
				// $spotlight            = $home_spotlights[ $i ];
				$spotlight            = $home_spotlight;
				$this->spotlight_name = ( ! empty( $spotlight->name ) ) ? $spotlight->name : '';
				?>
				<!-- <?php echo strtoupper( $this->spotlight_name ); ?> SPOTLIGHT -->
				<div class="col-md-12">
				<?php
					set_query_var(
						'carousel-section', array(
							'title'           => $this->spotlight_name,
							'categoryObj'     => $spotlight,
							'showViewAllLink' => ( isset( $this->browse_id_list_availables[ $spotlight->id ] ) ),
						)
					);
					// get_template_part( 'partials/section-carousel-pagination' );
					require plugin_dir_path( __FILE__ ) . 'partials/section-carousel-pagination.php';
				?>
				</div>
				<?php endforeach; // endfor; ?>
			</div>
		</section>
			<?php
			$html = ob_get_clean();
			echo $html;
		endif;
	}

	public function display_callout() {
		if ( is_home() || is_front_page() ) :
			$environment  = apply_filters( 'atv_get_extenal_subdomain', '' );
			$is_activated = ( ! intval( $this->theme_plugins_settings['home_callout'] ) ) ? intval( $this->theme_plugins_settings['home_callout'] ) : 1;
			if ( ! $is_activated ) {
				return;
			}

			$callout_one           = ( ! empty( $this->theme_text_settings['callout']['one'] ) ) ? $this->theme_text_settings['callout']['one'] : array();
			$callout_one_text      = ( ! empty( $callout_one['text'] ) ) ? $callout_one['text'] : 'Available on Roku, Apple TV, Samsung Smart TV, iPhone, iPad, web and more.';
			$callout_one_link      = ( ! empty( $callout_one['link'] ) ) ? $callout_one['link'] : home_url( '/' );
			$callout_one_link_text = ( ! empty( $callout_one['link_text'] ) ) ? $callout_one['link_text'] : 'Learn More';

			$callout_two           = ( ! empty( $this->theme_text_settings['callout']['two'] ) ) ? $this->theme_text_settings['callout']['two'] : array();
			$callout_two_text      = ( ! empty( $callout_two['text'] ) ) ? $callout_two['text'] : 'Over 1,800 hours of programming, including 60 shows you won\'t find anywhere else.';
			$callout_two_link      = ( ! empty( $callout_two['link'] ) ) ? $callout_two['link'] : home_url( '/' );
			$callout_two_link_text = ( ! empty( $callout_two['link_text'] ) ) ? $callout_two['link_text'] : 'Start Your Free Trial';

			ob_start();
			?>
		<section class="home-callout">
			<div class="container">
				<div class="col-md-12 home-callout-body">
					<div class="col-md-6" id="border-carousel">
						<div class="home-callout-content">
							<img src="<?php echo esc_url( get_template_directory_uri() . '/img/devices-icon.png' ); ?>" id="home-devices-img">
							<p class="home-callout-description"><?php echo esc_html( $callout_one_text ); ?></p>
							<a href="<?php echo esc_url( $callout_one_link ); ?>">
								<button><?php echo esc_html( $callout_one_link_text ); ?></button>
							</a>
						</div>
					</div>

					<div class="col-md-6">
						<div class="home-callout-content">
							<img src="<?php echo esc_url( get_template_directory_uri() . '/img/signup-icon.png' ); ?>" id="home-trial-img">
							<p class="home-callout-description"><?php echo esc_html( $callout_two_text ); ?></p>
							<a href="<?php echo esc_url( $callout_two_link ); ?>">
								<button><?php echo esc_html( $callout_two_link_text ); ?></button>
							</a>
						</div>
					</div>
				</div>
			</div>
		</section>
			<?php
			$html = ob_get_clean();
			echo $html;
		endif;
	}
}

$rlje_index_page = new RLJE_Index_Page();

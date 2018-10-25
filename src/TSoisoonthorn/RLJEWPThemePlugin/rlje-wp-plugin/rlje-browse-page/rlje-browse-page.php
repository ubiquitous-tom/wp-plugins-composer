<?php

class RLJE_Browse_Page {

	protected $nonce = 'atv#contentPage@token_nonce';

	public function __construct() {
		add_action( 'init', array( $this, 'add_browse_rewrite_rules' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_ajax_paginate', array( $this, 'ajax_carousel_pagination' ) );
		add_action( 'wp_ajax_nopriv_paginate', array( $this, 'ajax_carousel_pagination' ) );
		add_action( 'template_redirect', array( $this, 'browse_template_redirect' ) );
		// add_action( 'add_meta_boxes', array( $this, 'add_browse_template_meta_box' ) );
		add_action( 'rlje_display_watchlist_section_on_browse_page', array( $this, 'display_watchlist_section' ) );

		// add_filter( 'query_vars', array( $this, 'add_browse_query_vars' ) );
		add_filter( 'body_class', array( $this, 'browse_body_class' ) );
		add_filter( 'theme_page_templates', array( $this, 'add_browse_page_template' ), 10, 4 );
	}

	public function add_browse_rewrite_rules() {
		add_rewrite_rule( 'browse([^/]+)/?', 'index.php?pagename=browse', 'top' );
		// add_rewrite_rule( '^browse/([^/]+)/?', 'index.php?pagename=browse&browse_type=$matches[1]', 'top' );
		// add_rewrite_tag( '%browse_type%', '([^&]+)' );

		// We are using `section` query_var set in function.php for now.
		add_rewrite_rule( 'browse/([^/]+)/?', 'index.php?pagename=browse&section=$matches[1]', 'top' );
		// add_rewrite_tag( '%section%', '([^&]+)' ); // What we want to use in the future.
	}

	public function enqueue_scripts() {
		$pagename = get_query_var( 'pagename' );
		if ( 'browse' !== $pagename ) {
			return;
		}

		$orderby_js_ver = date( 'ymd-Gis', filemtime( plugin_dir_path( __FILE__ ) . 'js/orderby.js' ) );
		wp_enqueue_script( 'browse-orderby-js', plugins_url( 'js/orderby.js', __FILE__ ), array( 'jquery' ), $orderby_js_ver, true );
	}

	public function ajax_carousel_pagination() {
		if ( ! wp_verify_nonce( $_POST['token'], $this->nonce ) ) {
			die( 'Action Not Allow!' );
		}

		$content = ( ! empty( $_POST['content'] ) ) ? $_POST['content'] : null;
		$page = ( ! empty( $_POST['page'] ) ) ? $_POST['page'] : null;

		$data = rljeApiWP_getContentPageItems( $content, $page );
		wp_send_json_success( $data );
	}

	public function browse_template_redirect() {
		if ( 'browse' === get_query_var( 'pagename' ) ) {
			$active_section = get_query_var( 'section' );
			$is_user_active = false;
			$atv_session_cookie = null;
			$is_order_by_enabled = true;
			if ( ! empty( $_COOKIE['ATVSessionCookie'] ) && rljeApiWP_isUserActive( $_COOKIE['ATVSessionCookie'] ) ) {
				$is_user_active = true;
				$atv_session_cookie         = $_COOKIE['ATVSessionCookie'];
			}

			$list_sections = [
				'all' => 'All',
				// 'comingsoon' => 'Coming Soon',
			];
			if ( $is_user_active ) {
				$list_sections = array_merge(
					array(
						'recentlywatched' => 'Recently Watched',
						'yourwatchlist'   => 'My Watchlist',
					),
					$list_sections
				);
			}
			$guide_items = array();

			$guide_obj = rljeApiWP_getBrowseItems( 'guide' );
			if ( ! empty( $guide_obj->options ) && is_array( $guide_obj->options ) ) {
				$guide_items = $guide_obj->options;
				foreach ( $guide_obj->options as $guide ) {
					$browse_id                   = apply_filters( 'atv_get_browse_section_id', $guide->id );
					$list_sections[ $browse_id ] = $guide->name;
				}
			}
			if ( empty( $active_section ) ) {
				$is_order_by_enabled = false;
			}

			if( isset( $list_sections[ $active_section ] ) || empty( $active_section ) ) {
				if( in_array( $active_section, [ 'recentlywatched', 'yourwatchlist' ] ) ) {
					if ( !$is_user_active ) {
						wp_redirect( home_url( 'browse' ), 303 );
						exit();
					}
					$is_order_by_enabled = false;
				}

				global $wp_query;
				// Prevent internal 404 on custome search page because of template_redirect hook.
				$wp_query->is_404     = false;
				$wp_query->is_page    = true;
				status_header( 200 );

				ob_start();
				require_once plugin_dir_path( __FILE__ ) . 'templates/browse.php';
				$html = ob_get_clean();
				echo $html;
				exit();
			}
		}
	}

	public function add_browse_template_meta_box() {
		add_meta_box( 'browse_template_id', 'Custom Template', array( $this, 'browse_template_meta_box' ), 'page', 'side', 'default' );
	}

	public function browse_template_meta_box( $post, $post_id ) {
		$templates[] = [
			'path' => 'templates/browse.php',
			'name' => 'Browse Template',
			'value' => 'Browse',
		];
		$page_template = get_post_meta($post->ID, '_rlje_custom_page_template', true);
		?>
		<p class="post-attributes-label-wrapper">
			<label for="rlje-custom-page-template" class="post-attributes-label">Custom Template</label>
		</p>
		<select name="rlje_custom_page_template" id="rlje-custom-page-template" class="widefat">
			<option value="">Custom Template</option>
			<?php foreach ( $templates as $template ) : ?>
			<option value="<?php echo esc_attr( $template['value'] ); ?>" <?php selected( $current_page_template, $template['path'] ); ?>><?php echo esc_html( $template['name']) ?></option>
			<?php endforeach; ?>
		</select>
		<p>Use this Custom Template to set up <i>RLJE specific page</i>. Such as "Browse" page.</p>
		<?php
	}

	public function display_watchlist_section() {
		if ( ! empty( $_COOKIE['ATVSessionCookie'] ) && rljeApiWP_isUserActive( $_COOKIE['ATVSessionCookie'] ) ) :
			$watch_spotlight_items = apply_filters( 'atv_get_watch_spotlight_items', 'recentlyWatched' );
			if ( 0 < count( $watch_spotlight_items ) ) :
				?>
				<!-- RECENTLY WATCHED || WATCHLIST SPOTLIGHT-->
				<div class="col-md-12">
				<?php
					$browse_id = 'recentlywatched';
					set_query_var( 'carousel-items', $watch_spotlight_items );
					require plugin_dir_path( __FILE__ ) . 'partials/section-generic-carousel.php';
				?>
				</div>
				<?php
			endif;
		endif;
	}

	public function browse_body_class( $classes ) {
		$pagename = get_query_var( 'pagename' );
		if ( 'browse' === $pagename ) {
			// $classes[] = $pagename;
			$classes[] = 'page-' . $pagename;

			$section = get_query_var( 'section' );
			if ( ! empty( $section ) ) {
				$classes[] = $pagename . '-' . $section;
				$classes[] = 'page-' . $section;
				// $classes[] = $section;
			}
		}

		return $classes;
	}

	public function add_browse_page_template( $post_templates, $wp_theme, $post, $post_type ) {
		if ( 'page' === $post_type ) {
			$post_templates['templates/browse.php'] = 'Browse';
		}

		return $post_templates;
	}
}

$rlje_browse_page = new RLJE_Browse_Page();

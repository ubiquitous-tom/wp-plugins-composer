<?php

class RLJE_UMC_Theme {

	protected $theme = 'umc';

	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_filter( 'rlje_main_favicon_url', array( $this, 'umc_main_favicon_url' ) );
		add_filter( 'rlje_theme_header_logo', array( $this, 'theme_header_logo' ), 11 );
		add_filter( 'rlje_json_ld_header', array( $this, 'umc_json_ld_header' ) );
		add_filter( 'atv_add_img_and_href', array( $this, 'umc_add_img_and_href' ) );
		add_filter( 'rlje_franchise_artwork', array( $this, 'umc_franchise_artwork_image_h' ), 10, 2 );
		add_filter( 'rlje_title', array( $this, 'umc_title_format' ) );
		add_filter( 'rlje_seasons_dropdown_filter_by_text', array( $this, 'umc_seasons_dropdown_filter_by_text' ) );
		add_filter( 'rlje_front_page_homepage_hero_left_arrow', array( $this, 'umc_homepage_hero_left_arrow' ) );
		add_filter( 'rlje_front_page_homepage_hero_right_arrow', array( $this, 'umc_homepage_hero_right_arrow' ) );
		add_filter( 'rlje_carousel_hero_responsive_images', array( $this, 'umc_carousel_hero_responsive_images' ), 10, 4 );
		// add_filter( 'rlje_app_smartbanner', array( $this, 'umc_app_smartbanner' ) );

		require_once plugin_dir_path( __FILE__ ) . 'franchise/umc-franchise-page.php';
	}

	public function enqueue_scripts( $hook ) {
		wp_enqueue_style( 'google-webfont-nunito', '//fonts.googleapis.com/css?family=Nunito:300,400,400i,600,600i,700,700i' );

		$css_ver = date( 'ymd-Gis', filemtime( plugin_dir_path( __FILE__ ) . 'css/style.css' ) );
		wp_enqueue_style( 'rlje-umc-theme', plugins_url( 'css/style.css', __FILE__ ), array( 'main_style_css' ), $css_ver );

		$umc_carousel_pagination_js_ver = date( 'ymd-Gis', filemtime( plugin_dir_path( __FILE__ ) . 'js/umc-carousel-pagination.js' ) );

		// Special js hook to update carousel pagination image url to use the right one for umc.
		wp_enqueue_script( 'rlje-umc-carousel-pagination-js', plugins_url( 'js/umc-carousel-pagination.js', __FILE__ ), array( 'rlje-carousel-pagination-js' ), $umc_carousel_pagination_js_ver, true );
	}

	public function umc_main_favicon_url( $favicon_url ) {
		$favicon_url = plugins_url( 'img/favicon.ico', __FILE__ );

		return $favicon_url;
	}

	public function theme_header_logo( $logo_url ) {
		$logo_url = plugin_dir_url( __FILE__ ) . 'img/logo.png';

		return $logo_url;
	}

	public function umc_json_ld_header( $json_ld ) {
		$json_ld['image']     = plugins_url( 'img/logo.png', __FILE__ );
		$json_ld['publisher'] = [
			'logo'  => [
				'url'    => plugins_url( 'img/logo.png', __FILE__ ),
			],
		];

		return $json_ld;
	}

	public function umc_add_img_and_href( $item ) {
		if ( ! isset( $item->href ) ) {
			$id         = ( isset( $item->id ) ) ? $item->id : $item->franchiseID;
			$item->href = $id;
		}

		if ( ! isset( $item->img ) ) {
			// $img = ( isset( $item->image ) ) ? $item->image : $item->href . '_avatar';
			// $img       = ( isset( $item->image_h ) ) ? $item->image_h : $item->image;
			$img       = ( isset( $item->image_s ) ) ? $item->image_s : $item->image_h;
			$item->img = rljeApiWP_getImageUrlFromServices( $img );
		}

		return $item;
	}

	public function umc_franchise_artwork_image_h( $item_image, $item ) {
		if ( ! empty( $item->image_h ) ) {
			$item_image = $item->image_h;
		}

		// For Recently Watch page - /browse/recentlywatch/
		if ( ! empty( $item->image_s ) ) {
			$item_image = $item->image_s;
		}

		return $item_image;
	}

	public function umc_title_format( $title ) {
		if ( ! empty( get_query_var( 'franchise_id' ) ) ) {
			$franchise = rljeApiWP_getFranchiseById( get_query_var( 'franchise_id' ) );
			if ( is_object( $franchise ) ) {
				$meta_title = htmlentities( $franchise->name );
				$meta_descr = htmlentities( $franchise->longDescription );
			}

			$title['title'] = $meta_title;
			$title['tagline'] = $title['site'] . ' - ' . get_bloginfo( 'description' );
			unset( $title['site'] );
		}

		return $title;
	}

	public function umc_seasons_dropdown_filter_by_text( $filter_by_text ) {
		$filter_by_text = 'Filter By Seasons';

		return $filter_by_text;
	}

	public function umc_homepage_hero_left_arrow( $left_arrow_url ) {
		$left_arrow_url =  plugins_url( 'img/hero-left.png', __FILE__ );

		return $left_arrow_url;
	}

	public function umc_homepage_hero_right_arrow( $right_arrow_url ) {
		$right_arrow_url =  plugins_url( 'img/hero-right.png', __FILE__ );

		return $right_arrow_url;
	}

	public function umc_carousel_hero_responsive_images( $slide_image, $image_link, $type, $image ) {
		ob_start();
		?>
		<img title="" alt="hero image" class="hero-img visible-xs" src="<?php echo rljeApiWP_getImageUrlFromServices( $image_link . '?t=Web3&h=288' ); ?>">
		<img title="" alt="hero image" class="hero-img hidden-xs" src="<?php echo rljeApiWP_getImageUrlFromServices( $image_link . '?t=Web3' ); ?>">
		<?php
		$slide_image = ob_get_clean();

		return $slide_image;
	}

	// public function umc_app_smartbanner( $smart_banner ) {
	// 	$smart_banner['title'] = 'UMC - Urban Movie Channel';
	// 	$smart_banner['author'] = 'RLJ Entertainment, Inc.';
	// 	$smart_banner['icon-apple'] = plugins_url( 'img/logo.png', __FILE__ );
	// 	$smart_banner['icon-google'] = plugins_url( 'img/logo.png', __FILE__ );
	// 	$smart_banner['button-url-apple'] = 'https://itunes.apple.com/us/app/umc-best-in-black-film-tv/id1032488115?mt=8';
	// 	$smart_banner['button-url-google'] = 'https://play.google.com/store/apps/details?id=com.rljentertainment.umc.android&hl=en_US';

	// 	return $smart_banner;
	// }
}

$rlje_umc_theme = new RLJE_UMC_Theme();

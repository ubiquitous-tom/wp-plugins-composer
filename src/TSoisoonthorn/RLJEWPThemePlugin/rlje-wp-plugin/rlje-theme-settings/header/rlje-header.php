<?php

class RLJE_Header {

	protected $theme_settings;
	protected $sailthru;
	protected $google;
	protected $apple;
	protected $tealium;
	// protected $franchise;


	public function __construct() {
		$this->sailthru = get_option( 'rlje_sailthru_settings' );
		$this->google   = get_option( 'rlje_google_settings' );
		$this->apple    = get_option( 'rlje_apple_settings' );
		$this->tealium  = get_option( 'rlje_tealium_settings' );

		// add_action( 'wp_head', array( $this, 'initialize_headers' ) );
		add_action( 'wp_head', array( $this, 'add_sailthru_script' ) );
		add_action( 'wp_head', array( $this, 'add_tealium_script' ) );
		add_action( 'wp_head', array( $this, 'add_google_analytics_script' ) );
		add_action( 'wp_head', array( $this, 'add_google_play_app_meta_tag' ), 0 );
		add_action( 'wp_head', array( $this, 'add_google_site_verification_meta_tag' ), 0 );
		add_action( 'wp_head', array( $this, 'add_apple_itunes_app_meta_tag' ), 0 );
		// add_action( 'wp_head', array( $this, 'rlje_wp_title' ) );
		// add_filter( 'wp_title', array( $this, 'rlje_wp_title' ), 10, 3 );
		add_filter( 'document_title_separator', array( $this, 'rlje_title_separator' ) );
		add_filter( 'document_title_parts', array( $this, 'rlje_title_parts' ) );
		add_action( 'rlje_tag_mananger_iframe', array( $this, 'add_google_tag_manager_frame' ) );
		add_action( 'wp_head', array( $this, 'add_description_meta_tag' ), 1 );
	}

	// public function initialize_headers() {
	// 	$this->franchise = rljeApiWP_getFranchiseById( get_query_var( 'franchise_id' ) );
	// }

	public function add_sailthru_script() {
		$sailthru_customer_id = ( ! empty( $this->sailthru['customer_id'] ) ) ? $this->sailthru['customer_id'] : '';
		if ( ! empty( $sailthru_customer_id ) ) : ?>
			<script src="https://ak.sail-horizon.com/spm/spm.v1.min.js"></script>
			<script>Sailthru.init({ customerId: '<?php echo $sailthru_customer_id; ?>' });</script>
			<?php
		endif;
	}

	public function add_tealium_script() {
		$tealium_id = ( ! empty( $this->tealium['tealium_id'] ) ) ? $this->tealium['tealium_id'] : '';
		if ( ! empty( $tealium_id ) ) :
			?>
		<script>
			var versaTag = {};
			versaTag.id = "<?php echo esc_attr( $tealium_id ); ?>";
			versaTag.sync = 0;
			versaTag.dispType = "js";
			versaTag.ptcl = "HTTPS";
			versaTag.bsUrl = "bs.serving-sys.com/BurstingPipe";
			//VersaTag activity parameters include all conversion parameters including custom parameters and Predefined parameters. Syntax: "ParamName1":"ParamValue1", "ParamName2":"ParamValue2". ParamValue can be empty.
			versaTag.activityParams = {
				//Predefined parameters:
				"Session":""
				//Custom parameters:
			};
			//Static retargeting tags parameters. Syntax: "TagID1":"ParamValue1", "TagID2":"ParamValue2". ParamValue can be empty.
			versaTag.retargetParams = {};
			//Dynamic retargeting tags parameters. Syntax: "TagID1":"ParamValue1", "TagID2":"ParamValue2". ParamValue can be empty.
			versaTag.dynamicRetargetParams = {};
			// Third party tags conditional parameters and mapping rule parameters. Syntax: "CondParam1":"ParamValue1", "CondParam2":"ParamValue2". ParamValue can be empty.
			versaTag.conditionalParams = {};
		</script>
		<script id="ebOneTagUrlId" src="https://secure-ds.serving-sys.com/SemiCachedScripts/ebOneTag.js"></script>
		<noscript>
			<iframe src="https://bs.serving-sys.com/BurstingPipe?cn=ot&amp;onetagid=6377&amp;ns=1&amp;activityValues=$$Session=[Session]$$&amp;retargetingValues=$$$$&amp;dynamicRetargetingValues=$$$$&amp;acp=$$$$&amp;" style="display:none;width:0px;height:0px"></iframe>
		</noscript>
			<?php
		endif;
	}

	public function add_google_analytics_script() {
		$google_analytics_id = ( ! empty( $this->google['google_analytics_id'] ) ) ? $this->google['google_analytics_id'] : '';
		if ( ! empty( $google_analytics_id ) ) :
			?>
		<!-- Google Tag Manager Script -->
		<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
		new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
		j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
		'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
		})(window,document,'script','dataLayer','<?php echo esc_attr( $google_analytics_id ); ?>');</script>
		<!-- End Google Tag Manager Script -->
			<?php
		endif;
	}

	public function add_google_play_app_meta_tag() {
		$google_play_app_id = ( ! empty( $this->google['google_play_app_id'] ) ) ? $this->google['google_play_app_id'] : '';
		if ( ! empty( $google_play_app_id ) ) :
			?>
			<meta name="google-play-app" content="<?php echo esc_attr( $google_play_app_id ); ?>">
			<?php
		endif;
	}

	public function add_google_site_verification_meta_tag() {
		$google_site_verification_id = ( ! empty( $this->google['google_site_verification_id'] ) ) ? $this->google['google_site_verification_id'] : '';
		if ( ! empty( $google_site_verification_id ) ) :
			?>
			<meta name="google-site-verification" content="<?php echo esc_attr( $google_site_verification_id ); ?>">
			<?php
		endif;
	}

	public function add_apple_itunes_app_meta_tag() {
		$apple_itunes_app_id = ( ! empty( $this->apple['apple_itunes_app_id'] ) ) ? $this->apple['apple_itunes_app_id'] : '';
		if ( ! empty( $apple_itunes_app_id ) ) :
			?>
			<meta name="apple-itunes-app" content="<?php echo esc_attr( $apple_itunes_app_id ); ?>">
			<?php
		endif;
	}

	public function rlje_title_separator( $sep ) {
		$sep = '|';

		return $sep;
	}

	public function rlje_title_parts( $title ) {
		global $wp;
		list( $franchise_id, $season_id, $episode_id ) = array_pad( explode( '/', $wp->request ), 3, '' );
		$real_franchise = ( ! empty( $franchise_id ) ) ? rljeApiWP_getFranchiseById( $franchise_id ) : false;
		if ( is_object( $real_franchise ) ) {
			$meta_title = htmlentities( $real_franchise->name );
			$meta_descr = htmlentities( $real_franchise->longDescription );
			$title['title'] = $meta_title;
		}
		return $title;
	}

	public function add_google_tag_manager_frame() {
		if ( ! empty( $this->google['google_analytics_id'] ) ) {
			?>
			<!-- Google Tag Manager iFrame -->
			<noscript><iframe src="//www.googletagmanager.com/ns.html?id=<?php echo esc_attr( $this->google['google_analytics_id'] ); ?>"
			height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
			<!-- End Google Tag Manager iFrame -->
			<?php
		}
	}

	public function add_description_meta_tag() {
		$meta_descr = '';
		global $wp;
		list( $franchise_id, $season_id, $episode_id ) = array_pad( explode( '/', $wp->request ), 3, '' );
		$real_franchise = ( ! empty( $franchise_id ) ) ? rljeApiWP_getFranchiseById( $franchise_id ) : false;
		if ( is_object( $real_franchise ) ) {
			$meta_descr = htmlentities( $real_franchise->longDescription );
		}
		$meta_descr = apply_filters( 'rlje_description_meta_tag_content', $meta_descr );

		if ( ! empty( $meta_descr ) ) :
		?>
		<meta name="description" content="<?php echo esc_html( $meta_descr ); ?>">
		<?php
		endif;
	}
}

$rlje_header = new RLJE_Header();

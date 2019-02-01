<?php
class RLJE_Store_Page {
	private $api_helper;
	private $stripe_key;
	public function __construct() {
		$this->api_helper = new RLJE_api_helper();
		add_action( 'init', [ $this, 'add_browse_rewrite_rules' ] );
		add_action( 'wp', [ $this, 'fetch_stripe_key' ] );
		add_action( 'template_redirect', [ $this, 'browse_template_redirect' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

		add_action( 'wp_ajax_purchase_gift', [ $this, 'purchase_gift' ] );
		add_action( 'wp_ajax_nopriv_purchase_gift', [ $this, 'purchase_gift' ] );
	}

	public function add_browse_rewrite_rules() {
		add_rewrite_rule( '^gift/?', 'index.php?pagename=gift', 'top' );
	}

	public function browse_template_redirect() {
		global $wp_query;
		$pagename = get_query_var( 'pagename' );
		if ( 'gift' === $pagename ) {
			status_header( 200 );
			$wp_query->is_404  = false;
			$wp_query->is_page = true;
			// $wp_query->is_archive = true;
			ob_start();
			require_once plugin_dir_path( __FILE__ ) . 'templates/gift.php';
			$html = ob_get_clean();
			echo $html;
			exit();
		}
	}

	public function enqueue_scripts() {
		if ( in_array( get_query_var( 'pagename' ), [ 'gift' ] ) ) {
			wp_enqueue_style( 'store-main-style', plugins_url( 'css/style.css', __FILE__ ) );
			wp_enqueue_script( 'store-gift-script', plugins_url( 'js/gift.js', __FILE__ ), [ 'jquery-core', 'blueimp-javascript-templates', 'stripe-js' ] );
			wp_localize_script(
				'store-gift-script', 'gift_vars', [
					'ajax_url'   => admin_url( 'admin-ajax.php' ),
					'site_name'  => get_bloginfo( 'name' ),
					'stripe_key' => $this->stripe_key,
				]
			);
		}
	}

	public function fetch_stripe_key() {
		if ( in_array( get_query_var( 'pagename' ), [ 'gift' ] ) ) {
			$this->stripe_key = $this->api_helper->fetch_stripe_key();
		}
	}

	public function purchase_gift() {
		$response     = [
			'success' => false,
			'error'   => '',
		];
		$params       = [
			'Credentials'    => [
				'Username' => $_POST['email'],
			],
			'App'            => [
				'AppVersion' => 'UMCTV.Version.2.0',
			],
			'PaymentMethod'  => [
				'NameOnAccount' => $_POST['card_name'],
				'StripeToken'   => $_POST['stripe_token'],
			],
			'BillingAddress' => [
				'Zip'     => $_POST['zip'],
				'Country' => $_POST['country'],
			],
			'Gift'           => [
				'Quantity' => $_POST['quantity'],
			],
		];
		$api_response = $this->api_helper->hit_api( $params, 'giftpayment', 'POST' );
		$api_response = json_decode( wp_remote_retrieve_body( $api_response ), true );
		if ( isset( $api_response['GiftCodes'] ) ) {
			$response['success']  = true;
			$response['order_id'] = $api_response['OrderID'];
			$response['codes']    = $api_response['GiftCodes'];
		} else {
			$response['error'] = 'Unable to complete the purchase. Please contact support.';
		}
		wp_send_json( $response );
	}
}

$rlje_store_page = new RLJE_Store_Page();

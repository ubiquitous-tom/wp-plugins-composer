<?php
class RLJE_signup_page {

	private $api_helper;
	private $stripe_key;
	private $membership_plans;

	public function __construct() {
		$this->api_helper            = new RLJE_api_helper();
		$this->membership_plans      = [
			'yearly'  => [
				'Term'     => 12,
				'TermType' => 'MONTH',
			],
			'monthly' => [
				'Term'     => 30,
				'TermType' => 'DAY',
			],
		];

		add_action( 'init', array( $this, 'add_signup_rewrite_rule' ) );
		add_action( 'wp', [ $this, 'fetch_stripe_key' ] );
		add_action( 'template_redirect', array( $this, 'signup_template_redirect' ) );

		add_action( 'wp_ajax_initialize_account', [ $this, 'initialize_account' ] );
		add_action( 'wp_ajax_nopriv_initialize_account', [ $this, 'initialize_account' ] );

		add_action( 'wp_ajax_create_membership', [ $this, 'create_membership' ] );
		add_action( 'wp_ajax_nopriv_create_membership', [ $this, 'create_membership' ] );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}


	public function fetch_stripe_key() {
		if ( in_array( get_query_var( 'pagename' ), [ 'signup' ] ) ) {
			$this->stripe_key = $this->api_helper->fetch_stripe_key();
		}
	}

	public function enqueue_scripts() {
		if ( in_array( get_query_var( 'pagename' ), [ 'signup' ] ) ) {
			wp_enqueue_style( 'signup-main-style', plugins_url( 'css/style.css', __FILE__ ) );
			wp_enqueue_script( 'signup-script', plugins_url( 'js/signup-process.js', __FILE__ ), [ 'jquery', 'stripe-js', 'brightcove-public-player' ] );
			wp_localize_script(
				'signup-script', 'signup_vars', [
					'ajax_url'      => admin_url( 'admin-ajax.php' ),
					'stripe_key'    => $this->stripe_key,
					'bc_account_id' => get_option( 'rlje_theme_brightcove_shared_settings' )['shared_account_id'],
					'bc_player_id'  => get_option( 'rlje_theme_brightcove_shared_settings' )['shared_player_id'],
					'bc_video_id'   => '5180867444001',
				]
			);
		}
	}

	public function add_signup_rewrite_rule() {
		add_rewrite_rule( '^signup/?', 'index.php?pagename=signup', 'top' );
	}

	public function create_membership() {
		$session_id         = strval( $_POST['session_id'] );
		$promo_code         = strval( $_POST['promo_code'] );
		$billing_first_name = strval( $_POST['billing_first_name'] );
		$billing_last_name  = strval( $_POST['billing_last_name'] );
		$name_on_card       = strval( $_POST['name_on_card'] );
		$stripe_token       = strval( $_POST['stripe_token'] );
		$sub_plan           = strval( $_POST['subscription_plan'] );

		$params = [
			'Session'        => [
				'SessionID' => $session_id,
			],
			'Membership'     => $this->membership_plans[ $sub_plan ],
			'BillingAddress' => [
				'FirstName' => $billing_first_name,
				'LastName'  => $billing_last_name,
			],
			'PaymentMethod'  => [
				'NameOnAccount' => $name_on_card,
				'StripeToken'   => $stripe_token,
			],
		];

		if ( $promo_code ) {
			$params['PromoCode'] = [
				'Code' => $promo_code,
			];
		}

		$response = [
			'success' => false,
			'error'   => '',
		];

		$api_response = $this->api_helper->hit_api( $params, 'membership', 'POST' );
		$api_response = json_decode( wp_remote_retrieve_body( $api_response ), true );

		if ( isset( $api_response['error'] ) ) {
			$response['error'] = $api_response['error'];
		} elseif ( isset( $api_response['Membership'] ) ) {
			$response = [
				'success' => true,
			];
		}

		wp_send_json( $response );
	}

	// Initialize user account
	public function initialize_account() {
		$user_email    = strval( $_POST['email_address'] );
		$user_password = strval( $_POST['password'] );
		$promo_code    = strval( $_POST['promo_code'] );
		$response      = [
			'success'    => false,
			'error'      => '',
			'session_id' => '',
			'promo'      => [],
		];

		$profile_responose = $this->api_helper->hit_api( [ 'Email' => $user_email ], 'profile', 'GET' );
		$profile_responose = json_decode( wp_remote_retrieve_body( $profile_responose ), true );

		if ( isset( $profile_responose['Customer'] ) ) {
			if( isset( $profile_responose['Membership'] ) ) {
				// redirect to sign in
				$response['error'] = 'Email already registered with the site. Sign in to start watching.';
				wp_send_json( $response );
			} else {
				// User has an account but no membership.
				// Pass to step two so they can select a plan.
				if( !empty( $promo_code ) ) {
					// Corner case: RENEWUMC should only be used by expired users.
					if( in_array( strtolower( $promo_code ), [ 'renewumc' ] ) ) {
						$response['error'] = "Promo code not found";
						wp_send_json( $response );
					} else {
						$promo_response = $this->api_helper->get_promo( $promo_code );
						if( isset( $promo_response[ "PromotionID" ] ) ) {
							$response[ 'promo' ] = $promo_response;
						} elseif( isset( $promo_response[ "error" ] ) ) {
							$response['error'] = $promo_response[ "error" ];
							wp_send_json( $response );
						}
					}
				}
				$response['success']    = true;
				$response['session_id'] = $profile_responose['Session']['SessionID'];
				wp_send_json( $response );
			}
		}

		if( !empty( $promo_code ) ) {
			// Corner case: RENEWUMC should only be used by expired users.
			if( in_array( strtolower( $promo_code ), [ 'renewumc' ] ) ) {
				$response['error'] = "Promo code not found";
				wp_send_json( $response );
			} else {
				$promo_response = $this->api_helper->get_promo( $promo_code );
				if( isset( $promo_response[ "PromotionID" ] ) ) {
					$response[ 'promo' ] = $promo_response;
				} elseif( isset( $promo_response[ "error" ] ) ) {
					$response['error'] = $promo_response[ "error" ];
					wp_send_json( $response );
				}
			}
		}

		$api_response = $this->api_helper->signup_user( $user_email, $user_password );

		if ( isset( $api_response['error'] ) ) {
			$response['error'] = $api_response['error'];
		} elseif ( isset( $api_response['Session'] ) ) {
			$response['success'] = true;
			$response['session_id'] = $api_response['Session']['SessionID'];
		}

		wp_send_json( $response );
	}

	public function signup_template_redirect() {
		global $wp_query;
		$pagename = get_query_var( 'pagename' );

		if ( 'signup' === $pagename ) {
			if ( isset( $_COOKIE['ATVSessionCookie'] ) && rljeApiWP_isUserEnabled( $_COOKIE['ATVSessionCookie'] ) ) {
				wp_redirect( home_url(), 303 );
				exit();
			}
			// Prevent internal 404 on custome search page because of template_redirect hook.
			status_header( 200 );
			$wp_query->is_404  = false;
			$wp_query->is_page = true;

			ob_start();
			require_once plugin_dir_path( __FILE__ ) . 'templates/main.php';
			$html = ob_get_clean();
			echo $html;
			exit();
		}
	}
}

$rlje_signup_page = new RLJE_signup_page();

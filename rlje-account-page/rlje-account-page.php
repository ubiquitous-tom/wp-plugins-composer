<?php
class RLJE_Account_Page {

	private $api_helper;
	private $account_action = 'status';
	private $user_profile;
	private $membership_plans;

	public function __construct() {
		$this->api_helper       = new RLJE_api_helper();
		add_action( 'init', array( $this, 'add_browse_rewrite_rules' ) );
		add_action( 'wp', [ $this, 'fetch_stripe_key' ] );
		add_action( 'template_redirect', array( $this, 'browse_template_redirect' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_action( 'wp_ajax_user_update_email', [ $this, 'update_email' ] );
		add_action( 'wp_ajax_nopriv_user_update_email', [ $this, 'update_email' ] );

		add_action( 'wp_ajax_user_update_password', [ $this, 'update_password' ] );
		add_action( 'wp_ajax_nopriv_user_update_password', [ $this, 'update_password' ] );

		add_action( 'wp_ajax_cancel_sub', [ $this, 'cancel_membership' ] );
		add_action( 'wp_ajax_nopriv_cancel_sub', [ $this, 'cancel_membership' ] );

		add_action( 'wp_ajax_apply_promo_code', array( $this, 'apply_code' ) );
		add_action( 'wp_ajax_nopriv_apply_promo_code', [ $this, 'apply_code' ] );

		add_action( 'wp_ajax_update_subscription', [ $this, 'update_subscription' ] );
		add_action( 'wp_ajax_nopriv_update_subscription', [ $this, 'update_subscription' ] );

		add_action( 'wp_ajax_apply_renewal_promo', [ $this, 'apply_renewal_promo' ] );
		add_action( 'wp_ajax_nopriv_apply_renewal_promo', [ $this, 'apply_renewal_promo' ] );

		// add_filter( 'body_class', array( $this, 'browse_body_class' ) );
	}

	public function fetch_stripe_key() {
		if ( 'account' === get_query_var( 'pagename' ) && 'renew' === get_query_var( 'action' ) ) {
			$this->stripe_key = $this->api_helper->fetch_stripe_key();
		}
	}

	public function enqueue_scripts() {
		if ( in_array( get_query_var( 'pagename' ), [ 'account' ] ) ) {
			if ( 'renew' == get_query_var( 'action' ) ) {
				wp_enqueue_style( 'account-renewal-style', plugins_url( 'css/renewal.css', __FILE__ ), [ 'account-main-style' ] );
				wp_enqueue_script( 'account-renewal-script', plugins_url( 'js/account-renewal.js', __FILE__ ), [ 'jquery-core', 'stripe-js' ] );
				wp_localize_script(
					'account-renewal-script', 'local_vars', [
						'ajax_url'   => admin_url( 'admin-ajax.php' ),
						'stripe_key' => $this->stripe_key,
						'plans' => $this->api_helper->get_plans(),
					]
				);
			}
			wp_enqueue_style( 'account-main-style', plugins_url( 'css/style.css', __FILE__ ) );
			wp_enqueue_script( 'account-main-script', plugins_url( 'js/account-management.js', __FILE__ ) );
			wp_localize_script(
				'account-main-script', 'account_management_vars', [
					'ajax_url'   => admin_url( 'admin-ajax.php' ),
					'session_id' => $_COOKIE['ATVSessionCookie'],
				]
			);
		}
	}

	public function browse_body_class( $classes ) {

	}

	// Figures out the memebership term
	// Can be monthly, yearly, trial or expired.
	public function get_user_term() {
		$canceled = false;
		$start_date = date_create($this->user_profile->Customer->OriginalMembershipJoinDate);
		if( isset($this->user_profile->Membership->NextBillingDate) ) {
			$end_date = date_create($this->user_profile->Membership->NextBillingDate);
		} elseif( isset($this->user_profile->Membership->CancelDate) ) {
			$canceled = true;
			$end_date = date_create($this->user_profile->Membership->ExpireDate);
		}

		$interval = $start_date->diff($end_date);
		if($interval->days <= 7) {
			$membership_term = 'trial';
		} else {
			if( $canceled && date_create( "now" ) > $end_date ) {
				$membership_term = "expired";
			} else {
				if ( $this->user_profile->Membership->Term == 30 && strtolower( $this->user_profile->Membership->TermType ) == 'day' ) {
					$membership_term = 'monthly';
				} elseif ( $this->user_profile->Membership->Term == 12 && strtolower( $this->user_profile->Membership->TermType ) == 'month' ) {
					$membership_term = 'yearly';
				}
			}
		}
		return $membership_term;
	}

	// If account is active. we'll get NextBillingDateAsLong
	// If account was cancelled, we don't get that.
	public function get_next_billing_date() {
		if ( isset( $this->user_profile->Membership->NextBillingDateAsLong ) ) {
			return $this->user_profile->Membership->NextBillingDate;
		} else {
			return 'N/A';
		}
	}

	// If account is active. we'll get NextBillingAmount
	// If account was cancelled, we don't get that.
	public function get_next_billing_amount() {
		if ( isset( $this->user_profile->Membership->NextBillingAmount ) ) {
			return '$' . $this->user_profile->Membership->NextBillingAmount;
		} else {
			return 'N/A';
		}
	}

	public function get_user_name() {
		return $this->user_profile->Customer->FirstName . ' ' . $this->user_profile->Customer->LastName;
	}

	public function get_user_email() {
		return $this->user_profile->Customer->Email;
	}

	public function get_user_join_date() {
		return $this->user_profile->Customer->OriginalMembershipJoinDate;
	}

	public function account_cancelable() {
		if ( isset( $this->user_profile->Membership->Cancelable ) ) {
			return $this->user_profile->Membership->Cancelable;
		} else {
			return false;
		}
	}

	public function add_browse_rewrite_rules() {
		add_rewrite_rule( '^account/([^/]*)/?', 'index.php?pagename=account&action=$matches[1]', 'top' );
		add_rewrite_tag( '%action%', '([^&]+)' );
	}

	function get_user_profile( $session_id ) {
		return rljeApiWP_getUserProfile( $session_id );
	}

	function update_email() {
		$params   = [
			'SessionID' => $_COOKIE['ATVSessionCookie'],
			'NewEmail'  => $_POST['new_email'],
		];
		$api_response = $this->api_helper->hit_api( $params, 'changeemail', 'POST' );
		$response = json_decode( wp_remote_retrieve_body( $api_response ), true );
		if( isset( $response['Email'] ) ) {
			$this->remove_cached_profile( $_COOKIE['ATVSessionCookie'] );
			wp_send_json( [
				'success' => true,
			] );
		} else {
			wp_send_json( [
				'success' => false,
			] );
		}
	}

	function update_password() {
		$params   = [
			'Session'     => [
				'SessionID' => $_COOKIE['ATVSessionCookie'],
			],
			'Credentials' => [
				'Password' => $_POST['new_password'],
			],
		];
		$api_response = $this->api_helper->hit_api( $params, 'password', 'POST' );
		$response = json_decode( wp_remote_retrieve_body( $api_response ), true );
		wp_send_json( $response );
	}

	function logUserOut( $session_id ) {
		// Mark the session as inactive in WP object cache
		wp_cache_set( 'userStatus_' . md5( $session_id ), 'inactive', 'userStatus' );
		// Ask transient to delete userprofile for that session
		$this->remove_cached_profile( $_COOKIE['ATVSessionCookie'] );
	}

	function cancel_membership() {
		$session_id   = strval( $_POST['session_id'] );
		$params       = [
			'Session' => [
				'SessionID' => $session_id,
			],
		];
		$api_response = $this->api_helper->hit_api( $params, 'membership', 'DELETE' );
		$api_response = json_decode( wp_remote_retrieve_body( $api_response ), true );
		if ( isset( $api_response['Membership'] ) ) {
			$this->remove_cached_profile( $_COOKIE['ATVSessionCookie'] );
			$ajax_response = [ 'success' => true ];
		} else {
			$ajax_response = [ 'success' => false ];
		}
		wp_send_json( $ajax_response );
	}

	function apply_code() {
		// Go hit api and apply code
		$session_id   = $_COOKIE['ATVSessionCookie'];
		$promo_code   = strval( $_POST['promo_code'] );
		$params       = [
			'Session'   => [
				'SessionID' => $session_id,
			],
			'PromoCode' => [
				'Code' => $promo_code,
			],
		];
		$api_response = $this->api_helper->hit_api( $params, 'promo', 'POST' );
		$api_response = json_decode( wp_remote_retrieve_body( $api_response ), true );
		wp_send_json( $api_response );
	}

	function apply_renewal_promo() {
		$plans = $this->api_helper->get_plans();
		$promo_code = strval( $_GET['promo_code'] );
		$response = [
			'success' => false,
			'error' => "",
			'plans' => $plans,
		];
		if ( 'renewumc' === strtolower( $promo_code ) ) {
			$response['success'] = true;
			$response['plans'] = [
				[
					'title' => 'Yearly',
					'duration' => [
						'term' => 12,
						'type' => 'month',
					],
					'cost' => 19.99,
				],
			];
		} else {
			$promo_response = $this->api_helper->get_promo( $promo_code );
			if( isset( $promo_response[ "PromotionID" ] ) ) {
				if( $promo_response['MembershipTerm'] == 12 && $promo_response['MembershipTermType'] == 'MONTH' ) {
					$response['plans'] = [
						[
							'title' => 'Yearly',
							'duration' => [
								'term' => 12,
								'type' => 'month',
							],
							'cost' => 0.00,
						],
					];
				}
				$response[ 'success' ] = true;
			} elseif( isset( $promo_response[ "error" ] ) ) {
				$response['error'] = $promo_response[ "error" ];
			}
		}
		wp_send_json( $response );
	}

	function update_subscription() {
		$response = [
			'success' => false,
			'error'   => 'We could not proccess your request.',
		];
		$this->membership_plans = $this->api_helper->get_plans();
		$session_id         = $_COOKIE['ATVSessionCookie'];
		$promo_code         = strval( $_POST['promo_code'] );
		$billing_first_name = strval( $_POST['billing_first_name'] );
		$billing_last_name  = strval( $_POST['billing_last_name'] );
		$name_on_card       = strval( $_POST['name_on_card'] );
		$stripe_token       = strval( $_POST['stripe_token'] );
		$sub_plan           = strval( $_POST['subscription_plan'] );

		$plan_key = array_search( $sub_plan, array_column( $this->membership_plans, 'title' ) );
		$params = [
			'Session'        => [
				'SessionID' => $session_id,
			],
			'Membership'     => $this->build_api_appropriate_plan( $this->membership_plans[$plan_key] ),
			'BillingAddress' => [
				'FirstName' => $billing_first_name,
				'LastName'  => $billing_last_name,
			],
			'PaymentMethod'  => [
				'NameOnAccount' => $name_on_card,
				'StripeToken'   => $stripe_token,
			],
		];

		if ( !empty( $promo_code ) ) {
			// Hack: API can't associate promos with specific plans
			// Treat renewumc as a yearly only plan.
			if( 'renewumc' === strtolower( $promo_code ) && 'monthly' === $sub_plan ) {
				$response['error'] = 'Promo ' . strtoupper( $promo_code ) . ' can only be used with a yearly plan.';
				wp_send_json( $response );
			}
			$params['PromoCode'] = [
				'Code' => $promo_code,
			];
		}

		$api_response = $this->api_helper->hit_api( $params, 'membership', 'POST' );
		$api_response = json_decode( wp_remote_retrieve_body( $api_response ), true );

		if ( isset( $api_response['error'] ) ) {
			$response['error'] = $api_response['error'];
		} elseif ( isset( $api_response['Membership'] ) ) {
			$this->remove_cached_profile( $session_id );
			$response = [
				'success' => true,
			];
		}

		wp_send_json( $response );
	}

	private function build_api_appropriate_plan($plan) {
		return [
			"Term" => $plan['duration']['term'],
			"TermType" => strtoupper( $plan['duration']['type'] ),
		];
	}

	private function remove_cached_profile ( $session_id ) {
		delete_transient( 'atv_userProfile_' . md5( $session_id ) );
	}

	public function show_subsection() {
		switch ( $this->account_action ) {
			case 'status':
				$partial_url = plugin_dir_path( __FILE__ ) . 'partials/status.php';
				break;

			case 'editEmail':
				$partial_url = plugin_dir_path( __FILE__ ) . 'partials/edit-email.php';
				break;

			case 'editPassword':
				$partial_url = plugin_dir_path( __FILE__ ) . 'partials/edit-password.php';
				break;

			case 'editBilling':
				$partial_url = plugin_dir_path( __FILE__ ) . 'partials/edit-billing.php';
				break;

			case 'cancelMembership':
				$partial_url = plugin_dir_path( __FILE__ ) . 'partials/cancel-membership.php';
				break;

			case 'applyCode':
				$partial_url = plugin_dir_path( __FILE__ ) . 'partials/apply-code.php';
				break;

			default:
				$partial_url = plugin_dir_path( __FILE__ ) . 'partials/status.php';
		}
		return $partial_url;
	}

	public function browse_template_redirect() {
		global $wp_query;
		$pagename = get_query_var( 'pagename' );
		$action   = get_query_var( 'action' );

		if ( 'account' === $pagename ) {
			if ( ! empty( $action ) ) {
				$this->account_action = $action;
			}
			if ( in_array( $this->account_action, ( [ 'status', 'editEmail', 'editPassword', 'editBilling', 'cancelMembership', 'applyCode', 'logout' ] ) ) ) {
				if ( ! isset( $_COOKIE['ATVSessionCookie'] ) || ! rljeApiWP_isUserEnabled( $_COOKIE['ATVSessionCookie'] ) ) {
					wp_redirect( home_url( 'signin' ), 303 );
					exit();
				}
				if ( 'logout' === $action ) {
					$this->logUserOut( $_COOKIE['ATVSessionCookie'] );
					setcookie( 'ATVSessionCookie', '', time() - 3600, '/' );
					wp_redirect( home_url(), 303 );
					exit();
				} elseif ( 'editBilling' === $action ) {
					$this->user_profile = $this->get_user_profile( $_COOKIE['ATVSessionCookie'] );

					// Build the stunning iFrame URL so it can be used on the template
					$stripe_id = $this->user_profile->Customer->StripeCustomerID;
					if( isset (get_option( 'rlje_stunning_settings' )['stunning_id'] ) ) {
						$stunning_token = get_option( 'rlje_stunning_settings' )['stunning_id'];
					} else {
						$stunning_token = '2047dxrbmvipxdnrcfbetfxoe';
					}
					$stunning_url = 'https://payments.stunning.co/payment_update/' . $stunning_token . '/' . $stripe_id;
					// Prevent internal 404 on custome search page because of template_redirect hook.
					status_header( 200 );
					$wp_query->is_404  = false;
					$wp_query->is_page = true;
					ob_start();
					require_once plugin_dir_path( __FILE__ ) . 'templates/updatecard.php';
					$html = ob_get_clean();
					echo $html;
					exit();
				}
				$this->user_profile = $this->get_user_profile( $_COOKIE['ATVSessionCookie'] );
				// Prevent internal 404 on custome search page because of template_redirect hook.
				status_header( 200 );
				$wp_query->is_404  = false;
				$wp_query->is_page = true;
				ob_start();
				require_once plugin_dir_path( __FILE__ ) . 'templates/main.php';
				$html = ob_get_clean();
				echo $html;
				exit();
			} elseif ( $this->account_action == 'renew' ) {
				if ( ! isset( $_COOKIE['ATVSessionCookie'] ) || wp_cache_get( 'userStatus_' . md5( $_COOKIE['ATVSessionCookie'] ), 'userStatus' ) !== 'expired' ) {
					wp_redirect( home_url( 'signin' ), 303 );
					exit();
				}
				// Prevent internal 404 on custome search page because of template_redirect hook.
				status_header( 200 );
				$wp_query->is_404  = false;
				$wp_query->is_page = true;
				$this->membership_plans = $this->api_helper->get_plans();
				ob_start();
				require_once plugin_dir_path( __FILE__ ) . 'templates/membership-renewal.php';
				$html = ob_get_clean();
				echo $html;
				exit();
			}
		}
	}
}

$rlje_account_page = new RLJE_Account_Page();

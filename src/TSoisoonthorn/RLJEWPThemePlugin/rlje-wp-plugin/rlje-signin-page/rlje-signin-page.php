<?php

class RLJE_Signin_Page {

	private $api_helper;
	private $api_app_version        = 'UMCTV.Version.2.0';
	private $api_time_refresh_cache = 90000;

	public function __construct() {
		$this->api_helper = new RLJE_api_helper();
		add_action( 'init', array( $this, 'add_browse_rewrite_rules' ) );
		add_action( 'template_redirect', array( $this, 'browse_template_redirect' ) );

		add_action( 'wp_ajax_signin_user', [ $this, 'signin_user' ] );
		add_action( 'wp_ajax_nopriv_signin_user', [ $this, 'signin_user' ] );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( 'body_class', array( $this, 'browse_body_class' ) );

	}

	public function enqueue_scripts() {
		if ( in_array( get_query_var( 'pagename' ), [ 'signin', 'forgotpassword' ] ) ) {
			wp_enqueue_style( 'signin-index', plugins_url( 'css/style.css', __FILE__ ) );
			wp_enqueue_script( 'signin-script', plugins_url( 'js/signin.js', __FILE__ ), [ 'jquery' ] );
			wp_localize_script(
				'signin-script', 'signin_vars', [
					'ajax_url' => admin_url( 'admin-ajax.php' ),
				]
			);
		}
	}

	public function add_browse_rewrite_rules() {
		add_rewrite_rule( '^signin([^/]+)/?', 'index.php?pagename=signin', 'top' );
		add_rewrite_rule( '^forgotpassword([^/]+)/?', 'index.php?pagename=forgot-password', 'top' );
	}

	private function cacheUserProfile( $user_profile ) {
		$session_id = $user_profile['Session']['SessionID'];
		// Ask Transient to cache user profile
		//set_transient( 'atv_userProfile_' . md5( $session_id ), $user_profile, $this->api_time_refresh_cache );
		rljeApiWP_getUserProfile( $session_id );
	}

	// this function authenticates the user
	public function signin_user() {
		$response      = [
			'success' => false,
			'error'   => '',
			'status'  => 'inactive',
		];
		$user_status   = 'inactive';
		$user_email    = strval( $_POST['email_address'] );
		$user_password = strval( $_POST['password'] );
		$request_body  = [
			'App'         => [
				'AppVersion' => $this->api_app_version,
			],
			'Credentials' => [
				'Username' => $user_email,
				'Password' => $user_password,
			],
			'Request'     => [
				'OperationalScenario' => 'SIGNIN',
			],
		];

		$api_response = $this->api_helper->hit_api( $request_body, 'initializeapp', 'POST' );
		if ( isset( $api_response['Session'] ) ) {
			$session_id = $api_response['Session']['SessionID'];
			if ( isset( $api_response['Membership'] ) ) {
				$response['success'] = true;
				// Set ATVSessionCookie for the authenticated user
				setcookie( 'ATVSessionCookie', $session_id, time() + ( 2 * 7 * 24 * 60 * 60 ), '/' );

				if ( strtolower( $api_response['Membership']['Status'] ) == 'active' ) {
					$user_status = 'active';
				} else {
					$user_status = 'expired';
				}
			} else {
				$response['error'] = "You don't have a subscription on your account. Please signup.";
			}

			wp_cache_set( 'userStatus_' . md5( $session_id ), $user_status, 'userStatus', $this->api_time_refresh_cache );
			// Ask Transient to cache user data
			$this->cacheUserProfile( $api_response );
		} else {
			$response['error'] = 'No account with that email address exists.';
		}

		$response['status'] = $user_status;

		wp_send_json( $response );
	}

	private function resetPassword( $email_address ) {
		$success      = false;
		$request_body = [
			'Customer' => [
				'Email' => $email_address,
			],
		];
		if ( ! empty( $email_address ) ) {
			$response = $this->api_helper->hit_api( $request_body, 'forgotpassword', 'POST' );
			if ( isset( $response['success'] ) && $response['success'] == true ) {
				$success = true;
			}
		}
		return $success;
	}

	public function browse_template_redirect() {
		$pagename = get_query_var( 'pagename' );
		global $wp_query;

		if ( 'signin' === $pagename ) {
			if ( isset( $_COOKIE['ATVSessionCookie'] ) && rljeApiWP_isUserActive( $_COOKIE['ATVSessionCookie'] ) ) {
				wp_redirect( home_url(), 303 );
				exit();
			}
			// Prevent internal 404 on custome search page because of template_redirect hook.
			$wp_query->is_404  = false;
			$wp_query->is_page = true;
			// $wp_query->is_archive = true;
			ob_start();
			require_once plugin_dir_path( __FILE__ ) . 'templates/signin.php';
			$html = ob_get_clean();
			echo $html;
			exit();
		}
		if ( 'forgotpassword' === $pagename ) {
			if ( isset( $_COOKIE['ATVSessionCookie'] ) && rljeApiWP_isUserActive( $_COOKIE['ATVSessionCookie'] ) ) {
				wp_redirect( home_url(), 303 );
			}
			if ( ! empty( $_POST ) ) {
				// Hook into API to reset user password
				$email_address = $_POST['user_email'];
				if ( $this->resetPassword( $email_address ) ) {
					$password_reset_failed = false;
				} else {
					$password_reset_failed = true;
				}
			}
			// Prevent internal 404 on custome search page because of template_redirect hook.
			$wp_query->is_404  = false;
			$wp_query->is_page = true;
			ob_start();
			require_once plugin_dir_path( __FILE__ ) . 'templates/forgot_password.php';
			$html = ob_get_clean();
			echo $html;
			exit();
		}
	}

	public function browse_body_class( $classes ) {
		$pagename = get_query_var( 'pagename' );
		if ( 'signin' === $pagename ) {
			// $classes[] = $pagename;
			$classes[] = 'page-' . $pagename;
		}

		return $classes;
	}

}

$rlje_signin_page = new RLJE_Signin_Page();

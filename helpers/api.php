<?php
class RLJE_api_helper {

	private $api_base;
	private $api_key         = 'LCmkd93u2kcLCkdmacmmc8dkDe';
	private $api_app_version = 'UMCTV.Version.2.0';

	public function __construct() {
		add_action( 'init', [ $this, 'setup_api_helper' ] );
	}

	public function setup_api_helper() {
		$this->api_base = constant( 'RLJE_BASE_URL' );
	}

	private function encode_hash( $data, $api_key = '' ) {
		$hash = json_encode( $data ) . $api_key;
		return base64_encode( $hash );
	}

	public function fetch_stripe_key() {
		$api_response = wp_remote_retrieve_body( $this->hit_api( [], 'stripekey' ) );
		$api_response_decoded = json_decode( $api_response, true );
		if ( isset( $api_response_decoded['StripeKey'] ) ) {
			return $api_response_decoded['StripeKey'];
		} else {
			return null;
		}
	}

	public function signin_user( $email, $password ) {
		$response = [
			'error' => '',
		];
		$request_body  = [
			'App'         => [
				'AppVersion' => $this->api_app_version,
			],
			'Credentials' => [
				'Username' => $email,
				'Password' => $password,
			],
			'Request'     => [
				'OperationalScenario' => 'SIGNIN',
			],
		];
		$api_response = $this->hit_api( $request_body, 'initializeapp', 'POST' );
		if ( $api_response ) {
			switch ( wp_remote_retrieve_response_code( $api_response ) ) {
				case 404:
					$response['error'] = 'No account with that email address exists.';
					break;
				case 401:
					$response['error'] = 'Sign in failed. Please check your sign in information and try again.';
					break;
				case 201:
					$response = array_merge( $response, json_decode( wp_remote_retrieve_body( $api_response ), true ) );
					break;
				default:
					$response['error'] = 'Could not process the request. Please try again later.';
					break;
			}
		} else {
			$response['error'] = 'Could not process the request. Please try again later.';
		}
		return $response;
	}

	public function signup_user( $email, $password ) {
		$response = [
			'error' => '',
		];
		$request_body  = [
			'App'         => [
				'AppVersion' => $this->api_app_version,
			],
			'Credentials' => [
				'Username' => $email,
				'Password' => $password,
			],
			'Request'     => [
				'OperationalScenario' => 'CREATE_ACCOUNT',
			],
		];
		$api_response = $this->hit_api( $request_body, 'initializeapp', 'POST' );
		return json_decode( wp_remote_retrieve_body( $api_response ), true );
	}

	private function get_user_agent() {
		$userAgent = '';
		if ( ! empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
			$userAgent = $_SERVER['HTTP_USER_AGENT'];
		}
		return $userAgent;
	}

	private function get_client_ip() {
		if ( array_key_exists( 'HTTP_X_FORWARDED_FOR', $_SERVER ) ) {
			$ip_array = array_values( array_filter( explode( ',', $_SERVER[ 'HTTP_X_FORWARDED_FOR' ] ) ) );
			return array_pop( $IParray );
		} elseif ( array_key_exists( 'REMOTE_ADDR', $_SERVER ) ) {
			return $_SERVER['REMOTE_ADDR'];
		} elseif ( array_key_exists( 'HTTP_CLIENT_IP', $_SERVER ) ) {
			return $_SERVER['HTTP_CLIENT_IP'];
		}

		return '';
	}

	public function get_promo( $promo_code ) {
		$api_response = $this->hit_api( [ 'Code' => $promo_code ], 'promo' );
		return json_decode( wp_remote_retrieve_body( $api_response ), true );
	}

	public function get_plans() {
		return [
			[
				"title" => "monthly",
				"duration" => [
					"term" => 30,
					"type" => "day"
				],
				"cost" => 4.99
			],
			[
				"title" => "yearly",
				"duration" => [
					"term" => 12,
					"type" => "month"
				],
				"cost" => 49.99
			],
		];
	}

	/**
	 *  Hits RLJE API with provided parameters and returns its output
	 *
	 * @param array  $params input array gets passed to RLJE API.
	 * @param string $method API end-point to hit.
	 * @param string $verb HTTP mothod to use.
	 *
	 * @return array|boolean returns false if there was an error
	 * an array if it was successful.
	 */
	public function hit_api( $params, $method, $verb = 'GET' ) {
		$url     = $this->api_base . '/' . $method;
		$headers = [
			'x-atv-hash'          => $this->encode_hash( $params, $this->api_key ),
			'Accept'              => 'application/json',
			'X-RLJ-Forwarded-For' => $this->get_client_ip(),
		];
		switch ( $verb ) {
			case 'GET':
				if ( ! empty( $params ) ) {
					$url = $url . '?' . http_build_query( $params );
				}
				$raw_response = wp_remote_get( $url );
				break;

			case 'POST':
				if ( 'initializeapp' === $method ) {
					$headers['RLJ-User-Agent'] = $this->get_user_agent();
				}
				$raw_response = wp_remote_post(
					$url, [
						'timeout' => 15,
						'headers' => $headers,
						'body'    => json_encode( $params ),
					]
				);
				if ( is_wp_error( $raw_response ) ) {
					error_log( 'Error hiting API ' . $raw_response->get_error_message() );
					$response = false;
				}
				break;

			case 'DELETE':
				$raw_response = wp_remote_request(
					$url, [
						'method'  => 'DELETE',
						'headers' => $headers,
						'body'    => json_encode( $params ),
					]
				);
				break;

			default:
				// code...
				break;
		}
		if ( is_wp_error( $raw_response ) ) {
			error_log( 'Error hiting API ' . $raw_response->get_error_message() );
			$response = false;
		} else {
			$response = $raw_response;
		}
		//$response = json_decode( wp_remote_retrieve_body( $raw_response ), true );
		return $response;
	}
}

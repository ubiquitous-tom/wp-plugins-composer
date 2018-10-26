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
		return  $this->hit_api( [ 'Code' => $promo_code ], 'promo' );
	}

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
				if ( is_wp_error( $raw_response ) ) {
					error_log( 'Error hiting API ' . $raw_response->get_error_message() );
					$response = false;
				}
				break;

			default:
				// code...
				break;
		}
		$response = json_decode( wp_remote_retrieve_body( $raw_response ), true );
		return $response;
	}
}

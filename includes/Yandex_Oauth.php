<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Yandex_Oauth {

	public static $instance;
	private $app_id;
	private $app_secret;
	public $error;
	private $access_token;
	const OAUTH_SERVER = 'https://oauth.yandex.com/token';


	/**
	 * @param string $app_id     Your app id
	 * @param string $app_secret App secret
	 *
	 * @link https://oauth.yandex.com/client/my
	 */
	public function __construct( $app_id, $app_secret ) {
		self::$instance   = $this;
		$this->app_id     = $app_id;
		$this->app_secret = $app_secret;
	}

	/**
	 * @param int $confirmation_code
	 *
	 * @return bool
	 */
	public function connect_oauth_server( $confirmation_code ) {

		$response = wp_remote_post( self::OAUTH_SERVER, array(
				'method'      => 'POST',
				'timeout'     => 45,
				'httpversion' => '1.1',
				'body'        => array(
					'grant_type'    => 'authorization_code',
					'code'          => $confirmation_code,
					'client_id'     => $this->app_id,
					'client_secret' => $this->app_secret,
				),
				'sslverify'   => true,
			) );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$http_code = $response["response"]["code"];

		if ( ( $http_code != '200' ) && ( $http_code != '400' ) ) {
			$this->error = $http_code;

			return false;
		}

		$result = json_decode( $response["body"], true );

		if ( empty( $result['error'] ) ) {
			$this->access_token = $result['access_token'];

			return true;
		}


		$this->error = $result['error'];

		return false;
	}

	/**
	 * @return bool
	 */
	public function check_access() {
		if ( ! empty( $this->access_token ) && ( empty( $this->error ) ) ) {
			return true;
		}

		$this->error = 'expired_token';

		return false;
	}

	/**
	 * What is the problem?
	 *
	 * @return string
	 */
	public function get_error() {
		if ( ! empty( $this->error ) ) {

			switch ( $this->error ) {
				case 'invalid_request':
					return 'Incorrect request format.';

				case 'invalid_grant':
					return 'Invalid or expired authorization code.';

				case 'unsupported_grant_type':
					return 'Incorrect value for the grant_type parameter.';

				case 'invalid_client':
					return 'Invalid app_id or app_secret';

				case '404':
					return 'Requested url not found';

				case '403':
					return 'Forbidden zone';

				case '500':
					return 'The server encountered an unexpected condition that prevents it from fulfilling the request';

				default:
					return 'ERROR: ' . $this->error;
			}

		}

	}

	/**
	 * @return mixed Access token
	 */
	public function get_access_token() {
		if ( $this->check_access() ) {
			return $this->access_token;
		}
	}


}
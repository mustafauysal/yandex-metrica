<?php


class Yandex_Oauth {

	public static $instance;
	private $app_id;
	private $app_secret;
	public $error;
	private $access_token;


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
		$param = 'grant_type=authorization_code&code=' . $confirmation_code . '&client_id=' . $this->app_id . '&client_secret=' . $this->app_secret . '';
		$url   = "https://oauth.yandex.com/token";

		$header = array(
			'POST /token HTTP/1.1',
			'Host: oauth.yandex.com',
			'Content-type:  application/json',
			'Content-Length: ' . strlen( $param ),
		);

		$data = wp_remote_post( $url, array(
				'method'      => 'POST',
				'timeout'     => 45,
				'httpversion' => '1.1',
				'header'      => $header,
				'body'        => $param,
				'sslverify'   => false
			)
		);


		$http_code = $data["response"]["code"];

		if ( ( $http_code != '200' ) && ( $http_code != '400' ) ) {
			$this->error = $http_code;
			return false;
		}

		$result = json_decode( $data["body"], true );

		if ( empty( $result['error'] ) ) {
			$this->access_token = $result['access_token'];
			return true;
		}
		else {
			$this->error = $result['error'];
			return false;
		}


	}

	/**
	 * @return bool
	 */
	public function check_access() {
		if ( ! empty( $this->access_token ) && ( empty( $this->error ) ) ) {
			return true;
		}
		else {
			$this->error = 'expired_token';
			return false;
		}
	}

	/**
	 * What is the problem?
	 * @return string
	 */
	public function get_error() {
		if ( ! empty( $this->error ) ) {

			switch ( $this->error ) {
				case 'invalid_request':
					return 'Incorrect request format.';
					break;

				case 'invalid_grant':
					return 'Invalid or expired authorization code.';
					break;

				case 'unsupported_grant_type':
					return 'Incorrect value for the grant_type parameter.';
					break;

				case 'invalid_client':
					return 'Invalid app_id or app_secret';
					break;

				case '404':
					return 'Requested url not found';
					break;

				case '403':
					return 'Forbidden zone';
					break;

				case '500':
					return 'The server encountered an unexpected condition that prevents it from fulfilling the request';
					break;

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
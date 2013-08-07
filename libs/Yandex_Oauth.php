<?php


class Yandex_Oauth {

    const VERSION = "1.0-alpha";
    public static $instance;
    private  $app_id;
    private  $app_secret;
    public   $error;
    private  $access_token;


    /**
     * @param string $app_id Your app id
     * @param string $app_secret App secret
     * @link https://oauth.yandex.com/client/my
     */
    public function __construct( $app_id, $app_secret ){
        self::$instance = $this;
        $this->app_id = $app_id;
        $this->app_secret  = $app_secret;
    }

    /**
     * @param int $confirmation_code
     * @return bool
     */
    public function connect_oauth_server( $confirmation_code ){

        if( ! $this->check_curl() ) {
            $this->error = "CURL is not installed on this server, you should install it!";
            return false;
        }

        $url = 'grant_type=authorization_code&code='.$confirmation_code.'&client_id='.$this->app_id.'&client_secret='.$this->app_secret.'';
        $host = "https://oauth.yandex.com/token";
        $header = array(
            'POST /token HTTP/1.1',
            'Host: oauth.yandex.com',
            'Content-type:  application/json',
            'Content-Length: '.strlen( $url ),
        );

        $connect = array(
            CURLOPT_POST => TRUE,
            CURLOPT_HEADER => FALSE,
            CURLOPT_URL => $host,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_FRESH_CONNECT => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_FORBID_REUSE => TRUE,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_POSTFIELDS => $url,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_RETURNTRANSFER => TRUE
        );



        $ch = curl_init();
        curl_setopt_array( $ch, $connect );

        if( !$data = curl_exec( $ch ) )
        {
            $this->error = 'curl'.curl_errno( $ch );
            return false;
        }

        $http_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
        if ( ( $http_code != '200' ) && ( $http_code != '400' ) )
        {
            $this->error = $http_code;
            return false;
        }
        curl_close($ch);

        $result = json_decode( $data, true );

        if ( empty( $result['error'] ) )
        {
            $this->access_token = $result['access_token'];
            if ( ! empty( $result['expires_in'] ) )
            {
                $this->life_time = $result['expires_in'];
            }
            $this->create_time = time();
            return true;
        } else {
            $this->error = $result['error'];
            return false;
        }


    }

    public function check_access() {
        if  ( ! empty( $this->access_token ) && ( empty( $this->error ) ) ) {
            return true;
        } else {
            $this->error = 'expired_token';
            return false;
        }

    }


    public function get_error(){
        if( !empty( $this->error ) ) {

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
                    return 'ERROR: '.$this->error;
            }
        }

    }

    /**
     * @return mixed Access token
     */
    public function get_access_token(){
        if( $this->check_access() ) {
            return $this->access_token;
        }
    }

    /**
     * Check curl installed on the server. API access need it!
     * @since 1.0
     * @return bool
     * $link http://php.net/manual/en/book.curl.php
     */
    public function check_curl() {
        if  ( in_array  ('curl', get_loaded_extensions())) {
            return true;
        } else {
            return false;
        }
    }



}





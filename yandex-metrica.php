<?php
/*
Plugin Name: Yandex Metrica
Plugin URI: http://uysalmustafa.com/plugins/yandex-metrica
Description: Easy way to use Yandex Metrica in your WordPress site.
Author: Mustafa Uysal
Version: 1.0 - alpha
Text Domain: yandex_metrica
Domain Path: /languages/
Author URI: http://uysalmustafa.com
License: GPLv2 (or later)
*/


require_once( dirname( __FILE__ ) . '/libs/wp-stack-plugin.php' );
require_once( dirname( __FILE__ ) . '/libs/Yandex_Oauth.php' );
require_once( dirname( __FILE__ ) . '/libs/Yandex_Metrica.php' );


class WP_Yandex_Metrica extends WP_Stack_Plugin
{
    public static $instance;
    public static $metrica_api;
    private $options;
    const OPTION = 'metrica_options';
    const MENU_SLUG   = 'yandex-metrica';
    const YANDEX_APP_ID = 'e1a0017805e24d7b9395f969b379b7bf';
    const YANDEX_APP_SECRET = '410e753d1ab9478eaa21aa2c3f9a7d88';   // If you want to create your app? you can change app_id and app_secret!


    public function __construct() {
        self::$instance = $this;
        $this->hook( 'init' );


    }


    public function init(){
        // Load langauge pack
        load_plugin_textdomain( 'yandex_metrica', false, basename( dirname( __FILE__ ) ) . '/languages' );

        $this->options =  $this->get_options();

        $this->hook( 'admin_menu' );
        $this->hook( 'wp_footer' );   // using wp_footer for adding tracking code. If you theme don't have it, this plugin can't track your site.

        if ( $this->is_authorized() ) {
            self::$metrica_api = new Yandex_Metrica( $this->options["access_token"] );

            $this->hook( 'wp_dashboard_setup' );
            $this->hook( 'in_admin_footer',  'enqueue');    // footer probably is the best place for speed matters...
        }

    }

    private function get_options() {
        // default options
        $defaults = array(
            'counter_id' => "",
            'webvisor' => true,
            'clickmap' => true,
            'tracklinks' => true,
            'accurate_track' => false,
            'track-logged-in' => true,
            'untrack-roles' => array("administrator"),
            'widget-access-roles' => array("administrator"),
            'legacy-mode' => false
        );
        return wp_parse_args( get_option( self::OPTION ), $defaults );
    }

    public function admin_menu() {
        add_options_page( 'Yandex Metrica', 'Yandex Metrica', 'manage_options' ,self::MENU_SLUG, array($this, 'metrica_settings_page') );
    }

    /**
     * @param $code numeric confirmation code
     * @return bool|void
     */
    public function authorize( $code ) {
        $Auth = new Yandex_Oauth( self::YANDEX_APP_ID, self::YANDEX_APP_SECRET );
        if( $Auth->connect_oauth_server( $code ) ) {
            $this->options['access_token'] = $Auth->get_access_token();
            $this->update_options( $this->options );
            $this->init();
         return  true;
        }
        return false;
    }

    /**
     * Check authorization status
     * @return bool
     */
    public function is_authorized() {
        if( ! empty( $this->options["access_token"] ) ) {
            return true;
        }
        return false;
    }


    public function metrica_settings_page() {
        global $wp_roles;

        if ( ! current_user_can( 'manage_options' ) )  {
            wp_die( __( 'You do not have sufficient permissions to access this page.', 'yandex_metrica' ) );
        }
        include( dirname( __FILE__ ) . '/templates/settings.php' );
    }






    public function wp_dashboard_setup() {
        /**
         * Check user access
         */
        if ( $this->current_user_has_access( $this->options["widget-access-roles"] ) &&  self::$metrica_api->is_valid_counter( $this->options["counter_id"] ) ) {
            $this->hook( 'admin_head', 'dashboard_chart_js' ); // add neccessary js
            wp_add_dashboard_widget( 'yandex_metrica_widget', __('Metrica Statistics','yandex_metrica'), array( $this, 'metrica_dashboard_widget' ));
        }

    }



    public function metrica_dashboard_widget() {
            $total_values = $this->get_metrica_stats( false, "totals" );
            $popular_posts = self::$metrica_api->get_popular_content( $this->options["counter_id"] );
            $top_referrers = self::$metrica_api->get_referal_sites( $this->options["counter_id"] );
            $top_searches = self::$metrica_api->get_search_terms( $this->options["counter_id"] );
            include( dirname( __FILE__ ) . '/templates/dashboard-widget.php' );
    }


    public function enqueue() {
        if( self::$metrica_api->is_valid_counter( $this->options["counter_id"] ) ) {
            wp_enqueue_script( 'highcharts', plugins_url( "js/highcharts/highcharts.js", __FILE__ ) );
            wp_enqueue_script( 'highcharts-exporting', plugins_url( "js/highcharts/modules/exporting.js", __FILE__ ) );
        }
    }

    private function update_options( $options ) {
        $this->options = $options;
        update_option( self::OPTION, $options );
    }


    /**
     * Fetch counter statistics from metrica api.
     *
     * @param null | mixed $period time pediof of the data array, (week,daily,month etc...)
     * @param null $value  of the array
     * @return mixed
     */
    public function get_metrica_stats( $period = null , $value = null ){
        $grab_data = self::$metrica_api->get_counter_statistics( $this->options["counter_id"] );

        if( is_null( $value ) ) {
            return $grab_data;
        }
        return $grab_data[$value];
    }

    /**
     * @return mixed | Current user' role
     */
    public function current_user_role() {
        global $current_user;

        $user_roles = $current_user->roles;
        $user_role = array_shift($user_roles);

        return $user_role;
    }


    private function current_user_has_access( $array ) {
        if( is_array( $array ) && in_array( $this->current_user_role(), $array ) ){
            return true;
        }
        return false;
    }


    public function dashboard_chart_js() {
        wp_enqueue_script( 'jquery' );
        $this->get_metrica_stats();
        $statical_data = array_reverse( $this->get_metrica_stats( false, "data" ) );
        include( dirname( __FILE__ ) . '/templates/dashboard-charts-js.php' );
    }

    public function wp_footer() {
        if( $this->options["track-logged-in"] === true && ( is_user_logged_in() && ! $this->current_user_has_access( $this->options["untrack-roles"] ) ) || ( ! is_user_logged_in() ) ) {
            include( dirname( __FILE__ ) . '/templates/tracker-js.php' );
        } elseif ( $this->options["track-logged-in"] === false && !is_user_logged_in() ) {
            include( dirname( __FILE__ ) . '/templates/tracker-js.php' );
        }
    }


}

new WP_Yandex_Metrica;



////////// YANDEXLE YANDEXLE.... :) \\\\\\\\\\\\\\\

<?php
/*
Plugin Name: Yandex Metrica
Plugin URI: http://uysalmustafa.com/plugins/yandex-metrica
Description: Best metrica plugin for the use Yandex Metrica in your WordPress site.
Author: Mustafa Uysal
Version: 1.8.1
Text Domain: yandex-metrica
Domain Path: /languages/
Author URI: http://uysalmustafa.com
License: GPLv2 (or later)
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once( dirname( __FILE__ ) . '/libs/wp-stack-plugin.php' );
require_once( dirname( __FILE__ ) . '/libs/Yandex_Oauth.php' );
require_once( dirname( __FILE__ ) . '/libs/Yandex_Metrica.php' );
require_once( dirname( __FILE__ ) . '/libs/widget.php' );


class WP_Yandex_Metrica extends WP_Stack_Plugin {
	public static $instance;
	public static $metrica_api;
	private $options;
	const OPTION            = 'metrica_options';
	const MENU_SLUG         = 'yandex-metrica';
	const YANDEX_APP_ID     = 'e1a0017805e24d7b9395f969b379b7bf';
	const YANDEX_APP_SECRET = '410e753d1ab9478eaa21aa2c3f9a7d88'; // If you want to create your app? you can change app_id and app_secret!
	public $period = "weekly", $start_date, $end_date;


	public function __construct() {
		self::$instance = $this;
		$this->options  = $this->get_options();

		$this->hook( 'init' );

		if ( $this->is_authorized() ) {
			$this->hook( 'widgets_init', 'call_metrica_widget' );
		}

	}


	public function init() {
		// Load langauge pack
		load_plugin_textdomain( 'yandex-metrica', false, basename( dirname( __FILE__ ) ) . '/languages' );

		$this->hook( 'admin_menu' );
		$this->hook( 'wp_head' ); // using wp_head for adding tracking code. If your theme doesn't have it, this plugin can't track your site.

		if ( $this->is_authorized() ) {
			self::$metrica_api = new Yandex_Metrica( $this->options["access_token"] );
            $this->set_period( $this->period );
            $this->hook( 'wp_ajax_metrica_actions', 'ajax_listener' );
			if ( $this->current_user_has_access( $this->options["widget-access-roles"] ) )
				$this->hook( 'wp_dashboard_setup' );

		}

		$this->widgets_init();

	}

	private function get_options() {
		// default options
		$defaults = array(
			'counter_id'               => "",
			'webvisor'                 => true,
			'clickmap'                 => true,
			'tracklinks'               => true,
			'accurate_track'           => false,
			'track_hash'               => false,
			'track-logged-in'          => true,
			'untrack-roles'            => array( "administrator" ),
			'widget-access-roles'      => array( "administrator" ),
			'backward'                 => false,
			'new_yandex_code'          => true, // @since 1.7,
			'dispatch_ecommerce'       => false, // @since 1.8.1
			'ecommerce_container_name' => 'dataLayer', // @since 1.8.1
			'tracker-address'          => ""
		);
		return wp_parse_args( get_option( self::OPTION ), $defaults );
	}


	public function admin_menu() {
		add_options_page( __('Yandex Metrica', 'yandex-metrica'), __('Yandex Metrica', 'yandex-metrica'), 'manage_options', self::MENU_SLUG, array( $this, 'metrica_settings_page' ) );
	}


	/**
	 * @param $code numeric confirmation code
	 *
	 * @return bool
	 */
	public function authorize( $code ) {
		$Auth = new Yandex_Oauth( self::YANDEX_APP_ID, self::YANDEX_APP_SECRET );
		if ( $Auth->connect_oauth_server( $code ) ) {
			$this->options['access_token'] = $Auth->get_access_token();
			$this->update_options( $this->options );
			$this->init();
			return true;
		}
		return false;
	}


	/**
	 * Check authorization status
	 * @return bool
	 */
	public function is_authorized() {
		if ( ! empty( $this->options["access_token"] ) ) {
			return true;
		}
		return false;
	}


	public function metrica_settings_page() {
		global $wp_roles;

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'yandex-metrica' ) );
		}
		wp_enqueue_script( 'jquery' );
		include( dirname( __FILE__ ) . '/templates/settings.php' );
	}


	public function wp_dashboard_setup() {
		/**
		 * Check user access
		 */
		if ( self::$metrica_api->is_valid_counter( $this->options["counter_id"] ) ) {

			/**
			 * add inline chart js
			 */
			$this->hook( 'admin_head', 'dashboard_chart_js' );
			wp_add_dashboard_widget( 'yandex_metrica_widget', __( 'Metrica Statistics', 'yandex-metrica' ), array( $this, 'metrica_dashboard_widget' ) );
		}
		else {
			wp_add_dashboard_widget( 'yandex_metrica_widget', __( 'Metrica Statistics', 'yandex-metrica' ), array( $this, 'temporary_dashboard_widget' ) );
		}

	}


	public function temporary_dashboard_widget() {
		echo '<p><b>' . __( 'Oh no! There is nothing to display. Here Are the Possible Causes', 'yandex-metrica' ) . '</b></p>';
		echo '<ol><li>' . __( 'If selected a new counter (recently created), please give a few hours for verification. Please be patient.', 'yandex-metrica' ) . '</li>';
		echo '<li>' . __( 'Did you save options? You need to save options at least once after account confirmation.', 'yandex-metrica' ) . '</li>';
		echo '<li>' . __( 'Are you sure you selected the correct counter? Please confirm.', 'yandex-metrica' ) . '</li>';
		echo '<li>' . __( 'Did you change your Yandex password? If changed, you need to re-authorize this plugin.', 'yandex-metrica' ) . '</li>';
		echo '<li>' . __( 'Temporary, connectivity problem!', 'yandex-metrica' ) . '</li><ol>';
	}


	public function metrica_dashboard_widget() {
		$total_values  = self::$metrica_api->get_counter_statistics( $this->options["counter_id"], $this->start_date, $this->end_date, 'total' );
		$popular_posts = self::$metrica_api->get_popular_content( $this->options["counter_id"], $this->start_date, $this->end_date );
		$top_referrers = self::$metrica_api->get_referal_sites( $this->options["counter_id"], $this->start_date, $this->end_date );
		$top_searches  = self::$metrica_api->get_search_terms( $this->options["counter_id"], $this->start_date, $this->end_date );

		include( dirname( __FILE__ ) . '/templates/dashboard-widget.php' );
	}


	public function dashboard_chart_js() {
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'yandex-metrica-chart', plugins_url( "js/Chart.min.js", __FILE__ ) );

		$statical_data =  self::$metrica_api->get_counter_statistics( $this->options["counter_id"], $this->start_date, $this->end_date, "daily" );

        include( dirname( __FILE__ ) . '/templates/dashboard-charts-js.php' );
	}

	/**
	 * Ajax request handler
	 */
	public function ajax_listener() {

		if ( isset( $_POST["period"] ) && check_ajax_referer( "yandex-metrica-nonce" ) ) {
			$period = stripslashes( $_POST["period"] );
			$this->set_period( $period );
			$this->dashboard_chart_js();
			$this->metrica_dashboard_widget();
		}

		die();
	}

	private function update_options( $options ) {
		$this->options = $options;
		update_option( self::OPTION, $options );
	}

	/**
	 * Calculate time period of data blocks.
	 *
	 * @param string $period
	 *
	 * @return string
	 */
	public function set_period( $period = "weekly" ) {

		switch ( $period ) {
			case "monthly":
				$this->start_date = date( 'Y-m-d', strtotime( "-1 month" ) );
				$this->end_date   = date( 'Y-m-d' );
				break;
			case "weekly":
				$this->start_date = date( 'Y-m-d', strtotime( "-6 days" ) );
				$this->end_date   = date( 'Y-m-d' );
				break;
			case "daily":
				$this->start_date = date( 'Y-m-d' );
				$this->end_date   = date( 'Y-m-d' );
				break;
		}

		return $this->period = $period;
	}


	/**
	 * @return mixed | Current user' role
	 */
	public function current_user_role() {
		global $current_user;

		$user_roles = $current_user->roles;
		$user_role  = array_shift( $user_roles );

		return $user_role;
	}


	private function current_user_has_access( $array ) {
		if ( is_array( $array ) && in_array( $this->current_user_role(), $array ) ) {
			return true;
		}
		return false;
	}


	public function wp_head() {
		if ( ! empty( $this->options['counter_id'] ) ) {

			$tracker_file = $this->options["new_yandex_code"] === true ? "tracker-js-new.php" : "tracker-js.php";

			if ( $this->options["track-logged-in"] === true && ( is_user_logged_in() && ! $this->current_user_has_access( $this->options["untrack-roles"] ) ) || ( ! is_user_logged_in() ) ) {
				include( dirname( __FILE__ ) . '/templates/' . $tracker_file );
			}
			elseif ( $this->options["track-logged-in"] === false && ! is_user_logged_in() ) {
				include( dirname( __FILE__ ) . '/templates/' . $tracker_file );
			}
		}
	}


	public function metrica_informer() {
		echo '<img src="https://informer.yandex.ru/informer/' . esc_attr( $this->options['counter_id'] ) . '/3_1_FFFFFFFF_EFEFEFFF_0_pageviews"  class="ym-advanced-informer" data-cid="' . esc_attr( $this->options['counter_id'] ) . '"  style="width:80px; height:31px; border:0;" />';
	}

	/**
	 * Call back for metrica widget
	 */
	public function call_metrica_widget() {
		register_widget( 'Metrica_Widget' );
	}


	public function widgets_init() {
		wp_register_sidebar_widget(
			'metrica_informer',
			'Metrica Informer',
			array( $this, 'metrica_informer' ),
			array(
				'description' => 'Add metrica Informer to your sidebar, share daily statistics'
			)
		);

	}


}

new WP_Yandex_Metrica;
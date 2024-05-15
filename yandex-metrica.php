<?php
/*
Plugin Name: Yandex Metrica
Plugin URI: https://github.com/mustafauysal/yandex-metrica
Description: The best Yandex Metrica plugin for WordPress.
Author: Mustafa Uysal
Version: 2.0.1
Requires PHP: 5.6
Requires at least: 5.0
Text Domain: yandex-metrica
Domain Path: /languages/
Author URI: https://uysalmustafa.com
License: GPLv2 (or later)
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once( dirname( __FILE__ ) . '/includes/constants.php' );
require_once( dirname( __FILE__ ) . '/includes/utils.php' );
require_once( dirname( __FILE__ ) . '/includes/Encryption.php' );
require_once( dirname( __FILE__ ) . '/includes/wp-stack-plugin.php' );
require_once( dirname( __FILE__ ) . '/includes/Yandex_Oauth.php' );
require_once( dirname( __FILE__ ) . '/includes/Yandex_Metrica.php' );
require_once( dirname( __FILE__ ) . '/includes/widget.php' );


class WP_Yandex_Metrica extends WP_Stack_Plugin {
	public static $instance;
	public static $metrica_api;
	private $options;
	public $period = "weekly", $start_date, $end_date;

	public function __construct() {
		self::$instance = $this;
		$this->options  = \YandexMetrica\Utils\get_settings();


		$this->hook( 'init' );

		if ( $this->is_authorized() ) {
			$this->hook( 'widgets_init', 'call_metrica_widget' );
		}

	}


	public function init() {
		$this->maybe_install();
		// Load langauge pack
		load_plugin_textdomain( 'yandex-metrica', false, basename( dirname( __FILE__ ) ) . '/languages' );

		$this->hook( 'admin_menu' );
		$this->hook( 'wp_head' ); // using wp_head for adding tracking code. If your theme doesn't have it, this plugin can't track your site.

		if ( $this->is_authorized() ) {
			self::$metrica_api = new Yandex_Metrica( \YandexMetrica\Utils\get_decrypted_setting( 'access_token' ) );
            $this->set_period( $this->period );
            $this->hook( 'wp_ajax_metrica_actions', 'ajax_listener' );
			if ( $this->current_user_has_access( $this->options["widget-access-roles"] ) )
				$this->hook( 'wp_dashboard_setup' );

		}

		$this->widgets_init();

	}

	public function admin_menu() {
		add_options_page( __('Yandex Metrica', 'yandex-metrica'), __('Yandex Metrica', 'yandex-metrica'), 'manage_options',  YandexMetrica\Constants\MENU_SLUG, array( $this, 'metrica_settings_page' ) );
	}


	/**
	 * @param int $code confirmation code
	 *
	 * @return bool
	 */
	public function authorize( $code ) {
		$Auth = new Yandex_Oauth( $this->get_app_id(), $this->get_app_secret() );
		if ( $Auth->connect_oauth_server( $code ) ) {
			$encryption   = new \YandexMetrica\Encryption();
			$access_token = $Auth->get_access_token();
			// save encrypted access token
			$this->options['access_token'] = $encryption->encrypt( $access_token );
			$this->update_options( $this->options );
			$this->init();

			return true;
		}

		return false;
	}


	/**
	 * Check authorization status
	 *
	 * @return bool
	 */
	public function is_authorized() {
		if ( ! empty( \YandexMetrica\Utils\get_decrypted_setting( 'access_token' ) ) ) {
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
		wp_enqueue_script( 'yandex-metrica-chart', plugins_url( "js/Chart.min.js", __FILE__ ), array(), '3.4.0' );

		$statical_data = self::$metrica_api->get_counter_statistics( $this->options["counter_id"], $this->start_date, $this->end_date, "daily" );

		include( dirname( __FILE__ ) . '/templates/dashboard-charts-js.php' );
	}

	/**
	 * Ajax request handler
	 */
	public function ajax_listener() {

		if ( isset( $_POST["period"] ) && check_ajax_referer( "yandex-metrica-nonce" ) ) {
			$period = sanitize_text_field( stripslashes( $_POST["period"] ) );
			$this->set_period( $period );
			$this->dashboard_chart_js();
			$this->metrica_dashboard_widget();
		}

		die();
	}

	private function update_options( $options ) {
		$this->options = $options;
		update_option( YandexMetrica\Constants\OPTION, $options );
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
				'description' => esc_html__( 'Add metrica Informer to your sidebar, share daily statistics', 'yandex-metrica' )
			)
		);
	}

	/**
	 * Get APP ID
	 *
	 * @return mixed|void
	 */
	private function get_app_id() {
		return apply_filters( 'yandex_metrica_app_id', YandexMetrica\Constants\YANDEX_APP_ID );
	}

	/**
	 * Get APP Secret
	 *
	 * @return mixed|void
	 */
	private function get_app_secret() {
		return apply_filters( 'yandex_metrica_app_secret', YandexMetrica\Constants\YANDEX_APP_SECRET );
	}


	/**
	 * Run db installation routine
	 */
	public function maybe_install() {
		if ( ! is_blog_installed() ) {
			return;
		}

		// Check if we are not already running
		if ( 'yes' === get_transient( 'yandex_metrica_installing' ) ) {
			return;
		}

		// lets set the transient now.
		set_transient( 'yandex_metrica_installing', 'yes', MINUTE_IN_SECONDS );

		if ( version_compare( get_option( YandexMetrica\Constants\DB_VERSION_OPTION ), YandexMetrica\Constants\DB_VERSION, '<' ) ) {
			$this->maybe_upgrade_20();
			update_option( YandexMetrica\Constants\DB_VERSION_OPTION, YandexMetrica\Constants\DB_VERSION, false );
			do_action( 'yandex_metrica_db_upgraded' );
		}

		delete_transient( 'yandex_metrica_installing' );
	}

	/**
	 * Upgrade routine for 2.0
	 *
	 * @return void
	 */
	public function maybe_upgrade_20() {
		$current_version = get_option( YandexMetrica\Constants\DB_VERSION );

		if ( ! version_compare( $current_version, '2.0', '<' ) ) {
			return;
		}

		$encryption = new \YandexMetrica\Encryption();

		if ( ! empty( $this->options['access_token'] ) && false === $encryption->decrypt( $this->options['access_token'] ) ) {
			$this->options['access_token'] = $encryption->encrypt( $this->options['access_token'] );
		}

		$this->update_options( $this->options );
	}


}

new WP_Yandex_Metrica;
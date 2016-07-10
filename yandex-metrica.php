<?php
/*
Plugin Name: Yandex Metrica
Plugin URI: http://uysalmustafa.com/plugins/yandex-metrica
Description: Best metrica plugin for the use Yandex Metrica in your WordPress site.
Author: Mustafa Uysal
Version: 1.3
Text Domain: yandex_metrica
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
		load_plugin_textdomain( 'yandex_metrica', false, basename( dirname( __FILE__ ) ) . '/languages' );

		$this->hook( 'admin_menu' );
		$this->hook( 'wp_footer' ); // using wp_footer for adding tracking code. If you theme don't have it, this plugin can't track your site.
		$this->hook( 'admin_head', 'yandex_metrica_js' );

		if ( $this->is_authorized() ) {
			self::$metrica_api = new Yandex_Metrica( $this->options["access_token"] );
            $this->set_period( $this->period );
            $this->hook( 'wp_ajax_metrica_actions', 'ajax_listener' );
			if ( $this->current_user_has_access( $this->options["widget-access-roles"] ) )
				$this->hook( 'wp_dashboard_setup' );
			$this->hook( 'in_admin_footer', 'enqueue' ); // footer probably is the best place for speed matters...

		}

		$this->widgets_init();

	}

	private function get_options() {
		// default options
		$defaults = array(
			'counter_id'          => "",
			'webvisor'            => true,
			'clickmap'            => true,
			'tracklinks'          => true,
			'accurate_track'      => false,
			'track-logged-in'     => true,
			'untrack-roles'       => array( "administrator" ),
			'widget-access-roles' => array( "administrator" ),
			'backward'            => false
		);
		return wp_parse_args( get_option( self::OPTION ), $defaults );
	}


	public function admin_menu() {
		add_options_page( __('Yandex Metrica', 'yandex_metrica'), __('Yandex Metrica', 'yandex_metrica'), 'manage_options', self::MENU_SLUG, array( $this, 'metrica_settings_page' ) );
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
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'yandex_metrica' ) );
		}
		include( dirname( __FILE__ ) . '/templates/settings.php' );
	}


	public function wp_dashboard_setup() {
		/**
		 * Check user access
		 */
		if ( self::$metrica_api->is_valid_counter( $this->options["counter_id"] ) ) {

			$this->hook( 'admin_head', 'dashboard_chart_js' ); // add neccessary jsc
			wp_add_dashboard_widget( 'yandex_metrica_widget', __( 'Metrica Statistics', 'yandex_metrica' ), array( $this, 'metrica_dashboard_widget' ) );
		}
		else {
			wp_add_dashboard_widget( 'yandex_metrica_widget', __( 'Metrica Statistics', 'yandex_metrica' ), array( $this, 'temporary_dashboard_widget' ) );
		}

	}


	public function temporary_dashboard_widget() {
		echo '<p><b>' . __( 'Oh no! There is nothing to display. Here Are the Possible Causes', 'yandex_metrica' ) . '</b></p>';
		echo '<ol><li>' . __( 'If selected a new counter (recently created), please give a few hours for verification. Please be patient.', 'yandex_metrica' ) . '</li>';
		echo '<li>' . __( 'Did you save options? You need to save options at least once after account confirmation.', 'yandex_metrica' ) . '</li>';
		echo '<li>' . __( 'Are you sure you selected the correct counter? Please confirm.', 'yandex_metrica' ) . '</li>';
		echo '<li>' . __( 'Did you change your Yandex password? If changed, you need to re-authorize this plugin.', 'yandex_metrica' ) . '</li>';
		echo '<li>' . __( 'Temporary, connectivity problem!', 'yandex_metrica' ) . '</li><ol>';
	}


	public function metrica_dashboard_widget() {
		$total_values  = self::$metrica_api->get_counter_statistics( $this->options["counter_id"], $this->start_date, $this->end_date, "totals" );
		$popular_posts = self::$metrica_api->get_popular_content( $this->options["counter_id"], $this->start_date, $this->end_date );
		$top_referrers = self::$metrica_api->get_referal_sites( $this->options["counter_id"], $this->start_date, $this->end_date );
		$top_searches  = self::$metrica_api->get_search_terms( $this->options["counter_id"], $this->start_date, $this->end_date );

		include( dirname( __FILE__ ) . '/templates/dashboard-widget.php' );
	}


	public function yandex_metrica_js() {
		?>
		<script type="text/javascript">
			jQuery(document).ready(function ($) {
				jQuery('#yandex_metrica_widget h3.hndle span').append('<span class="postbox-title-action"><a href="http://metrica.yandex.com" class="edit-box open-box"><?php _e('View Full Report', 'yandex_metrica');?></a></span>');

				$(document).on("change", "#period", function () {

					jQuery.ajax({
						type : 'post',
						url  : 'admin-ajax.php',
						cache: false,
						data : {
							action     : 'metrica_actions',
							period     : $(this).val(),
							_ajax_nonce: '<?php echo wp_create_nonce("yandex-metrica-nonce");?>'

						},

						beforeSend: function () {
							jQuery("#metricaloading").html('<img src="<?php echo admin_url("images/wpspin_light.gif")?>" />').show();
						},

						success: function (html) {
							//console.log(html);
							jQuery("#metricaloading").hide();
							jQuery('#yandex_metrica_widget .inside').html(html);
							return true;
						}

					});
				});
			});
		</script>
	<?php
	}

	public function dashboard_chart_js() {
		wp_enqueue_script( 'jquery' );
		$statical_data =  self::$metrica_api->get_counter_statistics( $this->options["counter_id"], $this->start_date, $this->end_date, "data" );
        foreach ( (array) $statical_data as $key => $row) {
            $days[$key]  = $row['date'];
        }
        array_multisort($days, SORT_ASC, $statical_data);
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


	public function enqueue() {
		if ( self::$metrica_api->is_valid_counter( $this->options["counter_id"] ) ) {
			wp_enqueue_script( 'highcharts', plugins_url( "js/highcharts/highcharts.js", __FILE__ ) );
			wp_enqueue_script( 'highcharts-exporting', plugins_url( "js/highcharts/modules/exporting.js", __FILE__ ) );
		}
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
				$this->start_date = date( 'Ymd', strtotime( "-1 month" ) );
				$this->end_date   = date( 'Ymd' );
				break;
			case "weekly":
				$this->start_date = date( 'Ymd', strtotime( "-6 days" ) );
				$this->end_date   = date( 'Ymd' );
				break;
			case "daily":
				$this->start_date = date( 'Ymd' );
				$this->end_date   = date( 'Ymd' );
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


	public function wp_footer() {
		if ( ! empty( $this->options['counter_id'] ) ) {

			if ( $this->options["track-logged-in"] === true && ( is_user_logged_in() && ! $this->current_user_has_access( $this->options["untrack-roles"] ) ) || ( ! is_user_logged_in() ) ) {
				include( dirname( __FILE__ ) . '/templates/tracker-js.php' );
			}
			elseif ( $this->options["track-logged-in"] === false && ! is_user_logged_in() ) {
				include( dirname( __FILE__ ) . '/templates/tracker-js.php' );
			}
		}
	}


	public function metrica_informer() {
		echo '<img src="http://bs.yandex.ru/informer/' . $this->options['counter_id'] . '/3_1_FFFFFFFF_EFEFEFFF_0_pageviews" style="width:80px; height:31px; border:0;" />';
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
			array( &$this, 'metrica_informer' ),
			array(
				'description' => 'Add metrica Informer to your sidebar, share daily statistics'
			)
		);

	}


}

new WP_Yandex_Metrica;
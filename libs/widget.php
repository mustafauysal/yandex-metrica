<?php if ( ! defined( 'ABSPATH' ) ) die(); ?>
<?php
/**
 * @package   : WordPress
 * @subpackage: Yandex Metrica
 */


class Metrica_Widget extends WP_Widget {
	protected $defaults;


	function __construct() {

		$this->defaults = array(
			'title'           => '',
			'time'            => 'today',
			'show_page_views' => true,
			'show_visits'     => true,
			'show_visitors'   => true
		);


		parent::__construct(
			'metrica_widget',
			'Metrica Widget',
			array( 'description' => __( 'Display statistics within selected period.', 'yandex_metrica' ), ) // Args
		);
	}


	public function widget( $args, $instance ) {
		$instance = wp_parse_args( (array) $instance, $this->defaults ); // merge options with default

		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $args['before_widget'];
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];


		if ( $instance['time'] == "week" ) {
			$start_date = date( 'Ymd', strtotime( "-6 days" ) );
			$end_date   = date( 'Ymd' );
		}
		elseif ( $instance['time'] == 'month' ) {
			$start_date = date( 'Ymd', strtotime( "-1 month" ) );;
			$end_date = date( 'Ymd' );
		}
		else {
			$start_date = date( 'Ymd' );
			$end_date   = date( 'Ymd' );
		}

		printf( __( '<h3>Metrica statistics for %s </h3>', 'yandex_metrica' ), __( ucfirst( $instance["time"] ), 'yandex_metrica' ) );
		$main_options = get_option( 'metrica_options' );
		$metrica_api  = new Yandex_Metrica( $main_options['access_token'] );
		$results      = $metrica_api->get_counter_statistics( $main_options['counter_id'], $start_date, $end_date, "totals" );

		$instance["page_views"] = $results["page_views"];
		$instance["visits"]     = $results["visits"];
		$instance["visitors"]   = $results["visitors"];


		if ( $instance['show_page_views'] === true && ( ! empty( $instance["page_views"] ) ) )
			printf( __( 'Page Views: <b>%d</b> <br/>', 'yandex_metrica' ), $instance['page_views'] );
		if ( $instance['show_visits'] === true && ! empty( $instance["visits"] ) )
			printf( __( 'Visits: <b>%d</b> </br>', 'yandex_metrica' ), $instance['visits'] );
		if ( $instance['show_visitors'] === true && ! empty( $instance["visits"] ) )
			printf( __( 'Visitors: <b>%d</b> </br>', 'yandex_metrica' ), $instance['visits'] );

		echo $args['after_widget'];
	}


	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, $this->defaults );

		if ( isset( $instance['title'] ) ) {
			$title = $instance['title'];
		}
		?>

		<p>
			<label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_name( 'time' ); ?>"><?php _e( 'Statistics for:', 'yandex_metrica' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'time' ); ?>" name="<?php echo $this->get_field_name( 'time' ); ?>">
				<option <?php selected( $instance['time'], 'today' ); ?> value="today"><?php _e( 'Today', 'yandex_metrica' ); ?></option>
				<option <?php selected( $instance['time'], 'week' ); ?> value="week"><?php _e( 'Week', 'yandex_metrica' ); ?></option>
				<option <?php selected( $instance['time'], 'month' ); ?> value="month"><?php _e( 'Month', 'yandex_metrica' ); ?></option>
			</select>
		</p>
		<p>
			<label><input type="checkbox" <?php checked( $instance['show_page_views'] ); ?>     name="<?php echo $this->get_field_name( 'show_page_views' ); ?>" value="1">  <?php _e( 'Show Pageview', 'yandex_metrica' ) ?>
			</label><br>
			<label><input type="checkbox" <?php checked( $instance['show_visits'] ); ?>     name="<?php echo $this->get_field_name( 'show_visits' ); ?>" value="1">  <?php _e( 'Show Visits', 'yandex_metrica' ) ?>
			</label><br>
			<label><input type="checkbox" <?php checked( $instance['show_visitors'] ); ?>     name="<?php echo $this->get_field_name( 'show_visitors' ); ?>" value="1">  <?php _e( 'Show Visitors', 'yandex_metrica' ) ?>
			</label><br>
		</p>
	<?php
	}


	public function update( $new_instance, $old_instance ) {

		$instance['title']           = $new_instance['title'];
		$instance['time']            = $new_instance['time'];
		$instance['show_page_views'] = empty( $new_instance['show_page_views'] ) ? false : true;
		$instance['show_visits']     = empty( $new_instance['show_visits'] ) ? false : true;
		$instance['show_visitors']   = empty( $new_instance['show_visitors'] ) ? false : true;

		return $instance;
	}


}

new Metrica_Widget;
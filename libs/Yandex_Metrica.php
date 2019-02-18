<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Yandex_Metrica {

	private $access_token;
	public $error;
	const TRANSIENT_VERSION = '1.5';

	/**
	 * @param string $access_token
	 */
	public function __construct( $access_token ) {
		if ( empty( $access_token ) ) {
			$this->error = "Empty access token!";
		}
		$this->access_token = $access_token;
	}

	/**
	 * Fetch data via WordPress HTTP api
	 *
	 * @param $url
	 *
	 * @return mixed
	 */
	public function fetch_data( $url ) {

		$args = array(
			'timeout'     => 7,
			'httpversion' => '1.1',
			'sslverify'   => true,
			'headers'     => array(
				'Authorization' => 'OAuth '.$this->access_token
			)
		);

		$response = wp_remote_get( $url, $args );

		if ( is_wp_error( $response ) ) {
			$this->error = "HTTP Request problem";

			return false;
		}

		if ( isset( $response['response']['message'] ) && 'OK' != $response['response']['message'] ) {
			$this->error = "API connectivity problem.";

			return false;
		}

		return $response["body"];
	}


	/**
	 * Fetch counter lists
	 *
	 * @since 1.0
	 * @return array|mixed counters
	 */
	public function get_all_counters() {

		$url = esc_url_raw( add_query_arg( array(
		), 'https://api-metrika.yandex.com/management/v1/counters' ) );

		$response = $this->fetch_data( $url );
		$results  = json_decode( $response, true );

		return $results;
	}

	/**
	 * How many counters are there?
	 *
	 * @return mixed
	 * @since 1.0
	 */
	public function count_counters() {
		$counters = $this->get_all_counters();

		return $counters["rows"];
	}

	/**
	 * Get all counters
	 *
	 * @return mixed | array
	 * @since 1.0
	 */
	public function get_counters() {
		$results = $this->get_all_counters();

		return $results["counters"];
	}

	/**
	 * Check metrica counter installed correctly
	 *
	 * @param $counter_id int
	 *
	 * @return bool
	 */
	public function is_valid_counter( $counter_id ) {
		$current_counter = $this->fetch_counter( $counter_id );

		if ( $current_counter["counter"]["code_status"] == "CS_OK" ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Fetch counter' all data
	 *
	 * @see   http://api.yandex.com/metrika/doc/ref/reference/get-counter.xml
	 *
	 * @param $counter_id
	 *
	 * @return array|mixed
	 * @since 1.0
	 */
	public function fetch_counter( $counter_id ) {
		$results = get_transient( 'metrica_counter_' . self::TRANSIENT_VERSION . '_' . $counter_id );

		if ( ! $results ) {
			$counter_url = 'https://api-metrika.yandex.com/management/v1/counter/' . $counter_id;
			$data        = $this->fetch_data( $counter_url );
			$results     = json_decode( $data, true );
			set_transient( 'metrica_counter_' . self::TRANSIENT_VERSION . '_' . $counter_id, $results, 720 );
		}

		return $results;
	}


	public function get_counter_name( $counter_id ) {
		$current_counter = $this->fetch_counter( $counter_id );

		return $current_counter["counter"]["name"];
	}


	public function get_counter_statistics( $counter_id, $start_date, $end_date, $stats_type = null ) {
		$stats = get_transient( 'counter_statistics_' . self::TRANSIENT_VERSION . '_' . $counter_id . '_' . $start_date . $end_date );

		if ( ! $stats ) {
			$stats_url = esc_url_raw( add_query_arg( array(
				'date1'       => $start_date,
				'date2'       => $end_date,
				'metrics'     => 'ym:s:pageviews,ym:s:visits,ym:s:users,ym:s:percentNewVisitors,ym:s:pageDepth,ym:s:avgVisitDurationSeconds',
				'group'       => 'day',
				'ids'         => $counter_id,
			), 'https://api-metrika.yandex.com/stat/v1/data/bytime' ) );


			$response = json_decode( $this->fetch_data( $stats_url ), true );
			$daily    = array();

			$pageviews    = $response['data'][0]['metrics'][0];
			$visits       = $response['data'][0]['metrics'][1];
			$users        = $response['data'][0]['metrics'][2];
			$new_visitors = $response['data'][0]['metrics'][3];
			$page_depth   = $response['data'][0]['metrics'][4];
			$duration     = $response['data'][0]['metrics'][5];

			foreach ( $response['time_intervals'] as $key => $time ) {
				$daily[ $time[0] ] = array(
					'pageviews'    => $pageviews[ $key ],
					'visits'       => $visits[ $key ],
					'visitors'     => $users[ $key ],
					'new_visitors' => $new_visitors[ $key ],
					'page_depth'   => $page_depth[ $key ],
					'duration'     => $duration[ $key ],
				);
			}

			$total = array(
				'pageviews'    => $response['totals'][0][0],
				'visits'       => $response['totals'][0][1],
				'visitors'     => $response['totals'][0][2],
				'new_visitors' => $response['totals'][0][3],
				'page_depth'   => $response['totals'][0][4],
				'duration'     => $response['totals'][0][5],
			);

			$stats = array(
				'daily' => $daily,
				'total' => $total,
			);

			set_transient( 'counter_statistics_' . self::TRANSIENT_VERSION . '_' . $counter_id . '_' . $start_date . $end_date, $stats, 3600 );
		}


		if ( ! is_null( $stats_type ) && array_key_exists( $stats_type, $stats ) ) {
			return $stats[ $stats_type ];
		}

		return $stats;
	}


	public function get_popular_content( $counter_id, $start_date, $end_date, $per_page = 5 ) {
		$popular_content = get_transient( 'popular_content_' . self::TRANSIENT_VERSION . '_' . $counter_id . '_' . $start_date . $end_date );

		if ( ! $popular_content ) {

			$stats_url = esc_url_raw( add_query_arg( array(
				'date1'       => $start_date,
				'date2'       => $end_date,
				'dimensions'  => 'ym:pv:URL',
				'metrics'     => 'ym:pv:pageviews',
				'group'       => 'day',
				'ids'         => $counter_id,
				'limit'       => $per_page, // @todo this parameter is not works, take a look later
			), 'https://api-metrika.yandex.com/stat/v1/data/bytime' ) );


			$popular_content_result = json_decode( $this->fetch_data( $stats_url ), true );


			$popular_content = array();

			foreach ( $popular_content_result['data'] as $data_key => $data ) {
				$popular_content[ $data_key ]['url']       = $data['dimensions'][0]['name'];
				$popular_content[ $data_key ]['pageviews'] = $popular_content_result['totals'][0][ $data_key ];
				if ( count( $popular_content ) >= $per_page ) {
					break;
				}
			}

			set_transient( 'popular_content_' . self::TRANSIENT_VERSION . '_' . $counter_id . '_' . $start_date . $end_date, $popular_content, 3600 );
		}

		return $popular_content;
	}


	public function get_referal_sites( $counter_id, $start_date, $end_date, $per_page = 5 ) {
		$top_referrers = get_transient( 'top_referrers_' . self::TRANSIENT_VERSION . '_' . $counter_id . '_' . $start_date . $end_date );

		if ( ! $top_referrers ) {

			$stats_url = esc_url_raw( add_query_arg( array(
				'date1'       => $start_date,
				'date2'       => $end_date,
				'dimensions'  => 'ym:s:referer',
				'metrics'     => 'ym:s:visits',
				'group'       => 'day',
				'ids'         => $counter_id,
				'limit'       => $per_page,
			), 'https://api-metrika.yandex.com/stat/v1/data/bytime' ) );


			$top_referrers_result = json_decode( $this->fetch_data( $stats_url ), true );


			$top_referrers = array();

			foreach ( $top_referrers_result['data'] as $data_key => $data ) {
				$top_referrers[ $data_key ]['url']    = $data['dimensions'][0]['name'];
				$top_referrers[ $data_key ]['visits'] = $top_referrers_result['totals'][0][ $data_key ];
				if ( count( $top_referrers ) >= $per_page ) {
					break;
				}
			}

			set_transient( 'top_referrers_' . self::TRANSIENT_VERSION . '_' . $counter_id . '_' . $start_date . $end_date, $top_referrers, 3600 );
		}


		return $top_referrers;
	}


	public function get_search_terms( $counter_id, $start_date, $end_date, $per_page = 5 ) {

		$top_searches = get_transient( 'top_searches_' . self::TRANSIENT_VERSION . '_' . $counter_id . '_' . $start_date . $end_date );

		if ( ! $top_searches ) {

			$stats_url = add_query_arg( array(
				'date1'       => $start_date,
				'date2'       => $end_date,
				'dimensions'  => 'ym:s:<attribution>SearchPhrase',
				'metrics'     => 'ym:s:visits',
				'group'       => 'day',
				'ids'         => $counter_id,
				'limit'       => $per_page,
			), 'https://api-metrika.yandex.com/stat/v1/data/bytime' );


			$top_searches_result = json_decode( $this->fetch_data( $stats_url ), true );

			$top_searches = array();

			foreach ( $top_searches_result['data'] as $data_key => $data ) {
				$top_searches[ $data_key ]['name']   = $data['dimensions'][0]['name'];
				$top_searches[ $data_key ]['url']    = $data['dimensions'][0]['url'];
				$top_searches[ $data_key ]['visits'] = $top_searches_result['totals'][0][ $data_key ];
				if ( count( $top_searches ) >= $per_page ) {
					break;
				}
			}

			set_transient( 'top_searches_' . self::TRANSIENT_VERSION . '_' . $counter_id . '_' . $start_date . $end_date, $top_searches, 3600 );
		}


		return $top_searches;
	}


}
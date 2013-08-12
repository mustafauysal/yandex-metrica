<?php

class Yandex_Metrica {

    private $access_token;
    public $error;

    /**
     * Fetch data via WordPress http api
     * @param $url
     * @return mixed
     */
    public function fetch_data( $url ) {
        $data = wp_remote_get( $url, array( 'timeout' => 5, 'httpversion' => '1.1', 'sslverify' => false ));

        if ( ! empty ( $data["body"] ) ){
            return $data["body"];
        }
        $this->error = "API connectivity problem.";
        return false;
    }



    public function __construct( $access_token ) {
        if( empty( $access_token ) ) {
            $this->error = "Empty access token!";
        }
        $this->access_token = $access_token;
    }




    /**
     * Fetch all results
     * since 1.0
     * @return array|mixed counters
     */
    public function fetch_results() {
        $url = 'http://api-metrika.yandex.com/counters.json?pretty=1&oauth_token='.$this->access_token;
        $data = $this->fetch_data( $url );
        $results = json_decode( $data, true);

        return $results;
    }

    /**
     * How many counters are there?
     * @return mixed
     * @since 1.0
     */
    public function count_counters() {
        $counters =  $this->fetch_results();
        return  $counters["rows"];
    }

    /**
     * Get all counters
     * @return mixed | array
     * @since 1.0
     */
    public function get_counters() {
        $results = $this->fetch_results();
        return  $results["counters"];
    }

    /**
     * Check metrica counter installed correctly
     * @param $counter_id int
     * @return bool
     */
    public function is_valid_counter( $counter_id ) {
        $current_counter = $this->fetch_counter( $counter_id );

        if( $current_counter["counter"]["code_status"] == "CS_OK" ) {
            return true;
        }else{
            return false;
        }
    }

    /**
     * Fetch counter' all data
     * @see http://api.yandex.com/metrika/doc/ref/reference/get-counter.xml
     * @param $counter_id
     * @return array|mixed
     * @since 1.0
     */
    public function fetch_counter( $counter_id ) {
        $counter_url = 'http://api-metrika.yandex.com/counter/'.$counter_id.'.json?pretty=1&oauth_token='.$this->access_token;
        $data = $this->fetch_data( $counter_url );
        $results = json_decode( $data, true );

        return $results;
    }

    public function get_counter_name( $counter_id ) {
        $current_counter = $this->fetch_counter( $counter_id );
        return $current_counter["counter"]["name"];
    }






    public function get_counter_statistics( $counter_id, $start_date, $end_day ){
        $stats_url = 'http://api-metrika.yandex.com/stat/traffic/summary.json?id='.$counter_id.'&pretty=1&date1='.$start_date.'&date2='.$end_day.'&oauth_token='.$this->access_token;
        $statistics = json_decode( $this->fetch_data ( $stats_url ), true );
        return $statistics;
    }




    public function get_popular_content( $counter_id , $period, $per_page = 5 ){
        if( $period == "monthly" ){
            $start_date = date('Ymd', strtotime("-1 month"));
            $end_day = date('Ymd');
        } elseif( $period == "daily" ){
            $start_date = date('Ymd');
            $end_day = date('Ymd');
        }else{
            $start_date = date('Ymd', strtotime("-6 days"));
            $end_day = date('Ymd');
        }

        $content_url = 'http://api-metrika.yandex.com/stat/content/popular.json?id='.$counter_id.'&pretty=1&date1='.$start_date.'&date2='.$end_day.'&per_page='.$per_page.'&oauth_token='.$this->access_token;
        $popular_content = json_decode( $this->fetch_data( $content_url ), true );
        return $popular_content;
    }

    public function get_referal_sites( $counter_id, $period, $per_page = 5 ){
        if( $period == "monthly" ){
            $start_date = date('Ymd', strtotime("-1 month"));
            $end_day = date('Ymd');
        } elseif( $period == "daily" ){
            $start_date = date('Ymd');
            $end_day = date('Ymd');
        }else{
            $start_date = date('Ymd', strtotime("-6 days"));
            $end_day = date('Ymd');
        }

        $referral_url = 'http://api-metrika.yandex.com/stat/sources/sites.json?id='.$counter_id.'&pretty=1&date1='.$start_date.'&date2='.$end_day.'&per_page='.$per_page.'&oauth_token='.$this->access_token;
        $top_referrers = json_decode( $this->fetch_data( $referral_url ), true );
        return $top_referrers;

    }


    public function get_search_terms( $counter_id, $period, $per_page = 5 ){
        if( $period == "monthly" ){
            $start_date = date('Ymd', strtotime("-1 month"));
            $end_day = date('Ymd');
        } elseif( $period == "daily" ){
            $start_date = date('Ymd');
            $end_day = date('Ymd');
        }else{
            $start_date = date('Ymd', strtotime("-6 days"));
            $end_day = date('Ymd');
        }

        $phrases_url =   'http://api-metrika.yandex.com/stat/sources/phrases.json?id='.$counter_id.'&pretty=1&date1='.$start_date.'&date2='.$end_day.'&per_page='.$per_page.'&oauth_token='.$this->access_token;
        $top_searches = json_decode( $this->fetch_data( $phrases_url ), true);
        return $top_searches;
    }


}

<?php

class Yandex_Metrica {

    private $access_token;
    public $error;

    /**
     * Fetch data via curl, generated for common usage.
     * @param $url
     * @return mixed
     */
    public function fetch_data_by_curl( $url ) {
        $ch = curl_init();

        curl_setopt( $ch, CURLOPT_AUTOREFERER, TRUE );
        curl_setopt( $ch, CURLOPT_HEADER, 0 );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, TRUE );

        $data = curl_exec( $ch );
        curl_close( $ch );

        return $data;
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
        $data = $this->fetch_data_by_curl( $url );
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
        $data = $this->fetch_data_by_curl( $counter_url );
        $results = json_decode( $data, true );

        return $results;
    }


    public function get_counter_statistics( $counter_id ){
        $stats_url = 'http://api-metrika.yandex.com/stat/traffic/summary.json?id='.$counter_id.'&pretty=1&oauth_token='.$this->access_token;
        $statistics = json_decode( $this->fetch_data_by_curl ( $stats_url ), true );
        return $statistics;
    }

    public function get_popular_content( $counter_id , $per_page = 5 ){
        $content_url = 'http://api-metrika.yandex.com/stat/content/popular.json?id='.$counter_id.'&pretty=1&per_page='.$per_page.'&oauth_token='.$this->access_token;
        $popular_content = json_decode( $this->fetch_data_by_curl( $content_url ), true );
        return $popular_content;
    }

    public function get_referal_sites( $counter_id, $per_page = 5 ){
        $referral_url = 'http://api-metrika.yandex.com/stat/sources/sites.json?id='.$counter_id.'&pretty=1&per_page='.$per_page.'&oauth_token='.$this->access_token;
        $top_referrers = json_decode( $this->fetch_data_by_curl( $referral_url ), true );
        return $top_referrers;

    }


    public function get_search_terms( $counter_id, $per_page = 5 ){
        $phrases_url =   'http://api-metrika.yandex.com/stat/sources/phrases.json?id='.$counter_id.'&pretty=1&per_page='.$per_page.'&oauth_token='.$this->access_token;
        $top_searches = json_decode( $this->fetch_data_by_curl( $phrases_url ), true);
        return $top_searches;
    }


}

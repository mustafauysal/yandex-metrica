<?php defined( 'ABSPATH' ) or die();?>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        $('#metrica-graph').highcharts({
            chart: {
                type: 'line'
            },
            title: {
                text: 'Metrica Traffic'
            },
            credits: {
                enabled: false
            },
            subtitle: {
                text: 'Source:<?php echo self::$metrica_api->get_counter_name( $this->options["counter_id"] );?>'
            },
            xAxis: {
                type: 'datetime',
                categories: [
                    <?php
                    // use WordPress' date function date_i18n instead of the php's date. Because localization matters...
                         foreach(  $statical_data as $item){
                            echo "'" .date_i18n('D', strtotime($item["date"])). "',";
                         };
                    ?>
                ]

            },
            yAxis: {
                title: {
                    text: 'Visits'
                },
                min: 0
            },
            tooltip: {

                formatter: function() {
                    return '<b>'+ this.series.name +'</b><br/>'+
                        this.x +': '+  this.y;
                }
            },

            series: [{
                name: 'Visits',
                data: [
                    <?php foreach( $statical_data as $item){
                       echo $item["visits"].",";
                     };?>
                ]
            }, {
                name: 'Unique',
                data: [
                    <?php foreach( $statical_data as $item){
                       echo $item["visitors"].',';
                    };?>
                ]
            }]
        });
    });





</script>
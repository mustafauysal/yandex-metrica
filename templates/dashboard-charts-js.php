<?php defined( 'ABSPATH' ) or die(); ?>

<script type="text/javascript">
	jQuery(document).ready(function ($) {
		$('#metrica-graph').highcharts({
			chart   : {
				type: 'line'
			},
			title   : {
				text: '<?php echo __('Metrica Traffic','yandex_metrica');?>'
			},
			credits : {
				enabled: false
			},
			subtitle: {
				text: '<?php echo __('Source','yandex_metrica');?>:<?php echo self::$metrica_api->get_counter_name( $this->options["counter_id"] );?>'
			},
			xAxis   : {
				type      : 'datetime',
				categories: [
					<?php
					// use WordPress' date function date_i18n instead of the php's date. Because localization matters...
							if($this->period != "monthly"){
							foreach(  $statical_data as $item){
									echo "'" .date_i18n('D', strtotime($item["date"])). "',";
							 };
							} else {
							foreach(  $statical_data as $item){
									echo "'" .date_i18n('d M', strtotime($item["date"])). "',";
							 };
							}
					?>
				]

			},
			yAxis   : {
				title: {
					text: '<?php echo __('Visits','yandex_metrica');?>'
				},
				min  : 0
			},
			tooltip : {

				formatter: function () {
					return '<b>' + this.series.name + '</b><br/>' +
							this.x + ': ' + this.y;
				}
			},

			series: [
				{
					name: '<?php echo __('Visits','yandex_metrica');?>',
					data: [
						<?php foreach( $statical_data as $item){
							 echo $item["visits"].",";
						 };?>
					]
				},
				{
					name: '<?php echo __('Unique','yandex_metrica');?>',
					data: [
						<?php foreach( $statical_data as $item){
							 echo $item["visitors"].',';
						};?>
					]
				}
			]
		});

        $( "#toggle-popular-pages" ).click(function() {
            $( ".metrica-popular-pages" ).toggle(  function() {

            });
        });

        $( "#toggle-top-referrers" ).click(function() {
            $( ".metrica-top-referrers" ).toggle(  function() {

            });
        });

        $( "#toggle-top-searches" ).click(function() {
            $( ".metrica-top-searches" ).toggle(  function() {

            });
        });


    });


</script>
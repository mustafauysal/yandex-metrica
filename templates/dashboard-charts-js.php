<?php defined( 'ABSPATH' ) or die(); ?>

<script type="text/javascript">
	jQuery(document).ready(function ($) {
		$('#metrica-graph').highcharts({
			chart   : {
				type: 'line'
			},
			title   : {
				text: 'Metrica Traffic'
			},
			credits : {
				enabled: false
			},
			subtitle: {
				text: 'Source:<?php echo self::$metrica_api->get_counter_name( $this->options["counter_id"] );?>'
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
					text: '<?php echo _x('Visits','yandex_metrica');?>'
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
					name: '<?php echo _x('Visits','yandex_metrica');?>',
					data: [
						<?php foreach( $statical_data as $item){
							 echo $item["visits"].",";
						 };?>
					]
				},
				{
					name: '<?php echo _x('Unique','yandex_metrica');?>',
					data: [
						<?php foreach( $statical_data as $item){
							 echo $item["visitors"].',';
						};?>
					]
				}
			]
		});
	});


</script>
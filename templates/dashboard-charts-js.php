<?php defined( 'ABSPATH' ) or die(); ?>

<script type="text/javascript">
	jQuery(document).ready(function ($) {
		<?php if( ! is_array( $statical_data ) || empty( $statical_data ) ) { ?>
		$('#metrica-graph').hide();
		$('#metrica-graph-warning').html("<p><?php _e('Sorry, couldn\'t draw graph for selected period, please try different time period.','yandex-metrica');?></p>");
		<?php } else { ?>

		window.chartColors = {
			red: 'rgb(255, 99, 132)',
			orange: 'rgb(255, 159, 64)',
			yellow: 'rgb(255, 205, 86)',
			green: 'rgb(75, 192, 192)',
			blue: 'rgb(54, 162, 235)',
			purple: 'rgb(153, 102, 255)',
			grey: 'rgb(201, 203, 207)'
		};

		var data = {
			labels: [
				<?php
				// use WordPress' date function date_i18n instead of the php's date. Because localization matters...
				$date_format = ( $this->period != "monthly" ? 'D' : 'd M' );
				foreach(  $statical_data as $date => $stats_item ){
					echo "'" .date_i18n($date_format, strtotime( $date ) ). "',";
				}
				?>
			],

			datasets: [
				{
					label: "<?php echo __('Pageviews','yandex-metrica');?>",
					backgroundColor: window.chartColors.blue,
					borderColor: window.chartColors.blue,
					fill: false,
					data : [
						<?php foreach( $statical_data as $item){
						echo $item["pageviews"].",";
					};?>
					]
				},
				{
					label: "<?php echo __('Visits','yandex-metrica');?>",
					backgroundColor: window.chartColors.orange,
					borderColor: window.chartColors.orange,
					fill: false,
					data: [
						<?php foreach( $statical_data as $item){
						echo $item["visits"].",";
					};?>
					]
				},
				{
					label: "<?php echo __('Unique','yandex-metrica');?>",
					backgroundColor: window.chartColors.green,
					borderColor: window.chartColors.green,
					fill: false,
					data : [
						<?php foreach( $statical_data as $item){
						echo $item["visitors"].',';
					};?>
					]
				}
			]
		};

		var context = document.querySelector('#metrica-graph').getContext('2d');

		new Chart(context, {
			type: '<?php echo( $this->period == "daily" ? 'bar' : 'line' );?>',
			data   : data,
			options: {
				responsive: true,
				title     : {
					display: true,
					text   : '<?php echo __( 'Metrica Traffic', 'yandex-metrica' );?>'
				},
			}
		});


		$("#toggle-metrica-popular-pages").click(function () {
			$(".metrica-popular-pages").toggle();
		});

		$("#toggle-metrica-top-referrers").click(function () {
			$(".metrica-top-referrers").toggle();
		});

		$("#toggle-metrica-top-searches").click(function () {
			$(".metrica-top-searches").toggle();
		});

		<?php } ?>

    });

</script>
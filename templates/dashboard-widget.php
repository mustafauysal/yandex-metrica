<?php if ( !defined( 'ABSPATH' ) ) die(); ?>
<select name="period" id="period">
    <option <?php selected( $this->period, "daily" ); ?> value="daily">Daily</option>
    <option <?php selected( $this->period, "weekly" ); ?> <?php if( empty ( $this->period )) echo "selected";?> value="weekly">Weekly</option>
    <option <?php selected( $this->period, "monthly" ); ?> value="monthly">Monthly</option>
</select>
<span id="metricaloading"></span>
<div id="metrica-graph" style="width:100%; height: 400px;"></div>
<div id="metrica-widget-data">
    <h3><?php _e('Site Usage','yandex_metrica');?></h3>
    <table width="100%">
        <tr>
            <td width="20%">
                <?php echo $total_values["visits"];?>
            </td>
            <td width="20%">
                <b><?php _e('Visits','yandex_metrica');?></b>
            </td>
            <td width="20%">
                <?php echo '%'.$total_values["new_visitors_perc"]*100;?>
            </td>
            <td width="20%">
                <b><?php echo _e('New Visitors','yandex_metrica');?></b>
            </td>
        </tr>

        <tr>
            <td>
                <?php echo $total_values["page_views"];?>
            </td>
            <td>
                <b><?php echo _e('Page Views','yandex_metrica');?></b>
            </td>
            <td>
                <?php echo round( $total_values["depth"],1);?>
            </td>
            <td>
                <b><?php echo _e('Session depth','yandex_metrica');?></b>
            </td>
        </tr>

        <tr>
            <td>
                <?php echo $total_values["visitors"];?>
            </td>
            <td>
                <b><?php _e('Visitors','yandex_metrica');?></b>
            </td>
            <td>
                <?php echo gmdate("H:i:s", $total_values["visit_time"]);?>
            </td>
            <td>
                <b><?php echo _e('Avg. Time on Site','yandex_metrica');?></b>
            </td>
        </tr>

    </table>

    <div id="popular-posts">
        <h3><?php _e('Popular Pages','yandex_metrica');?></h3>
        <ol>
            <?php
            foreach ( $popular_posts["data"] as $post) {
                echo '<li><a href="' . $post["url"] . '">' . $post["url"] . '</a> - ' . $post["page_views"] . ' ' . _x('Views', 'yandex_metrica') . '</li>';
            }
            ?>
        </ol>
    </div>


    <div id="metrica-incoming">
        <table>
            <tr valign="top">
                <td width="50%">
                    <div class="top-referrers">
                        <h4><?php _e('Top Referrers','yandex_metrica');?></h4>

                        <ol>
                            <?php
                                foreach ( $top_referrers["data"] as $referrer) {
                                    echo '<li><a href="' . $referrer["url"] . '">' . $referrer["url"] . '</a> - ' . $referrer["visits"] . ' ' . _x('Visits', 'yandex_metrica') . '</li>';
                                }
                            ?>
                        </ol>

                    </div>
                </td>
                <td>
                    <div class="top-searches">
                        <h4><?php _e('Search Terms','yandex_metrica');?></h4>
                        <ol>
                            <?php
                                foreach( $top_searches["data"] as $search_term){
                                    echo '<li><strong>'.$search_term["phrase"].'</strong> - ' . $referrer["visits"] . '  ' . _x('Visits', 'yandex_metrica') . '</li>';
                                }
                            ?>
                        </ol>
                    </div>
                </td>
            </tr>
        </table>
    </div>

</div>
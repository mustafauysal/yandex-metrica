<?php if ( ! defined( 'ABSPATH' )) {
    die();
} ?>
<select name="period" id="period">
    <option <?php selected( $this->period, "daily" ); ?> value="daily"><?php _e( 'Daily', 'yandex_metrica' ); ?></option>
    <option <?php selected( $this->period, "weekly" ); ?> <?php if (empty ( $this->period )) {
        echo "selected";
    } ?> value="weekly"><?php _e( 'Weekly', 'yandex_metrica' ); ?></option>
    <option <?php selected( $this->period, "monthly" ); ?> value="monthly"><?php _e( 'Monthly', 'yandex_metrica' ); ?></option>
</select>
<span id="metricaloading"></span>
<?php if ( ! empty( $total_values["visits"] ) || ! empty( $total_values["visitors"] )): ?>
    <div id="metrica-graph" style="width:100%; height: 400px;"></div>
<?php endif; ?>
<div id="metrica-widget-data">
    <h3><?php _e( 'Site Usage', 'yandex_metrica' ); ?></h3>
    <table width="100%">
        <tr>
            <td width="20%">
                <b><?php _e( 'Visits', 'yandex_metrica' ); ?>:</b>
            </td>
            <td width="20%">
                <?php
                if ( ! empty( $total_values["visits"] )) {
                    echo $total_values["visits"];
                } else {
                     _e( 'None', 'yandex_metrica' );
                }
                ?>
            </td>
            <td width="20%">
                <b><?php _e( 'New Visitors', 'yandex_metrica' ); ?>:</b>
            </td>
            <td width="20%">
                <?php
                if ( ! empty( $total_values["new_visitors_perc"] )) {
                    echo '%'.$total_values["new_visitors_perc"] * 100;
                } else {
                     _e( 'None', 'yandex_metrica' );
                }
                ?>
            </td>

        </tr>

        <tr>
            <td>
                <b><?php  _e( 'Page Views', 'yandex_metrica' ); ?>:</b>
            </td>
            <td>
                <?php if ( ! empty( $total_values["page_views"] )) {
                    echo $total_values["page_views"];
                } else {
                    _e( 'None', 'yandex_metrica' );
                }
                ?>
            </td>
            <td>
                <b><?php  _e( 'Session depth', 'yandex_metrica' ); ?>:</b>
            </td>
            <td>
                <?php if ( ! empty( $total_values["depth"] )) {
                    echo round( $total_values["depth"], 1 );
                } else {
                    _e( 'None', 'yandex_metrica' );
                }
                ?>
            </td>

        </tr>

        <tr>
            <td>
                <b><?php _e( 'Visitors', 'yandex_metrica' ); ?>:</b>
            </td>
            <td>
                <?php if ( ! empty( $total_values["visitors"] )) {
                    echo $total_values["visitors"];
                } else {
                    echo __( 'None', 'yandex_metrica' );
                }
                ?>
            </td>
            <td>
                <b><?php  _e( 'Avg. Time on Site', 'yandex_metrica' ); ?>:</b>
            </td>
            <td>
                <?php
                if ( ! empty( $total_values["visit_time"] )) {
                    echo gmdate( "H:i:s", $total_values["visit_time"] );
                } else {
                    _e( 'None', 'yandex_metrica' );
                }
                ?>
            </td>

        </tr>

    </table>


    <div id="popular-posts" class="postbox">
        <div class="handlediv" id="toggle-popular-pages"><br /></div>

        <h3><?php _e( 'Popular Pages', 'yandex_metrica' ); ?></h3>

        <ol class="metrica-popular-pages">

            <?php if ( ! empty( $popular_posts["data"] )): ?>
                <?php foreach ($popular_posts["data"] as $post): ?>
                    <li>
                        <a href="<?php echo esc_url( $post["url"] ); ?>"><?php echo esc_url( $post["url"] ); ?></a> -
                        <?php echo sprintf( _n( '%d View', '%d Views', $post["page_views"], 'yandex_metrica' ), $post["page_views"] ); ?>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <?php _e( 'None', 'yandex_metrica' ); ?>
            <?php endif; ?>

        </ol>
    </div>


    <div id="metrica-incoming" class="postbox">
        <div class="handlediv" id="toggle-top-referrers"><br /></div>

        <h3><?php _e( 'Top Referrers', 'yandex_metrica' ); ?></h3>

        <ol class="metrica-top-referrers" style="display:none">

            <?php if ( ! empty( $top_referrers["data"] )): ?>
                <?php foreach ($top_referrers["data"] as $referrer): ?>
                    <li>
                        <a href="<?php echo esc_url( $referrer["url"] ); ?>"><?php echo esc_url( $referrer["url"] ); ?></a> -
                        <?php echo sprintf( _n( '%d Visit', '%d Visits', $referrer["visits"], 'yandex_metrica' ), $referrer["visits"] ); ?>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <?php _e( 'None', 'yandex_metrica' ); ?>
            <?php endif; ?>

        </ol>

    </div>


    <div id="top-searches" class="postbox">
        <div class="handlediv" id="toggle-top-searches"><br /></div>

        <h3><?php _e( 'Search Terms', 'yandex_metrica' ); ?></h3>
        <ol class="metrica-top-searches" style="display:none">

            <?php if ( ! empty( $top_searches["data"] )) : ?>
                <?php foreach ($top_searches["data"] as $search_term): ?>
                    <li>
                        <strong><?php echo $search_term["phrase"]; ?></strong> -
                        <?php echo sprintf( _n( '%d Visit', '%d Visits', $search_term["visits"], 'yandex_metrica' ), $search_term["visits"] ); ?>
                    </li>
                <?php endforeach ?>
            <?php else: ?>
                <?php _e( 'None', 'yandex_metrica' ); ?>
            <?php endif; ?>

        </ol>
    </div>

</div>



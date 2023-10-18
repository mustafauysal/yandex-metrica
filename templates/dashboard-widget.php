<?php if ( ! defined( 'ABSPATH' )) {
    die();
} ?>
<label>
    <select name="period" id="period">
        <option <?php selected( $this->period, "daily" ); ?> value="daily"><?php esc_html_e( 'Daily', 'yandex-metrica' ); ?></option>
        <option <?php selected( $this->period, "weekly" ); ?> <?php if ( empty ( $this->period ) ) {
            echo "selected";
        } ?> value="weekly"><?php esc_html_e( 'Weekly', 'yandex-metrica' ); ?></option>
        <option <?php selected( $this->period, "monthly" ); ?> value="monthly"><?php esc_html_e( 'Monthly', 'yandex-metrica' ); ?></option>
    </select>
</label>

<span id="metricaloading"></span>

<?php if ( ! empty( $total_values["visits"] ) || ! empty( $total_values["visitors"] ) ): ?>
    <canvas id="metrica-graph" style="width:100%; height: 400px;"></canvas>
    <div id="metrica-graph-warning"></div>
<?php endif; ?>

<div id="metrica-widget-data">
    <h3><?php esc_html_e( 'Site Usage', 'yandex-metrica' ); ?></h3>
    <table width="100%">
        <tr>
            <td style="width:20%;">
                <b><?php esc_html_e( 'Visits', 'yandex-metrica' ); ?>:</b>
            </td>
            <td style="width:20%;">
                <?php
                if ( ! empty( $total_values["visits"] )) {
                    echo $total_values["visits"];
                } else {
                     esc_html_e( 'None', 'yandex-metrica' );
                }
                ?>
            </td>
            <td style="width:20%;">
                <b><?php esc_html_e( 'New Visitors', 'yandex-metrica' ); ?>:</b>
            </td>
            <td style="width:20%;">
                <?php
                if ( ! empty( $total_values["new_visitors"] )) {
                    echo '% '.round($total_values["new_visitors"],2);
                } else {
                     esc_html_e( 'None', 'yandex-metrica' );
                }
                ?>
            </td>

        </tr>

        <tr>
            <td>
                <b><?php  esc_html_e( 'Page Views', 'yandex-metrica' ); ?>:</b>
            </td>
            <td>
                <?php if ( ! empty( $total_values["pageviews"] )) {
                    echo $total_values["pageviews"];
                } else {
                    esc_html_e( 'None', 'yandex-metrica' );
                }
                ?>
            </td>
            <td>
                <b><?php  esc_html_e( 'Session depth', 'yandex-metrica' ); ?>:</b>
            </td>
            <td>
                <?php if ( ! empty( $total_values["page_depth"] )) {
                    echo round( $total_values["page_depth"], 1 );
                } else {
	                esc_html_e( 'None', 'yandex-metrica' );
                }
                ?>
            </td>

        </tr>

        <tr>
            <td>
                <b><?php esc_html_e( 'Visitors', 'yandex-metrica' ); ?>:</b>
            </td>
            <td>
                <?php if ( ! empty( $total_values["visitors"] )) {
                    echo $total_values["visitors"];
                } else {
                    echo __( 'None', 'yandex-metrica' );
                }
                ?>
            </td>
            <td>
                <b><?php  esc_html_e( 'Avg. Time on Site', 'yandex-metrica' ); ?>:</b>
            </td>
            <td>
                <?php
                if ( ! empty( $total_values["duration"] ) ) {
	                echo gmdate( "H:i:s", round( $total_values["duration"] ) );
                } else {
	                esc_html_e( 'None', 'yandex-metrica' );
                }
                ?>
            </td>

        </tr>

    </table>


    <div id="popular-posts" class="postbox <?php echo postbox_classes('popular-posts', 'dashboard');?>">
        <div class="postbox-header">
            <h2 class="hndle not-sortable"><?php esc_html_e( 'Popular Pages', 'yandex-metrica' ); ?></h2>
            <div class="handle-actions hide-if-no-js">
                <button type="button" class="handlediv button-link" id="toggle-metrica-popular-pages">
                    <span class="toggle-indicator" aria-hidden="true"></span>
                </button>
            </div>
        </div>


        <div class="metrica-inside">
            <ol class="metrica-popular-pages <?php echo postbox_classes('popular-posts', 'dashboard');?>">

                <?php if ( ! empty( $popular_posts ) ): ?>
                    <?php foreach ( $popular_posts as $post ): ?>
                        <li>
                            <a href="<?php echo esc_url( $post["url"] ); ?>"><?php echo esc_url( $post["url"] ); ?></a> -
                            <?php echo sprintf( _n( '%d View', '%d Views', $post["pageviews"], 'yandex-metrica' ), $post["pageviews"] ); ?>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <?php esc_html_e( 'None', 'yandex-metrica' ); ?>
                <?php endif; ?>

            </ol>
        </div>
    </div>


    <div id="metrica-incoming" class="postbox <?php echo postbox_classes('metrica-incoming', 'dashboard');?>">
        <div class="postbox-header">
            <h2 class="hndle not-sortable"><?php esc_html_e( 'Top Referrers', 'yandex-metrica' ); ?></h2>
            <div class="handle-actions hide-if-no-js">
                <button type="button" class="handlediv button-link" id="toggle-metrica-top-referrers">
                    <span class="toggle-indicator" aria-hidden="true"></span>
                </button>
            </div>
        </div>
        
        <div class="metrica-inside">
            <ol class="metrica-top-referrers <?php echo postbox_classes('metrica-incoming', 'dashboard');?>">

                <?php if ( ! empty( $top_referrers ) ): ?>
                    <?php foreach ( $top_referrers as $referrer ): ?>
                        <li>
                            <a href="<?php echo esc_url( $referrer["url"] ); ?>"><?php echo esc_url( $referrer["url"] ); ?></a> -
                            <?php echo sprintf( _n( '%d Visit', '%d Visits', $referrer["visits"], 'yandex-metrica' ), $referrer["visits"] ); ?>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <?php esc_html_e( 'None', 'yandex-metrica' ); ?>
                <?php endif; ?>

            </ol>
        </div>
    </div>


    <div id="top-searches" class="postbox <?php echo postbox_classes('top-searches', 'dashboard');?>">
        <div class="postbox-header">
            <h2 class="hndle not-sortable"><?php esc_html_e( 'Search Terms', 'yandex-metrica' ); ?></h2>
            <div class="handle-actions hide-if-no-js">
                <button type="button" class="handlediv button-link" id="toggle-metrica-top-searches">
                    <span class="toggle-indicator" aria-hidden="true"></span>
                </button>
            </div>
        </div>
        
        <div class="metrica-inside">
            <ol class="metrica-top-searches <?php echo postbox_classes('top-searches', 'dashboard');?>">

                <?php if ( ! empty( $top_searches ) ) : ?>
                    <?php foreach ( $top_searches as $search_term ): ?>
                        <li>
                            <strong><?php echo $search_term["name"]; ?></strong> -
                            <?php echo sprintf( _n( '%d Visit', '%d Visits', $search_term["visits"], 'yandex-metrica' ), $search_term["visits"] ); ?>
                        </li>
                    <?php endforeach ?>
                <?php else: ?>
                    <?php esc_html_e( 'None', 'yandex-metrica' ); ?>
                <?php endif; ?>

            </ol>

        </div>
    </div>

</div>

<style>
    .metrica-inside .closed{
        display: none;
    }
    .not-sortable{
        cursor:unset!important;
    }
    .metrica-popular-pages,
    .metrica-top-referrers,
	.metrica-top-searches{
		word-wrap: break-word;
    }
</style>


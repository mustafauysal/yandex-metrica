<?php
/**
 * Yandex Informer widget
 */
function metrica_widget()
{
    $metrica_data = get_option('metrica_options');
    $metrica_id = $metrica_data['counter_id'];
    echo '<img src="http://bs.yandex.ru/informer/' . $metrica_id . '/3_1_FFFFFFFF_EFEFEFFF_0_pageviews" style="width:80px; height:31px; border:0;" />';
}

wp_register_sidebar_widget(
    'metrica_informer',
    'Metrica Informer',
    'metrica_widget',
    array(
        'description' => 'Add metrica Informer to your sidebar, share daily statistics'
    )
);







<?php defined( 'ABSPATH' ) or die();


    if (isset($_POST['save'])) {

        $metrica_options = array();
        if( is_numeric($_POST['auth-code']) ){
            $metrica_options['authcode'] = esc_attr($_POST['auth-code']);
            add_settings_error( 'metrica-notices', 'myplugin-discount-updated', __('Discount code updated.', 'myplugin'), 'updated' );
        }else{
            add_settings_error( 'metrica-notices', 'yandex_activation_error', __('Please emter a valid activation code!', 'yandex_metrica'), 'error' );
        }

            update_option("metrica_options", $metrica_options);

    }

    settings_errors( 'metrica-notices' );

  //  echo ' <div class="updated"><p>' . __('Options saved','yandex_metrica') . '</p></div>';

?>


<div class="wrap">
    <h2><?php _e('Yandex Metrica','yandex_metrica');?></h2>
    <p><?php _e('You need to sign in to Yandex and grant this plugin access to your Yandex Metrica account','yandex_metrica');?></p>
    <p></p><a onclick="window.open('https://oauth.yandex.com/authorize?response_type=code&client_id=e1a0017805e24d7b9395f969b379b7bf&display=popup', 'activate','width=700, height=600, menubar=0, status=0, location=0, toolbar=0')" target="_blank" href="javascript:void(0);"><b><?php _e('Click here for getting activation code','yandex_metrica');?></b></a></p>

    <form method="post" action="">

    <div id="metrica-settings">
        <input type="text" name="auth-code"  placeholder="<?php _e('Enter your Metrica Authentication Code in this box','yandex_metrica');?>" style="width: 300px;" />
        <?php submit_button( __( 'Save', 'yandex_metrica' ), 'primary', 'yandex-metrica-save', false ); ?>
    </div>




    </form>
</div>


<?php
if (isset($_POST['save'])) {

    $metrica_options = array();
    $metrica_options['counter_id'] = $_POST['metrica_counter_id'];
    $metrica_options['webvisor'] = empty($_POST['metrica_webvisor']) ? 'false' : 'true';
    $metrica_options['clickmap'] = empty($_POST['metrica_clickmap']) ? 'false' : 'true';
    $metrica_options['tracklinks'] = empty($_POST['metrica_tracklinks']) ? 'false' : 'true';
    $metrica_options['accurate_track'] = empty($_POST['metrica_accurate_track']) ? 'false' : 'true';

    $metrica_data  = $metrica_options;
    update_option("metrica_options", $metrica_data);



    echo ' <div class="updated"><p>' . __('Options saved','yandex_metrica') . '</p></div>';
}
?>

<div class="wrap">
    <h2>Yandex Metrica</h2>
    <form method="post" action="">

        <table class="form-table">

            <tr>
                <td>
                    <label><?php _e('Metrica Counter ID', 'yandex_metrica');?>:</label>
                    <input type="text" name="metrica_counter_id" value="<?php echo  $metrica_data['counter_id']; ?>"/>
                </td>
            </tr>

            <tr>
                <td>
                    <input type="checkbox" <?php if ($metrica_data['webvisor'] == 'true') {
                        echo 'checked=checked';
                    }; ?> name="metrica_webvisor" value="1">  <?php _e('Webvisor', 'yandex_metrica')?>
                </td>
            </tr>


            <tr>
                <td>
                    <input type="checkbox" <?php if ($metrica_data['clickmap'] == 'true') {
                        echo 'checked=checked';
                    }; ?> name="metrica_clickmap" value="1">  <?php _e('Track Clickmap', 'yandex_metrica');?>
                </td>
            </tr>


            <tr>
                <td>
                    <input type="checkbox" <?php if ($metrica_data['tracklinks'] == 'true') {
                        echo 'checked=checked';
                    }; ?> name="metrica_tracklinks"
                           value="1">  <?php _e('Track Links, social sharing, file requests...', 'yandex_metrica');?>
                </td>
            </tr>

            <tr>
                <td>
                    <input type="checkbox" <?php if ($metrica_data['accurate_track'] == 'true') {
                        echo 'checked=checked';
                    }; ?> name="metrica_accurate_track"
                           value="1">  <?php _e('Accurate Track Bounce', 'yandex_metrica');?>
                </td>
            </tr>


        </table>

        <input type="hidden" name="action" value="update"/>

        <p class="submit">
            <input type="submit" name="save" class="button-primary" value="<?php _e('Save Changes') ?>"/>
        </p>




    </form>
</div>

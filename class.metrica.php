<?php
/*
 * @author Mustafa
 * @package WordPress
 * @subpackage Metrica Plugin
 * @since 0.1
 * @link http://api.yandex.com/metrika/
 *
 */


class Yandex_Metrica
{
    public $counter_id;
    public $webvisor;
    public $clickmap;
    public $tracklink;
    public $trackbounce;

    public function __construct()
    {
        add_action('plugins_loaded', array(&$this, 'plugin_localization'));
        add_action('wp_footer', array(&$this, 'metrica_counter_display'));
        add_action('admin_menu', array(&$this, 'metrica_admin_menu'));
        add_action('admin_init',array(&$this, 'save_metrica_options'));

    }

    /**
     * Localization
     */
    function plugin_localization()
    {
        load_plugin_textdomain('yandex_metrica', false, '/metrica/languages/');


    }

    /**
     * Admin menu function
     */
    function metrica_admin_menu()
    {
            add_options_page('Yandex Metrica', 'Yandex Metrica', 'manage_options' ,'metrica', array(&$this, 'metrica_settings_page'));
    }

    public function get_metrica_counter_id()
    {
        $metrica_data = $this->metrica_data;
        $id = $metrica_data['counter_id'];
        return $id;
    }


    function get_metrica_options()
    {
        $metrica_data = get_option('metrica_options');
        return $metrica_data;
    }


    function metrica_settings_page()
    {
        global $wpdb, $wp_roles, $current_user;
        //check user role
        if ( !current_user_can( 'manage_options' ) )  {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }
        $metrica_data = $this->get_metrica_options();
        $this->counter_id = $metrica_data['counter_id'];
        include(WP_PLUGIN_DIR . '/metrica/settings.php');


    }


    function metrica_counter_display()
    {
    //output
        $metrica_data = get_option('metrica_options');


        echo'          <!--Yandex.Metrika counter by metrica plugin-->
                <script type = "text/javascript" >
                       (function (d, w, c) {
                 (w[c] = w[c] || []).push(function () {
                            try {
                    w . '

            . 'yaCounter' . $metrica_data['counter_id'] . ' = new Ya .Metrika({
                            id:' . $metrica_data['counter_id'] . ',' .
            'webvisor:' . $metrica_data['clickmap'] . ',' .
            'clickmap:' . $metrica_data['clickmap'] . ',' .
            'trackLinks:' . $metrica_data['tracklinks'] . ',' .
            'accurateTrackBounce:' . $metrica_data['accurate_track'] . '
                            });
                } catch (e) {
                            }
                        });

            var n = d . getElementsByTagName("script")[0],
                s = d . createElement("script"),
                f = function () {
                    n . parentNode . insertBefore(s, n);
                };
            s . type = "text/javascript";
            s . async = true;
            s . src = (d . location . protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

            if (w . opera == "[object Opera]") {
                d . addEventListener("DOMContentLoaded", f, false);
            } else {
                f();
            }
        })(document, window, "yandex_metrika_callbacks");
        </script >
        <noscript ><div ><img src = "//mc.yandex.ru/watch/' . $metrica_data['counter_id'] . '" style = "position:absolute; left:-9999px;" alt ="" /></div ></noscript >
        <!-- /Yandex . Metrika counter-->
     ';

    }


    function save_metrica_options()
    {
        if (isset($_POST['save'])) {
            $metrica_options = array();
            $metrica_options['counter_id'] = $_POST['metrica_counter_id'];
            $metrica_options['webvisor'] = empty($_POST['metrica_webvisor']) ? 'false' : 'true';
            $metrica_options['clickmap'] = empty($_POST['metrica_clickmap']) ? 'false' : 'true';
            $metrica_options['tracklinks'] = empty($_POST['metrica_tracklinks']) ? 'false' : 'true';
            $metrica_options['accurate_track'] = empty($_POST['metrica_accurate_track']) ? 'false' : 'true';

            echo ' <div class="updated"><p>' . __('Options saved','yandex_metrica') . '</p></div>';
            return update_option("metrica_options", $metrica_options);

        } else{
            return $this->get_metrica_options();
        }

    }
}

$metrica = new Yandex_Metrica;


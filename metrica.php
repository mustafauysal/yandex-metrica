<?php
/*
Plugin Name: Yandex Metrica
Plugin URI: http://uysalmustafa.com/plugins/yandex-metrica
Description: Easy way to use Yandex Metrica in your WordPress site.
Author: Mustafa Uysal
Version: 0.1.1
Text Domain: yandex_metrica
Domain Path: /languages/
Author URI: http://blog.uysalmustafa.com
License: GPLv2 (or later)
*/

if (!defined('WP_CONTENT_URL'))
    define('WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
if (!defined('WP_CONTENT_DIR'))
    define('WP_CONTENT_DIR', ABSPATH . 'wp-content');
if (!defined('WP_PLUGIN_URL'))
    define('WP_PLUGIN_URL', WP_CONTENT_URL . '/plugins');
if (!defined('WP_PLUGIN_DIR'))
    define('WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins');


require_once(WP_PLUGIN_DIR . '/yandex-metrica/class.metrica.php');
require_once(WP_PLUGIN_DIR . '/yandex-metrica/widget.php');


////////// YANDEXLE YANDEXLE.... :) \\\\\\\\\\\\\\\
<?php
/**
 * Uninstall Yandex Metrica
 *
 * Deletes all plugin related data and configurations
 *
 */

// Exit if accessed directly.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

require_once 'yandex-metrica.php';

delete_option( 'metrica_options' );
delete_option( 'yandex_metrica_db_version' );
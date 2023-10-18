<?php
/**
 * Utils
 *
 * @package YandexMetrica
 */

namespace YandexMetrica\Utils;

use YandexMetrica\Encryption;

/**
 * Get plugin settings
 *
 * @return mixed|void
 */
function get_settings() {
	// default options
	$defaults = array(
		'counter_id'               => "",
		'webvisor'                 => true,
		'clickmap'                 => true,
		'tracklinks'               => true,
		'accurate_track'           => false,
		'track_hash'               => false,
		'track-logged-in'          => true,
		'untrack-roles'            => array( "administrator" ),
		'widget-access-roles'      => array( "administrator" ),
		'backward'                 => false,
		'new_yandex_code'          => true, // @since 1.7,
		'dispatch_ecommerce'       => false, // @since 1.8.1
		'ecommerce_container_name' => 'dataLayer', // @since 1.8.1
		'tracker-address'          => "",
	);

	return wp_parse_args( get_option( \YandexMetrica\Constants\OPTION ), $defaults );
}


/**
 * Get sensitive data in decrypted form
 *
 * @param string $field field name
 *
 * @return bool|mixed|string
 */
function get_decrypted_setting( $field ) {
	$settings = \YandexMetrica\Utils\get_settings();
	$value    = isset( $settings[ $field ] ) ? $settings[ $field ] : '';

	// decrypt the value
	$encryption      = new Encryption();
	$decrypted_value = $encryption->decrypt( $value );
	if ( false !== $decrypted_value ) {
		return $decrypted_value;
	}

	return $value;
}

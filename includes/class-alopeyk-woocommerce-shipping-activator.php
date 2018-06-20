<?php

/**
 * Fired during plugin activation
 *
 * @link       https://alopeyk.com
 * @since      1.0.0
 *
 * @package    Alopeyk_WooCommerce_Shipping
 * @subpackage Alopeyk_WooCommerce_Shipping/includes
 * @author     Alopeyk <dev@alopeyk.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'Alopeyk_WooCommerce_Shipping_Activator' ) ) {
	return;
}

/**
 * @since 1.0.0
 */
class Alopeyk_WooCommerce_Shipping_Activator {

	/**
	 * @since 1.0.0
	 */
	public static function activate() {

		if ( ! in_array  ( 'curl', get_loaded_extensions() ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			wp_die( __( 'Sorry, but you cannot run this plugin, it requires the <a href="http://php.net/manual/en/book.curl.php">cURL</a> support on your server/hosting to function.', 'alopeyk-woocommerce-shipping' ) );
		}

		if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			wp_die( __( 'Sorry, WooCommerce plugin should be installed and activated before activating Alopeyk WooCommerce Shipping plugin.', 'alopeyk-woocommerce-shipping' ) );
		}
		
	}

}

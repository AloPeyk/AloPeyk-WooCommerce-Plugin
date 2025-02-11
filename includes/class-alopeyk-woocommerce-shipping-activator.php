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

	    if ( ! in_array( 'curl', get_loaded_extensions() ) ) {
	        deactivate_plugins( plugin_basename( __FILE__ ) );
	        wp_die( esc_html__( 'Sorry, but you cannot run this plugin, it requires the <a href="http://php.net/manual/en/book.curl.php">cURL</a> support on your server/hosting to function.', 'alopeyk-shipping-for-woocommerce' ) );
	    }

	    if ( ! self::is_woocommerce_active() ) {
	        deactivate_plugins( plugin_basename( __FILE__ ) );
	        wp_die( esc_html__( 'Sorry, WooCommerce plugin should be installed and activated before activating Alopeyk WooCommerce Shipping Method plugin.', 'alopeyk-shipping-for-woocommerce' ) );
	    }

	    self::change_store_city();

	    self::change_checkout_content();
	}

	private static function is_woocommerce_active() {
	    if ( function_exists( 'is_plugin_active' ) ) {
	        return is_plugin_active( 'woocommerce/woocommerce.php' );
	    }

	    $active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
	    return in_array( 'woocommerce/woocommerce.php', $active_plugins ) || is_plugin_active_for_network( 'woocommerce/woocommerce.php' );
	}

	public static function change_store_city() {
		$schedule_name = ALOPEYK_METHOD_ID . '_check_mandatory_options';
		wp_schedule_event( time(), $schedule_name . '_interval', $schedule_name );
	}

	public static function change_checkout_content() {
	    if ( is_multisite() ) {
	        $sites = get_sites();

	        foreach ($sites as $site) {
	            switch_to_blog($site->blog_id); 

	            self::update_checkout_content();

	            restore_current_blog(); 
	        }
	    } else {
	        self::update_checkout_content();
	    }
	}

	private static function update_checkout_content() {
		$checkout_page_id = wc_get_page_id('checkout');
		if ($checkout_page_id) {
			$new_content = "[woocommerce_checkout]";
			$updated_post = array(
				'ID'           => $checkout_page_id,
				'post_content' => $new_content,
			);
			$result = wp_update_post($updated_post);
		} else {
			if ( defined('WP_DEBUG') && WP_DEBUG ) {
				$logger = wc_get_logger();
				$logger->debug(
					'Checkout page ID is not valid for site ID: ' . get_current_blog_id(),
					array('source' => 'alopeyk-shipping-for-woocommerce')
				);
			}
		}
	}
}
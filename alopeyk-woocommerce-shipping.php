<?php

/**
 * @link                  https://alopeyk.com
 * @since                 1.0.0
 * @package               Alopeyk_WooCommerce_Shipping
 *
 * @wordpress-plugin
 * Plugin Name:           Alopeyk WooCommerce Shipping
 * Plugin URI:            https://alopeyk.com/#section-services
 * Description:           Include Alopeyk On-demand Delivery in WooCommerce shop shipping methods.
 * Version:               4.2.0
 * Author:                Alopeyk
 * Author URI:            https://alopeyk.com/
 * Text Domain:           alopeyk-woocommerce-shipping
 * Domain Path:           /languages
 *
 * WC requires at least:  3.9
 * WC tested up to:       8.5
 *
 * Copyright:             © 2017-2018 Alopeyk.
 * License:               GNU General Public License v3.0
 * License URI:           http://www.gnu.org/licenses/gpl-3.0.html
 * "awcshm" stands for Alopeyk woocommerce shipping method
 */

/**
 * @TODO : Create method to generate css and js files url by WP_DEBUG
 * @TODO : Install more filters and actions to make plugin more extensible for developers
 * @TODO : Using more comments to make code more readable
 * @TODO : Recheck function types
 * @TODO : Move public-only functions from Common class to Public class
 * @TODO : Use Transients API where possible
 * @TODO : Add php function to add prefix
 */

if (!defined('ABSPATH')) {
    exit;
}

define('PLUGIN_VERSION', '4.2.0');
define('METHOD_ID', 'alopeyk_woocommerce_shipping_method');
define('PLUGIN_BASENAME', plugin_basename(__FILE__));
define('PLUGIN_PATH', plugin_dir_path(__FILE__));

include_once(ABSPATH . 'wp-admin/includes/plugin.php');
$isWoocommerceActive = is_plugin_active('woocommerce/woocommerce.php') and file_exists(WP_PLUGIN_DIR . '/woocommerce/woocommerce.php');
if (!$isWoocommerceActive) {
    add_action('admin_notices', function () {
        $message = 'Woocommerce plugin is not active, to use "Alopeyk Woocommerce shipping method" plugin you need to enable it';
        echo '<div class="error"><p>' . $message . '</p></div>';
    });
    return;
}

add_action('before_woocommerce_init', function () {
    if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
    }
});

if (!function_exists('activate_alopeyk_woocommerce_shipping')) {
    function activate_alopeyk_woocommerce_shipping()
    {
        require_once plugin_dir_path(__FILE__) . 'includes/class-alopeyk-woocommerce-shipping-activator.php';
        Alopeyk_WooCommerce_Shipping_Activator::activate();
    }
}

if (!function_exists('deactivate_alopeyk_woocommerce_shipping')) {
    function deactivate_alopeyk_woocommerce_shipping()
    {
        require_once plugin_dir_path(__FILE__) . 'includes/class-alopeyk-woocommerce-shipping-deactivator.php';
        Alopeyk_WooCommerce_Shipping_Deactivator::deactivate();
    }
}

register_activation_hook(__FILE__, 'activate_alopeyk_woocommerce_shipping');
register_deactivation_hook(__FILE__, 'deactivate_alopeyk_woocommerce_shipping');
require plugin_dir_path(__FILE__) . 'includes/class-alopeyk-woocommerce-shipping.php';

if (!function_exists('run_alopeyk_woocommerce_shipping')) {
    function run_alopeyk_woocommerce_shipping()
    {
        $plugin = new Alopeyk_WooCommerce_Shipping();
        $plugin->run();
    }
}

run_alopeyk_woocommerce_shipping();

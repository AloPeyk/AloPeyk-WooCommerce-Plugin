<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://alopeyk.com
 * @since      1.0.0
 *
 * @package    Alopeyk_WooCommerce_Shipping
 * @subpackage Alopeyk_WooCommerce_Shipping/public
 * @author     Alopeyk <dev@alopeyk.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'Alopeyk_WooCommerce_Shipping_Public' ) ) {
	return;
}

/**
 * @since 1.0.0
 */
class Alopeyk_WooCommerce_Shipping_Public {

	private $plugin_name;

	/**
	 * @since 1.0.0
	 * @param string $plugin_name
	 * @param string $version
	 */
	public function __construct( $plugin_name = null, $version = null ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->set_helpers();

	}

	/**
	 * @since 1.0.0
	 */
	public function set_helpers() {

		$this->helpers = new Alopeyk_WooCommerce_Shipping_Common();

	}

	/**
	 * @since 1.0.0
	 */
	public function enqueue_styles() {

		if ( $this->helpers->is_enabled() ) {
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/alopeyk-woocommerce-shipping-public' . ( WP_DEBUG ? '.min' : '' ) . '.css', array(), $this->version, 'all' );
		}

	}

	/**
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {

		if ( $this->helpers->is_enabled() ) {
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/alopeyk-woocommerce-shipping-public' . ( WP_DEBUG ? '.min' : '' ) . '.js', array( 'jquery' ), $this->version, false );
		}

	}

}

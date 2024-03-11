<?php

/**
 * The shipping method functionality of the plugin.
 *
 * @link       https://alopeyk.com
 * @since      1.0.0
 *
 * @package    Alopeyk_WooCommerce_Shipping
 * @subpackage Alopeyk_WooCommerce_Shipping/admin
 * @author     Alopeyk <dev@alopeyk.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( METHOD_ID ) ) {
	return;
}

/**
 * @since 1.0.0
 */
class alopeyk_woocommerce_shipping_method extends WC_Shipping_Method {

	public $package = null;

    private $helpers;

    /**
	 * @since 1.0.0
	 */
	public function __construct( $instance_id = 0 ) {

		$this->id                 = METHOD_ID;
		$this->instance_id        = absint( $instance_id );
		$this->method_title       = __( 'Alopeyk', 'alopeyk-woocommerce-shipping' );
		$this->method_description = __( 'Alopeyk On-demand Delivery will be included in this WooCommerce shipping zone.', 'alopeyk-woocommerce-shipping' );
		$this->title              = $this->method_title;
		$this->supports           = array(
			'shipping-zones',
			'instance-settings',
			'instance-settings-modal',
		);
		$this->helpers            = new Alopeyk_WooCommerce_Shipping_Common();
		$this->init_form_fields();
	}


	/**
	 * @since 1.0.0
	 */
	public function init_form_fields() {
		$this->instance_form_fields = [
			'title' => array(
				'type' => 'title',
				'title' => __("You can customize the shipping method in the 'Alopeyk' tab in the Woocommerce settings", 'alopeyk-woocommerce-shipping'),
            )
		];
	}

	/**
	 * @since  1.0.0
	 * @param  array $package
	 * @return array
	 */
	public function get_package_data( $package ) {

		if ( $this->package ) {
			return $this->package;
		}
		$package = (object) $package;
		$weights = array( WC()->cart->cart_contents_weight );
		$destinations = array( $package->destination );
		$dimensions = array();
		$contents = $package->contents;
		$total_volume = 0;
		if ( count( $contents ) ) {
			$dimensions = array_map( function( $content ) use ( $total_volume ) {
				$width  = (float) $content['data']->get_width();
				$height = (float) $content['data']->get_height();
				$length = (float) $content['data']->get_length();
				return array(
					'width'    => $width,
					'height'   => $height,
					'length'   => $length,
					'volume'   => $width * $height * $length,
					'quantity' => $content['quantity']
				);
			}, $contents );
			$total_volume = array_sum( array_column( $dimensions, 'volume' ) );
		}
		$transport_types = $this->helpers->get_transport_types();
		$weight_unit = get_option( 'woocommerce_weight_unit' );
		$dimension_unit = get_option( 'woocommerce_dimension_unit' );
		foreach ( $transport_types as $key => $transport_type ) {
			$overflowed[$key] = $this->helpers->has_overflow( $weights, $dimensions, $weight_unit, $dimension_unit, $key );
		}
		$subtotal = isset( $package->cart_subtotal ) ? $package->cart_subtotal : 0;
		$payment_method = $package->active_payment_method;
		$package = array(
			'extra'          => $package,
			'weights'        => $weights,
			'dimensions'     => $dimensions,
			'destinations'   => $destinations,
			'overflowed'     => $overflowed,
			'subtotal'       => $subtotal,
			'payment_method' => $payment_method,
			'total_volume'   => $total_volume,
			'total_weight'   => $weights[0],
		);
		WC()->session->set( 'package_data', $package );
		$this->package = $package;
		return $package;

	}

	/**
	 * @since 1.0.0
	 * @param array $package
	 */
	public function calculate_shipping( $package = array() ) {

		WC()->session->set( 'return_cost', 0 );
		if ( $package && count( $package ) ) {
			$package = $this->get_package_data( $package );
			$min_price = INF;
			$rates = array();
			$shipping_methods = [];
			$shipping_infos   = [];
			$selected_type_id = 0;
			$shippings = (object) $this->helpers->calculate_shipping( $package );
			foreach ( $shippings as $key => $shipping ) {
				if ( ! is_null( $shipping['cost'] ) ) {
					$shipping_methods[]   = $key;
					$shipping_infos[$key] = $shipping;
					$method_title = $this->title . ' (' . $this->helpers->get_transport_type_name( $shipping['type'] ) . ')';
					$rate = array(
						'id'    => $this->id .'-'. $key,
						'label' => $method_title,
						'cost'  => $this->helpers->normalize_price( $shipping['cost'] ),
					);
					if ( $this->helpers->get_option( 'auto_type', 'yes' ) == 'yes' ) {
						$rates[] = $rate;
						if ( $this->helpers->get_option( 'auto_type_static', 'yes' ) == 'yes' ) {
							$selected_type_id = 1;
						} elseif ( $rate['cost'] < $min_price ) {
							$selected_type_id = count( $rates );
							$min_price = $rate['cost'];
						}
					} else {
						$selected_type_id = -1;
						$this->add_rate( $rate );
					}
				}
			}
			if ( $selected_type_id != 0 ){
				if ( $selected_type_id == -1 ){
					$chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods' );
					if ( is_array( $chosen_shipping_methods ) ) {
						$method_name = explode( '-', $chosen_shipping_methods[0] );
						$shipping_method = isset( $method_name[1] ) ? $method_name[1] : '';
					} else {
						$shipping_method = '';
					}
				} else {
					$shipping_method = $shipping_methods[ $selected_type_id - 1 ];
					$this->add_rate( $rates[ $selected_type_id - 1 ] );
				}
				if ( isset( $shipping_infos[$shipping_method] ) ) {
					if ( $shippings->{$shipping_method}['cost_type'] == 'dynamic' ) {
						$cost_details = (object) $shippings->{$shipping_method}['cost_details'];
						WC()->session->set( 'return_cost', $this->helpers->normalize_price( $cost_details->price_with_return - $cost_details->price ) );
					}
				}
			}
		}

	}

	/**
	 * @since  1.0.0
	 * @param  array   $package
	 * @return boolean
	 */
	public function is_available( $package = array() ){

		WC()->session->set( 'return_cost', 0 );
		if (
			$package &&
			count( $package ) &&
			$this->helpers->is_enabled() &&
			$this->helpers->is_available_for_currency( get_woocommerce_currency() )
		) {
			$transport_types = $this->helpers->get_transport_types();
			foreach ( $transport_types as $key => $transport_type ) {
				if ( $this->helpers->is_available( $this->get_package_data( $package ), $key ) ) {
					return true;
				}
			}
		}
		return false;

	}

}
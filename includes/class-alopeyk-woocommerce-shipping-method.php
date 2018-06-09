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

	/**
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->id                 = METHOD_ID;
		$this->method_title       = __( 'Alopeyk', 'alopeyk-woocommerce-shipping' );
		$this->method_description = __( 'By filling the following fields and checking enabled field, Alopeyk On-demand Delivery will be included in WooCommerce shop shipping methods.', 'alopeyk-woocommerce-shipping' );
		$this->required_fields    = array( 'api_key', 'store_name', 'store_phone', 'store_lat', 'store_lng', 'store_address', 'store_city' );
		$this->errors = new WP_Error();
		$this->init();

	}

	/**
	 * @since 1.0.0
	 */
	function init() {

		$this->title     = $this->get_option( 'title', __( 'Alopeyk', 'alopeyk-woocommerce-shipping' ) );
		$this->enabled   = $this->get_option( 'enabled', 'no' );
		$this->wrong_key = $this->get_option( 'wrong_key', 'no' );
		foreach ( $this->required_fields as $required_field ) {
			$this->{$required_field} = $this->get_option( $required_field );
		}
		$this->set_helpers();
		$this->init_form_fields();
		$this->init_settings();
		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );

	}

	/**
	 * @since 1.0.0
	 */
	public function init_form_fields() {

		$form_fields = array(
			'enabled' => array(
				'title'   => __( 'Enable/Disable', 'alopeyk-woocommerce-shipping' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable Alopeyk shipping', 'alopeyk-woocommerce-shipping' ),
				'default' => 'no',
			),      
			'api_key' => array(
				'title'       => __( 'API Key', 'alopeyk-woocommerce-shipping' ),
				'type'        => 'text',
				'default'     => '',
				'description' => sprintf( __( 'Please <a href="%s">contact Alopeyk</a> to get one.', 'alopeyk-woocommerce-shipping' ), $this->helpers->get_support_url() ),
				'custom_attributes' => array(
					'required' => 'required'
				)
			),
		);
		if ( ! empty( $this->api_key ) && $this->wrong_key != 'yes' ) {
			$form_fields['title'] = array(
				'title'       => __( 'Method Title', 'alopeyk-woocommerce-shipping' ),
				'type'        => 'text',
				'default'     => __( 'Alopeyk', 'alopeyk-woocommerce-shipping'),
				'description' => __( 'This controls the title which the user will see during checkout proccess.', 'alopeyk-woocommerce-shipping' ),
			);
			$form_fields['store_options_title_spacer'] = array(
				'type'  => 'title',
				'title' => '&nbsp;'
			);
			$form_fields['store_options_title'] = array(
				'title'       => __( 'Store Details', 'alopeyk-woocommerce-shipping' ),
				'type'        => 'title',
			);
			$form_fields['store_name'] = array(
				'title'             => __( 'Store Name', 'alopeyk-woocommerce-shipping' ),
				'type'              => 'text',
				'description'       => __( 'This is your store\'s name.', 'alopeyk-woocommerce-shipping' ),
				'custom_attributes' => array(
					'required' => 'required'
				)
			);
			$form_fields['store_number'] = array(
				'title'             =>  __( 'Store Number', 'alopeyk-woocommerce-shipping' ),
				'type'              => 'text',
				'description'       => __( 'This is your store\'s number.', 'alopeyk-woocommerce-shipping' ),
				'custom_attributes' => array(
					'pattern' => '\d*',
				)
			);
			$form_fields['store_unit'] = array(
				'title'             => __( 'Store Unit', 'alopeyk-woocommerce-shipping' ),
				'type'              => 'text',
				'description'       => __( 'This is your store\'s unit.', 'alopeyk-woocommerce-shipping' ),
				'custom_attributes' => array(
					'pattern' => '\d*',
				)
			);
			$form_fields['store_phone'] = array(
				'title'             => __( 'Store Phone', 'alopeyk-woocommerce-shipping' ),
				'type'              => 'text',
				'description'       => __( 'This is your store\'s phone.', 'alopeyk-woocommerce-shipping' ),
				'custom_attributes' => array(
					'required' => 'required',
					'maxlength'=> '11',
					'pattern'  => '\d*',
				)
			);
			$form_fields['store_lat'] = array(
				'title'             => __( 'Store Latitude', 'alopeyk-woocommerce-shipping' ),
				'type'              => 'text',
				'description'       => __( 'Latitude for specified store address.', 'alopeyk-woocommerce-shipping' ),
				'custom_attributes' => array(
					'required' => 'required'
				)
			);
			$form_fields['store_lng'] = array(
				'title'             => __( 'Store Longitude', 'alopeyk-woocommerce-shipping' ),
				'type'              => 'text',
				'description'       => __( 'Longitude for specified store address.', 'alopeyk-woocommerce-shipping' ),
				'custom_attributes' => array(
					'required' => 'required'
				)
			);
			$form_fields['store_city'] = array(
				'title'             => __( 'Store City', 'alopeyk-woocommerce-shipping' ),
				'type'              => 'hidden',
				'class'             => 'disabled hide-parent-row',
				'default'           => '',
				'css'               => 'pointer-events: none;',
				'description'       => __( 'This will be automatically fetched when you specify your store location via moving below map marker.', 'alopeyk-woocommerce-shipping' ),
			);
			$form_fields['store_address'] = array(
				'title'             => __( 'Store Address', 'alopeyk-woocommerce-shipping' ),
				'type'              => 'hidden',
				'class'             => 'disabled',
				'default'           => '',
				'css'               => 'pointer-events: none;',
				'description'       => __( 'Please specify the exact address for your stock, because it will be used as origin address. The origin address will later be used for picking the packages by the courier and calculation of dynamic shipping cost.', 'alopeyk-woocommerce-shipping' ),
				'custom_attributes' => array(
					'data-autocomplete-placeholder' => __( 'Please enter your address ...', 'alopeyk-woocommerce-shipping' )
				)
			);
			$form_fields['store_description'] = array(
				'title'       => __( 'Origin Description', 'alopeyk-woocommerce-shipping' ),
				'type'        => 'textarea',
				'description' => __( 'This will be used as origin description shown on couriers device. In most cases it consists of store address details.', 'alopeyk-woocommerce-shipping' ),
			);
			$form_fields['map_options_title_spacer'] = array(
				'type'  => 'title',
				'title' => '&nbsp;'
			);
			$form_fields['map_options_title'] = array(
				'title' => __( 'Map Options', 'alopeyk-woocommerce-shipping' ),
				'type'  => 'title',
			);
			$form_fields['gmap_api_key'] = array(
				'title'             => __( 'Google Maps API Key', 'alopeyk-woocommerce-shipping' ),
				'type'              => 'text',
				'description'       => __( 'It is highly recommended to get a specific API key from <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">this link</a> for your store to avoid <a href="https://developers.google.com/maps/documentation/geocoding/usage-limits" target="_blank">limitation</a> issues.', 'alopeyk-woocommerce-shipping' ),
			);
			$form_fields['map_marker'] = array(
				'title'             => __( 'Marker Image', 'alopeyk-woocommerce-shipping' ),
				'type'              => 'hidden',
				'class'             => 'input-upload',
				'description'       => __( 'You can upload your desired marker image here to be used instead of Google\'s default marker image on address maps around your store.', 'alopeyk-woocommerce-shipping' ),
				'custom_attributes' => array(
					'data-upload-label'   => __( 'Upload', 'alopeyk-woocommerce-shipping' ),
					'data-remove-label'   => '<i class="dashicons dashicons-trash"></i>',
				)
			);
			$form_fields['map_styles'] = array(
				'type'  => 'textarea',
				'title' => __( 'Map Styles', 'alopeyk-woocommerce-shipping' ),
				'description'       => __( 'You can paste your custom map styles taken from <a href="http://mapstylr.com/" target="_blank">MapStyler</a>, <a href="https://snazzymaps.com/" target="_blank">SnazzyMaps</a> or similar tools and want to be applied to address maps.', 'alopeyk-woocommerce-shipping' ),
			);
			$form_fields['cost_options_title_spacer'] = array(
				'type'  => 'title',
				'title' => '&nbsp;'
			);
			$form_fields['cost_options_title'] = array(
				'title' => __( 'Cost Options', 'alopeyk-woocommerce-shipping' ),
				'type'  => 'title',
			);
			$form_fields['cost_type'] = array(
				'title'       => __( 'Cost Type', 'alopeyk-woocommerce-shipping' ),
				'type'        => 'select',
				'options'     => array(
					'dynamic' => __( 'Dynamic Cost', 'alopeyk-woocommerce-shipping' ),
					'static'  => __( 'Static Cost', 'alopeyk-woocommerce-shipping' )
				),
				'default'     => 'dynamic',
				'description' => __( 'This option will specify that whether the shipping cost is a static value or should be fetched from Alopeyk API.', 'alopeyk-woocommerce-shipping' ),
			);
			$form_fields['static_cost_type'] = array(
				'title'       => __( 'Static Cost Type', 'alopeyk-woocommerce-shipping' ),
				'type'        => 'select',
				'options'     => array(
					'percentage' => __( 'Percentage', 'alopeyk-woocommerce-shipping' ),
					'fixed'      => __( 'Fixed', 'alopeyk-woocommerce-shipping' )
				),
				'default'     => 'fixed',
				'description' => __( 'This option will specify that wether the shipping cost is a fixed value or a percentage of cart price.', 'alopeyk-woocommerce-shipping' ),
			);
			$form_fields['static_cost_fixed'] = array(
				'title'       => __( 'Fixed Cost', 'alopeyk-woocommerce-shipping' ),
				'type'        => 'text',
				'default'     => '0',
				'description' => __( 'This option defines the fixed cost should be added to total cart amount. (IRR)', 'alopeyk-woocommerce-shipping' ),
			);
			$form_fields['static_cost_percentage'] = array(
				'title'       => __( 'Percentage Cost', 'alopeyk-woocommerce-shipping' ),
				'type'        => 'text',
				'default'     => '0',
				'description' => __( 'This option defines the percentage of cart amount that should be added to total cart amount.', 'alopeyk-woocommerce-shipping' ),
			);
			$form_fields['auto_type'] = array(
				'title'       => __( 'Smart Switch', 'alopeyk-woocommerce-shipping' ),
				'type'        => 'checkbox',
				'default'     => 'yes',
				'label'       => __( 'Automatically switch from motorbike to cargo if weight or dimensions exceeds the limits.', 'alopeyk-woocommerce-shipping' ),
				'description' => __( 'If not checked, Alopeyk will not be visible in frontend if weight or dimensions of cart content exceeds the limits.', 'alopeyk-woocommerce-shipping' ),
			);
			$form_fields['order_options_title_spacer'] = array(
				'type'  => 'title',
				'title' => '&nbsp;'
			);
			$form_fields['order_options_title'] = array(
				'title' => __( 'Order Options', 'alopeyk-woocommerce-shipping' ),
				'type'  => 'title',
			);
			$form_fields[ 'status_change' ] = array(
				'title'             => __( 'Status Change', 'alopeyk-woocommerce-shipping' ),
				'label'             => __( 'Enabled', 'alopeyk-woocommerce-shipping' ),
				'type'              => 'checkbox',
				'default'           => 'yes',
				'description'       => __( 'Check this checkbox only if you want WooCommerce orders\' status to be changed based on changes being made in Alopeyk delivery status.', 'alopeyk-woocommerce-shipping' ),
			);
			$form_fields[ 'customer_dashboard' ] = array(
				'title'             => __( 'Dashboard Tracking', 'alopeyk-woocommerce-shipping' ),
				'label'             => __( 'Enabled', 'alopeyk-woocommerce-shipping' ),
				'type'              => 'checkbox',
				'default'           => 'yes',
				'description'       => __( 'Check this checkbox only if you want your customers to be able to track their delivering from their account dashboard.', 'alopeyk-woocommerce-shipping' ),
			);
			$form_fields[ 'tehran_timezone' ] = array(
				'title'             => __( 'Use Tehran TimeZone', 'alopeyk-woocommerce-shipping' ),
				'label'             => __( 'Enabled', 'alopeyk-woocommerce-shipping' ),
				'type'              => 'checkbox',
				'default'           => 'yes',
				'description'       => __( 'Check this checkbox only if you want to use “Tehran TomeZone” for Alopeyk orders, otherwise default Wordpress timezone will be used.', 'alopeyk-woocommerce-shipping' ),
			);
			$form_fields['refresh_options_title_spacer'] = array(
				'type'  => 'title',
				'title' => '&nbsp;'
			);
			$form_fields['refresh_options_title'] = array(
				'title' => __( 'Refresh Options', 'alopeyk-woocommerce-shipping' ),
				'type'  => 'title',
			);
			$form_fields[ 'refresh_cron_interval' ] = array(
				'title'       => __( 'Cron Interval', 'alopeyk-woocommerce-shipping' ),
				'label'       => __( 'Enabled' ),
				'type'        => 'text',
				'default'     => '10',
				'description' => __( 'The number of seconds between each request for fetching the latest status of an active order. It is highly recommended to be more than 10.', 'alopeyk-woocommerce-shipping' ),
				'placeholder' => '10',
				'custom_attributes' => array(
					'pattern' => '\d*'
				)
			);
			$form_fields[ 'refresh_admin_interval' ] = array(
				'title'       => __( 'Admin Refresh Interval', 'alopeyk-woocommerce-shipping' ),
				'type'        => 'text',
				'default'     => '10',
				'description' => __( 'The number of seconds between refreshes in admin panel for bringing latest order details to the screen. It is highly recommended to be more than 10.', 'alopeyk-woocommerce-shipping' ),
				'placeholder' => '10',
				'custom_attributes' => array(
					'pattern' => '\d*'
				)
			);
			$form_fields[ 'refresh_public_interval' ] = array(
				'title'       => __( 'Front Refresh Interval', 'alopeyk-woocommerce-shipping' ),
				'type'        => 'text',
				'default'     => '10',
				'description' => __( 'The number of seconds between refreshes in customer dashboard for bringing latest order details to the screen. It is highly recommended to be more than 10.', 'alopeyk-woocommerce-shipping' ),
				'placeholder' => '10',
				'custom_attributes' => array(
					'pattern' => '\d*'
				)
			);
			if ( is_admin() ) {
				$wc_payment_gateways = @WC()->payment_gateways;
				if ( $wc_payment_gateways ) {
					$gateways = $wc_payment_gateways->get_available_payment_gateways();
					if ( $gateways && count( $gateways ) ) {
						$form_fields['payment_options_title_spacer'] = array(
							'type'  => 'title',
							'title' => '&nbsp;'
						);
						$form_fields['payment_options_title'] = array(
							'title' => __( 'Payment Options', 'alopeyk-woocommerce-shipping' ),
							'type'  => 'title',
						);
						foreach ( $gateways as $gateway ) { 
							$form_fields[ 'return_' . $gateway->id ] = array(
								'title'             => $gateway->title,
								'label'             => __( 'Has return', 'alopeyk-woocommerce-shipping' ),
								'type'              => 'checkbox',
								'default'           => $gateway->id == 'cod' ? 'yes' : 'no',
								'description'       => __( 'Check this checkbox only if you need this payment method to have return trip. For example if want the courier to take the money from the customer after delivering the package and bring it back to your store.', 'alopeyk-woocommerce-shipping' ),
								'custom_attributes' => array(
									'data-checkbox-toggle-target' => 'toggler-checkbox-id-' . $gateway->id
								)
							);
							$form_fields[ 'return_' . $gateway->id . '_customer' ] = array(
								'label'             => __( 'Customer should pay for return cost', 'alopeyk-woocommerce-shipping' ),
								'type'              => 'checkbox',
								'default'           => 'no',
								'description'       => __( 'Check this checkbox only if you want customers to pay for return costs whenever this payment method is chosen. If not checked, the cost will be deducted from your Alopeyk account.', 'alopeyk-woocommerce-shipping' ),
								'custom_attributes' => array(
									'data-checkbox-toggle-id' => 'toggler-checkbox-id-' . $gateway->id,
									'data-checkbox-toggle-target' => 'toggler-checkbox-id-' . $gateway->id . '-child'
								)
							);
							$form_fields[ 'return_' . $gateway->id . '_customer_alert' ] = array(
								'label'             => __( 'Warn customer about price change', 'alopeyk-woocommerce-shipping' ),
								'type'              => 'checkbox',
								'default'           => 'yes',
								'description'       => __( 'Check this checkbox only if you want inform customers about the return cost that will be added to total price.', 'alopeyk-woocommerce-shipping' ),
								'custom_attributes' => array(
									'data-checkbox-toggle-id' => 'toggler-checkbox-id-' . $gateway->id . '-child'
								)
							);
						}
					}
				}
			}
		}
		$this->form_fields = $form_fields;

	}

	/**
	 * @since 1.0.0
	 */
	public function admin_options() {

		if ( $this->wrong_key == 'yes' && ! empty( $this->api_key ) ) {
			$this->errors->add( 'wrong_key', __( 'The <strong>API key</strong> is not valid.', 'alopeyk-woocommerce-shipping' ) );
		}
		$empty_fields = array();
		foreach ( $this->required_fields as $required_field ) {
			if ( isset( $this->form_fields[ $required_field ] ) && empty( $this->{$required_field} ) ) {
				$empty_fields[] = $this->form_fields[ $required_field ]['title'];
			}
		}
		if ( count( $empty_fields ) ) {
			$this->empty_fields_string = '';
			$empty_fields = array_map( function ( $field, $index ) use ( $empty_fields ) {
				$this->empty_fields_string .= ( $index == 0 ? '' : ( $index == count( $empty_fields ) - 1 ? ' ' . __( 'and', 'alopeyk-woocommerce-shipping' ) . ' ' : __( ',', 'alopeyk-woocommerce-shipping' ) . ' ' ) ) . '<strong>' . $field . '</strong>';
				return $field;
			}, $empty_fields, array_keys( $empty_fields ) );
			$this->errors->add( 'missing', sprintf( __( 'Please fill %s field(s), otherwise Alopeyk shipping method cannot be enabled.', 'alopeyk-woocommerce-shipping' ), $this->empty_fields_string ) );
		}
		foreach ( $this->errors->get_error_messages() as $error ) {
			echo '<div class="error notice below-heading is-dismissible"><p>' . $error . '</p></div>';
		}
		parent::admin_options();

	}

	/**
	 * @since 1.0.0
	 */
	public function set_helpers() {

		$this->helpers = new Alopeyk_WooCommerce_Shipping_Common();

	}

	/**
	 * @since  1.0.0
	 * @param  array $package
	 * @return array
	 */
	public function get_package_data( $package ) {

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
		$overflowed = $this->helpers->has_overflow( $weights, $dimensions, get_option( 'woocommerce_weight_unit' ), get_option( 'woocommerce_dimension_unit' ) );
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
			$shipping = (object) $this->helpers->calculate_shipping( $package );
			if ( isset( $shipping->cost ) ) {
				if ( $shipping->cost_type == 'dynamic' ) {
					$cost_details = (object) $shipping->cost_details;
					WC()->session->set( 'return_cost', $this->helpers->normalize_price( $cost_details->price_with_return - $cost_details->price ) );
				}
				$method_title = $this->title . ' (' . ( $shipping->type == 'motorbike' ? __( 'Motorbike', 'alopeyk-woocommerce-shipping' ) : ( $shipping->type == 'cargo' ? __( 'Cargo', 'alopeyk-woocommerce-shipping' ) : __( $shipping->type, 'alopeyk-woocommerce-shipping' ) ) ) . ')';
				$rate = array(
					'id'    => $this->id,
					'label' => $method_title,
					'cost'  => $this->helpers->normalize_price( $shipping->cost ),
				);
				$this->add_rate( $rate );
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
		if ( $package && count( $package ) ) {
			return $this->helpers->is_available( $this->get_package_data( $package ) );
		}
		return false;

	}

	/**
	 * @since  1.0.0
	 * @return array
	 */
	public function process_admin_options() {

		$this->init_settings();
		$post_data = $this->get_post_data();
		$fields = $this->get_form_fields();
		foreach ( $fields as $key => $field ) {
			if ( 'title' != $this->get_field_type( $field ) ) {
				try {
					$this->settings[ $key ] = $this->get_field_value( $key, $field, $post_data );
				} catch ( Exception $e ) {
					$this->add_error( $e->getMessage() );
				}
			}
		}
		$api_key = $this->get_field_value( 'api_key', $fields['api_key'], $post_data );
		if ( ! empty( $api_key ) ) {
			if ( $this->helpers->authenticate( true, $api_key, true ) && $user_data = $this->helpers->get_user_data() ) {
				$phone = isset( $fields['store_phone'] ) ? $this->get_field_value( 'store_phone', $fields['store_phone'], $post_data ) : '';
				$name  = isset( $fields['store_name'] ) ? $this->get_field_value( 'store_name', $fields['store_name'], $post_data ) : '';
				if ( empty( $phone ) ) {
					$this->settings[ 'store_phone' ] = $user_data->phone;
				}
				if ( empty( $name ) ) {
					$this->settings[ 'store_name' ] = $user_data->firstname . ' ' . $user_data->lastname;
				}
				$this->settings[ 'wrong_key' ] = 'no';
			} else {
				$this->settings[ 'enabled' ] = 'no';
				$this->settings[ 'wrong_key' ] = 'yes';
			}
		}
		$empty_fields = array();
		foreach ( $this->required_fields as $required_field ) {
			if ( ! isset( $this->settings[$required_field] ) || empty( $this->settings[$required_field] ) ) {
				$this->settings[ 'enabled' ] = 'no';
				break;
			}
		}
		return update_option( $this->get_option_key(), apply_filters( 'woocommerce_settings_api_sanitized_fields_' . $this->id, $this->settings ) );

	}

}
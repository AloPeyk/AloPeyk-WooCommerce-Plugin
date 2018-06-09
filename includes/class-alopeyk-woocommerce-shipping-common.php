<?php

/**
 * The core functionality of the plugin.
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

if ( class_exists( 'Alopeyk_WooCommerce_Shipping_Common' ) ) {
	return;
}

use AloPeyk\AloPeykApiHandler;
use AloPeyk\Model\Address;
use AloPeyk\Model\Order;
use AloPeyk\Config\Configs;

/**
 * @since 1.0.0
 */
class Alopeyk_WooCommerce_Shipping_Common {

	private $plugin_name;
	private $version;

	private $config;

	public static $order_post_type_name = 'alopeyk_order';
	public static $order_status_taxonomy_name = 'alopeyk_order_status';

	public static $configs = array(
		'max_weight'                   => 25000,                                                           // g
		'max_length'                   => 45,                                                              // cm
		'max_width'                    => 45,                                                              // cm
		'max_height'                   => 45,                                                              // cm
		'addresses_limit'              => 5,
		'cancel_penalty_delay'         => 5,                                                               // Minutes
		'cancel_penalty_amount'        => 0,                                                               // Tomans
		'schedule_days_count'          => 30,
		'schedule_time_interval'       => 1,                                                               // Minutes
		'schedule_first_request_delay' => 1,                                                               // Minutes
		'credit_amounts'               => array( '10000', '20000', '30000', '50000', '100000', '200000' ), // Tomans
		'supportTel'                   => '+982141346',
		'devEmail'                     => 'dev@alopeyk.com',
	);

	/**
	 * @since 1.0.0
	 * @param string $plugin_name
	 * @param string $version
	 */
	public function __construct( $plugin_name = null, $version = null ) {

		$this->version     = $version;
		$this->plugin_name = $plugin_name;

	}

	/**
	 * @since  1.0.0
	 * @return array
	 */
	public function plugin_override() {

		if ( ! function_exists( 'WC' ) ) {
			function WC() {
				return $GLOBALS['woocommerce'];
			}
		}

	}

	/**
	 * @since 1.0.0
	 */
	public function shipping_init() {

		include_once( 'class-alopeyk-woocommerce-shipping-method.php' );

	}

	/**
	 * @since 1.0.0
	 * @param string $message
	 */
	public function add_log( $message = null ) {

		if ( $message ) {
			date_default_timezone_set( $this->get_timezone_setting() );
			error_log( $message, 0 );
			$logger = new WC_Logger();
			$logger->add( METHOD_ID, $message );
		}

	}

	/**
	 * @since  1.0.0
	 * @return string
	 */
	public function get_log_url() {

		if ( defined( 'WC_LOG_DIR' ) ) {
			$log_url = add_query_arg( 'tab', 'logs', add_query_arg( 'page', 'wc-status', admin_url( 'admin.php' ) ) );
			$log_key = METHOD_ID . '-' . sanitize_file_name( wp_hash( METHOD_ID ) ) . '-log';
			return add_query_arg( 'log_file', $log_key, $log_url );
		}
		return false;

	}

	/**
	 * @since  1.0.0
	 * @param  array $methods
	 * @return array
	 */
	public function add_method( $methods ) {

		$methods[] = METHOD_ID;
		return $methods;

	}

	/**
	 * @since 1.0.0
	 */
	public function localize_scripts() {

		$data = is_admin() ? array(
			'schedule_dates' => $this->get_schedule_dates()
		) : null;
		wp_localize_script( $this->plugin_name, 'awcshm', $this->get_localize_data( $data ) );

	}

	/**
	 * @since  1.0.0
	 * @param  array $data
	 * @return array
	 */
	public function get_localize_data( $data = null ) {

		return array(
			'alopeyk' => array(
				'wcshm' => array(
					'id'      => METHOD_ID,
					'name'    => $this->plugin_name,
					'version' => $this->version,
					'map'     => array(
						'marker'  => $this->get_option( 'map_marker' ),
						'styles'  => $this->get_option( 'map_styles' ),
						'api_key' => $this->get_gmap_api_key(),
					),
					'loader'  => $this->get_loader_url(),
					'scope'   => $data
				)
			),
			'woocommerce' => array(
				'version'  => WC()->version,
				'checkout' => is_checkout()
			),
			'ajaxOptions' => array (
				'url'   => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( $this->plugin_name )
			),
			'translations' => array(
				'Ship'                   => __( 'Ship', 'alopeyk-woocommerce-shipping' ),
				'Submit'                 => __( 'Submit', 'alopeyk-woocommerce-shipping' ),
				'Cancel'                 => __( 'Cancel', 'alopeyk-woocommerce-shipping' ),
				'Close'                  => __( 'Close', 'alopeyk-woocommerce-shipping' ),
				'Add Alopeyk Coupon'     => __( 'Add Alopeyk Coupon', 'alopeyk-woocommerce-shipping' ),
				'Add Alopeyk Credit'     => __( 'Add Alopeyk Credit', 'alopeyk-woocommerce-shipping' ),
				'Cancel Alopeyk Order'   => __( 'Cancel Alopeyk Order', 'alopeyk-woocommerce-shipping' ),
				'Rate Alopeyk Courier'   => __( 'Rate Alopeyk Courier', 'alopeyk-woocommerce-shipping' ),
				'Cancel Order'           => __( 'Cancel Order', 'alopeyk-woocommerce-shipping' ),
				'Alopeyk Order'          => __( 'Alopeyk Order', 'alopeyk-woocommerce-shipping' ),
				'Alopeyk Coupon'         => __( 'Alopeyk Coupon', 'alopeyk-woocommerce-shipping' ),
				'Add Coupon'             => __( 'Add Coupon', 'alopeyk-woocommerce-shipping' ),
				'Pay'                    => __( 'Pay', 'alopeyk-woocommerce-shipping' ),
				'Apply'                  => __( 'Apply', 'alopeyk-woocommerce-shipping' ),
				'Yes'                    => __( 'Yes', 'alopeyk-woocommerce-shipping' ),
				'No'                     => __( 'No', 'alopeyk-woocommerce-shipping' ),
				'Track Order'            => __( 'Track Order', 'alopeyk-woocommerce-shipping' ),
				'View Order'             => __( 'View Order', 'alopeyk-woocommerce-shipping' ),
				'View Invoice'           => __( 'View Invoice', 'alopeyk-woocommerce-shipping' ),
				'Ship via Alopeyk'       => __( 'Ship via Alopeyk', 'alopeyk-woocommerce-shipping' ),
				'Unkown error occurred.' => __( 'Unkown error occurred.', 'alopeyk-woocommerce-shipping' ),
				'Request failed:'        => __( 'Request failed:', 'alopeyk-woocommerce-shipping' )
			),
			'dynamic_parts' => $this->get_dynamic_parts( is_admin() ),
			'refresh_interval' => $this->get_option( ( is_admin() ? 'refresh_admin_interval' : 'refresh_public_interval' ), 10 ),
			'time' => (int) $this->get_now_in_milliseconds()
		);

	}

	/**
	 * @since  1.1.0
	 * @return int
	 */
	public function get_now_in_milliseconds() {

		$mt = explode( ' ', microtime() );
		return ( (int) $mt[1] ) * 1000 + ( (int) round( $mt[0] * 1000 ) );

	}

	/**
	 * @since  1.0.0
	 * @param  boolean $is_admin
	 * @return array
	 */
	public function get_dynamic_parts( $is_admin = true ) {

		global $post;
		$dynamic_parts = array();
		if ( $is_admin ) {
			$dynamic_parts = array(
				'.awcshm-credit-widget',
				'#woocommerce-order-notes',
				'#alopeyk_woocommerce_shipping_method-wcorder-actions',
				'#alopeyk_woocommerce_shipping_method-wcorder-history'
			);
			$screen = get_current_screen();
			if ( in_array( $screen->id, array( 'edit-shop_order', 'edit-' . self::$order_post_type_name ) ) ) {
				$dynamic_parts[] = '.wrap';
			}
			if ( in_array( $screen->id, array( self::$order_post_type_name ) ) && in_array( get_post_status( $post->ID ), array( 'awcshm-progress', 'awcshm-pending', 'awcshm-scheduled' ) ) ) {
				$dynamic_parts[] = '#alopeyk_woocommerce_shipping_method-order-info-actions .inside';
				$dynamic_parts[] = '#alopeyk_woocommerce_shipping_method-order-courier-actions .inside';
				$dynamic_parts[] = '#alopeyk_woocommerce_shipping_method-order-shipping-actions .inside';
			}
		}
		return $dynamic_parts;

	}

	/**
	 * @since  1.0.0
	 * @return string
	 */
	public function get_loader_url() {

		return includes_url() . 'images/spinner.gif';

	}

	/**
	 * @since  1.0.0
	 * @return array
	 */
	public function index_reverse_translation() {

		$clauses = array(
			__( 'دیر رسیدن به مبدا یا مقصد', 'alopeyk-woocommerce-shipping' ),
			__( 'برخورد و رفتار بد', 'alopeyk-woocommerce-shipping' ),
			__( 'ظاهر نامرتب و بی نظم', 'alopeyk-woocommerce-shipping' ),
			__( 'برخورد و رفتار بد', 'alopeyk-woocommerce-shipping' ),
			__( 'دیر رسیدن به مبدا یا مقصد', 'alopeyk-woocommerce-shipping' ),
			__( 'درخواست هزینه اضافه', 'alopeyk-woocommerce-shipping' ),
			__( 'عدم تماس با درخواست دهنده', 'alopeyk-woocommerce-shipping' ),
			__( 'نداشتن باکس حمل مرسوله', 'alopeyk-woocommerce-shipping' ),
			__( 'عدم تسلط بر مسیر', 'alopeyk-woocommerce-shipping' ),
			__( 'سایر موارد', 'alopeyk-woocommerce-shipping' ),
			__( 'برخورد و رفتار بد', 'alopeyk-woocommerce-shipping' ),
			__( 'پیک تقاضای لغو درخواست نمود', 'alopeyk-woocommerce-shipping' ),
			__( 'فاصله پیک تا مبدا', 'alopeyk-woocommerce-shipping' ),
			__( 'سفیر باکس حمل مرسوله به همراه نداشت', 'alopeyk-woocommerce-shipping' ),
			__( 'عدم تماس با درخواست دهنده', 'alopeyk-woocommerce-shipping' ),
			__( 'سایر موارد', 'alopeyk-woocommerce-shipping' )
		);
		return $clauses;

	}

	/**
	 * @since  1.0.0
	 * @param  object $order
	 * @return string
	 */
	public function get_order_address_status( $order ) {

		$status = $order->status;
		$next_address_any = isset( $order->next_address_any ) ? $order->next_address_any : null;
		$has_return = $order->has_return;
		$addresses_count = count( $order->addresses );
		$eta = $order->eta_minimal;
		$hasSingleAddress = $addresses_count < 3 || ( $addresses_count == 3 && $has_return );
		$statusText = __( 'Please wait ...', 'alopeyk-woocommerce-shipping' );
		$duration = $eta && $eta->duration ? ceil( $eta->duration / 60 ) : 0;
		if ( $next_address_any ) {
			if ( $next_address_any->status === 'pending' && $eta && (int) $eta->address_id === $next_address_any->id ) {
				$remainedTimeString = $duration ? $duration . ' ' . __( 'minute(s)', 'alopeyk-woocommerce-shipping' ) : __( 'Less than a minute', 'alopeyk-woocommerce-shipping' );
				$statusText = sprintf( __( '%s left until the courier %s %s.', 'alopeyk-woocommerce-shipping' ), $remainedTimeString, ( in_array( $next_address_any->type, array( 'origin', 'destination' ) ) ? __( 'reaches', 'alopeyk-woocommerce-shipping' ) : __( 'returns', 'alopeyk-woocommerce-shipping' ) ), ( in_array( $next_address_any->type, array( 'origin', 'return' ) ) ? __( 'origin', 'alopeyk-woocommerce-shipping' ) : __( 'destination', 'alopeyk-woocommerce-shipping' ) . ( $addresses_count < 3 || ( $addresses_count == 3 && $has_return ) ? '' : ' ' . $next_address_any->priority ) ) );
			} else if ( $next_address_any->status === 'arrived' ) {
				$statusText = sprintf( ( $next_address_any->type == 'return' ? __( 'Courier returned to %s.', 'alopeyk-woocommerce-shipping' ) : __( 'Courier reached %s.', 'alopeyk-woocommerce-shipping' ) ), ( in_array ( $next_address_any->type, array( 'origin', 'return' ) ) ? __( 'origin', 'alopeyk-woocommerce-shipping' ) : __( 'destination', 'alopeyk-woocommerce-shipping' ) . ( $hasSingleAddress ? '' : ' ' . ( $next_address_any->type ? $next_address_any->priority : $addresses_count - 1 - ( $has_return ? 1 : 0 ) ) ) ) );
			} else {
				if ( in_array( $status, array( 'searching', 'new' ) ) ) {
					$statusText = __( 'Searching for closest courier.', 'alopeyk-woocommerce-shipping' );
				} else if ( $status === 'picking' ) {
					$statusText = __( 'Courier is driving to origin.', 'alopeyk-woocommerce-shipping' );
				} else if ( $status === 'delivering' ) {
					$statusText = sprintf( __( 'Courier is driving to %s.', 'alopeyk-woocommerce-shipping' ), ( $next_address_any->type == 'return' ? __( 'origin', 'alopeyk-woocommerce-shipping' ) : __( 'destination', 'alopeyk-woocommerce-shipping' ) . ( $hasSingleAddress ? '' : ' ' . $next_address_any->priority ) ) );
				} else if ( $status === 'delivered' ) {
					$statusText = sprintf( __( 'Courier arrived at destination%s.', 'alopeyk-woocommerce-shipping' ), ( $hasSingleAddress ? '' : ' ' . ( $next_address_any->type ? $next_address_any->priority : $addresses_count - 1 - ( $has_return ? 1 : 0 ) ) ) );
				} else if ( $status === 'accepted' ) {
					$statusText = __( 'Courier is driving to origin.', 'alopeyk-woocommerce-shipping' );
				}
			}
		} else {
			$statusText = __( 'Courier arrived at destination.', 'alopeyk-woocommerce-shipping' );
		}
		return $statusText;

	}

	/**
	 * @since  1.0.0
	 * @param  string  $uri
	 * @param  boolean $remove_random_string
	 * @return string
	 */
	public function get_signature_url( $uri = '', $remove_random_string = true ) {

		if ( $remove_random_string ) {
			$uri = strpos( $uri, '?' ) !== false ? explode( '?', $uri ) : array( $uri );
			$uri = $uri[0];
		}
		return AloPeykApiHandler::getSignaturePath( ltrim( $uri, '/' ) );

	}

	/**
	 * @since  1.0.0
	 * @return array
	 */
	public function get_schedule_dates() {

		date_default_timezone_set( $this->get_timezone_setting() );
		$days_count = (float) $this->get_config( 'schedule_days_count' );
		$time_interval = min( (float) $this->get_config( 'schedule_time_interval' ), 59 );
		$first_request_delay = (float) $this->get_config( 'schedule_first_request_delay' );
		$from = date( 'Y-m-d H:i:s', strtotime( '+ ' . $first_request_delay . 'minutes' ) );
		$times = array();
		$schedule_dates = array(
			'dates' => null,
			'steps' => $time_interval,
			'error' => __( 'You have chosen a date which is passed. So your order will be shipped as soon as being created. Are you sure?', 'alopeyk-woocommerce-shipping' )
		);
		for ( $t = 0; $t < 24 * 60 / $time_interval; $t++ ) {
			$time = $t * $time_interval / 60;
			$time = sprintf( '%02d:%02d', (int) $time, round( fmod( $time, 1 ) * 60 ) );
			$times[ $time . ':00' ] = date_i18n( 'H:i', strtotime( $time ) );
		}
		for ( $i = 0; $i < $days_count; $i++ ) {
			$times_filtered = $times;
			$date = date( 'Y-m-d', strtotime( $from . ' +' . $i . ' days' ) );
			if ( $i == 0 ) {
				$time = explode( ':', date( 'H:i', strtotime( $from ) ) );
				$pieces = round ( ( ( $time[0] * 60 ) + $time[1] ) / $time_interval );
				$times_filtered = array_slice( $times_filtered, $pieces + 1 );
			}
			if ( count( $times_filtered ) ) {
				$initial_time = explode( ':', array_values($times_filtered)[0] );
				$schedule_dates['dates'][$date] = array(
					// 'times'          => $times_filtered,
					'label'          => date_i18n( 'j F Y', strtotime( $date ) ),
					'initial_hour'   => (int) $initial_time[0],
					'initial_minute' => (int) $initial_time[1]
				);
			}
		}
		return $schedule_dates;

	}

	/**
	 * @since  1.0.0
	 * @return string
	 */
	public function get_gmap_api_key() {

		return $this->get_option( 'gmap_api_key', 'AIzaSyBgNlYgCBNDiwArVqJBHJ3RZo4zR3CVYcg', false );

	}

	/**
	 * @since 1.0.0
	 */
	public function recalculate_shipping() {

		if ( $this->is_enabled() ) {
			$cost_type = $this->get_option( 'cost_type' );
			if ( ! $cost_type || $cost_type == 'dynamic' ) {
				$packages = WC()->cart->get_shipping_packages();
				foreach ( $packages as $key => $value ) {
					$shipping_session = 'shipping_for_package_' . $key;
					unset( WC()->session->$shipping_session );
				}
			}
		}
		return;

	}

	/**
	 * @since  1.0.0
	 * @return array
	 */
	public function get_options() {

		$options = 'woocommerce_' . METHOD_ID . '_settings';
		return class_exists( 'WC_Admin_Settings' ) ? WC_Admin_Settings::get_option( $options ) : get_option( $options );

	}

	/**
	 * @since  1.0.0
	 * @param  string  $option
	 * @param  mixed   $default
	 * @param  boolean $can_be_empty
	 * @return mixed
	 */
	public function get_option( $option, $default = null, $can_be_empty = true ) {

		$options = $this->get_options();
		return isset( $options[ $option ] ) && ( $can_be_empty || ! empty( $options[ $option ] ) ) ? $options[ $option ] : $default;

	}

	/**
	 * @since  1.0.0
	 * @return boolean
	 */
	public function is_enabled() {

		$enabled = $this->get_option( 'enabled' );
		return $enabled && $enabled == 'yes';

	}

	/**
	 * @since  1.0.0
	 * @param  boolean $show_shipping
	 * @return boolean
	 */
	public function show_shipping( $show_shipping ) {

		if ( $this->is_enabled() && is_cart() ) {
			$show_shipping = false;
		}
		return $show_shipping;

	}

	/**
	 * @since  1.0.0
	 * @return integer
	 */
	public function get_user_id() {

		return get_current_user_id();

	}

	/**
	 * @since  1.0.0
	 * @param  integer $user_id
	 * @return array
	 */
	public function get_user( $user_id = null ) {

		$user_id = $user_id ? $user_id : $this->get_user_id();
		return get_userdata( $user_id );

	}

	/**
	 * @since  1.0.0
	 * @param  string $meta
	 * @return mixed
	 */
	public function get_user_meta( $meta = null ) {

		if ( $meta ) {
			$user_id = get_current_user_id();
			$user = get_userdata( $user_id );
			if ( $user ) {
				return get_user_meta( $user_id, $meta, true );
			}
		}
		return null;

	}

	/**
	 * @since  1.0.0
	 * @param  array  $actions
	 * @param  object $order
	 * @return array
	 */
	public function add_tracking_button_caller( $actions, $order ) {

		$this->add_tracking_button( $order );
		return $actions;

	}

	/**
	 * @since 1.0.0
	 * @param object $order
	 */
	public function add_tracking_button( $order = null ) {
		
		if ( $order ) {
			$last_alopeyk_order = $this->get_order_history( $order->get_id(), array( 'posts_per_page' => 1 ) );
			if ( $last_alopeyk_order ) {
				$last_alopeyk_order = $last_alopeyk_order[0];
				$order_data = get_post_meta( $last_alopeyk_order['id'], '_awcshm_order_data', true );
				$wc_orders = get_post_meta( $last_alopeyk_order['id'], '_awcshm_wc_order_id' );
				if ( $this->can_be_tracked( $order_data ) && $this->get_option( 'customer_dashboard', 'yes' ) == 'yes' && $wc_orders && count( $wc_orders ) == 1 ) {
					echo '<a href="' . $this->get_tracking_url( $order_data, false ) . '" target="_blank" class="button awcshm-dashboard-track-button">' . __( 'Track Order', 'alopeyk-woocommerce-shipping' ) . '</a>';
				}
			}
		}

	}

	/**
	 * @since 1.0.0
	 * @param array $checkout
	 */
	public function add_address_fields( $checkout = null ) {

		if ( $this->is_enabled() ) {
			$shipping_address_latitude  = $checkout ? WC()->session->get( 'destination_latitude' )  : null;
			$shipping_address_longitude = $checkout ? WC()->session->get( 'destination_longitude' ) : null;
			$shipping_address_city      = $checkout ? WC()->session->get( 'destination_city' )      : null;
			$shipping_address           = $checkout ? WC()->session->get( 'destination_address' )   : null;
			$shipping_address_unit      = $checkout ? WC()->session->get( 'destination_unit' )      : null;
			$shipping_address_number    = $checkout ? WC()->session->get( 'destination_number' )    : null;
			echo '<div id="awcshm-address-details"><h3>' . __( 'Address Details', 'alopeyk-woocommerce-shipping' ) . '</h3>';
			woocommerce_form_field( 'destination_latitude', array(
				'type'              => 'text',
				'required'          => true,
				'class'             => array( 'awcshm-hidden' ),
				'custom_attributes' => array(
					'style' => 'display: none;'
				)
			), $shipping_address_latitude ? $shipping_address_latitude : $this->get_user_meta( 'shipping_address_latitude' ) );
			woocommerce_form_field( 'destination_longitude', array(
				'type'              => 'text',
				'required'          => true,
				'class'             => array( 'awcshm-hidden' ),
				'custom_attributes' => array(
					'style' => 'display: none;'
				)
			), $shipping_address_longitude ? $shipping_address_longitude : $this->get_user_meta( 'shipping_address_longitude' ) );
			woocommerce_form_field( 'destination_city', array(
				'type'              => 'text',
				'required'          => true,
				'class'             => array( 'awcshm-hidden' ),
				'custom_attributes' => array(
					'style' => 'display: none;',
				)
			), $shipping_address_city ? $shipping_address_city : $this->get_user_meta( 'shipping_address_city' ) );
			woocommerce_form_field( 'destination_address', array(
				'type'              => 'text',
				'required'          => true,
				'custom_attributes' => array(
					'style' => 'display: none;',
					'data-autocomplete-placeholder' => __( 'Please enter your address ...', 'alopeyk-woocommerce-shipping' )
				)
			), $shipping_address ? $shipping_address : $this->get_user_meta( 'shipping_address' ) );
			woocommerce_form_field( 'destination_unit', array(
				'type'              => 'text',
				'class'             => array( 'form-row-first' ),
				'label'             => __( 'Unit', 'alopeyk-woocommerce-shipping' ),
				'custom_attributes' => array(
					'pattern' => '\d*',
				)
			), $shipping_address_unit ? $shipping_address_unit : $this->get_user_meta( 'shipping_address_unit' ) );
			woocommerce_form_field( 'destination_number', array(
				'type'              => 'text',
				'class'             => array( 'form-row-last' ),
				'label'             => __( 'Plaque', 'alopeyk-woocommerce-shipping' ),
				'custom_attributes' => array(
					'pattern' => '\d*',
				)
			), $shipping_address_number ? $shipping_address_number : $this->get_user_meta( 'shipping_address_number' ) );
			echo '<div class="clear"></div></div>';
		}

	}

	/**
	 * @since 1.0.0
	 * @param integer $user_id
	 * @param string  $type
	 */
	public function save_address_fields( $user_id = null, $type = null ) {

		if ( $user_id && $type && $type == 'shipping' ) {
			$data = (object) $_POST;
			if ( isset ( $data->destination_latitude ) ) {
				update_user_meta( $user_id, 'shipping_address_latitude', htmlentities( $data->destination_latitude ) );
			}
			if ( isset ( $data->destination_longitude ) ) {
				update_user_meta( $user_id, 'shipping_address_longitude', htmlentities( $data->destination_longitude ) );
			}
			if ( isset ( $data->destination_city ) ) {
				update_user_meta( $user_id, 'shipping_address_city', htmlentities( $data->destination_city ) );
			}
			if ( isset ( $data->destination_address ) ) {
				update_user_meta( $user_id, 'shipping_address', htmlentities( $data->destination_address ) );
			}
			if ( isset ( $data->destination_unit ) ) {
				update_user_meta( $user_id, 'shipping_address_unit', htmlentities( $data->destination_unit ) );
			}
			if ( isset ( $data->destination_number ) ) {
				update_user_meta( $user_id, 'shipping_address_number', htmlentities( $data->destination_number ) );
			}
		}

	}

	/**
	 * @since 1.0.0
	 */
	public function check_checkout_fields() {

		if ( $this->is_enabled() ) {
			$data = (object) $_POST;
			if ( isset( $data->shipping_method )       && in_array( METHOD_ID, $data->shipping_method ) &&
			   ( isset( $data->destination_latitude )  && ( ! $data->destination_latitude  || empty( $data->destination_latitude ) ) )  ||
			   ( isset( $data->destination_longitude ) && ( ! $data->destination_longitude || empty( $data->destination_longitude ) ) ) ||
			   ( isset( $data->destination_address )   && ( ! $data->destination_address   || empty( $data->destination_address ) ) )   ||
			   ( isset( $data->destination_city )      && ( ! $data->destination_city      || empty( $data->destination_city ) ) )
			) {
				wc_add_notice( __( 'Please specify your exact location on the map.', 'alopeyk-woocommerce-shipping' ), 'error' );
			}
		}

	}

	/**
	 * @since 1.0.0
	 * @param integer $order_id
	 */
	public function update_order_meta( $order_id ) {

		if ( $this->is_enabled() ) {
			$data = (object) $_POST;
			if ( in_array( METHOD_ID, $data->shipping_method ) ) {
				$create_account = isset( $data->createaccount ) && $data->createaccount;
				$user_id = $create_account ? wc_get_order( $order_id )->get_user_id() : null;
				$order = wc_get_order( $order_id );
				$shipping_prefix = '_shipping_';
				if ( isset( $data->destination_latitude ) && $data->destination_latitude ) {
					$order->update_meta_data( $shipping_prefix . 'address_latitude', $data->destination_latitude );
					if ( $user_id ) {
						update_user_meta( $user_id, 'shipping_address_latitude', esc_attr( $data->destination_latitude ) );
					}
				}
				if ( isset( $data->destination_longitude ) && $data->destination_longitude ) {
					$order->update_meta_data( $shipping_prefix . 'address_longitude', $data->destination_longitude );
					if ( $user_id ) {
						update_user_meta( $user_id, 'shipping_address_longitude', esc_attr( $data->destination_longitude ) );
					}
				}
				if ( isset( $data->destination_address ) && $data->destination_address ) {
					$order->update_meta_data( $shipping_prefix . 'address_location', $data->destination_address );
					if ( $user_id ) {
						update_user_meta( $user_id, 'shipping_address', esc_attr( $data->destination_address ) );
					}
				}
				if ( isset( $data->destination_city ) && $data->destination_city ) {
					$order->update_meta_data( $shipping_prefix . 'address_location_city', $data->destination_city );
					if ( $user_id ) {
						update_user_meta( $user_id, 'shipping_address_city', esc_attr( $data->destination_city ) );
					}
				}
				if ( isset( $data->destination_unit ) && $data->destination_unit ) {
					$order->update_meta_data( $shipping_prefix . 'address_unit', $data->destination_unit );
					if ( $user_id ) {
						update_user_meta( $user_id, 'shipping_address_unit', esc_attr( $data->destination_unit ) );
					}
				}
				if ( isset( $data->destination_number ) && $data->destination_number ) {
					$order->update_meta_data( $shipping_prefix . 'address_number', $data->destination_number );
					if ( $user_id ) {
						update_user_meta( $user_id, 'shipping_address_number', esc_attr( $data->destination_number ) );
					}
				}
				if ( $package_data = WC()->session->get( 'package_data' ) ) {
					$package_data = (object) $package_data;
					if ( isset( $package_data->total_weight ) ) {
						$order->update_meta_data( '_total_weight', $package_data->total_weight );
					}
					if ( isset( $package_data->total_volume ) ) {
						$order->update_meta_data( '_total_volume', $package_data->total_volume );
					}
					if ( isset( $package_data->overflowed ) ) {
						$order->update_meta_data( '_awcshm_overflowed', $package_data->overflowed );
					}
					if ( isset( $package_data->shipping ) ) {
						$order->update_meta_data( '_awcshm_shipping', $package_data->shipping );
					}
				}
				$order->save();
			}
		}

	}

	/**
	 * @since  1.0.0
	 * @param  string $description
	 * @param  string $method
	 * @return string
	 */
	public function update_method_description( $description, $method ) {

		$return_cost = WC()->session->get( 'return_cost' );
		if ( $return_cost ) {
			$should_return   = $this->get_option( 'return_' . $method, 'no' );
			$return_customer = $this->get_option( 'return_' . $method . '_customer', 'no' );
			$return_alert    = $this->get_option( 'return_' . $method . '_customer_alert', 'no' );
			$show_alert      = $should_return == 'yes' && $return_customer == 'yes' && $return_alert == 'yes';
			if ( $show_alert ) {
				$description .= '<span class="awcshm-return-price-alert">' . sprintf( __( 'You will be charged %s more by choosing this payment method.', 'alopeyk-woocommerce-shipping' ), wc_price( $return_cost ) ) . '</span>';
			}
		}
		return $description;

	}

	/**
	 * @since  1.0.0
	 * @param  array $packages
	 * @return array
	 */
	public function add_fields_to_packages( $packages ) {

		$packages[0]['destination']['latitude']      = WC()->session->get( 'destination_latitude' );
		$packages[0]['destination']['longitude']     = WC()->session->get( 'destination_longitude' );
		$packages[0]['destination']['location']      = WC()->session->get( 'destination_address' );
		$packages[0]['destination']['location_city'] = WC()->session->get( 'destination_city' );
		$packages[0]['active_payment_method']        = WC()->session->get( 'active_payment_method' );
		return $packages;

	}

	/**
	 * @since 1.0.0
	 * @param object $location
	 */
	public function set_location( $location = null ) {

		WC()->session->set( 'destination_latitude',  $location ? esc_attr( $location->lat )     : null );
		WC()->session->set( 'destination_longitude', $location ? esc_attr( $location->lng )     : null );
		WC()->session->set( 'destination_address',   $location ? esc_attr( $location->address ) : null );
		WC()->session->set( 'destination_city',      $location ? esc_attr( $location->city )    : null );
		WC()->session->set( 'destination_number',    $location ? esc_attr( $location->number )  : null );
		WC()->session->set( 'destination_unit',      $location ? esc_attr( $location->unit )    : null );

	}

	/**
	 * @since 1.0.0
	 * @param string $method
	 */
	public function set_payment_method( $method = null ) {

		WC()->session->set( 'active_payment_method', esc_attr( $method ) );

	}

	/**
	 * @since 1.0.0
	 * @param array $data
	 */
	public function ajax_set_session( $data ) {

		$data = (object) $data;
		$location = (object) array(
			'lat'     => $data->lat,
			'lng'     => $data->lng,
			'city'    => $data->city,
			'address' => $data->address,
			'number'  => $data->number,
			'unit'    => $data->unit,
		);
		if ( is_null( $location->address ) || empty ( $location->address ) ) {
			$location = null;
		}
		$this->set_location( $location );
		$this->set_payment_method( $data->payment_method );
		$this->respond_ajax( null, true );

	}

	/**
	 * @since  1.0.0
	 * @param  boolean $only_set_token
	 * @param  string  $api_key
	 * @param  boolean $force_set
	 * @return boolean
	 */
	public function authenticate( $only_set_token = false, $api_key = null, $force_set = false ) {
	
		if ( ! AloPeykApiHandler::getToken() || $force_set ) {
			$api_key = $api_key ? $api_key : $this->get_option( 'api_key', null, false );
			if ( $api_key ) {
				AloPeykApiHandler::setToken( $api_key );
				if ( $only_set_token ) {
					return true;
				}
				try {
					$apiResponse = AloPeykApiHandler::authenticate();
					if ( $apiResponse && isset( $apiResponse->status ) && $apiResponse->status == 'success' ) {
						return true;
					}
				} catch ( Exception $e ) {
					$error = __( 'Authentication failed.', 'alopeyk-woocommerce-shipping' ) . ' ' . __( 'API Key', 'alopeyk-woocommerce-shipping' ) . ': ' . $api_key;
					$this->add_log( $error );
				}
				AloPeykApiHandler::setToken( null );
				return false;
			}
			return false;
		}
		return true;

	}

	/**
	 * @since 1.0.0
	 */
	public function dispatch_requests() {

		check_ajax_referer( $this->plugin_name, 'nonce' );
		if ( isset( $_POST['request'] ) ) {
			if ( isset( $_POST['authenticate'] ) && $_POST['authenticate'] == true ) {
				if ( ! $this->authenticate() ) {
					$this->respond_ajax( __( 'Authentication failed may be because of wrong API key.', 'alopeyk-woocommerce-shipping' ), false );
				}
			}
			$request = 'ajax_' . $_POST['request'];
			$scope = $this;
			if ( isset( $_POST['scope'] ) && $_POST['scope'] == 'admin' ) {
				$scope = new Alopeyk_WooCommerce_Shipping_Admin();
			}
			if ( method_exists( $scope, $request ) ) {
				$scope->$request( $_POST );
			} else {
				$this->respond_ajax( __( 'No action defined for given request.', 'alopeyk-woocommerce-shipping' ), false );
			}
		}
		wp_die();

	}

	/**
	 * @since  1.0.0
	 * @param  mixed   $response
	 * @param  boolean $success
	 * @param  array   $extra
	 * @return string
	 */
	public function respond_ajax( $response = null, $success = true, $extra = null ) {

		wp_send_json(
			array(
				'data'    => $response,
				'extra'   => $extra,
				'success' => $success,
			)
		);

	}

	/**
	 * @since  1.0.0
	 * @param  float $lat
	 * @param  float $lng
	 * @return object
	 */
	public function get_location( $lat = null, $lng = null ) {

		$location = null;
		if ( ! is_null( $lat ) && ! is_null( $lng ) ) {
			$location = (object) array(
				'lat' => number_format( (float) $lat, 6, '.', '' ),
				'lng' => number_format( (float) $lng, 6, '.', '' )
			);
		}
		return $location;

	}

	/**
	 * @since 1.0.0
	 * @param array $data
	 */
	public function ajax_get_address( $data ) {

		$data = (object) $data;
		$lat  = $data->lat;
		$lng  = $data->lng;
		$ask_google = isset( $data->ask_google ) ? filter_var( $data->ask_google, FILTER_VALIDATE_BOOLEAN ) : false;
		$location = $this->get_location( $lat, $lng );
		$address = $this->get_address( $location, $ask_google );

		if ( $address ) {
			$this->respond_ajax( $address );
		} else {
			$this->respond_ajax( array(
				'city'    => null,
				'address' => __( 'This address is out of service.', 'alopeyk-woocommerce-shipping' )
			), false );
		}

	}

	/**
	 * @since  1.0.0
	 * @param  string $url
	 * @return array
	 */
	public function get_google_response( $url = null ) {

		if ( $url ) {
			$curl_options = array(
				CURLOPT_URL            => 'https://maps.googleapis.com/maps/api/geocode/json?' . $url . '&language=fa&region=ir&key=' . $this->get_gmap_api_key(),
				CURLOPT_ENCODING       => '',
				CURLOPT_MAXREDIRS      => 10,
				CURLOPT_TIMEOUT        => 30,
				CURLOPT_FAILONERROR    => true,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_CUSTOMREQUEST  => 'GET',
				CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
				CURLOPT_HTTPHEADER     => array(
					'Content-Type: application/json; charset=utf-8',
					'X-Requested-With: XMLHttpRequest'
				),
			);
			$curl = curl_init();
			curl_setopt_array( $curl, $curl_options );
			$response = curl_exec( $curl );
			$err = curl_error( $curl );
			curl_close( $curl );
			if ( ! $err ) {
				$response = json_decode( $response );
				if ( $response->status == 'OK' && count( $response->results ) ) {
					return $response->results;
				}
			} else {
				$this->add_log( $err );
			}
		}
		return null;

	}

	/**
	 * @since 1.0.0
	 * @param array $data
	 */
	public function ajax_suggest_address( $data ) {

		$data = (object) $data;
		$input = $data->input;
		$ask_google = isset( $data->ask_google ) ? filter_var( $data->ask_google, FILTER_VALIDATE_BOOLEAN ) : true;
		$addresses = $this->suggest_address( $input, $ask_google );
		if ( $addresses ) {
			$this->respond_ajax( $addresses );
		} else {
			$this->respond_ajax( __( 'No address found.', 'alopeyk-woocommerce-shipping' ), false );
		}

	}

	/**
	 * @since  1.0.0
	 * @param  object  $location
	 * @param  boolean $ask_google
	 * @return array
	 */
	public function get_address( $location, $ask_google = false ) {

		if ( ! is_null( $location ) ) {
			$apiResponse = null;
			try {
				$apiResponse = AloPeykApiHandler::getAddress( $location->lat, $location->lng );
			} catch ( Exception $e ) {
				$this->add_log( $e->getMessage() );
			}
			if ( $apiResponse && $apiResponse->status == 'success' && isset( $apiResponse->object->address ) ) {
				$location = $apiResponse->object;
				return array(
					'city'    => $location->city,
					'address' => $location->region . ( isset( $location->address[0] ) ? __( ',', 'alopeyk-woocommerce-shipping' ) . ' ' . $location->address[0] : '' )
				);
			} else if ( $ask_google ) {
				$addresses = $this->get_google_response( 'latlng=' . $location->lat . ',' . $location->lng );
				if ( $addresses && count( $addresses ) ) {
					$location = $addresses[0];
					$city = '__NF__';
					for ( $i = 0; $i < count ( $location->address_components ); $i++ ) {
						$component = $location->address_components[$i];
						if ( in_array( 'locality', $component->types ) ) {
							$city = $component->long_name;
						}
					}
					return array(
						'city'    => $city,
						'address' => $location->formatted_address
					);
				}
			}
		}
		return null;

	}

	/**
	 * @since  1.0.0
	 * @param  string  $input
	 * @param  boolean $ask_google
	 * @return array
	 */
	public function suggest_address( $input, $ask_google = true ) {

		if ( ! empty( $input ) ) {
			$addresses   = array();
			$apiResponse = null;
			try {
				$apiResponse = AloPeykApiHandler::getLocationSuggestion( $input );
			} catch (Exception $e) {
				$this->add_log( $e->getMessage() );
			}
			if ( $apiResponse && $apiResponse->status == 'success' ) {
				$addresses = array_map( function ( $location ) {
					return array(
						'lat'     => $location->lat,
						'lng'     => $location->lng,
						'city'    => $location->city,
						'address' => $location->region . __( ',', 'alopeyk-woocommerce-shipping' ) . ' ' . $location->title
					);
				}, $apiResponse->object );
			}
			if ( $ask_google ) {
				$extra_addresses = $this->get_google_response( 'address=' . str_replace( ' ', '+', $input ) );
				if ( $extra_addresses && count( $extra_addresses ) ) {
					foreach ( $extra_addresses as $extra_address ) {
						$city = '__NF__';
						for ( $i = 0; $i < count ( $extra_address->address_components ); $i++ ) {
							$component = $extra_address->address_components[$i];
							if ( in_array( 'locality', $component->types ) ) {
								$city = $component->long_name;
							}
						}
						$addresses[] = array(
							'lat'     => $extra_address->geometry->location->lat,
							'lng'     => $extra_address->geometry->location->lng,
							'city'    => $city,
							'address' => $extra_address->formatted_address
						);
					}
				}
			}
			return $addresses;
		}
		return null;

	}

	/**
	 * @since 1.0.0
	 */
	public function create_post_type() {

		register_post_type( self::$order_post_type_name, array(
			'label'               => __( 'Alopeyk Orders', 'alopeyk-woocommerce-shipping' ),
			'labels'              => array(
				'name'               => __( 'Alopeyk Orders', 'alopeyk-woocommerce-shipping' ),
				'singular_name'      => __( 'Alopeyk Order', 'alopeyk-woocommerce-shipping' ),
				'menu_name'          => _x( 'Alopeyk Orders', 'Admin menu name', 'alopeyk-woocommerce-shipping' ),
				'not_found'          => __( 'No orders found.', 'alopeyk-woocommerce-shipping' ),
				'not_found_in_trash' => __( 'No orders found in Trash.', 'alopeyk-woocommerce-shipping' )
			),
			'description'         => '',
			'public'              => false,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_rest'        => true,
			'rest_base'           => '',
			'has_archive'         => true,
			'show_in_menu'        => 'alopeyk',
			'exclude_from_search' => true,
			'capability_type'     => 'post',
			'capabilities'        => array(
				'create_posts' => false
			),
			'map_meta_cap'        => true,
			'hierarchical'        => false,
			'rewrite'             => false,
			'query_var'           => false,
			'supports'            => false,
			'taxonomies'          => array(),
		));

	}

	/**
	 * @since 1.0.0
	 */
	public function create_order_statuses() {

		$order_statuses = array(
			'awcshm-progress'  => array(
				'label'                     => _x( 'In Progress', 'Order status', 'alopeyk-woocommerce-shipping' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'In Progress <span class="count">(%s)</span>', 'In Progress <span class="count">(%s)</span>', 'alopeyk-woocommerce-shipping' ),
			),
			'awcshm-pending'  => array(
				'label'                     => _x( 'Pending', 'Order status', 'alopeyk-woocommerce-shipping' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'Pending <span class="count">(%s)</span>', 'Pending <span class="count">(%s)</span>', 'alopeyk-woocommerce-shipping' ),
			),
			'awcshm-scheduled' => array(
				'label'                     => _x( 'Scheduled', 'Order status', 'alopeyk-woocommerce-shipping' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'Scheduled <span class="count">(%s)</span>', 'Scheduled <span class="count">(%s)</span>', 'alopeyk-woocommerce-shipping' ),
			),
			'awcshm-failed'    => array(
				'label'                     => _x( 'Failed', 'Order status', 'alopeyk-woocommerce-shipping' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'Failed <span class="count">(%s)</span>', 'Failed <span class="count">(%s)</span>', 'alopeyk-woocommerce-shipping' ),
			),
			'awcshm-done'      => array(
				'label'                     => _x( 'Completed', 'Order status', 'alopeyk-woocommerce-shipping' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'Completed <span class="count">(%s)</span>', 'Completed <span class="count">(%s)</span>', 'alopeyk-woocommerce-shipping' ),
			)
		);
		foreach ( $order_statuses as $order_status => $values ) {
			register_post_status( $order_status, $values );
		}

	}

	/**
	 * @since  1.0.0
	 * @return boolean
	 */
	public function smart_switch_enabled() {

		$smart_switch = $this->get_option( 'auto_type' );
		return $smart_switch && $smart_switch == 'yes';

	}

	/**
	 * @since  1.0.0
	 * @param  array   $weights
	 * @param  string  $unit               
	 * @param  boolean $check_smart_switch 
	 * @param  string  $type               
	 * @return boolean                     
	 */
	public function is_available_for_weights( $weights = array(), $unit = null, $check_smart_switch = true, $type = 'motorbike' ) {

		if ( ( $check_smart_switch && $this->smart_switch_enabled() ) || $type != 'motorbike' ) {
			return true;	
		}
		if ( $weights && $unit ) {
			$total_weight = 0;
			foreach ( $weights as $weight ) {
				$weight = wc_get_weight( (float) $weight, 'g', $unit );
				$total_weight += $weight;
				if ( $total_weight > $this->get_config( 'max_weight' ) ) {
					return false;
				}
			}
			return true;
		}
		return false;

	}

	/**
	 * @since  1.0.0
	 * @param  array   $dimensions
	 * @param  string  $unit
	 * @param  boolean $check_smart_switch
	 * @param  string  $type
	 * @return boolean
	 */
	public function is_available_for_dimensions( $dimensions = array(), $unit = null, $check_smart_switch = true, $type = 'motorbike' ) {

		if ( ( $check_smart_switch && $this->smart_switch_enabled() ) || $type != 'motorbike' ) {
			return true;	
		}
		if ( $dimensions && $unit ) {
			$total_volume = 0;
			$max_volume = $this->get_config( 'max_width' ) * $this->get_config( 'max_height' ) * $this->get_config( 'max_length' );
			foreach ( $dimensions as $dimension ) {
				$dimension = (object) $dimension;
				$width    = wc_get_dimension( (float) $dimension->width,  'cm', $unit );
				$height   = wc_get_dimension( (float) $dimension->height, 'cm', $unit );
				$length   = wc_get_dimension( (float) $dimension->length, 'cm', $unit );
				$volume   = isset( $dimension->volume ) ? wc_get_dimension( (float) $dimension->volume, 'cm', $unit ) : null;
				$quantity = isset( $dimension->quantity ) ? $dimension->quantity : 1;
				$total_volume += $volume ? $volume : ( $width * $height * $length * $quantity );
				$is_large = $width        > $this->get_config( 'max_width'  ) ||
							$height       > $this->get_config( 'max_height' ) ||
							$length       > $this->get_config( 'max_length' ) ||
							$total_volume > $max_volume;
				if ( $is_large ) {
					return false;
				}
			}
			return true;
		}
		return false;

	}

	/**
	 * @since  1.0.0
	 * @param  string  $currency
	 * @return boolean
	 */
	public function is_available_for_currency( $currency ) {

		return in_array( $currency, array( 'IRR', 'IRT', 'IRHT' ) );

	}

	/**
	 * @since  1.0.0
	 * @param  array   $destinations
	 * @return boolean
	 */
	public function is_available_for_destinations( $destinations = array() ) {

		if ( $destinations && count( $destinations ) ) {
			try {
				if ( $this->authenticate() ) {
					foreach ( $destinations as $destination ) {
						$destination = (object) $destination;
						$available = isset( $destination->latitude )      && ! empty( $destination->latitude )  &&
									 isset( $destination->longitude )     && ! empty( $destination->longitude ) &&
									 isset( $destination->location )      && ! empty( $destination->location )  &&
									 isset( $destination->location_city ) && ! empty( $destination->location_city );
						if ( ! $available ) {
							return false;
						}
						$location  = $this->get_location( $destination->latitude, $destination->longitude );
						if ( ! $this->get_address( $location ) ) {
							return false;
						}
					}
					return true;
				}
				return false;
			} catch ( Exception $e ) {
				return false;
			}
		}
		return false;

	}

	/**
	 * @since  1.0.0
	 * @param  array   $weights
	 * @param  array   $dimensions
	 * @param  string  $weight_unit
	 * @param  string  $dimension_unit
	 * @return boolean
	 */
	public function has_overflow( $weights = array(), $dimensions = array(), $weight_unit = null, $dimension_unit = null ) {

		if ( $weights && $dimensions && $weight_unit && $dimension_unit ) {
			if ( ! $this->is_available_for_weights( $weights, $weight_unit, false ) ) {
				return true;
			}
			if ( ! $this->is_available_for_dimensions( $dimensions, $dimension_unit, false ) ) {
				return true;
			}
			return false;
		}
		return false;

	}

	/**
	 * @since  1.0.0
	 * @param  array   $package
	 * @param  string  $type
	 * @return boolean
	 */
	public function is_available( $package = null, $type = 'motorbike' ) {

		$available = $this->is_enabled();
		if ( $package && $available ) {
			$package = (object) $package;
			$available = $this->is_available_for_currency( get_woocommerce_currency() );
			if ( $available ) {
				$available = $this->is_available_for_weights( $package->weights, get_option( 'woocommerce_weight_unit' ), true, $type );
			}
			if ( $available ) {
				$available = $this->is_available_for_dimensions( $package->dimensions, get_option( 'woocommerce_dimension_unit' ), true, $type );
			}
			if ( $available ) {
				$available = $this->is_available_for_destinations( $package->destinations );
			}
		}
		return apply_filters( METHOD_ID . '/is_available', $available, $package );
	}

	/**
	 * @since  1.0.0
	 * @param  array   $package
	 * @param  string  $type
	 * @param  boolean $has_return
	 * @param  string  $cost_type
	 * @return array
	 */
	public function calculate_shipping( $package = null, $type = 'motorbike', $has_return = null, $cost_type = null, $override = true ) {

		$cost         = null;
		$cost_details = null;
		if ( $package ) {
			$package = (object) $package;
			$cost_type = $cost_type ? $cost_type : $this->get_option( 'cost_type' );
			if ( $cost_type == 'static' ) {
				$static_cost_type = $this->get_option( 'static_cost_type' );
				if ( $static_cost_type == 'fixed' ) {
					$static_cost_fixed = $this->get_option( 'static_cost_fixed' );
					$cost = $this->normalize_price( $this->get_option( 'static_cost_fixed' ) ) * count( $package->destinations );
					$cost_details = array(
						'type'   => 'fixed',
						'amount' => $static_cost_fixed
					);
				} else if ( $static_cost_type == 'percentage' ) {
					$static_cost_percentage = $this->get_option( 'static_cost_percentage' );
					$cost = (float) $static_cost_percentage * count( $package->destinations ) * $package->subtotal / 10;
					$cost_details = array(
						'type'   => 'percentage',
						'amount' => $static_cost_percentage
					);
				}
			} else if ( $cost_type == 'dynamic' ) {
				try {
					$type = $this->get_transport_type( $package->overflowed, $type );
					if ( $type ) {
						$destinations    = array();
						$origin_location = $this->get_location( $this->get_option( 'store_lat' ), $this->get_option( 'store_lng' ) );
						$origin          = new Address( 'origin', $this->get_option( 'store_city' ), $origin_location->lat, $origin_location->lng );
						if ( is_null( $has_return ) ) {
							$has_return = $this->has_return( $package->payment_method );
						}
						foreach ( $package->destinations as $dest ) {
							$dest                 = (object) $dest;
							$destination_location = $this->get_location( $dest->latitude, $dest->longitude );
							$destination          = new Address( 'destination', $dest->location_city, $destination_location->lat, $destination_location->lng );
							$destinations[]       = $destination;
						}
						$order = new Order( $type, $origin, $destinations );
						$order->setHasReturn( false );
						$apiResponse       = $order->getPrice();
						$price             = 10 * $apiResponse->object->price;
						$price_with_return = 10 * $apiResponse->object->price_with_return;
						$cost              = $has_return ? $price_with_return : $price;
						$cost_details = array(
							'price'             => $price,
							'price_with_return' => $price_with_return
						);
					}
				} catch ( Exception $e ) {
					$this->add_log( $e->getMessage() );
				}
			}
		}
		$shipping_info = array(
			'type'         => $type,
			'cost'         => $cost,
			'cost_type'    => $cost_type,
			'has_return'   => $has_return,
			'cost_details' => $cost_details,
		);
		if ( $override ) {
			$shipping_info = apply_filters( METHOD_ID . '/shipping_info', $shipping_info, $package );
		}
		$package_data = isset( WC()->session ) ? WC()->session->get( 'package_data' ) : null;
		if ( $package_data ) {
			$package_data['shipping'] = $shipping_info;
			WC()->session->set( 'package_data', $package_data );
		}
		return $shipping_info;

	}

	/**
	 * @since  1.0.0
	 * @param  float $price IRR
	 * @return float
	 */
	static function normalize_price( $price = 0 ) {

		$price = (float) $price;
		$currency = get_woocommerce_currency();
		if ( $currency == 'IRT' ) {
			$price /= 10;
		} else if ( $currency == 'IRHT' ) {
			$price /= 10000;
		}
		return $price;

	}

	/**
	 * @since  1.0.0
	 * @param  boolean $overflowed        
	 * @param  string  $type              
	 * @param  boolean $check_smart_switch
	 * @return string
	 */
	public function get_transport_type( $overflowed = false, $type = 'motorbike', $check_smart_switch = true ) {

		if ( ! $type || $type != 'cargo' ) {
			$type = $overflowed ? ( $check_smart_switch && $this->smart_switch_enabled() ? 'cargo' : null ) : 'motorbike';
		}
		return $type;

	}

	/**
	 * @since  1.0.0
	 * @param  string  $payment_method
	 * @return boolean
	 */
	public function has_return( $payment_method = null ) {

		if ( $payment_method ) {
			return $this->get_option( 'return_' . $payment_method, 'no' ) == 'yes' && $this->get_option( 'return_' . $payment_method . '_customer', 'no' ) == 'yes';
		}
		return null;

	}

	/**
	 * @since  1.0.0
	 * @param  string  $key
	 * @param  mixed   $default
	 * @param  boolean $can_be_empty
	 * @return mixed
	 */
	public function get_user_data( $key = null, $default = null, $can_be_empty = true ) {

		$user_data = $this->get_configs( 'user' );
		if ( $user_data ) {
			if ( $key ) {
				return isset( $user_data->{$key} ) && ( $can_be_empty || ! empty( $user_data->{$key} ) ) ? $user_data->{$key} : $default;
			}
			return $user_data;
		}
		return null;

	}

	/**
	 * @since  1.0.0
	 * @param  array  $args
	 * @return string
	 */
	public function get_add_credit_url( $args = array() ) {

		$user_id = $this->get_user_data( 'id' );
		if ( $user_id ) {
			$base_url = AloPeykApiHandler::getPaymentRoute( $user_id, '' );
			$base_url = remove_query_arg( 'amount', $base_url );
			$args = array_merge( $args, array(
				'from'     => 'customer',
				'customer' => $_SERVER['SERVER_NAME'],
			));
			return add_query_arg( $args, $base_url );
		}
		return '#';

	}

	/**
	 * @since  1.0.0
	 * @return array
	 */
	public function get_payment_gateways() {

		return AloPeykApiHandler::getPaymentGateways();

	}

	/**
	 * @since  1.0.0
	 * @param  object $order
	 * @return string
	 */
	public function get_invoice_url( $order ) {

		if ( $order && $order->id && $order->order_token ) {
			return AloPeykApiHandler::getPrintInvoice( $order->id, $order->order_token );
		}
		return '#';

	}

	/**
	 * @since  1.0.0
	 * @param  object  $order
	 * @param  boolean $is_admin
	 * @return string
	 */
	public function get_tracking_url( $order, $is_admin = true ) {

		if ( $order && $order->order_token ) {
			$base_url = AloPeykApiHandler::getTrackingUrl( $order->order_token );
			if ( ! $is_admin ) {
				$base_url .= base64_encode( 'public' );
			}
			return $base_url;
		}
		return '#';

	}

	/**
	 * @since  1.0.0
	 * @return array
	 */
	public function get_default_credit_amounts() {

		return $this->get_config( 'credit_amounts' );

	}

	/**
	 * @since  1.0.0
	 * @param  object $order
	 * @return string
	 */
	public function get_order_status( $order ) {

		$status = '';
		if ( $order && isset( $order->status ) ) {
			$status = $order->status;
			if ( $status == 'scheduled' ) {
				$status = 'awcshm-scheduled';
			} else if ( in_array( $status, array( 'new', 'searching' ) ) ) {
				$status = "awcshm-pending";
			} else if ( in_array( $status, array( 'accepted', 'picking', 'delivering' ) ) ) {
				$status = 'awcshm-progress';
			} else if ( in_array( $status, array( 'delivered', 'finished' ) ) ) {
				$status = 'awcshm-done';
			} else if ( in_array( $status, array( 'cancelled', 'expired', 'deleted' ) ) ) {
				$status = 'awcshm-failed';
			} else {
				$status = 'awcshm-' . $status;
			}
		}
		return $status;

	}

	/**
	 * @since  1.0.0
	 * @param  string $order_status
	 * @return string
	 */
	public function get_order_status_label( $order_status = null ) {

		if ( $order_status ) {
			$order_status_object = get_post_status_object( $order_status );
			return $order_status_object ? $order_status_object->label : $order_status;
		}
		return null;

	}

	/**
	 * @since  1.0.0
	 * @param  object  $order
	 * @param  integer $order_id
	 * @return array
	 */
	public function get_wc_order_status( $order = null, $order_id = null ) {

		$response = array();
		if ( $order && isset( $order->status ) ) {
			$status = $order->status;
			if ( $status == 'scheduled' ) {
				date_default_timezone_set( $this->get_timezone_setting() );
				$response = array(
					'status' => 'wc-on-hold',
					'note'   => sprintf( __( 'Order scheduled to be shipped via Alopeyk shipping method at %s.', 'alopeyk-woocommerce-shipping' ), date_i18n( 'j F Y (g:i A)', strtotime( $order->scheduled_at ) ) )
				);
			} else if ( in_array( $status, array( 'new', 'searching' ) ) ) {
				$response = array(
					'status' => 'wc-on-hold',
					'note'   => __( 'Searching for the closest courier to assign shipping task.', 'alopeyk-woocommerce-shipping' )
				);
			} else if ( in_array( $status, array( 'accepted', 'picking', 'delivering' ) ) ) {
				$courier_info = isset( $order->courier_info ) ? ' (' . $order->courier_info->firstname . ' ' . $order->courier_info->lastname . ' ' . __( 'with the phone number', 'alopeyk-woocommerce-shipping' ) . ' ' . $order->courier_info->phone . ')' : '';
				$response = array(
					'status' => 'wc-processing',
					'note'   => sprintf( __( 'Courier%s assigned and <a href="%s" target="_blank">shipping proccess</a> is started. It can be tracked <a href="%s" target="_blank">here</a>.', 'alopeyk-woocommerce-shipping' ), $courier_info, $order_id ? admin_url( 'post.php?action=edit&post=' . $order_id ) : '#', $this->get_tracking_url( $order ) )
				);
			} else if ( in_array( $status, array( 'delivered', 'finished' ) ) ) {
				$response = array(
					'status' => 'wc-completed',
					'note'   => __( 'Order successfully delivered.', 'alopeyk-woocommerce-shipping' )
				);
			} else if ( in_array( $status, array( 'cancelled', 'deleted' ) ) ) {
				$response = array(
					'status' => 'wc-cancelled',
					'note'   => __( 'Shipping canceled.', 'alopeyk-woocommerce-shipping' )
				);
			} else if ( in_array( $status, array( 'expired' ) ) ) {
				$response = array(
					'status' => 'wc-failed',
					'note'   => __( 'No courier found.', 'alopeyk-woocommerce-shipping' )
				);
			}
		}
		return $response;

	}

	/**
	 * @since  1.0.0
	 * @return integer
	 */
	public function get_max_destination() {

		return $this->get_config( 'addresses_limit' );

	}

	/**
	 * @since  1.0.0
	 * @param  string $scope
	 * @return object
	 */
	public function get_configs( $scope = 'config' ) {

		if ( $this->config ) {
			return $scope ? ( isset( $this->config->{$scope} ) ? $this->config->{$scope} : null ) : $this->config;
		}
		$configs = (object) array( 'config' => array() );
		try {
			if ( $this->authenticate( true ) ) {
				$apiResponse = AloPeykApiHandler::authenticate( true );
				if ( $apiResponse && isset( $apiResponse->status ) && $apiResponse->status == 'success' ) {
					$configs = $apiResponse->object;
				}
			}
		} catch ( Exception $e ) {}
		$configs->config = (object) array_merge( self::$configs, (array) $configs->config );
		$this->config = (object) $configs;
		return $scope ? ( isset( $this->config->{$scope} ) ? $this->config->{$scope} : null ) : $this->config;

	}

	/**
	 * @since  1.0.0
	 * @param  string  $config
	 * @param  mixed   $default
	 * @param  boolean $can_be_empty
	 * @return mixed
	 */
	public function get_config( $config, $default = null, $can_be_empty = true ) {

		$configs = $this->get_configs();
		return $configs && isset( $configs->{$config} ) && ( $can_be_empty || ! empty( $configs->{$config} ) ) ? $configs->{$config} : $default;

	}

	/**
	 * @since  1.0.0
	 * @return string
	 */
	public function get_chat_url() {

		return $this->get_config( 'chatUrl', 'https://chat.alopeyk.com/chat.php' );

	}

	/**
	 * @since  1.0.0
	 * @return string
	 */
	public function get_support_tel() {

		$support_tel = $this->get_config( 'supportTel' );
		return '+98' . ltrim( ltrim( $support_tel, '0' ), '+98' ); 

	}

	/**
	 * @since  1.0.0
	 * @return array
	 */
	public function get_rating_reasons() {

		$rating_reasons = $this->get_config( 'rating' );
		return $rating_reasons && isset( $rating_reasons->why ) ? $rating_reasons->why : $rating_reasons;

	}

	/**
	 * @since  1.0.0
	 * @return array
	 */
	public function get_cancel_reasons() {

		return $this->get_config( 'cancel_reason' );

	}

	/**
	 * @since  1.0.0
	 * @return integer
	 */
	public function get_cancel_penalty_amount() {

		return $this->get_config( 'cancel_penalty_amount' ) * 10; // IRR

	}

	/**
	 * @since  1.0.0
	 * @return integer
	 */
	public function get_cancel_penalty_delay() {

		return $this->get_config( 'cancel_penalty_delay' ); // Minutes

	}

	/**
	 * @since  1.0.0
	 * @param  object  $order
	 * @return boolean
	 */
	public function can_be_invoiced( $order = null ) {

		if ( $order && isset( $order->status ) ) {
			if ( in_array( $order->status, array( 'finished', 'delivered' ) ) ) {
				return true;
			}
		}
		return false;

	}

	/**
	 * @since  1.0.0
	 * @param  object  $order
	 * @return boolean
	 */
	public function can_be_tracked( $order = null ) {

		if ( $order && isset( $order->status ) ) {
			if ( in_array( $order->status, array( 'accepted', 'picking', 'delivering' ) ) ) {
				return true;
			}
		}
		return false;

	}

	/**
	 * @since  1.0.0
	 * @param  object  $order
	 * @return boolean
	 */
	public function can_be_rated( $order = null ) {

		if ( $order && isset( $order->status ) ) {
			if ( in_array( $order->status, array( 'delivered' ) ) ) {
				return true;
			}
		}
		return false;

	}

	/**
	 * @since  1.0.0
	 * @param  object  $order
	 * @return boolean
	 */
	public function can_be_repeated( $order = null ) {

		if ( $order && isset( $order->status ) ) {
			if ( in_array( $order->status, array( 'finished', 'delivered', 'cancelled', 'expired', 'deleted' ) ) ) {
				return true;
			}
		}
		return false;

	}

	/**
	 * @since  1.0.0
	 * @param  object  $order
	 * @return boolean
	 */
	public function can_be_canceled( $order = null ) {

		$cancel = array(
			'enabled'      => false,
			'penalty'      => false,
			'reasons'      => false,
			'penalty_info' => null
		);
		if ( $order && isset( $order->status ) ) {
			if ( in_array( $order->status, array( 'new', 'searching', 'scheduled' ) ) ) {
				$cancel = array(
					'enabled'      => true,
					'penalty'      => false,
					'reasons'      => false,
					'penalty_info' => null
				);
			} else if ( in_array( $order->status, array( 'accepted' ) ) ) {
				if ( isset( $order->accepted_at ) && $order->accepted_at ) {
					$penalty_delay = $this->get_cancel_penalty_delay();
					$penalty_amount = $this->get_cancel_penalty_amount();
					$has_penalty = !! $penalty_amount;
					if ( $has_penalty ) {
						$free_cancel_deadline = date( 'Y-m-d H:i:s', strtotime( $order->accepted_at . ' +' . $penalty_amount . 'minutes' ) );
						date_default_timezone_set( $this->get_timezone_setting() );
						$now = date( 'Y-m-d H:i:s' );
						$has_penalty = $now > $free_cancel_deadline;
					}
					$cancel = array(
						'enabled'      => true,
						'penalty'      => $has_penalty,
						'reasons'      => true,
						'penalty_info' => $has_penalty ? array(
							'delay'  => $penalty_delay,
							'amount' => $penalty_amount
						) : null
					);
				}
			}
		}
		return $cancel;

	}

	/**
	 * @since  1.0.0
	 * @param  object  $order
	 * @param  object  $address
	 * @return boolean
	 */
	public function is_active_address( $order = null, $address = null ) {

		if ( $order && $address ) {
			return in_array( $address->status, array( 'pending', 'arrived' ) ) && $order->eta_minimal && (int) $order->eta_minimal->address_id === (int) $address->id && ! in_array( $order->status, array( 'cancelled', 'deleted' ) );
		}
		return null;

	}

	/**
	 * @since  1.0.0
	 * @param  mixed   $wc_orders
	 * @return boolean
	 */
	public function is_in_progress( $wc_orders ) {

		$is_in_progress = false;
		$wc_orders = $wc_orders ? ( is_int( $wc_orders ) || is_string( $wc_orders ) ? array( $wc_orders ) : (array) $wc_orders ) : null;
		if ( $wc_orders && count( $wc_orders ) ) {
			$progress_query = new WP_Query( array (
				'post_type'   => self::$order_post_type_name,
				'post_status' => array( 'awcshm-progress', 'awcshm-pending', 'awcshm-scheduled' ),
				'meta_query'  => array(
					array(
						'key'     => '_awcshm_wc_order_id',
						'value'   => $wc_orders,
						'compare' => 'IN',
					)
				)
			));
			$is_in_progress = $progress_query->have_posts();
		}
		return $is_in_progress;

	}

	/**
	 * @since  1.0.0
	 * @param  integer $wc_order
	 * @param  array   $args
	 * @return array
	 */
	public function get_order_history( $wc_order = null, $args = array() ) {

		$history = null;
		if ( $wc_order ) {
			$args = array_merge( array (
				'post_type'      => self::$order_post_type_name,
				'meta_query'     => array(    
					array(
						'key'     => '_awcshm_wc_order_id',
						'value'   => $wc_order,
						'compare' => '=',
					)
				),
				'posts_per_page' => -1
			), $args );
			$history_query = new WP_Query( $args );
			if ( $history_query->have_posts() ) {
				$orders = $history_query->posts;
				foreach ( $orders as $order ) {
					$order_id = $order->ID;
					$order_data = get_post_meta( $order_id, '_awcshm_order_data', true );
					$order_status = get_post_status( $order_id );
					$order_status_label = $this->get_order_status_label( $order_status );
					$order_actions = array();
					$order_actions['view'] = get_edit_post_link( $order_id );
					$can_be_canceled = $this->can_be_canceled( $order_data );
					$order_actions['cancel'] = $can_be_canceled['enabled'];
					if ( $this->can_be_tracked( $order_data ) ) {
						$order_actions['track'] = $this->get_tracking_url( $order_data );
					}
					if ( $this->can_be_invoiced( $order_data ) ) {
						$order_actions['invoice'] = $this->get_invoice_url( $order_data );
					}
					$history[] = array(
						'id'           => $order_id,
						'status'       => $order_status,
						'actions'      => $order_actions,
						'status_label' => $order_status_label
					);
				}
			}
		}
		return $history;

	}

	/**
	 * @since  1.0.0
	 * @param  string $content
	 * @param  string $medium
	 * @param  string $name
	 * @param  string $website
	 * @return string
	 */
	static function get_campaign_url( $content = '', $medium = 'email', $name = null, $website = 'https://alopeyk.com', $source = 'woocommerce_plugin' ) {

		if ( $website && ! empty( $website ) ) {
			$name = $name ? $name : $_SERVER['SERVER_NAME'];
			$pieces = array(
				'utm_medium'   => $medium,
				'utm_source'   => $source,
				'utm_content'  => $content,
				'utm_campaign' => $name,
			);
			foreach ( $pieces as $key => $piece ) {
				if ( ! $piece || empty( $piece ) ) {
					unset( $pieces[$key] );
				} else {
					$pieces[$key] = $key . '=' . $piece;
				}
			}
			return count( $pieces ) ? $website . '?' . implode( '&', $pieces ) : $website;
		}
		return false;

	}

	/**
	 * @since  1.0.0
	 * @return string
	 */
	public function get_support_url() {

		return admin_url( 'admin.php?page=alopeyk-support' );

	}

	/**
	 * @since  1.0.0
	 * @param  string $coupon_code
	 * @return array
	 */
	public function apply_coupon( $coupon_code = null ) {

		$response = array(
			'success' => false,
			'message' => __( 'Coupon code is required.', 'alopeyk-woocommerce-shipping' ),
		);
		if ( $coupon_code ) {
			try {
				if ( $this->authenticate() ) {
					$apiResponse = AloPeykApiHandler::validateCoupon( array( 'coupon_code' => $coupon_code ) );
					if ( $apiResponse->status == 'success' ) {
						$response = array(
							'success' => true,
							'message' => __( 'Coupon code successfully applied.', 'alopeyk-woocommerce-shipping' ),
						);
					} else if ( $apiResponse->status == 'fail' && $apiResponse->object->error == 'invalid_coupon' ) {
						$response = array(
							'success' => false,
							'message' => __( 'Entered coupon code is not valid.', 'alopeyk-woocommerce-shipping' ),
						);
					} else {
						$response = array(
							'success' => false,
							'message' => __( 'Error occured while trying to apply coupon code.', 'alopeyk-woocommerce-shipping' ) . '<br><br><strong>' . __( 'Detail:', 'alopeyk-woocommerce-shipping' ) . '</strong><br>' . $apiResponse->message,
						);
					}
				} else {
					$response = array(
						'success' => false,
						'message' => __( 'Authentication failed.', 'alopeyk-woocommerce-shipping' )
					);
				}
			} catch ( Exception $e ) {
				$response = array(
					'success' => false,
					'message' => __( 'Error occured while trying to apply coupon code.', 'alopeyk-woocommerce-shipping' ) . '<br><br><strong>' . __( 'Detail:', 'alopeyk-woocommerce-shipping' ) . '</strong><br>' . $e->getMessage(),
				);
			}
		}
		return $response;

	}

	/**
	 * @since  1.0.0
	 * @param  array  $recipient
	 * @param  string $subject
	 * @param  string $message
	 * @param  string $email_id
	 */
	public function send_email( $recipient = array(), $subject = '', $message = '', $email_id = '' ) {

		if ( $recipient && count( $recipient ) ) {
			$content = get_local_template_part( 'alopeyk-woocommerce-shipping-public-email', array(
				'title'        => $subject,
				'tel'          => $this->get_support_tel(),
				'extra'        => $this->get_config( 'targeted_ads' ),
				'message'      => $message,
				'campaign_url' => $this->get_campaign_url( $email_id )
			), false, 'public' );
			$css = get_local_template_part( 'alopeyk-woocommerce-shipping-public-email-styles', array(), false, 'public' );
			try {
				if ( ! class_exists( 'Emogrifier' ) && class_exists( 'DOMDocument' ) ) {
					include_once( ABSPATH . '/wp-content/plugins/woocommerce/includes/libraries/class-emogrifier.php' );
				}
				$emogrifier = new Emogrifier( $content, $css );
				$content    = $emogrifier->emogrify();
			} catch ( Exception $e ) {
				$this->add_log( $e->getMessage() );
			}
			wc_mail( $recipient, $subject, $content );
		}

	}

	/**
	 * @since  1.0.0
	 * @param  array $wc_order_ids
	 * @return array
	 */
	public function get_orders_package( $wc_order_ids = array() ) {

		if ( $wc_order_ids && count( $wc_order_ids ) ) {
			$weights = array();
			$dimensions = array();
			$destinations = array();
			$overflowed = false;
			$has_return = false;
			foreach ( $wc_order_ids as $order_id ) {
				$order = new WC_Order( $order_id );
				$weights[] = $order->get_meta( '_total_weight' );
				$dimensions[] = array(
					'width'    => 0,
					'height'   => 0,
					'length'   => 0,
					'quantity' => 1,
					'volume'   => $order->get_meta( '_total_volume' )
				);
				$destinations[] = array(
					'latitude'      => $order->get_meta( '_shipping_address_latitude' ),
					'longitude'     => $order->get_meta( '_shipping_address_longitude' ),
					'location'      => $order->get_meta( '_shipping_address_location' ),
					'location_city' => $order->get_meta( '_shipping_address_location_city' ),
					'unit'          => $order->get_meta( '_shipping_address_unit' ),
					'number'        => $order->get_meta( '_shipping_address_number' ),
					'description'   => $order->get_meta( '_shipping_address_description' ),
					'phone'         => $order->get_billing_phone(),
					'fullname'      => $order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name(),
				);
				$shipping = (object) $order->get_meta( '_awcshm_shipping' );
				$_has_return = false;
				if ( $shipping && isset( $shipping->has_return ) ) {
					$_has_return = $shipping->has_return;
				} else {
					$_has_return = $this->has_return( $order->get_payment_method() );
				}
				if ( $_has_return ) {
					$has_return = true;
				}
			}
			$overflowed = $this->has_overflow( $weights, $dimensions, get_option( 'woocommerce_weight_unit' ), get_option( 'woocommerce_dimension_unit' ) );
			return $package = array(
				'weights'      => $weights,
				'dimensions'   => $dimensions,
				'destinations' => $destinations,
				'overflowed'   => $overflowed,
				'has_return'   => $has_return
			);
		}
		return null;

	}

	/**
	 * @since  1.0.0
	 * @param  object $package
	 * @return array
	 */
	public function create_order( $package ) {

		$order_data = null;
		$wc_orders = $package->orders;
		if ( $this->is_in_progress( $wc_orders ) ) {
			$response = array(
				'success' => false,
				'message' => __( 'Shipping proccess of one or more selected orders is in progress. Please cancel them before creating a new order.', 'alopeyk-woocommerce-shipping' ),
				'data'    => $order_data,
			);
		} else {
			if ( $this->authenticate() ) {
				$destinations = array();
				$origin_location = $this->get_location( $this->get_option( 'store_lat' ), $this->get_option( 'store_lng' ) );
				$origin = new Address( 'origin', $this->get_option( 'store_city' ), $origin_location->lat, $origin_location->lng );
				$origin->setAddress( $this->get_option( 'store_address' ) );
				$origin->setDescription( $package->description );
				$origin->setUnit( $this->get_option( 'store_unit' ) );
				$origin->setNumber( $this->get_option( 'store_number' ) );
				$origin->setPersonFullname( $this->get_option( 'store_name' ) );
				$origin->setPersonPhone( $this->get_option( 'store_phone' ) );
				foreach ( $package->destinations as $dest ) {
					$dest = (object) $dest;
					$destination_location = $this->get_location( $dest->latitude, $dest->longitude );
					$destination = new Address( 'destination', $dest->location_city, $destination_location->lat, $destination_location->lng );
					$destination->setAddress( $dest->location );
					$destination->setDescription( $dest->description );
					$destination->setUnit( $dest->unit );
					$destination->setNumber( $dest->number );
					$destination->setPersonFullname( $dest->fullname );
					$destination->setPersonPhone( $dest->phone );
					$destinations[] = $destination;
				}
				$new_order_id = null;
				try {
					$order = new Order( $package->type, $origin, $destinations );
					$order->setHasReturn( $package->has_return );
					$order->setCashed( false );
					if ( $package->scheduled_at ) {
						$order->setScheduledAt( $package->scheduled_at );
					}
					// Recheck customer credit to be sure that order will be created using credit
					if ( $credit = $this->get_user_data( 'credit' ) * 10 ) {
						$order_data = $order->create( $order );
						if ( $order_data->status == 'success' ) {
							$order_data = $order_data->object;
							$new_order_id = $order_data->id;
							$tracking_url = $this->get_tracking_url( $order_data );
							$detailed_order_data = Order::getDetails( $order_data->id );
							if ( $detailed_order_data && isset( $detailed_order_data->status ) && $detailed_order_data->status == 'success' && isset( $detailed_order_data->object ) ) {
								$order_data = $detailed_order_data->object;
								$result = wp_insert_post( array(
									'post_title'  => $order_data->invoice_number,
									'post_type'   => self::$order_post_type_name,
									'post_status' => $this->get_order_status( $order_data ),
								), true );
								if ( is_wp_error( $result ) ) {
									$response = array(
										'success' => false,
										'message' => sprintf( __( 'Error occurred while trying to write order as a Wordpress post. But your Alopeyk order is created and is in progress. You can <a href="%s" target="_blank">track your order here</a> or <a href="%s" target="_blank" >contact Alopeyk support</a>.', 'alopeyk-woocommerce-shipping' ), $tracking_url, $this->get_support_url() ) . '<br><br><strong>' . __( 'Detail:', 'alopeyk-woocommerce-shipping' ) . '</strong><br>' . $result->get_error_message(),
										'data'    => $order_data,
									);
									$this->add_log( $result->get_error_message() );
								} else {
									$order_id = $result;
									update_post_meta( $order_id, '_awcshm_order_id', $order_data->id );
									update_post_meta( $order_id, '_awcshm_order_data', $order_data );
									if ( isset( $order_data->transport_type ) ) {
										update_post_meta( $order_id, '_awcshm_order_type', $order_data->transport_type );
									}
									if ( isset( $order_data->price ) ) {
										update_post_meta( $order_id, '_awcshm_order_price', $order_data->price * 10 );
									}
									if ( $wc_orders && count( $wc_orders ) ) {
										foreach ( $wc_orders as $wc_order ) {
											add_post_meta( $order_id, '_awcshm_wc_order_id', $wc_order );
											add_post_meta( $order_id, '_awcshm_user_id', get_post_meta( $wc_order, '_customer_user', true ) );
											$order = new WC_Order( $wc_order );
											$status_details = $this->get_wc_order_status( $order_data, $order_id );
											if ( $status_details && count( $status_details ) && $status_details['status'] != get_post_status( $wc_order ) && $this->get_option('status_change', 'yes') == 'yes' ) {
												$order->update_status( $status_details['status'], $status_details['note'] );
											}
										}
									}
									$this->update_active_order( $order_id );
									$schedule_name = METHOD_ID . '_active_order_update';
									wp_schedule_event( time(), $schedule_name . '_interval', $schedule_name, array( 'order_id' => $order_id ) );
									$order_data->tracking_url = $tracking_url;
									$order_data->edit_url = get_edit_post_link( $order_id );
									$response = array(
										'success' => true,
										'message' => __( 'Your order has been successfully created and is in progress.', 'alopeyk-woocommerce-shipping' ),
										'data'    => $order_data,
									);
								}
							} else {
								$this->cancel_order( $order_data->id, '', $order_id );
								$response = array(
									'success' => true,
									'message' => __( 'Error occured while trying to fetch Alopeyk order details. Order cancelled due to security reasons. Please try again later.', 'alopeyk-woocommerce-shipping' ),
									'data'    => $order_data,
								);
							}
						} else {
							$response = array(
								'success' => false,
								'message' => __( $order->message, 'alopeyk-woocommerce-shipping' ),
								'data'    => $order_data,
							);
						}
					} else {
						$response = array(
							'success' => false,
							'message' => __( 'Unable to get your Alopeyk credit.', 'alopeyk-woocommerce-shipping' )
						);
					}
				} catch ( Exception $e ) {
					if ( $new_order_id ) {
						$this->cancel_order( $new_order_id, '' );
					}
					$response = array(
						'success' => false,
						'message' => __( $e->getMessage(), 'alopeyk-woocommerce-shipping' ),
						'data'    => $order_data,
					);
				}
			} else {
				$response = array(
					'success' => false,
					'message' => sprintf( __( 'You are not authenticated. Please recheck your API key entered in <a href="%s" target="_blank">Settings</a> page or <a href="%s" target="_blank">Contact Alopeyk</a>.', 'alopeyk-woocommerce-shipping' ), $this->get_settings_url(), $this->get_support_url() ),
					'data'    => $order_data,
				);
			}
		}
		return $response;

	}

	/**
	 * @since  1.0.0
	 * @param  array  $wc_order_ids
	 * @param  string $type
	 * @param  string $scheduled_at
	 * @param  string $description
	 * @return array
	 */
	public function check_order( $wc_order_ids = array(), $type = null, $scheduled_at = null, $description = null ) {

		$package = null;
		if ( $wc_order_ids && count ( $wc_order_ids ) ) {
			if ( $package = $this->get_orders_package( $wc_order_ids ) ) {
				$package = (object) $package;
				$package->description = $description;
				$package->scheduled_at = $scheduled_at;
				$package->orders = $wc_order_ids;
				if ( $this->is_enabled() ) {
					$type = $this->get_transport_type( $package->overflowed, $type, false );
					$package->type = $type;
					if ( $type ) {
						if ( $this->is_available_for_destinations( $package->destinations ) ) {
							if ( $credit = $this->get_user_data( 'credit' ) * 10 ) {
								$shipping = (object) $this->calculate_shipping( $package, $type, $package->has_return, 'dynamic', false );
								$package->shipping = $shipping;
								$cost = $shipping->cost;
								$diff = $cost - $credit;
								if ( $diff <= 0 ) {
									$response = array(
										'success' => true,
										'message' => __( 'Your Alopeyk order is ready to submit.', 'alopeyk-woocommerce-shipping' )
									);
								} else {
									$response = array(
										'success' => false,
										'message' => sprintf( __( 'Order price is %s while your credit balance is %s. You need to <a href="%s" class="awcshm-credit-modal-toggler" data-credit-amount="%s">add at least %s more credit to your Alopeyk account</a> to be enable to ship selected package(s).', 'alopeyk-woocommerce-shipping' ), wc_price( $this->normalize_price( $cost ) ), wc_price( $this->normalize_price( $credit ) ), add_query_arg( 'amount', $diff, admin_url( 'admin.php?page=alopeyk-credit' ) ), $diff, wc_price( $this->normalize_price( $diff ) ) )
									);
								}
							} else {
								$response = array(
									'success' => false,
									'message' => __( 'Unable to get your Alopeyk credit.', 'alopeyk-woocommerce-shipping' )
								);
							}
						} else {
							$response = array(
								'success' => false,
								'message' => __( 'One or more order shipping addresses are not supported by Alopeyk shipping method.', 'alopeyk-woocommerce-shipping' ),
							);
						}
					} else {
						$response = array(
							'success' => false,
							'message' => __( 'Order items have a total weight or volume more than maximum allowed for motorbike.', 'alopeyk-woocommerce-shipping' ),
						);
					}
				} else {
					$response = array(
						'success' => false,
						'message' => __( 'Alopeyk shipping method is not active.', 'alopeyk-woocommerce-shipping' ),
					);
				}
			} else {
				$response = array(
					'success' => false,
					'message' => __( 'Error occurred while fetching data for orders.', 'alopeyk-woocommerce-shipping' ),
				);
			}
		} else {
			$response = array(
				'success' => false,
				'message' => __( 'No order selected for shipping.', 'alopeyk-woocommerce-shipping' ),
			);
		}
		$response['package'] = $package;
		return $response;

	}

	/**
	 * @since  1.0.0
	 * @param  integer $order_id
	 * @param  integer $rate
	 * @param  string  $reason
	 * @param  string  $comment
	 * @param  integer $local_order_id
	 * @return array
	 */
	public function finish_order( $order_id = null, $rate = null, $reason = null, $comment = '', $local_order_id = null ) {

		if ( $order_id ) {
			if ( $rate ) {
				if ( $reason || $rate == 5 ) {
					try {
						if ( $this->authenticate() ) {
							$apiResponse = Order::finish( $order_id, array(
								'rate' => $rate,
								'comment' => $reason && $rate != 5 ? ( $reason == 'others' && ! empty( $comment ) ? 'others: ' . $comment : $reason ) : ''
							));
							if ( $apiResponse && isset( $apiResponse->status ) ) {
								if ( $apiResponse->status == 'success' ) {
									if ( $local_order_id ) {
										$this->update_active_order( $local_order_id, 'finished' );
									}
									$response = array(
										'success' => true,
										'message' => __( 'Your rate successfully submitted.', 'alopeyk-woocommerce-shipping' )
									);
								} else if ( isset( $apiResponse->message ) ) {
									$response = array(
										'success' => false,
										'message' => __( 'Error occured while trying to save your rate.', 'alopeyk-woocommerce-shipping' ) . '<br><br><strong>' . __( 'Detail:', 'alopeyk-woocommerce-shipping' ) . '</strong><br>' . $apiResponse->message
									);
								} else {
									$response = array(
										'success' => false,
										'message' => __( 'Error occured while trying to save your rate.', 'alopeyk-woocommerce-shipping' )
									);
								}
							} else {
								$response = array(
									'success' => false,
									'message' => __( 'Error occured while trying to save your rate.', 'alopeyk-woocommerce-shipping' )
								);
							}
						} else {
							$response = array(
								'success' => false,
								'message' => __( 'Authentication failed.', 'alopeyk-woocommerce-shipping' )
							);
						}
					} catch ( Exception $e ) {
						$response = array(
							'success' => false,
							'message' => __( $e->getMessage(), 'alopeyk-woocommerce-shipping' ),
						);
					}
				} else {
					$response = array(
						'success' => false,
						'message' => __( 'No reason selected for low score.', 'alopeyk-woocommerce-shipping' )
					);
				}
			} else {
				$response = array(
					'success' => false,
					'message' => __( 'Rate is not specified.', 'alopeyk-woocommerce-shipping' )
				);
			}
		} else {
			$response = array(
				'success' => false,
				'message' => __( 'Order ID is required for rate submission.', 'alopeyk-woocommerce-shipping' )
			);
		}
		return $response;

	}

	/**
	 * @since  1.0.0
	 * @param  integer $order_id
	 * @param  string  $reason
	 * @param  integer $local_order_id
	 * @return array
	 */
	public function cancel_order( $order_id = null, $reason = '', $local_order_id = null ) {

		$response = array(
			'success' => false,
			'message' => __( 'Order should be specified to be canceled.', 'alopeyk-woocommerce-shipping' ),
		);
		if ( $order_id ) {
			try {
				if ( $this->authenticate() ) {
					$apiResponse = Order::cancel( $order_id, $reason );
					if ( isset( $apiResponse->status ) && $apiResponse->status == 'success' ) {
						if ( $local_order_id ) {
							$this->update_active_order( $local_order_id, 'cancelled' );
						}
						$response = array(
							'success' => true,
							'message' => __( 'Order successfully canceled.', 'alopeyk-woocommerce-shipping' ),
						);
					} else if ( isset( $apiResponse->status ) && $apiResponse->status == 'fail' && isset( $apiResponse->object ) && isset( $apiResponse->object->error_msg ) ) {
						$response = array(
							'success' => false,
							'message' => __( 'Cannot cancel selected order.', 'alopeyk-woocommerce-shipping' ) . '<br><br><strong>' . __( 'Detail:', 'alopeyk-woocommerce-shipping' ) . '</strong><br>' . $apiResponse->object->error_msg,
						);
					} else {
						$response = array(
							'success' => false,
							'message' => __( 'Error occured while trying to cancel selected order.', 'alopeyk-woocommerce-shipping' )
						);
					}
				} else {
					$response = array(
						'success' => false,
						'message' => __( 'Authentication failed.', 'alopeyk-woocommerce-shipping' )
					);
				}
			} catch ( Exception $e ) {
				$response = array(
					'success' => false,
					'message' => __( 'Error occured while trying to cancel selected order.', 'alopeyk-woocommerce-shipping' ) . '<br><br><strong>' . __( 'Detail:', 'alopeyk-woocommerce-shipping' ) . '</strong><br>' . $e->getMessage(),
				);
			}
		}
		return $response;

	}

	/**
	 * @since 1.0.0
	 * @param integer $order_id
	 * @param object  $old_order_data
	 * @param object  $new_order_data
	 * @param string  $status
	 */
	public function update_order ( $order_id, $old_order_data, $new_order_data, $status = null ) {

		if ( $new_order_data && isset( $new_order_data->status ) ) {
			if ( $new_order_data->status == 'success' ) {
				$new_order_data = $new_order_data->object;
				$should_update = isset( $new_order_data->updated_at ) ? ( $new_order_data->updated_at != $old_order_data->updated_at ) : true;
				if ( ! $should_update ) {
					$new_addresses_count = isset( $new_order_data->addresses ) ? count( $new_order_data->addresses ) : 0;
					$old_addresses_count = isset( $old_order_data->addresses ) ? count( $old_order_data->addresses ) : 0;
					$should_update = $new_addresses_count != $old_addresses_count;
					if ( ! $should_update ) {
						$old_addresses = $old_order_data->addresses;
						$i = 0;
						foreach ( $new_order_data->addresses as $new_order_address ) {
							if ( ( $new_order_address->id != $old_addresses[$i]->id ) || ( $new_order_address->updated_at != $old_addresses[$i]->updated_at ) ) {
								$should_update = true;
								break;
							}
							$i++;
						}
					}
				}
			} else {
				$should_update = true;
				$new_order_data = $old_order_data;
				$new_order_data->status = $status ? $status : 'deleted';
				$new_order_data->updated_at = date( 'Y-m-dTH:i:s' ); // Only to support deleted status
			}
			if ( $should_update ) {
				$result = wp_update_post( array(
					'ID'            => $order_id,
					'post_status'   => $this->get_order_status( $new_order_data ),
					'post_date_gmt' => '0000-00-00 00:00:00'
				), true );
				if ( is_wp_error( $result ) ) {
					$this->add_log( $result->get_error_message() );
				}
				update_post_meta( $order_id, '_awcshm_order_data', $new_order_data );
				update_post_meta( $order_id, '_awcshm_order_type', $new_order_data->transport_type );
				update_post_meta( $order_id, '_awcshm_order_price', $new_order_data->price * 10 );
				$wc_orders = get_post_meta( $order_id, '_awcshm_wc_order_id' );
				$status = $new_order_data->status;
				if ( $wc_orders ) {
					foreach ( $wc_orders as $wc_order ) {
						$order = new WC_Order( $wc_order );
						$status_details = $this->get_wc_order_status( $new_order_data, $order_id );
						if ( $status_details && count( $status_details ) && $status_details['status'] != get_post_status( $wc_order ) && $this->get_option('status_change', 'yes') == 'yes' ) {
							$order->update_status( $status_details['status'], $status_details['note'] );
						}

					}
				}
				if ( in_array( $new_order_data->status, array( 'cancelled', 'expired', 'finished', 'deleted' ) ) ) {
					wp_clear_scheduled_hook( METHOD_ID . '_active_order_update', array( 'order_id' => $order_id ) );
				}
				if ( in_array( $new_order_data->status, array( 'accepted' ) ) && count( $wc_orders ) && $customers = get_post_meta( $order_id, '_awcshm_user_id' ) ) {
					$recipients = array_map( function ( $user_id ) {
						$user_email = null;
						if ( $user_data = get_userdata( $user_id ) ) {
							$user_email = $user_data->user_email;
						}
						return $user_email;
					}, $customers );
					$order_link = null;
					$message = '';
					if ( count( $wc_orders ) == 1 ) {
						$wc_order = $wc_orders[0];
						$wc_order = new WC_Order( $wc_order );
						$order_link = $wc_order->get_view_order_url();
						$message .= '<p><strong>' . sprintf( __( 'Dear %s,', 'alopeyk-woocommerce-shipping' ), $order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name() ) . '</strong></p>';
					}
					$subject = __( 'Your order is being shipped via Alopeyk', 'alopeyk-woocommerce-shipping' );
					$message .= '<p>' . sprintf( __( '%s is picked up from <a href="%s">%s</a> and is being delivered to you by <a href="%s">Alopeyk</a> courier.', 'alopeyk-woocommerce-shipping' ), ( $order_link ? '<a href="' . $order_link . '">' . __( 'Your order', 'alopeyk-woocommerce-shipping' ) . '</a>' : __( 'Your order', 'alopeyk-woocommerce-shipping' ) ), get_permalink( wc_get_page_id( 'shop' ) ), get_option( 'woocommerce_email_from_name' ), $this->get_campaign_url( 'status_change:' . $new_order_data->status ) ) . '</p>';
					if ( $this->can_be_tracked( $new_order_data ) ) {
						$message .= '<p class="button-container"><a href="' . $this->get_tracking_url( $new_order_data, false ) . '" class="button">' . __( 'Track', 'alopeyk-woocommerce-shipping' ) . '</a></p>';
					}
					$this->send_email( $recipients, $subject, $message, 'status_change:' . $new_order_data->status );
				};
			}
		}

	}

	/**
	 * @since 1.0.0
	 * @param integer $order_id
	 * @param string  $status
	 */
	public function update_active_order( $order_id = null, $status = null ) {

		if ( $order_id ) {
			$alopeyk_order_id = get_post_meta( $order_id, '_awcshm_order_id', true );
			if ( $alopeyk_order_id ) {
				$old_order_data = get_post_meta( $order_id, '_awcshm_order_data', true );
				if ( ! $old_order_data ) {
					$old_order_data = (object) array( 'updated_at' => '0000-00-00 00:00:00' );
				}
				if ( $status ) {
					$new_order_data = (object) array( 'status' => 'manual' );
					$this->update_order( $order_id, $old_order_data, $new_order_data, $status );
				} else {
					try {
						if ( $this->authenticate() ) {
							$new_order_data = Order::getDetails( $alopeyk_order_id );
							$this->update_order( $order_id, $old_order_data, $new_order_data );
						}
					} catch ( Exception $e ) {
						$this->add_log( $e->getMessage() );
					}
				}
			}
		}

	}

	/**
	 * @since  1.0.0
	 * @param  array $schedules
	 * @return array
	 */
	public function add_cron_schedule( $schedules ) {

		$interval = (int) $this->get_option( 'refresh_cron_interval', 10 );
		$schedules[ METHOD_ID . '_active_order_update_interval' ] = array(
			'interval' => $interval > 1 ? $interval : 1,
			'display'  => __( 'Update Active Order Interval', 'alopeyk-woocommerce-shipping' ),
		);
		return $schedules;

	}

	/**
	 * @since  1.0.0
	 * @param  string $string
	 * @return string
	 */
	static function convert_numbers_to_persion( $string ) {

		$persian_num_array = array(
			'0' => '۰',
			'1' => '۱',
			'2' => '۲',
			'3' => '۳',
			'4' => '۴',
			'5' => '۵',
			'6' => '۶',
			'7' => '۷',
			'8' => '۸',
			'9' => '۹'
		);
		return strtr( $string, $persian_num_array );

	}
	
	/**
	 * @since  1.4.0
	 * @return string
	 */
	public function get_timezone_setting() {

		$timezonestring = get_option( 'timezone_string' );
		if ( $this->get_option( 'tehran_timezone', 'yes' ) == 'yes' ) {
			return 'Asia/Tehran';
		} elseif( empty( $timezonestring ) ) {
			return 'UTC';
		} else {
			return $timezonestring;
		}

	}

}

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
		'addresses_limit'              => 5,
		'cancel_penalty_delay'         => 5,                                                               // Minutes
		'cancel_penalty_amount'        => 0,                                                               // Tomans
		'schedule_days_count'          => 30,
		'schedule_time_interval'       => 1,                                                               // Minutes
		'schedule_first_request_delay' => 1,                                                               // Minutes
		'credit_amounts'               => array( '10000', '20000', '30000', '50000', '100000', '200000' ), // Tomans
		'supportTel'                   => '+982141346',
		'devEmail'                     => 'dev@alopeyk.com',
		'transport_limits'             => array(
			'motorbike' => array(
				'max_weight' => 25000,    // g
				'max_width'  => 45,       // cm
				'max_height' => 45,       // cm
				'max_length' => 45,       // cm
			),
			'car' => array(
				'max_weight' => 100000,   // g
				'max_width'  => 50,       // cm
				'max_height' => 100,      // cm
				'max_length' => 50,       // cm
			),
			'cargo_s' => array(
				'max_weight' => 500000,   // g
				'max_width'  => 150,      // cm
				'max_height' => 150,      // cm
				'max_length' => 150,      // cm
			),
			'cargo' => array(
				'max_weight' => 1500000,  // g
				'max_width'  => 150,      // cm
				'max_height' => 200,      // cm
				'max_length' => 150,      // cm
			),

		),
	);
	public $transport_types = null;
	private static $parsimap_base_url = "https://pm2.parsimap.com/webapi.svc/";

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
	 * @since 2.0.0
	 */
	public function woocommerce_tab_init( $settings ) {
		
		include_once( 'class-alopeyk-woocommerce-shipping-tab.php' );
		$settings[] = new Alopeyk_WooCommerce_Shipping_Common_Settings();
		return $settings;

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

		$methods[METHOD_ID] = METHOD_ID;
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
						'marker'  => $this->get_option( 'map_marker', 'https://unpkg.com/leaflet@1.6.0/dist/images/marker-icon.png', false ),
						'styles'  => $this->get_option( 'map_styles' ),
						'api_url' => 'https://alopeyk.parsimap.com/comapi.svc/tile/googlemap/{x}/{y}/{z}/{{TOKEN}}',
						'api_key' => 'ALo575W-53FG6cv8-OPw330-kmA99q',
						'leaflet_gesture_handling' => array(
							'css' => plugin_dir_url( __FILE__ ) . '../public/css/leaflet-gesture-handling' . ( WP_DEBUG ? '' : '.min' ) . '.css',
							'js'  => plugin_dir_url( __FILE__ ) . '../public/js/leaflet-gesture-handling'  . ( WP_DEBUG ? '' : '.min' ) . '.js',
						),
					),
					'config'  => array(
						'store_city'         => strtolower( $this->get_option( 'store_city', 'tehran' ) ),
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
				'Ship'                                => __( 'Ship',                              'alopeyk-woocommerce-shipping' ),
				'Submit'                              => __( 'Submit',                            'alopeyk-woocommerce-shipping' ),
				'Cancel'                              => __( 'Cancel',                            'alopeyk-woocommerce-shipping' ),
				'Close'                               => __( 'Close',                             'alopeyk-woocommerce-shipping' ),
				'Submit Order'                        => __( 'Submit Order',                      'alopeyk-woocommerce-shipping' ),
				'Add Alopeyk Coupon'                  => __( 'Add Alopeyk Coupon',                'alopeyk-woocommerce-shipping' ),
				'Add Alopeyk Credit'                  => __( 'Add Alopeyk Credit',                'alopeyk-woocommerce-shipping' ),
				'Cancel Alopeyk Order'                => __( 'Cancel Alopeyk Order',              'alopeyk-woocommerce-shipping' ),
				'Rate Alopeyk Courier'                => __( 'Rate Alopeyk Courier',              'alopeyk-woocommerce-shipping' ),
				'Cancel Order'                        => __( 'Cancel Order',                      'alopeyk-woocommerce-shipping' ),
				'Alopeyk Order'                       => __( 'Alopeyk Order',                     'alopeyk-woocommerce-shipping' ),
				'Alopeyk Coupon'                      => __( 'Alopeyk Coupon',                    'alopeyk-woocommerce-shipping' ),
				'Add Coupon'                          => __( 'Add Coupon',                        'alopeyk-woocommerce-shipping' ),
				'Pay'                                 => __( 'Pay',                               'alopeyk-woocommerce-shipping' ),
				'Apply'                               => __( 'Apply',                             'alopeyk-woocommerce-shipping' ),
				'Yes'                                 => __( 'Yes',                               'alopeyk-woocommerce-shipping' ),
				'No'                                  => __( 'No',                                'alopeyk-woocommerce-shipping' ),
				'Track Order'                         => __( 'Track Order',                       'alopeyk-woocommerce-shipping' ),
				'View Order'                          => __( 'View Order',                        'alopeyk-woocommerce-shipping' ),
				'View Invoice'                        => __( 'View Invoice',                      'alopeyk-woocommerce-shipping' ),
				'Ship via Alopeyk'                    => __( 'Ship via Alopeyk',                  'alopeyk-woocommerce-shipping' ),
				'Unkown error occurred.'              => __( 'Unkown error occurred.',            'alopeyk-woocommerce-shipping' ),
				'Request failed:'                     => __( 'Request failed:',                   'alopeyk-woocommerce-shipping' ),
				'Add Discount Coupon'                 => __( 'Add Discount Coupon',               'alopeyk-woocommerce-shipping' ),
				'Order Status'                        => __( 'Order Status',                      'alopeyk-woocommerce-shipping' ),
				'Increase credit'                     => __( 'Increase credit',                   'alopeyk-woocommerce-shipping' ),
				'Charge account with gift card'       => __( 'Charge account with gift card',     'alopeyk-woocommerce-shipping' ),
				'Convert Alopeyk Scores to Credit'    => __( 'Convert Alopeyk Scores to Credit',  'alopeyk-woocommerce-shipping' ),
				'Use two fingers to move the map'     => __( 'Use two fingers to move the map',   'alopeyk-woocommerce-shipping' ),
				'Use ctrl + scroll to zoom the map'   => __( 'Use ctrl + scroll to zoom the map', 'alopeyk-woocommerce-shipping' ),
				'Use ⌘ + scroll to zoom the map'      => __( 'Use ⌘ + scroll to zoom the map',    'alopeyk-woocommerce-shipping' ),
			),
			'dynamic_parts' => $this->get_dynamic_parts( is_admin() ),
			'refresh_interval' => $this->get_option( ( is_admin() ? 'refresh_admin_interval' : 'refresh_public_interval' ), 10 ),
			'time' => (int) $this->get_now_in_milliseconds(),
		);

	}

	/**
	 * @since  2.0.0
	 * @return mixed
	 */
	public function get_partials_data($name) {

		return ( plugin_dir_url( __FILE__ ) . '../public/partials/' . $name );

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
				'.awcshm-credit-widget-container',
				'#woocommerce-order-notes',
				'#alopeyk_woocommerce_shipping_method-wcorder-actions',
				'#alopeyk_woocommerce_shipping_method-wcorder-history'
			);
			$screen = get_current_screen();
			if ( in_array( $screen->id, array( 'edit-shop_order', 'edit-' . self::$order_post_type_name ) ) ) {
				$dynamic_parts[] = '.wp-list-table';
				$dynamic_parts[] = '.subsubsub';
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
	 * only for translation and add words to pot file
	 * @since  1.0.0
	 * @return array
	 */
	public function index_reverse_translation() {

		$clauses = array(
			__( 'دیر رسیدن به مبدا یا مقصد',           'alopeyk-woocommerce-shipping' ),
			__( 'برخورد و رفتار بد',                   'alopeyk-woocommerce-shipping' ),
			__( 'ظاهر نامرتب و بی نظم',                'alopeyk-woocommerce-shipping' ),
			__( 'برخورد و رفتار بد',                   'alopeyk-woocommerce-shipping' ),
			__( 'دیر رسیدن به مبدا یا مقصد',           'alopeyk-woocommerce-shipping' ),
			__( 'درخواست هزینه اضافه',                 'alopeyk-woocommerce-shipping' ),
			__( 'عدم تماس با درخواست دهنده',           'alopeyk-woocommerce-shipping' ),
			__( 'نداشتن باکس حمل مرسوله',              'alopeyk-woocommerce-shipping' ),
			__( 'عدم تسلط بر مسیر',                    'alopeyk-woocommerce-shipping' ),
			__( 'سایر موارد',                          'alopeyk-woocommerce-shipping' ),
			__( 'برخورد و رفتار بد',                   'alopeyk-woocommerce-shipping' ),
			__( 'پیک تقاضای لغو درخواست نمود',         'alopeyk-woocommerce-shipping' ),
			__( 'فاصله پیک تا مبدا',                   'alopeyk-woocommerce-shipping' ),
			__( 'سفیر باکس حمل مرسوله به همراه نداشت', 'alopeyk-woocommerce-shipping' ),
			__( 'عدم تماس با درخواست دهنده',           'alopeyk-woocommerce-shipping' ),
			__( 'سایر موارد',                          'alopeyk-woocommerce-shipping' ),
		);
		return $clauses;

	}

	/**
	 * only for translation and add words to pot file
	 * @since  1.5.1
	 * @return array
	 */
	public function index_transport_types_translation() {

		$clauses = array(
			__( 'Motorbike',   'alopeyk-woocommerce-shipping' ),
			__( 'Cart Bike',   'alopeyk-woocommerce-shipping' ),
			__( 'Cargo',       'alopeyk-woocommerce-shipping' ),
			__( 'Small Cargo', 'alopeyk-woocommerce-shipping' ),
			__( 'Car',         'alopeyk-woocommerce-shipping' ),
			__( 'Production',  'alopeyk-woocommerce-shipping' ),
			__( 'Sandbox',     'alopeyk-woocommerce-shipping' ),
			__( 'Custom',      'alopeyk-woocommerce-shipping' ),

		);
		return $clauses;

	}

	/**
	 * @since  1.0.0
	 * @param  object $order
	 * @return string
	 */
	public function get_order_address_status( $order ) {

		$status           = $order->status;
		$next_address_any = isset( $order->next_address_any_full ) ? $order->next_address_any_full : ( isset( $order->next_address_any ) ? $order->next_address_any : null );
		$has_return       = $order->has_return;
		$addresses_count  = count( $order->addresses );
		$eta              = $order->eta_minimal;
		$hasSingleAddress = $addresses_count < 3 || ( $addresses_count == 3 && $has_return );
		$statusText       = __( 'Please wait ...', 'alopeyk-woocommerce-shipping' );
		$duration         = $eta && $eta->duration ? ceil( $eta->duration / 60 ) : 0;
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
					'initial_hour'   => (int) $this->convert_numbers( $initial_time[0], 'persian' ),
					'initial_minute' => (int) $this->convert_numbers( $initial_time[1], 'persian' ),
				);
			}
		}
		return $schedule_dates;

	}

	/**
	 * @since  1.5.0
	 * @return string
	 */
	public function get_parsimap_api_key() {

		$parsimap_api_key = null;
		$response = wp_remote_get( self::$parsimap_base_url . 'login/www.parsimap.com/39/0/b3031348-7334-4fbf-ae8c-eedf7de0f905/1' );
		if( $response ) {
			$responseBody = json_decode(wp_remote_retrieve_body($response));
			$parsimap_api_key = $responseBody->user_token;
			update_option( 'awcshm_parsimap_api_key', $parsimap_api_key );
		}
		return $parsimap_api_key;

	}

	/**
	 * @since  2.0.0
	 * @return string
	 */
	public function get_parsimap_api_response($url) {

		$parsimap_api_key = get_option( 'awcshm_parsimap_api_key' );
		$infLoop = true;
		do {
			if ( !$parsimap_api_key ) {
				$parsimap_api_key = $this->get_parsimap_api_key();
			}
			$requestUrl = str_replace('%token%', $parsimap_api_key, $url );
			$response = wp_remote_get( $requestUrl );

			if( $response ) {
				$responseBody = json_decode(wp_remote_retrieve_body($response));
				if ( $responseBody && $responseBody->status =="SUCCESS" ) {
					$infLoop =false;
				}
			}

			$parsimap_api_key = null;
		} while ( $infLoop );

		return $responseBody;

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
	* @since 1.6.0
	*/
	public function has_virtual_product() {

		global $woocommerce;
		$has_virtual_products = false;
		$virtual_products = 0;
		$products = $woocommerce->cart->get_cart();
		foreach ( $products as $product ) {
			$product_id = $product['product_id'];
			$is_virtual = get_post_meta( $product_id, '_virtual', true );
			if ( $is_virtual == 'yes' ) {
				$virtual_products += 1;
			}
		}
		if ( count( $products ) == $virtual_products ) {
			$has_virtual_products = true;
		}
		return $has_virtual_products;
	}

	/**
	 * @since 1.0.0
	 * @param array $checkout
	 */
	public function add_address_fields( $checkout = null ) {

		if ( $this->is_enabled() && ! ( $checkout && $this->has_virtual_product() ) ) {
			$shipping_address_latitude  = $checkout ? WC()->session->get( 'destination_latitude' )  : null;
			$shipping_address_longitude = $checkout ? WC()->session->get( 'destination_longitude' ) : null;
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
			   ( isset( $data->destination_address )   && ( ! $data->destination_address   || empty( $data->destination_address ) ) )
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
			$matched_methods = array_filter( $data->shipping_method, function( $var ) {
				return (bool) ( METHOD_ID == explode( '-', $var )[0] );
			});
			if ( count( $matched_methods ) ) {
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

		$packages[0]['destination']['latitude']  = WC()->session->get( 'destination_latitude' );
		$packages[0]['destination']['longitude'] = WC()->session->get( 'destination_longitude' );
		$packages[0]['destination']['location']  = WC()->session->get( 'destination_address' );
		$packages[0]['active_payment_method']    = WC()->session->get( 'active_payment_method' );
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
			// 'city'    => $data->city,
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
	public function authenticate( $only_set_token = false, $api_key = null, $force_set = false, $environment = null, $endpoint = null ) {

		if ( ! AloPeykApiHandler::getToken() || $force_set ) {
			$api_key                  = $api_key                           ? $api_key                  : $this->get_option( 'api_key',               null, false );
			$environment              = $environment                       ? $environment              : $this->get_option( 'environment',           null, false );
			$endpoint['url']          = isset( $endpoint['url']          ) ? $endpoint['url']          : $this->get_option( 'endpoint_url',          null, false );
			$endpoint['api_url']      = isset( $endpoint['api_url']      ) ? $endpoint['api_url']      : $this->get_option( 'endpoint_api_url',      null, false );
			$endpoint['tracking_url'] = isset( $endpoint['tracking_url'] ) ? $endpoint['tracking_url'] : $this->get_option( 'endpoint_tracking_url', null, false );

			if ( $api_key ) {
				AloPeykApiHandler::setToken( $api_key );
				AloPeykApiHandler::setEndpoint( $environment, $endpoint );
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
				AloPeykApiHandler::setEndpoint( null, null );
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
	 * @param  float   $lat
	 * @param  float   $lng
	 * @param  boolean $array
	 * @return object
	 */
	public function get_location( $lat = null, $lng = null, $array = false ) {

		$location = null;
		if ( ! is_null( $lat ) && ! is_null( $lng ) ) {
			$lat = number_format( (float) $lat, 6, '.', '' );
			$lng = number_format( (float) $lng, 6, '.', '' );
			$location = $array ? [ $lat, $lng ] : (object) array(
				'lat' => $lat,
				'lng' => $lng
			);
		}
		return $location;

	}

	/**
	 * @since 1.0.0
	 * @param array $data
	 */
	public function ajax_get_address( $data ) {

		$data     = (object) $data;
		$lat      = $data->lat;
		$lng      = $data->lng;
		$location = $this->get_location( $lat, $lng );
		$address  = $this->get_address( $location );
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
	 * @since  1.5.0
	 * @param  string $url
	 * @return array
	 */
	public function get_parsimap_response( $url = null , $multiple = false, $latlng = null ) {

		if ( $url ) {
			if ( $multiple ) {
				$latlng = $latlng ? $latlng : '35.744989,51.375284';
				$response = $this->get_parsimap_api_response(self::$parsimap_base_url . 'geocode/' . urlencode( $url ) . '/5/' . str_replace(',', '/', $latlng ) . '/%token%/' . time() )->result;
			} else {
				$response = $this->get_parsimap_api_response(self::$parsimap_base_url . 'areaInfo/' . str_replace(',', '/', $url ) . '/18/1/' . '%token%/1' );
			}
		}
		if ( $response ) {
			return $response;
		}
		
		return null;

	}

	/**
	 * @since 1.0.0
	 * @param array $data
	 */
	public function ajax_suggest_address( $data ) {

		$data   = (object) $data;
		$input  = $data->input;
		$latlng = '';
		$lat = isset( $data->lat ) ? $data->lat : null;
		$lng = isset( $data->lng ) ? $data->lng : null;
		$latlng = $lat && $lng ? ( $lat . ',' . $lng ) : '';
		$addresses = $this->suggest_address( $input, $latlng );
		if ( $addresses ) {
			$this->respond_ajax( $addresses );
		} else {
			$this->respond_ajax( __( 'No address found.', 'alopeyk-woocommerce-shipping' ), false );
		}

	}

	/**
	 * @since 1.7.0
	 * @param array $data
	 */
	// public function ajax_get_iran_cities( $data ) {

	// 	$data = (object) $data;
	// 	if ( isset( $data->selected_state) ) {
	// 		$cities = $this->get_iran_province_cities_data($data->selected_state);
	// 		$this->respond_ajax( array(
	// 			'cities'            => $cities,
	// 			'pre_billing_city'  => get_user_meta( get_current_user_id(), 'billing_city',  true ),
	// 			'pre_shipping_city' => get_user_meta( get_current_user_id(), 'shipping_city', true ),
	// 		), false );
	// 	}

	// }

	/**
	 * @since  2.0.0
	 * @return mixed
	 */
	// public function get_iran_province_cities_data($province) {

	// 	$data = json_decode(file_get_contents($this->get_partials_data('iranـprovinces_cities.json')));

	// 	foreach ($data->provinces as $dProvince) {
	// 		$provinceName = null;
	// 		if($dProvince->name == $province) {
	// 			$provinceName = strtolower($dProvince->name_en);
	// 			break;
	// 		}
	// 	}

	// 	if($provinceName) {
	// 		foreach ($data->cities as $key => $cityList) {
	// 			if($key == $provinceName) {
	// 				return $cityList;
	// 			}
	// 		}
	// 	}

	// 	return $data->provinces;

	// }

	/**
	 * @since  1.0.0
	 * @param  object  $location
	 * @return array
	 */
	public function get_address( $location, $is_admin = false ) {

		if ( ! is_null( $location ) ) {
			if ( $is_admin ) {
				$apiResponse = null;
				try {
					$apiResponse = AloPeykApiHandler::getAddress( $location->lat, $location->lng );
				} catch ( Exception $e ) {
					$this->add_log( $e->getMessage() );
				}
				if ( $apiResponse && $apiResponse->status == 'success' && isset( $apiResponse->object->address ) ) {
					$location = $apiResponse->object;
					return array(
						'city'    => $location->city_fa,
						'address' => $location->city_fa . __( ',', 'alopeyk-woocommerce-shipping' ) . ' ' . ($location->region ? $location->region . __( ',', 'alopeyk-woocommerce-shipping' ) . ' ' : '') . ( isset( $location->address[0] ) ? $location->address[0] : '' )
					);
				}
			} else {
				$response = $this->get_parsimap_response( $location->lat . ',' . $location->lng , false );
				if ( $response ) {
					return array(
						'province' => ($response->result && $response->result[0]) ? $response->result[0]->title : '',
						'city'     => ($response->result && $response->result[1]) ? $response->result[1]->title : '',
						'address'  => $response->limitedFullAddress
					);
				}
			}
		}
		return null;

	}

	/**
	 * @since  1.0.0
	 * @param  string  $input
	 * @return array
	 */
	public function suggest_address( $input, $latlng = '', $is_admin = false ) {

		if ( ! empty( $input ) ) {
			$addresses   = array();
			$apiResponse = null;
			if ( $is_admin ) {
				try {
					$apiResponse = AloPeykApiHandler::getLocationSuggestion( $input, $latlng );
				} catch (Exception $e) {
					$this->add_log( $e->getMessage() );
				}
				if ( $apiResponse && $apiResponse->status == 'success' ) {
					$addresses = array_map( function ( $location ) {
						return array(
							'lat'     => $location->lat,
							'lng'     => $location->lng,
							'latlng'  => $location->lat . ',' . $location->lng,
							'city'    => $location->city,
							'address' => $location->city_fa . __( ',', 'alopeyk-woocommerce-shipping' ) . ' ' . $location->region . __( ',', 'alopeyk-woocommerce-shipping' ) . ' ' . $location->title
						);
					}, $apiResponse->object );
				}
			}
			if ( ! $is_admin ) {
				$extra_addresses = $this->get_parsimap_response( str_replace( ' ', '+', $input ) , true, $latlng );
				if ( $extra_addresses && count( $extra_addresses ) ) {
					foreach ( $extra_addresses as $extra_address ) {
						$addresses[] = array(
							'lat'     => $extra_address->center->lat,
							'lng'     => $extra_address->center->lng,
							'latlng'  => $extra_address->center->lat . ',' . $extra_address->center->lng,
							'city'    => $extra_address->area_name,
							'address' => $extra_address->area_name . __( ',', 'alopeyk-woocommerce-shipping' ) . ' ' . $extra_address->local_name
						);
					}
				}
			}
			$unique_addresses = array_unique( array_column( $addresses, 'latlng' ) );
			$addresses = array_intersect_key( $addresses, $unique_addresses );
			return array_values( $addresses );
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
	public function create_order_statuses( $return_statuses = false ) {

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
		if ( $return_statuses ) {
			return $order_statuses;
		}
		$wc_awcshm_statuses = $this->set_wc_awcshm_statuses();
		$order_statuses = array_merge( $order_statuses, $wc_awcshm_statuses );
		foreach ( $order_statuses as $order_status => $values ) {
			register_post_status( $order_status, $values );
		}

	}

	public function set_wc_awcshm_statuses() {
		
		$return= array(
			'wc-awcshm-scheduled'    => array(
				'label'                     => _x( 'Scheduled for sending with Alopeyk', 'Order status', 'alopeyk-woocommerce-shipping' ),
				'public'                    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'Scheduled for sending with Alopeyk <span class="count">(%s)</span>', 'Scheduled for sending with Alopeyk <span class="count">(%s)</span>', 'alopeyk-woocommerce-shipping' ),
			),
			'wc-awcshm-searching'  => array(
				'label'                     => _x( 'Finding Alopeyk courier', 'Order status', 'alopeyk-woocommerce-shipping' ),
				'public'                    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'Finding Alopeyk courier <span class="count">(%s)</span>', 'Finding Alopeyk courier <span class="count">(%s)</span>', 'alopeyk-woocommerce-shipping' ),
			),
			'wc-awcshm-processing' => array(
				'label'                     => _x( 'Sending with Alopeyk', 'Order status', 'alopeyk-woocommerce-shipping' ),
				'public'                    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'Sending with Alopeyk <span class="count">(%s)</span>', 'Sending with Alopeyk <span class="count">(%s)</span>', 'alopeyk-woocommerce-shipping' ),
			),
			'wc-awcshm-completed'  => array(
				'label'                     => _x( 'Delivered with Alopeyk', 'Order status', 'alopeyk-woocommerce-shipping' ),
				'public'                    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'Delivered with Alopeyk <span class="count">(%s)</span>', 'Delivered with Alopeyk <span class="count">(%s)</span>', 'alopeyk-woocommerce-shipping' ),
			),
			'wc-awcshm-failed'     => array(
				'label'                     => _x( 'Unsuccessful sending with Alopeyk', 'Order status', 'alopeyk-woocommerce-shipping' ),
				'public'                    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'Unsuccessful sending with Alopeyk <span class="count">(%s)</span>', 'Unsuccessful sending with Alopeyk <span class="count">(%s)</span>', 'alopeyk-woocommerce-shipping' ),
			)
		);
		return $return;

	}

	/**
	 * @since 1.7.0
	 */
	public function add_awcshm_order_statuses( $order_statuses ) {

		$wc_awcshm_statuses = $this->set_wc_awcshm_statuses();
		foreach ( $wc_awcshm_statuses as $key => $wc_awcshm_status ) {
			$order_statuses[$key] = $wc_awcshm_status['label'];
		}
		return $order_statuses;

	}

	/**
	 * @since  1.0.0
	 * @param  array   $weights
	 * @param  string  $unit
	 * @param  string  $type               
	 * @return boolean                     
	 */
	public function is_available_for_weights( $weights = array(), $unit = null, $limits = array() ) {

		if ( $weights && $unit ) {
			$total_weight = 0;
			foreach ( $weights as $weight ) {
				$weight = wc_get_weight( (float) $weight, 'g', $unit );
				$total_weight += $weight;
				if ( $total_weight > $limits['max_weight'] ) {
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
	 * @param  string  $type
	 * @return boolean
	 */
	public function is_available_for_dimensions( $dimensions = array(), $unit = null, $limits = array() ) {

		$total_volume    = 0;

		if ( $dimensions && $unit ) {
			$max_width  = $limits['max_width'];
			$max_height = $limits['max_height'];
			$max_length = $limits['max_length'];
			$max_volume = $max_width * $max_height * $max_length;
			foreach ( $dimensions as $dimension ) {
				$dimension = (object) $dimension;
				$width    = wc_get_dimension( (float) $dimension->width,  'cm', $unit );
				$height   = wc_get_dimension( (float) $dimension->height, 'cm', $unit );
				$length   = wc_get_dimension( (float) $dimension->length, 'cm', $unit );
				$volume   = isset( $dimension->volume ) ? wc_get_dimension( (float) $dimension->volume, 'cm', $unit ) : null;
				$quantity = isset( $dimension->quantity ) ? $dimension->quantity : 1;
				$total_volume += $volume ? $volume : ( $width * $height * $length * $quantity );
				$is_large = $width        > $max_width  ||
							$height       > $max_height ||
							$length       > $max_length ||
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
			foreach ( $destinations as $destination ) {
				$destination = (object) $destination;
				$available = isset( $destination->latitude )  && ! empty( $destination->latitude )  &&
							 isset( $destination->longitude ) && ! empty( $destination->longitude );
				if ( ! $available ) {
					return false;
				}
			}
			return true;
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
	public function has_overflow( $weights = array(), $dimensions = array(), $weight_unit = null, $dimension_unit = null, $type = 'motorbike' ) {

		if ( $weights && $dimensions && $weight_unit && $dimension_unit ) {
			$transport_types = $this->get_transport_types();
			if ( isset( $transport_types[$type] ) ) {
				$limits = $transport_types[$type]['limits'];
				if (
					! $this->is_available_for_weights( $weights, $weight_unit, $limits ) ||
					! $this->is_available_for_dimensions( $dimensions, $dimension_unit, $limits )
				) {
					return true;
				}
			}
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

		$available     = false;
		$check_weights = false;
		if ( $package ) {
			$package = (object) $package;
			$transport_types = $this->get_transport_types();
			if ( isset( $transport_types[$type] ) ) {
				$limits = $transport_types[$type]['limits'];
			} else {
				$limits = array();
			}
			if ( isset( $package->overflowed ) && isset( $package->overflowed[$type] ) ) {
				$available = ! $package->overflowed[$type];
			} else {
				$check_weights = true;
			}

			if ( $check_weights ) {
				$available = $this->is_available_for_weights( $package->weights, get_option( 'woocommerce_weight_unit' ), $limits );
			}
			if ( $available ) {
				$available = $this->is_available_for_dimensions( $package->dimensions, get_option( 'woocommerce_dimension_unit' ), $limits );
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
	public function calculate_shipping( $package = null, $type = 'motorbike', $has_return = null, $cost_type = null, $shipping_info = true, $is_frontend = true, $discount_coupon = null ) {

		if ( ! is_array( $shipping_info ) ) {
			$cost         = null;
			$cost_details = null;
			if ( $package ) {
				$package   = (object) $package;
				$cost_type = $cost_type ? $cost_type : $this->get_option( 'cost_type' );
				if ( $cost_type == 'static' ) {
					$static_cost_type = $this->get_option( 'static_cost_type' );
					if ( $static_cost_type == 'fixed' ) {
						$static_cost_fixed = $this->get_option( 'static_cost_fixed' );
						$cost = $static_cost_fixed * count( $package->destinations );
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
						$destinations    = [];
						$orders          = [];
						$apiResponses    = [];
						$store_params    = new stdClass();
						$store_params->store_lat  = $this->get_option( 'store_lat' );
						$store_params->store_lng  = $this->get_option( 'store_lng' );
						$store_params    = apply_filters( METHOD_ID . '/store_params', $store_params );
						$origin_location = $this->get_location( $store_params->store_lat, $store_params->store_lng );
						$origin          = new Address( 'origin', $origin_location->lat, $origin_location->lng );
						if ( is_null( $has_return ) ) {
							$has_return  = $this->has_return( $package->payment_method );
						}
						foreach ( $package->destinations as $dest ) {
							$dest                 = (object) $dest;
							$destination_location = $this->get_location( $dest->latitude, $dest->longitude );
							$destination          = new Address( 'destination', $destination_location->lat, $destination_location->lng );
							$destinations[]       = $destination;
						}
						if ( $is_frontend ) {
							$transport_types = $this->get_transport_types();
							foreach ( $transport_types as $key => $transport_type ) {
								if( ! $this->get_transport_type( $package->overflowed, $key ) ) {
									continue;
								}
								$order = new Order( $key, $origin, $destinations, null, $discount_coupon );
								$order->setHasReturn( false );
								$orders[] = $order;
							}
							if ( count( $orders ) ) {
								$apiResponse  = AloPeykApiHandler::getBatchPrice( $orders );
								$apiResponses = $apiResponse->object;
							}
						} else {
							$order = new Order( $type, $origin, $destinations, null, $discount_coupon );
							$order->setHasReturn( $has_return );
							$apiResponses[] = $order->getPrice()->object;
						}
					} catch ( Exception $e ) {
						$this->add_log( $e->getMessage() );
					}
				}
			}
			$shipping_info = [];
			if ( $cost_type == 'static' ) {
				$shipping_info['fixed'] = array(
					'type'         => 'fixed',
					'cost'         => $cost,
					'cost_type'    => $cost_type,
					'has_return'   => $has_return,
					'cost_details' => $cost_details,
				);
			} else {
				foreach ( $apiResponses as $apiResponse ) {
					if ( isset( $apiResponse->error ) ) {
						// Todo store error log
						continue;
					}

					$price             = isset( $apiResponse->price )             ? is_null( $apiResponse->price )             ? null : 10 * $apiResponse->price             : null;
					$price_with_return = isset( $apiResponse->price_with_return ) ? is_null( $apiResponse->price_with_return ) ? null : 10 * $apiResponse->price_with_return : null;
					$final_price       = isset( $apiResponse->final_price )       ? is_null( $apiResponse->final_price )       ? null : 10 * $apiResponse->final_price       : null;
					$discount          = ( isset( $apiResponse->discount ) && ! is_null( $apiResponse->discount ) ) ? ( $apiResponse->discount > 0 ? 10 * $apiResponse->discount : null ) : null;
					$score             = $is_frontend ? null : (isset( $apiResponse->score ) ? $apiResponse->score : null);
					$cost              = $is_frontend ? ( $has_return ? $price_with_return : $price ) : $price;
					$invalid_discount  = isset( $apiResponse->invalid_discount_coupons ) ? is_null( $apiResponse->invalid_discount_coupons ) ? null : $apiResponse->invalid_discount_coupons : null;
					$cost_details = array(
						'price'             => $price,
						'price_with_return' => $price_with_return,
						'final_price'       => $final_price,
						'discount'          => $discount,
					);
					$shipping_info[$apiResponse->transport_type] = array(
						'type'         => $apiResponse->transport_type,
						'cost'         => $cost,
						'cost_type'    => $cost_type,
						'has_return'   => $has_return,
						'cost_details' => $cost_details,
					);
					if ( $is_frontend ) {
						$shipping_info[$apiResponse->transport_type] = apply_filters( METHOD_ID . '/shipping_info', $shipping_info[$apiResponse->transport_type], $package );
					} else {
						$shipping_info[$apiResponse->transport_type]['score']       = $score;
						$shipping_info[$apiResponse->transport_type]['final_price'] = $final_price;
						$shipping_info[$apiResponse->transport_type]['discount']    = $discount;
						if ( isset( $discount ) && ! $discount && isset( $invalid_discount ) && $invalid_discount ) {
							if( isset( $$invalid_discount[0]->error_msg ) )
								$shipping_info[$apiResponse->transport_type]['discount_coupons_error_msg'] = $$invalid_discount[0]->error_msg;
						}
					}
				}
			}
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
	 * @return string
	 */
	public function get_transport_type( $overflowed = false, $type = 'motorbike' ) {

		if ( count( $overflowed ) ) {
			if ( isset( $overflowed[$type] ) && $overflowed[$type] ) {
				return false;
			}
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
	public function get_user_data( $key = null, $default = null, $can_be_empty = true, $with = false, $store_configs = true ) {

		$user_data = $this->get_configs( 'user', $with, $store_configs );
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
					'status' => 'wc-awcshm-scheduled',
					'note'   => sprintf( __( 'Order scheduled to be shipped via Alopeyk shipping method at %s.', 'alopeyk-woocommerce-shipping' ), date_i18n( 'j F Y (g:i A)', strtotime( $order->scheduled_at ) ) )
				);
			} else if ( in_array( $status, array( 'new', 'searching' ) ) ) {
				$response = array(
					'status' => 'wc-awcshm-searching',
					'note'   => __( 'Searching for the closest courier to assign shipping task.', 'alopeyk-woocommerce-shipping' )
				);
			} else if ( in_array( $status, array( 'accepted', 'picking', 'delivering' ) ) ) {
				$courier_info = isset( $order->courier_info ) ? ' (' . $order->courier_info->firstname . ' ' . $order->courier_info->lastname . ' ' . __( 'with the phone number', 'alopeyk-woocommerce-shipping' ) . ' ' . $order->courier_info->phone . ')' : '';
				$response = array(
					'status' => 'wc-awcshm-processing',
					'note'   => sprintf( __( 'Courier%s assigned and <a href="%s" target="_blank">shipping proccess</a> is started. It can be tracked <a href="%s" target="_blank">here</a>.', 'alopeyk-woocommerce-shipping' ), $courier_info, $order_id ? admin_url( 'post.php?action=edit&post=' . $order_id ) : '#', $this->get_tracking_url( $order ) )
				);
			} else if ( in_array( $status, array( 'delivered', 'finished' ) ) ) {
				$response = array(
					'status' => 'wc-awcshm-completed',
					'note'   => __( 'Order successfully delivered.', 'alopeyk-woocommerce-shipping' )
				);
			} else if ( in_array( $status, array( 'cancelled', 'deleted', 'expired' ) ) ) {
				$response = array(
					'status' => 'wc-awcshm-failed',
					'note'   => __( 'Shipping canceled or No courier found.', 'alopeyk-woocommerce-shipping' )
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
	 * @param  boolean $object
	 * @param  boolean $store_configs
	 * @return object
	 */
	public function get_configs( $scope = 'config', $with = false, $store_configs = true ) {

		if ( $this->config ) {
			return $scope ? ( isset( $this->config->{$scope} ) ? $this->config->{$scope} : null ) : $this->config;
		}
		$configs = (object) array( 'config' => array() );
		try {
			if ( $this->authenticate( true ) ) {
				$apiResponse = AloPeykApiHandler::authenticate( true, $with );
				if ( $apiResponse && isset( $apiResponse->status ) && $apiResponse->status == 'success' ) {
					$configs = $apiResponse->object;
				}
			}
		} catch ( Exception $e ) {}
		$configs->config = (object) array_merge( self::$configs, (array) $configs->config );
		if ( $store_configs ) {
			$this->config = (object) $configs;
		}
		return $scope ? ( isset( $configs->{$scope} ) ? $configs->{$scope} : null ) : $this->config;

	}

	/**
	 * @since  1.0.0
	 * @param  string  $config
	 * @param  mixed   $default
	 * @param  boolean $can_be_empty
	 * @param  boolean $store_configs
	 * @return mixed
	 */
	public function get_config( $config, $default = null, $can_be_empty = true, $store_configs = true ) {

		$configs = $this->get_configs( 'config', false, $store_configs );
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
			$weights      = array();
			$dimensions   = array();
			$destinations = array();
			$overflowed   = array();
			$has_return   = false;
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
			$transport_types = $this->get_transport_types();
			$weight_unit = get_option( 'woocommerce_weight_unit' );
			$dimension_unit = get_option( 'woocommerce_dimension_unit' );
			foreach ( $transport_types as $key => $transport_type ) {
				$overflowed[$key] = $this->has_overflow( $weights, $dimensions, $weight_unit, $dimension_unit, $key );
			}
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
		$wc_orders  = $package->orders;
		if ( $this->is_in_progress( $wc_orders ) ) {
			return array(
				'success' => false,
				'message' => __( 'Shipping proccess of one or more selected orders is in progress. Please cancel them before creating a new order.', 'alopeyk-woocommerce-shipping' ),
				'data'    => $order_data,
			);
		}

		if ( ! $this->authenticate() ) {
			return array(
				'success' => false,
				'message' => sprintf( __( 'You are not authenticated. Please recheck your API key entered in <a href="%s" target="_blank">Settings</a> page or <a href="%s" target="_blank">Contact Alopeyk</a>.', 'alopeyk-woocommerce-shipping' ), $this->get_settings_url(), $this->get_support_url() ),
				'data'    => $order_data,
			);
		}

		$origin_location = $this->get_location( $this->get_option( 'store_lat' ), $this->get_option( 'store_lng' ) );
		$origin          = new Address( 'origin', $origin_location->lat, $origin_location->lng );
		$origin->setDescription( $package->description );
		$origin->setUnit( $this->get_option( 'store_unit' ) );
		$origin->setNumber( $this->get_option( 'store_number' ) );
		$origin->setPersonFullname( $this->get_option( 'store_name' ) );
		$origin->setPersonPhone( $this->get_option( 'store_phone' ) );

		$destinations = array();
		foreach ( $package->destinations as $dest ) {
			$dest                 = (object) $dest;
			$destination_location = $this->get_location( $dest->latitude, $dest->longitude );
			$destination          = new Address( 'destination', $destination_location->lat, $destination_location->lng );
			$destination->setDescription( $dest->description );
			$destination->setUnit( $dest->unit );
			$destination->setNumber( $dest->number );
			$destination->setPersonFullname( $dest->fullname );
			$destination->setPersonPhone( $dest->phone );
			$destinations[] = $destination;
		}

		$new_order_id = null;
		try {
			$order = new Order( $package->type, $origin, $destinations, null, $package->discount_coupon );
			$order->setHasReturn( $package->has_return );
			$order->setCashed( false );
			if ( $package->scheduled_at ) {
				$order->setScheduledAt( $package->scheduled_at );
			}

			$credit = $this->get_user_data( 'credit' ) * 10;
			if ( ! $credit ) {
				return array(
					'success' => false,
					'message' => __( 'Unable to get your Alopeyk credit.', 'alopeyk-woocommerce-shipping' )
				);
			}


			$order_data = $order->create();
			if ( $order_data->status != 'success' ) {
				return array(
					'success' => false,
					'message' => __( $order->message, 'alopeyk-woocommerce-shipping' ),
					'data'    => $order_data,
				);
			}

			$order_data          = $order_data->object;
			$new_order_id        = $order_data->id;
			$tracking_url        = $this->get_tracking_url( $order_data );
			$detailed_order_data = Order::getDetails( $order_data->id );

			if ( empty( $detailed_order_data ) or ! isset( $detailed_order_data->status ) or $detailed_order_data->status != 'success' or ! isset( $detailed_order_data->object ) ) {
				if ( $new_order_id ) {
					$this->cancel_order( $new_order_id, '' );
				}

				return array(
					'success' => true,
					'message' => __( 'Error occured while trying to fetch Alopeyk order details. Order cancelled due to security reasons. Please try again later.', 'alopeyk-woocommerce-shipping' ),
					'data'    => $order_data,
				);
			}


			$order_data = $detailed_order_data->object;
			$result     = wp_insert_post( array(
				'post_title'  => $order_data->invoice_number,
				'post_type'   => self::$order_post_type_name,
				'post_status' => $this->get_order_status( $order_data ),
			), true );

			if ( is_wp_error( $result ) ) {
				$this->add_log( $result->get_error_message() );

				return array(
					'success' => false,
					'message' => sprintf( __( 'Error occurred while trying to write order as a Wordpress post. But your Alopeyk order is created and is in progress. You can <a href="%s" target="_blank">track your order here</a> or <a href="%s" target="_blank" >contact Alopeyk support</a>.', 'alopeyk-woocommerce-shipping' ), $tracking_url, $this->get_support_url() ) . '<br><br><strong>' . __( 'Detail:', 'alopeyk-woocommerce-shipping' ) . '</strong><br>' . $result->get_error_message(),
					'data'    => $order_data,
				);
			}

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
					$order          = new WC_Order( $wc_order );
					$status_details = $this->get_wc_order_status( $order_data, $order_id );
					if ( $status_details && count( $status_details ) && $status_details['status'] != get_post_status( $wc_order ) && $this->get_option( 'status_change', 'yes' ) == 'yes' ) {
						$order->update_status( $status_details['status'], $status_details['note'] );
					}
				}
			}
			$this->update_active_order( $order_id );
			$schedule_name = METHOD_ID . '_active_order_update';
			wp_schedule_event( time(), $schedule_name . '_interval', $schedule_name, array( 'order_id' => $order_id ) );
			$order_data->tracking_url = $tracking_url;
			$order_data->edit_url     = get_edit_post_link( $order_id );

			return array(
				'success' => true,
				'message' => __( 'Your order has been successfully created and is in progress.', 'alopeyk-woocommerce-shipping' ),
				'data'    => $order_data,
			);
		} catch ( Exception $e ) {
			if ( $new_order_id ) {
				$this->cancel_order( $new_order_id, '' );
			}

			return array(
				'success' => false,
				'message' => __( $e->getMessage(), 'alopeyk-woocommerce-shipping' ),
				'data'    => $order_data,
			);
		}
	}

	/**
	 * @since  1.0.0
	 * @param  array  $wc_order_ids
	 * @param  string $type
	 * @param  string $scheduled_at
	 * @param  string $description
	 * @return array
	 */
	public function check_order( $wc_order_ids = array(), $type = null, $scheduled_at = null, $description = null, $discount_coupon = null ) {

		$package = null;
		if ( $wc_order_ids && count ( $wc_order_ids ) ) {
			if ( $package = $this->get_orders_package( $wc_order_ids ) ) {
				$package = (object) $package;
				$package->description = $description;
				$package->scheduled_at = $scheduled_at;
				$package->orders = $wc_order_ids;
				$package->discount_coupon = $discount_coupon;
				if ( $this->is_enabled() ) {
					$type = $this->get_transport_type( $package->overflowed, $type );
					$package->type = $type;
					if ( $type ) {
						if ( $this->is_available_for_destinations( $package->destinations ) ) {
							$credit = $this->get_user_data( 'credit' ) * 10;
							if ( $credit >= 0 ) {
								$shipping = (object) $this->calculate_shipping( $package, $type, $package->has_return, 'dynamic', true, false, $discount_coupon );
								$shipping = (object) $shipping->{$type};
								$package->shipping = $shipping;
								$cost = $shipping->cost;
								if ( ! is_null( $cost ) ) {
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
										'message' => __( 'Unfortunately, we are not able to submit this request.', 'alopeyk-woocommerce-shipping' )
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
							'message' => __( 'Order items have a total weight or volume more than maximum allowed for the selected shipping method.', 'alopeyk-woocommerce-shipping' ),
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
	 * @since 1.7.0
	 */
	public function update_active_orders() {

		$progress_query = new WP_Query( array (
			'post_type'   => self::$order_post_type_name,
			'post_status' => array( 'awcshm-progress', 'awcshm-pending', 'awcshm-scheduled' ),
		));
		while ( $progress_query->have_posts() ) {
			$progress_query->the_post();
			$this->update_active_order( get_the_ID() );
		}

	}

	/**
	 * @since 1.7.0
	 */
	public function check_mandatory_options() {

		if ( ! get_option( 'awcshm_check_mandatory_options' ) ) {
			$awcshm_option_name = 'woocommerce_' . METHOD_ID . '_settings';
			if ( $options = get_option( $awcshm_option_name ) ) {
				if ( isset( $options[ 'wrong_key' ] ) ) {
					$options[ 'wrong_key' ] = 'yes';
					$options[  'enabled'  ] = 'no';
				}
				if ( isset( $options[ 'store_lat' ] ) && isset( $options[ 'store_lng' ] ) && isset( $options[ 'store_city' ] ) ) {
					$location = $this->get_location( $options[ 'store_lat' ], $options[ 'store_lng' ] );
					$location = $this->get_address( $location );
					if ( isset( $location[ 'city' ] ) ) {
						$options[ 'store_city' ] = $location[ 'city' ];
					}
				}
				update_option( $awcshm_option_name, $options );
				wp_clear_scheduled_hook( METHOD_ID . '_check_mandatory_options' );
			}
			update_option( 'awcshm_check_mandatory_options', true );
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
		$schedules[ METHOD_ID . '_check_mandatory_options_interval' ] = array(
			'interval' => 1,
			'display'  => __( 'Every Second', 'alopeyk-woocommerce-shipping' ),
		);
		return $schedules;

	}

	/**
	 * @since  1.0.0
	 * @param  string $string
	 * @return string
	 */
	static function convert_numbers( $string, $source = 'english' ) {

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
		if ( $source == 'persian' ) {
			$persian_num_array = array_flip( $persian_num_array );
		}
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

	/**
	 * @since  1.5.1
	 * @return array
	 */
	public function get_transport_types( $apply_admin_filter = true ) {

		if ( $this->transport_types ) {
			return $this->transport_types;
		}
		$transport_types = Configs::TRANSPORT_TYPES;
		$transport_limits = $this->get_config( 'transport_limits', null, true, false );
		foreach ( $transport_types as $key => $transport_type ) {

			if ( ! $transport_type['delivery'] || ( $apply_admin_filter && $this->get_option( 'pt_' . $key, 'yes' ) != 'yes' ) ) {
				unset( $transport_types[$key] );
				continue;
			}
			$transport_types[$key]['limits'] = $transport_limits[$key];
			$transport_types[$key]['label']  = $this->get_transport_type_name( $transport_types[$key]['label'], false );

		}
		uasort( $transport_types, array( $this, 'sort_transport_types' ) );
		$this->transport_types = $transport_types;
		return $transport_types;

	}

	/**
	 * @since  1.5.1
	 * @return string
	 */
	public function get_transport_type_name( $name, $recheck = true ) {

		if ( $recheck ) {
			if ( $name == 'fixed' ) {
				$name = 'Fixed';
			} else {
				$transport_types = Configs::TRANSPORT_TYPES;
				$name = $transport_types[$name]['label'];
			}
		}
		return __( $name, 'alopeyk-woocommerce-shipping' );

	}

	/**
	 * @since  1.5.1
	 * @return boolean
	 */
	public function sort_transport_types( $a, $b ) {

		if ( $a['limits']['max_weight'] == $b['limits']['max_weight'] ) {
			return 0;
		}
		return $a['limits']['max_weight'] > $b['limits']['max_weight'];

	}

	/**
	 * @since  2.0.0
	 * @return mixed
	 */
	// public function get_iran_provinces_data() {

	// 	$data = json_decode(file_get_contents($this->get_partials_data('iranـprovinces_cities.json')));
	// 	return $data->provinces;

	// }

	/**
	 * @since  1.7.0
	 * @param  string $coupon_code
	 * @return array
	 */
	public function get_customer_loyalty_products( $productId = null, $submit = false ) {

		$return_response = true;
		if ( $this->authenticate() ) {
			if ( is_null( $productId ) ) {
				$apiResponse = AloPeykApiHandler::CustomerLoyaltyProducts();
			} else {
				if ( $submit ) {
					$apiResponse = AloPeykApiHandler::CustomerLoyaltyProducts( $productId . '/redeem', 'POST' );
					if ( $apiResponse->status == 'success' ) {
						$response = array(
							'success' => true,
							'message' => '<div class="updated notice"><p>' . __( 'Successful Purchase! Your credit has been added.', 'alopeyk-woocommerce-shipping' ) . '</p></div>'
						);
						return $response;
					}
				} else {
					$apiResponse = AloPeykApiHandler::CustomerLoyaltyProducts( $productId );
				}
			}
			if ( isset( $apiResponse->status ) ) {
				$response = (array) $apiResponse;
			} else {
				$response = array(
					'success' => false,
					'message' => __( 'Error occured.', 'alopeyk-woocommerce-shipping' ),
				);
			}
		} else {
			$response = array(
				'success' => false,
				'message' => __( 'Authentication failed.', 'alopeyk-woocommerce-shipping' )
			);
		}
		
		return $response;

	}

	/**
	 * @since  1.7.0
	 * @return array
	 */
	public function get_endpoints_pack() {

		$endpoints_packs = Configs::ENDPOINTS;
		if ( defined('ALOPEYK_ENVIRONMENT') ) {
			$endpoints_packs[ALOPEYK_ENVIRONMENT] = ALOPEYK_ENVIRONMENT;
		}
		return $endpoints_packs;

	}

	/**
	 * @since  1.7.0
	 * @return array
	 */
	public function get_api_endpoint() {

		return AloPeykApiHandler::getEndpoint();

	}

	/**
	 * @since  1.7.0
	 * @return string or boolean
	 */
	public function is_api_user() {

		$userData = $this->get_user_data( null, null, true, [ 'with' => [ 'customer' ] ], false );
		if ( isset( $userData->customer->is_api ) && $userData->customer->is_api ) {
			return true;
		}
		return __( 'Contact <a href="https://alopeyk.com/api#section-form" target="_blank">Alopeyk</a> to become an API user and unlock the premium features for free.', 'alopeyk-woocommerce-shipping' );

	}
	/**
	 * @since  1.7.0
	 * generate link to alopeyk url
	 * @return string
	 */
	public function get_logo_url() {

		return plugins_url( 'admin/img/logo.png', dirname( __FILE__ ) );

	}
}

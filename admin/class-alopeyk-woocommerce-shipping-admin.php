<?php

/**
 * The admin-specific functionality of the plugin.
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

if ( class_exists( 'Alopeyk_WooCommerce_Shipping_Admin' ) ) {
	return;
}

/**
 * @since 1.0.0
 */
class Alopeyk_WooCommerce_Shipping_Admin {

	private $plugin_name;

	private $version;

	private $helpers;

	/**
	 * @since 1.0.0
	 * @param string $plugin_name
	 * @param string $version
	 */
	public function __construct( $plugin_name = null, $version = null ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->set_helpers();
		$this->set_schedule_event();

	}

	/**
	 * @since 1.0.0
	 */
	public function set_helpers() {

		$this->helpers = new Alopeyk_WooCommerce_Shipping_Common();

	}

	/**
	 * set scheduled event such as check all active orders status
	 * @since 1.7.0
	 */
	public function set_schedule_event() {

		$schedule_name = METHOD_ID . '_active_orders_update';
		if ( ! wp_next_scheduled( $schedule_name ) ) {
			wp_schedule_event( time(), 'hourly', $schedule_name );
		}

	}

	/**
	 * @since  1.0.0
	 * @return boolean
	 */
	public function is_order_edit() {

        // wordpress 5 -> shop_order
        // wordpress 6 -> woocommerce_page_wc
		$screen = get_current_screen();
		return in_array($screen->id, ['woocommerce_page_wc-orders', 'shop_order']);

	}

	/**
	 * @since 1.0.0
	 */
	public function enqueue_styles( $hook_suffix ) {

		$screen = get_current_screen();
		if ( in_array( $screen->id, array( 'edit-shop_order', 'edit-alopeyk_order', 'alopeyk_order', 'shop_order','woocommerce_page_wc-orders' ) ) || strpos( $screen->id, '_page_alopeyk-credit' ) ) {
			wp_deregister_style( 'jquery-ui-style' );
			wp_enqueue_style( 'wp-jquery-ui-dialog' );
		}
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/alopeyk-woocommerce-shipping-admin' . ( WP_DEBUG ? '' : '.min' ) . '.css', array(), $this->version, 'all' );
		if ( $this->is_order_edit() ) {
			wp_enqueue_style( $this->plugin_name . '__front', plugins_url( 'public/css/alopeyk-woocommerce-shipping-public' . ( WP_DEBUG ? '' : '.min' ) . '.css', dirname( __FILE__ ) ), array(), $this->version, 'all' );
		}
		if ( strpos( $hook_suffix , 'alopeyk-support' ) !== false ) {
			wp_enqueue_style( $this->plugin_name . '__help_desk', plugin_dir_url( __FILE__ ) . 'css/alopeyk-woocommerce-shipping-admin-help-desk' . ( WP_DEBUG ? '' : '.min' ) . '.css', array(), $this->version, 'all' );
		}

	}

	/**
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		
		wp_enqueue_media();
		wp_enqueue_script( 'jquery-ui-dialog' );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/alopeyk-woocommerce-shipping-admin' . ( WP_DEBUG ? '' : '.min' ) . '.js', array( 'jquery' ), $this->version, false );
		if ( $this->is_order_edit() ) {
			wp_enqueue_script( $this->plugin_name . '__front', plugins_url( 'public/js/alopeyk-woocommerce-shipping-public' . ( WP_DEBUG ? '' : '.min' ) . '.js', dirname( __FILE__ ) ), array(), $this->version, 'all' );
		}

	}

	/**
	 * @since  1.0.0
	 * @return boolean
	 */
	public function get_wc_settings_url() {

		return version_compare( WC()->version, '2.1', '>=' ) ? 'wc-settings' : 'woocommerce_settings';

	}

	/**
	 * @since  1.0.0
	 * @return boolean
	 */
	public function supports_plugin_meta() {

		global $wp_version;
		return version_compare( $wp_version, '2.8alpha', '>' );

	}

	/**
	 * @since  1.0.0
	 * @param  array  $links
	 * @param  string $file
	 * @return array
	 */
	public function meta_links( $links, $file ) {

		if ( $file == PLUGIN_BASENAME )
		{
			$plugin_links = array(
				'<a href="' . $this->helpers->get_support_url() . '">' . __( 'Support', 'alopeyk-woocommerce-shipping' ) . '</a>'
			);
			$links = array_merge( $links, $plugin_links );
		}
		return $links;

	}

	/**
	 * @since  1.0.0
	 * @param  array $links
	 * @return array
	 */
	public function action_links( $links ) {

		$plugin_links[] = '<a href="' . $this->get_settings_url() . '">' . __( 'Settings', 'alopeyk-woocommerce-shipping' ) . '</a>';

		if ( ! $this->supports_plugin_meta() ) {
			$meta_links = array(
				'<a href="' . $this->helpers->get_support_url() . '">' . __( 'Support', 'alopeyk-woocommerce-shipping' ) . '</a>'
			);
			$plugin_links = array_merge( $plugin_links, $meta_links );
		}
		return array_merge( $plugin_links, $links );

	}

	/**
	 * @since  1.0.0
	 * @return string
	 */
	public function get_settings_url() {

		return admin_url( 'admin.php?page=' . $this->get_wc_settings_url() . '&tab=' . METHOD_ID );

	}

	/**
	 * @since  1.7.0
	 * @return string
	 */
	public function get_orders_list_url( $post_status = false ) {

		$sub_ids = array( 'post_type' => 'alopeyk_order' );
		if ( $post_status ) {
			$sub_ids['post_status'] = $post_status;
		}
		$sub_ids = http_build_query( $sub_ids );
		return admin_url( 'edit.php?' . $sub_ids );

	}

	/**
	 * @since  1.0.0
	 * @param  array $fields
	 * @return array
	 */
	public function add_address_fields( $fields ) {

		$extra_fields = array(
			'address_latitude' => array(
				'label'         => null,
				'show'          => false,
				'type'          => 'hidden',
				'wrapper_class' => 'awcshm-hidden',
			),
			'address_longitude' => array(
				'label'         => null,
				'show'          => false,
				'type'          => 'hidden',
				'wrapper_class' => 'awcshm-hidden',
			),
			'address_location_city' => array(
				'label'         => null,
				'show'          => false,
				'type'          => 'hidden',
				'wrapper_class' => 'awcshm-hidden',
			),
			'address_location' => array(
				'label'             => null,
				'show'              => false,
				'type'              => 'hidden',
				'wrapper_class'     => 'form-field-wide',
				'custom_attributes' => array(
					'data-autocomplete-placeholder' => __( 'Please enter your address ...', 'alopeyk-woocommerce-shipping' )
				)
			),
			'address_unit' => array(
				'label' => __( 'Unit', 'alopeyk-woocommerce-shipping' ),
				'show'  => false,
			),
			'address_number' => array(
				'label' => __( 'Plaque', 'alopeyk-woocommerce-shipping' ),
				'show'  => false,
			),
		);
		return array_merge( $fields, $extra_fields );

	}

	/**
	 * @since 1.0.0
	 */
	public function add_address_description_field() {

		if ( $this->helpers->is_enabled() ) {
            $order = wc_get_order();
            $description = $order->get_meta('_shipping_address_description');
			echo '<p id="_shipping_address_description_field" class="form-field form-field-wide">' .
					'<label for="_shipping_address_description"><strong>' . __( 'Address Description', 'alopeyk-woocommerce-shipping' ) . '</strong></label>' .
					'<span class="awcshm-meta">' . __( 'This will be shown on courier device if order is being sent via Alopeyk shipping method and usually consists of order value, address details or any other sort of data needed for courier to know.', 'alopeyk-woocommerce-shipping' ) . '</span>' .
					'<textarea id="_shipping_address_description" name="_shipping_address_description" rows="3">' . $description . '</textarea>' .
				'</p>';
		}

	}

	/**
	 * @since 1.0.0
	 * @param integer $post_id
	 */
	public function save_address_description_field( $post_id ) {

		$shipping_address_description_field = '_shipping_address_description';
		if ( in_array(get_post_type( $post_id ), ['woocommerce_page_wc-orders', 'shop_order']) && isset( $_POST[$shipping_address_description_field] ) ) {
			update_post_meta( $post_id, $shipping_address_description_field, $_POST[$shipping_address_description_field] );
		}

	}

	/**
	 * @since 1.0.0
	 */
	public function check_address_fields() {

		// Check new added address fields if needed while order is being saved in admin panel

	}

	/**
	 * @since 1.0.0
	 */
	public function handle_meta_boxes() {

		global $post;
        $common = $this->helpers;

        if (!$common->is_enabled()) {
            return;
        }

        $screen = get_current_screen();
		$screen = $screen->id;

        if ($post and $post->post_type == $common::$order_post_type_name ) {
            $this->check_and_update_active_order_status($post);

            remove_meta_box( 'submitdiv', $screen, 'side' );
            $order_data = get_post_meta( $post->ID, '_awcshm_order_data', true );

            if (!$order_data) {
                add_action( 'admin_notices', function () {
                    echo '<div class="notice error"><p>' . __( 'Order details not found.', 'alopeyk-woocommerce-shipping' ) . '</p></div>';
                });
                return;
            }

            $order_data->transport_type_name = $this->helpers->get_transport_type_name( $order_data->transport_type );
            add_meta_box( METHOD_ID . '-order-courier-actions', __( 'Courier Information', 'alopeyk-woocommerce-shipping' ), function () use ( $order_data ) {
                $courier_info = isset( $order_data->courier_info ) && $order_data->courier_info ? $order_data->courier_info : null;
                if ( $courier_info ) {
                    $content = get_local_template_part( 'alopeyk-woocommerce-shipping-admin-meta-courier-info', array ( 'order_data' => $order_data ) );
                } else {
                    $content = '<ul><li class="wide awcshm-meta-box-content-container">' . __( 'No courier assigned to this order yet.', 'alopeyk-woocommerce-shipping' ) . '</li></ul>';
                }
                echo $content;
            }, $screen, 'side' );

            add_meta_box( METHOD_ID . '-order-info-actions', __( 'Order Information', 'alopeyk-woocommerce-shipping' ), function () use ( $order_data ) {
                echo get_local_template_part( 'alopeyk-woocommerce-shipping-admin-meta-order-info', array ( 'order_data' => $order_data ) );
            }, $screen, 'side' );

            add_meta_box( METHOD_ID . '-order-shipping-actions', __( 'Shipping Details', 'alopeyk-woocommerce-shipping' ), function () use ( $order_data ) {
                echo get_local_template_part( 'alopeyk-woocommerce-shipping-admin-meta-shipping-info', array ( 'order_data' => $order_data ) );
            }, $screen, 'normal', 'high' );
        } else {
            $order = wc_get_order();

            if (!$order) {
                return;
            }

            $last_status = $common->get_order_history( $order->get_id(), array( 'posts_per_page' => 1 ) );
            add_meta_box( METHOD_ID . '-wcorder-actions', __( 'Ship via Alopeyk', 'alopeyk-woocommerce-shipping' ), function () use ( $last_status ) {
                echo get_local_template_part( 'alopeyk-woocommerce-shipping-admin-meta-wcorder', array ( 'last_status' => $last_status ? $last_status[0] : null ) );
			}, $screen, 'side' );

            $args = array();
            $requestOrder = isset( $_GET['order'] ) ? $_GET['order'] : null;
            if ( $requestOrder ) {
                $args['order'] = $requestOrder;
            }

            $orderby = isset( $_GET['orderby'] ) ? $_GET['orderby'] : null;
            if ( $orderby ) {
                if ( in_array( $orderby, array( 'date', 'title' ) ) ) {
                    $args['orderby'] = $orderby;
                } else if ( $orderby == 'customer' ) {
                    $args['orderby'] = 'meta_value_num';
                    $args['meta_key'] = '_awcshm_user_id';
                } else if ( $orderby == 'wc_order' ) {
                    $args['orderby'] = 'meta_value_num';
                    $args['meta_key'] = '_awcshm_wc_order_id';
                } else {
                    $args['orderby'] = 'meta_value_num';
                    $args['meta_key'] = '_awcshm_' . $orderby;
                }
            }

            $last_status = $common->get_order_history( $order->get_id(), $args );
            add_meta_box( METHOD_ID . '-wcorder-history', __( 'Alopeyk Orders History', 'alopeyk-woocommerce-shipping' ), function () use ( $last_status ) {
                echo get_local_template_part( 'alopeyk-woocommerce-shipping-admin-order-history', array ( 'history' => $last_status ) );
            }, $screen, 'normal', 'high' );
        }
	}

	/**
	 * @since 1.0.0
	 */
	public function admin_menu_items() {

		add_menu_page( __( 'Alopeyk', 'alopeyk-woocommerce-shipping' ), __( 'Alopeyk', 'alopeyk-woocommerce-shipping' ), 'manage_options', 'alopeyk', null, plugins_url( 'admin/img/icon.svg', dirname( __FILE__ ) ), '55.7' );
		add_submenu_page( 'alopeyk', __( 'Profile', 'alopeyk-woocommerce-shipping' ), __( 'Profile', 'alopeyk-woocommerce-shipping' ), 'manage_options', 'alopeyk-credit', function () {
			if ( $user_data = $this->helpers->get_user_data( null, null, true, [ 'with' => [ 'credit', 'score' ] ] ) ) {
				$endpoint = $this->helpers->get_api_endpoint();
				echo get_local_template_part( 'alopeyk-woocommerce-shipping-admin-credit-page', array( 
							'user_data'   => $user_data,
							'user_credit' => $this->helpers->normalize_price( $this->helpers->get_user_data( 'credit' ) * 10 ),
							'api_url'     => $endpoint['url'],
				) );
			} else {
				echo '<div class="error notice awcshm-credit-widget-wrapper"><p>' . sprintf( __( 'User data not found. You have to enter a valid API key in <a href="%s" target="blank">settings page</a> in order to access this page.', 'alopeyk-woocommerce-shipping' ), $this->get_settings_url() ) . '</p></div>';
			}
		});
		add_submenu_page( 'alopeyk', __( 'Settings', 'alopeyk-woocommerce-shipping' ), __( 'Settings', 'alopeyk-woocommerce-shipping' ), 'manage_options', 'alopeyk-shipping-settings', function () {
			wp_redirect( $this->get_settings_url(), 301 );
			exit;
		});
		add_submenu_page( 'alopeyk', __( 'Support', 'alopeyk-woocommerce-shipping' ), __( 'Support', 'alopeyk-woocommerce-shipping' ), 'manage_options', 'alopeyk-support', function () {
			echo get_local_template_part( 'alopeyk-woocommerce-shipping-admin-support-page', array(
				'log_url'     => $this->helpers->get_log_url(),
				'chat_url'    => $this->helpers->get_chat_url(),
				'dev_email'   => $this->helpers->get_config( 'devEmail' ),
				'support_tel' => $this->helpers->get_support_tel(),
				'is_api_user' => $this->helpers->is_api_user(),
			));
		});

	}

	/**
	 * @since  1.0.0
	 * @param  string $content
	 * @return string
	 */
	public function remove_footer_content( $content ) {

		$screen = get_current_screen();
		return strpos( $screen->id, '_page_alopeyk-support' ) ? '' : $content;

	}

	/**
	 * @since 1.0.0
	 */
	public function remove_admin_notices() {

		$screen = get_current_screen();
		if ( isset( $screen->id ) && strpos( $screen->id, '_page_alopeyk-support' ) ) {
			remove_all_actions( 'admin_notices' );
		}

	}

	/**
	 * @since  1.0.0
	 * @param  array $columns
	 * @return array
	 */
	public function add_columns( $columns = array() ) {

		unset( $columns[ 'date' ] );
		unset( $columns['title'] );
		$columns['post_status']   = __( '', 'alopeyk-woocommerce-shipping' );
		$columns['order_title']   = __( 'Order', 'alopeyk-woocommerce-shipping' );
		$columns['order_type']    = __( 'Type', 'alopeyk-woocommerce-shipping' );
		$columns['wc_order']      = __( 'Shop Order(s)', 'alopeyk-woocommerce-shipping' );
		$columns['customer']      = __( 'Customer(s)', 'alopeyk-woocommerce-shipping' );
		$columns['order_price']   = __( 'Cost', 'alopeyk-woocommerce-shipping' );
		$columns['order_date']    = __( 'Date', 'alopeyk-woocommerce-shipping' );
		$columns['order_actions'] = __( 'Actions', 'alopeyk-woocommerce-shipping' );
		return $columns;

	}

	/**
	 * @since  1.0.0
	 * @param  array $columns
	 * @return array
	 */
	public function add_sortable_columns( $columns = array() ) {

		$columns['post_status']   = array( 'order_status', false );
		$columns['order_title']   = array( 'title', false );
		$columns['order_type']    = array( 'order_type', false );
		$columns['customer']      = array( 'customer', false );
		$columns['wc_order']      = array( 'wc_order', false );
		$columns['order_price']   = array( 'order_price', false );
		$columns['order_date']    = array( 'date', false );
		return $columns;

	}

	/**
	 * @since 1.0.0
	 * @param string  $column
	 * @param integer $post_id
	 */
	public function set_custom_column_content( $column, $post_id ) {

		$post = get_post( $post_id );
        if ($this->check_and_update_active_order_status($post)) {
            $post = get_post( $post_id );
        }

        $post_status = $post->post_status;
        $post_type = $post->post_type;
		$post_status_label = $this->helpers->get_order_status_label( $post_status );
		$alopeyk_order_id = get_post_meta( $post_id, '_awcshm_order_id' );
		switch ( $column ) {
			case 'post_status' :
				echo '<a href="' . admin_url( 'edit.php?post_status=' . $post_status . '&post_type=' . $post_type ) . '" class="awcshm-tooltip awcshm-status type--' . $post_status . '"><i class="awcshm-status-icon"></i><span class="awcshm-tooltip-label awcshm-status-label">' . $post_status_label . '</span></a>';
			break;
			case 'order_title' :
				echo '<a href="' . admin_url( 'post.php?post=' . $post_id . '&action=edit' ) . '" class="row-title">#' . $post->post_title . '</a><small class="awcshm-meta">' . __( 'ID', 'alopeyk-woocommerce-shipping' ) . ': ' . ( $alopeyk_order_id ? $alopeyk_order_id[0] : '—' ) . '</small>';
			break;
			case 'order_type' :
				$type = get_post_meta( $post_id, '_awcshm_order_type' );
				$type_label = $type ? $this->helpers->get_transport_type_name( $type[0] ) : null;
				echo $type_label ? '<a href="' . admin_url( 'edit.php?post_type=alopeyk_order&transport_type=' . $type[0] ) . '">' . $type_label . '</a>' : '—';
			break;
			case 'customer' :
				$user_ids = get_post_meta( $post_id, '_awcshm_user_id' );
				if ( $user_ids && count( $user_ids ) ) {
					$user_output = array();
					foreach ( $user_ids as $user_id ) {
						if ( $user_id ) {
							$user_data = get_userdata( $user_id );
							if ( $user_data ) {
								$user_output[] = '<a href="' . admin_url( 'user-edit.php?user_id=' . $user_id ) . '" target="_blank">' . $user_data->first_name . ' ' . $user_data->last_name . '</a>';
							}
						}
					}
					echo implode( __( ',', 'alopeyk-woocommerce-shipping' ) . ' ', array_unique( $user_output ) );
				} else {
					echo '—';
				}
			break;
			case 'wc_order' :
				$order_ids = get_post_meta( $post_id, '_awcshm_wc_order_id' );
				if ( $order_ids && count( $order_ids ) ) {
					$order_output = array();
					foreach ( $order_ids as $order_id ) {
						$order_output[] = '<a href="' . admin_url( 'post.php?post=' . $order_id ) . '&action=edit" target="_blank">#' . $order_id . '</a>';
					}
					echo implode( __( ',', 'alopeyk-woocommerce-shipping' ) . ' ', $order_output );
				} else {
					echo '—';
				}
			break;
			case 'order_price' :
				$price = get_post_meta( $post_id, '_awcshm_order_price', true );
				echo $price ? wc_price( $this->helpers->normalize_price( $price ) ) : '—';
			break;
			case 'order_date' :
				$timezone = get_option( 'timezone_string' );
				if ( $timezone && ! empty( $timezone ) ) {
					date_default_timezone_set( $timezone );
				}
				$datetime = new DateTime( get_post_time( 'Y-m-d H:i:s', false, $post_id ) );
				$datetime->setTimezone( new DateTimeZone( 'Asia/Tehran' ) );
				$post_date = strtotime( $datetime->format( 'Y-m-d H:i:s' ) );
				echo date_i18n( 'j F Y', $post_date ) . '<br>' . date_i18n( 'g:i A', $post_date );
			break;
			case 'order_actions' :
				$order_data = get_post_meta( $post_id, '_awcshm_order_data', true );
				echo '<a class="awcshm-tooltip button awcshm-icon-button" href="' . get_edit_post_link( $post_id ) . '"><i class="awcshm-icon-button-icon dashicons dashicons-visibility"></i><span class="awcshm-tooltip-label">' . __( 'View', 'alopeyk-woocommerce-shipping' ) . '</span></a>';
				if ( $this->helpers->can_be_tracked( $order_data ) ) {
					echo '<a class="awcshm-tooltip button awcshm-icon-button" target="_blank" href="' . $this->helpers->get_tracking_url( $order_data ) . '"><i class="awcshm-icon-button-icon dashicons dashicons-welcome-view-site"></i><span class="awcshm-tooltip-label">' . __( 'Track', 'alopeyk-woocommerce-shipping' ) . '</span></a>';
				}
				if ( $this->helpers->can_be_invoiced( $order_data ) ) {
					echo '<a class="awcshm-tooltip button awcshm-icon-button" target="_blank" href="' . $this->helpers->get_invoice_url( $order_data ) . '"><i class="awcshm-icon-button-icon dashicons dashicons-format-aside"></i><span class="awcshm-tooltip-label">' . __( 'Invoice', 'alopeyk-woocommerce-shipping' ) . '</span></a>';
				}
				$cancel = $this->helpers->can_be_canceled( $order_data );
				if ( $cancel['enabled'] ) {
					echo '<a class="awcshm-tooltip button awcshm-icon-button awcshm-cancel-modal-toggler" data-order-id="' . $post_id . '" href="#"><i class="awcshm-icon-button-icon dashicons dashicons-no"></i><span class="awcshm-tooltip-label">' . __( 'Cancel Order', 'alopeyk-woocommerce-shipping' ) . '</span></a>';
				}
				if ( $this->helpers->can_be_rated( $order_data ) ) {
					echo '<a class="awcshm-tooltip button awcshm-icon-button awcshm-rate-modal-toggler" data-order-id="' . $post_id . '" href="#"><i class="awcshm-icon-button-icon dashicons dashicons-star-filled"></i><span class="awcshm-tooltip-label">' . __( 'Rate', 'alopeyk-woocommerce-shipping' ) . '</span></a>';
				}
				$order_ids = get_post_meta( $post_id, '_awcshm_wc_order_id' );
				if ( $this->helpers->can_be_repeated( $order_data ) && $order_ids && count( $order_ids ) ) {
					echo '<a class="awcshm-tooltip button awcshm-icon-button awcshm-order-modal-toggler" data-order-ids="' . implode( ',', $order_ids ) . '" data-order-types="' . $order_data->transport_type . '" href="#"><i class="awcshm-icon-button-icon dashicons dashicons-image-rotate"></i><span class="awcshm-tooltip-label">' . __( 'Ship Again', 'alopeyk-woocommerce-shipping' ) . '</span></a>';
				}
			break;
		}

	}

	/**
	 * @since 1.0.0
	 * @param string $current_post_type
	 */
	public function add_orders_filter( $current_post_type ) {

		$common = $this->helpers;
		$post_type = $common::$order_post_type_name;
		if ( $post_type == $current_post_type ) {
			foreach ( $this->helpers->get_transport_types() as $key => $transport_type ) {
				$types[$key] = $transport_type['label'];
			}
			?>
			<select name="transport_type">
				<option value=""><?php echo __( 'Transport Type', 'alopeyk-woocommerce-shipping' ); ?>&nbsp;</option>
			<?php
				$selected = isset( $_GET['transport_type'] ) ? $_GET['transport_type'] : '';
				foreach ( $types as $type => $label ) {
					printf( '<option value="%s"%s>%s</option>', $type, $type == $selected ? ' selected="selected"' : '', $label );
				}
		}

	}

	/**
	 * @since  1.0.0
	 * @param  object $query
	 * @return object
	 */
	public function orders_filter( $query ) {

		global $pagenow;
		$common = $this->helpers;
		$post_type = $common::$order_post_type_name;
		$query_vars = &$query->query_vars;
		if ( $pagenow == 'edit.php' && isset( $query_vars['post_type'] ) && $query_vars['post_type'] == $post_type && isset( $_GET['transport_type'] ) && ! empty ( $_GET['transport_type'] ) ) {
			$query->query_vars['meta_key'] = '_awcshm_order_type';
			$query->query_vars['meta_value'] = $_GET['transport_type'];
		}

	}

	/**
	 * @since  1.0.0
	 * @param  array  $actions
	 * @param  object $post
	 * @return array
	 */
	public function remove_useless_actions( $actions, $post ) {

		$common = $this->helpers;
		$post_type = $common::$order_post_type_name;
		if ( $post->post_type == $post_type ) {
			return array();
		}
		return $actions;

	}

	/**
	 * @since  1.0.0
	 * @param  array $actions
	 * @return array
	 */
	public function remove_useless_bulk_actions( $actions ) {

		$actions = array();
		return $actions;

	}

	/**
	 * @since  1.0.0
	 * @param  array $actions
	 * @return array
	 */
	public function add_cumulative_shipping( $actions ) {

		$actions['alopeyk_cumulative_shipping'] = __( 'Ship via Alopeyk', 'alopeyk-woocommerce-shipping' );
		return $actions;

	}

	/**
	 * @since  1.0.0
	 * @param  object $query
	 * @return object
	 */
	public function sort_meta_columns( $query ) {

		if ( ! is_admin() ){
            return;
        }

		$orderby = $query->get( 'orderby' );
		if ( 'order_type' == $orderby ) {  
			$query->set( 'meta_key','_awcshm_order_type' );  
			$query->set( 'orderby','meta_value_num' );
		}
		if ( 'customer' == $orderby ) {  
			$query->set( 'meta_key','_awcshm_user_id' );  
			$query->set( 'orderby','meta_value_num' );
		}
		if ( 'wc_order' == $orderby ) {  
			$query->set( 'meta_key','_awcshm_wc_order_id' );  
			$query->set( 'orderby','meta_value_num' );
		}
		if ( 'order_price' == $orderby ) {  
			$query->set( 'meta_key','_awcshm_order_price' );  
			$query->set( 'orderby','meta_value_num' );
		}

		return $query;

	}

	/**
	 * @since 1.0.0
	 * @param array $data
	 */
	public function ajax_create_order_modal( $data ) {

		if ( isset( $data['data'] ) && $data['data'] ) {
			$data = $data['data'];
			$data['all_transport_types'] = $this->helpers->get_transport_types();
			if ( $data && isset( $data['orders'] ) && count( $data['orders'] ) ) {
				$max_destination = $this->helpers->get_max_destination();
				if ( $max_destination >= count( $data['orders'] ) ) {
					$data['description'] = isset( $data['description'] ) ? $data['description'] : $this->helpers->get_option( 'store_description' );
					$this->helpers->respond_ajax( get_local_template_part( 'alopeyk-woocommerce-shipping-admin-order-create', $data ) );
				} else {
					$this->helpers->respond_ajax( sprintf( __( 'Maximum %s orders can be shipped at once.', 'alopeyk-woocommerce-shipping' ), $max_destination ), false );
				}
			}
		}
		$this->helpers->respond_ajax( __( 'No order selected for shipping.', 'alopeyk-woocommerce-shipping' ), false );

	}

	/**
	 * @since 1.0.0
	 * @param array $data
	 */
	public function ajax_check_order_modal( $data ) {

		$response = $this->check_order_data( $data );
		$success  = $response['success'];
		$message  = $response['message'];
		$package  = (array) $response['package'];
		if ( $success ) {
			$package['type_name'] = $this->helpers->get_transport_type_name( $package['type'] );
			$message = get_local_template_part( 'alopeyk-woocommerce-shipping-admin-order-review', $package );
		}
		$this->helpers->respond_ajax( $message, $success, $package );

	}

	/**
	 * @since 1.0.0
	 * @param array $data
	 */
	public function ajax_create_credit_modal( $data ) {

		$data = isset( $data['data'] ) && $data['data'] ? $data['data'] : array();
		$data = array_merge( $data, array(
			'action'  => $this->helpers->get_add_credit_url(),
			'amounts' => $this->helpers->get_default_credit_amounts()
		));
		$this->helpers->respond_ajax( get_local_template_part( 'alopeyk-woocommerce-shipping-admin-credit', $data ) );

	}

	/**
	 * @since 1.0.0
	 * @param array $data
	 */
	public function ajax_create_coupon_modal( $data ) {

		$data = isset( $data['data'] ) && $data['data'] ? $data['data'] : array();
		$this->helpers->respond_ajax( get_local_template_part( 'alopeyk-woocommerce-shipping-admin-coupon', $data ) );

	}

	/**
	 * @since 1.0.0
	 * @param array $data
	 */
	public function ajax_add_coupon_modal( $data ) {

		if ( isset( $data['data'] ) && $data['data'] ) {
			parse_str( $data['data'], $data );
			if ( $data && isset( $data['coupon_code'] ) ) {
				$response = $this->helpers->apply_coupon( $data['coupon_code'] );
				$success = $response['success'];
				$message = $response['message'];
				if ( $success ) {
					$message = '<div class="updated notice"><p>' . $message . '</p></div>';
				}
				$this->helpers->respond_ajax( $message, $success );
			}
		}
		$this->helpers->respond_ajax( __( 'Coupon data not found.', 'alopeyk-woocommerce-shipping' ), false );

	}

	/**
	 * @since 1.0.0
	 * @param array $data
	 */
	public function ajax_create_cancel_modal( $data ) {

		if ( isset( $data['data'] ) && $data['data'] ) {
			$data = $data['data'];
			if ( $data && isset( $data['order'] ) ) {
				$order_data = get_post_meta( $data['order'], '_awcshm_order_data', true );
				$can_be_canceled = $this->helpers->can_be_canceled( $order_data );
				if ( $can_be_canceled['enabled'] ) {
					$data = array_merge( $data, array(
						'cancel'     => $this->helpers->can_be_canceled( $order_data ),
						'reasons'    => $this->helpers->get_cancel_reasons(),
						'order_data' => $order_data,
					));
					$this->helpers->respond_ajax( get_local_template_part( 'alopeyk-woocommerce-shipping-admin-order-cancel', $data ) );
				} else {
					$this->helpers->respond_ajax( __( 'Order cannot be canceled.', 'alopeyk-woocommerce-shipping' ), false );
				}
			}
		}
		$this->helpers->respond_ajax( __( 'No order is selected to be canceled.', 'alopeyk-woocommerce-shipping' ), false );

	}

	/**
	 * @since 1.0.0
	 * @param array $data
	 */
	public function ajax_cancel_result_modal( $data ) {

		if ( isset( $data['data'] ) && $data['data'] ) {
			parse_str( $data['data'], $data );
			if ( $data && isset( $data['order'] ) ) {
				$order_id = get_post_meta( $data['order'], '_awcshm_order_id', true );
				$response = $this->helpers->cancel_order( $order_id, isset( $data['reason'] ) ? $data['reason'] : '', $data['order'] );
				$success = $response['success'];
				$message = $response['message'];
				if ( $success ) {
					$message = '<div class="updated notice"><p>' . $message . '</p></div>';
				}
				$this->helpers->respond_ajax( $message, $success );
			}
		}
		$this->helpers->respond_ajax( __( 'Cancelation data not found.', 'alopeyk-woocommerce-shipping' ), false );

	}

	/**
	 * @since 1.0.0
	 * @param array $data
	 */
	public function ajax_submit_order_modal( $data ) {

		if ( isset( $data['data'] ) && $data['data'] ) {
			$data = (object) $data['data'];
			$response = $this->helpers->create_order( $data );
			$data    = $response['data'];
			$success = $response['success'];
			$message = $response['message'];
			if ( $success ) {
				$message = '<div class="updated notice"><p>' . $message . '</p></div>';
				$is_api_user = $this->helpers->is_api_user();
				if ( $is_api_user !== true ) {
					$message .= '<br /><div class="error notice"><p>' . $is_api_user . '</p></div>';
				}
			}
			$this->helpers->respond_ajax( $message, $success, $data );
		}
		$this->helpers->respond_ajax( __( 'Order data not found.', 'alopeyk-woocommerce-shipping' ), false );
	}

	/**
	 * @since 1.0.0
	 * @param array $data
	 */
	public function ajax_create_rate_modal( $data ) {

		if ( isset( $data['data'] ) && $data['data'] ) {
			$data = $data['data'];
			if ( $data && isset( $data['order'] ) ) {
				$order_data = get_post_meta( $data['order'], '_awcshm_order_data', true );
				if ( $order_data->status == 'delivered' ) {
					$data = array_merge( $data, array(
						'reasons'    => $this->helpers->get_rating_reasons(),
						'order_data' => $order_data,
					));
					$this->helpers->respond_ajax( get_local_template_part( 'alopeyk-woocommerce-shipping-admin-order-rate', $data ) );
				} else {
					$this->helpers->respond_ajax( __( 'Courier can not be rated because it is already rated.', 'alopeyk-woocommerce-shipping' ), false );
				}
			}
		}
		$this->helpers->respond_ajax( __( 'Order data not found.', 'alopeyk-woocommerce-shipping' ), false );

	}

	/**
	 * @since 1.0.0
	 * @param array $data
	 */
	public function ajax_rate_result_modal( $data ) {

		if ( isset( $data['data'] ) && $data['data'] ) {
			parse_str( $data['data'], $data );
			if ( $data && isset( $data['order'] ) ) {
				$local_order_id = $data['order'];
				$order_id = $local_order_id ? get_post_meta( $local_order_id, '_awcshm_order_id', true ) : null;
				$reason = isset( $data['reason'] ) ? $data['reason'] : null;
				$comment = isset( $data['description'] ) ? $data['description'] : null;
				$rate = isset( $data['rate'] ) ? $data['rate'] : null;
				$response = $this->helpers->finish_order( $order_id, $rate, $reason, $comment, $local_order_id );
				$success = $response['success'];
				$message = $response['message'];
				if ( $success ) {
					$message = '<div class="updated notice"><p>' . $message . '</p></div>';
				}
				$this->helpers->respond_ajax( $message, $success );
			}
		}
		$this->helpers->respond_ajax( __( 'Order data not found.', 'alopeyk-woocommerce-shipping' ), false );

	}
	
	/**
	 * @since 1.7.0
	 * @param array $data
	 */
	public function ajax_discount_coupon_modal() {

		$this->helpers->respond_ajax( get_local_template_part( 'alopeyk-woocommerce-shipping-admin-discount-coupon' ) );

	}


	/**
	 * @since 1.7.0
	 */
	public function ajax_create_customer_score_exchange_modal() {

		$products = (object) $this->helpers->get_customer_loyalty_products();
		if ( $products->status ) {
			$credit_cards = array();
			foreach ( $products->object->items as $product ) {
				if ( $product->delivery_type == 'alopeyk_credit_card' ) {
					$credit_cards[] = $product;
				}
			}
			$this->helpers->respond_ajax( get_local_template_part( 'alopeyk-woocommerce-shipping-admin-customer-score-exchange', $credit_cards ) );
		} else {
			$this->helpers->respond_ajax( __( $products->message, 'alopeyk-woocommerce-shipping' ), false );
		}

	}

	/**
	 * @since 1.7.0
	 * @param array $data
	 */
	public function ajax_submit_customer_score_exchange_modal( $data ) {

		$card_data = (array) $this->helpers->get_customer_loyalty_products( $data['data'] );
		$this->helpers->respond_ajax( get_local_template_part( 'alopeyk-woocommerce-shipping-admin-customer-score-exchange-submit', $card_data ) );

	}

	/**
	 * @since 1.7.0
	 * @param array $data
	 */
	public function ajax_add_customer_score_exchange_modal( $data ) {

		if ( isset( $data['data'] ) && $data['data'] ) {
			parse_str( $data['data'], $data );
			if ( $data ) {
				$product_id = isset( $data['product-id'] ) ? $data['product-id'] : '';
				if ( is_numeric( $product_id ) ) {
					$response = $this->helpers->get_customer_loyalty_products( $product_id, true );
					$success  = $response['success'];
					$message  = $response['message'];
					if ( $success ) {
						$this->helpers->respond_ajax( $message, true );
					} else {
						$this->helpers->respond_ajax( __( $message, 'alopeyk-woocommerce-shipping' ), false );
					}
				} else {
					$this->helpers->respond_ajax( __( 'An error occurred while loading card data.', 'alopeyk-woocommerce-shipping' ), false );
				}
			}
		}

	}

	/**
	 * @since 1.7.0
	 * @param array $data
	 */
	public function ajax_discount_coupon_submit_modal( $data ) {

		$response = $this->check_order_data( $data );
		$package  = (array) $response['package'];
		if ( isset( $package['shipping']->discount ) && $package['shipping']->discount >= 0 ) {
			$this->helpers->respond_ajax( '', true );
		} elseif ( isset( $package['shipping']->discount_coupons_error_msg ) ) {
			$this->helpers->respond_ajax( $package['shipping']->discount_coupons_error_msg, false );
		} else {
			$this->helpers->respond_ajax( __( 'An error occurred while trying to apply entered discount coupon.', 'alopeyk-woocommerce-shipping' ), false );
		}

	}

	/**
	 * @since 1.7.0
	 * @param array $data
	 */
	public function ajax_get_address( $data ) {

		$data            = (object) $data;
		$lat             = $data->lat;
		$lng             = $data->lng;
		$location        = $this->helpers->get_location( $lat, $lng );
		$address         = $this->helpers->get_address( $location, true );
		if ( $address ) {
			$this->helpers->respond_ajax( $address );
		} else {
			$this->helpers->respond_ajax( array(
				'city'    => null,
				'address' => __( 'This address is out of service.', 'alopeyk-woocommerce-shipping' )
			), false );
		}

	}

	/**
	 * @since 1.7.0
	 * @param array $data
	 */
	public function ajax_suggest_address( $data ) {

		$data   = (object) $data;
		$input  = $data->input;
		$latlng = '';
		$lat = isset( $data->lat ) ? $data->lat : null;
		$lng = isset( $data->lng ) ? $data->lng : null;
		$latlng = $lat && $lng ? ( $lat . ',' . $lng ) : '';
		$addresses = $this->helpers->suggest_address( $input, $latlng, true );
		if ( $addresses ) {
			$this->helpers->respond_ajax( $addresses );
		} else {
			$this->helpers->respond_ajax( __( 'No address found.', 'alopeyk-woocommerce-shipping' ), false );
		}

	}

	/**
	 * @since 1.7.0
	 * @param array $data
	 */
	public function check_order_data( $data ) {

		if ( isset( $data['data'] ) && $data['data'] ) {
			parse_str( $data['data'], $data );
			if ( $data ) {
				$orders       = isset( $data['orders'] )      ? $data['orders']        : array();
                $type         = isset( $data['type'] )        ? $data['type']          : null;
				$description  = isset( $data['description'] ) ? $data['description']   : null;
				$ship_now     = isset( $data['ship_now'] )    ? $data['ship_now']      : null;
				$ship_date    = isset( $data['ship_date'] )   ? $data['ship_date']     : null;
				$ship_hour    = isset( $data['ship_hour'] )   ? $data['ship_hour']     : null;
				$ship_minute  = isset( $data['ship_minute'] ) ? $data['ship_minute']   : null;
				$scheduled_at = ( $ship_date && $ship_hour && $ship_minute && $ship_now !== 'true' ) ? ( $ship_date . ' ' . $ship_hour . ':' . $ship_minute . ':00' ) : null;
				$discount_coupon = isset( $data['discount_coupon'] ) ? $data['discount_coupon'] : null;
				return $this->helpers->check_order( $orders, $type, $scheduled_at, $description, $discount_coupon );
			}
		}
		$this->helpers->respond_ajax( __( 'Order data not found.', 'alopeyk-woocommerce-shipping' ), false );

	}

	/**
	 * @since 1.7.0
	 * @param array $data
	 */
	public function dashboard_widget() {

		$wrong_key = $this->helpers->get_option( 'wrong_key', true );
		$wrong_key_state = $wrong_key == 'yes';
		if ( ! $this->helpers->is_enabled() && $wrong_key_state ) {
			$dashboard_widget_callback = 'dashboard_widget_enter_api';
		} elseif ( ! $this->helpers->is_enabled() && ! $wrong_key_state ) {
			$dashboard_widget_callback = 'dashboard_widget_enable_awcshm';
		} else {
			$dashboard_widget_callback = 'dashboard_widget_summary';
		}
		wp_add_dashboard_widget( 'awcshm_admin_widget', __( 'Alopeyk Woocommerce Shipping', 'alopeyk-woocommerce-shipping' ), array( $this, $dashboard_widget_callback ) );
		global $wp_meta_boxes;
	 	$normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
	 	$widget_backup    = array( 'awcshm_admin_widget' => $normal_dashboard['awcshm_admin_widget'] );
	 	$sorted_dashboard = array_merge( $widget_backup, $normal_dashboard );
	 	$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;

	}

	public function dashboard_widget_enter_api() {
		?>
		<div class="awcshm-dashboard-widget" >
			<p><img class="awcshm-dashboard-widget-logo" src="<?php echo $this->helpers->get_logo_url(); ?>"></p>
			<p><?php echo __( 'In order to active Alopeyk shipping method, enter Alopeyk API Key in the woocommerce settings.', 'alopeyk-woocommerce-shipping' ); ?></p>
			<p>&nbsp;</p>
			<p><a href="<?php echo esc_url( $this->get_settings_url() ); ?>" class="button button-primary" title="<?php echo __( 'Enter API key', 'alopeyk-woocommerce-shipping' ); ?>"><?php echo __( 'Enter API key', 'alopeyk-woocommerce-shipping' ); ?></a></p>
			<p><a href="<?php echo $this->helpers->get_support_url();?>" title="<?php echo __( 'I have no API key', 'alopeyk-woocommerce-shipping' ); ?>"><u><?php echo __( 'I have no API key', 'alopeyk-woocommerce-shipping' ); ?></u></a></p>
		</div>
		<?php
	}

	public function dashboard_widget_enable_awcshm() {
		?>
		<div class="awcshm-dashboard-widget">
			<p><img class="awcshm-dashboard-widget-logo" src="<?php echo $this->helpers->get_logo_url(); ?>"></p>
			<p><?php echo __( 'Alopeyk shipping method is not activated for your store. You can activate it by enabling the method via Settings page.', 'alopeyk-woocommerce-shipping' ); ?></p>
			<p>&nbsp;</p>
			<p><a href="<?php echo esc_url( $this->get_settings_url() ); ?>" class="button button-primary" title="<?php echo __( 'Settings', 'alopeyk-woocommerce-shipping' ); ?>"><?php echo __( 'Settings', 'alopeyk-woocommerce-shipping' ); ?></a></p>
			<p><a href="<?php echo $this->helpers->get_support_url();?>" title="<?php echo __( 'I have problem with my API key', 'alopeyk-woocommerce-shipping' ); ?>"><u><?php echo __( 'I have problem with my API key', 'alopeyk-woocommerce-shipping' ); ?></u></a></p>
		</div>
		<?php
	}

	public function dashboard_widget_summary() {
		
		$common      = $this->helpers;
		$post_type   = $common::$order_post_type_name;
		$orders_list = wp_count_posts( $post_type );
		$scheduled   = $orders_list->{'awcshm-scheduled'};
		$progress    = $orders_list->{'awcshm-progress' };
		$pending     = $orders_list->{'awcshm-pending'  };
		$failed      = $orders_list->{'awcshm-failed'   };
		$done        = $orders_list->{'awcshm-done'     };
		$total       = $scheduled + $progress + $pending + $failed + $done;
		?>
		<div id="awcshm_dashboard_status">
			<ul class="wc_status_list">
				<li class="all-orders">
					<a href="<?php echo $this->get_orders_list_url(); ?>">
						<strong>
							<?php echo $total; ?> <?php echo __( 'Order', 'alopeyk-woocommerce-shipping' )?>
						</strong><?php echo __( 'Order Created', 'alopeyk-woocommerce-shipping' )?>
					</a>
				</li>
				<li class="failed-orders">
					<a href="<?php echo $this->get_orders_list_url( 'awcshm-failed' ); ?>">
						<strong>
							<?php echo $failed; ?> <?php echo __( 'Order', 'alopeyk-woocommerce-shipping' )?>
						</strong><?php echo __( 'unsuccessful sending', 'alopeyk-woocommerce-shipping' )?>
					</a>
				</li>
				<li class="progress-orders">
					<a href="<?php echo $this->get_orders_list_url( 'awcshm-progress' ); ?>">
						<strong>
							<?php echo $progress; ?> <?php echo __( 'Order', 'alopeyk-woocommerce-shipping' )?>
						</strong><?php echo __( 'sending', 'alopeyk-woocommerce-shipping' )?>
					</a>
				</li>
				<li class="done-orders">
					<a href="<?php echo $this->get_orders_list_url( 'awcshm-done' ); ?>">
						<strong><?php echo $done; ?>
							<?php echo __( 'Order', 'alopeyk-woocommerce-shipping' )?>
						</strong><?php echo __( 'delivered', 'alopeyk-woocommerce-shipping' )?>
					</a>
				</li>
				<li class="pending-orders">
					<a href="<?php echo $this->get_orders_list_url( 'awcshm-pending' ); ?>">
						<strong>
							<?php echo $pending; ?> <?php echo __( 'Order', 'alopeyk-woocommerce-shipping' )?>
						</strong><?php echo __( 'finding', 'alopeyk-woocommerce-shipping' )?>
					</a>
				</li>
				<li class="scheduled-orders">
					<a href="<?php echo $this->get_orders_list_url( 'awcshm-scheduled' ); ?>">
						<strong>
							<?php echo $scheduled; ?> <?php echo __( 'Order', 'alopeyk-woocommerce-shipping' )?>
						</strong><?php echo __( 'scheduled for sending', 'alopeyk-woocommerce-shipping' )?>
					</a>
				</li>
		<?php
				$is_api_user = $this->helpers->is_api_user();
				if ( $is_api_user !== true ) {
					echo '<li class="check-api-fault"><p>' . $is_api_user . '</p></li>';
				}
		?>
			</ul>
		</div>
		<?php
	}

	public function awcshm_admin_notice() {
		$wrong_key = $this->helpers->get_option( 'wrong_key', true );
		$class = 'notice notice-error is-dismissible';

        if($wrong_key == 'yes') {
            $message = __('The API key is not valid.', 'alopeyk-woocommerce-shipping');
            printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
        }
	}

    public function check_and_update_active_order_status($alopeyk_order){
        if (in_array($alopeyk_order->post_status, array( 'awcshm-progress', 'awcshm-pending', 'awcshm-scheduled' ))) {
            $this->helpers->update_active_order( $alopeyk_order->ID );
            return true;
        }

        return false;
    }
}

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

		$schedule_name = ALOPEYK_METHOD_ID . '_active_orders_update';
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

		if ( $file == ALOPEYK_PLUGIN_BASENAME )
		{
			$plugin_links = array(
				'<a href="' . $this->helpers->get_support_url() . '">' . esc_html__( 'Support', 'alopeyk-shipping-for-woocommerce' ) . '</a>'
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

		$plugin_links[] = '<a href="' . $this->get_settings_url() . '">' . esc_html__( 'Settings', 'alopeyk-shipping-for-woocommerce' ) . '</a>';

		if ( ! $this->supports_plugin_meta() ) {
			$meta_links = array(
				'<a href="' . $this->helpers->get_support_url() . '">' . esc_html__( 'Support', 'alopeyk-shipping-for-woocommerce' ) . '</a>'
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

		return admin_url( 'admin.php?page=' . $this->get_wc_settings_url() . '&tab=' . ALOPEYK_METHOD_ID );

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
					'data-autocomplete-placeholder' => esc_html__( 'Please enter your address ...', 'alopeyk-shipping-for-woocommerce' )
				)
			),
			'address_unit' => array(
				'label' => esc_html__( 'Unit', 'alopeyk-shipping-for-woocommerce' ),
				'show'  => false,
			),
			'address_number' => array(
				'label' => esc_html__( 'Plaque', 'alopeyk-shipping-for-woocommerce' ),
				'show'  => false,
			),
		);
		return array_merge( $fields, $extra_fields );

	}

	/**
	 * @since 1.0.0
	 */
	public function add_address_description_field( $order ) {
		if ( $this->helpers->is_enabled() ) {
			$description = $order->get_meta('_shipping_address_description');
			wp_nonce_field('alopeyk_save_address_desc', '_alopeyk_address_desc_nonce');
			echo '<p id="_shipping_address_description_field" class="form-field form-field-wide">' .
					'<label for="_shipping_address_description"><strong>' . esc_html__( 'Address Description', 'alopeyk-shipping-for-woocommerce' ) . '</strong></label>' .
					'<span class="awcshm-meta">' . esc_html__( 'This will be shown on courier device if order is being sent via Alopeyk shipping method and usually consists of order value, address details or any other sort of data needed for courier to know.', 'alopeyk-shipping-for-woocommerce' ) . '</span>' .
					'<textarea id="_shipping_address_description" name="_shipping_address_description" rows="3">' . esc_html($description) . '</textarea>' .
				'</p>';
		}
	}
	
	
	

/**
 * @since 1.0.0
 * @param integer $post_id
 */
public function save_address_description_field( $post_id ) {
    if (
        defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ||
        ! isset($_POST['_alopeyk_address_desc_nonce']) ||
        ! wp_verify_nonce(
            sanitize_key(wp_unslash($_POST['_alopeyk_address_desc_nonce'])), 
            'alopeyk_save_address_desc'
        ) ||
        get_post_type($post_id) !== 'shop_order'
    ) {
        return;
    }

    if (!current_user_can('edit_shop_order', $post_id)) {
        wp_die(esc_html__('You do not have permission to edit this order.', 'alopeyk-shipping-for-woocommerce'));
    }

    $shipping_address_description_field = '_shipping_address_description';
    if (isset($_POST[$shipping_address_description_field])) {
        $description = sanitize_textarea_field(wp_unslash($_POST[$shipping_address_description_field]));
        update_post_meta($post_id, $shipping_address_description_field, $description);
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
                    echo '<div class="notice error"><p>' . esc_html__( 'Order details not found.', 'alopeyk-shipping-for-woocommerce' ) . '</p></div>';
                });
                return;
            }

            $order_data->transport_type_name = $this->helpers->get_transport_type_name( $order_data->transport_type );
            add_meta_box( ALOPEYK_METHOD_ID . '-order-courier-actions', esc_html__( 'Courier Information', 'alopeyk-shipping-for-woocommerce' ), function () use ( $order_data ) {
                $courier_info = isset( $order_data->courier_info ) && $order_data->courier_info ? $order_data->courier_info : null;
                if ( $courier_info ) {
                    $content = alopeyk_get_local_template_part( 'alopeyk-woocommerce-shipping-admin-meta-courier-info', array ( 'order_data' => $order_data ) );
                } else {
                    $content = '<ul><li class="wide awcshm-meta-box-content-container">' . esc_html__( 'No courier assigned to this order yet.', 'alopeyk-shipping-for-woocommerce' ) . '</li></ul>';
                }
                echo wp_kses_post($content);
            }, $screen, 'side' );

            add_meta_box( ALOPEYK_METHOD_ID . '-order-info-actions', esc_html__( 'Order Information', 'alopeyk-shipping-for-woocommerce' ), function () use ( $order_data ) {
				$content = alopeyk_get_local_template_part( 'alopeyk-woocommerce-shipping-admin-meta-order-info', array( 'order_data' => $order_data ) );
				echo wp_kses_post( $content ); 
            }, $screen, 'side' );

            add_meta_box( ALOPEYK_METHOD_ID . '-order-shipping-actions', esc_html__( 'Shipping Details', 'alopeyk-shipping-for-woocommerce' ), function () use ( $order_data ) {
				$content = alopeyk_get_local_template_part( 'alopeyk-woocommerce-shipping-admin-meta-shipping-info', array ( 'order_data' => $order_data ) );
				echo wp_kses_post( $content ); 
            }, $screen, 'normal', 'high' );
        } else {
            $order = wc_get_order();

            if (!$order) {
                return;
            }

            $last_status = $common->get_order_history( $order->get_id(), array( 'posts_per_page' => 1 ) );
            add_meta_box( ALOPEYK_METHOD_ID . '-wcorder-actions', esc_html__( 'Ship via Alopeyk', 'alopeyk-shipping-for-woocommerce' ), function () use ( $last_status ) {
				$content = alopeyk_get_local_template_part( 'alopeyk-woocommerce-shipping-admin-meta-wcorder', array ( 'last_status' => $last_status ? $last_status[0] : null ) );
				echo wp_kses_post( $content ); 
			}, $screen, 'side' );

            $args = array();
            $requestOrder = isset( $_GET['order'] ) ? sanitize_text_field( $_GET['order'] ) : null;
            if ( $requestOrder ) {
                $args['order'] = $requestOrder;
            }

            $orderby = isset( $_GET['orderby'] ) ? sanitize_text_field( $_GET['orderby'] ) : null;
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
                    $args['meta_key'] = '_awcshm_' . sanitize_key( $orderby );
                }
            }

            $last_status = $common->get_order_history( $order->get_id(), $args );
            add_meta_box( ALOPEYK_METHOD_ID . '-wcorder-history', esc_html__( 'Alopeyk Orders History', 'alopeyk-shipping-for-woocommerce' ), function () use ( $last_status ) {
				$content = alopeyk_get_local_template_part( 'alopeyk-woocommerce-shipping-admin-order-history', array ( 'history' => $last_status ) );
				echo wp_kses_post( $content ); 
            }, $screen, 'normal', 'high' );
        }
	}

	/**
	 * @since 1.0.0
	 */
	public function admin_menu_items() {

		add_menu_page( esc_html__( 'Alopeyk', 'alopeyk-shipping-for-woocommerce' ), esc_html__( 'Alopeyk', 'alopeyk-shipping-for-woocommerce' ), 'manage_options', 'alopeyk', null, plugins_url( 'admin/img/icon.svg', dirname( __FILE__ ) ), '55.7' );
		add_submenu_page( 'alopeyk', esc_html__( 'Profile', 'alopeyk-shipping-for-woocommerce' ), esc_html__( 'Profile', 'alopeyk-shipping-for-woocommerce' ), 'manage_options', 'alopeyk-credit', function () {
			if ( $user_data = $this->helpers->get_user_data( null, null, true, [ 'with' => [ 'credit', 'score' ] ] ) ) {
				$endpoint = $this->helpers->get_api_endpoint();
				$content = alopeyk_get_local_template_part( 'alopeyk-woocommerce-shipping-admin-credit-page', array( 
					'user_data'   => $user_data,
					'user_credit' => $this->helpers->normalize_price( $this->helpers->get_user_data( 'credit' ) * 10 ),
					'api_url'     => $endpoint['url'],
				) );
				echo wp_kses_post( $content ); 
			} else {
				echo '<div class="error notice awcshm-credit-widget-wrapper"><p>';
				echo sprintf(
					/* translators: %1$s: Setting url */
					esc_html__(
						'User data not found. You have to enter a valid API key in %1$s in order to access this page.',
						'alopeyk-shipping-for-woocommerce'
					),
					'<a href="' . esc_url($this->get_settings_url()) . '" target="_blank">' . esc_html__('settings page', 'alopeyk-shipping-for-woocommerce') . '</a>'
				);
				echo '</p></div>';
				
			}
		});
		add_submenu_page( 'alopeyk', esc_html__( 'Settings', 'alopeyk-shipping-for-woocommerce' ), esc_html__( 'Settings', 'alopeyk-shipping-for-woocommerce' ), 'manage_options', 'alopeyk-shipping-for-woocommerce-settings', function () {
			wp_redirect( $this->get_settings_url(), 301 );
			exit;
		});
		add_submenu_page( 'alopeyk', esc_html__( 'Support', 'alopeyk-shipping-for-woocommerce' ), esc_html__( 'Support', 'alopeyk-shipping-for-woocommerce' ), 'manage_options', 'alopeyk-support', function () {
			$content = alopeyk_get_local_template_part( 'alopeyk-woocommerce-shipping-admin-support-page', array(
				'log_url'     => $this->helpers->get_log_url(),
				'chat_url'    => $this->helpers->get_chat_url(),
				'dev_email'   => $this->helpers->get_config( 'devEmail' ),
				'support_tel' => $this->helpers->get_support_tel(),
				'is_api_user' => $this->helpers->is_api_user(),
			));
			echo wp_kses_post( $content ); 
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
		$columns['post_status']   = esc_html__( 'Status', 'alopeyk-shipping-for-woocommerce' );
		$columns['order_title']   = esc_html__( 'Order', 'alopeyk-shipping-for-woocommerce' );
		$columns['order_type']    = esc_html__( 'Type', 'alopeyk-shipping-for-woocommerce' );
		$columns['wc_order']      = esc_html__( 'Shop Order(s)', 'alopeyk-shipping-for-woocommerce' );
		$columns['customer']      = esc_html__( 'Customer(s)', 'alopeyk-shipping-for-woocommerce' );
		$columns['order_price']   = esc_html__( 'Cost', 'alopeyk-shipping-for-woocommerce' );
		$columns['order_date']    = esc_html__( 'Date', 'alopeyk-shipping-for-woocommerce' );
		$columns['order_actions'] = esc_html__( 'Actions', 'alopeyk-shipping-for-woocommerce' );
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
				echo '<a href="' . esc_url( admin_url( 'edit.php?post_status=' . $post_status . '&post_type=' . $post_type )) . '" class="awcshm-tooltip awcshm-status type--' . esc_html($post_status) . '"><i class="awcshm-status-icon"></i><span class="awcshm-tooltip-label awcshm-status-label">' . esc_html($post_status_label) . '</span></a>';
			break;
			case 'order_title' :
				echo '<a href="' . esc_url( admin_url( 'post.php?post=' . $post_id . '&action=edit' )) . '" class="row-title">#' . esc_html($post->post_title) . '</a><small class="awcshm-meta">' . esc_html__( 'ID', 'alopeyk-shipping-for-woocommerce' ) . ': ' . ( $alopeyk_order_id ? esc_html($alopeyk_order_id[0]) : '—' ) . '</small>';
			break;
			case 'order_type' :
				$type = get_post_meta( $post_id, '_awcshm_order_type' );
				$type_label = $type ? $this->helpers->get_transport_type_name( $type[0] ) : null;
				echo $type_label ? '<a href="' . esc_url( admin_url( 'edit.php?post_type=alopeyk_order&transport_type=' . $type[0] )) . '">' . esc_html($type_label) . '</a>' : '—';
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
					$user_output_unique = array_unique( $user_output );
					$user_output_escaped = array_map( 'wp_kses_post', $user_output_unique ); 
					echo implode(', ', array_map('wp_kses_post', $user_output_escaped));
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
					echo wp_kses_post(implode(esc_html__(' ,', 'alopeyk-shipping-for-woocommerce') . ' ', $order_output));
				} else {
					echo '—';
				}
			break;
			case 'order_price':
				$order_data = get_post_meta($post_id, '_awcshm_order_data', true);
				$final_price = null;

				if (is_object($order_data)) {
						$original_price = $order_data->price;
						$final_price = $order_data->final_price;
				}
			
				$currency = get_woocommerce_currency();
				if ( $currency == 'IRT' ){
					$original_price = $original_price;	   
					$final_price = $final_price; 
					
				}else if($currency == 'IRR'){
					$original_price = $original_price * 10;
					$final_price = $final_price * 10;
				}			
				$output = '';
			
				if ($original_price == $final_price ) {
					$display_price = $original_price ?: $final_price;
					if ($display_price) {
						$output = wc_price($display_price);
					}
				} else {
					$output = '<del>' . wc_price($original_price) . '</del>';
					$output .= '<br>' . wc_price($final_price);
				}

				echo $output ? wp_kses_post($output) : '—';
			break;
			case 'order_date' :
				$datetime =  get_post_time( 'Y-m-d H:i:s', false, $post_id ) ;
				$post_date = strtotime( $datetime);
				echo esc_html( date_i18n( 'j F Y', $post_date )) . '<br>' . esc_html( date_i18n( 'g:i A', $post_date ));
			break;
			case 'order_actions' :
				$order_data = get_post_meta( $post_id, '_awcshm_order_data', true );
				echo '<a class="awcshm-tooltip button awcshm-icon-button" href="' . esc_url( get_edit_post_link( $post_id )) . '"><i class="awcshm-icon-button-icon dashicons dashicons-visibility"></i><span class="awcshm-tooltip-label">' . esc_html__( 'View', 'alopeyk-shipping-for-woocommerce' ) . '</span></a>';
				if ( $this->helpers->can_be_tracked( $order_data ) ) {
					echo '<a class="awcshm-tooltip button awcshm-icon-button" target="_blank" href="' . esc_url( $this->helpers->get_tracking_url( $order_data )) . '"><i class="awcshm-icon-button-icon dashicons dashicons-welcome-view-site"></i><span class="awcshm-tooltip-label">' . esc_html__( 'Track', 'alopeyk-shipping-for-woocommerce' ) . '</span></a>';
				}
				if ( $this->helpers->can_be_invoiced( $order_data ) ) {
					echo '<a class="awcshm-tooltip button awcshm-icon-button" target="_blank" href="' . esc_url( $this->helpers->get_invoice_url( $order_data )) . '"><i class="awcshm-icon-button-icon dashicons dashicons-format-aside"></i><span class="awcshm-tooltip-label">' . esc_html__( 'Invoice', 'alopeyk-shipping-for-woocommerce' ) . '</span></a>';
				}
				$cancel = $this->helpers->can_be_canceled( $order_data );
				if ( $cancel['enabled'] ) {
					echo '<a class="awcshm-tooltip button awcshm-icon-button awcshm-cancel-modal-toggler" data-order-id="' . esc_attr($post_id) . '" href="#"><i class="awcshm-icon-button-icon dashicons dashicons-no"></i><span class="awcshm-tooltip-label">' . esc_html__( 'Cancel Order', 'alopeyk-shipping-for-woocommerce' ) . '</span></a>';
				}
				if ( $this->helpers->can_be_rated( $order_data ) ) {
					echo '<a class="awcshm-tooltip button awcshm-icon-button awcshm-rate-modal-toggler" data-order-id="' . esc_attr($post_id) . '" href="#"><i class="awcshm-icon-button-icon dashicons dashicons-star-filled"></i><span class="awcshm-tooltip-label">' . esc_html__( 'Rate', 'alopeyk-shipping-for-woocommerce' ) . '</span></a>';
				}
				$order_ids = get_post_meta( $post_id, '_awcshm_wc_order_id' );
				if ( $this->helpers->can_be_repeated( $order_data ) && $order_ids && count( $order_ids ) ) {
					echo '<a class="awcshm-tooltip button awcshm-icon-button awcshm-order-modal-toggler" data-order-ids="' . esc_attr( implode( ',', $order_ids ) ) . '" data-order-types="' . esc_attr($order_data->transport_type) . '" href="#"><i class="awcshm-icon-button-icon dashicons dashicons-image-rotate"></i><span class="awcshm-tooltip-label">' .esc_html__( 'Ship Again', 'alopeyk-shipping-for-woocommerce' ) . '</span></a>';
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
			$types = [];
			
			foreach ( $this->helpers->get_transport_types() as $key => $transport_type ) {
				$types[$key] = $transport_type['label'];
			}
			?>
			
			<select name="transport_type">
				<option value="">
					<?php echo esc_html__( 'Transport Type', 'alopeyk-shipping-for-woocommerce' ); ?>&nbsp;
				</option>
				
				<?php
				$selected = isset( $_GET['transport_type'] ) ? sanitize_text_field( $_GET['transport_type'] ) : '';
				foreach ( $types as $type => $label ) {
					printf(
						'<option value="%s"%s>%s</option>',
						esc_attr($type),
						$type == $selected ? ' selected="selected"' : '',
						esc_html($label)
					);
				}
				?>
				
			</select> 
			
			<?php
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
			$query->query_vars['meta_value'] = sanitize_text_field($_GET['transport_type']);
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

		$actions['alopeyk_cumulative_shipping'] = esc_html__( 'Ship via Alopeyk', 'alopeyk-shipping-for-woocommerce' );
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
					$this->helpers->respond_ajax( alopeyk_get_local_template_part( 'alopeyk-woocommerce-shipping-admin-order-create', $data ) );
				} else {
					/* translators: %s: Maximum order */
					$this->helpers->respond_ajax( sprintf( esc_html__( 'Maximum %s orders can be shipped at once.', 'alopeyk-shipping-for-woocommerce' ), $max_destination ), false );
				}
			}
		}
		$this->helpers->respond_ajax( esc_html__( 'No order selected for shipping.', 'alopeyk-shipping-for-woocommerce' ), false );

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
			$message = alopeyk_get_local_template_part( 'alopeyk-woocommerce-shipping-admin-order-review', $package );
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
		$this->helpers->respond_ajax( alopeyk_get_local_template_part( 'alopeyk-woocommerce-shipping-admin-credit', $data ) );

	}

	/**
	 * @since 1.0.0
	 * @param array $data
	 */
	public function ajax_create_coupon_modal( $data ) {

		$data = isset( $data['data'] ) && $data['data'] ? $data['data'] : array();
		$this->helpers->respond_ajax( alopeyk_get_local_template_part( 'alopeyk-woocommerce-shipping-admin-coupon', $data ) );

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
		$this->helpers->respond_ajax( esc_html__( 'Coupon data not found.', 'alopeyk-shipping-for-woocommerce' ), false );

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
					$this->helpers->respond_ajax( alopeyk_get_local_template_part( 'alopeyk-woocommerce-shipping-admin-order-cancel', $data ) );
				} else {
					$this->helpers->respond_ajax( esc_html__( 'Order cannot be canceled.', 'alopeyk-shipping-for-woocommerce' ), false );
				}
			}
		}
		$this->helpers->respond_ajax( esc_html__( 'No order is selected to be canceled.', 'alopeyk-shipping-for-woocommerce' ), false );

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
		$this->helpers->respond_ajax( esc_html__( 'Cancelation data not found.', 'alopeyk-shipping-for-woocommerce' ), false );

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
		$this->helpers->respond_ajax( esc_html__( 'Order data not found.', 'alopeyk-shipping-for-woocommerce' ), false );
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
					$this->helpers->respond_ajax( alopeyk_get_local_template_part( 'alopeyk-woocommerce-shipping-admin-order-rate', $data ) );
				} else {
					$this->helpers->respond_ajax( esc_html__( 'Courier can not be rated because it is already rated.', 'alopeyk-shipping-for-woocommerce' ), false );
				}
			}
		}
		$this->helpers->respond_ajax( esc_html__( 'Order data not found.', 'alopeyk-shipping-for-woocommerce' ), false );

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
		$this->helpers->respond_ajax( esc_html__( 'Order data not found.', 'alopeyk-shipping-for-woocommerce' ), false );

	}
	
	/**
	 * @since 1.7.0
	 * @param array $data
	 */
	public function ajax_discount_coupon_modal() {

		$this->helpers->respond_ajax( alopeyk_get_local_template_part( 'alopeyk-woocommerce-shipping-admin-discount-coupon' ) );

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
			$this->helpers->respond_ajax( alopeyk_get_local_template_part( 'alopeyk-woocommerce-shipping-admin-customer-score-exchange', $credit_cards ) );
		} else {
			/* translators: %s: Message */
			$this->helpers->respond_ajax(sprintf(esc_html__('Message: %s', 'alopeyk-shipping-for-woocommerce'), esc_html($products->message)), false);
		}

	}

	/**
	 * @since 1.7.0
	 * @param array $data
	 */
	public function ajax_submit_customer_score_exchange_modal( $data ) {

		$card_data = (array) $this->helpers->get_customer_loyalty_products( $data['data'] );
		$this->helpers->respond_ajax( alopeyk_get_local_template_part( 'alopeyk-woocommerce-shipping-admin-customer-score-exchange-submit', $card_data ) );

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
						/* translators: %s: Message */
						$this->helpers->respond_ajax(sprintf(esc_html__('Message: %s', 'alopeyk-shipping-for-woocommerce'), esc_html($message)), false);
					}
				} else {
					$this->helpers->respond_ajax( esc_html__( 'An error occurred while loading card data.', 'alopeyk-shipping-for-woocommerce' ), false );
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
			$this->helpers->respond_ajax( esc_html__( 'An error occurred while trying to apply entered discount coupon.', 'alopeyk-shipping-for-woocommerce' ), false );
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
				'address' => esc_html__( 'This address is out of service.', 'alopeyk-shipping-for-woocommerce' )
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
			$this->helpers->respond_ajax( esc_html__( 'No address found.', 'alopeyk-shipping-for-woocommerce' ), false );
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
		$this->helpers->respond_ajax( esc_html__( 'Order data not found.', 'alopeyk-shipping-for-woocommerce' ), false );

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
		wp_add_dashboard_widget( 'awcshm_admin_widget', esc_html__( 'Alopeyk Shipping Method for woocommerce', 'alopeyk-shipping-for-woocommerce' ), array( $this, $dashboard_widget_callback ) );
		global $wp_meta_boxes;
	 	$normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
	 	$widget_backup    = array( 'awcshm_admin_widget' => $normal_dashboard['awcshm_admin_widget'] );
	 	$sorted_dashboard = array_merge( $widget_backup, $normal_dashboard );
	 	$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;

	}

	public function dashboard_widget_enter_api() {
		?>
		<div class="awcshm-dashboard-widget" >
			<!--This image is located in the admin/img/logo.png path of the plugin. -->
			<p><img class="awcshm-dashboard-widget-logo" src="<?php echo esc_url( $this->helpers->get_logo_url()); ?>"></p>
			<p><?php echo esc_html__( 'In order to active Alopeyk shipping method, enter Alopeyk API Key in the woocommerce settings.', 'alopeyk-shipping-for-woocommerce' ); ?></p>
			<p>&nbsp;</p>
			<p><a href="<?php echo esc_url( $this->get_settings_url() ); ?>" class="button button-primary" title="<?php echo esc_html__( 'Enter API key', 'alopeyk-shipping-for-woocommerce' ); ?>"><?php echo esc_html__( 'Enter API key', 'alopeyk-shipping-for-woocommerce' ); ?></a></p>
			<p><a href="<?php echo esc_url( $this->helpers->get_support_url());?>" title="<?php echo esc_html__( 'I have no API key', 'alopeyk-shipping-for-woocommerce' ); ?>"><u><?php echo esc_html__( 'I have no API key', 'alopeyk-shipping-for-woocommerce' ); ?></u></a></p>
		</div>
		<?php
	}

	public function dashboard_widget_enable_awcshm() {
		?>
		<div class="awcshm-dashboard-widget">
			<!--This image is located in the admin/img/logo.png path of the plugin. -->
			<p><img class="awcshm-dashboard-widget-logo" src="<?php echo esc_url( $this->helpers->get_logo_url()); ?>"></p>
			<p><?php echo esc_html__( 'Alopeyk shipping method is not activated for your store. You can activate it by enabling the method via Settings page.', 'alopeyk-shipping-for-woocommerce' ); ?></p>
			<p>&nbsp;</p>
			<p><a href="<?php echo esc_url( $this->get_settings_url() ); ?>" class="button button-primary" title="<?php echo esc_html__( 'Settings', 'alopeyk-shipping-for-woocommerce' ); ?>"><?php echo esc_html__( 'Settings', 'alopeyk-shipping-for-woocommerce' ); ?></a></p>
			<p><a href="<?php echo esc_url( $this->helpers->get_support_url());?>" title="<?php echo esc_html__( 'I have problem with my API key', 'alopeyk-shipping-for-woocommerce' ); ?>"><u><?php echo esc_html__( 'I have problem with my API key', 'alopeyk-shipping-for-woocommerce' ); ?></u></a></p>
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
					<a href="<?php echo esc_url( $this->get_orders_list_url()); ?>">
						<strong>
							<?php echo esc_html($total); ?> <?php echo esc_html__( 'Order', 'alopeyk-shipping-for-woocommerce' )?>
						</strong><?php echo esc_html__( 'Order Created', 'alopeyk-shipping-for-woocommerce' )?>
					</a>
				</li>
				<li class="failed-orders">
					<a href="<?php echo esc_url( $this->get_orders_list_url( 'awcshm-failed' )); ?>">
						<strong>
							<?php echo esc_html($failed); ?> <?php echo esc_html__( 'Order', 'alopeyk-shipping-for-woocommerce' )?>
						</strong><?php echo esc_html__( 'unsuccessful sending', 'alopeyk-shipping-for-woocommerce' )?>
					</a>
				</li>
				<li class="progress-orders">
					<a href="<?php echo esc_url( $this->get_orders_list_url( 'awcshm-progress' )); ?>">
						<strong>
							<?php echo esc_html($progress); ?> <?php echo esc_html__( 'Order', 'alopeyk-shipping-for-woocommerce' )?>
						</strong><?php echo esc_html__( 'sending', 'alopeyk-shipping-for-woocommerce' )?>
					</a>
				</li>
				<li class="done-orders">
					<a href="<?php echo esc_url( $this->get_orders_list_url( 'awcshm-done' )); ?>">
						<strong><?php echo esc_html($done); ?>
							<?php echo esc_html__( 'Order', 'alopeyk-shipping-for-woocommerce' )?>
						</strong><?php echo esc_html__( 'delivered', 'alopeyk-shipping-for-woocommerce' )?>
					</a>
				</li>
				<li class="pending-orders">
					<a href="<?php echo esc_url( $this->get_orders_list_url( 'awcshm-pending' )); ?>">
						<strong>
							<?php echo esc_html($pending); ?> <?php echo esc_html__( 'Order', 'alopeyk-shipping-for-woocommerce' )?>
						</strong><?php echo esc_html__( 'finding', 'alopeyk-shipping-for-woocommerce' )?>
					</a>
				</li>
				<li class="scheduled-orders">
					<a href="<?php echo esc_url( $this->get_orders_list_url( 'awcshm-scheduled' )); ?>">
						<strong>
							<?php echo esc_html($scheduled); ?> <?php echo esc_html__( 'Order', 'alopeyk-shipping-for-woocommerce' )?>
						</strong><?php echo esc_html__( 'scheduled for sending', 'alopeyk-shipping-for-woocommerce' )?>
					</a>
				</li>
		<?php
				$is_api_user = $this->helpers->is_api_user();
				if ( $is_api_user !== true ) {
					echo '<li class="check-api-fault"><p>' . esc_html($is_api_user) . '</p></li>';
				}
		?>
			</ul>
		</div>
		<?php
	}

	public function awcshm_admin_notice() {
		$wrong_key = $this->helpers->get_option( 'wrong_key', true );
		$class = 'notice notice-error is-dismissible';

        if($wrong_key == 'yes' and $this->helpers->get_option('api_key')) {
            $message = esc_html__('The API key is not valid.', 'alopeyk-shipping-for-woocommerce');
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

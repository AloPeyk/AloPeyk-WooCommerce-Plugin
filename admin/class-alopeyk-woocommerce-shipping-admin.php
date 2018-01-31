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
	 * @since  1.0.0
	 * @return boolean
	 */
	public function is_order_edit() {

		$screen = get_current_screen();
		return $screen->id == 'shop_order';

	}

	/**
	 * @since 1.0.0
	 */
	public function enqueue_styles() {

		$screen = get_current_screen();
		if ( in_array( $screen->id, array( 'edit-shop_order', 'edit-alopeyk_order', 'alopeyk_order', 'shop_order' ) ) || strpos( $screen->id, '_page_alopeyk-credit' ) ) {
			wp_deregister_style( 'jquery-ui-style' );
			wp_enqueue_style( 'wp-jquery-ui-dialog' );
		}
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/alopeyk-woocommerce-shipping-admin' . ( WP_DEBUG ? '.min' : '' ) . '.css', array(), $this->version, 'all' );
		if ( $this->is_order_edit() ) {
			wp_enqueue_style( $this->plugin_name . '__front', plugins_url( 'public/css/alopeyk-woocommerce-shipping-public' . ( WP_DEBUG ? '.min' : '' ) . '.css', dirname( __FILE__ ) ), array(), $this->version, 'all' );
		}

	}

	/**
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		
		wp_enqueue_media();
		wp_enqueue_script( 'jquery-ui-dialog' );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/alopeyk-woocommerce-shipping-admin' . ( WP_DEBUG ? '.min' : '' ) . '.js', array( 'jquery' ), $this->version, false );
		if ( $this->is_order_edit() ) {
			wp_enqueue_script( $this->plugin_name . '__front', plugins_url( 'public/js/alopeyk-woocommerce-shipping-public' . ( WP_DEBUG ? '.min' : '' ) . '.js', dirname( __FILE__ ) ), array(), $this->version, 'all' );
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

		return admin_url( 'admin.php?page=' . $this->get_wc_settings_url() . '&tab=shipping&section=' . METHOD_ID );

	}

	/**
	 * @since  1.0.0
	 * @param  array $fields
	 * @return array
	 */
	public function add_address_fields( $fields ) {

		global $post;
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
			global $post;
			echo '<p id="_shipping_address_description_field" class="form-field form-field-wide">' .
					'<label for="_shipping_address_description"><strong>' . __( 'Address Description', 'alopeyk-woocommerce-shipping' ) . '</strong></label>' .
					'<span class="awcshm-meta">' . __( 'This will be shown on courier device if order is being sent via Alopeyk shipping method and usually consists of order value, address details or any other sort of data needed for courier to know.', 'alopeyk-woocommerce-shipping' ) . '</span>' .
					'<textarea id="_shipping_address_description" name="_shipping_address_description" rows="3">' . get_post_meta( $post->ID, '_shipping_address_description', true ) . '</textarea>' .
				'</p>';
		}

	}

	/**
	 * @since 1.0.0
	 * @param integer $post_id
	 */
	public function save_address_description_field( $post_id ) {

		$shipping_address_description_field = '_shipping_address_description';
		if ( get_post_type( $post_id ) == 'shop_order' && isset( $_POST[$shipping_address_description_field] ) ) {
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
		if ( $post && $common = $this->helpers ) {
			if ( $common->is_enabled() ) {
				$screen = get_current_screen();
				$screen = $screen->id;
				if ( $post->post_type == $common::$order_post_type_name ) {
					$post_title = $post->post_title;
					remove_meta_box( 'submitdiv', $screen, 'side' );
					$order_data = get_post_meta( $post->ID, '_awcshm_order_data', true );
					if ( $order_data ) {
						add_meta_box( METHOD_ID . '-order-courier-actions', __( 'Courier Information', 'alopeyk-woocommerce-shipping' ), function () use ( $order_data ) {
							$courier_info = isset( $order_data->courier_info ) && $order_data->courier_info ? $order_data->courier_info : null;
							if ( $courier_info ) {
								$content = get_local_template_part( 'alopeyk-woocommerce-shipping-admin-meta-courier-info', array ( 'order_data' => $order_data ) );
							} else {
								$content = '<ul><li class="wide awcshm-meta-box-content-container">' . __( 'No courier assigned to this order yet.', 'alopeyk-woocommerce-shipping' ) . '</li></ul>';
							}
							echo $content;
						}, $screen, 'side', 'default' );
						add_meta_box( METHOD_ID . '-order-info-actions', __( 'Order Information', 'alopeyk-woocommerce-shipping' ), function () use ( $order_data ) {
							echo get_local_template_part( 'alopeyk-woocommerce-shipping-admin-meta-order-info', array ( 'order_data' => $order_data ) );
						}, $screen, 'side', 'default' );
						add_meta_box( METHOD_ID . '-order-shipping-actions', __( 'Shipping Details', 'alopeyk-woocommerce-shipping' ), function () use ( $order_data ) {
							echo get_local_template_part( 'alopeyk-woocommerce-shipping-admin-meta-shipping-info', array ( 'order_data' => $order_data ) );
						}, $screen, 'normal', 'high' );
					} else {
						add_action( 'admin_notices', function () {
							echo '<div class="notice error"><p>' . __( 'Order details not found.', 'alopeyk-woocommerce-shipping' ) . '</p></div>';
						});
					}
				} else if ( $post->post_type == 'shop_order' ) {
					$last_status = $common->get_order_history( $post->ID, array( 'posts_per_page' => 1 ) );
					add_meta_box( METHOD_ID . '-wcorder-actions', __( 'Ship via Alopeyk', 'alopeyk-woocommerce-shipping' ), function () use ( $last_status ) {
						echo get_local_template_part( 'alopeyk-woocommerce-shipping-admin-meta-wcorder', array ( 'last_status' => $last_status ? $last_status[0] : null ) );
					}, $screen, 'side', 'default' );
					$args = array();
					$orderby = isset( $_GET['orderby'] ) ? $_GET['orderby'] : null;
					$order = isset( $_GET['order'] ) ? $_GET['order'] : null;
					if ( $order ) {
						$args['order'] = $order;
					}
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
					$last_status = $common->get_order_history( $post->ID, $args );
					add_meta_box( METHOD_ID . '-wcorder-history', __( 'Alopeyk Orders History', 'alopeyk-woocommerce-shipping' ), function () use ( $last_status ) {
						echo get_local_template_part( 'alopeyk-woocommerce-shipping-admin-order-history', array ( 'history' => $last_status ) );
					}, $screen, 'normal', 'high' );
				}
			}
		}

	}

	/**
	 * @since 1.0.0
	 */
	public function admin_menu_items() {

		add_menu_page( __( 'Alopeyk', 'alopeyk-woocommerce-shipping' ), __( 'Alopeyk', 'alopeyk-woocommerce-shipping' ), 'manage_options', 'alopeyk', null, plugins_url( 'admin/img/icon.svg', dirname( __FILE__ ) ), '55.7' );
		add_submenu_page( 'alopeyk', __( 'Credit', 'alopeyk-woocommerce-shipping' ), __( 'Credit', 'alopeyk-woocommerce-shipping' ), 'manage_options', 'alopeyk-credit', function () {
			if ( $user_data = $this->helpers->get_user_data() ) {
				echo get_local_template_part( 'alopeyk-woocommerce-shipping-admin-credit-page', array( 'user_data' => $user_data ) );
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
		if ( strpos( $screen->id, '_page_alopeyk-support' ) ) {
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
		$post_type = $post->post_type;
		$post_status = $post->post_status;
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
				$type_label = $type ? ( $type[0] == 'motorbike' ? __( 'Motorbike', 'alopeyk-woocommerce-shipping' ) : ( $type[0] == 'cargo' ? __( 'Cargo', 'alopeyk-woocommerce-shipping' ) : $type[0] ) ) : null;
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
					echo '<a class="awcshm-tooltip button awcshm-icon-button" target="_blank" href="' . $this->helpers->get_tracking_url( $order_data ) . '"><i class="awcshm-icon-button-icon dashicons dashicons-location"></i><span class="awcshm-tooltip-label">' . __( 'Track', 'alopeyk-woocommerce-shipping' ) . '</span></a>';
				}
				if ( $this->helpers->can_be_invoiced( $order_data ) ) {
					echo '<a class="awcshm-tooltip button awcshm-icon-button" target="_blank" href="' . $this->helpers->get_invoice_url( $order_data ) . '"><i class="awcshm-icon-button-icon dashicons dashicons-info"></i><span class="awcshm-tooltip-label">' . __( 'Invoice', 'alopeyk-woocommerce-shipping' ) . '</span></a>';
				}
				$cancel = $this->helpers->can_be_canceled( $order_data );
				if ( $cancel['enabled'] ) {
					echo '<a class="awcshm-tooltip button awcshm-icon-button awcshm-cancel-modal-toggler" data-order-id="' . $post_id . '" href="#"><i class="awcshm-icon-button-icon dashicons dashicons-no"></i><span class="awcshm-tooltip-label">' . __( 'Cancel', 'alopeyk-woocommerce-shipping' ) . '</span></a>';
				}
				if ( $this->helpers->can_be_rated( $order_data ) ) {
					echo '<a class="awcshm-tooltip button awcshm-icon-button awcshm-rate-modal-toggler" data-order-id="' . $post_id . '" href="#"><i class="awcshm-icon-button-icon dashicons dashicons-thumbs-up"></i><span class="awcshm-tooltip-label">' . __( 'Rate', 'alopeyk-woocommerce-shipping' ) . '</span></a>';
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
			$types = array(
				'cargo'     => __( 'Cargo', 'alopeyk-woocommerce-shipping' ),
				'motorbike' => __( 'Motorbike', 'alopeyk-woocommerce-shipping' )
			);
			?>
			<select name="transport_type">
				<option value=""><?php _e( 'Transport Type', 'alopeyk-woocommerce-shipping' ); ?>&nbsp;</option>
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

		if ( ! is_admin() )
			return;  
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

		if ( isset( $data['data'] ) && $data['data'] ) {
			parse_str( $data['data'], $data );
			if ( $data ) {
				$orders       = isset( $data['orders'] )      ? $data['orders']       : array();
				$type         = isset( $data['type'] )        ? $data['type']         : null;
				$description  = isset( $data['description'] ) ? $data['description']  : null;
				$ship_now     = isset( $data['ship_now'] )    ? $data['ship_now']     : null;
				$ship_date    = isset( $data['ship_date'] )   ? $data['ship_date']    : null;
				$ship_time    = isset( $data['ship_time'] )   ? $data['ship_time']    : null;
				$scheduled_at = ( $ship_date && $ship_time && $ship_now !== 'true' ) ? ( $ship_date . ' ' . $ship_time ) : null;
				$response = $this->helpers->check_order( $orders, $type, $scheduled_at, $description );
				$success = $response['success'];
				$message = $response['message'];
				$package = (array) $response['package'];
				if ( $success ) {
					$message = get_local_template_part( 'alopeyk-woocommerce-shipping-admin-order-review', $package );
				}
				$this->helpers->respond_ajax( $message, $success, $package );
			}
		}
		$this->helpers->respond_ajax( __( 'Order data not found.', 'alopeyk-woocommerce-shipping' ), false );

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

}

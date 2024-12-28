<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
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

if ( class_exists( 'Alopeyk_WooCommerce_Shipping' ) ) {
	return;
}

/**
 * @since 1.0.0
 */
class Alopeyk_WooCommerce_Shipping {

	protected $loader;
	protected $plugin_name;
	protected $version;

	/**
	 * @since 1.0.0
	 */
	public function __construct() {
		
		if ( defined( 'ALOPEYK_PLUGIN_VERSION' ) ) {
			$this->version = ALOPEYK_PLUGIN_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'alopeyk-shipping-for-woocommerce';
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_common_hooks();

	}

	/**
	 * @since 1.0.0
	 */
	private function load_dependencies() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'vendor/autoload.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-alopeyk-woocommerce-shipping-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-alopeyk-woocommerce-shipping-i18n.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-alopeyk-woocommerce-shipping-template.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-alopeyk-woocommerce-shipping-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-alopeyk-woocommerce-shipping-public.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-alopeyk-woocommerce-shipping-common.php';
		$this->loader = new Alopeyk_WooCommerce_Shipping_Loader();

	}

	/**
	 * @since 1.0.0
	 */
	private function set_locale() {

		$plugin_i18n = new Alopeyk_WooCommerce_Shipping_i18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * @since 1.0.0
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Alopeyk_WooCommerce_Shipping_Admin( $this->get_plugin_name(), $this->get_version() );
		$order_post_type = Alopeyk_WooCommerce_Shipping_Common::$order_post_type_name;
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles', 1000 );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_filter( 'plugin_row_meta', $plugin_admin, 'meta_links', 10, 2 );
		$this->loader->add_filter( 'plugin_action_links_' . ALOPEYK_PLUGIN_BASENAME, $plugin_admin, 'action_links' );
		$this->loader->add_filter( 'woocommerce_admin_shipping_fields', $plugin_admin, 'add_address_fields', 1000 );
		$this->loader->add_action( 'woocommerce_admin_order_data_after_shipping_address', $plugin_admin, 'add_address_description_field' );
		$this->loader->add_action( 'save_post', $plugin_admin, 'save_address_description_field' );
		$this->loader->add_filter( 'woocommerce_process_shop_order_meta', $plugin_admin, 'check_address_fields' );
		$this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'handle_meta_boxes' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'admin_menu_items' );
		$this->loader->add_action( 'restrict_manage_posts', $plugin_admin, 'add_orders_filter' );
		$this->loader->add_filter( 'parse_query', $plugin_admin, 'orders_filter' );
		$this->loader->add_filter( 'manage_edit-' . $order_post_type . '_columns', $plugin_admin, 'add_columns' );
		$this->loader->add_filter( 'manage_edit-' . $order_post_type . '_sortable_columns', $plugin_admin, 'add_sortable_columns' );
		$this->loader->add_filter( 'manage_' . $order_post_type. '_posts_custom_column', $plugin_admin, 'set_custom_column_content', 10, 2 );
		$this->loader->add_filter( 'pre_get_posts', $plugin_admin, 'sort_meta_columns' );
		$this->loader->add_filter( 'post_row_actions', $plugin_admin, 'remove_useless_actions', 10, 2 );
		$this->loader->add_filter( 'bulk_actions-edit-' . $order_post_type, $plugin_admin, 'remove_useless_bulk_actions' );
		$this->loader->add_filter( 'bulk_actions-edit-shop_order', $plugin_admin, 'add_cumulative_shipping' );
		$this->loader->add_filter( 'admin_footer_text', $plugin_admin, 'remove_footer_content', 1000 );
		$this->loader->add_filter( 'update_footer', $plugin_admin, 'remove_footer_content', 1000 );
		$this->loader->add_action( 'admin_head', $plugin_admin, 'remove_admin_notices', 1000 );
		$this->loader->add_action( 'wp_dashboard_setup', $plugin_admin, 'dashboard_widget' );
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'awcshm_admin_notice' );

	}

	/**
	 * @since 1.0.0
	 */
	private function define_public_hooks() {

		$plugin_public = new Alopeyk_WooCommerce_Shipping_Public( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * @since 1.0.0
	 */
	private function define_common_hooks() {

		$plugin_common = new Alopeyk_WooCommerce_Shipping_Common( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'cron_schedules', $plugin_common, 'add_cron_schedule' );
		$this->loader->add_action( ALOPEYK_METHOD_ID . '_active_order_update', $plugin_common, 'update_active_order' );
		$this->loader->add_action( ALOPEYK_METHOD_ID . '_active_orders_update', $plugin_common, 'update_active_orders' );
		$this->loader->add_action( ALOPEYK_METHOD_ID . '_check_mandatory_options', $plugin_common, 'check_mandatory_options' );
		$this->loader->add_action( 'init', $plugin_common, 'create_post_type' );
		$this->loader->add_action( 'init', $plugin_common, 'create_order_statuses' );
		$this->loader->add_action( 'wc_order_statuses', $plugin_common, 'add_awcshm_order_statuses' );
		$this->loader->add_action( 'plugins_loaded', $plugin_common, 'plugin_override' );
		$this->loader->add_action( 'woocommerce_shipping_init', $plugin_common, 'shipping_init' );
		$this->loader->add_filter( 'woocommerce_shipping_methods', $plugin_common, 'add_method' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_common, 'localize_scripts' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_common, 'localize_scripts' );
		$this->loader->add_action( 'wp_ajax_' . ALOPEYK_METHOD_ID, $plugin_common, 'dispatch_requests' );
		$this->loader->add_action( 'wp_ajax_nopriv_' . ALOPEYK_METHOD_ID, $plugin_common, 'dispatch_requests' );
		$this->loader->add_action( 'woocommerce_after_checkout_billing_form', $plugin_common, 'add_address_fields' );
		$this->loader->add_action( 'woocommerce_after_edit_address_form_shipping', $plugin_common, 'add_address_fields' );
		$this->loader->add_action( 'woocommerce_customer_save_address', $plugin_common, 'save_address_fields', 10, 2 );
		$this->loader->add_action( 'woocommerce_checkout_process', $plugin_common, 'check_checkout_fields' );
		$this->loader->add_action( 'woocommerce_cart_shipping_packages', $plugin_common, 'add_fields_to_packages' );
		$this->loader->add_action( 'woocommerce_checkout_update_order_review', $plugin_common, 'recalculate_shipping' );
		$this->loader->add_action( 'woocommerce_cart_needs_shipping', $plugin_common, 'show_shipping', 1000 );
		$this->loader->add_action( 'woocommerce_checkout_update_order_meta', $plugin_common, 'update_order_meta' );
		$this->loader->add_filter( 'woocommerce_gateway_description', $plugin_common, 'update_method_description', 10, 2 );
		$this->loader->add_action( 'woocommerce_order_details_after_order_table', $plugin_common, 'add_tracking_button' );
		$this->loader->add_action( 'woocommerce_my_account_my_orders_actions', $plugin_common, 'add_tracking_button_caller', 10, 2 );
		//new alopeyk woocommerce settings tabs
		$this->loader->add_filter( 'woocommerce_get_settings_pages', $plugin_common, 'woocommerce_tab_init' );

	}

	/**
	 * @since 1.0.0
	 */
	public function run() {

		$this->loader->run();

	}

	/**
	 * @since  1.0.0
	 * @return string
	 */
	public function get_plugin_name() {

		return $this->plugin_name;

	}

	/**
	 * @since  1.0.0
	 * @return class
	 */
	public function get_loader() {

		return $this->loader;

	}

	/**
	 * @since  1.0.0
	 * @return string
	 */
	public function get_version() {

		return $this->version;

	}

}

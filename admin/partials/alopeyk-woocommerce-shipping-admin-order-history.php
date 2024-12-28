<?php

/**
 *
 * @link       https://alopeyk.com
 * @since      1.0.0
 *
 * @package    Alopeyk_WooCommerce_Shipping
 * @subpackage Alopeyk_WooCommerce_Shipping/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

if ( ! class_exists( 'Alopeyk_History_Log_List_Table' ) ) {

	/**
	 * @since 1.0.0
	 */
	class Alopeyk_History_Log_List_Table extends WP_List_Table {

		private $order_data;
		private $admin_helpers;

		/**
		 * @since 1.0.0
		 * @param class $admin_helpers
		 * @param array $order_data
		 */
		function __construct ( $admin_helpers, $order_data ) {

			$this->localize( $admin_helpers, $order_data );
			parent::__construct( array(
				'ajax'      => true,
				'singular'  => 'history-log',
				'plural'    => 'history-logs',
			));
			
		}

		/**
		 * @since 1.0.0
		 * @param class $admin_helpers
		 * @param array $order_data
		 */
		function localize( $admin_helpers, $order_data ) {

			$this->order_data = $order_data;
			$this->admin_helpers = $admin_helpers;

		}

		/**
		 * @since 1.0.0
		 * @param array  $item
		 * @param string $column_name
		 */
		function column_default( $item, $column_name ) {

			$this->admin_helpers->set_custom_column_content( $column_name, $item['id'] );

		}

		/**
		 * @since  1.0.0
		 * @return array
		 */
		function get_columns() {

			return $this->admin_helpers->add_columns();

		}

		/**
		 * @since  1.0.0
		 * @return array
		 */
		function get_sortable_columns() {

			return $this->admin_helpers->add_sortable_columns();

		}

		/**
		 * @since 1.0.0
		 */
		function prepare_items() {

			$this->_column_headers = array( $this->get_columns(), array(), $this->get_sortable_columns() );
			$this->items = $this->order_data;
			
		}

	}

}

if ( ! function_exists( 'alopeyk_render_history_log_list' ) ) {

	/**
	 * @since 1.0.0
	 * @param array $order_data
	 */
	function alopeyk_render_history_log_list( $order_data ) {

		$admin_helpers = new Alopeyk_WooCommerce_Shipping_Admin();
		$testListTable = new Alopeyk_History_Log_List_Table( $admin_helpers, $order_data );
		$testListTable->prepare_items();
		$testListTable->display();

	}

}

$data = $this->vars;

if ( isset( $data['history'] ) && $data['history'] ) {
	alopeyk_render_history_log_list( $data['history'] );
} else {
	echo esc_html__( 'This order is not yet shipped via Alopeyk shipping method.', 'alopeyk-shipping-for-woocommerce' );
}
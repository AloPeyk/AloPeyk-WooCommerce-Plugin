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

$data = $this->vars;
$last_status = isset( $data['last_status'] ) ? $data['last_status'] : null;
?>
<ul class="order_actions">
	<li class="wide awcshm-meta-box-content-container">
		<?php if ( $last_status ) { ?>
		<p>
			<span><?php echo __( 'Last Status', 'alopeyk-shipping-for-woocommerce' ); ?>: </span>
			<strong><?php echo $last_status['status_label']; ?></strong>
		</p>
		<?php } else { ?>
		<p><?php echo __( 'Click "Ship" button to start shipping of this order using Alopeyk shipping method.', 'alopeyk-shipping-for-woocommerce' ); ?></p>
		<?php } ?>
	</li>
	<li class="wide awcshm-meta-box-actions-container">
		<?php
            $order = wc_get_order();
		    $order_shipping = $order->get_meta('_awcshm_shipping');
			$transport_type = $order_shipping && isset( $order_shipping['type'] ) ? $order_shipping['type'] : null;
			if ( $last_status ) {
				if ( isset( $last_status['actions']['view'] ) ) {
		?>
		<a class="button" href="<?php echo $last_status['actions']['view']; ?>"><?php echo __( 'View Details', 'alopeyk-shipping-for-woocommerce' ); ?></a>
		<?php
				}
				if ( in_array( $last_status['status'], array( 'awcshm-progress', 'awcshm-pending', 'awcshm-scheduled' ) ) ) {
					if ( isset( $last_status['actions']['cancel'] ) && $last_status['actions']['cancel'] ) {
		?>
		<button type="button" class="button button-primary awcshm-cancel-modal-toggler" data-order-id="<?php echo $last_status['id']; ?>"><?php echo __( 'Cancel Order', 'alopeyk-shipping-for-woocommerce' ); ?></button>
		<?php
					} else {
		?>
		<img src="<?php echo includes_url(); ?>images/spinner.gif">
		<?php
					}
				} else {
		?>
		<button type="button" class="button button-primary awcshm-order-modal-toggler" data-order-ids="<?php echo wc_get_order()->get_id(); ?>"<?php if ( $transport_type ) { ?> data-order-types="<?php echo $transport_type ?>"<?php } ?>><?php echo __( 'Ship Again', 'alopeyk-shipping-for-woocommerce' ); ?></button>
		<?php
				}
			} else {
		?>
		<div class="awcshm-single-action-container">
			<button type="button" class="button button-primary awcshm-order-modal-toggler" data-order-ids="<?php echo wc_get_order()->get_id(); ?>"<?php if ( $transport_type ) { ?> data-order-types="<?php echo $transport_type ?>"<?php } ?>><?php echo __( 'Ship', 'alopeyk-shipping-for-woocommerce' ); ?></button>
		</div>
		<?php
			}
		?>
	</li>
</ul>

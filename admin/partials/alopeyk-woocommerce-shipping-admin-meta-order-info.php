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
$order_data = isset( $data['order_data'] ) ? $data['order_data'] : null;
if ( $order_data ) {
	global $post;
	$helpers = new Alopeyk_WooCommerce_Shipping_Common();
	if ( $order_data->screenshot && isset( $order_data->screenshot->url ) ) {
?>
<a href="<?php echo $helpers->get_tracking_url( $order_data ); ?>" target="_blank">
	<img src="<?php echo $order_data->screenshot->url; ?>" alt="<?php echo __( 'Order Screenshot', 'alopeyk-woocommerce-shipping' ); ?>" class="awcshm-order-screenshot">
</a>
<?php
	}
?>
<ul class="order_actions">
	<li class="wide awcshm-meta-box-content-container">
		<p>
			<span><?php echo __( 'Status', 'alopeyk-woocommerce-shipping' ); ?>: </span>
			<strong><?php echo $helpers->get_order_status_label( $helpers->get_order_status( $order_data ) ); ?></strong>
		</p>
		<?php if ( isset( $order_data->invoice_number ) ) { ?>
		<p>
			<span><?php echo __( 'Invoice Number', 'alopeyk-woocommerce-shipping' ); ?>: </span>
			<strong><?php echo $order_data->invoice_number; ?></strong>
		</p>
		<?php } ?>
		<?php if ( isset( $order_data->id ) ) { ?>
		<p>
			<span><?php echo __( 'ID', 'alopeyk-woocommerce-shipping' ); ?>: </span>
			<strong><?php echo $order_data->id; ?></strong>
		</p>
		<?php } ?>
		<?php if ( isset( $order_data->price ) ) { ?>
		<p>
			<span><?php echo __( 'Cost', 'alopeyk-woocommerce-shipping' ); ?>: </span>
			<strong><?php echo wc_price( $helpers->normalize_price( $order_data->price * 10 ) ); ?></strong>
		</p>
		<?php } ?>
		<?php if ( isset( $order_data->order_discount ) && $order_data->order_discount ) { ?>
				<p>
					<span><?php echo __( 'Discount Code Value', 'alopeyk-woocommerce-shipping' ); ?>: </span>
					<strong><?php echo ( isset( $order_data->order_discount->discount ) && ! is_null( $order_data->order_discount->discount ) ) ? wc_price( $helpers->normalize_price( $order_data->order_discount->discount * 10 ) ) : '—'; ?></strong>
				</p>
		<?php } ?>
		<?php if ( isset( $order_data->transport_type ) ) { ?>
		<p>
			<span><?php echo __( 'Transport Type', 'alopeyk-woocommerce-shipping' ); ?>: </span>
			<strong><?php echo $order_data->transport_type_name; ?></strong>
		</p>
		<?php } ?>
		<p>
			<span><?php echo __( 'Shop Order(s)', 'alopeyk-woocommerce-shipping' ); ?>: </span>
			<?php
				$order_ids = get_post_meta( $post->ID, '_awcshm_wc_order_id' );
				if ( $order_ids && count( $order_ids ) ) {
					$order_output = array();
					foreach ( $order_ids as $order_id ) {
						$order_output[] = '<strong><a href="' . admin_url( 'post.php?post=' . $order_id ) . '&action=edit" target="_blank">#' . $order_id . '</a></strong>';
					}
					echo implode( __( ',', 'alopeyk-woocommerce-shipping' ) . ' ', $order_output );
				} else {
					echo '<strong>—</strong>';
				}
			?>
			<strong><?php echo ''; ?></strong>
		</p>
		<p>
			<span><?php echo __( 'Customer(s)', 'alopeyk-woocommerce-shipping' ); ?>: </span>
			<?php
				$user_ids = get_post_meta( $post->ID, '_awcshm_user_id' );
				if ( $user_ids && count( $user_ids ) ) {
					$user_output = array();
					foreach ( $user_ids as $user_id ) {
						if ( $user_id ) {
							$user_data = get_userdata( $user_id );
							if ( $user_data ) {
								$user_output[] = '<strong><a href="' . admin_url( 'user-edit.php?user_id=' . $user_id ) . '" target="_blank">' . $user_data->first_name . ' ' . $user_data->last_name . '</a></strong>';
							}
						}
					}
					echo implode( __( ',', 'alopeyk-woocommerce-shipping' ) . ' ', array_unique( $user_output ) );
				} else {
					echo '<strong>—</strong>';
				}
			?>
		</p>
	</li>
	<li class="wide awcshm-meta-box-actions-container">
		<?php
			if ( $helpers->can_be_invoiced( $order_data ) ) {
		?>
		<a class="button" target="_blank" href="<?php echo $helpers->get_invoice_url( $order_data ); ?>"><?php echo __( 'Invoice', 'alopeyk-woocommerce-shipping' ); ?></a>
		<?php
			}
			if ( $helpers->can_be_tracked( $order_data ) ) {
		?>
		<a class="button" target="_blank" href="<?php echo $helpers->get_tracking_url( $order_data ); ?>"><?php echo __( 'Track', 'alopeyk-woocommerce-shipping' ); ?></a>
		<?php
			}
			if ( in_array( $helpers->get_order_status( $order_data ), array( 'awcshm-progress', 'awcshm-pending', 'awcshm-scheduled' ) ) ) {
				$can_be_canceled = $helpers->can_be_canceled( $order_data );
				if ( $can_be_canceled['enabled'] ) {
		?>
		<button type="button" class="button button-primary awcshm-cancel-modal-toggler" data-order-id="<?php echo $post->ID; ?>"><?php echo __( 'Cancel Order', 'alopeyk-woocommerce-shipping' ); ?></button>
		<?php
				} else {
		?>
		<img src="<?php echo includes_url(); ?>images/spinner.gif">
		<?php
				}
			} else {
		?>
		<button type="button" class="button button-primary awcshm-order-modal-toggler" data-order-ids="<?php echo implode( ',', $order_ids ); ?>" data-order-types="<?php echo $order_data->transport_type ?>"><?php echo __( 'Ship Again', 'alopeyk-woocommerce-shipping' ); ?></button>
		<?php
			}
		?>
	</li>
</ul>
<?php } ?>

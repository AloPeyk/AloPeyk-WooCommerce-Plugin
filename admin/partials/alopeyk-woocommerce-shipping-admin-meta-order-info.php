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
<a href="<?php echo esc_url($helpers->get_tracking_url($order_data)); ?>" target="_blank">
	<!--This image varies for each order and is sourced from the official Alopeyk API-->
	<img src="<?php echo esc_url($order_data->screenshot->url); ?>" alt="<?php echo esc_attr__('Order Screenshot', 'alopeyk-shipping-for-woocommerce'); ?>" class="awcshm-order-screenshot">
</a>
<?php
	}
?>
<ul class="order_actions">
	<li class="wide awcshm-meta-box-content-container">
		<p>
			<span><?php echo esc_html__( 'Status', 'alopeyk-shipping-for-woocommerce' ); ?>: </span>
			<strong><?php echo esc_html($helpers->get_order_status_label($helpers->get_order_status($order_data))); ?></strong>
		</p>
		<?php if ( isset( $order_data->invoice_number ) ) { ?>
		<p>
			<span><?php echo esc_html__( 'Invoice Number', 'alopeyk-shipping-for-woocommerce' ); ?>: </span>
			<strong><?php echo esc_html($order_data->invoice_number); ?></strong>
		</p>
		<?php } ?>
		<?php if ( isset( $order_data->id ) ) { ?>
		<p>
			<span><?php echo esc_html__( 'ID', 'alopeyk-shipping-for-woocommerce' ); ?>: </span>
			<strong><?php echo esc_html($order_data->id); ?></strong>
		</p>
		<?php } ?>
		<?php if ( isset( $order_data->price ) ) { ?>
		<p>
			<span><?php echo esc_html__( 'Cost', 'alopeyk-shipping-for-woocommerce' ); ?>: </span>
			<strong><?php echo wp_kses_post(wc_price($helpers->normalize_price($order_data->price * 10))); ?></strong>
		</p>
		<?php } ?>
		<?php if ( isset( $order_data->order_discount ) && $order_data->order_discount ) { ?>
				<p>
					<span><?php echo esc_html__( 'Discount Code Value', 'alopeyk-shipping-for-woocommerce' ); ?>: </span>
					<strong><?php echo (isset($order_data->order_discount->discount) && !is_null($order_data->order_discount->discount)) ? wp_kses_post(wc_price($helpers->normalize_price($order_data->order_discount->discount * 10))) : '—'; ?></strong>
				</p>
		<?php } ?>
		<?php if ( isset( $order_data->transport_type ) ) { ?>
		<p>
			<span><?php echo esc_html__( 'Transport Type', 'alopeyk-shipping-for-woocommerce' ); ?>: </span>
			<strong><?php echo esc_html($order_data->transport_type_name); ?></strong>
		</p>
		<?php } ?>
		<p>
			<span><?php echo esc_html__( 'Shop Order(s)', 'alopeyk-shipping-for-woocommerce' ); ?>: </span>
			<?php
				$order_ids = get_post_meta( $post->ID, '_awcshm_wc_order_id' );
				if ( $order_ids && count( $order_ids ) ) {
					$order_output = array();
					foreach ( $order_ids as $order_id ) {
						$order_output[] = '<strong><a href="' . admin_url( 'post.php?post=' . $order_id ) . '&action=edit" target="_blank">#' . $order_id . '</a></strong>';
					}
					echo implode(', ', array_map('wp_kses_post', $order_output));
				} else {
					echo '<strong>—</strong>';
				}
			?>
			<strong><?php echo ''; ?></strong>
		</p>
		<p>
			<span><?php echo esc_html__( 'Customer(s)', 'alopeyk-shipping-for-woocommerce' ); ?>: </span>
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
					echo implode(', ', array_map('wp_kses_post', array_unique($user_output)));
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
		<a class="button" target="_blank" href="<?php echo esc_url($helpers->get_invoice_url($order_data)); ?>"><?php echo esc_html__('Invoice', 'alopeyk-shipping-for-woocommerce'); ?></a>
		<?php
			}
			if ( $helpers->can_be_tracked( $order_data ) ) {
		?>
		<a class="button" target="_blank" href="<?php echo esc_url($helpers->get_tracking_url($order_data)); ?>"><?php echo esc_html__('Track', 'alopeyk-shipping-for-woocommerce'); ?></a>
		<?php
			}
			if ( in_array( $helpers->get_order_status( $order_data ), array( 'awcshm-progress', 'awcshm-pending', 'awcshm-scheduled' ) ) ) {
				$can_be_canceled = $helpers->can_be_canceled( $order_data );
				if ( $can_be_canceled['enabled'] ) {
		?>
		<button type="button" class="button button-primary awcshm-cancel-modal-toggler" data-order-id="<?php echo esc_attr($post->ID); ?>"><?php echo esc_html__('Cancel Order', 'alopeyk-shipping-for-woocommerce'); ?></button>
		<?php
				} else {
		?>
	<!--This image is of the main WordPress files located in the wp-includes folder.-->
		<img src="<?php echo esc_url(includes_url('images/spinner.gif')); ?>" alt="<?php esc_attr_e('Loading...', 'alopeyk-shipping-for-woocommerce'); ?>">
		<?php
				}
			} else {
		?>
		<button type="button" class="button button-primary awcshm-order-modal-toggler" data-order-ids="<?php echo esc_attr(implode(',', $order_ids)); ?>" data-order-types="<?php echo esc_attr($order_data->transport_type); ?>"><?php echo esc_html__('Ship Again', 'alopeyk-shipping-for-woocommerce'); ?></button>
		<?php
			}
		?>
	</li>
</ul>
<?php } ?>

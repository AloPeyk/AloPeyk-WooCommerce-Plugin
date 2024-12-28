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
?>

<table cellpadding="0" cellspacing="0" border="0" class="fixed striped awcshm-horizonal-table">
	<tbody>
		<tr>
			<th width="130" valign="middle"><?php echo esc_html__( 'Transport Type', 'alopeyk-shipping-for-woocommerce' ); ?></th>
			<td valign="middle">
				<?php echo isset($data['type_name']) ? esc_html($data['type_name']) : '—'; ?>
			</td>
		</tr>
		<tr>
			<th width="130" valign="middle"><?php echo esc_html__( 'Shipping Time', 'alopeyk-shipping-for-woocommerce' ); ?></th>
			<td valign="top">
				<?php echo isset($data['scheduled_at']) ? esc_html(date_i18n('j F Y (g:i A)', strtotime($data['scheduled_at']))) : esc_html__('Now', 'alopeyk-shipping-for-woocommerce'); ?>
			</td>
		</tr>
		<?php
			if ( isset( $data['orders'] ) && $orders = $data['orders'] ) {
				$count = count( $orders );
				$label = $count > 1 ? esc_html__( 'Orders', 'alopeyk-shipping-for-woocommerce' ) : esc_html__( 'Order', 'alopeyk-shipping-for-woocommerce' );
		?>
		<tr>
			<th width="130" valign="middle"><?php echo esc_html($label); ?></th>
			<td valign="top">
				<?php foreach ( $orders as $order_id ) { ?>
				<a href="<?php echo esc_url(get_edit_post_link($order_id)); ?>" target="_blank">#<?php echo esc_html($order_id); ?></a>&nbsp;
				<?php } ?>
			</td>
		</tr>
		<?php
			}
		?>
		<tr>
			<th width="130" valign="middle"><?php echo esc_html__( 'Has Return', 'alopeyk-shipping-for-woocommerce' ); ?></th>
			<td valign="top">
				<?php echo isset( $data['shipping']->has_return ) && $data['shipping']->has_return ? esc_html__( 'yes', 'alopeyk-shipping-for-woocommerce' ) : esc_html__( 'no', 'alopeyk-shipping-for-woocommerce' ); ?>
			</td>
		</tr>
		<tr>
			<th width="130" valign="middle"><?php echo esc_html__( 'Cost', 'alopeyk-shipping-for-woocommerce' ); ?></th>
			<td valign="top">
				<?php echo isset($data['shipping']) ? esc_html(wc_price(Alopeyk_WooCommerce_Shipping_Common::normalize_price($data['shipping']->cost))) : '—'; ?>
			</td>
		</tr>
		<tr>
			<th width="130" valign="middle"><?php echo esc_html__( 'Order Point', 'alopeyk-shipping-for-woocommerce' ); ?></th>
			<td valign="top">
				<?php echo (isset($data['shipping']->score) && !is_null($data['shipping']->score)) ? esc_html($data['shipping']->score) : '—'; ?>
			</td>
		</tr>
		<?php
			if ( isset( $data['shipping']->discount ) && ! is_null( $data['shipping']->discount ) ) :
		?>
				<tr>
					<th width="130" valign="middle"><?php echo esc_html__( 'Discount Code Value', 'alopeyk-shipping-for-woocommerce' ); ?></th>
					<td valign="top">
						<?php echo esc_html(wc_price(Alopeyk_WooCommerce_Shipping_Common::normalize_price($data['shipping']->discount))); ?>
						<span class="remove-discount-coupon"><a href="#"><?php echo esc_html__( '(Remove)', 'alopeyk-shipping-for-woocommerce' ); ?></a></span>
					</td>
				</tr>
				<tr>
					<th width="130" valign="middle"><?php echo esc_html__( 'Final Cost', 'alopeyk-shipping-for-woocommerce' ); ?></th>
					<td valign="top">
						<?php echo (isset($data['shipping']->final_price) && !is_null($data['shipping']->final_price)) ? esc_html(wc_price(Alopeyk_WooCommerce_Shipping_Common::normalize_price($data['shipping']->final_price))) : '—'; ?>
					</td>
				</tr>
		<?php
			elseif ( isset( $data['shipping']->discount_coupons_error_msg ) ) :
		?>
				<tr>
					<th width="130" valign="middle"><?php echo esc_html__( 'Order Point', 'alopeyk-shipping-for-woocommerce' ); ?></th>
					<td valign="top">
						<?php echo esc_html($data['shipping']->discount_coupons_error_msg); ?>
					</td>
				</tr>
		<?php
			endif;
		?>
	</tbody>
</table>

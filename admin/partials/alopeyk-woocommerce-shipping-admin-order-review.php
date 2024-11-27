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
			<th width="130" valign="middle"><?php echo __( 'Transport Type', 'alopeyk-shipping-for-woocommerce' ); ?></th>
			<td valign="middle">
				<?php echo isset( $data['type'] ) ? ( $data['type_name'] ) : '—'; ?>
			</td>
		</tr>
		<tr>
			<th width="130" valign="middle"><?php echo __( 'Shipping Time', 'alopeyk-shipping-for-woocommerce' ); ?></th>
			<td valign="top">
				<?php echo isset( $data['scheduled_at'] ) ? date_i18n( 'j F Y (g:i A)', strtotime( $data['scheduled_at'] ) ) : __( 'Now', 'alopeyk-shipping-for-woocommerce' ); ?>
			</td>
		</tr>
		<?php
			if ( isset( $data['orders'] ) && $orders = $data['orders'] ) {
				$count = count( $orders );
				$label = $count > 1 ? __( 'Orders', 'alopeyk-shipping-for-woocommerce' ) : __( 'Order', 'alopeyk-shipping-for-woocommerce' );
		?>
		<tr>
			<th width="130" valign="middle"><?php echo $label; ?></th>
			<td valign="top">
				<?php foreach ( $orders as $order_id ) { ?>
				<a href="<?php echo get_edit_post_link( $order_id ); ?>" target="_blank">#<?php echo $order_id; ?></a>&nbsp;
				<?php } ?>
			</td>
		</tr>
		<?php
			}
		?>
		<tr>
			<th width="130" valign="middle"><?php echo __( 'Has Return', 'alopeyk-shipping-for-woocommerce' ); ?></th>
			<td valign="top">
				<?php echo isset( $data['shipping']->has_return ) && $data['shipping']->has_return ? __( 'yes', 'alopeyk-shipping-for-woocommerce' ) : __( 'no', 'alopeyk-shipping-for-woocommerce' ); ?>
			</td>
		</tr>
		<tr>
			<th width="130" valign="middle"><?php echo __( 'Cost', 'alopeyk-shipping-for-woocommerce' ); ?></th>
			<td valign="top">
				<?php echo isset( $data['shipping'] ) ? wc_price( Alopeyk_WooCommerce_Shipping_Common::normalize_price( $data['shipping']->cost ) ) : '—'; ?>
			</td>
		</tr>
		<tr>
			<th width="130" valign="middle"><?php echo __( 'Order Point', 'alopeyk-shipping-for-woocommerce' ); ?></th>
			<td valign="top">
				<?php echo ( isset( $data['shipping']->score ) && ! is_null( $data['shipping']->score ) ) ? $data['shipping']->score : '—'; ?>
			</td>
		</tr>
		<?php
			if ( isset( $data['shipping']->discount ) && ! is_null( $data['shipping']->discount ) ) :
		?>
				<tr>
					<th width="130" valign="middle"><?php echo __( 'Discount Code Value', 'alopeyk-shipping-for-woocommerce' ); ?></th>
					<td valign="top">
						<?php echo wc_price( Alopeyk_WooCommerce_Shipping_Common::normalize_price( $data['shipping']->discount ) ) ?>
						<span class="remove-discount-coupon"><a href="#"><?php echo __( '(Remove)', 'alopeyk-shipping-for-woocommerce' ); ?></a></span>
					</td>
				</tr>
				<tr>
					<th width="130" valign="middle"><?php echo __( 'Final Cost', 'alopeyk-shipping-for-woocommerce' ); ?></th>
					<td valign="top">
						<?php echo ( isset( $data['shipping']->final_price ) && ! is_null( $data['shipping']->final_price ) ) ? wc_price( Alopeyk_WooCommerce_Shipping_Common::normalize_price( $data['shipping']->final_price ) ) : '—'; ?>
					</td>
				</tr>
		<?php
			elseif ( isset( $data['shipping']->discount_coupons_error_msg ) ) :
		?>
				<tr>
					<th width="130" valign="middle"><?php echo __( 'Order Point', 'alopeyk-shipping-for-woocommerce' ); ?></th>
					<td valign="top">
						<?php echo $data['shipping']->discount_coupons_error_msg; ?>
					</td>
				</tr>
		<?php
			endif;
		?>
	</tbody>
</table>

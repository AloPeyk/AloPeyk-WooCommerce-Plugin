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
			<th width="130" valign="middle"><?php echo __( 'Transport Type', 'alopeyk-woocommerce-shipping' ); ?></th>
			<td valign="middle">
				<?php echo isset( $data['type'] ) ? ( $data['type_name'] ) : '—'; ?>
			</td>
		</tr>
		<tr>
			<th width="130" valign="middle"><?php echo __( 'Shipping Time', 'alopeyk-woocommerce-shipping' ); ?></th>
			<td valign="top">
				<?php echo isset( $data['scheduled_at'] ) ? date_i18n( 'j F Y (g:i A)', strtotime( $data['scheduled_at'] ) ) : __( 'Now', 'alopeyk-woocommerce-shipping' ); ?>
			</td>
		</tr>
		<?php
			if ( isset( $data['orders'] ) && $orders = $data['orders'] ) {
				$count = count( $orders );
				$label = $count > 1 ? __( 'Orders', 'alopeyk-woocommerce-shipping' ) : __( 'Order', 'alopeyk-woocommerce-shipping' );
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
			<th width="130" valign="middle"><?php echo __( 'Has Return', 'alopeyk-woocommerce-shipping' ); ?></th>
			<td valign="top">
				<?php echo isset( $data['shipping']->has_return ) && $data['shipping']->has_return ? __( 'yes', 'alopeyk-woocommerce-shipping' ) : __( 'no', 'alopeyk-woocommerce-shipping' ); ?>
			</td>
		</tr>
		<tr>
			<th width="130" valign="middle"><?php echo __( 'Cost', 'alopeyk-woocommerce-shipping' ); ?></th>
			<td valign="top">
				<?php echo isset( $data['shipping'] ) ? wc_price( Alopeyk_WooCommerce_Shipping_Common::normalize_price( $data['shipping']->cost ) ) : '—'; ?>
			</td>
		</tr>
		<tr>
			<th width="130" valign="middle"><?php echo __( 'Order Point', 'alopeyk-woocommerce-shipping' ); ?></th>
			<td valign="top">
				<?php echo ( isset( $data['shipping']->score ) && ! is_null( $data['shipping']->score ) ) ? $data['shipping']->score : '—'; ?>
			</td>
		</tr>
		<?php
			if ( isset( $data['shipping']->discount ) && ! is_null( $data['shipping']->discount ) ) :
		?>
				<tr>
					<th width="130" valign="middle"><?php echo __( 'Discount Code Value', 'alopeyk-woocommerce-shipping' ); ?></th>
					<td valign="top">
						<?php echo wc_price( Alopeyk_WooCommerce_Shipping_Common::normalize_price( $data['shipping']->discount ) ) ?>
						<span class="remove-discount-coupon"><a href="#"><?php echo __( '(Remove)', 'alopeyk-woocommerce-shipping' ); ?></a></span>
					</td>
				</tr>
				<tr>
					<th width="130" valign="middle"><?php echo __( 'Final Cost', 'alopeyk-woocommerce-shipping' ); ?></th>
					<td valign="top">
						<?php echo ( isset( $data['shipping']->final_price ) && ! is_null( $data['shipping']->final_price ) ) ? wc_price( Alopeyk_WooCommerce_Shipping_Common::normalize_price( $data['shipping']->final_price ) ) : '—'; ?>
					</td>
				</tr>
		<?php
			elseif ( isset( $data['shipping']->discount_coupons_error_msg ) ) :
		?>
				<tr>
					<th width="130" valign="middle"><?php echo __( 'Order Point', 'alopeyk-woocommerce-shipping' ); ?></th>
					<td valign="top">
						<?php echo $data['shipping']->discount_coupons_error_msg; ?>
					</td>
				</tr>
		<?php
			endif;
		?>
	</tbody>
</table>

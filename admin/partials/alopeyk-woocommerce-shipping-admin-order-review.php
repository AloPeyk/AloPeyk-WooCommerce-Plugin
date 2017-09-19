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

<table cellpadding="0" cellspacing="0" border="0" class="fixed striped wp-list-table awcshm-horizonal-table">
	<tbody>
		<tr>
			<th width="130" valign="middle"><?php echo __( 'Transport Type', 'alopeyk-woocommerce-shipping' ); ?></th>
			<td valign="middle">
				<?php echo isset( $data['type'] ) ? ( $data['type'] == 'cargo' ? __( 'Cargo', 'alopeyk-woocommerce-shipping' ) : ( $data['type'] == 'motorbike' ? __( 'Motorbike', 'alopeyk-woocommerce-shipping' ) : __( $data['type'], 'alopeyk-woocommerce-shipping' ) ) ) : '—'; ?>
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
			<th width="130" valign="middle"><?php echo __( 'Cost', 'alopeyk-woocommerce-shipping' ); ?></th>
			<td valign="top">
				<?php echo isset( $data['shipping'] ) ? wc_price( Alopeyk_WooCommerce_Shipping_Common::normalize_price( $data['shipping']->cost ) ) : '—'; ?>
			</td>
		</tr>
	</tbody>
</table>

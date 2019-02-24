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

<form class="awcshm-create-order-form">
	<table cellpadding="0" cellspacing="0" border="0" class="fixed striped awcshm-horizonal-table">
		<tbody>
			<tr>
				<th valign="middle"><?php echo __( 'Transport Type', 'alopeyk-woocommerce-shipping' ); ?></th>
				<td valign="middle">
					<select name="type">
						<?php
							$selected_type_index = isset( $data['type'] ) ? array_search( $data['type'], array_keys( $data['all_transport_types'] ) ) : 0;
							foreach ( $data['all_transport_types'] as $key => $transport_type ) :
								if ( array_search( $key, array_keys( $data['all_transport_types'] ) ) < $selected_type_index ) {
									continue;
								}
						?>
								<option value="<?php echo $key; ?>"<?php if ( isset( $data['type'] ) && $data['type'] == $key ) { ?> selected="selected"<?php } ?>><?php echo $transport_type['label']; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<th valign="top"><?php echo __( 'Shipping Time', 'alopeyk-woocommerce-shipping' ); ?></th>
				<td valign="top">
					<input id="ship_now_true" type="radio" name="ship_now" value="true" checked="checked"><label for="ship_now_true"><?php echo __( 'Now', 'alopeyk-woocommerce-shipping' ); ?></label>&nbsp;
					<input id="ship_now_false" type="radio" name="ship_now" value="false" class="awcshm-shipping-time-filter-trigger"><label for="ship_now_false"><?php echo __( 'Later', 'alopeyk-woocommerce-shipping' ); ?></label>
					<div class="awcshm-shipping-time-filter">
						<div class="awcshm-shipping-date-container">
							<select name="ship_date"></select>						
						</div>
						<div class="awcshm-shipping-time-container">
							<select name="ship_hour" class="ship-hour"></select>
							<span class="awcshm-shipping-time-delimiter">:</span>
							<select name="ship_minute" class="ship-minute"></select>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<th valign="top"><?php echo __( 'Origin Description', 'alopeyk-woocommerce-shipping' ); ?></th>
				<td valign="top">
					<textarea name="description" rows="5" width="100%"><?php echo isset( $data['description'] ) ? $data['description'] : ''; ?></textarea>
					<span class="awcshm-meta"><?php echo __( 'This will be shown on courier device and usually consists of order value, address details or any other sort of data needed for courier to know about the origin address or the whole order.', 'alopeyk-woocommerce-shipping' ); ?></span>
				</td>
			</tr>
			<?php
				if ( isset( $data['orders'] ) && $orders = $data['orders'] ) {
					$count = count( $orders );
					$label = $count > 1 ? __( 'Orders', 'alopeyk-woocommerce-shipping' ) : __( 'Order', 'alopeyk-woocommerce-shipping' );
			?>
			<tr>
				<th valign="top"><?php echo $label; ?></th>
				<td valign="top">
					<?php
						if ( $count == 0 ) {
							echo __( 'No order selected for shipping.', 'alopeyk-woocommerce-shipping' );
						} else if ( $count == 1 ) {
							$order_id = $orders[0];
					?>
					<a href="<?php echo get_edit_post_link( $order_id ); ?>" target="_blank">#<?php echo $order_id; ?></a>
					<input type="hidden" name="orders[]" value="<?php echo $order_id; ?>">
					<?php
						} else {
							foreach ( $orders as $order_id ) {
					?>
					<label>
						<input type="checkbox" name="orders[]" checked="checked" value="<?php echo $order_id; ?>"><a href="<?php echo get_edit_post_link( $order_id ); ?>" target="_blank">#<?php echo $order_id; ?></a>
					</label>&nbsp;
					<?php
							}
						}
					?>
				</td>
			</tr>
			<?php
				}
			?>
		</tbody>
	</table>
	<button type="submit" class="awcshm-hidden"></button>
</form>

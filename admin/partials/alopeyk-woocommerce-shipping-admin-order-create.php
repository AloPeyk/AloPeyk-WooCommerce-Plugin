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
				<th valign="middle"><?php echo esc_html__( 'Transport Type', 'alopeyk-shipping-for-woocommerce' ); ?></th>
				<td valign="middle">
					<select name="type">
						<?php
							$selected_type_index = isset( $data['type'] ) ? array_search( $data['type'], array_keys( $data['all_transport_types'] ) ) : 0;
							foreach ( $data['all_transport_types'] as $key => $transport_type ) :
								if ( array_search( $key, array_keys( $data['all_transport_types'] ) ) < $selected_type_index ) {
									continue;
								}
						?>
								<option value="<?php echo esc_attr($key); ?>"<?php if (isset($data['type']) && $data['type'] == $key) { ?> selected="selected"<?php } ?>><?php echo esc_html($transport_type['label']); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<th valign="top"><?php echo esc_html__( 'Shipping Time', 'alopeyk-shipping-for-woocommerce' ); ?></th>
				<td valign="top">
					<input id="ship_now_true" type="radio" name="ship_now" value="true" checked="checked"><label for="ship_now_true"><?php echo esc_html__( 'Now', 'alopeyk-shipping-for-woocommerce' ); ?></label>&nbsp;
					<input id="ship_now_false" type="radio" name="ship_now" value="false" class="awcshm-shipping-time-filter-trigger"><label for="ship_now_false"><?php echo esc_html__( 'Later', 'alopeyk-shipping-for-woocommerce' ); ?></label>
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
				<th valign="top"><?php echo esc_html__( 'Origin Description', 'alopeyk-shipping-for-woocommerce' ); ?></th>
				<td valign="top">
					<textarea name="description" rows="5" style="width: 100%;"><?php echo esc_textarea(isset($data['description']) ? $data['description'] : ''); ?></textarea>
					<span class="awcshm-meta"><?php echo esc_html__( 'This will be shown on courier device and usually consists of order value, address details or any other sort of data needed for courier to know about the origin address or the whole order.', 'alopeyk-shipping-for-woocommerce' ); ?></span>
				</td>
			</tr>
			<?php
				if ( isset( $data['orders'] ) && $orders = $data['orders'] ) {
					$count = count( $orders );
					$label = $count > 1 ? esc_html__( 'Orders', 'alopeyk-shipping-for-woocommerce' ) : esc_html__( 'Order', 'alopeyk-shipping-for-woocommerce' );
			?>
			<tr>
				<th valign="top"><?php echo esc_html($label); ?></th>
				<td valign="top">
					<?php
						if ( $count == 0 ) {
							echo esc_html__( 'No order selected for shipping.', 'alopeyk-shipping-for-woocommerce' );
						} else if ( $count == 1 ) {
							$order_id = $orders[0];
					?>
					<a href="<?php echo esc_url(get_edit_post_link($order_id)); ?>" target="_blank">#<?php echo esc_html($order_id); ?></a>
					<input type="hidden" name="orders[]" value="<?php echo esc_attr($order_id); ?>">
					<?php
						} else {
							foreach ( $orders as $order_id ) {
					?>
					<label>
						<input type="checkbox" name="orders[]" checked="checked" value="<?php echo esc_attr($order_id); ?>"><a href="<?php echo esc_url(get_edit_post_link($order_id)); ?>" target="_blank">#<?php echo esc_html($order_id); ?></a></label>
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

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
$order_id = isset( $data['order'] ) ? $data['order'] : null;
$edit_link = $order_id ? get_edit_post_link( $order_id ) : '#';
$order_data = isset( $data['order_data'] ) ? $data['order_data'] : null;
$order_label = $order_data ? '#' . $order_data->invoice_number : ( $order_id ? $order_id : esc_html__( 'Alopeyk order', 'alopeyk-shipping-for-woocommerce' ) );
$cancel = isset( $data['cancel'] ) ? $data['cancel'] : null;
?>

<form class="awcshm-cancel-order-form">
	<p>
	<?php
		echo wp_kses(
			sprintf(
				/* translators: %1$s: URL cancel order, %2$s: Cancel URL */
				html_entity_decode(esc_html__('You are about to cancel <strong><a href="%1$s" target="_blank">%2$s</a></strong>.', 'alopeyk-shipping-for-woocommerce')),
				esc_url($edit_link),
				$order_label
			),
			array(
				'strong' => array(),
				'a'      => array(
					'href'  => array(),
					'target' => array()
				),
			)
		);
	?>
	</p>
	<?php
		if ( $cancel && isset( $cancel['penalty'] ) && $cancel['penalty'] && isset( $cancel['penalty_info'] ) && isset( $cancel['penalty_info']['amount'] ) && $penalty_amount = $cancel['penalty_info']['amount'] ) {
	?>
	<div class="error notice">
		<?php if ( isset( $cancel['penalty_info']['delay'] ) && $penalty_delay = $cancel['penalty_info']['delay'] ) { ?>
		<?php /* translators: %1$s: money , %2$s: time */ ?>
		<p><?php echo sprintf( esc_html__('You will be charged %1$s for this cancellation, because more than %2$s minutes passed after order being accepted by Alopeyk courier.', 'alopeyk-shipping-for-woocommerce'), esc_html(wc_price($penalty_amount)), esc_html($penalty_delay)); ?></p>	
		<?php } else { ?>
		<?php /* translators: %s: First: money */ ?>
		<p><?php echo sprintf( esc_html__('You will be charged %s for this cancellation.', 'alopeyk-shipping-for-woocommerce'), esc_html(wc_price($penalty_amount))); ?></p>
		<?php } ?>
	</div>
	<?php
		}
		if ( $cancel && isset( $cancel['reasons'] ) && $cancel['reasons'] ) {
			if ( isset( $data['reasons'] ) && $reasons = (array) $data['reasons'] ) {
	?>
	<div class="awcshm-radio-list-container">
		<p>
			<strong><?php echo esc_html__( 'Which of the following reasons make you decide to cancel this request?', 'alopeyk-shipping-for-woocommerce' ); ?></strong>
		</p>
		<p>
			<?php foreach ( $reasons as $reason => $label ) { ?>
			<label class="awcshm-radio-list-item">
				<input type="radio" name="reason" value="<?php echo esc_attr($reason); ?>" required="required">
				<span><?php echo esc_html($label); ?></span>
			</label>
			<?php } ?>
		</p>
	</div>
	<?php
			}
		}
		if ( $order_id ) {
	?>
	<input type="hidden" name="order" value="<?php echo esc_attr($order_id); ?>">
	<?php
		}
	?>
	<button type="submit" class="awcshm-hidden"></button>
</form>

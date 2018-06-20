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
$order_label = $order_data ? '#' . $order_data->invoice_number : ( $order_id ? $order_id : __( 'Alopeyk order', 'alopeyk-woocommerce-shipping' ) );
$cancel = isset( $data['cancel'] ) ? $data['cancel'] : null;
?>

<form class="awcshm-cancel-order-form">
	<p><?php echo sprintf( __( 'You are about to cancel <strong><a href="%s" target="blank">%s</a></strong>.', 'alopeyk-woocommerce-shipping' ), $edit_link, $order_label ); ?></p>
	<?php
		if ( $cancel && isset( $cancel['penalty'] ) && $cancel['penalty'] && isset( $cancel['penalty_info'] ) && isset( $cancel['penalty_info']['amount'] ) && $penalty_amount = $cancel['penalty_info']['amount'] ) {
	?>
	<div class="error notice">
		<?php if ( isset( $cancel['penalty_info']['delay'] ) && $penalty_delay = $cancel['penalty_info']['delay'] ) { ?>
		<p><?php echo sprintf( __( 'You will be charged %s for this cancelation, because more than %s minutes passed after order being accepted by Alopeyk courier.', 'alopeyk-woocommerce-shipping' ), wc_price( $penalty_amount ), $penalty_delay ); ?></p>
		<?php } else { ?>
		<p><?php echo sprintf( __( 'You will be charged %s for this cancelation.', 'alopeyk-woocommerce-shipping' ), wc_price( $penalty_amount ) ); ?></p>
		<?php } ?>
	</div>
	<?php
		}
		if ( $cancel && isset( $cancel['reasons'] ) && $cancel['reasons'] ) {
			if ( isset( $data['reasons'] ) && $reasons = (array) $data['reasons'] ) {
	?>
	<div class="awcshm-radio-list-container">
		<p>
			<strong><?php echo __( 'Which of the following reasons make you decide to cancel this request?', 'alopeyk-woocommerce-shipping' ); ?></strong>
		</p>
		<p>
			<?php foreach ( $reasons as $reason => $label ) { ?>
			<label class="awcshm-radio-list-item">
				<input type="radio" name="reason" value="<?php echo $reason; ?>" required="required">
				<span><?php echo $label; ?></span>
			</label>
			<?php } ?>
		</p>
	</div>
	<?php
			}
		}
		if ( $order_id ) {
	?>
	<input type="hidden" name="order" value="<?php echo $order_id; ?>">
	<?php
		}
	?>
	<button type="submit" class="awcshm-hidden"></button>
</form>

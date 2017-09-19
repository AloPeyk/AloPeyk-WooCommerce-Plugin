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
$courier_info = isset( $data['order_data'] ) && isset( $data['order_data']->courier_info ) ? $data['order_data']->courier_info : null;
?>

<form class="awcshm-rate-order-form">
	<?php
		if ( $courier_info ) {
			$avatar = strpos( $courier_info->abs_avatar->url, '?' ) !== false ? explode( '?', $courier_info->abs_avatar->url ) : array( $courier_info->abs_avatar->url );
			$avatar = $avatar[0];
	?>
	<div class="awcshm-rate-courier-info">
		<div>
			<figure class="awcshm-courier-avatar" style="background-image: url( '<?php echo $avatar; ?>' );"></figure>
		</div>
		<div>
			<strong><?php echo $courier_info->firstname . ' ' . $courier_info->lastname; ?></strong>
		</div>
	</div>
	<?php
		}
	?>
	<div class="awcshm-rates-container">
		<input type="radio" name="rate" id="awcshm-rate-checkbox-1" value="1" required="required">
		<label for="awcshm-rate-checkbox-1"><?php echo __( 'Very Bad', 'alopeyk-woocommerce-shipping' ); ?></label>
		<input type="radio" name="rate" id="awcshm-rate-checkbox-2" value="2" required="required">
		<label for="awcshm-rate-checkbox-2"><?php echo __( 'Bad', 'alopeyk-woocommerce-shipping' ); ?></label>
		<input type="radio" name="rate" id="awcshm-rate-checkbox-3" value="3" required="required">
		<label for="awcshm-rate-checkbox-3"><?php echo __( 'Not Bad', 'alopeyk-woocommerce-shipping' ); ?></label>
		<input type="radio" name="rate" id="awcshm-rate-checkbox-4" value="4" required="required">
		<label for="awcshm-rate-checkbox-4"><?php echo __( 'Good', 'alopeyk-woocommerce-shipping' ); ?></label>
		<input type="radio" name="rate" id="awcshm-rate-checkbox-5" value="5" checked="checked" required="required">
		<label for="awcshm-rate-checkbox-5"><?php echo __( 'Very Good', 'alopeyk-woocommerce-shipping' ); ?></label>
		<?php if ( isset( $data['reasons'] ) && $reasons = (array) $data['reasons'] ) { ?>
		<div class="awcshm-radio-list-container">
			<p>
				<strong><?php echo __( 'Which of the following reasons make you decide to choose this score?', 'alopeyk-woocommerce-shipping' ); ?></strong>
			</p>
			<p>
				<?php foreach ( $reasons as $reason => $label ) { ?>
				<label class="awcshm-radio-list-item">
					<input type="radio" name="reason" value="<?php echo $reason; ?>" required="required" <?php if ( $reason == 'others' ) { ?>checked="checked"<?php } ?>>
					<span><?php echo $label; ?></span>
					<?php if ( $reason == 'others' ) { ?>
					<textarea name="description" placeholder="<?php echo __( 'Description', 'alopeyk-woocommerce-shipping' ); ?>" rows="4" class="awcshm-rate-description" autofocus></textarea>
					<?php } ?>
				</label>
				<?php } ?>
			</p>
		</div>
		<?php } ?>
	</div>
	<?php if ( $order_id ) { ?>
	<input type="hidden" name="order" value="<?php echo $order_id; ?>">
	<?php } ?>
	<button type="submit" class="awcshm-hidden"></button>
</form>

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
$action = isset( $data['action'] ) ? $data['action'] : '';
$currency = get_woocommerce_currency_symbol('IRT');
$params = array();
if ( ! empty( $action ) ) {
	$components = explode( '?', $action, 2 );
	if ( $components && isset( $components[1] ) ) {
		parse_str( $components[1], $params );
	}
}
?>
<form class="awcshm-add-credit-form" target="_blank" method="GET" action="<?php echo esc_url($action); ?>">
	<?php if ( isset( $data['amounts'] ) && $amounts = $data['amounts'] ) { ?>
	<div class="awcshm-amounts-container">
		<?php
			foreach ( $amounts as $amount ) {
				$normalized_amount = Alopeyk_WooCommerce_Shipping_Common::normalize_price( $amount * 10 );
		?>
		<button type="button" class="button awcshm-amount-button" data-credit-amount="<?php echo esc_attr($amount); ?>"><?php echo wp_kses_post(wc_price($normalized_amount)); ?></button>
		<?php
			}
		?>
	</div>
	<?php } ?>
	<label class="awcshm-amount-input-container">
		<input type="text" name="amount" value="<?php echo isset($data['amount']) ? esc_attr($data['amount'] / 10) : ''; ?>" class="awcshm-amount-input awcshm-price-input" autofocus pattern="\d{3,}" required="required">
		<span class="awcshm-amount-currency"><?php echo esc_html($currency ? $currency : esc_html__('Tomans', 'alopeyk-shipping-for-woocommerce')); ?></span>
	</label>
	<?php foreach ( $params as $name => $value ) { ?>
	<input type="hidden" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($value); ?>">
	<?php } ?>
	<button type="submit" class="awcshm-hidden"></button>
</form>

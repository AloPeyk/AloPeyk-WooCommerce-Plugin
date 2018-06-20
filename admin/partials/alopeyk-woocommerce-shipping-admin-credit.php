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
<form class="awcshm-add-credit-form" target="_blank" method="GET" action="<?php echo $action; ?>">
	<?php if ( isset( $data['amounts'] ) && $amounts = $data['amounts'] ) { ?>
	<div class="awcshm-amounts-container">
		<?php
			foreach ( $amounts as $amount ) {
				$normalized_amount = Alopeyk_WooCommerce_Shipping_Common::normalize_price( $amount * 10 );
		?>
		<button type="button" class="button awcshm-amount-button" data-credit-amount="<?php echo $amount; ?>"><?php echo wc_price( $normalized_amount ); ?></button>
		<?php
			}
		?>
	</div>
	<?php } ?>
	<label class="awcshm-amount-input-container">
		<input type="text" name="amount" value="<?php echo isset( $data['amount'] ) ? $data['amount'] / 10 : ''; ?>" class="awcshm-amount-input awcshm-price-input" autofocus pattern="\d{3,}" required="required">
		<span class="awcshm-amount-currency"><?php echo $currency ? $currency : __( 'Tomans', 'alopeyk-woocommerce-shipping' ); ?></span>
	</label>
	<?php foreach ( $params as $name => $value ) { ?>
	<input type="hidden" name="<?php echo $name ?>" value="<?php echo $value; ?>">
	<?php } ?>
	<button type="submit" class="awcshm-hidden"></button>
</form>

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
<form class="awcshm-add-coupon-form">
	<input type="text" name="coupon_code" class="awcshm-coupon-input" autofocus required="required" placeholder="<?php echo __( 'Gift Card Code', 'alopeyk-woocommerce-shipping' ); ?>">
	<button type="submit" class="awcshm-hidden"></button>
</form>
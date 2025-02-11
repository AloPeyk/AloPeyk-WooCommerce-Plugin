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
<form class="awcshm-add-discount-coupon-form">
	<input type="text" name="discount_coupon" class="awcshm-discount-coupon-input" autofocus required="required" placeholder="<?php echo esc_html__( 'Discount Coupon Code', 'alopeyk-shipping' ); ?>">
	<button type="submit" class="awcshm-hidden"></button>
</form>

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
$card = $data["object"];
?>
<form class="awcshm-add-customer-score-exchange-form" target="_blank" method="GET" action="<?php echo esc_url($action); ?>">
    <?php /* translators: %1$s: score, %2$s:" money */?>
	<?php echo sprintf(esc_html__('Are you sure you want to buy a %1$s worthing %2$s ?', 'alopeyk-shipping-for-woocommerce'), esc_html($card->title), esc_html($card->subtitle)); ?>
	<input type="hidden" name="product-id" value="<?php echo esc_attr($card->id); ?>">
	<button type="submit" class="awcshm-hidden"></button>
</form>
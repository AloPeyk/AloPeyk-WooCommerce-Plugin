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
<form class="awcshm-add-customer-score-exchange-form" target="_blank" method="GET" action="<?php echo $action; ?>">
	<?php echo sprintf( __( 'Are you sure you want to buy a %s worthing %s?', 'alopeyk-woocommerce-shipping' ), $card->title, $card->subtitle ); ?>
	<input type="hidden" name="product-id" value="<?php echo $card->id; ?>">
	<button type="submit" class="awcshm-hidden"></button>
</form>
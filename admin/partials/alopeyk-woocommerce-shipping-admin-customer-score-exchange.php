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

$data  = $this->vars;
$cards = array();
foreach ( $data as $card ) {
	if ( count( $cards ) >= 4 ) {
		break;
	}
	$cards[] = $card;
}
?>
<form class="awcshm-customer-score-exchange-form" target="_blank" method="GET" action="<?php echo esc_url($action); ?>">
	<?php if ( count( $cards ) ) { ?>
	<div class="awcshm-customer-score-exchange-container">
		<?php
			foreach ( $cards as $card ) {
		?>
		<div data-id="<?php echo esc_attr($card->id); ?>" class="awcshm-customer-score-exchange-card">
				<div class="loyalty-product-image" style="background-image: url('<?php echo esc_url($card->cover_image->url); ?>'); width:<?php echo esc_attr($card->image->width); ?>px; height:<?php echo esc_attr($card->image->height); ?>px">
				<!--This image varies for each order and is sourced from the official Alopeyk API.-->
				<img src="<?php echo esc_url($card->image->url); ?>" />
				</div>
				<div class="loyalty-product-text">
					<p><?php echo esc_html__('Points required', 'alopeyk-shipping-for-woocommerce') . ' : ' . esc_html($card->price_score); ?></p>
				</div>
		</div>
		<?php
			}
		?>
	</div>

	<?php } ?>
	<button type="submit" class="awcshm-hidden"></button>
</form>
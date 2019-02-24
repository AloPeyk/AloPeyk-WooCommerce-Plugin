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
<form class="awcshm-customer-score-exchange-form" target="_blank" method="GET" action="<?php echo $action; ?>">
	<?php if ( count( $cards ) ) { ?>
	<div class="awcshm-customer-score-exchange-container">
		<?php
			foreach ( $cards as $card ) {
		?>
		<div data-id="<?php echo $card->id; ?>" class="awcshm-customer-score-exchange-card">
				<div class="loyalty-product-image" style="background-image: url('<?php echo $card->cover_image->url; ?>'); width:<?php echo $card->image->width; ?>px; height:<?php echo $card->image->height; ?>px">
					<img src="<?php echo $card->image->url; ?>" />
				</div>
				<div class="loyalty-product-text">
					<p><?php echo __( 'Points required', 'alopeyk-woocommerce-shipping' ) . ' : ' . $card->price_score; ?></p>
				</div>
		</div>
		<?php
			}
		?>
	</div>

	<?php } ?>
	<button type="submit" class="awcshm-hidden"></button>
</form>
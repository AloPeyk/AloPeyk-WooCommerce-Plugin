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
$user_data = isset( $data['user_data'] ) ? $data['user_data'] : null;
if ( $user_data ) {
	$avatar = strpos( $user_data->abs_avatar->url, '?' ) !== false ? explode( '?', $user_data->abs_avatar->url ) : array( $user_data->abs_avatar->url );
	$avatar = $avatar[0];
?>
<div class="awcshm-credit-widget-wrapper">
	<div class="awcshm-credit-widget">
		<figure class="awcshm-courier-avatar" style="background-image: url( '<?php echo $avatar; ?>' );"></figure>
		<span class="awcshm-credit-widget-name"><?php echo $user_data->firstname . ' ' . $user_data->lastname; ?></span>
		<?php if ( isset( $data['user_credit'] ) && ! is_null( $data['user_credit'] ) ) { ?>
		<strong class="awcshm-credit-widget-credit"><?php echo wc_price( $data['user_credit'] ); ?></strong>
		<?php } ?>
		<div class="awcshm-credit-widget-actions">
			<button type="button" class="button button-primary awcshm-credit-modal-toggler"><?php echo __( 'Add Alopeyk Credit', 'alopeyk-woocommerce-shipping' ); ?></button>
			<button type="button" class="button button-primary awcshm-coupon-modal-toggler"><?php echo __( 'Add Coupon', 'alopeyk-woocommerce-shipping' ); ?></button>
		</div>
	</div>
</div>
<?php
	}
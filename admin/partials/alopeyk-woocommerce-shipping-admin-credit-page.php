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
	$avatar  = strpos( $user_data->avatar->url, '?' ) !== false ? explode( '?', $user_data->avatar->url ) : array( $user_data->avatar->url );
	$avatar  = $avatar[0];
	$api_url = explode( '/', $data['api_url'] );
	array_pop( $api_url );
	$api_url = implode('/', $api_url);
	$avatar  = $api_url . $avatar;
?>

<div class="awcshm-credit-widget-container">
	<div class="avatar">
		<span style="background-image:url('<?php echo $avatar; ?>')"></span>
	</div>
	<span class="awcshm-credit-widget-name">
		<?php echo $user_data->firstname . ' ' . $user_data->lastname; ?>
	</span>
	<span class="awcshm-credit-widget-id">
		<?php echo __( 'ID Number', 'alopeyk-woocommerce-shipping' ) . ' : ' . $user_data->id; ?>
	</span>
	<hr />
	<?php if ( isset( $data['user_credit'] ) && ! is_null( $data['user_credit'] ) ) { ?>
		<span class="awcshm-credit-widget-credit">
			<?php echo __( 'My Credit', 'alopeyk-woocommerce-shipping' ) . ' : <span class="awcshm-credit-widget-number">' . number_format($data['user_credit'] / 10) . ' ' . __('Tomans', 'alopeyk-woocommerce-shipping') ?></span>
		</span>
	<?php } ?>
	<div class="awcshm-credit-widget-actions">
		<button type="button" class="button button-primary awcshm-credit-modal-toggler"><?php echo __( 'Increase credit', 'alopeyk-woocommerce-shipping' ); ?></button>
	</div>
	<div class="awcshm-credit-widget-actions awcshm-credit-widget-coupon">
		<button type="button" class="button awcshm-coupon-modal-toggler"><?php echo __( 'Charge account with gift card', 'alopeyk-woocommerce-shipping' ); ?></button>
	</div>
	<?php if ( isset( $data['user_credit'] ) && ! is_null( $data['user_credit'] ) ) { ?>
		<span class="awcshm-credit-widget-score">
			<?php echo __( 'My Score', 'alopeyk-woocommerce-shipping' ) . ' : <span class="awcshm-credit-widget-number">' . number_format( $user_data->score ); ?></span>
		</span>
	<?php } ?>
	<div class="awcshm-credit-widget-actions awcshm-score-exchange">
		<button type="button" class="button awcshm-customer-score-exchange-modal-toggler"><?php echo __( 'Convert Alopeyk Scores to Credit', 'alopeyk-woocommerce-shipping' ); ?></button>
	</div>
</div>
<?php
	}
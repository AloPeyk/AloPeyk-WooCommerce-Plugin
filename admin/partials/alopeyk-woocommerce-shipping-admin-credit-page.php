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

	if(empty($avatar)){
		$avatar = plugin_dir_url( __DIR__ ).'img/avatar.svg';
	}else{
	$api_url = explode( '/', $data['api_url'] );
	array_pop( $api_url );
	$api_url = implode('/', $api_url);
	$avatar  = $api_url . $avatar;
	}
?>

<div class="awcshm-credit-widget-container">
	<div class="avatar">
		<span style="background-image:url('<?php echo esc_url($avatar); ?>')"></span>
	</div>
	<span class="awcshm-credit-widget-name">
		<?php echo esc_html($user_data->firstname) . ' ' . esc_html($user_data->lastname); ?>
	</span>
	<span class="awcshm-credit-widget-id">
		<?php echo esc_html__('ID Number', 'alopeyk-shipping-for-woocommerce') . ' : ' . esc_html($user_data->id); ?>
	</span>
	<hr />
	<?php if ( isset( $data['user_credit'] ) && ! is_null( $data['user_credit'] ) ) { ?>
		<span class="awcshm-credit-widget-credit">
			<?php echo esc_html__( 'My Credit', 'alopeyk-shipping-for-woocommerce' ) . ' : <span class="awcshm-credit-widget-number">' . number_format($data['user_credit'] / 10) . ' ' . esc_html__('Tomans', 'alopeyk-shipping-for-woocommerce') ?></span>
		</span>
	<?php } ?>
	<div class="awcshm-credit-widget-actions">
		<button type="button" class="button button-primary awcshm-credit-modal-toggler"><?php echo esc_html__( 'Increase credit', 'alopeyk-shipping-for-woocommerce' ); ?></button>
	</div>
	<div class="awcshm-credit-widget-actions awcshm-credit-widget-coupon">
		<button type="button" class="button awcshm-coupon-modal-toggler"><?php echo esc_html__( 'Charge account with gift card', 'alopeyk-shipping-for-woocommerce' ); ?></button>
	</div>
	<?php if ( isset( $data['user_credit'] ) && ! is_null( $data['user_credit'] ) ) { ?>
		<span class="awcshm-credit-widget-score">
			<?php echo esc_html__( 'My Score', 'alopeyk-shipping-for-woocommerce' ) . ' : <span class="awcshm-credit-widget-number">' . number_format( $user_data->score ); ?></span>
		</span>
	<?php } ?>
	<div class="awcshm-credit-widget-actions awcshm-score-exchange">
		<button type="button" class="button awcshm-customer-score-exchange-modal-toggler"><?php echo esc_html__( 'Convert Alopeyk Scores to Credit', 'alopeyk-shipping-for-woocommerce' ); ?></button>
	</div>
</div>
<?php
	}
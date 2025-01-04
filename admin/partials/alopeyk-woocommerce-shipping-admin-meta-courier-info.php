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
$courier_info = isset( $data['order_data'] ) && isset( $data['order_data']->courier_info ) ? $data['order_data']->courier_info : null;
if ( $courier_info ) {
	global $post;
	$transport_type = $data['order_data']->transport_type;
	$avatar = strpos( $courier_info->abs_avatar->url, '?' ) !== false ? explode( '?', $courier_info->abs_avatar->url ) : array( $courier_info->abs_avatar->url );
	$avatar = $avatar[0];
	if(empty($avatar)){
		$avatar = plugin_dir_url( __DIR__ ).'img/avatar.png';
	}
	$helpers = new Alopeyk_WooCommerce_Shipping_Common();
?>
<ul>
	<li class="wide awcshm-meta-box-content-container">
		<div class="awcshm-courier-info-container">
			<figure class="awcshm-courier-avatar" style="background-image: url('<?php echo esc_url($avatar); ?>');"></figure>
			<div class="awchm-courier-info">
				<div>
					<span class="awcshm-meta"><?php echo esc_html__( 'Rate', 'alopeyk-shipping-for-woocommerce' ) . ': ' . number_format( $courier_info->rates_avg, 2 ) . ' ' . esc_html__( 'out of', 'alopeyk-shipping-for-woocommerce' ) . ' 5'; ?></span>
				</div>
				<div>
					<strong><?php echo esc_html($courier_info->firstname . ' ' . $courier_info->lastname); ?></strong>
				</div>
				<div>
					<strong><a href="tel:<?php echo esc_html($courier_info->phone); ?>"><?php echo esc_html($courier_info->phone); ?></a></strong>
				</div>
				<div class="awcshm-courier-plate type--<?php echo esc_attr($transport_type); ?>">
					<?php
						$plate_number = $courier_info->plate_number;
						if ( $transport_type == 'motorbike' ) {
							$pa = substr( $plate_number, 0, 3 );
							$pb = substr( $plate_number, 3, 5 );
					?>
					<span class="awcshm-courier-plate-component component--a"><?php echo esc_html($helpers->convert_numbers($pa)); ?></span>
					<span class="awcshm-courier-plate-component component--b"><?php echo esc_html($helpers->convert_numbers($pb)); ?></span>
					<?php
						} else {
							$pa = mb_substr( $plate_number, 0, 2 );
							$pb = mb_substr( $plate_number, 2, 1 );
							$pc = mb_substr( $plate_number, 3, 3 );
							$pd = mb_substr( $plate_number, 6, 2 );
					?>
					<span class="awcshm-courier-plate-component component--a"><?php echo esc_html($helpers->convert_numbers($pa)); ?></span>
					<span class="awcshm-courier-plate-component component--b"><?php echo esc_html($pb); ?></span>
					<span class="awcshm-courier-plate-component component--c"><?php echo esc_html($helpers->convert_numbers($pc)); ?></span>
					<span class="awcshm-courier-plate-component component--d"><?php echo esc_html($helpers->convert_numbers($pd)); ?></span>
					<?php
						}
					?>
				</div>
			</div>
		</div>
	</li>
</ul>
<?php
	if ( $helpers->can_be_rated( $data['order_data'] ) ) {
?>
<div class="wide awcshm-meta-box-actions-container">
	<button type="button" class="button button-primary awcshm-rate-modal-toggler awcshm-full-width" data-order-id="<?php echo esc_attr($post->ID); ?>"><?php echo esc_html__('Rate this courier', 'alopeyk-shipping-for-woocommerce'); ?></button>
</div>
<?php
	}
}
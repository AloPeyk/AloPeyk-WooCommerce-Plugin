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
	$helpers = new Alopeyk_WooCommerce_Shipping_Common();
?>
<ul>
	<li class="wide awcshm-meta-box-content-container">
		<div class="awcshm-courier-info-container">
			<figure class="awcshm-courier-avatar" style="background-image: url( '<?php echo $avatar; ?>' );"></figure>
			<div class="awchm-courier-info">
				<div>
					<span class="awcshm-meta"><?php echo __( 'Rate', 'alopeyk-woocommerce-shipping' ) . ': ' . number_format( $courier_info->rates_avg, 2 ) . ' ' . __( 'out of', 'alopeyk-woocommerce-shipping' ) . ' 5'; ?></span>
				</div>
				<div>
					<strong><?php echo $courier_info->firstname . ' ' . $courier_info->lastname; ?></strong>
				</div>
				<div>
					<strong><a href="tel:<?php echo $courier_info->phone; ?>"><?php echo $courier_info->phone; ?></a></strong>
				</div>
				<div class="awcshm-courier-plate type--<?php echo $transport_type; ?>">
					<?php
						$plate_number = $courier_info->plate_number;
						if ( $transport_type == 'motorbike' ) {
							$pa = substr( $plate_number, 0, 3 );
							$pb = substr( $plate_number, 3, 5 );
					?>
					<span class="awcshm-courier-plate-component component--a"><?php echo $helpers->convert_numbers_to_persion( $pa ); ?></span>
					<span class="awcshm-courier-plate-component component--b"><?php echo $helpers->convert_numbers_to_persion( $pb ); ?></span>
					<?php
						} else {
							$pa = mb_substr( $plate_number, 0, 2 );
							$pb = mb_substr( $plate_number, 2, 1 );
							$pc = mb_substr( $plate_number, 3, 3 );
							$pd = mb_substr( $plate_number, 6, 2 );
					?>
					<span class="awcshm-courier-plate-component component--a"><?php echo $helpers->convert_numbers_to_persion( $pa ); ?></span>
					<span class="awcshm-courier-plate-component component--b"><?php echo $pb; ?></span>
					<span class="awcshm-courier-plate-component component--c"><?php echo $helpers->convert_numbers_to_persion( $pc ); ?></span>
					<span class="awcshm-courier-plate-component component--d"><?php echo $helpers->convert_numbers_to_persion( $pd ); ?></span>
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
	<button type="button" class="button button-primary awcshm-rate-modal-toggler awcshm-full-width" data-order-id="<?php echo $post->ID; ?>"><?php echo __( 'Rate this courier', 'alopeyk-woocommerce-shipping' ); ?></button>
</div>
<?php
	}
}
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
$order_data = isset( $data['order_data'] ) ? $data['order_data'] : null;
if ( $order_data ) {
	$addresses = isset( $order_data->addresses ) ? $order_data->addresses : array();
	$helpers = new Alopeyk_WooCommerce_Shipping_Common();
	$address_delimiter = __( ',', 'alopeyk-shipping-for-woocommerce' ) . ' ';
?>
<ul>
	<li class="wide awcshm-meta-box-content-container">
		<?php if ( $order_data->created_at ) { ?>
		<div class="postbox awcshm-address-info-box">
			<div class="awcshm-address-info-title">
				<span><?php echo __( 'Order Created', 'alopeyk-shipping-for-woocommerce' ); ?></span>
				<span><?php echo date_i18n( 'j F Y (g:i A)', strtotime( $order_data->created_at ) ) ?></span>
			</div>
		</div>
		<?php } ?>
		<?php if ( $order_data->scheduled_at ) { ?>
		<div class="postbox awcshm-address-info-box">
			<div class="awcshm-address-info-title">
				<span><?php echo __( 'Order Scheduled', 'alopeyk-shipping-for-woocommerce' ); ?></span>
				<span><?php echo date_i18n( 'j F Y (g:i A)', strtotime( $order_data->scheduled_at ) ) ?></span>
			</div>
		</div>
		<?php } ?>
		<?php if ( $order_data->accepted_at ) { ?>
		<div class="postbox awcshm-address-info-box">
			<div class="awcshm-address-info-title">
				<span><?php echo __( 'Order Accepted', 'alopeyk-shipping-for-woocommerce' ); ?></span>
				<span><?php echo date_i18n( 'j F Y (g:i A)', strtotime( $order_data->accepted_at ) ) ?></span>
			</div>
		</div>
		<?php } ?>
		<?php foreach ( $addresses as $key => $address ) { ?>
		<div class="postbox awcshm-address-info-box">
			<div class="awcshm-address-info-title">
				<?php if ( $helpers->is_active_address( $order_data, $address ) ) { ?>
				<span><?php echo $helpers->get_order_address_status( $order_data ); ?></span>
				<img src="<?php echo $helpers->get_loader_url(); ?>">
				<?php } else if ( $address->status == 'handled' ) { ?>
				<span><?php echo sprintf( ( $address->type == 'return' ? __( 'Courier returned to %s.', 'alopeyk-shipping-for-woocommerce' ) : __( 'Courier reached %s.', 'alopeyk-shipping-for-woocommerce' ) ), ( in_array( $address->type, array( 'origin', 'return' ) ) ? __( 'origin', 'alopeyk-shipping-for-woocommerce' ) : __( 'destination', 'alopeyk-shipping-for-woocommerce' ) . ( count( $order_data->addresses ) < 3 || ( count( $order_data->addresses ) == 3 && $order_data->has_return ) ? '' : ' ' . $key ) ) ); ?></span>
				<span><?php echo date_i18n( 'j F Y (g:i A)', strtotime( $address->handled_at ) ) ?></span>
				<?php } else { ?>
				<span><?php echo in_array( $address->type, array( 'origin', 'return' ) ) ? __( 'origin', 'alopeyk-shipping-for-woocommerce' ) : __( 'destination', 'alopeyk-shipping-for-woocommerce' ) . ( count( $order_data->addresses ) < 3 || ( count( $order_data->addresses ) == 3 && $order_data->has_return ) ? '' : ' ' . $key ); ?></span>
				<?php } ?>
			</div>
			<div class="awcshm-address-info-inside">
				<ul class="awcshm-address-info-items">
					<?php if ( ! empty( $address->address ) ) { ?>
					<li>
						<strong><?php echo __( 'Address', 'alopeyk-shipping-for-woocommerce' ); ?>: </strong>
						<?php
							$address_parts = array( str_replace( $address_delimiter . ' ', $address_delimiter, str_replace( array( ',', '،' ), $address_delimiter, $address->address ) ) );
							if ( ! empty( $address->unit ) ) {
								$address_parts[] = __( 'Unit', 'alopeyk-shipping-for-woocommerce' ) . ' ' . $address->unit;
							}
							if ( ! empty( $address->number ) ) {
								$address_parts[] = __( 'Plaque', 'alopeyk-shipping-for-woocommerce' ) . ' ' . $address->number;
							}
						?>
						<span><?php echo implode( $address_delimiter, $address_parts ); ?></span>
					</li>
					<?php } ?>
					<?php if ( ! empty( $address->description ) ) { ?>
					<li>
						<strong><?php echo __( 'Description', 'alopeyk-shipping-for-woocommerce' ); ?>: </strong>
						<span><?php echo str_replace( 'nn','<br>',$address->description ); ?></span>
					</li>
					<?php } ?>
					<?php if ( ! empty( $address->person_fullname ) ) { ?>
					<li>
						<strong><?php echo __( 'Name', 'alopeyk-shipping-for-woocommerce' ); ?>: </strong>
						<span><?php echo $address->person_fullname; ?></span>
					</li>
					<?php } ?>
					<?php if ( ! empty( $address->person_phone ) ) { ?>
					<li>
						<strong><?php echo __( 'Phone', 'alopeyk-shipping-for-woocommerce' ); ?>: </strong>
						<span><?php echo $address->person_phone; ?></span>
					</li>
					<?php } ?>
					<?php if ( ! empty( $address->signed_by ) ) { ?>
					<li>
						<strong><?php echo __( 'Signed By', 'alopeyk-shipping-for-woocommerce' ); ?>: </strong>
						<span><?php echo $address->signed_by; ?></span>
					</li>
					<?php } ?>
				</ul>
				<?php if ( $address->signed_by && $address->signature && isset( $address->signature->url ) && $address->status == 'handled' && $address->type != 'origin' ) { ?>
				<div align="center">
					<img src="<?php echo $helpers->get_signature_url( $address->signature->url ); ?>" alt="<?php echo __( 'Signature', 'alopeyk-shipping-for-woocommerce' ); ?>" title="<?php echo $address->signed_by; ?>" class="awcshm-address-signature">
				</div>
				<?php } ?>
			</div>
		</div>
		<?php } ?>
		<?php if ( $order_data->status == 'deleted' ) { ?>
		<div class="postbox awcshm-address-info-box">
			<div class="awcshm-address-info-title">
				<span><?php echo __( 'Order Deleted', 'alopeyk-shipping-for-woocommerce' ); ?></span>
				<span><?php echo date_i18n( 'j F Y (g:i A)', strtotime( $order_data->updated_at ) ) ?></span>
			</div>
		</div>
		<?php } ?>
		<?php if ( $order_data->status == 'cancelled' ) { ?>
		<div class="postbox awcshm-address-info-box">
			<div class="awcshm-address-info-title">
				<span><?php echo __( 'Order Canceled', 'alopeyk-shipping-for-woocommerce' ); ?></span>
				<span><?php echo date_i18n( 'j F Y (g:i A)', strtotime( $order_data->updated_at ) ) ?></span>
			</div>
		</div>
		<?php } ?>
		<?php if ( $order_data->status == 'expired' ) { ?>
		<div class="postbox awcshm-address-info-box">
			<div class="awcshm-address-info-title">
				<span><?php echo __( 'Order Expired', 'alopeyk-shipping-for-woocommerce' ); ?></span>
				<span><?php echo date_i18n( 'j F Y (g:i A)', strtotime( $order_data->updated_at ) ) ?></span>
			</div>
		</div>
		<?php } ?>
	</li>
</ul>
<?php
	}
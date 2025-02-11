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
	$address_delimiter = esc_html__( ',', 'alopeyk-shipping' ) . ' ';
?>
<ul>
	<li class="wide awcshm-meta-box-content-container">
		<?php if ( $order_data->created_at ) { ?>
		<div class="postbox awcshm-address-info-box">
			<div class="awcshm-address-info-title">
				<span><?php echo esc_html__( 'Order Created', 'alopeyk-shipping' ); ?></span>
				<span><?php echo esc_html(wp_date('j F Y (g:i A)', strtotime($order_data->created_at))); ?></span>
			</div>
		</div>
		<?php } ?>
		<?php if ( $order_data->scheduled_at ) { ?>
		<div class="postbox awcshm-address-info-box">
			<div class="awcshm-address-info-title">
				<span><?php echo esc_html__( 'Order Scheduled', 'alopeyk-shipping' ); ?></span>
				<span><?php echo esc_html(wp_date('j F Y (g:i A)', strtotime($order_data->scheduled_at))); ?></span>
			</div>
		</div>
		<?php } ?>
		<?php if ( $order_data->accepted_at ) { ?>
		<div class="postbox awcshm-address-info-box">
			<div class="awcshm-address-info-title">
				<span><?php echo esc_html__( 'Order Accepted', 'alopeyk-shipping' ); ?></span>
				<span><?php echo esc_html(wp_date('j F Y (g:i A)', strtotime($order_data->accepted_at))); ?></span>
			</div>
		</div>
		<?php } ?>
		<?php foreach ( $addresses as $key => $address ) { ?>
		<div class="postbox awcshm-address-info-box">
			<div class="awcshm-address-info-title">
				<?php if ( $helpers->is_active_address( $order_data, $address ) ) { ?>
				<span><?php echo esc_html($helpers->get_order_address_status($order_data)); ?></span>
				<!--This image is of the main WordPress files located in the wp-includes folder.-->
				<img src="<?php echo esc_url(includes_url('images/spinner.gif')); ?>" alt="<?php esc_attr_e('Loading...', 'alopeyk-shipping'); ?>">
				<?php } else if ( $address->status == 'handled' ) { ?>
				<span>
					<?php 
						$message = ( $address->type == 'return' ) 
						/* translators: %s: location */
						? esc_html__('Courier returned to %s.', 'alopeyk-shipping') 
						/* translators: %s: location */
						: esc_html__('Courier reached %s.', 'alopeyk-shipping'); 

						$location = ( in_array( $address->type, array( 'origin', 'return' ) ) ) 
						/* translators: %s: location */
						? esc_html__( 'origin', 'alopeyk-shipping' ) 
						/* translators: %s: location */
						: esc_html__( 'destination', 'alopeyk-shipping' ) . ( count( $order_data->addresses ) < 3 || ( count( $order_data->addresses ) == 3 && $order_data->has_return ) ? '' : ' ' . $key );

						echo esc_html( sprintf( $message, $location ) );

					?>
				</span>
				<span><?php echo esc_html(wp_date('j F Y (g:i A)', strtotime($address->handled_at))); ?></span>
				<?php } else { ?>
				<span><?php echo esc_html(in_array($address->type, array('origin', 'return')) ? esc_html__('origin', 'alopeyk-shipping') : esc_html__('destination', 'alopeyk-shipping') . (count($order_data->addresses) < 3 || (count($order_data->addresses) == 3 && $order_data->has_return) ? '' : ' ' . esc_html($key))); ?></span>
				<?php } ?>
			</div>
			<div class="awcshm-address-info-inside">
				<ul class="awcshm-address-info-items">
					<?php if ( ! empty( $address->address ) ) { ?>
					<li>
						<strong><?php echo esc_html__( 'Address', 'alopeyk-shipping' ); ?>: </strong>
						<?php
							$address_parts = array( str_replace( $address_delimiter . ' ', $address_delimiter, str_replace( array( ',', '،' ), $address_delimiter, $address->address ) ) );
							if ( ! empty( $address->unit ) ) {
								$address_parts[] = esc_html__( 'Unit', 'alopeyk-shipping' ) . ' ' . $address->unit;
							}
							if ( ! empty( $address->number ) ) {
								$address_parts[] = esc_html__( 'Plaque', 'alopeyk-shipping' ) . ' ' . $address->number;
							}
						?>
						<span><?php echo esc_html(implode($address_delimiter, $address_parts)); ?></span>
					</li>
					<?php } ?>
					<?php if ( ! empty( $address->description ) ) { ?>
					<li>
						<strong><?php echo esc_html__( 'Description', 'alopeyk-shipping' ); ?>: </strong>
						<span><?php echo wp_kses_post(str_replace('nn', '<br>', $address->description)); ?></span>
					</li>
					<?php } ?>
					<?php if ( ! empty( $address->person_fullname ) ) { ?>
					<li>
						<strong><?php echo esc_html__( 'Name', 'alopeyk-shipping' ); ?>: </strong>
						<span><?php echo esc_html($address->person_fullname); ?></span>
					</li>
					<?php } ?>
					<?php if ( ! empty( $address->person_phone ) ) { ?>
					<li>
						<strong><?php echo esc_html__( 'Phone', 'alopeyk-shipping' ); ?>: </strong>
						<span><?php echo esc_html($address->person_phone); ?></span>
					</li>
					<?php } ?>
					<?php if ( ! empty( $address->signed_by ) ) { ?>
					<li>
						<strong><?php echo esc_html__( 'Signed By', 'alopeyk-shipping' ); ?>: </strong>
						<span><?php echo esc_html($address->signed_by); ?></span>
					</li>
					<?php } ?>
				</ul>
				<?php if ( $address->signed_by && $address->signature && isset( $address->signature->url ) && $address->status == 'handled' && $address->type != 'origin' ) { ?>
				<div align="center">
					<!--This image varies for each order and is sourced from the official Alopeyk API-->
					<img src="<?php echo esc_url($helpers->get_signature_url($address->signature->url)); ?>" alt="<?php echo esc_attr(__('Signature', 'alopeyk-shipping')); ?>" title="<?php echo esc_attr($address->signed_by); ?>" class="awcshm-address-signature">
				</div>
				<?php } ?>
			</div>
		</div>
		<?php } ?>
		<?php if ( $order_data->status == 'deleted' ) { ?>
		<div class="postbox awcshm-address-info-box">
			<div class="awcshm-address-info-title">
				<span><?php echo esc_html__( 'Order Deleted', 'alopeyk-shipping' ); ?></span>
				<span><?php echo esc_html(wp_date('j F Y (g:i A)', strtotime($order_data->updated_at))); ?></span>
			</div>
		</div>
		<?php } ?>
		<?php if ( $order_data->status == 'cancelled' ) { ?>
		<div class="postbox awcshm-address-info-box">
			<div class="awcshm-address-info-title">
				<span><?php echo esc_html__( 'Order Canceled', 'alopeyk-shipping' ); ?></span>
				<span><?php echo esc_html(wp_date('j F Y (g:i A)', strtotime($order_data->updated_at))); ?></span>
			</div>
		</div>
		<?php } ?>
		<?php if ( $order_data->status == 'expired' ) { ?>
		<div class="postbox awcshm-address-info-box">
			<div class="awcshm-address-info-title">
				<span><?php echo esc_html__( 'Order Expired', 'alopeyk-shipping' ); ?></span>
				<span><?php echo esc_html(wp_date('j F Y (g:i A)', strtotime($order_data->updated_at))); ?></span>
			</div>
		</div>
		<?php } ?>
	</li>
</ul>
<?php
	}
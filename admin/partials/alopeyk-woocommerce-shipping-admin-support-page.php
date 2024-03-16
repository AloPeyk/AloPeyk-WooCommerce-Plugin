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
?>
<div class="awcshm-support-container">
	<input type="checkbox" id="awcshm-support-chat-toggler" class="awcshm-outbound">
	<label for="awcshm-support-chat-toggler" class="awcshm-support-chat-open">
		<i class="dashicons dashicons-format-status"></i>
	</label>
	<div class="awcshm-support-content">
		<div class="awcshm-support-content-items-wrapper">
			<div class="awcshm-support-content-item">
				<div class="awcshm-support-content-item-inner">
					<div class="awcshm-support-content-item-header">
						<i class="dashicons dashicons-admin-generic"></i>
					</div>
					<div class="awcshm-support-content-item-body">
						<div class="awcshm-support-content-item-body-inner">
							<p><?php echo __( 'You can access all of your activity logs such as orders and transactions, and also edit your profile information via Alopeyk web dashboard.', 'alopeyk-woocommerce-shipping' ); ?></p>
						</div>
					</div>
					<div class="awcshm-support-content-item-footer">
						<a href="https://app.alopeyk.com" target="_blank" class="button button-primary"><?php echo __( 'Go to my dashboard', 'alopeyk-woocommerce-shipping' ); ?></a>
					</div>
				</div>
			</div>
			<?php if ( isset( $data['support_tel'] ) && $support_tel = $data['support_tel'] ) { ?>
			<div class="awcshm-support-content-item">
				<div class="awcshm-support-content-item-inner">
					<div class="awcshm-support-content-item-header">
						<i class="dashicons dashicons-phone"></i>
					</div>
					<div class="awcshm-support-content-item-body">
						<div class="awcshm-support-content-item-body-inner">
							<p><?php echo __( 'You can call our support team to get your questions answered just in time.', 'alopeyk-woocommerce-shipping' ); ?></p>
							<?php echo isset( $data['is_api_user'] ) && $data['is_api_user'] !== true ? '<p>' . $data['is_api_user'] . '</p>' : ''; ?>
						</div>
					</div>
					<div class="awcshm-support-content-item-footer">
						<a href="tel:<?php echo $support_tel; ?>" class="button button-primary"><?php echo sprintf( __( 'Call Support: %s', 'alopeyk-woocommerce-shipping' ), '<span class="awcshm-phone">' . $support_tel . '</span>' ); ?></a>
					</div>
				</div>
			</div>
			<?php } ?>
			<?php if ( isset( $data['dev_email'] ) && $dev_email = $data['dev_email'] ) { ?>
			<div class="awcshm-support-content-item">
				<div class="awcshm-support-content-item-inner">
					<div class="awcshm-support-content-item-header">
						<i class="dashicons dashicons-email"></i>
					</div>
					<div class="awcshm-support-content-item-body">
						<div class="awcshm-support-content-item-body-inner">
							<p>
								<?php
									echo __( 'Please let us know about bugs or UX problems you may encounter while working with this extension.', 'alopeyk-woocommerce-shipping' );
									if ( isset( $data['log_url'] ) && $data['log_url'] ) {
										echo ' ' . sprintf( __( 'Its better to attach a copy of <a href="%s" target="_blank">system logs</a> to your email.', 'alopeyk-woocommerce-shipping' ), $data['log_url'] );
									}
								?>
							</p>
						</div>
					</div>
					<div class="awcshm-support-content-item-footer">
						<a href="mailto:<?php echo $dev_email; ?>" class="button button-primary"><?php echo sprintf( __( 'Report a Bug: %s', 'alopeyk-woocommerce-shipping' ), $dev_email ); ?></a>
					</div>
				</div>
			</div>
			<?php } ?>
			<div class="awcshm-support-content-item">
				<div class="awcshm-support-content-item-inner">
					<div class="awcshm-support-content-item-header">
						<i class="dashicons dashicons-admin-plugins"></i>
					</div>
					<div class="awcshm-support-content-item-body">
						<div class="awcshm-support-content-item-body-inner">
							<p><?php echo __( 'Check our contact page on Alopeyk website to leave us reviews about this extension or find more available communication methods.', 'alopeyk-woocommerce-shipping' ); ?></p>
						</div>
					</div>
					<div class="awcshm-support-content-item-footer">
						<a href="https://alopeyk.com/contact" target="_blank" class="button button-primary"><?php echo __( 'Contact Us', 'alopeyk-woocommerce-shipping' ); ?></a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php if ( isset( $data['chat_url'] ) && $chat_url = $data['chat_url'] ) { ?>
	<div class="awcshm-support-sidebar">
		<label for="awcshm-support-chat-toggler" class="awcshm-support-chat-close">
			<i class="dashicons dashicons-no-alt"></i>
		</label>
		<iframe src="<?php echo $chat_url; ?>" frameBorder="0" seamless="seamless" scrolling="no" height="100%" width="100%" class="awcshm-support-sidebar-frame"></iframe>
	</div>
	<?php } ?>
</div>
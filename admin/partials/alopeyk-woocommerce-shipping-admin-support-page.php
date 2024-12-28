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
							<p><?php echo esc_html__( 'You can access all of your activity logs such as orders and transactions, and also edit your profile information via Alopeyk web dashboard.', 'alopeyk-shipping-for-woocommerce' ); ?></p>
						</div>
					</div>
					<div class="awcshm-support-content-item-footer">
						<a href="https://app.alopeyk.com" target="_blank" class="button button-primary"><?php echo esc_html__( 'Go to my dashboard', 'alopeyk-shipping-for-woocommerce' ); ?></a>
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
							<p><?php echo esc_html__( 'You can call our support team to get your questions answered just in time.', 'alopeyk-shipping-for-woocommerce' ); ?></p>
							<?php echo isset($data['is_api_user']) && $data['is_api_user'] !== true ? '<p>' . esc_html($data['is_api_user']) . '</p>' : ''; ?>
						</div>
					</div>
					<div class="awcshm-support-content-item-footer">
					<?php /* translators: %s: Support tell */?>
						<a href="tel:<?php echo esc_attr($support_tel); ?>" class="button button-primary"> <?php echo sprintf( esc_html__( 'Call Support: %s', 'alopeyk-shipping-for-woocommerce' ), '<span class="awcshm-phone">' . esc_html($support_tel) . '</span>' ); ?></a>
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
									echo esc_html__( 'Please let us know about bugs or UX problems you may encounter while working with this extension.', 'alopeyk-shipping-for-woocommerce' );
									if ( isset( $data['log_url'] ) && $data['log_url'] ) {
										/* translators: %s: URL log */
										echo ' ' . sprintf( esc_html__('It\'s better to attach a copy of <a href="%s" target="_blank">system logs</a> to your email.', 'alopeyk-shipping-for-woocommerce'), esc_url($data['log_url']) );
									}
								?>
							</p>
						</div>
					</div>
					<div class="awcshm-support-content-item-footer">
						<?php /* translators: %s: Email */ ?>
						<a href="mailto:<?php echo esc_url( 'mailto:' . $dev_email ); ?>" class="button button-primary"><?php echo sprintf( esc_html__( 'Report a Bug: %s', 'alopeyk-shipping-for-woocommerce' ), esc_html( $dev_email ) ); ?></a>
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
							<p><?php echo esc_html__( 'Check our contact page on Alopeyk website to leave us reviews about this extension or find more available communication methods.', 'alopeyk-shipping-for-woocommerce' ); ?></p>
						</div>
					</div>
					<div class="awcshm-support-content-item-footer">
						<a href="https://alopeyk.com/contact" target="_blank" class="button button-primary"><?php echo esc_html__( 'Contact Us', 'alopeyk-shipping-for-woocommerce' ); ?></a>
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
		<iframe src="<?php echo esc_url($chat_url); ?>" frameBorder="0" seamless="seamless" scrolling="no" height="100%" width="100%" class="awcshm-support-sidebar-frame"></iframe>
	</div>
	<?php } ?>
</div>
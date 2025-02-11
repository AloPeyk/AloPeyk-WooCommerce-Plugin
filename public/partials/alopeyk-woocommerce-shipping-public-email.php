<?php

/**
 *
 * @link       https://alopeyk.com
 * @since      1.0.0
 *
 * @package    Alopeyk_WooCommerce_Shipping
 * @subpackage Alopeyk_WooCommerce_Shipping/public/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$data         = $this->vars;
$tel          = isset( $data['tel'] ) ? $data['tel'] : null;
$extra        = isset( $data['extra'] ) ? $data['extra'] : null;
$message      = isset( $data['message'] ) ? $data['message'] : '';
$direction    = is_rtl() ? 'rtl' : 'ltr';
$campaign_url = isset( $data['campaign_url'] ) ? $data['campaign_url'] : 'https://alopeyk.com';

// Load colors
$color_blue       = '#1da5e1';
$color_blue_dark  = '#2f3742';
$color_gray       = '#95a5a6';
$color_gray_light = '#ECF0F1';
$color_white      = '#ffffff';
$text_color       = $color_gray;
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo esc_html__( 'Alopeyk', 'alopeyk-shipping' ); ?><?php echo ( isset( $data['title'] ) && $data['title'] && strlen( $data['title'] ) ) ? ' | ' . esc_html( $data['title'] ) : ''; ?></title>
		<meta name="charset" content="utf-8">
		<meta name="robots" content="noindex,nofollow">
		<meta name="format-detection" content="email=no">
		<meta name="format-detection" content="address=no">
		<meta name="format-detection" content="date=no">
		<meta name="format-detection" content="telephone=no">
		<meta name="viewport" content="width=device-width" initial-scale="1.0" user-scalable="yes">

	</head>
	<body class="alopeyk-email" bgcolor="#ECF0F1" leftmargin="0" marginheight="0" marginwidth="0" style="background:#ECF0F1;">
		<table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" align="center" style="min-width:680px;width:680px;background:#ffffff;" width="680">
			<tbody>
				<tr>
					<td width="680" class="wrapper-inner" style="width:680px;">
						<table align="center" border="0" cellpadding="0" cellspacing="0" style="min-width:642px;width:642px;" width="642">
							<tbody>
								<tr>
									<td align="center" valign="top" class="content-inner" style="width:680px;" width="680">
										<?php echo esc_html($message); ?>
									</td>
								</tr>
							</tbody>
						</table>
						<?php if ( $extra ) { ?>
						<table align="center" border="0" cellpadding="0" cellspacing="0" style="min-width:680px;width:680px;" width="680">
							<tbody>
								<tr>
									<td align="center" valign="top" width="680" style="width:680px;" width="680">
										<?php echo esc_html($extra); ?>
									</td>
								</tr>
							</tbody>
						</table>
						<?php } ?>
						<table align="center" border="0" cellpadding="0" cellspacing="0" style="min-width:642px;width:642px;" width="642">
							<tbody>
								<tr>
									<td align="center" style="min-width:642px;width:642px;" width="642">
										<table align="center" bgcolor="#2f3742" border="0" cellpadding="0" cellspacing="0" style="background:#2f3742;width:642px;" width="642">
											<tbody>
												<tr>
													<td align="center" class="footer-inner" width="100%">
														<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
															<tbody>
																<tr>
																	<td align="center" width="100%">
																		<a href="https://instagram.com/alopeyk" class="social-link">
																			<img alt="<?php echo esc_html__( 'Instagram', 'alopeyk-shipping' ); ?>" title="<?php echo esc_html__( 'Instagram', 'alopeyk-shipping' ); ?>" border="0" src="<?php plugin_dir_url( __DIR__ ) ?> public/img/instagram.png" width="32" style="width:32px;">
																		</a>
																		<a href="https://www.linkedin.com/company/alopeyk" class="social-link">
																			<img alt="<?php echo esc_html__( 'Linkedin', 'alopeyk-shipping' ); ?>" title="<?php echo esc_html__( 'Linkedin', 'alopeyk-shipping' ); ?>" border="0" src="<?php plugin_dir_url( __DIR__ ) ?> public/img/linkedin.png" width="32" style="width:32px;">
																		</a>
																		<a href="https://www.facebook.com/alopeyk/" class="social-link">
																			<img alt="<?php echo esc_html__( 'Facebook', 'alopeyk-shipping' ); ?>" title="<?php echo esc_html__( 'Facebook', 'alopeyk-shipping' ); ?>" border="0" src="<?php plugin_dir_url( __DIR__ ) ?> public/img/facebook.png" width="32" style="width:32px;">
																		</a>
																		<a href="https://twitter.com/alopeyk" class="social-link">
																			<img alt="<?php echo esc_html__( 'Twitter', 'alopeyk-shipping' ); ?>" title="<?php echo esc_html__( 'Twitter', 'alopeyk-shipping' ); ?>" border="0" src="<?php plugin_dir_url( __DIR__ ) ?> public/img/twitter.png" width="32" style="width:32px;">
																		</a>
																		<a href="<?php echo esc_url($campaign_url); ?>" class="social-link">
																			<img alt="<?php echo esc_html__( 'Website', 'alopeyk-shipping' ); ?>" title="<?php echo esc_html__( 'Website', 'alopeyk-shipping' ); ?>" border="0" src="<?php plugin_dir_url( __DIR__ ) ?> public/img/website.png" width="32" style="width:32px;">
																		</a>
																	</td>
																</tr>
															</tbody>
														</table>
														<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
															<tbody>
																<tr>
																	<td align="center" width="100%" class="footer-data">
																		<p class="footer-copyright"><?php echo esc_html__( '© Alopeyk all rights reserved', 'alopeyk-shipping' ); ?></p>
																		<p><span style="color:#ffffff;"><?php echo esc_html__( 'Licensed by the Tehran Pickup And Bike Delivery Union', 'alopeyk-shipping' ); ?></span></p>
																		<p><?php echo esc_html__( 'Alopeyk building, No.5, Vozara 14th, Shahid beheshti St., Tehran, I.R.Iran', 'alopeyk-shipping' ); ?></p>
																		<?php if ( $tel ) { ?>
																		<p class="ltr"><?php echo esc_html($tel); ?></p>
																		<?php } ?>
																	</td>
																</tr>
															</tbody>
														</table>
													</td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
	</body>
</html>
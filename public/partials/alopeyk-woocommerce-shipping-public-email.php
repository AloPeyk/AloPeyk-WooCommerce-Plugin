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

$data = $this->vars;
$tel = isset( $data['tel'] ) ? $data['tel'] : null;
$extra = isset( $data['extra'] ) ? $data['extra'] : null;
$message = isset( $data['message'] ) ? $data['message'] : '';
$direction = is_rtl() ? 'rtl' : 'ltr';
$campaign_url = isset( $data['campaign_url'] ) ? $data['campaign_url'] : 'https://alopeyk.com';
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo __( 'Alopeyk', 'alopeyk-woocommerce-shipping' ); ?><?php echo ( isset( $data['title'] ) && $data['title'] && strlen( $data['title'] ) ) ? ' | ' . $data['title'] : ''; ?></title>
		<meta name="charset" content="utf-8">
		<meta name="robots" content="noindex,nofollow">
		<meta name="format-detection" content="email=no">
		<meta name="format-detection" content="address=no">
		<meta name="format-detection" content="date=no">
		<meta name="format-detection" content="telephone=no">
		<meta name="viewport" content="width=device-width" initial-scale="1.0" user-scalable="yes">
		<link href="https://1160650779.rsc.cdn77.org/assets/css/iransans.css" rel="stylesheet" type="text/css">
	</head>
	<body bgcolor="#ECF0F1" leftmargin="0" marginheight="0" marginwidth="0" style="background:#ECF0F1;">
		<table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" align="center" style="min-width:680px;width:680px;background:#ffffff;" width="680">
			<tbody>
				<tr>
					<td width="680" class="wrapper-inner" style="width:680px;">
						<table align="center" border="0" cellpadding="0" cellspacing="0" style="min-width:642px;width:642px;" width="642">
							<tbody>
								<tr>
									<td align="center" valign="top" width="642" style="width:642px;">
										<a href="<?php echo $campaign_url; ?>" class="logo">
											<img src="https://bucket.mlcdn.com/a/243/243983/images/5b524ed08e12993b954c9744bfc423b6a6256b13.jpeg" width="130" height="127" border="0" style="max-width:130px;" alt="<?php echo __( 'Alopeyk', 'alopeyk-woocommerce-shipping' ); ?>" title="<?php echo __( 'Alopeyk', 'alopeyk-woocommerce-shipping' ); ?>">
										</a>
									</td>
								</tr>
								<tr>
									<td align="center" valign="top" class="content-inner" style="width:680px;" width="680">
										<?php echo $message; ?>
									</td>
								</tr>
							</tbody>
						</table>
						<?php if ( $extra ) { ?>
						<table align="center" border="0" cellpadding="0" cellspacing="0" style="min-width:680px;width:680px;" width="680">
							<tbody>
								<tr>
									<td align="center" valign="top" width="680" style="width:680px;" width="680">
										<?php echo $extra; ?>
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
																			<img alt="<?php echo __( 'Instagram', 'alopeyk-woocommerce-shipping' ); ?>" title="<?php echo __( 'Instagram', 'alopeyk-woocommerce-shipping' ); ?>" border="0" src="https://static.mailerlite.com/images/social-icons/new/set4/instagram.png" width="32" style="width:32px;">
																		</a>
																		<a href="https://www.linkedin.com/company/alopeyk" class="social-link">
																			<img alt="<?php echo __( 'Linkedin', 'alopeyk-woocommerce-shipping' ); ?>" title="<?php echo __( 'Linkedin', 'alopeyk-woocommerce-shipping' ); ?>" border="0" src="https://static.mailerlite.com/images/social-icons/new/set4/linkedin.png" width="32" style="width:32px;">
																		</a>
																		<a href="https://www.facebook.com/alopeyk/" class="social-link">
																			<img alt="<?php echo __( 'Facebook', 'alopeyk-woocommerce-shipping' ); ?>" title="<?php echo __( 'Facebook', 'alopeyk-woocommerce-shipping' ); ?>" border="0" src="https://static.mailerlite.com/images/social-icons/new/set4/facebook.png" width="32" style="width:32px;">
																		</a>
																		<a href="https://twitter.com/alopeyk" class="social-link">
																			<img alt="<?php echo __( 'Twitter', 'alopeyk-woocommerce-shipping' ); ?>" title="<?php echo __( 'Twitter', 'alopeyk-woocommerce-shipping' ); ?>" border="0" src="https://static.mailerlite.com/images/social-icons/new/set4/twitter.png" width="32" style="width:32px;">
																		</a>
																		<a href="<?php echo $campaign_url; ?>" class="social-link">
																			<img alt="<?php echo __( 'Website', 'alopeyk-woocommerce-shipping' ); ?>" title="<?php echo __( 'Website', 'alopeyk-woocommerce-shipping' ); ?>" border="0" src="https://static.mailerlite.com/images/social-icons/new/set4/website.png" width="32" style="width:32px;">
																		</a>
																	</td>
																</tr>
															</tbody>
														</table>
														<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
															<tbody>
																<tr>
																	<td align="center" width="100%" class="footer-data">
																		<p class="footer-copyright"><?php echo __( '© Alopeyk all rights reserved', 'alopeyk-woocommerce-shipping' ); ?></p>
																		<p><span style="color:#ffffff;"><?php echo __( 'Licensed by the Tehran Pickup And Bike Delivery Union', 'alopeyk-woocommerce-shipping' ); ?></span></p>
																		<p><?php echo __( 'Alopeyk building, No.5, Vozara 14th, Shahid beheshti St., Tehran, I.R.Iran', 'alopeyk-woocommerce-shipping' ); ?></p>
																		<?php if ( $tel ) { ?>
																		<p class="ltr"><?php echo $tel; ?></p>
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
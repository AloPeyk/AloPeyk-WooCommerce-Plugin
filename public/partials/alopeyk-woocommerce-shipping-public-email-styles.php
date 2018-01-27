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

// Load colors
$color_blue       = '#1da5e1';
$color_blue_dark  = '#2f3742';
$color_gray       = '#95a5a6';
$color_gray_light = '#ECF0F1';
$color_white      = '#ffffff';
$text_color       = $color_gray;
?>
* {
	direction: <?php echo is_rtl() ? 'rtl' : 'ltr' ?>;
	color: <?php echo $color_gray ?>;
	font-size: 13px;
	font-family: 'Iran Sans', IranSans, tahoma !important;
	line-height: 1.7;
	text-rendering: optimizeLegibility !important;
	-webkit-font-smoothing: antialiased !important;
	-moz-osx-font-smoothing: grayscale !important;
	font-smooth: always !important;
	-ms-text-size-adjust: 100% !important;
	-webkit-text-size-adjust: 100% !important;
}
body {
	margin: 0;
	padding: 0;
}
a {
	display: inline-block;
	vertical-align: middle;
	text-decoration: none;
	font-family: inherit !important;
	font-size: inherit !important;
	font-style: inherit !important;
	font-variant-caps: inherit !important;
	line-height: inherit !important;
	font-weight: 600 !important;
}
p {
	margin-top: 13px;
	margin-bottom: 13px;
	text-align: <?php echo is_rtl() ? 'right' : 'left' ?>;
}
img {
	margin: 0 !important;
	color: inherit !important;
	font: inherit !important;
	border: 0 none !important;
	white-space: nowrap;
	max-width: 100%;
	overflow: hidden;
	text-overflow: ellipsis;
}
a img {
	display: block;
}
a, b, strong {
	font-weight: 600;
}
.ltr {
	direction: ltr !important;
}
.wrapper-inner {
	padding-top: 16px;
	padding-bottom: 16px;
}
.content-inner {
	padding: 40px;
}
.footer-inner {
	padding: 30px 50px 30px 50px;
}
.footer-inner * {
	margin: 0;
	padding: 0;
	color: <?php echo $color_white ?>;
	text-align: center;
	font-size: 11px;
	line-height: 150%;
}
.footer-data > *,
.footer-inner  > * {
	margin: 0 0 5px;
}
.footer-data > *:last-child,
.footer-inner  > *:last-child {
	margin: 0 0 0 0;
}
.footer-copyright {
	margin: 10px 0 25px;
	font-size: 13px;
	font-weight: 600;
}
.logo {
	margin-top: 50px;
	font-size: 48px !important;
	color: <?php echo $color_blue ?> !important;
}
.button {
	margin: 0 auto;
	padding: 10px 30px;
	-webkit-border-radius: 5px !important;
	-moz-border-radius: 5px !important;
	border-radius: 5px !important;
	color: <?php echo $color_white ?>;
	background-color: <?php echo $color_blue ?>;
	font-size: 15px;
	line-height: 1;
	text-align: center;
}
.button-container {
	margin-top: 30px;
	text-align: center;
}
.social-link {
	margin: 0 4px;
}
<?php
/**
 * Customer processing order email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-processing-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates/Emails
 * @version 3.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

include(CUSTOM_BOTAKSIGN_PATH . "includes/email-templates/email_header.php");
$method = $order->get_shipping_method();
if($method=='Self-collection') {
    include(CUSTOM_BOTAKSIGN_PATH . "includes/email-templates/B1.php");
} else {
    include(CUSTOM_BOTAKSIGN_PATH . "includes/email-templates/A1.php");
}
include(CUSTOM_BOTAKSIGN_PATH . "includes/email-templates/email_footer.php");
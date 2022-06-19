<?php
/**
 * Customer refunded order email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-refunded-order.php.
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

defined( 'ABSPATH' ) || exit;

$email_button_title = "Order Refunded";
$email_button_color = "transparent linear-gradient(180deg, #1BCB3F 0%, #45D242 33%, #7BDB46 78%, #91DF48 100%) 0% 0% no-repeat padding-box";

include(CUSTOM_BOTAKSIGN_PATH . "includes/email-templates-new/email_header.php");
?>
<div style="margin-bottom: 25px;">
    <span class="info-title" style="display:block;font-size:17px !important; line-height: 20px; font-weight: 500; margin-bottom: 12px;"><?php printf( esc_html__( 'Hi %s,', 'woocommerce' ), esc_html( $order->get_billing_first_name() ) ); ?></span>
    <span class="info-subtext" style="font-size:14px !important; line-height: 24px; color:#231f20;">We have refunded your order <span style="font-weight: 500;">#<?php echo $order->get_id(); ?></span>. We will automatically credit the funds to your bank account / credit card. Please note that it might take up to 7 working days.</span>
</div>

<div style="display: flex; justify-content: center; width: 100%;">
    <div style="border-top-width: 2px; border-top-style: solid; border-top-color: #ECECEC; width: 200px;"></div>
</div>

<?php

include(CUSTOM_BOTAKSIGN_PATH . "includes/email-templates-new/email_footer.php");
?>
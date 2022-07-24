<?php 
$email_button_title = "Payment Failed";
$email_button_color = "#FF0000";
?>

<table id="header-logo" style="width:100%;padding-top:20px;border-collapse:collapse;margin-bottom:45px;">
    <tbody>
        <tr>
            <td align="left" style="width:50%;"><img class="logo" src="https://botaksign.com/wp-content/plugins/custom-botaksign/assets/images/logo.png" style="margin-left:0px;margin-top:0px;height: 56px; width: auto;"></td>
            <td align="right" style="width:50%;">
                <?php if($email_button_title && $email_button_color) {
                    echo '<button class="status-button" style="background: '. $email_button_color .';box-shadow: 0px 10px 20px #00000029; border: none; color: #fff; padding: 14px 40px; font-size: 22px; line-height: 28px; border-radius: 10px;">'. $email_button_title .'</button>';
                } ?>
            </td>
        </tr>
    </tbody>
</table>
<div style="margin-bottom: 25px;">
    <span class="info-title" style="display:block;font-size:17px; line-height: 20px; font-weight: 500; margin-bottom: 12px;"><?php printf( esc_html__( 'Hi %s,', 'woocommerce' ), esc_html( $order->get_billing_first_name() ) ); ?></span>
    <span class="info-subtext" style="font-size:14px !important; line-height: 24px; color:#231f20;">The order you have placed is not successful as payment has failed. If you’d like to proceed with this order, click <a href="<?php echo wc_get_page_permalink( 'myaccount' ).'view-order/'.$order_id; ?>" class="order-link-here" style="color: #1BCB3F; font-size: 15x;"> HERE</a> to make the payment again.</span>
</div>

<div style="display: flex; justify-content: center; width: 100%;">
    <div style="border-top-width: 2px; border-top-style: solid; border-top-color: #ECECEC; width: 200px;"></div>
</div>

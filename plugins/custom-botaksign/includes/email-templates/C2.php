<?php
if ($order) {
    $order_data = $order->get_data();
    $WCX_Order = new WPO\WC\PDF_Invoices\Compatibility\Order;
    $link_invoice = '';
    if (isset($WCX_Order)) {
        $link_invoice = str_replace('&amp;', '&', wp_nonce_url(admin_url("admin-ajax.php?action=generate_wpo_wcpdf&document_type=invoice&order_ids=" . $WCX_Order::get_id($order)), 'generate_wpo_wcpdf'));
    }
    $email_button_title = "Order Processed";
    $email_button_color = "transparent linear-gradient(180deg, #1BCB3F 0%, #45D242 33%, #7BDB46 78%, #91DF48 100%) 0% 0% no-repeat padding-box";
    ?>
    <table id="header-logo" style="width:100%;padding-top:20px;border-collapse:collapse;margin-bottom:45px;">
        <tbody>
            <tr>
                <td align="left" style="width:50%;"><img class="logo" src="<?php echo CUSTOM_BOTAKSIGN_URL . '/assets/images/logo-transparent.png'; ?>" style="margin-left:0px;margin-top:0px;height: 56px; width: auto;"></td>
                <td align="right" style="width:50%;">
                    <?php if($email_button_title && $email_button_color) {
                        echo '<button class="status-button" style="background: '. $email_button_color .';box-shadow: 0px 10px 20px #00000029; border: none; color: #fff; padding: 14px 40px; font-size: 22px; line-height: 28px; border-radius: 10px;">'. $email_button_title .'</button>';
                    } ?>
                </td>
            </tr>
        </tbody>
    </table>
    <div id="infor" style="margin-top: 20px;width: 95%;height: auto;margin-right: 25px;margin-left: 25px;">
        <span class="info-title" style="color: #27793d; display: block; font-family: segoe-bold; font-size: 15pt;">Hi <?php echo $order_data['billing']['first_name'] . ' ' . $order_data['billing']['last_name']; ?>,</span><br>
        <span class="info-subtext" style="font-family: Myriad-Pro-Semibold; color: #231f20; font-size: 15pt;">Your order <span class="order_id" style="color: #231f20;/* display: block; */font-size: 15pt;font-family: segoe-bold;"> #<?php echo $order->get_id(); ?> </span>has been sent for printing! You’ll receive another email notifying
            you when your order is ready for delivery/collection.</span>
    </div>
    <div class="text-content-3" style="width: 100%; padding-top: 25px; padding-left: 25px; padding-right: 25px;">
        <span class="order-link-text-1" style="color: #27793d; font-family: segoe-bold; font-size: 15pt;">Estimated Collection Date: </span><span class="order-link-text" style="color: #15171b; font-family: segoe-bold; font-size: 15pt;"> 28 February 2020</span><br>
        <span class="order-link-text" style="color: #15171b; font-family: segoe-bold; font-size: 15pt;">View your order status: <a href="<?php echo $link_invoice; ?>" class="order-link-here" style="color: #fcaf17; font-family: segoe-bold; font-size: 15pt;"> HERE</a></span>
    </div>

    <div id="order-note" style="width: 95%;margin-top: 20px;padding-left: 25px;padding-right: 25px;padding-bottom: 35px;text-align: left;color: #231f20;font-size: 15pt;"><span class="order-note-text">Feel free to drop us an email at <a href="#" class="order-note-link" style="color: #27793d; font-family: segoe-bold;">info@botaksign.com.sg</a>, or give us a call at
            <a href="#" class="order-note-link" style="color: #27793d;/* display: block; */font-family: segoe-bold;">6286 2298</a> if you have any enquiries. Thank you for ordering with us!</span></div>
    <div id="line-border" style="border-bottom: 1px solid #a3cf62; width: 200pt; margin: 0px auto; padding-top: 20px;"></div>
<?php } ?>
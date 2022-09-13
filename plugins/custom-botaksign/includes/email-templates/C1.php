<?php 
$email_button_title = "Artwork Amendment";
$email_button_color = "#FF9E28";
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

<?php 
if ($order) {
    $pay_now_url = esc_url( $order->get_checkout_payment_url() );
    $order_data = $order->get_data();
    $rate_percent = 7;
    foreach ( $order->get_items('tax') as $tax_item ) {
        $rate_percent = (int)$tax_item->get_rate_percent();
    }
    ?>
    <div style="margin-bottom: 25px;">
        <span class="info-title" style="display:block;font-size:17px; line-height: 20px; font-weight: 500; margin-bottom: 12px;"><?php printf( esc_html__( 'Hi %s,', 'woocommerce' ), esc_html( $order->get_billing_first_name() ) ); ?></span>
        <span class="info-subtext" style="font-size:14px !important; line-height: 24px; color:#231f20;">We’ve received your order <span style="font-weight: 500;">#<?php echo $order_id; ?></span>, but unfortunately, we are unable to proceed as the artwork uploaded is unsuitable for print. Your order has been put on hold for now. Below is a list of the affected items in your order:</span>
    </div>
    <div style="border: 1px solid #ECECEC; box-shadow: 0px 0px 12px #0000001F; border-radius: 1em; padding: 20px; overflow: hidden; background: #fafafa; font-size: 14px; line-height: 24px;">
        <table class="product" style="border-collapse:collapse;">
            <tbody><tr>
                    <td class="stt" align="left" style="width:50px;padding-bottom:5px;padding-left:5px;color:#000000;font-weight: 500;border-bottom-width:1px;border-bottom-style:solid;border-bottom-color:#ECECEC;">No.</td>
                    <td class="description" align="left" style="width:300px;padding-bottom:5px;color:#000000;font-weight: 500; border-bottom-width:1px;border-bottom-style:solid;border-bottom-color:#ECECEC;">Item</td>
                    <td class="qty" align="center" style="width:30px;padding-bottom:5px;color:#000000;font-weight: 500; border-bottom-width:1px;border-bottom-style:solid;border-bottom-color:#ECECEC;">Issue</td>
                    <td class="amount" align="right" style="width:180px;padding-bottom:5px;color:#000000;font-weight: 500; border-bottom-width:1px;border-bottom-style:solid;border-bottom-color:#ECECEC;">Price</td>
                </tr>
                <?php

                $order_items = $order->get_items('line_item');
                $d = 1;
                $subtotal = 0;
                foreach ( $order_items as $item_id => $item ) {  
                    if( wc_get_product($item->get_product_id())->is_type( 'service' ) ){
                        $_product = wc_get_product($item->get_product_id());
                        foreach ( $order_items as $_item_id => $_item ) {
                            if( wc_get_order_item_meta($_item_id, '_item_id_service') == $item_id) {
                                $item_issue = wc_get_order_item_meta($_item_id, '_item_meta_issue');
                                $product_name = $_item->get_name();
                            }
                        }
                        $subtotal += $_product->get_price();
                        if (isset($_product) && $_product != false) {
                            ?>
                            <tr>
                                <td class="stt-text" align="left" style="width:50px;color:#231f20;padding-bottom:5px;padding-top:5px;"><?php echo $d; ?></td>
                                <td align="left" style="width:300px;padding-bottom:5px;padding-top:5px;">
                                    <div style="font-weight: 500"><?php echo esc_html($product_name); ?></div>
                                    <div>
                                        <span class="sub-product" style="color: #706F6F;">(Service: <?php echo $_product->get_name(); ?>)</span>
                                    </div>
                                </td>
                                <td class="qty-text" align="center" style="width:30px;color:#231f20;padding-bottom:5px;padding-top:5px;">
                                    <?php echo $item_issue; ?>
                                </td>
                                <td class="amount-text" align="right" style="width:180px;color:#231f20;padding-bottom:5px;padding-top:5px;"><?php echo wc_price($_product->get_price()); ?></td>
                            </tr>
                            <?php
                            $d++;
                        }
                    } 
                }
                $gst = $subtotal * $rate_percent / 100;
               
                ?>
                <tr>
                    <td></td>
                    <td></td>
                    <td colspan="2">
                        <table id="total-price" style="width: 100%;border-collapse:collapse; font-size: 14px; line-height: 25px;color:#231f20;">
                            <tbody>
                                <tr style="border-top-width:1px;border-top-style:solid;border-top-color:#ECECEC;">
                                    <td class="subtotal" align="left" style="width: 60px;color:#231f20;display:block;font-size: 14px; line-height: 20px;">Subtotal</td>
                                    <td class="subtotal-price" align="right" style="width: 140px;"><?php echo wc_price($subtotal); ?></td>
                                </tr>
                                <tr>
                                    <td class="gst" align="left" style="width: 60px;color:#231f20;display:block;font-size: 14px; line-height: 20px;">GST</td>
                                    <td class="gst-price" align="right" style="width: 140px;"><?php echo wc_price($gst); ?></td>
                                </tr>
                                <tr style="border-top-width:1px;border-top-style:solid;border-top-color:#ECECEC;border-bottom-width:1px;border-bottom-style:solid;border-bottom-color:#ECECEC;">
                                    <td class="total" align="left" style="position: relative;top: 1px;width: 60px;color:#231f20;display:block;font-size: 14px !important; line-height: 20px; font-weight: 500; ">Total</td>
                                    <td class="total-price" align="right" style="width: 140px;border-bottom-width:1px;"><?php echo wc_price($subtotal + $gst); ?></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td class="total-price" align="right" style="padding-top: 15px;">
                                        <button style="border: none;background: transparent linear-gradient(0deg, #1BCB3F 0%, #55D443 51%, #91DF48 100%) 0% 0% no-repeat padding-box;box-shadow: 0px 3px 6px #00000029;border-radius: 3px;padding: 5px 20px"><a style="text-decoration: none; font-size: 11px; line-height: 13px; color: #fff" href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>">PAY HERE</a></button>
                                    </td>
                                </tr>
                            </tbody></table>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div style="font-size: 14px; line-height: 25px; color: #000000; padding-top: 20px;">
        <div style="margin-bottom: 5px;">
            <div>
                For artwork adjustment, there will be a fee to be charged.
            </div>
            <div>
                Alternatively:
            </div>
        </div>
        <div style="font-weight: 500;margin-bottom: 5px;">
            <div>
                1. You may send in a new artwork
            </div>
            <div>
                2. Please send us an email to proceed with the current artwork
            </div>
        </div>
        <div>
            <div>
                Please take note that amendment of artwork will cause delay in 
            </div>
            <div>
                Collection / Delivery of your order. Will update once order is sent for printing with the Collection / Delivery time
            </div>
        </div>
    </div>
<?php
} ?>

<div style="display: flex; justify-content: center; width: 100%;">
    <div style="border-top-width: 2px; border-top-style: solid; border-top-color: #ECECEC; width: 200px;"></div>
</div>
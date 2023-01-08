<?php 
if ($order) {
    $pay_now_url = esc_url( $order->get_checkout_payment_url() );
    $order_data = $order->get_data();
    $gst = 0;
    $taxs = array_slice($order->get_taxes(), 0, 1);
    if($taxs) {
        $gst = array_shift($taxs)->get_rate_percent( 'view' );
    }
    ?>
    <table id="header-infor" style="border-collapse: collapse; width: 100%;" width="100%">
        <tbody><tr>
                <td style="width:100%;padding-top:30px;" class="bill-to-th" align="left"><img style="width:100%" class="confim" src="https://botaksign.com/wp-content/plugins/custom-botaksign/assets/images/amendment.png"></td>
            </tr>
        </tbody></table>
    <div id="infor" style="margin-top: 20px;width: 95%;height: auto;padding-right: 25px;padding-left: 25px;">
        <span class="info-title" style="color: #27793d; display: block; font-family: segoe-bold; font-size: 15pt;">Hi <?php echo $order_data['billing']['first_name'] . ' ' . $order_data['billing']['last_name']; ?>,</span><br>
        <span class="info-subtext" style="font-family: Myriad-Pro-Semibold; color: #231f20; font-size: 15pt;">We’ve received your order <span class="order_id" style="color: #231f20;font-size: 15pt;font-family: segoe-bold;">#<?php echo $order->get_id(); ?></span>, but unfortunately, we are unable to proceed as
            the artwork uploaded is unsuitable for print. Your order has been put <span class="order_id" style="color: #231f20;/* display: block; */font-size: 15pt;font-family: segoe-bold;">on hold</span>. for
            now. Below is a list of the affected items in your order:</span>
    </div>
    <table class="product" style="width: 90%; border-collapse: collapse; margin-right: 50px; margin-left: 50px;" width="90%">
        <tbody><tr>
                <td style="color: #27793d; font-size: 15pt; font-family: segoe-bold; border-bottom: 1px solid #27793d; padding-top: 15px; width: 30px; padding-bottom: 5px;" class="stt" align="left" width="30">No.</td>
                <td style="color: #27793d; font-size: 15pt; font-family: segoe-bold; border-bottom: 1px solid #27793d; padding-top: 15px; width: 300px; padding-bottom: 5px;" class="description" align="left" width="300">Item</td>
                <td style="color: #27793d; font-size: 15pt; font-family: segoe-bold; border-bottom: 1px solid #27793d; padding-top: 15px; width: 100px; padding-bottom: 5px;" class="qty" align="left" width="100">Issue</td>
                <td style="color: #27793d; font-size: 15pt; font-family: segoe-bold; border-bottom: 1px solid #27793d; padding-top: 15px; width: 80px; padding-bottom: 5px;" class="amount" align="right" width="80">Price</td>
            </tr>
            <?php

            $order_items = $order->get_items('line_item');
            $d = 1;
            foreach ( $order_items as $item_id => $item ) {  
                if( wc_get_product($item->get_product_id())->is_type( 'service' ) ){
                    $_product = wc_get_product($item->get_product_id());
                    foreach ( $order_items as $_item_id => $_item ) {
                        if( wc_get_order_item_meta($_item_id, '_item_id_service') == $item_id) {
                            $item_issue = wc_get_order_item_meta($_item_id, '_item_meta_issue');
                            $product_name = $_item->get_name();
                        }
                    }
                    if (isset($_product) && $_product != false) {
                        ?>
                        <tr>
                            <td style="color: #231f20; font-size: 15pt; border-bottom: 1px solid #27793d; padding-bottom: 15px; padding-top: 15px; width: 30px; font-family: segoe-bold,Myriad-Pro-Semibold;" class="stt-text" align="left" width="30"><?php echo $d; ?></td>
                            <td style="border-bottom: 1px solid #27793d; padding-bottom: 15px; padding-top: 15px; width: 300px;" align="left" width="300">
                                <div><b><?php echo esc_html($product_name); ?></b></div>
                                <div>
                                    <span class="sub-product" style="color: #231f20; font-size: 10pt;">&emsp;Service: <?php echo $_product->get_name(); ?> &emsp;&emsp;</span>
                                    <a href="<?php echo get_permalink( $item['product_id'] ); ?>" class="sub-product-link" style="padding-left: 10px; color: #00afff; font-size: 7pt;">Find out more</a>
                                </div>
                            </td>
                            <td style="color: #231f20; font-size: 15pt; border-bottom: 1px solid #27793d; padding-bottom: 15px; padding-top: 15px; width: 100px; font-family: segoe-bold,Myriad-Pro-Semibold;" class="qty-text" align="left" width="100"><?php echo $item_issue; ?></td>
                            <td style="color: #231f20; font-size: 15pt; border-bottom: 1px solid #27793d; padding-bottom: 15px; padding-top: 15px; width: 80px; font-family: segoe-bold,Myriad-Pro-Semibold;" class="amount-text" align="right" width="80"><?php echo wc_price($_product->get_price()); ?></td>
                        </tr>
                        <?php
                        $d++;
                    }
                } 
            }
           
            ?>
        </tbody>
    </table>
    <table id="total-price" style="width: 90%; border-collapse: collapse; margin-right: 50px; margin-left: 50px;">
        <tbody><tr>
                <td rowspan="3" style="width: 510px; padding-top: -30px;" align="left" width="510">
                    <span>We are unable to adjust the artwork for items listed without a price.</span><br>
                    <span>Kindly choose one of the two options below if that is the case for your artwork.</span>
                </td>
                <td style="color: #27793d;/* display: block; */font-size: 13pt;font-family: segoe-bold;width: 80px;padding-top: 5px;padding-bottom: 5px;border-bottom: 1px solid #27793d;" class="subtotal" align="right" width="80">Subtotal</td>
                <td style="font-size: 13pt; color: #231f20; width: 80px; padding-top: 5px; padding-bottom: 5px; border-bottom: 1px solid #27793d; font-family: Myriad-Pro-Semibold,segoe-bold;" class="subtotal-price" align="right" width="80"><?php echo wc_price($order->get_subtotal()); ?></td>
            </tr>
            <tr>
                <td style="color: #27793d;/* display: block; */font-size: 13pt;font-family: segoe-bold;width: 80px;padding-top: 5px;padding-bottom: 5px;border-bottom: 1px solid #27793d;" class="gst" align="right" width="80"><?php echo $gst; ?>% GST</td>
                <td style="font-size: 13pt; color: #231f20; width: 80px; padding-top: 5px; padding-bottom: 5px; border-bottom: 1px solid #27793d; font-family: Myriad-Pro-Semibold,segoe-bold;" class="gst-price" align="right" width="80"><?php echo wc_price($order->get_total_tax()); ?></td>
            </tr>
            <tr>
                <td style="color: #27793d;/* display: block; */font-family: segoe-bold;width: 80px;padding-top: 5px;padding-bottom: 5px;border-bottom: 1px solid #27793d;font-size: 15pt;" class="total" align="right" width="80">Total</td>
                <td style="font-size: 15pt; color: #231f20; width: 80px; padding-top: 5px; padding-bottom: 5px; border-bottom: 1px solid #27793d; font-family: Myriad-Pro-Semibold,segoe-bold;" class="total-price" align="right" width="80"><?php echo wc_price($order->get_total()); ?></td>
            </tr>
        </tbody></table><div class="pay-here" style="width: 96%; text-align: right; padding-top: 15px; padding-right: 50px;"><a href="<?php echo $pay_now_url; ?>"><img style="width:200px" src="https://botaksign.com/wp-content/plugins/custom-botaksign/assets/images/pay-here.png"></a></div>
    <div style="padding-left: 25px;padding-right: 25px;padding-top: 25px;font-size: 15pt;width: 95%;" id="view-order-text"><span class="view-order-text">We are able to adjust your artwork for you for a fee. If you’d like us to adjust it for you,
            kindly click the ‘<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>">PAY HERE</a>’ button to make payment. Otherwise, you may either:</span></div>
    <div style="padding-left: 50px; padding-right: 50px; padding-top: 25px; font-size: 15pt; width: 100%;" id="view-order-text-2">
        <span class="view-order-text-number" style="color: #231f20; display: block; font-size: 15pt; font-family: segoe-bold;">1:<a href="<?php echo generateLinkEditOrder($order->get_id()); ?>" class="view-order-text-1-link" style="color: #fcaf17;"> Amend the artwork and resend it to us</a></span><br>
        <span class="view-order-text-number" style="color: #231f20;/* display: block; */font-size: 15pt;font-family: segoe-bold;">2:</span><span> Proceed with the current artwork as it is</span>
    </div>
    <div style="padding-top: 25px;padding-left: 25px;color: #231f20;font-size: 15pt;padding-right: 25px;width: 95%;" id="view-order-text-3"><span class="view-order-text">Let us know what you prefer, and we will do our best to assist you. Do note that your
            <span class="view-order-text-3-bold" style="color: #231f20; font-size: 15pt; font-family: segoe-bold;">collection/delivery time will be delayed</span> as a result, and your order will only be
            processed once we’ve verited that the artwork is suitable for print. We’ll update you
            again once it’s sent for printing, with the new estimated collection/delivery timing.</span></div>
    <div style="padding-top: 25px; padding-left: 25px; color: #231f20; font-size: 15pt; padding-right: 25px; width: 100%;" id="view-order-text-4"><span class="view-order-text-4">Feel free to reply to this email or call us at <span class="view-order-text-4-phone" style="color: #27793d;font-size: 15pt;/* padding-right: 25px; */font-family: segoe-bold;">6286 2298</span> if you have any enquiries.<br>
            Thank you!</span></div>
    <div id="line-border" style="border-bottom: 1px solid #a3cf62; width: 200pt; margin: 0px auto; padding-top: 20px;"></div>
<?php } ?>
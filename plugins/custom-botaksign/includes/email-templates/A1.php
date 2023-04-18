<?php
if ($order) {
    $order_data = $order->get_data();
    $link_order = get_permalink(get_option('woocommerce_myaccount_page_id')) . '/view-order/' . $order->get_id();
    $est_time = show_est_completion($order);
    
    $production_time = new DateTime($est_time['production_date_completed']);
    $shipping_time = new DateTime($est_time['shipping_date_completed']);
    $dDiff = (int) $production_time->diff($shipping_time)->format('%a');
    $shipping_days = $dDiff > 0 ? $dDiff : 1;
    if ($shipping_days == 1) {
        $shipping_days_text = '1 day';
    } else {
        $shipping_days_text = $shipping_days . ' days';
    }
    ?>
    <style type="text/css">
        @media only screen and (min-width: 992px) {
            #view-order-2 {
                width: 95%!important;
                margin-right: 25px!important;
                margin-left: 25px!important; 
            }
            table.product,#total-price {
                margin-right: 50px!important; 
                margin-left: 50px!important; 
                width: 90%!important;
            }
        }
    </style>
    <table id="header-infor" style="width: 100%;border-collapse:collapse">
        <tbody><tr>
                <td class="bill-to-th" align="left" style="width:100%;padding-top:30px;"><img class="confim" src="https://botaksign.com/wp-content/plugins/custom-botaksign/assets/images/confim.png" style="width:100%;"></td>
            </tr>
        </tbody></table>
    <div id="infor" style="margin-top:20px;width: 95%;height:auto;padding-right:2.5%;padding-left:2.5%;">
        <span class="info-title" style="color:#27793d;display:block;font-size:15pt !important;font-family:segoe-bold;">Hi <?php echo $order_data['billing']['first_name'] . ' ' . $order_data['billing']['last_name']; ?>,</span><br>
        <span class="info-subtext" style="font-size:15pt !important;font-family:Myriad-Pro-Semibold;color:#231f20;">Thanks for ordering with us! Your order <span class="order_id" style="color:#231f20;font-size:15pt;font-family:segoe-bold;">#<?php echo $order->get_id(); ?></span> has been placed succesfully. You’ll receive another email notifying you when your order is ready for delivery.</span>
    </div>
    <table class="product" style="width: 100%;border-collapse:collapse">
        <tbody><tr>
                <td class="stt" align="left" style="width:30px;padding-bottom:5px;color:#27793d;font-size:15pt;font-family:segoe-bold;border-bottom-width:1px;border-bottom-style:solid;border-bottom-color:#27793d;padding-top:15px;">No.</td>
                <td class="description" align="left" style="width:400px;padding-bottom:5px;color:#27793d;font-size:15pt;font-family:segoe-bold;border-bottom-width:1px;border-bottom-style:solid;border-bottom-color:#27793d;padding-top:15px;">Item</td>
                <td class="qty" align="center" style="width:50px;padding-bottom:5px;color:#27793d;font-size:15pt;font-family:segoe-bold;border-bottom-width:1px;border-bottom-style:solid;border-bottom-color:#27793d;padding-top:15px;">Qty</td>
                <td class="amount" align="right" style="width:80px;padding-bottom:5px;color:#27793d;font-size:15pt;font-family:segoe-bold;border-bottom-width:1px;border-bottom-style:solid;border-bottom-color:#27793d;padding-top:15px;">Price</td>
            </tr>
            <?php
            $items = $order->get_items();
            $d = 1;
            foreach ($items as $item) {
                $_product = wc_get_product($item['product_id']);
                if (isset($item['variation_id']) && $item['variation_id'] > 0) {
                    $_product = wc_get_product($item['variation_id']);
                }
                
                if (isset($_product) && $_product != false) {
                    ?>
                    <tr>
                        <td class="stt-text" align="left" style="width:30px;color:#231f20;font-size:15pt;font-family:segoe-bold,Myriad-Pro-Semibold !important;border-bottom-width:1px;border-bottom-style:solid;border-bottom-color:#27793d;padding-bottom:15px;padding-top:15px;"><?php echo $d; ?></td>
                        <td align="left" style="width:400px;border-bottom-width:1px;border-bottom-style:solid;border-bottom-color:#27793d;padding-bottom:15px;padding-top:15px;">
                            <span class="description-text" style="padding-top:10px;padding-bottom:10px;color:#231f20;font-size:15pt;font-family:segoe-bold,Myriad-Pro-Semibold !important;"><?php echo $_product->get_title(); ?></span>
                        </td>
                        <td class="qty-text" align="center" style="width:50px;color:#231f20;font-size:15pt;font-family:segoe-bold,Myriad-Pro-Semibold !important;border-bottom-width:1px;border-bottom-style:solid;border-bottom-color:#27793d;padding-bottom:15px;padding-top:15px;"><?php echo ($item['quantity'] > 0 ? $item['quantity'] : 0); ?></td>
                        <td class="amount-text" align="right" style="width:80px;color:#231f20;font-size:15pt;font-family:segoe-bold,Myriad-Pro-Semibold !important;border-bottom-width:1px;border-bottom-style:solid;border-bottom-color:#27793d;padding-bottom:15px;padding-top:15px;"><?php echo wc_price($item['line_total']); ?></td>
                    </tr>
                    <?php
                }
                $d++;
            }
            ?>
        </tbody></table>
    <?php
        $gst = 0;
        $taxs = array_slice($order->get_taxes(), 0, 1);
        if($taxs) {
            $gst = array_shift($taxs)->get_rate_percent( 'view' );
        }
    ?>
    <table id="total-price" style="width: 100%;border-collapse:collapse">
        <tbody>
            <tr>
                <td align="left" style="width: 60%;padding-top:10px;">
                    <span class="disclaimer" style="color:#27793d;font-size:15pt !important;font-family:segoe-bold;">Order Date:</span><span class="disclaimer-sub" style="color:#231f20;font-size:15pt !important;font-family:segoe-bold;"> <?php echo $order_data['date_created']->date('d F Y'); ?></span><br>
                </td>
                <td align="right" style="padding-top:10px;width: 40%;">
                    <table style="border-bottom: 1px solid #27793d;">
                        <tbody>
                            <tr>
                                <td class="subtotal" align="right" style="width: 80px;padding-top:5px;padding-bottom:8px;color:#27793d;display:block;font-size:13pt;font-family:segoe-bold;">Subtotal</td>
                                <td class="subtotal-price" align="right" style="width: 120px;padding-top:5px;padding-bottom:5px;font-size:13pt;color:#231f20;font-family:Myriad-Pro-Semibold,segoe-bold !important;"><?php echo wc_price($order->get_subtotal()); ?></td>
                            </tr>
                        </tbody></table>
                </td>
            </tr>
            <tr>
                <td align="left" rowspan="3" style="width: 60%;padding-top:10px;vertical-align: top;">
                    <span class="disclaimer-text2" style="color:#27793d;font-size:15pt !important;font-family:segoe-bold;">Estimated Delivery Date:</span><span class="disclaimer-sub2" style="color:#231f20;font-size:15pt !important;font-family:segoe-bold;"> <?= $est_time['shipping_date_completed']; ?></span>
                </td>
                <td align="right" style="padding-top:10px;width: 40%;">
                    <table style="border-bottom: 1px solid #27793d;">
                        <tbody>
                            <tr>
                                <td class="gst" align="right" style="width:80px;padding-top:5px;padding-bottom:5px;color:#27793d;display:block;font-size:13pt;font-family:segoe-bold;">Delivery</td>
                                <td class="gst-price" align="right" style="width:120px;padding-top:5px;padding-bottom:5px;border-bottom-width:1px;font-size:13pt;color:#231f20;font-family:Myriad-Pro-Semibold,segoe-bold !important;"><?php echo wc_price($order_data['shipping_total']); ?></td>
                            </tr>
                        </tbody></table>
                </td>
            </tr>
            <tr>
                <td align="right" style="padding-top:10px;width: 40%;">
                    <table style="border-bottom: 1px solid #27793d;">
                        <tbody>
                            <tr>
                                <td class="gst" align="right" style="width:80px;padding-top:5px;padding-bottom:5px;color:#27793d;display:block;font-size:13pt;font-family:segoe-bold;"><?php echo $gst; ?>% GST</td>
                                <td class="gst-price" align="right" style="width:120px;padding-top:5px;padding-bottom:5px;font-size:13pt;color:#231f20;font-family:Myriad-Pro-Semibold,segoe-bold !important;"><?php echo wc_price($order->get_total_tax()); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td align="right" style="padding-top:10px;width: 40%;">
                    <table style="border-bottom: 1px solid #27793d;">
                        <tbody>
                            <tr>
                                <td class="total" align="right" style="position: relative;top: 1px;width:80px;padding-top:5px;padding-bottom:5px;color:#27793d;display:block;font-size:15pt !important;font-family:segoe-bold;">Total</td>
                                <td class="total-price" align="right" style="width:120px;padding-top:5px;padding-bottom:5px;border-bottom-width:1px;font-size:15pt;color:#231f20;font-family:Myriad-Pro-Semibold,segoe-bold !important;"><?php echo wc_price($order->get_total()); ?></td>
                            </tr>
                        </tbody></table>
                </td>
            </tr>
        </tbody></table>
    <div id="view-order" style="width:88%;margin-right:25px;margin-left:25px;"><span class="view-order-text" style="font-size:15pt !important;font-family:segoe-bold;color:#231f20;">View your order status <a class="view-order-text-link" href="<?php echo $link_order; ?>" style="font-size:15pt !important;font-family:segoe-bold;color:#fcaf17;">HERE</a></span></div>
    <table id="view-order-2" style="width:100%;border-collapse:collapse;">
        <tbody><tr>
                <td align="left" style="width:60%;padding-top:20px;">
                    <span class="information-title" style="color:#27793d;display:block;font-size:16pt !important;font-family:segoe-bold;">Customer Information</span><br>
                    <span class="information-sub-title" style="color:black;display:block;font-size:14pt !important;font-family:segoe-bold;"><?php echo $order_data['billing']['first_name'] . ' ' . $order_data['billing']['last_name']; ?></span><br>
                    <span class="information-sub" style="font-size:14pt !important;"><?php echo $order_data['billing']['address_1']; ?> <br>
                        <?php echo $order_data['billing']['address_2']; ?><br>
                        <?php echo $order_data['billing']['country'] . ' ' . $order_data['billing']['postcode']; ?></span><br>
                    <span class="information-sub" style="font-size:14pt !important;"><?php echo $order_data['billing']['email']; ?></span><br>
                    <span class="information-sub" style="font-size:14pt !important;"><?php echo $order_data['billing']['phone']; ?></span><br>
                    <span class="information-title-4" style="color:#27793d;display:block;font-size:16pt !important;font-family:segoe-bold;">Payment Method</span><br>
                    <span class="information-sub-credit" style="color:black;display:block;font-size:14pt !important;font-family:segoe-bold;"><?php echo $order_data['payment_method_title']; ?></span><br>
                </td>
                <td align="right" style="width:40%;/*padding-top:20px;*/">
                    <span class="information-title-2" style="color:#27793d;display:block;font-size:16pt !important;font-family:segoe-bold;">Shipping Method</span><br>
                    <span class="information-sub-title-3" style="color:black;display:block;font-size:14pt !important;font-family:segoe-bold;">Delivery: <?php echo $order->get_shipping_method(); ?></span><br>
                    <span class="information-sub-title-4" style="color:black;display:block;font-size:14pt !important;font-family:segoe-bold;">Track your order <a class="information-sub-title-4-link" href="<?php echo $link_order; ?>" style="font-family:segoe-bold;color:#fcaf17;">HERE</a></span><br>
                    <span class="information-title-3" style="color:#27793d;display:block;font-size:16pt !important;font-family:segoe-bold;">Deliver to:</span><br>
                    <span class="information-sub-title-2" style="color:black;display:block;font-size:14pt !important;font-family:segoe-bold;"><?php echo $order_data['shipping']['first_name'] . ' ' . $order_data['shipping']['last_name']; ?></span>
                    <span class="information-sub" style="font-size:14pt !important;"><?php echo $order_data['shipping']['address_1']; ?><br>
                        <?php echo $order_data['shipping']['address_2']; ?><br>
                        <?php echo $order_data['shipping']['country'] . ' ' . $order_data['shipping']['postcode']; ?></span>
                </td>
            </tr>
        </tbody></table>
    <div id="order-note" style="width: 90%;background-color:#a3cf62;background-image:none;background-repeat:repeat;background-position:top left;background-attachment:scroll;margin-top:20px;padding-top:35px;padding-left:5%;padding-right:5%;padding-bottom:35px;text-align:center;"><span class="order-note-text" style="color:#27793d;display:block;font-size:17pt !important;font-family:segoe-bold;">Got any questions about your order? Reply to this email or give us a call at 6286 2298. We will be happy to help.</span></div>
<?php } ?>
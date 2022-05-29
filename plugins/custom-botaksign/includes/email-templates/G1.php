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
            table.product,#total-price {
                margin-right: 50px!important; 
                margin-left: 50px!important; 
                width: 90%!important;
            }
            #view-order-2 {
                margin-right: 25px!important; 
                margin-left: 25px!important; 
                width: 95%!important;
            }
        }
    </style>
    <table id="header-infor" style="border-collapse: collapse; width: 100%;" width="100%">
        <tr>
            <td style="width:100%;padding-top:30px;" class="bill-to-th" align="left"><img style="width:100%" class="confim" src="https://botaksign.com/wp-content/plugins/custom-botaksign/assets/images/received.jpg"></td>
        </tr>
    </table>
    <div id="infor" style="margin-top: 20px; width: 100%; height: auto; margin-right: 25px; margin-left: 25px;">
        <span class="info-title" style="color: #27793d; display: block; font-family: segoe-bold; font-size: 15pt;">Hi <?php echo $order_data['billing']['first_name'] . ' ' . $order_data['billing']['last_name']; ?>,</span><br>
        <span class="info-subtext" style="font-family: Myriad-Pro-Semibold; color: #231f20; font-size: 15pt;">Thanks for ordering with us! Your order <span class="order_id" style="color: #231f20; font-size: 15pt; font-family: segoe-bold;">#<?php echo $order->get_id(); ?></span> has been placed succesfully. You’ll
            receive another email notifying you when your order is ready for delivery.</span>
    </div>
    <table class="product" style="border-collapse: collapse; width: 100%;" width="100%">
        <tr>
            <td style="color: #27793d; font-size: 15pt; font-family: segoe-bold; border-bottom: 1px solid #27793d; padding-top: 15px; width: 30px; padding-bottom: 5px;" class="stt" align="left" width="30">No.</td>
            <td style="color: #27793d; font-size: 15pt; font-family: segoe-bold; border-bottom: 1px solid #27793d; padding-top: 15px; width: 400px; padding-bottom: 5px;" class="description" align="left" width="400">Item</td>
            <td style="color: #27793d; font-size: 15pt; font-family: segoe-bold; border-bottom: 1px solid #27793d; padding-top: 15px; width: 50px; padding-bottom: 5px;" class="qty" align="center" width="50">Qty</td>
            <td style="color: #27793d; font-size: 15pt; font-family: segoe-bold; border-bottom: 1px solid #27793d; padding-top: 15px; width: 80px; padding-bottom: 5px;" class="amount" align="right" width="80">Price</td>
        </tr>
        <?php
        $items = $order->get_items();
        $d = 1;
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal+=$item['line_total'];
            if (function_exists('get_product')) {
                if (isset($item['variation_id']) && $item['variation_id'] > 0):
                    $_product = get_product($item['variation_id']);
                else:
                    $_product = get_product($item['product_id']);
                endif;
            } else {
                if (isset($item['variation_id']) && $item['variation_id'] > 0):
                    $_product = new WC_Product_Variation($item['variation_id']);
                else:
                    $_product = new WC_Product($item['product_id']);
                endif;
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
        $gst = $subtotal * 7 / 100;
        ?>
    </table>
    <?php
    if ($order_data['shipping_total'] > 0) {
        $gst += ($order_data['shipping_total'] * 7/100 );
    }
    ?>
    <table id="total-price" style="width: 100%;border-collapse:collapse">
        <tbody>
            <tr>
                <td align="left" style="width: 60%; padding-top: 10px;" width="60%">
                    <span class="disclaimer" style="display: block; color: #27793d; font-family: segoe-bold; font-size: 15pt;">Order Date:</span><span class="disclaimer-sub" style="display: block; color: #231f20; font-family: segoe-bold; font-size: 15pt;"> <?php echo $order_data['date_created']->date('d F Y'); ?></span><br>
                </td>
                <td align="right" style="padding-top: 10px; width: 40%;" width="40%">
                    <table style="border-collapse: collapse; border-bottom: 1px solid #27793d;">
                        <tbody>
                            <tr>
                                <td class="subtotal" align="right" style="width: 80px; padding-top: 5px; padding-bottom: 8px; color: #27793d; display: block; font-size: 13pt; font-family: segoe-bold;" width="80">Subtotal</td>
                                <td class="subtotal-price" align="right" style="width: 120px; padding-top: 5px; padding-bottom: 5px; font-size: 13pt; color: #231f20; font-family: Myriad-Pro-Semibold,segoe-bold;" width="80"><?php echo wc_price($subtotal); ?></td>
                            </tr>
                        </tbody></table>
                </td>
            </tr>
            <tr>
                <td align="left" rowspan="3" style="width: 60%; padding-top: 10px; vertical-align: top;" width="60%" valign="top">
                    <span class="disclaimer-text2" style="display: block; color: #27793d; font-family: segoe-bold; font-size: 15pt;">Estimated Collection Date:</span><span class="disclaimer-sub2" style="display: block; color: #231f20; font-family: segoe-bold; font-size: 15pt;"><?= $est_time['shipping_date_completed']; ?></span>
                </td>
                <td align="right" style="padding-top: 10px; width: 40%;" width="40%">
                    <table style="border-collapse: collapse; border-bottom: 1px solid #27793d;">
                        <tbody>
                            <tr>
                                <td class="gst" align="right" style="width: 80px; padding-top: 5px; padding-bottom: 5px; color: #27793d; display: block; font-size: 13pt; font-family: segoe-bold;" width="80">Delivery</td>
                                <td class="gst-price" align="right" style="width: 120px; padding-top: 5px; padding-bottom: 5px; border-bottom-width: 1px; font-size: 13pt; color: #231f20; font-family: Myriad-Pro-Semibold,segoe-bold;" width="80"><?php echo wc_price($order_data['shipping_total']); ?></td>
                            </tr>
                        </tbody></table>
                </td>
            </tr>
            <tr>
                <td align="right" style="padding-top: 10px; width: 40%;" width="40%">
                    <table style="border-collapse: collapse; border-bottom: 1px solid #27793d;">
                        <tbody>
                            <tr>
                                <td class="gst" align="right" style="width: 80px; padding-top: 5px; padding-bottom: 5px; color: #27793d; display: block; font-size: 13pt; font-family: segoe-bold;" width="80">7% GST</td>
                                <td class="gst-price" align="right" style="width: 120px; padding-top: 5px; padding-bottom: 5px; font-size: 13pt; color: #231f20; font-family: Myriad-Pro-Semibold,segoe-bold;" width="80"><?php echo wc_price($gst); ?></td>
                            </tr>
                        </tbody></table>
                </td>
            </tr>
            <tr>
                <td align="right" style="padding-top: 10px; width: 40%;" width="40%">
                    <table style="border-collapse: collapse; border-bottom: 1px solid #27793d;">
                        <tbody>
                            <tr>
                                <td class="total" align="right" style="position: relative; top: 1px; width: 80px; padding-top: 5px; padding-bottom: 5px; color: #27793d; display: block; font-family: segoe-bold; font-size: 15pt;" width="80">Total</td>
                                <td class="total-price" align="right" style="width: 120px; padding-top: 5px; padding-bottom: 5px; border-bottom-width: 1px; font-size: 15pt; color: #231f20; font-family: Myriad-Pro-Semibold,segoe-bold;" width="80"><?php echo wc_price($subtotal + $gst + $order_data['shipping_total']); ?></td>
                            </tr>
                        </tbody></table>
                </td>
            </tr>
        </tbody></table>
    <div style="margin-right: 25px; margin-left: 25px; width: 100%;" id="view-order"><span class="view-order-text" style="font-family: segoe-bold; color: #231f20; font-size: 15pt;">View your order status <a class="view-order-text-link" href="<?php echo $link_order; ?>" style="font-family: segoe-bold; color: #fcaf17; font-size: 15pt;">HERE</a></span></div>
    <table id="view-order-2" style="border-collapse: collapse; width: 100%;" width="100%">
        <tr>
            <td style="width:60%;padding-top:20px;" align="left">
                <span class="information-title" style="color: #27793d; display: block; font-family: segoe-bold; font-size: 17pt;">Customer Information</span><br>
                <span class="information-sub-title" style="color: #231f20; display: block; font-family: segoe-bold; font-size: 14pt;"><?php echo $order_data['billing']['first_name'] . ' ' . $order_data['billing']['last_name']; ?></span>
                <span class="information-sub" style="font-size:14pt !important;"><?php echo $order_data['billing']['address_1']; ?> <br>
                    <?php echo $order_data['billing']['address_2']; ?><br>
                    <?php echo $order_data['billing']['country'] . ' ' . $order_data['billing']['postcode']; ?></span><br>
                <span class="information-sub" style="font-size:14pt !important;"><?php echo $order_data['billing']['email']; ?></span><br>
                <span class="information-sub" style="font-size:14pt !important;"><?php echo $order_data['billing']['phone']; ?></span><br>
            </td>
            <td style="width:40%;padding-top:-20px;" align="right">
                <span class="information-title-2" style="color: #27793d; display: block; font-family: segoe-bold; font-size: 17pt;">Shipping Method</span><br>
                <span class="information-sub-title-3" style="color: #231f20; display: block; font-family: segoe-bold; font-size: 14pt;">Self-collection</span><br><br>
                <span class="information-title-3" style="color: #27793d; display: block; font-family: segoe-bold; font-size: 17pt;">Payment Method</span><br>
                <span class="information-sub-title-2" style="color: #231f20; display: block; font-family: segoe-bold; font-size: 14pt;"><?php echo $order_data['payment_method_title']; ?></span><br>
            </td>
        </tr>
    </table>
    <div id="order-note" style="width: 90%;background-color:#a3cf62;background-image:none;background-repeat:repeat;background-position:top left;background-attachment:scroll;margin-top:20px;padding-top:35px;padding-left:5%;padding-right:5%;padding-bottom:35px;text-align:center;"><span class="order-note-text" style="color:#27793d;display:block;font-size:17pt !important;font-family:segoe-bold;">Got any questions about your order? Reply to this email or give us a call at 6286 2298. We will be happy to help.</span></div>
<?php } ?>
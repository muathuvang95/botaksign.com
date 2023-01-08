<?php
if ($order) {
    $order_data = $order->get_data();
    $link_order = get_permalink(get_option('woocommerce_myaccount_page_id')) . '/view-order/' . $order->get_id();
    ?>
    <table id="header-infor" style="border-collapse: collapse; width: 100%;" width="100%">
        <tr>
            <td style="width:100%;padding-top:30px;" class="bill-to-th" align="left"><img style="width:100%" class="confim" src="https://botaksign.com/wp-content/plugins/custom-botaksign/assets/images/complete.png"></td>
        </tr>
    </table>
    <div id="infor" style="margin-top: 20px; width: 100%; height: auto; margin-right: 25px; margin-left: 25px;">
        <span class="info-title" style="color: #27793d; display: block; font-family: segoe-bold; font-size: 15pt;">Hi <?php echo $order_data['billing']['first_name'] . ' ' . $order_data['billing']['last_name']; ?>,</span><br>
        <span class="info-subtext" style="font-family: Myriad-Pro-Semibold; color: #231f20; font-size: 15pt;">Your order <span class="order_id" style="color: #231f20; font-size: 15pt; font-family: segoe-bold;">#<?php echo $order->get_id(); ?></span> is ready for collection! Here are the collection details:</span>
    </div>
    <table class="product" style="width: 90%; border-collapse: collapse; margin-right: 50px; margin-left: 50px;" width="90%">
        <tbody>
            <tr>
                <td style="color: #27793d; font-size: 15pt; font-family: segoe-bold; border-bottom: 1px solid #27793d; padding-top: 15px; width: 30px; padding-bottom: 5px;" class="stt" align="left" width="30">No.</td>
                <td style="color: #27793d; font-size: 15pt; font-family: segoe-bold; border-bottom: 1px solid #27793d; padding-top: 15px; width: 500px; padding-bottom: 5px;" class="description" align="left" width="500">Item</td>
                <td style="color: #27793d; font-size: 15pt; font-family: segoe-bold; border-bottom: 1px solid #27793d; padding-top: 15px; width: 40px; padding-bottom: 5px;" class="qty" align="center" width="40">Qty</td>
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
                        <td style="color: #231f20; font-size: 15pt; border-bottom: 1px solid #27793d; padding-bottom: 15px; padding-top: 15px; width: 30px; font-family: segoe-bold,Myriad-Pro-Semibold;" class="stt-text" align="left" width="30"><?php echo $d; ?></td>
                        <td style="border-bottom: 1px solid #27793d; padding-bottom: 15px; padding-top: 15px; width: 500px;" align="left" width="500">
                            <span class="description-text" style="padding-top: 10px; padding-bottom: 10px; color: #231f20; font-size: 15pt; font-family: segoe-bold,Myriad-Pro-Semibold;"><?php echo $_product->get_title(); ?></span>
                        </td>
                        <td style="color: #231f20; font-size: 15pt; border-bottom: 1px solid #27793d; padding-bottom: 15px; padding-top: 15px; width: 40px; font-family: segoe-bold,Myriad-Pro-Semibold;" class="qty-text" align="center" width="40"><?php echo ($item['quantity'] > 0 ? $item['quantity'] : 0); ?></td>
                    </tr>
                    <?php
                }
                $d++;
            }
            ?>
        </tbody>
    </table>
    <?php 
        $collection_at = '';
        //if($order->get_shipping_method() == 'Self-collection') {
            $collection_at = '22 Yio Chu Kang Road #01-19 Highland Centre. 545535';
        // } else {
        //     $collection_at = $order_data['shipping']['address_1'];
        // }
    ?>
    <table id="view-order-2" style="border-collapse: collapse; margin-right: 50px; margin-left: 50px; width: 90%;" width="100%">
        <tr>
            <td style="width:50%;padding-top:20px;" align="left">
                <span class="information-title-2" style="color: #27793d; display: block; font-family: segoe-bold; font-size: 17pt;">Collection at:</span><br>
                <span class="information-title-sub-1" style="color: #231f20; font-size: 14pt;"><?php echo $collection_at; ?><br>
                <br>
                <span class="information-title-sub-1" style="color: #231f20; font-size: 14pt;">Open <span class="information-title-sub-1-bol" style="color: black; display: block; font-family: segoe-bold; font-size: 14pt;">Mon - Fri 9am - 6pm, Sat 9am - 1pm</span></span><br>
                <span class="information-title-sub-1" style="color: #231f20; font-size: 14pt;">Closed on Sundays and public holidays</span><br><br>
                <span class="information-sub-title-4" style="color: black; display: block; font-family: segoe-bold; font-size: 14pt;">Download your order invoice <a class="information-sub-title-4-link" href="<?php echo $link_order; ?>" style="font-family: segoe-bold; color: #fcaf17;">HERE</a></span>
            </td>
            <td style="width:50%;padding-top:-80px;" align="right">
                <span class="information-title" style="color: #27793d; display: block; font-family: segoe-bold; font-size: 17pt;">Note:</span><br>
                <span class="information-sub-title" style="color: black; display: block; font-family: segoe-bold; font-size: 14pt;">Kindly show this email upon collection
                    as proof of purchase.</span>
            </td>
        </tr>
    </table>
    <div id="order-note" style="width: 90%; margin-top: 20px; padding-left: 25px; padding-right: 25px; padding-bottom: 35px; text-align: left; color: #231f20; font-size: 15pt;"><span class="order-note-text">Feel free to drop us an email at <a href="#" class="order-note-link" style="color: #27793d; font-family: segoe-bold;">info@botaksign.com.sg</a>, or give us a call at
            <a href="#" class="order-note-link" style="color: #27793d; font-family: segoe-bold;">6286 2298</a> if you have any enquiries. Thank you for ordering with us!</span></div>
<?php } ?>
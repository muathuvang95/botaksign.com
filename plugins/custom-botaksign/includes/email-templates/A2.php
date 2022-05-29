<?php

function botak_convert_format_time($time) {
    $_time= explode(':' , $time );
    $hourse = (int)$_time[0];
    if( $hourse < 12 ) {
        return $time.'am';
    } elseif( $hourse == 12 ) {
        return $time.'pm';
    } else {
        $hourse = $hourse - 12;
        return $hourse.':'.$_time[1].'pm';
    }
}
if ($order) {
    $order_data = $order->get_data();

    // custom Phase 3
    $plotting_options = unserialize(get_option('plotting_options'));
    $order_completed = get_post_meta($order->get_id() , '_order_time_completed' , true);
    $order_completed_str = get_post_meta($order->get_id() , '_order_time_completed_str' , true);
    $date = date('d-m-Y' , $order_completed_str);
    $method = $order->get_shipping_method();
    $period_time_delivery = '';
    $check_day = false;
    if($method=='Delivery') {
        foreach ($plotting_options as $key => $plotting_option) {
            if ($plotting_option['shipping_method']['title'] == 'Delivery') {
                $period_calc = $plotting_option['period_calc'];
                $period_calc = explode('-' , $period_calc );
                $period_dp   = $plotting_option['period_dp'];
                if( count($period_calc) == 2 ) {
                    if(v3_time_to_minutes($period_calc[1]) > 0 && v3_time_to_minutes($period_calc[0]) > 0) {
                        if( v3_time_to_minutes($period_calc[1]) > v3_time_to_minutes($period_calc[0]) ) {
                            $time_from = $date . ' ' . $period_calc[0];
                            $time_to = $date . ' ' . $period_calc[1];
                        } else {
                            $time_from = $date . ' ' . $period_calc[1];
                            $time_to = $date . ' ' . $period_calc[0];
                        }
                        $time_from_str = strtotime($time_from);
                        $time_to_str = strtotime($time_to);
                        if( $order_completed_str >= $time_from_str && $order_completed_str <= $time_to_str) {
                            $period_time_delivery = $period_dp;
                            if($period_time_delivery) {
                                $period_calc = explode('-' , $period_time_delivery );
                                $period_time_delivery = botak_convert_format_time( $period_calc[0]) .' - '.botak_convert_format_time( $period_calc[1]);
                            }
                            if($plotting_option['date'] == 'next_day') {
                                $check_day = true;
                            }
                        }
                    }
                } 
            }
        }
    }
    if($check_day) {
        $order_completed_str += 24*60*60;
    }


    // end
    $link_order = get_permalink(get_option('woocommerce_myaccount_page_id')) . '/view-order/' . $order->get_id();
    ?>
    <table id="header-infor" style="border-collapse: collapse; width: 100%;" width="100%">
        <tr>
            <td style="width:100%;padding-top:30px;" class="bill-to-th" align="left"><img style="width:100%" class="confim" src="https://botaksign.com/wp-content/plugins/custom-botaksign/assets/images/complete.png"></td>
        </tr>
    </table>    
    <div id="infor" style="margin-top: 20px; width: 100%; height: auto; margin-right: 25px; margin-left: 25px;">
        <span class="info-title" style="color: #27793d; display: block; font-family: segoe-bold; font-size: 15pt;">Hi <?php echo $order_data['billing']['first_name'] . ' ' . $order_data['billing']['last_name']; ?>, </span><br>
        <span class="info-subtext" style="font-family: Myriad-Pro-Semibold; color: #231f20; font-size: 15pt;">Your order <span class="order_id" style="color: #231f20; display: block; font-size: 15pt; font-family: segoe-bold;">#<?php echo $order->get_id(); ?></span> is on its way to you! Here are the delivery details:</span>
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
    <div style="margin-right: 50px; margin-left: 50px; width: 100%; padding-top: 30px;" id="view-order"><span class="view-order-text" style="font-family: segoe-bold; color: #27793d; font-size: 17pt;">Tracking Number: <span class="view-order-text-number" style="font-family: segoe-bold; color: #231f20; font-size: 17pt;">D0012345678</span></span></div>
    <table id="view-order-2" style="border-collapse: collapse; margin-right: 50px; margin-left: 50px; width: 90%;" width="100%">
        <tr>
            <td style="width:60%;padding-top:20px;" align="left">
                <span class="information-title-2" style="color: #27793d; display: block; font-family: segoe-bold; font-size: 17pt;">Delivery Method</span><br>
                <span class="information-sub-title-3" style="color: black; display: block; font-family: segoe-bold; font-size: 14pt;">Delivery: 2 - 3 days</span><br>
                <span class="information-sub-title-4" style="color: black; display: block; font-family: segoe-bold; font-size: 14pt;">Track your order <a class="information-sub-title-4-link" href="<?php echo $link_order; ?>" style="font-family: segoe-bold; color: #fcaf17;">HERE</a></span><br><br>
                <!-- <span class="information-title-3" style="color: #27793d; display: block; font-family: segoe-bold; font-size: 17pt;">Estimated Delivery Date</span><br>
                <span class="information-sub-title-2" style="color: black; display: block; font-family: segoe-bold; font-size: 14pt;"><?php echo $period_time_delivery.' '.date("d F Y" , $order_completed_str); ?></span><br><br> -->
                <span class="information-sub-title-4" style="color: black; display: block; font-family: segoe-bold; font-size: 14pt;">Download your order invoice <a class="information-sub-title-4-link" href="<?php echo $link_order; ?>" style="font-family: segoe-bold; color: #fcaf17;">HERE</a></span>
            </td>
            <td style="width:40%;padding-top:-50px;" align="right">
                <span class="information-title" style="color: #27793d; display: block; font-family: segoe-bold; font-size: 17pt;">Deliver to:</span><br>
                <span class="information-sub-title" style="color: black; display: block; font-family: segoe-bold; font-size: 14pt;"><?php echo $order_data['shipping']['first_name'] . ' ' . $order_data['shipping']['last_name']; ?></span><br>
                <span class="information-sub" style="color: #231f20; font-size: 14pt;"><?php echo $order_data['shipping']['address_1']; ?><br>
                    <?php echo $order_data['shipping']['address_2']; ?><br>
                    <?php echo $order_data['shipping']['country'] . ' ' . $order_data['shipping']['postcode']; ?></span>
            </td>
        </tr>
    </table>
    <div id="order-note" style="width: 90%;margin-top: 20px;padding-left: 50px;padding-right: 50px;padding-bottom: 35px;text-align: left;color: #231f20;font-size: 15pt;"><span class="order-note-text">Feel free to drop us an email at <a href="#" class="order-note-link" style="color: #27793d;font-family: segoe-bold;">info@botaksign.com.sg</a>, or give us a call at
            <a href="#" class="order-note-link" style="color: #27793d;font-family: segoe-bold;">6286 2298</a> if you have any enquiries. Thank you for ordering with us!</span></div>
    <div id="line-border" style="border-bottom: 1px solid #a3cf62; width: 200pt; margin: 0px auto; padding-top: 20px;"></div>
<?php } ?>



<?php 
$email_button_title = "Order Confirmed";
$email_button_color = "#1BCB3F";
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
    $order_id = $order->get_id();
    $order_data = $order->get_data();
    $link_order = get_permalink(get_option('woocommerce_myaccount_page_id')) . '/view-order/' . $order_id;
    $est_time = show_est_completion($order);
    $plotting_options = unserialize(get_option('plotting_options'));
    $shippting_method = $order->get_shipping_method();

    $time_completed_display = 'Estimated Completion Time : ' . get_post_meta($order_id, '_order_time_completed', true);
    $order_completed_str = get_post_meta($order->get_id() , '_order_time_completed_str' , true);
    $date = date('d-m-Y' , $order_completed_str);

    $check_day = false;
    if($shippting_method != 'Self-collection') {
        $period_display = '';
        foreach ($plotting_options as $key => $plotting_option) {
            if ($plotting_option['shipping_method']['title'] == 'Delivery') {
                $period_calc = $plotting_option['period_calc'];
                $period_calc = explode('-' , $period_calc );
                $period_dp   = $plotting_option['period_dp'];
                $period_dp_array = explode('-' , $period_dp );
                if( v3_time_to_minutes($period_dp_array[1]) > v3_time_to_minutes($period_dp_array[0]) ) {
                    $time_from = $date . ' ' . $period_dp_array[0];
                    $time_to = $date . ' ' . $period_dp_array[1];
                    $period_display = v3_convert_time_adv($time_from).' to '.v3_convert_time_adv($time_to);
                } else {
                    $time_from = $date . ' ' . $period_dp_array[1];
                    $time_to = $date . ' ' . $period_dp_array[0];
                    $period_display = v3_convert_time_adv($time_from).' to '.v3_convert_time_adv($time_to);
                }
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
        if($check_day) {
            $order_completed_str += 24*60*60;
        }
        $time_completed_display = 'Estimated Delivery Time : ' . date("d/m/Y" , $order_completed_str). ' (' . $period_display . ')';
    }

    ?>
    <div style="margin-bottom: 25px;">
        <span class="info-title" style="display:block;font-size:17px; line-height: 20px; font-weight: 500; margin-bottom: 12px;"><?php printf( esc_html__( 'Hi %s,', 'woocommerce' ), esc_html( $order->get_billing_first_name() ) ); ?></span>
        <span class="info-subtext" style="font-size:14px !important; line-height: 24px; color:#231f20;">Thanks for ordering with us! Your order <span style="font-weight: 500;">#<?php echo $order_id; ?></span> has been placed successfully. You’ll receive another email notifying you when your order is ready for <?php echo $shippting_method == 'Self-collection' ? 'collection' : 'delivery' ?>.</span>
    </div>
    <div style="border: 1px solid #ECECEC; box-shadow: 0px 0px 12px #0000001F; border-radius: 1em; padding: 20px; overflow: hidden; background: #fafafa;">
        <table class="product" style="border-collapse:collapse;">
            <tbody><tr>
                    <td class="stt" align="center" style="width:50px;padding-bottom:5px;padding-left:5px;color:#000000;font-weight: 500;font-size:14px; line-height: 24px;border-bottom-width:1px;border-bottom-style:solid;border-bottom-color:#ECECEC;">No.</td>
                    <td class="description" align="left" style="width:300px;padding-bottom:5px;color:#000000;font-weight: 500;font-size:14px; line-height: 24px; border-bottom-width:1px;border-bottom-style:solid;border-bottom-color:#ECECEC;">Item</td>
                    <td class="qty" align="center" style="width:30px;padding-bottom:5px;color:#000000;font-weight: 500;font-size:14px; line-height: 24px; border-bottom-width:1px;border-bottom-style:solid;border-bottom-color:#ECECEC;">Qty</td>
                    <td class="amount" align="right" style="width:180px;padding-bottom:5px;color:#000000;font-weight: 500;font-size:14px; line-height: 24px; border-bottom-width:1px;border-bottom-style:solid;border-bottom-color:#ECECEC;">Price</td>
                </tr>
                <?php
                $items = $order->get_items();
                $d = 1;
                $subtotal = 0;
                foreach ($items as $item) {
                    $subtotal+=$item['line_total'];
                    if (isset($item['variation_id']) && $item['variation_id'] > 0):
                        $_product = wc_get_product($item['variation_id']);
                    else:
                        $_product = wc_get_product($item['product_id']);
                    endif;
                    if (isset($_product) && $_product != false) {
                        ?>
                        <tr>
                            <td class="stt-text" align="center" style="width:50px;color:#231f20;font-size: 14px;line-height: 24px;padding-bottom:5px;padding-top:5px;"><?php echo $d; ?></td>
                            <td align="left" style="width:300pxpadding-bottom:5px;padding-top:5px;">
                                <?php echo $_product->get_title();?>
                            </td>
                            <td class="qty-text" align="center" style="width:30px;color:#231f20;font-size: 14px;line-height: 24px;padding-bottom:5px;padding-top:5px;"><?php echo ($item['quantity'] > 0 ? $item['quantity'] : 0); ?></td>
                            <td class="amount-text" align="right" style="width:180px;color:#231f20;font-size: 14px;line-height: 24px;padding-bottom:5px;padding-top:5px;"><?php echo wc_price($item['line_total']); ?></td>
                        </tr>
                        <?php
                    }
                    $d++;
                }
                $gst = $subtotal * 7 / 100;
                if ($order_data['shipping_total'] > 0) {
                    $gst += ($order_data['shipping_total'] * 7/100 );
                }
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
                                    <td class="gst" align="left" style="width: 60px;color:#231f20;display:block;font-size: 14px; line-height: 20px;">Shipping</td>
                                    <td class="gst-price" align="right" style="width: 140px;border-bottom-width:1px;"><?php echo $shippting_method == 'Self-collection' ? 'Self-collection' : wc_price($order_data['shipping_total']); ?></td>
                                </tr>
                                <tr>
                                    <td class="gst" align="left" style="width: 60px;color:#231f20;display:block;font-size: 14px; line-height: 20px;">GST</td>
                                    <td class="gst-price" align="right" style="width: 140px;"><?php echo wc_price($gst); ?></td>
                                </tr>
                                <tr style="border-top-width:1px;border-top-style:solid;border-top-color:#ECECEC;border-bottom-width:1px;border-bottom-style:solid;border-bottom-color:#ECECEC;">
                                    <td class="total" align="left" style="position: relative;top: 1px;width: 60px;color:#231f20;display:block;font-size: 14px !important; line-height: 20px; font-weight: 500; ">Total</td>
                                    <td class="total-price" align="right" style="width: 140px;border-bottom-width:1px;"><?php echo wc_price($subtotal + $gst + $order_data['shipping_total']); ?></td>
                                </tr>
                            </tbody></table>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div style="margin: 20px 0; color:#221F1F;font-size: 14px !important; line-height: 20px;">
        <div style="font-weight: 500; margin-bottom: 5px;">
            <span class="disclaimer-text2"><?php echo $time_completed_display; ?></span>
        </div>
        <div>
            <span class="view-order-text" style="font-weight: 300">View your order status <a class="view-order-text-link" href="<?php echo $link_order; ?>" style="color:#1BCB3F;">HERE</a></span>
        </div>
    </div>
<?php } ?>
<div style="display: flex; justify-content: center; width: 100%;">
    <div style="border-top-width: 2px; border-top-style: solid; border-top-color: #ECECEC; width: 200px;"></div>
</div>

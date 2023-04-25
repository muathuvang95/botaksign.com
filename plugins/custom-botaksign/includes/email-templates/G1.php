<?php 
$email_button_title = "Order Received";
$email_button_color = "transparent linear-gradient(0deg, #1BCB3F 0%, #45D242 33%, #7BDB46 78%, #91DF48 100%) 0% 0% no-repeat padding-box";
?>

<table id="header-logo" style="width:100%;padding-top:20px;border-collapse:collapse;margin-bottom:45px;">
    <tbody>
        <tr>
            <td align="left" style="width:50%;"><img class="logo" src="<?php echo CUSTOM_BOTAKSIGN_URL . '/assets/images/logo-transparent.png'; ?>" style="margin-left:0px;margin-top:0px;height: 56px; width: auto;"></td>
            <td align="right" style="width:50%;">
                <?php if($email_button_title && $email_button_color) {
                    echo '<button class="status-button" style="background: '. $email_button_color .';box-shadow: 0px 10px 20px #00000029; border: none; color: #fff; padding: 14px 25px; font-size: 20px; line-height: 28px; border-radius: 10px;">'. $email_button_title .'</button>';
                } ?>
            </td>
        </tr>
    </tbody>
</table>

<?php
if ($order) {
    $order_id = $order->get_id();
    $link_order = get_permalink(get_option('woocommerce_myaccount_page_id')) . '/view-order/' . $order_id;
    $est_time = show_est_completion($order);
    $shippting_method = $order->get_shipping_method();

    $date_completed = date( 'd/m/Y H:i a' , strtotime($est_time['production_datetime_completed']) );
    $time_completed_display = 'Estimated Completion Date : ' . $date_completed;
    $order_completed_str = strtotime($est_time['production_datetime_completed']);

    if($shippting_method != 'Self-collection') {
        $est_delivery_time = unserialize(get_option('est_delivery_time'));
        $added_date = (float) isset($est_delivery_time[$shippting_method]) && isset($est_delivery_time[$shippting_method]['added_date']) ? $est_delivery_time[$shippting_method]['added_date'] : 0;
        $period_display = isset($est_delivery_time[$shippting_method]) && isset($est_delivery_time[$shippting_method]['period_display']) ? $est_delivery_time[$shippting_method]['period_display'] : 0;
        if($added_date) {
            $order_completed_str += $added_date * 24*60*60;
        }
        $time_completed_display = 'Estimated Delivery Date : ' . date("d/m/Y" , $order_completed_str). $period_display;
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
                foreach ($items as $item) {
                    $_product = wc_get_product($item['product_id']);
                    if (isset($item['variation_id']) && $item['variation_id'] > 0) {
                        $_product = wc_get_product($item['variation_id']);
                    }
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
                    $d ++;
                }
                $gst = 0;
                $taxs = array_slice($order->get_taxes(), 0, 1);
                if($taxs) {
                    $gst = array_shift($taxs)->get_rate_percent( 'view' );
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
                                    <td class="subtotal-price" align="right" style="width: 140px;"><?php echo wc_price($order->get_subtotal()); ?></td>
                                </tr>
                                <tr>
                                    <td class="gst" align="left" style="width: 60px;color:#231f20;display:block;font-size: 14px; line-height: 20px;">Shipping</td>
                                    <td class="gst-price" align="right" style="width: 140px;border-bottom-width:1px;"><?php echo $shippting_method == 'Self-collection' ? 'Self-collection' : wc_price($order->get_shipping_total()); ?></td>
                                </tr>
                                <tr>
                                    <td class="gst" align="left" style="width: 60px;color:#231f20;display:block;font-size: 14px; line-height: 20px;"><?php echo $gst; ?>% GST</td>
                                    <td class="gst-price" align="right" style="width: 140px;"><?php echo wc_price($order->get_total_tax()); ?></td>
                                </tr>
                                <tr style="border-top-width:1px;border-top-style:solid;border-top-color:#ECECEC;border-bottom-width:1px;border-bottom-style:solid;border-bottom-color:#ECECEC;">
                                    <td class="total" align="left" style="position: relative;top: 1px;width: 60px;color:#231f20;display:block;font-size: 14px !important; line-height: 20px; font-weight: 500; ">Total</td>
                                    <td class="total-price" align="right" style="width: 140px;border-bottom-width:1px;"><?php echo wc_price($order->get_total()); ?></td>
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

<div style="text-align: center; width: 100%;">
    <div style="display:inline-block; border-top-width: 2px; border-top-style: solid; border-top-color: #ECECEC; width: 200px;"></div>
</div>

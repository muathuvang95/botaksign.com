    <?php 
$email_button_title = "Order Complete";

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
    $time_completed_display = date( 'd F Y H:i a' , strtotime($est_time['production_datetime_completed']) );
    $order_completed_str = strtotime($est_time['production_datetime_completed']);
    if($shippting_method != 'Self-collection') {
        $est_delivery_time = unserialize(get_option('est_delivery_time'));
        $added_date = (float) isset($est_delivery_time[$shippting_method]) && isset($est_delivery_time[$shippting_method]['added_date']) ? $est_delivery_time[$shippting_method]['added_date'] : 0;
        $period_display = isset($est_delivery_time[$shippting_method]) && isset($est_delivery_time[$shippting_method]['period_display']) ? $est_delivery_time[$shippting_method]['period_display'] : 0;
        if($added_date) {
            $order_completed_str += $added_date * 24*60*60;
        }
        $time_completed_display = date("d F Y" , $order_completed_str). $period_display;
    }

    // end
    ?>
     <div style="margin-bottom: 25px;">
        <span class="info-title" style="display:block;font-size:17px; line-height: 20px; font-weight: 500; margin-bottom: 12px;"><?php printf( esc_html__( 'Hi %s,', 'woocommerce' ), esc_html( $order->get_billing_first_name() ) ); ?></span>
        <?php
            if($shippting_method == 'Self-collection') {
                ?>
                <span class="info-subtext" style="font-size:17px !important; line-height: 24px; color:#231f20;">Your order <span style="font-size:20px;font-weight: 600;">#<?php echo $order->get_id(); ?></span> is ready for collection! Here are the collection details :</span>
                <?php
            } else {
               ?>
                <span class="info-subtext" style="font-size:17px !important; line-height: 24px; color:#231f20;">Your order <span style="font-size:20px;font-weight: 600;">#<?php echo $order->get_id(); ?></span> is on its way to you! Here are the delivery details :</span>
                <?php 
            }
        ?> 
    </div>

    <?php
    if($shippting_method == 'Self-collection') {
        ?>
            <div style="border: 1px solid #ECECEC; box-shadow: 0px 0px 12px #0000001F; border-radius: 1em; padding: 20px; overflow: hidden; background: #fafafa;color:#000000;font-weight: 400;font-size:14px; line-height: 24px;">
                <div class="stt" align="left" style="padding-bottom:15px;">
                    <div style="padding-bottom:5px;color:#000000;font-weight: 600;font-size:17px;">Collection at : </div>
                    <div style="font-size:14px;line-height:17px;">22 Yio Chu Kang Road #01-19 Highland Centre Singapore 545535</div>
                </div>
                <div style="width: 100%; display: flex;">
                    <div style="width: 65%;">
                        <div class="stt" align="left" style="padding-bottom:15px;">
                            <div style="padding-bottom:5px;color:#000000;font-weight: 600;font-size:17px;">Operation Hours : </div>
                            <div style="font-size:14px;line-height:17px;">Open Mon - Fri 9am - 5pm, Sat 9am - 1pm</div>
                            <div style="font-size:14px;line-height:17px;">Closed on Sundays and public holidays</div>
                        </div>
                        <div class="stt" align="left" style="padding-bottom:15px;">
                            <div style="padding-bottom:5px;color:#000000;font-weight: 600;font-size:17px;">Note : </div>
                            <div style="font-size:14px;line-height:17px;">Kindly show this email upon collection as proof of purchase.</div>
                        </div>
                    </div>
                    <div style="width: 35%;display: flex;justify-content: flex-end; align-items: flex-end;">
                        <div style="width: 100%;height: 0;padding-bottom: 100%; position: relative;overflow: hidden;">
                            <img style="width: 100%; height: auto; position: absolute;" src="<?php echo esc_attr(CUSTOM_BOTAKSIGN_URL.'assets/images/gif3.gif'); ?>" alt="">
                        </div>
                    </div>
                </div>
            </div>
        <?php
    } else {
         ?>
            <div style="border: 1px solid #ECECEC; box-shadow: 0px 0px 12px #0000001F; border-radius: 1em; padding: 20px; overflow: hidden; background: #fafafa;color:#000000;font-weight: 400;font-size:14px; line-height: 24px;">
                <div style="display: flex;">
                    <div style="width: 65%;">
                        <div class="stt" align="left" style="padding-top:15px;">
                            <div style="padding-bottom:5px;color:#000000;font-weight: 600;font-size:17px;">Delivery Method : </div>
                            <div><?php echo $shippting_method; ?></div>
                        </div>
                        <div class="stt" align="left" style="padding-top:15px;">
                            <div style="padding-bottom:5px;color:#000000;font-weight: 600;font-size:17px;">Estimated Delivery Date : </div>
                            <div><?php echo $time_completed_display; ?></div>
                        </div>
                        <div class="stt" align="left" style="padding-top:15px; font-size:14px; line-height: 19px;">
                            <div style="padding-bottom:5px;color:#000000;font-weight: 600;font-size:17px;">Deliver to : </div>
                            <div><?php echo $order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name(); ?></div>
                            <div><?php echo $order->get_shipping_address_1(); ?></div>
                            <div><?php echo $order->get_shipping_address_2(); ?></div>
                            <div><?php echo $order->get_shipping_company() . ' ' . $order->get_shipping_postcode(); ?></div>
                            <div><?php echo $order->get_billing_email(); ?></div>
                            <div><?php echo $order->get_billing_phone(); ?></div>
                        </div>
                    </div>
                    <div style="width: 35%;display: flex;justify-content: flex-end; align-items: flex-end;">
                        <div style="width: 100%;height: 0;padding-bottom: 100%; position: relative;overflow: hidden;">
                            <img style="width: 100%; height: auto;position: absolute;" src="<?php echo esc_attr(CUSTOM_BOTAKSIGN_URL.'assets/images/gif3.gif'); ?>" alt="">
                        </div>
                    </div>
                </div>
            </div>
        <?php
    }
}
?>


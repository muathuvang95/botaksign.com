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
    $order_data = $order->get_data();
    $link_order = get_permalink(get_option('woocommerce_myaccount_page_id')) . '/view-order/' . $order_id;
    $est_time = show_est_completion($order);
    $plotting_options = unserialize(get_option('plotting_options'));
    $shippting_method = $order->get_shipping_method();

    $order_completed_str = get_post_meta($order->get_id() , '_order_time_completed_str' , true);
    $time_completed_display = get_post_meta($order_id, '_order_time_completed', true);

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
        $time_completed_display = date("d/m/Y" , $order_completed_str). ' (' . $period_display . ')';
    }

    // end

    ?>
     <div style="margin-bottom: 25px;">
        <span class="info-title" style="display:block;font-size:17px; line-height: 20px; font-weight: 500; margin-bottom: 12px;"><?php printf( esc_html__( 'Hi %s,', 'woocommerce' ), esc_html( $order->get_billing_first_name() ) ); ?></span>
        <span class="info-subtext" style="font-size:17px !important; line-height: 24px; color:#231f20;">Your order <span style="font-size:20px;font-weight: 600;">#<?php echo $order->get_id(); ?></span> is on its way to you! Here are the <?php echo $shippting_method == 'Self-collection' ? 'collection' : 'delivery'; ?> details :</span>
    </div>

    <?php
    if($shippting_method == 'Self-collection') {
        ?>
            <div style="border: 1px solid #ECECEC; box-shadow: 0px 0px 12px #0000001F; border-radius: 1em; padding: 20px; overflow: hidden; background: #fafafa;color:#000000;font-weight: 400;font-size:14px; line-height: 24px;">
                <div class="stt" align="left" style="padding-bottom:5px;">
                    <div style="padding-bottom:5px;color:#000000;font-weight: 600;font-size:17px;">Collection at : </div>
                    <div style="font-size:14px;line-height:17px;">22 Yio Chu Kang Road #01-19 Highland Centre Singapore 545535</div>
                </div>
                <div style="width: 100%; display: flex;">
                    <div style="width: 50%;">
                        <div class="stt" align="left" style="padding-bottom:5px;">
                            <div style="padding-bottom:5px;color:#000000;font-weight: 600;font-size:17px;">Operation Hours : </div>
                            <div style="font-size:14px;line-height:17px;">Open Mon - Fri 9am - 5pm, Sat 9am - 1pm</div>
                            <div style="font-size:14px;line-height:17px;">Closed on Sundays and public holidays</div>
                        </div>
                        <div class="stt" align="left" style="padding-bottom:5px;">
                            <div style="padding-bottom:5px;color:#000000;font-weight: 600;font-size:17px;">Note : </div>
                            <div style="font-size:14px;line-height:17px;">Kindly show this email upon collection as proof of purchase.</div>
                        </div>
                    </div>
                    <div style="width: 50%;display: flex;justify-content: flex-end;">
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
                    <div style="width: 50%;">
                        <div class="stt" align="left" style="padding-bottom:5px;">
                            <div style="padding-bottom:5px;color:#000000;font-weight: 600;font-size:17px;">Estimated Delivery Date : </div>
                            <div><?php echo $time_completed_display; ?></div>
                        </div>
                        <div class="stt" align="left" style="padding-bottom:5px; font-size:14px; line-height: 19px;">
                            <div style="padding-bottom:5px;color:#000000;font-weight: 600;font-size:17px;">Deliver to : </div>
                            <div><?php echo $order_data['billing']['first_name'] . ' ' . $order_data['billing']['last_name']; ?></div>
                            <div><?php echo $order_data['billing']['address_1']; ?></div>
                            <div><?php echo $order_data['billing']['address_2']; ?></div>
                            <div><?php echo $order_data['billing']['country'] . ' ' . $order_data['billing']['postcode']; ?></div>
                            <div><?php echo $order_data['billing']['email']; ?></div>
                            <div><?php echo $order_data['billing']['phone']; ?></div>
                        </div>
                    </div>
                    <div style="width: 50%;display: flex;justify-content: flex-end;">
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
<div style="display: flex; justify-content: center; width: 100%;">
    <div style="border-top-width: 2px; border-top-style: solid; border-top-color: #ECECEC; width: 200px;"></div>
</div>

<?php 
function v3_get_time_completed_item($max_production_time , $order){
    if ($order->get_date_created()) {
        $calc_production_date = calc_production_date($order->get_date_created(), $max_production_time * 60);
        $time_shipping = calc_completed_shipping_date($order)*3600;
        $time_delivered = $time_shipping*3600  + strtotime($calc_production_date);
        $calc_shipping_date = date( "H:i Y/m/d" , $time_delivered );
        $production_datetime_completed = date("l, d F Y, h:i A", strtotime($calc_production_date));
        $production_date_completed = date("l, d F Y", strtotime($calc_production_date));
        $shipping_date_completed = date("l, d F Y", strtotime($calc_shipping_date));
        $shipping_datetime_completed = date("l, d F Y, h:i A", strtotime($calc_shipping_date));
        if ($max_shipping_time == 0) {
            return [
                'total_time' => $production_datetime_completed,
                'production_datetime_completed' => $production_datetime_completed,
                'production_date_completed' => $production_date_completed,
                'shipping_date_completed' => $shipping_date_completed,
                'shipping_datetime_completed' => $shipping_datetime_completed,
            ];
        } else {
            return [
                'total_time' => $production_date_completed . ' - ' . $shipping_date_completed,
                'production_datetime_completed' => $production_datetime_completed,
                'production_date_completed' => $production_date_completed,
                'shipping_date_completed' => $shipping_date_completed,
                'shipping_datetime_completed' => $shipping_datetime_completed,
            ];
        }
    } else {
        return [
            'total_time' => date("l, d F Y, H:i", strtotime('00:00')),
            'production_datetime_completed' => date("l, d F Y, h:i A", strtotime('00:00')),
            'production_date_completed' => date("l, d F Y, h:i A", strtotime('00:00')),
            'shipping_date_completed' => date("l, d F Y, h:i A", strtotime('00:00')),
            'shipping_datetime_completed' => date("l, d F Y, h:i A", strtotime('00:00')),
        ];
    }
}
function v3_getLinkAWS($order_id) {
    global $wpdb;
    if (is_plugin_active('upload-aws-custom-db/upload-aws-custom-db.php')) {
        $sql = "SELECT link_download FROM $wpdb->prefix" . "aws_order_download WHERE id_order = $order_id";
        $results = $wpdb->get_results($sql);
        if (isset($results[0])) {
            $link_s3 = $results[0]->link_download;
            $link_s3 = str_replace('botaksignorder.s3.amazonaws', 'botaksignorder.s3.ap-southeast-1.amazonaws', $link_s3);
            $url = parse_url($link_s3);
            if($url['scheme'] == 'http' ) {
                 $link_s3 = str_replace('http' , 'https' , $link_s3);
            }
            return $link_s3;
        }
    }
    return '';
}
function v3_get_specialist_linked($user_id) {
    $user =  get_userdata($user_id);
    $user_name = $user->display_name;
    $linkeds = array();
    $linkeds[] = array(
        'name'  => $user_name,
        'id'    => $user_id,
    );
    $datas = unserialize(get_user_meta( $user_id, 'group_specialist' , true));
    if($datas) {
        foreach ($datas as $key => $value) {
            $u = get_userdata($value);
            $un = $u->display_name;
            $linkeds[] = array(
                'name'  => $un,
                'id'    => (int)$value,
            );
        }
    }
    return $linkeds;
}
function v3_get_production_time_item($item , $order , $format = true) {
    $max_production_time = 0;
    $have_pt = false;
    $pre_name = 'Standard';
    $user_id = $order->get_user_id();
    $user_meta =get_userdata($user_id);
    $role_use = '';
    if(isset($user_meta)) {
        $role_use = $user_meta->roles[0];
    }
    $have_role_use = false;
    $have_check_default = false;
    if ($item->get_meta('_nbo_options') && $item->get_meta('_nbo_field')) {
        $qty = $item->get_quantity();
        $options = $item->get_meta('_nbo_options');
        if( nbd_is_base64_string( $options['fields'] ) ){
            $options['fields'] = base64_decode( $options['fields'] );
        }
        $origin_fields = unserialize($options['fields']);
        $origin_fields = $origin_fields['fields'];
        $item_field = $item->get_meta('_nbo_field');
        $value = 0;
        if($origin_fields) {
            foreach ($origin_fields as $field) {
                $option_seleted = false;
                foreach ($item_field as $k => $v) {
                    if($k == $field['id'] && isset($v['value']) ) {
                        $value = $v['value'];
                        $option_seleted = true;
                    } 
                }
                if(!$option_seleted) continue;
                if (isset($field['nbd_type']) && $field['nbd_type'] === 'production_time') {
                    $have_pt = true;
                    if(isset($field['general']['role_options'])) {
                        foreach ($field['general']['role_options'] as $role_options) {
                            if($role_options['role_name'] ==  $role_use) {
                                $time_quantity_breaks_1 = $role_options['options'][$value]['time_quantity_breaks'];
                                $pre_name1 = $role_options['options'][$value]['name'];
                                $have_role_use = true;
                            }
                            if(isset($role_options['check_default']) && ( $role_options['check_default'] == 'on' || $role_options['check_default'] == '1' )) {
                                $have_check_default = true;
                                $time_quantity_breaks_2 = $role_options['options'][$value]['time_quantity_breaks'];
                                $pre_name2 = $role_options['options'][$value]['name'];
                            }  
                        }
                    }
                    if($have_role_use) {
                        $time_quantity_breaks = $time_quantity_breaks_1;
                        $pre_name =  $pre_name1;
                    }
                    if(!$have_role_use && $have_check_default ) {
                        $time_quantity_breaks = $time_quantity_breaks_2;
                        $pre_name =  $pre_name2;
                    }
                    if(empty($time_quantity_breaks)) {
                        $have_pt = false;
                        break;
                    }
                    // if(count($time_quantity_breaks) <= 1) {
                    //     $max_production_time = (float)$time_quantity_breaks[0]['time'];
                    // } else {
                    //     for ($i = 0; $i < count($time_quantity_breaks); $i++) {
                    //         if ($qty >= $time_quantity_breaks[$i]['qty'] && $qty <= $time_quantity_breaks[$i + 1]['qty'] ) {
                    //             $max_production_time = (float)$time_quantity_breaks[$i]['time'];
                    //         }
                    //     }
                    // }
                    //Sort time_quantity_breaks by quantity
                    usort($time_quantity_breaks, "sort_time_quantity_breaks");
                    if(count($time_quantity_breaks) <= 1) {
                        $max_production_time = (float)$time_quantity_breaks[0]['time'];
                    } else {
                        for ($i = 0; $i < count($time_quantity_breaks); $i++) {
                            if ($i === count($time_quantity_breaks) - 1) {
                                if ($qty >= $time_quantity_breaks[$i]['qty'] && (int)$time_quantity_breaks[$i]['time'] > $max_production_time) {
                                    $max_production_time = (float)$time_quantity_breaks[$i]['time'];
                                }
                                break;
                            }
                            if ($qty >= $time_quantity_breaks[$i]['qty'] && $qty < $time_quantity_breaks[$i + 1]['qty'] && (int)$time_quantity_breaks[$i]['time'] > $max_production_time) {
                                $max_production_time = (float)$time_quantity_breaks[$i]['time'];
                            }
                        }
                    }
                }
            }
        }
    } 
    if(!$have_pt || $max_production_time == 0) {
        $pre_name = 'Standard';
        $qty = $item->get_quantity();
        $_productiton_time_default = array();
        $productiton_time_default = unserialize(nbdesigner_get_option('nbdesigner_product_time_default'));
        // Convert array
        for( $f =0; $f < count($productiton_time_default[0]); $f++ ) {
            $_productiton_time_default[$f]['qty'] = $productiton_time_default[0][$f] ;
            $_productiton_time_default[$f]['time'] = $productiton_time_default[1][$f] ;
        }
        if(count($_productiton_time_default) <= 1) {
            $max_production_time = (float)$_productiton_time_default[0]['time'];
        } else {
            for ($i = 0; $i < count($_productiton_time_default); $i++) {
                if ($qty >= $_productiton_time_default[$i]['qty'] && $qty < $_productiton_time_default[$i + 1]['qty'] ) {
                    $max_production_time = (float)$_productiton_time_default[$i]['time'];
                }
            }
        }
    }
    if($format) {
        return $max_production_time;
    } else {
        return $pre_name. ' - ' .$max_production_time . ' h';
    }
}

add_action('admin_menu', 'cs_admin_menu');
function cs_admin_menu() {
    add_submenu_page('woocommerce', 'Custom Status Order' , 'Custom Status Order' , 'manage_options', 'custom-status-order', 'custom_status_order' );
}
function custom_status_order() {
    $custom_status_order = array(
        'pending_payment'            => array(
            'label'    => 'Pending Payment',
            'check'    => 'on',
            'index'    => 1,
            'df'       => true,
        )
        ,'order_received'            => array(
            'label'    => 'Order Received',
            'check'    => 'on',
            'index'    => 2,
            'df'       => true,
        )
        ,'processing'            => array(
            'label'    => 'Processing',
            'check'    => 'on',
            'index'    => 3,
            'df'       => true,
        )
        ,'artwork_amendment'            => array(
            'label'    => 'Artwork Amendment',
            'check'    => 'on',
            'index'    => 4,
            'df'       => true,
        )
        ,'collection_point'            => array(
            'label'    => 'Collection Point',
            'check'    => 'on',
            'index'    => 5,
            'df'       => true,
        )
        ,'collected'            => array(
            'label'    => 'Collected',
            'check'    => 'on',
            'index'    => 6,
            'df'       => true,
        )
        ,'cancelled'            => array(
            'label'    => 'Cancelled',
            'check'    => 'on',
            'index'    => 7,
            'df'       => true,
        )
        ,'out_source'            => array(
            'label'    => 'Outsource',
            'check'    => '',
            'index'    => 8,
            'df'       => false,
        ),
        'printing'              => array(
            'label'    => 'Printing',
            'check'    => '',
            'index'    => 9,
            'df'       => false,
        ),
        'finishing_1'           => array(
            'label'    => 'Finishing',
            'check'    => '',
            'index'    => 10,
            'df'       => false,
        ),
        'finishing_2'           => array(
            'label'    => 'DTP',
            'check'    => '',
            'index'    => 11,
            'df'       => false,
        ),
        'qc_packing'            => array(
            'label'    => 'QC / Packing',
            'check'    => '',
            'index'    => 12,
            'df'       => false,
        ),
        'delivery'              => array(
            'label'    => 'Delivery',
            'check'    => '',
            'index'    => 13,
            'df'       => false,
        ),
    );
    if( isset($_POST['status-updated']) ) {
        $add_order_status = isset($_POST['custom_status_order']) ? $_POST['custom_status_order'] : '';
        $save_status_order = array();
        foreach ($add_order_status as $key => $value) {
            if( isset($custom_status_order[$key]) ) {
               $custom_status_order[$key]['index'] = $value['index'];
               if(isset($value['check']) && $value['check'] == 'on') {
                    $custom_status_order[$key]['check'] = 'on';
               }
            }
        }
        $_custom_status_order = array_column($custom_status_order, 'index');
        array_multisort($_custom_status_order, SORT_ASC, $custom_status_order);
        foreach($custom_status_order as $key => $value) {
            if(isset($value['check']) && $value['check'] == 'on') {
                $save_status_order[$key] = $value['label'];
            }
        }
        update_option('custom_status_order' , serialize($save_status_order));
        update_option('_custom_status_order' , serialize($custom_status_order));
    }
    $get_status_order = unserialize(get_option('_custom_status_order'));
    if(!$get_status_order) {
        $get_status_order = $custom_status_order;
    }
    ?>
    <style type="text/css">
        .remove_row {
            cursor: pointer;
            text-align: center;
        }
    </style>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" crossorigin="anonymous">
    <div id="custom-status-order">
        <form method='post' action=''>
            <table class="table table-striped table-bordered" style="width: 500px;">
                <thead>
                    <tr>
                        <td><b>No.</b></td>
                        <td><b>Status</b></td>
                        <td>Add</td>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    foreach ($get_status_order as $key => $value) {
                    ?>
                        <tr class="row-status">
                            <td><input type="number" name="custom_status_order[<?php echo $key; ?>][index]" value="<?php echo $value['index']; ?>"></td>
                            <td>
                                <div class="label-order-status"><?php echo $value['label']; ?></div>
                            </td>
                            <td>
                                <?php if(!$value['df']) { ?>
                                    <input type="checkbox" name="custom_status_order[<?php echo $key; ?>][check]" <?php if($value['check'] == 'on') { echo 'checked'; } ?> >
                                <?php } ?>
                            </td>
                        </tr>
                    <?php
                    } 
                    ?>
                    
                </tbody>
            </table>
            <p class='submit'>
                <input type='submit' name='Submit' class='button-primary' value='<?php echo 'Save Changes'; ?>' />
                <input type='hidden' name='status-updated' value='true'/>
            </p>
        </form>
    </div>

    <?php
}
/*function custom_status_order() {
    $custom_status_order_df = array(
        'pending_payment'       => 'Pending Payment',
        'order_received'        => 'Order Received',
        'processing'            => 'Processing',
        'artwork_amendment'     => 'Artwork Amendment',
        'collection_point'      => 'Collection Point',
        'collected'             => 'Collected',
        'cancelled'             => 'Cancelled',
    );
    $custom_status_order_cs = array(
        'out_source'            => array(
            'label'    => 'Outsource',
            'check'    => '',
        ),
        'printing'              => array(
            'label'    => 'Printing',
            'check'    => '',
        ),
        'finishing_1'           => array(
            'label'    => 'Finishing 1',
            'check'    => '',
        ),
        'finishing_2'           => array(
            'label'    => 'Finishing 2',
            'check'    => '',
        ),
        'qc_packing'            => array(
            'label'    => 'QC / Packing',
            'check'    => '',
        ),
        'delivery'              => array(
            'label'    => 'Delivery',
            'check'    => '',
        ),
    );

    $save_status_order = $custom_status_order_df;
    if( isset($_POST['status-updated']) ) {
        $add_order_status = isset($_POST['custom_status_order']) ? $_POST['custom_status_order'] : '';
        foreach ($custom_status_order_cs as $key => $value) {
            if($add_order_status[$key] == 'on') {
                $save_status_order[$key] = $value['label'];
            }
            update_option('custom_status_order' , serialize($save_status_order));
        }
    }
    $get_status_order = unserialize(get_option('custom_status_order'));
    if($get_status_order) {
        foreach ($custom_status_order_cs as $key => $value) {
            if ($get_status_order[$key]) {
                $custom_status_order_cs[$key]['check'] = 'on';
            }
        }
    } else {
        $get_status_order = $custom_status_order_cs;
    }
    ?>
    <style type="text/css">
        .remove_row {
            cursor: pointer;
            text-align: center;
        }
    </style>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" crossorigin="anonymous">
    <div id="custom-status-order">
        <form method='post' action=''>
            <table class="table table-striped table-bordered" style="width: 500px;">
                <thead>
                    <tr>
                        <td><b>No.</b></td>
                        <td><b>Status</b></td>
                        <td colspan="2"></td>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $count1 = count($custom_status_order_df);
                    $no = 1;
                    foreach ($custom_status_order_df as $key => $value) {
                    ?>
                        <tr class="row-status">
                            <td><?php echo $no; ?></td>
                            <td>
                                <div class="label-order-status"><?php echo $value; ?></div>
                            </td>
                            <td></td>
                        </tr>
                    <?php
                    $no ++;
                    } 
                    $no = $count1 + 1;
                    foreach ($custom_status_order_cs as $key => $value) {
                    ?>
                        <tr class="row-status">
                            <td><?php echo $no; ?></td>
                            <td>
                                <div class="label-order-status"><?php echo $value['label']; ?></div>
                            </td>
                            <td><input type="checkbox" name="custom_status_order[<?php echo $key; ?>]" <?php if($value['check'] == 'on') { echo 'checked'; } ?> ></td>
                        </tr>
                    <?php
                    $no ++;
                    } 
                    ?>
                    
                </tbody>
            </table>
            <p class='submit'>
                <input type='submit' name='Submit' class='button-primary' value='<?php echo 'Save Changes'; ?>' />
                <input type='hidden' name='status-updated' value='true'/>
            </p>
        </form>
    </div>

    <?php
}*/

function v3_recalc_production_date($order_created_at, $max_production_time)
{
    $h = "8";// time zone of Singapo is (+8)
    $hm = $h * 60;
    $ms = $hm * 60;
    $calc_production_date = $order_created_at;
    $working_time_setting = get_option('working-time-options', true);
    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    $closed_days = [];
    if (isset($working_time_setting['working-days'])) {
        foreach ($days as $day) {
            if (!array_key_exists($day, $working_time_setting['working-days'])) {
                $closed_days[] = $day;
            }
            foreach ($working_time_setting['working-days'] as $wd) {
                if (!isset($working_time_setting[$wd]['open-time']) || !isset($working_time_setting[$wd]['close-time']) || $working_time_setting[$wd]['open-time'] === '' || $working_time_setting[$wd]['close-time'] === '') {
                    $closed_days[] = $wd;
                }
            }
        }
        $check_holiday = false;
        $add_holiday = true;
        $get_holiday = array();
        if(isset($working_time_setting['holidays']['start-holiday'])) {
            $cacl_time_holiday = array();
            foreach ($working_time_setting['holidays']['start-holiday'] as $key => $value) {
                $get_holiday['holidays'][$key]['start-holiday'] = $value;
                $get_holiday['holidays'][$key]['end-holiday'] = $working_time_setting['holidays']['end-holiday'][$key];

            }
            $cacl_time_holiday = $get_holiday['holidays'];
        }
        $count_holiday = 0; 
        // if(isset($get_holiday['holidays']) && $get_holiday['holidays'][0]['start-holiday'] != '' ) {
        //     if( strtotime($get_holiday['holidays'][0]['start-holiday']) > strtotime($calc_production_date) ) {
        //         $cacl_time_holiday = $get_holiday['holidays'];
        //     } else {
        //         foreach ($get_holiday['holidays'] as $key => $value) {
        //             if (strtotime($value['start-holiday']) <= strtotime($calc_production_date) && strtotime( date('0:0 Y/m/d' , strtotime($calc_production_date)) ) <= strtotime($value['end-holiday']) && $add_holiday ) {
        //                 $calc_production_date = $value['end-holiday'] . ' + 1 days';
        //                 $check_holiday = true;
        //                 $count_holiday = $key + 1;
        //                 $cacl_time_holiday = array();
        //             } else {
        //                 $cacl_time_holiday[] = $value;
        //             }
        //             if($check_holiday) {
        //                 $add_holiday = false;
        //             }
        //         }
        //     }
        // }
        $calc_production_date = strtotime($calc_production_date);
        //Check time order with Collection days => Time order
        if( isset($working_time_setting['collection-days']) ) {
            foreach ($days as $day) {
                if (!array_key_exists($day, $working_time_setting['collection-days'])) {
                    $col_closed_days[] = $day;
                }
                foreach ($working_time_setting['collection-days'] as $wd) {
                    if (!isset($working_time_setting[$wd]['col-open-time']) || !isset($working_time_setting[$wd]['col-close-time']) || $working_time_setting[$wd]['col-open-time'] === '' || $working_time_setting[$wd]['col-close-time'] === '') {
                        $col_closed_days[] = $wd;
                    }
                }
            }
            $check_time_order = true;
            $time_order_minute = $calc_production_date;
            $day_order = date('l', $time_order_minute);
            $count_holiday = 0;
            while($check_time_order) {
                $check_time_order = false;
                if( in_array($day_order, $col_closed_days) ) {
                    $time_order_minute += 86400;
                    $day_order = date('l', $time_order_minute);
                    $time_order_minute = strtotime(date('00:00 Y/m/d', $time_order_minute)) + split_time($working_time_setting[$day_order]['col-open-time'])*60 ;
                    $check_time_order = true;
                } 
                if( $working_time_setting[$day_order]['col-open-time'] >  date('H:i', $time_order_minute) ) {
                    $time_order_minute = strtotime(date('00:00 Y/m/d', $time_order_minute)) + split_time($working_time_setting[$day_order]['col-open-time'])*60 ;
                     $check_time_order = true;
                }
                if ( $working_time_setting[$day_order]['col-close-time'] <= date('H:i', $time_order_minute)  ) {
                    $time_order_minute += 86400;
                    $day_order = date('l', $time_order_minute);
                    $time_order_minute = strtotime(date('00:00 Y/m/d', $time_order_minute)) + split_time($working_time_setting[$day_order]['col-open-time'])*60 ;
                    $check_time_order = true;
                }
                if(isset($cacl_time_holiday)) {
                    foreach ($cacl_time_holiday as $key => $period_holiday) {
                        if( strtotime($period_holiday['start-holiday']) <= $time_order_minute && ( strtotime($period_holiday['end-holiday']) + 86399 ) >= $time_order_minute ) {
                            $time_order_minute = strtotime($period_holiday['end-holiday']) + 86400;
                            $day_order = date('l', $time_order_minute);
                            $time_order_minute = strtotime(date('00:00 Y/m/d', $time_order_minute)) + split_time($working_time_setting[$day_order]['col-open-time'])*60 ;
                            $next_day = true;
                            $check_time_order = true;
                            if($key < count($cacl_time_holiday) -1 ) {
                                $count_holiday = $key + 1;
                            } else {
                                $count_holiday = $key;
                            }
                        }
                    }
                } 
            }
            $calc_production_date = $time_order_minute;
        }
        $flag = false;
        $spend_time = 0;
        $time_work = $max_production_time;
        while($spend_time < $max_production_time) {
            $minutes_spend = 0;
            $tmp_day = date('l', $calc_production_date);
            if(date('H:i', $calc_production_date) < $working_time_setting[$tmp_day]['open-time'] ) {
                $calc_production_date = strtotime(date('00:00 Y/m/d', $calc_production_date)) + split_time($working_time_setting[$tmp_day]['open-time'])*60 ;
                $tmp_day = date('l', $calc_production_date);
                $flag = true;
            }
            if(date('H:i', $calc_production_date) > $working_time_setting[$tmp_day]['close-time']) {
                $calc_production_date += 86400;
                $tmp_day = date('l', $calc_production_date);
                $calc_production_date = strtotime(date('00:00 Y/m/d', $calc_production_date)) + split_time($working_time_setting[$tmp_day]['open-time'])*60;
                $flag = true;
            }
            if(in_array($tmp_day, $closed_days)) {
                $calc_production_date += 86400;
                $tmp_day = date('l', $calc_production_date);
                $calc_production_date = strtotime(date('00:00 Y/m/d', $calc_production_date)) + split_time($working_time_setting[$tmp_day]['open-time'])*60;
                $flag = true;
            }
            if(isset($cacl_time_holiday)) {
                foreach ($cacl_time_holiday as $key => $period_holiday) {
                    if( strtotime($period_holiday['start-holiday']) <= $calc_production_date && ( strtotime($period_holiday['end-holiday']) + 86399 ) >= $calc_production_date ) {
                        $calc_production_date = strtotime($period_holiday['end-holiday']) + 86400;
                        $tmp_day = date('l', $calc_production_date);
                        $calc_production_date = strtotime(date('00:00 Y/m/d', $calc_production_date)) + split_time($working_time_setting[$tmp_day]['open-time'])*60;
                        $flag = true;
                        if($key < count($cacl_time_holiday) -1 ) {
                            $count_holiday = $key + 1;
                        } else {
                            $count_holiday = $key;
                        }
                    }
                }
            }
            if( !$flag ) {
                $minutes_spend = (float)date_diff( new DateTime($working_time_setting[$tmp_day]['open-time']), new DateTime(date('H:i', $calc_production_date)) )->format('%h') * 60 + (float)date_diff(new DateTime($working_time_setting[$tmp_day]['open-time']), new DateTime(date('H:i', $calc_production_date)) )->format('%i');
            }
            if( (minute_working_on_day($tmp_day) - $minutes_spend ) >= $time_work) {
                $calc_production_date = $calc_production_date + $time_work*60;
                $_calc_production_date = $calc_production_date;
                break;
            } else {
                $spend_time = $spend_time + minute_working_on_day($tmp_day) - $minutes_spend;
                $time_work = $max_production_time - $spend_time;
                $calc_production_date += 86400;
                $tmp_day = date('l', $calc_production_date);
                $calc_production_date = strtotime(date('00:00 Y/m/d', $calc_production_date)) + split_time($working_time_setting[$tmp_day]['open-time'])*60;
                $flag = true;
                
            }
        }
    }
    if( isset($working_time_setting['collection-days']) ) {
        foreach ($days as $day) {
            if (!array_key_exists($day, $working_time_setting['collection-days'])) {
                $col_closed_days[] = $day;
            }
            foreach ($working_time_setting['collection-days'] as $wd) {
                if (!isset($working_time_setting[$wd]['col-open-time']) || !isset($working_time_setting[$wd]['col-close-time']) || $working_time_setting[$wd]['col-open-time'] === '' || $working_time_setting[$wd]['col-close-time'] === '') {
                    $col_closed_days[] = $wd;
                }
            }
        }
        $col_tmp_day = date('l', $_calc_production_date);
        $condition = true;
        while($condition) {
            $condition = false;
            if( isset($cacl_time_holiday[$count_holiday]) && strtotime($cacl_time_holiday[$count_holiday]['start-holiday']) <= $_calc_production_date && ( strtotime($cacl_time_holiday[$count_holiday]['end-holiday']) + 86399 ) >= $_calc_production_date ) {
                    $_calc_production_date = strtotime($cacl_time_holiday[$count_holiday]['end-holiday']) + 86400;
                    $col_tmp_day = date('l', $_calc_production_date);
                    $_calc_production_date = strtotime(date('00:00 Y/m/d', $_calc_production_date)) + split_time($working_time_setting[$col_tmp_day]['col-open-time'])*60 ;
                    $count_holiday++;
                    $condition = true;
            } else {
                if( in_array($col_tmp_day, $col_closed_days) ) {
                    $_calc_production_date += 86400;
                    $col_tmp_day = date('l', $_calc_production_date);
                    $_calc_production_date = strtotime(date('00:00 Y/m/d', $_calc_production_date)) + split_time($working_time_setting[$col_tmp_day]['col-open-time'])*60 ;
                    $condition = true;
                } else {
                    if( $working_time_setting[$col_tmp_day]['col-open-time'] >  date('H:i', $_calc_production_date) ) {
                        $_calc_production_date = strtotime(date('00:00 Y/m/d', $_calc_production_date)) + split_time($working_time_setting[$col_tmp_day]['col-open-time'])*60 ;
                    }
                    if( $working_time_setting[$col_tmp_day]['col-close-time'] <  date('H:i', $_calc_production_date) ) {
                        $_calc_production_date += 86400;
                        $col_tmp_day = date('l', $_calc_production_date);
                        $_calc_production_date = strtotime(date('00:00 Y/m/d', $_calc_production_date)) + split_time($working_time_setting[$col_tmp_day]['col-open-time'])*60 ;
                        $condition = true;

                    }
                }
                if(strtotime($cacl_time_holiday[$count_holiday]['end-holiday']) + 86399 <= $_calc_production_date) {
                   $count_holiday++; 
                   $condition = true;
                }
            }
        }
    }
    return date('H:i Y/m/d' , $_calc_production_date );
}

function v3_get_production_time_order($order) {
    //Find max production time
    $max_production_time = 0;
    $max_shipping_time = 0;
    $order_items = $order->get_items('line_item');
    $have_pt = false;
    $user_id = $order->get_user_id();
    $user_meta =get_userdata($user_id);
    $role_use = '';
    if(isset($user_meta)) {
        $role_use = $user_meta->roles[0];
    }
    $have_role_use = false;
    $have_check_default = false;
    foreach ($order_items as $item_id => $item) {
        if ($item->get_meta('_nbo_options') && $item->get_meta('_nbo_field')) {
            $qty = $item->get_quantity();
            $options = $item->get_meta('_nbo_options');
            if( nbd_is_base64_string( $options['fields'] ) ){
                $options['fields'] = base64_decode( $options['fields'] );
            }
            $origin_fields = unserialize($options['fields']);
            $origin_fields = $origin_fields['fields'];
            $item_field = $item->get_meta('_nbo_field');
            foreach ($item_field as $key => $value) {
                foreach ($origin_fields as $field) {
                    if ($field['id'] === $key && isset($field['nbd_type']) && $field['nbd_type'] === 'production_time') {
                        $have_pt = true;
                        if(isset($field['general']['role_options'])) {
                            foreach ($field['general']['role_options'] as $role_options) {
                                if($role_options['role_name'] ==  $role_use) {
                                    $time_quantity_breaks_1 = $role_options['options'][$value['value']]['time_quantity_breaks'];
                                    $have_role_use = true;
                                }
                                if(isset($role_options['check_default']) && ( $role_options['check_default'] == 'on' || $role_options['check_default'] == '1' )) {
                                    $have_check_default = true;
                                    $time_quantity_breaks_2 = $role_options['options'][$value['value']]['time_quantity_breaks'];
                                }  
                            }
                        }
                        if($have_role_use) {
                            $time_quantity_breaks = $time_quantity_breaks_1;
                        }
                        if(!$have_role_use && $have_check_default ) {
                            $time_quantity_breaks = $time_quantity_breaks_2;
                        }
                        if(empty($time_quantity_breaks)) {
                            $have_pt = false;
                            break;
                        }
                        //Sort time_quantity_breaks by quantity
                        usort($time_quantity_breaks, "sort_time_quantity_breaks");
                        for ($i = 0; $i < count($time_quantity_breaks); $i++) {
                            if ($i === count($time_quantity_breaks) - 1) {
                                if ($qty >= $time_quantity_breaks[$i]['qty'] && (int)$time_quantity_breaks[$i]['time'] > $max_production_time) {
                                    $max_production_time = (float)$time_quantity_breaks[$i]['time'];
                                }
                                break;
                            }
                            if ($qty >= $time_quantity_breaks[$i]['qty'] && $qty < $time_quantity_breaks[$i + 1]['qty'] && (int)$time_quantity_breaks[$i]['time'] > $max_production_time) {
                                $max_production_time = (float)$time_quantity_breaks[$i]['time'];
                            }
                        }
                    }
                }
            }
        } 
        if(!$have_pt || $max_production_time == 0) {
            $qty = $item->get_quantity();
            $_productiton_time_default = array();
            $productiton_time_default = unserialize(nbdesigner_get_option('nbdesigner_product_time_default'));
            for( $f =0; $f < count($productiton_time_default[0]); $f++ ) {
                $_productiton_time_default[$f]['qty'] = $productiton_time_default[0][$f] ;
                $_productiton_time_default[$f]['time'] = $productiton_time_default[1][$f] ;
            }
            for ($i = 0; $i < count($_productiton_time_default); $i++) {
                if ($i === count($_productiton_time_default) - 1) {
                    if ($qty >= $_productiton_time_default[$i]['qty'] && (int)$_productiton_time_default[$i]['time'] > $max_production_time) {
                        $max_production_time = (float)$_productiton_time_default[$i]['time'];
                    }
                    break;
                }
                if ($qty >= $_productiton_time_default[$i]['qty'] && $qty < $_productiton_time_default[$i + 1]['qty'] && (int)$_productiton_time_default[$i]['time'] > $max_production_time) {
                    $max_production_time = (float)$_productiton_time_default[$i]['time'];
                }
            }
        }
    }
    return $max_production_time;
}

function v3_recalc_time_completed($date_now , $max_production_time , $order){
    $calc_production_date = v3_recalc_production_date($date_now, $max_production_time * 60);
    $time_shipping = calc_completed_shipping_date($order)*3600;
    $time_delivered = $time_shipping*3600  + strtotime($calc_production_date);
    $calc_shipping_date = date( "H:i Y/m/d" , $time_delivered );
    $production_datetime_completed = date("l, d F Y, h:i A", strtotime($calc_production_date));
    $production_date_completed = date("l, d F Y", strtotime($calc_production_date));
    $shipping_date_completed = date("l, d F Y", strtotime($calc_shipping_date));
    $shipping_datetime_completed = date("l, d F Y, h:i A", strtotime($calc_shipping_date));
    if ($max_shipping_time == 0) {
        return [
            'total_time' => $production_datetime_completed,
            'production_datetime_completed' => $production_datetime_completed,
            'production_date_completed' => $production_date_completed,
            'shipping_date_completed' => $shipping_date_completed,
            'shipping_datetime_completed' => $shipping_datetime_completed,
        ];
    } else {
        return [
            'total_time' => $production_date_completed . ' - ' . $shipping_date_completed,
            'production_datetime_completed' => $production_datetime_completed,
            'production_date_completed' => $production_date_completed,
            'shipping_date_completed' => $shipping_date_completed,
            'shipping_datetime_completed' => $shipping_datetime_completed,
        ];
    }
   
}

add_action('after_nbd_save_customer_design' , 'v3_nbd_save_customer_design');
function v3_nbd_save_customer_design($result) {
    if(isset( $_POST['design_type'] ) && $_POST['design_type'] == 'edit_order') {
        global $wpdb;
        $nbd_item_key = $_POST['nbd_item_key'];
        $query = "SELECT order_item_id FROM wp_woocommerce_order_itemmeta AS mt1 WHERE ( mt1.meta_key = '_nbd' AND mt1.meta_value = '${nbd_item_key}')";
        $result =  $wpdb->get_results($query);
        $item_id = $result[0]->order_item_id;
        if( $nbd_item_key ){
            //$list_images = Nbdesigner_IO::get_list_images(NBDESIGNER_CUSTOMER_DIR .'/'. $nbd_item_key, 1);
            nbd_export_pdfs( $nbd_item_key, false, false, 'no' );
            $pdf_path   = NBDESIGNER_CUSTOMER_DIR .'/'. $nbd_item_key . '/customer-pdfs';
            $list_pdf   = Nbdesigner_IO::get_list_files_by_type($pdf_path, 1, 'pdf');
            if(count($list_pdf) > 0){
                foreach($list_pdf as $key => $file){
                    $zip_files[] = $file;
                }
            }
        }
        if( !count($zip_files) ){
    //                exit();
        }else{
            $pathZip = NBDESIGNER_DATA_DIR.'/download/customer-design-'.$item_id.'.zip';
            $nameZip = 'customer-design-'.$item_id.'.zip';
            $link_aws = nbd_zip_files_and_download( $zip_files, $pathZip, $nameZip, $option_name = array(), $download = false, $upload_aws = true );
            wc_update_order_item_meta($item_id , '_nbd_item_edit' , $link_aws);
        }
    }
}
/*
function v3_generate_order_detail_pdf($order_id)
{
    global $wpdb;
    $html = '';
    $order = wc_get_order($order_id);
    ob_start();
    ?>
    <style>
        ul{
            padding-left: 0;
            list-style-type: none;
        }
        table thead th, table thead td, table tbody th, table tbody td, table tfoot th, table tfoot td {
            border: none;
        }
        .header-invoice {
            margin: 20px 0;
            font-family: roboto;
        }
        .order-number {
            font-size: 22px;
            line-height: 25px;
            font-family: robotom;
        } 
        .content-customer-detail {
            width: 100%;
        }
        .re-order {
            color: #EC1E24;
            font-size: 18px;
            font-family: roboto;
        }
        .order-status-paid {
            padding: 8px 30px;
            border-radius: 10px;
            background: transparent linear-gradient(0deg, #1BCB3F 0%, #55D443 51%, #91DF48 100%) 0% 0% no-repeat padding-box;
            color: #fff;
            font-size: 23px;
            width: 70px;
            display: inline-block;
            float: right;
            font-family: robotom;
            text-align: center;
        }
        .title {
            width: 100%;
            margin: 15px 0;
            clear: both;
        }
        .title .text {
            font-size: 18px;
            line-height: 21px;
            color: #231F20;
            width: 33%;
            text-align: center;
            display: inline-block!important;
            float: left;
            font-family: robotom;
        }
        .title .line{
            height: 2px;
            background: #1BCB3F;
            width: 100%;
            margin-top: 10px;
        }
        .title .left{
            float: left;
            display: inline-block!important;
            width: 33%;
        }
        .title .right{
            float: left;
            display: inline-block!important;
            width: 33%;
        }
        .li.name span {
            font-size: 11px;
            line-height: 14px;
            color: #221F1F;
            font-family: roboto;
        }
        span.key {
            font-size: 14px;
            line-height: 17px;
            font-family: roboto;
        }
        span.value {
            font-size: 14px;
            line-height: 17px;
            font-family: robotol;
        }
        span.address-title {
            font-size: 14px;
            line-height: 17px;
            font-family: roboto;
            color:  #221F1F;
        }
        div.ul div.li {
            display: block;
            padding-bottom: 5px!!important;
        }
        .product-name {
            font-size: 14px;
            line-height: 17px;
            color: #000000;
            margin-bottom: 10px;
            font-family: roboto;
        }
        .wrap-order-detail {
            padding: 0 auto;
            border-radius: 10px;
            border: 1px solid #EEECEC;
            background: transparent linear-gradient(177deg, #FFFFFF 0%, #F6F8F7 100%) 0% 0% no-repeat padding-box;
        }
        span.item-key {
            font-size: 13px;
            line-height: 17px;
            font-family: robotom;
        }
        span.item-value {
            font-size: 13px;
            line-height: 17px;
            font-family: robotol;
        }
        table.order-detail a.thumbnail>img {
            width: 100px;
            height:100px;
        }
        .list-thumbnail a.thumbnail {
             width: 100px;
            height:100px;
            display: block; 
            overflow: hidden;
            vertical-align: middle;
        }
        .list-thumbnail a.thumbnail>img {
            width: 100px;
            height: 100px;
        }
        img {
            width: 120px;
            height: auto;
        }
        .sub-order-detail {
            padding: 15px 0;
        }
        .sub-order-detail span.value .amount{
            color: #333!important;
            font-size: 11px;
            line-height: 14px;
            font-family: robotom!important;
        }
        .sub-order-detail span.value {
            font-size: 14px;
            line-height: 17px;
            font-family: robotom;
        }
        .sub-order-detail span.key {
            font-size: 14px;
            line-height: 17px;
            font-family: roboto;
        }
        .list-thumbnail {
            margin: 10px;
        }
        .subtotal {
            font-size: 14px;
            color: #333!important;
            font-family: roboto!important;
        }
        .amount {
            display: inline-block!!important;
            font-size: 14px;
            color: #333!important;
            font-family: roboto;
        }
        .gst {
            font-size: 14px;
            color: #333!important;
            font-family: roboto!important;
        }
        .subtotal-price {
            font-size: 14px;
            color: #333!important;
            font-family: roboto!important;
        }
        .total {
            font-size: 14px;
            color: #333!important;
            font-family: robotom!important;
        }
        .total-price {
            font-family: robotom!important;
            font-size: 14px;
            color: #333!important;
        }
        .wrap-line {
            width: 680px;
            height: 2px;
            background: #a0a0a0;
        }
        .wrap-total {
            margin-top: 5px;
            width: 310px;
            padding: 10px 0;
            display: inline-block;
            float: right;
        }
        div.hidden {
            display: none;
        }
        #total-price td {
            padding-top: 10px;
        }
    </style>
    <?php
    $css = ob_get_clean();
    if ($order) {
        $order_data = $order->get_data();
        $user = $order->get_user();
        $user_id = $order->get_user_id();
        $items = $order->get_items();
        $order_again = '';
        if(is_array($items)) {
            $item_key_0 = array_keys($items)[0];
            if( wc_get_order_item_meta( $item_key_0 , '_order_again') ) $order_again = '(Re-Order #'.wc_get_order_item_meta( $item_key_0 , '_order_again').')';
        }
        $paid = '';
        if( get_post_meta( $order_id , '_payment_status' , true ) == 'paid' ) {
            $paid =  '<div class="order-status-paid">PAID</div>';
        }
        $id_specialist = get_user_meta( $user_id , 'specialist' ,true);
        $specialist = get_userdata($id_specialist)->display_name;
        // $invoice_header = '<div class="header-invoice"><table style="width: 100%"><tbody><tr><td style="width: 50%"><div class="order-number"><span>Order: '.$order_id.' </span><span class="specialist">('.$specialist.')</span></div><div class="re-order">'.$order_again.'</div></td><td style="width: 50%">'.$paid.'</td></tr></tbody></table>';
        $invoice_header = '<div><div style="width: 50%; display:inline-block;float:left"><div class="order-number"><span>Order: '.$order_id.' </span><span class="specialist">('.$specialist.')</span></div><div class="re-order">'.$order_again.'</div></div><div style="width: 50%; display:inline-block; float:right">'.$paid.'</div></div>';
        $invoice_text_01 = '<div class="title"><div class="left"><div class="line line-left"></div></div><div class="text">CUSTOMER DETAILS</div><div class="right"><div class="line line-right"></div></div></div>';
        $invoice_text_02 = '<table class="content-customer-detail"><tbody><tr><td style="width: 50%; padding-bottom: 20px;"><div class="ul"><div class="li name"><span>' . $order_data['billing']['first_name'] . ' ' . $order_data['billing']['last_name'] . '</span></div><div style="display: block;font-size: 4px">&nbsp;</div><div class="li"><span class="key">Email: </span><span class="value">' . $order->get_billing_email() . '</span></div><div style="display: block;font-size: 4px">&nbsp;</div><div class="li"><span class="key">Tel: </span><span class="value">' . $order->get_billing_phone() . '</span></div></div></td><td style="width: 50%;padding-bottom: 20px; padding-left: 65px"><div class="ul"><div class="li"><span class="key">Payment: </span><span class="value">' . $order->get_payment_method_title() . '</span></div><div style="display: block;font-size: 4px">&nbsp;</div><div class="li"><span class="key">Order date: </span><span class="value">'.date_format($order->get_date_created() , "d/m/Y").'</span></div><div style="display: block;font-size: 4px">&nbsp;</div><div class="li"><span class="key">Shipping Method: </span><span class="value">' . $order->get_shipping_method() . '</span></div></div></td></tr><tr><td style="width: 50%"><span class="address-title">Billing Address: </span><div class="ul address-detail"><div style="display: block;font-size: 4px">&nbsp;</div><div class="li"><span class="value">' . $order->get_billing_address_1() . '</span></div><div style="display: block;font-size: 4px">&nbsp;</div><div class="li"><span class="value">' . $order->get_billing_address_2() . '</span></div><div style="display: block;font-size: 4px">&nbsp;</div><div class="li"><span class="value">' . $order->get_billing_country() . ' ' . $order->get_billing_postcode() . '</span></div></div></td><td style="width: 50%;padding-left: 60px"><span class="address-title">Shipping Address: </span><div class="ul address-detail"><div style="display: block;font-size: 4px">&nbsp;</div><div class="li"><span class="value">' . $order->get_shipping_address_1() . '</span></div><div style="display: block;font-size: 4px">&nbsp;</div><div class="li"><span class="value">' . $order->get_shipping_address_2() . '</span><div style="display: block;font-size: 4px">&nbsp;</div></div><div class="li"><span class="value">' . $order->get_shipping_country() . ' ' . $order->get_shipping_postcode() . '</span></div></div></td></tr></tbody></table>';
        $invoice_product_page_01 = '<div class="title"><div class="left"><div class="line line-left"></div></div><div class="text">ORDER DETAILS</div><div class="right"><div class="line line-right"></div></div></div>';
        // $invoice_product_page_01 = '<div class="title"><div class="line line-left"></div><div class="text">ORDER DETAILS</div><div class="line line-right"></div></div>';
        $subtotal = 0;
        $loop = 1;
        $invoice_product_page_02 = '<div class="items-detail">';
        $info_1 = '';
        $total_elements = 0;
        foreach ($items as $order_item_id => $item) {
            $subtotal += $item['line_total'];
            if (isset($item['variation_id']) && $item['variation_id'] > 0) :
                $_product = wc_get_product($item['variation_id']);
            else :
                $_product = wc_get_product($item['product_id']);
            endif;
            $file = '';
            $thumbnail = '';
            $add_thumb_button = false;
            $element = 2;
            if (isset($_product) && $_product != false) {
                if (wc_get_order_item_meta($order_item_id, '_nbu')) {
                    $files = botak_get_list_file_s3('reupload-design/'. wc_get_order_item_meta($order_item_id, '_nbu'));
                    if(count($files) > 0 ) {
                        $file = $files[0];
                        $ext        = pathinfo( $file, PATHINFO_EXTENSION );
                        $src        = Nbdesigner_IO::get_thumb_file( pathinfo( $file, PATHINFO_EXTENSION ), '');
                        $file_url   = $file;
                        $create_preview     = nbdesigner_get_option('nbdesigner_create_preview_image_file_upload');
                        if(  $create_preview == 'yes' && ( $ext == 'png' || $ext == 'jpg' || $ext == 'pdf' ) ){
                            $dir        = pathinfo( $file, PATHINFO_DIRNAME );
                            $filename   = pathinfo( $file, PATHINFO_BASENAME );
                            $file_headers = @get_headers($dir.'_preview/'.$filename);
                            if(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found' || $file_headers[0] == 'HTTP/1.1 403 Forbidden') {
                                $exists = false;
                            }
                            else {
                                $exists = true;
                            }
                            if( $exists && ( $ext == 'png' || $ext == 'jpg' ) ){
                                $src = $dir.'_preview/'.$filename;
                            }else if( $ext == 'pdf' && botak_check_link_exists_s3($dir.'_preview/'.$filename.'.jpg') ){
                                $src = $dir.'_preview/'.$filename.'.jpg';
                            }else{
                                $src = Nbdesigner_IO::get_thumb_file( $ext, '' );
                            }
                        }else {
                            $src = Nbdesigner_IO::get_thumb_file( $ext, '' );
                        }
                        if( count($files) > 1 ) {
                            $add_thumb_button = true;
                        }
                    }
                }
                $total_elements ++;
                $info_1 .= '<div class="product-name">'.$loop.'. '.$_product->get_title().'</div><div class="product-meta"><div class="order-detail" style="width: 100%;">';
                $style_right = 'style="width: 670px; margin-left: 0; height: 130px;padding: 10px"';
                if( $file ) {
                    $info_1 .= '<div style="width: 150px;height: 150px;display: inline-block; float: left; margin-right: 10px"><a href="'.$file.'" class="thumbnail" target="_blank"><img style="width: 150px;height: 150px;" src="'.$src.'"></a></div>';
                    $style_right = 'style="width: 500px; height: 130px; display: inline-block; float: right; padding: 10px; margin-left: 10px"';
                }
                $sub_infor = '';
                $formatted_meta_data = $item->get_formatted_meta_data('_', true);
                $max_row = 5;
                $number_meta = 0;
                foreach ($formatted_meta_data as $k => $v) {
                    if($v->key != "Quantity Discount" && $v->key != "Production time" && $v->key != "SKU" && $v->key != "item_status" ) {
                        $number_meta++;
                    } 

                }
                if(is_array($formatted_meta_data)) {
                    if( $max_row < (int) ($number_meta/2) + ($number_meta%2) ) {
                        $max_row = (int) ($number_meta/2) + ($number_meta%2);
                    }
                }
                $info_1 .='<div '.$style_right.' class="wrap-order-detail">';
                $count_item = 1;
                $sub_infor_left = '';
                $sub_infor_right = '';
                foreach ($formatted_meta_data as $k => $v) {
                    if($v->key == "Quantity Discount" || $v->key == "Production Time" || $v->key == "SKU" || $v->key == "item_status") {
                        continue;
                    }
                    if( $count_item <= $max_row) {
                        $sub_infor_left .= '<div class="item-meta"><span class="item-key">' . $v->key . ':</span> <span class="item-value">' . preg_replace( '/&nbsp;&nbsp;(.*)/' , '' , $v->value) . '</span></div><div style="display: block;font-size: 6px">&nbsp;</div>';
                    } else {
                        $sub_infor_right .= '<div class="item-meta"><span class="item-key">' . $v->key . ':</span> <span class="item-value">' . preg_replace( '/&nbsp;&nbsp;(.*)/' , '' , $v->value) . '</span></div><div style="display: block;font-size: 6px">&nbsp;</div>';
                    }
                    if($v->key != "Quantity Discount" && $v->key != "Production time" && $v->key != "SKU" && $v->key != "item_status" ) {
                        $count_item ++;
                    } 

                }
                if( $count_item == 1 ) {
                    $info_1 = str_replace( 'class="wrap-order-detail' , 'class="wrap-order-detail hidden' , $info_1);
                }
                if( $sub_infor_left ) $sub_infor_left = '<td style="vertical-align: top">'.$sub_infor_left.'</td>';
                if( $sub_infor_right ) $sub_infor_right = '<td style="vertical-align: top">'.$sub_infor_right.'</td>';
                $info_1 .= '<div class="item-order-detail"><table><tbody><tr>' . $sub_infor_left . $sub_infor_right . '</tr></tbody></table></div></div>';
                // if($total_elements == 4 || $total_elements == 10) $info_1 .= '<div class="minh-phan-trang"></div>';
                $total_elements ++;
                $sub_infor .= '<div class="sub-order-detail">';
                if(!$formatted_meta_data) {
                    $sub_infor .= '<div style="margin-bottom: 4px"><span class="key">SKU : </span><span class="value">' . $_product->get_sku(). '</span></div>';
                } else {
                    $sub_infor .= '<div style="margin-bottom: 4px"><span class="key">SKU : </span><span class="value">' . wc_get_order_item_meta($order_item_id, 'SKU') . '</span></div>';
                }
                $sub_infor .= '<div style="margin-bottom: 4px"><span class="key">Quantity : </span><span class="value">' . $item['quantity'] . '</span></div>';
                $sub_infor .= '<div style="margin-bottom: 4px"><span class="key">Price : </span><span class="value">SGD $' . number_format($item['line_total'] , 2) . '</span></div>';
                $sub_infor .= '<div style="margin-bottom: 4px"><span class="key">Production Time : </span><span class="value">' . wc_get_order_item_meta($order_item_id, 'Production Time') . '</span></div>';
                $sub_infor .= '<div style="margin-bottom: 4px"><span class="key">Estimated Completion Time : </span><span class="value">' . wc_get_order_item_meta($order_item_id, '_item_time_completed') . '</span></div>';
                $sub_infor .= '</div>';
                $info_1 .= '</div></div>'.$sub_infor;
                // if($total_elements == 4 || $total_elements == 10) $info_1 .= '<div class="minh-phan-trang"></div>';
                if($add_thumb_button) {
                    $total_elements ++;
                    $info_1 .= '<div class="list-thumbnail"><table style="width:100%"><tbody><tr>';
                    $thumbnail_item = '';
                    $count_file = count($files);
                    $thumbnail_item_fake = '';
                    if($count_file < 6) {
                        for($i = 0; $i < (6-$count_file); $i++) {
                           $thumbnail_item_fake .= '<td style="width:16%;padding-right: 10px;"><a href="" class="thumbnail"  target="_blank"><img src="" style="display:none"></a></td>'; 
                        }
                        
                    }
                    foreach ($files as $file) {
                        $ext        = pathinfo( $file, PATHINFO_EXTENSION );
                        $src        = Nbdesigner_IO::get_thumb_file( pathinfo( $file, PATHINFO_EXTENSION ), '');
                        $file_url   = $file;
                        $create_preview     = nbdesigner_get_option('nbdesigner_create_preview_image_file_upload');
                        if(  $create_preview == 'yes' && ( $ext == 'png' || $ext == 'jpg' || $ext == 'pdf' ) ){
                            $dir        = pathinfo( $file, PATHINFO_DIRNAME );
                            $filename   = pathinfo( $file, PATHINFO_BASENAME );
                            $file_headers = @get_headers($dir.'_preview/'.$filename);
                            if(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found' || $file_headers[0] == 'HTTP/1.1 403 Forbidden') {
                                $exists = false;
                            }
                            else {
                                $exists = true;
                            }
                            if( $exists && ( $ext == 'png' || $ext == 'jpg' ) ){
                                $src = $dir.'_preview/'.$filename;
                            }else if( $ext == 'pdf' && botak_check_link_exists_s3($dir.'_preview/'.$filename.'.jpg') ){
                                $src = $dir.'_preview/'.$filename.'.jpg';
                            }else{
                                $src = Nbdesigner_IO::get_thumb_file( $ext, '' );
                            }
                        }else {
                            $src = Nbdesigner_IO::get_thumb_file( $ext, '' );
                        }
                        $thumbnail_item .= '<td style="padding-right: 10px;"><a href="'.$file.'" class="thumbnail"  target="_blank"><img style="width:100px;height:100px" src="'.$src.'"></a></td>';
                    }
                    $info_1 .= $thumbnail_item.$thumbnail_item_fake.'</tr></tbody></table></div>';
                    // if($total_elements == 4 || $total_elements == 10) $info_1 .= '<div class="minh-phan-trang"></div>';
                }
            }
            $loop ++;
        }
        $gst = $subtotal * 7 / 100;
        if ($order_data['shipping_total'] > 0) {
            $gst += ($order_data['shipping_total'] * 7/100 );
        }
        // fix Làm tròn giá
        $gst_1 = ($subtotal + $gst) - number_format( $subtotal, 2 );
        $invoice_product_page_02 .= $info_1.'</div>'; // close div item
        $total_price = '<div class="title"><div class="left"><div class="line line-left"></div></div><div class="text">SUMMARY</div><div class="right"><div class="line line-right"></div></div></div><div class="wrap-line"></div><table id="total-price" style="width:100%">
            <tr><td style="width:50%; padding-top:5px"></td>
            <td style="width:50%; padding-top:5px; padding-left:30px" align="left"><table><tbody><tr>
            <td style="width:20%;padding-top:5px" class="subtotal" align="left">Subtotal</td>
            <td style="width:50%;text-align: right;padding-top:5px" class="subtotal-price" >' . wc_price($subtotal) . '</td>
            </tr>
            <tr><td style="width:20%;padding-top:5px" class="subtotal" align="left">Shipping</td>
            <td style="width:80%;padding-top:5px;text-align: right; ư" class="subtotal-price">' . $order->get_payment_method_title() . ' (' . wc_price($order_data['shipping_total']) . ')</td>
            </tr>
            <tr><td style="width:20%" class="gst" align="left">GST (7%)</td>
            <td style="width:50%;text-align: right;" class="gst-price">' . wc_price($gst_1) . '</td>
            </tr>
            </tbody></table>
            </td></tr>
            </table>
            <div class="wrap-total"><div style="width: 310px; height: 1px; background: #a0a0a0"></div><div style="height:5px"></div><div style="width: 50%; display:inline-block;float: left" class="total" align="left">Total</div>
            <div style="width: 50%; display:inline-block;float:right;text-align:right" class="total-price">SGD $' . number_format($subtotal + $order_data['shipping_total'] + $gst , 2 ) . '</div><div style="height:5px"></div><div style="width: 310px; height: 1px; background: #a0a0a0"></div>
            </div>';
        $html = $css.$invoice_header.$invoice_text_01 . $invoice_text_02 . $invoice_product_page_01 . $invoice_product_page_02 . $total_price;
    }
    return $html;
}
*/
function v3_generate_order_detail_pdf($order_id)
{
    global $wpdb;
    $html = '';
    $order = wc_get_order($order_id);
    ob_start();
    ?>
    <style>
        ul{
            padding-left: 0;
            list-style-type: none;
        }
        table thead th, table thead td, table tbody th, table tbody td, table tfoot th, table tfoot td {
            border: none;
        }
        .header-invoice {
            margin: 20px 0;
            font-family: roboto;
        }
        .order-number {
            font-size: 22px;
            line-height: 25px;
            font-family: robotom;
        } 
        .content-customer-detail {
            width: 100%;
        }
        .re-order {
            color: #EC1E24;
            font-size: 18px;
            font-family: roboto;
        }
        .order-status-paid {
            padding: 8px 30px;
            border-radius: 10px;
            background: transparent linear-gradient(0deg, #1BCB3F 0%, #55D443 51%, #91DF48 100%) 0% 0% no-repeat padding-box;
            color: #fff;
            font-size: 23px;
            width: 70px;
            display: inline-block;
            float: right;
            font-family: robotom;
            text-align: center;
        }
        .title {
            width: 100%;
            margin: 15px 0;
            clear: both;
        }
        .title .text {
            font-size: 18px;
            line-height: 21px;
            color: #231F20;
            width: 33%;
            text-align: center;
            display: inline-block!important;
            float: left;
            font-family: robotom;
        }
        .title .line{
            height: 2px;
            background: #1BCB3F;
            width: 100%;
            margin-top: 10px;
        }
        .title .left{
            float: left;
            display: inline-block!important;
            width: 33%;
        }
        .title .right{
            float: left;
            display: inline-block!important;
            width: 33%;
        }
        .li.name span {
            font-size: 11px;
            line-height: 14px;
            color: #221F1F;
            font-family: roboto;
        }
        span.key {
            font-size: 14px;
            line-height: 17px;
            font-family: roboto;
        }
        span.value {
            font-size: 14px;
            line-height: 17px;
            font-family: robotol;
        }
        span.address-title {
            font-size: 14px;
            line-height: 17px;
            font-family: roboto;
            color:  #221F1F;
        }
        div.ul div.li {
            display: block;
            padding-bottom: 5px!!important;
        }
        .product-name {
            font-size: 14px;
            line-height: 17px;
            color: #000000;
            margin-bottom: 10px;
            font-family: roboto;
        }
        .wrap-order-detail {
            padding: 0 auto;
            border-radius: 10px;
            border: 1px solid #EEECEC;
            background: transparent linear-gradient(177deg, #FFFFFF 0%, #F6F8F7 100%) 0% 0% no-repeat padding-box;
        }
        span.item-key {
            font-size: 13px;
            line-height: 17px;
            font-family: robotom;
        }
        span.item-value {
            font-size: 13px;
            line-height: 17px;
            font-family: robotol;
        }
        table.order-detail a.thumbnail>img {
            width: 100px;
            height:100px;
        }
        .list-thumbnail a.thumbnail {
             width: 100px;
            height:100px;
            display: block; 
            overflow: hidden;
            vertical-align: middle;
        }
        .list-thumbnail a.thumbnail>img {
            width: 100px;
            height: 100px;
        }
        img {
            width: 120px;
            height: auto;
        }
        .sub-order-detail {
            padding: 15px 0;
        }
        .sub-order-detail span.value .amount{
            color: #333!important;
            font-size: 11px;
            line-height: 14px;
            font-family: robotom!important;
        }
        .sub-order-detail span.value {
            font-size: 14px;
            line-height: 17px;
            font-family: robotom;
        }
        .sub-order-detail span.key {
            font-size: 14px;
            line-height: 17px;
            font-family: roboto;
        }
        .list-thumbnail {
            margin: 10px;
        }
        .subtotal {
            font-size: 14px;
            color: #333!important;
            font-family: roboto!important;
        }
        .amount {
            display: inline-block!!important;
            font-size: 14px;
            color: #333!important;
            font-family: roboto;
        }
        .gst {
            font-size: 14px;
            color: #333!important;
            font-family: roboto!important;
        }
        .subtotal-price {
            font-size: 14px;
            color: #333!important;
            font-family: roboto!important;
        }
        .total {
            font-size: 14px;
            color: #333!important;
            font-family: robotom!important;
        }
        .total-price {
            font-family: robotom!important;
            font-size: 14px;
            color: #333!important;
        }
        .wrap-line {
            width: 680px;
            height: 2px;
            background: #a0a0a0;
        }
        .wrap-total {
            margin-top: 5px;
            width: 310px;
            padding: 10px 0;
            display: inline-block;
            float: right;
        }
        div.hidden {
            display: none;
        }
        #total-price td {
            padding-top: 10px;
        }
    </style>
    <?php
    $css = ob_get_clean();
    if ($order) {
        $order_data = $order->get_data();
        $user = $order->get_user();
        $user_id = $order->get_user_id();
        $items = $order->get_items();
        $order_again = '';
        if(is_array($items)) {
            $item_key_0 = array_keys($items)[0];
            if( wc_get_order_item_meta( $item_key_0 , '_order_again') ) $order_again = '(Re-Order #'.wc_get_order_item_meta( $item_key_0 , '_order_again').')';
        }
        $paid = '';
        if( get_post_meta( $order_id , '_payment_status' , true ) == 'paid' ) {
            $paid =  '<div class="order-status-paid">PAID</div>';
        }
        $id_specialist = get_user_meta( $user_id , 'specialist' ,true);
        $specialist = get_userdata($id_specialist)->display_name;
        // $invoice_header = '<div class="header-invoice"><table style="width: 100%"><tbody><tr><td style="width: 50%"><div class="order-number"><span>Order: '.$order_id.' </span><span class="specialist">('.$specialist.')</span></div><div class="re-order">'.$order_again.'</div></td><td style="width: 50%">'.$paid.'</td></tr></tbody></table>';
        $invoice_header = '<div><div style="width: 50%; display:inline-block;float:left"><div class="order-number"><span>Order: '.$order_id.' </span><span class="specialist">('.$specialist.')</span></div><div class="re-order">'.$order_again.'</div></div><div style="width: 50%; display:inline-block; float:right">'.$paid.'</div></div>';
        $invoice_text_01 = '<div class="title"><div class="left"><div class="line line-left"></div></div><div class="text">CUSTOMER DETAILS</div><div class="right"><div class="line line-right"></div></div></div>';
        $invoice_text_02 = '<table class="content-customer-detail"><tbody><tr><td style="width: 50%; padding-bottom: 20px;"><div class="ul"><div class="li name"><span>' . $order_data['billing']['first_name'] . ' ' . $order_data['billing']['last_name'] . '</span></div><div style="display: block;font-size: 4px">&nbsp;</div><div class="li"><span class="key">Email: </span><span class="value">' . $order->get_billing_email() . '</span></div><div style="display: block;font-size: 4px">&nbsp;</div><div class="li"><span class="key">Tel: </span><span class="value">' . $order->get_billing_phone() . '</span></div></div></td><td style="width: 50%;padding-bottom: 20px; padding-left: 65px"><div class="ul"><div class="li"><span class="key">Payment: </span><span class="value">' . $order->get_payment_method_title() . '</span></div><div style="display: block;font-size: 4px">&nbsp;</div><div class="li"><span class="key">Order date: </span><span class="value">'.date_format($order->get_date_created() , "d/m/Y").'</span></div><div style="display: block;font-size: 4px">&nbsp;</div><div class="li"><span class="key">Shipping Method: </span><span class="value">' . $order->get_shipping_method() . '</span></div></div></td></tr><tr><td style="width: 50%"><span class="address-title">Billing Address: </span><div class="ul address-detail"><div style="display: block;font-size: 4px">&nbsp;</div><div class="li"><span class="value">' . $order->get_billing_address_1() . '</span></div><div style="display: block;font-size: 4px">&nbsp;</div><div class="li"><span class="value">' . $order->get_billing_address_2() . '</span></div><div style="display: block;font-size: 4px">&nbsp;</div><div class="li"><span class="value">' . $order->get_billing_country() . ' ' . $order->get_billing_postcode() . '</span></div></div></td><td style="width: 50%;padding-left: 60px"><span class="address-title">Shipping Address: </span><div class="ul address-detail"><div style="display: block;font-size: 4px">&nbsp;</div><div class="li"><span class="value">' . $order->get_shipping_address_1() . '</span></div><div style="display: block;font-size: 4px">&nbsp;</div><div class="li"><span class="value">' . $order->get_shipping_address_2() . '</span><div style="display: block;font-size: 4px">&nbsp;</div></div><div class="li"><span class="value">' . $order->get_shipping_country() . ' ' . $order->get_shipping_postcode() . '</span></div></div></td></tr></tbody></table>';
        $invoice_product_page_01 = '<div class="title"><div class="left"><div class="line line-left"></div></div><div class="text">ORDER DETAILS</div><div class="right"><div class="line line-right"></div></div></div>';
        // $invoice_product_page_01 = '<div class="title"><div class="line line-left"></div><div class="text">ORDER DETAILS</div><div class="line line-right"></div></div>';
        $subtotal = 0;
        $loop = 1;
        $invoice_product_page_02 = '<div class="items-detail">';
        $info_1s = '';
        $total_elements = 0;
        foreach ($items as $order_item_id => $item) {
            $info_1 = '';
            $subtotal += $item['line_total'];
            if (isset($item['variation_id']) && $item['variation_id'] > 0) :
                $_product = wc_get_product($item['variation_id']);
            else :
                $_product = wc_get_product($item['product_id']);
            endif;
            $file = '';
            $thumbnail = '';
            $add_thumb_bottom = false;
            $element = 2;
            $nbu_files = array();
            $nbd_files = array();
            if (isset($_product) && $_product != false) {
                if (wc_get_order_item_meta($order_item_id, '_nbd')) {
                    $path_preview   = NBDESIGNER_CUSTOMER_DIR . '/' . wc_get_order_item_meta($order_item_id, '_nbd') . '/preview';
                    $nbd_files      = Nbdesigner_IO::get_list_images( $path_preview );
                    if(count($nbd_files) > 0 ) {
                        $file = $nbd_files[0];
                        $src  = Nbdesigner_IO::wp_convert_path_to_url( $file );
                        $file = $src;
                        if( count($nbd_files) > 1 ) {
                            $add_thumb_bottom = true;
                        }
                    }
                }
                if (wc_get_order_item_meta($order_item_id, '_nbu')) {
                    $nbu_files = botak_get_list_file_s3('reupload-design/'. wc_get_order_item_meta($order_item_id, '_nbu'));
                    if(count($nbu_files) > 0 ) {
                        $file = $nbu_files[0];
                        $ext        = pathinfo( $file, PATHINFO_EXTENSION );
                        $src        = Nbdesigner_IO::get_thumb_file( pathinfo( $file, PATHINFO_EXTENSION ), '');
                        $file_url   = $file;
                        $create_preview     = nbdesigner_get_option('nbdesigner_create_preview_image_file_upload');
                        if(  $create_preview == 'yes' && ( $ext == 'png' || $ext == 'jpg' || $ext == 'pdf' ) ){
                            if($ext == 'jpg') {
                                $src = $file;
                            } else {
                                $dir        = pathinfo( $file, PATHINFO_DIRNAME );
                                $filename   = pathinfo( $file, PATHINFO_BASENAME );
                                $file_headers = @get_headers($dir.'_preview/'.$filename);
                                if(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found' || $file_headers[0] == 'HTTP/1.1 403 Forbidden') {
                                    $exists = false;
                                }
                                else {
                                    $exists = true;
                                }
                                if( $exists && ( $ext == 'png' ) ){
                                    $src = $dir.'_preview/'.$filename;
                                }else if( $ext == 'pdf' && botak_check_link_exists_s3($dir.'_preview/'.$filename.'.jpg') ){
                                    $src = $dir.'_preview/'.$filename.'.jpg';
                                }else{
                                    $src = Nbdesigner_IO::get_thumb_file( $ext, '' );
                                }
                            }   
                        }else {
                            $src = Nbdesigner_IO::get_thumb_file( $ext, '' );
                        }
                        if( count($nbu_files) > 1 ) {
                            $add_thumb_bottom = true;
                        }
                    }
                }
                $total_elements ++;
                $info_1 .= '<div class="product-name">'.$loop.'. '.$_product->get_title().'</div><div class="product-meta"><div class="order-detail" style="width: 100%;">';
                $style_right = 'style="width: 670px; margin-left: 0; height: 130px;padding: 10px"';
                if( $file ) {
                    $info_1 .= '<div style="width: 150px;height: 150px;display: inline-block; float: left; margin-right: 10px"><a href="'.$file.'" class="thumbnail" target="_blank"><img style="width: 150px;height: 150px;" src="'.$src.'"></a></div>';
                    $style_right = 'style="width: 500px; height: 130px; display: inline-block; float: right; padding: 10px; margin-left: 10px"';
                }
                $sub_infor = '';
                $formatted_meta_data = $item->get_formatted_meta_data('_', true);
                $max_row = 5;
                $number_meta = 0;
                foreach ($formatted_meta_data as $k => $v) {
                    if($v->key != "Quantity Discount" && $v->key != "Production time" && $v->key != "SKU" && $v->key != "item_status" ) {
                        $number_meta++;
                    } 

                }
                if(is_array($formatted_meta_data)) {
                    if( $max_row < (int) ($number_meta/2) + ($number_meta%2) ) {
                        $max_row = (int) ($number_meta/2) + ($number_meta%2);
                    }
                }
                $info_1 .='<div '.$style_right.' class="wrap-order-detail">';
                $count_item = 1;
                $sub_infor_left = '';
                $sub_infor_right = '';
                foreach ($formatted_meta_data as $k => $v) {
                    if($v->key == "Quantity Discount" || $v->key == "Production Time" || $v->key == "SKU" || $v->key == "item_status") {
                        continue;
                    }
                    if( $count_item <= $max_row) {
                        $sub_infor_left .= '<div class="item-meta"><span class="item-key">' . $v->key . ':</span> <span class="item-value">' . preg_replace( '/&nbsp;&nbsp;(.*)/' , '' , $v->value) . '</span></div><div style="display: block;font-size: 6px">&nbsp;</div>';
                    } else {
                        $sub_infor_right .= '<div class="item-meta"><span class="item-key">' . $v->key . ':</span> <span class="item-value">' . preg_replace( '/&nbsp;&nbsp;(.*)/' , '' , $v->value) . '</span></div><div style="display: block;font-size: 6px">&nbsp;</div>';
                    }
                    if($v->key != "Quantity Discount" && $v->key != "Production time" && $v->key != "SKU" && $v->key != "item_status" ) {
                        $count_item ++;
                    } 

                }
                if( $count_item == 1 ) {
                    $info_1 = str_replace( 'class="wrap-order-detail' , 'class="wrap-order-detail hidden '. $_product->get_title() , $info_1);
                }
                if( $sub_infor_left ) $sub_infor_left = '<td style="vertical-align: top">'.$sub_infor_left.'</td>';
                if( $sub_infor_right ) $sub_infor_right = '<td style="vertical-align: top">'.$sub_infor_right.'</td>';
                $info_1 .= '<div class="item-order-detail"><table><tbody><tr>' . $sub_infor_left . $sub_infor_right . '</tr></tbody></table></div></div>';
                // if($total_elements == 4 || $total_elements == 10) $info_1 .= '<div class="minh-phan-trang"></div>';
                $total_elements ++;
                $sub_infor .= '<div class="sub-order-detail">';
                if(!$formatted_meta_data) {
                    $sub_infor .= '<div style="margin-bottom: 4px"><span class="key">SKU : </span><span class="value">' . $_product->get_sku(). '</span></div>';
                } else {
                    $sub_infor .= '<div style="margin-bottom: 4px"><span class="key">SKU : </span><span class="value">' . wc_get_order_item_meta($order_item_id, 'SKU') . '</span></div>';
                }
                $sub_infor .= '<div style="margin-bottom: 4px"><span class="key">Quantity : </span><span class="value">' . $item['quantity'] . '</span></div>';
                $sub_infor .= '<div style="margin-bottom: 4px"><span class="key">Price : </span><span class="value">SGD $' . number_format($item['line_total'] , 2) . '</span></div>';
                $sub_infor .= '<div style="margin-bottom: 4px"><span class="key">Production Time : </span><span class="value">' . wc_get_order_item_meta($order_item_id, 'Production Time') . '</span></div>';
                $sub_infor .= '<div style="margin-bottom: 4px"><span class="key">Estimated Completion Time : </span><span class="value">' . wc_get_order_item_meta($order_item_id, '_item_time_completed') . '</span></div>';
                $sub_infor .= '</div>';
                $info_1 .= '</div></div>'.$sub_infor;
                // if($total_elements == 4 || $total_elements == 10) $info_1 .= '<div class="minh-phan-trang"></div>';
                if($add_thumb_bottom) {
                    $total_elements ++;
                    $info_1 .= '<div class="list-thumbnail"><table style="width:100%"><tbody><tr>';
                    $thumbnail_item = '';
                    $count_file = count($nbu_files) + count($nbd_files);
                    $thumbnail_item_fake = '';
                    if($count_file < 6) {
                        for($i = 0; $i < (6-$count_file); $i++) {
                           $thumbnail_item_fake .= '<td style="width:16%;padding-right: 10px;"><a href="" class="thumbnail"  target="_blank"><img src="" style="display:none"></a></td>'; 
                        }
                        
                    }
                    foreach ($nbu_files as $file) {
                        $ext        = pathinfo( $file, PATHINFO_EXTENSION );
                        $src        = Nbdesigner_IO::get_thumb_file( pathinfo( $file, PATHINFO_EXTENSION ), '');
                        $file_url   = $file;
                        $create_preview     = nbdesigner_get_option('nbdesigner_create_preview_image_file_upload');
                        if(  $create_preview == 'yes' && ( $ext == 'png' || $ext == 'jpg' || $ext == 'pdf' ) ){
                            $dir        = pathinfo( $file, PATHINFO_DIRNAME );
                            $filename   = pathinfo( $file, PATHINFO_BASENAME );
                            $file_headers = @get_headers($dir.'_preview/'.$filename);
                            if(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found' || $file_headers[0] == 'HTTP/1.1 403 Forbidden') {
                                $exists = false;
                            }
                            else {
                                $exists = true;
                            }
                            if( $exists && ( $ext == 'png' || $ext == 'jpg' ) ){
                                $src = $dir.'_preview/'.$filename;
                            }else if( $ext == 'pdf' && botak_check_link_exists_s3($dir.'_preview/'.$filename.'.jpg') ){
                                $src = $dir.'_preview/'.$filename.'.jpg';
                            }else{
                                $src = Nbdesigner_IO::get_thumb_file( $ext, '' );
                            }
                        }else {
                            $src = Nbdesigner_IO::get_thumb_file( $ext, '' );
                        }
                        $thumbnail_item .= '<td style="padding-right: 10px;"><a href="'.$file.'" class="thumbnail"  target="_blank"><img style="width:100px;height:100px" src="'.$src.'"></a></td>';
                    }
                    foreach ($nbd_files as $file) {
                        $src        = Nbdesigner_IO::wp_convert_path_to_url( $file );
                        $thumbnail_item .= '<td style="padding-right: 10px;"><a href="'.$src.'" class="thumbnail"  target="_blank"><img style="width:100px;height:100px" src="'.$src.'"></a></td>';
                    }
                    $info_1 .= $thumbnail_item.$thumbnail_item_fake.'</tr></tbody></table></div>';
                    // if($total_elements == 4 || $total_elements == 10) $info_1 .= '<div class="minh-phan-trang"></div>';
                }
                $info_1s .= $info_1;
            }
            $loop ++;
        }
        $gst = $subtotal * 7 / 100;
        if ($order_data['shipping_total'] > 0) {
            $gst += ($order_data['shipping_total'] * 7/100 );
        }
        // fix Làm tròn giá
        $gst_1 = ($subtotal + $gst) - number_format( $subtotal, 2 );
        $invoice_product_page_02 .= $info_1s.'</div>'; // close div item
        $total_price = '<div class="title"><div class="left"><div class="line line-left"></div></div><div class="text">SUMMARY</div><div class="right"><div class="line line-right"></div></div></div><div class="wrap-line"></div><table id="total-price" style="width:100%">
            <tr><td style="width:50%; padding-top:5px"></td>
            <td style="width:50%; padding-top:5px; padding-left:30px" align="left"><table><tbody><tr>
            <td style="width:20%;padding-top:5px" class="subtotal" align="left">Subtotal</td>
            <td style="width:50%;text-align: right;padding-top:5px" class="subtotal-price" >' . wc_price($subtotal) . '</td>
            </tr>
            <tr><td style="width:20%;padding-top:5px" class="subtotal" align="left">Shipping</td>
            <td style="width:80%;padding-top:5px;text-align: right; ư" class="subtotal-price">' . $order->get_payment_method_title() . ' (' . wc_price($order_data['shipping_total']) . ')</td>
            </tr>
            <tr><td style="width:20%" class="gst" align="left">GST (7%)</td>
            <td style="width:50%;text-align: right;" class="gst-price">' . wc_price($gst_1) . '</td>
            </tr>
            </tbody></table>
            </td></tr>
            </table>
            <div class="wrap-total"><div style="width: 310px; height: 1px; background: #a0a0a0"></div><div style="height:5px"></div><div style="width: 50%; display:inline-block;float: left" class="total" align="left">Total</div>
            <div style="width: 50%; display:inline-block;float:right;text-align:right" class="total-price">SGD $' . number_format($subtotal + $order_data['shipping_total'] + $gst , 2 ) . '</div><div style="height:5px"></div><div style="width: 310px; height: 1px; background: #a0a0a0"></div>
            </div>';
        $html = $css.$invoice_header.$invoice_text_01 . $invoice_text_02 . $invoice_product_page_01 . $invoice_product_page_02 . $total_price;
    }
    return $html;
}
function v3_array_merge($array1 , $array2) {
    $array = array();
    if(empty($array1)) {
        return $array2;
    }
    if(empty($array2)) {
        return $array1;
    }
    if($array1 && $array2) {
        foreach ($array1 as $key => $value) {
            if($value != $array2[$key]) {
                $array[$key] = '1';
            } else {
                $array[$key] = $value;
            }
        }
    }
    return $array;
}
function v3_get_role_status_by_user($user_id) {
    $get_option_roles = unserialize(get_option('status_role_methods'));
    $roles = get_userdata($user_id)->roles;
    $user_can = array();  
    if(is_array($roles)) {
        foreach ($roles as $key => $role) {
            $user_can = v3_array_merge($user_can , $get_option_roles[$role] );
        }
    }
    return $user_can;
}

//update data when order.

add_action( 'woocommerce_thankyou' , 'v3_update_date_api' );
function v3_update_date_api($order_id) {
    $order = wc_get_order($order_id);
    $order_items = $order->get_items('line_item');
    // Update specialist for order
    $current_user = get_post_meta($order_id, '_customer_user', true);
    if($current_user) {
        $specialist = get_user_meta($current_user, 'specialist', true);
        if($specialist) {
            update_post_meta($order_id, '_specialist_id', $specialist);
        }
    }
    if($order_items) {
        foreach ( $order_items as $item_id => $item ) {
            $time_completed = date( 'd/m/Y H:i a' , strtotime( v3_get_time_completed_item(v3_get_production_time_item($item ,$order , true) ,$order)['production_datetime_completed'] ) );
            wc_update_order_item_meta($item_id , '_item_time_completed' , $time_completed);
            // $opt_status = 'order_received';
            // wc_update_order_item_meta($item_id, '_item_status', $opt_status);
        }
    }
    //update_post_meta( $order_id , '_order_status' , 'New');
    $date_completed = date( 'd/m/Y H:i a' , strtotime(show_est_completion($order)['production_datetime_completed']) );
    update_post_meta( $order_id , '_order_time_completed', $date_completed );
    update_post_meta( $order_id , '_order_time_completed_str', strtotime(show_est_completion($order)['production_datetime_completed']) );

}

// create button payment authentic in order
add_action( 'add_meta_boxes_shop_order', 'v3_add_meta_boxes' );
function v3_add_meta_boxes() {
    $order_id = $_GET['post'];
    if(wc_get_order($order_id)->get_payment_method() != 'cod') { return; }
    add_meta_box(
        'nb_authentic_payment',
        'Payment Authentication',
        'nb_output_authentic_payment',
        'shop_order',
        'side',
        'high'
    );

    
}
function nb_output_authentic_payment() {
    $order_id = $_GET['post'];
    $status = get_post_meta($order_id , '_payment_status' , true);
    if(wc_get_order($order_id)->get_payment_method() != 'cod') { return; }
    ?>
    <style type="text/css">
        #nb_authentic_payment .authentic-wrap {
            clear: both;
            display: block;
            height: 50px;
        }
        #nb_authentic_payment .authentic-wrap .left {
            float: left;
            display: inline-block;
        }
         #nb_authentic_payment .authentic-wrap .right {
            float: right;
            display: inline-block;
        }
    </style>
    <div class="authentic-wrap">
        <div class="left">
            <div style="margin-bottom: 5px;"><b>RS Payment</b></div>
            <input type="checkbox" <?php if($status == 'paid') { echo 'checked'; } ?> value="authentic_payment" name="authentic_payment"><span>Paid</span>
        </div>
    </div>
    
    <?php 
}
add_action('woocommerce_process_shop_order_meta' , 'nb_save_authentic_payment' , 65 , 1);
function nb_save_authentic_payment($order_id) {
    $authentic_payment = isset($_POST['authentic_payment']) ? $_POST['authentic_payment']: '' ;
    if(isset($_POST['action']) && isset($_POST['action']) == 'editpost' && $authentic_payment == 'authentic_payment') {
        update_post_meta($order_id , '_payment_status' , 'paid');
    } else {
        update_post_meta($order_id , '_payment_status' , 'pendding');
    }
}

// Settings Delivery Plotter
add_action( 'admin_menu' , 'v3_add_admin_menu' );
function v3_add_admin_menu() {
    add_menu_page('New Order Dashboard', 'New Order Dashboard', 'nb_custom_dashboard', 'mybts/vue_order_dashboard', 'vue_order_dashboard_admin_page', 'dashicons-tickets', 7);
    add_submenu_page('mybts/vue_order_dashboard', 'Delivery Plotter', 'Delivery Plotter' , 'nb_custom_dashboard', 'mybts/delivery_plotter', 'v3_delivery_plotter' );
    add_submenu_page('mybts/vue_order_dashboard', 'Delivery Plotter (Settings)', 'Settings' , 'nb_custom_dashboard', 'mybts/settings', 'v3_delivery_plotter_settings' );
}

function vue_order_dashboard_admin_page()
{
    $user_id = get_current_user_id();
    $user = get_userdata($user_id);
    $specialist_linking = '';
    if (in_array('administrator', $user->roles) || in_array('production', $user->roles) || in_array('customer_service', $user->roles)) {
    } else {
        $user_name = $user->display_name;
        $linkeds = array();
        $linkeds[] = array(
            'name'  => $user_name,
            'id'    => $user_id,
        );
        $datas = unserialize(get_user_meta( $user_id, 'group_specialist' , true));
        if (count($datas) > 0) {
            foreach ($datas as $key => $value) {
                $u = get_userdata($value);
                $un = $u->display_name;
                $linkeds[] = array(
                    'name'  => $un,
                    'id'    => (int)$value,
                );
            }
        }
        
        $specialist_linking = json_encode($linkeds);
    }
    ?>
   
    <input type="text" id="specialist-linking" value="<?php echo esc_attr($specialist_linking);?>" style="display: none">
    <input type="text" id="current-user-id" value="<?php echo esc_attr($user_id);?>" style="display: none">
    <link href="<?php echo plugin_dir_url(__FILE__) .'../vue-dashboard/custome-assets/css/style.css'; ?>" rel="stylesheet">
    <link href="<?php echo plugin_dir_url(__FILE__) .'../vue-dashboard/css/app.css'; ?>" rel="stylesheet">
    <style>
        #hello-world .card {
           max-width: 100%!important;
        }
    </style>
    <div id=app>
    </div>
    <script type="text/javascript" src="<?php echo plugin_dir_url(__FILE__) .'../vue-dashboard/js/manifest.js?t='.strtotime("now"); ?>"></script>
    <script type="text/javascript" src="<?php echo plugin_dir_url(__FILE__) .'../vue-dashboard/js/vendor.js?t='.strtotime("now"); ?>"></script>
    <script type="text/javascript" src="<?php echo plugin_dir_url(__FILE__) .'../vue-dashboard/js/app.js?t='.strtotime("now"); ?>"></script>
    <?php
}
// custom xem ngày order trong plotter
add_action('wp_ajax_v3_custom_get_order_detail', 'v3_custom_get_order_detail');
add_action('wp_ajax_nopriv_v3_custom_get_order_detail', 'v3_custom_get_order_detail');
function v3_custom_get_order_detail() {
    $params = array();
    $results = array(
        'flag'  => 0,
        'data'  => 'No results'
    );
    $order_id = isset($_POST['order_id']) ? $_POST['order_id'] : 0;
    if( $order_id ) {
        $check_in_plot = get_post_meta($_value->ID , '_order_in_plot' , true);
        $date = get_post_meta($order_id , '_order_in_plot_completed', true);
        $data_cpl = (int) get_post_meta($order_id , '_order_time_completed_str', true);
        if($check_in_plot) {
            $results = array(
                'flag'  => 1,
                'data'  => $date
            );
        } else {
            $results = array(
                'flag'  => 1,
                'data'  => date( 'Y-m-d' , $data_cpl).' maybe: '. date( 'Y-m-d' , $data_cpl + 86400 )
            );
        }
    }
    wp_send_json_success($results);
 
    die();
}
function v3_delivery_plotter() {
    ?>

    <div style="padding: 20px 15px;">
        <div><b>Check order in plotter</b></div>
        <input class="order-id-check" type="number" width="300" name="order-id-check"> <div id="botak-check-order" class="btn btn-primary">Check</div>
        <div><b>Order in:</b><span class="botak-order-results"></span></div>
    </div>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('#botak-check-order').on('click' , function() {
                var order_id = $('.order-id-check').val();
                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    url: '<?php echo admin_url('admin-ajax.php');?>',
                    data : {
                        action: "v3_custom_get_order_detail",
                        order_id : order_id,
                    },
                    success: function (data) {
                        console.log(data);
                        if (data.data.flag == 1) {
                            $('.botak-order-results').html(data.data.data);
                        } else {
                            $('.botak-order-results').html('No results');
                        }
                    },
                    error: function (xhr, status, error) {
                        alert(error);
                    }
                });
            });
        });
    </script>

    <link href="<?php echo plugin_dir_url(__FILE__) .'../vue-plotter/css/app.css'; ?>" rel="stylesheet">
    <style>
        #hello-world .card {
           max-width: 100%!important;
        }
    </style>
    <div id=app>
    </div>
    <script type="text/javascript" src="<?php echo plugin_dir_url(__FILE__) .'../vue-plotter/js/manifest.js?t='.strtotime("now"); ?>"></script>
    <script type="text/javascript" src="<?php echo plugin_dir_url(__FILE__) .'../vue-plotter/js/vendor.js?t='.strtotime("now"); ?>"></script>
    <script type="text/javascript" src="<?php echo plugin_dir_url(__FILE__) .'../vue-plotter/js/app.js?t='.strtotime("now"); ?>"></script>
    <?php
}

// get all shipping method
function v3_get_shipping_method() {
    global $wpdb;
    $zone_id = 3;
    $shipping_method = array();
    $query_method = "SELECT * FROM wp_woocommerce_shipping_zone_methods WHERE zone_id = 3 AND is_enabled = 1";
    $query_zone_method = $wpdb->get_results($query_method);
    if( isset($query_zone_method) ) {
        foreach ($query_zone_method as $key => $value) {
            $instance_id = $value->instance_id;
            $method_id   = $value->method_id;
            // $_text = 'woocommerce_'.$method_id.'_'.$instance_id.'_settings';
            // return $_text;
            $shipping_info = get_option('woocommerce_'.$method_id.'_'.$instance_id.'_settings') ;
            $shipping_method[] = array(
                'title' => $shipping_info['title'],
                'key'   => $shipping_info['title'],
            );
            if($instance_id == 17) {
                $shipping_method[] = array(
                    'title' => $shipping_info['title'],
                    'key'   => 'delivery_clone',
                );
            }
        }
    }
    return $shipping_method;
}

// BOtak Custom Get shipping medthod

function v3_get_shipping_method_order($order) {
    global $wpdb;
    $shipping_method_c = $order->get_shipping_method();
    $shipping_method = array();
    // get and set shipping meothod
    //[{"zone_id":"3","instance_id":"16","method_id":"local_pickup","method_order":"3","is_enabled":"1"},
    // {"zone_id":"3","instance_id":"20","method_id":"table_rate","method_order":"5","is_enabled":"1"}]
    $zone_id = 3;
    $query_method = "SELECT * FROM wp_woocommerce_shipping_zone_methods WHERE zone_id = 3 AND is_enabled = 1";
    $query_zone_method = $wpdb->get_results($query_method);
    if( isset($query_zone_method) ) {
        foreach ($query_zone_method as $key => $value) {
            $shipping_info = get_option('woocommerce_'. $value->method_id.'_'.$value->instance_id.'_settings') ;
            if($value->method_id == 'table_rate') {
                $instance_id = $value->instance_id;
                $query_rate = "SELECT comment FROM wp_woocommerce_shipping_table_rate WHERE wp_woocommerce_shipping_table_rate.instance_id = '${instance_id}'";
                foreach ($wpdb->get_results($query_rate) as $rate) {
                    $shipping_method[$rate->comment] = array(
                        'value' => 2,
                        'name'  => $shipping_info['title'],
                    );
                }
            } else {
                $shipping_method[$shipping_info['title']] = 1;
            }
        }
        update_option('_get_title_shipping_method' , serialize($shipping_method));

    }
    if( $shipping_method[$shipping_method_c] == 1 ) {
        return $shipping_method_c;
    } else {
        if( $shipping_method[$shipping_method_c]['value'] == 2) {
            return $shipping_method[$shipping_method_c]['name'];
        }
    }
}
function v3_time_to_minutes($time) {
    $time = explode(':', $time);
    return $time[0] *60 + $time[1];
}
function v3_convert_time($time) {
    $time = date('h:i (a)' , strtotime($time));
    return $time;
}
function v3_delivery_plotter_settings() {
    $shipping_methods = v3_get_shipping_method();
    $period_calc_options = unserialize(get_option('period_calc_options'));
    $period_dp_options = unserialize(get_option('period_dp_options'));
    $plotting_options = unserialize(get_option('plotting_options'));
    if(!$plotting_options) {
        if($shipping_methods) {
            foreach ($shipping_methods as $key => $shipping_method) {
                $item_plotting['shipping_method']['title'] = $shipping_method['title'];
                $item_plotting['shipping_method']['key'] = $shipping_method['key'];
                $plotting_options[$key] = $item_plotting;
            }
            update_option( 'plotting_options' , serialize($plotting_options) ); 
        }
    } else {
        if($shipping_methods) {
            $_plotting_options = array();
            foreach ($shipping_methods as $key => $shipping_method) {
                $check = false;
                $_shipping_method   = array();
                $_date              = '';
                $_period_calc       = '';
                $_period_dp         = '';
                foreach ($plotting_options as $key1 => $plotting_option) {
                    if($shipping_method['key'] == $plotting_option['shipping_method']['key']) {
                        $check = true;
                        $_shipping_method['title']    = $plotting_option['shipping_method']['title'];
                        $_shipping_method['key']    = $plotting_option['shipping_method']['key'];
                        $_date               = $plotting_option['date'];
                        $_period_calc        = $plotting_option['period_calc'];
                        $_period_dp          = $plotting_option['period_dp'];
                    }
                }
                if($check) {
                    $item_plotting['shipping_method']['title']   = $_shipping_method['title'];
                    $item_plotting['shipping_method']['key']   = $_shipping_method['key'];
                    $item_plotting['date']              = $_date;
                    $item_plotting['period_calc']       = $_period_calc;
                    $item_plotting['period_dp']         = $_period_dp;
                } else {
                    $item_plotting['shipping_method']['title'] = $shipping_method['title'];
                    $item_plotting['shipping_method']['key'] = $shipping_method['key'];
                }
                $_plotting_options[$key] = $item_plotting;
            }
            $plotting_options = $_plotting_options;
            update_option( 'plotting_options' , serialize($plotting_options) ); 
        }
    }
    if(isset($_POST['status-updated']) && $_POST['status-updated'] == 'updated') {
        $item_plotting = array();
        if(isset($_POST['period_calc_options'])) {
            update_option('period_calc_options' , serialize($_POST['period_calc_options']));
        } else {
            update_option('period_calc_options' , serialize(array('other')));
        }
        if(isset($_POST['period_dp_options'])) {
            update_option('period_dp_options' , serialize($_POST['period_dp_options']));
        } else {
            update_option('period_dp_options' , serialize(array('other')));
        }
        $period_calc_options = unserialize(get_option('period_calc_options'));
        $period_dp_options = unserialize(get_option('period_dp_options'));
        $value_option = '';
        if( isset($_POST['plotting_options']) ) {
            foreach ($shipping_methods as $key => $shipping_method) {
                $item_plotting['shipping_method']['title'] = $shipping_method['title'];
                $item_plotting['shipping_method']['key'] = $shipping_method['key'];
                $item_plotting['period_calc'] = $_POST['plotting_options']['period_calc'] ? $_POST['plotting_options']['period_calc'][$key] : '';
                $item_plotting['period_dp'] = $_POST['plotting_options']['period_dp'] ? $_POST['plotting_options']['period_dp'][$key] : '';
                $item_plotting['date'] =  $_POST['plotting_options']['date'] ? $_POST['plotting_options']['date'][$key] : '';
                // if( $item_plotting['shipping_method']['title'] == 'Delivery' ) {
                //     if( $value_option == '' ) {
                //         $value_option =  $_POST['plotting_options']['date'] ? $_POST['plotting_options']['date'][$key] : '';
                //     } else {
                //        $item_plotting['date'] =  $value_option;       
                //     }
                    
                // }                
                $plotting_options[$key] = $item_plotting;
            }
            update_option( 'plotting_options' , serialize($plotting_options) );
        }
    }
    
    
    ?>  
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <style type="text/css">
        #delivery-plotter table {
            max-width: 500px;
        }
        #delivery-plotter .item-meta {
            border-radius: 0.25rem;
            border: 1px solid #dee2e6;
            padding: 20px;
            margin: 20px 15px;
        }
        #delivery-plotter .period-new {
            margin: 0 10px 20px 0;
        }
        #delivery-plotter .hidden {
            display: none!important;
        }
        #delivery-plotter .row-period .title {
            width: 75%;
        }
        #delivery-plotter .icon-period {
            text-align: center;
            cursor: pointer;
        }
        #delivery-plotter .icon-period:hover {
            color: #646668;
        }
        #delivery-plotter .delete-period {
            color: #f00;
        }
        #delivery-plotter .item-meta label , #delivery-plotter .item-meta .period-from {
            margin-right: 10px;
        }
    </style>
    <div id="delivery-plotter">
        <form method='post' action=''>
            <div class="title">
                <h1>Settings</h1>
            </div>
            <div class="delivery-plotter-wrap">
                <div class="item-wrap plotting-wrap">
                    <div class="item-title">
                        <b>Plotting</b>
                    </div>
                    <div class="item-meta">
                        <table class="table" style="border: none">
                            <thead>
                                <tr>
                                    <th>Method</th>
                                    <th>Date</th>
                                    <th>Period Est.Completed</th>
                                    <th>Period Delivery</th>
                                </tr>
                            </thead>
                           <tbody>
                                <?php 
                                if($plotting_options) {
                                    foreach ($plotting_options as $plotting_option) {
                                        $_date = isset($plotting_option['date']) ? $plotting_option['date'] : '';
                                        $_period_calc = isset($plotting_option['period_calc']) ? $plotting_option['period_calc'] : '';
                                        $_period_dp = isset($plotting_option['period_dp']) ? $plotting_option['period_dp'] : '';
                                        ?>
                                        <tr>
                                            <td><?php echo esc_html($plotting_option['shipping_method']['title']); ?></td>
                                            <td>
                                                <select name="plotting_options[date][]">
                                                    <option <?php echo $_date == "none" ? 'selected' : '' ?> value="none">None</option>
                                                    <option <?php echo $_date == "next_day" ? 'selected' : '' ?> value="next_day">Next Day</option>
                                                    <option <?php echo $_date == "same_day" ? 'selected' : '' ?> value="same_day">Same Day</option>
                                                </select>
                                            </td>
                                            <td>
                                                <select style="min-width: 100px;" name="plotting_options[period_calc][]" >
                                                    <option value=""></option>
                                                    <?php
                                                    if($period_calc_options) {
                                                        foreach ($period_calc_options as $key => $period) {
                                                            if( strpos( $period , '-')) {
                                                                $period_from = explode('-', $period)[0];
                                                                $period_to = explode('-', $period)[1];
                                                                $period = v3_convert_time($period_from).' - '.v3_convert_time($period_to);
                                                                $period_value = $period_from.'-'.$period_to;
                                                            } else {
                                                                $period_from = '';
                                                                $period_to = '';
                                                                $period_input = true;
                                                                $period_value = $period;
                                                            }
                                                            ?>
                                                                <option <?php echo $_period_calc == $period_value ? "selected" : '';?>  value="<?php echo $period_value;?>"><?php echo $period;?></option>
                                                            <?php
                                                        }  
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td>
                                                <select style="min-width: 100px;" name="plotting_options[period_dp][]" >
                                                    <option value=""></option>
                                                    <?php
                                                    if($period_dp_options) {
                                                        foreach ($period_dp_options as $key => $period) {
                                                            if( strpos( $period , '-')) {
                                                                $period_from = explode('-', $period)[0];
                                                                $period_to = explode('-', $period)[1];
                                                                $period = v3_convert_time($period_from).' - '.v3_convert_time($period_to);
                                                                $period_value = $period_from.'-'.$period_to;
                                                            } else {
                                                                $period_from = '';
                                                                $period_to = '';
                                                                $period_input = true;
                                                                $period_value = $period;
                                                            }
                                                            ?>
                                                                <option <?php echo $_period_dp == $period_value ? "selected" : '';?>  value="<?php echo $period_value;?>"><?php echo $period;?></option>
                                                            <?php
                                                        }  
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                            </tbody>
                        </table>            
                    </div>
                </div>
                <div class="item-wrap period-wrap">
                    <div class="item-title">
                        <b>Period(s)</b>
                    </div>
                    <div class="item-meta">
                        <div class="row">
                            <div class="col-md-6 col-calc">
                                <h5 style="margin-bottom: 10px;">Period Est.Completed</h5>
                                <label for="period-calc-from" >From: </label><input id="period-calc-from" class="period-new" type="time"><label for="period-calc-to">To: </label><input id="period-calc-to" class="period-new" type="time"><div class="button button-primary add-new-period-calc">+ Add New</div>
                                <table class="table table-striped table-bordered table-period">
                                    <thead>
                                        <tr>
                                            <th colspan="3">Period(s)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        if(!$period_calc_options) {
                                            $period_calc_options = array(
                                                'other'
                                            );
                                        }
                                        $period_input = false;
                                        if($period_calc_options) {
                                            foreach ($period_calc_options as $period) {
                                                if( strpos( $period , '-')) {
                                                    $period_from = explode('-', $period)[0];
                                                    $period_to = explode('-', $period)[1];
                                                    $period = v3_convert_time($period_from).' - '.v3_convert_time($period_to);
                                                    $period_value = $period_from.'-'.$period_to;
                                                } else {
                                                    $period_from = '';
                                                    $period_to = '';
                                                    $period_input = true;
                                                    $period_value = $period;
                                                }
                                                ?>
                                                <tr class="row-period">
                                                    <td class="title">
                                                        <div class="period-title"><?php echo $period; ?></div>
                                                        
                                                        <?php 
                                                        if($period_input) {
                                                            ?>
                                                            <input class="period-value period-from  hidden" value="<?php echo $period; ?>" type="text">
                                                            <?php
                                                        } else {
                                                            ?>
                                                            <input class="period-value period-from hidden" value="<?php echo $period_from; ?>" type="time">
                                                            <input class="period-value period-to hidden" value="<?php echo $period_to; ?>" type="time">
                                                            <?php
                                                        }
                                                        ?>
                                                        
                                                        <input type="hidden" class="period-time-value" name="period_calc_options[]" value="<?php echo $period_value; ?>">
                                                    </td>
                                                    <td>
                                                        <div class="icon-period edit-period" onclick="edit_period(this)">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                                              <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456l-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                                              <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                                                            </svg>
                                                        </div>
                                                        <div class="icon-period save-period hidden" onclick="save_period(this)">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-save" viewBox="0 0 16 16">
                                                              <path d="M2 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H9.5a1 1 0 0 0-1 1v7.293l2.646-2.647a.5.5 0 0 1 .708.708l-3.5 3.5a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L7.5 9.293V2a2 2 0 0 1 2-2H14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h2.5a.5.5 0 0 1 0 1H2z"/>
                                                            </svg>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="icon-period delete-period" onclick="delete_period(this)">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                                                                <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                                            </svg>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        } else {
                                            echo '<tr></tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6 col-display">
                                <h5 style="margin-bottom: 10px;">Period Delivery</h5>
                                <label for="period-dp-from" >From: </label><input id="period-dp-from" class="period-new" type="time"><label for="period-dp-to">To: </label><input id="period-dp-to" class="period-new" type="time"><div class="button button-primary add-new-period-dp">+ Add New</div>
                                <table class="table table-striped table-bordered table-period">
                                    <thead>
                                        <tr>
                                            <th colspan="3">Period(s)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        if(!$period_dp_options) {
                                            $period_dp_options = array(
                                                'other'
                                            );
                                        }
                                        $period_input = false;
                                        if($period_dp_options) {
                                            foreach ($period_dp_options as $period) {
                                                if( strpos( $period , '-')) {
                                                    $period_from = explode('-', $period)[0];
                                                    $period_to = explode('-', $period)[1];
                                                    $period = v3_convert_time($period_from).' - '.v3_convert_time($period_to);
                                                    $period_value = $period_from.'-'.$period_to;
                                                } else {
                                                    $period_from = '';
                                                    $period_to = '';
                                                    $period_input = true;
                                                    $period_value = $period;
                                                }
                                                ?>
                                                <tr class="row-period">
                                                    <td class="title">
                                                        <div class="period-title"><?php echo $period; ?></div>
                                                        
                                                        <?php 
                                                        if($period_input) {
                                                            ?>
                                                            <input class="period-value period-from  hidden" value="<?php echo $period; ?>" type="text">
                                                            <?php
                                                        } else {
                                                            ?>
                                                            <input class="period-value period-from hidden" value="<?php echo $period_from; ?>" type="time">
                                                            <input class="period-value period-to hidden" value="<?php echo $period_to; ?>" type="time">
                                                            <?php
                                                        }
                                                        ?>
                                                        
                                                        <input type="hidden" class="period-time-value" name="period_dp_options[]" value="<?php echo $period_value; ?>">
                                                    </td>
                                                    <td>
                                                        <div class="icon-period edit-period" onclick="edit_period(this)">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                                              <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456l-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                                              <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                                                            </svg>
                                                        </div>
                                                        <div class="icon-period save-period hidden" onclick="save_period(this)">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-save" viewBox="0 0 16 16">
                                                              <path d="M2 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H9.5a1 1 0 0 0-1 1v7.293l2.646-2.647a.5.5 0 0 1 .708.708l-3.5 3.5a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L7.5 9.293V2a2 2 0 0 1 2-2H14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h2.5a.5.5 0 0 1 0 1H2z"/>
                                                            </svg>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="icon-period delete-period" onclick="delete_period(this)">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                                                                <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                                            </svg>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        } else {
                                            echo '<tr></tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
             <p class='submit'>
                <input type='submit' name='Submit' class='button-primary' value='<?php echo 'Save Changes'; ?>' />
                <input type='hidden' name='status-updated' value='updated'/>
            </p>
        </form>
    </div>
    <script type="text/javascript">
        // function change_time(e) {
        //     var time_from = jQuery(e).parents('.item-meta').find('#period-from').val();
        //     jQuery(e).parents('.item-meta').find('#period-to').attr('min' , time_from);
        // }
        function edit_period(e) {
            jQuery(e).parents('.row-period').find('.period-title').toggleClass('hidden');
            jQuery(e).parents('.row-period').find('.period-value').toggleClass('hidden');
            jQuery(e).parents('.row-period').find('.edit-period').toggleClass('hidden');
            jQuery(e).parents('.row-period').find('.save-period').toggleClass('hidden');
        }
        function save_period(e) {
            jQuery(e).parents('.row-period').find('.period-title').toggleClass('hidden');
            jQuery(e).parents('.row-period').find('.period-value').toggleClass('hidden');
            jQuery(e).parents('.row-period').find('.edit-period').toggleClass('hidden');
            jQuery(e).parents('.row-period').find('.save-period').toggleClass('hidden'); 
            if( jQuery(e).parents('.row-period').find('.period-value').val() ) {
                var change = jQuery(e).parents('.row-period').find('.period-value').val();
                var value_change = jQuery(e).parents('.row-period').find('.period-value').val();
                var period_from = jQuery(e).parents('.row-period').find('.period-from').val(); 
                var period_to = jQuery(e).parents('.row-period').find('.period-to').val();
                if(period_from && period_to) {
                    var change = conver_time(period_from)+' - '+conver_time(period_to);
                    var value_change = period_from+'-'+period_to;
                }
            }
            jQuery(e).parents('.row-period').find('.period-title').html(change);          
            jQuery(e).parents('.row-period').find('.period-time-value').val(value_change);          
        }
        function delete_period(e) {
            jQuery(e).parents('.row-period').remove();
        }
        function conver_time(time) {
            var d = new Date('1 '+time);
            var hours = d.getHours();
            var minutes = d.getMinutes();
            if(hours >= 12) {
                hours = hours == 12 ? hours : hours -12;
                hours = hours <10 ? '0'+hours : hours;
                minutes = minutes <10 ? '0'+minutes : minutes;
                var time = hours+':'+minutes+ ' (pm)'
            } else {
                hours = hours == 0 ? 12 : hours;
                hours = hours <10 ? '0'+hours : hours;
                minutes = minutes <10 ? '0'+minutes : minutes;
                var time = hours+':'+minutes+ ' (am)'
            }
            
            return time;
        }
        jQuery(document).ready(function($){
            // $('.change-value-clone').on('change' , function(e) {
            //     var value_option = $(e.currentTarget).val();
            //     console.log(value_option);
            //     $('.change-value-clone').val(value_option);
            // })
            $('.add-new-period-calc').on('click' , function(value , key) {
                var period_calc_from = $('input#period-calc-from').val();
                var period_calc_to = $('input#period-calc-to').val();

                if(period_calc_from && period_calc_to) {
                    var time_l = conver_time(period_calc_from)+' - '+conver_time(period_calc_to);
                    var period_l = period_calc_from+'-'+ period_calc_to;
                } else {
                    var period_l = '';
                    var time_l = '';
                }

                var item_last_left = $('#delivery-plotter .col-calc .table-period > tbody tr.row-period:last-child');
                $('#delivery-plotter .col-calc .table-period > tbody tr.row-period:last-child').remove();
                $('#delivery-plotter .col-calc .table-period > tbody:last-child').append('<tr class="row-period"><td class="title"><div class="period-title">'+time_l+'</div><input class="period-value period-from hidden" value="'+period_calc_from+'" type="time"><input class="period-value period-to hidden" value="'+period_calc_to+'" type="time"><input type="hidden" class="period-time-value" name="period_calc_options[]" value="'+period_l+'"></td> <td> <div class="icon-period edit-period" onclick="edit_period(this)"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16"> <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456l-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/> <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/> </svg></div><div class="icon-period save-period hidden" onclick="save_period(this)"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-save" viewBox="0 0 16 16"> <path d="M2 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H9.5a1 1 0 0 0-1 1v7.293l2.646-2.647a.5.5 0 0 1 .708.708l-3.5 3.5a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L7.5 9.293V2a2 2 0 0 1 2-2H14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h2.5a.5.5 0 0 1 0 1H2z"/> </svg> </div></td><td> <div class="icon-period delete-period" onclick="delete_period(this)"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16"> <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/> </svg> </div> </td> </tr>');
                $('#delivery-plotter .col-calc .table-period > tbody:last-child').append(item_last_left);

            });
            $('.add-new-period-dp').on('click' , function(value , key) {
                var period_dp_from = $('input#period-dp-from').val();
                var period_dp_to = $('input#period-dp-to').val();

                if(period_dp_from && period_dp_to) {
                    var time_r = conver_time(period_dp_from)+' - '+conver_time(period_dp_to);
                    var period_r = period_dp_from+'-'+ period_dp_to;
                } else {
                    var period_r = '';
                    var time_r = '';
                }

                var item_last_right = $('#delivery-plotter .col-display .table-period > tbody tr.row-period:last-child');
                $('#delivery-plotter .col-display .table-period > tbody tr.row-period:last-child').remove();
                $('#delivery-plotter .col-display .table-period > tbody:last-child').append('<tr class="row-period"><td class="title"><div class="period-title">'+time_r+'</div><input class="period-value period-from hidden" value="'+period_dp_from+'" type="time"><input class="period-value period-to hidden" value="'+period_dp_to+'" type="time"><input type="hidden" class="period-time-value" name="period_dp_options[]" value="'+period_r+'"></td> <td> <div class="icon-period edit-period" onclick="edit_period(this)"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16"> <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456l-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/> <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/> </svg></div><div class="icon-period save-period hidden" onclick="save_period(this)"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-save" viewBox="0 0 16 16"> <path d="M2 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H9.5a1 1 0 0 0-1 1v7.293l2.646-2.647a.5.5 0 0 1 .708.708l-3.5 3.5a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L7.5 9.293V2a2 2 0 0 1 2-2H14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h2.5a.5.5 0 0 1 0 1H2z"/> </svg> </div></td><td> <div class="icon-period delete-period" onclick="delete_period(this)"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16"> <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/> </svg> </div> </td> </tr>');
                $('#delivery-plotter .col-display .table-period > tbody:last-child').append(item_last_right);
            });
        }); 
    </script>
    <?php
}

add_filter( 'woocommerce_hidden_order_itemmeta', 'add_hidden_order_items' );
function add_hidden_order_items( $order_items ) {
    $order_items[] = 'item_status';
    $order_items[] = '_item_status';
    $order_items[] = 'item_meta_service';
    $order_items[] = '_item_meta_service';
    $order_items[] = 'item_meta_issue';
    $order_items[] = 'item_on_hold';
    $order_items[] = '_item_id_service';
    $order_items[] = '_item_time_completed';
    $order_items[] = '_cart_item_key';
    $order_items[] = '_product_type';
    $order_items[] = '_nbtwccs_order_rate';
    $order_items[] = '_nbtwccs_order_base_currency';
    $order_items[] = '_nbtwccs_order_currency_changed_mannualy';
    $order_items[] = '_item_date_out';
    // and so on...
    return $order_items;
}

function v3_check_link_download_order($order_id) {
    $order = wc_get_order($order_id);
    $check_item = false;
    if($order) {
        $order_items = $order->get_items('line_item');
        foreach ($order_items as $item_id => $item) {
            $nbu_item_key = wc_get_order_item_meta($item_id, '_nbu');
            if($nbu_item_key) {
                $files = botak_get_list_file_s3('reupload-design/'. $nbu_item_key);
                if(count($files)) {
                    $check_item = true;
                    break;
                }
            }         
        }
    }
    return $check_item;
    // if(!v3_getLinkAWS($order_id)) {
    //     $check_link = true;
    // }
    // if($check_link &&  $check_item ) {
    //     return true;
    // } else {
    //     return false;
    // }
}
function v3_get_link_download_order($order_id) {
    $order = wc_get_order($order_id);
    if($order) {
        $products = $order->get_items();
        $list_file = array();
        $list_files = array();
        $no = 1;
        foreach($products as $order_item_id => $product) {
            $product_name = $product->get_name();
            $nbu_item_key = wc_get_order_item_meta($order_item_id, '_nbu');
            if($nbu_item_key) {
                $files = botak_get_list_file_s3('reupload-design/'. $nbu_item_key);
                foreach ($files as $i => $file) {
                    $list_file['no'] = $no;
                    $list_file['link'] = $file;
                    $list_file['name'] = $product_name;
                    $list_file['size'] = v3_get_size_link_file($file);
                    $list_file['file_name'] = basename($file);
                    $list_files[] = $list_file;
                }
            } 
            $no++;           
        }
    }
    return $list_files;
}
function v3_get_size_link_file($link = '') {
    if($link):
        $ch = curl_init($link);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, TRUE);
        curl_setopt($ch, CURLOPT_NOBODY, TRUE);

        $data = curl_exec($ch);
        $size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);

        curl_close($ch);
        return  round($size/(1024*1024) , 2). ' Mb';
    endif;
}
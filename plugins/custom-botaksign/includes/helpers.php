<?php

if (!function_exists('detect_search_engines')) {
    function detect_search_engines($useragent)
    {
        $searchengines = array(
            'Googlebot',
            'Slurp',
            'search.msn.com',
            'nutch',
            'simpy',
            'bot',
            'ASPSeek',
            'crawler',
            'msnbot',
            'Libwww-perl',
            'FAST',
            'Baidu',
        );

        $is_se = false;

        foreach ($searchengines as $searchengine) {
            if (!empty($_SERVER['HTTP_USER_AGENT']) and false !== strpos(strtolower($useragent), strtolower($searchengine))) {
                $is_se = true;
                break;
            }
        }
        if ($is_se) {
            return true;
        } else {
            return false;
        }

    }
}

/**
 * Test a users capability
 *
 * Checks if user has `manage_woocommerce` abilities by default.
 */
function cxecrt_test_user_role($role = NULL)
{

    switch ($role) {

        case 'administrator':
            $capability = 'manage_options';
            break;

        case 'shop_manager':
        default:
            $capability = 'manage_woocommerce';
            break;
    }

    $user_id = get_current_user_id();

    return user_can($user_id, $capability);
}

/**
 * Loads Resources required to render the cart templates,
 * if we try to render it while not on the frontend.
 */
function cxecrt_maybe_load_required_cart_resources()
{

    $files = get_included_files();

    // All of the following are omitted during WC()->init(),
    // by being wrapped in `if ( is_request( 'frontend' ) )...`
    // and we may not be on the frontend.

    WC()->frontend_includes();

    $file_name = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, plugin_dir_path(WC_PLUGIN_FILE) . 'includes\abstracts\abstract-wc-session.php');
    if (file_exists($file_name) && !in_array($file_name, $files)) { // ! class_exists( 'WC_Session' )
        // s( 'Had to include: abstract-wc-session.php' ); // Debug
        include_once($file_name);
    }

    $file_name = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, plugin_dir_path(WC_PLUGIN_FILE) . 'includes\class-wc-session-handler.php');
    if (file_exists($file_name) && !in_array($file_name, $files)) { // ! class_exists( 'WC_Session_Handler' )
        // s( 'Had to include: class-wc-session-handler.php' ); // Debug
        include_once($file_name);
    }

    $file_name = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, plugin_dir_path(WC_PLUGIN_FILE) . 'includes\wc-template-functions.php');
    if (file_exists($file_name) && !in_array($file_name, $files)) { // ! function_exists( 'wc_template_redirect' )
        // s( 'Had to include: wc-template-functions.php' ); // Debug
        include_once($file_name);
    }

    if (!isset(WC()->session)) {
        // s( 'Had to do: session' ); // Debug
        $session_class = apply_filters('woocommerce_session_handler', 'WC_Session_Handler');
        WC()->session = new $session_class();
    }

    if (!isset(WC()->cart)) {
        // s( 'Had to do: cart' ); // Debug
        WC()->cart = new WC_Cart();
        remove_action('shutdown', array(WC()->cart, 'maybe_set_cart_cookies'), 0); // Set cookies before shutdown and ob flushing.
    }

    if (!isset(WC()->customer)) {
        // s( 'Had to do: customer' ); // Debug
        WC()->customer = new WC_Customer();
    }
}

/**
 * Get the WC cart url.
 */

function cxecrt_get_woocommerce_cart_url()
{

    // Since WC2.5.0
    if (function_exists('wc_get_cart_url'))
        return wc_get_cart_url();

    // If we on the front-end and the WC cart is already loaded.
    if (isset(WC()->cart) && method_exists(WC()->cart, 'get_cart_url'))
        return WC()->cart->get_cart_url();

    // If we on the backend and the WC cart is not loaded.
    if ($cart_page_id = get_option('woocommerce_cart_page_id'))
        return get_permalink($cart_page_id);
    else
        return get_permalink('cart');
}

/**
 * Get the WC checkout url.
 */

function cxecrt_get_woocommerce_checkout_url()
{

    // Since WC2.5.0
    if (function_exists('wc_get_checkout_url'))
        return wc_get_checkout_url();

    // If we on the front-end and the WC cart is already loaded.
    if (isset(WC()->cart) && method_exists(WC()->cart, 'get_checkout_url'))
        return WC()->cart->get_checkout_url();

    // If we on the backend and the WC cart is not loaded.
    if ($cart_page_id = get_option('woocommerce_checkout_page_id'))
        return get_permalink($cart_page_id);
    else
        return get_permalink('cart');
}

/**
 * Get one of our options.
 *
 * Automatically mixes in our defaults if nothing is saved yet.
 *
 * @param string $key key name of the option.
 * @return mixed       the value stored with the option, or the default if nothing stored yet.
 */
function cxecrt_get_option($key)
{
    return get_option($key, cxecrt_get_default($key));
}

/**
 * Get one of defaults options.
 *
 * @param string $key key name of the option.
 * @return mixed       the default set for that option, or FALSE if none has been set.
 */
function cxecrt_get_default($key)
{

    $settings = WC_Email_Cart_Settings::get_settings();

    $default = FALSE;

    foreach ($settings as $setting) {
        if (isset($setting['id']) && $key == $setting['id'] && isset($setting['default'])) {
            $default = $setting['default'];
        }
    }

    return $default;
}

function generate_quote_pdf($quote_id)
{
    global $wpdb;
    $html = '';
    $cart_status = get_post_meta($quote_id, '_cxecrt_status', true);
    if ($cart_status == 1 || isset($_POST['noauth'])) {
        $cart = new WCEC_Saved_Cart();
        $cart->load_saved_cart($quote_id);
        $invoice_text_01 = '<table id="header-infor" style="width:100%">
            <tr>
            <td style="width:50%; padding-top:5px;" class="bill-to-th" align="left"><h2 class="bill-to">ATTN:</h2></td>
            <td style="width:50%;" class="cash-invoice-no-th" align="right"><h2 class="cash-invoice-no">QUOTATION NO.: ' . $quote_id . '</h2></td>
            </tr>
            </table>';
        $country = get_user_meta($cart->cart_author_id, 'billing_country', '')[0];
        if ($country != '') {
            $country = WC()->countries->countries[get_user_meta($cart->cart_author_id, 'billing_country', '')[0]];
        }
        $invoice_text_02 = '<table style="width:100%">
            <tr>
            <td style="width:65%" align="left"><span class="tex-bol" >' . $cart->cart_author_fullname . '</span></td>
            <td style="width:20%" align="right"><span class="tex-bol" >Quotation Date:</span></td>
            <td style="width:15%" align="right"><span class="tex-sub" >' . date('d M Y', strtotime($cart->cart_date)) . '</span></td>
            </tr>
            <tr>
            <td style="width:50%" align="left">
            <span class="tex-sub" >' . get_user_meta($cart->cart_author_id, 'billing_address_1', '')[0] . '</span>
            <span class="tex-sub" >' . get_user_meta($cart->cart_author_id, 'billing_address_2', '')[0] . '</span>
            <br>
            <span class="tex-sub" >' . $country . ' ' . get_user_meta($cart->cart_author_id, 'billing_postcode', '')[0] . '</span>
            <br>
            <span class="tex-sub" >' . get_user_meta($cart->cart_author_id, 'billing_email', '')[0] . '</span>
            <br>
            <span class="tex-sub" >' . get_user_meta($cart->cart_author_id, 'billing_phone', '')[0] . '</span>
            <br>
            <span class="tex-sub" >' . get_user_meta($cart->cart_author_id, 'billing_company', '')[0] . '</span>
            </td>
            </tr>
            </table>';
        $head_table_pro = '<table class="product" style="width:100%">
            <tr>
            <td class="page-invoice" style="width:30px;padding-bottom:5px;padding-top: 50px" class="stt" align="left">No.</td>
            <td class="page-invoice" style="width:400px;padding-bottom:5px;padding-top: 50px" class="description" align="left">Description</td>
            <td class="page-invoice" style="width:80px;padding-bottom:5px;padding-top: 50px" class="qty" align="center">Qty</td>
            <td class="page-invoice" style="width:80px;padding-bottom:5px;padding-top: 50px" class="unit_price" align="right">Unit Price</td>
            <td class="page-invoice" style="width:80px;padding-bottom:5px;padding-top: 50px" class="amount" align="right">Amount</td>
            </tr>';
        $invoice_product_page_02 = $head_table_pro;
        //$cartitems = get_post_meta($quote_id, '_cxecrt_cart_data', true);
        $pmt = $wpdb->get_row("SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE post_id = {$quote_id} AND meta_key = '_cxecrt_cart_data'");
        if ($pmt) {
            $cartitems = $pmt->meta_value;
        }
        $items_arr = str_replace(array('O:17:"WC_Product_Simple"', 'O:10:"WC_Product"'), 'O:8:"stdClass"', $cartitems);
        if (isset($cartitems) && $cartitems != false) {
            preg_match('/^s\:\d+\:\"(.*?)\"\;$/', $items_arr, $output_array);
            if (count($output_array) == 2) {
                //$order_items = (array) maybe_unserialize($items_arr);
                $order_items = maybe_unserialize($output_array[1]);
            }
        }
        $loop = 0;
        $arr_variation = cxecrt_get_variation_product_cart($quote_id);
        $strlength = 0;
        if (sizeof($order_items) > 0 && $order_items != false) {
            $subtotal = 0;
            foreach ($order_items as $item) {
                $subtotal += $item['line_total'];
                if (function_exists('wc_get_product')) {
                    if (isset($item['variation_id']) && $item['variation_id'] > 0) :
                        $_product = wc_get_product($item['variation_id']);
                    else :
                        $_product = wc_get_product($item['product_id']);
                    endif;
                } else {
                    if (isset($item['variation_id']) && $item['variation_id'] > 0) :
                        $_product = new WC_Product_Variation($item['variation_id']);
                    else :
                        $_product = new WC_Product($item['product_id']);
                    endif;
                }
                if (isset($_product) && $_product != false) {
                    //write_log($_product->attributes['pa_color']['options']);
                    $invoice_product_page_02 .= '<tr>
                        <td style="width:30px;" class="stt-text" align="left">' . ($loop + 1) . '</td>
                        <td style="width:400px" align="left">
                        <span class="description-text">' . $_product->get_title() . '</span>' . (isset($arr_variation[$loop]) ? '<br/>' . $arr_variation[$loop] : '')
                        . '</td>
                        <td style="width:80px" class="qty-text" align="center">' . ($item['quantity'] > 0 ? $item['quantity'] : 0) . '</td>
                        <td style="width:80px" class="unit_price-text" align="right">' . wc_price($item['line_total'] / $item['quantity']) . '</td>
                        <td style="width:80px" class="amount-text" align="right">' . wc_price($item['line_total']) . '</td>
                        </tr>';
                    /* if(isset($arr_variation[$loop])) {
                        $strlength+=strlen($arr_variation[$loop]);
                        $invoice_product_page_02 .= ($loop+1!=count($arr_variation) && $strlength>1882?'</table><div class="minh-phan-trang"></div>'.$head_table_pro:'');
                        if($strlength>1882) {
                        $strlength = 0;
                        }
                    } */
                }
                $loop++;
            }
            $gst = $subtotal * 7 / 100;
            $fee_ship = 0;
            foreach (WC()->cart->get_shipping_packages() as $package_id => $package) {
                // Check if a shipping for the current package exist
                if (WC()->session->__isset('shipping_for_package_' . $package_id)) {
                    // Loop through shipping rates for the current package
                    $ship_method = get_post_meta($quote_id, '_cxecrt_ship_method', true);
                    foreach (WC()->session->get('shipping_for_package_' . $package_id)['rates'] as $shipping_rate_id => $shipping_rate) {
                        if ($shipping_rate->get_id() == $ship_method) {
                            $fee_ship = $shipping_rate->get_cost();
                            $invoice_product_page_02 .= '<tr>
                        <td style="width:30px;" class="stt-text" align="left"></td>
                        <td style="width:400px" align="left">
                        <span class="description-text">Delivery</span><br/>' . $shipping_rate->get_label() . '
                        </td>
                        <td style="width:80px" class="qty-text" align="center">-</td>
                        <td style="width:80px" class="unit_price-text" align="right">-</td>
                        <td style="width:80px" class="amount-text" align="right">' . wc_price($shipping_rate->get_cost()) . '</td>
                        </tr>';
                            break;
                        }
                    }
                }
            }
            if ($fee_ship > 0) {
                $gst += ($fee_ship * 7/100 );
            }
        }
        $invoice_product_page_02 .= '</table>';
        $total_price = '<table id="total-price" style="width:100%">
            <tr>
            <td style="width:510px; padding-top:10px;" align="left">
            <span class="disclaimer">ITEMS NOT INCLUDED:</span>
            </td>
            <td style="width:80px;padding-top:10px;" class="subtotal" align="right">Subtotal</td>
            <td style="width:80px;padding-top:10px;" class="subtotal-price" align="right">' . wc_price($subtotal) . '</td>
            </tr>
            <tr>
            <td style="width:510px; padding-top:10px;" align="left">
            </td>
            <td style="width:80px;padding-top:10px;" class="subtotal" align="right">Shipping</td>
            <td style="width:80px;padding-top:10px;" class="subtotal-price" align="right">' . wc_price($fee_ship) . '</td>
            </tr>
            <tr>
            <td align="left" rowspan="2" style="width:510px;padding-top:-30px;" >
            <span class="text-number">1.</span><span class="text-val"> Design & artwork (Artwork Services must be included in your order if required)</span><br>
            </td>
            <td style="width:80px" class="gst" align="right">7% GST</td>
            <td style="width:80px" class="gst-price" align="right">' . wc_price($gst) . '</td>
            </tr>
            <tr>
            <td style="width:80px" class="total" align="right">Total</td>
            <td style="width:80px" class="total-price" align="right">' . wc_price($subtotal + $gst + $fee_ship) . '</td>
            </tr>
            </table>';
        $total_price .= '<table style="width:100%">
            <tr>
            <td align="left"><span class="disclaimer">VALIDATION:</span></td>
            </tr>';
        if (cxecrt_get_option('cxecrt_cart_expiration_active') && cxecrt_get_option('cxecrt_cart_expiration_time')) {
            $total_price .= '<tr>
                <td align="left"><span class="total-price">This price quote is valid for a period of ' . cxecrt_get_option('cxecrt_cart_expiration_time') . ' days from the issuing date of the quotation.</span></td>
                </tr>';
        }
        $total_price .= '<tr>
                <td align="left"><span class="total-price">* Items in this quotation are subject to stock availability</span></td>
                </tr>';
        $total_price .= '</table>';
        $html = $invoice_text_01 . $invoice_text_02 . $invoice_product_page_02 . $total_price;
    }
    return $html;
}

function cxecrt_get_variation_product_cart($quo_id)
{
    global $cxecrt;
    $temp = array();
    $cxecrt->backup_current_cart();
    $cxecrt->load_cart_from_post($quo_id);
    if (0 !== sizeof(WC()->cart->get_cart())) {
        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
            $str = '';
            $item_data = WC()->cart->get_item_data($cart_item, FALSE);
            if ($item_data) {
                $item_data = array_filter(explode("\n", $item_data));
                for ($i = 0; $i < count($item_data); $i++) {
                    if (strpos($item_data[$i], '</dd>') !== false) {
                        $str .= '<span class="sub-product">&emsp;' . $item_data[$i] . '</span>';
                    } else {
                        if (strpos($item_data[$i], '</dl>') !== false) {
                            array_push($temp, $str);
                        }
                    }
                }
            }
        }
    }
    $cxecrt->restore_current_cart();
    return $temp;
}

function generate_order_detail_pdf($order_id)
{
    global $wpdb;
    $html = '';
    $order = wc_get_order($order_id);
    if( $order->get_payment_method() == 'cod') {
        die();
    }
    if ($order) {
        $order_data = $order->get_data();
        //$user = $order->get_user();
        //$user_id = $order->get_user_id();
        $sig_img = get_post_meta($order_id, '_cxecrt_signature', true);
         $invoice_text_01 = '<table><tr><td style="width: 55%" align="left"> <table id="header-infor">
            <tr>
            <td style="padding-top:50px;" class="bill-to-th" align="left"><h2 class="bill-to">Billing details:</h2></td>
            </tr>
            </table>';
        $invoice_text_02 = '<table style="display: inline-block; float:left;">
            <tr>
            <td align="left"><span class="tex-bol" >' . $order_data['billing']['first_name'] . ' ' . $order_data['billing']['last_name'] . '</span></td>
            <td>'.($sig_img?'<span class="tex-bol" >Signature</span>':'').'</td>
            </tr>
            <tr>
            <td align="left">' . ($order_data['billing']['company'] != '' ? '<span class="tex-sub" >' . $order_data['billing']['company'] . '</span><br>' : '')
            . ($order->get_billing_address_1() != '' ? '<span class="tex-sub" >' . $order->get_billing_address_1() . '</span><br>' : '')
            . ($order->get_billing_email() != '' ? '<span class="tex-sub" >' . $order->get_billing_email() . '</span><br>' : '')
            . ($order->get_billing_phone() != '' ? '<span class="tex-sub" >' . $order->get_billing_phone() . '</span><br>' : '')
            . ($order->get_payment_method_title() != '' ? '<span class="tex-sub" >Payment via ' . $order->get_payment_method_title() . '</span>' : '')
            . '</td>
            <td align="right">'.($sig_img?'<img id="sig-image" src="'.$sig_img.'" style="width: 250px; border: 1px solid #ccc;"/>':'').'</td>
            </tr>
            </table>';
        $invoice_text_03 = '<table style="margin-top: 10px;">
            <tr>
            <td align="left">
                <span class="tex-bol" >Shipping Method : </span><br/>';
        $num = 1;
        foreach ($order->get_items('shipping') as $item_id => $shipping_item_obj) {
            $invoice_text_03 .= '<span class="tex-sub" >Shipping Method ' . $shipping_item_obj->get_instance_id() . ' - ' . $shipping_item_obj->get_method_title() . '</span>';
        }
        $invoice_text_03 .= '</td></tr><tr><td><span class="tex-bol" >Shipping Address :</span><br/><span class="tex-sub" >' . $order_data['billing']['first_name'] . ' ' . $order_data['billing']['last_name'] . '</span><br/><span class="tex-sub" >' . $order->get_shipping_address_1() . '</span><br/><span class="tex-sub" >' . $order->get_shipping_address_2(). '<br/><span class="tex-sub" >' . $order->get_shipping_postcode() .'</td></tr></table> </td>';
        $invoice_text_03 .= '<td class="order-data" style="width: 45%" align="right">
            <table>
                
                <tr>
                    <td align="left"><span class="tex-bol" style="font-family: Oduda-Bold; font-weight: 300">INVOICE NO.:</span></td>
                    <td align="left">'.get_post_meta( $order->get_id(), '_wcpdf_invoice_number', true).'</td>
                </tr>       
                <tr>
                    <td align="left"><span class="tex-bol" style="font-family: Oduda-Bold; font-weight: 300">Invoice Date:</span></td>
                    <td align="left">'.date_format( date_create(get_post_meta( $order->get_id(), '_wcpdf_invoice_date_formatted', true)) , "F  d  Y" ).'</td>
                </tr>
                <tr>
                    <td align="left"><span class="tex-bol" style="font-family: Oduda-Bold; font-weight: 300">Order Number:</span></td>
                    <td align="left">'.$order->get_order_number().'</td>
                </tr>
                <tr>
                    <td align="left"><span class="tex-bol" style="font-family: Oduda-Bold; font-weight: 300">Order Date:</span></td>
                    <td align="left">'.date_format($order->get_date_created() , "F  d  Y").'</td>
                </tr>
                <tr>
                    <td align="left"><span class="tex-bol" style="font-family: Oduda-Bold; font-weight: 300">Payment Method:</span></td>
                    <td align="left">'.$order->get_payment_method_title().'</td>
                </tr>
               
            </table>        
        </td></tr></table>';
        $invoice_product_page_02 = '<table class="product" style="width:100%">
            <tr>
            <td class="page-invoice" style="width:30px;padding-bottom:5px;padding-top: 100px" class="stt" align="left">No.</td>
            <td class="page-invoice" style="width:400px;padding-bottom:5px;padding-top: 100px" class="description" align="left">Description</td>
            <td class="page-invoice" style="width:80px;padding-bottom:5px;padding-top: 100px" class="qty" align="center">Qty</td>
            <td class="page-invoice" style="width:80px;padding-bottom:5px;padding-top: 100px" class="unit_price" align="right">Unit Price</td>
            <td class="page-invoice" style="width:80px;padding-bottom:5px;padding-top: 100px" class="amount" align="right">Amount</td>
            </tr>';
        $items = $order->get_items();
        $loop = $subtotal = 0;
        foreach ($items as $order_item_id => $item) {
            $subtotal += $item['line_total'];
            if (function_exists('get_product')) {
                if (isset($item['variation_id']) && $item['variation_id'] > 0) :
                    $_product = wc_get_product($item['variation_id']);
                else :
                    $_product = wc_get_product($item['product_id']);
                endif;
            } else {
                if (isset($item['variation_id']) && $item['variation_id'] > 0) :
                    $_product = new WC_Product_Variation($item['variation_id']);
                else :
                    $_product = new WC_Product($item['product_id']);
                endif;
            }
            if (isset($_product) && $_product != false) {
                $sub_infor = '';
                $ext_upload_preview = '';

                $img_bl = '';
                $formatted_meta_data = $item->get_formatted_meta_data('_', true);
                foreach ($formatted_meta_data as $k => $v) {
                    if($v->key == "Quantity Discount") {
                        continue;
                    }
                    $sub_infor .= '&emsp;' . $v->key . ' : ' . $v->value . '<br/>';
                    if (wc_get_order_item_meta($order_item_id, '_nbu')) {
                        if (strpos(strtolower($v->key), 'size') !== false) {
                            $arr_bl = infor_bleed_line($_product->get_id(), $v->value);
                            if (isset($arr_bl['bleed_top_bottom']) && isset($arr_bl['bleed_left_right'])) {
                                $nbu_item_key = wc_get_order_item_meta($order_item_id, '_nbu');
                                $upload_dir = wp_upload_dir();
                                $basedir = $upload_dir['basedir'];
                                $folder_preview = $basedir . '/nbdesigner/uploads/' . $nbu_item_key . '_preview';
                                $files = Nbdesigner_IO::get_list_files($folder_preview);
                                if (count($files) > 0) {
                                    foreach ($files as $key => $file) {
                                        $file_ext = pathinfo($file, PATHINFO_EXTENSION);
                                        $size_img = getimagesize($file);
                                        $dpi = 300;
                                        $unitRatio = 25.4 / $dpi;
                                        $bleed_top = $arr_bl['bleed_top_bottom'] * $unitRatio;
                                        $bleed_left = $arr_bl['bleed_left_right'] * $unitRatio;
                                        $img_new = '';
                                        write_log($file);
                                        if ($file_ext == 'png') {
                                            $img_new = imagecreatefrompng($file);
                                        } elseif ($file_ext == 'jpg') {
                                            $img_new = imagecreatefromjpeg($file);
                                        }
                                        if ($img_new != '') {
                                            $color = imagecolorallocate($img_new, 255, 0, 0);
                                            imagerectangle($img_new, $bleed_left, $bleed_top, ($size_img[0] - ($bleed_left * 2)), ($size_img[1] - ($bleed_top * 2)), $color);
                                        }
                                        if ($file_ext == 'png') {
                                            $img_bl = $folder_preview . "/image_upload_design_bl.png";
                                            imagepng($img_new, $img_bl);
                                        } elseif ($file_ext == 'jpg') {
                                            $img_bl = $folder_preview . "/image_upload_design_bl.jpg";
                                            imagejpeg($img_new, $folder_preview . "/image_upload_design_bl.jpg");
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                if(!$formatted_meta_data) {
                    $sub_infor .= '&emsp; SKU : ' . $_product->get_sku(). '<br/>';
                }
                if ($img_bl != '') {
                    if (file_exists($img_bl)) {
                        $sub_infor .= '<br/>&emsp;Upload Preview :<br/><br/><img src="' . $img_bl . '" /><br/>';
                    }
                }

                $invoice_product_page_02 .= '<tr>
                    <td style="width:30px;" class="stt-text" align="left">' . ($loop + 1) . '</td>
                    <td style="width:400px" align="left">
                    <span class="description-text">' . $_product->get_title() . '</span>' .
                    ($sub_infor != '' ? '<br><span class="sub-product">' . $sub_infor . '</span>' : '') .
                    '</td>
                    <td style="width:80px" class="qty-text" align="center">' . ($item['quantity'] > 0 ? $item['quantity'] : 0) . '</td>
                    <td style="width:80px" class="unit_price-text" align="right">' . wc_price($item['line_total'] / $item['quantity']) . '</td>
                    <td style="width:80px" class="amount-text" align="right">' . wc_price($item['line_total']) . '</td>
                    </tr>';
            }
            $loop++;
        }
        $gst = $subtotal * 7 / 100;
        if ($order_data['shipping_total'] > 0) {
            $gst += ($order_data['shipping_total'] * 7/100 );
        }
        $invoice_product_page_02 .= '</table>';
        $total_price = '<table id="total-price" style="width:100%">
            <tr>
            <td style="width:510px; padding-top:10px;" align="left">
            <span class="disclaimer">ITEMS NOT INCLUDED:</span>
            </td>
            <td style="width:80px;padding-top:10px;" class="subtotal" align="right">Subtotal</td>
            <td style="width:80px;padding-top:10px;" class="subtotal-price" align="right">' . wc_price($subtotal) . '</td>
            </tr>
            <tr>
            <td style="width:510px; padding-top:10px;" align="left">
            </td>
            <td style="width:80px;padding-top:10px;" class="subtotal" align="right">Shipping</td>
            <td style="width:80px;padding-top:10px;" class="subtotal-price" align="right">' . wc_price($order_data['shipping_total']) . '</td>
            </tr>
            <tr>
            <td align="left" rowspan="2" style="width:510px;padding-top:-30px;" >
            <span class="text-number">1.</span><span class="text-val"> Design & artwork (Artwork Services must be included in your order if required)</span><br>
            <span class="text-number">2.</span><span class="text-val"> Delivery fees (if any)</span>
            </td>
            <td style="width:80px" class="gst" align="right">7% GST</td>
            <td style="width:80px" class="gst-price" align="right">' . wc_price($gst) . '</td>
            </tr>
            <tr>
            <td style="width:80px" class="total" align="right">Total</td>
            <td style="width:80px" class="total-price" align="right">' . wc_price($subtotal + $order_data['shipping_total'] + $gst) . '</td>
            </tr>
            </table>';
        $html = $invoice_text_01 . $invoice_text_02 . $invoice_text_03 . $invoice_product_page_02 . $total_price;
    }
    return $html;
}

function infor_bleed_line($pro_id = 0, $v_size = '')
{
    $arr_result = [];
    if ($pro_id != 0 && class_exists('NBD_FRONTEND_PRINTING_OPTIONS')) {
        $option_id = NBD_FRONTEND_PRINTING_OPTIONS::get_product_option($pro_id);
        if ($option_id) {
            $_options = NBD_FRONTEND_PRINTING_OPTIONS::get_option($option_id);
            if ($_options) {
                $options = unserialize($_options['fields']);
                if (!isset($options['fields'])) {
                    $options['fields'] = array();
                }
                $options['fields'] = NBD_FRONTEND_PRINTING_OPTIONS::recursive_stripslashes($options['fields']);
                foreach ($options['fields'] as $key => $field) {
                    if ($field['nbd_type'] == 'size' && isset($field['general']['attributes'])) {
                        $arr_options = $field['general']['attributes']['options'];
                        if (count($arr_options) > 0) {
                            for ($i = 0; $i < count($arr_options); $i++) {
                                if (isset($arr_options[$i]['show_bleed'])) {
                                    if ($arr_options[$i]['show_bleed'] == 'on' && $arr_options[$i]['name'] == $v_size) {
                                        $arr_result = $arr_options[$i];
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
                // write_log($options);
            }
        }
    }
    return $arr_result;
}

function cxecrt_get_user_email($user_id)
{
    $email = '';
    $author_obj = get_user_by('id', $user_id);
    if ($author_obj) {
        $email = $author_obj->data->user_email;
    }
    return $email;
}

function cxecrt_get_author_quote($post_id)
{
    global $wpdb;
    $user_id = 0;
    $tem = $wpdb->get_row("SELECT post_author FROM {$wpdb->prefix}posts WHERE ID = {$post_id} AND post_type = 'stored-carts'");
    if ($tem) {
        $user_id = $tem->post_author;
    }
    return $user_id;
}

function check_enable_update_status_od($status)
{
    $check = false;
    $user = get_userdata(get_current_user_id());
    if ($user->roles[0] == 'administrator') {
        $check = true;
    } else {
        if ($user->roles[0] == 'specialist' && $status <= 4) {
            $check = true;
        }
        if ($user->roles[0] == 'production' && ($status > 4 && $status < 9)) {
            $check = true;
        }
        if ($user->roles[0] == 'customer_service' && $status >= 9) {
            $check = true;
        }
    }
    return $check;
}

function cxecrt_update_status_order_by_role($order_id, $status, $key2 = '')
{
    $key = cxecrt_get_key_by_role_user();
    if ($key2 != '') {
        $key = $key2;
    }
    update_post_meta($order_id, '_cxecrt_status_od', $status);
    update_post_meta($order_id, '_cxecrt_status_od' . $key, $status);
}

function cxecrt_get_key_by_role_user()
{
    $user = get_userdata(get_current_user_id());
    $key = '';
    if (isset($user->roles) && in_array('specialist', $user->roles)) {
        $key = '_s';
    }
    if (isset($user->roles) && in_array('production', $user->roles)) {
        $key = '_p';
    }
    if (isset($user->roles) && in_array('customer_service', $user->roles)) {
        $key = '_cs';
    }
    return $key;
}
function nb_get_from_address() {
    $from_address = get_option( 'woocommerce_email_from_address' );
    return sanitize_email( $from_address );
}
function nb_get_from_name() {
    $from_name = get_option( 'woocommerce_email_from_name' );
    return wp_specialchars_decode( esc_html( $from_name ), ENT_QUOTES );
}
function nb_get_content_type() {
    return 'text/html';
}
function send_botaksign_email($order_id, $title_email = '', $template_email = '', $upass = '', $user = null)
{
    global $order;
    $temp_content = '';
    $subject = "Welcome to Botaksign!";
    if ($order_id != 0) {
        $order = wc_get_order($order_id);
        $order_data = $order->get_data();
        $to = $order_data['billing']['email'];
        $method = $order->get_shipping_method();
        if ($template_email != '') {
            $subject = $title_email;
            $temp_content = $template_email;
        } else {
            if ($method == 'Self-collection') {
                $subject = "ORDER CONFIRMED";
                $temp_content = 'B1.php';
            } else {
                $subject = "ORDER CONFIRMED";
                $temp_content = 'A1.php';
            }
        }
    }
    write_log('check user:');
    write_log($user);
    if ($user != null) {
        $to = $user->user_email;
        $subject = $title_email;
        $temp_content = $template_email;
    }

    if ($temp_content != '') {
//          $to = 'thanhminh182@gmail.com';
        $content_type = 'text/html; charset=UTF-8';
        $from_name = get_option( 'woocommerce_email_from_name' );
        $from_address = get_option( 'woocommerce_email_from_address' );
        add_filter( 'wp_mail_from', 'nb_get_from_address' );
        add_filter( 'wp_mail_from_name', 'nb_get_from_name' );
        add_filter( 'wp_mail_content_type', 'nb_get_content_type' );

        $headers = 'Content-Type: ' . $content_type . "\r\n";
        $headers .= 'Reply-To: ' . $from_name . ' <' . $from_address . ">\r\n";
        ob_start();
        ?>
        <style type="text/css">
            @font-face {
                font-family: 'segoe-bold';
                src: url(<?php echo CUSTOM_BOTAKSIGN_URL . 'assets/fonts/segoe-ui-bold.ttf' ?>) format('truetype');
            }
        </style>
        <?php
        include("email-templates/email_header.php");
        include('email-templates/' . $temp_content);
        include("email-templates/email_footer.php");
        $message = ob_get_contents();
        ob_end_clean();
        wp_mail($to, $subject, $message, $headers);
    }
}

//Sort array time quantity break by quantity
if (!function_exists("sort_time_quantity_breaks")) {
    function sort_time_quantity_breaks($a, $b)
    {
        if ($a['qty'] == $b['qty']) {
            return 0;
        }
        return ($a['qty'] < $b['qty']) ? -1 : 1;
    }
}
function show_est_completion($order)
{
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
        if(!$have_pt) {
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
    if( $max_production_time == 0 ) {
        $max_production_time = unserialize(nbdesigner_get_option('nbdesigner_product_time_default'))[1][0];
    }
    $shipping_duration = maybe_unserialize(get_option('woocommerce_shipping_duration'));
    if (is_array($shipping_duration)) {
        foreach ($order->get_shipping_methods() as $shipping_method) {
            $shipping_method->get_instance_id();
            if (array_key_exists("wsd_" . $shipping_method->get_instance_id(), $shipping_duration)) {
                if ($max_shipping_time < $shipping_duration["wsd_" . $shipping_method->get_instance_id()]) {
                    $max_shipping_time = $shipping_duration["wsd_" . $shipping_method->get_instance_id()];
                }
            }
        };
    }

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

function check_suborder_24h($order_id)
{
    global $wpdb;
    $check = false;
    $tem = $wpdb->get_row("SELECT ID FROM {$wpdb->prefix}posts WHERE post_excerpt LIKE 'sub_order_%' AND post_date >= NOW() - INTERVAL 1 DAY AND ID = {$order_id}");
    if ($tem) {
        $check = true;
    }
    return $check;
}

/**
 * @param object $order_created_at Object datetime when order created
 * @param int $max_production_time Max production time (minutes) of order
 * @return timestamp $calc_production_date completed date
 */

function minute_working_on_day($day) {
    $working_time_setting = get_option('working-time-options', true);
    $hours_working = (float)date_diff(new DateTime($working_time_setting[$day]['open-time']), new DateTime($working_time_setting[$day]['close-time']))->format('%h');
    $minutes_working = (float)date_diff(new DateTime($working_time_setting[$day]['open-time']), new DateTime($working_time_setting[$day]['close-time']))->format('%i');
    return $minutes_working_day = $hours_working * 60 + $minutes_working;
}
function calc_time_ranger($time1 , $time2) {
    $hours_working = (float)date_diff(new DateTime($time1), new DateTime($time2))->format('%h');
    $minutes_working = (float)date_diff(new DateTime($time1), new DateTime($time2))->format('%i');
    return $time_minute = $hours_working * 60 + $minutes_working;
}
function split_time($time) {
    $_hourse = (float)date_create($time)->format('H');
    $_minute = (float)date_create($time)->format('i');
    return $minute_time = $_hourse*60 + $_minute;
}
function calc_production_date($order_created_at, $max_production_time)
{
    $h = "8";// time zone of Singapo is (+8)
    $hm = $h * 60;
    $ms = $hm * 60;
    $calc_production_date = date('H:i Y/m/d', $order_created_at->getTimestamp() + ($ms));
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
        // if($get_holiday['holidays'][0]['start-holiday'] != '' ) {
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
                if(isset($cacl_time_holiday[$count_holiday]) && strtotime($cacl_time_holiday[$count_holiday]['end-holiday']) + 86399 <= $_calc_production_date) {
                   $count_holiday++; 
                   $condition = true;
                }
            }
        }
    }
    return date('H:i Y/m/d' , $_calc_production_date );
}

/**
 * @param object $order
 * @param object $calc_shipping_date start date object to calc
 * @return timestamp $calc_production_date completed date
 */
function calc_completed_shipping_date($order)
{
    $shipping_time = 0;
    $max_shipping_time = 0; //Hours
    $shipping_duration = maybe_unserialize(get_option('woocommerce_shipping_duration'));
    if (is_array($shipping_duration)) {
        foreach ($order->get_shipping_methods() as $shipping_method) {
            $shipping_method->get_instance_id();
            if (array_key_exists("wsd_" . $shipping_method->get_instance_id(), $shipping_duration)) {
                if ($max_shipping_time < $shipping_duration["wsd_" . $shipping_method->get_instance_id()]) {
                    $max_shipping_time = $shipping_duration["wsd_" . $shipping_method->get_instance_id()];
                }
            }
        };
    }
    return $max_shipping_time;
}

function generateLinkEditOrder($order_id)
{
   global $wpdb;
    $link = wc_get_endpoint_url('orders', '', get_permalink(get_option('woocommerce_myaccount_page_id')));
    $link .= '?order_again=' . $order_id . '&edit_order=' .  $order_id;
    return $link;
}

function updateFolderDesignOrder($order_item_id, $type, $value)
{
    global $wpdb;
    $tem = $wpdb->get_row("SELECT meta_id FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE meta_key = '{$type}' AND order_item_id = {$order_item_id}");
    if ($tem) {
        $wpdb->update(
            $wpdb->prefix . 'woocommerce_order_itemmeta',
            array(
                'meta_value' => $value
            ),
            array('order_item_id' => $order_item_id, 'meta_key' => $type)
        );
    } else {
        $wpdb->replace(
            $wpdb->prefix . 'woocommerce_order_itemmeta',
            array(
                'order_item_id' => $order_item_id,
                'meta_key' => $type,
                'meta_value' => $value
            ),
            array(
                '%d',
                '%s',
                '%s'
            )
        );
    }
}

function countResendArtwork()
{
    global $wpdb;
    $num = 0;
    $tem = $wpdb->get_row("SELECT COUNT(meta_id) AS dem FROM {$wpdb->prefix}postmeta WHERE meta_key = '_resend_artwork' AND meta_value = 1");
    if ($tem) {
        $num = $tem->dem;
    }
    return $num;
}

?>
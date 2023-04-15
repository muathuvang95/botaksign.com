<?php
/**
 * Order details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.7.0
 */

defined( 'ABSPATH' ) || exit;

$order = wc_get_order( $order_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.OverrideProhibited
if (!$order) return;
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

// check customer can re-order
$order_created_at = $order->get_date_created();
$can_reorder = true;
foreach ($items as $item_id => $item) {
    $product = $item->get_product();
    $product_id = $product->get_id();
    if($product_id) {
        $option_id = NBD_FRONTEND_PRINTING_OPTIONS::get_product_option($product_id);
        $_options = NBD_FRONTEND_PRINTING_OPTIONS::get_option( $option_id );
        if(isset($_options['modified']) && $_options['modified'] ) {
            if( strtotime($_options['modified']) > strtotime($order_created_at) ) {
                $can_reorder = false;
            }
        }
    }
}


?>
<style type="text/css">
    .order-heading {
        padding: 20px 0;
    }
    .text-title {
        text-align: center;
        font-family: inherit;
        align-items: center;
        margin: 4px 0;
    }
    .text-title h3 {
        font-size: 18px;
        line-height: 21px;
        color: #231F20;
        font-family: inherit;
        text-transform: uppercase;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0;
    }
    .line-tile {
        display: block;
        height: 1px;
        width: 100%;
        background: #7ada45;
    }
    .items-meta {
        font-size: 12px;
        line-height: 1.7;
        font-family: inherit;
        padding: 12px;
        border-radius: 10px;
        background: transparent linear-gradient(177deg, #FFFFFF 0%, #F6F8F7 100%) 0% 0% no-repeat padding-box;
        border: 1px solid #EEECEC;
        backdrop-filter: blur(50px);
        -webkit-backdrop-filter: blur(50px);
    }
    .customer-details .billing-address-right, .order-info-right {
        text-align: right;
    }
    .customer-details .billing-address .key, .order-info .key {
        font-weight: 600;
        font-size: 14px;
        line-height: 2;
    }
    .customer-details .billing-address .value, .order-info .value {
        font-size: 14px;
        line-height: 2;
    }
    .customer-details .title {
        font-size: 15px;
        line-height: 1.5;
        font-weight: 600;
    }
    .order-details {
        margin: 10px 0;
    }
    .order-details .product-title {
        font-size: 14px;
        line-height: 2;
        font-weight: 600;
    }
    .order-details .items-meta .item-key {
        font-weight: 600;
    }
    .order-details .items-detail {
        font-size: 14px;
        line-height: 2;
    }
    .order-details .items-detail .item-value {
        font-weight: 600;
    }
    .order-summary .item-key,
    .order-summary .item-value {
        font-size: 15px;
        line-height: 2;
        font-weight: 600;
    }
    .order-actions {
        display: inline-block;
    }
    .order-actions .btk-btn-success {
        background: transparent linear-gradient(0deg, #1BCB3F 0%, #55D443 51%, #91DF48 100%) 0% 0% no-repeat padding-box;
        box-shadow: 0px 10px 20px #00000029;
        border-radius: 10px;
        border: none;
        color: #ffffff;
        font-weight: 600;
        text-transform: capitalize;
    }
    .btk-btn-pay {
        background: transparent linear-gradient(0deg, #919191 0%, #a5a5a5 51%, #c1c0c0 100%) 0% 0% no-repeat padding-box;;
        box-shadow: 0px 10px 20px #00000029;
        border-radius: 10px;
        border: none;
        color: #ffffff;
        font-weight: 600;
        padding-left: 30px;
        padding-right: 30px;
    }
    .item-printing-options {
        margin: 10px 0;
    }
    @media only screen and (max-width: 576px) {
        /*.line-tile {
            display: none;
        }*/
    }
</style>
<div class="printcart-custom-order">
    <div class="order-heading">
        <div class="row">
            <div class="col-sm-6">
                <div class="order-number fw-bold">
                    <?php echo 'Order: ' . $order_id . ' </span><span class="specialist">('.$specialist.')'; ?>
                </div>
                <div class="reorder-number text-danger">
                    <?php echo $order_again; ?>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="order-actions d-flex justify-content-end">
                    <?php
                    echo '<button class="me-3 btn btk-btn-success">Re-Order</button>';
                    // if($can_reorder) woocommerce_order_again_button($order);
                    nb_custom_pay_paynow($order_id);
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="customer-details">
        <div class="text-center text-title row">
            <div class="col-4 line-tile"></div>
            <h3 class="col-4">Customer details</h3>
            <div class="col-4 line-tile"></div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="order-info order-info-left">
                    <div class="title">
                        <?php echo $order->get_billing_first_name(). ' ' .$order->get_billing_last_name(); ?>
                    </div>
                    <div>
                        <span class="key">Email: </span>
                        <span class="value"><?php echo $order->get_billing_email(); ?></span>
                    </div>
                    <div>
                        <span class="key">Tel: </span>
                        <span class="value"><?php echo $order->get_billing_phone(); ?></span>
                    </div>
                </div>
                <div class="billing-address billing-address-left">
                    <div class="title">
                        Billing Address
                    </div>
                    <div><?php echo $order->get_billing_address_1(); ?></div>
                    <div><?php echo $order->get_billing_address_2(); ?></div>
                    <div><?php echo $order->get_billing_country() . ' ' . $order->get_billing_postcode(); ?></div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="order-info order-info-right">
                    <div>
                        <span class="key">Payment: </span>
                        <span class="value"><?php echo $order->get_payment_method_title(); ?></span>
                    </div>
                    <div>
                        <span class="key">Order Date: </span>
                        <span class="value"><?php echo date_format($order->get_date_created() , "d/m/Y"); ?></span>
                    </div>
                    <div>
                        <span class="key">Shipping Method: </span>
                        <span class="value"><?php echo $order->get_shipping_method(); ?></span>
                    </div>
                </div>
                <div class="billing-address billing-address-right">
                    <div class="title">Shipping Address</div>
                    <div><?php echo $order->get_shipping_address_1(); ?></div>
                    <div><?php echo $order->get_shipping_address_2(); ?></div>
                    <div><?php echo $order->get_shipping_country() . ' ' . $order->get_shipping_postcode(); ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="order-details">
        <div class="text-center text-title row">
            <div class="col-4 line-tile"></div>
            <h3 class="col-4">Order details</h3>
            <div class="col-4 line-tile"></div>
        </div>
        <div class="items-details">
            <?php
            $loop = 1;
            foreach ($items as $order_item_id => $item) {
                if (isset($item['variation_id']) && $item['variation_id'] > 0) :
                    $_product = wc_get_product($item['variation_id']);
                else :
                    $_product = wc_get_product($item['product_id']);
                endif;
                $file = '';
                $add_thumb_bottom = false;
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
                }
                ?>
                <div class="product-title"><?php echo $loop.'. '.$_product->get_title(); ?></div>
                <div class="item-printing-options">
                    <div class="row align-items-center">
                        <div class="col-md-3 col-sm-4">
                            <div class="design-image py-2">
                                <a href="<?php echo $file; ?>" class="thumbnail" target="_blank">
                                    <img class="border" style="width: 150px;height: 150px;" src="<?php echo $src; ?>">
                                </a>
                            </div>
                        </div>
                        <div class="col-md-9 col-sm-8">
                            <div class="items-meta">
                                <div class="row">
                                    <?php
                                    $formatted_meta_data = $item->get_formatted_meta_data('_', true);
                                    foreach ($formatted_meta_data as $k => $v) {
                                        if($v->key == "Quantity Discount" || $v->key == "Production Time" || $v->key == "SKU" || $v->key == "item_status") {
                                            continue;
                                        }
                                        echo '<div class="item-meta col-md-6"><span class="item-key">' . $v->key . ':</span> <span class="item-value">' . preg_replace( '/&nbsp;&nbsp;(.*)/' , '' , $v->value) . '</span></div>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="items-detail">
                    <?php
                    if(!$formatted_meta_data) {
                        echo '<div class="item-detail"><span class="item-key">SKU : </span><span class="item-value">' . $_product->get_sku(). '</span></div>';
                    } else {
                        echo '<div class="item-detail"><span class="item-key">SKU : </span><span class="item-value">' . wc_get_order_item_meta($order_item_id, 'SKU') . '</span></div>';
                    }
                    ?>
                    <div class="item-detail">
                        <span class="item-key">Quantity : </span>
                        <span class="item-value"><?php echo $item['quantity']; ?></span>
                    </div>
                    <div class="item-detail">
                        <span class="item-key">Price : </span>
                        <span class="item-value"><?php echo 'SGD $' . number_format($item['line_total'] , 2); ?></span>
                    </div>
                    <div class="item-detail">
                        <span class="item-key">Production Time : </span>
                        <span class="item-value"><?php echo wc_get_order_item_meta($order_item_id, 'Production Time'); ?></span>
                    </div>
                    <div class="item-detail">
                        <span class="item-key">Estimated Completion Time : </span>
                        <span class="item-value"><?php echo wc_get_order_item_meta($order_item_id, '_item_time_completed'); ?></span>
                    </div>
                </div>
                <div class="design-preview">
                    <div class="row">
                        <?php 
                        if($add_thumb_bottom) {
                            $thumbnail_item = '';
                            $count_file = count($nbu_files) + count($nbd_files);
                            $thumbnail_item_fake = '';
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
                                if($src) {
                                    echo '<div col-md-2 col-sm-3><a href="'.$file.'" class="thumbnail"  target="_blank"><img style="width:100px;height:100px" src="'.$src.'"></a></div>';
                                }
                            }
                            foreach ($nbd_files as $file) {
                                $src        = Nbdesigner_IO::wp_convert_path_to_url( $file );
                                if( $src ) {
                                    echo '<div col-md-2 col-sm-3><a href="'.$src.'" class="thumbnail"  target="_blank"><img style="width:100px;height:100px" src="'.$src.'"></a></div>';
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
                <?php
                $loop ++;
            }
            ?>
        </div>
    </div>
    <?php 
    $gst = 0;
    $taxs = array_slice($order->get_taxes(), 0, 1);
    if($taxs) {
        $gst = array_shift($taxs)->get_rate_percent( 'view' );
    }
    ?>
    <div class="order-summary">
        <div class="text-center text-title row">
            <div class="col-4 line-tile"></div>
            <h3 class="col-4">Summary</h3>
            <div class="col-4 line-tile"></div>
        </div>
        <hr />
        <div class="row">
            <div class="col-md-6"></div>
            <div class="col-md-6">
                <div class="summary-items">
                    <div class="summary-item d-flex justify-content-between">
                        <span class="item-key">Subtotal</span>
                        <span class="item-value"><?php echo wc_price($order->get_subtotal()); ?></span>
                    </div>
                    <div class="summary-item d-flex justify-content-between">
                        <span class="item-key">Shipping</span>
                        <span class="item-value"><?php echo $order->get_payment_method_title(); ?></span>
                    </div>
                    <div class="summary-item d-flex justify-content-between">
                        <span class="item-key">GST (<?php echo $gst; ?>%)</span>
                        <span class="item-value"><?php echo wc_price($order->get_total_tax()); ?></span>
                    </div>
                    <hr>
                    <div class="summary-item d-flex justify-content-between">
                        <span class="item-key">Total</span>
                        <span class="item-value"><?php echo wc_price($order->get_total()); ?></span>
                    </div>
                    <hr>
                </div>
            </div>
        </div>
    </div>
</div>
<?php 

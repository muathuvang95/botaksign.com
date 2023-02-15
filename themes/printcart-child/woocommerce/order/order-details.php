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
?>
<div>
    <div>
        <div class="row">
            <div class="col-md-6">
                <div class="order-number">
                    <?php echo 'Order: ' . $order_id . ' </span><span class="specialist">('.$specialist.')'; ?>
                </div>
                <div class="reorder-number">
                    <?php echo $order_again; ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="order-actions d-flex">
                    <div class="reorder-action">
                        <button class="btn btn-success">Re-order</button>
                        <button class="btn btn-light">Pay</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="customer-details">
        <div class="text-center">
            <h3>CUSTOMER DETAILS</h3>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="order-info">
                    <div>
                        <b><?php echo $order->get_billing_first_name(). ' ' .$order->get_billing_last_name(); ?></b>
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
                <div class="billing-address">
                    <div>
                        <b>Billing Address</b>
                    </div>
                    <div><?php echo $order->get_billing_address_1(); ?></div>
                    <div><?php echo $order->get_billing_address_2(); ?></div>
                    <div><?php echo $order->get_billing_country() . ' ' . $order->get_billing_postcode(); ?></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="order-info">
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
                <div class="billing-address">
                    <div>
                        <b>Shipping Address</b>
                    </div>
                    <div><?php echo $order->get_shipping_address_1(); ?></div>
                    <div><?php echo $order->get_shipping_address_2(); ?></div>
                    <div><?php echo $order->get_shipping_country() . ' ' . $order->get_shipping_postcode(); ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="order-details">
        <div class="text-center">
            <h3>CUSTOMER DETAILS</h3>
        </div>
        <div class="items-details">
            <?php
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
                <div class="item-printing-options">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="design-image">
                                <a href="<?php echo $file; ?>" class="thumbnail" target="_blank">
                                    <img style="width: 150px;height: 150px;" src="<?php echo $src; ?>">
                                </a>
                            </div>
                        </div>
                        <div class="col-md-9">
                            
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</div>

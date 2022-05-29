<?php
?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<style type="text/css">
    #nb-custom-reupload {
        margin-top: 20px;
        color: color: rgb(105,105,105);
        font-size: 14px;
    }
    #nb-custom-reupload .woocommerce-cart-form {
         margin-bottom: 10px;
    }
    #nb-custom-reupload .woocommerce-cart-form .nb-cart-left {
       width: 100%;
    }
    #nb-custom-reupload .woocommerce-cart-form .product-item {
        margin-bottom: 10px;
    }
    #nb-custom-reupload a:hover {
        text-decoration: none;
    }
    #nb-custom-reupload .nb-product-name dd p {
        display: inline-block;
        margin: 0;
    }
    #nb-custom-reupload .nb-product-link {
        border-bottom: 1px solid #ccc;
        font-size: 18px;
        line-height: 24px;
        font-weight: 600;
        margin-bottom: 10px;
        color: #696969;
        display: inline-block;
    }
    #nb-custom-reupload .wrap-product {
        padding: 10px;
    }
    .nb-over-load-reupload {
        position: fixed;
        top: calc( 50% - 120px);
        left: calc( 50% - 220px);
        width: 500px;
        height: 300px;
        background: rgba(177, 177, 177 , 0.7);
        border: 1px #848080 solid;
        border-radius: 0.25rem;
        display: none;
    }
    .nb-over-load-reupload i{
        border: none;
        color: 000;
        font-size: 50px;
        position: fixed;
        top: 50%;
        left: 50%;
    }
    .nb-over-load-reupload svg {
        position: fixed;
        top: 50%;
        left: 50%;
        color: #28c475;
        display: none;
    }
</style>
<?php
$order_id = isset($_GET['edit_order']) ? $_GET['edit_order'] : '';
$order = wc_get_order($order_id);
if($order) {
    $items = $order->get_items('line_item');
    $price_service = 0;
    if($items):
        ?>
            <div id="nb-custom-reupload" class="container">
                <div class="row">
                    <div class="woocommerce-cart-form">
                        <div class="row">
                            <?php
                            foreach ($items as $item_id => $item) {
                                if( wc_get_product($item->get_product_id())->is_type( 'service' ) ){
                                    $price_service += (float) $item->get_subtotal();
                                    continue;
                                }
                                ?>
                                <div class="cart-left-section nb-cart-left col">
                                    <div class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
                                        <div class="product-item">
                                            <div class="row wrap-product">
                                                <div class="product-thumbnail">
                                                    <a href="<?php echo esc_attr(get_permalink( $item->get_product_id() )); ?>"><img width="600" height="600" src="<?php echo esc_attr(get_the_post_thumbnail_url($item->get_product_id())); ?>" class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail" alt=""></a>        
                                                </div>
                                                <?php
                                                if ( $meta_data = $item->get_formatted_meta_data( '_', true) ) : ?>
                                                    <div class="nb-product-name row">
                                                        <div class="col-md-6">
                                                            <a href="<?php echo esc_attr(get_permalink( $item->get_product_id() )); ?>" class="nb-product-link"><?php echo $item->get_product()->get_name(); ?></a>
                                                            <?php 
                                                             foreach ( $meta_data as $meta_id => $meta ) :
                                                                if ( in_array( $meta->key, $hidden_order_itemmeta, true ) ) {
                                                                    continue;
                                                                }
                                                                ?>
                                                                <dd class="variation-<?php echo wp_kses_post( $meta->display_key ); ?>"><b><?php echo wp_kses_post( $meta->display_key ); ?></b> : <?php echo wp_kses_post( force_balance_tags( $meta->display_value ) ); ?></dd>
                                                            <?php endforeach;
                                                            ?>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <?php
                                                            if( wc_get_order_item_meta($item_id , '_item_status') == 'artwork_amendment' || wc_get_order_item_meta($item_id , '_item_status') == 'order_received') {
                                                                echo v3_button_reupload($order, $order_id, $item_id , $item);
                                                            } 
                                                            ?>
                                                        </div>
                                                    </div>
                                                <?php endif;
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                               <?php
                            }
                            ?>
                            
                        </div>
                    </div>
                </div>
                <?php 
                if($price_service > 0) {
                    $totals = $order->get_order_item_totals();
                    $link_to_parent = esc_url( $order->get_checkout_payment_url());
                    ?>
                    <?php if ( $totals ) : ?>
                        <table class="shop_table">
                            <tfoot>
                                <?php foreach ( $totals as $total ) : ?>
                                    <tr>
                                        <th scope="row" colspan="2"><?php echo $total['label']; ?></th>
                                        <td class="product-total"><?php echo $total['value']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tfoot>
                        </table>
                    <?php endif; ?>
                    <div class="save-reupload">
                        <button class="button button-completed nb-save-reupload" data-link-pay="<?php echo $link_to_parent; ?>" data-order="<?php echo $order_id; ?>">
                            Save and Pay for service
                        </button>
                    </div>
                    <?php
                } else {
                    ?>
                    <div class="save-reupload">
                        <button class="button button-completed nb-save-reupload" data-order="<?php echo $order_id; ?>">
                            Save Changes
                        </button>
                    </div>
                    <?php 
                } ?>
            </div>
            <div class="nb-over-load-reupload">
                <i class="fa fa-circle-o-notch fa-spin"></i>
                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-check-circle" viewBox="0 0 16 16">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                    <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/>
                </svg>
            </div>
        <?php
    endif;
}
?>
<script type="text/javascript">
    jQuery(document).ready(function($) {
        $('.nb-save-reupload').on('click' , function() {
            var order_id = $(this).data('order');
            var link_pay = $(this).data('link-pay');
            $.ajax({
                type : "post", 
                dataType : "json", 
                url : '<?php echo admin_url('admin-ajax.php');?>',
                data : {
                    action: "nb_save_reupload", //Tên action
                    order_id : order_id,//Biến truyền vào xử lý. $_POST['order_id']
                },
                context: this,
                beforeSend: function(){
                    $('.nb-over-load-reupload').css('display' , 'block');
                },
                success: function(response) {
                    //Làm gì đó khi dữ liệu đã được xử lý
                    if(response.success) {
                        $('.nb-over-load-reupload i').css('display' , 'none');
                        $('.nb-over-load-reupload svg').css('display' , 'block');
                        setTimeout(function(){
                            $('.nb-over-load-reupload').css('display' , 'none');
                            if(link_pay) {
                                window.location = link_pay;
                            }
                        }, 1000);
                    }
                    else {
                        alert('Error!');
                    }
                },
                error: function( jqXHR, textStatus, errorThrown ){
                    //Làm gì đó khi có lỗi xảy ra
                    console.log( 'The following error occured: ' + textStatus, errorThrown );
                }
            })
        })
    })
</script>
<?php
function v3_button_reupload($order, $order_id, $item_id, $item) {
    $product_id = $item->get_product_id();
    $layout = nbd_get_product_layout( $product_id );
    $nbd_item_key = wc_get_order_item_meta($item_id , '_nbd');
    $nbu_item_key = wc_get_order_item_meta($item_id , '_nbu');
    $is_nbdesign                    = get_post_meta($product_id, '_nbdesigner_enable', true);
    $_enable_upload                 = get_post_meta($product_id, '_nbdesigner_enable_upload', true);
    $_enable_upload_without_design  = get_post_meta($product_id, '_nbdesigner_enable_upload_without_design', true);
    $product_permalink = get_permalink( $product_id );
    if( $nbd_item_key ){ 
        $html          .= '<div class="nbd-custom-dsign nbd-cart-item-design">';
        $remove_design  = is_cart() ? '<a class="remove nbd-remove-design nbd-cart-item-remove-design" href="javascript:void(0)" onclick="NBDESIGNERPRODUCT.remove_design(\'custom\', \''.$cart_item_key.'\')">&times;</a>' : '';
        $html          .= '<p><b>'. esc_html__('Custom design', 'web-to-print-online-designer') .$remove_design.'</b></p>';
        $list           = Nbdesigner_IO::get_list_images(NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key . '/preview');
        asort($list);
        foreach ($list as $img) {
            $src = Nbdesigner_IO::convert_path_to_url($img) . '?&t=' . round(microtime(true) * 1000);
            $html .= '<img class="nbd_cart_item_design_preview" src="' . $src . '"/>';
        }
        $link_edit_design = add_query_arg(
            array(
                'task'          => 'edit',
                'product_id'    => $product_id,
                'nbd_item_key'  => $nbd_item_key,
                'view'          => $layout,
                'order_id'      => $order_id,
                'design_type'   => 'edit_order',
                'reupload'      => 'reupload',
                'item_id'       =>  $item_id,
                'rd'            => 'reupload'
            ),
            getUrlPageNBD('create'));
        if( $product_permalink ){
            $att_query = parse_url( $product_permalink, PHP_URL_QUERY );
            $link_edit_design .= '&'.$att_query;
        }    
        if( $layout == 'v' ){
            $link_edit_design = add_query_arg(
                array(
                    'nbdv-task'     => 'edit',
                    'task'          => 'edit',
                    'product_id'    => $product_id,
                    'nbd_item_key'  => $nbd_item_key,
                    'order_id'      => $order_id,
                    'design_type'   => 'edit_order',
                    'reupload'      => 'reupload',
                    'item_id'       =>  $item_id,
                    'rd'            => 'reupload'
                ),
                $product_permalink );
        }
        if($cart_item['variation_id'] > 0){
            $link_edit_design .= '&variation_id=' . $cart_item['variation_id'];
        }
        $html .= '<br /><a class="button nbd-edit-design" href="'.$link_edit_design.'">'. esc_html__('Edit design', 'web-to-print-online-designer') .'</a>';
        $html .= '</div>';
    }else if( $is_nbdesign && !$_enable_upload_without_design ){
        $link_create_design = add_query_arg(
            array(
                'task'          => 'new',
                'task2'         => 'update',
                'product_id'    => $product_id,
                'order_id'      => $order_id,
                'variation_id'  => $variation_id,
                'view'          => $layout,
                'reupload'      => 'reupload',
                'item_id'       =>  $item_id,
                'rd'            => 'reupload'
            ),
            getUrlPageNBD('create'));
        if( $layout == 'v' ){
            $link_create_design = add_query_arg(
                array(
                    'nbdv-task'     => 'new',
                    'task'          => 'new',
                    'task2'         => 'update',
                    'product_id'    => $product_id,
                    'order_id'      => $order_id,
                    'variation_id'  => $variation_id,
                    'view'          => $layout,
                    'item_id'       =>  $item_id,
                    'rd'            => 'reupload'
                ),
                $product_permalink );
        }
        if( $product_permalink ){
            $att_query = parse_url( $product_permalink, PHP_URL_QUERY );
            $link_create_design .= '&'.$att_query;
        }                    
        $html .= '<div class="nbd-cart-upload-file nbd-cart-item-add-design">';
        $html .=    '<a class="button nbd-create-design" href="'.$link_create_design.'">'. esc_html__('Add design', 'web-to-print-online-designer') .'</a>';
        $html .= '</div>';
    }
    if( $nbu_item_key ){
        $html          .= '<div id="'.$id.'" class="nbd-cart-upload-file nbd-cart-item-upload-file">';
        $files          = Nbdesigner_IO::get_list_files(NBDESIGNER_UPLOAD_DIR . '/' . $nbu_item_key);
        $create_preview = nbdesigner_get_option('nbdesigner_create_preview_image_file_upload');
        $upload_html    = '';
        foreach ($files as $file) {
            $ext        = pathinfo( $file, PATHINFO_EXTENSION );
            $src        = Nbdesigner_IO::get_thumb_file( pathinfo( $file, PATHINFO_EXTENSION ), '');
            $file_url   = Nbdesigner_IO::wp_convert_path_to_url( $file );
            if(  $create_preview == 'yes' && ( $ext == 'png' || $ext == 'jpg' || $ext == 'pdf' ) ){
                $dir        = pathinfo( $file, PATHINFO_DIRNAME );
                $filename   = pathinfo( $file, PATHINFO_BASENAME );
                if( file_exists($dir.'_preview/'.$filename) ){
                    $src = Nbdesigner_IO::wp_convert_path_to_url( $dir.'_preview/'.$filename );
                }else if( $ext == 'pdf' && file_exists($dir.'_preview/'.$filename.'.jpg' ) ){
                    $src = Nbdesigner_IO::wp_convert_path_to_url( $dir.'_preview/'.$filename.'.jpg' );
                }else{
                    $src = Nbdesigner_IO::get_thumb_file( $ext, '' );
                }
            }else {
                $src = Nbdesigner_IO::get_thumb_file( $ext, '' );
            }
            $upload_html .= '<div class="nbd-cart-item-upload-preview-wrap"><a target="_blank" href='.$file_url.'><img class="nbd-cart-item-upload-preview" src="' . $src . '"/></a><p class="nbd-cart-item-upload-preview-title">'. basename($file).'</p></div>';
        }
        $upload_html = apply_filters('nbu_cart_item_html', $upload_html, $cart_item, $nbu_item_key);
        $html .= $upload_html;
        $link_reup_design = add_query_arg(
            array(
                'task'          => 'reup',
                'product_id'    => $product_id,
                'order_id'      => $order_id,
                'nbu_item_key'  => $nbu_item_key,
                'reupload'      => 'reupload',
                'item_id'       =>  $item_id,
                'rd'            => 'reupload'
            ),
            getUrlPageNBD('create'));
        $html .= '<br /><a class="button nbd-reup-design" href="'.$link_reup_design.'">'. esc_html__('Reupload design', 'web-to-print-online-designer') .'</a>';
        $html .= '</div>';
    }else if( $_enable_upload ){
        $link_create_design = add_query_arg(
            array(
                'task'          => 'new',
                'task2'         => 'add_file',
                'product_id'    => $product_id,
                'order_id'      => $order_id,
                'variation_id'  => $variation_id,
                'reupload'      => 'reupload',
                'item_id'       =>  $item_id,
                'rd'            => 'reupload'
            ),
            getUrlPageNBD('create'));
        $html .= '<div class="nbd-cart-upload-file nbd-cart-item-upload-file">';
        $html .=    '<a class="button nbd-upload-design" href="' . $link_create_design . '">' . esc_html__('Upload design', 'web-to-print-online-designer') . '</a>';
        $html .= '</div>';
    }
    return $html;
}

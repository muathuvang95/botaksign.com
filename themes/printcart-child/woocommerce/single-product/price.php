<?php
/**
 * Single Product Price
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/price.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

//Append price after cart
add_action('woocommerce_after_add_to_cart_quantity', 'botak_price', 10);
function botak_price() {
    global $product;
    ?>
        <?php //CS botak show product design ?>
        <h4 id="nbdesigner-preview-title" style="display: none;"><b><?php esc_html_e('Custom design(s)', 'web-to-print-online-designer'); ?></b></h4>    
        <div id="nbd-actions" style="display: none;">
        <?php
            $layout = nbd_get_product_layout($product->get_id());
            if( $layout == 'c' ){
                if( nbdesigner_get_option( 'nbdesigner_save_for_later', 'no' ) == 'yes' ) include('classic-layout-save-for-later.php');
                if( nbdesigner_get_option( 'nbdesigner_share_design', 'no' ) == 'yes' ) include('classic-layout-share-design.php');
            }
        ?>
        </div>    
        <div id="nbdesigner_frontend_area"></div>
        <h4 id="nbdesigner-upload-title" style="display: none;"><b><?php esc_html_e('Upload file', 'web-to-print-online-designer'); ?></b></h4>
        <div id="nbdesigner_upload_preview"></div>
        <?php //End CS botak show product design ?>
        
        <div class="wrap-price-pro">
            <?php if ("taxable" === $product->get_tax_status()): ?>
                <p class="<?php echo esc_attr( apply_filters( 'woocommerce_product_price_class', 'price' ) );?>">
                    <span>Total w/o GST</span>
                    <?php 
                        $regular_price = $product->get_regular_price();
                        $sale_price = $product->get_sale_price();
                        if ($product->is_on_sale()) {
                            $price = '<del>' . ( is_numeric( $regular_price ) ? wc_price( $regular_price ) : $regular_price ) . '</del> <ins>' . ( is_numeric( $sale_price ) ? wc_price( $sale_price ) : $sale_price ) . '</ins>';
                        } else {
                            $price = '<ins>' . ( is_numeric( $regular_price ) ? wc_price( $regular_price ) : $regular_price ) . '</ins>';
                        }
                    ?>
                    <?php echo $price; ?>
                </p>
                <!--<p class="price-regular"><span>Total w/</span><?php // echo wc_price($product->get_regular_price()); ?></p>-->
                <p class="price-regular price-include-tax">
                    <span>Total w/ GST</span>
                    <?php echo wc_price(wc_get_price_including_tax($product)); ?>
                </p>
            <?php else: ?>
                <p class="price-regular">
                    <?php echo $product->get_price_html(); ?>
                </p>
            <?php endif; ?>
        </div>
    <?php
}
?>

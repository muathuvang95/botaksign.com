<?php
/**
 * Single Product Meta
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/meta.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @author      WooThemes
 * @package     WooCommerce/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $product;
$product_id = $product->get_id();
$items = nbd_get_items_product_grouped($product_id);
?>
<div class="product_meta">

    <?php do_action( 'woocommerce_product_meta_start' ); ?>
    
    <?php 
        $availability = $product->get_availability(); 
        if(is_array($items)) {
            $f_product = wc_get_product($items[0]['id']);
            if( isset($f_product) && $f_product ) {
                $availability = $f_product->get_availability();
            }
        }
    ?>
    <div class="wc-availability">
        <span class="stock <?php echo ( $product->is_in_stock() ? 'in-stock' : 'out-stock' ); ?>">
            <?php
            if ( $product->get_manage_stock() == 'yes' && !empty($availability['availability']) ) :
                echo esc_html( $availability['availability'] );
            elseif ( $product->is_purchasable() && $product->is_in_stock() ) :
                esc_html_e( 'In Stock', 'printcart' );
            else :
                esc_html_e( 'Out Of Stock', 'printcart' );
            endif;
            ?>
        </span>
    </div>

    <div class="sku_wrapper">
        <span class="meta-name"><?php esc_html_e( 'SKU:', 'printcart' ); ?></span>
        <span class="sku"><?php echo ( $sku = $product->get_sku() ) ? $sku : esc_html__( 'N/A', 'printcart' ); ?></span>
    </div>





    <?php do_action( 'woocommerce_product_meta_end' ); ?>

</div>

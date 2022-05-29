<?php
/**
 * Single Product stock.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/stock.php.
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

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
$product_id = $product->get_id();
$items = nbd_get_items_product_grouped($product_id);
if(is_array($items)) {
    $f_product = wc_get_product($items[0]['id']);
    $f_availability = $f_product->get_availability();
    $class = $f_availability['class'];
    $availability = $f_availability['availability'];
}
?>
<style type="text/css">
    .single-product-wrap .clearfix p.stock {
        display: block!important;
    }
    p.stock.in-stock {
        font-size: 1.2em;
        margin-bottom: 10px;
    }
    .botak-out-of-stock {
        color: #FF0000FF;

    }
    .botak-wrap-stock {
            padding: 10px 15px;
        background: #f7f3f3;
        box-shadow: 1px 1px #b4b4b4;
        border-radius: 5px;
        font-size: 18px;
        margin: 0 0 15px 0;
        font-weight: 600;
    }
</style>
<?php if($availability == 'Out of stock'){  ?>
    <div class="botak-wrap-stock">
        <span class="stock botak-out-of-stock <?php echo esc_attr( $class ); ?>"><?php echo wp_kses_post( $availability ).'!'; ?></span>
        <span class="stock-desc">(Prices will appear when stocks are available)</span>
    </div>
<?php 
} else {?>
    <p class="stock <?php echo esc_attr( $class ); ?>"><?php echo wp_kses_post( $availability ); ?></p>
<?php 
} ?>

<?php
/**
 * Add to wishlist button template
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 2.0.8
 */

if ( ! defined( 'YITH_WCWL' ) ) {
    exit;
} // Exit if accessed directly

global $product;
?>

<a href="<?php echo esc_url( add_query_arg( 'add_to_wishlist', $product_id ) )?>" rel="nofollow" data-product-id="<?php echo esc_attr($product_id); ?>" data-product-type="<?php echo esc_attr($product_type);?>" class="<?php echo esc_attr($link_classes); ?>" >
    <i class="icon-heart"></i>
    <span class="tooltip"><?php echo esc_html($label); ?></span>
</a>
<span class="ajax-loading" style="visibility:hidden">
    <i></i>
</span>
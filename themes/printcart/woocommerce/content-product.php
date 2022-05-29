<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

global $product;

// Ensure visibility
if ( empty( $product ) || ! $product->is_visible() ) {
    return;
}
$product_list = printcart_get_options('nbcore_product_list');
$product_category_compare = printcart_get_options('product_category_compare');
$product_category_wishlist = printcart_get_options('product_category_wishlist');
$product_category_quickview = printcart_get_options('product_category_quickview');
$start_design_button 		= 0;
if(class_exists('Nbdesigner_Plugin') && is_nbdesigner_product($product->get_id())){

	$catalog_button_pos = nbdesigner_get_option('nbdesigner_position_button_in_catalog');

	if($catalog_button_pos == 2) {
		$start_design_button = 1;
	}
}

$col = $product_category_compare + $product_category_wishlist + $product_category_quickview + $start_design_button + 1;
$product_meta_align = printcart_get_options('nbcore_product_meta_align');
$product_hover 		= printcart_get_options('nbcore_product_hover') . '-hover';

?>
<div <?php post_class(array('alt-button-' . $col, 'align-' . $product_meta_align, $product_hover )); ?> >
	<div class="pt-product-meta">
    <?php wc_get_template('netbase/content-product/' . esc_attr($product_list) . '.php'); ?>
	</div>
</div>

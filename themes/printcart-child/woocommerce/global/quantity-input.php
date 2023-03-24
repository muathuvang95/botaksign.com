<?php
/**
 * Product quantity inputs
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/global/quantity-input.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $product;
// $product_id = $product->get_id();
// $items = nbd_get_items_product_grouped($product_id);
// if($items) {
//     $f_product = wc_get_product($items[0]['id']);
//     $max_value = $f_product->get_stock_quantity();
//     $min_value = $f_product->get_height_stock_amount();
// }
if(empty($max_value)){ $max_value = 9999; }
// $input_style = printcart_get_options('nbcore_add_cart_style');
$input_style = 'style-2';
?>
	<div class="nb-quantity input-group">
		<?php if('style-1' == $input_style): ?>
			<input type="number" class="input-text qty text" step="<?php echo esc_attr( $step ); ?>" min="<?php echo esc_attr( $min_value ); ?>" max="<?php echo esc_attr( 0 < $max_value ? $max_value : '' ); ?>" name="<?php echo esc_attr( $input_name ); ?>" value="<?php echo esc_attr( $input_value ); ?>" title="<?php echo esc_attr_x( 'Qty', 'Product quantity input tooltip', 'printcart' ) ?>" size="4" pattern="<?php echo esc_attr( $pattern ); ?>" inputmode="<?php echo esc_attr( $inputmode ); ?>" />
			<div class="qty-buttons">
				<span class="wac-btn-inc quantity-plus-cart pt-icon-plus"></span>
				<span class="wac-btn-sub quantity-minus-cart pt-icon-minus"></span>
			</div>
		<?php else: ?>
			<div class="input-group-prepend">
				<span class="wac-btn-sub btn btn-outline-light quantity-minus-cart pt-icon-minus qty-button btk-btn-light"></span>
			</div>
			<input type="number" class="input-text qty text form-control" step="<?php echo esc_attr( $step ); ?>" min="<?php echo esc_attr( $min_value ); ?>" max="<?php echo esc_attr( 0 < $max_value ? $max_value : '' ); ?>" name="<?php echo esc_attr( $input_name ); ?>" value="<?php echo esc_attr( $input_value ); ?>" title="<?php echo esc_attr_x( 'Qty', 'Product quantity input tooltip', 'printcart' ) ?>" size="4" pattern="<?php echo esc_attr( $pattern ); ?>" inputmode="<?php echo esc_attr( $inputmode ); ?>" />
			<div class="input-group-append">
				<span class="wac-btn-inc btn btn-outline-light quantity-plus-cart pt-icon-plus qty-button btk-btn-light"></span>
			</div>
		<?php endif; ?>
	</div>

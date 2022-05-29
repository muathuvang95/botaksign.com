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

if(empty($max_value)){ $max_value = 9999; }
$input_style = printcart_get_options('nbcore_add_cart_style');
?>
	<?php
	if( method_exists($product, 'is_type') && $product->is_type('simple')){
		?>
		<div class="atc-notice-sinple">
			<h4><?php esc_html_e('Job Summary', 'printcart');?></h4>
			<p><?php esc_html_e('We will email you a link to a PDF proof within 6 hours.', 'printcart');?></p>
		</div>
		<?php
	}
	?>

	<div class="nb-quantity <?php echo esc_attr($input_style); ?>">
		<?php if('style-1' == $input_style): ?>
			<input type="number" class="input-text qty text" step="<?php echo esc_attr( $step ); ?>" min="<?php echo esc_attr( $min_value ); ?>" max="<?php echo esc_attr( 0 < $max_value ? $max_value : '' ); ?>" name="<?php echo esc_attr( $input_name ); ?>" value="<?php echo esc_attr( $input_value ); ?>" title="<?php echo esc_attr_x( 'Qty', 'Product quantity input tooltip', 'printcart' ) ?>" size="4" pattern="<?php echo esc_attr( $pattern ); ?>" inputmode="<?php echo esc_attr( $inputmode ); ?>" />
			<div class="qty-buttons">
				<span class="wac-btn-inc quantity-plus-cart pt-icon-plus"></span>
				<span class="wac-btn-sub quantity-minus-cart pt-icon-minus"></span>
			</div>
		<?php else: ?>
			<span class="wac-btn-sub quantity-minus pt-icon-minus"></span>
			<input type="number" class="input-text qty text" step="<?php echo esc_attr( $step ); ?>" min="<?php echo esc_attr( $min_value ); ?>" max="<?php echo esc_attr( 0 < $max_value ? $max_value : '' ); ?>" name="<?php echo esc_attr( $input_name ); ?>" value="<?php echo esc_attr( $input_value ); ?>" title="<?php echo esc_attr_x( 'Qty', 'Product quantity input tooltip', 'printcart' ) ?>" size="4" pattern="<?php echo esc_attr( $pattern ); ?>" inputmode="<?php echo esc_attr( $inputmode ); ?>" />
			<span class="wac-btn-inc quantity-plus-cart pt-icon-plus"></span>
		<?php endif; ?>
	</div>

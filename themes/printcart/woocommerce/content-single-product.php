<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
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

	/**
	 * woocommerce_before_single_product hook.
	 *
	 * @hooked wc_print_notices - 10
	 */
	do_action( 'woocommerce_before_single_product' );

	if ( post_password_required() ) {
		echo get_the_password_form();
		return;
	}
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class( '', $product ); ?>>
	<div class="single-product-wrap">
		<div class="product-image">
		<?php
			/**
			 * woocommerce_before_single_product_summary hook.
			 *
			 * @hooked woocommerce_show_product_sale_flash - 10
			 * @hooked woocommerce_show_product_images - 20
			 */
			do_action( 'woocommerce_before_single_product_summary' );
		?>
		</div>
		<?php 
		$nbdesigner_page_design_tool_class = '';
		$class_is_edit_mode = '';
		if(class_exists('Nbdesigner_Plugin') && is_nbdesigner_product($product->get_id())){

			$nbdesigner_page_design_tool = nbdesigner_get_option('nbdesigner_page_design_tool');
			//show design tool in new page
			if($nbdesigner_page_design_tool == 2) {
				$nbdesigner_page_design_tool_class = ' js_open_desginer_in_new_page';
			}

			if ( isset( $_REQUEST['nbo_cart_item_key'] ) && $_REQUEST['nbo_cart_item_key'] != '' ){
				$class_is_edit_mode = ' js_is_edit_mode';
			}
		}
		
		?>
		<div class="summary entry-summary<?php echo esc_attr($nbdesigner_page_design_tool_class);?><?php echo esc_attr($class_is_edit_mode);?>">
			<?php
				/**
				 * woocommerce_single_product_summary hook.
				 *
				 * @hooked woocommerce_template_single_title - 5
				 * @hooked woocommerce_template_single_rating - 10
				 * @hooked woocommerce_template_single_price - 10
				 * @hooked woocommerce_template_single_excerpt - 20
				 * @hooked woocommerce_template_single_add_to_cart - 30
				 * @hooked woocommerce_template_single_meta - 40
				 * @hooked woocommerce_template_single_sharing - 50
				 * @hooked WC_Structured_Data::generate_product_data() - 60
				 */
				do_action( 'woocommerce_single_product_summary' );
			?>
		</div><!-- .summary -->
	</div>

	<?php
	$enable_price_matrix = get_post_meta($product->get_id(), '_enable_price_matrix', true);
	$settings_modules = !empty(get_option('solutions_core_settings')) ? get_option('solutions_core_settings') : array();
    if(in_array('price-matrix', $settings_modules)) {
		$price_matrix_option = get_option('price-matrix_settings');
		$price_matrix_show_on = $price_matrix_option['wc_price-matrix_show_on'];
	}
	else {
		$price_matrix_show_on = 'default';
	}
	if( ! $product->is_type( 'simple' ) && ($price_matrix_show_on != 'before_tab' || empty($enable_price_matrix) ) ) {
		do_action('netbase_add_to_cart_hook');
	}
	
	/**
	 * woocommerce_after_single_product_summary hook.
	 *
	 * @hooked woocommerce_output_product_data_tabs - 10
	 * @hooked woocommerce_upsell_display - 15
	 * @hooked woocommerce_output_related_products - 20
	 */

	do_action( 'woocommerce_after_single_product_summary' );

	if('accordion-tabs' == printcart_get_options('nbcore_info_style')) {
		wc_get_template('netbase/single-product/tabs/accordion.php');
	} else {
		wc_get_template('netbase/single-product/tabs/default.php');
	}

	if(printcart_get_options('nbcore_show_upsells')) {
		woocommerce_upsell_display();
	}
	
	if(printcart_get_options('nbcore_show_related')) {
		woocommerce_output_related_products();
	}
	?>

</div><!-- #product-<?php the_ID(); ?> -->

<?php do_action( 'woocommerce_after_single_product' ); ?>

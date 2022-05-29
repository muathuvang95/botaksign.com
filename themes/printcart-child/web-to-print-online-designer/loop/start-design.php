<?php
if (!defined('ABSPATH')) {
	exit;
}

$product_id = $product->get_id();
$product_id = get_wpml_original_id($product_id);
$pa_image = get_field('product_additional_image', $product_id);
$option_id = NBD_FRONTEND_PRINTING_OPTIONS::get_product_option($product_id);
if ($option_id) {
	$_options = NBD_FRONTEND_PRINTING_OPTIONS::get_option($option_id);
	if ($_options) {
		$options = unserialize($_options['fields']);
	}
}
$url = add_query_arg(array(
	'product_id' => $product_id,
), getUrlPageNBD('create'));

echo sprintf('<a rel="nofollow" href="%s" data-quantity="%s" data-product_id="%s" data-product_sku="%s" class="%s %s" data-pai="%s"><span class="tooltip">%s</span></a>',
	$url,
	esc_attr(isset($quantity) ? $quantity : 1),
	esc_attr($product->get_id()),
	esc_attr($product->get_sku()),
	esc_attr(isset($class) ? $class : 'button'),
	nbdesigner_get_option('nbdesigner_class_design_button_catalog'),
	$pa_image,
	esc_html($label)
);

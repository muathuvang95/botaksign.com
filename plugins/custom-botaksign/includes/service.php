<?php
/**
 * Register the custom product type after init CS botak
 */
function register_service_product_type() {
	/**
	 * This should be in its own separate file.
	 */
	class WC_Product_Service extends WC_Product {
		public function __construct( $product ) {
			$this->product_type = 'service';
			parent::__construct( $product );
		}
                
                public function get_category_ids( $context = 'view' ) {
                        return [];
                }
                
                public function add_to_cart_url() {
                    $url = $this->is_purchasable() && $this->is_in_stock() ? remove_query_arg( 'added-to-cart', add_query_arg( 'add-to-cart', $this->id ) ) : get_permalink( $this->id );

                    return apply_filters( 'woocommerce_product_add_to_cart_url', $url, $this );
                }
	}
}
add_action( 'plugins_loaded', 'register_service_product_type' );


/**
 * Add to product type drop down.
 */
function add_service_product( $types ){
	// Key should be exactly the same as in the class
	$types[ 'service' ] = __( 'Service' );
	return $types;

}
add_filter( 'product_type_selector', 'add_service_product' );


/**
 * Show pricing fields for service product.
 */
function service_custom_js() {
	if ( 'product' != get_post_type() ) :
		return;
	endif;

	?><script type='text/javascript'>
            jQuery(document).ready(function () {
                //for Price tab
                jQuery('.product_data_tabs .general_tab').addClass('show_if_service').show();
                jQuery('#general_product_data .pricing').addClass('show_if_service').show();
                //for Inventory tab
                jQuery('.inventory_options').addClass('show_if_service').show();
                jQuery('#inventory_product_data ._manage_stock_field').addClass('show_if_service').show();
                jQuery('#inventory_product_data ._sold_individually_field').parent().addClass('show_if_service').show();
                jQuery('#inventory_product_data ._sold_individually_field').addClass('show_if_service').show();
            });
	</script><?php
}
add_action( 'admin_footer', 'service_custom_js' );

function service_add_to_cart () {
    global $product;

    // Make sure it's our custom product type
    if ( 'service' == $product->get_type() ) { ?>
        <form class="cart" action="<?php echo get_permalink($product->get_id());?>" method="post" enctype="multipart/form-data" ajax-url="<?php echo home_url('wp-admin/admin-ajax.php?action=nbo_ajax_cart'); ?>">
            <?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>
                <?php echo woocommerce_quantity_input( array(), $product, false ); ?>
                <button type="submit" name="add-to-cart" value="<?php echo $product->get_id(); ?>" class="single_add_to_cart_button button alt">
                    Add to cart
                </button>
            <?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
        </form>
    <?php }
}
add_action( 'woocommerce_single_product_summary', 'service_add_to_cart', 60 );

//Hide service product in shop and category
function hide_service_pre_get_posts( $query ) {
    if (is_admin()) return;
    $tax_query = array(
        'taxonomy'  => 'product_type',
        'field'     => 'slug',
        'terms'     => array('service'),
        'operator'  => 'NOT IN'
    );
    $query->tax_query->queries[] = $tax_query;
    $query->query_vars['tax_query'] = $query->tax_query->queries;
    $query->set('tax_query', $query->tax_query->queries);
}
add_action( 'pre_get_posts', 'hide_service_pre_get_posts', 1 );

//Add class for service in order
function add_service_class($class, $item, $order) {
    if ($item->get_meta('_parent_cart_item_key')) {
        $class .= " product-service";
    }
    return $class;
}
add_filter( 'woocommerce_order_item_class', 'add_service_class', 10, 3 );

// Add Terms and Condition Meta box to admin products pages
add_action('add_meta_boxes', 'create_product_terms_meta_box');
function create_product_terms_meta_box() {
    add_meta_box(
            'custom_product_terms_meta_box',
            __('Terms & Conditions'),
            'add_custom_content_terms_meta_box',
            'product',
            'normal',
            'default'
    );
}

// Terms and Condition metabox content in admin product pages
function add_custom_content_terms_meta_box($post) {
    $product = wc_get_product($post->ID);
    $content = $product->get_meta('terms_conditions_meta');

    echo '<div class="product_terms_conditions_meta">';
    wp_editor($content, 'terms_conditions_meta', ['textarea_rows' => 10]);
    echo '</div>';
}

// Save Terms and Condition WYSIWYG field value from product admin pages
add_action('woocommerce_admin_process_product_object', 'save_product_custom_terms_wysiwyg_field', 10, 1);
function save_product_custom_terms_wysiwyg_field($product) {
    if (isset($_POST['terms_conditions_meta'])) {
        $product->update_meta_data('terms_conditions_meta', wp_kses_post($_POST['terms_conditions_meta']));
    }
}
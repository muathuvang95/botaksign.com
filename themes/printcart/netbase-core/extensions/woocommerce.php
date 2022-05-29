<?php
/**
 * Extend and customize Woocommerce
 */
class NBT_Extensions_Woocommerce {

    public function __construct()
    {
        remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
        remove_action('woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10);
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
        remove_action('woocommerce_cart_collaterals', 'woocommerce_cross_sell_display');
        remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
        remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
        remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
        remove_action('woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
		remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);

        if(!printcart_get_options('nbcore_product_rating')) {
            remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
        }

        add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );
        add_filter( 'woocommerce_before_main_content', 'printcart_page_title', 5 );
        add_filter( 'loop_shop_columns', array($this, 'loop_columns') );
        add_filter( 'loop_shop_per_page', array($this, 'products_per_page'), 20 );
        add_filter( 'woocommerce_pagination_args', array($this, 'woocommerce_pagination') );
        add_filter('woocommerce_product_description_heading', '__return_empty_string');
        add_filter('woocommerce_product_additional_information_heading', '__return_empty_string');
        add_filter('woocommerce_review_gravatar_size', array($this, 'wc_review_avatar_size'));
        add_filter('woocommerce_cross_sells_total', array($this, 'cross_sells_limit'));
        add_filter('woocommerce_upsells_total', array($this, 'upsells_limit'));
        add_filter('yith_add_quick_view_button_html', array($this, 'quickview_button'), 10, 3);
        add_filter('yith_quick_view_loader_gif', '__return_empty_string');
        add_filter( 'option_yith_woocompare_button_text',  array($this, 'compare_button_text'), 99 );
        add_filter( 'woocommerce_breadcrumb_defaults', array($this, 'custom_woocommerce_breadcrumbs') );
        add_filter('woocommerce_add_to_cart_fragments', array($this, 'header_add_to_cart_fragment'));
        add_filter('woocommerce_add_to_cart_fragments', array($this, 'mini_cart_fragments'));

        add_action('woocommerce_after_shop_loop_item', array($this, 'product_action_div_open'), 6);
        add_action('woocommerce_after_shop_loop_item', array($this, 'product_action_div_close'), 50);

        if(printcart_get_options('nbcore_product_action_style') != 'vertical_fix_wl' && printcart_get_options('nbcore_product_action_style') != 'horizontal_fix_wl' ) {
            add_action('woocommerce_after_shop_loop_item', array($this, 'wishlist_button'), 20); 
        } else {
            add_action('woocommerce_after_shop_loop_item', array($this, 'wishlist_fixed_button'), 53);    
        }

        add_action('woocommerce_after_shop_loop_item', array($this, 'compare_button'), 20);
        add_action('woocommerce_shop_loop_item_title', array($this, 'product_title'), 10);
        add_action('woocommerce_before_main_content', array($this, 'shop_banner'), 15);

        if(printcart_get_options('nbcore_product_image_mask')) {
            add_action('woocommerce_after_shop_loop_item', array($this, 'product_img_mask_div_open'), 52);
            add_action('woocommerce_after_shop_loop_item', array($this, 'product_img_mask_div_close'), 52);
        }

        add_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 15);
        add_action('woocommerce_single_product_summary', array($this, 'wide_meta_left_div_open'), 9);
        add_action('woocommerce_single_product_summary', array($this, 'wide_meta_left_div_close'), 24);
        add_action('woocommerce_single_product_summary', array($this, 'wide_meta_right_div_open'), 26);
        add_action('woocommerce_single_product_summary', array($this, 'wide_meta_right_div_close'), 55);
        add_action('woocommerce_single_product_summary', array($this, 'woocommerce_template_single_sharing'), 50);
        add_action('woocommerce_cart_collaterals', 'woocommerce_cross_sell_display', 15);
        add_action('wp_footer', array($this, 'add_cart_notice'));    
        
        //TODO Fix this hack?
        add_filter('woocommerce_in_cart_product', array($this, 'remove_wishlist_quickview'), 50, 1 );

        add_action('woocommerce_after_shop_loop_item_title', array($this, 'wc_shop_loop_item_desc'), 50);

        add_action('netbase_add_to_cart_hook', 'woocommerce_template_single_add_to_cart', 10);

        add_action('woocommerce_before_shop_loop', array($this, 'woocommerce_term_title'), 20);
        add_action( 'woocommerce_product_query', array($this, 'filter_product_query') );
        add_action('woocommerce_before_shop_loop_item_title', array($this, 'template_loop_product_thumbnail'));
    }

    public function woocommerce_term_title() {
        global $wp_query;
        $cat = $wp_query->get_queried_object();
        if( is_shop() ){
            echo '<h1>' . esc_attr( $cat->label ) .'</h1>';
        }else{
            echo '<h1>' . esc_attr( $cat->name ) .'</h1>';
        }
    }

    public function wc_shop_loop_item_desc()
    {

        if(printcart_get_options('nbcore_grid_product_description')) {

            global $product;

            echo '<p class="product-excerpt">'.esc_html(get_the_excerpt($product->get_id())).'</p>';
        }
    }

    public function compare_button_text( $button_text )
    {
        return '<i class="fa fa-refresh"></i><span class="tooltip">'.esc_html($button_text).'</span>';
    }

    public function remove_wishlist_quickview($a)
    {
        add_filter('yith_add_quick_view_button_html',  '__return_empty_string', 50, 3);
    }

    public function loop_columns()
    {
        return printcart_get_options('nbcore_loop_columns');
    }

    public function product_action_div_open()
    {
        $product_action_style = ' ' . printcart_get_options('nbcore_product_action_style');
        echo '<div class="product-action' . esc_attr($product_action_style) . '">';
    }

    public function product_action_div_close()
    {        
        echo '</div>';
    }

    public function product_img_mask_div_open()
    {
        echo '<div class="product-image-mask">';
    }

    public function product_img_mask_div_close()
    {
        echo '</div>';
    }

    public function wishlist_button()
    {
        if(printcart_get_options('product_category_wishlist')) {
            if ( class_exists( 'YITH_WCWL' ) ) {
                echo '<div class="wishlist-btn button bt-4">' . do_shortcode( '[yith_wcwl_add_to_wishlist]' ) . '</div>';
            }
        }
    }

    public function wishlist_fixed_button()
    {
        if(printcart_get_options('product_category_wishlist')) {
            if ( class_exists( 'YITH_WCWL' ) ) {
                echo '<div class="wishlist-fixed-btn">' . do_shortcode( '[yith_wcwl_add_to_wishlist]' ) . '</div>';
            }
        }
    }

    public function compare_button()
    {

        if(printcart_get_options('product_category_compare')) {
            if ( class_exists( 'YITH_WOOCOMPARE' ) ) {
                echo do_shortcode( '[yith_compare_buttons]' );
            }
        }
    }

    public function product_title()
    {
        echo '<h4 class="product-title"><a href="' . esc_url(get_the_permalink()) . '">' . esc_html(get_the_title()) . '</a></h4>';
    }

    public function products_per_page($cols)
    {
        return printcart_get_options('nbcore_products_per_page');
    }

    public function woocommerce_pagination()
    {
        return array(
            'prev_text' => '<i class="icon-left-arrow"></i>',
            'next_text' => '<i class="icon-arrow-right"></i>',
            'end_size' => 1,
            'mid_size' => 1,
        );
    }

    public function product_description()
    {
        if ( has_excerpt() ){
          echo '<p class="product-description">' . strip_tags(get_the_excerpt()) . '</p>';
      }
  }

  public function product_category()
  {
    global $post;
    $terms = get_the_terms( $post->ID, 'product_cat' );
    foreach ($terms as $term) {
        echo '<a class="product-category-link" href="' . esc_url(get_term_link($term->term_id)) . '">' . esc_html($term->name) . '</a>';
    }
}

public function shop_banner()
{
    if(function_exists( 'is_shop' ) && is_shop()) {
        $shop_banner_url = printcart_get_options('nbcore_shop_banner');
        if ($shop_banner_url) {
            echo '<div class="shop-banner"><img src="' . esc_url(wp_get_attachment_url(absint($shop_banner_url))) . '" /></div>';
        }
    }
}

public function woocommerce_template_single_sharing()
{
    global $product;

    if ( is_plugin_active( 'web-to-print-online-designer/nbdesigner.php' ) && get_post_meta($product->get_id(), '_nbdesigner_enable', true) ):
        $nbdesigner_position_button_product_detail = nbdesigner_get_option('nbdesigner_position_button_product_detail');

        if($nbdesigner_position_button_product_detail == 4):
    ?>
        <div class="wc-single-online-desginer">
            <h4><?php esc_html_e('Online Design', 'printcart');?></h4>
            <h6><?php esc_html_e('Combine it with our layouts and fonts.', 'printcart');?></h6>
            <?php echo do_shortcode( '[nbdesigner_button]' );?>
        </div>
        <?php endif;?>
    <?php endif; ?>
<div class="clearfix">
    <?php if( $product->is_type( 'simple' ) ) {
        do_action('netbase_add_to_cart_hook');
    }?>
</div>

<?php
if( $product->is_type( 'simple' ) && function_exists('nbcore_single_share_social') ) {
    nbcore_single_share_social();
}
}

public function wc_review_avatar_size()
{
    return '80';
}

public function wide_meta_left_div_open()
{
    if('wide' === printcart_get_options('nbcore_pd_meta_layout')) {
        echo '<div class="pd-meta-left">';
    }
}

public function wide_meta_left_div_close()
{
    if('wide' === printcart_get_options('nbcore_pd_meta_layout')) {
        echo '</div>';
    }
}

public function wide_meta_right_div_open()
{
    if('wide' === printcart_get_options('nbcore_pd_meta_layout')) {
        echo '<div class="pd-meta-right">';
    }
}

public function wide_meta_right_div_close()
{
    if('wide' === printcart_get_options('nbcore_pd_meta_layout')) {
        echo '</div>';
    }
}

public function wc_share_social()
{
    if(printcart_get_options('nbcore_pd_show_social') && function_exists('nbcore_share_social')) {
    }
}

public function cross_sells_limit()
{
    $cross_sells_limit = printcart_get_options('nbcore_cross_sells_limit');
    return $cross_sells_limit;
}

public function upsells_limit()
{
    $upsells_limit = printcart_get_options('nbcore_upsells_limit');
    return $upsells_limit;
}

public function quickview_button($button, $label, $product)
{
    $html = '';
    if(printcart_get_options('product_category_quickview')) {
        global $product;

        $product_id = yit_get_prop( $product, 'id', true );

        $html = '<a href="#" class="button yith-wcqv-button bt-4" data-product_id="' . $product_id . '"><i class="icon-quick-view"></i><span class="tooltip">' . $label . '</span></a>';
    }
    return $html;
}

public function upload_scripts()
{
    wp_enqueue_script('media-upload');
    wp_enqueue_media();
}

public function add_cart_notice()
{
    $settings_modules = !empty(get_option('solutions_core_settings')) ? get_option('solutions_core_settings') : array();
    if(! in_array('ajax-cart', $settings_modules)) {
        $url = wc_get_cart_url();
        ?>
        <div class="cart-notice-wrap">
            <div class="cart-notice">
                <p><?php esc_html_e('Product has been added to cart', 'aidoo'); ?></p>
                <p class="cart-url button"><a href="<?php echo esc_url($url); ?>"><?php esc_html_e('View Cart', 'aidoo'); ?></a></p>
                <span><i class="icon-cancel-circle"></i></span>
            </div>
        </div>
        <?php
    }
}

public function custom_woocommerce_breadcrumbs() {
    return array(
        'delimiter'   => '<span>&#47;</span>',
        'wrap_before' => '<nav class="woocommerce-breadcrumb" itemprop="breadcrumb">',
        'wrap_after'  => '</nav>',
        'before'      => '',
        'after'       => '',
        'home'        => _x( 'Home', 'breadcrumb', 'printcart' ),
    );
}

public function filter_product_query($q) {
    $meta_query = $q->get( 'meta_query' );

    if( isset($_GET['price_from']) && isset($_GET['price_to']) ){
        $meta_query[] = array(
            'key' => '_price',
            'value' => array($_GET['price_from'], $_GET['price_to']),
            'compare' => 'BETWEEN',
            'type' => 'NUMERIC'
        );
        $q->set( 'meta_query', $meta_query );
    }
}

public function template_loop_product_thumbnail() {
    global $post, $woocommerce;

    the_post_thumbnail('printcart-masonry', ['alt' => esc_attr($post->post_title)]);
}

public function header_add_to_cart_fragment($fragments)
{
    global $woocommerce;
    ob_start();
    ?>
    <div class="cart-wrapper">
        <span class="counter-number"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
        <div class="show-cart">
            <span class="text"><span class="df-text"><?php echo esc_html__('Your Cart', 'printcart');?></span></span>
            <span class="price-wrapper"><span class="price"><?php echo wc_price(WC()->cart->total);?></span>
        </div>
    </div>
    <?php
    $fragments['div.cart-wrapper'] = ob_get_clean();

    return $fragments;
}

public function mini_cart_fragments($fragments)
{
    ob_start();?>
    <div class="mini-cart-wrap">
        <?php woocommerce_mini_cart(); ?>
    </div>
    <?php
    $fragments['.mini-cart-wrap'] = ob_get_clean();
    return $fragments;

}

}
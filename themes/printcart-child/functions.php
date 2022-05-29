<?php
/**
 * Theme functions and definitions.
 * This child theme was generated by Merlin WP.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 */

/*
 * If your child theme has more than one .css file (eg. ie.css, style.css, main.css) then
 * you will have to make sure to maintain all of the parent theme dependencies.
 *
 * Make sure you're using the correct handle for loading the parent theme's styles.
 * Failure to use the proper tag will result in a CSS file needlessly being loaded twice.
 * This will usually not affect the site appearance, but it's inefficient and extends your page's loading time.
 *
 * @link https://codex.wordpress.org/Child_Themes
 */
function printcart_child_enqueue_styles()
{
    if (is_rtl()) {
        wp_enqueue_style('printcart-style-rtl', get_template_directory_uri() . '/rtl.css');
    }
    wp_enqueue_style('printcart-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('printcart-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array('printcart-style'),
        wp_get_theme()->get('Version')
    );
    wp_enqueue_style('botak-style', get_stylesheet_directory_uri() . '/botak-style.css');
    wp_enqueue_script('fontawesome', 'https://kit.fontawesome.com/54ed714a8b.js', array(), 'latest', false);
    wp_enqueue_script('printcart-custom-js', get_stylesheet_directory_uri() . '/js/customize.js', array(), 'latest', false);
}

add_action('wp_enqueue_scripts', 'printcart_child_enqueue_styles');
add_filter('woocommerce_product_tabs', 'woo_remove_product_tabs', 99);

function woo_remove_product_tabs($tabs)
{

    unset($tabs['shipping']); // Remove the description tab
    unset($tabs['reviews']); // Remove the reviews tab
    unset($tabs['additional_information']); // Remove the additional information tab
    unset($tabs['seller']); // Remove vendor tab
    unset($tabs['more_seller_product']); // Remove more product tab

    return $tabs;

}

add_filter('woocommerce_product_tabs', 'woo_rename_tabs', 98);
function woo_rename_tabs($tabs)
{
    $tabs['description']['title'] = __('Info'); // Rename the description tab
    return $tabs;

}

// Adds widget: Printshop: Custom Categories
class Printshopcustomcate_Widget extends WP_Widget
{

    function __construct()
    {
        parent::__construct(
            'printshopcustomcate_widget', esc_html__('Printshop: Custom Categories', 'custom-botaksign')
        );
    }

    private $widget_fields = array(
        array(
            'label' => 'Hidden the category is empty',
            'id' => 'hiddenthecatego_select',
            'default' => 'Yes',
            'type' => 'select',
            'options' => array(
                'Yes',
                'No',
            ),
        ),
    );

    public function widget($args, $instance)
    {
        echo $args['before_widget'];
        $term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
        $termId = is_object($term) ? $term->term_id : 0;
        $ancestors = get_ancestors($termId, get_query_var('taxonomy'));
        // Output generated fields
        $top_level_terms = get_terms(array(
            'taxonomy' => get_query_var('taxonomy'),
            'parent' => is_object($term) ? $term->parent : '',
            'hide_empty' => ($instance['hiddenthecatego_select'] == 'Yes' ? true : false),
        ));
        $sort_arrow = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" width="10" height="24" viewBox="0 0 451.847 451.847" xml:space="preserve"><g><path d="M225.923,354.706c-8.098,0-16.195-3.092-22.369-9.263L9.27,151.157c-12.359-12.359-12.359-32.397,0-44.751   c12.354-12.354,32.388-12.354,44.748,0l171.905,171.915l171.906-171.909c12.359-12.354,32.391-12.354,44.744,0   c12.365,12.354,12.365,32.392,0,44.751L248.292,345.449C242.115,351.621,234.018,354.706,225.923,354.706z"/></g></svg>';
        if ($top_level_terms) {
            if (count($ancestors) > 0) {
                $level_mtop = get_term_by('id', absint($ancestors[0]), get_query_var('taxonomy'));
                echo '<h3 class="widget-title sidebar-desk">' . $level_mtop->name . '</h3>';
                echo is_object($term) ? '<h3 class="widget-title siderbar-mb">' . $term->name . '</h3>' : '<h3 class="widget-title siderbar-mb">' . $sort_arrow . '</h3>';

            }
            echo '<ul class="product_categories">';
            if (isset($level_mtop)) {
                echo '<li class="all-product"><a href="' . get_term_link($level_mtop->slug, get_query_var('taxonomy')) . '">All Product</a><span class="pc-count">' . $level_mtop->count . '</span></li>';
            }
            if (is_array($top_level_terms)) {
                foreach ($top_level_terms as $top_level_term) {
                    echo '<li class="' . ($top_level_term->term_id == $termId ? 'active' : '') . '"><a href="' . get_term_link($top_level_term->slug, get_query_var('taxonomy')) . '">' . $top_level_term->name . '</a><span class="pc-count">' . $top_level_term->count . '</span></li>';
                }
            }
            echo '</ul>';
        }
        echo $args['after_widget'];
    }

    public function field_generator($instance)
    {
        $output = '';
        foreach ($this->widget_fields as $widget_field) {
            $default = '';
            if (isset($widget_field['default'])) {
                $default = $widget_field['default'];
            }
            $widget_value = !empty($instance[$widget_field['id']]) ? $instance[$widget_field['id']] : esc_html__($default, 'custom-botaksign');
            switch ($widget_field['type']) {
                case 'select':
                    $output .= '<p>';
                    $output .= '<label for="' . esc_attr($this->get_field_id($widget_field['id'])) . '">' . esc_attr($widget_field['label'], 'textdomain') . ':</label> ';
                    $output .= '<select id="' . esc_attr($this->get_field_id($widget_field['id'])) . '" name="' . esc_attr($this->get_field_name($widget_field['id'])) . '">';
                    foreach ($widget_field['options'] as $option) {
                        if ($widget_value == $option) {
                            $output .= '<option value="' . $option . '" selected>' . $option . '</option>';
                        } else {
                            $output .= '<option value="' . $option . '">' . $option . '</option>';
                        }
                    }
                    $output .= '</select>';
                    $output .= '</p>';
                    break;
                default:
                    $output .= '<p>';
                    $output .= '<label for="' . esc_attr($this->get_field_id($widget_field['id'])) . '">' . esc_attr($widget_field['label'], 'custom-botaksign') . ':</label> ';
                    $output .= '<input class="widefat" id="' . esc_attr($this->get_field_id($widget_field['id'])) . '" name="' . esc_attr($this->get_field_name($widget_field['id'])) . '" type="' . $widget_field['type'] . '" value="' . esc_attr($widget_value) . '">';
                    $output .= '</p>';
            }
        }
        echo $output;
    }

    public function form($instance)
    {
        $this->field_generator($instance);
    }

    public function update($new_instance, $old_instance)
    {
        $instance = array();
        foreach ($this->widget_fields as $widget_field) {
            switch ($widget_field['type']) {
                default:
                    $instance[$widget_field['id']] = (!empty($new_instance[$widget_field['id']])) ? strip_tags($new_instance[$widget_field['id']]) : '';
            }
        }
        return $instance;
    }

}

function register_printshopcustomcate_widget()
{
    register_widget('Printshopcustomcate_Widget');
}

add_action('widgets_init', 'register_printshopcustomcate_widget');

add_action('woocommerce_after_shop_loop', 'botak_ajax_load_product', 20);
function botak_ajax_load_product()
{
    global $wp_query;
    $cat = $wp_query->get_queried_object();
    if (get_term_meta($cat->term_id, 'display_type', true) != 'subcategories') {
        $total = wc_get_loop_prop('total_pages');
        $current = wc_get_loop_prop('current_page');
        $current_url = remove_query_arg('add-to-cart', get_pagenum_link(1, false));
        $base_url = isset(explode('?', $current_url)[0]) ? explode('?', $current_url)[0] : '';
        $parameter = isset(explode('?', $current_url)[1]) ? explode('?', $current_url)[1] : '';
        ?>
        <div id="pagination-loadding" style="display: none;">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                 style="margin:auto;background:#fff;display:block;" width="100" height="100" viewBox="0 0 100 100"
                 preserveAspectRatio="xMidYMid">
                <g transform="rotate(0 50 50)">
                    <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#00a651">
                        <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s"
                                 begin="-0.9166666666666666s" repeatCount="indefinite"></animate>
                    </rect>
                </g>
                <g transform="rotate(30 50 50)">
                    <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#00a651">
                        <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s"
                                 begin="-0.8333333333333334s" repeatCount="indefinite"></animate>
                    </rect>
                </g>
                <g transform="rotate(60 50 50)">
                    <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#00a651">
                        <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.75s"
                                 repeatCount="indefinite"></animate>
                    </rect>
                </g>
                <g transform="rotate(90 50 50)">
                    <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#00a651">
                        <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s"
                                 begin="-0.6666666666666666s" repeatCount="indefinite"></animate>
                    </rect>
                </g>
                <g transform="rotate(120 50 50)">
                    <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#00a651">
                        <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s"
                                 begin="-0.5833333333333334s" repeatCount="indefinite"></animate>
                    </rect>
                </g>
                <g transform="rotate(150 50 50)">
                    <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#00a651">
                        <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.5s"
                                 repeatCount="indefinite"></animate>
                    </rect>
                </g>
                <g transform="rotate(180 50 50)">
                    <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#00a651">
                        <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s"
                                 begin="-0.4166666666666667s" repeatCount="indefinite"></animate>
                    </rect>
                </g>
                <g transform="rotate(210 50 50)">
                    <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#00a651">
                        <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s"
                                 begin="-0.3333333333333333s" repeatCount="indefinite"></animate>
                    </rect>
                </g>
                <g transform="rotate(240 50 50)">
                    <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#00a651">
                        <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.25s"
                                 repeatCount="indefinite"></animate>
                    </rect>
                </g>
                <g transform="rotate(270 50 50)">
                    <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#00a651">
                        <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s"
                                 begin="-0.16666666666666666s" repeatCount="indefinite"></animate>
                    </rect>
                </g>
                <g transform="rotate(300 50 50)">
                    <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#00a651">
                        <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s"
                                 begin="-0.08333333333333333s" repeatCount="indefinite"></animate>
                    </rect>
                </g>
                <g transform="rotate(330 50 50)">
                    <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#00a651">
                        <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="0s"
                                 repeatCount="indefinite"></animate>
                    </rect>
                </g>
            </svg>
        </div>
        <script>
            jQuery(document).ready(function ($) {
                var total_page = <?php echo (int)$total; ?>,
                    current_page = <?php echo (int)$current; ?>,
                    base_url = "<?php echo $base_url; ?>",
                    base_parameter = "<?php echo $parameter; ?>",
                    loading = false;
                $(window).scroll(function () {
                    if ($(window).scrollTop() + $(window).height() > $('.products.row').offset().top + $('.products.row').height() && !loading) {
                        if (current_page < total_page) {
                            loading = true;
                            $('#pagination-loadding').show();
                            var next_page = current_page + 1;
                            $.get(base_url + '/page/' + next_page + '?' + base_parameter, function (data) {
                                var products = $(data).find(".products.row");
                                if (products.length > 0) {
                                    $('.products.row').append(products[0].innerHTML);
                                    $(".products .product .product-action").each(function () {
                                        var swco = $(this).find('.nbo-archive-swatches-wrap');
                                        if (swco.length > 0) {
                                            $(this).parent('.product-image').after(swco.clone());
                                            swco.remove();
                                        }
                                    });
                                    current_page++;
                                }
                                loading = false;
                                $('#pagination-loadding').hide();
                            });
                        }
                    }
                })
            })
        </script>
        <?php
    }
}

//Add select2 for VC element
vc_add_shortcode_param('select2', 'my_param_settings_field');
function my_param_settings_field($settings, $value)
{
    if (!is_array($value)) {
        $value = explode(',', $value);
    }
    $return = '<div class="my_param_block">'
        . '<select name="' . esc_attr($settings['param_name']) . '" class="wc-product-search wpb_vc_param_value wpb-textinput ' .
        esc_attr($settings['param_name']) . ' ' .
        esc_attr($settings['type']) . '_field" multiple="multiple" style="width: 100%;" data-placeholder="Search for a product">';

    $return .= '</select>' .
        '</div>' .
        '<script>jQuery(".wc-product-search").select2({ajax:{url: "' . admin_url('admin-ajax.php') . '", type: "GET", dataType: "json", delay: 250, action: "botak_get_product_by_ids", data: function (params){return{term: params.term, action: "botak_get_product_by_ids",};}, processResults: function (response){return{results: response};}}}); </script>';

    foreach ($value as $v) {
        $return .= '<script>var newOption = new Option("' . get_the_title($v) . '", ' . $v . ', true, true); jQuery(".wc-product-search").append(newOption).trigger("change"); </script>';
    }

    return $return;
}

function botaksign_status_order($status)
{
    $result = '';
    if ($status == 4) {
        $status = 3;
    } elseif ($status == 6 || $status == 7) {
        $status = 5;
    }
    $arr_status = array('', 'Order Received', 'Processing', 'Artwork Amendment', 'Outsource', 'Printing', 'Finishing 1', 'Finishing 2', 'QC / Packing', 'Collection Point', 'Delivery', 'Completed');
    if (isset($arr_status[$status])) {
        $result = $arr_status[$status];
    }
    return $result;
}

function get_ids_product_grouped()
{
    global $wpdb;
    $id_child = array();
    $all_ids = $wpdb->get_col("SELECT ID FROM {$wpdb->prefix}posts WHERE post_type = 'product' AND post_status = 'publish'");
    foreach ($all_ids as $id) {
        if (nbd_get_items_product_grouped($id)) {
            $items = nbd_get_items_product_grouped($id);
            foreach ($items as $value) {
                $id_child[] = $value['id'];
            }
        }
    }
    return $id_child;
}

function hide_items_grouped_woo($q)
{
    $id_child = get_ids_product_grouped();
    $q->set('post__not_in', $id_child);
}

add_action('woocommerce_product_query', 'hide_items_grouped_woo');

add_action('pre_get_posts', function ($q) {
    if (!is_admin() && $q->is_main_query() && !$q->is_tax() && $q->is_front_page()) {
        $q->set('post_type', array('question'));
    }
});

function nb_display_item_meta( $item, $args = array() ) {
    $strings = array();
    $html    = '';
    $args    = wp_parse_args(
        $args,
        array(
            'before'       => '<ul class="wc-item-meta"><li>',
            'after'        => '</li></ul>',
            'separator'    => '</li><li>',
            'echo'         => false,
            'autop'        => false,
            'label_before' => '<strong class="wc-item-meta-label">',
            'label_after'  => ':</strong> ',
        )
    );
    foreach ( $item->get_formatted_meta_data() as $meta_id => $meta ) {
        if($meta->display_key == 'Quantity Discount') {
            continue;
        }
        $value     = $args['autop'] ? wp_kses_post( $meta->display_value ) : wp_kses_post( make_clickable( trim( $meta->display_value ) ) );
        $strings[] = $args['label_before'] . wp_kses_post( $meta->display_key ) . $args['label_after'] . $value;
    }

    if ( $strings ) {
        $html = $args['before'] . implode( $args['separator'], $strings ) . $args['after'];
    }

    $html = apply_filters( 'woocommerce_display_item_meta', $html, $item, $args );

    if ( $args['echo'] ) {
        echo $html; // WPCS: XSS ok.
    } else {
        return $html;
    }
}

add_filter( 'wpseo_breadcrumb_single_link' , 'wpseo_remove_breadcrumb_link' , 10 ,2);
function wpseo_remove_breadcrumb_link( $link_output , $link ){
$text_to_remove = 'Products';

if( $link['text'] == $text_to_remove ) {
$link_output = '';
}

return $link_output;
}

add_filter( 'wp_get_attachment_metadata' , 'nb_convert_size_image' , 10 , 2);
function nb_convert_size_image($data, $post_id) {
    if(!is_array($data)) {
        $data = unserialize($data);
    }
    return  $data;
}

function woocommerce_template_loop_category_title( $category ) {
    ?>
    <h4 class="product-title" style="font-size: 18px;">
        <?php
        echo esc_html( $category->name );

        if ( $category->count > 0 ) {
            echo apply_filters( 'woocommerce_subcategory_count_html', ' <mark class="count">(' . esc_html( $category->count ) . ')</mark>', $category ); // WPCS: XSS ok.
        }
        ?>
    </h4>
    <?php
}
function woocommerce_template_loop_category_link_open( $category ) {
    echo '<div class="pt-product-meta cs-botak-category"><div class="product-image"><a href="' . esc_url( get_term_link( $category, 'product_cat' ) ) . '">';
}
function woocommerce_template_loop_category_link_close() {
    echo '</a></div></div>';
}
function woocommerce_default_product_tabs( $tabs = array() ) {
    global $product, $post;
    $post_id = get_the_id();
    $post_content = get_post($post_id);
    $content = $post_content->post_content;
    // Description tab - shows product content.
    if ( $content ) {
        $tabs['description'] = array(
            'title'    => __( 'Description', 'woocommerce' ),
            'priority' => 10,
            'callback' => 'woocommerce_product_description_tab',
        );
    }

    // Additional information tab - shows attributes.
    if ( $product && ( $product->has_attributes() || apply_filters( 'wc_product_enable_dimensions_display', $product->has_weight() || $product->has_dimensions() ) ) ) {
        $tabs['additional_information'] = array(
            'title'    => __( 'Additional information', 'woocommerce' ),
            'priority' => 20,
            'callback' => 'woocommerce_product_additional_information_tab',
        );
    }

    // Reviews tab - shows comments.
    if ( comments_open() ) {
        $tabs['reviews'] = array(
            /* translators: %s: reviews count */
            'title'    => sprintf( __( 'Reviews (%d)', 'woocommerce' ), $product->get_review_count() ),
            'priority' => 30,
            'callback' => 'comments_template',
        );
    }

    return $tabs;
}

// Update time completed
function nb_get_time_completed_item($max_production_time , $order){
    if ($order->get_date_created()) {
        $calc_production_date = calc_production_date($order->get_date_created(), $max_production_time * 60);
        $time_shipping = calc_completed_shipping_date($order)*3600;
        $time_delivered = $time_shipping*3600  + strtotime($calc_production_date);
        $calc_shipping_date = date( "H:i yy/m/d" , $time_delivered );
        $production_datetime_completed = date("d/m/Y H:i a", strtotime($calc_production_date));
        $production_date_completed = date("l, d F Y", strtotime($calc_production_date));
        $shipping_date_completed = date("l, d F Y", strtotime($calc_shipping_date));
        $shipping_datetime_completed = date("l, d F Y, h:i A", strtotime($calc_shipping_date));
        if ($max_shipping_time == 0) {
            return [
                'total_time' => $production_datetime_completed,
                'production_datetime_completed' => $production_datetime_completed,
                'production_date_completed' => $production_date_completed,
                'shipping_date_completed' => $shipping_date_completed,
                'shipping_datetime_completed' => $shipping_datetime_completed,
            ];
        } else {
            return [
                'total_time' => $production_date_completed . ' - ' . $shipping_date_completed,
                'production_datetime_completed' => $production_datetime_completed,
                'production_date_completed' => $production_date_completed,
                'shipping_date_completed' => $shipping_date_completed,
                'shipping_datetime_completed' => $shipping_datetime_completed,
            ];
        }
    } else {
        return [
            'total_time' => date("l, d F Y, H:i", strtotime('00:00')),
            'production_datetime_completed' => date("d/m/Y H:i a", strtotime('00:00')),
            'production_date_completed' => date("l, d F Y, h:i A", strtotime('00:00')),
            'shipping_date_completed' => date("l, d F Y, h:i A", strtotime('00:00')),
            'shipping_datetime_completed' => date("l, d F Y, h:i A", strtotime('00:00')),
        ];
    }
}
function nb_get_production_time_item($item , $order) {
    $max_production_time = 0;
    $have_pt = false;
    $user_id = $order->get_user_id();
    $user_meta =get_userdata($user_id);
    $role_use = '';
    if(isset($user_meta)) {
        $role_use = $user_meta->roles[0];
    }
    $have_role_use = false;
    $have_check_default = false;
    if ($item->get_meta('_nbo_options') && $item->get_meta('_nbo_field')) {
        $qty = $item->get_quantity();
        $options = $item->get_meta('_nbo_options');
        $origin_fields = unserialize($options['fields']);
        $origin_fields = $origin_fields['fields'];
        $item_field = $item->get_meta('_nbo_field');
        $value = 0;
        foreach ($origin_fields as $field) {
            if (isset($field['nbd_type']) && $field['nbd_type'] === 'production_time') {
                $have_pt = true;
                foreach ($item_field as $k => $v) {
                    if($k == $field['id']) {
                        $value = $v['value'];
                    } 
                }
                if(isset($field['general']['role_options'])) {
                    foreach ($field['general']['role_options'] as $role_options) {
                        if($role_options['role_name'] ==  $role_use) {
                            $time_quantity_breaks_1 = $role_options['options'][$value]['time_quantity_breaks'];
                            $have_role_use = true;
                        }
                        if(isset($role_options['check_default']) && ( $role_options['check_default'] == 'on' || $role_options['check_default'] == '1' )) {
                            $have_check_default = true;
                            $time_quantity_breaks_2 = $role_options['options'][$value]['time_quantity_breaks'];
                        }  
                    }
                }
                if($have_role_use) {
                    $time_quantity_breaks = $time_quantity_breaks_1;
                }
                if(!$have_role_use && $have_check_default ) {
                    $time_quantity_breaks = $time_quantity_breaks_2;
                }
                if(empty($time_quantity_breaks)) {
                    $have_pt = false;
                    break;
                }
                if(count($time_quantity_breaks) <= 1) {
                    $max_production_time = (float)$time_quantity_breaks[0]['time'];
                } else {
                    for ($i = 0; $i < count($time_quantity_breaks); $i++) {
                        if ($qty >= $time_quantity_breaks[$i]['qty'] && $qty < $time_quantity_breaks[$i + 1]['qty'] ) {
                            $max_production_time = (float)$time_quantity_breaks[$i]['time'];
                        }
                    }
                }
            }
        }
    } 
    if(!$have_pt || $max_production_time == 0) {
        $qty = $item->get_quantity();
        $_productiton_time_default = array();
        $productiton_time_default = unserialize(nbdesigner_get_option('nbdesigner_product_time_default'));
        // Convert array
        for( $f =0; $f < count($productiton_time_default[0]); $f++ ) {
            $_productiton_time_default[$f]['qty'] = $productiton_time_default[0][$f] ;
            $_productiton_time_default[$f]['time'] = $productiton_time_default[1][$f] ;
        }
        if(count($_productiton_time_default) <= 1) {
            $max_production_time = (float)$_productiton_time_default[0]['time'];
        } else {
            for ($i = 0; $i < count($_productiton_time_default); $i++) {
                if ($qty >= $_productiton_time_default[$i]['qty'] && $qty < $_productiton_time_default[$i + 1]['qty'] ) {
                    $max_production_time = (float)$_productiton_time_default[$i]['time'];
                }
            }
        }
    }
    return $max_production_time;
}
add_action( 'wp_ajax_update_database', 'update_database' );
add_action( 'wp_ajax_nopriv_update_database', 'update_database' );
function update_database() {
    global $wpdb;
    $sql = "SELECT ID FROM wp_posts WHERE wp_posts.post_type = 'shop_order'";
    $options = $wpdb->get_results($sql, 'ARRAY_A');
    $time1 = array();
    foreach ($options as $key => $value) {
        $order_id = $value['ID'];
        $order = wc_get_order($order_id);
        $items = $order->get_items('line_item');
        $timeline_str_f = strtotime('00:00 24-01-2021');
        $timeline_str_t = strtotime('23:59 31-01-2021');
        $date_create_str = $order->get_date_created()->getTimestamp() + 8*3600;

        if($order_id) {
            // if( $date_create_str < $timeline_str_t && $date_create_str > $timeline_str_f ) {
                // wp_update_post(array(
                //     'ID'    =>  $order_id,
                //     'post_status'   =>  'wc-completed'
                // ));
                $time1[] = $order_id;
            // } 
        }
    }
    wp_send_json_success($result);
    die();
}
add_action('wp_ajax_nb_save_reupload', 'nb_save_reupload');
add_action('wp_ajax_nopriv_nb_save_reupload', 'nb_save_reupload');
function nb_save_reupload() {
    $order_id = $_POST['order_id'];
    $result['order_id'] = $order_id;
    if($order_id) {
        $order = wc_get_order($order_id);
        $items = $order->get_items('line_item');
        $index = 1;
        foreach ($items as $item_id => $item) {
            if( wc_get_order_item_meta($item_id , '_product_type' ) == 'service' ){
                continue;
            }
            $item->get_product();
            $nbd_check = wc_get_order_item_meta($item_id , '_nbd_check');
            $nbu_check = wc_get_order_item_meta($item_id , '_nbu_check');
            $nbd_item_key = wc_get_order_item_meta($item_id, '_nbd');
            $nbu_item_key = wc_get_order_item_meta($item_id, '_nbu');
            if( $nbd_check && $nbd_item_key ){
                //$list_images = Nbdesigner_IO::get_list_images(NBDESIGNER_CUSTOMER_DIR .'/'. $nbd_item_key, 1);
                nbd_export_pdfs( $nbd_item_key, false, false, 'no' );
                $pdf_path   = NBDESIGNER_CUSTOMER_DIR .'/'. $nbd_item_key . '/customer-pdfs';
                $list_pdf   = Nbdesigner_IO::get_list_files_by_type($pdf_path, 1, 'pdf');
                if(count($list_pdf) > 0){
                    foreach($list_pdf as $key => $file){
                        $zip_files[] = $file;
                    }
                }
                nbd_export_pdfs( $nbd_item_key, false, false, 'no' );
                $pdf_path   = NBDESIGNER_CUSTOMER_DIR .'/'. $nbd_item_key . '/customer-pdfs';
                $list_pdf   = Nbdesigner_IO::get_list_files_by_type($pdf_path, 1, 'pdf');
                if(count($list_pdf) > 0){
                    foreach($list_pdf as $key => $file){
                        $zip_files[] = $file;
                    }
                }
            }
            if( $nbu_check  && $nbu_item_key ){
                $files = Nbdesigner_IO::get_list_files( NBDESIGNER_UPLOAD_DIR .'/'. $nbu_item_key );
                $files = apply_filters( 'nbu_download_upload_files', $files, $product );
                if(count($files) > 0){
                    foreach($files as $key => $file){
                        $zip_files[] = $file;
                    }
                }
            }
            if( !count($zip_files) ){
        
            }else{
                $pathZip = NBDESIGNER_DATA_DIR.'/download/customer-design-'.$item_id.'.zip';
                $nameZip = 'customer-design-'.$item_id.'.zip';
                $link_aws = nbd_zip_files_and_download( $zip_files, $pathZip, $nameZip, $option_name = array(), $download = false, $upload_aws = true );
                wc_update_order_item_meta($item_id , '_nbd_item_edit' , $link_aws);
            }
        }
    }
    $result['success'] = true;
    echo json_encode($result);
    wp_die();
}

// custom cron Job sent mail fail
add_action('custom_send_email_pending_order' , 'cs_create_cron_job' );
function cs_create_cron_job() {
    $order_ids = get_all_pending_order();
    foreach ($order_ids  as $order_id) {
        $str_date_create = strtotime(get_pending_order($order_id->order_id)->created);
        $time_now = strtotime('now');
        $period_time = $time_now - $str_date_create;
        if($period_time >= 1800) {
            $order          = wc_get_order($order_id->order_id);
            if($order) {
                $status         = $order->get_status();            
                if( $status == 'on-hold' || $status == 'pending') {
                    delete_pending_order($order_id->order_id) ; 
                    send_botaksign_email($order_id->order_id, 'PAYMENT FAIL', 'I1.php');
                } else {
                    delete_pending_order($order_id->order_id) ; 
                }
            } else {
                delete_pending_order($order_id->order_id) ; 
            }
            
        }
    }

}
add_filter('cron_schedules','botak_cron_schedules');
function botak_cron_schedules($schedules){
    if(!isset($schedules["1min"])){
        $schedules["1min"] = array(
            'interval' => 60,
            'display' => __('Once every 1 minutes'));
    }
    return $schedules;
}
wp_clear_scheduled_hook( 'action_scheduler_run_queue' );

if ( !wp_next_scheduled('custom_send_email_pending_order') ) { 
    wp_schedule_event(time(), '1min', 'custom_send_email_pending_order');
}

add_filter('woocommerce_cancel_unpaid_order','botak_cancel_unpaid_order' , 10 , 2);
function botak_cancel_unpaid_order( $check , $order) {
    $order_id = $order->get_id();
    if( isset($order_id ) ) {
        $price_old = get_post_meta($order_id , 'price_old' , true);
        $paid = get_post_meta($order_id , '_payment_status' , true);
        if( ( isset($price_old) && $price_old) || $paid == 'paid' ) {
            $check = false;
        }
    }   
    return $check;
}

add_filter('woocommerce_payment_complete_order_status','botak_payment_complete_order_status' , 10 , 3);
function botak_payment_complete_order_status( $var , $order_id , $instance ) {
    if(isset( $order_id ) && $order_id ) {
        $order = wc_get_order($order_id);
        if(isset($order)) {
            $payment_method = $order->get_payment_method();
            if( $payment_method == 'omise_paynow' ) {
                if( $var == 'processing' || $var == 'completed' ) {
                    $var = 'processing';
                }
            }
        }
    }
    return $var;
}


function botak_get_unpaid_orders( $date ) {
    global $wpdb;

    $unpaid_orders = $wpdb->get_col(
        $wpdb->prepare(
            // @codingStandardsIgnoreStart
            "SELECT posts.ID
            FROM {$wpdb->posts} AS posts
            WHERE   posts.post_type   IN ('" . implode( "','", wc_get_order_types() ) . "')
            AND     posts.post_status = 'wc-on-hold'
            AND     posts.post_modified < %s",
            // @codingStandardsIgnoreEnd
            gmdate( 'Y-m-d H:i:s', absint( $date ) )
        )
    );

    return $unpaid_orders;
}
function botak_cancel_unpaid_orders() {
    $held_duration = get_option( 'woocommerce_hold_stock_minutes' );

    if ( $held_duration < 1 || 'yes' !== get_option( 'woocommerce_manage_stock' ) ) {
        return;
    }

    $unpaid_orders = botak_get_unpaid_orders( strtotime( '-' . absint( $held_duration ) . ' MINUTES', current_time( 'timestamp' ) ) );

    if ( $unpaid_orders ) {
        foreach ( $unpaid_orders as $unpaid_order ) {
            $order = wc_get_order( $unpaid_order );

            if ( apply_filters( 'woocommerce_cancel_unpaid_order', 'checkout' === $order->get_created_via(), $order ) ) {
                if( $order->get_payment_method() == 'omise_paynow') {
                    $order->update_status( 'cancelled', __( 'Unpaid order cancelled - time limit reached.', 'woocommerce' ) );
                }
            }
        }
    }
}
add_action( 'woocommerce_cancel_unpaid_orders', 'botak_cancel_unpaid_orders' );

function var_dump_($func) {
    echo '<pre>';
    var_dump($func);
    echo '</pre>';
}
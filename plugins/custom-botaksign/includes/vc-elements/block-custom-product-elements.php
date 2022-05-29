<?php

add_action( 'vc_before_init', 'block_custom_product_map', 12 );
add_shortcode( 'vc_block_custom_product', 'vc_block_custom_product' );

add_action( 'wp_ajax_botak_get_product_by_ids', 'botak_get_product_by_ids' );
add_action( 'wp_ajax_nopriv_botak_get_product_by_ids', 'botak_get_product_by_ids' );
function botak_get_product_by_ids() {
    $data = [];
    if (isset($_GET['term'])) {
        global $wpdb;
        $search_query = "SELECT ID, post_title FROM {$wpdb->prefix}posts WHERE post_type = 'product' AND post_title LIKE %s";

        $like = '%'.$_GET['term'].'%';
        $results = $wpdb->get_results($wpdb->prepare($search_query, $like), ARRAY_N);
        foreach ($results as $key => $result) {
            $data[] = [
                'id' => $result[0],
                'text' => $result[1],
            ];
        }
    };

    wp_send_json($data, 200);
    die();
}

function block_custom_product_map() {         
    $new_option = array();
    
    $taxonomy     = 'product_cat';
    $orderby      = 'name';  
    $show_count   = 0;      // 1 for yes, 0 for no
    $pad_counts   = 0;      // 1 for yes, 0 for no
    $hierarchical = 1;      // 1 for yes, 0 for no  
    $title        = '';  
    $empty        = 0;

    $args = array(
        'taxonomy'     => $taxonomy,
        'orderby'      => $orderby,
        'show_count'   => $show_count,
        'pad_counts'   => $pad_counts,
        'hierarchical' => $hierarchical,
        'title_li'     => $title,
        'hide_empty'   => $empty
    );
    $all_categories = get_categories( $args );
    foreach ($all_categories as $cat) {
        $new_option[$cat->name] = $cat->term_id;
    }

    vc_map( array(
        "name" => "Get product by IDs",
        "base" => "vc_block_custom_product",
        "class" => "",
        "icon" => "vc_element-icon vc_icon-vc-gitem-image",
        "category" => "Content",
        "params" => array(
            array(
                "type"          => "textfield",
                "holder"        => "div",
                "group"         => __('Content Options'),
                "class"         => "block-title",
                "heading"       => __( "Title", "nb-elements" ),
                "value"         => "",
                "param_name"    => "add_vc_block_custom_product_title",
                "save_always"   => true
            ),
            array(
                "type"          => "select2",
                "holder"        => "div",
                "group"         => __('Content Options'),
                "class"         => "block-custom-product",
                "heading"       => __( "Products", "nb-elements" ),
                "value"         => "",
                "param_name"    => "add_vc_block_custom_product",
                "save_always"   => true
            ),
        )
    ));
}

function vc_block_custom_product($atts, $content = null) {
    extract( shortcode_atts( array(
        'add_vc_block_custom_product' => array(),
        'add_vc_block_custom_product_title'   => ""
    ), $atts ) );

    $product_ids = explode(',', $atts['add_vc_block_custom_product']);
    $block_title = $atts['add_vc_block_custom_product_title'];

    if( count($product_ids) ) {
        ob_start();
        $args = array(
            'post_type'         => 'product',
            'post_status'       => 'publish',
            'posts_per_page'    => -1,
            'post__in'          => $product_ids
        );

        // query database
        $products = new WP_Query($args);
        if ($products->have_posts()) {
            ?>
            <div class="container">
                <div class="product-items grid-type">
                    <div class="block-category-product">
                        <?php 
                            echo '<h2 class="archive-title-css">' . $block_title . '</h2>';

                            woocommerce_product_loop_start();

                            while ($products->have_posts()) {
                                $products->the_post();

                                wc_get_template_part('content', 'product');
                            };

                            woocommerce_product_loop_end();
                        ?>
                        <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
                        <script>
                            jQuery(function($) {
                                $('.product-items .block-category-product .products').addClass('owl-carousel');
                                jQuery(".owl-carousel").owlCarousel({
                                    items: 4,
                                    loop: false,
                                    margin: 0,
                                    nav: true,
                                    navText: ["<i class=\'fa fa-caret-left\'></i>", "<i class=\'fa fa-caret-right\'></i>"],
                                    autoplay: true,
                                    autoplayHoverPause: true,
                                    responsive: {0: {items: 2}, 600: {items: 3}, 1000: {items: 4}}
                                });
                            });
                        </script>
                        <a class="view-all" href="#">View All Products</a>
                    </div>
                </div>
            </div>
            <?php
        }
        wp_reset_postdata();
    }

    return ob_get_clean();
}

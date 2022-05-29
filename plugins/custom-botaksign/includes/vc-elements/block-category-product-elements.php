<?php

add_action( 'vc_before_init', 'block_category_product_map', 12 );
add_shortcode( 'vc_block_category_product', 'vc_block_category_product' );

function block_category_product_map() {         
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
        "name" => "Get product by Category",
        "base" => "vc_block_category_product",
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
                "value"         => "Block product",
                "param_name"    => "add_vc_block_category_product_title",
                "save_always"   => true
            ),
            array(
                'type' => 'param_group',
                'value' => '',
                'heading' => __('Categories'),
                'class' => "block-categories",
                'param_name' => 'add_vc_category',
                'group' => __('Content Options', 'nb-elements'),
                // Note params is mapped inside param-group:
                'params' => array(
                    array(
                        'type' => 'dropdown',
                        'heading' => __('Select Category'),
                        'param_name' => 'add_vc_category',
                        'group' => __('Content Options', 'nb-elements'),
                        'value' => $new_option,
                        'save_always' => true
                    )
                )
            ),
            array(
                "type"          => "textfield",
                "holder"        => "div",
                "group"         => __('Content Options'),
                "class"         => "block-number-product",
                "heading"       => __( "Total product", "nb-elements" ),
                "value"         => "10",
                "param_name"    => "add_vc_block_number_product",
                "save_always"   => true
            ),
        )
    ));
}

function vc_block_category_product($atts, $content = null) {
    extract( shortcode_atts( array(
        'add_vc_category' => array(),
        'add_vc_block_number_product' => 10,
        'add_vc_block_category_product_title'   => ""
    ), $atts ) );

    $categories = vc_param_group_parse_atts($atts['add_vc_category']);
    $block_title = $atts['add_vc_block_category_product_title'];
    $total_product = $atts['add_vc_block_number_product'];

    if( count($categories) ) {
        ob_start();
        $products = [];
        $category_ids = [];
        foreach ($categories as $c) {
            $category_ids[] = $c['add_vc_category'];
        }
        $args = array(
            'post_type'         => 'product',
            'post_status'       => 'publish',
            'posts_per_page'    => $total_product,
            'tax_query'         => array(
                array(
                    'taxonomy'          => 'product_cat',
                    'field'             => 'id',
                    'terms'             => $category_ids,
                    'include_children'  => true,
                ),
            )
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

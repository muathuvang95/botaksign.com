<?php

add_action( 'vc_before_init', 'block_category_map', 12 );
add_shortcode( 'vc_block_category', 'vc_block_category' );

function block_category_map() {         
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
        "name" => "Show Block Category",
        "base" => "vc_block_category",
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
                "param_name"    => "add_vc_block_category_title",
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
        )
    ));
}

function vc_block_category($atts, $content = null) {
    extract( shortcode_atts( array(
        'add_vc_category' => array(),
        'add_vc_block_category_title'   => ""
    ), $atts ) );

    $categories = vc_param_group_parse_atts($atts['add_vc_category']);
    $block_title = $atts['add_vc_block_category_title'];

    if( count($categories) ) {
        ob_start();

        $category_ids = [];
        foreach ($categories as $c) {
            $category_ids[] = $c['add_vc_category'];
        }

        ob_start();
        $pcat_args = array(
            'hide_empty'    => 0,
            'hierarchical'  => 1,
            'taxonomy'      => 'product_cat',
            'include'       => $category_ids,
        );
        $product_categories = get_categories($pcat_args);
        
        foreach ($product_categories as $cat) {
            $thumbnail_id = get_term_meta($cat->term_id, 'thumbnail_id', true);
            $image = wp_get_attachment_url($thumbnail_id) != '' ? wp_get_attachment_url($thumbnail_id) : wc_placeholder_img_src();
            ?>
            <li class="product-category product">
                <a href="<?php echo get_term_link($cat->slug, 'product_cat'); ?>"><img
                        src="<?php echo esc_url($image); ?>" alt="<?php echo $cat->name; ?>" width="600"
                        height="600" title="" style="outline: red dashed 1px;">
                    <h2 class="woocommerce-loop-category__title">
            <?php echo $cat->name; ?>
                        <mark class="count">(<?php echo $cat->count; ?>)</mark>
                    </h2>
                </a>
            </li>
            <?php
        }
        return '<h2 class="archive-title-css">' . $block_title . '</h2><div class="products row grid-type"><ul>' . ob_get_clean() . '</ul></div>';
    }

    return ob_get_clean();
}

<?php

add_action( 'vc_before_init', 'artwork_services_map', 12 );
add_shortcode( 'vc_artwork_services', 'vc_artwork_services' );
add_action( 'wp_enqueue_scripts', 'enqueue_service_scripts');
add_action( 'admin_enqueue_scripts', 'enqueue_service_scripts');

function enqueue_service_scripts() {
    wp_enqueue_style('service', CUSTOM_BOTAKSIGN_URL . '/assets/css/service.css');
}

function artwork_services_map() {         
    $new_option = array();
    
    $args = array(
        'post_type'         => 'product',
        'post_status'       => 'publish',
        'posts_per_page'    => -1,
        'tax_query' => array(
            array(
                'taxonomy'  => 'product_type',
                'field'     => 'slug',
                'terms'     => 'service'
            )
        )
    );
    $query = new WP_Query( $args );
    
    if ($query->have_posts()) {
        $services = $query->posts;
        foreach ($services as $s) {
            $new_option[$s->post_title] = $s->ID;
        }
    }

    vc_map( array(
        "name" => "Artwork Services",
        "base" => "vc_artwork_services",
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
                "param_name"    => "add_vc_artwork_title",
                "save_always"   => true
            ),   
            array(
                'type' => 'param_group',
                'value' => '',
                'heading' => __('Services'),
                'class' => "block-services",
                'param_name' => 'add_vc_artwork_service',
                'group' => __('Content Options', 'nb-elements'),
                // Note params is mapped inside param-group:
                'params' => array(
                    array(
                        'type' => 'dropdown',
                        'heading' => __('Service name'),
                        'param_name' => 'add_vc_artwork_service',
                        'group' => __('Content Options', 'nb-elements'),
                        'value' => $new_option,
                        'save_always' => true
                    )
                )
            )
        )
    ));
}

function vc_artwork_services($atts, $content = null) {
    extract( shortcode_atts( array(
        'add_vc_artwork_service' => array(),
        'add_vc_artwork_title'   => ""
    ), $atts ) );

    $artwork_service = vc_param_group_parse_atts($atts['add_vc_artwork_service']);
    $block_title = $atts['add_vc_artwork_title'];

    if( count($artwork_service) ) {
        ob_start();
        $services = [];
        foreach ($artwork_service as $sv) {
            $id = $sv['add_vc_artwork_service'];
            $data = [];
            $service = wc_get_product((int) $id); //convert string id to int id
            if (is_object($service)) {
                $data = [
                    'id'            => $service->get_id(),
                    'title'         => $service->get_title(),
                    'price'         => $service->get_price() ? $service->get_price() : 0,
                    'image'         => $service->get_image(),
                    'url'           => get_permalink($service->get_id()),
                    'description'   => $service->get_short_description() != "" ? $service->get_short_description() : $service->get_description(),
                ];
            }

            if (count($data)) {
                $services[] = $data;
            }
        } ?>

        <section class="container artwork-service">
            <div class="row title-block">
                <h2><?php echo $block_title;?><span></span></h2>
            </div>
                <?php foreach ($services as $s) :?>
                    <div class="row service-block">
                        <div class="image-block">
                            <?php echo $s['image']; ?>
                        </div>
                        <div class="content-block">
                            <div class="content-block-container">
                                <p class="title">
                                    <a href="<?php echo esc_url($s['url']) ;?>" product_id="<?php echo $s['id']; ?>"><?php echo $s['title']; ?></a>
                                    <span>&#8250;</span>
                                </p>
                                <p class="description">
                                    <?php echo $s['description']; ?>
                                </p>
                                <p class="price-block-mobile">
                                    From <?php echo wc_price($s['price']); ?>
                                </p>
                            </div>
                        </div>
                        <div class="price-block-desktop">
                            <p class="title">
                                From
                            </p>
                            <p class="price">
                                <?php echo wc_price($s['price']); ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </section>
        <?php
    }

    return ob_get_clean();
}

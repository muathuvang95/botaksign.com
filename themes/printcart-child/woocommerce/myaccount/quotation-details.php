<?php

if(count( WC()->cart->get_cart()) <= 0) return;
$loop = 1;
foreach ( WC()->cart->get_cart() as $order_item_id => $cart_item ) {

    $_product   = $cart_item['data'];
    $product_id = $cart_item['product_id'];

    $item_data = array();

    // Variation values are shown only if they are not found in the title as of 3.0.
    // This is because variation titles display the attributes.

    if ( $cart_item['data']->is_type( 'variation' ) && is_array( $cart_item['variation'] ) ) {
        foreach ( $cart_item['variation'] as $name => $value ) {
            $taxonomy = wc_attribute_taxonomy_name( str_replace( 'attribute_pa_', '', urldecode( $name ) ) );

            if ( taxonomy_exists( $taxonomy ) ) {
                // If this is a term slug, get the term's nice name.
                $term = get_term_by( 'slug', $value, $taxonomy );
                if ( ! is_wp_error( $term ) && $term && $term->name ) {
                    $value = $term->name;
                }
                $label = wc_attribute_label( $taxonomy );
            } else {
                // If this is a custom option slug, get the options name.
                $value = $value;
                $label = wc_attribute_label( str_replace( 'attribute_', '', $name ), $cart_item['data'] );
            }

            // Check the nicename against the title.
            if ( '' === $value || wc_is_attribute_in_product_name( $value, $cart_item['data']->get_name() ) ) {
                continue;
            }

            $item_data[] = array(
                'key'   => $label,
                'value' => $value,
            );
        }
    }

    $item_data = apply_filters( 'woocommerce_get_item_data', $item_data, $cart_item );

    // Format item data ready to display.
    foreach ( $item_data as $key => $data ) {
        // Set hidden to true to not display meta on cart.
        if ( ! empty( $data['hidden'] ) ) {
            unset( $item_data[ $key ] );
            continue;
        }
        $item_data[ $key ]['key']     = ! empty( $data['key'] ) ? $data['key'] : $data['name'];
        $item_data[ $key ]['display'] = ! empty( $data['display'] ) ? $data['display'] : $data['value'];
    }
    $production_time = '';
    if ( $_product && $_product->exists() && $cart_item['quantity'] > 0) {
    	$product_image = wp_get_attachment_image_src( get_post_thumbnail_id( $product_id ), 'single-post-thumbnail' );
    	$src = $product_image && isset($product_image[0]) && $product_image[0] ? $product_image[0] : '';

        ?>
        <div class="nb-quotation-title">
        	<div class="product-name"><?php echo $loop.'. '.$_product->get_name(); ?></div>
        </div>
        <div class="row">
        	<div class="col-md-3">
        		<div class="nb-quotation-thumbnail">
        			<?php echo $_product->get_image(); ?>
        		</div>
        	</div>
        	<div class="col-md-9">
                <div class="nb-quotation-items">
            		<div class="row">
                    	<?php
                        if(count($item_data) > 0) {
                            foreach ($item_data as $k => $v) {
                                if($v['key'] == "Quantity Discount" || $v['key'] == "Production Time" || $v['key'] == "SKU" || $v['key'] == "item_status") {
                                    if($v['key'] == "Production Time") {
                                        $production_time = $v['display'];
                                    }
                                    continue;
                                }
                                echo '<div class="item-meta col-md-6"><span class="item-key">' . $v['key'] . ':</span> <span class="item-value">' . preg_replace( '/&nbsp;&nbsp;(.*)/' , '' , $v['display']) . '</span></div>';
                            }
                        }
    	                ?>
                    </div>
                </div>
        	</div>
        </div>
        
        <div class="nb-quotation-product-details">
        	<div><span class="key">SKU : </span><span class="value">
                <?php echo nb_get_product_sku_quotation($cart_item); ?>   
            </span></div>
            <div style="margin-bottom: 4px"><span class="key">Quantity : </span><span class="value"><?php echo $cart_item['quantity']; ?></span></div>
            <div style="margin-bottom: 4px"><span class="key">Price : </span><span class="value">SGD $ <?php echo number_format($cart_item['line_total'] , 2); ?></span></div>
            <div style="margin-bottom: 4px"><span class="key">Production Time : </span><span class="value"><?php echo $production_time; ?></span></div>
        </div>
        <?php
    }
    $loop ++;
}

function nb_get_product_sku_quotation($cart_item)
{
    // The WC_Product object
    $product = $cart_item['data'];

    //CS botak check condition to change gallery
    $sku = '';
    if (isset($cart_item['nbo_meta'])) {
         if( nbd_is_base64_string( $cart_item['nbo_meta']['options']['fields'] )) {
            $cart_item['nbo_meta']['options']['fields'] = base64_decode( $cart_item['nbo_meta']['options']['fields'] ); // custom botak fix lose the sku when update base64_decode
        }
        $option_fields = maybe_unserialize($cart_item['nbo_meta']['options']['fields']);
        try {
            $check = NBD_FRONTEND_PRINTING_OPTIONS::check_and_get_change_gallery($option_fields['gallery_options'], maybe_unserialize($cart_item['nbo_meta'])['field'], $cart_item['quantity']);
            if ($check['change'] === true && $check['option']['sku']) {
                $sku = $check['option']['sku'];
            }
            foreach ($cart_item['nbo_meta']['field'] as $f_id => $fvalue) {
                $select = !is_array($fvalue) ? $fvalue : $fvalue['value'];
                foreach ($option_fields['fields'] as $data) {
                    if ($f_id === $data['id']) {
                        $option = $data['general']['attributes']['options'][$select];
                        if (isset($option['sku']) && $option['sku'] != '') {
                            $sku .= $option['sku'];
                        }
                    }
                }
            }
        } catch (Exception $e) {
            write_log($e->getMessage());
        }
    }
    //End CS botak check condition to change gallery

    // Get the  SKU
    if ($sku == '') {
        $sku = $product->get_sku();
    }

    // When sku doesn't exist
    if (empty($sku) || $sku == '') {
        return $item_name;
    }

    return $sku;
}
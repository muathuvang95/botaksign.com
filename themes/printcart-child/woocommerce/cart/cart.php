<?php
/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.8.0
 */

defined( 'ABSPATH' ) || exit;

wp_enqueue_style('service', CUSTOM_BOTAKSIGN_URL . '/assets/css/service.css');

$data = array(
    'ajax_url'                     => WC()->ajax_url(),
    'wc_ajax_url'                  => WC_AJAX::get_endpoint( '%%endpoint%%' ),
    'update_shipping_method_nonce' => wp_create_nonce( 'update-shipping-method' ),
    'apply_coupon_nonce'           => wp_create_nonce( 'apply-coupon' ),
    'remove_coupon_nonce'          => wp_create_nonce( 'remove-coupon' ),
);
wp_localize_script( 'printcart_front_script', 'wc_cart_params', $data );

//sort cart by item and cart service
$products_in_cart = array();
$products_parent = array();
$products_service = array();
$cart_contents = array();
foreach (WC()->cart->cart_contents as $key => $item) {
    if (array_key_exists('nbo_meta', $item)) {
        if (array_key_exists('parent_cart_item', $item['nbo_meta'])) {
            //Save products service to array, with key is parent item cart id
            $products_service[$key] = $item;
        } else {
            //Save products parent to array
            $products_parent[$key] = $item;
        }
    } else {
        $products_parent[$key] = $item;
    }
};

//if (count($products_parent)) {
//    foreach ($products_parent as $key => $item_parent) {
//        $cart_contents[$key] = $item_parent;
//        if (array_key_exists($key, $products_service)) {
//            //Get item child in array with key is item parent id
//            foreach ($products_service as $k => $item_service) {
//                if ($key === $item_service['nbo_meta']['parent_cart_item']) {
//                    $cart_contents[$k] = $item_service;
//                    unset($products_service[$k]); //unset service be added from services array
//                }
//            }
//        }
//    }
//}
//
//if (count($products_service)) {
//    foreach ($products_service as $key => $item_service) {
//        $cart_contents[$key] = $item_service;
//        unset($products_service[$k]); //unset service be added from services array
//    }
//}
//
//WC()->cart->cart_contents = $cart_contents;

do_action('woocommerce_before_cart');
?>

<form class="woocommerce-cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
    <div class="row">
        <?php do_action('woocommerce_before_cart_table'); ?>

        <div class="cart-left-section nb-cart-left col-lg-8">
            <?php do_action('woocommerce_before_cart_contents'); ?>
            <div class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">

                <?php foreach ($products_parent as $parent_key => $parent_item) { ?>
                    <div class="product-item">
                        <?php
                        $_product = apply_filters('woocommerce_cart_item_product', $parent_item['data'], $parent_item, $parent_key);
                        $product_id = apply_filters('woocommerce_cart_item_product_id', $parent_item['product_id'], $parent_item, $parent_key);

                        if ($_product && $_product->exists() && $parent_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $parent_item, $parent_key)) {
                            $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($parent_item) : '', $parent_item, $parent_key);
                            render_product_and_service($parent_key, $parent_item, $_product, $product_id, $product_permalink);
                        }

                        foreach ($products_service as $cart_item_key => $cart_item) {
                            if ($parent_key === $cart_item['nbo_meta']['parent_cart_item']) {
                                $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                                $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

                                if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
                                    $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
                                    render_product_and_service($cart_item_key, $cart_item, $_product, $product_id, $product_permalink);
                                }
                                unset($products_service[$cart_item_key]);  //unset service be added from services array
                            }
                        }
                        ?>
                    </div>
                    <?php
                }

                foreach ($products_service as $cart_item_key => $cart_item) { ?>
                    <div class="product-item">
                        <?php 
                            $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                            $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

                            if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
                                $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
                                render_product_and_service($cart_item_key, $cart_item, $_product, $product_id, $product_permalink);
                            }
                        ?>
                    </div>
                <?php }
                ?>

                <?php do_action('woocommerce_cart_contents'); ?>
            </div>
            <?php do_action('woocommerce_after_cart_contents'); ?>

            <button type="submit" class="button nb-primary-button bt-5" name="update_cart" value="<?php esc_attr_e('Update cart', 'woocommerce'); ?>"><?php esc_html_e('Update cart', 'woocommerce'); ?></button>

            <?php do_action('woocommerce_cart_actions'); ?>

            <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
            
            <?php do_action('woocommerce_after_cart_table'); ?>
        </div>

        <?php do_action('woocommerce_before_cart_collaterals'); ?>


        <div class="cart-right-section nb-cart-right col-lg-4">
            <div class="cart-collaterals">
                <?php
                /**
                 * Cart collaterals hook.
                 *
                 * @hooked woocommerce_cross_sell_display
                 * @hooked woocommerce_cart_totals - 10
                 */
                do_action('woocommerce_cart_collaterals');
                ?>
            </div>
            <div>
                <div colspan="6" class="actions">

                    <?php if (wc_coupons_enabled()) { ?>
                        <div class="coupon">
                            <h3>Coupon</h3>
                            <div class="coupon-wrap">
                                <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e('Coupon code', 'woocommerce'); ?>" />
                                <button type="submit" class="bt-5 nb-wide-button button" name="apply_coupon" value="<?php esc_attr_e('Apply coupon', 'woocommerce'); ?>"><?php esc_attr_e('Apply coupon', 'woocommerce'); ?></button>
                            </div>
                            <?php do_action('woocommerce_cart_coupon'); ?>
                        </div>
                    <?php } ?>

                </div>
            </div>
        </div>

        <?php do_action('woocommerce_after_cart'); ?>
    </div>
</form>

<?php

function render_product_and_service($cart_item_key, $cart_item, $_product, $product_id, $product_permalink) { ?>
    <div class="row woocommerce-cart-form__cart-item <?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">
        <div class="col-md-2 btk-product-thumbnail">
            <?php
            $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key);

            if (!$product_permalink) {
                echo $thumbnail; // PHPCS: XSS ok.
            } else {
                printf('<a href="%s">%s</a>', esc_url($product_permalink), $thumbnail); // PHPCS: XSS ok.
            }
            ?>
        </div>

        <div class="col-md-5 btk-product-name">
            <?php
            if (!$product_permalink) {
                echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key) . '&nbsp;');
            } else {
                echo wp_kses_post(apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s" class="product-link">%s<span></span></a>', esc_url($product_permalink), $_product->get_name()), $cart_item, $cart_item_key));
            }

            do_action('woocommerce_after_cart_item_name', $cart_item, $cart_item_key);

            // Meta data.
            echo wc_get_formatted_cart_item_data($cart_item); // PHPCS: XSS ok.
            // Backorder notification.
            if ($_product->backorders_require_notification() && $_product->is_on_backorder($cart_item['quantity'])) {
                echo wp_kses_post(apply_filters('woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__('Available on backorder', 'woocommerce') . '</p>', $product_id));
            }
            ?>
        </div>
        <div class="col-md-5 btk-action-table">
            <div class="row row-price">
                <div class="product-subtotal" data-title="<?php esc_attr_e('Total', 'woocommerce'); ?>">
                    <p class="title"><?php esc_attr_e('Total', 'woocommerce'); ?></p>
                    <?php
                        echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); // PHPCS: XSS ok.
                    ?>
                </div>
            </div>
            <div class="row row-action">
                <div class="btk-product-quantity" data-title="<?php esc_attr_e('Quantity', 'woocommerce'); ?>">
                    <?php
                    if ($_product->is_sold_individually()) {
                        $product_quantity = sprintf('1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key);
                    } else {
                        $product_quantity = woocommerce_quantity_input(
                            array(
                                'input_name' => "cart[{$cart_item_key}][qty]",
                                'input_value' => $cart_item['quantity'],
                                'max_value' => $_product->get_max_purchase_quantity(),
                                'min_value' => '0',
                                'product_name' => $_product->get_name(),
                            ), $_product, false
                        );
                    }

                    echo apply_filters('woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item); // PHPCS: XSS ok.
                    ?>
                </div>
                <div class="btk-product-edit">
                    <?php
                        $link = add_query_arg(
                            array(
                                'nbo_cart_item_key'  => $cart_item_key,
                            ), $_product->get_permalink( $cart_item ) );
                        $link = wp_nonce_url( $link, 'nbo-edit' );
                        $svg_edit = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="20" height="20" viewBox="0 0 1000 1000" enable-background="new 0 0 1000 1000" xml:space="preserve"><g><path d="M420,693c17.5,1.2,35.3-4,49.6-15.8l4.8-1.5L322.7,523.9l-1.5,4.8c-11.8,14.3-17.1,32.1-15.9,49.6l-53.8,168.5L420,693z M969.4,78.6l-49.6-49.6c-27.4-27.4-71.9-27.4-99.3,0L772.4,77l148.9,148.9l48.1-48.1C996.9,150.4,996.9,106,969.4,78.6z M870.2,277.1L721.2,128.2L375.3,474.1L524.2,623L870.2,277.1z M852.4,921.4H78V147.1h493.6V79H80.2C41.4,79,10,110.5,10,149.2v772.2c0,38.8,31.4,70.2,70.2,70.2h772.2c38.8,0,70.2-31.4,70.2-70.2V430h-70.2V921.4z"/></g></svg>';
                        $edit_html = '<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">'.$svg_edit.' <span class="btk-title-action">Edit</span></a>';
                        echo apply_filters(// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                            'woocommerce_cart_item_edit_link', sprintf(
                                $edit_html, $link, esc_html__('Remove this item', 'woocommerce'), esc_attr($product_id), esc_attr($_product->get_sku())
                            ), $cart_item_key
                        );
                    ?>
                </div>
                <div class="btk-product-remove">
                    <?php
                    $svg_trash = '<svg xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:cc="http://creativecommons.org/ns#" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:svg="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg" xmlns:sodipodi="http://sodipodi.sourceforge.net/DTD/sodipodi-0.dtd" xmlns:inkscape="http://www.inkscape.org/namespaces/inkscape" viewBox="0 -256 1792 1792" id="svg3741" version="1.1" inkscape:version="0.48.3.1 r9886" width="20" height="20" sodipodi:docname="trash_font_awesome.svg"> <g transform="matrix(1,0,0,-1,197.42373,1255.0508)" id="g3743"> <path d="M 512,800 V 224 q 0,-14 -9,-23 -9,-9 -23,-9 h -64 q -14,0 -23,9 -9,9 -9,23 v 576 q 0,14 9,23 9,9 23,9 h 64 q 14,0 23,-9 9,-9 9,-23 z m 256,0 V 224 q 0,-14 -9,-23 -9,-9 -23,-9 h -64 q -14,0 -23,9 -9,9 -9,23 v 576 q 0,14 9,23 9,9 23,9 h 64 q 14,0 23,-9 9,-9 9,-23 z m 256,0 V 224 q 0,-14 -9,-23 -9,-9 -23,-9 h -64 q -14,0 -23,9 -9,9 -9,23 v 576 q 0,14 9,23 9,9 23,9 h 64 q 14,0 23,-9 9,-9 9,-23 z M 1152,76 v 948 H 256 V 76 Q 256,54 263,35.5 270,17 277.5,8.5 285,0 288,0 h 832 q 3,0 10.5,8.5 7.5,8.5 14.5,27 7,18.5 7,40.5 z M 480,1152 h 448 l -48,117 q -7,9 -17,11 H 546 q -10,-2 -17,-11 z m 928,-32 v -64 q 0,-14 -9,-23 -9,-9 -23,-9 h -96 V 76 q 0,-83 -47,-143.5 -47,-60.5 -113,-60.5 H 288 q -66,0 -113,58.5 Q 128,-11 128,72 v 952 H 32 q -14,0 -23,9 -9,9 -9,23 v 64 q 0,14 9,23 9,9 23,9 h 309 l 70,167 q 15,37 54,63 39,26 79,26 h 320 q 40,0 79,-26 39,-26 54,-63 l 70,-167 h 309 q 14,0 23,-9 9,-9 9,-23 z" id="path3745" inkscape:connector-curvature="0" style="fill:currentColor"/> </g></svg>';
                    $remove_html = '<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">'.$svg_trash.' <span class="btk-title-action">Remove</span></a>';
                    echo apply_filters(// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        'woocommerce_cart_item_remove_link', sprintf(
                            $remove_html, esc_url(wc_get_cart_remove_url($cart_item_key)), esc_html__('Remove this item', 'woocommerce'), esc_attr($product_id), esc_attr($_product->get_sku())
                        ), $cart_item_key
                    );
                    ?>
                </div>
            </div>
        </div>
    </div>
    <?php }
?>
<?php
/**
 * Simple product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/simple.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.0
 */
defined('ABSPATH') || exit;

global $product;
if (!$product->is_purchasable()) {
    return;
}
echo wc_get_stock_html($product); // WPCS: XSS ok.
$product_id = $product->get_id();
$items = nbd_get_items_product_grouped($product_id);
if(is_array($items)) {
    $f_product = wc_get_product($items[0]['id']);
}
if ($product->is_in_stock()) :
    if( isset($f_product) && $f_product && !$f_product->is_in_stock() ) { 
        echo '<div class="hidden" style="display: none!important">'; 
    }
    ?>
    <?php do_action('woocommerce_before_add_to_cart_form'); ?>

    <form class="cart" action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink())); ?>" method="post" enctype='multipart/form-data'>
        <div class="quantity-block simple-product">
            <label><b>Quantity</b></label>
            <?php
            //CS botak position input quantity
            woocommerce_quantity_input(array(
                'min_value' => apply_filters('woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product),
                'max_value' => apply_filters('woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product),
                'input_value' => isset($_POST['quantity']) ? wc_stock_amount(wp_unslash($_POST['quantity'])) : $product->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.
            ));
            ?>
        </div>
        
        <?php do_action('woocommerce_before_add_to_cart_button'); ?>

        <?php
        do_action('woocommerce_before_add_to_cart_quantity');
        do_action('woocommerce_after_add_to_cart_quantity');
        ?>

        <button type="submit" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>" class="single_add_to_cart_button button alt "><?php echo esc_html($product->single_add_to_cart_text()); ?></button>
        <input type="hidden" name="woosg" class="woosg-product-type" value="woosg">
    
        <?php do_action('woocommerce_after_add_to_cart_button'); ?>
    </form>

    <?php do_action('woocommerce_after_add_to_cart_form'); 
    if( isset($f_product) && $f_product && !$f_product->is_in_stock() ) { 
        echo '</div>'; 
        $options_settings = get_option( NBT_NOTI_SETTINGS );
        $stock = $product->get_stock_quantity();
        if ( !$stock > 0  && !$f_product->is_in_stock() ) {
            if ( isset($options_settings['nbt_product_notification_form_placeholder']) && $options_settings['nbt_product_notification_form_placeholder'] ) {
                $placeholder = $options_settings['nbt_product_notification_form_placeholder'] ;
            } else {
                $placeholder = 'Email address';
            }
            if (isset($options_settings['nbt_product_notification_form_button']) && $options_settings['nbt_product_notification_form_button'] ) {
                $submit_value = $options_settings['nbt_product_notification_form_button'];
            } else {
                $submit_value = 'Notify me when in stock';
            }
            $form_desc = '';
            if (isset($options_settings['nbt_product_notification_desc']) && $options_settings['nbt_product_notification_desc'] ) {
                $form_desc = '<div class="product-noti-desc">'.$options_settings['nbt_product_notification_desc'].'</div>';
            }
            $form = ''.$form_desc.'

                <form action="" method="post" class="alert_wrapper">
                    <input type="email" name="alert_email" id="alert_email" placeholder="' . $placeholder . '" />
                    <input type="hidden" name="alert_id" id="alert_id" value="' . $f_product->get_id() . '"/>
                    <input type="submit" value="' . $submit_value . '" class="pnotisubmit" />
                </form> <div class="nbt-alert-msg"></div>
            ';
            echo $form;
        }
    }
    ?>
<?php endif;?>
<?php
/**
 * Cart totals
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-totals.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.3.6
 */
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="cart_totals <?php if (WC()->customer->has_calculated_shipping()) echo 'calculated_shipping'; ?>">

    <?php do_action('woocommerce_before_cart_totals'); ?>

    <div class="cart-totals-wrap">
        <div class="block-title"><?php esc_html_e('Order Summary', 'printcart'); ?></div>
        <div class="block-body">
            <table cellspacing="0" class="shop_table shop_table_responsive">

                <tr class="cart-subtotal">
                    <th><?php esc_html_e('Items', 'printcart'); ?></th>
                    <td data-title="<?php esc_attr_e('Subtotal', 'printcart'); ?>"><?php wc_cart_totals_subtotal_html(); ?></td>
                </tr>

                <?php /* <tr class="shipping">
                    <th><?php esc_html_e('Shipping', 'printcart'); ?></th>
                    <td data-title="<?php esc_attr_e('Shipping', 'printcart'); ?>"><?php echo wc_price(0); ?></td>
                </tr> */ ?>
                    
                <?php foreach (WC()->cart->get_coupons() as $code => $coupon) : ?>
                    <tr class="cart-discount coupon-<?php echo esc_attr(sanitize_title($code)); ?>">
                        <th><?php wc_cart_totals_coupon_label($coupon); ?></th>
                        <td data-title="<?php echo esc_attr(wc_cart_totals_coupon_label($coupon, false)); ?>"><?php wc_cart_totals_coupon_html($coupon); ?></td>
                    </tr>
                <?php endforeach; ?>

                <?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()) : ?>

                    <?php do_action('woocommerce_cart_totals_before_shipping'); ?>

                    <?php wc_cart_totals_shipping_html(); ?>

                    <?php do_action('woocommerce_cart_totals_after_shipping'); ?>

                <?php elseif (WC()->cart->needs_shipping() && 'yes' === get_option('woocommerce_enable_shipping_calc')) : ?>

                    <tr class="shipping">
                        <th><?php esc_html_e('Shipping', 'printcart'); ?></th>
                        <td data-title="<?php esc_attr_e('Shipping', 'printcart'); ?>"><?php woocommerce_shipping_calculator(); ?></td>
                    </tr>

            <?php endif; ?>

                <?php foreach (WC()->cart->get_fees() as $fee) : ?>
                    <tr class="fee">
                        <th><?php echo esc_html($fee->name); ?></th>
                        <td data-title="<?php echo esc_attr($fee->name); ?>"><?php wc_cart_totals_fee_html($fee); ?></td>
                    </tr>
                <?php endforeach; ?>

                <?php
                if (wc_tax_enabled() && 'excl' === WC()->cart->tax_display_cart) :
                    $taxable_address = WC()->customer->get_taxable_address();
                    $estimated_text = WC()->customer->is_customer_outside_base() && !WC()->customer->has_calculated_shipping() ? sprintf(' <small>' . esc_html__('(estimated for %s)', 'printcart') . '</small>', WC()->countries->estimated_for_prefix($taxable_address[0]) . WC()->countries->countries[$taxable_address[0]]) : '';

                    if ('itemized' === get_option('woocommerce_tax_total_display')) :
                        ?>
                        <?php foreach (WC()->cart->get_tax_totals() as $code => $tax) : ?>
                            <tr class="tax-rate tax-rate-<?php echo sanitize_title($code); ?>">
                                <th><?php echo esc_html($tax->label) . $estimated_text; ?></th>
                                <td data-title="<?php echo esc_attr($tax->label); ?>"><?php echo wp_kses_post($tax->formatted_amount); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr class="tax-total">
                            <th><?php echo esc_html(WC()->countries->tax_or_vat()) . $estimated_text; ?></th>
                            <td data-title="<?php echo esc_attr(WC()->countries->tax_or_vat()); ?>"><?php wc_cart_totals_taxes_total_html(); ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endif; ?>

                <?php do_action('woocommerce_cart_totals_before_order_total'); ?>

                <tr class="order-total">
                    <th><?php esc_html_e('Subtotal (incl. tax)', 'printcart'); ?></th>
                    <td data-title="<?php esc_attr_e('Subtotal (incl. tax)', 'printcart'); ?>"><?php wc_cart_totals_order_total_html(); ?></td>
                </tr>

                <?php do_action('woocommerce_cart_totals_after_order_total'); ?>

            </table>

            <?php if (is_user_logged_in()) { ?>
                <div class="wc-proceed-to-checkout">
                    <a class="checkout-button button alt wc-forward bt-5 nb-wide-button btn-generate-quotation" style="cursor: pointer;">
                        <span>
                            <span><svg xmlns="http://www.w3.org/2000/svg" height="40" viewBox="-75 0 512 512" width="24"><path d="m272 21.214844v68.785156h68.785156zm0 0" style="fill: #fff;"/><path d="m218.5 210h-75v122h-31.074219l68.574219 68.007812 68.574219-68.007812h-31.074219zm0 0" style="fill: #fff;"/><path d="m242 120v-120h-242v512h362v-392zm-61 322.257812-141.425781-140.257812h73.925781v-122h135v122h73.925781zm0 0" style="fill: #fff;"/></svg></span>
                            <span>Generate Quotation</span>
                        </span>
                    </a>
                </div>
            <?php } ?>

            <div class="wc-continue-shiping">
                <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ) ?>" class="wc-backward checkout-button button alt wc-forward bt-5 nb-wide-button btn-continue-shiping"><?php echo __( 'Continue shopping', 'woocommerce' ); ?></a>
            </div>
            
            <div class="wc-proceed-to-checkout">
                <?php do_action('woocommerce_proceed_to_checkout'); ?>
            </div>
        </div>
    </div>

    <?php do_action('woocommerce_after_cart_totals'); ?>

</div>

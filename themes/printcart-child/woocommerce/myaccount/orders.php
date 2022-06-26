<?php
/**
 * Orders
 *
 * Shows orders on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/orders.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.7.0
 */

defined('ABSPATH') || exit;

do_action('woocommerce_before_account_orders', $has_orders); ?>

<?php

// BOTAK custom reupload Phase 3 
$user_id_current = get_current_user_id();
$order_id = isset($_GET['edit_order']) ? $_GET['edit_order'] : '';
$user_id = '';
if( $order_id ) {
    $order = wc_get_order($order_id);
    if($order) {
        $user_id =  $order->get_user_id();
    }
}
if( $order_id  && $user_id == $user_id_current) {
    if ( is_user_logged_in() ) {
        wc_get_template( 'myaccount/reupload-order.php' );
    } else {
        echo( 'Non-Personalized Message!' );
    }
    
} else {
    if ($has_orders) : ?>
        <div class="nb-orders">
            <div class="nb-orders-table">
                <table class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">
                    <thead>
                    <tr>
                        <?php foreach (wc_get_account_orders_columns() as $column_id => $column_name) : ?>
                            <?php if ('order-total' === $column_id) {
                                ?>
                                <th class="woocommerce-orders-table__header woocommerce-orders-table__header-<?php echo esc_attr($column_id); ?>">
                                <span class="nobr">Details</span></th>
                                <?php
                            } else {
                                ?>
                                <th class="woocommerce-orders-table__header woocommerce-orders-table__header-<?php echo esc_attr($column_id); ?>">
                                <span class="nobr"><?php echo esc_html($column_name); ?></span></th>
                                <?php
                            } ?>
                        <?php endforeach; ?>
                    </tr>
                    </thead>

                    <tbody>
                    <?php
                    foreach ($customer_orders->orders as $customer_order) {
                        $order = wc_get_order($customer_order); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.OverrideProhibited
                        $item_count = $order->get_item_count() - $order->get_item_count_refunded();
                        ?>
                        <tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-<?php echo esc_attr($order->get_status()); ?> order">
                            <?php foreach (wc_get_account_orders_columns() as $column_id => $column_name) : ?>
                                <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-<?php echo esc_attr($column_id); ?>"
                                    data-title="<?php echo esc_attr($column_name); ?>">
                                    <?php if (has_action('woocommerce_my_account_my_orders_column_' . $column_id)) : ?>
                                        <?php do_action('woocommerce_my_account_my_orders_column_' . $column_id, $order); ?>

                                    <?php elseif ('order-number' === $column_id) : ?>
                                        <a href="<?php echo esc_url($order->get_view_order_url()); ?>">
                                            <?php echo esc_html(_x('#', 'hash before order number', 'woocommerce') . $order->get_order_number()); ?>
                                        </a>

                                    <?php elseif ('order-date' === $column_id) : ?>
                                        <time datetime="<?php echo esc_attr($order->get_date_created()->date('c')); ?>"><?php echo esc_html(wc_format_datetime($order->get_date_created())); ?></time>

                                    <?php elseif ('order-status' === $column_id) : ?>
                                        <?php
                                        $opt_status = get_post_meta($order->get_id(), '_cxecrt_status_od', true);
                                        if ($opt_status) {
                                            echo botaksign_status_order($opt_status);
                                        } else {
                                            echo esc_html(wc_get_order_status_name($order->get_status()));
                                        }
                                        ?>

                                    <?php elseif ('order-total' === $column_id) : ?>
                                        <a href="<?php echo esc_url($order->get_view_order_url()); ?>">
                                            <div class="nb-title">View</div>
                                            <div class="nb-icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                                                </svg>
                                            </div>  
                                        </a>
                                    <?php elseif ('order-actions' === $column_id) : ?>
                                        <?php
                                        $actions = wc_get_account_orders_actions($order);
                                        unset( $actions[ 'view' ] );
                                        if (!empty($actions)) {
                                            foreach ($actions as $key => $action) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.OverrideProhibited
                                                if($key == 'invoice' && $order->get_payment_method() == 'cod') {
                                                    echo "<div class='nb-no-action'>-</div>";
                                                } else {
                                                    if( $key == 'invoice' ) {
                                                        ?>
                                                        <a href="<?php echo esc_url($action['url']); ?>" class="woocommerce-button button mb-1 <?php echo sanitize_html_class($key); ?>">
                                                            <div class="nb-title">
                                                                <?php echo esc_html($action['name']); ?>
                                                            </div>
                                                            <div class="nb-icon">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cloud-arrow-down" viewBox="0 0 16 16">
                                                                  <path fill-rule="evenodd" d="M7.646 10.854a.5.5 0 0 0 .708 0l2-2a.5.5 0 0 0-.708-.708L8.5 9.293V5.5a.5.5 0 0 0-1 0v3.793L6.354 8.146a.5.5 0 1 0-.708.708l2 2z"></path>
                                                                  <path d="M4.406 3.342A5.53 5.53 0 0 1 8 2c2.69 0 4.923 2 5.166 4.579C14.758 6.804 16 8.137 16 9.773 16 11.569 14.502 13 12.687 13H3.781C1.708 13 0 11.366 0 9.318c0-1.763 1.266-3.223 2.942-3.593.143-.863.698-1.723 1.464-2.383zm.653.757c-.757.653-1.153 1.44-1.153 2.056v.448l-.445.049C2.064 6.805 1 7.952 1 9.318 1 10.785 2.23 12 3.781 12h8.906C13.98 12 15 10.988 15 9.773c0-1.216-1.02-2.228-2.313-2.228h-.5v-.5C12.188 4.825 10.328 3 8 3a4.53 4.53 0 0 0-2.941 1.1z"></path>
                                                                </svg>
                                                            </div>
                                                        </a>
                                                        <?php
                                                    } else {
                                                        echo '<a href="' . esc_url($action['url']) . '" class="woocommerce-button button mb-1 ' . sanitize_html_class($key) . '">' . esc_html($action['name']) . '</a>';
                                                    }
                                                }
                                            }
                                        } else {
                                            echo "<div class='nb-no-action'>-</div>";
                                        }
                                        ?>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <?php do_action('woocommerce_before_account_orders_pagination'); ?>

            <?php if (1 < $customer_orders->max_num_pages) : ?>
                <div class="woocommerce-pagination woocommerce-pagination--without-numbers woocommerce-Pagination">
                    <?php if (1 !== $current_page) : ?>
                        <a class="woocommerce-button woocommerce-button--previous woocommerce-Button woocommerce-Button--previous button"
                           href="<?php echo esc_url(wc_get_endpoint_url('orders', $current_page - 1)); ?>"><?php esc_html_e('Previous', 'woocommerce'); ?></a>
                    <?php endif; ?>

                    <?php if (intval($customer_orders->max_num_pages) !== $current_page) : ?>
                        <a class="woocommerce-button woocommerce-button--next woocommerce-Button woocommerce-Button--next button"
                           href="<?php echo esc_url(wc_get_endpoint_url('orders', $current_page + 1)); ?>"><?php esc_html_e('Next', 'woocommerce'); ?></a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

    <?php else : ?>
        <div class="woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info woocommerce-info">
            <a class="woocommerce-Button button"
               href="<?php echo esc_url(apply_filters('woocommerce_return_to_shop_redirect', wc_get_page_permalink('shop'))); ?>">
                <?php esc_html_e('Browse products', 'woocommerce'); ?>
            </a>
            <?php esc_html_e('No order has been made yet.', 'woocommerce'); ?>
        </div>
    <?php endif; ?>

    <?php do_action('woocommerce_after_account_orders', $has_orders);
}


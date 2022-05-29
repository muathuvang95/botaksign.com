<?php
/**
 * View Order
 *
 * Shows the details of a particular order on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/view-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.0.0
 */

defined( 'ABSPATH' ) || exit;

$notes = $order->get_customer_order_notes();

//$opt_status = get_post_meta($order->get_id(), '_cxecrt_status_od', true);
//$status_botak = wc_get_order_status_name( $order->get_status() );
//if ($opt_status) {
//    $status_botak = botaksign_status_order($opt_status);
//}
?>
<!--<p>-->
<?php
//printf(
//  /* translators: 1: order number 2: order date 3: order status */
//  esc_html__( 'Order #%1$s was placed on %2$s and is currently %3$s.', 'woocommerce' ),
//  '<mark class="order-number">' . $order->get_order_number() . '</mark>', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
//  '<mark class="order-date">' . wc_format_datetime( $order->get_date_created() ) . '</mark>', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
//  '<mark class="order-status">' . $status_botak . '</mark>' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
//);
?>
<!--</p>-->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<style type="text/css">
    button.btn.btn-completed {
        color: #26181b;
        background: #fff200;
    }
    button.btn.btn-status {
        font-weight: 600;
    }
</style>
<div class="view-order-header">
    <div class="order-id">
        Order #<?php echo $order->get_order_number(); ?>
    </div>
    <div class="order-status">
        Status
        <?php
            $opt_status = get_post_meta($order->get_id(), '_order_status', true);
            switch ($opt_status) {
                case 'Pending':
                    echo '<button type="button" class="btn-status btn btn-secondary">Pending</button>';
                    break;
                case 'New':
                    echo '<button type="button" class="btn-status btn btn-completed">New</button>';
                    break;
                case 'Ongoing':
                    echo '<button type="button" class="btn-status btn btn-warning">Ongoing</button>';
                    break;
                case 'Completed':
                    echo '<button type="button" class="btn-status btn btn-success">Completed</button>';
                    break;
                 case 'Collected':
                    echo '<button type="button" class="btn-status btn btn-primary">Collected</button>';
                    break;
                case 'Cancelled':
                    echo '<button type="button" class="btn-status btn btn-danger">Cancelled</button>';
                    break;
                default:
                    # code...
                    break;
            }
        ?>

    </div>
</div>

<?php if ( $notes ) : ?>
    <h2><?php esc_html_e( 'Order updates', 'woocommerce' ); ?></h2>
    <ol class="woocommerce-OrderUpdates commentlist notes">
        <?php foreach ( $notes as $note ) : ?>
        <li class="woocommerce-OrderUpdate comment note">
            <div class="woocommerce-OrderUpdate-inner comment_container">
                <div class="woocommerce-OrderUpdate-text comment-text">
                    <p class="woocommerce-OrderUpdate-meta meta"><?php echo date_i18n( esc_html__( 'l jS \o\f F Y, h:ia', 'woocommerce' ), strtotime( $note->comment_date ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
                    <div class="woocommerce-OrderUpdate-description description">
                        <?php echo wpautop( wptexturize( $note->comment_content ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="clear"></div>
            </div>
        </li>
        <?php endforeach; ?>
    </ol>
<?php endif; ?>

<?php do_action( 'woocommerce_view_order', $order_id ); ?>

<?php
/**
 * Checkout Form
 *
 * This is an overridden copy of the woocommerce/templates/checkout/form-checkout.php file.
 *
 * @package WPMultiStepCheckout
 */

defined( 'ABSPATH' ) || exit;


wc_print_notices();

// show the tabs

// $i                  = 0;
// $number_of_steps    = ( $show_login_step ) ? count( $steps ) + 1 : count( $steps );
// $current_step_title = ( $show_login_step ) ? 'login' : key( array_slice( $steps, 0, 1, true ) );

// do_action( 'wpmc_before_tabs' );
// 

include dirname(__FILE__) . '/nb-step-checkout/functions.php';

$steps = array(
    "shipping" => array(
        "title" => "Shipping",
        "class" => "nb-step-shipping",
        "sections" => array(
            "shipping" , "billing"
        )
    ),
    "payment" => array(
        "title" => "Payment",
        "class" => "nb-step-payment",
    ),
    "checkout" => array(
        "title" => "Check Out",
        "class" => "nb-step-checkout",
    ),
)

?>

<link rel="stylesheet" type="text/css" href="<?php echo get_stylesheet_directory_uri(). '/woocommerce/checkout/nb-step-checkout/assets/css/style.css'; ?>">

<div class="nb-tabs-wrapper">
    <ul class="nb-tabs-list nb-<?php echo $number_of_steps; ?>-tabs" data-current-title="<?php echo $current_step_title; ?>">
    <?php if ( $show_login_step ) : ?>
        <li class="nb-tab-item current nb-login" data-step-title="login">
            <div class="nb-tab-number"><?php echo $i = $i + 1; ?></div>
            <div class="nb-tab-text"><?php echo $options['t_login']; ?></div>
        </li>
    <?php endif; ?>
    <?php
    foreach ( $steps as $_id => $_step ) :
        $class = ( ! $show_login_step && $i == 0 ) ? ' current' : '';
        ?>
        <li class="nb-tab-item<?php echo $class; ?> nb-<?php echo $_id; ?>" data-step-title="<?php echo $_id; ?>">
            <div class="nb-tab-number"><?php echo $i = $i + 1; ?></div>
            <div class="nb-tab-text"><?php echo $_step['title']; ?></div>
        </li>
    <?php endforeach; ?>
    </ul>
</div>
<!-- End The steps tabs -->


<div style="clear: both;"></div>

<div class="nb-steps-wrapper">

<div id="checkout_coupon" class="woocommerce_checkout_coupon" style="display: none;">
	<?php do_action( 'nb-woocommerce_checkout_coupon_form', $checkout ); ?>
</div>

<div id="woocommerce_before_checkout_form" class="woocommerce_before_checkout_form" data-step="<?php echo apply_filters('woocommerce_before_checkout_form_step', 'step-review'); ?>" style="display: none;">
    <?php do_action( 'woocommerce_before_checkout_form', $checkout ); ?>
</div>

<!-- Step: Login -->


<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( $checkout_url ); ?>" enctype="multipart/form-data">

<?php foreach( $steps as $key => $step ) {
    echo '<!-- Step: '.$step['title'].' -->'; 
	echo '<div class="nb-step-item '.$step['class'].'">';
    if ( isset($step['sections'] ) ) {
        foreach ( $step['sections'] as $section ) {
            echo '<div class="nb-step-item-inner nb-step-item-'.$section.'">';

            do_action('nb_step_content_' . $section);

            echo '</div>';
        }
    } else {
        do_action('nb_step_content_' . $key);
    }

    echo '</div>';
} ?>
</form>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
</div>



<!-- The steps buttons -->
<!-- <?php
$buttons_class = apply_filters( 'wmsc_buttons_class', 'button alt' );
$wrapper_class = apply_filters( 'wmsc_buttons_wrapper_class', 'wpmc-nav-wrapper' );
$back_to_cart  = ( isset( $options['show_back_to_cart_button'] ) && $options['show_back_to_cart_button'] ) ? true : false;
if ( ! $back_to_cart ) {
    $wrapper_class .= ' wpmc-no-back-to-cart';
}

?>

<div class="<?php echo $wrapper_class; // phpcs:ignore ?>">
    <?php if ( $back_to_cart ) : ?>
        <button data-href="<?php echo wc_get_cart_url(); ?>" id="wpmc-back-to-cart" class="<?php echo $buttons_class; // phpcs:ignore ?>" type="button"><?php echo $options['t_back_to_cart']; // phpcs:ignore ?></button>
    <?php endif; ?>
    <button id="wpmc-prev" class="<?php echo $buttons_class; // phpcs:ignore ?> button-inactive wpmc-nav-button" type="button"><?php echo $options['t_previous']; // phpcs:ignore ?></button>
    <?php if ( $show_login_step ) : ?>
        <button id="wpmc-next" class="<?php echo $buttons_class; // phpcs:ignore ?> button-active wpmc-nav-button" type="button"><?php echo $options['t_next']; // phpcs:ignore ?></button>
        <button id="wpmc-skip-login" class="<?php echo $buttons_class; // phpcs:ignore ?> button-active current wpmc-nav-button" type="button"><?php echo $options['t_skip_login']; // phpcs:ignore ?></button>
    <?php else : ?>
        <button id="wpmc-next" class="<?php echo $buttons_class; // phpcs:ignore ?> button-active current wpmc-nav-button" type="button"><?php echo $options['t_next']; // phpcs:ignore ?></button>
    <?php endif; ?>
</div>

<div style="clear: both;"></div> -->
<!-- EndThe steps buttons -->

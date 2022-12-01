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

$i  = 0;

include dirname(__FILE__) . '/nb-step-checkout/functions.php';

$steps = array(
    "shipping" => array(
        "title" => "Shipping",
        "class" => "nb-step-shipping",
        "show"  => true,
    ),
    "billing" => array(
        "title" => "Billing",
        "class" => "nb-step-billing",
        "show"  => false,
    ),
    "payment" => array(
        "title" => "Payment",
        "class" => "nb-step-payment",
        "show"  => true,
    ),
    "checkout" => array(
        "title" => "Check Out",
        "class" => "nb-step-checkout",
        "show"  => true
    ),
);
$current_step_title = 'shipping';

?>

<link rel="stylesheet" type="text/css" href="<?php echo get_stylesheet_directory_uri(). '/woocommerce/checkout/nb-step-checkout/assets/css/style.css'; ?>">

<div class="nb-tabs-wrapper">
    <div class="nb-tabs-list" data-current-title="<?php echo $current_step_title; ?>">
    <?php
    $total_step = count($steps);
    foreach ( $steps as $_id => $_step ) {
        if($_step['show']) {
            $class = $i == 0 ? ' current' : '';
            ?>
            <div class="nb-tab-item<?php echo $class; ?> nb-<?php echo $_id; ?>" data-step-title="<?php echo $_id; ?>">
                <div class="nb-tab-number"><?php echo $i = $i + 1; ?></div>
                <div class="nb-tab-text"><?php echo $_step['title']; ?></div>   
            </div>
            <?php
            if($i < $total_step) {
                echo '<div class="nb-tab-line"></div>';
            }
        } else {
            $total_step -= 1;
        }
    } ?>
    </div>
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
        do_action('nb_step_content_' . $key);
        echo '</div>';
    } ?>
    </form>

    <?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
</div>



<!-- The steps buttons -->

<div class="nb-footer-action wc-proceed-to-checkout" style="max-width: 962px; margin: 20px auto 20px;">
    <div class="row nb-action">
        <div class="col-sm-6">
            <div class="row">
                <div class="col-sm-6">
                    <a id="nb-prev" class="button nb-btn-light" style="cursor: pointer;">
                        <span>PREVIOUS</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-sm-6 nb-action-right">
            <div class="row">
                <div class="col-sm-6">
                    <a class="checkout-button button btn-generate-quotation nb-btn-light current" style="cursor: pointer;">
                        <span>Quotation</span>
                    </a>
                </div>
                <div class="col-sm-6">
                    <a id="nb-next" class="button nb-btn-success current" style="cursor: pointer;">
                        <span>CHECK OUT</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div style="clear: both;"></div>
<!-- EndThe steps buttons -->
<script src="<?php echo get_stylesheet_directory_uri(). '/woocommerce/checkout/nb-step-checkout/assets/js/custom.js'; ?>"></script> 
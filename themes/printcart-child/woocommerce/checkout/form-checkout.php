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
    <div class="nb-tabs-list nb-<?php echo $number_of_steps; ?>-tabs" data-current-title="<?php echo $current_step_title; ?>">
    <?php
    foreach ( $steps as $_id => $_step ) :
        $class = $i == 0 ? ' current' : '';
        ?>
        <div class="nb-tab-item<?php echo $class; ?> nb-<?php echo $_id; ?>" data-step-title="<?php echo $_id; ?>">
            <div class="nb-tab-number"><?php echo $i = $i + 1; ?></div>
            <div class="nb-tab-text"><?php echo $_step['title']; ?></div>   
        </div>
        <?php
        if($i < count($steps)) {
            echo '<div class="nb-tab-line"></div>';
        }
        ?>
    <?php endforeach; ?>
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


<div class="row nb-footer-action wc-proceed-to-checkout">
    <div class="col-md-6">
        <a id="nb-prev" class="checkout-button button nb-button-check-out hidden" style="cursor: pointer;">
            <span>PREVIOUS</span>
        </a>
    </div>
    <div class="col-md-6">
        <div class="row nb-footer-action-right">
            <div class="col-md-6 nb-col-6">
                <a class="checkout-button button alt wc-forward bt-5 btn-generate-quotation" style="cursor: pointer;">
                    <span>Quotation</span>
                </a>
            </div>
            <div class="col-md-6 nb-col-6">
                <a id="nb-next" class="checkout-button button nb-button-check-out" style="cursor: pointer;">
                    <span>Check out</span>
                </a>
            </div> 
        </div>
    </div> 
</div>

<div style="clear: both;"></div>
<!-- EndThe steps buttons -->
<script src="<?php echo get_stylesheet_directory_uri(). '/woocommerce/checkout/nb-step-checkout/assets/js/custom.js'; ?>"></script> 
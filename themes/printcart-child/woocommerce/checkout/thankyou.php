<?php
/**
 * Thankyou page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/thankyou.php.
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

defined( 'ABSPATH' ) || exit;

if( $order->get_date_paid() || $order->get_payment_method() == 'cod') {
	$i  = 0;

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

	<div class="nb-tabs-wrapper nb-tabs-checkout-thankyou">
	    <div class="nb-tabs-list" data-current-title="<?php echo $current_step_title; ?>">
	    <?php
	    foreach ( $steps as $_id => $_step ) :
	        $class = $_id == 'checkout' ? ' current' : '';
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

	<!-- Step: Login -->
	    
	        <!-- Step: checkout -->
	    	<div class="nb-step-item nb-step-checkout current">
		        <div class="woocommerce-order">

					<?php if ( $order ) :

						do_action( 'woocommerce_before_thankyou', $order->get_id() ); ?>

						<?php if ( $order->has_status( 'failed' ) ) : ?>

							<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed"><?php esc_html_e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce' ); ?></p>

							<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions">
								<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay"><?php esc_html_e( 'Pay', 'woocommerce' ); ?></a>
								<?php if ( is_user_logged_in() ) : ?>
									<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button pay"><?php esc_html_e( 'My account', 'woocommerce' ); ?></a>
								<?php endif; ?>
							</p>

						<?php else : ?>
							<div class="nb-thankyou-image-mobile">
								<img src="<?php echo esc_attr(CUSTOM_BOTAKSIGN_URL.'assets/images/gif4.gif'); ?>" alt="">
							</div>
							<div class="nb-step-checkout-thankyou">
								<div class="row">
									<div class="col-md-7 nb-checkout-thankyou-left">
										<div class="nb-checkout-success">
											<span class="nb-checkout-icon">
												<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
												  <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
												</svg>
											</span>
											<span class="nb-checkout-icon mobile">
												<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-square-fill" viewBox="0 0 16 16">
													<path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm10.03 4.97a.75.75 0 0 1 .011 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.75.75 0 0 1 1.08-.022z"/>
												</svg>
											</span>
											<span class="nb-checkout-title"><?php echo esc_html__( 'Your order was successful.', 'woocommerce' ) ?></span>
										</div>
										<p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received"><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', esc_html__( 'Thank you ordering. We’ve sent you an email with the order information.', 'woocommerce' ), $order ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
										<a href="<?php echo home_url(); ?>" class="nb-checkout-go-home">
											Home
										</a>
									</div>
									<div class="col-md-5 nb-checkout-thankyou-right">
										<img style="width: 90%; height: auto" src="<?php echo esc_attr(CUSTOM_BOTAKSIGN_URL.'assets/images/gif4.gif'); ?>" alt="">
									</div>
								</div>
							</div>

						<?php endif; ?>

					<?php else : ?>
						<div class="nb-checkout-success">
							<span class="nb-checkout-icon">
								<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
								  <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
								</svg>
							</span>
							<span class="nb-checkout-title"><?php esc_html__( 'Your order was successful.', 'woocommerce' ) ?></span>
						</div>
						<p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received"><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', esc_html__( 'Thank you ordering. We’ve sent you an email with the order information.', 'woocommerce' ), null ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
						<a href="<?php echo home_url(); ?>" class="nb-checkout-go-home">
							Home
						</a>

					<?php endif; ?>

				</div>

	        </div>

	</div>
<?php
} else {
	if($order->get_payment_method() == 'omise_paynow') {
		do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() );
	}
}


<?php

// Add the content functions to the steps.
add_action( 'nb_step_content_login', 'nb_step_content_login', 10 );
add_action( 'nb_step_content_shipping', 'nb_step_content_shipping', 10 );
add_action( 'nb_step_content_billing', 'nb_step_content_billing', 10 );
add_action( 'nb_step_content_payment', 'nb_step_content_payment', 10 );

if ( ! function_exists( 'nb_step_content_shipping' ) ) {

	/**
	 * The content of the Shipping step.
	 */
	function nb_step_content_shipping() {
		$packages = WC()->shipping()->get_packages();
		$first    = true;
		foreach ( $packages as $i => $package ) {

			$chosen_method = isset( WC()->session->chosen_shipping_methods[ $i ] ) ? WC()->session->chosen_shipping_methods[ $i ] : '';

			$available_methods = $package['rates'];
			$index = $i;

			if ( $available_methods ) { ?>
				<div id="nb_shipping_method" class="row nb-shipping-methods">
					<?php foreach ( $available_methods as $method ) : ?>
						<div class="col-md-6 nb-shipping-method">
							<div class="shipping-method <?php echo $method->id == $chosen_method ? 'active' : ''; ?>" data-shipping-method="<?php echo esc_attr($method->get_label()); ?>">

							<?php
								printf( '<input type="radio" hidden name="shipping_method[%1$d]" data-index="%1$d" id="shipping_method_%1$d_%2$s" value="%3$s" %4$s />', $index, esc_attr( sanitize_title( $method->id ) ), esc_attr( $method->id ), checked( $method->id, $chosen_method, false ) ); // WPCS: XSS ok.
								printf( '<label class="shipping-method-title" for="shipping_method_%1$s_%2$s">%3$s</label>', $index, esc_attr( sanitize_title( $method->id ) ), nb_cart_totals_shipping_method_label( $method ) ); // 
								do_action( 'woocommerce_after_shipping_rate', $method, $index );
							?>

							</div>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="form-row place-order">
					<div class="row nb-shipping-order-details">
						<div class="col-md-6 nb-col-6"></div>
						<div class="col-md-6 nb-col-6">
							<div class="nb-delivery-detail">
								<?php botak_show_production_time(); ?>
							</div>
							<div class="nb-total-price">
								<div class="nb-title">
									TOTAL
								</div>
								<div class="nb-price">
									<?php echo WC()->cart->get_total(); ?>
								</div>
							</div>
						</div>				
					</div>
					
				</div>
			<?php 
			}

			$first = false;
		}
	}
}

if ( ! function_exists( 'nb_step_content_payment' ) ) {

	/**
	 * The content of the Order Payment step.
	 */
	function nb_step_content_payment() {
		if ( WC()->cart->needs_payment() ) {
			$available_gateways = WC()->payment_gateways()->get_available_payment_gateways();
			WC()->payment_gateways()->set_current_gateway( $available_gateways );
		} else {
			$available_gateways = array();
		}

		$checkout = WC()->checkout();

		?>
		<div class="nb-woocommerce-checkout-payment">
			<?php if ( WC()->cart->needs_payment() ) : ?>
				<div class="row wc_payment_methods payment_methods methods">
					<?php
					if ( ! empty( $available_gateways ) ) {
						foreach ( $available_gateways as $gateway ) {
							?>
							<div class="col-md-6 wc_payment_method payment_method_<?php echo esc_attr( $gateway->id );   ?>">
								<div class="wc_payment_method_wrap<?php echo $gateway->chosen ? ' active' : '';  ?>">
									<input hidden id="payment_method_<?php echo esc_attr( $gateway->id ); ?>" type="radio" class="input-radio" name="payment_method" value="<?php echo esc_attr( $gateway->id ); ?>" data-order_button_text="<?php echo esc_attr( $gateway->order_button_text ); ?>" <?php checked( $gateway->chosen, true ); ?> />

									<label for="payment_method_<?php echo esc_attr( $gateway->id ); ?>">
										<img src="<?php echo get_stylesheet_directory_uri(). '/woocommerce/checkout/nb-step-checkout/assets/logo/' . $gateway->id . '.png'; ?>">
									</label>
								</div>
							</div>
							<?php
						}
					} 
					?>
				</div>
			<?php endif; ?>
			<?php wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' ); ?>
		</div>
		<?php
	}
}

if ( ! function_exists( 'nb_step_content_billing' ) ) {

	/**
	 * The content of the Billing step.
	 */
	function nb_step_content_billing() {
		echo '<div class="nb-billing-customer-details">';
		do_action( 'woocommerce_checkout_before_customer_details' );
		do_action( 'woocommerce_checkout_billing' );
		echo '</div>';
		do_action( 'woocommerce_checkout_shipping' );
		do_action( 'woocommerce_checkout_after_customer_details' );
	}
}

if ( ! function_exists( 'nb_step_content_login' ) ) {

	/**
	 * The content for the Login step.
	 *
	 * @param object $checkout The Checkout object from the WooCommerce plugin.
	 * @param bool   $stop_at_login If the user should be logged in in order to checkout.
	 */
	function nb_step_content_login( $checkout, $stop_at_login ) { ?> 
		<div class="nb-step-item nb-step-login">
			<div id="checkout_login" class="woocommerce_checkout_login wp-multi-step-checkout-step">
				<?php
				woocommerce_login_form(
					array(
						'message'  => apply_filters( 'woocommerce_checkout_logged_in_message', __( 'If you have shopped with us before, please enter your details in the boxes below. If you are a new customer, please proceed to the Billing &amp; Shipping section.', 'wp-multi-step-checkout' ) ),
						'redirect' => wc_get_page_permalink( 'checkout' ),
						'hidden'   => false,
					)
				);
				?>
			</div>
				<?php
				if ( $stop_at_login ) {
					echo apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>
		</div>
	<?php }
}

function nb_cart_totals_shipping_method_label( $method ) {
	$label     = '<div class="nb-shipping-title">'. $method->get_label() . '</div>';
	$label 	   = str_replace( '(', '<br>(', $label);
	$has_cost  = 0 < $method->cost;
	$hide_cost = ! $has_cost && in_array( $method->get_method_id(), array( 'free_shipping', 'local_pickup' ), true );

	if ( $has_cost && ! $hide_cost ) {
		if ( WC()->cart->display_prices_including_tax() ) {
			$label .= ' <div class="nb-price">(' . wc_price( $method->cost + $method->get_shipping_tax() ) . ')</div>';
			if ( $method->get_shipping_tax() > 0 && ! wc_prices_include_tax() ) {
				$label .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
			}
		} else {
			$label .= ' <div class="nb-price">(' . wc_price( $method->cost ) . ')</div>';
			if ( $method->get_shipping_tax() > 0 && wc_prices_include_tax() ) {
				$label .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
			}
		}
	}

	return apply_filters( 'woocommerce_cart_shipping_method_full_label', $label, $method );
}


<?php
/**
 * Template Name: Sign in
 * 
 */
get_header();

wp_enqueue_style('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css', array(), NBT_VER);

wp_enqueue_style('nb-custom-style', get_stylesheet_directory_uri().'/css/nb-custom-style.css', array(), '1.0.0');

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if (function_exists('yoast_breadcrumb')) { ?>
    <div class="nb-page-title-wrap single-breadcrum">
        <div class="container">
            <?php yoast_breadcrumb('<nav class="woocommerce-breadcrumb" itemprop="breadcrumb">', '</nav>'); ?>
        </div>
    </div>
<?php } ?>
<div class="container">

	<div id="primary" class="content-area page-full-width">

        <main id="main" class="site-main" role="main">

			<?php if ( 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) ) : ?>

			<div class="entry-content nb-form-login-wrap nb-sign-in" id="customer_login">

				<div class="woocommerce">

					<?php wc_print_notices(); ?>

					<div class="woocommerce-notices-wrapper"></div>

					<div class="row">
						<div class="col-md-5 nb-login-left">

							<div class="nb-form-login">

								<div class="nb-signin-title"><?php esc_html_e( 'Sign In', 'woocommerce' ); ?></div>
								<p>Continue where you left off</p>

								<form class="woocommerce-form woocommerce-form-login login" method="post">

									<?php do_action( 'woocommerce_login_form_start' ); ?>

									<div class="form-group">
<!-- 										<label for="username"><?php esc_html_e( 'Username or email address', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label> -->
										<input type="text" class="form-control" name="username" id="username" placeholder="<?php esc_html_e( 'Username or email', 'woocommerce' ); ?>" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
									</div>
									<div class="form-group">
										<!-- <label for="password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label> -->
										<input class="form-control" type="password" placeholder="<?php esc_html_e( 'Password', 'woocommerce' ); ?>" name="password" id="password" autocomplete="current-password" />
									</div>

									<?php do_action( 'woocommerce_login_form' ); ?>
									<div class="form-check mb-2">
									    <input class="form-check-input" name="rememberme" type="checkbox" id="rememberme" value="forever" />
									    <label class="form-check-label" for="exampleCheck1"><?php esc_html_e( 'Remember me', 'woocommerce' ); ?></label>
									 </div>
									<p class="form-row">
										<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
										<input type="hidden" name="redirect" value="<?php echo esc_attr(wc_get_page_permalink( 'myaccount' )); ?>">
										<button type="submit" class="woocommerce-button btn btn-success w-100" name="login" value="<?php esc_attr_e( 'Sign in', 'woocommerce' ); ?>"><?php esc_html_e( 'Sign in', 'woocommerce' ); ?></button>
									</p>
									<p class="woocommerce-LostPassword lost_password">
										<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'woocommerce' ); ?></a>
									</p>

								</form>

							</div>
						</div>
						<div class="col-md-2 nb-login-middle">
							<div class="nb-social-log">
								<span>Or</span>
							</div>
						</div>
						<div class="col-md-5 nb-login-right">
							<div class="nb-social-login">
						        <div class="nb-social-login">
									<?php if( class_exists('NextendSocialLogin') ) { ?>
										<div class="nb-log-with-social">
											<?php do_action( 'woocommerce_register_form_end' ); ?>
										</div>
									<?php } ?>
								</div>
						        <div class="nb-sign-up-now">
						        	<span>Don’t have account yet? </span><a href="<?php echo esc_url(home_url().'/sign-up'); ?>">Sign up</a>
						        </div>
							</div>
						</div>
					</div>

				</div>

			</div>
			<?php endif; ?>
		</main>
	</div>
</div>
<?php

get_footer();

?>

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
						<div class="col-md-5">

							<div class="nb-form-login">

								<h2><?php esc_html_e( 'Login', 'woocommerce' ); ?></h2>

								<form class="woocommerce-form woocommerce-form-login login" method="post">

									<?php do_action( 'woocommerce_login_form_start' ); ?>

									<div class="form-group">
										<label for="username"><?php esc_html_e( 'Username or email address', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
										<input type="text" class="form-control" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
									</div>
									<div class="form-group">
										<label for="password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
										<input class="form-control" type="password" name="password" id="password" autocomplete="current-password" />
									</div>

									<?php do_action( 'woocommerce_login_form' ); ?>
									<div class="form-check mb-2">
									    <input class="form-check-input" name="rememberme" type="checkbox" id="rememberme" value="forever" />
									    <label class="form-check-label" for="exampleCheck1"><?php esc_html_e( 'Remember me', 'woocommerce' ); ?></label>
									 </div>
									<p class="form-row">
										<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
										<button type="submit" class="woocommerce-button btn btn-success w-100" name="login" value="<?php esc_attr_e( 'Log in', 'woocommerce' ); ?>"><?php esc_html_e( 'Log in', 'woocommerce' ); ?></button>
									</p>
									<p class="woocommerce-LostPassword lost_password">
										<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'woocommerce' ); ?></a>
									</p>

									<?php if( class_exists('NextendSocialLogin') ) { ?>
										<div class="log-with-social">
											<?php do_action( 'woocommerce_register_form_end' ); ?>
										</div>
									<?php } ?>

								</form>

							</div>
						</div>
						<div class="col-md-2">
							<div class="nb-social-log">
								<span>Or</span>
							</div>
						</div>
						<div class="col-md-5">
							<div class="nb-social-login">
								<div id="nsl-custom-login-form-1">
								    <div
								      class="nsl-container nsl-container-block nsl-container-embedded-login-layout-below"
								      data-align="left"
								      style="display: flex;justify-content: center;"
								    >
								      <div class="nsl-container-buttons">
								        <a
								          href="http://kita.loc/wp-login.php?loginSocial=facebook&amp;redirect=http%3A%2F%2Fkita.loc%2F"
								          rel="nofollow"
								          aria-label="Continue with Facebook"
								          data-plugin="nsl"
								          data-action="connect"
								          data-provider="facebook"
								          data-popupwidth="475"
								          data-popupheight="175"
								        >
								          <div
								            class="nsl-button nsl-button-default nsl-button-facebook"
								            data-skin="light"
								            style="background-color:#fff;"
								          >
								            <div class="nsl-button-svg-container">
								              <svg
								                xmlns="http://www.w3.org/2000/svg"
								                viewBox="0 0 1365.3 1365.3"
								                height="1365.3"
								                width="1365.3"
								              >
								                <path
								                  d="M1365.3 682.7A682.7 682.7 0 10576 1357V880H402.7V682.7H576V532.3c0-171.1 102-265.6 257.9-265.6 74.6 0 152.8 13.3 152.8 13.3v168h-86.1c-84.8 0-111.3 52.6-111.3 106.6v128h189.4L948.4 880h-159v477a682.8 682.8 0 00576-674.3"
								                  fill="#1877f2"
								                ></path>
								                <path
								                  d="M948.4 880l30.3-197.3H789.3v-128c0-54 26.5-106.7 111.3-106.7h86V280s-78-13.3-152.7-13.3c-156 0-257.9 94.5-257.9 265.6v150.4H402.7V880H576v477a687.8 687.8 0 00213.3 0V880h159.1"
								                  fill="#fff"
								                ></path>
								              </svg>
								            </div>
								            <div class="nsl-button-label-container">Continue with Facebook</div>
								          </div>
								        </a>
								        <a
								          href="http://kita.loc/wp-login.php?loginSocial=google&amp;redirect=http%3A%2F%2Fkita.loc%2F"
								          rel="nofollow"
								          aria-label="Continue with Google"
								          data-plugin="nsl"
								          data-action="connect"
								          data-provider="google"
								          data-popupwidth="600"
								          data-popupheight="600"
								        >
								          <div
								            class="nsl-button nsl-button-default nsl-button-google"
								            data-skin="light"
								            style="background-color:#fff;"
								          >
								            <div class="nsl-button-svg-container">
								              <svg xmlns="http://www.w3.org/2000/svg">
								                <g fill="none" fill-rule="evenodd">
								                  <path
								                    fill="#4285F4"
								                    fill-rule="nonzero"
								                    d="M20.64 12.2045c0-.6381-.0573-1.2518-.1636-1.8409H12v3.4814h4.8436c-.2086 1.125-.8427 2.0782-1.7959 2.7164v2.2581h2.9087c1.7018-1.5668 2.6836-3.874 2.6836-6.615z"
								                  ></path>
								                  <path
								                    fill="#34A853"
								                    fill-rule="nonzero"
								                    d="M12 21c2.43 0 4.4673-.806 5.9564-2.1805l-2.9087-2.2581c-.8059.54-1.8368.859-3.0477.859-2.344 0-4.3282-1.5831-5.036-3.7104H3.9574v2.3318C5.4382 18.9832 8.4818 21 12 21z"
								                  ></path>
								                  <path
								                    fill="#FBBC05"
								                    fill-rule="nonzero"
								                    d="M6.964 13.71c-.18-.54-.2822-1.1168-.2822-1.71s.1023-1.17.2823-1.71V7.9582H3.9573A8.9965 8.9965 0 0 0 3 12c0 1.4523.3477 2.8268.9573 4.0418L6.964 13.71z"
								                  ></path>
								                  <path
								                    fill="#EA4335"
								                    fill-rule="nonzero"
								                    d="M12 6.5795c1.3214 0 2.5077.4541 3.4405 1.346l2.5813-2.5814C16.4632 3.8918 14.426 3 12 3 8.4818 3 5.4382 5.0168 3.9573 7.9582L6.964 10.29C7.6718 8.1627 9.6559 6.5795 12 6.5795z"
								                  ></path>
								                  <path d="M3 3h18v18H3z"></path>
								                </g>
								              </svg>
								            </div>
								            <div class="nsl-button-label-container">Continue with Google</div>
								          </div>
								        </a>
								        <div class="nb-sign-up-now">
								        	<span>Don't have an account </span><a href="<?php echo esc_url(home_url().'sign-up'); ?>">Sign in now</a>
								        </div>
								      </div>
								    </div>
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

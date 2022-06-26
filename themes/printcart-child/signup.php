<?php
/**
 * Template Name: Sign up
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

			<div class="entry-content nb-form-login-wrap nb-sign-up" id="customer_login">

				<div class="woocommerce">

					<?php wc_print_notices(); ?>

					<div class="woocommerce-notices-wrapper"></div>

					<div class="row">
						<div class="col-md-12">

							<div class="nb-form-login">

								<h2><?php esc_html_e( 'Sign Up!', 'woocommerce' ); ?></h2>
								<p>Create a new account with us!</p>

								<?php echo do_shortcode('[erforms id="11461"]'); ?>

							</div>
						</div>
						<div class="col md-6">
							<div class="nb-social-login"></div>
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

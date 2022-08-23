<?php
/**
 * Login Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-login.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<?php wc_print_notices(); ?>

<?php do_action( 'woocommerce_before_customer_login_form' ); 
$login_wrap_class = 'custom-login-wrap';
if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) {
	$login_wrap_class = 'custom-login-wrap has-register-form';
}

wp_enqueue_style('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css', array(), NBT_VER);

wp_enqueue_style('nb-custom-style', get_stylesheet_directory_uri().'/css/nb-custom-style.css', array(), '1.0.0');
?>

<div class="nb-form-login-wrap nb-sign-in <?php echo esc_attr($login_wrap_class);?>">
	<div class="row">
		<div class="col-md-5 nb-login-left">

			<div class="nb-form-login">

				<div class="nb-signin-title"><?php esc_html_e( 'Sign In', 'woocommerce' ); ?></div>
				<p class="nb-signin-desc">Continue where you left off</p>
				<form class="woocommerce-form woocommerce-form-login login" method="post">

					<?php do_action( 'woocommerce_login_form_start' ); ?>

					<div class="form-group">
						<!-- <label for="username"><?php esc_html_e( 'Username or email address', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label> -->
						<input type="text" class="form-control" name="username" placeholder="<?php esc_html_e( 'Username or email', 'woocommerce' ); ?>" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
					</div>
					<div class="form-group">
						<!-- <label for="password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label> -->
						<input class="form-control" type="password" placeholder="<?php esc_html_e( 'Password', 'woocommerce' ); ?>" name="password" id="password" autocomplete="current-password" />
					</div>

					<?php do_action( 'woocommerce_login_form' ); ?>
					<div class="form-check mb-4">
					    <input class="form-check-input" name="rememberme" type="checkbox" id="rememberme" value="forever" />
					    <label class="form-check-label" for="exampleCheck1"><?php esc_html_e( 'Remember me', 'woocommerce' ); ?></label>
					</div>
					<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
					<input type="hidden" name="redirect" value="<?php echo esc_attr(wc_get_page_permalink( 'myaccount' )); ?>">
					<button type="submit" class="woocommerce-button btn btn-success w-100" name="login" value="<?php esc_attr_e( 'Sign in', 'woocommerce' ); ?>"><?php esc_html_e( 'Sign in', 'woocommerce' ); ?></button>
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

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>

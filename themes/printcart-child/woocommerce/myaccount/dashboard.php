<?php
/**
 * My Account Dashboard
 *
 * Shows the first intro screen on the account dashboard.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/dashboard.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce/Templates
 * @version     2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<div class="nb-dashboard">
	<div class="nb-dashboard-header">
		<div class="row">
			<div class="col-sm-6" style="padding-top: 10px; padding-bottom: 20px;">
				<div class="nb-dashboard-header-title">
					<span class="name">Hello<span class="nb-space"><?php echo esc_html( $current_user->display_name ); ?>!</span>
				</div>
				<div class="nb-dashboard-header-desc">
					It’s good to see you again.
				</div>
			</div>
			<div class="col-sm-6" style="display: flex;align-items: end;">
				<div class="nb-dashboard-thumbnail-wrap">
					<img style="width: 90%; height: auto" src="<?php echo esc_attr(CUSTOM_BOTAKSIGN_URL.'assets/images/gif2.gif'); ?>" alt="">
				</div>
			</div>
		</div>
	</div>

	<div class="nb-dashboard-body">
		<div class="accordion nb-accordion" id="accordionAccout">
			<div class="accordion-item">
				<h2 class="accordion-header" id="headingBilling">
					<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBilling" aria-expanded="true" aria-controls="collapseBilling">
					Billing address
					</button>
				</h2>
				<div id="collapseBilling" class="accordion-collapse collapse" aria-labelledby="headingBilling" data-bs-parent="#accordionAccout">
					<div class="accordion-body">
						<?php woocommerce_account_edit_address('billing'); ?>
					</div>
				</div>
			</div>

			<div class="accordion-item">
				<h2 class="accordion-header" id="headingShipping">
					<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseShipping" aria-expanded="true" aria-controls="collapseShipping">
					Shipping address
					</button>
				</h2>
				<div id="collapseShipping" class="accordion-collapse collapse" aria-labelledby="headingShipping" data-bs-parent="#accordionAccout">
					<div class="accordion-body">
						<?php woocommerce_account_edit_address('shipping'); ?>
					</div>
				</div>
			</div>

			<div class="accordion-item">
				<h2 class="accordion-header" id="headingAccountDetails">
					<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAccountDetails" aria-expanded="true" aria-controls="collapseAccountDetails">
					Account details
					</button>
				</h2>
				<div id="collapseAccountDetails" class="accordion-collapse collapse" aria-labelledby="headingAccountDetails" data-bs-parent="#accordionAccout">
					<div class="accordion-body">
						<?php woocommerce_account_edit_account(); ?>
					</div>
				</div>
			</div>

			<div class="accordion-item">
				<h2 class="accordion-header" id="headingChangePassword">
					<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseChangePassword" aria-expanded="true" aria-controls="collapseChangePassword">
					Change Password
					</button>
				</h2>
				<div id="collapseChangePassword" class="accordion-collapse collapse" aria-labelledby="headingChangePassword" data-bs-parent="#accordionAccout">
					<div class="accordion-body">
						<?php wc_get_template('myaccount/form-change-password.php'); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

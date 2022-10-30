<?php
/**
 * Change password
 *
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_edit_account_form' ); ?>

<form class="woocommerce-EditAccountForm edit-account" action="" method="post" <?php do_action( 'woocommerce_edit_account_form_tag' ); ?> >

	<fieldset>
		<div class="row">
			<div class="col-md-6">
				<p class="form-floating">
					<input type="password" class="form-control nb-input-text" name="password_current" id="password_current" autocomplete="off" />
					<label for="password_current"><?php esc_html_e( 'Current password', 'woocommerce' ); ?></label>
				</p>
			</div>
			<div class="col-md-6"></div>
		</div>
		<div class="row">
			<div class="col-md-6">
				<p class="form-floating">
					<input type="password" class="form-control nb-input-text" name="password_1" id="password_1" autocomplete="off" />
					<label for="password_1"><?php esc_html_e( 'New password', 'woocommerce' ); ?></label>
				</p>
			</div><div class="col-md-6">
				<p class="form-floating">
					<input type="password" class="form-control nb-input-text" name="password_2" id="password_2" autocomplete="off" />
					<label for="password_2"><?php esc_html_e( 'Confirm new password', 'woocommerce' ); ?></label>
				</p>
			</div>
		</div>
	</fieldset>
	<div class="clear"></div>

	<p class="nb-submit-change">
		<?php wp_nonce_field( 'nb_save_account_password', 'save-account-change-password-nonce' ); ?>
		<button type="submit" class="woocommerce-Button button" name="nb_save_account_password" value="<?php esc_attr_e( 'Save changes', 'woocommerce' ); ?>"><?php esc_html_e( 'Update', 'woocommerce' ); ?></button>
		<input type="hidden" name="action" value="nb_save_account_password" />
	</p>

	<?php do_action( 'woocommerce_edit_account_form_end' ); ?>
</form>

<?php do_action( 'woocommerce_after_edit_account_form' ); ?>

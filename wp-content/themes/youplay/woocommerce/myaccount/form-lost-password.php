<?php
/**
 * Lost password form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-lost-password.php.
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
 * @version 3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

wc_print_notices(); ?>

<form method="post" class="lost_reset_password">

    <p><?php echo apply_filters( 'woocommerce_lost_password_message', esc_html__( 'Lost your password? Please enter your username or email address. You will receive a link to create a new password via email.', 'youplay' ) ); ?></p><?php // @codingStandardsIgnoreLine ?>

	<div class="form-row form-row-first">
		<label for="user_login"><?php esc_html_e( 'Username or email', 'youplay' ); ?></label>
		<div class="youplay-input">
			<input class="input-text" type="text" name="user_login" id="user_login" />
		</div>
	</div>

	<div class="clear"></div>

    <?php do_action( 'woocommerce_lostpassword_form' ); ?>

	<div class="form-row">
		<input type="hidden" name="wc_reset_password" value="true" />
		<button type="submit" class="btn btn-default"><?php esc_html_e( 'Reset Password', 'youplay' ); ?></button>
	</div>

	<?php wp_nonce_field( 'lost_password' ); ?>

</form>

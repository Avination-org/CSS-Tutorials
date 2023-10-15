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
 * @package WooCommerce/Templates
 * @version 4.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<?php do_action( 'woocommerce_before_customer_login_form' ); ?>

<div class="c-login" id="customer_login">

	<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) { ?>
		<div class="c-login__header">
			<a href="#" class="c-login__tab-login c-login__tab-login--active js-tab-header"
			   data-tab-class="js-login-form"><?php esc_html_e( 'Login', 'antek' ); ?></a>
			<span class="c-login__title-or"><?php esc_html_e( 'or', 'antek' ); ?></span>
			<a href="#" class="c-login__tab-register c-login__tab-register--not-active js-tab-header"
			   data-tab-class="js-register-form"><?php esc_html_e( 'Create Account', 'antek' ); ?></a>
		</div>
	<?php } ?>

	<div class="c-login__form js-login-form c-login__form--active">
		<form class="c-form" method="post">

			<?php do_action( 'woocommerce_login_form_start' ); ?>

			<p class="c-form__row">
				<label class="c-form__label"
					   for="username"><?php esc_html_e( 'Username or email address', 'antek' ); ?> <span
						class="required">*</span></label>
				<input type="text"
					   class="c-form__input c-form__input--full c-form__input--fill woocommerce-Input woocommerce-Input--text input-text"
					   name="username" id="username" autocomplete="username"
					   value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>"/>
			</p>
			<p class="c-form__row">
				<label class="c-form__label" for="password"><?php esc_html_e( 'Password', 'antek' ); ?> <span
						class="required">*</span></label>
				<input
					class="c-form__input c-form__input--full c-form__input--fill woocommerce-Input woocommerce-Input--text input-text"
					type="password" name="password" id="password" autocomplete="current-password"/>
			</p>

			<?php do_action( 'woocommerce_login_form' ); ?>

			<p class="c-form__row c-form__row--inline">
				<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
				<label class="c-form__label">
					<input class="c-form__checkbox" name="rememberme" type="checkbox" id="rememberme"
						   value="forever"/> <?php esc_html_e( 'Remember me', 'antek' ); ?>
				</label>
				<span class="c-login__lost-password">
					<a class="c-login__lost-password-link"
					   href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'antek' ); ?></a>
				</span>
			</p>

			<p class="c-form__row">
				<button type="submit" class="c-button c-button--default c-button--full woocommerce-Button button"
						name="login"
						value="<?php esc_attr_e( 'Log in', 'antek' ); ?>"><?php esc_html_e( 'Log in', 'antek' ); ?></button>
			</p>


			<?php do_action( 'woocommerce_login_form_end' ); ?>

		</form>
	</div>

	<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) { ?>

		<div class="c-login__form js-register-form">
			<form method="post" class="c-form" <?php do_action( 'woocommerce_register_form_tag' ); ?>>

				<?php do_action( 'woocommerce_register_form_start' ); ?>

				<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>

					<p class="c-form__row">
						<label class="c-form__label"
							   for="reg_username"><?php esc_html_e( 'Username', 'antek' ); ?> <span
								class="required">*</span></label>
						<input type="text"
							   class="c-form__input c-form__input--full c-form__input--fill woocommerce-Input woocommerce-Input--text input-text"
							   name="username" id="reg_username" autocomplete="username"
							   value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>"/>
					</p>

				<?php endif; ?>

				<p class="c-form__row">
					<label class="c-form__label" for="reg_email"><?php esc_html_e( 'Email address', 'antek' ); ?> <span
							class="required">*</span></label>
					<input type="email"
						   class="c-form__input c-form__input--full c-form__input--fill woocommerce-Input woocommerce-Input--text input-text"
						   name="email" id="reg_email" autocomplete="email"
						   value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>"/>
				</p>

				<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>

					<p class="c-form__row">
						<label class="c-form__label"
							   for="reg_password"><?php esc_html_e( 'Password', 'antek' ); ?> <span
								class="required">*</span></label>
						<input type="password"
							   class="c-form__input c-form__input--full c-form__input--fill woocommerce-Input woocommerce-Input--text input-text"
							   name="password" id="reg_password" autocomplete="new-password"/>
					</p>

				<?php else : ?>

					<p class="c-form__row"><?php esc_html_e( 'A password will be sent to your email address.', 'antek' ); ?></p>

				<?php endif; ?>

				<?php do_action( 'woocommerce_register_form' ); ?>

				<p class="c-form__row">
					<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
					<button type="submit" class="c-button c-button--default c-button--full woocommerce-Button button"
							name="register"
							value="<?php esc_attr_e( 'Register', 'antek' ); ?>"><?php esc_html_e( 'Register', 'antek' ); ?></button>
				</p>

				<?php do_action( 'woocommerce_register_form_end' ); ?>

			</form>
		</div>

	<?php } ?>

	<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
</div>

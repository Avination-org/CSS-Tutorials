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
 * @see           https://docs.woocommerce.com/document/template-structure/
 * @author        WooThemes
 * @package       WooCommerce/Templates
 * @version       3.7.0
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="c-order">

	<?php if ( $order ) {

		do_action( 'woocommerce_before_thankyou', $order->get_id() ); ?>

		<?php if ( $order->has_status( 'failed' ) ) { ?>

			<div class="c-order__result">
				<i class="ip-wc-error c-order__result-ico c-order__result-ico--failed"></i>
				<div class="c-order__result-message">
					<?php esc_html_e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'antek' ); ?>
				</div>
				<div class="c-order__result-action">
					<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>"
					   class="c-form__button"><?php esc_html_e( 'Pay', 'antek' ) ?></a>
					<?php if ( is_user_logged_in() ) : ?>
						<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"
						   class="c-form__button"><?php esc_html_e( 'My account', 'antek' ); ?></a>
					<?php endif; ?>
				</div>
			</div>

		<?php } else { ?>

			<div class="c-order__result">
				<i class="ip-wc-success c-order__result-ico c-order__result-ico--success"></i>
				<div class="c-order__result-message">
					<?php echo apply_filters( 'woocommerce_thankyou_order_received_text', esc_html__( 'Thank you. Your order has been received.', 'antek' ), $order ); ?>
				</div>
			</div>

			<ul class="c-order__details">

				<li class="c-order__details-item c-order__details-item--order">
					<?php esc_html_e( 'Order number:', 'antek' ); ?>
					<strong
						class="c-order__details-value"><?php echo ideapark_wrap( $order->get_order_number() ); ?></strong>
				</li>

				<li class="c-order__details-item c-order__details-item--date">
					<?php esc_html_e( 'Date:', 'antek' ); ?>
					<strong
						class="c-order__details-value"><?php echo wc_format_datetime( $order->get_date_created() ); ?></strong>
				</li>

				<?php if ( is_user_logged_in() && $order->get_user_id() === get_current_user_id() && $order->get_billing_email() ) : ?>
					<li class="c-order__details-item c-order__details-item--email">
						<?php esc_html_e( 'Email:', 'antek' ); ?>
						<strong
							class="c-order__details-value"><?php echo ideapark_wrap( $order->get_billing_email() ); ?></strong>
					</li>
				<?php endif; ?>

				<li class="c-order__details-item c-order__details-item--total">
					<?php esc_html_e( 'Total:', 'antek' ); ?>
					<strong
						class="c-order__details-value"><?php echo ideapark_wrap( $order->get_formatted_order_total() ); ?></strong>
				</li>

				<?php if ( $order->get_payment_method_title() ) : ?>
					<li class="c-order__details-item c-order__details-item--payment-method">
						<?php esc_html_e( 'Payment method:', 'antek' ); ?>
						<strong
							class="c-order__details-value"><?php echo wp_kses_post( $order->get_payment_method_title() ); ?></strong>
					</li>
				<?php endif; ?>
			</ul>

		<?php } ?>

		<div class="c-order__info">
			<?php do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() ); ?>
			<?php do_action( 'woocommerce_thankyou', $order->get_id() ); ?>
		</div>

	<?php } else { ?>

		<div class="c-order__result">
			<i class="ip-wc-success c-order__result-ico c-order__result-ico--success"></i>
			<div class="c-order__result-message">
				<?php echo apply_filters( 'woocommerce_thankyou_order_received_text', esc_html__( 'Thank you. Your order has been received.', 'antek' ), null ); ?>
			</div>
		</div>

	<?php } ?>

</div>
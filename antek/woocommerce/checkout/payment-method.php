<?php
/**
 * Output a single payment method
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/payment-method.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce/Templates
 * @version     3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<li class="wc_payment_method payment_method_<?php echo esc_attr( $gateway->id ); ?>">
	<span class="c-cart__payment-methods-wrap">
		<input id="payment_method_<?php echo esc_attr( $gateway->id ); ?>" type="radio"
			   class="c-cart__payment-method-radio input-radio" name="payment_method"
			   value="<?php echo esc_attr( $gateway->id ); ?>" <?php checked( $gateway->chosen, true ); ?> data-order_button_text="<?php echo esc_attr( $gateway->order_button_text ); ?>"/>
	</span>

	<label class="c-cart__payment-methods-title" for="payment_method_<?php echo esc_attr( $gateway->id ); ?>">
		<?php echo ideapark_wrap( $gateway->get_title() ); ?><?php echo ideapark_wrap( $gateway->get_icon() ); ?>
	</label>
	<?php if ( $gateway->has_fields() || $gateway->get_description() ) : ?>
		<div class="c-cart__payment-methods-box payment_box payment_method_<?php echo esc_attr( $gateway->id ); ?>"
		     <?php if ( ! $gateway->chosen ) : /* phpcs:ignore Squiz.ControlStructures.ControlSignature.NewlineAfterOpenBrace */ ?>style="display:none;"<?php endif; /* phpcs:ignore Squiz.ControlStructures.ControlSignature.NewlineAfterOpenBrace */ ?>>
			<?php $gateway->payment_fields(); ?>
		</div>
	<?php endif; ?>
</li>

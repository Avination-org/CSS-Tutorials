<?php
/**
 * Shipping Methods Display
 *
 * In 2.1 we show methods per package. This allows for multiple methods per order if so desired.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-shipping.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see           https://docs.woocommerce.com/document/template-structure/
 * @package       WooCommerce/Templates
 * @version       3.6.0
 */

defined( 'ABSPATH' ) || exit;

$formatted_destination    = isset( $formatted_destination ) ? $formatted_destination : WC()->countries->get_formatted_address( $package['destination'], ', ' );
$has_calculated_shipping  = ! empty( $has_calculated_shipping );
$show_shipping_calculator = ! empty( $show_shipping_calculator );
$calculator_text          = '';
?>
<tr class="woocommerce-shipping-totals shipping">
	<td class="c-cart__totals-td" data-title="<?php echo esc_attr( $package_name ); ?>" colspan="2">
		<div class="c-cart__sub-header"><?php echo wp_kses_post( $package_name ); ?></div>
		<?php if ( $available_methods ) : ?>
			<ul id="shipping_method" class="c-cart__shipping-methods woocommerce-shipping-methods">
				<?php foreach ( $available_methods as $method ) : ?>
					<li class="c-cart__shipping-methods-item">
						<?php
						printf( '<span class="c-cart__shipping-methods-wrap"><input type="radio" name="shipping_method[%1$d]" data-index="%1$d" id="shipping_method_%1$d_%2$s" value="%3$s" class="shipping_method" %4$s /></span>', $index, esc_attr( sanitize_title( $method->id ) ), esc_attr( $method->id ), checked( $method->id, $chosen_method, false ) ); // WPCS: XSS ok.
						printf( '<label class="c-cart__shipping-methods-label" for="shipping_method_%1$s_%2$s">%3$s</label>', $index, esc_attr( sanitize_title( $method->id ) ), wc_cart_totals_shipping_method_label( $method ) ); // WPCS: XSS ok.
						do_action( 'woocommerce_after_shipping_rate', $method, $index );
						?>
					</li>
				<?php endforeach; ?>
			</ul>
			<?php if ( is_cart() ) : ?>
				<div class="c-cart__shipping-destination woocommerce-shipping-destination">
					<?php
					if ( $formatted_destination ) {
						// Translators: $s shipping destination.
						printf( esc_html__( 'Shipping to %s.', 'antek' ) . ' ', '<strong>' . esc_html( $formatted_destination ) . '</strong>' );
						$calculator_text = __( 'Change address', 'antek' );
					} else {
						echo wp_kses_post( apply_filters( 'woocommerce_shipping_estimate_html', __( 'Shipping options will be updated during checkout.', 'antek' ) ) );
					}
					?>
				</div>
			<?php endif; ?>
		<?php
		elseif ( ! $has_calculated_shipping || ! $formatted_destination ) :
			echo wp_kses_post( apply_filters( 'woocommerce_shipping_may_be_available_html', __( 'Enter your address to view shipping options.', 'antek' ) ) );
		elseif ( ! is_cart() ) :
			echo wp_kses_post( apply_filters( 'woocommerce_no_shipping_available_html', __( 'There are no shipping methods available. Please ensure that your address has been entered correctly, or contact us if you need any help.', 'antek' ) ) );
		else :
			// Translators: $s shipping destination.
			echo wp_kses_post( apply_filters( 'woocommerce_cart_no_shipping_available_html', sprintf( esc_html__( 'No shipping options were found for %s.', 'antek' ) . ' ', '<strong>' . esc_html( $formatted_destination ) . '</strong>' ) ) );
			$calculator_text = __( 'Enter a different address', 'antek' );
		endif;
		?>

		<?php if ( $show_package_details ) : ?>
			<?php echo '<p class="woocommerce-shipping-contents"><small>' . esc_html( $package_details ) . '</small></p>'; ?>
		<?php endif; ?>
	</td>
</tr>

<?php if ( ! empty( $show_shipping_calculator ) ) : ?>
	<tr class="shipping-calculator">
		<td data-title="<?php esc_attr_e( 'Shipping', 'antek' ); ?>" colspan="2">
			<?php woocommerce_shipping_calculator( $calculator_text ); ?>
		</td>
	</tr>
<?php endif; ?>


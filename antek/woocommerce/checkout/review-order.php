<?php
/**
 * Review order table
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/review-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see           https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 5.2.0
 */

defined( 'ABSPATH' ) || exit;
?>
<table class="c-cart__totals-table shop_table woocommerce-checkout-review-order-table">
	<thead>
	<tr>
		<th class="c-cart__totals-th product-name"><?php esc_html_e( 'Product', 'antek' ); ?></th>
		<th class="c-cart__totals-th c-cart__totals-th--product-total product-total"><?php esc_html_e( 'Subtotal', 'antek' ); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	do_action( 'woocommerce_review_order_before_cart_contents' );

	foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
		$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

		if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
			?>
			<tr>
				<td colspan="2" class="c-cart__totals-product-space"></td>
			</tr>
			<tr class="c-cart__totals-product <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

				<td class="c-cart__totals-product-name">
					<?php echo apply_filters( 'woocommerce_cart_item_name', method_exists( $_product, 'get_name' ) ? $_product->get_name() : $_product->get_title(), $cart_item, $cart_item_key ) . '&nbsp;'; ?>
					<?php echo apply_filters( 'woocommerce_checkout_cart_item_quantity', ' <strong class="c-cart__totals-product-quantity product-quantity">' . sprintf( '&times;&nbsp;%s', $cart_item['quantity'] ) . '</strong>', $cart_item, $cart_item_key ); ?>
					<?php echo function_exists( 'wc_get_formatted_cart_item_data' ) ? wc_get_formatted_cart_item_data( $cart_item ) : WC()->cart->get_item_data( $cart_item ); ?>
				</td>
				<td class="c-cart__totals-price">
					<?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); ?>
				</td>
			</tr>
			<?php
		}
	}

	do_action( 'woocommerce_review_order_after_cart_contents' );
	?>
	</tbody>
	<tfoot>
	<?php if ( ideapark_mod( 'show_subtotal' ) ) { ?>
		<tr>
			<td colspan="2" class="c-cart__totals-space c-cart__totals-space--hr"></td>
		</tr>
		<tr class="cart-subtotal">
			<th class="c-cart__sub-header"><?php esc_html_e( 'Subtotal', 'antek' ); ?></th>
			<td class="c-cart__totals-price"><?php wc_cart_totals_subtotal_html(); ?></td>
		</tr>
	<?php } ?>

	<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
		<tr>
			<td colspan="2" class="c-cart__totals-space"></td>
		</tr>
		<tr class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
			<th class="c-cart__sub-header"><?php wc_cart_totals_coupon_label( $coupon ); ?></th>
			<td class="c-cart__totals-price"><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
		</tr>
	<?php endforeach; ?>

	<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>

		<tr>
			<td colspan="2" class="c-cart__totals-space"></td>
		</tr>

		<?php do_action( 'woocommerce_review_order_before_shipping' ); ?>

		<?php wc_cart_totals_shipping_html(); ?>

		<?php do_action( 'woocommerce_review_order_after_shipping' ); ?>

	<?php endif; ?>

	<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
		<tr>
			<td colspan="2" class="c-cart__totals-space"></td>
		</tr>
		<tr class="fee">
			<th class="c-cart__sub-header"><?php echo esc_html( $fee->name ); ?></th>
			<td class="c-cart__totals-price"><?php wc_cart_totals_fee_html( $fee ); ?></td>
		</tr>
	<?php endforeach; ?>

	<?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
		<?php if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
			<?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : ?>
				<tr>
					<td colspan="2" class="c-cart__totals-space"></td>
				</tr>
				<tr class="tax-rate tax-rate-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
					<th class="c-cart__sub-header"><?php echo esc_html( $tax->label ); ?></th>
					<td class="c-cart__totals-price"><?php echo wp_kses_post( $tax->formatted_amount ); ?></td>
				</tr>
			<?php endforeach; ?>
		<?php else : ?>
			<tr>
				<td colspan="2" class="c-cart__totals-space"></td>
			</tr>
			<tr class="tax-total">
				<th class="c-cart__sub-header"><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></th>
				<td class="c-cart__totals-price"><?php wc_cart_totals_taxes_total_html(); ?></td>
			</tr>
		<?php endif; ?>
	<?php endif; ?>

	<?php do_action( 'woocommerce_review_order_before_order_total' ); ?>

	<tr>
		<td colspan="2" class="c-cart__collaterals-hr"></td>
	</tr>

	<tr class="order-total">
		<th class="c-cart__sub-header"><?php esc_html_e( 'Total', 'antek' ); ?></th>
		<td class="c-cart__totals-price c-cart__totals-price--total"
			data-title="<?php esc_attr_e( 'Total', 'antek' ); ?>"><?php wc_cart_totals_order_total_html(); ?></td>
	</tr>

	<tr>
		<td colspan="2" class="c-cart__collaterals-hr"></td>
	</tr>

	<?php do_action( 'woocommerce_review_order_after_order_total' ); ?>

	</tfoot>
</table>

<?php
/**
 * Empty cart page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-empty.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see        https://docs.woocommerce.com/document/template-structure/
 * @package    WooCommerce/Templates
 * @version    3.5.0
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="c-cart-empty">
	<div class="c-cart-empty__image-wrap">
		<?php if ( ideapark_mod( 'cart_empty_image' ) ) { ?>
			<img src="<?php echo esc_url( ideapark_mod( 'cart_empty_image' ) ); ?>"
				 alt="<?php esc_html_e( 'Your cart is currently empty', 'antek' ); ?>" class="c-cart-empty__image"/>
		<?php } else { ?>
			<i class="ip-cart-empty c-cart-empty__svg"></i>
		<?php } ?>
	</div>
	<h1 class="c-cart-empty__header"><?php esc_html_e( 'Your cart is currently empty', 'antek' ); ?></h1>
	<?php if (ideapark_mod( 'cart_empty_button' ) == 'shop') { ?>
		<a class="c-form__button c-cart-empty__backward" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
			<?php echo esc_html( apply_filters( 'woocommerce_return_to_shop_text', __( 'Return to shop', 'woocommerce' ) ) ); ?>
		</a>
	<?php } else { ?>
		<a class="c-form__button c-cart-empty__backward"
		   href="<?php echo esc_url( get_post_type_archive_link( 'catalog' ) ); ?>">
			<?php esc_html_e( 'Return to booking', 'antek' ) ?>
		</a>
	<?php } ?>
</div>
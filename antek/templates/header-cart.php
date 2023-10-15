<?php if ( ideapark_mod( 'header_cart' ) && ideapark_woocommerce_on() ) { ?>
	<div class="c-header__main-row-item c-header__main-row-item--cart">
		<a class="c-header__cart js-cart" href="<?php echo esc_url( wc_get_cart_url() ); ?>">
			<i class="ip-cart c-header__cart-icon"><!-- --></i>
			<?php echo ideapark_cart_info(); ?>
		</a>
	</div>
<?php } ?>
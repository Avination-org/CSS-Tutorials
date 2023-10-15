<?php get_header(); ?>
<?php get_template_part( 'templates/page-header' ); ?>

<?php global $post;
$with_sidebar = ! is_singular( 'product' ) && ideapark_mod( 'shop_sidebar' ) && is_active_sidebar( 'shop-sidebar' ) && ! ( ideapark_woocommerce_on() && ( is_cart() || is_checkout() || is_account_page() ) );
?>

<div
	class="l-section l-section--container <?php ideapark_class( is_singular( 'product' ), 'l-section--margin-120', 'l-section--margin-80' ); ?><?php if ( $with_sidebar ) { ?> l-section--with-sidebar<?php } ?>">
	<?php if ( $with_sidebar ) { ?>
		<div class="c-catalog__filter-show-button">
			<button class="h-cb c-button c-button--default c-button--outline js-sidebar-button" type="button"><i
					class="ip-filter c-catalog__filter-ico"></i><?php esc_html_e( 'Filter', 'antek' ); ?></button>
		</div>
	<?php } ?>
	<?php if ( $with_sidebar ) { ?>
		<div class="l-section__sidebar l-section__sidebar--left l-section__sidebar--popup">
			<?php get_sidebar( 'woocommerce' ); ?>
		</div>
	<?php } ?>
	<div class="l-section__content<?php if ( $with_sidebar ) { ?> l-section__content--with-sidebar<?php } ?>">
		<?php if ( ! is_singular( 'product' ) && woocommerce_product_loop() ) { ?>
			<div class="c-catalog-ordering">
				<div class="c-catalog-ordering__col c-catalog-ordering__col--result">
					<?php woocommerce_result_count(); ?>
				</div>
				<div class="c-catalog-ordering__col c-catalog-ordering__col--ordering">
					<?php woocommerce_catalog_ordering(); ?>
				</div>
			</div>
		<?php } ?>
		<div
			class="c-woocommerce <?php ideapark_class( ideapark_mod( 'sticky_sidebar' ), 'js-sticky-sidebar-nearby' ); ?>">
			<?php woocommerce_content(); ?>
		</div><!-- /.c-woocommerce -->
	</div><!-- /.c-woocommerce -->
</div>

<?php get_footer(); ?>
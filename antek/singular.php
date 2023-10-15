<?php get_header(); ?>
<?php get_template_part( 'templates/page-header' ); ?>

<?php global $post;
$with_sidebar = ideapark_mod( 'sidebar_post' ) && is_active_sidebar( 'post-sidebar' ) && ! ( ideapark_woocommerce_on() && ( is_cart() || is_checkout() || is_account_page() ) );
?>

<?php if ( have_posts() ): ?>
	<?php while ( have_posts() ) : the_post(); ?>
		<div
			class="l-section l-section--container l-section--margin-120<?php if ( $with_sidebar ) { ?> l-section--with-sidebar<?php } ?>">
			<div
				class="l-section__content<?php if ( $with_sidebar ) { ?> l-section__content--with-sidebar<?php ideapark_class( ideapark_mod( 'sticky_sidebar' ), 'js-sticky-sidebar-nearby' ); ?><?php } ?>">
				<?php
				if ( ideapark_woocommerce_on() && ( is_cart() || is_checkout() || is_account_page() ) ) {
					the_content();
				} else {
					get_template_part( 'templates/content' );
				}
				?>
			</div>
			<?php if ( $with_sidebar ) { ?>
				<div class="l-section__sidebar l-section__sidebar--right">
					<?php get_sidebar(); ?>
				</div>
			<?php } ?>
		</div>

	<?php endwhile; ?>
<?php endif; ?>

<?php get_footer(); ?>












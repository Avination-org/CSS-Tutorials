<?php get_header(); ?>
<?php get_template_part( 'templates/page-header' ); ?>
<?php $with_sidebar = ideapark_mod( 'sidebar_blog' ) && is_active_sidebar( 'post-sidebar' ); ?>

<div
	class="c-blog l-section l-section--container l-section--margin-120<?php if ( $with_sidebar ) { ?> l-section--with-sidebar<?php } ?>">
	<div
		class="l-section__content<?php if ( $with_sidebar ) { ?> l-section__content--with-sidebar<?php } ?>">
		<?php if ( have_posts() ): ?>
			<div
				class="c-blog__<?php echo ideapark_mod( 'post_layout' ); ?><?php ideapark_class( ideapark_mod( 'sticky_sidebar' ), 'js-sticky-sidebar-nearby' ); ?>">
				<div
					class="<?php if ( ideapark_mod( 'post_layout' ) == 'grid' ) { ?>c-blog__grid-wrap<?php } else { ?>c-blog__list-wrap<?php } ?>">

					<?php while ( have_posts() ) : the_post(); ?>
						<?php get_template_part( 'templates/content', ideapark_mod( 'post_layout' ) ); ?>
					<?php endwhile; ?>
				</div>
			</div>
			<?php ideapark_corenavi();
		else : ?>
			<div class="c-blog__nothing">
				<div
					class="c-blog__nothing-text"><?php esc_html_e( 'We could not find any results for your search. You can give it another try through the search form below:', 'antek' ); ?>
				</div>
				<div class="c-blog__nothing-search">
					<?php get_search_form(); ?>
				</div>
			</div>
		<?php endif; ?>
	</div>

	<?php if ( $with_sidebar ) { ?>
		<div class="l-section__sidebar l-section__sidebar--right">
			<?php get_sidebar(); ?>
		</div>
	<?php } ?>
</div>

<?php get_footer(); ?>

<?php get_header();
$page_id = apply_filters( 'wpml_object_id', ideapark_mod( '404_page' ), 'any' );
if ( 'publish' != ideapark_post_status( $page_id ) ) {
	$page_id = 0;
}

if ( $page_id ) {
	if ( ideapark_is_elementor_page( $page_id ) ) {
		$page_content = Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $page_id );
	} elseif ( $post = get_post( $page_id ) ) {
		$page_content = apply_filters( 'the_content', $post->post_content );
		$page_content = str_replace( ']]>', ']]&gt;', $page_content );
		$page_content = ideapark_wrap( $page_content, '<div class="entry-content">', '</div>' );
		wp_reset_postdata();
	} else {
		$page_content = '';
	}
	echo ideapark_wrap( $page_content, '<div class="l-section">', '</div>' );
} else {
	get_template_part( 'templates/page-header' ); ?>
	<section class="l-section l-section--container">
		<div class="c-404">
			<div class="c-404__image-wrap">
				<?php if ( ideapark_mod( '404_image' ) ) { ?>
					<img src="<?php echo esc_url( ideapark_mod( '404_image' ) ); ?>"
						 alt="<?php esc_attr_e( 'Oops! That page can’t be found.', 'antek' ); ?>" class="c-404__image"/>
				<?php } else { ?>
					<i class="ip-404 c-404__svg"></i>
				<?php } ?>
			</div>
			<h1 class="c-404__header"><?php esc_html_e( 'Oops! That page can’t be found.', 'antek' ); ?></h1>
			<div
				class="c-404__text"><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try a search?', 'antek' ); ?></div>
			<div class="c-404__search-wrap">
				<?php get_search_form(); ?>
			</div>
		</div>
	</section>
<?php } ?>

<?php get_footer(); ?>

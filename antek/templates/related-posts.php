<?php

if ( $categories = get_the_category( $post->ID ) ) {

	$category_ids = [];

	foreach ( $categories as $individual_category ) {
		$category_ids[] = $individual_category->term_id;
	}

	$args = [
		'category__in'        => $category_ids,
		'post__not_in'        => [ $post->ID ],
		'posts_per_page'      => 2,
		'ignore_sticky_posts' => 1,
		'orderby'             => 'rand',
		'suppress_filters'    => false
	];

	$old_sidebar_blog = ideapark_mod( 'sidebar_blog' );
	$old_post_layout  = ideapark_mod( 'post_layout' );
	ideapark_mod_set_temp( 'sidebar_blog', false );
	ideapark_mod_set_temp( 'post_layout', 'grid' );
	if ( $related_posts = get_posts( $args ) ) { ?>
		<div class="c-post__decor"></div>
		<div class="c-post__related">
			<h2 class="c-post__related-header"><?php esc_html_e( 'You Might Also Like', 'antek' ); ?></h2>
			<div class="c-post__divider"><i class="ip-decor"></i></div>
			<div class="c-post__related-list">
				<?php foreach ( $related_posts as $post ) {
						setup_postdata( $post );
						get_template_part( 'templates/content-grid' );
				}; ?>
			</div>
		</div>
	<?php }
	ideapark_mod_set_temp( 'sidebar_blog', $old_sidebar_blog );
	ideapark_mod_set_temp( 'post_layout', $old_post_layout );
	wp_reset_postdata();
}


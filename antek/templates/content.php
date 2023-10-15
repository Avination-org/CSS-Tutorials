<article id="post-<?php the_ID(); ?>" <?php post_class( 'c-post' ); ?>>

	<?php $is_product = ( ideapark_woocommerce_on() && get_post_type() == 'product' ); ?>
	<?php $is_catalog = ( get_post_type() == 'catalog' ); ?>
	<?php $is_page = ( get_post_type() == 'page' ); ?>

	<?php if ( has_post_thumbnail() ) { ?>
		<div class="c-post__thumb">
			<?php the_post_thumbnail( 'medium_large', [ 'class' => 'c-post__img' ] ); ?>
			<?php if ( ! $is_page && ! ideapark_mod( 'post_hide_category' ) ) { ?>
				<?php if ( $is_product ) {
					$product_categories = [];
					$term_ids           = wc_get_product_term_ids( get_the_ID(), 'product_cat' );
					foreach ( $term_ids as $term_id ) {
						$product_categories[] = get_term_by( 'id', $term_id, 'product_cat' );
					}
				} elseif ( $is_catalog ) {
					$product_categories = wp_get_post_terms( get_the_ID(), 'vehicle_type' );
				} ?>

				<ul class="c-post__categories">
					<li class="c-post__categories-item"><?php ideapark_category( '</li><li class="c-post__categories-item">', ( $is_product || $is_catalog ) && ! empty( $product_categories ) ? $product_categories : null, 'c-post__categories-item-link' ); ?></li>
				</ul>
			<?php } ?>
		</div>
	<?php } ?>

	<?php if ( ! $is_page && ideapark_mod( 'post_title' ) == 'content' ) { ?>
		<h1 class="c-post__title">
			<?php the_title(); ?>
			<?php echo( is_sticky() ? '<i class="c-post__sticky ip-sticky"><!-- --></i>' : '' ); ?>
		</h1>
	<?php } ?>

	<?php if ( ! $is_product && ! $is_catalog && ! $is_page ) { ?>
		<ul class="c-post__meta">
			<?php if ( ! ideapark_mod( 'post_hide_date' ) ) { ?>
				<li class="c-post__meta-item c-post__meta-item--date">
					<i class="ip-cal c-post__meta-icon c-post__meta-icon--date"><!-- --></i>
					<?php the_time( get_option( 'date_format' ) ); ?>
				</li>
			<?php } ?>

			<?php if ( ! ideapark_mod( 'post_hide_author' ) ) { ?>
				<li class="c-post__meta-item c-post__meta-item--author">
					<i class="ip-author c-post__meta-icon c-post__meta-icon--author"><!-- --></i>
					<?php esc_html_e( 'By', 'antek' ); ?> <span
						class="c-post__meta-author"><?php the_author_link(); ?></span>
				</li>
			<?php } ?>

			<?php if ( ! ideapark_mod( 'post_hide_comment' ) ) { ?>
				<?php $comments_count = wp_count_comments( $post->ID ); ?>
				<?php if ( $comments_count->total_comments > 0 ) { ?>
					<li class="c-post__meta-item c-post__meta-item--comment">
						<i class="ip-comment c-post__meta-icon c-post__meta-icon--comment"><!-- --></i>
						<?php echo sprintf( _n( '%s Comment', '%s Comments', $comments_count->total_comments, 'antek' ), $comments_count->total_comments ) ?>
					</li>
				<?php } ?>
			<?php } ?>
		</ul>
	<?php } ?>

	<?php ob_start(); ?>
	<?php the_content( '<span class="c-post__more-button">' . esc_html__( 'Continue Reading', 'antek' ) . '</span>' ); ?>
	<?php $content = trim( ob_get_clean() ); ?>

	<?php ob_start(); ?>

	<?php if ( $content ) { ?>
		<div
			class="c-post__content entry-content <?php ideapark_class( ideapark_mod( 'sidebar_post' ), 'entry-content--sidebar', 'entry-content--fullwidth' ); ?>">
			<?php echo ideapark_wrap( $content ); ?>
		</div>
		<div class="h-clearfix"></div>
	<?php } ?>

	<?php wp_link_pages( [
		'before'   => '<div class="c-post__page-links"><div class="c-post__page-links-title">' . esc_html__( 'Pages:', 'antek' ) . '</div>',
		'after'    => '</div>',
		'pagelink' => '<span>%</span>'
	] ); ?>

	<?php if ( ! $is_page && ( ! ideapark_mod( 'post_hide_share' ) || ! ideapark_mod( 'post_hide_tags' ) ) ) { ?>
		<div class="c-post__bottom">
			<?php if ( has_tag() && ! ideapark_mod( 'post_hide_tags' ) ) { ?>
				<div class="c-post__tags">
					<?php the_tags( "", "" ); ?>
				</div>
			<?php } ?>
			<?php if ( ! ideapark_mod( 'post_hide_share' ) && shortcode_exists( 'ip-post-share' ) ) { ?>
				<div class="c-post__share">
					<?php echo ideapark_shortcode( '[ip-post-share]' ); ?>
				</div>
			<?php } ?>
		</div>
	<?php } ?>
	<?php $page_content = trim( ob_get_clean() ); ?>

	<?php echo ideapark_wrap( $page_content, '<div class="c-post__wrap">', '</div>' ) ?>

	<?php if ( is_single() && ! ideapark_mod( 'post_hide_author' ) ) { ?>
		<?php get_template_part( 'templates/post-author' ); ?>
	<?php } ?>

	<?php if ( is_single() && ! ideapark_mod( 'post_hide_postnav' ) ) { ?>
		<?php ideapark_post_nav(); ?>
	<?php } ?>

	<?php if ( is_single() && ! ideapark_mod( 'post_hide_related' ) ) { ?>
		<?php get_template_part( 'templates/related-posts' ); ?>
	<?php } ?>

	<?php comments_template( '', true ); ?>

</article>


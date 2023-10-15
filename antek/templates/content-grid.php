<article id="post-<?php the_ID(); ?>" <?php post_class( 'c-post-grid' ); ?>>

	<?php $is_product = ( ideapark_woocommerce_on() && get_post_type() == 'product' ); ?>
	<?php $is_catalog = ( get_post_type() == 'catalog' ); ?>
	<?php $is_page = ( get_post_type() == 'page' ); ?>
	<?php $thumbnail_id = get_post_thumbnail_id( $post->ID ); ?>

	<div class="c-post-grid__thumb-wrap">
		<?php if ( $thumbnail_id ) { ?>
			<a href="<?php echo get_permalink() ?>">
				<?php the_post_thumbnail( 'medium_large', [ 'class' => 'c-post-grid__thumb' ] ); ?>
			</a>
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

				<ul class="c-post-grid__categories">
					<li class="c-post-grid__categories-item"><?php ideapark_category( '</li><li class="c-post-grid__categories-item">', ( $is_product || $is_catalog ) && ! empty( $product_categories ) ? $product_categories : null, 'c-post-grid__categories-item-link' ); ?></li>
				</ul>
			<?php } ?>
		<?php } ?>
	</div>

	<div class="c-post-grid__content-wrap">
		<?php if ( ! $is_product && ! $is_catalog && ! $is_page ) { ?>
			<ul class="c-post-grid__meta">
				<?php if ( ! ideapark_mod( 'post_hide_date' ) ) { ?>
					<li class="c-post-grid__meta-item c-post-grid__meta-item--date">
						<?php the_time( get_option( 'date_format' ) ); ?>
					</li>
				<?php } ?>
				<?php if ( ! ideapark_mod( 'post_hide_author' ) ) { ?>
					<li class="c-post-grid__meta-item c-post-grid__meta-item--author">
						<?php esc_html_e( 'By', 'antek' ); ?> <span
							class="c-post-grid__author"><?php the_author_link(); ?></span>
					</li>
				<?php } ?>

				<?php if ( false && ! ideapark_mod( 'post_hide_comment' ) ) { ?>
					<?php $comments_count = wp_count_comments( $post->ID ); ?>
					<?php if ( $comments_count->total_comments > 0 ) { ?>
						<li class="c-post-grid__meta-item c-post-grid__meta-item--comments">
							<?php echo sprintf( _n( '%s Comment', '%s Comments', $comments_count->total_comments, 'antek' ), $comments_count->total_comments ) ?>
						</li>
					<?php } ?>
				<?php } ?>
			</ul>
		<?php } ?>
		<?php echo ideapark_wrap( get_the_title(), '<div class="c-post-grid__title"><a href="' . esc_url( get_permalink() ) . '">', ( is_sticky() ? '<i class="c-post-grid__sticky ip-sticky"><!-- --></i>' : '' ) . '</a></div>' ); ?>
		<?php echo ideapark_wrap( ideapark_truncate( get_the_excerpt(), 100 ), '<div class="c-post-grid__excerpt">', '</div>' ); ?>
	</div>
	<a class="c-post-grid__continue" href="<?php echo get_permalink() ?>">
		<?php if ( $is_product || $is_catalog ) { ?>
			<?php esc_html_e( 'Details', 'antek' ); ?>
		<?php } else { ?>
			<?php esc_html_e( 'Read More', 'antek' ); ?>
		<?php } ?>
		<i class="ip-double-arrow c-post-grid__arrow"></i>
	</a>

</article>
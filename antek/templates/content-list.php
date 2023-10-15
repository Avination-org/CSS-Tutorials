<article id="post-<?php the_ID(); ?>" <?php post_class( 'c-post-list' ); ?>>

	<?php $is_product = ( ideapark_woocommerce_on() && get_post_type() == 'product' ); ?>
	<?php $is_catalog = ( get_post_type() == 'catalog' ); ?>
	<?php $is_page = ( get_post_type() == 'page' ); ?>

	<?php if ( has_post_thumbnail() ) { ?>
		<div class="c-post-list__thumb">
			<a href="<?php echo get_permalink() ?>">
				<?php the_post_thumbnail( 'medium_large', [ 'class' => 'c-post-list__img' ] ); ?>
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

				<ul class="c-post-list__categories">
					<li class="c-post-list__categories-item"><?php ideapark_category( '</li><li class="c-post-list__categories-item">', ( $is_product || $is_catalog ) && ! empty( $product_categories ) ? $product_categories : null, 'c-post-list__categories-item-link' ); ?></li>
				</ul>
			<?php } ?>
		</div>
	<?php } ?>

	<div class="c-post-list__wrap">

		<a class="c-post-list__title-link" href="<?php echo get_permalink(); ?>">
			<h2 class="c-post-list__title">
				<?php the_title(); ?>
				<?php echo( is_sticky() ? '<i class="c-post-list__sticky ip-sticky"><!-- --></i>' : '' ); ?>
			</h2>
		</a>

		<?php if ( ! $is_product && ! $is_catalog && ! $is_page ) { ?>
			<ul class="c-post-list__meta">
				<?php if ( ! ideapark_mod( 'post_hide_date' ) ) { ?>
					<li class="c-post-list__meta-item c-post-list__meta-item--date">
						<i class="ip-cal c-post-list__meta-icon c-post-list__meta-icon--date"><!-- --></i>
						<?php the_time( get_option( 'date_format' ) ); ?>
					</li>
				<?php } ?>

				<?php if ( ! ideapark_mod( 'post_hide_author' ) ) { ?>
					<li class="c-post-list__meta-item c-post-list__meta-item--author">
						<i class="ip-author c-post-list__meta-icon c-post-list__meta-icon--author"><!-- --></i>
						<?php esc_html_e( 'By', 'antek' ); ?> <span
							class="c-post-list__author"><?php the_author_link(); ?></span>
					</li>
				<?php } ?>

				<?php if ( ! ideapark_mod( 'post_hide_comment' ) ) { ?>
					<?php $comments_count = wp_count_comments( $post->ID ); ?>
					<?php if ( $comments_count->total_comments > 0 ) { ?>
						<li class="c-post-list__meta-item c-post-list__meta-item--comment">
							<i class="ip-comment c-post-list__meta-icon c-post-list__meta-icon--comment"><!-- --></i>
							<?php echo sprintf( _n( '%s Comment', '%s Comments', $comments_count->total_comments, 'antek' ), $comments_count->total_comments ) ?>
						</li>
					<?php } ?>
				<?php } ?>
			</ul>
		<?php } ?>



		<?php if ( ! $is_page ) { ?>
			<div class="c-post-list__excerpt">
				<?php if ( empty( $post->post_title ) ) { ?><a href="<?php echo get_permalink() ?>"><?php } ?>
					<?php the_excerpt() ?>
					<?php if ( empty( $post->post_title ) ) { ?></a><?php } ?>
			</div>

			<div class="c-post-list__bottom">
				<a class="c-button c-button--outline c-post-list__continue"
				   href="<?php echo get_permalink(); ?>"><?php if ( $is_product || $is_catalog ) { ?><?php esc_html_e( 'Details', 'antek' ); ?><?php } else { ?><?php esc_html_e( 'Read More', 'antek' ); ?><?php } ?><!--
					--><i class="ip-double-arrow c-button__arrow"></i></a>
			</div>
		<?php } ?>
	</div>

</article>
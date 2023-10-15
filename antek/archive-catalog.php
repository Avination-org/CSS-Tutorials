<?php get_header(); ?>
<?php get_template_part( 'templates/page-header' ); ?>
<?php
global $wp_query;
$is_favorites_list    = isset( $_REQUEST['favorites'] );
$with_sidebar         = ideapark_mod( 'sidebar_catalog' ) && is_active_sidebar( 'catalog-sidebar' ) && ! $is_favorites_list;
$html_block           = '';
$is_bottom_html_block = true;
$is_first_page_only   = true;
$paged                = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
$term_id              = 0;
$page_id              = 0;
$is_taxonomy          = false;
if ( is_tax() ) {
	$term_id = get_queried_object_id();
	$meta    = get_term_meta( $term_id );
	if ( ! empty( $meta['block_description'][0] ) && ( $page_id = apply_filters( 'wpml_object_id', (int) $meta['block_description'][0], 'any' ) ) ) {
		$is_first_page_only   = ! empty( $meta['block_first_page_only'][0] );
		$is_bottom_html_block = empty( $meta['block_place'][0] ) || $meta['block_place'][0] == 'bottom';
		if ( $is_first_page_only && $paged != 1 ) {
			$page_id = 0;
		}
	}
	$is_taxonomy = true;
}

if ( ! $page_id && ( $page_id = apply_filters( 'wpml_object_id', (int) ideapark_mod( 'archive_block' ), 'any' ) ) ) {
	if ( 'publish' != ideapark_post_status( $page_id ) ) {
		$page_id = 0;
	}

	$is_first_page_only   = ideapark_mod( 'archive_block_on_first_page_only' );
	$is_bottom_html_block = ideapark_mod( 'archive_block_place' ) !== 'top';

	if ( $is_first_page_only && $is_taxonomy ) {
		$page_id = 0;
	}
}

if ( $page_id ) {
	if ( ideapark_is_elementor_page( $page_id ) ) {
		$page_content = Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $page_id );
	} elseif ( $post = get_post( $page_id ) ) {
		$page_content = apply_filters( 'the_content', $post->post_content );
		$page_content = str_replace( ']]>', ']]&gt;', $page_content );
		$page_content = ideapark_wrap( $page_content, '<div class="l-section__container"><div class="entry-content">', '</div></div>' );
		wp_reset_postdata();
	} else {
		$page_content = '';
	}
	$html_block = ideapark_wrap( $page_content, '<div class="l-section">', '</div>' );
} else {
	$html_block = '';
}

$params            = ideapark_get_cookie_params();
$sort_options      = ideapark_parse_checklist( ideapark_mod( 'catalog_order_list' ) );
$sort_options_html = [];
foreach ( $sort_options as $index => $enabled ) {
	if ( $enabled ) {
		$name = '';
		switch ( $index ) {
			case 'newest':
				$name = __( 'Newest first', 'antek' );
				break;
			case 'low_price':
				$name = __( 'Low price first', 'antek' );
				break;
			case 'high_price':
				$name = __( 'High price first', 'antek' );
				break;
			case 'menu_order':
				$name = __( 'Default sorting', 'antek' );
				break;
		}
		if ( $name ) {
			ob_start(); ?>
			<option
				value="<?php echo esc_attr( $index ); ?>" <?php selected( $index, $params['sort'] ); ?>><?php echo esc_html( $name ); ?></option>
			<?php
			$sort_options_html[] = ob_get_clean();
		}
	}
}

?>
<?php if ( $html_block && ! $is_bottom_html_block ) {
	echo ideapark_wrap( $html_block, '<div class="c-html-block c-html-block--top">', '</div>' );
} ?>


	<div
		class="c-catalog l-section l-section--margin-120 l-section--container-wide <?php ideapark_class( $with_sidebar, 'l-section--with-sidebar' ); ?>">
		<?php if ( $with_sidebar ) { ?>
			<div class="c-catalog__filter-show-button">
				<button class="h-cb c-button c-button--outline c-button--full js-sidebar-button" type="button"><i
						class="ip-filter c-catalog__filter-ico"></i><?php esc_html_e( 'Filter', 'antek' ); ?></button>
			</div>
		<?php } ?>
		<?php if ( $with_sidebar ) { ?>
			<div class="l-section__sidebar l-section__sidebar--left l-section__sidebar--popup">
				<?php get_sidebar( 'catalog' ); ?>
			</div>
		<?php } ?>
		<div class="l-section__content<?php if ( $with_sidebar ) { ?> l-section__content--with-sidebar<?php } ?>">
			<?php if ( $with_sidebar && ideapark_mod( 'sticky_sidebar' ) ) { ?>
			<div class="js-sticky-sidebar-nearby"><?php } ?>
				<div class="c-catalog-ordering">
					<div class="c-catalog-ordering__col c-catalog-ordering__col--result">
						<?php echo sprintf( wp_kses_post( __( 'Your search results: <b>%s</b>', 'antek' ) ), $wp_query->found_posts ); ?>
					</div>
					<?php if ( sizeof( $sort_options_html ) > 1 ) { ?>
						<div class="c-catalog-ordering__col c-catalog-ordering__col--sort">
							<select class="c-catalog-ordering__select styled js-ordering-sort">
								<?php echo implode( '', $sort_options_html ); ?>
							</select>
						</div>
					<?php } ?>
				</div>
				<?php if ( have_posts() ) { ?>
					<div
						class="c-catalog__list">
						<?php while ( have_posts() ) : the_post(); ?>
							<?php ideapark_get_template_part( 'templates/vehicle' ); ?>
						<?php endwhile; ?>
					</div>
					<?php ideapark_corenavi();
				} else { ?>
					<?php if ( $is_favorites_list ) { ?>
						<div class="c-cart-empty">
							<div class="c-cart-empty__image-wrap">
								<?php if ( ideapark_mod( 'cart_empty_favorites' ) ) { ?>
									<img src="<?php echo esc_url( ideapark_mod( 'cart_empty_favorites' ) ); ?>"
										 alt="<?php esc_html_e( 'Your favorites list is empty', 'antek' ); ?>"
										 class="c-cart-empty__image"/>
								<?php } else { ?>
									<i class="ip-star-empty c-cart-empty__svg"></i>
								<?php } ?>
							</div>
							<h2 class="c-cart-empty__header"><?php esc_html_e( 'Your favorites list is empty', 'antek' ); ?></h2>
							<a class="c-form__button c-cart-empty__backward"
							   href="<?php echo esc_url( get_post_type_archive_link( 'catalog' ) ); ?>">
								<?php esc_html_e( 'Return to catalog', 'antek' ) ?>
							</a>
						</div>
					<?php } else { ?>
						<p class="c-catalog__nothing"><?php esc_html_e( 'Sorry, no items were found.', 'antek' ); ?></p>
					<?php } ?>
				<?php } ?>
				<?php if ( $with_sidebar && ideapark_mod( 'sticky_sidebar' ) ) { ?>
			</div><?php } ?>
		</div>
	</div>

<?php if ( $html_block && $is_bottom_html_block ) {
	echo ideapark_wrap( $html_block, '<div class="c-html-block c-html-block--bottom">', '</div>' );
} ?>

<?php get_footer(); ?>
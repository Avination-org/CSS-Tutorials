<?php
global $post;
$layout = ! empty( $ideapark_var['layout'] ) ? $ideapark_var['layout'] : '';
$lang   = apply_filters( 'wpml_current_language', null );
$meta   = get_post_meta( $post->ID );
if ( function_exists( 'ideapark_set_details_transient' ) ) {
	$details = ideapark_set_details_transient( 'list' );
} else {
	$details = [];
}

foreach ( $details as $detail_slug => $detail ) {
	$detail_slug_new = $detail_slug;
	if ( $lang ) {
		$detail_slug_new = preg_replace( '~-' . $lang . '$~', '', $detail_slug_new );
	}
	$details[ $detail_slug_new ]['value'] = ! empty( $meta[ $detail_slug ] ) ? $meta[ $detail_slug ] : [];
	$details[ $detail_slug_new ]['text']  = isset( $meta[ $detail_slug ][0] ) && $meta[ $detail_slug ][0] !== '' && $meta[ $detail_slug ][0] !== null ? $meta[ $detail_slug ][0] : '';
}

$permalink = apply_filters( 'the_permalink', get_permalink() );
$args      = [ 'start', 'end', 'pickup', 'delivery' ];
foreach ( $args as $arg ) {
	if ( ! empty( $_REQUEST[ $arg ] ) ) {
		$permalink = add_query_arg( $arg, $_REQUEST[ $arg ], $permalink );
	}
}

$is_hide_price  = ! empty( $ideapark_var['hide_price'] );
$original_price = 0;

if ( ! empty( $meta['price_on_request'][0] ) ) {
	$price_on_request = ideapark_mod( 'price_on_request_label' );
} else {
	$price = ! empty( $post->price_total ) ? $post->price_total : ( ! empty( $meta['price'][0] ) ? $meta['price'][0] : ( ! empty( $meta['price_week'][0] ) ? $meta['price_week'][0] : ( ! empty( $meta['price_month'][0] ) ? $meta['price_month'][0] : 0 ) ) );
	if ( function_exists( 'ideapark_get_filter_dates_range' ) ) {
		$dates_range = ideapark_get_filter_dates_range();
		$diff        = $dates_range['diff'];
	} else {
		$diff = 1;
	}
	$original_price   = ! empty( $meta['price'][0] ) ? $diff * $meta['price'][0] + ( ! empty( $dates_range['delivery'] ) ? ideapark_get_delivery_price( $post->ID ) : 0 ) : 0;
	$price_on_request = false;
}
$is_favorites_list = isset( $_REQUEST['favorites'] );
$is_favorite       = ( $fav = ideapark_get_favorites() ) && array_key_exists( $post->ID, $fav );
ob_start();
if ( $price_on_request ) { ?>
	<span class="c-vehicle__total-request">
		<?php echo esc_html( $price_on_request ); ?>
	</span>
<?php } else {
	if ( ! empty( $meta['custom_price_text'][0] ) ) { ?>
		<?php echo esc_html( $meta['custom_price_text'][0] ); ?>
	<?php } elseif ( ideapark_woocommerce_on() ) { ?>
		<?php if ( ideapark_mod( 'show_price_before_discount' ) && $original_price > $price ) { ?>
			<div class="c-vehicle__total-orig"><?php echo wc_price( $original_price ); ?></div>
		<?php } ?>
		<?php echo wc_price( $price ); ?>
	<?php }
}
$total_html = ob_get_clean();
if ( $total_html ) {
	ob_start(); ?>
	<div class="c-vehicle__total-wrap">
		<?php if ( ! $is_hide_price ) { ?>
			<?php if ( $layout ) { ?>
				<div class="c-vehicle__total-wrap-col">
			<?php } ?>
			<?php echo ideapark_wrap( ideapark_mod( 'price_block_title' ), '<div class="c-vehicle__total-title">', '</div>' ); ?>
			<?php echo ideapark_wrap( ideapark_mod( 'price_block_tax' ), '<div class="c-vehicle__total-tax">', '</div>' ); ?>
			<?php if ( $layout ) { ?>
				</div>
			<?php } ?>
			<?php echo ideapark_wrap( $total_html, '<div class="c-vehicle__total">', '</div>' ); ?>
		<?php } ?>
	</div>
	<?php
	$total_html = ob_get_clean();
}

if ( $layout ) {
	ob_start();
}
?>
	<div
		class="c-vehicle<?php if ( $is_favorite ) { ?> c-vehicle--favorite<?php } ?> <?php if ( ! $total_html ) { ?> c-vehicle--wide<?php } ?>"
		<?php if ( $is_favorites_list ) { ?>data-favorites-list="yes"<?php } ?>
		data-id="<?php echo esc_attr( $post->ID ); ?>" data-title="<?php echo esc_attr( get_the_title() ); ?>">
		<div class="c-vehicle__thumb-wrap">

			<a href="<?php echo esc_url( $permalink ) ?>" class="c-vehicle__link">
				<div class="c-vehicle__thumb-inner">
					<?php if ( has_post_thumbnail() ) { ?>
						<?php the_post_thumbnail( $layout ? 'medium_large' : 'ideapark-vehicle', [ 'class' => 'c-vehicle__thumb' ] ); ?>
						<?php if ( $layout ) { ?>
							<div class="c-vehicle__image-overlay"></div>
							<i class="ip-plus c-vehicle__plus"></i>
						<?php } ?>
					<?php } ?>
				</div>
			</a>

			<?php if ( array_key_exists( 'sale', $details ) && ! empty( $meta['sale'][0] ) ) { ?>
				<div class="c-vehicle__sale" <?php if ( ! empty( $meta['sale_color'][0] ) ) {
					echo ideapark_bg( ideapark_mod_hex_color_norm( $meta['sale_color'][0] ) ) ?><?php } ?>>
					<?php echo esc_html( $meta['sale'][0] ); ?>
				</div>
			<?php } ?>

			<?php if ( $is_favorite ) { ?>
				<a href="<?php echo esc_url( add_query_arg( 'favorites', '', get_post_type_archive_link( 'catalog' ) ) ); ?>"
				   class="js-favorites">
					<i class="ip-star c-vehicle__favorite-ico"></i>
				</a>
				<a href="" onclick="return false;" class="js-favorite-remove">
					<i class="ip-close c-vehicle__favorite-ico-remove"></i>
				</a>
			<?php } ?>

			<div class="c-vehicle__thumb-buttons">
				<?php if ( ! empty( $details['download']['value'][0] ) && ( $url = wp_get_attachment_url( $details['download']['value'][0] ) ) ) { ?>
					<?php
					$image       = get_post( $details['download']['value'][0] );
					$image_title = $image->post_title;
					if ( ! $image_title ) {
						$image_title = __( 'Attached File', 'antek' );
					}
					?>
					<a
						target="_blank"
						<?php if ( get_post_mime_type( $details['download']['value'][0] ) == 'application/pdf' ) { ?>data-vbtype="iframe"<?php } ?>
						class="c-vehicle__download" href="<?php echo esc_attr( $url ); ?>">
						<?php esc_html_e( 'View', 'antek' ); ?>&nbsp;<?php echo esc_html( $image_title ); ?>
					</a>
					<span class="c-vehicle__download-spacer"></span>
				<?php } ?>
				<a class="c-vehicle__download"
				   href="<?php echo esc_url( $permalink ) ?>"><?php esc_html_e( 'View Details', 'antek' ); ?></a>
			</div>
		</div>
		<div
			class="c-vehicle__content-wrap<?php if ( ! $total_html ) { ?> c-vehicle__content-wrap--wide<?php } ?>">
			<a href="<?php echo esc_url( $permalink ) ?>" class="c-vehicle__title-link">
				<div class="c-vehicle__title"><span class="c-vehicle__title-inner"><?php the_title(); ?></span></div>
			</a>
			<?php if ( $layout ) {
				echo ideapark_wrap( $total_html );
			} ?>
			<?php
			if ( ideapark_woocommerce_on() ) {
				ob_start();
				if ( ideapark_mod( 'price_type' ) == 'cond' ) {
					$price_postfix = ideapark_mod( 'booking_type' ) == 'day' ? esc_html__( 'Day', 'antek' ) : esc_html__( 'Night', 'antek' );
					if ( ! $price_on_request ) {
						if ( array_key_exists( 'price', $details ) ) {
							$price_per_day = $diff ? ( $price - ( $dates_range['delivery'] ? ideapark_get_delivery_price( $post->ID ) : 0 ) ) / $diff : 0;
							?>
							<li class="c-vehicle__price"><?php echo wc_price( $price_per_day ); ?>
								/ <?php echo ideapark_wrap( $price_postfix ); ?></li>
							<?php
						}
					} elseif ( ! empty( $details['price']['text'] ) ) { ?>
						<li class="c-vehicle__price"><?php echo wc_price( $details['price']['text'] ); ?>
							/ <?php echo ideapark_wrap( $price_postfix ); ?></li>
					<?php }
				} else {
					foreach (
						[
							'price'       => esc_html__( 'Day', 'antek' ),
							'price_week'  => esc_html__( 'Week', 'antek' ),
							'price_month' => esc_html__( 'Month', 'antek' ),
						] as $price_name => $price_postfix
					) {
						if ( array_key_exists( $price_name, $details ) && ! empty( $details[ $price_name ]['text'] ) ) {
							?>
							<li class="c-vehicle__price"><?php echo wc_price( $details[ $price_name ]['text'] ); ?>
								/ <?php echo ideapark_wrap( $price_postfix ); ?></li>
							<?php
						}
					}
				}
				$prices = ob_get_clean();
			} else {
				$prices = '';
			}

			?>

			<?php echo ideapark_wrap( $prices, '<ul class="c-vehicle__prices">', '</ul>' ); ?>
			<?php if ( ideapark_woocommerce_on() && array_key_exists( 'price_delivery', $details ) && ! empty( $details['price_delivery']['value'] ) ) { ?>
				<div
					class="c-vehicle__price-delivery">
					<?php echo esc_html( ideapark_mod( 'delivery_title_list' ) ); ?><?php if ( $details['price_delivery']['text'] ) { ?><!--
					-->: <?php echo wc_price( $details['price_delivery']['text'] ); ?>
					<?php } ?>
				</div>
			<?php } ?>
			<?php if ( has_excerpt() ) { ?>
				<div class="c-vehicle__excerpt">
					<?php the_excerpt(); ?>
				</div>
			<?php } ?>
			<?php if ( $details ) {
				ob_start();
				foreach ( $details as $detail_slug => $detail ) {
					if ( in_array( $detail_slug, [
							'sale',
							'price',
							'price_week',
							'price_month',
							'price_delivery',
							'download',
						] ) || empty( $detail['value'] ) && ! in_array( $detail_slug, [
							'location',
							'vehicle_type'
						] ) ) {
						continue;
					}
					$text = esc_html( $detail['text'] );
					switch ( $detail_slug ) {
						case 'location':
							if ( $locations = get_the_terms( $post->ID, 'location' ) ) {
								$text = [];
								foreach ( $locations as $location ) {
									$text[] = $location->name;
								}
								$text = implode( ', ', $text );
							} else {
								continue 2;
							}
							break;
						case 'vehicle_type':
							if ( $types = get_the_terms( $post->ID, 'vehicle_type' ) ) {
								$text = [];
								foreach ( $types as $type ) {
									$text[] = $type->name;
								}
								$text = implode( ', ', $text );
							} else {
								continue 2;
							}
							break;
					}
					if ( $text !== '' ) { ?>
						<li class="c-vehicle__detail-item">
							<?php $name = apply_filters( 'wpml_translate_single_string', $detail['name'], IDEAPARK_DOMAIN, 'Details - ' . $detail['name'], apply_filters( 'wpml_current_language', null ) ); ?>
							<?php if ( $detail['unit'] ) { ?>
								<?php $unit = apply_filters( 'wpml_translate_single_string', $detail['unit'], IDEAPARK_DOMAIN, 'Details - ' . $detail['unit'], apply_filters( 'wpml_current_language', null ) ); ?>
								<?php $unit = ideapark_wrap( $unit, '<span class="c-vehicle__detail-unit">', '</span>' ); ?>
							<?php } else {
								$unit = '';
							} ?>
							<?php echo ideapark_wrap( esc_html( $name ), '<span class="c-vehicle__detail-name">', ':</span>' ); ?>
							<?php echo ideapark_wrap( esc_html( $text ), '<span class="c-vehicle__detail-value">', $unit . '</span>' ); ?>
						</li>
					<?php }
				}
				$content = ob_get_clean();
				echo ideapark_wrap( $content, '<ul class="c-vehicle__detail-list">', '</ul>' );
			} ?>
		</div>

		<div class="c-vehicle__booking-wrap<?php if ( ! $total_html ) { ?> c-vehicle__booking-wrap--wide<?php } ?>">

			<?php if ( ! $layout ) {
				echo ideapark_wrap( $total_html );
			} ?>

			<?php if ( ideapark_mod( 'reserve_button_title_list' ) ) { ?>
				<a href="<?php echo esc_url( $permalink ) ?>"
				   class="c-button c-button--default c-button--compact c-vehicle__button">
					<?php echo esc_html( ideapark_mod( 'reserve_button_title_list' ) ); ?><?php if ( $layout ) { ?><!--
					--><i class="ip-double-arrow c-button__arrow"></i><?php } ?></a>
			<?php } ?>
		</div>

	</div>
<?php
if ( $layout ) {
	echo str_replace( 'c-vehicle', 'c-vehicle-' . $layout, ob_get_clean() );
}
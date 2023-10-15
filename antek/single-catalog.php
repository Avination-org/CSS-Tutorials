<?php use Elementor\Plugin;

get_header();
get_template_part( 'templates/page-header' );
global $post;

$with_booking = ( ideapark_woocommerce_on() && ! ideapark_mod( 'disable_booking' ) );
$with_sidebar = ! ideapark_mod( 'hide_booking_sidebar' ) && ( $with_booking || ideapark_mod( 'show_price_block' ) || is_active_sidebar( 'catalog-page-sidebar' ) );
$request_form = ! $with_booking && ideapark_mod( 'request_shortcode' );
$lang         = apply_filters( 'wpml_current_language', null );

$vehicle_id = $post->ID;
$meta       = get_post_meta( $vehicle_id );

if ( function_exists( 'ideapark_set_details_transient' ) ) {
	$details = ideapark_set_details_transient( 'page' );
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
$images           = ideapark_vehicle_images();
$index            = 0;
$text             = [];
$text_location_id = 0;
if ( $locations = get_the_terms( $vehicle_id, 'location' ) ) {
	foreach ( $locations as $location ) {
		$text[] = $location->name;
		if ( ! $text_location_id ) {
			$text_location_id = $location->term_id;
		}
	}
}
$text_location = implode( ', ', $text );

$phrases        = ideapark_request_from_phrases();
$dates          = function_exists( 'ideapark_get_filter_dates_range' ) ? ideapark_get_filter_dates_range( false, false, ideapark_mod( 'auto_set_minimum_days' ) ) : [];
$original_price = 0;

if ( ! empty( $meta['price_on_request'][0] ) ) {
	$price_on_request = ideapark_mod( 'price_on_request_label' );
	$diff             = $dates['diff'];
} else {
	$price = ! empty( $details['price']['text'] ) ? $details['price']['text'] : 0;
	if ( $dates ) {
		$price = ideapark_get_price( ideapark_get_orig_catalog_id( $vehicle_id ), $dates['start'], $dates['end'], 0 /* $dates['delivery'] */ );
		$diff  = $dates['diff'];
	} else {
		$diff = 1;
	}
	$original_price   = ! empty( $meta['price'][0] ) ? $diff * $meta['price'][0] : 0;
	$price_on_request = false;
}

$has_delivery = array_key_exists( 'price_delivery', $details ) && ! empty( $details['price_delivery']['value'] ) || ideapark_mod( 'disable_self_pickup' );
$is_favorite  = ( $fav = ideapark_get_favorites() ) && array_key_exists( $vehicle_id, $fav );

?>
<?php if ( have_posts() ): ?>
	<?php while ( have_posts() ) : the_post(); ?>

		<?php if ( $price_on_request || $request_form ) { ?>
			<div class="c-header__callback-popup c-header__callback-popup--disabled js-callback-popup"
				 data-button=".js-request-price">
				<div class="c-header__callback-wrap">
					<div class="c-header__callback-content">
						<?php echo ideapark_shortcode( $price_on_request ? ideapark_mod( 'price_on_request_shortcode' ) : ideapark_mod( 'request_shortcode' ) ); ?>
					</div>
					<button type="button" class="h-cb h-cb--svg c-header__callback-close js-callback-close"
							id="ideapark-callback-close"><i class="ip-close"></i></button>
				</div>
			</div>
		<?php } ?>

		<div
			class="l-section l-section--container <?php if ( $with_sidebar ) { ?>l-section--with-sidebar<?php } ?> l-section--margin-120">
			<div
				class="l-section__content<?php if ( $with_sidebar ) { ?> l-section__content--with-sidebar<?php } ?>">
				<article id="vehicle-<?php the_ID(); ?>"
						 data-title="<?php echo esc_attr( get_the_title() ); ?>"
						 class="c-vehicle-details <?php ideapark_class( ideapark_mod( 'sticky_sidebar' ) && $with_sidebar, 'js-sticky-sidebar-nearby' ); ?>">

					<?php $section = []; ?>

					<?php ob_start(); ?>
					<?php if ( $images ) { ?>
						<div class="c-vehicle-details__images-block">
							<div
								class="c-vehicle-details__gallery js-single-product-carousel h-carousel h-carousel--inner h-carousel--hover">
								<?php if ( $images ) {
									foreach ( $images as $i => $image ) {
										if ( ! empty( $image['video_url'] ) ) {
											if ( ideapark_mod( 'product_modal' ) ) {
												$image_wrap_open  = '';
												$image_wrap_close = sprintf( '<a download href="%s" class="c-vehicle-details__image-link %s" data-index="%s" data-product-id="%s" onclick="return false;">', esc_url( $image['video_url'] ), ideapark_mod( 'product_modal' ) ? ' c-vehicle-details__image-link--zoom js-vehicle-zoom js-vehicle-zoom-video' : '', $index ++, $vehicle_id ) . '</a>';
											} else {
												$image_wrap_open  = '';
												$image_wrap_close = '';
											}

											echo sprintf( '<div class="c-vehicle-details__gallery-item c-vehicle-details__gallery-item--video">%s%s%s</div>', $image_wrap_open, '<span class="c-vehicle-details__gallery-video" style="background-image: url(' . $image['image_url'] . ')"></span>', $image_wrap_close );
										} else {
											if ( ideapark_mod( 'product_modal' ) ) {
												$image_wrap_open  = '';
												$image_wrap_close = sprintf( '<a download href="%s" class="c-vehicle-details__image-link %s" data-size="%sx%s" data-index="%s" data-product-id="%s" onclick="return false;">', esc_url( $image['full'][0] ), ideapark_mod( 'product_modal' ) ? ' c-vehicle-details__image-link--zoom js-vehicle-zoom' : '', intval( $image['full'][1] ), intval( $image['full'][2] ), $index ++, $vehicle_id ) . '</a>';
											} else {
												$image_wrap_open  = '';
												$image_wrap_close = '';
											}

											echo sprintf( '<div class="c-vehicle-details__gallery-item ">%s%s%s</div>', $image_wrap_open, $image['image'], $image_wrap_close );
										}
									}
								} ?>
							</div>

							<?php if ( sizeof( $images ) > 1 ) { ?>
								<div class="c-vehicle-details__thumbs js-product-thumbs-carousel">
									<?php if ( sizeof( $images ) > 1 ) { ?>
										<?php foreach ( $images as $ii => $image ) { ?>
											<?php echo sprintf( '<div class="c-vehicle-details__thumbs-item ' . ( ! $ii ? 'active' : '' ) . '"><button type="button" class="h-cb js-single-product-thumb %s" data-index="%s" %s>%s</button></div>', ! empty( $image['thumb_url'] ) ? 'c-vehicle-details__thumbs-video' : '', $ii, '', ! empty( $image['thumb_url'] ) ? '<img class="c-vehicle-details__thumbs-video-img" alt="' . esc_attr__( 'Youtube Video', 'antek' ) . '" src="' . $image['thumb_url'] . '" />' : $image['thumb'] ); ?>
										<?php } ?>
									<?php } ?>
								</div>
							<?php } ?>

							<?php if ( array_key_exists( 'sale', $details ) && ! empty( $details['sale']['text'] ) ) { ?>
								<div class="c-vehicle-details__sale"<?php if ( ! empty( $meta['sale_color'][0] ) ) {
									echo ideapark_bg( ideapark_mod_hex_color_norm( $meta['sale_color'][0] ) ) ?><?php } ?>>
									<?php echo esc_html( $details['sale']['text'] ); ?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
					<?php $section['gallery'] = trim( ob_get_clean() ); ?>

					<?php ob_start(); ?>
					<h1 class="c-vehicle-details__title"><?php the_title(); ?></h1>
					<?php $section['header'] = trim( ob_get_clean() ); ?>

					<?php ob_start(); ?>
					<?php if ( array_key_exists( 'location', $details ) && $locations ) { ?>
						<div class="c-vehicle-details__subtitle">
							<i class="ip-map-pin c-vehicle__location-icon"></i>
							<?php echo esc_html( $text_location );
							?>
						</div>
					<?php } ?>
					<?php $section['locations'] = trim( ob_get_clean() ); ?>

					<?php ob_start(); ?>
					<?php if ( ! ideapark_mod( 'disable_booking' ) && ideapark_mod( 'show_booked_days' ) ) { ?>
						<h3 class="c-vehicle-details__subheader"><?php esc_html_e( 'Booking calendar', 'antek' ) ?></h3>
						<div class="c-vehicle-details__decor"><i class="ip-decor"></i></div>

						<input type="hidden" class="js-booking-calendar-input" value=""/>
						<div class="c-vehicle-details__booking-calendar js-booking-calendar-info"></div>
						<div class="c-vehicle-details__booked-wrap">
							<div
								class="c-vehicle-details__booked <?php ideapark_class( current_user_can( 'administrator' ), 'c-vehicle-details__booked--b', 'c-vehicle-details__booked--r' ); ?>"><?php esc_html_e( 'Booked', 'antek' ) ?><?php if ( current_user_can( 'administrator' ) ) { ?>
									<div
										class="c-vehicle-details__booked-hint"><?php esc_html_e( 'only the admin sees it', 'antek' ) ?></div><?php } ?>
							</div>
							<?php if ( current_user_can( 'administrator' ) ) { ?>
								<?php if ( ideapark_mod( 'cleaning_days' ) ) { ?>
									<div
										class="c-vehicle-details__booked c-vehicle-details__booked--c"><?php esc_html_e( 'Cleaning', 'antek' ) ?>
										<div
											class="c-vehicle-details__booked-hint"><?php esc_html_e( 'only the admin sees it', 'antek' ) ?></div>
									</div>
								<?php } ?>
								<div
									class="c-vehicle-details__booked c-vehicle-details__booked--r"><?php esc_html_e( 'Reserved', 'antek' ) ?></div>
							<?php } ?>
							<?php if ( sizeof( explode( ',', ideapark_mod( 'pickup_dropoff_days' ) ) ) < 7 ) { ?>
								<div
									class="c-vehicle-details__booked c-vehicle-details__booked--n-a"><?php esc_html_e( 'Not available for pick up or drop off', 'antek' ) ?></div>
							<?php } ?>
						</div>
					<?php } ?>
					<?php $section['calendar'] = trim( ob_get_clean() ); ?>

					<?php
					ob_start();
					the_content();
					$content = trim( ob_get_clean() )
					?>
					<?php if ( $content ) {
						ob_start(); ?>
						<h3 class="c-vehicle-details__subheader"><?php esc_html_e( 'Description', 'antek' ) ?></h3>
						<div class="c-vehicle-details__decor"><i class="ip-decor"></i></div>
						<div class="entry-content entry-content--sidebar">
							<?php echo ideapark_wrap( $content ); ?>
						</div>
						<?php $section['description'] = trim( ob_get_clean() ); ?>
					<?php } else {
						$section['description'] = '';
					} ?>

					<?php ob_start(); ?>
					<div class="c-vehicle-details__download-wrap">
						<?php if ( ! empty( $details['download']['value'][0] ) && ( $url = wp_get_attachment_url( $details['download']['value'][0] ) ) ) { ?>
							<?php
							$image       = get_post( $details['download']['value'][0] );
							$image_title = $image->post_title;
							if ( ! $image_title ) {
								$image_title = __( 'Attached File', 'antek' );
							}
							?>
							<a target="_blank" class="c-vehicle-details__download"
							   <?php if ( get_post_mime_type( $details['download']['value'][0] ) == 'application/pdf' ) { ?>data-vbtype="iframe"<?php } ?>
							   href="<?php echo esc_attr( $url ); ?>">
								<i class="ip-pdf c-vehicle-details__download-ico"></i>
								<span class="c-vehicle-details__download-title">
								<?php esc_html_e( 'View', 'antek' ); ?>&nbsp;<?php echo esc_html( $image_title ); ?>
								</span>
							</a>
						<?php } ?>
						<a
							onclick="return false"
							class="c-vehicle-details__download js-favorite<?php if ( $is_favorite ) { ?> active<?php } ?>"
							data-id="<?php echo esc_attr( $vehicle_id ); ?>"
							data-add="<?php esc_attr_e( 'Favourite This Equipment', 'antek' ); ?>"
							data-remove="<?php esc_attr_e( 'This Equipment is Favourite', 'antek' ); ?>">
							<i class="ip-star c-vehicle-details__download-ico"></i>
							<span class="c-vehicle-details__download-title">
								<?php if ( $is_favorite ) { ?>
									<?php esc_html_e( 'This Equipment is Favourite', 'antek' ); ?>
								<?php } else { ?>
									<?php esc_html_e( 'Favourite This Equipment', 'antek' ); ?>
								<?php } ?>
							</span>
						</a>
					</div>
					<?php $section['download'] = trim( ob_get_clean() ); ?>

					<?php ob_start(); ?>
					<?php if ( $details ) { ?>
						<h3 class="c-vehicle-details__subheader"><?php esc_html_e( 'Details', 'antek' ) ?></h3>
						<div class="c-vehicle-details__decor"><i class="ip-decor"></i></div>
						<div class="c-vehicle-details__detail-list-wrap">
							<table class="c-vehicle-details__detail-list">
								<tbody>
								<?php
								$index = 0;

								foreach ( $details as $detail_slug => $detail ) {
									if ( in_array( $detail_slug, [
											'price',
											'price_week',
											'price_month',
											'price_delivery',
											'download',
											'sale'
										] ) || empty( $detail['value'] ) && ! in_array( $detail_slug, [
											'vehicle_type'
										] ) ) {
										continue;
									}

									$text = esc_html( $detail['text'] );
									switch ( $detail_slug ) {
										case 'vehicle_type':
											if ( $types = get_the_terms( $vehicle_id, 'vehicle_type' ) ) {
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
									if ( $text !== '' ) {
										$index ++; ?>
										<tr class="c-vehicle-details__detail-row">
											<td class="c-vehicle-details__detail-col c-vehicle-details__detail-col--name">
												<?php $name = apply_filters( 'wpml_translate_single_string', $detail['name'], IDEAPARK_DOMAIN, 'Details - ' . $detail['name'], apply_filters( 'wpml_current_language', null ) ); ?>
												<?php echo esc_html( trim( $name ) ); ?>:
											</td>
											<td class="c-vehicle-details__detail-col c-vehicle-details__detail-col--value">
												<?php echo esc_html( $text ); ?>
												<?php if ( $detail['unit'] ) { ?>
													<?php $unit = apply_filters( 'wpml_translate_single_string', $detail['unit'], IDEAPARK_DOMAIN, 'Details - ' . $detail['unit'], apply_filters( 'wpml_current_language', null ) ); ?>
													<?php echo ideapark_wrap( $unit, '<span class="c-vehicle-details__detail-unit">', '</span>' ); ?>
												<?php } ?>
											</td>
										</tr>
									<?php } ?>
									<?php
								} ?>
								</tbody>
							</table>
						</div>
					<?php } ?>
					<?php $section['details'] = trim( ob_get_clean() ); ?>

					<?php
					$section_enable = ideapark_parse_checklist( ideapark_mod( 'product_sections' ) );
					$section_out    = [];
					foreach ( $section_enable as $section_index => $enabled ) {
						if ( $enabled && array_key_exists( $section_index, $section ) ) {
							$section_out[] = $section[ $section_index ];
						}
					}

					echo implode( '', $section_out );
					?>
				</article>
			</div>
			<?php if ( $with_sidebar ) { ?>
			<div class="l-section__sidebar l-section__sidebar--right">
				<div
					class="c-vehicle-details__sidebar <?php ideapark_class( ideapark_mod( 'sticky_sidebar' ), 'js-sticky-sidebar' ); ?>">

					<?php if ( $with_booking || ideapark_mod( 'show_price_block' ) ) { ?>
						<?php if ( $price_on_request ) { ?>
							<div class="c-vehicle-book__price">
								<div class="c-vehicle-book__price-wrap">
									<i class="ip-decor-left c-vehicle-book__price-decor"></i>
									<span class="c-vehicle-book__amount">
												<span class="c-vehicle-book__request">
													<?php echo esc_html( $price_on_request ); ?>
												</span>
											</span>
									<i class="ip-decor-right c-vehicle-book__price-decor"></i>
								</div>
								<?php if ( $with_booking && ideapark_mod( 'show_cnt_day' ) ) { ?>
									<div
										class="c-vehicle-book__cnt<?php if ( $diff <= 1 ) { ?> h-hidden<?php } ?>">
										<?php echo esc_html( $phrases['days'] ) . ': '; ?>
										<span
											class="js-book-cnt c-vehicle-book__cnt-value"><?php echo esc_html( $diff ); ?></span>
									</div>
								<?php } ?>
							</div>
						<?php } else { ?>
							<?php if ( ! empty( $price ) && function_exists( 'wc_price' ) ) { ?>
								<div class="c-vehicle-book__price">
									<?php if ( ideapark_mod( 'show_price_before_discount' ) && $original_price > $price ) { ?>
										<div
											class="c-vehicle-book__total-orig"><?php echo wc_price( $original_price ); ?></div>
									<?php } ?>
									<div class="c-vehicle-book__price-wrap">
										<i class="ip-decor-left c-vehicle-book__price-decor"></i>
										<span class="c-vehicle-book__amount">
										<?php echo wc_price( $price ); ?>
									</span>
										<i class="ip-decor-right c-vehicle-book__price-decor"></i>
									</div>
									<?php if ( $with_booking && ideapark_mod( 'show_cnt_day' ) ) { ?>
										<div
											class="c-vehicle-book__cnt<?php if ( $diff <= 1 ) { ?> h-hidden<?php } ?>">
											<?php echo esc_html( $phrases['days'] ) . ': '; ?>
											<span
												class="js-book-cnt c-vehicle-book__cnt-value"><?php echo esc_html( $diff ); ?></span>
										</div>
									<?php } ?>
								</div>
								<?php ob_start();
								if ( ideapark_mod( 'price_type' ) == 'cond' ) {
									$price_postfix = ideapark_mod( 'booking_type' ) == 'day' ? esc_html__( 'Day', 'antek' ) : esc_html__( 'Night', 'antek' );
									if ( array_key_exists( 'price', $details ) ) {
										$price_per_day = $diff ? $price / $diff : 0;
										?>
										<li class="c-vehicle-details__price"><span
												class="js-book-day-price"><?php echo wc_price( $price_per_day ); ?></span>
											/ <?php echo ideapark_wrap( $price_postfix ); ?></li>
										<?php
									}
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
											<li class="c-vehicle-details__price"><?php echo wc_price( $details[ $price_name ]['text'] ); ?>
												/ <?php echo ideapark_wrap( $price_postfix ); ?></li>
											<?php
										}
									}
								}
								echo ideapark_wrap( trim( ob_get_clean() ), '<ul class="c-vehicle-details__prices">', '</ul>' ); ?>
							<?php } ?>
						<?php } ?>
					<?php } ?>

					<?php if ( $with_booking ) { ?>
						<div class="c-vehicle-book">
							<form method="POST" class="js-book-form" data-id="<?php echo esc_attr( $vehicle_id ); ?>"
								  data-price="<?php echo esc_attr( $price_on_request ? $price_on_request : $price ); ?>">
								<input type="hidden" name="lang" value="<?php echo esc_attr( $lang ); ?>">
								<input type="hidden" name="action" value="ideapark_calc_total" class="js-book-action">
								<input type="hidden" name="vehicle_id" value="<?php echo esc_attr( $vehicle_id ); ?>">
								<div class="c-vehicle-book__wrap">
									<ul class="c-vehicle-book__dates-list">
										<li class="c-vehicle-book__dates-item c-vehicle-book__dates-item--input">
											<?php if ( ( $fields = apply_filters( 'ideapark_booking_fields', [] ) ) && is_array( $fields ) ) {
												foreach ( $fields as $key => $val ) {
													?><input type="hidden" name="<?php echo esc_attr( $key ); ?>"
															 value="<?php echo esc_attr( $val ); ?>" /><?php
												}
											} ?>
											<?php if ( ideapark_mod( 'pickup_dropoff_time' ) ) { ?>
												<input type="hidden" class="js-book-date-start-time" name="start_time"
													   value="<?php echo esc_attr( ! empty( $_REQUEST['start_time'] ) ? ideapark_validate_time( $_REQUEST['start_time'], 'pickup' ) : '' ) ?>"/>
												<input type="hidden" class="js-book-date-end-time" name="end_time"
													   value="<?php echo esc_attr( ! empty( $_REQUEST['end_time'] ) ? ideapark_validate_time( $_REQUEST['end_time'], 'dropoff' ) : '' ) ?>"/>
											<?php } ?>
											<?php
											$date_start = ! empty( $_REQUEST['start'] ) ? $_REQUEST['start'] : ( ! empty( $dates['start'] ) ? $dates['start']->format( ideapark_date_format() ) : '' );
											$date_end   = ! empty( $_REQUEST['end'] ) ? $_REQUEST['end'] : ( ! empty( $dates['end'] ) ? $dates['end']->format( ideapark_date_format() ) : '' );
											?>
											<input type="hidden" class="js-book-date-start" name="start" value=""
												   data-value="<?php echo esc_attr( $date_start ) ?>"/>
											<input type="hidden" class="js-book-date-end" name="end" value=""
												   data-value="<?php echo esc_attr( $date_end ) ?>"/>
											<input type="text" class="c-vehicle-book__date js-book-date-range"
												   value="<?php if ( $date_start && $date_end ) {
												       echo ideapark_wrap( '  —  ', $date_start, $date_end );
											       } ?>"
												   readonly/>
											<button class="h-cb c-vehicle-book__date-btn js-book-date-btn"
													type="button">
												<i class="ip-cal-alt"><!-- --></i></button>
										</li>

										<?php if ( array_key_exists( 'price_delivery', $details ) ) { ?>
											<li class="c-vehicle-book__dates-item c-vehicle-book__dates-item--delivery">
												<?php if ( ! ideapark_mod( 'disable_self_pickup' ) ) { ?>
													<label>
												<span class="c-vehicle-book__delivery-row">
													<span>
														<input type="radio"
															   name="delivery"
															   class="c-vehicle-book__delivery js-book-delivery"
															   value="0"
														       <?php if ( empty( $dates['delivery'] ) || ! $has_delivery ) { ?>checked<?php } ?>/>
													</span>
													<span class="c-vehicle-book__delivery-title">
														<?php esc_html_e( 'Self-Pickup', 'antek' ); ?>
													</span>
													<span class="c-vehicle-book__delivery-price">
													</span>
												</span>
													</label>
												<?php } ?>
												<?php if ( $has_delivery ) { ?>
													<label>
													<span
														class="c-vehicle-book__delivery-row c-vehicle-book__delivery-row--owner">
														<span>
															<input type="radio"
																   name="delivery"
																   class="c-vehicle-book__delivery js-book-delivery"
																   value="1"
															       <?php if ( ! empty( $dates['delivery'] ) ) { ?>checked<?php } ?>/>
														</span>
														<span class="c-vehicle-book__delivery-title">
															<?php echo esc_html( ideapark_mod( 'delivery_title_item' ) ); ?>
														</span>
														<?php if ( $details['price_delivery']['text'] ) { ?>
															<span class="c-vehicle-book__delivery-price">
																<?php echo wc_price( $details['price_delivery']['text'] ); ?>
															</span>
														<?php } ?>
													</span>
													</label>
												<?php } ?>
											</li>
										<?php } ?>
										<?php
										if ( $types = wp_get_post_terms( $vehicle_id, 'vehicle_type' ) ) { ?>
											<input type="hidden" name="type" class="js-book-type"
												   value="<?php echo esc_attr( $types[0]->term_id ); ?>"/>
										<?php }
										?>
										<?php if ( $text_location ) { ?>
											<?php if ( sizeof( $locations ) == 1 ) { ?>
												<li class="c-vehicle-book__dates-item">
													<?php esc_html_e( 'Where:', 'antek' ); ?>&nbsp;
													<span
														class="js-book-pick-up-text"><?php echo esc_html( $text_location ); ?></span>
													<input type="hidden" name="pick_up" class="js-book-pick-up"
														   value="<?php echo esc_attr( $locations[0]->term_id ); ?>"/>
													<input type="hidden" name="drop_off"
														   value="<?php echo esc_attr( $text_location_id ); ?>"/>
												</li>
											<?php } else { ?>
												<li class="c-vehicle-book__dates-item c-vehicle-book__dates-item--select">
														<span
															class="c-vehicle-book__date-title"><?php esc_html_e( 'Where:', 'antek' ) ?></span>
													<div class="c-vehicle-book__select-wrap">
														<select
															class="c-vehicle-book__pick_up h-cb styled js-book-pick-up"
															name="pick_up">
															<?php foreach ( $locations as $term ) { ?>
																<option
																	value="<?php echo esc_attr( $term->term_id ); ?>"
																	<?php if ( ! empty( $_REQUEST['pickup'] ) && $term->term_id == $_REQUEST['pickup'] ) { ?>selected<?php } ?>><?php echo esc_html( $term->name ); ?></option>
															<?php } ?>
														</select>
													</div>
												</li>

											<?php } ?>
										<?php } ?>
									</ul>

									<div class="c-vehicle-book__space js-book-space"></div>

									<div class="c-vehicle-book__block js-book-block">

										<div class="c-vehicle-book__dates-status">
									<span class="c-vehicle-book__dates-available js-book-dates-available h-hidden"><i
											class="ip-check c-vehicle-book__dates-ico"><!-- --></i><?php esc_html_e( 'Dates are available', 'antek' ); ?></span>
											<span
												class="c-vehicle-book__dates-not-available js-book-dates-not-available h-hidden"><i
													class="ip-warning c-vehicle-book__dates-ico"><!-- --></i><span
													class="js-not-available-message"
													data-message="<?php esc_attr_e( 'Dates are not available', 'antek' ); ?>"><?php esc_html_e( 'Dates are not available', 'antek' ); ?></span></span>
										</div>

										<div class="c-vehicle-book__price-block h-hidden js-book-price-block">

											<?php if ( function_exists( 'wc_price' ) && ( $extra_options = get_the_terms( $vehicle_id, 'extra_option' ) ) ) { ?>
												<div
													class="c-vehicle-book__sub-title"><?php esc_html_e( 'Extra options:', 'antek' ) ?></div>
												<ul class="c-vehicle-book__extra-list">
													<?php
													foreach ( $extra_options as $i => $extra_option ) {
														$term_meta = get_term_meta( $extra_option->term_id );
														if ( ! isset( $term_meta['type'][0] ) ) {
															$term_meta['type'][0] = 'day';
														}
														if ( ! isset( $term_meta['price'][0] ) ) {
															$term_meta['price'][0] = 0;
														}
														if ( ! isset( $term_meta['max'][0] ) ) {
															$term_meta['max'][0] = 1;
														}
														$option_price = ideapark_get_extra_total( $extra_option->term_id, 1, true, true );
														$field_id     = 'extra-' . $extra_option->term_id;
														$is_checkbox  = $term_meta['max'][0] == 1 || ! empty( $term_meta['always_included'][0] ); ?>
														<li class="c-vehicle-book__extra-item js-extra-item"
															data-id="<?php echo esc_attr( $extra_option->term_id ); ?>"
															data-type="<?php echo esc_attr( $term_meta['type'][0] ); ?>"
															data-price="<?php echo esc_attr( $term_meta['price'][0] ); ?>"
															data-max="<?php echo esc_attr( $term_meta['max'][0] ); ?>">
															<div
																class="c-vehicle-book__extra-title c-vehicle-book__extra-title--<?php if ( $is_checkbox ) { ?>checkbox<?php } else { ?>number<?php } ?>">
																<div class="c-vehicle-book__extra-title-btn">
																	<?php if ( $term_meta['max'][0] == 1 || ! empty( $term_meta['always_included'][0] ) ) { ?>
																		<input type="hidden" class="js-extra-qty"
																			   name="extra[<?php echo esc_attr( $extra_option->term_id ); ?>]"
																			   value="<?php echo empty( $term_meta['always_included'][0] ) ? 0 : 1 ?>"/>
																		<input type="checkbox"
																			   class="c-vehicle-book__extra-checkbox js-extra-chk"
																			   value="1"
																			   id="<?php echo esc_attr( $field_id ); ?>"
																		       <?php if ( ! empty( $term_meta['always_included'][0] ) ) { ?>checked
																			   disabled<?php } ?>/>
																	<?php } else { ?>
																		<?php if ( empty( $term_meta['always_included'][0] ) ) { ?>
																			<button
																				class="h-cb c-vehicle-book__extra-button c-vehicle-book__extra-button--minus js-extra-minus"
																				type="button"></button>
																			<input
																				name="extra[<?php echo esc_attr( $extra_option->term_id ); ?>]"
																				type="number"
																				class="c-vehicle-book__extra-input js-extra-qty"
																				id="<?php echo esc_attr( $field_id ); ?>"
																				min="0"
																				max="<?php echo esc_attr( $term_meta['max'][0] ); ?>"
																				value="0"/>
																			<button
																				class="h-cb c-vehicle-book__extra-button c-vehicle-book__extra-button--plus js-extra-plus"
																				type="button"></button>
																		<?php } else { ?>
																			<input type="hidden"
																				   class="js-extra-qty"
																				   name="extra[<?php echo esc_attr( $extra_option->term_id ); ?>]"
																				   value="1"/>
																			<span
																				class="c-vehicle-book__extra-input h-input">1</span>
																		<?php } ?>
																	<?php } ?>
																</div>
																<div class="c-vehicle-book__extra-title-text">
																	<label
																		for="<?php echo esc_attr( $field_id ); ?>">
																		<?php echo esc_html( $extra_option->name ); ?>
																	</label>
																</div>
															</div>
															<div class="c-vehicle-book__extra-price">
																<span
																	class="js-extra-price-<?php echo esc_attr( $extra_option->term_id ); ?>">
																<?php if ( $option_price < $term_meta['price'][0] ) { ?>
																	<del><?php echo wc_price( $term_meta['price'][0] ); ?></del>
																	<?php echo wc_price( $option_price ); ?>
																<?php } else { ?>
																	<?php echo wc_price( $term_meta['price'][0] ); ?>
																<?php } ?>
																</span>

																<div class="c-vehicle-book__extra-price-per">
																	<?php if ( $term_meta['type'][0] == 'day' ) { ?>
																		<?php esc_html_e( 'Day', 'antek' ) ?>
																	<?php } else { ?>
																		<?php esc_html_e( 'Total', 'antek' ) ?>
																	<?php } ?>
																</div>
															</div>
														</li>
														<?php
													}
													?>
												</ul>
											<?php } ?>

											<?php if ( ! $price_on_request ) { ?>
												<div class="c-vehicle-book__total">
												<span
													class="c-vehicle-book__total-title"><?php esc_html_e( 'Total', 'antek' ) ?></span>
													<span class="c-vehicle-book__total-amount js-book-total"></span>
												</div>
											<?php } ?>

											<?php if ( $price_on_request ) { ?>
												<?php if ( ideapark_mod( 'price_on_request_button' ) ) { ?>
													<button
														class="c-button c-button--default c-vehicle-book__book js-request-price"
														type="button"><?php echo esc_html( ideapark_mod( 'price_on_request_button' ) ) ?>
														<i class="ip-double-arrow c-button__arrow"></i></button>
												<?php } ?>
											<?php } else { ?>
												<?php if ( ideapark_mod( 'reserve_button_title_item' ) ) { ?>
													<button
														class="c-button c-button--default c-vehicle-book__book js-book"
														type="button"><?php echo esc_html( ideapark_mod( 'reserve_button_title_item' ) ) ?>
														<i class="ip-double-arrow c-button__arrow"></i></button>
												<?php } ?>
											<?php } ?>


											<div class="c-vehicle-book__loading js-book-loading"></div>
										</div>
									</div>
								</div>
							</form>
						</div>
					<?php } else { ?>
						<?php if ( $price_on_request ) { ?>
							<?php if ( ideapark_mod( 'price_on_request_button' ) ) { ?>
								<div class="c-vehicle-book">
									<div class="c-vehicle-book__wrap">
										<button
											class="c-button c-button--default c-vehicle-book__book js-request-price"
											type="button"><?php echo esc_html( ideapark_mod( 'price_on_request_button' ) ) ?>
											<i class="ip-double-arrow c-button__arrow"></i></button>
									</div>
								</div>
							<?php } ?>

						<?php } elseif ( $request_form ) { ?>
							<div class="c-vehicle-book">
								<div class="c-vehicle-book__wrap">
									<button
										class="c-button c-button--default c-vehicle-book__book js-request-price"
										type="button"><?php echo esc_html( ideapark_mod( 'reserve_button_title_item' ) ) ?>
										<i class="ip-double-arrow c-button__arrow"></i></button>
								</div>
							</div>
						<?php } ?>
					<?php } ?>
					<?php if ( is_active_sidebar( 'catalog-page-sidebar' ) ) { ?>
						<?php get_sidebar( 'catalog-page' ); ?>
					<?php } ?>
				</div>
			</div>
			<?php } ?>
		</div>

		<?php
		if ( ( $page_id = apply_filters( 'wpml_object_id', ideapark_mod( 'product_bottom_page' ), 'any' ) ) && 'publish' == ideapark_post_status( $page_id ) ) {
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
			echo ideapark_wrap( $page_content, '<div class="l-section">', '</div>' );
		}
		?>

	<?php endwhile; ?>
<?php endif; ?>

<?php get_footer(); ?>
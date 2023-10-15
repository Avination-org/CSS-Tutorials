<?php

/**
 * @var $ideapark_var array
 */

$icon        = '';
$icon_id     = isset( $ideapark_var['icon_id'] ) ? (int) $ideapark_var['icon_id'] : '';
$image       = '';
$image_id    = isset( $ideapark_var['image_id'] ) ? (int) $ideapark_var['image_id'] : '';
$title       = isset( $ideapark_var['title'] ) ? esc_html( $ideapark_var['title'] ) : '';
$is_h1       = true;
$with_filter = false;

if ( ideapark_woocommerce_on() && is_woocommerce() && is_shop() && ! $title ) {
	$shop_page_id = wc_get_page_id( 'shop' );
	$title        = get_the_title( $shop_page_id );
} elseif ( ideapark_woocommerce_on() && is_woocommerce() && ! $title ) {
	if ( is_product() ) {
		if ( ideapark_mod( 'product_title_in_header' ) ) {
			$title = get_the_title();
		} else {
			$is_h1 = false;
			$title = esc_html( ideapark_mod( 'product_header' ) );
		}
	} else {
		$title = woocommerce_page_title( false );
	}

} elseif ( is_404() ) {
	$title = esc_html__( '404 error', 'antek' );
} elseif ( is_single() ) {
	if ( ! $title ) {
		if ( is_singular( [ 'catalog' ] ) ) {
			$title = esc_html( ideapark_mod( 'catalog_item_header' ) );
			$is_h1 = false;
		} else {
			if ( ideapark_woocommerce_on() && is_product() ) {
				$is_h1 = false;
				$title = esc_html( ideapark_mod( 'product_header' ) );
			} else {
				if ( is_singular( [ 'post' ] ) && ideapark_mod( 'post_title' ) == 'content' ) {
					$is_h1 = false;
					$title = get_the_title( get_option( 'page_for_posts' ) );
				} else {
					$title = get_the_title() . ( is_sticky() ? '<i class="ip-sticky c-page-header__sticky"><!-- --></i>' : '' );
				}
			}
		}
	}
} elseif ( ideapark_woocommerce_on() && is_woocommerce() && ! $title ) {
	if ( apply_filters( 'woocommerce_show_page_title', true ) ) {
		$title = woocommerce_page_title( false );
	}
} elseif ( is_search() && ! $title ) {
	$found_posts = $wp_query->found_posts;
	if ( $found_posts ) {
		$title = esc_html__( 'Search:', 'antek' ) . ' ' . esc_html( get_search_query( false ) );
	} else {
		$title = esc_html__( 'No search results for:', 'antek' ) . ' ' . esc_html( get_search_query( false ) );
	}
} elseif ( is_archive() ) {
	if ( ! $title ) {
		if ( is_category() ) {
			$title = single_cat_title( '', false );
		} elseif ( is_tax() ) {
			if ( is_tax( 'vehicle_type' ) ) {
				$with_filter = ! ideapark_mod( 'disable_filter' );
			}
			$title = single_term_title( '', false );
		} elseif ( is_tag() ) {
			$title = single_tag_title( '', false );
		} elseif ( is_author() ) {
			the_post();
			$title = get_the_author();
			rewind_posts();
		} elseif ( is_day() ) {
			$title = get_the_date();
		} elseif ( is_month() ) {
			$title = get_the_date( 'F Y' );
		} elseif ( is_year() ) {
			$title = get_the_date( 'Y' );
		} else {
			$queried_object = get_queried_object();
			if ( $queried_object->name == 'catalog' ) {
				$title       = esc_html( ideapark_mod( 'catalog_list_header' ) ?: $queried_object->labels->name );
				$with_filter = ! ideapark_mod( 'disable_filter' );
				if ( isset( $_REQUEST['favorites'] ) && ideapark_mod( 'favorites_header' ) ) {
					$title = esc_html( ideapark_mod( 'favorites_header' ) );
				}
			} else {
				$title = esc_html__( 'Archives', 'antek' );
			}
		}
	}
} elseif ( is_home() && get_option( 'page_for_posts' ) && 'page' == get_option( 'show_on_front' ) && ! $title ) {
	$title = get_the_title( get_option( 'page_for_posts' ) );
} elseif ( is_front_page() && get_option( 'page_on_front' ) && 'page' == get_option( 'show_on_front' ) && ! $title ) {
	$title = get_the_title( get_option( 'page_on_front' ) );
} elseif ( is_home() && ! $title ) {
	$title = esc_html__( 'Posts', 'antek' );
}

if (
	! $image_id && ( ( is_category() || is_tax() || is_tag() ) && ( $queried_object = get_queried_object() ) )
) {
	if ( ( $term_meta = get_term_meta( $queried_object->term_id ) ) && ! empty( $term_meta['header_bg_image'][0] ) ) {
		$image_id = $term_meta['header_bg_image'][0];
	}

	if ( ! $image_id && ! empty( $queried_object->parent ) && ( $term_meta = get_term_meta( $queried_object->parent ) ) && ! empty( $term_meta['header_bg_image'][0] ) ) {
		$image_id = $term_meta['header_bg_image'][0];
	}

	if ( ! $image_id && $queried_object->taxonomy == 'vehicle_type' && ideapark_mod( 'header_image_catalog__attachment_id' ) ) {
		$image_id = ideapark_mod( 'header_image_catalog__attachment_id' );
	}

	if ( ! $image_id && $queried_object->taxonomy == 'product_cat' && ideapark_mod( 'header_image_shop__attachment_id' ) ) {
		$image_id = ideapark_mod( 'header_image_shop__attachment_id' );
	}
}

if ( ! $image_id && is_singular() ) {
	$image_id = get_post_meta( $post->ID, 'header_bg_image', true );
	if ( ! $image_id ) {
		$post_type = get_post_type();

		if ( $post_type == 'catalog' ) {
			if ( ( $terms = wp_get_post_terms( $post->ID, 'vehicle_type' ) ) && ( ! empty( $terms[0] ) ) ) {
				$term_meta = get_term_meta( $terms[0]->term_id );
				if ( ! empty( $term_meta['header_bg_image'][0] ) ) {
					$image_id = $term_meta['header_bg_image'][0];
				}

				if ( ! $image_id && ! empty( $terms[0]->parent ) && ( $term_meta = get_term_meta( $terms[0]->parent ) ) && ! empty( $term_meta['header_bg_image'][0] ) ) {
					$image_id = $term_meta['header_bg_image'][0];
				}
			}

			if ( ! $image_id && ideapark_mod( 'header_image_catalog__attachment_id' ) ) {
				$image_id = ideapark_mod( 'header_image_catalog__attachment_id' );
			}

		} elseif ( $post_type == 'product' ) {
			if ( ( $terms = wp_get_post_terms( $post->ID, 'product_cat' ) ) && ( ! empty( $terms[0] ) ) ) {
				$term_meta = get_term_meta( $terms[0]->term_id );
				if ( ! empty( $term_meta['header_bg_image'][0] ) ) {
					$image_id = $term_meta['header_bg_image'][0];
				}

				if ( ! $image_id && ! empty( $terms[0]->parent ) && ( $term_meta = get_term_meta( $terms[0]->parent ) ) && ! empty( $term_meta['header_bg_image'][0] ) ) {
					$image_id = $term_meta['header_bg_image'][0];
				}
			}
			if ( ! $image_id && ideapark_mod( 'header_image_shop__attachment_id' ) ) {
				$image_id = ideapark_mod( 'header_image_shop__attachment_id' );
			}
		}
	}
}

if ( ! $image_id && is_post_type_archive( 'catalog' ) && ideapark_mod( 'header_image_catalog__attachment_id' ) ) {
	$image_id = ideapark_mod( 'header_image_catalog__attachment_id' );
}

if ( ! $image_id && ideapark_woocommerce_on() && is_woocommerce() && is_shop() && ideapark_mod( 'header_image_shop__attachment_id' ) ) {
	$image_id = ideapark_mod( 'header_image_shop__attachment_id' );
}

if ( ! $image_id ) {
	$image_id = ideapark_mod( 'header_image__attachment_id' );
}

if ( ! $icon_id ) {
	$icon_id = ideapark_mod( 'header_icon__attachment_id' );
}

if ( ! $title && $is_h1 ) {
	$title = get_the_title();
}
?>

<?php if ( ideapark_mod( 'header_type' ) == 'header-type-3' || $title ) { ?>
	<header
		class="l-section c-page-header c-page-header--<?php echo ideapark_mod( 'header_type' ); ?><?php ideapark_class( ! $title, 'c-page-header--small' ); ?><?php ideapark_class( $with_filter, 'c-page-header--filter' ); ?>">
		<?php
		if ( ! empty( $image_id ) && ( $type = get_post_mime_type( $image_id ) ) ) {
			if ( $type == 'image/svg+xml' ) {
				echo ideapark_get_inline_svg( $image_id, 'c-page-header__image' );
			} elseif ( $image_meta = ideapark_image_meta( $image_id, 'full' ) ) {
				echo ideapark_img( $image_meta, 'c-page-header__image' );
			} ?>
		<?php } ?>
		<div class="c-page-header__shadow"></div>
		<?php echo ideapark_get_inline_svg( IDEAPARK_DIR . '/assets/img/bar-page-header.svg', 'c-page-header__bar' ); ?>
		<div
			class="c-page-header__wrap <?php ideapark_class( $with_filter, 'c-page-header__wrap--filter', '' ); ?><?php ideapark_class( ! $title, 'c-page-header__wrap-small l-section__container' ); ?>">
			<?php
			if ( ! $with_filter && ! empty( $icon_id ) && ( $type = get_post_mime_type( $icon_id ) ) ) {
				if ( $type == 'image/svg+xml' ) {
					echo ideapark_get_inline_svg( $icon_id, 'c-page-header__icon' );
				} elseif ( $image_meta = ideapark_image_meta( $icon_id, 'thumbnail' ) ) {
					echo ideapark_img( $image_meta, 'c-page-header__icon' );
				} ?>
				<div>
					<i class="ip-decor c-page-header__decor"></i>
				</div>
			<?php } ?>
			<?php if ( $title ) { ?>
				<?php if ( $is_h1 ) { ?>
					<h1 class="l-section__container c-page-header__title"><?php echo ideapark_wrap( $title ); ?></h1>
				<?php } else { ?>
					<div class="l-section__container c-page-header__title"><?php echo ideapark_wrap( $title ); ?></div>
				<?php } ?>
			<?php } ?>
			<?php if ( empty( $ideapark_var['hide_breadcrumbs'] ) || $ideapark_var['hide_breadcrumbs'] != 'yes' ) { ?>
				<?php get_template_part( 'templates/breadcrumbs' ); ?>
			<?php } ?>
			<?php if ( $with_filter ) { ?>
				<?php ideapark_get_template_part( 'templates/filter', [ 'layout' => 'header' ] ); ?>
			<?php } ?>
		</div>
	</header>
<?php } ?>

<?php if ( ideapark_woocommerce_on() && function_exists( 'wc_print_notices' ) && isset( WC()->session ) && ! ( strtolower( filter_input( INPUT_SERVER, 'HTTP_X_REQUESTED_WITH' ) ) === 'xmlhttprequest' ) ) { ?>
	<div class="woocommerce-notices-wrapper">
		<?php wc_print_notices(); ?>
	</div>
<?php } ?>
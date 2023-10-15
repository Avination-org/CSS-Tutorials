<?php

if ( ! function_exists( 'ideapark_setup_woocommerce' ) ) {
	function ideapark_setup_woocommerce() {

		if ( ( ideapark_is_requset( 'frontend' ) || ideapark_is_elementor_preview() ) && ideapark_woocommerce_on() ) {

			if ( ideapark_is_elementor_preview() ) {
				WC()->frontend_includes();
			}

			/* Product loop page */

			ideapark_ra( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
			ideapark_ra( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
			ideapark_ra( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );

			ideapark_ra( 'woocommerce_before_shop_loop', 'woocommerce_output_all_notices', 10 );

			add_filter( 'woocommerce_gallery_image_size', function (){return 'woocommerce_single';}, 99, 1 );

			add_filter('woocommerce_post_class', function($classes){
				$classes[] = 'product-image--' . ideapark_mod( 'grid_image_fit' );
				return $classes;
			});

			add_filter('product_cat_class', function($classes){
				$classes[] = 'product-image--' . ideapark_mod( 'subcat_image_fit' );
				return $classes;
			});

			add_action( 'woocommerce_after_page_title', 'woocommerce_output_all_notices', 10 );

			add_action( 'woocommerce_before_subcategory_title', function () { ?><div class="product-thumb-wrap"><?php }, 9 );
			add_action( 'woocommerce_before_subcategory_title', function () { ?></div><?php }, 11 );

			add_action( 'woocommerce_before_shop_loop_item_title', function () { ?><div class="product-thumb-wrap"><?php }, 9 );
			add_action( 'woocommerce_before_shop_loop_item_title', function () { ?>
				<div class="product-image-overlay"></div><i
					class="ip-plus product-image-plus"></i></div><?php }, 15 );
			add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_link_close', 20 );
			add_action( 'woocommerce_before_shop_loop_item_title', function () { ?><div class="product-content-wrap"><?php }, 25 );
			add_action( 'woocommerce_after_shop_loop_item', function () { ?></div><?php }, 9 );

			add_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_link_open', 9 );
			add_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_link_close', 11 );

			add_action( 'woocommerce_after_shop_loop_item_title', function () { ?><div class="product-price-wrap"><?php }, 8 );
		add_action( 'woocommerce_after_shop_loop_item_title', function () { ?>
			<div
				class="product-price-wrap-col"><?php echo ideapark_wrap( ideapark_mod( 'product_price_block_title' ), '<div class="product-price-total-title">', '</div>' ) . ideapark_wrap( ideapark_mod( 'product_price_block_tax' ), '<div class="product-price-total-tax">', '</div>' ); ?></div>
			<div class="product-price-wrap-col"><?php }, 8 );
			add_action( 'woocommerce_after_shop_loop_item_title', function () { ?></div></div><?php }, 11 );

			add_filter( 'woocommerce_loop_add_to_cart_link', function ( $text, $product ) {
				$text = str_replace( 'class="', 'class="c-button c-button--outline ', $text );
				if ( $product->is_type( 'simple' ) ) {
					$text = str_replace( '</a>', '<i class="ip-cart-button c-button__arrow c-button__arrow--cart"></i></a>', $text );
				} else {
					$text = str_replace( '</a>', '<i class="ip-double-arrow c-button__arrow"></i></a>', $text );
				}

				return $text;
			}, 99, 2 );

			add_filter( 'woocommerce_format_price_range', function ( $price, $from, $to ) {
				return '<span class="range">' . $price . '</span>';
			}, 99, 3 );

			add_filter( 'woocommerce_after_output_product_categories', function () { return '</ul><ul class="products">'; } );

			/* Product page */
			if ( ideapark_mod( 'product_title_in_header' ) ) {
				ideapark_ra( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
			}

			add_action( 'woocommerce_product_thumbnails', function () {
				global $product;
				$product_id     = $product->get_id();
				$attachment_ids = $product->get_gallery_image_ids();
				if ( ! is_array( $attachment_ids ) ) {
					$attachment_ids = [];
				}
				if ( get_post_meta( $product_id, '_thumbnail_id', true ) ) {
					array_unshift( $attachment_ids, get_post_thumbnail_id( $product_id ) );
				}

				if ( $attachment_ids && $product->get_image_id() && sizeof( $attachment_ids ) > 1 ) { ?>
					</figure>
					<div
						class="c-product__thumbs h-carousel h-carousel--nav-hide h-carousel--dots-hide js-product-thumbs-carousel">
						<?php foreach ( $attachment_ids as $index => $attachment_id ) {
							$thumb = wp_get_attachment_image( $attachment_id, 'woocommerce_gallery_thumbnail', false, [
								'alt'   => get_the_title( $product_id ),
								'class' => 'c-product__thumbs-img'
							] );
							?>
							<?php echo sprintf( '<div class="c-product__thumbs-item ' . ( ! $index ? 'active' : '' ) . '"><button type="button" class="h-cb js-single-product-thumb wc-thumb" data-index="%s">%s</button></div>',
								$index,
								$thumb
							); ?>
						<?php } ?>
					</div>
					<figure class="h-hidden">
					<?php
				}
			}, 30 );

			/* Cart page */
			ideapark_ra( 'woocommerce_before_cart', 'woocommerce_output_all_notices', 10 );
			add_action( 'woocommerce_before_cart_totals', 'woocommerce_checkout_coupon_form', 10 );
			if ( filter_input( INPUT_SERVER, 'HTTP_X_REQUESTED_WITH' ) ) {
				add_action( 'woocommerce_before_cart_table', 'woocommerce_output_all_notices', 10 );
			}

			/* Checkout page */
			ideapark_ra( 'woocommerce_before_checkout_form_cart_notices', 'woocommerce_output_all_notices', 10 );
			ideapark_ra( 'woocommerce_before_checkout_form', 'woocommerce_output_all_notices', 10 );
			ideapark_ra( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
			add_action( 'woocommerce_checkout_before_order_review', 'woocommerce_checkout_coupon_form', 10 );

			/* All Account pages */
			ideapark_ra( 'woocommerce_account_content', 'woocommerce_output_all_notices', 5 );

			/* All WC pages */
			ideapark_ra( 'woocommerce_before_lost_password_form', 'woocommerce_output_all_notices', 10 );
			ideapark_ra( 'woocommerce_before_reset_password_form', 'woocommerce_output_all_notices', 10 );
			ideapark_ra( 'woocommerce_before_customer_login_form', 'woocommerce_output_all_notices', 10 );

		}
	}
}

if ( ! function_exists( 'ideapark_woocommerce_breadcrumbs' ) ) {
	function ideapark_woocommerce_breadcrumbs() {
		return [
			'delimiter'   => '',
			'wrap_before' => '<nav class="c-breadcrumbs"><ol class= "c-breadcrumbs__list">',
			'wrap_after'  => '</ol></nav>',
			'before'      => '<li class= "c-breadcrumbs__item">',
			'after'       => '</li>',
			'home'        => esc_html_x( 'Home', 'breadcrumb', 'antek' ),
		];
	}
}

if ( ! function_exists( 'ideapark_woocommerce_account_menu_items' ) ) {
	function ideapark_woocommerce_account_menu_items( $items ) {
		unset( $items['customer-logout'] );

		return $items;
	}
}

if ( ! function_exists( 'ideapark_remove_product_description_heading' ) ) {
	function ideapark_remove_product_description_heading() {
		return '';
	}
}

if ( ! function_exists( 'ideapark_woocommerce_search_form' ) ) {
	function ideapark_woocommerce_search_form() {
		if ( is_search() ) {
			echo '<div class="c-product-search-form">';
			get_search_form();
			echo '</div>';
		}
	}
}

if ( ! function_exists( 'ideapark_woocommerce_pagination_args' ) ) {
	function ideapark_woocommerce_pagination_args( $args ) {
		$args['prev_text'] = '<i class="ip-double-arrow page-numbers__prev-ico"></i>';
		$args['next_text'] = '<i class="ip-double-arrow page-numbers__next-ico"></i>';
		$args['end_size']  = 1;
		$args['mid_size']  = 1;

		return $args;
	}
}

if ( ! function_exists( 'ideapark_add_to_cart_ajax_notice' ) ) {
	function ideapark_add_to_cart_ajax_notice( $product_id ) {
		wc_add_to_cart_message( $product_id );
	}
}

if ( ! function_exists( 'ideapark_excerpt_in_product_archives' ) ) {
	function ideapark_excerpt_in_product_archives() {
		if ( ideapark_mod( 'product_short_description' ) ) {
			?>
			<div class="woocommerce-loop-product__excerpt">
				<?php the_excerpt(); ?>
			</div>
			<?php
		}
	}
}

if ( ! function_exists( 'ideapark_cart_item_thumbnail' ) ) {
	function ideapark_cart_item_thumbnail( $product_get_image, $cart_item, $cart_item_key ) {
		if ( empty( $cart_item['ideapark_antek'] ) ) {
			$_product          = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_get_image = $_product->get_image( 'woocommerce_gallery_thumbnail' );
		}

		return $product_get_image;
	}
}

if ( ! function_exists( 'ideapark_header_add_to_cart_fragment' ) ) {
	function ideapark_header_add_to_cart_fragment( $fragments ) {

		ob_start();
		wc_print_notices();
		$fragments['ideapark_notice'] = ob_get_clean();
		$fragments['.js-cart-info']   = ideapark_cart_info();

		return $fragments;
	}
}

if ( ! function_exists( 'ideapark_wc_product_post_class' ) ) {
	function ideapark_wc_product_post_class( $classes, $class = '', $post_id = 0 ) {
		if ( is_singular( [ 'product', 'product_variation' ] ) && $post_id == get_queried_object_id() ) {
			$classes[] = 'c-product';
			$classes[] = 'c-product--image-' . ideapark_mod( 'product_image_fit' );
		}

		return $classes;
	}
}


if ( ! function_exists( 'ideapark_related_products_args' ) ) {
	function ideapark_related_products_args( $args ) {
		$args['posts_per_page'] = 3;

		return $args;
	}
}

if ( ! function_exists( 'ideapark_author_box_gravatar_size' ) ) {
	function ideapark_author_box_gravatar_size( $size ) {
		return 90;
	}
}

if ( ! function_exists( 'ideapark_ajax_product_images' ) ) {
	function ideapark_ajax_product_images() {
		ob_start();
		if ( isset( $_REQUEST['product_id'] ) && ( $product_id = absint( $_REQUEST['product_id'] ) ) && ( $product = wc_get_product( $product_id ) ) ) {
			$variation_id   = isset( $_REQUEST['variation_id'] ) ? absint( $_REQUEST['variation_id'] ) : 0;
			$attachment_ids = $product->get_gallery_image_ids();
			$images         = [];
			if ( $variation_id && ( $attachment_id = get_post_thumbnail_id( $variation_id ) ) ) {
				array_unshift( $attachment_ids, $attachment_id );
			} else if ( $attachment_id = get_post_thumbnail_id( $product_id ) ) {
				array_unshift( $attachment_ids, $attachment_id );
			}
			foreach ( $attachment_ids as $attachment_id ) {
				$image    = wp_get_attachment_image_src( $attachment_id, 'full' );
				$images[] = [
					'src' => $image[0],
					'w'   => $image[1],
					'h'   => $image[2],
				];
			}

			if ( $video_url = get_post_meta( $product_id, '_ip_product_video_url', true ) ) {
				$images[] = [
					'html' => ideapark_wrap( wp_oembed_get( $video_url ), '<div class="pswp__video-wrap">', '</div>' )
				];
			}
			ob_end_clean();
			wp_send_json( [ 'images' => $images ] );
		}
		ob_end_clean();
	}
}

if ( ! function_exists( 'ideapark_product_id' ) ) {
	function ideapark_product_id() {
		global $post;
		?>
		<input type="hidden" class="js-product-id" value="<?php echo esc_attr( $post->ID ); ?>"/>
	<?php }
}

if ( ! function_exists( 'ideapark_product_sidebar_start' ) ) {
	function ideapark_product_sidebar_start() {
		if ( ideapark_mod( 'product_sidebar' ) && is_active_sidebar( 'product-sidebar' ) ) { ?>
			<div class="l-section__content l-section__content--with-sidebar l-section__content--st-width">
		<?php }
	}
}

if ( ! function_exists( 'ideapark_product_sidebar_end' ) ) {
	function ideapark_product_sidebar_end() {
		if ( ideapark_mod( 'product_sidebar' ) && is_active_sidebar( 'product-sidebar' ) ) { ?>
			</div>
			<div class="l-section__sidebar l-section__sidebar--right">
				<?php get_sidebar( 'product-page' ); ?>
			</div>
		<?php }
	}
}

if ( ! function_exists( 'ideapark_sale_flash' ) ) {
	function ideapark_sale_flash( $html, $post, $product ) {
		return '<div class="onsale__wrap">' . $html . '</div>';
	}
}

if ( ! function_exists( 'ideapark_cart_info' ) ) {
	function ideapark_cart_info() {
		$cart_count = wc()->cart->get_cart_contents_count();

		return '<span class="js-cart-info">'
		       . ( ! wc()->cart->is_empty() ? ideapark_wrap( esc_html( $cart_count ), '<span class="c-header__cart-count js-cart-count">', '</span>' ) : '' )
		       . '</span>';
	}
}

if ( ! function_exists( 'ideapark_woocommerce_demo_store' ) ) {
	function ideapark_woocommerce_demo_store( $notice ) {
		return str_replace( 'woocommerce-store-notice ', 'woocommerce-store-notice woocommerce-store-notice--' . ideapark_mod( 'store_notice' ) . ' ', $notice );
	}
}

if ( ! function_exists( 'ideapark_remove_subtotal' ) ) {
	function ideapark_remove_subtotal( $totals ) {
		if ( ! ideapark_mod( 'show_subtotal' ) ) {
			unset( $totals['cart_subtotal'] );
		}

		return $totals;
	}
}

if ( ! function_exists( 'ideapark_header_categories' ) ) {
	function ideapark_header_categories( $_parent_id = null ) {
		if ( ideapark_woocommerce_on() && $_parent_id !== null ) {

			$parent_id = $_parent_id ?: ( is_product_category() ? get_queried_object_id() : 0 );
			ob_start();
			woocommerce_output_product_categories(
				[ 'parent_id' => $parent_id ]
			);
			$subcategories = ob_get_clean();

			if ( $subcategories ) {
				$subcategories = str_replace('<h2', '<div', $subcategories);
				$subcategories = str_replace('</h2', '</div', $subcategories);
				echo ideapark_wrap( $subcategories, '<ul class="products">', '</ul>' );
			}

			return ! ! $subcategories;
		} else {
			return false;
		}
	}
}

if ( IDEAPARK_IS_AJAX_IMAGES ) {
	add_action( 'wp_ajax_ideapark_product_images', 'ideapark_ajax_product_images' );
	add_action( 'wp_ajax_nopriv_ideapark_product_images', 'ideapark_ajax_product_images' );
} else {
	add_action( 'wp_loaded', 'ideapark_setup_woocommerce', 99 );
	add_action( 'woocommerce_before_shop_loop', 'ideapark_woocommerce_search_form', 30 );
	add_action( 'woocommerce_ajax_added_to_cart', 'ideapark_add_to_cart_ajax_notice' );
	add_action( 'woocommerce_after_shop_loop_item_title', 'ideapark_excerpt_in_product_archives', 40 );
	add_action( 'woocommerce_before_single_product_summary', 'ideapark_product_id', 1 );
	add_action( 'woocommerce_before_single_product_summary', 'ideapark_product_sidebar_start', 2 );
	add_action( 'woocommerce_after_single_product_summary', 'ideapark_product_sidebar_end', 11 );

	add_filter( 'woocommerce_cart_item_thumbnail', 'ideapark_cart_item_thumbnail', 10, 3 );
	add_filter( 'woocommerce_enqueue_styles', '__return_false' );
	add_filter( 'woocommerce_show_page_title', '__return_false' );
	add_filter( 'woocommerce_product_additional_information_heading', '__return_false' );
	add_filter( 'woocommerce_show_variation_price', '__return_true' );
	add_filter( 'woocommerce_breadcrumb_defaults', 'ideapark_woocommerce_breadcrumbs' );
	add_filter( 'woocommerce_account_menu_items', 'ideapark_woocommerce_account_menu_items' );
	add_filter( 'woocommerce_product_description_heading', 'ideapark_remove_product_description_heading' );
	add_filter( 'woocommerce_pagination_args', 'ideapark_woocommerce_pagination_args' );
	add_filter( 'woocommerce_add_to_cart_fragments', 'ideapark_header_add_to_cart_fragment' );
	add_filter( 'woocommerce_output_related_products_args', 'ideapark_related_products_args', 20 );
	add_filter( 'post_class', 'ideapark_wc_product_post_class', 99, 3 );
	add_filter( 'genesis_author_box_gravatar_size', 'ideapark_author_box_gravatar_size' );
	add_filter( 'woocommerce_review_gravatar_size', 'ideapark_author_box_gravatar_size' );
	add_filter( 'woocommerce_sale_flash', 'ideapark_sale_flash', 10, 3 );
	add_filter( 'woocommerce_demo_store', 'ideapark_woocommerce_demo_store' );
	add_filter( 'woocommerce_get_order_item_totals', 'ideapark_remove_subtotal', 100, 1 );
}
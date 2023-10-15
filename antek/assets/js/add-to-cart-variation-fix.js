(function ($, root, undefined) {
	"use strict";
	/**
	 * Sets product images for the chosen variation
	 */
	$.fn.wc_variations_image_update = function (variation) {
		var $form = this,
			$product = $form.closest('.product'),
			$product_gallery = $product.find('.js-single-product-carousel'),
			$product_img_wrap = $product_gallery.find('.c-product__slider-item').first(),
			$product_img = $product_img_wrap.find('.c-product__slider-img'),
			$product_link = $product_img_wrap.find('a').first(),
			$product_zoom = $product_img_wrap.find('.js-product-zoom'),
			$gallery_img = $product.find('.c-product__thumbs-img').first();
		
		if (variation && variation.image && variation.image.src && variation.image.src.length > 1) {
			$product_img.wc_set_variation_attr('src', variation.image.src);
			$product_img.wc_set_variation_attr('height', variation.image.src_h);
			$product_img.wc_set_variation_attr('width', variation.image.src_w);
			$product_img.wc_set_variation_attr('srcset', variation.image.srcset);
			$product_img.wc_set_variation_attr('sizes', variation.image.sizes);
			$product_img.wc_set_variation_attr('title', variation.image.title);
			$product_img.wc_set_variation_attr('alt', variation.image.alt);
			$product_img.wc_set_variation_attr('data-src', variation.image.full_src);
			$product_img.wc_set_variation_attr('data-large_image', variation.image.full_src);
			$product_img.wc_set_variation_attr('data-large_image_width', variation.image.full_src_w);
			$product_img.wc_set_variation_attr('data-large_image_height', variation.image.full_src_h);
			$product_link.wc_set_variation_attr('href', variation.image.full_src);
			$product_img_wrap.wc_set_variation_attr('data-thumb', variation.image.src);
			if ($product_zoom.length) {
				var old_img = $product_zoom.data('img');
				$product_zoom.wc_set_variation_attr('data-img', variation.image.full_src);
				$product_zoom.data('img', $product_zoom.attr('data-img'));
				if (old_img != $product_zoom.data('img')) {
					$product_zoom.removeClass('init').trigger('zoom.destroy');
					ideapark_init_zoom();
				}
			}
			
			$gallery_img.wc_set_variation_attr('srcset', variation.image.srcset);
			$gallery_img.wc_set_variation_attr('src', variation.image.gallery_thumbnail_src);
		} else {
			$product_img.wc_reset_variation_attr('src');
			$product_img.wc_reset_variation_attr('width');
			$product_img.wc_reset_variation_attr('height');
			$product_img.wc_reset_variation_attr('srcset');
			$product_img.wc_reset_variation_attr('sizes');
			$product_img.wc_reset_variation_attr('title');
			$product_img.wc_reset_variation_attr('data-caption');
			$product_img.wc_reset_variation_attr('alt');
			$product_img.wc_reset_variation_attr('data-src');
			$product_img.wc_reset_variation_attr('data-large_image');
			$product_img.wc_reset_variation_attr('data-large_image_width');
			$product_img.wc_reset_variation_attr('data-large_image_height');
			$product_img_wrap.wc_reset_variation_attr('data-thumb');
			if ($product_zoom.length) {
				$product_zoom.wc_reset_variation_attr('data-img');
				$product_zoom.data('img', $product_zoom.attr('data-img'));
				$product_zoom.removeClass('init').trigger('zoom.destroy');
				ideapark_init_zoom();
			}
			$product_link.wc_reset_variation_attr('href');
			$gallery_img.wc_reset_variation_attr('src');
			$gallery_img.wc_reset_variation_attr('srcset');
		}
		
		window.setTimeout(function () {
			$(window).trigger('resize');
			$form.wc_maybe_trigger_slide_position_reset(variation);
			$product_gallery.trigger('woocommerce_gallery_init_zoom');
		}, 20);
	};
	
})(jQuery, this);
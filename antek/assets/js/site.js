(function ($, root, undefined) {
		"use strict";
		
		$.migrateMute = true;
		$.migrateTrace = false;
		
		var old_define;
		if (!ideapark_empty(requirejs)) {
			requirejs.config({
				baseUrl    : ideapark_wp_vars.themeUri + '/assets/js',
				paths      : {
					text: 'requirejs/text',
					css : 'requirejs/css',
					json: 'requirejs/json'
				},
				urlArgs    : function (id, url) {
					var args = '';
					
					if (url.indexOf('/css/photoswipe/') !== -1) {
						args = 'v=' + ideapark_wp_vars.stylesHash;
					}
					
					if (id === 'photoswipe/photoswipe.min' || id === 'photoswipe/photoswipe-ui-default.min') {
						args = 'v=' + ideapark_wp_vars.scriptsHash;
					}
					
					return args ? (url.indexOf('?') === -1 ? '?' : '&') + args : '';
				},
				waitSeconds: 0
			});
			
			old_define = root.define;
			root.define = null;
		}
		
		root.ideapark_videos = [];
		root.ideapark_players = [];
		root.ideapark_env_init = false;
		root.ideapark_slick_paused = false;
		root.ideapark_is_mobile = false;
		
		root.old_windows_width = 0;
		
		var $window = $(window);
		var ideapark_date_format = ideapark_wp_vars.dateFormat;
		
		var $ideapark_masonry_sidebar = $('.c-sidebar-wrap');
		var ideapark_masonry_sidebar_on = !!$ideapark_masonry_sidebar.length && $ideapark_masonry_sidebar.find('.widget').length > 2;
		var ideapark_is_masonry_init = false;
		var ideapark_masonry_sidebar_object = null;
		var ideapark_is_venobox_loaded = false;
		var $ideapark_mobile_menu = $('.js-mobile-menu');
		var ideapark_mobile_menu_initialized = false;
		var ideapark_mobile_menu_active = false;
		var ideapark_mobile_menu_page = -1;
		var ideapark_shop_sidebar_active = false;
		var $ideapark_shop_sidebar = $('.js-popup-sidebar');
		var $ideapark_shop_sidebar_open = $('.js-sidebar-button');
		var $ideapark_shop_sidebar_close = $('#ideapark-shop-sidebar-close');
		var $ideapark_shop_sidebar_wrap = $('.c-catalog-sidebar__wrap');
		var $ideapark_shop_sidebar_content = $('.c-catalog-sidebar__content');
		var ideapark_shop_sidebar_initialized = false;
		var $ideapark_search = $('#ideapark-ajax-search');
		var $ideapark_search_input = $('#ideapark-ajax-search-input');
		var $ideapark_search_result = $('#ideapark-ajax-search-result');
		var $ideapark_search_loader = $('<i class="h-loading c-header-search__loading"></i>');
		var ideapark_search_popup_active = false;
		var ideapark_search_input_filled = false;
		var $ideapark_callback_popup = $('.js-callback-popup');
		var $ideapark_desktop_mega_menu;
		var $ideapark_to_top_button = $('.js-to-top-button');
		var $ideapark_sticky_sidebar = $('.js-sticky-sidebar');
		var $ideapark_sticky_sidebar_nearby = $('.js-sticky-sidebar-nearby');
		var ideapark_sticky_sidebar_old_style = null;
		var ideapark_is_sticky_sidebar_inner = !!$ideapark_sticky_sidebar_nearby.find('.js-sticky-sidebar').length;
		var ideapark_sticky_sidebar_position = 'relative';
		var $ideapark_counters = $('.js-counter-number');
		
		var $slick_product_single = $('.slick-product-single');
		var $slick_product_single_slides = $('.slide', $slick_product_single);
		var $slick_product_thumbnails = $('.slick-product');
		
		var ideapark_vehicle_types_short_block = false;
		var $ideapark_vehicle_types_short_last = null;
		var ideapark_date_class_lock = false;
		var $ideapark_hint;
		var ideapark_date_hint_timeout;
		
		var ideapark_default_start;
		var ideapark_default_end;
		var ideapark_maximum_days;
		var ideapark_minimal_days_error = '';
		
		var ideapark_is_night = ideapark_wp_vars.bookingType == 'night';
		var ideapark_is_day = ideapark_wp_vars.bookingType == 'day';
		
		var $ideapark_desktop_sticky_row = $('.js-desktop-sticky-menu');
		var $ideapark_mobile_sticky_row = $('.js-mobile-sticky-menu');
		var ideapark_sticky_active = false;
		var ideapark_sticky_desktop_active = false;
		var ideapark_sticky_mobile_init = false;
		
		$(function () {
			root.define = old_define;
			
			$ideapark_counters = $('.js-counter-number');
			$('.js-search-button').click(function () {
				ideapark_search_popup(true);
				setTimeout(function () {
					$ideapark_search_input.focus();
				}, 500);
			});
			$('.js-tab-header').click(function (e) {
				var $this = $(this);
				e.preventDefault();
				if ($this.hasClass('c-login__tab-register')) {
					$this.addClass('c-login__tab-register--active').removeClass('c-login__tab-register--not-active');
					$('.c-login__tab-login--active').removeClass('c-login__tab-login--active').addClass('c-login__tab-login--not-active');
				} else {
					$this.addClass('c-login__tab-login--active').removeClass('c-login__tab-login--not-active');
					$('.c-login__tab-register--active').removeClass('c-login__tab-register--active').addClass('c-login__tab-register--not-active');
				}
				$('.c-login__form--active').removeClass('c-login__form--active');
				$('.' + $this.data('tab-class')).addClass('c-login__form--active');
			});
			
			$(document)
				.on('click', '.h-link-yes', function (e) {
					e.preventDefault();
					var $scope = $(this);
					if ($scope.data('ip-url') && $scope.data('ip-link') == 'yes') {
						if ($scope.data('ip-new-window') == 'yes') {
							window.open($scope.data('ip-url'));
						} else {
							location.href = $scope.data('ip-url');
						}
					}
				})
				.on('click', ".js-ip-video", function (e) {
					var $this = $(this);
					e.preventDefault();
					if (!ideapark_is_venobox_loaded) {
						ideapark_is_venobox_loaded = true;
						require([
							'venobox/venobox.min',
							'css!' + ideapark_wp_vars.themeUri + '/assets/css/venobox/venobox.min',
						], function () {
							$this.venobox().trigger('click');
						});
					} else {
						$this.venobox().trigger('click');
					}
				})
				.on('click', "[data-vbtype=\"iframe\"]", function (e) {
					var $this = $(this);
					e.preventDefault();
					if (!ideapark_is_venobox_loaded) {
						ideapark_is_venobox_loaded = true;
						require([
							'venobox/venobox.min',
							'css!' + ideapark_wp_vars.themeUri + '/assets/css/venobox/venobox.min',
						], function () {
							$this.venobox().trigger('click');
						});
					} else {
						$this.venobox().trigger('click');
					}
				})
				.on('ideapark.filter.update', function (e, $thisbutton) {
					$('.c-catalog-sidebar__sticky').addClass('c-catalog-sidebar__sticky--visible');
					
				})
				.on('adding_to_cart', function (e, $thisbutton) {
					$thisbutton.ideapark_button('loading', 16);
				})
				.on('added_to_cart', function (e, fragments, cart_hash, $thisbutton) {
					$thisbutton.ideapark_button('reset');
					if (typeof fragments.ideapark_notice !== 'undefined') {
						ideapark_show_notice(fragments.ideapark_notice);
					}
				})
				.on('updated_wc_div', function (e) {
					ideapark_search_notice();
				})
				.on('wc_fragments_loaded wc_fragment_refresh wc_fragments_refreshed', function (e) {
					if (ideapark_masonry_sidebar_object) {
						ideapark_masonry_sidebar_object.layout();
					}
				})
				.on('click', ".gallery a", function (e) {
					e.preventDefault();
				})
				.on('click', ".js-product-zoom", function (e) {
					e.preventDefault();
					var $button = $(this);
					var $button_loading = $button.find('.js-loading-wrap');
					if ($button.hasClass('js-product-zoom-video')) {
						$button_loading = $button.find('.c-product__video-wrap');
					}
					if ($button.hasClass('js-loading')) {
						return;
					}
					var index = 0;
					if (ideapark_isset($button.data('index'))) {
						$button_loading.ideapark_button('loading', 25);
						index = $button.data('index');
					} else {
						$button_loading.ideapark_button('loading');
					}
					var $product = $button.closest('.product');
					var variation_id = $product.find('.variation_id').val();
					require([
						'photoswipe/photoswipe.min',
						'photoswipe/photoswipe-ui-default.min',
						'json!' + ideapark_wp_vars.ajaxUrl + '?action=ideapark_product_images&product_id=' + $('.js-product-id').val() + (!ideapark_empty(variation_id) ? '&variation_id=' + variation_id : '') + '!bust',
						'css!' + ideapark_wp_vars.themeUri + '/assets/css/photoswipe/photoswipe',
						'css!' + ideapark_wp_vars.themeUri + '/assets/css/photoswipe/default-skin/default-skin'
					], function (PhotoSwipe, PhotoSwipeUI_Default, images) {
						$button_loading.ideapark_button('reset');
						if (images.images.length) {
							var options = {
								index              : index ? index : 0,
								showHideOpacity    : true,
								bgOpacity          : 1,
								loop               : false,
								closeOnVerticalDrag: false,
								mainClass          : '',
								barsSize           : {top: 0, bottom: 0},
								captionEl          : false,
								fullscreenEl       : false,
								zoomEl             : true,
								shareEl            : false,
								counterEl          : false,
								tapToClose         : true,
								tapToToggleControls: false,
								history            : false
							};
							
							var pswpElement = $('.pswp')[0];
							
							ideapark_wpadminbar_resize();
							
							var gallery = new PhotoSwipe(pswpElement, PhotoSwipeUI_Default, images.images, options);
							gallery.init();
							
							gallery.listen('afterChange', function () {
								if (!ideapark_empty(gallery.currItem.html)) {
									$('.pswp__video-wrap').fitVids();
								}
							});
							
							gallery.listen('close', function () {
								$('.pswp__video-wrap').html('');
							});
							
							$('.pswp__video-wrap').fitVids();
						}
					});
				})
				.on('click', ".js-vehicle-zoom", function (e) {
					e.preventDefault();
					var $button = $(this);
					var $button_loading = $button;
					if ($button.hasClass('js-loading')) {
						return;
					}
					var index = 0;
					if (ideapark_isset($button.data('index'))) {
						$button_loading.ideapark_button('loading', 25);
						index = $button.data('index');
					} else {
						$button_loading.ideapark_button('loading');
					}
					require([
						'photoswipe/photoswipe.min',
						'photoswipe/photoswipe-ui-default.min',
						'json!' + ideapark_wp_vars.ajaxUrl + '?action=ideapark_vehicle_images&product_id=' + $button.data('product-id') + '!bust',
						'css!' + ideapark_wp_vars.themeUri + '/assets/css/photoswipe/photoswipe',
						'css!' + ideapark_wp_vars.themeUri + '/assets/css/photoswipe/default-skin/default-skin'
					], function (PhotoSwipe, PhotoSwipeUI_Default, images) {
						$button_loading.ideapark_button('reset');
						if (images.images.length) {
							var options = {
								index              : index ? index : 0,
								showHideOpacity    : true,
								bgOpacity          : 1,
								loop               : false,
								closeOnVerticalDrag: false,
								mainClass          : '',
								barsSize           : {top: 0, bottom: 0},
								captionEl          : false,
								fullscreenEl       : false,
								zoomEl             : true,
								shareEl            : false,
								counterEl          : false,
								tapToClose         : true,
								tapToToggleControls: false,
								history            : false
							};
							
							var pswpElement = $('.pswp')[0];
							
							ideapark_wpadminbar_resize();
							
							var gallery = new PhotoSwipe(pswpElement, PhotoSwipeUI_Default, images.images, options);
							gallery.init();
							
							gallery.listen('afterChange', function () {
								if (!ideapark_empty(gallery.currItem.html)) {
									$('.pswp__video-wrap').fitVids();
								}
							});
							
							gallery.listen('close', function () {
								$('.pswp__video-wrap').html('');
							});
							
							$('.pswp__video-wrap').fitVids();
						}
					});
				})
				.on('click', ".js-ajax-search-all", function (e) {
					$('.js-search-form').submit();
				})
				.on('click', '.js-notice-close', function (e) {
					e.preventDefault();
					var $notice = $(this).closest('.woocommerce-notice');
					$notice.animate({
						opacity: 0,
					}, 500, function () {
						$notice.remove();
					});
				})
				.on('click', '.js-cart-coupon', function (e) {
					e.preventDefault();
					var $coupon = $(".c-cart__coupon-from-wrap");
					$coupon.toggleClass('c-cart__coupon-from-wrap--opened');
					$('.c-cart__select-svg').toggleClass('c-cart__select-svg--opened');
					if ($coupon.hasClass('c-cart__coupon-from-wrap--opened')) {
						setTimeout(function () {
							$coupon.find('input[type=text]').first().focus();
						}, 500);
					}
					return false;
				})
				.on('checkout_error updated_checkout applied_coupon removed_coupon', function () {
					var $notices = $('div.woocommerce-notice:not(.shown), div.woocommerce-error:not(.shown), div.woocommerce-message:not(.shown)');
					if ($notices.length) {
						$notices.detach();
						ideapark_show_notice($notices);
					}
				})
				.on('change', '#ship-to-different-address input', function () {
					if (ideapark_wp_vars.stickySidebar && $ideapark_sticky_sidebar_nearby.length && $ideapark_sticky_sidebar_nearby.length) {
						setTimeout(function () {
							delete root.ideapark_scroll_offset_last;
							if (ideapark_sticky_sidebar_old_style !== null) {
								$ideapark_sticky_sidebar.attr('style', ideapark_sticky_sidebar_old_style);
								ideapark_sticky_sidebar_old_style = null;
							}
							ideapark_sticky_sidebar();
						}, 1000);
					}
				})
				.on('click', ".js-apply-coupon", function () {
					
					var params = null;
					var is_cart = false;
					
					if (typeof wc_checkout_params != 'undefined') {
						params = wc_checkout_params;
						is_cart = false;
					}
					
					if (typeof wc_cart_params != 'undefined') {
						params = wc_cart_params;
						is_cart = true;
					}
					
					if (!params) {
						return false;
					}
					
					var $collaterals = $(this).closest('.c-cart__collaterals');
					
					if ($collaterals.is('.processing')) {
						return false;
					}
					
					$collaterals.addClass('processing').block({
						message   : null,
						overlayCSS: {
							background: '#fff',
							opacity   : 0.6
						}
					});
					
					var data = {
						security   : params.apply_coupon_nonce,
						coupon_code: $collaterals.find('input[name="coupon_code"]').val()
					};
					
					$.ajax({
						type    : 'POST',
						url     : params.wc_ajax_url.toString().replace('%%endpoint%%', 'apply_coupon'),
						data    : data,
						success : function (code) {
							if (code) {
								ideapark_show_notice(code);
								if (is_cart) {
									$.ajax({
										url     : params.wc_ajax_url.toString().replace('%%endpoint%%', 'get_cart_totals'),
										dataType: 'html',
										success : function (response) {
											$collaterals.html(response);
										},
										complete: function () {
											$collaterals.removeClass('processing').unblock();
										}
									});
								} else {
									$collaterals.removeClass('processing').unblock();
									$(document.body).trigger('update_checkout', {update_shipping_method: false});
								}
							}
						},
						dataType: 'html'
					});
					
					return false;
				})
				.on('click', '.wc-tabs li a', function (e) {
					e.preventDefault();
					var $tab = $(this);
					var $tabs_wrapper = $tab.closest('.wc-tabs-wrapper, .woocommerce-tabs');
					$tabs_wrapper.find('.wc-tab.visible').removeClass('visible');
					$tabs_wrapper.find('.wc-tab.current').removeClass('current');
					$tabs_wrapper.find($tab.attr('href')).addClass('current');
					
					setTimeout(function () {
						$tabs_wrapper.find($tab.attr('href')).addClass('visible');
					}, 100);
					
				});
			
			ideapark_wpadminbar_resize();
			ideapark_init_custom_select();
			ideapark_init_notice();
			ideapark_init_mobile_menu();
			
			ideapark_defer_action_add(function () {
				
				if (ideapark_wp_vars.jsDelay) {
					require([
						'moment-with-locales.min',
					], function (moment) {
						root.moment = moment;
						require([
							'daterangepicker.min',
						], function () {
							ideapark_init_date_range_picker();
							ideapark_init_date_range_picker_filter();
						});
					});
				} else {
					ideapark_init_date_range_picker();
					ideapark_init_date_range_picker_filter();
				}
				
				$ideapark_to_top_button.click(function () {
					$('html, body').animate({scrollTop: 0}, 800);
				});
				$(window).bind("pageshow", function () {
					$('.js-book-form').trigger('reset');
					var $input_start = $('.js-book-date-start');
					var $input_end = $('.js-book-date-end');
					var $input_range = $('.js-book-date-range');
					if ($input_start.length && $input_end.length) {
						moment.locale(ideapark_wp_vars.locale);
						var dateFormat = ideapark_date_format;
						var startDateText = moment($input_start.data('value'), dateFormat).format(dateFormat);
						var endDateText = moment($input_end.data('value'), dateFormat).format(dateFormat);
						$input_start.val(startDateText);
						$input_end.val(endDateText);
						$input_range.val(startDateText + '  —  ' + endDateText);
					}
				});
				$('.woocommerce-product-gallery__wrapper').addClass('js-single-product-carousel h-carousel h-carousel--compact h-carousel--hover');
				$('#tab-reviews').removeClass('entry-content');
				$('.js-filter-reset').click(function () {
					var $form = $('.js-filter-form');
					if ($form.length) {
						$('.js-type-all').first().trigger('click');
						if ($form.hasClass('js-filter-form--active')) {
							$form.submit();
						} else {
							document.location.href = $form.attr('action');
						}
					} else {
						document.location.href = $('.js-catalog-url').val();
					}
				});
				$('.js-filter-type').change(function () {
					var $this = $(this);
					var $form = $('.js-filter-form');
					var $hidden_permalink = $('.js-filter-permalink');
					if ($hidden_permalink.length) {
						$hidden_permalink.remove();
						var params = $.parseParams($this.val().replace(/(^.*\?)/, ''));
						if (params) {
							for (var prop in params) {
								var $new_param = $('<input class="js-filter-permalink" type="hidden">').attr('name', prop).attr('value', params[prop]);
								$form.prepend($new_param);
							}
						}
					}
					$form.attr('action', $this.val());
				});
				$('.js-filter-type-widget').click(function () {
					$(document).trigger('ideapark.filter.update');
					var $this = $(this);
					var $form = $('.js-filter-form');
					var $widget = $this.closest('.c-filter-types__wrap');
					$widget.find('.js-filter-type-widget').not(this).prop('checked', false);
					var $hidden_permalink = $('.js-filter-permalink');
					if ($hidden_permalink.length) {
						var params = $.parseParams($this.val().replace(/(^.*\?)/, ''));
						if (params) {
							for (var prop in params) {
								$hidden_permalink.attr('name', prop);
								$hidden_permalink.attr('value', params[prop]);
							}
						}
					}
					if ($form.length) {
						$form.attr('action', $this.val());
					}
				});
				if ($('.js-filter-type-widget:checked').hasClass('js-type')) {
					$('.c-filter-reset.h-hidden').removeClass('h-hidden');
				}
				var ideapark_vehicle_types_tab_timeout = null;
				$('.js-vehicle-types-tab').on('mouseover click', function () {
					
					if (ideapark_vehicle_types_tab_timeout !== null) {
						clearTimeout(ideapark_vehicle_types_tab_timeout);
						ideapark_vehicle_types_tab_timeout = null;
					}
					
					var $this = $(this);
					if ($this.hasClass('active')) {
						return;
					}
					$('.c-vehicle-types__item--layout-2.active').removeClass('active');
					$('.c-vehicle-types__tab.active').removeClass('active visible');
					$this.addClass('active');
					
					var $tab_content = $('.c-vehicle-types__tab[data-id="' + $this.data('id') + '"]');
					
					$tab_content.addClass('visible');
					
					ideapark_vehicle_types_tab_timeout = setTimeout(function () {
						$tab_content.addClass('active');
						$('.c-vehicle-types__tab.visible:not(.active)').removeClass('visible');
						ideapark_vehicle_types_tab_timeout = null;
					}, 100);
				});
				$('.c-vehicle-types__item').each(function () {
					var $this = $(this);
					$this.css({'min-height': $this.outerHeight() + 'px'});
				});
				$('.js-extra-minus').click(function () {
					var $this = $(this);
					var $li = $this.closest('.c-vehicle-book__extra-item');
					var $qty = $li.find('.js-extra-qty');
					var qty = parseInt($qty.val());
					if (qty > 0) {
						qty--;
						$qty.val(qty);
					}
					
					ideapark_get_total();
				});
				$('.js-extra-plus').click(function () {
					var $this = $(this);
					var $li = $this.closest('.c-vehicle-book__extra-item');
					var $qty = $li.find('.js-extra-qty');
					var qty = parseInt($qty.val());
					var max = parseInt($li.data('max'));
					if (qty < max) {
						qty++;
						$qty.val(qty);
					}
					
					ideapark_get_total();
				});
				$('.js-extra-chk').change(function () {
					var $extra_qty_field = $(this).parent().find(".js-extra-qty");
					$extra_qty_field.val($(this).prop('checked') ? 1 : 0).trigger('change');
				});
				$('.js-extra-qty,.js-book-drop-off,.js-book-pick-up,.js-book-delivery').change(function () {
					ideapark_get_total();
				});
				$('.js-book').click(function () {
					var $button = $(this);
					if ($('.js-book-total').text() == '') {
						return;
					}
					$button.ideapark_button('loading', 22);
					$('.js-book-action').val('ideapark_book');
					$.ajax({
						url     : ideapark_wp_vars.ajaxUrl,
						type    : 'POST',
						data    : $('.js-book-form').serialize(),
						dataType: 'json',
						success : function (results) {
							if (results.redirect) {
								document.location.href = results.redirect;
							} else if (results.error) {
								$button.ideapark_button('reset');
								ideapark_show_notice_error(results.error);
							} else if (results.message) {
								$button.ideapark_button('reset');
								ideapark_show_notice_success(results.message);
							} else {
								$button.ideapark_button('reset');
							}
						}
					});
				});
				$('.js-vehicle-types-short').on('mouseover', function () {
					var $this = $(this);
					$ideapark_vehicle_types_short_last = $this;
					if (ideapark_vehicle_types_short_block) {
						return;
					}
					var $block = $this.closest('.c-vehicle-types');
					var $bottom = $('.c-vehicle-types__bottom', $this);
					if ($this.hasClass('active')) {
						return;
					}
					
					$('.c-vehicle-types__short.active').removeClass('active');
					var $tab_content = $('.c-vehicle-types__short[data-id="' + $this.data('id') + '"]');
					var left = $this.offset().left - $block.offset().left;
					var top = $bottom.offset().top - $block.offset().top;
					
					$tab_content.css({left: left + 'px', top: top + 'px'}).addClass('active');
					
				}).on('mouseout', function () {
					$ideapark_vehicle_types_short_last = null;
					$('.c-vehicle-types__short.active').removeClass('active');
				});
				$('.js-ordering-sort').change(function () {
					ideapark_cookies.set('sort_' + ideapark_wp_vars.cookieHash, $(this).val(), 31536000);
					ideapark_cookies.set('reset_page_' + ideapark_wp_vars.cookieHash, 1, 10);
					document.location.reload();
				});
				$('.js-sidebar-filter-btn').click(function () {
					var exclude = '';
					var url = '';
					
					$('.js-filter-price').each(function () {
						var $slider = $(this);
						var slug = $slider.data('slug');
						
						var $min_price = $('.js-filter-price-min_price', $slider);
						var $max_price = $('.js-filter-price-max_price', $slider);
						
						if ($min_price.length && parseFloat($min_price.val()) === parseFloat($min_price.data('min'))) {
							exclude += ':not([name="min_' + slug + '"])';
						}
						if ($max_price.length && parseFloat($max_price.val()) === parseFloat($max_price.data('max'))) {
							exclude += ':not([name="max_' + slug + '"])';
						}
					});
					
					var $form = $('.js-filter-form');
					var $type_filter = $('.js-filter-type-widget:checked');
					if ($form.length) {
						url = $form.attr('action');
					} else if ($type_filter.length) {
						url = $type_filter.val();
					} else {
						url = $('.js-catalog-url').val();
					}
					var params = $('.js-filter-field' + exclude).serialize();
					document.location.href = url + (params ? (url.indexOf('?') === -1 ? '?' : '&') + params : '');
				});
				$('.js-mega-menu a[href^="#"], .js-mobile-top-menu a[href^="#"], .js-top-menu a[href^="#"]').click(ideapark_hash_menu_animate);
				$('.js-ordering-map').click(function () {
					var $this = $(this);
					if ($this.hasClass('active')) {
						ideapark_cookies.remove('map-mode_' + ideapark_wp_vars.cookieHash);
						$this.removeClass('active');
					} else {
						ideapark_cookies.set('map-mode_' + ideapark_wp_vars.cookieHash, 'on', 31536000);
						$this.addClass('active');
					}
				});
				$(".js-ip-video").attr('data-vbtype', 'video').attr('data-autoplay', 'true');
				if ($ideapark_callback_popup.length) {
					
					$ideapark_callback_popup.each(function () {
						var $popup = $(this);
						var $button = $($popup.data('button'));
						$popup.removeClass('c-header__callback-popup--disabled');
						
						$button.click(function () {
							ideapark_mobile_menu_popup(false);
							$popup.addClass('c-header__callback-popup--active');
							bodyScrollLock.disableBodyScroll($('.c-header__callback-content', $popup)[0]);
						});
						
						$('.js-callback-close', $popup).click(function () {
							$popup.removeClass('c-header__callback-popup--disabled');
							$popup.toggleClass('c-header__callback-popup--active');
							bodyScrollLock.clearAllBodyScrollLocks();
						});
						
						$(document).on('ideapark.wpadminbar.scroll', function (event, wpadminbar_height) {
							$popup.css({
								transform   : 'translateY(' + wpadminbar_height + 'px)',
								'max-height': 'calc(100% - ' + wpadminbar_height + 'px)'
							});
						});
					});
					
					if (ideapark_wp_vars.priceRequestField || ideapark_wp_vars.requestField) {
						$('.js-request-price').click(function () {
							var $field = $('.js-callback-popup[data-button=".js-request-price"] [name="' + (ideapark_wp_vars.priceRequestField ? ideapark_wp_vars.priceRequestField.trim() : ideapark_wp_vars.requestField.trim()) + '"]');
							if (!$field.length && ideapark_wp_vars.requestField) {
								$field = $('.js-callback-popup[data-button=".js-request-price"] [name="' + ideapark_wp_vars.requestField.trim() + '"]');
							}
							var names = ideapark_wp_vars.priceRequestNames;
							var disable_booking = ideapark_wp_vars.disableBooking;
							var info = '';
							
							if ($field.length) {
								if (disable_booking) {
									info = $('.c-vehicle-details__title').text() + "\n";
								} else {
									var dateFormat = ideapark_date_format;
									var startDate = moment($('.js-book-date-start').val(), dateFormat);
									var endDate = moment($('.js-book-date-end').val(), dateFormat);
									var days = endDate.diff(startDate, 'days') + (ideapark_is_day ? 1 : 0);
									
									var new_start_time = '', new_end_time = '';
									if (ideapark_wp_vars.pickup_dropoff_time) {
										new_start_time = $('.js-book-date-start-time').val();
										if (new_start_time !== '') {
											new_start_time = ' — ' + new_start_time;
										}
										new_end_time = $('.js-book-date-end-time').val();
										if (new_end_time !== '') {
											new_end_time = ' — ' + new_end_time;
										}
									}
									
									info = names.name + ': ' + $('.c-vehicle-details__title').text() + "\n";
									info += names.start + ': ' + $('.js-book-date-start').val() + new_start_time + "\n";
									info += names.end + ': ' + $('.js-book-date-end').val() + new_end_time + "\n";
									info += names.days + ': ' + days + "\n";
									
									var puck_up_text = $('.js-book-pick-up-text').text().trim();
									var drop_off_text = $('.js-book-drop-off-text').text().trim();
									if (!drop_off_text) {
										drop_off_text = $('.js-book-drop-off.styled option:selected').text().trim();
									}
									
									if (puck_up_text) {
										if (!drop_off_text) {
											info += names.where + ': ' + puck_up_text + "\n";
										} else {
											info += names.pickup + ': ' + puck_up_text + "\n";
										}
									}
									
									if (drop_off_text) {
										info += names.dropoff + ': ' + drop_off_text + "\n";
									}
									
									var $extra = $('.js-extra-item');
									if ($extra.length) {
										info += names.extra + ': ' + "\n";
										$extra.each(function () {
											var $item = $(this);
											info += ' - ' + $('.c-vehicle-book__extra-title-text', $item).text().trim() + ' x ' + $('.js-extra-qty', $item).val() + "\n";
										});
									}
								}
								
								$field.val(info);
							}
						});
					}
				}
				
				ideapark_search_init();
				ideapark_mega_menu_init();
				ideapark_shop_sidebar_init();
				ideapark_init_single_product_carousel();
				ideapark_init_price_filter();
				ideapark_init_review_placeholder();
				ideapark_init_masonry();
				ideapark_init_favorites();
				
				$('.daterangepicker:not(.type-day):not(.type-night)').addClass('type-' + ideapark_wp_vars.bookingType);
				
				ideapark_scroll_action_add(function () {
					ideapark_to_top_button();
					ideapark_sticky_sidebar();
					ideapark_filter_sticky();
				});
				
				ideapark_resize_action_layout_add(function () {
					ideapark_init_mobile_menu();
					ideapark_search_popup(false);
					ideapark_mega_menu_sticky_init();
					ideapark_mobile_menu_popup(false);
					ideapark_sidebar_popup(false);
					ideapark_shop_sidebar_init();
					ideapark_set_notice_offset();
					ideapark_sticky_sidebar();
					ideapark_filter_sticky();
				});
				
				ideapark_resize_action_500_add(function () {
					$('.hasCustomSelect').trigger('clear').trigger('render');
					ideapark_init_masonry();
				});
				
				$('.entry-content').fitVids();
				
				$(document)
					.trigger('ideapark.wpadminbar.scroll', ideapark_adminbar_visible_height);
			});
			
			if (!ideapark_wp_vars.jsDelay || ideapark_wp_vars.elementorPreview || ($window.width() >= 768 && $window.width() <= 1189)) {
				ideapark_defer_action_run();
			}
			
			$(document)
				.on('ideapark.wpadminbar.scroll', ideapark_set_notice_offset)
				.trigger('ideapark.wpadminbar.scroll', ideapark_adminbar_visible_height);
		});
		
		
		root.ideapark_search_popup = function (show) {
			if (show && !ideapark_search_popup_active) {
				ideapark_mobile_menu_popup(false);
				ideapark_search_popup_active = true;
				$ideapark_search.addClass('c-header-search--active');
				$ideapark_search.find('.c-header-search__wrap').addClass('c-header-search__wrap--active');
				bodyScrollLock.disableBodyScroll(ideapark_is_mobile_layout ? $ideapark_search_result[0] : $ideapark_search[0]);
			} else if (ideapark_search_popup_active) {
				ideapark_search_popup_active = false;
				$ideapark_search.removeClass('c-header-search--active');
				$ideapark_search.find('.c-header-search__wrap').removeClass('c-header-search__wrap--active');
				bodyScrollLock.clearAllBodyScrollLocks();
			}
		};
		
		root.ideapark_search_clear = function () {
			$ideapark_search_input.val('').trigger('input').focus();
			$ideapark_search.off(ideapark_on_transition_end, ideapark_search_clear);
		};
		
		root.ideapark_wpadminbar_resize = function () {
			$ideapark_admin_bar = $('#wpadminbar');
			if ($ideapark_admin_bar.length) {
				var window_width = $window.width();
				if (window_width > 782 && $ideapark_admin_bar.hasClass('mobile')) {
					$ideapark_admin_bar.removeClass('mobile');
				} else if (window_width <= 782 && !$ideapark_admin_bar.hasClass('mobile')) {
					$ideapark_admin_bar.addClass('mobile');
				}
				ideapark_adminbar_height = $ideapark_admin_bar.outerHeight();
				ideapark_adminbar_position = $ideapark_admin_bar.css('position');
				
				if (ideapark_adminbar_position === 'fixed' || ideapark_adminbar_position === 'absolute') {
					$(".js-fixed").css({
						top         : ideapark_adminbar_visible_height,
						'max-height': 'calc(100% - ' + ideapark_adminbar_visible_height + 'px)'
					});
				} else {
					$(".js-fixed").css({
						top         : 0,
						'max-height': '100%'
					});
				}
				
				ideapark_wpadminbar_scroll();
			}
		};
		
		root.ideapark_wpadminbar_scroll = function () {
			if ($ideapark_admin_bar === null) {
				$ideapark_admin_bar = $('#wpadminbar');
			}
			if ($ideapark_admin_bar.length) {
				var scroll_top_mobile = window.scrollY;
				var top_new = 0;
				
				if (ideapark_adminbar_position === 'fixed') {
					top_new = ideapark_adminbar_height;
				} else {
					top_new = ideapark_adminbar_height - scroll_top_mobile;
					if (top_new < 0) {
						top_new = 0;
					}
				}
				
				if (ideapark_adminbar_visible_height != top_new) {
					ideapark_adminbar_visible_height = top_new;
					$(document).trigger('ideapark.wpadminbar.scroll', ideapark_adminbar_visible_height);
				}
			}
		};
		
		root.ideapark_open_photo_swipe = function (imageWrap, index) {
			var $this, $a, $img, items = [], size, item;
			$slick_product_single_slides.each(function () {
				$this = $(this);
				$a = $this.children('a');
				$img = $a.children('img');
				size = $a.data('size').split('x');
				
				item = {
					src : $a.attr('href'),
					w   : parseInt(size[0], 10),
					h   : parseInt(size[1], 10),
					msrc: $img.attr('src'),
					el  : $a[0]
				};
				
				items.push(item);
			});
			
			var options = {
				index              : index,
				showHideOpacity    : true,
				bgOpacity          : 1,
				loop               : false,
				closeOnVerticalDrag: false,
				mainClass          : ($slick_product_single_slides.length > 1) ? 'pswp--minimal--dark' : 'pswp--minimal--dark pswp--single--image',
				barsSize           : {top: 0, bottom: 0},
				captionEl          : false,
				fullscreenEl       : false,
				zoomEl             : false,
				shareEl            : false,
				counterEl          : false,
				tapToClose         : true,
				tapToToggleControls: false
			};
			
			var pswpElement = $('.pswp')[0];
			
			var gallery = new PhotoSwipe(pswpElement, PhotoSwipeUI_Default, items, options);
			gallery.init();
			
			gallery.listen('initialZoomIn', function () {
				$(this).product_thumbnails_speed = $slick_product_thumbnails.slick('slickGetOption', 'speed');
				$slick_product_thumbnails.slick('slickSetOption', 'speed', 0);
			});
			
			var slide = index;
			gallery.listen('beforeChange', function (dirVal) {
				slide = slide + dirVal;
				$slick_product_single.slick('slickGoTo', slide, true);
			});
			gallery.listen('close', function () {
				$slick_product_thumbnails.slick('slickSetOption', 'speed', $(this).product_thumbnails_speed);
			});
		};
		
		root.ajaxSearchFunction = ideapark_debounce(function () {
			var search = $ideapark_search_input.val().trim();
			var $search_form = $ideapark_search_input.closest('form');
			if (ideapark_empty(search)) {
				$ideapark_search_result.html('');
			} else {
				$ideapark_search_loader.insertBefore($ideapark_search_input);
				$.ajax({
					url    : ideapark_wp_vars.ajaxUrl,
					type   : 'POST',
					data   : {
						action: 'ideapark_ajax_search',
						s     : search,
						lang  : $('input[name="lang"]', $search_form).val()
					},
					success: function (results) {
						$ideapark_search_loader.remove();
						$ideapark_search_result.html((ideapark_empty($ideapark_search_input.val().trim())) ? '' : results);
					}
				});
			}
		}, 500);
		
		root.ideapark_mega_menu_init = function () {
			
			$ideapark_desktop_mega_menu = $('.js-header-desktop .js-mega-menu');
			
			ideapark_mega_menu_sticky_init(true);
		};
		
		root.ideapark_mega_menu_sticky_wpadmin = function (event, wpadminbar_height) {
			if (ideapark_sticky_mobile_init && ideapark_is_mobile_layout && $ideapark_mobile_sticky_row.length) {
				$ideapark_mobile_sticky_row.css({
					transform: 'translateY(' + wpadminbar_height + 'px)'
				});
			}
		};
		
		root.ideapark_mega_menu_sticky_init = function (force) {
			
			if ((!ideapark_sticky_mobile_init || force) && ideapark_is_mobile_layout) {
				if (ideapark_sticky_mobile_init) {
					$(document).off('ideapark.wpadminbar.scroll', ideapark_mega_menu_sticky_wpadmin);
				}
				$(document).on('ideapark.wpadminbar.scroll', ideapark_mega_menu_sticky_wpadmin);
				ideapark_sticky_mobile_init = true;
			}
		};
		
		root.ideapark_search_init = function () {
			$ideapark_search.removeClass('c-header-search--disabled');
			
			$('#ideapark-ajax-search-close').click(function () {
				$ideapark_search.on(ideapark_on_transition_end, ideapark_search_clear);
				ideapark_search_popup(false);
			});
			
			$ideapark_search_input.on('keydown', function (e) {
				var $this = $(this);
				var is_not_empty = !ideapark_empty($this.val().trim());
				
				if (e.keyCode == 13) {
					e.preventDefault();
					if ($this.hasClass('c-header-search__input--no-ajax') && is_not_empty) {
						$this.closest('form').submit();
					}
				} else if (e.keyCode == 27) {
					ideapark_search_popup(false);
				}
			}).on('input', function () {
				var $this = $(this);
				var is_not_empty = !ideapark_empty($this.val().trim());
				
				if (is_not_empty && !ideapark_search_input_filled) {
					ideapark_search_input_filled = true;
					$('#ideapark-ajax-search-clear').addClass('c-header-search__clear--active');
					
				} else if (!is_not_empty && ideapark_search_input_filled) {
					ideapark_search_input_filled = false;
					$('#ideapark-ajax-search-clear').removeClass('c-header-search__clear--active');
				}
				ajaxSearchFunction();
			});
			
			$('#ideapark-ajax-search-clear').click(function () {
				ideapark_search_clear();
			});
			
			$(document).on('ideapark.wpadminbar.scroll', function (event, wpadminbar_height) {
				$ideapark_search.css({
					transform   : 'translateY(' + wpadminbar_height + 'px)',
					'max-height': 'calc(100% - ' + wpadminbar_height + 'px)'
				});
			});
		};
		
		root.ideapark_sidebar_popup = function (show) {
			if (ideapark_shop_sidebar_initialized) {
				if (show && !ideapark_shop_sidebar_active) {
					ideapark_shop_sidebar_active = true;
					$ideapark_shop_sidebar.addClass('c-catalog-sidebar--active');
					$ideapark_shop_sidebar_wrap.addClass('c-catalog-sidebar__wrap--active');
					bodyScrollLock.disableBodyScroll($ideapark_shop_sidebar_content[0]);
				} else if (ideapark_shop_sidebar_active) {
					ideapark_shop_sidebar_active = false;
					$ideapark_shop_sidebar.removeClass('c-catalog-sidebar--active');
					$ideapark_shop_sidebar_wrap.removeClass('c-catalog-sidebar__wrap--active');
					bodyScrollLock.clearAllBodyScrollLocks();
				}
			}
		};
		
		root.ideapark_shop_sidebar_init = function () {
			if (ideapark_is_mobile_layout && !ideapark_shop_sidebar_initialized && $ideapark_shop_sidebar.length) {
				$(document).on('ideapark.wpadminbar.scroll', function (event, wpadminbar_height) {
					if (ideapark_is_mobile_layout) {
						$ideapark_shop_sidebar.css({
							transform   : 'translateY(' + wpadminbar_height + 'px)',
							'max-height': 'calc(100% - ' + wpadminbar_height + 'px)'
						});
					} else {
						$ideapark_shop_sidebar.css({
							transform   : '',
							'max-height': ''
						});
					}
				});
				$ideapark_shop_sidebar_open.click(function () {
					ideapark_sidebar_popup(true);
				});
				
				$ideapark_shop_sidebar_close.click(function () {
					ideapark_sidebar_popup(false);
				});
				ideapark_shop_sidebar_initialized = true;
			}
		};
		
		root.ideapark_mobile_menu_popup = function (show) {
			if (ideapark_mobile_menu_initialized) {
				if (show && !ideapark_mobile_menu_active) {
					ideapark_mobile_menu_active = true;
					$ideapark_mobile_menu.addClass('c-header__menu--active');
				} else if (ideapark_mobile_menu_active) {
					ideapark_mobile_menu_active = false;
					$ideapark_mobile_menu.removeClass('c-header__menu--active');
					bodyScrollLock.clearAllBodyScrollLocks();
				}
			}
		};
		
		root.ideapark_init_mobile_menu = function () {
			var $wrap = $('.js-mobile-menu-wrap');
			var $back = $('.js-mobile-menu-back');
			var action_lock = false;
			var ideapark_mobile_menu_init_page = function (page, $ul) {
				var $page = $('<div class="c-header__menu-page js-menu-page" data-page="' + page + '"></div>');
				var $ul_new = $ul.clone();
				if (!page) {
					var $li = $('<li></li>');
					$('.js-mobile-blocks').clone().removeClass('.js-mobile-blocks').appendTo($li);
					$li.appendTo($ul_new);
				}
				$ul_new.appendTo($page);
				$page.appendTo($wrap);
			}
			var ideapark_mobile_menu_scroll_lock = function () {
				var $submenu = $('.js-menu-page[data-page="' + ideapark_mobile_menu_page + '"]');
				bodyScrollLock.clearAllBodyScrollLocks();
				bodyScrollLock.disableBodyScroll($submenu[0]);
			}
			if (ideapark_is_mobile_layout && !ideapark_mobile_menu_initialized && $ideapark_mobile_menu.length) {
				
				$(document).on('ideapark.wpadminbar.scroll', function (event, wpadminbar_height) {
					$ideapark_mobile_menu.css({
						transform   : 'translateY(' + wpadminbar_height + 'px)',
						'max-height': 'calc(100% - ' + wpadminbar_height + 'px)'
					});
				});
				
				$ideapark_mobile_menu.find('.c-mobile-menu__item--has-children, .c-mobile-menu__subitem--has-children').each(function () {
					var $li = $(this);
					var $a = $li.children('a').first();
					var $ul_submenu = $li.children('.c-mobile-menu__submenu').first();
					if ($a.length && $ul_submenu.length) {
						if ($a.attr('href') != '#') {
							var $li_new = $ul_submenu.prop("tagName") == 'UL' ?
								$('<li class="c-mobile-menu__subitem c-mobile-menu__subitem--parent"></li>') :
								$('<div class="c-mobile-menu__subitem c-mobile-menu__subitem--parent c-mobile-menu__subitem--parent-div"></div>');
							$a.clone().appendTo($li_new);
							$ul_submenu.prepend($li_new);
						}
					}
				});
				
				$(document.body).on('click', '.c-mobile-menu__item--has-children > a:first-child, .c-mobile-menu__subitem--has-children > a:first-child', function (e) {
					e.preventDefault();
					if (action_lock) {
						return;
					}
					action_lock = true;
					var $submenu = $(this).closest('li').children('.c-mobile-menu__submenu');
					ideapark_mobile_menu_page++;
					ideapark_mobile_menu_init_page(ideapark_mobile_menu_page, $submenu);
					ideapark_on_transition_end_callback($wrap, function () {
						action_lock = false;
					});
					$wrap.addClass('c-header__menu-wrap--page-' + ideapark_mobile_menu_page);
					$back.addClass('c-header__menu-back--active');
					ideapark_mobile_menu_scroll_lock();
				});
				
				$back.click(function () {
					if (action_lock || ideapark_mobile_menu_page <= 0) {
						return;
					}
					action_lock = true;
					ideapark_on_transition_end_callback($wrap, function () {
						$('.js-menu-page[data-page="' + ideapark_mobile_menu_page + '"]').remove();
						ideapark_mobile_menu_page--;
						if (!ideapark_mobile_menu_page) {
							$back.removeClass('c-header__menu-back--active');
						}
						ideapark_mobile_menu_scroll_lock();
						action_lock = false;
					});
					$wrap.removeClass('c-header__menu-wrap--page-' + ideapark_mobile_menu_page);
				});
				
				$('.js-mobile-menu-open').click(function () {
					if (ideapark_mobile_menu_page === -1) {
						ideapark_mobile_menu_page = 0;
						ideapark_mobile_menu_init_page(ideapark_mobile_menu_page, $('.c-mobile-menu__list'));
					}
					ideapark_mobile_menu_popup(true);
					ideapark_mobile_menu_scroll_lock();
				});
				
				$('.js-mobile-menu-close').click(function () {
					ideapark_mobile_menu_popup(false);
				});
				
				ideapark_mobile_menu_initialized = true;
			}
		};
		
		root.ideapark_init_custom_select = function () {
			$('select.styled:not(.hasCustomSelect), select.orderby:not(.hasCustomSelect), .c-ordering__select select:not(.hasCustomSelect)').each(function () {
				$(this).customSelect({
					customClass: "c-custom-select",
					mapClass   : false,
				}).parent().find('.c-custom-select').append('<i class="ip-select c-custom-select__angle"><!-- --></i>');
			});
		};
		
		root.ideapark_get_notice_offset = function () {
			var $notice = $('.woocommerce-notices-wrapper--ajax');
			var offset = ideapark_is_mobile_layout || $notice.length ? ideapark_adminbar_visible_height : 0;
			if ($notice.length) {
				if (!ideapark_is_mobile_layout) {
					if (ideapark_sticky_active) {
						offset = $ideapark_desktop_sticky_row.outerHeight() + ideapark_adminbar_visible_height;
					}
				}
			}
			
			return offset;
		};
		
		root.ideapark_set_notice_offset = function (offset) {
			var $notice = $('.woocommerce-notices-wrapper--ajax');
			if ($notice.length) {
				if (typeof offset !== 'number') {
					offset = ideapark_get_notice_offset();
				}
				$notice.css({
					transform: 'translateY(' + offset + 'px)',
				});
			}
		};
		
		root.ideapark_to_top_button = function () {
			if ($ideapark_to_top_button.length) {
				if ($window.scrollTop() > 500) {
					if (!$ideapark_to_top_button.hasClass('c-to-top-button--active')) {
						$ideapark_to_top_button.addClass('c-to-top-button--active');
					}
				} else {
					if ($ideapark_to_top_button.hasClass('c-to-top-button--active')) {
						$ideapark_to_top_button.removeClass('c-to-top-button--active');
					}
				}
			}
		};
		
		root.ideapark_daterangepicker_position = function (position) {
			var $input = $('.js-book-date-range');
			var $daterangepicker = $('.daterangepicker:not(.inner-calendar)');
			if (!position || position === 'relative') {
				$daterangepicker.css({position: 'absolute'});
				$(window).trigger('resize.daterangepicker');
			} else {
				if ($input.length && $input.hasClass('opened')) {
					if (position === 'fixed') {
						var $sidebar = $('.js-sticky-sidebar');
						var _top = $input.offset().top - $sidebar.offset().top + $input.outerHeight() + $sidebar.position().top;
						$daterangepicker.css({
							top     : _top + 'px',
							position: 'fixed'
						});
					} else if ((position === 'absolute')) {
						$daterangepicker.css({position: 'absolute'});
						$(window).trigger('resize.daterangepicker');
					}
				}
			}
		}
		
		root.ideapark_sticky_sidebar = function () {
			if (ideapark_wp_vars.stickySidebar && $ideapark_sticky_sidebar.length && $ideapark_sticky_sidebar_nearby.length) {
				
				var sb = $ideapark_sticky_sidebar;
				var content = $ideapark_sticky_sidebar_nearby;
				var is_disable_transition = false;
				var is_enable_transition = false;
				
				if (ideapark_is_mobile_layout) {
					
					if (ideapark_sticky_sidebar_old_style !== null) {
						sb.attr('style', ideapark_sticky_sidebar_old_style);
						ideapark_sticky_sidebar_old_style = null;
					}
					
				} else {
					
					var sb_height = sb.outerHeight(true);
					var content_height = content.outerHeight(true);
					var content_top = content.offset().top;
					var scroll_offset = $window.scrollTop();
					var window_width = $window.width();
					
					var top_panel_fixed_height = ideapark_sticky_desktop_active ? $ideapark_desktop_sticky_row.outerHeight() + ideapark_adminbar_visible_height + 25 : ideapark_adminbar_visible_height;
					
					if (sb_height < content_height && scroll_offset + top_panel_fixed_height > content_top) {
						
						var sb_init = {
							'position': 'undefined',
							'float'   : 'none',
							'top'     : 'auto',
							'bottom'  : 'auto'
						};
						
						if (typeof ideapark_scroll_offset_last == 'undefined') {
							root.ideapark_sb_top_last = content_top;
							root.ideapark_scroll_offset_last = scroll_offset;
							root.ideapark_scroll_dir_last = 1;
							root.ideapark_window_width_last = window_width;
						}
						
						var scroll_dir = scroll_offset - ideapark_scroll_offset_last;
						if (scroll_dir === 0) {
							scroll_dir = ideapark_scroll_dir_last;
						} else {
							scroll_dir = scroll_dir > 0 ? 1 : -1;
						}
						
						var sb_big = sb_height + 30 >= $window.height() - top_panel_fixed_height,
							sb_top = sb.offset().top;
						
						if (sb_top < 0) {
							sb_top = ideapark_sb_top_last;
						}
						
						if (sb_big) {
							
							if (scroll_dir != ideapark_scroll_dir_last && sb.css('position') == 'fixed') {
								sb_init.top = sb_top - content_top;
								sb_init.position = 'absolute';
								
							} else if (scroll_dir > 0) {
								if (scroll_offset + $window.height() >= content_top + content_height + 30) {
									if (ideapark_is_sticky_sidebar_inner) {
										sb_init.top = (content_height - sb_height) + 'px';
										is_disable_transition = true;
									} else {
										sb_init.bottom = 0;
									}
									sb_init.position = 'absolute';
									
								} else if (scroll_offset + $window.height() >= (sb.css('position') == 'absolute' ? sb_top : content_top) + sb_height + 30) {
									sb_init.bottom = 30;
									sb_init.position = 'fixed';
									is_enable_transition = true;
								}
								
							} else {
								
								if (scroll_offset + top_panel_fixed_height <= sb_top) {
									sb_init.top = top_panel_fixed_height;
									sb_init.position = 'fixed';
									is_enable_transition = true;
								}
							}
							
						} else {
							if (scroll_offset + top_panel_fixed_height >= content_top + content_height - sb_height) {
								if (ideapark_is_sticky_sidebar_inner) {
									sb_init.top = (content_height - sb_height) + 'px';
									is_disable_transition = true;
									
								} else {
									sb_init.bottom = 0;
								}
								sb_init.position = 'absolute';
							} else {
								sb_init.top = top_panel_fixed_height;
								sb_init.position = 'fixed';
								is_enable_transition = true;
							}
						}
						
						if (is_disable_transition) {
							is_disable_transition = false;
							sb.addClass('js-sticky-sidebar--disable-transition');
						}
						
						if (sb_init.position != 'undefined') {
							
							if (sb.css('position') != sb_init.position || ideapark_scroll_dir_last != scroll_dir || ideapark_window_width_last != window_width) {
								
								root.ideapark_window_width_last = window_width;
								sb_init.width = sb.parent().width();
								
								if (ideapark_sticky_sidebar_old_style === null) {
									var style = sb.attr('style');
									if (!style) {
										style = '';
									}
									ideapark_sticky_sidebar_old_style = style;
								}
								sb.css(sb_init);
								
								ideapark_sticky_sidebar_position = sb_init.position;
								ideapark_daterangepicker_position(sb_init.position);
							}
						}
						
						if (is_enable_transition) {
							is_enable_transition = false;
							setTimeout(function () {
								sb.removeClass('js-sticky-sidebar--disable-transition');
							}, 20);
						}
						
						root.ideapark_sb_top_last = sb_top;
						root.ideapark_scroll_offset_last = scroll_offset;
						root.ideapark_scroll_dir_last = scroll_dir;
						
					} else {
						if (ideapark_sticky_sidebar_old_style !== null) {
							sb.attr('style', ideapark_sticky_sidebar_old_style);
							ideapark_sticky_sidebar_old_style = null;
							
							ideapark_sticky_sidebar_position = 'relative';
							ideapark_daterangepicker_position();
						}
						setTimeout(function () {
							sb.removeClass('js-sticky-sidebar--disable-transition');
						}, 20);
					}
				}
				
			}
		};
		
		root.ideapark_hash_menu_animate = function (e) {
			var $this = $(this), $el;
			if (ideapark_isset(e)) {
				e.preventDefault();
			}
			if ($this.attr('href').length > 1 && ($el = $($this.attr('href'))) && $el.length) {
				var offset = $el.offset().top - 25 - (ideapark_adminbar_position === 'fixed' ? ideapark_adminbar_height : 0);
				if (ideapark_is_mobile_layout) {
					ideapark_mobile_menu_popup(false);
					if ($ideapark_mobile_sticky_row.length) {
						offset -= $ideapark_mobile_sticky_row.outerHeight();
					}
				}
				$('html, body').animate({scrollTop: offset}, 800);
			}
		};
		
		root.ideapark_get_minimal_days = function (start, end, pickup, type) {
			var days;
			var dateFormat = ideapark_date_format;
			ideapark_minimal_days_error = '';
			
			if (!ideapark_isset(start) || !ideapark_isset(end)) {
				days = ideapark_wp_vars.minimumDays;
				ideapark_maximum_days = ideapark_wp_vars.maximumDays;
			} else if (ideapark_wp_vars.minimumDaysConditional) {
				days = 1;
				var start_text = start.format(dateFormat);
				var end_text = end.format(dateFormat);
				
				$.ajax({
					url     : ideapark_wp_vars.ajaxUrl,
					type    : 'POST',
					async   : false,
					data    : {
						action: 'ideapark_minimum',
						start : start_text,
						end   : end_text,
						pickup: pickup,
						type  : type
					},
					dataType: 'json',
					success : function (result) {
						days = parseInt(result.minimum_days);
						ideapark_maximum_days = parseInt(result.maximum_days);
						ideapark_minimal_days_error = result.error;
					}
				});
			} else {
				days = ideapark_wp_vars.minimumDays;
				ideapark_maximum_days = ideapark_wp_vars.maximumDays;
			}
			return days > 1 ? days : 1;
		};
		
		root.ideapark_init_date_range_picker_filter = function () {
			moment.locale(ideapark_wp_vars.locale);
			var dateFormat = ideapark_date_format;
			var minDate = moment(ideapark_wp_vars.minDate, 'YYYY-MM-DD').format(dateFormat);
			$('.js-filter-date-start').each(function () {
				var $container = $(this).closest('.c-filter');
				var $input_start = $('.js-filter-date-start', $container);
				var $input_end = $('.js-filter-date-end', $container);
				var $input_range = $('.js-filter-date-range', $container);
				if ($input_start.length && $input_end.length) {
					var startDate;
					var endDate;
					
					var defaultStartDate = moment($input_start.data('value'), dateFormat);
					var defaultEndDate = moment($input_end.data('value'), dateFormat);
					if (
						defaultStartDate.isValid() &&
						defaultEndDate.isValid() &&
						(ideapark_is_day ? (defaultStartDate <= defaultEndDate) : (defaultStartDate < defaultEndDate))
					) {
						startDate = defaultStartDate;
						endDate = defaultEndDate;
					} else {
						startDate = moment(ideapark_wp_vars.minDate, 'YYYY-MM-DD');
						endDate = startDate.clone().add(ideapark_is_night ? 1 : 0, 'd');
						endDate = startDate.clone().add(ideapark_get_minimal_days(startDate, endDate, $('.js-filter-pickup').val(), $('.js-filter-type option:selected').data('id')) - (ideapark_is_day ? 1 : 0), 'd');
					}
					
					$input_start.val(startDate.format(dateFormat));
					$input_end.val(endDate.format(dateFormat));
					$input_range.val(startDate.format(dateFormat) + '  —  ' + endDate.format(dateFormat));
					
					$input_range.daterangepicker({
						locale             : {
							format     : dateFormat,
							applyLabel : ideapark_wp_vars.applyLabel,
							cancelLabel: ideapark_wp_vars.cancelLabel,
						},
						opens              : "center",
						alwaysShowCalendars: true,
						minDate            : minDate,
						startDate          : startDate,
						endDate            : endDate,
						autoApply          : true,
						autoUpdateInput    : false,
						isCustomDate       : ideapark_get_date_class_avail
					}, function (start, end, label) {
						
						if (ideapark_is_night && start.isSame(end, 'day')) {
							end = start.clone().add(1, 'day');
						}
						
						if (ideapark_wp_vars.pickupDropoffDays.length < 7) {
							var pickup_dow = start.day();
							if (!pickup_dow) {
								pickup_dow = 7;
							}
							if (!ideapark_wp_vars.pickupDropoffDays.includes(pickup_dow + '')) {
								ideapark_show_notice_error(start.format(dateFormat) + ' ' + ideapark_wp_vars.pickupNotAvailMsg);
								$input_range.on('hide.daterangepicker.ip', function () {
									$input_range.off('hide.daterangepicker.ip');
									setTimeout(function () {
										$input_range.trigger('click');
									}, 100);
								});
							}
							
							var dropoff_dow = end.day();
							if (!dropoff_dow) {
								dropoff_dow = 7;
							}
							if (!ideapark_wp_vars.pickupDropoffDays.includes(dropoff_dow + '')) {
								ideapark_show_notice_error(end.format(dateFormat) + ' ' + ideapark_wp_vars.dropoffNotAvailMsg);
								$input_range.on('hide.daterangepicker.ip', function () {
									$input_range.off('hide.daterangepicker.ip');
									setTimeout(function () {
										$input_range.trigger('click');
									}, 100);
								});
							}
						}
						
						var days = end.diff(start, 'days') + (ideapark_is_day ? 1 : 0);
						var minDates = ideapark_get_minimal_days(start, end, $('.js-filter-pickup').val(), $('.js-filter-type option:selected').data('id'));
						
						$('.js-filter-button').prop('disabled', false);
						
						if (days < minDates || ideapark_minimal_days_error) {
							if (ideapark_minimal_days_error) {
								ideapark_show_notice_error(ideapark_minimal_days_error);
								$('.js-filter-button').prop('disabled', true);
							} else {
								end = start.clone().add(minDates - (ideapark_is_day ? 1 : 0), 'day');
								ideapark_show_notice_error(ideapark_wp_vars.minimumDaysMsg + ' ' + minDates);
							}
							$input_range.on('hide.daterangepicker.ip', function () {
								$input_range.off('hide.daterangepicker.ip');
								setTimeout(function () {
									$input_range.trigger('click');
								}, 100);
							});
						}
						
						if (ideapark_maximum_days > 0 && days > ideapark_maximum_days) {
							end = start.clone().add(ideapark_maximum_days - (ideapark_is_day ? 1 : 0), 'day');
							ideapark_show_notice_error(ideapark_wp_vars.maximumDaysMsg + ' ' + ideapark_maximum_days);
							$input_range.on('hide.daterangepicker.ip', function () {
								$input_range.off('hide.daterangepicker.ip');
								setTimeout(function () {
									$input_range.trigger('click');
								}, 100);
							});
						}
						var selectedStartDate = start.format(dateFormat);
						var selectedEndDate = end.format(dateFormat);
						
						$input_start.val(selectedStartDate);
						$input_end.val(selectedEndDate);
						$input_range.val(selectedStartDate + '  —  ' + selectedEndDate);
						
						$input_range.data('daterangepicker').setStartDate(selectedStartDate);
						$input_range.data('daterangepicker').setEndDate(selectedEndDate);
					});
					$input_range
						.on('show.daterangepicker', function (ev, picker) {
							$input_range.addClass('opened');
						})
						.on('hide.daterangepicker', function (ev, picker) {
							$input_range.removeClass('opened');
						});
					
				} // End Daterange Picker
				$('.js-filter-pickup,.js-filter-type', $container).on('change', function () {
					var start = moment($input_start.val(), dateFormat);
					var end = moment($input_end.val(), dateFormat);
					var days = end.diff(start, 'days') + (ideapark_is_day ? 1 : 0);
					var minDates = ideapark_get_minimal_days(start, end, $('.js-filter-pickup').val(), $('.js-filter-type option:selected').data('id'));
					
					$('.js-filter-button').prop('disabled', false);
					if (days < minDates || ideapark_minimal_days_error) {
						if (ideapark_minimal_days_error) {
							ideapark_show_notice_error(ideapark_minimal_days_error);
							$('.js-filter-button').prop('disabled', true);
						} else {
							end = start.clone().add(minDates - (ideapark_is_day ? 1 : 0), 'day');
							ideapark_show_notice_error(ideapark_wp_vars.minimumDaysMsg + ' ' + minDates);
							$('.js-filter-button').prop('disabled', true);
						}
						var selectedStartDate = start.format(dateFormat);
						var selectedEndDate = end.format(dateFormat);
						$input_start.val(selectedStartDate);
						$input_end.val(selectedEndDate);
						$input_range.val(selectedStartDate + '  —  ' + selectedEndDate);
						$input_range.data('daterangepicker').setStartDate(selectedStartDate);
						$input_range.data('daterangepicker').setEndDate(selectedEndDate);
						$input_range.trigger('click');
					}
				});
			});
		};
		
		root.ideapark_init_date_range_picker = function () {
			var $input_start = $('.js-book-date-start');
			var $input_end = $('.js-book-date-end');
			var $input_range = $('.js-book-date-range');
			if ($input_start.length && $input_end.length) {
				moment.locale(ideapark_wp_vars.locale);
				var dateFormat = ideapark_date_format;
				var minDate = moment(ideapark_wp_vars.minDate, 'YYYY-MM-DD').format(dateFormat);
				var startDate = moment($input_start.data('value'), dateFormat);
				var endDate = moment($input_end.data('value'), dateFormat);
				
				var startDateText = startDate.format(dateFormat);
				var endDateText = endDate.format(dateFormat);
				
				$input_start.val(startDateText);
				$input_end.val(endDateText);
				$input_range.val(startDateText + '  —  ' + endDateText);
				
				ideapark_default_start = startDateText;
				ideapark_default_end = endDateText;
				
				var params = {
					locale             : {
						format     : dateFormat,
						applyLabel : ideapark_wp_vars.applyLabel,
						cancelLabel: ideapark_wp_vars.cancelLabel,
					},
					opens              : "left",
					alwaysShowCalendars: true,
					minDate            : minDate,
					startDate          : startDate,
					endDate            : endDate,
					autoApply          : !ideapark_wp_vars.pickup_dropoff_time,
					autoUpdateInput    : false,
					isCustomDate       : ideapark_wp_vars.show_date_class ? ideapark_get_date_class : ideapark_get_date_class_avail,
				};
				
				if (ideapark_wp_vars.pickup_dropoff_time) {
					params.timePicker = true;
					params.timePicker24Hour = true;
					params.timePickerIncrement = parseInt(ideapark_wp_vars.time_increment);
					$input_range
						.on('showCalendar.daterangepicker', function (ev, picker) {
							var $calendars = $('body > .daterangepicker');
							var $left = $calendars.find('.left');
							var $right = $calendars.find('.right');
							var from = $('.js-book-date-start-time').val();
							var to = $('.js-book-date-end-time').val();
							
							var f_change = function () {
								var $this = $(this);
								var $calendar = $this.closest('.drp-calendar');
								var time = $this.val().split(':');
								$('.hourselect', $calendar).val(parseInt(time[0]) + '');
								$('.minuteselect', $calendar).val(parseInt(time[1]) + '');
								if ($calendar.hasClass('left')) {
									$('.js-book-date-start-time').val($this.val());
								} else {
									$('.js-book-date-end-time').val($this.val());
								}
							};
							
							$left.find('.calendar-time-range').remove();
							$left
								.append('<div class="calendar-time-range"><label><span class="timeselect__title">' + ideapark_wp_vars.pickup_time_label + '</span> <select class="timeselect">' + ideapark_wp_vars.pickup_time_options + '</select></label></div>')
								.find('.timeselect')
								.on('change', f_change)
								.trigger('change');
							if (from) {
								$left.find('.timeselect').val(from).trigger('change');
							}
							
							$right.find('.calendar-time-range').remove();
							$right
								.append('<div class="calendar-time-range"><label><span class="timeselect__title">' + ideapark_wp_vars.dropoff_time_label + '</span> <select class="timeselect">' + ideapark_wp_vars.dropoff_time_options + '</select></label></div>')
								.find('.timeselect')
								.on('change', f_change)
								.trigger('change');
							if (to) {
								$right.find('.timeselect').val(to).trigger('change');
							}
						});
				}
				
				$input_range.daterangepicker(params, function (start, end, label) {
					if (ideapark_is_night && start.isSame(end, 'day')) {
						end = start.clone().add(1, 'day');
					}
					
					if (ideapark_wp_vars.autoMinimumDays) {
						var days = end.diff(start, 'days') + (ideapark_is_day ? 1 : 0);
						var minDates = ideapark_get_minimal_days(start, end, $('.js-book-pick-up').val(), $('.js-book-type').val());
						
						if (days < minDates || ideapark_minimal_days_error) {
							if (ideapark_minimal_days_error) {
							} else {
								end = start.clone().add(minDates - (ideapark_is_day ? 1 : 0), 'day');
								ideapark_show_notice_error(ideapark_wp_vars.minimumDaysMsg + ' ' + minDates);
							}
							$input_range.on('hide.daterangepicker.ip', function () {
								$input_range.off('hide.daterangepicker.ip');
								setTimeout(function () {
									$input_range.trigger('click');
								}, 100);
							});
						}
						
						if (ideapark_maximum_days > 0 && days > ideapark_maximum_days) {
							end = start.clone().add(ideapark_maximum_days - (ideapark_is_day ? 1 : 0), 'day');
							ideapark_show_notice_error(ideapark_wp_vars.maximumDaysMsg + ' ' + ideapark_maximum_days);
							$input_range.on('hide.daterangepicker.ip', function () {
								$input_range.off('hide.daterangepicker.ip');
								setTimeout(function () {
									$input_range.trigger('click');
								}, 100);
							});
						}
					}
					
					var selectedStartDate = start.format(dateFormat);
					var selectedEndDate = end.format(dateFormat);
					
					$input_start.val(selectedStartDate);
					$input_end.val(selectedEndDate);
					$input_range.val(selectedStartDate + '  —  ' + selectedEndDate);
					
					$input_range.data('daterangepicker').setStartDate(selectedStartDate);
					$input_range.data('daterangepicker').setEndDate(selectedEndDate);
					
					ideapark_get_total();
				});
				
				
				$input_range
					.on('apply.daterangepicker', function (ev, picker) {
						ideapark_push_state();
					})
					.on('show.daterangepicker', function (ev, picker) {
						$input_range.addClass('opened');
						ideapark_daterangepicker_position(ideapark_sticky_sidebar_position);
						$('.daterangepicker:not(.inner-calendar)').on('mouseover', 'td', function () {
							var $this = $(this);
							var hint_date = $this.attr('class').match(/date-(\d+-\d+-\d+)/);
							var hide_hint = true;
							if (hint_date) {
								var date_text = hint_date[1];
								if (typeof ideapark_wp_vars.hints === 'object' && !ideapark_empty(ideapark_wp_vars.hints) && date_text in ideapark_wp_vars.hints) {
									ideapark_hint(ideapark_wp_vars.hints[date_text], $this);
									hide_hint = false;
								}
							}
							
							if (hide_hint && $ideapark_hint) {
								$ideapark_hint.closest('.hint').removeClass('hint');
								$ideapark_hint.remove();
							}
						});
					})
					.on('showCalendar.daterangepicker', function (ev, picker) {
						ideapark_daterangepicker_position();
					})
					.on('hide.daterangepicker', function (ev, picker) {
						$input_range.removeClass('opened');
						ideapark_daterangepicker_position();
					});
				
				$('.js-book-date-btn').click(function () {
					$(this).parent().find('input[type="text"]').trigger('click');
				});
				
				ideapark_get_total(true);
				
				var $calendar_info = $('.js-booking-calendar-input');
				if ($calendar_info.length && ideapark_wp_vars.show_date_class) {
					var picker = $calendar_info.daterangepicker({
						locale             : {
							format     : dateFormat,
							applyLabel : ideapark_wp_vars.applyLabel,
							cancelLabel: ideapark_wp_vars.cancelLabel,
						},
						parentEl           : ".js-booking-calendar-info",
						alwaysShowCalendars: true,
						minDate            : minDate,
						autoApply          : true,
						autoUpdateInput    : false,
						singleDatePicker   : false,
						isCustomDate       : function (data) {
							return ideapark_get_date_class(data);
						}
					}).on('show.daterangepicker', function () {
						$('.js-booking-calendar-info .daterangepicker').addClass('inner-calendar');
					});
					picker.data('daterangepicker').hide = function (e) {
						var _this = picker.data('daterangepicker');
						if (!_this.endDate) {
							_this.startDate = _this.oldStartDate.clone();
							_this.endDate = _this.oldEndDate.clone();
						}
						if (!_this.startDate.isSame(_this.oldStartDate) || !_this.endDate.isSame(_this.oldEndDate))
							_this.callback(_this.startDate.clone(), _this.endDate.clone(), _this.chosenLabel);
						_this.updateElement();
						_this.element.trigger('hide.daterangepicker', _this);
					};
					picker.data('daterangepicker').show();
					$(".js-booking-calendar-info")
						.on('click', function (e) {
							if ($(e.target).attr('class') == 'calendar-table') {
								var offset = $(".js-book-date-range").offset().top - 25;
								$('html, body').animate({scrollTop: offset}, 500);
								setTimeout(function () {
									$(".js-book-date-range").trigger('click');
								}, 500);
							}
						})
						.find('.active').removeClass('active in-range start-date end-date');
				}
				
				window.addEventListener('popstate', function (e) {
					if (e.state && e.state.start && e.state.end) {
						$input_start.val(e.state.start);
						$input_end.val(e.state.end);
						$input_range.val(e.state.start + '  —  ' + e.state.end);
					} else {
						$input_start.val(ideapark_default_start);
						$input_end.val(ideapark_default_end);
						$input_range.val(ideapark_default_start + '  —  ' + ideapark_default_end);
					}
					ideapark_get_total();
					
				}, true);
				
			} // End Daterange Picker
		};
		
		root.ideapark_get_date_class_avail = function (date) {
			if (ideapark_wp_vars.pickupDropoffDays.length < 7) {
				var dow = moment(date).day();
				if (!dow) {
					dow = 7;
				}
				return ideapark_wp_vars.pickupDropoffDays.includes(dow + '') ? '' : 'not-avail'
			} else {
				return '';
			}
		}
		
		root.ideapark_get_date_class = function (date) {
			var date_text = moment(date).format("YYYY-MM-DD");
			if (typeof ideapark_wp_vars.date_class === 'object' && !ideapark_empty(ideapark_wp_vars.date_class) && date_text in ideapark_wp_vars.date_class) {
				return ideapark_wp_vars.date_class[date_text];
			} else {
				if (ideapark_date_class_lock) {
					return;
				}
				$.ajax({
					url     : ideapark_wp_vars.ajaxUrl,
					type    : 'POST',
					async   : false,
					data    : {
						action    : 'ideapark_ajax_date_class',
						vehicle_id: $('.js-book-form').data('id'),
						frontend  : 1,
						date      : date_text
					},
					dataType: 'json',
					success : function (result) {
						if (typeof result.dates === 'object') {
							ideapark_wp_vars.date_class = Object.assign({}, ideapark_wp_vars.date_class, result.dates);
						}
						if (typeof result.hints === 'object') {
							ideapark_wp_vars.hints = Object.assign({}, ideapark_wp_vars.hints, result.hints);
						}
					}
				});
				
				if (typeof ideapark_wp_vars.date_class === 'object' && !ideapark_empty(ideapark_wp_vars.date_class) && date_text in ideapark_wp_vars.date_class) {
					return ideapark_wp_vars.date_class[date_text];
				} else {
					ideapark_date_class_lock = true;
					setTimeout(function () {
						ideapark_date_class_lock = false;
					}, 1000);
				}
			}
		};
		
		root.ideapark_hint = function (message, $td) {
			if ($ideapark_hint && !$td.hasClass('hint')) {
				$ideapark_hint.closest('.hint').removeClass('hint');
				$ideapark_hint.remove();
			}
			if ($td.hasClass('hint')) {
				return;
			}
			
			$td.addClass('hint');
			
			if (ideapark_date_hint_timeout !== null) {
				clearTimeout(ideapark_date_hint_timeout);
				ideapark_date_hint_timeout = null;
			}
			var $html = $('<div class="c-hint"></div>');
			$html.append(message);
			$ideapark_hint = $html;
			$td.append($html);
			ideapark_date_hint_timeout = setTimeout(function () {
				ideapark_date_hint_timeout = null;
				if ($ideapark_hint) {
					$ideapark_hint.closest('.hint').removeClass('hint');
					$ideapark_hint.remove();
				}
			}, 3000);
		};
		
		root.ideapark_get_total = function (not_popup) {
			var $block = $('.js-book-block');
			var $price_block = $('.js-book-price-block');
			var $loading = $('.js-book-loading');
			var is_hidden = $price_block.hasClass('h-hidden');
			$block.addClass('c-vehicle-book__block--loading');
			if (is_hidden) {
				$('.js-book-space').ideapark_button('loading', 22);
			} else {
				$loading.addClass('c-vehicle-book__loading--active');
				$loading.ideapark_button('loading', 22);
			}
			$('.js-book-action').val('ideapark_calc_total');
			$.ajax({
				url     : ideapark_wp_vars.ajaxUrl,
				type    : 'POST',
				data    : $('.js-book-form').serialize(),
				dataType: 'json',
				success : function (results) {
					if (is_hidden) {
						$('.js-book-space').ideapark_button('reset');
					} else {
						$loading.ideapark_button('reset');
						$loading.removeClass('c-vehicle-book__loading--active');
					}
					$block.removeClass('c-vehicle-book__block--loading');
					if (results.price) {
						$('.c-vehicle-book__amount').html(results.price);
					}
					$('.js-book-cnt').text(results.days);
					$('.js-book-day-price').html(results.price_per_day);
					if (results.days > 1) {
						$('.c-vehicle-book__per').addClass('h-hidden');
						$('.c-vehicle-book__cnt').removeClass('h-hidden');
					} else {
						$('.c-vehicle-book__per').removeClass('h-hidden');
						$('.c-vehicle-book__cnt').addClass('h-hidden');
					}
					var $available_message = $('.js-book-dates-available');
					var $not_available_message = $('.js-book-dates-not-available');
					var $not_available_message_text = $('.js-not-available-message');
					if (results.extra) {
						for (var option_id in results.extra) {
							$(".js-extra-price-" + option_id).html(results.extra[option_id]);
						}
					}
					if (results.total) {
						$('.js-book-total').html(results.total);
						$price_block.removeClass('h-hidden');
						$available_message.removeClass('h-hidden');
						$not_available_message.addClass('h-hidden');
					} else {
						$price_block.addClass('h-hidden');
						$('.js-book-total').html('');
						$available_message.addClass('h-hidden');
						$not_available_message.removeClass('h-hidden');
						
						var $input_range = $('.js-book-date-range');
						var custom_message = '';
						if (results.custom_message) {
							custom_message = results.custom_message;
						} else {
							if (results.minimal_days) {
								custom_message = ideapark_wp_vars.minimumDaysMsg + ' ' + results.minimal_days;
							}
							
							if (results.maximum_days) {
								custom_message = ideapark_wp_vars.maximumDaysMsg + ' ' + results.maximum_days;
							}
						}
						
						if (custom_message) {
							$not_available_message_text.html(custom_message);
							if (typeof not_popup === 'undefined' || !not_popup) {
								ideapark_show_notice_error(custom_message);
								$input_range.trigger('click');
							}
						} else {
							$not_available_message_text.html($not_available_message_text.data('message'));
						}
					}
				}
			});
		};
		
		root.ideapark_push_state = function () {
			if (window.history) {
				var new_start = $('.js-book-date-start').val();
				var new_end = $('.js-book-date-end').val();
				var current_title = $('title').text();
				var new_href = location.href;
				new_href = ideapark_replace_url_param(new_href, 'start', new_start);
				new_href = ideapark_replace_url_param(new_href, 'end', new_end);
				
				if (ideapark_wp_vars.pickup_dropoff_time) {
					var new_start_time = $('.js-book-date-start-time').val();
					var new_end_time = $('.js-book-date-end-time').val();
					new_href = ideapark_replace_url_param(new_href, 'start_time', new_start_time);
					new_href = ideapark_replace_url_param(new_href, 'end_time', new_end_time);
				}
				
				var state = {
					start: new_start,
					end  : new_end
				};
				window.history.pushState(state, current_title, new_href);
			}
		};
		
		root.ideapark_init_price_filter = function () {
			
			if (typeof ideapark_wp_vars.currency_format_symbol === 'undefined') {
				return false;
			}
			
			if (typeof $.fn.slider !== 'function') {
				return;
			}
			
			$('.js-filter-price').each(function () {
				var $slider = $(this);
				var slug = $slider.data('slug');
				
				$(document).bind('ideapark.' + slug + '.slider.create ideapark.' + slug + '.slider.slide', function (event, min, max) {
					
					if (slug === 'price') {
						$('.js-filter-price-amount span.from', $slider).html(accounting.formatMoney(min, {
							symbol   : ideapark_wp_vars.currency_format_symbol,
							decimal  : ideapark_wp_vars.currency_format_decimal_sep,
							thousand : ideapark_wp_vars.currency_format_thousand_sep,
							precision: ideapark_wp_vars.currency_format_num_decimals,
							format   : ideapark_wp_vars.currency_format
						}));
						
						$('.js-filter-price-amount span.to', $slider).html(accounting.formatMoney(max, {
							symbol   : ideapark_wp_vars.currency_format_symbol,
							decimal  : ideapark_wp_vars.currency_format_decimal_sep,
							thousand : ideapark_wp_vars.currency_format_thousand_sep,
							precision: ideapark_wp_vars.currency_format_num_decimals,
							format   : ideapark_wp_vars.currency_format
						}));
					} else {
						$('.js-filter-price-amount span.from', $slider).html(min);
						$('.js-filter-price-amount span.to', $slider).html(max);
					}
				});
				
				$('.js-filter-price-slider, .js-filter-price-label', $slider).show();
				
				var min_price = parseFloat($('.js-filter-price-min_price', $slider).data('min')),
					max_price = parseFloat($('.js-filter-price-max_price', $slider).data('max')),
					step = parseFloat($('.js-filter-price-amount', $slider).data('step') || 1),
					current_min_price = parseFloat($('.js-filter-price-min_price', $slider).val()),
					current_max_price = parseFloat($('.js-filter-price-max_price', $slider).val());
				$('.js-filter-price-slider:not(.ui-slider)', $slider).slider({
					range  : true,
					animate: true,
					min    : min_price,
					max    : max_price,
					step   : step,
					values : [current_min_price, current_max_price],
					create : function (event, ui) {
						
						$('.js-filter-price-min_price', $slider).val(current_min_price);
						$('.js-filter-price-max_price', $slider).val(current_max_price);
						
						var offset = Math.round(275 * (current_min_price - min_price) / (max_price - min_price));
						$(".ui-slider-range", $slider).css({
							'background-position': 'left -' + offset + 'px top 0px'
						});
						
						$(document).trigger('ideapark.' + slug + '.slider.create', [current_min_price, current_max_price]);
					},
					slide  : function (event, ui) {
						
						$('.js-filter-price-min_price', $slider).val(ui.values[0]);
						$('.js-filter-price-max_price', $slider).val(ui.values[1]);
						
						// var $slider = $(event.target).closest('.c-filter-price');
						var min = parseFloat($('.js-filter-price-min_price', $slider).data('min'));
						var max = parseFloat($('.js-filter-price-max_price', $slider).data('max'));
						var offset = Math.round(275 * (ui.values[0] - min) / (max - min));
						
						$(".ui-slider-range", $slider).css({
							'background-position': 'left -' + offset + 'px top 0px'
						});
						$(document).trigger('ideapark.' + slug + '.slider.slide', [ui.values[0], ui.values[1]]);
					},
					change : function (event, ui) {
						$(document).trigger('ideapark.filter.update');
					}
				});
				//$( ".selector" ).on( "slide", function( event, ui ) {} );
			});
			
			var $wc_slider = $('.price_slider');
			
			if ($wc_slider.length) {
				$wc_slider
					.on('slide', function (event, ui) {
						var $slider = $(event.target).closest('.price_slider_wrapper');
						var min = parseInt($('#min_price', $slider).data('min'));
						var max = parseInt($('#max_price', $slider).data('max'));
						var offset = Math.round(275 * (ui.values[0] - min) / (max - min));
						
						$(".ui-slider-range", $slider).css({
							'background-position': 'left -' + offset + 'px top 0px'
						});
					});
				setTimeout(function () {
					if (typeof $wc_slider.slider == 'function') {
						var values = $wc_slider.slider("values");
						var $slider = $($wc_slider).closest('.price_slider_wrapper');
						var min = parseInt($('#min_price', $slider).data('min'));
						var max = parseInt($('#max_price', $slider).data('max'));
						var offset = Math.round(275 * (values[0] - min) / (max - min));
						
						$(".ui-slider-range", $slider).css({
							'background-position': 'left -' + offset + 'px top 0px'
						});
					}
				}, 300);
			}
		};
		
		root.ideapark_filter_sticky = function () {
			if (ideapark_is_mobile_layout) {
				return
			}
			var filter = document.getElementById('filter-sticky');
			var sidebar = document.getElementById('sidebar');
			if (filter) {
				var filter_buttons = document.getElementById('filter-bottom');
				var offsets = filter_buttons.getBoundingClientRect();
				var top = offsets.top;
				var diff = top + filter_buttons.offsetHeight - window.innerHeight;
				var is_top = sidebar.offsetHeight - filter.offsetHeight - diff - 25 <= 0;
				if (is_top && !filter.classList.contains('c-catalog-sidebar__sticky--top')) {
					filter.classList.add('c-catalog-sidebar__sticky--top');
					filter.classList.remove('c-catalog-sidebar__sticky--bottom');
				} else if (!is_top && diff < 0 && !filter.classList.contains('c-catalog-sidebar__sticky--bottom')) {
					filter.classList.add('c-catalog-sidebar__sticky--bottom');
					filter.classList.remove('c-catalog-sidebar__sticky--top');
				} else if (!is_top && diff >= 0 && (filter.classList.contains('c-catalog-sidebar__sticky--bottom') || filter.classList.contains('c-catalog-sidebar__sticky--top'))) {
					filter.classList.remove('c-catalog-sidebar__sticky--bottom');
					filter.classList.remove('c-catalog-sidebar__sticky--top');
				}
			}
		};
		
		root.ideapark_owl_hide_arrows = function (event) {
			var $element;
			if (event instanceof jQuery) {
				$element = event;
			} else {
				$element = $(event.target);
			}
			var $prev = $element.find('.owl-prev');
			var $next = $element.find('.owl-next');
			var dot_count = $element.find('.owl-dot').length;
			if (!$element.hasClass('h-carousel--dots-hide')) {
				if (dot_count > 1) {
					$element.find('.owl-dots').removeClass('disabled');
				} else {
					$element.find('.owl-dots').addClass('disabled');
				}
			}
			if (!$element.hasClass('h-carousel--nav-hide')) {
				$element.find('.owl-nav').removeClass('disabled');
				if ($prev.length && $next.length) {
					if ($prev.hasClass('disabled') && $next.hasClass('disabled')) {
						$prev.addClass('h-hidden');
						$next.addClass('h-hidden');
						$element.find('.owl-nav').addClass('disabled');
					} else {
						$prev.removeClass('h-hidden');
						$next.removeClass('h-hidden');
					}
				}
			}
		};
		
		root.ideapark_init_notice = function () {
			var $notices;
			var $wrapper = $('.woocommerce-notices-wrapper');
			if ($wrapper.length && $wrapper.hasClass('woocommerce-notices-wrapper--ajax')) {
			} else {
				if ($wrapper.length) {
					if ($wrapper.text().trim() != '') {
						$notices = $wrapper.find('.woocommerce-notice').detach();
					}
					$wrapper.remove();
				} else {
					$notices = $('.woocommerce .woocommerce-notice').detach();
				}
				var $header = $('.c-page-header');
				if (!$header.length) {
					$header = $('#main-header');
				}
				$wrapper = $('<div class="woocommerce-notices-wrapper woocommerce-notices-wrapper--ajax"></div>');
				$header.after($wrapper);
			}
			ideapark_mega_menu_sticky_wpadmin(null, ideapark_adminbar_visible_height);
			if ($notices && $notices.length) {
				ideapark_show_notice($notices);
			}
		};
		
		root.ideapark_search_notice = function () {
			var $notices;
			var $wrapper = $('.woocommerce-notices-wrapper:not(.woocommerce-notices-wrapper--ajax)');
			if ($wrapper.length) {
				if ($wrapper.text().trim() != '') {
					$notices = $wrapper.find('.woocommerce-notice').detach();
				}
				$wrapper.remove();
				
				ideapark_mega_menu_sticky_wpadmin(null, ideapark_adminbar_visible_height);
				if ($notices && $notices.length) {
					ideapark_show_notice($notices);
				}
			}
		};
		
		root.ideapark_show_notice = function (notice) {
			if (ideapark_empty(notice)) {
				return;
			}
			var $wrapper = $('.woocommerce-notices-wrapper');
			var $notices = notice instanceof jQuery ? notice : $(notice);
			var is_new = !$wrapper.find('.woocommerce-notice').length;
			if (is_new) {
				$wrapper.css({display: 'none'});
			}
			$notices.addClass('shown');
			$wrapper.append($notices);
			ideapark_mega_menu_sticky_wpadmin(null, ideapark_adminbar_visible_height);
			if (is_new) {
				var dif = $wrapper.outerHeight() + 150;
				var top_orig = ideapark_is_mobile_layout ? 0 : parseInt($wrapper.css('top').replace('px', ''));
				$wrapper.css({top: (top_orig - dif) + 'px'});
				$wrapper.css({display: ''});
				$({y: top_orig}).animate({y: top_orig + dif}, {
					step    : function (y) {
						$wrapper.css({
							top: (y - dif) + 'px',
						});
					},
					duration: 500,
					complete: function () {
						$wrapper.addClass('woocommerce-notices-wrapper--transition');
					}
				});
			}
			
			$notices.find('.js-notice-close').each(function () {
				var $close = $(this);
				setTimeout(function () {
					$close.trigger('click');
				}, 5000);
			});
		};
		
		root.ideapark_show_notice_success = function (message) {
			ideapark_show_notice($('<div class="woocommerce-notice shown" role="alert">\n' +
				'\t\t<i class="ip-wc-success woocommerce-notice-success-svg"></i>\n' +
				'\t\t' + message + '\t\t<button class="h-cb h-cb--svg woocommerce-notice-close js-notice-close"><i class="ip-close woocommerce-notice-close-svg"></i></button>\n' +
				'\t</div>'))
		};
		
		root.ideapark_show_notice_error = function (message) {
			ideapark_show_notice($('<div class="woocommerce-notice  shown" role="alert">\n' +
				'\t\t<i class="ip-wc-error woocommerce-notice-error-svg"></i>\n' +
				'\t\t' + message + '\t\t<button class="h-cb h-cb--svg woocommerce-notice-close js-notice-close"><i class="ip-close woocommerce-notice-close-svg"></i></button>\n' +
				'\t</div>'))
		};
		
		root.ideapark_init_single_product_carousel = function () {
			
			var $carousel = $('.js-single-product-carousel:not(.owl-carousel)');
			if ($carousel.hasClass('woocommerce-product-gallery__wrapper')) {
				var index = 0;
				$carousel.find('a').each(function () {
					var $this = $(this);
					$this.addClass('js-product-zoom').data('index', index).attr('data-elementor-open-lightbox', 'no');
					index++;
					$this.append('<span class="c-product__loading js-loading-wrap"></span>');
				});
			}
			
			if ($carousel.children().length > 1) {
				$carousel.addClass('owl-carousel').owlCarousel({
					center    : false,
					items     : 1,
					loop      : false,
					margin    : 0,
					nav       : true,
					dots      : false,
					autoHeight: true,
					navText   : ideapark_nav_text
				})
					.on('changed.owl.carousel', function (event) {
						var currentItem = event.item.index;
						$('.c-vehicle-details__thumbs-item.active,.c-product__thumbs-item.active').removeClass('active');
						$('.c-vehicle-details__thumbs-item,.c-product__thumbs-item').eq(currentItem).addClass('active');
						$('.js-product-thumbs-carousel').trigger('to.owl.carousel', [currentItem, 300]);
					});
				ideapark_init_product_thumbs_carousel();
				
				setTimeout(function () {
					$('.c-vehicle-details__gallery-img[loading="lazy"]').removeAttr('loading');
				}, 1500);
			}
		};
		
		root.ideapark_init_product_thumbs_carousel = function () {
			$('.js-product-thumbs-carousel:not(.owl-carousel)').addClass('owl-carousel').owlCarousel({
				center   : false,
				loop     : false,
				margin   : 20,
				nav      : false,
				dots     : false,
				autoWidth: true,
				navText  : ideapark_nav_text
			});
			$('.js-single-product-thumb:not(.init)').addClass('init').click(function () {
				var $this = $(this);
				var index = $this.data('index');
				var $item;
				
				if ($this.hasClass('wc-thumb')) {
					$item = $this.closest('.c-product__thumbs-item');
					$('.c-product__thumbs-item.active').removeClass('active');
				} else {
					$item = $this.closest('.c-vehicle-details__thumbs-item');
					$('.c-vehicle-details__thumbs-item.active').removeClass('active');
				}
				
				$item.addClass('active');
				$('.js-single-product-carousel').trigger("to.owl.carousel", [index, 300]);
			});
		};
		
		root.ideapark_init_review_placeholder = function () {
			$('#reviews #commentform textarea, #reviews #commentform input, .woocommerce-Input--text').each(function () {
				var $this = $(this);
				var $label = $this.parent().find('label');
				if ($label.length) {
					$this.attr('placeholder', $label.text());
				}
			});
		};
		
		root.ideapark_init_masonry = function () {
			if (ideapark_masonry_sidebar_on) {
				var window_width = $window.width();
				var is_sidebar_masonry_width = window_width >= 720 && window_width <= 1169;
				if (!ideapark_is_masonry_init) {
					
					ideapark_is_masonry_init = true;
					
					var init_f = function () {
						if (ideapark_masonry_sidebar_on && is_sidebar_masonry_width) {
							ideapark_masonry_sidebar_object = new Masonry($ideapark_masonry_sidebar[0], {
								itemSelector   : '.widget:not(.widget[style*="display: none"])',
								percentPosition: true
							});
							$ideapark_masonry_sidebar.addClass('init-masonry');
						}
					};
					
					if (typeof root.Masonry !== 'undefined') {
						init_f();
					} else {
						require([
							ideapark_wp_vars.masonryUrl
						], function (Masonry) {
							root.Masonry = Masonry;
							init_f();
						});
					}
				} else {
					if (ideapark_masonry_sidebar_on) {
						var is_init = $ideapark_masonry_sidebar.hasClass('init-masonry');
						if (is_sidebar_masonry_width && !is_init) {
							ideapark_masonry_sidebar_object = new Masonry($ideapark_masonry_sidebar[0], {
								itemSelector   : '.widget:not(.widget[style*="display: none"])',
								percentPosition: true
							});
							$ideapark_masonry_sidebar.addClass('init-masonry');
						} else if (!is_sidebar_masonry_width && is_init) {
							ideapark_masonry_sidebar_object.destroy();
							ideapark_masonry_sidebar_object = null;
							$ideapark_masonry_sidebar.removeClass('init-masonry');
							setTimeout(function () {
								$ideapark_masonry_sidebar.find('.widget').css({left: '', top: ''});
							}, 300);
						}
					}
				}
			}
		};
		
		root.ideapark_init_favorites = function () {
			var favoritesCookieName = "ip-favorites";
			var $button = $('.js-favorite');
			var $button_remove = $('.js-favorite-remove');
			var pageID = $button.length ? $button.data('id') : 0;
			var favoritesCookie = {};
			var viewButton = '<a class="button" href="' + ideapark_wp_vars.catalogLink + '">' + ideapark_wp_vars.viewFavorites + '</a> ';
			
			if ($.fn.cookie || typeof (Cookies) !== 'undefined') {
				favoritesCookie = $.fn.cookie ? $.cookie(favoritesCookieName) : Cookies.get(favoritesCookieName);
				favoritesCookie = favoritesCookie ? JSON.parse(favoritesCookie) : {};
			}
			
			var switch_on_off = function () {
				if (Object.keys(favoritesCookie).length) {
					$('.c-header__top-row-item--favorites:not(.active)').addClass('active');
					return true;
				} else {
					$('.c-header__top-row-item--favorites.active').removeClass('active');
					return false;
				}
			};
			
			$button.click(function (e) {
				e.preventDefault();
				var $this = $(this);
				var $title = $('.c-vehicle-details__download-title', $this);
				var $vehicle = $this.closest('.c-vehicle-details');
				$this.toggleClass('active');
				if ($this.hasClass('active')) {
					$title.html($this.data('remove'));
					if (!favoritesCookie.hasOwnProperty(pageID)) {
						favoritesCookie[pageID] = pageID;
						ideapark_show_notice_success(viewButton + $vehicle.data('title') + ' ' + ideapark_wp_vars.addedToFavorites);
					}
				} else {
					$title.html($this.data('add'));
					if (favoritesCookie.hasOwnProperty(pageID)) {
						delete favoritesCookie[pageID];
						ideapark_show_notice_success(viewButton + $vehicle.data('title') + ' ' + ideapark_wp_vars.removedFromFavorites);
					}
				}
				if ($.fn.cookie) {
					$.cookie(favoritesCookieName, JSON.stringify(favoritesCookie), {expires: 30});
				} else if (typeof (Cookies) !== 'undefined') {
					Cookies.set(favoritesCookieName, JSON.stringify(favoritesCookie), {expires: 30})
				}
				switch_on_off();
			});
			
			$button_remove.click(function (e) {
				e.preventDefault();
				var $vehicle = $(this).closest('.c-vehicle,.c-vehicle-vert');
				var pageID = $vehicle.data('id');
				$vehicle.find('.js-favorites,.js-favorite-remove').remove();
				if (favoritesCookie.hasOwnProperty(pageID)) {
					delete favoritesCookie[pageID];
					ideapark_show_notice_success(viewButton + $vehicle.data('title') + ' ' + ideapark_wp_vars.removedFromFavorites);
				}
				if ($.fn.cookie) {
					$.cookie(favoritesCookieName, JSON.stringify(favoritesCookie), {expires: 30});
				} else if (typeof (Cookies) !== 'undefined') {
					Cookies.set(favoritesCookieName, JSON.stringify(favoritesCookie), {expires: 30})
				}
				if ($vehicle.data('favorites-list')) {
					ideapark_on_transition_end_callback($vehicle, function () {
						$vehicle.remove();
					});
					$vehicle.addClass('c-vehicle--hidden');
					if (!switch_on_off()) {
						document.location.reload();
					}
				} else {
					switch_on_off();
				}
			});
			
			if (favoritesCookie) {
				for (var id in favoritesCookie) {
					$('.c-favorites__item-' + favoritesCookie[id] + '-btn').addClass('c-favorites__btn--added');
				}
			}
		};
		
		$.fn.extend({
			ideapark_button: function (option, size) {
				return this.each(function () {
					var $this = $(this);
					if (typeof size === 'undefined') {
						size = 16;
					}
					if (option === 'loading' && !$this.hasClass('js-loading')) {
						$this.data('button', $this.html());
						$this.data('css-width', $this.css('width'));
						$this.data('css-height', $this.css('height'));
						$this.css('height', $this.outerHeight());
						$this.css('width', $this.outerWidth());
						var $loader = $('<i class="h-loading c-add-to-cart__loading"></i>');
						$loader.css({
							width : size + 'px',
							height: size + 'px',
						});
						$this.html($loader);
						$this.addClass('h-after-before-hide js-loading');
					} else if (option === 'reset' && $this.hasClass('js-loading')) {
						var css_width = $this.data('css-width');
						var css_height = $this.data('css-height');
						var content = $this.data('button');
						$this.data('button', '');
						$this.data('css-width', '');
						$this.data('css-height', '');
						$this.html(content);
						$this.removeClass('h-after-before-hide js-loading');
						$this.css('width', css_width);
						$this.css('height', css_height);
					}
				})
			}
		});
		
		$.parseParams = function (query) {
			var re = /([^&=]+)=?([^&]*)/g;
			var decodeRE = /\+/g;
			var decode = function (str) {
				return decodeURIComponent(str.replace(decodeRE, " "));
			};
			var params = {}, e;
			while (e = re.exec(query)) {
				var k = decode(e[1]), v = decode(e[2]);
				if (k.substring(k.length - 2) === '[]') {
					k = k.substring(0, k.length - 2);
					(params[k] || (params[k] = [])).push(v);
				} else params[k] = v;
			}
			return params;
		};
		
	}
)(jQuery, window);


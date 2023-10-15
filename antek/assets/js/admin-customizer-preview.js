(function ($, root, undefined) {
	"use strict";

	var api = root.wp.customize;
	var ideapark_callback_after_reload = null;
	
	var ideapark_reload_custom_css = function ($element) {
		if (!ideapark_empty($element)) {
			$element.addClass('ip-customize-partial-refreshing');
		} else {
			$('body').addClass('ip-customize-partial-refreshing');
		}
		$.ajax({
			url    : ideapark_wp_vars.ajaxUrl,
			type   : 'POST',
			data   : {
				action: 'ideapark_ajax_custom_css',
			},
			success: function (result) {
				if ($('#ideapark-core-inline-css').length) {
					$('#ideapark-core-inline-css').html(result);
				} else {
					$('head').append('<style id="ideapark-core-inline-css">' + result + '</style>');
				}
				if (!ideapark_empty($element)) {
					$element.removeClass('ip-customize-partial-refreshing');
				} else {
					$('body').removeClass('ip-customize-partial-refreshing');
				}
				
				if (ideapark_callback_after_reload !== null) {
					ideapark_callback_after_reload();
					ideapark_callback_after_reload = null;
				}
			}
		});
	};

	api.bind(
		'preview-ready', function () {
			api.preview.bind(
				'refresh-pre-callback', function (obj) {
					if (ideapark_isset(obj.callback) && ideapark_is_function(window[obj.callback])) {
						window[obj.callback]();
					}
				}
			);
			api.preview.bind(
				'refresh-custom-css', function (obj) {
					ideapark_reload_custom_css(!ideapark_empty(obj.selector) ? $(obj.selector) : null);
				}
			);
			api.preview.bind(
				'customize-partial-edit-shortcut', function (obj) {
					var shortcutTitle, $buttonContainer, $button, $image;
					shortcutTitle = wp.customize.selectiveRefresh.data.l10n.clickEditMisc;

					for (var index in obj) {
						if (obj.hasOwnProperty(index)) {
							var control_name = obj[index];

							$image = $('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M13.89 3.39l2.71 2.72c.46.46.42 1.24.03 1.64l-8.01 8.02-5.56 1.16 1.16-5.58s7.6-7.63 7.99-8.03c.39-.39 1.22-.39 1.68.07zm-2.73 2.79l-5.59 5.61 1.11 1.11 5.54-5.65zm-2.97 8.23l5.58-5.6-1.07-1.08-5.59 5.6z"/></svg>');
							$button = $('<button>', {
								'aria-label': shortcutTitle,
								'title'     : shortcutTitle,
								'class'     : 'customize-partial-edit-shortcut-button ideapark-customize-partial-edit-shortcut-button'
							});
							$buttonContainer = $('<span>', {
								'class': 'customize-partial-edit-shortcut ideapark-customize-partial-edit-shortcut customize-partial-edit-shortcut-' + control_name
							});
							$button.append($image);
							$buttonContainer.append($button);
							$(index).prepend($buttonContainer);
							(function ($buttonContainer, control_name) {
								$buttonContainer.click(function () {
									api.preview.send('focus-control-for-setting', control_name);
									setTimeout(function () {
										$buttonContainer.blur();
									}, 500);
								});
							})($buttonContainer, control_name);
						}
					}
				}
			);
			api.preview.bind(
				'refresh-set-callback', function (obj) {
					ideapark_callback_after_reload = ideapark_isset(obj.callback) && ideapark_is_function(window[obj.callback]) ? window[obj.callback] : null;
				}
			);
			api.selectiveRefresh.bind(
				'partial-content-rendered', function (obj) {
					if (ideapark_callback_after_reload !== null) {
						ideapark_callback_after_reload();
						ideapark_callback_after_reload = null;
					}
				}
			);
			$(document).on('click', '.customizer-edit', function () {
				api.preview.send('focus-control-for-setting', $(this).data('control'));
			});
		}
	);
})(jQuery, this);
(function ($, root, undefined) {
	"use strict";
	$(document).ready(
		function () {
			$('body').on(
				'click', '.ideapark_plugins_installer_link', function (e) {
					e.preventDefault();
					var $this = $(this);
					if ($this.hasClass('process-now')) {
						return;
					}
					$this.addClass('process-now updating-message');

					ideapark_plugin_install_action($this);
					return false;
				}
			);
		}
	);

	var ideapark_plugin_install_current_action = '';
	var ideapark_plugin_install_current_action_cnt = 0;
	var ideapark_plugin_install_button_text_default = '';
	var $error = $('.ideapark_plugins_installer_error');
	var ideapark_plugin_install_action = function ($button) {

		var is_additional = $button.hasClass('additional');
		var array_values = [];

		if (is_additional) {
			$('.ideapark_additional_plugin').each(function () {
				if ($(this).is(':checked')) {
					array_values.push($(this).val());
				}
			});
		}

		$error.html('');
		$.ajax({
			url     : ideapark_pi_vars.ajaxUrl,
			type    : 'POST',
			dataType: 'json',
			data    : {
				action       : 'ideapark_about_ajax',
				is_additional: is_additional ? 1 : 0,
				plugins      : array_values.join(',')
			},
			success : function (result) {
				if (typeof result !== 'undefined' && result.action) {
					if (ideapark_plugin_install_current_action == result.action) {
						ideapark_plugin_install_current_action_cnt++;
					} else {
						ideapark_plugin_install_current_action_cnt = 0;
					}
					if (ideapark_plugin_install_current_action_cnt < 2) {
						if (!ideapark_plugin_install_button_text_default) {
							ideapark_plugin_install_button_text_default = $button.html();
						}
						ideapark_plugin_install_current_action = result.action;
						$button.html(result.name);
						$.get(result.action, function () {
							ideapark_plugin_install_action($button)
						});
					} else {
						$button.removeClass('process-now updating-message');
						$button.html(ideapark_plugin_install_button_text_default);
					}
				} else if (typeof result !== 'undefined' && result.success) {
					$button.removeClass('process-now updating-message');
					if (!is_additional) {
						$button.addClass('hidden');
						$('.ideapark_plugins_installer_success').removeClass('hidden');
						$('.ideapark_about_notes,.ideapark_about_description').addClass('hidden');
						$('.ideapark_about_next_step').removeClass('hidden');
					} else {
						if (result.list != '') {
							$button.html(ideapark_plugin_install_button_text_default);
						} else {
							$button.addClass('hidden');
							$('.additional-plugins-installed').removeClass('hidden');
						}
						$('.plugins_list').replaceWith(result.list);
					}
				} else {
					$button.removeClass('process-now updating-message');
					$error.html(ideapark_pi_vars.errorText);
				}
			},
			error   : function (xhr, ajaxOptions, thrownError) {
				$button.removeClass('process-now updating-message');
				$error.html(ideapark_pi_vars.errorText);
			}
		});
	}
})(jQuery, this);
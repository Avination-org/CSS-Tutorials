(function ($, root, undefined) {
	"use strict";
	
	$(document).on(
		'tinymce-editor-init', function () {
			$('.ideapark_text_editor .wp-editor-area').each(
				function () {
					var tArea = $(this),
						id = tArea.attr('id'),
						input = tArea.parents('.ideapark_text_editor').prev(),
						editor = tinyMCE.get(id),
						content;
					// Duplicate content from TinyMCE editor
					if (editor) {
						editor.on(
							'change', function () {
								this.save();
								content = editor.getContent();
								input.val(content).trigger('change');
							}
						);
					}
					// Duplicate content from HTML editor
					tArea.css(
						{
							visibility: 'visible'
						}
					).on(
						'keyup', function () {
							content = tArea.val();
							input.val(content).trigger('change');
						}
					);
				}
			);
		}
	);
	
	(function (api) {
		if (api === undefined) {
			return;
		}
		
		$(document).ready(function () {
			
			$('.js-ideapark-checkbox-set-item').click(function () {
				var choices = '';
				var $control = $(this).closest('.ideapark-checkbox-set-list');
				$control.find('input[type="checkbox"]').each(
					function () {
						if ($(this).get(0).checked) {
							choices += (choices ? ',' : '') + $(this).val();
						}
					}
				);
				$('.js-ideapark-checkbox-set',$control).val(choices).trigger('change');
			});
			
			var ideapark_checklist_delete = function () {
				var $button = $(this);
				var section_id = $button.data('section');
				var $control = $button.closest('.customize-control');
				var $checklist = $control.find('.ideapark_checklist');
				var $element = $button.closest('.ideapark_checklist_item_label');
				var delete_ajax_action = $checklist.data('delete-ajax-action');
				if (delete_ajax_action && !ideapark_empty(section_id)) {
					
					$element.addClass('ideapark_checklist_item_label--loading');
					$.ajax({
						url    : ideapark_ac_vars.ajaxUrl,
						type   : 'POST',
						data   : {
							action : delete_ajax_action,
							section: section_id
						},
						success: function (result) {
							$button.removeClass('loading');
							$button.prop('disabled', false);
							if (result.success) {
								$element.remove();
								$checklist.sortable('refresh');
								ideapark_admin_refresh_checklist($checklist);
								if (!ideapark_empty(api.section('ideapark_section_' + section_id))) {
									api.section('ideapark_section_' + section_id).deactivate();
								}
							}
							if (result.error) {
								alert(result.error);
							}
							$element.removeClass('ideapark_checklist_item_label--loading');
						},
						error  : function (xhr, ajaxOptions, thrownError) {
							$element.removeClass('ideapark_checklist_item_label--loading');
							$button.removeClass('loading');
							$button.prop('disabled', false);
							$button.removeClass('process-now updating-message');
							$error.html(ideapark_ac_vars.errorText);
						}
					});
				} else {
					$element.remove();
					$checklist.sortable('refresh');
					ideapark_admin_refresh_checklist($checklist);
				}
			};
			
			$('.ideapark_checklist_item_delete').click(ideapark_checklist_delete);
			
			$('.ideapark_checklist_add_button').click(function () {
				var $button = $(this);
				var $control = $button.closest('.customize-control');
				var $select = $control.find('.ideapark_checklist_add_select');
				var section_id = $select.val();
				var section_name = $select.find(':selected').text().replace(/^[ \-]+/g, '');
				var $checklist = $control.find('.ideapark_checklist');
				var add_ajax_action = $checklist.data('add-ajax-action');
				
				if (ideapark_empty(section_id) || section_id.substring(0, 1) === '*') {
					return false;
				}
				
				if (add_ajax_action && !ideapark_empty(section_id) && $control.length) {
					if ($button.hasClass('loading')) {
						return;
					}
					$button.addClass('loading');
					$button.prop('disabled', true);
					$.ajax({
						url    : ideapark_ac_vars.ajaxUrl,
						type   : 'POST',
						data   : {
							action : add_ajax_action,
							section: section_id
						},
						success: function (result) {
							$button.removeClass('loading');
							$button.prop('disabled', false);
							if (result.name) {
								var $new_row = $('<div class="ideapark_checklist_item_label ideapark_sortable_item"><label><input type="checkbox" value="1" data-name="' + result.id + '">' + result.name + '</label><button type="button" class="ideapark_checklist_item_delete" data-section="' + result.id + '"><span class="dashicons dashicons-no-alt"></span></button></div>');
								$control.find('.ideapark_checklist_add_notice').show('slow');
								$new_row.find('.ideapark_checklist_item_delete').click(ideapark_checklist_delete);
								$checklist.append($new_row);
								$checklist.sortable('refresh');
								ideapark_admin_refresh_checklist($checklist);
							}
							if (result.error) {
								alert(result.error);
							}
						},
						error  : function (xhr, ajaxOptions, thrownError) {
							$button.removeClass('loading');
							$button.prop('disabled', false);
							$button.removeClass('process-now updating-message');
							$error.html(ideapark_ac_vars.errorText);
						}
					});
				} else if (!ideapark_empty(section_id) && $control.length) {
					var $new_row = $('<div class="ideapark_checklist_item_label ideapark_sortable_item"><label><input type="checkbox" value="1" data-name="' + section_id + '">' + section_name + '</label><button type="button" class="ideapark_checklist_item_delete"><span class="dashicons dashicons-no-alt"></span></button></div>');
					$new_row.find('.ideapark_checklist_item_delete').click(ideapark_checklist_delete);
					$checklist.append($new_row);
					$checklist.sortable('refresh');
					ideapark_admin_refresh_checklist($checklist);
				}
			});
			
			var ideapark_admin_refresh_checklist = function (container) {
				var choices = '';
				container.find('input[type="checkbox"]').each(
					function () {
						choices += (choices ? '|' : '') + $(this).data('name') + '=' + ($(this).get(0).checked ? $(this).val() : '0');
					}
				);
				container.siblings('input[type="hidden"]').eq(0).val(choices).trigger('change');
			};
			
			$('.ideapark_checklist:not(.inited)').addClass('inited')
				.on(
					'change', 'input[type="checkbox"]', function () {
						ideapark_admin_refresh_checklist($(this).parents('.ideapark_checklist'));
					}
				)
				.each(
					function () {
						if ($.ui.sortable && $(this).hasClass('ideapark_sortable')) {
							var id = $(this).attr('id');
							if (id === undefined) {
								$(this).attr('id', 'ideapark_sortable_' + ('' + Math.random()).replace('.', ''));
							}
							
							$(this).find('.ideapark_checklist_item_edit').click(function () {
								var $button = $(this);
								if ($button.data('control')) {
									var control = api.control($button.data('control'));
									if (control) {
										control.focus();
									}
								}
							});
							
							$(this).sortable(
								{
									items      : ".ideapark_sortable_item",
									placeholder: ' ideapark_checklist_item_label ideapark_sortable_item ideapark_sortable_placeholder',
									update     : function (event, ui) {
										var choices = '';
										ui.item.parent().find('input[type="checkbox"]').each(
											function () {
												choices += (choices ? '|' : '')
													+ $(this).data('name') + '=' + ($(this).get(0).checked ? $(this).val() : '0');
											}
										);
										ui.item.parent().siblings('input[type="hidden"]').eq(0).val(choices).trigger('change');
									}
								}
							)
								.disableSelection();
						} else {
							var $first_checked_element = $(this).find('input[type=checkbox]:checked').first();
							if ($first_checked_element.length) {
								var scroll = $first_checked_element.closest('.ideapark_checklist_item_label').position().top;
								$(this)[0].scrollTop = scroll;
							}
						}
					}
				);
			
			if ($.ui && $.ui.slider) {
				$('.ideapark_range_slider:not(.inited)').addClass('inited')
					.each(
						function () {
							// Get parameters
							var range_slider = $(this);
							var linked_field = range_slider.data('linked_field');
							if (linked_field === undefined) {
								linked_field = range_slider.siblings('input[type="hidden"],input[type="text"]');
							} else {
								linked_field = $('#' + linked_field);
							}
							if (linked_field.length == 0) {
								return;
							}
							linked_field.on(
								'change', function () {
									var minimum = range_slider.data('min');
									if (minimum === undefined) {
										minimum = 0;
									}
									var maximum = range_slider.data('max');
									if (maximum === undefined) {
										maximum = 0;
									}
									var values = $(this).val().split(',');
									for (var i = 0; i < values.length; i++) {
										if (isNaN(values[i])) {
											value[i] = minimum;
										}
										values[i] = Math.max(minimum, Math.min(maximum, Number(values[i])));
										if (values.length == 1) {
											range_slider.slider('value', values);
										} else {
											range_slider.slider('values', i, values[i]);
										}
									}
									update_cur_values(values);
									$(this).val(values.join(','));
								}
							);
							var range_slider_cur = range_slider.find('> .ideapark_range_slider_label_cur');
							var range_slider_type = range_slider.data('range');
							if (range_slider_type === undefined) {
								range_slider_type = 'min';
							}
							var values = linked_field.val().split(',');
							var minimum = range_slider.data('min');
							if (minimum === undefined) {
								minimum = 0;
							}
							var maximum = range_slider.data('max');
							if (maximum === undefined) {
								maximum = 0;
							}
							var step = range_slider.data('step');
							if (step === undefined) {
								step = 1;
							}
							// Init range slider
							var init_obj = {
								range : range_slider_type,
								min   : minimum,
								max   : maximum,
								step  : step,
								slide : function (event, ui) {
									var cur_values = range_slider_type === 'min' ? [ui.value] : ui.values;
									linked_field.val(cur_values.join(',')).trigger('change');
									update_cur_values(cur_values);
								},
								create: function (event, ui) {
									update_cur_values(values);
								}
							};
							
							function update_cur_values(cur_values) {
								for (var i = 0; i < cur_values.length; i++) {
									range_slider_cur.eq(i)
										.html(cur_values[i])
										.css('left', Math.max(0, Math.min(100, (cur_values[i] - minimum) * 100 / (maximum - minimum))) + '%');
								}
							}
							
							if (range_slider_type === true) {
								init_obj.values = values;
							} else {
								init_obj.value = values[0];
							}
							range_slider.addClass('inited').slider(init_obj);
						}
					);
			}
			
		});
		
		var reloading_element_selector = null;
		
		var ideapark_refresh_css = ideapark_debounce(function () {
			api.previewer.send('refresh-custom-css', {selector: reloading_element_selector});
		}, 500);
		
		api.controlConstructor.category_image_background_position = api.BackgroundPositionControl;
		
		api.bind(
			'change', function (obj) {
				
				if (ideapark_isset(ideapark_dependencies.refresh_pre_callback) && typeof ideapark_dependencies.refresh_pre_callback[obj.id] !== 'undefined') {
					api.previewer.send('refresh-pre-callback', {callback: ideapark_dependencies.refresh_pre_callback[obj.id]});
				}
				
				if (ideapark_isset(ideapark_dependencies.refresh_callback) && typeof ideapark_dependencies.refresh_callback[obj.id] !== 'undefined') {
					api.previewer.send('refresh-set-callback', {callback: ideapark_dependencies.refresh_callback[obj.id]});
				}
				
				if (ideapark_isset(ideapark_dependencies.refresh_css) && ideapark_in_array(obj.id, ideapark_dependencies.refresh_css)) {
					if (typeof ideapark_dependencies.refresh[obj.id] !== 'undefined') {
						reloading_element_selector = ideapark_dependencies.refresh[obj.id];
					} else {
						reloading_element_selector = null;
					}
					ideapark_refresh_css();
				}
				
				// ideapark_customizer_check_dependencies($('#customize-theme-controls #customize-control-' + obj.id).parents('.control-section'));
				$('#customize-theme-controls .control-section').each(
					function () {
						ideapark_customizer_check_dependencies($(this));
					}
				);
			});
		
		api.bind('ready', function () {
			var control_name = window.location.hash.substring(1);
			if (control_name) {
				var control = api.control(control_name);
				if (control) {
					control.focus();
					window.location.hash = '';
				}
			}
			
			$('.ideapark-customizer-reload').click(function () {
				window.location.hash = $(this).data('id');
				window.location.reload();
			});
			
			api.previewer.bind('focus-control-for-setting', function (settingId) {
				var matchedControls = [];
				api.control.each(function (control) {
					var settingIds = _.pluck(control.settings, 'id');
					if (-1 !== _.indexOf(settingIds, settingId)) {
						matchedControls.push(control);
					}
				});
				
				if (matchedControls.length) {
					matchedControls.sort(function (a, b) {
						return a.priority() - b.priority();
					});
					matchedControls[0].container.addClass('ideapark-shaked');
					setTimeout(function () {
						matchedControls[0].container.removeClass('ideapark-shaked');
					}, 500);
				}
			});
			
			api.previewer.bind('ready', function () {
				api.previewer.send('customize-partial-edit-shortcut', ideapark_dependencies.refresh_only_css);
			});
			
			$('#customize-theme-controls .control-section').each(
				function () {
					ideapark_customizer_check_dependencies($(this));
				}
			);
			
			$('#customize-theme-controls .control-section > .accordion-section-title').on(
				'click', function () {
					var id = $(this).parent().attr('aria-owns');
					if (id !== '') {
						var section = $('#' + id);
						if (section.length > 0) {
							ideapark_customizer_check_dependencies(section);
						}
					}
				}
			);
			
			$('#customize-theme-controls .ideapark-control-focus').click(function (e) {
				e.preventDefault();
				var control_name = $(this).data('control');
				if (control_name) {
					var control = api.control(control_name);
					if (control) {
						control.focus();
					}
				}
			})
		});
		
	})(wp.customize);
	
	function ideapark_customizer_check_dependencies(container) {
		container.find('.customize-control').each(
			function () {
				var ctrl = $(this), id = ctrl.attr('id');
				if (id == undefined) {
					return;
				}
				id = id.replace('customize-control-', '');
				var fld = null, val = '', i;
				var depend = false;
				for (fld in ideapark_dependencies.dependency) {
					if (fld == id) {
						depend = ideapark_dependencies.dependency[id];
						break;
					}
				}
				if (depend) {
					var dep_cnt = 0, dep_all = 0;
					var dep_cmp = typeof depend.compare != 'undefined' ? depend.compare.toLowerCase() : 'and';
					var dep_strict = typeof depend.strict != 'undefined';
					for (i in depend) {
						if (i == 'compare' || i == 'strict') {
							continue;
						}
						dep_all++;
						var control = wp.customize.control(i);
						
						if (control) {
							val = control.setting.get();
							for (var j in depend[i]) {
								if (
									(('' + depend[i][j]).indexOf('search!=') === 0 && ('' + val).indexOf(('' + depend[i][j]).substring(8)) === -1)
									|| (('' + depend[i][j]).indexOf('search=') === 0 && ('' + val).indexOf(('' + depend[i][j]).substring(7)) !== -1)
									|| (depend[i][j] == 'not_empty' && (val !== '' && val !== '0' && val !== 0 && val !== false))   // Main field value is not empty - show current field
									|| (depend[i][j] == 'is_empty' && (val === '' || val === '0' || val === 0 || val === false))    // Main field value is empty - show current field
									|| (val !== '' && (!isNaN(depend[i][j])      // Main field value equal to specified value - show current field
											? val == depend[i][j]
											: (dep_strict
													? val == depend[i][j]
													: ('' + val).indexOf(depend[i][j]) == 0
											)
										)
									)
									|| (val !== '' && ('' + depend[i][j]).charAt(0) == '^' && ('' + val).indexOf(depend[i][j].substr(1)) == -1)	// Main field value not equal to specified value - show current field
								) {
									dep_cnt++;
									break;
								}
							}
						} else {
							dep_all--;
						}
						if (dep_cnt > 0 && dep_cmp == 'or') {
							break;
						}
					}
					if (((dep_cnt > 0 || dep_all == 0) && dep_cmp == 'or') || (dep_cnt == dep_all && dep_cmp == 'and')) {
						ctrl.show().removeClass('ideapark-control-not-used');
					} else {
						ctrl.hide().addClass('ideapark-control-not-used');
					}
				}
			}
		);
	}
	
})(jQuery, this);
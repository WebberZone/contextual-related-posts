jQuery(document).ready(function ($) {

	const prefix = WZSettingsAdmin.prefix || 'wz';

	// File browser.
	$('.file-browser').on('click', function (event) {
		event.preventDefault();

		var self = $(this);

		// Create the media frame.
		var file_frame = wp.media.frames.file_frame = wp.media({
			title: self.data('uploader_title'),
			button: {
				text: self.data('uploader_button_text'),
			},
			multiple: false
		});

		file_frame.on('select', function () {
			attachment = file_frame.state().get('selection').first().toJSON();
			self.prev('.file-url').val(attachment.url).change();
		});

		// Finally, open the modal
		file_frame.open();
	});

	$(function () {
		$("#post-body-content").tabs({
			create: function (event, ui) {
				$(ui.tab.find("a")).addClass("nav-tab-active");
			},
			activate: function (event, ui) {
				$(ui.oldTab.find("a")).removeClass("nav-tab-active");
				$(ui.newTab.find("a")).addClass("nav-tab-active");
			}
		});
	});

	// Initialize ColorPicker.
	$('.color-field').each(function (i, element) {
		$(element).wpColorPicker();
	});

	// Reset default thumbnail - uses plugin-specific localized data.
	$('.reset-default-thumb').on('click', function () {
		var settingsKey = WZSettingsAdmin.settings_key || '';
		var thumbDefault = (typeof window[WZSettingsAdmin.prefix + '_admin'] !== 'undefined')
			? window[WZSettingsAdmin.prefix + '_admin'].thumb_default
			: '';
		$('#' + settingsKey + '\\[thumb_default\\]').val(thumbDefault);
	});

	// Reset formmodified on submit.
	$('#' + prefix + '-settings-form').on('submit', function () {
		formmodified = 0;
	});

	// Initialize Repeater Fields.
	$('.wz-repeater-wrapper').each(function () {
		var wrapper = $(this);
		var itemsContainer = wrapper.find('.wz-repeater-items');
		var index = parseInt(wrapper.data('index'), 10) || 0;
		var liveUpdateField = wrapper.data('live-update-field') || 'name';
		var fallbackTitle = wrapper.data('fallback-title') || '';
		var liveUpdateOptions = wrapper.data('live-update-field-options') || {};

		function reindexItems() {
			itemsContainer.find('.wz-repeater-item').each(function (idx) {
				$(this).find(':input').each(function () {
					var name = $(this).attr('name');
					if (name) {
						name = name.replace(/\[\d+\](?=\[(?:fields|row_id)\])/, '[' + idx + ']');
						$(this).attr('name', name);
					}
				});
			});
		}

		// Add Item.
		wrapper.on('click', '.add-item', function () {
			var templateEl = wrapper.find('.repeater-template').get(0);
			if (!templateEl) {
				return;
			}
			var template = templateEl.innerHTML;
			var uniqueId = 'row_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
			template = template.replace(/{{INDEX}}/g, index);
			template = template.replace(/{{ROW_ID}}/g, uniqueId);
			itemsContainer.append(template);
			index++;
			var newItem = itemsContainer.find('.wz-repeater-item:last');
			itemsContainer.find('.repeater-item-header:last .toggle-icon').text('\u25b2');
			itemsContainer.find('.repeater-item-content:last').css('display', 'block');
			if (window.WZInitTomSelect) {
				window.WZInitTomSelect(newItem.get(0));
			}
			document.dispatchEvent(new CustomEvent('wz:repeater-item-added', { detail: { container: newItem.get(0) } }));
		});

		// Remove Item.
		wrapper.on('click', '.remove-item', function () {
			$(this).closest('.wz-repeater-item').remove();
			reindexItems();
		});

		// Move Up.
		wrapper.on('click', '.move-up', function () {
			var item = $(this).closest('.wz-repeater-item');
			var prev = item.prev();
			if (prev.length) {
				item.insertBefore(prev);
				reindexItems();
			}
		});

		// Move Down.
		wrapper.on('click', '.move-down', function () {
			var item = $(this).closest('.wz-repeater-item');
			var next = item.next();
			if (next.length) {
				item.insertAfter(next);
				reindexItems();
			}
		});

		// Toggle Accordion.
		wrapper.on('click', '.repeater-item-header', function () {
			var $this = $(this);
			var $toggleIcon = $this.find('.toggle-icon');
			var $content = $this.next('.repeater-item-content');
			if ($content.is(':visible')) {
				$content.slideUp();
				$toggleIcon.text('\u25bc');
			} else {
				$content.slideDown();
				$toggleIcon.text('\u25b2');
			}
		});

		// Enforce unique selection across rows for the field named in data-unique-field.
		var uniqueField = wrapper.data('unique-field') || '';
		function syncUniqueSelects() {
			if (!uniqueField) {
				return;
			}
			var $selects = itemsContainer.find('.wz-repeater-item select[name$="[fields][' + uniqueField + ']"]');
			var usedValues = {};
			$selects.each(function () {
				var val = $(this).val();
				if (val) {
					usedValues[val] = true;
				}
			});
			$selects.each(function () {
				var $sel = $(this);
				var ownVal = $sel.val();
				$sel.find('option').each(function () {
					var optVal = $(this).val();
					if (!optVal) {
						return;
					}
					$(this).prop('disabled', optVal !== ownVal && usedValues[optVal]);
				});
			});
		}

		if (uniqueField) {
			syncUniqueSelects();
			wrapper.on('change', '.wz-repeater-item select[name$="[fields][' + uniqueField + ']"]', function () {
				syncUniqueSelects();
			});
			wrapper.on('click', '.remove-item', function () {
				// Sync after DOM removal; removal handler fires before remove, so defer.
				setTimeout(syncUniqueSelects, 0);
			});
			document.addEventListener('wz:repeater-item-added', function (e) {
				if (wrapper.get(0).contains(e.detail.container)) {
					syncUniqueSelects();
				}
			});
		}

		// Live update repeater title when the specified field changes.
		// Handles text inputs (input event), selects (change event, uses option text),
		// and TomSelect-enhanced inputs (change event, uses displayed text or value).
		function updateRepeaterTitle($field) {
			var newName;
			var val = $field.val();
			if ($field.is('select')) {
				// Ignore placeholder options (empty value).
				newName = val ? $field.find('option:selected').text().trim() : '';
			} else {
				newName = val ? (liveUpdateOptions[val] || val) : '';
			}
			$field.closest('.wz-repeater-item').find('.repeater-title').text(newName || fallbackTitle);
		}

		wrapper.on('input change', '.wz-repeater-item :input[name$="[fields][' + liveUpdateField + ']"]', function () {
			updateRepeaterTitle($(this));
		});
	});

});

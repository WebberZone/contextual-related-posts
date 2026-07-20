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
				window.scrollTo({ top: 0, behavior: 'smooth' });
			}
		});
	});

	// Settings search: live-filters setting rows across all tabs.
	(function () {
		var $container = $('#post-body-content');
		var $searchInput = $('#' + prefix + '-settings-search');
		var $form = $('#' + prefix + '-settings-form');

		if (!$searchInput.length || !$form.length || !$container.length) {
			return;
		}

		var $panels = $form.children('div[id]');
		var $navLinks = $container.find('> ul.nav-tab-wrapper a.nav-tab');
		var $clearButton = $container.find('.wz-settings-search-clear');
		var $status = $container.find('.wz-settings-search-status');
		var strings = (typeof WZSettingsAdmin !== 'undefined' && WZSettingsAdmin.strings) || {};
		var $noResults = null;
		var $actionsBar = null;
		var indexBuilt = false;
		var debounceTimer = null;

		function getRows($panel) {
			return $panel.children('table.form-table').find('> tbody > tr, > tr');
		}

		// Build a searchable text string per row once, on first search.
		// Strips noise (select options, dropdown widgets, code editors) and adds input ids/names.
		function buildIndex() {
			$panels.each(function () {
				getRows($(this)).each(function () {
					var $row = $(this);
					var $clone = $row.clone();
					$clone.find('option, script, style, template, .ts-dropdown, .CodeMirror').remove();
					var text = $clone.text() + ' ';
					$row.find(':input[id], :input[name]').each(function () {
						text += ' ' + (this.id || '') + ' ' + (this.name || '');
					});
					$row.data('wzSearchText', text.toLowerCase());
				});
			});
			indexBuilt = true;
		}

		function clearHighlights() {
			$form.find('mark.wz-search-mark').each(function () {
				var parent = this.parentNode;
				$(this).replaceWith(document.createTextNode($(this).text()));
				parent.normalize();
			});
		}

		// Elements the highlighter must never descend into.
		var highlightSkip = 'input, textarea, select, button, script, style, template, mark, .CodeMirror, .ts-wrapper, .wp-picker-container, .wz-repeater-wrapper';

		// Wrap the first occurrence of query in each matching text node with <mark>.
		function highlightTerm($el, query) {
			$el.contents().each(function () {
				if (3 === this.nodeType) {
					var text = this.nodeValue;
					var idx = text.toLowerCase().indexOf(query);
					if (-1 !== idx) {
						var matched = text.slice(idx, idx + query.length);
						var afterNode = document.createTextNode(text.slice(idx + query.length));
						var mark = $('<mark class="wz-search-mark"></mark>').text(matched);
						this.nodeValue = text.slice(0, idx);
						$(this).after(afterNode).after(mark);
					}
				} else if (1 === this.nodeType && !$(this).is(highlightSkip)) {
					highlightTerm($(this), query);
				}
			});
		}

		function updateTabBadge(panelId, count) {
			var $link = $navLinks.filter('[href="#' + panelId + '"]');
			if (!$link.length) {
				return;
			}
			var $badge = $link.find('.wz-tab-count');
			if (0 === count) {
				$badge.remove();
			} else {
				if (!$badge.length) {
					$badge = $('<span class="wz-tab-count"><span class="wz-tab-count-number"></span><span class="screen-reader-text"></span></span>').appendTo($link);
					$badge.find('.screen-reader-text').text(' ' + (strings.search_matches_label || 'matching settings'));
				}
				$badge.find('.wz-tab-count-number').text(count);
			}
			$link.toggleClass('wz-tab-no-matches', 0 === count);
		}

		// Announce the result count to screen readers via the polite live region.
		function announceResults(total) {
			var template;
			if (0 === total) {
				template = strings.search_no_results || 'No settings found.';
			} else if (1 === total) {
				template = strings.search_results_single || '%d setting found.';
			} else {
				template = strings.search_results_plural || '%d settings found.';
			}
			$status.text(template.replace('%d', total));
		}

		// Single Save Changes bar shown while searching, replacing the hidden per-tab button rows.
		function toggleActionsBar(show) {
			if (show && !$actionsBar) {
				var $save = $form.find('input[type="submit"][name="submit"]').first().clone().removeAttr('id');
				if (!$save.length) {
					return;
				}
				$actionsBar = $('<p class="wz-search-actions"></p>').append($save).appendTo($form);
			}
			if ($actionsBar) {
				$actionsBar.toggle(show);
			}
		}

		function toggleNoResults(show) {
			if (show && !$noResults) {
				$noResults = $('<p class="wz-search-no-results"></p>')
					.text(strings.search_no_results || 'No settings found.')
					.appendTo($form);
			}
			if ($noResults) {
				$noResults.toggle(show);
			}
		}

		function resetSearch() {
			$container.removeClass('wz-searching');
			clearHighlights();
			$form.find('.wz-search-hidden').removeClass('wz-search-hidden');
			$form.find('.wz-search-match').removeClass('wz-search-match');
			$panels.removeClass('wz-has-matches');
			$navLinks.removeClass('wz-tab-no-matches').find('.wz-tab-count').remove();
			toggleNoResults(false);
			toggleActionsBar(false);
			$clearButton.prop('hidden', true);
			$status.text('');
		}

		function applySearch() {
			var query = $.trim($searchInput.val()).toLowerCase();

			if (!query) {
				resetSearch();
				return;
			}

			if (!indexBuilt) {
				buildIndex();
			}

			clearHighlights();
			$container.addClass('wz-searching');

			var total = 0;

			$panels.each(function () {
				var $panel = $(this);
				var $rows = getRows($panel);
				var count = 0;

				$rows.each(function () {
					var $row = $(this);
					var matched = -1 !== ($row.data('wzSearchText') || '').indexOf(query);
					$row.toggleClass('wz-search-match', matched).toggleClass('wz-search-hidden', !matched);
					if (matched) {
						count++;
					}
				});

				// Keep a section header visible when any row in its group matched.
				$rows.filter('.wz-settings-header-row').each(function () {
					var $header = $(this);
					if ($header.hasClass('wz-search-match')) {
						return;
					}
					var groupHasMatch = $header.nextUntil('.wz-settings-header-row').filter('.wz-search-match').length > 0;
					if (groupHasMatch) {
						$header.removeClass('wz-search-hidden');
					}
				});

				$panel.toggleClass('wz-has-matches', count > 0);
				updateTabBadge($panel.attr('id'), count);
				total += count;
			});

			// Highlight the matched term in the visible rows (labels and descriptions).
			$form.find('tr.wz-search-match').children('th, td').each(function () {
				highlightTerm($(this), query);
			});

			toggleNoResults(0 === total);
			toggleActionsBar(total > 0);
			$clearButton.prop('hidden', false);
			announceResults(total);
		}

		// 'search' also fires when the native clear (x) button is used.
		$searchInput.on('input search', function () {
			clearTimeout(debounceTimer);
			debounceTimer = setTimeout(applySearch, 200);
		});

		$searchInput.on('keydown', function (e) {
			if ('Escape' === e.key) {
				$(this).val('');
				applySearch();
			}
		});

		// Clear button: reset the search and return focus to the input.
		$clearButton.on('click', function () {
			$searchInput.val('');
			applySearch();
			$searchInput.trigger('focus');
		});

		// While searching, tab clicks scroll to that tab's results instead of switching tabs.
		// Capture-phase listener so it runs before, and blocks, the jQuery UI Tabs handlers.
		var navWrapper = $container.find('> ul.nav-tab-wrapper').get(0);
		if (navWrapper) {
			navWrapper.addEventListener(
				'click',
				function (e) {
					if (!$container.hasClass('wz-searching')) {
						return;
					}
					var link = e.target.closest ? e.target.closest('a.nav-tab') : null;
					if (!link) {
						return;
					}
					e.preventDefault();
					e.stopPropagation();
					var $panel = $(link.getAttribute('href'));
					if ($panel.hasClass('wz-has-matches')) {
						// Move focus to the section heading for keyboard/screen-reader users.
						var title = $panel.find('.wz-section-title').get(0);
						if (title) {
							title.focus({ preventScroll: true });
						}
						window.scrollTo({ top: $panel.offset().top - 80, behavior: 'smooth' });
					}
				},
				true
			);
		}
	})();

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
		$('#' + settingsKey + '-thumb_default').val(thumbDefault);
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

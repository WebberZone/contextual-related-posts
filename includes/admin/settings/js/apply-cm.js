/**
 * Initialise CodeMirror editors for various code types.
 *
 * @since 1.0.0
 */
jQuery(document).ready(function ($) {
	/**
	 * Mark form as modified.
	 *
	 * @since 1.0.0
	 */
	function confirmFormChange() {
		formmodified = 1;
	}

	/**
	 * Initialise a CodeMirror editor with specified mode.
	 *
	 * @since 1.0.0
	 *
	 * @param {jQuery} $element The textarea element to initialise.
	 * @param {string} mode     The CodeMirror mode (e.g., 'javascript', 'css').
	 */
	function initialiseCodeMirror($element, mode) {
		if (!$element.length || typeof wp.codeEditor !== 'object') {
			return;
		}

		// Skip if already initialised.
		if ($element.data('codemirror-initialised')) {
			return;
		}

		const editorSettings = wp.codeEditor.defaultSettings ? _.clone(wp.codeEditor.defaultSettings) : {};

		editorSettings.codemirror = _.extend(
			{},
			editorSettings.codemirror,
			mode ? { mode: mode } : {}
		);

		const editor = wp.codeEditor.initialize($element, editorSettings);
		editor.codemirror.on('change', confirmFormChange);

		// Mark as initialised.
		$element.data('codemirror-initialised', true);
	}

	// Initialise HTML editors.
	$('.codemirror_html').each(function () {
		initialiseCodeMirror($(this), null);
	});

	// Initialise JavaScript editors.
	$('.codemirror_js').each(function () {
		initialiseCodeMirror($(this), 'javascript');
	});

	// Initialise CSS editors.
	$('.codemirror_css').each(function () {
		initialiseCodeMirror($(this), 'css');
	});
});
/**
 * CodeMirror Media Insertion functionality.
 *
 * Handles media library integration for CodeMirror editor instances.
 *
 * @since 1.0.0
 */
jQuery(document).ready(function ($) {

	/**
	 * Insert string into CodeMirror editor.
	 *
	 * @since 1.0.0
	 *
	 * @param {Object} editor CodeMirror instance.
	 * @param {string} str    String to insert.
	 */
	function insertString(editor, str) {
		var selection = editor.getSelection();

		if (selection.length > 0) {
			editor.replaceSelection(str);
		} else {
			var doc = editor.getDoc();
			var cursor = doc.getCursor();
			var pos = {
				line: cursor.line,
				ch: cursor.ch
			};

			doc.replaceRange(str, pos);
		}
	}

	/**
	 * Generate attachment HTML via AJAX.
	 *
	 * @since 1.0.0
	 *
	 * @param {Object} props      Display properties.
	 * @param {Object} attachment Attachment object.
	 * @return {Promise} jQuery AJAX promise.
	 */
	function attachmentHtml(props, attachment) {
		var caption = attachment.caption;
		var options;
		var html;

		// Clear caption if disabled globally.
		if (!wp.media.view.settings.captions) {
			delete attachment.caption;
		}

		props = wp.media.string.props(props, attachment);

		options = {
			id: attachment.id,
			post_content: attachment.description,
			post_excerpt: caption
		};

		if (props.linkUrl) {
			options.url = props.linkUrl;
		}

		if ('image' === attachment.type) {
			html = wp.media.string.image(props);

			_.each({
				align: 'align',
				size: 'image-size',
				alt: 'image_alt'
			}, function (option, prop) {
				if (props[prop]) {
					options[option] = props[prop];
				}
			});
		} else if ('video' === attachment.type) {
			html = wp.media.string.video(props, attachment);
		} else if ('audio' === attachment.type) {
			html = wp.media.string.audio(props, attachment);
		} else {
			html = wp.media.string.link(props);
			options.post_title = props.title;
		}

		return $.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajaxurl,
			data: {
				action: 'send-attachment-to-editor',
				nonce: wp.media.view.settings.nonce.sendToEditor,
				attachment: options,
				html: html,
				post_id: wp.media.view.settings.post.id
			}
		});
	}

	/**
	 * Process media selection and insert into editor.
	 *
	 * @since 1.0.0
	 *
	 * @param {Object} editor    CodeMirror instance.
	 * @param {Object} fileFrame Media frame instance.
	 */
	function processMediaSelection(editor, fileFrame) {
		var selection = fileFrame.state().get('selection');
		var promises = [];

		selection.each(function (attachment) {
			var props = fileFrame.state().display(attachment).toJSON();
			var promise = attachmentHtml(props, attachment.toJSON()).done(function (response) {
				insertString(editor, response.data);
			});

			promises.push(promise);
		});

		$.when.apply($, promises).always(function () {
			fileFrame.close();
		});
	}

	/**
	 * Handle media insertion button clicks.
	 *
	 * @since 1.0.0
	 */
	$('.insert-codemirror-media').on('click', function (event) {
		event.preventDefault();
		event.stopImmediatePropagation();
		event.stopPropagation();
		$(this).removeClass('add_media');

		var editor = $('#wp-content-editor-container .CodeMirror')[0].CodeMirror;
		var fileFrame;

		fileFrame = wp.media.frames.file_frame = wp.media({
			frame: 'post',
			state: 'insert',
			multiple: false
		});

		// Remove old handlers and add insert handler only.
		fileFrame.off('insert').on('insert', function () {
			processMediaSelection(editor, fileFrame);
		});

		fileFrame.open();
	});

});
jQuery(document).ready(function ($) {
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
			}

			doc.replaceRange(str, pos);

		}

	}

	// Media selector.
	$('.insert-codemirror-media').on('click', function (event) {
		event.preventDefault();

		var self = $(this);
		var editor = $('#wp-content-editor-container .CodeMirror')[0].CodeMirror;

		function attachmentHtml(props, attachment) {
			var caption = attachment.caption,
				options, html;

			// If captions are disabled, clear the caption.
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
				},
				success: function (response) {
					//mediaHtml = response.data;
				}
			});
		}

		// Create the media frame.
		var file_frame = wp.media.frames.file_frame = wp.media({
			frame: 'post',
			state: 'insert',
			multiple: true
		});

		file_frame.on('insert', function () {
			var selection = file_frame.state().get('selection');

			selection.map(function (attachment) {

				var props = file_frame.state().display(attachment).toJSON();

				$.when(attachmentHtml(props, attachment.toJSON())).done(function (response) {
					mediaHtml = response.data;
					insertString(editor, mediaHtml);
				});
			});

		});

		// Finally, open the modal
		file_frame.open();
	});
});

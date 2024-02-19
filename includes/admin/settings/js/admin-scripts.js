jQuery(document).ready(function($) {
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

	// Prompt the user when they leave the page without saving the form.
	var formmodified=0;

	function confirmFormChange() {
		formmodified=1;
	}

	function confirmExit() {
		if ( formmodified == 1 ) {
			return true;
		}
	}

	function formNotModified() {
		formmodified = 0;
	}

	$('form *').change( confirmFormChange );

	window.onbeforeunload = confirmExit;

	$( "input[name='submit']" ).click(formNotModified);
	$( "input[id='search-submit']" ).click(formNotModified);
	$( "input[id='doaction']" ).click(formNotModified);
	$( "input[id='doaction2']" ).click(formNotModified);
	$( "input[name='filter_action']" ).click(formNotModified);

	$( function() {
		$( "#post-body-content" ).tabs({
			create: function( event, ui ) {
				$( ui.tab.find("a") ).addClass( "nav-tab-active" );
			},
			activate: function( event, ui ) {
				$( ui.oldTab.find("a") ).removeClass( "nav-tab-active" );
				$( ui.newTab.find("a") ).addClass( "nav-tab-active" );
			}
		});
	});

	// Initialise ColorPicker.
	$( '.color-field' ).each( function ( i, element ) {
		$( element ).wpColorPicker();
	});
	
	$('.reset-default-thumb').click(function(){
		document.getElementById("tptn_settings[thumb_default]").value = tptn_admin.thumb_default;
	});
});

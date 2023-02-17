// Function to clear the cache.
function crpClearCache() {
	/**** since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php ****/
	jQuery.post(ajaxurl, {
		action: 'crp_clear_cache',
		security: crp_admin_data.security
	}, function (response, textStatus, jqXHR) {
		alert(response.message);
	}, 'json');
}

jQuery(document).ready(function($) {
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

	// Initialise CodeMirror.
	$( ".codemirror_html" ).each( function( index, element ) {
		if( $( element ).length && typeof wp.codeEditor === 'object' ) {
			var editorSettings = wp.codeEditor.defaultSettings ? _.clone( wp.codeEditor.defaultSettings ) : {};
			editorSettings.codemirror = _.extend(
				{},
				editorSettings.codemirror,
				{
				}
			);
			var editor = wp.codeEditor.initialize( $( element ), editorSettings );
			editor.codemirror.on( 'change', confirmFormChange );
		}
	});

	$( ".codemirror_js" ).each( function( index, element ) {
		if( $( element ).length && typeof wp.codeEditor === 'object' ) {
			var editorSettings = wp.codeEditor.defaultSettings ? _.clone( wp.codeEditor.defaultSettings ) : {};
			editorSettings.codemirror = _.extend(
				{},
				editorSettings.codemirror,
				{
					mode: 'javascript',
				}
			);
			var editor = wp.codeEditor.initialize( $( element ), editorSettings );
			editor.codemirror.on( 'change', confirmFormChange );
		}
	});

	$( ".codemirror_css" ).each( function( index, element ) {
		if( $( element ).length && typeof wp.codeEditor === 'object' ) {
			var editorSettings = wp.codeEditor.defaultSettings ? _.clone( wp.codeEditor.defaultSettings ) : {};
			editorSettings.codemirror = _.extend(
				{},
				editorSettings.codemirror,
				{
					mode: 'css',
				}
			);
			var editor = wp.codeEditor.initialize( $( element ), editorSettings );
			editor.codemirror.on( 'change', confirmFormChange );
		}
	});
});

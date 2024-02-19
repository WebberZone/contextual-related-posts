jQuery(document).ready(function($) {
	function confirmFormChange() {
		formmodified=1;
	}

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

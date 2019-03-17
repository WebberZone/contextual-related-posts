<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link  https://webberzone.com
 * @since 2.6.0
 *
 * @package    Contextual Related Posts
 * @subpackage Admin
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Creates the admin submenu pages under the Downloads menu and assigns their
 * links to global variables
 *
 * @since 2.6.0
 *
 * @global $crp_settings_page
 * @return void
 */
function crp_add_admin_pages_links() {
	global $crp_settings_page, $crp_settings_tools_help, $crp_settings_popular_posts, $crp_settings_popular_posts_daily;

	$crp_settings_page = add_menu_page( esc_html__( 'Contextual Related Posts Settings', 'contextual-related-posts' ), esc_html__( 'Contextual Related Posts', 'contextual-related-posts' ), 'manage_options', 'crp_options_page', 'crp_options_page', 'dashicons-editor-ol' );
	add_action( "load-$crp_settings_page", 'crp_settings_help' ); // Load the settings contextual help.
	add_action( "admin_head-$crp_settings_page", 'crp_adminhead' ); // Load the admin head.

	$plugin_page = add_submenu_page( 'crp_options_page', esc_html__( 'Contextual Related Posts Settings', 'contextual-related-posts' ), esc_html__( 'Settings', 'contextual-related-posts' ), 'manage_options', 'crp_options_page', 'crp_options_page' );
	add_action( 'admin_head-' . $plugin_page, 'crp_adminhead' );

	$crp_settings_tools_help = add_submenu_page( 'crp_options_page', esc_html__( 'Contextual Related Posts Tools', 'contextual-related-posts' ), esc_html__( 'Tools', 'contextual-related-posts' ), 'manage_options', 'crp_tools_page', 'crp_tools_page' );
	add_action( "load-$crp_settings_tools_help", 'crp_settings_tools_help' );
	add_action( 'admin_head-' . $crp_settings_tools_help, 'crp_adminhead' );

	// Initialise Contextual Related Posts Statistics pages.
	$crp_stats_screen = new Top_Ten_Statistics();

	$crp_settings_popular_posts = add_submenu_page( 'crp_options_page', __( 'Contextual Related Posts', 'contextual-related-posts' ), __( 'related posts', 'contextual-related-posts' ), 'manage_options', 'crp_popular_posts', array( $crp_stats_screen, 'plugin_settings_page' ) );
	add_action( "load-$crp_settings_popular_posts", array( $crp_stats_screen, 'screen_option' ) );
	add_action( 'admin_head-' . $crp_settings_popular_posts, 'crp_adminhead' );

	$crp_settings_popular_posts_daily = add_submenu_page( 'crp_options_page', __( 'Contextual Related Posts Daily related posts', 'contextual-related-posts' ), __( 'Daily related posts', 'contextual-related-posts' ), 'manage_options', 'crp_popular_posts&orderby=daily_count&order=desc', array( $crp_stats_screen, 'plugin_settings_page' ) );
	add_action( "load-$crp_settings_popular_posts_daily", array( $crp_stats_screen, 'screen_option' ) );
	add_action( 'admin_head-' . $crp_settings_popular_posts_daily, 'crp_adminhead' );

}
add_action( 'admin_menu', 'crp_add_admin_pages_links' );


/**
 * Function to add CSS and JS to the Admin header.
 *
 * @since 2.6.0
 * @return void
 */
function crp_adminhead() {
	global $crp_settings_page, $crp_settings_tools_help, $crp_settings_popular_posts, $crp_settings_popular_posts_daily;

	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui-autocomplete' );
	wp_enqueue_script( 'jquery-ui-tabs' );
	wp_enqueue_script( 'plugin-install' );
	wp_enqueue_script( 'jquery-ui-datepicker' );
	add_thickbox();

	$screen = get_current_screen();

	if ( $screen->id === $crp_settings_popular_posts || $screen->id === $crp_settings_popular_posts_daily ) {
		wp_enqueue_style(
			'crp-admin-ui-css',
			plugins_url( 'includes/admin/css/contextual-related-posts-admin.min.css', CRP_PLUGIN_FILE ),
			false,
			'1.0',
			false
		);
	}
	?>
	<script type="text/javascript">
	//<![CDATA[
		// Function to clear the cache.
		function clearCache() {
			/**** since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php ****/
			jQuery.post(ajaxurl, {
				action: 'crp_clear_cache'
			}, function (response, textStatus, jqXHR) {
				alert(response.message);
			}, 'json');
		}

		// Function to add auto suggest.
		jQuery(document).ready(function($) {
			$.fn.crpTagsSuggest = function( options ) {

				var cache;
				var last;
				var $element = $( this );

				var taxonomy = $element.attr( 'data-wp-taxonomy' ) || 'category';

				function split( val ) {
					return val.split( /,\s*/ );
				}

				function extractLast( term ) {
					return split( term ).pop();
				}

				$element.on( "keydown", function( event ) {
						// Don't navigate away from the field on tab when selecting an item.
						if ( event.keyCode === $.ui.keyCode.TAB &&
						$( this ).autocomplete( 'instance' ).menu.active ) {
							event.preventDefault();
						}
					})
					.autocomplete({
						minLength: 2,
						source: function( request, response ) {
							var term;

							if ( last === request.term ) {
								response( cache );
								return;
							}

							term = extractLast( request.term );

							if ( last === request.term ) {
								response( cache );
								return;
							}

							$.ajax({
								type: 'POST',
								dataType: 'json',
								url: '<?php echo admin_url( 'admin-ajax.php' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>',
								data: {
									action: 'crp_tag_search',
									tax: taxonomy,
									q: term
								},
								success: function( data ) {
									cache = data;

									response( data );
								}
							});

							last = request.term;

						},
						search: function() {
							// Custom minLength.
							var term = extractLast( this.value );

							if ( term.length < 2 ) {
								return false;
							}
						},
						focus: function( event, ui ) {
							// Prevent value inserted on focus.
							event.preventDefault();
						},
						select: function( event, ui ) {
							var terms = split( this.value );

							// Remove the last user input.
							terms.pop();

							// Add the selected item.
							terms.push( ui.item.value );

							// Add placeholder to get the comma-and-space at the end.
							terms.push( "" );
							this.value = terms.join( ", " );
							return false;
						}
					});

			};

			$( '.category_autocomplete' ).each( function ( i, element ) {
				$( element ).crpTagsSuggest();
			});

			// Prompt the user when they leave the page without saving the form.
			formmodified=0;

			$('form *').change(function(){
				formmodified=1;
			});

			window.onbeforeunload = confirmExit;

			function confirmExit() {
				if (formmodified == 1) {
					return "<?php esc_html__( 'New information not saved. Do you wish to leave the page?', 'contextual-related-posts' ); ?>";
				}
			}

			$( "input[name='submit']" ).click( function() {
				formmodified = 0;
			});

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

			$( function() {
				var dateFormat = 'dd M yy',
				from = $( "#datepicker-from" )
					.datepicker({
						changeMonth: true,
						maxDate: 0,
						dateFormat: dateFormat
					})
					.on( "change", function() {
						to.datepicker( "option", "minDate", getDate( this ) );
					}),
				to = $( "#datepicker-to" )
					.datepicker({
						changeMonth: true,
						maxDate: 0,
						dateFormat: dateFormat
					})
					.on( "change", function() {
						from.datepicker( "option", "maxDate", getDate( this ) );
					});

				function getDate( element ) {
					var date;
					try {
						date = $.datepicker.parseDate( dateFormat, element.value );
					} catch( error ) {
						date = null;
					}

					return date;
				}
			} );



		});

	//]]>
	</script>
	<?php
}


/**
 * Customise the taxonomy columns.
 *
 * @since 2.6.0
 * @param  array $columns Columns in the admin view.
 * @return array Updated columns.
 */
function crp_tax_columns( $columns ) {

	// Remove the description column.
	unset( $columns['description'] );

	$new_columns = array(
		'tax_id' => 'ID',
	);

	return array_merge( $columns, $new_columns );
}
add_filter( 'manage_edit-crp_category_columns', 'crp_tax_columns' );
add_filter( 'manage_edit-crp_category_sortable_columns', 'crp_tax_columns' );
add_filter( 'manage_edit-crp_tag_columns', 'crp_tax_columns' );
add_filter( 'manage_edit-crp_tag_sortable_columns', 'crp_tax_columns' );


/**
 * Add taxonomy ID to the admin column.
 *
 * @since 2.6.0
 *
 * @param  string     $value Deprecated.
 * @param  string     $name  Name of the column.
 * @param  int|string $id    Category ID.
 * @return int|string
 */
function crp_tax_id( $value, $name, $id ) {
	return 'tax_id' === $name ? $id : $value;
}
add_filter( 'manage_crp_category_custom_column', 'crp_tax_id', 10, 3 );
add_filter( 'manage_crp_tag_custom_column', 'crp_tax_id', 10, 3 );


/**
 * Add rating links to the admin dashboard
 *
 * @since 2.6.0
 *
 * @param string $footer_text The existing footer text.
 * @return string Updated Footer text
 */
function crp_admin_footer( $footer_text ) {

	if ( get_current_screen()->parent_base === 'crp_options_page' ) {

		$text = sprintf(
			/* translators: 1: Contextual Related Posts website, 2: Plugin reviews link. */
			__( 'Thank you for using <a href="%1$s" target="_blank">Contextual Related Posts</a>! Please <a href="%2$s" target="_blank">rate us</a> on <a href="%2$s" target="_blank">WordPress.org</a>', 'contextual-related-posts' ),
			'https://webberzone.com/contextual-related-posts',
			'https://wordpress.org/support/plugin/contextual-related-posts/reviews/#new-post'
		);

		return str_replace( '</span>', '', $footer_text ) . ' | ' . $text . '</span>';

	} else {

		return $footer_text;

	}
}
add_filter( 'admin_footer_text', 'crp_admin_footer' );


/**
 * Add CSS to Admin head
 *
 * @since 2.6.0
 *
 * return void
 */
function crp_admin_head() {
	?>
	<style type="text/css" media="screen">
		#dashboard_right_now .crp-article-count:before {
			content: "\f331";
		}
	</style>
	<?php
}
add_filter( 'admin_head', 'crp_admin_head' );

/**
 * Adding WordPress plugin action links.
 *
 * @version 1.9.2
 *
 * @param   array $links Action links.
 * @return  array   Links array with our settings link added.
 */
function crp_plugin_actions_links( $links ) {

	return array_merge(
		array(
			'settings' => '<a href="' . admin_url( 'options-general.php?page=crp_options_page' ) . '">' . __( 'Settings', 'contextual-related-posts' ) . '</a>',
		),
		$links
	);

}
add_filter( 'plugin_action_links_' . plugin_basename( CRP_PLUGIN_FILE ), 'crp_plugin_actions_links' );


/**
 * Add links to the plugin action row.
 *
 * @since 2.6.0
 *
 * @param   array $links Action links.
 * @param   array $file Plugin file name.
 * @return  array   Links array with our links added
 */
function crp_plugin_actions( $links, $file ) {
	$plugin = plugin_basename( CRP_PLUGIN_FILE );

	if ( $file === $plugin ) {
		$links[] = '<a href="https://wordpress.org/support/plugin/contextual-related-posts/">' . __( 'Support', 'contextual-related-posts' ) . '</a>';
		$links[] = '<a href="https://ajaydsouza.com/donate/">' . __( 'Donate', 'contextual-related-posts' ) . '</a>';
		$links[] = '<a href="https://github.com/WebberZone/contextual-related-posts">' . __( 'Contribute', 'contextual-related-posts' ) . '</a>';
	}
	return $links;
}
add_filter( 'plugin_row_meta', 'crp_plugin_actions', 10, 2 );



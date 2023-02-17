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
 * @global $crp_settings_page, $crp_settings_tools
 * @return void
 */
function crp_add_admin_pages_links() {
	global $crp_settings_page, $crp_settings_tools;

	$crp_settings_page = add_options_page(
		esc_html__( 'Contextual Related Posts', 'contextual-related-posts' ),
		esc_html__( 'Related Posts', 'contextual-related-posts' ),
		'manage_options',
		'crp_options_page',
		'crp_options_page'
	);
	add_action( "load-$crp_settings_page", 'crp_settings_help' ); // Load the settings contextual help.

	$crp_settings_tools = add_management_page(
		esc_html__( 'Contextual Related Posts Tools', 'contextual-related-posts' ),
		esc_html__( 'Related Posts Tools', 'contextual-related-posts' ),
		'manage_options',
		'crp_tools_page',
		'crp_tools_page'
	);
	add_action( "load-$crp_settings_tools", 'crp_settings_help' );

}
add_action( 'admin_menu', 'crp_add_admin_pages_links', 99 );


/**
 * Add rating links to the admin dashboard
 *
 * @since 2.6.0
 *
 * @param string $footer_text The existing footer text.
 * @return string Updated Footer text
 */
function crp_admin_footer( $footer_text ) {
	global $crp_settings_page, $crp_settings_tools;

	$current_screen = get_current_screen();

	if ( $current_screen->id === $crp_settings_page || $current_screen->id === $crp_settings_tools ) {

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
 * Enqueue Admin JS
 *
 * @since 2.9.0
 *
 * @param string $hook The current admin page.
 */
function crp_load_admin_scripts( $hook ) {

	global $crp_settings_page, $crp_settings_tools;

	wp_register_script(
		'crp-admin-js',
		CRP_PLUGIN_URL . 'includes/admin/js/admin-scripts.min.js',
		array( 'jquery', 'jquery-ui-tabs', 'jquery-ui-datepicker' ),
		CRP_VERSION,
		true
	);
	wp_register_script(
		'crp-suggest-js',
		CRP_PLUGIN_URL . 'includes/admin/js/crp-suggest.min.js',
		array( 'jquery', 'jquery-ui-autocomplete' ),
		CRP_VERSION,
		true
	);

	wp_register_style(
		'crp-admin-customizer-css',
		CRP_PLUGIN_URL . 'includes/admin/css/crp-customizer.min.css',
		false,
		CRP_VERSION
	);

	if ( in_array( $hook, array( $crp_settings_page, $crp_settings_tools ), true ) ) {

		wp_enqueue_script( 'crp-admin-js' );
		wp_enqueue_script( 'crp-suggest-js' );
		wp_enqueue_script( 'plugin-install' );
		add_thickbox();

		wp_enqueue_code_editor(
			array(
				'type'       => 'text/html',
				'codemirror' => array(
					'indentUnit' => 2,
					'tabSize'    => 2,
				),
			)
		);
		wp_localize_script(
			'crp-admin-js',
			'crp_admin_data',
			array(
				'security' => wp_create_nonce( 'crp-admin' ),
			)
		);

	}
}
add_action( 'admin_enqueue_scripts', 'crp_load_admin_scripts' );


/**
 * This function enqueues scripts and styles in the Customizer.
 *
 * @since 2.9.0
 */
function crp_customize_controls_enqueue_scripts() {
	wp_enqueue_script( 'customize-controls' );
	wp_enqueue_script( 'crp-suggest-js' );

	wp_enqueue_style( 'crp-admin-customizer-css' );

}
add_action( 'customize_controls_enqueue_scripts', 'crp_customize_controls_enqueue_scripts', 99 );


/**
 * This function enqueues scripts and styles on widgets.php.
 *
 * @since 2.9.0
 *
 * @param string $hook The current admin page.
 */
function crp_enqueue_scripts_widgets( $hook ) {
	if ( 'widgets.php' !== $hook ) {
		return;
	}
	wp_enqueue_script( 'crp-suggest-js' );
	wp_enqueue_style( 'crp-admin-customizer-css' );
}
add_action( 'admin_enqueue_scripts', 'crp_enqueue_scripts_widgets', 99 );

/**
 * Adds minor CSS styles to the admin menu.
 *
 * @since 3.1.1
 */
function crp_admin_css() {

	if ( ! is_customize_preview() ) {
		$css = '
			<style type="text/css">
				a.crp_button {
					background: green;
					padding: 10px;
					color: white;
					text-decoration: none;
					text-shadow: none;
					border-radius: 3px;
					transition: all 0.3s ease 0s;
					border: 1px solid green;
				}
				a.crp_button:hover {
					box-shadow: 3px 3px 10px #666;
				}
			</style>';

		echo $css; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
add_action( 'admin_head', 'crp_admin_css' );

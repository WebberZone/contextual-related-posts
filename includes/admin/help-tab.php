<?php
/**
 * Help tab.
 *
 * Functions to generated the help tab on the Settings page.
 *
 * @link  https://webberzone.com
 * @since 2.6.0
 *
 * @package Contextual_Related_Posts
 * @subpackage Admin/Help
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Generates the settings help page.
 *
 * @since 2.6.0
 */
function crp_settings_help() {
	$screen = get_current_screen();

	$screen->set_help_sidebar(
		/* translators: 1: Support link. */
		'<p>' . sprintf( __( 'For more information or how to get support visit the <a href="%1$s" target="_blank">WebberZone support site</a>.', 'contextual-related-posts' ), esc_url( 'https://webberzone.com/support/' ) ) . '</p>' .
		/* translators: 1: Forum link. */
		'<p>' . sprintf( __( 'Support queries should be posted in the <a href="%1$s" target="_blank">WordPress.org support forums</a>.', 'contextual-related-posts' ), esc_url( 'https://wordpress.org/support/plugin/contextual-related-posts' ) ) . '</p>' .
		'<p>' . sprintf(
			/* translators: 1: Github Issues link, 2: Github page. */
			__( '<a href="%1$s" target="_blank">Post an issue</a> on <a href="%2$s" target="_blank">GitHub</a> (bug reports only).', 'contextual-related-posts' ),
			esc_url( 'https://github.com/WebberZone/contextual-related-posts/issues' ),
			esc_url( 'https://github.com/WebberZone/contextual-related-posts' )
		) . '</p>'
	);

	$screen->add_help_tab(
		array(
			'id'      => 'crp-settings',
			'title'   => __( 'Settings', 'contextual-related-posts' ),
			'content' =>
			'<p>' . __( 'This screen provides the various settings for configuring Contextual Related Posts.', 'contextual-related-posts' ) . '</p>' .
			'<p>' . sprintf(
			/* translators: 1: Link to Knowledge Base article. */
				__( 'You can find detailed information on each of the settings in these <a href="%1$s" target="_blank">knowledgebase articles</a>.', 'contextual-related-posts' ),
				esc_url( 'https://webberzone.com/support/section/contextual-related-posts/01-crp-getting-started/' )
			) . '</p>',
		)
	);

	$screen->add_help_tab(
		array(
			'id'      => 'crp-settings-tools',
			'title'   => __( 'Tools', 'contextual-related-posts' ),
			'content' =>
			'<p>' . __( 'This screen provides some tools that help maintain certain features of Contextual Related Posts.', 'contextual-related-posts' ) . '</p>' .
				'<p>' . __( 'Clear the cache, recreate the fulltext indices (including code to manually run this in phpMyAdmin), export/import settings and delete the older settings.', 'contextual-related-posts' ) . '</p>' .
				'<p>' . sprintf(
				/* translators: 1: Link to Knowledge Base article. */
					__( 'You can find more information on each of these tools in this <a href="%1$s" target="_blank">knowledgebase article</a>.', 'contextual-related-posts' ),
					esc_url( 'https://webberzone.com/support/knowledgebase/contextual-related-posts-settings-tools/' )
				) . '</p>',
		)
	);

	do_action( 'crp_settings_help', $screen );

}


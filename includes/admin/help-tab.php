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
	global $crp_settings_page;

	$screen = get_current_screen();

	if ( $screen->id !== $crp_settings_page ) {
		return;
	}

	$screen->set_help_sidebar(
		/* translators: 1: Support link. */
		'<p>' . sprintf( __( 'For more information or how to get support visit the <a href="%1$s">WebberZone support site</a>.', 'contextual-related-posts' ), esc_url( 'https://webberzone.com/support/' ) ) . '</p>' .
		/* translators: 1: Forum link. */
		'<p>' . sprintf( __( 'Support queries should be posted in the <a href="%1$s">WordPress.org support forums</a>.', 'contextual-related-posts' ), esc_url( 'https://wordpress.org/support/plugin/contextual-related-posts' ) ) . '</p>' .
		'<p>' . sprintf(
			/* translators: 1: Github Issues link, 2: Github page. */
			__( '<a href="%1$s">Post an issue</a> on <a href="%2$s">GitHub</a> (bug reports only).', 'contextual-related-posts' ),
			esc_url( 'https://github.com/WebberZone/contextual-related-posts/issues' ),
			esc_url( 'https://github.com/WebberZone/contextual-related-posts' )
		) . '</p>'
	);

	$screen->add_help_tab(
		array(
			'id'      => 'crp-settings-general',
			'title'   => __( 'General', 'contextual-related-posts' ),
			'content' =>
			'<p>' . __( 'This screen provides the basic settings for configuring Contextual Related Posts.', 'contextual-related-posts' ) . '</p>' .
				'<p>' . __( 'Enable the trackers and cache, configure basic tracker settings and uninstall settings.', 'contextual-related-posts' ) . '</p>',
		)
	);

	$screen->add_help_tab(
		array(
			'id'      => 'crp-settings-counter',
			'title'   => __( 'Counter/Tracker', 'contextual-related-posts' ),
			'content' =>
			'<p>' . __( 'This screen provides settings to tweak the display counter and the tracker.', 'contextual-related-posts' ) . '</p>' .
				'<p>' . __( 'Choose where to display the counter and customize the text. Select the type of tracker and which user groups to track.', 'contextual-related-posts' ) . '</p>',
		)
	);

	$screen->add_help_tab(
		array(
			'id'      => 'crp-settings-list',
			'title'   => __( 'Posts list', 'contextual-related-posts' ),
			'content' =>
			'<p>' . __( 'This screen provides settings to tweak the output of the list of related posts.', 'contextual-related-posts' ) . '</p>' .
				'<p>' . __( 'Set the number of posts, which categories or posts to exclude, customize what to display and specific basic HTML markup used to create the posts.', 'contextual-related-posts' ) . '</p>',
		)
	);

	$screen->add_help_tab(
		array(
			'id'      => 'crp-settings-thumbnail',
			'title'   => __( 'Thumbnail', 'contextual-related-posts' ),
			'content' =>
			'<p>' . __( 'This screen provides settings to tweak the thumbnail that can be displayed for each post in the list.', 'contextual-related-posts' ) . '</p>' .
				'<p>' . __( 'Set the location and size of the thumbnail. Additionally, you can choose additional sources for the thumbnail i.e. a meta field, first image or a default thumbnail when nothing is available.', 'contextual-related-posts' ) . '</p>',
		)
	);

	$screen->add_help_tab(
		array(
			'id'      => 'crp-settings-styles',
			'title'   => __( 'Styles', 'contextual-related-posts' ),
			'content' =>
			'<p>' . __( 'This screen provides options to control the look and feel of the related posts list.', 'contextual-related-posts' ) . '</p>' .
				'<p>' . __( 'Choose for default set of styles or add your own custom CSS to tweak the display of the posts.', 'contextual-related-posts' ) . '</p>',
		)
	);

	$screen->add_help_tab(
		array(
			'id'      => 'crp-settings-maintenance',
			'title'   => __( 'Maintenance', 'contextual-related-posts' ),
			'content' =>
			'<p>' . __( 'This screen provides options to control the maintenance cron.', 'contextual-related-posts' ) . '</p>' .
				'<p>' . __( 'Choose how often to run maintenance and at what time of the day.', 'contextual-related-posts' ) . '</p>',
		)
	);

	do_action( 'crp_settings_help', $screen );

}

/**
 * Generates the Tools help page.
 *
 * @since 2.6.0
 */
function crp_settings_tools_help() {
	global $crp_settings_tools_help;

	$screen = get_current_screen();

	if ( $screen->id !== $crp_settings_tools_help ) {
		return;
	}

	$screen->set_help_sidebar(
		/* translators: 1: Support link. */
		'<p>' . sprintf( __( 'For more information or how to get support visit the <a href="%1$s">WebberZone support site</a>.', 'contextual-related-posts' ), esc_url( 'https://webberzone.com/support/' ) ) . '</p>' .
		/* translators: 1: Forum link. */
		'<p>' . sprintf( __( 'Support queries should be posted in the <a href="%1$s">WordPress.org support forums</a>.', 'contextual-related-posts' ), esc_url( 'https://wordpress.org/support/plugin/contextual-related-posts' ) ) . '</p>' .
		'<p>' . sprintf(
			/* translators: 1: Github Issues link, 2: Github page. */
			__( '<a href="%1$s">Post an issue</a> on <a href="%2$s">GitHub</a> (bug reports only).', 'contextual-related-posts' ),
			esc_url( 'https://github.com/WebberZone/contextual-related-posts/issues' ),
			esc_url( 'https://github.com/WebberZone/contextual-related-posts' )
		) . '</p>'
	);

	$screen->add_help_tab(
		array(
			'id'      => 'crp-settings-general',
			'title'   => __( 'General', 'contextual-related-posts' ),
			'content' =>
			'<p>' . __( 'This screen provides some tools that help maintain certain features of Contextual Related Posts.', 'contextual-related-posts' ) . '</p>' .
				'<p>' . __( 'Clear the cache, reset the related posts tables plus some miscellaneous fixes for older versions of Contextual Related Posts.', 'contextual-related-posts' ) . '</p>',
		)
	);

	do_action( 'crp_settings_tools_help', $screen );
}

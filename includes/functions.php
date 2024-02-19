<?php
/**
 * Template functions for use in the plugin and theme.
 *
 * @package   Contextual_Related_Posts
 */

use WebberZone\Contextual_Related_Posts\Frontend\Display;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Main function to generate the related posts output
 *
 * @since 1.0.1
 *
 * @param string|array $args Parameters in a query string format.
 * @return string HTML formatted list of related posts
 */
function get_crp( $args = array() ) {
	return Display::related_posts( $args );
}

/**
 * Echos the related posts. Used for manual install
 *
 * @since 1.0.1
 *
 * @param array $args Array of arguments to control the output.
 */
function echo_crp( $args = array() ) {

	$defaults = array(
		'is_manual' => true,
	);

	// Parse incomming $args into an array and merge it with $defaults.
	$args = wp_parse_args( $args, $defaults );

	echo get_crp( $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Retrieves an array of the related posts.
 *
 * The defaults are as follows:
 *
 * @since 1.8.6
 * @since 3.0.0 Parameters have been dropped for a single $args parameter.
 *
 * @see CRP_Query::prepare_query_args()
 *
 * @param array $args Optional. Arguments to retrieve posts. See WP_Query::parse_query() for all available arguments.
 * @return WP_Post[]|int[] Array of post objects or post IDs.
 */
function get_crp_posts( $args = array() ) {
	return Display::get_posts( $args );
}

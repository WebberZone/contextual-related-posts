<?php
/**
 * Contextual Related Posts Cache interface.
 *
 * @package   Contextual_Related_Posts
 * @author    Ajay D'Souza <me@ajaydsouza.com>
 * @license   GPL-2.0+
 * @link      https://webberzone.com
 * @copyright 2009-2015 Ajay D'Souza
 */

/**
 * Function to clear the CRP Cache with Ajax.
 *
 * @since	1.8.10
 */
function crp_ajax_clearcache() {

	global $wpdb;

	$meta_keys = crp_cache_get_keys();
	$error = false;

	foreach ( $meta_keys as $meta_key ) {

		$count = $wpdb->query( $wpdb->prepare( "
			DELETE FROM {$wpdb->postmeta}
			WHERE meta_key = %s
		", $meta_key ) );

		if ( false === $count ) {
			$error = true;
		} else {
			$counter[] = $count;
		}
	}

	/**** Did an error occur? ****/
	if ( $error ) {
		exit( wp_json_encode( array(
			'success' => 0,
			'message' => __( 'An error occurred clearing the cache. Please contact your site administrator.\n\nError message:\n', 'contextual-related-posts' ) . $wpdb->print_error(),
		) ) );
	} else {	// No error, return the number of.
		exit( wp_json_encode( array(
			'success' => 1,
			'message' => ( array_sum( $counter ) ) . __( ' cached row(s) cleared', 'contextual-related-posts' ),
		) ) );
	}
}
add_action( 'wp_ajax_crp_clear_cache', 'crp_ajax_clearcache' );



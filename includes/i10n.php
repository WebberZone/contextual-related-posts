<?php
/**
 * Language functions
 *
 * @package Contextual_Related_Posts
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Initialises text domain for l10n.
 *
 * @since   2.2.0
 */
function crp_lang_init() {
	load_plugin_textdomain( 'contextual-related-posts', false, dirname( plugin_basename( CRP_PLUGIN_FILE ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'crp_lang_init' );

/**
 * Get the ID of a post in the current language. Works with WPML and PolyLang.
 *
 * @since 3.0.0
 *
 * @param array $results Arry of Posts.
 * @return array Updated array of WP_Post objects.
 */
function crp_translate_ids( $results ) {
	global $post;

	$processed_ids     = array();
	$processed_results = array();

	foreach ( $results as $result ) {

		$resultid = crp_object_id_cur_lang( $result->ID );

		// If this is NULL or already processed ID or matches current post then skip processing this loop.
		if ( ! $resultid || in_array( $resultid, $processed_ids ) || intval( $resultid ) === intval( $post->ID ) ) { // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
			continue;
		}

		// Push the current ID into the array to ensure we're not repeating it.
		array_push( $processed_ids, $resultid );

		$result = get_post( $resultid );    // Let's get the Post using the ID.
		array_push( $processed_results, $result );
	}
	return $processed_results;
}
add_filter( 'get_crp_posts_id', 'crp_translate_ids', 999 );


/**
 * Returns the object identifier for the current language (WPML).
 *
 * @since   2.1.0
 *
 * @param int|string $post_id Post ID.
 */
function crp_object_id_cur_lang( $post_id ) {

	$return_original_if_missing = false;

	$post         = get_post( $post_id );
	$current_lang = apply_filters( 'wpml_current_language', null );

	/**
	 * Filter to modify if the original language ID is returned.
	 *
	 * @since   2.2.3
	 *
	 * @param   bool    $return_original_if_missing
	 * @param   int $post_id    Post ID
	 */
	$return_original_if_missing = apply_filters( 'crp_wpml_return_original', $return_original_if_missing, $post_id );

	// Polylang implementation.
	if ( function_exists( 'pll_get_post' ) ) {
		$post_id = pll_get_post( $post_id );
	}

	// WPML implementation.
	$post_id = apply_filters( 'wpml_object_id', $post_id, $post->post_type, $return_original_if_missing, $current_lang );

	/**
	 * Filters object ID for current language (WPML).
	 *
	 * @since   2.1.0
	 *
	 * @param   int $post_id    Post ID
	 */
	return apply_filters( 'crp_object_id_cur_lang', $post_id );
}


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
 * @since 2.2.0
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
 * @param array $results Array of Posts.
 * @return array Updated array of WP_Post objects.
 */
function crp_translate_ids( $results ) {

	$processed_ids     = array();
	$processed_results = array();

	foreach ( $results as $result ) {

		$result = crp_object_id_cur_lang( $result );

		// If this is NULL or already processed ID then skip processing this loop.
		if ( ! $result->ID || in_array( $result->ID, $processed_ids ) ) { // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
			continue;
		}

		// Push the current ID into the array to ensure we're not repeating it.
		array_push( $processed_ids, $result->ID );

		$result = get_post( $result );    // Let's get the Post using the ID.
		array_push( $processed_results, $result );
	}
	return $processed_results;
}
add_filter( 'get_crp_posts_id', 'crp_translate_ids', 999 );
add_filter( 'crp_query_the_posts', 'crp_translate_ids', 999 );


/**
 * Returns the object identifier for the current language (WPML).
 *
 * @since 2.1.0
 * @since 3.0.0 Parameter can be a WP_Post object. Return is a WP_Post object.
 *
 * @param WP_Post|int|string $post Post object or Post ID.
 * @return WP_Post Post opbject, updated if needed.
 */
function crp_object_id_cur_lang( $post ) {

	$return_original_if_missing = false;

	$post         = get_post( $post );
	$current_lang = apply_filters( 'wpml_current_language', null );

	// Polylang implementation.
	if ( function_exists( 'pll_get_post' ) ) {
		$post = pll_get_post( $post->ID );
		$post = get_post( $post );
	}

	// WPML implementation.
	if ( class_exists( 'SitePress' ) ) {
		/**
		 * Filter to modify if the original language ID is returned.
		 *
		 * @since   2.2.3
		 *
		 * @param bool $return_original_if_missing Flag to return original post ID if translated post ID is missing.
		 * @param int  $id                         Post ID
		 */
		$return_original_if_missing = apply_filters( 'crp_wpml_return_original', $return_original_if_missing, $post->ID );

		$post = apply_filters( 'wpml_object_id', $post->ID, $post->post_type, $return_original_if_missing, $current_lang );
		$post = get_post( $post );
	}

	/**
	 * Filters post object for current language.
	 *
	 * @since 2.1.0
	 *
	 * @param WP_Post $id Post object.
	 */
	return apply_filters( 'crp_object_id_cur_lang', $post );
}


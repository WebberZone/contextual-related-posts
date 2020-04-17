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


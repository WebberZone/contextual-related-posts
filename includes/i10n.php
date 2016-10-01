<?php
/**
 * Language functions
 *
 * @package Contextual_Related_Posts
 */

/**
 * Initialises text domain for l10n.
 *
 * @since	2.2.0
 */
function crp_lang_init() {
	load_plugin_textdomain( 'contextual-related-posts', false, dirname( plugin_basename( CRP_PLUGIN_FILE ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'crp_lang_init' );


/**
 * Returns the object identifier for the current language (WPML).
 *
 * @since	2.1.0
 *
 * @param int|string $post_id Post ID.
 */
function crp_object_id_cur_lang( $post_id ) {

	$return_original_if_missing = true;

	/**
	 * Filter to modify if the original language ID is returned.
	 *
	 * @since	2.2.3
	 *
	 * @param	bool	$return_original_if_missing
	 * @param	int	$post_id	Post ID
	 */
	$return_original_if_missing = apply_filters( 'crp_wpml_return_original', $return_original_if_missing, $post_id );

	if ( function_exists( 'pll_get_post' ) ) {
		$post_id = pll_get_post( $post_id );
	} elseif ( function_exists( 'wpml_object_id_filter' ) ) {
		$post_id = wpml_object_id_filter( $post_id, 'any', $return_original_if_missing );
	} elseif ( function_exists( 'icl_object_id' ) ) {
		$post_id = icl_object_id( $post_id, 'any', $return_original_if_missing );
	}

	/**
	 * Filters object ID for current language (WPML).
	 *
	 * @since	2.1.0
	 *
	 * @param	int	$post_id	Post ID
	 */
	return apply_filters( 'crp_object_id_cur_lang', $post_id );
}


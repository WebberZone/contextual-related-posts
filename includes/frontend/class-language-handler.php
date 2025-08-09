<?php
/**
 * Language handler
 *
 * @package Contextual_Related_Posts
 */

namespace WebberZone\Contextual_Related_Posts\Frontend;

use WebberZone\Contextual_Related_Posts\Util\Hook_Registry;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Language handler class.
 *
 * @since 3.5.0
 */
class Language_Handler {

	/**
	 * Constructor.
	 *
	 * @since 3.5.0
	 */
	public function __construct() {
		Hook_Registry::add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		Hook_Registry::add_filter( 'crp_query_the_posts', array( $this, 'translate_ids' ), 999 );
	}

	/**
	 * Initialises text domain for l10n.
	 *
	 * @since 3.5.0
	 *
	 * @return void
	 */
	public static function load_plugin_textdomain() {
		load_plugin_textdomain( 'contextual-related-posts', false, dirname( plugin_basename( WZ_CRP_PLUGIN_FILE ) ) . '/languages/' );
	}

	/**
	 * Get the ID of a post in the current language. Works with WPML and PolyLang.
	 *
	 * @since 3.5.0
	 *
	 * @param int[] $results Arry of Posts.
	 * @return \WP_Post[] Updated array of WP_Post objects.
	 */
	public static function translate_ids( $results ) {
		global $post;

		$processed_ids     = ! empty( $post->ID ) ? array( $post->ID ) : array();
		$processed_results = array();

		foreach ( $results as $result ) {

			$result = self::object_id_cur_lang( $result );
			if ( ! $result ) {
				continue;
			}

			// If this is NULL or already processed ID or matches current post then skip processing this loop.
			if ( ! $result->ID || in_array( $result->ID, $processed_ids ) ) { // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
				continue;
			}

			// Push the current ID into the array to ensure we're not repeating it.
			array_push( $processed_ids, $result->ID );

			// Let's get the Post using the ID.
			$result = get_post( $result );
			array_push( $processed_results, $result );
		}
		return $processed_results;
	}

	/**
	 * Returns the object identifier for the current language (WPML).
	 *
	 * @since 3.5.0
	 *
	 * @param int|string|\WP_Post $post Post object or Post ID.
	 * @return \WP_Post|array|null Post opbject, updated if needed.
	 */
	public static function object_id_cur_lang( $post ) {

		$return_original_if_missing = false;

		$post         = get_post( $post );
		$current_lang = apply_filters( 'wpml_current_language', null );

		// Polylang implementation.
		if ( function_exists( 'pll_get_post' ) ) {
			$post = \pll_get_post( $post->ID );
			$post = get_post( $post );
		}

		// WPML implementation.
		if ( class_exists( 'SitePress' ) ) {
			/**
			 * Filter to modify if the original language ID is returned.
			 *
			 * @since 2.2.3
			 *
			 * @param bool $return_original_if_missing Flag to return original post ID if translated post ID is missing.
			 * @param int  $id                         Post ID
			 */
			$return_original_if_missing = apply_filters( 'crp_wpml_return_original', $return_original_if_missing, $post->ID );

			$post = apply_filters( 'wpml_object_id', $post->ID, $post->post_type, $return_original_if_missing, $current_lang );
			$post = get_post( $post );
		}

		/**
		 * Filters Post object for current language.
		 *
		 * @since 2.1.0
		 *
		 * @param \WP_Post|array|null $id Post object.
		 */
		return apply_filters( 'crp_object_id_cur_lang', $post );
	}
}

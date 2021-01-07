<?php
/**
 * Manual posts module
 *
 * @package   Contextual_Related_Posts
 * @subpackage  Manual_Posts
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Checks if a list of manual posts exists and adds them before the other posts.
 *
 * @since   2.2.0
 *
 * @param   object $results    Original object array with post results.
 * @return  object  Updated object array with post results
 */
function crp_manual_posts( $results ) {
	global $post, $wpdb;

	$crp_post_meta = get_post_meta( $post->ID, 'crp_post_meta', true );

	if ( isset( $crp_post_meta['manual_related'] ) && ( '' != $crp_post_meta['manual_related'] ) ) { // phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison

		// Fields to return.
		$fields = " $wpdb->posts.ID ";

		/**
		 * Defined in contextual-related-posts.php
		 */
		$fields = apply_filters( 'crp_posts_fields', $fields, $post->ID );

		$sql = "SELECT DISTINCT $fields FROM $wpdb->posts
				WHERE 1=1
				AND {$wpdb->posts}.ID IN ({$crp_post_meta['manual_related']})
				ORDER BY FIELD({$wpdb->posts}.ID,{$crp_post_meta['manual_related']})";

		$results1 = $wpdb->get_results( $sql ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared

		$results = (object) array_merge( (array) $results1, (array) $results );

	}

	return apply_filters( 'crp_manual_posts', $results );
}
add_filter( 'get_crp_posts_id', 'crp_manual_posts' );



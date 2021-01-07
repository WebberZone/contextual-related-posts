<?php
/**
 * Taxonomies control module
 *
 * @package   Contextual_Related_Posts
 * @subpackage  Manual_Posts
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Filter JOIN clause of CRP query to add taxonomy tables.
 *
 * @since 1.0.0
 *
 * @param   mixed $join JOIN clause.
 * @return  string  Filtered CRP JOIN clause
 */
function crp_exclude_categories_join( $join ) {
	global $wpdb, $crp_settings;

	if ( ! empty( $crp_settings['exclude_categories'] ) ) {

		$sql  = $join;
		$sql .= " INNER JOIN $wpdb->term_relationships AS excat_tr ON ($wpdb->posts.ID = excat_tr.object_id) ";
		$sql .= " INNER JOIN $wpdb->term_taxonomy AS excat_tt ON (excat_tr.term_taxonomy_id = excat_tt.term_taxonomy_id) ";

		return $sql;
	}

	return $join;
}
add_filter( 'crp_posts_join', 'crp_exclude_categories_join' );

/**
 * Filter WHERE clause of CRP query to exclude posts belonging to certain categories.
 *
 * @since 1.0.0
 *
 * @param   mixed $where WHERE clause.
 * @return  string  Filtered CRP WHERE clause
 */
function crp_exclude_categories_where( $where ) {
	global $wpdb, $crp_settings;

	if ( ! empty( $crp_settings['exclude_categories'] ) ) {

		$terms = $crp_settings['exclude_categories'];

		$sql = $where;

		$sql .= " AND $wpdb->posts.ID NOT IN (
			SELECT object_id
			FROM $wpdb->term_relationships
			WHERE term_taxonomy_id IN ($terms)
		)";

		return $sql;
	}

	return $where;
}
add_filter( 'crp_posts_where', 'crp_exclude_categories_where' );



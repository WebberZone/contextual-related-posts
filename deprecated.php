<?php
/**
 * Deprecated functions from Contextual Related Posts. You shouldn't
 * use these functions and look for the alternatives instead. The functions will be
 * removed in a later version.
 *
 * @package Contextual_Related_Posts
 */


/**
 * Fetch related posts.
 *
 * @since 1.8.6
 *
 * @deprecated v2.0.0
 * @see	get_crp_posts_id
 *
 * @param int $postid (default: FALSE) The post ID for which you want the posts for
 * @param int $limit (default: FALSE) Maximum posts to retreive
 * @param boolean $strict_limit (default: TRUE) Setting to true will fetch exactly as per limit above
 * @return object Object with Post IDs
 */
function get_crp_posts( $postid = FALSE, $limit = FALSE, $strict_limit = TRUE ) {

	_deprecated_function( 'get_crp_posts', '2.0.0', 'get_crp_posts_id' );

	$results = get_crp_posts_id( array(
		'postid' => $postid,
		'limit' => $limit,
		'strict_limit' => $strict_limit
	) );

	/**
	 * Filter object containing the post IDs.
	 *
	 * @since	1.9
	 *
	 * @param	object   $results  Object containing the related post IDs
	 */
	return apply_filters( 'get_crp_posts', $results );
}


?>
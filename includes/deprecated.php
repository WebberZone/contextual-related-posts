<?php
/**
 * Deprecated functions from Contextual Related Posts. You shouldn't
 * use these functions and look for the alternatives instead. The functions will be
 * removed in a later version.
 *
 * @package Contextual_Related_Posts
 */

/**
 * Holds the URL for CRP
 *
 * @since	1.2
 * @deprecated 2.3.0
 *
 * @var string
 */
$crp_url = plugins_url() . '/' . plugin_basename( dirname( __FILE__ ) );

/**
 * Fetch related posts.
 *
 * @since 1.8.6
 *
 * @deprecated v2.0.0
 * @see	get_crp_posts_id
 *
 * @param int     $postid (default: FALSE) The post ID for which you want the posts for.
 * @param int     $limit (default: FALSE) Maximum posts to retreive.
 * @param boolean $strict_limit (default: TRUE) Setting to true will fetch exactly as per limit above.
 * @return object Object with Post IDs
 */
function get_crp_posts( $postid = false, $limit = false, $strict_limit = true ) {

	_deprecated_function( __FUNCTION__, '2.0.0', 'get_crp_posts_id()' );

	$results = get_crp_posts_id( array(
		'postid' => $postid,
		'limit' => $limit,
		'strict_limit' => $strict_limit,
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


/**
 * Main function to generate the related posts output
 *
 * @since 1.0.1
 * @deprecated	2.2.0
 * @see	get_crp
 *
 * @param	array $args   Parameters in a query string format.
 * @return	string			HTML formatted list of related posts
 */
function ald_crp( $args = array() ) {

	_deprecated_function( __FUNCTION__, '2.2.0', 'get_crp()' );

	$output = get_crp( $args );

	/**
	 * Filter the output
	 *
	 * @since	1.9.1
	 *
	 * @param	string	$output	Formatted list of related posts
	 * @param	array	$args	Complete set of arguments
	 */
	return apply_filters( 'ald_crp', $output, $args );
}


/**
 * Filter for 'the_content' to add the related posts.
 *
 * @since 1.0.1
 * @deprecated	2.2.0
 * @see	crp_content_filter
 *
 * @param string $content Post content.
 * @return string After the filter has been processed
 */
function ald_crp_content( $content ) {

	_deprecated_function( __FUNCTION__, '2.2.0', 'crp_content_filter()' );

	return crp_content_filter( $content );
}


/**
 * Filter to add related posts to feeds.
 *
 * @since 1.8.4
 * @deprecated	2.2.0
 * @see	crp_rss_filter
 *
 * @param	string $content Post content.
 * @return	string	Formatted content
 */
function ald_crp_rss( $content ) {

	_deprecated_function( __FUNCTION__, '2.2.0', 'crp_rss_filter()' );

	return crp_content_filter( $content );
}


/**
 * Manual install of the related posts.
 *
 * @since 1.0.1
 * @deprecated	2.2.0
 * @see	echo_crp
 *
 * @param string $args Array of arguments.
 */
function echo_ald_crp( $args = array() ) {

	_deprecated_function( __FUNCTION__, '2.2.0', 'echo_crp()' );

	echo_crp( $args );
}


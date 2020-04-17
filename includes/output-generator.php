<?php
/**
 * Generates the output
 *
 * @package   Contextual_Related_Posts
 * @author    Ajay D'Souza
 * @license   GPL-2.0+
 * @link      https://webberzone.com
 * @copyright 2009-2020 Ajay D'Souza
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Returns the link attributes.
 *
 * @since   2.2.0
 *
 * @param   array $args   Array of arguments.
 * @return  string  Space separated list of link attributes.
 */
function crp_link_attributes( $args ) {

	$rel_attribute    = ( $args['link_nofollow'] ) ? ' rel="nofollow" ' : ' ';
	$target_attribute = ( $args['link_new_window'] ) ? ' target="_blank" ' : ' ';

	$link_attributes = array(
		'rel_attribute'    => $rel_attribute,
		'target_attribute' => $target_attribute,
	);

	/**
	 * Filter the title of the Related Posts list
	 *
	 * @since   2.2.0
	 *
	 * @param   array   $link_attributes    Array of link attributes
	 * @param   array   $args   Array of arguments
	 */
	$link_attributes = apply_filters( 'crp_link_attributes', $link_attributes, $args );

	// Convert it to a string.
	$link_attributes = implode( ' ', $link_attributes );

	return $link_attributes;

}


/**
 * Returns the heading of the related posts.
 *
 * @since   2.2.0
 *
 * @param   array $args   Array of arguments.
 * @return  string  Space separated list of link attributes
 */
function crp_heading_title( $args ) {
	global $post;

	$title = '';

	if ( $args['heading'] && ! $args['is_widget'] ) {
		$title = str_replace( '%postname%', $post->post_title, $args['title'] );    // Replace %postname% with the title of the current post.
	}

	/**
	 * Filter the title of the Related Posts list
	 *
	 * @since   1.9
	 *
	 * @param   string  $title  Title/heading of the Related Posts list
	 * @param   array   $args   Array of arguments
	 */
	return apply_filters( 'crp_heading_title', $title, $args );
}


/**
 * Returns the opening tag of the related posts list.
 *
 * @since   2.2.0
 *
 * @param   array $args   Array of arguments.
 * @return  string  Space separated list of link attributes
 */
function crp_before_list( $args ) {

	$before_list = $args['before_list'];

	/**
	 * Filter the opening tag of the related posts list
	 *
	 * @since   1.9
	 *
	 * @param   string  $before_list    Opening tag set in the Settings Page
	 * @param   array   $args   Array of arguments
	 */
	return apply_filters( 'crp_before_list', $before_list, $args );

}


/**
 * Returns the closing tag of the related posts list.
 *
 * @since   2.2.0
 *
 * @param   array $args   Array of arguments.
 * @return  string  Space separated list of link attributes
 */
function crp_after_list( $args ) {

	$after_list = $args['after_list'];

	/**
	 * Filter the closing tag of the related posts list
	 *
	 * @since   1.9
	 *
	 * @param   string  $after_list Closing tag set in the Settings Page
	 * @param   array   $args   Array of arguments
	 */
	return apply_filters( 'crp_after_list', $after_list, $args );

}


/**
 * Returns the opening tag of each list item.
 *
 * @since   2.2.0
 *
 * @param   array  $args   Array of arguments.
 * @param   object $result Object of the current post result.
 * @return  string  Space separated list of link attributes
 */
function crp_before_list_item( $args, $result ) {

	$before_list_item = $args['before_list_item'];

	/**
	 * Filter the opening tag of each list item.
	 *
	 * @since   1.9
	 *
	 * @param   string  $before_list_item   Tag before each list item. Can be defined in the Settings page.
	 * @param   object  $result Object of the current post result
	 * @param   array   $args   Array of arguments
	 */
	return apply_filters( 'crp_before_list_item', $before_list_item, $result, $args );

}


/**
 * Returns the closing tag of each list item.
 *
 * @since   2.2.0
 *
 * @param   array  $args   Array of arguments.
 * @param   object $result Object of the current post result.
 * @return  string  Space separated list of link attributes
 */
function crp_after_list_item( $args, $result ) {

	$after_list_item = $args['after_list_item'];

	/**
	 * Filter the closing tag of each list item.
	 *
	 * @since   1.9
	 *
	 * @param   string  $after_list_item    Tag after each list item. Can be defined in the Settings page.
	 * @param   object  $result Object of the current post result
	 * @param   array   $args   Array of arguments
	 */
	return apply_filters( 'crp_after_list_item', $after_list_item, $result, $args );

}


/**
 * Returns the title of each list item.
 *
 * @since   2.2.0
 *
 * @param   array  $args   Array of arguments.
 * @param   object $result Object of the current post result.
 * @return  string  Space separated list of link attributes
 */
function crp_title( $args, $result ) {

	$title = crp_trim_char( $result->post_title, $args['title_length'] );  // Get the post title and crop it if needed.

	/**
	 * Filter the title of each list item.
	 *
	 * @since   1.9
	 *
	 * @param   string  $title  Title of the post.
	 * @param   object  $result Object of the current post result
	 * @param   array   $args   Array of arguments
	 */
	return apply_filters( 'crp_title', $title, $result, $args );

}


/**
 * Returns the author of each list item.
 *
 * @since   2.2.0
 *
 * @param   array  $args   Array of arguments.
 * @param   object $result Object of the current post result.
 * @return  string  Space separated list of link attributes
 */
function crp_author( $args, $result ) {

	$author_info = get_userdata( $result->post_author );
	$author_link = ( false === $author_info ) ? '' : get_author_posts_url( $author_info->ID );
	$author_name = ( false === $author_info ) ? '' : ucwords( trim( stripslashes( $author_info->display_name ) ) );

	/**
	 * Filter the author name.
	 *
	 * @since   1.9.1
	 *
	 * @param   string  $author_name    Proper name of the post author.
	 * @param   object  $author_info    WP_User object of the post author
	 */
	$author_name = apply_filters( 'crp_author_name', $author_name, $author_info );

	if ( ! empty( $author_name ) ) {
		$crp_author = '<span class="crp_author"> ' . __( ' by ', 'contextual-related-posts' ) . '<a href="' . $author_link . '">' . $author_name . '</a></span> ';
	} else {
		$crp_author = '';
	}

	/**
	 * Filter the text with the author details.
	 *
	 * @since   2.0.0
	 *
	 * @param   string  $crp_author Formatted string with author details and link
	 * @param   object  $author_info    WP_User object of the post author
	 * @param   object  $result Object of the current post result
	 * @param   array   $args   Array of arguments
	 */
	return apply_filters( 'crp_author', $crp_author, $author_info, $result, $args );

}


/**
 * Returns the permalink of each list item.
 *
 * @since   2.5.0
 *
 * @param   array  $args   Array of arguments.
 * @param   object $result Object of the current post result.
 * @return  string  Space separated list of link attributes
 */
function crp_permalink( $args, $result ) {

	$link = get_permalink( $result->ID );

	/**
	 * Filter the title of each list item.
	 *
	 * @since   2.5.0
	 *
	 * @param   string  $title  Permalink of the post.
	 * @param   object  $result Object of the current post result
	 * @param   array   $args   Array of arguments
	 */
	return apply_filters( 'crp_permalink', $link, $result, $args );

}


/**
 * Returns the formatted list item with link and and thumbnail for each list item.
 *
 * @since   2.2.0
 *
 * @param   array  $args   Array of arguments.
 * @param   object $result Object of the current post result.
 * @return  string Space separated list of link attributes
 */
function crp_list_link( $args, $result ) {

	$output          = '';
	$title           = crp_title( $args, $result );
	$link            = crp_permalink( $args, $result );
	$link_attributes = crp_link_attributes( $args );

	$output .= '<a href="' . $link . '" ' . $link_attributes . ' class="' . $result->post_type . '-' . $result->ID . '">';

	if ( 'after' === $args['post_thumb_op'] ) {
		$output .= '<span class="crp_title">' . $title . '</span>'; // Add title when required by settings.
	}

	if ( 'inline' === $args['post_thumb_op'] || 'after' === $args['post_thumb_op'] || 'thumbs_only' === $args['post_thumb_op'] ) {
		$output .= crp_get_the_post_thumbnail(
			array(
				'postid'             => $result,
				'thumb_height'       => $args['thumb_height'],
				'thumb_width'        => $args['thumb_width'],
				'thumb_meta'         => $args['thumb_meta'],
				'thumb_html'         => $args['thumb_html'],
				'thumb_default'      => $args['thumb_default'],
				'thumb_default_show' => $args['thumb_default_show'],
				'scan_images'        => $args['scan_images'],
				'class'              => 'crp_thumb',
			)
		);
	}

	if ( 'inline' === $args['post_thumb_op'] || 'text_only' === $args['post_thumb_op'] ) {
		$output .= '<span class="crp_title">' . $title . '</span>'; // Add title when required by settings.
	}

	$output .= '</a>';

	/**
	 * Filter Formatted list item with link and and thumbnail.
	 *
	 * @since   2.2.0
	 *
	 * @param   string  $output Formatted list item with link and and thumbnail
	 * @param   object  $result Object of the current post result
	 * @param   array   $args   Array of arguments
	 */
	return apply_filters( 'crp_list_link', $output, $result, $args );

}

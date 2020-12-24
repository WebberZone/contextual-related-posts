<?php
/**
 * Deprecated functions from Contextual Related Posts. You shouldn't
 * use these functions and look for the alternatives instead. The functions will be
 * removed in a later version.
 *
 * @package Contextual_Related_Posts
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Holds the URL for CRP
 *
 * @since   1.2
 * @deprecated 2.3.0
 *
 * @var string
 */
$crp_url = plugins_url() . '/' . plugin_basename( dirname( __FILE__ ) );

/**
 * Main function to generate the related posts output
 *
 * @since 1.0.1
 * @deprecated  2.2.0
 * @see get_crp
 *
 * @param   array $args   Parameters in a query string format.
 * @return  string          HTML formatted list of related posts
 */
function ald_crp( $args = array() ) {

	_deprecated_function( __FUNCTION__, '2.2.0', 'get_crp()' );

	$output = get_crp( $args );

	/**
	 * Filter the output
	 *
	 * @since   1.9.1
	 * @deprecated 2.2.0
	 *
	 * @param   string  $output Formatted list of related posts
	 * @param   array   $args   Complete set of arguments
	 */
	return apply_filters( 'ald_crp', $output, $args );
}


/**
 * Filter for 'the_content' to add the related posts.
 *
 * @since 1.0.1
 * @deprecated  2.2.0
 * @see crp_content_filter
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
 * @deprecated  2.2.0
 * @see crp_rss_filter
 *
 * @param   string $content Post content.
 * @return  string  Formatted content
 */
function ald_crp_rss( $content ) {

	_deprecated_function( __FUNCTION__, '2.2.0', 'crp_rss_filter()' );

	return crp_content_filter( $content );
}


/**
 * Manual install of the related posts.
 *
 * @since 1.0.1
 * @deprecated  2.2.0
 * @see echo_crp
 *
 * @param string $args Array of arguments.
 */
function echo_ald_crp( $args = array() ) {

	_deprecated_function( __FUNCTION__, '2.2.0', 'echo_crp()' );

	echo_crp( $args );
}

/**
 * Function to limit content by characters.
 *
 * @since 1.8.4
 * @deprecated 2.4.0
 *
 * @param   string $content    Content to be used to make an excerpt.
 * @param   int    $no_of_char Maximum length of excerpt in characters.
 * @return  string              Formatted content.
 */
function crp_max_formatted_content( $content, $no_of_char = -1 ) {

	_deprecated_function( __FUNCTION__, '2.4.0', 'crp_trim_char()' );

	$content = crp_trim_char( $content, $no_of_char );

	/**
	 * Filters formatted content after cropping.
	 *
	 * @since   1.9
	 * @deprecated 2.4.0
	 *
	 * @param   string  $content    Formatted content
	 * @param   int     $no_of_char Maximum length of excerpt in characters
	 */
	return apply_filters( 'crp_max_formatted_content', $content, $no_of_char );
}


/**
 * Default options.
 *
 * @since 1.0.1
 * @deprecated 2.6.0
 *
 * @return array Default options
 */
function crp_default_options() {

	_deprecated_function( __FUNCTION__, '2.6.0' );

	$title = '<h3>' . __( 'Related Posts', 'contextual-related-posts' ) . ':</h3>';

	$blank_output_text = __( 'No related posts found', 'contextual-related-posts' );

	$thumb_default = plugins_url( 'default.png', __FILE__ );

	$crp_settings = array(
		// General options.
		'cache'                    => false,            // Cache output for faster page load.

		'add_to_content'           => true,     // Add related posts to content (only on single posts).
		'add_to_page'              => true,     // Add related posts to content (only on single pages).
		'add_to_feed'              => false,        // Add related posts to feed (full).
		'add_to_home'              => false,        // Add related posts to home page.
		'add_to_category_archives' => false,        // Add related posts to category archives.
		'add_to_tag_archives'      => false,        // Add related posts to tag archives.
		'add_to_archives'          => false,        // Add related posts to other archives.

		'content_filter_priority'  => 10,   // Content priority.
		'insert_after_paragraph'   => -1,   // Insert after paragraph number.
		'disable_on_mobile'        => false, // Disable on mobile.
		'disable_on_amp'           => false, // Disable on AMP.
		'show_metabox'             => true, // Show metabox to admins.
		'show_metabox_admins'      => false,    // Limit to admins as well.

		'show_credit'              => false,        // Link to this plugin's page?

		// List tuning options.
		'limit'                    => '6',              // How many posts to display?
		'daily_range'              => '1095',               // How old posts should be displayed?
		'random_order'             => false,    // Randomise posts.

		'match_content'            => true,     // Match against post content as well as title.
		'match_content_words'      => '0',  // How many characters of content should be matched? 0 for all chars.

		'post_types'               => 'post,page',      // WordPress custom post types.
		'same_post_type'           => false,    // Limit to the same post type.

		'exclude_categories'       => '',   // Exclude these categories.
		'exclude_cat_slugs'        => '',   // Exclude these categories (slugs).
		'exclude_post_ids'         => '',   // Comma separated list of page / post IDs that are to be excluded in the results.

		// Output options.
		'title'                    => $title,           // Add before the content.
		'blank_output'             => true,     // Blank output?
		'blank_output_text'        => $blank_output_text,       // Blank output text.

		'show_excerpt'             => false,            // Show post excerpt in list item.
		'show_date'                => false,            // Show date in list item.
		'show_author'              => false,            // Show author in list item.
		'excerpt_length'           => '10',     // Length of characters.
		'title_length'             => '60',     // Limit length of post title.

		'link_new_window'          => false,            // Open link in new window.
		'link_nofollow'            => false,            // Includes rel nofollow to links.

		'before_list'              => '<ul>',   // Before the entire list.
		'after_list'               => '</ul>',  // After the entire list.
		'before_list_item'         => '<li>',   // Before each list item.
		'after_list_item'          => '</li>',  // After each list item.

		'exclude_on_post_ids'      => '',   // Comma separate list of page/post IDs to not display related posts on.
		'exclude_on_post_types'    => '',       // WordPress custom post types.

		// Thumbnail options.
		'post_thumb_op'            => 'inline', // Default option to display text and no thumbnails in posts.
		'thumb_size'               => 'thumbnail',  // Default thumbnail size.
		'thumb_height'             => '150',    // Height of thumbnails.
		'thumb_width'              => '150',    // Width of thumbnails.
		'thumb_crop'               => true,     // Crop mode. default is hard crop.
		'thumb_create_sizes'       => true,     // Create thumbnail sizes.
		'thumb_html'               => 'html',       // Use HTML or CSS for width and height of the thumbnail?
		'thumb_meta'               => 'post-image', // Meta field that is used to store the location of default thumbnail image.
		'scan_images'              => true,         // Scan post for images.
		'thumb_default'            => $thumb_default,   // Default thumbnail image.
		'thumb_default_show'       => true, // Show default thumb if none found (if false, don't show thumb at all).

		// Feed options.
		'limit_feed'               => '5',          // How many posts to display in feeds.
		'post_thumb_op_feed'       => 'text_only',  // Default option to display text and no thumbnails in Feeds.
		'thumb_height_feed'        => '50', // Height of thumbnails in feed.
		'thumb_width_feed'         => '50', // Width of thumbnails in feed.
		'show_excerpt_feed'        => false,            // Show description in list item in feed.

		// Custom styles.
		'custom_CSS'               => '',           // Custom CSS to style the output.
		'include_default_style'    => true,         // Include default style - Will be DEPRECATED in the next version.
		'crp_styles'               => 'rounded_thumbs', // Defaault style is rounded thubnails.
	);

	/**
	 * Filters the default options array.
	 *
	 * @since   1.9.1
	 * @deprecated 2.6.0
	 *
	 * @param   array   $crp_settings   Default options.
	 */
	return apply_filters( 'crp_default_options', $crp_settings );
}


/**
 * Function to read options from the database.
 *
 * @since 1.0.1
 * @deprecated 2.6.0
 *
 * @return array Contextual Related Posts options
 */
function crp_read_options() {

	_deprecated_function( __FUNCTION__, '2.6.0', 'crp_get_settings()' );

	$crp_settings = crp_get_settings();

	/**
	 * Filters the options array.
	 *
	 * @since   1.9.1
	 * @deprecated 2.6.0
	 *
	 * @param   array   $crp_settings   Options read from the database
	 */
	return apply_filters( 'crp_read_options', $crp_settings );
}


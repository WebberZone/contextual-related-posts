<?php
/**
 * Contextual Related Posts.
 *
 * Contextual Related Posts is the best related posts plugin for WordPress that
 * allows you to display a list of related posts on your website and in your feed.
 *
 * @package   Contextual_Related_Posts
 * @author    Ajay D'Souza <me@ajaydsouza.com>
 * @license   GPL-2.0+
 * @link      https://webberzone.com
 * @copyright 2009-2015 Ajay D'Souza
 *
 * @wordpress-plugin
 * Plugin Name:	Contextual Related Posts
 * Plugin URI:	https://webberzone.com/plugins/contextual-related-posts/
 * Description:	Display a set of related posts on your website or in your feed. Increase reader retention and reduce bounce rates
 * Version: 	2.2.0
 * Author: 		WebberZone
 * Author URI: 	https://webberzone.com
 * Text Domain:	crp
 * License: 	GPL-2.0+
 * License URI:	http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:	/languages
 * GitHub Plugin URI: https://github.com/WebberZone/contextual-related-posts/
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Holds the text domain.
 *
 * @since	1.4
 */
define( 'CRP_LOCAL_NAME', 'crp' );

/**
 * Holds the filesystem directory path (with trailing slash) for CRP
 *
 * @since	1.2
 *
 * @var string
 */
$crp_path = plugin_dir_path( __FILE__ );

/**
 * Holds the URL for CRP
 *
 * @since	1.2
 *
 * @var string
 */
$crp_url = plugins_url() . '/' . plugin_basename( dirname( __FILE__ ) );

/**
 * Global variable holding the current settings for Contextual Related Posts
 *
 * @since	1.8.10
 *
 * @var array
 */
global $crp_settings;
$crp_settings = crp_read_options();


/**
 * Initialises text domain for l10n.
 *
 * @since	2.2.0
 */
function crp_lang_init() {
	load_plugin_textdomain( CRP_LOCAL_NAME, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'crp_lang_init' );


/**
 * Main function to generate the related posts output
 *
 * @since 1.0.1
 *
 * @param	array	$args	Parameters in a query string format
 * @return	string			HTML formatted list of related posts
 */
function get_crp( $args = array() ) {
	global $wpdb, $post, $crp_settings;

	$defaults = array(
		'is_widget' => FALSE,
		'is_manual' => FALSE,
		'echo' => TRUE,
		'heading' => TRUE,
	);
	$defaults = array_merge( $defaults, $crp_settings );

	// Parse incomming $args into an array and merge it with $defaults
	$args = wp_parse_args( $args, $defaults );

	//Support caching to speed up retrieval
	if ( ! empty( $args['cache'] ) ) {
		$meta_key = 'crp_related_posts';
		if ( $args['is_widget'] ) {
			$meta_key .= '_widget';
		}
		if ( $args['is_manual'] ) {
			$meta_key .= '_manual';
		}
		if ( is_feed() ) {
			$meta_key .= '_feed';
		}
		$output = get_post_meta( $post->ID, $meta_key, true );
		if ( $output ) {
			return $output;
		}
	}

	$exclude_categories = explode( ',', $args['exclude_categories'] );

	// Retrieve the list of posts
	$results = get_crp_posts_id( array_merge( $args, array(
		'postid' => $post->ID,
		'strict_limit' => TRUE,
	) ) );

	$output = ( is_singular() ) ? '<div id="crp_related" class="crp_related' . ( $args['is_widget'] ? '_widget' : '' ) . '">' : '<div class="crp_related' . ( $args['is_widget'] ? '_widget' : '' ) . '">';

	if ( $results ) {
		$loop_counter = 0;

		$output .= crp_heading_title( $args );

		$output .= crp_before_list( $args );

		foreach ( $results as $result ) {

			/**
			 * Filter the post ID for each result. Allows a custom function to hook in and change the ID if needed.
			 *
			 * @since	1.9
			 *
			 * @param	int	$result->ID	ID of the post
			 */
			$resultid = apply_filters( 'crp_post_id', $result->ID );

			$result = get_post( $resultid );	// Let's get the Post using the ID

			$output .= crp_before_list_item( $args, $result );

			$output .= crp_list_link( $args, $result );

			if ( $args['show_author'] ) {
				$output .= crp_author( $args, $result );
			}

			if ( $args['show_date'] ) {
				$output .= '<span class="crp_date"> ' . mysql2date( get_option( 'date_format', 'd/m/y' ), $result->post_date ) . '</span> ';
			}

			if ( $args['show_excerpt'] ) {
				$output .= '<span class="crp_excerpt"> ' . crp_excerpt( $result->ID, $excerpt_length ) . '</span>';
			}

			$loop_counter++;

			$output .= crp_after_list_item( $args, $result );

			if ( $loop_counter == $args['limit'] ) break;	// End loop when related posts limit is reached
		} //end of foreach loop

		if ( $args['show_credit'] ) {

			$output .= crp_before_list_item( $args, $result );

			$output .= sprintf( __( 'Powered by <a href="%s" rel="nofollow">Contextual Related Posts</a>', CRP_LOCAL_NAME ), esc_url( 'https://webberzone.com/plugins/contextual-related-posts/' ) );

			$output .= crp_after_list_item( $args, $result );

		}

		$output .= crp_after_list( $args );

		$clearfix = '<div class="crp_clear"></div>';

		/**
		 * Filter the clearfix div tag. This is included after the closing tag to clear any miscellaneous floating elements;
		 *
		 * @since	2.0.0
		 *
		 * @param	string	$clearfix	Contains: <div style="clear:both"></div>
		 */
		$output .= apply_filters( 'crp_clearfix', $clearfix );

	} else {
		$output .= ( $args['blank_output'] ) ? ' ' : '<p>' . $args['blank_output_text'] . '</p>';
	}

	// Check if the opening list tag is missing in the output, it means all of our results were eliminated cause of the category filter
	if ( false === ( strpos( $output, $args['before_list_item'] ) ) ) {
		$output = '<div id="crp_related">';
		$output .= ( $args['blank_output'] ) ? ' ' : '<p>' . $args['blank_output_text'] . '</p>';
	}

	$output .= '</div>'; // closing div of 'crp_related'


	//Support caching to speed up retrieval
	if ( ! empty( $args['cache'] ) ) {
		update_post_meta( $post->ID, $meta_key, $output, '' );
	}

	/**
	 * Filter the output
	 *
	 * @since	1.9.1
	 *
	 * @param	string	$output	Formatted list of related posts
	 * @param	array	$args	Complete set of arguments
	 */
	return apply_filters( 'get_crp', $output, $args );
}


/**
 * Fetch related posts IDs.
 *
 * @since 1.9
 *
 * @param array $args
 * @return object $results
 */
function get_crp_posts_id( $args = array() ) {
	global $wpdb, $post, $crp_settings;

	// Initialise some variables
	$fields = '';
	$where = '';
	$join = '';
	$groupby = '';
	$orderby = '';
	$having = '';
	$limits = '';
	$match_fields = '';

	$defaults = array(
		'postid' => FALSE,
		'strict_limit' => TRUE,
	);
	$defaults = array_merge( $defaults, $crp_settings );

	// Parse incoming $args into an array and merge it with $defaults
	$args = wp_parse_args( $args, $defaults );

	// Fix the thumb size in case it is missing
	$crp_thumb_size = crp_get_all_image_sizes( $args['thumb_size'] );

	if ( isset( $crp_thumb_size['width'] ) ) {
		$thumb_width = $crp_thumb_size['width'];
		$thumb_height = $crp_thumb_size['height'];
	}

	if ( empty( $thumb_width ) ) {
		$thumb_width = $crp_settings['thumb_width'];
	}

	if ( empty( $thumb_height ) ) {
		$thumb_height = $crp_settings['thumb_height'];
	}

	$post = ( empty( $args['postid'] ) ) ? $post : get_post( $args['postid'] );

	$limit = ( $args['strict_limit'] ) ? $args['limit'] : ( $args['limit'] * 3 );

	parse_str( $args['post_types'], $post_types );	// Save post types in $post_types variable

	/**
	 * Filter the post_type clause of the query.
	 *
	 * @since 2.2.0
	 *
	 * @param array  $post_types  Array of post types to filter by
	 * @param int    $post->ID    Post ID
	 */
	$post_types = apply_filters( 'crp_posts_post_types', $post_types, $post->ID );

	// Are we matching only the title or the post content as well?
	$match_fields = array(
		'post_title',
	);

	$match_fields_content = array(
		$post->post_title,
	);

	if( $args['match_content'] ) {

		$match_fields[] = 'post_content';
		$match_fields_content[] = crp_excerpt( $post->ID, $args['match_content_words'], false );
	}

	/**
	 * Filter the fields that are to be matched.
	 *
	 * @since	2.2.0
	 *
	 * @param array   $match_fields	Array of fields to be matched
	 * @param int	   $post->ID	Post ID
	 */
	$match_fields = apply_filters( 'crp_posts_match_fields', $match_fields, $post->ID );

	/**
	 * Filter the content of the fields that are to be matched.
	 *
	 * @since	2.2.0
	 *
	 * @param array	$match_fields_content	Array of content of fields to be matched
	 * @param int	$post->ID	Post ID
	 */
	$match_fields_content = apply_filters( 'crp_posts_match_fields_content', $match_fields_content, $post->ID );

	// Convert our arrays into their corresponding strings after they have been filtered
	$match_fields = implode( ",", $match_fields );
	$stuff = implode( " ", $match_fields_content );

	// Make sure the post is not from the future
	$time_difference = get_option( 'gmt_offset' );
	$now = gmdate( "Y-m-d H:i:s", ( time() + ( $time_difference * 3600 ) ) );

	// Limit the related posts by time
	$current_time = current_time( 'timestamp', 0 );
	$from_date = $current_time - ( $args['daily_range'] * DAY_IN_SECONDS );
	$from_date = gmdate( 'Y-m-d H:i:s' , $from_date );

	// Create the SQL query to fetch the related posts from the database
	if ( ( is_int( $post->ID ) ) && ( '' != $stuff ) ) {

		// Fields to return
		$fields = " $wpdb->posts.ID ";

		// Create the base MATCH clause
		$match = $wpdb->prepare( " AND MATCH (" . $match_fields . ") AGAINST ('%s') ", $stuff );	// FULLTEXT matching algorithm

		/**
		 * Filter the MATCH clause of the query.
		 *
		 * @since	2.1.0
		 *
		 * @param string   $match  		The MATCH section of the WHERE clause of the query
		 * @param string   $stuff  		String to match fulltext with
		 * @param int	   $post->ID	Post ID
		 */
		$match = apply_filters( 'crp_posts_match', $match, $stuff, $post->ID );

		// Create the maximum date limit
		$now_clause = $wpdb->prepare( " AND $wpdb->posts.post_date < '%s' ", $now );		// Show posts before today

		/**
		 * Filter the Maximum date clause of the query.
		 *
		 * @since	2.1.0
		 *
		 * @param string   $now_clause  The Maximum date of the WHERE clause of the query.
		 * @param int	   $post->ID	Post ID
		 */
		$now_clause = apply_filters( 'crp_posts_now_date', $now_clause, $post->ID );

		// Create the minimum date limit
		$from_clause = ( 0 == $args['daily_range'] ) ? '' : $wpdb->prepare( " AND $wpdb->posts.post_date >= '%s' ", $from_date );	// Show posts after the date specified

		/**
		 * Filter the Maximum date clause of the query.
		 *
		 * @since	2.1.0
		 *
		 * @param string   $from_clause  The Minimum date of the WHERE clause of the query.
		 * @param int	   $post->ID	Post ID
		 */
		$from_clause = apply_filters( 'crp_posts_from_date', $from_clause, $post->ID );

		// Create the base WHERE clause
		$where = $match;
		$where .= $now_clause;
		$where .= $from_clause;
		$where .= " AND $wpdb->posts.post_status = 'publish' ";					// Only show published posts
		$where .= $wpdb->prepare( " AND $wpdb->posts.ID != %d ", $post->ID );	// Show posts after the date specified
		if ( '' != $args['exclude_post_ids'] ) {
			$where .= " AND $wpdb->posts.ID NOT IN (" . $args['exclude_post_ids'] . ") ";
		}
		$where .= " AND $wpdb->posts.post_type IN ('" . join( "', '", $post_types ) . "') ";	// Array of post types

		// Create the base LIMITS clause
		$limits .= $wpdb->prepare( " LIMIT %d ", $limit );

		/**
		 * Filter the SELECT clause of the query.
		 *
		 * @since	2.0.0
		 *
		 * @param string   $fields  The SELECT clause of the query.
		 * @param int	   $post->ID	Post ID
		 */
		$fields = apply_filters( 'crp_posts_fields', $fields, $post->ID );

		/**
		 * Filter the JOIN clause of the query.
		 *
		 * @since	2.0.0
		 *
		 * @param string   $join  The JOIN clause of the query.
		 * @param int	   $post->ID	Post ID
		 */
 		$join = apply_filters( 'crp_posts_join', $join, $post->ID );

		/**
		 * Filter the WHERE clause of the query.
		 *
		 * @since	2.0.0
		 *
		 * @param string   $where  The WHERE clause of the query.
		 * @param int	   $post->ID	Post ID
		 */
		$where = apply_filters( 'crp_posts_where', $where, $post->ID );

		/**
		 * Filter the GROUP BY clause of the query.
		 *
		 * @since	2.0.0
		 *
		 * @param string   $groupby  The GROUP BY clause of the query.
		 * @param int	   $post->ID	Post ID
		 */
		$groupby = apply_filters( 'crp_posts_groupby', $groupby, $post->ID );

		/**
		 * Filter the HAVING clause of the query.
		 *
		 * @since	2.2.0
		 *
		 * @param string  $having  The HAVING clause of the query.
		 * @param int	    $post->ID	Post ID
		 */
		$having = apply_filters( 'crp_posts_having', $having, $post->ID );

		/**
		 * Filter the ORDER BY clause of the query.
		 *
		 * @since	2.0.0
		 *
		 * @param string   $orderby  The ORDER BY clause of the query.
		 * @param int	   $post->ID	Post ID
		 */
		$orderby = apply_filters( 'crp_posts_orderby', $orderby, $post->ID );

		/**
		 * Filter the LIMIT clause of the query.
		 *
		 * @since	2.0.0
		 *
		 * @param string   $limits  The LIMIT clause of the query.
		 * @param int	   $post->ID	Post ID
		 */
		$limits = apply_filters( 'crp_posts_limits', $limits, $post->ID );

		if ( ! empty( $groupby ) ) {
			$groupby = 'GROUP BY ' . $groupby;
		}

		if ( ! empty( $having ) ) {
			$having = 'HAVING ' . $having;
		}

		if ( !empty( $orderby ) ) {
			$orderby = 'ORDER BY ' . $orderby;
		}

		$sql = "SELECT DISTINCT $fields FROM $wpdb->posts $join WHERE 1=1 $where $groupby $having $orderby $limits";
		$results = $wpdb->get_results( $sql );
	} else {
		$results = false;
	}

	/**
	 * Filter object containing the post IDs.
	 *
	 * @since	1.9
	 *
	 * @param 	object   $results  Object containing the related post IDs
	 */
	return apply_filters( 'get_crp_posts_id', $results );
}


/**
 * Content function with user defined filter.
 *
 * @since 1.9
 *
 */
function crp_content_prepare_filter() {
	global $crp_settings;

    $priority = isset ( $crp_settings['content_filter_priority'] ) ? $crp_settings['content_filter_priority'] : 10;

	add_filter( 'the_content', 'crp_content_filter', $priority );
}
add_action( 'template_redirect', 'crp_content_prepare_filter' );


/**
 * Filter for 'the_content' to add the related posts.
 *
 * @since 1.0.1
 *
 * @param string $content
 * @return string After the filter has been processed
 */
function crp_content_filter( $content ) {

	global $post, $crp_settings;

	// Return if it's not in the loop or in the main query
	if  ( ! in_the_loop() && ! is_main_query() ) {
		return $content;
	}

	// If this post ID is in the DO NOT DISPLAY list
	$exclude_on_post_ids = explode( ',', $crp_settings['exclude_on_post_ids'] );
	if ( in_array( $post->ID, $exclude_on_post_ids ) ) return $content;	// Exit without adding related posts

	// If this post type is in the DO NOT DISPLAY list
	parse_str( $crp_settings['exclude_on_post_types'], $exclude_on_post_types );	// Save post types in $exclude_on_post_types variable
	if ( in_array( $post->post_type, $exclude_on_post_types ) ) return $content;	// Exit without adding related posts

	// If the DO NOT DISPLAY meta field is set
	$crp_post_meta = get_post_meta( $post->ID, 'crp_post_meta', true );

	if ( isset( $crp_post_meta['crp_disable_here'] ) ) {
		$crp_disable_here = $crp_post_meta['crp_disable_here'];
	} else {
		$crp_disable_here = 0;
	}

	if ( $crp_disable_here ) return $content;

	// Else add the content
    if ( ( is_single() ) && ( $crp_settings['add_to_content'] ) ) {
        return $content.get_crp( 'is_widget=0' );
    } elseif ( ( is_page() ) && ( $crp_settings['add_to_page'] ) ) {
        return $content.get_crp( 'is_widget=0' );
    } elseif ( ( is_home() ) && ( $crp_settings['add_to_home'] ) ) {
        return $content.get_crp( 'is_widget=0' );
    } elseif ( ( is_category() ) && ( $crp_settings['add_to_category_archives'] ) ) {
        return $content.get_crp( 'is_widget=0' );
    } elseif ( ( is_tag() ) && ( $crp_settings['add_to_tag_archives'] ) ) {
        return $content.get_crp( 'is_widget=0' );
    } elseif ( ( ( is_tax() ) || ( is_author() ) || ( is_date() ) ) && ( $crp_settings['add_to_archives'] ) ) {
        return $content.get_crp( 'is_widget=0' );
    } else {
        return $content;
    }
}


/**
 * Filter to add related posts to feeds.
 *
 * @since 1.8.4
 *
 * @param	string	$content
 * @return	string	Formatted content
 */
function crp_rss_filter( $content ) {
	global $post, $crp_settings;

	$limit_feed = $crp_settings['limit_feed'];
	$show_excerpt_feed = $crp_settings['show_excerpt_feed'];
	$post_thumb_op_feed = $crp_settings['post_thumb_op_feed'];

	if ( $crp_settings['add_to_feed'] ) {
		$output = $content;
		$output .= get_crp( 'is_widget=0&limit='.$limit_feed.'&show_excerpt='.$show_excerpt_feed.'&post_thumb_op='.$post_thumb_op_feed );
		return $output;
	} else {
        return $content;
    }
}
add_filter( 'the_excerpt_rss', 'crp_rss_filter' );
add_filter( 'the_content_feed', 'crp_rss_filter' );


/**
 * Echos the related posts. Used for manual install
 *
 * @since 1.0.1
 *
 * @param	string	List of arguments to control the output
 */
function echo_crp( $args = array() ) {

	$defaults = array(
		'is_manual' => TRUE,
	);

	// Parse incomming $args into an array and merge it with $defaults
	$args = wp_parse_args( $args, $defaults );

	echo get_crp( $args );
}


/**
 * Enqueue styles.
 *
 * @since 1.9
 *
 */
function crp_heading_styles() {
	global $crp_settings;

	if ( 'rounded_thumbs' == $crp_settings['crp_styles'] ) {
		wp_register_style( 'crp-style-rounded-thumbs', plugins_url( 'css/default-style.css', __FILE__ ) );
		wp_enqueue_style( 'crp-style-rounded-thumbs' );


        $custom_css = "
.crp_related a {
  width: {$crp_settings['thumb_width']}px;
  height: {$crp_settings['thumb_height']}px;
  text-decoration: none;
}
.crp_related img {
  max-width: {$crp_settings['thumb_width']}px;
  margin: auto;
}
.crp_related .crp_title {
  width: " . ( $crp_settings['thumb_width'] - 6 ) . "px;
}
                ";

		wp_add_inline_style( 'crp-style-rounded-thumbs', $custom_css );

	}
}
add_action( 'wp_enqueue_scripts', 'crp_heading_styles' );


/**
 * Default options.
 *
 * @since 1.0.1
 *
 * @return array Default options
 */
function crp_default_options() {

	$title = __( '<h3>Related Posts:</h3>', CRP_LOCAL_NAME );

	$blank_output_text = __( 'No related posts found', CRP_LOCAL_NAME );

	$thumb_default = plugins_url( 'default.png' , __FILE__ );

	// Set default post types to post and page
	$post_types = array(
		'post' => 'post',
		'page' => 'page',
	);
	$post_types	= http_build_query( $post_types, '', '&' );

	$crp_settings = array(
		// General options
		'cache' => false,			// Cache output for faster page load

		'add_to_content' => true,		// Add related posts to content (only on single posts)
		'add_to_page' => true,		// Add related posts to content (only on single pages)
		'add_to_feed' => false,		// Add related posts to feed (full)
		'add_to_home' => false,		// Add related posts to home page
		'add_to_category_archives' => false,		// Add related posts to category archives
		'add_to_tag_archives' => false,		// Add related posts to tag archives
		'add_to_archives' => false,		// Add related posts to other archives

		'content_filter_priority' => 10,	// Content priority
		'show_metabox'	=> true,	// Show metabox to admins
		'show_metabox_admins'	=>	false,	// Limit to admins as well

		'show_credit' => false,		// Link to this plugin's page?

		// List tuning options
		'limit' => '6',				// How many posts to display?
		'daily_range' => '1095',				// How old posts should be displayed?

		'match_content' => true,		// Match against post content as well as title
		'match_content_words' => '0',	// How many characters of content should be matched? 0 for all chars

		'post_types' => $post_types,		// WordPress custom post types

		'exclude_categories' => '',	// Exclude these categories
		'exclude_cat_slugs' => '',	// Exclude these categories (slugs)
		'exclude_post_ids' => '',	// Comma separated list of page / post IDs that are to be excluded in the results

		// Output options
		'title' => $title,			// Add before the content
		'blank_output' => true,		// Blank output?
		'blank_output_text' => $blank_output_text,		// Blank output text

		'show_excerpt' => false,			// Show post excerpt in list item
		'show_date' => false,			// Show date in list item
		'show_author' => false,			// Show author in list item
		'excerpt_length' => '10',		// Length of characters
		'title_length' => '60',		// Limit length of post title

		'link_new_window' => false,			// Open link in new window - Includes target="_blank" to links
		'link_nofollow' => false,			// Includes rel="nofollow" to links

		'before_list' => '<ul>',	// Before the entire list
		'after_list' => '</ul>',	// After the entire list
		'before_list_item' => '<li>',	// Before each list item
		'after_list_item' => '</li>',	// After each list item

		'exclude_on_post_ids' => '', 	// Comma separate list of page/post IDs to not display related posts on
		'exclude_on_post_types' => '',		// WordPress custom post types

		// Thumbnail options
		'post_thumb_op' => 'inline',	// Default option to display text and no thumbnails in posts
		'thumb_size' => 'crp_thumbnail',	// Default thumbnail size
		'thumb_height' => '150',	// Height of thumbnails
		'thumb_width' => '150',	// Width of thumbnails
		'thumb_crop' => true,		// Crop mode. default is hard crop
		'thumb_html' => 'html',		// Use HTML or CSS for width and height of the thumbnail?
		'thumb_meta' => 'post-image',	// Meta field that is used to store the location of default thumbnail image
		'scan_images' => true,			// Scan post for images
		'thumb_default' => $thumb_default,	// Default thumbnail image
		'thumb_default_show' => true,	// Show default thumb if none found (if false, don't show thumb at all)

		// Feed options
		'limit_feed' => '5',				// How many posts to display in feeds
		'post_thumb_op_feed' => 'text_only',	// Default option to display text and no thumbnails in Feeds
		'thumb_height_feed' => '50',	// Height of thumbnails in feed
		'thumb_width_feed' => '50',	// Width of thumbnails in feed
		'show_excerpt_feed' => false,			// Show description in list item in feed

		// Custom styles
		'custom_CSS' => '',			// Custom CSS to style the output
		'include_default_style' => true,	// Include default style - Will be DEPRECATED in the next version
		'crp_styles'	=> 'rounded_thumbs'	// Defaault style is rounded thubnails
	);


	/**
	 * Filters the default options array.
	 *
	 * @since	1.9.1
	 *
	 * @param	array	$crp_settings	Default options
	 */
	return apply_filters( 'crp_default_options', $crp_settings );
}


/**
 * Function to read options from the database.
 *
 * @since 1.0.1
 *
 * @return array Contextual Related Posts options
 */
function crp_read_options() {
	$crp_settings_changed = false;

	$defaults = crp_default_options();

	$crp_settings = array_map( 'stripslashes', (array) get_option( 'ald_crp_settings') );
	unset( $crp_settings[0] ); // produced by the (array) casting when there's nothing in the DB

	foreach ( $defaults as $k=>$v ) {
		if ( ! isset( $crp_settings[ $k ] ) ) {
			$crp_settings[ $k ] = $v;
		}
		$crp_settings_changed = true;
	}
	if ( true == $crp_settings_changed ) {
		update_option('ald_crp_settings', $crp_settings);
	}

	/**
	 * Filters the options array.
	 *
	 * @since	1.9.1
	 *
	 * @param	array	$crp_settings	Options read from the database
	 */
	return apply_filters( 'crp_read_options', $crp_settings );
}


/**
 * Filter for wp_head to include the custom CSS.
 *
 * @since 1.8.4
 *
 * @return	string	Echoed string with the CSS output in the Header
 */
function crp_header() {
	global $wpdb, $post, $crp_settings;

	$crp_custom_CSS = stripslashes( $crp_settings['custom_CSS'] );

	// Add CSS to header
	if ( '' != $crp_custom_CSS ) {
	    if ( ( is_single() ) ) {
			echo '<style type="text/css">'.$crp_custom_CSS.'</style>';
	    } elseif((is_page())) {
			echo '<style type="text/css">'.$crp_custom_CSS.'</style>';
	    } elseif ( ( is_home() ) && ( $crp_settings['add_to_home'] ) ) {
			echo '<style type="text/css">'.$crp_custom_CSS.'</style>';
	    } elseif ( ( is_category() ) && ( $crp_settings['add_to_category_archives'] ) ) {
			echo '<style type="text/css">'.$crp_custom_CSS.'</style>';
	    } elseif ( ( is_tag() ) && ( $crp_settings['add_to_tag_archives'] ) ) {
			echo '<style type="text/css">'.$crp_custom_CSS.'</style>';
	    } elseif( ( ( is_tax() ) || ( is_author() ) || ( is_date() ) ) && ( $crp_settings['add_to_archives'] ) ) {
			echo '<style type="text/css">'.$crp_custom_CSS.'</style>';
	    } elseif ( is_active_widget( false, false, 'CRP_Widget', true ) ) {
			echo '<style type="text/css">'.$crp_custom_CSS.'</style>';
	    }
	}
}
add_action( 'wp_head', 'crp_header' );


/*----------------------------------------------------------------------------*
 * Activate the plugin
 *----------------------------------------------------------------------------*/

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-name-activator.php
 *
 * @since 2.2.0
 *
 */
function activate_crp( $network_wide ) {
	require_once plugin_dir_path( __FILE__ ) . 'includes/plugin-activator.php';
	crp_activate( $network_wide );
}
register_activation_hook( __FILE__, 'activate_crp' );


/**
 * Fired when a new site is activated with a WPMU environment.
 *
 * @since 2.0.0
 *
 * @param    int    $blog_id    ID of the new blog.
 */
function crp_activate_new_site( $blog_id ) {

	if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
		return;
	}

	require_once plugin_dir_path( __FILE__ ) . 'includes/plugin-activator.php';

	switch_to_blog( $blog_id );
	crp_single_activate();
	restore_current_blog();

}
add_action( 'wpmu_new_blog', 'crp_activate_new_site' );



/*----------------------------------------------------------------------------*
 * WordPress widget
 *----------------------------------------------------------------------------*/

/**
 * Initialise the widget.
 *
 * @since 1.9.1
 *
 */
function register_crp_widget() {
	require_once( plugin_dir_path( __FILE__ ) . 'includes/class-crp-widget.php' );

	register_widget( 'CRP_Widget' );
}
add_action( 'widgets_init', 'register_crp_widget' );


/*----------------------------------------------------------------------------*
 * CRP modules & includes
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'includes/output-generator.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/media-handler.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/tools.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/modules/manual-posts.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/modules/shortcode.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/modules/taxonomies.php' );


/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

if ( is_admin() || strstr( $_SERVER['PHP_SELF'], 'wp-admin/' ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/admin.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'admin/loader.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'admin/metabox.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'admin/cache.php' );

} // End admin.inc


/*----------------------------------------------------------------------------*
 * Deprecated functions
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'includes/deprecated.php' );


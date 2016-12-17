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
 * Version: 	2.3.1
 * Author: 		WebberZone
 * Author URI: 	https://webberzone.com
 * License: 	GPL-2.0+
 * License URI:	http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:	contextual-related-posts
 * Domain Path:	/languages
 * GitHub Plugin URI: https://github.com/WebberZone/contextual-related-posts/
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Holds the filesystem directory path (with trailing slash) for Contextual Related Posts.
 *
 * @since 2.3.0
 *
 * @var string Plugin folder path
 */
if ( ! defined( 'CRP_PLUGIN_DIR' ) ) {
	define( 'CRP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

/**
 * Holds the filesystem directory path (with trailing slash) for Contextual Related Posts.
 *
 * @since 2.3.0
 *
 * @var string Plugin folder URL
 */
if ( ! defined( 'CRP_PLUGIN_URL' ) ) {
	define( 'CRP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

/**
 * Holds the filesystem directory path (with trailing slash) for Contextual Related Posts.
 *
 * @since 2.3.0
 *
 * @var string Plugin Root File
 */
if ( ! defined( 'CRP_PLUGIN_FILE' ) ) {
	define( 'CRP_PLUGIN_FILE', __FILE__ );
}


/**
 * Maximum words to match in the content.
 *
 * @since 2.3.0
 *
 * @var int Maximum number of words to match.
 */
if ( ! defined( 'CRP_MAX_WORDS' ) ) {
	define( 'CRP_MAX_WORDS', 500 );
}


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
 * Main function to generate the related posts output
 *
 * @since 1.0.1
 *
 * @param array $args Parameters in a query string format.
 * @return string HTML formatted list of related posts
 */
function get_crp( $args = array() ) {
	global $post, $crp_settings;

	// If set, save $exclude_categories.
	if ( isset( $args['exclude_categories'] ) && '' != $args['exclude_categories'] ) {
		$exclude_categories = explode( ',', $args['exclude_categories'] );
		$args['strict_limit'] = false;
	}
	$defaults = array(
		'is_widget' => false,
		'is_shortcode' => false,
		'is_manual' => false,
		'echo' => true,
		'heading' => true,
		'offset' => 0,
	);
	$defaults = array_merge( $defaults, $crp_settings );

	// Parse incomming $args into an array and merge it with $defaults.
	$args = wp_parse_args( $args, $defaults );

	// WPML support.
	if ( function_exists( 'wpml_object_id_filter' ) || function_exists( 'icl_object_id' ) ) {
		$args['strict_limit'] = false;
	}

	// Support caching to speed up retrieval.
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

	// Retrieve the list of posts.
	$results = get_crp_posts_id( array_merge( $args, array(
		'postid' => $post->ID,
		'strict_limit' => isset( $args['strict_limit'] ) ? $args['strict_limit'] : true,
	) ) );

	/**
	 * Filter to create a custom HTML output
	 *
	 * @since 2.2.3
	 *
	 * @param	mixed              Default return value
	 * @param	array   $results   Array of IDs of related posts
	 * @param	array   $args      Array of settings
	 * @return	string             Custom HTML formatted list of related posts
	 */
	$custom_template = apply_filters( 'crp_custom_template', null, $results, $args );
	if ( ! empty( $custom_template ) ) {
		if ( ! empty( $args['cache'] ) ) {
			update_post_meta( $post->ID, $meta_key, $custom_template, '' );
		}
		return $custom_template;
	}

	$widget_class = $args['is_widget'] ? 'crp_related_widget' : 'crp_related ';
	$shortcode_class = $args['is_shortcode'] ? 'crp_related_shortcode ' : '';

	$post_classes = $widget_class . $shortcode_class;

	/**
	 * Filter the classes added to the div wrapper of the Contextual Related Posts.
	 *
	 * @since	2.2.3
	 *
	 * @param	string   $post_classes	Post classes string.
	 */
	$post_classes = apply_filters( 'crp_post_class', $post_classes );

	$output = '<div class="' . $post_classes . '">';

	if ( $results ) {
		$loop_counter = 0;

		$output .= crp_heading_title( $args );

		$output .= crp_before_list( $args );

		// We need this for WPML support.
		$processed_results = array();

		foreach ( $results as $result ) {

			/* Support WPML */
		    $resultid = crp_object_id_cur_lang( $result->ID );

			// If this is NULL or already processed ID or matches current post then skip processing this loop.
			if ( ! $resultid || in_array( $resultid, $processed_results ) || intval( $resultid ) === intval( $post->ID ) ) {
			    continue;
			}

			// Push the current ID into the array to ensure we're not repeating it.
			array_push( $processed_results, $resultid );

			/**
			 * Filter the post ID for each result. Allows a custom function to hook in and change the ID if needed.
			 *
			 * @since	1.9
			 *
			 * @param	int	$resultid	ID of the post
			 */
			$resultid = apply_filters( 'crp_post_id', $resultid );

			$result = get_post( $resultid );	// Let's get the Post using the ID.

			// Process the category exclusion if passed in the shortcode.
			if ( isset( $exclude_categories ) ) {

				$categorys = get_the_category( $result->ID );	// Fetch categories of the plugin.

				$p_in_c = false;	// Variable to check if post exists in a particular category
				foreach ( $categorys as $cat ) {	// Loop to check if post exists in excluded category.
					$p_in_c = ( in_array( $cat->cat_ID, $exclude_categories ) ) ? true : false;
					if ( $p_in_c ) {
						break;	// Skip loop execution and go to the next step.
					}
				}
				if ( $p_in_c ) { continue;	// Skip loop execution and go to the next step.
				}
			}

			$output .= crp_before_list_item( $args, $result );

			$output .= crp_list_link( $args, $result );

			if ( $args['show_author'] ) {
				$output .= crp_author( $args, $result );
			}

			if ( $args['show_date'] ) {
				$output .= '<span class="crp_date"> ' . mysql2date( get_option( 'date_format', 'd/m/y' ), $result->post_date ) . '</span> ';
			}

			if ( $args['show_excerpt'] ) {
				$output .= '<span class="crp_excerpt"> ' . crp_excerpt( $result->ID, $args['excerpt_length'] ) . '</span>';
			}

			$loop_counter++;

			$output .= crp_after_list_item( $args, $result );

			if ( $loop_counter == $args['limit'] ) {
				break;	// End loop when related posts limit is reached.
			}
		} //end of foreach loop

		if ( $args['show_credit'] ) {

			$output .= crp_before_list_item( $args, $result );

			$output .= sprintf( __( 'Powered by <a href="%s" rel="nofollow">Contextual Related Posts</a>', 'contextual-related-posts' ), esc_url( 'https://webberzone.com/plugins/contextual-related-posts/' ) );

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

	// Check if the opening list tag is missing in the output, it means all of our results were eliminated cause of the category filter.
	if ( false === ( strpos( $output, $args['before_list_item'] ) ) ) {
		$output = '<div id="crp_related">';
		$output .= ( $args['blank_output'] ) ? ' ' : '<p>' . $args['blank_output_text'] . '</p>';
	}

	$output .= '</div>'; // Closing div of 'crp_related'.

	// Support caching to speed up retrieval.
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
 * @param array $args Arguments array.
 * @return object $results
 */
function get_crp_posts_id( $args = array() ) {
	global $wpdb, $post, $crp_settings;

	// Initialise some variables.
	$fields = '';
	$where = '';
	$join = '';
	$groupby = '';
	$orderby = '';
	$having = '';
	$limits = '';
	$match_fields = '';

	$defaults = array(
		'postid' => false,	// Get related posts for a specific post ID.
		'strict_limit' => true,	// If this is set to false, then it will fetch 5x posts.
		'offset' => 0,	// Offset the related posts returned by this number.
	);
	$defaults = array_merge( $defaults, $crp_settings );

	// Parse incoming $args into an array and merge it with $defaults.
	$args = wp_parse_args( $args, $defaults );

	// Fix the thumb size in case it is missing.
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

	$source_post = ( empty( $args['postid'] ) ) ? $post : get_post( $args['postid'] );

	$limit = ( $args['strict_limit'] ) ? $args['limit'] : ( $args['limit'] * 3 );
	$offset = isset( $args['offset'] ) ? $args['offset'] : 0;

	// Save post types in $post_types variable.
	parse_str( $args['post_types'], $post_types );

	/**
	 * Filter the post_type clause of the query.
	 *
	 * @since 2.2.0
	 *
	 * @param array  $post_types  Array of post types to filter by
	 * @param int    $source_post->ID    Post ID
	 */
	$post_types = apply_filters( 'crp_posts_post_types', $post_types, $source_post->ID );

	// Are we matching only the title or the post content as well?
	$match_fields = array(
		'post_title',
	);

	$match_fields_content = array(
		$source_post->post_title,
	);

	if ( $args['match_content'] ) {

		$match_fields[] = 'post_content';
		$match_fields_content[] = crp_excerpt( $source_post->ID, $args['match_content_words'], false );
	}

	/**
	 * Filter the fields that are to be matched.
	 *
	 * @since	2.2.0
	 *
	 * @param array   $match_fields	Array of fields to be matched
	 * @param int	   $source_post->ID	Post ID
	 */
	$match_fields = apply_filters( 'crp_posts_match_fields', $match_fields, $source_post->ID );

	/**
	 * Filter the content of the fields that are to be matched.
	 *
	 * @since	2.2.0
	 *
	 * @param array	$match_fields_content	Array of content of fields to be matched
	 * @param int	$source_post->ID	Post ID
	 */
	$match_fields_content = apply_filters( 'crp_posts_match_fields_content', $match_fields_content, $source_post->ID );

	// Convert our arrays into their corresponding strings after they have been filtered.
	$match_fields = implode( ',', $match_fields );
	$stuff = implode( ' ', $match_fields_content );

	// Make sure the post is not from the future.
	$time_difference = get_option( 'gmt_offset' );
	$now = gmdate( 'Y-m-d H:i:s', ( time() + ( $time_difference * 3600 ) ) );

	// Limit the related posts by time.
	$current_time = current_time( 'timestamp', 0 );
	$from_date = $current_time - ( $args['daily_range'] * DAY_IN_SECONDS );
	$from_date = gmdate( 'Y-m-d H:i:s' , $from_date );

	// Create the SQL query to fetch the related posts from the database.
	if ( is_int( $source_post->ID ) ) {

		// Fields to return.
		$fields = " $wpdb->posts.ID ";

		// Create the base MATCH clause.
		$match = $wpdb->prepare( ' AND MATCH (' . $match_fields . ") AGAINST ('%s') ", $stuff );

		/**
		 * Filter the MATCH clause of the query.
		 *
		 * @since	2.1.0
		 *
		 * @param string   $match  		The MATCH section of the WHERE clause of the query
		 * @param string   $stuff  		String to match fulltext with
		 * @param int	   $source_post->ID	Post ID
		 */
		$match = apply_filters( 'crp_posts_match', $match, $stuff, $source_post->ID );

		// Create the maximum date limit. Show posts before today.
		$now_clause = $wpdb->prepare( " AND $wpdb->posts.post_date < '%s' ", $now );

		/**
		 * Filter the Maximum date clause of the query.
		 *
		 * @since	2.1.0
		 *
		 * @param string   $now_clause  The Maximum date of the WHERE clause of the query.
		 * @param int	   $source_post->ID	Post ID
		 */
		$now_clause = apply_filters( 'crp_posts_now_date', $now_clause, $source_post->ID );

		// Create the minimum date limit. Show posts after the date specified.
		$from_clause = ( 0 == $args['daily_range'] ) ? '' : $wpdb->prepare( " AND $wpdb->posts.post_date >= '%s' ", $from_date );

		/**
		 * Filter the Maximum date clause of the query.
		 *
		 * @since	2.1.0
		 *
		 * @param string   $from_clause  The Minimum date of the WHERE clause of the query.
		 * @param int	   $source_post->ID	Post ID
		 */
		$from_clause = apply_filters( 'crp_posts_from_date', $from_clause, $source_post->ID );

		// Create the base WHERE clause.
		$where = $match;
		$where .= $now_clause;
		$where .= $from_clause;
		$where .= " AND $wpdb->posts.post_status = 'publish' ";					// Only show published posts
		$where .= $wpdb->prepare( " AND {$wpdb->posts}.ID != %d ", $source_post->ID );	// Show posts after the date specified.

		// Convert exclude post IDs string to array so it can be filtered
		$exclude_post_ids = explode( ',', $args['exclude_post_ids'] );

		/**
		 * Filter exclude post IDs array.
		 *
		 * @since 2.3.0
		 *
		 * @param array   $exclude_post_ids  Array of post IDs.
		 */
		$exclude_post_ids = apply_filters( 'crp_exclude_post_ids', $exclude_post_ids );

		// Convert it back to string
		$exclude_post_ids = implode( ',', array_filter( $exclude_post_ids ) );

		if ( '' != $exclude_post_ids ) {
			$where .= " AND $wpdb->posts.ID NOT IN ({$exclude_post_ids}) ";
		}

		$where .= " AND $wpdb->posts.post_type IN ('" . join( "', '", $post_types ) . "') ";	// Array of post types.

		// Create the base LIMITS clause.
		$limits .= $wpdb->prepare( ' LIMIT %d, %d ', $offset, $limit );

		/**
		 * Filter the SELECT clause of the query.
		 *
		 * @since	2.0.0
		 *
		 * @param string   $fields  The SELECT clause of the query.
		 * @param int	   $source_post->ID	Post ID
		 */
		$fields = apply_filters( 'crp_posts_fields', $fields, $source_post->ID );

		/**
		 * Filter the JOIN clause of the query.
		 *
		 * @since	2.0.0
		 *
		 * @param string   $join  The JOIN clause of the query.
		 * @param int	   $source_post->ID	Post ID
		 */
			$join = apply_filters( 'crp_posts_join', $join, $source_post->ID );

		/**
		 * Filter the WHERE clause of the query.
		 *
		 * @since	2.0.0
		 *
		 * @param string   $where  The WHERE clause of the query.
		 * @param int	   $source_post->ID	Post ID
		 */
		$where = apply_filters( 'crp_posts_where', $where, $source_post->ID );

		/**
		 * Filter the GROUP BY clause of the query.
		 *
		 * @since	2.0.0
		 *
		 * @param string   $groupby  The GROUP BY clause of the query.
		 * @param int	   $source_post->ID	Post ID
		 */
		$groupby = apply_filters( 'crp_posts_groupby', $groupby, $source_post->ID );

		/**
		 * Filter the HAVING clause of the query.
		 *
		 * @since	2.2.0
		 *
		 * @param string  $having  The HAVING clause of the query.
		 * @param int	    $source_post->ID	Post ID
		 */
		$having = apply_filters( 'crp_posts_having', $having, $source_post->ID );

		/**
		 * Filter the ORDER BY clause of the query.
		 *
		 * @since	2.0.0
		 *
		 * @param string   $orderby  The ORDER BY clause of the query.
		 * @param int	   $source_post->ID	Post ID
		 */
		$orderby = apply_filters( 'crp_posts_orderby', $orderby, $source_post->ID );

		/**
		 * Filter the LIMIT clause of the query.
		 *
		 * @since	2.0.0
		 *
		 * @param string   $limits  The LIMIT clause of the query.
		 * @param int	   $source_post->ID	Post ID
		 */
		$limits = apply_filters( 'crp_posts_limits', $limits, $source_post->ID );

		if ( ! empty( $groupby ) ) {
			$groupby = 'GROUP BY ' . $groupby;
		}

		if ( ! empty( $having ) ) {
			$having = 'HAVING ' . $having;
		}

		if ( ! empty( $orderby ) ) {
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
 */
function crp_content_prepare_filter() {
	global $crp_settings;

	$priority = isset( $crp_settings['content_filter_priority'] ) ? $crp_settings['content_filter_priority'] : 10;

	add_filter( 'the_content', 'crp_content_filter', $priority );
}
add_action( 'template_redirect', 'crp_content_prepare_filter' );


/**
 * Filter for 'the_content' to add the related posts.
 *
 * @since 1.0.1
 *
 * @param string $content Post content.
 * @return string After the filter has been processed
 */
function crp_content_filter( $content ) {

	global $post, $crp_settings;

	// Return if it's not in the loop or in the main query.
	if ( ! in_the_loop() && ! is_main_query() ) {
		return $content;
	}

	// If this post ID is in the DO NOT DISPLAY list.
	$exclude_on_post_ids = explode( ',', $crp_settings['exclude_on_post_ids'] );
	if ( in_array( $post->ID, $exclude_on_post_ids ) ) {
		return $content;	// Exit without adding related posts.
	}
	// If this post type is in the DO NOT DISPLAY list
	parse_str( $crp_settings['exclude_on_post_types'], $exclude_on_post_types );	// Save post types in $exclude_on_post_types variable.
	if ( in_array( $post->post_type, $exclude_on_post_types ) ) {
		return $content;	// Exit without adding related posts.
	}
	// If the DO NOT DISPLAY meta field is set.
	$crp_post_meta = get_post_meta( $post->ID, 'crp_post_meta', true );

	if ( isset( $crp_post_meta['crp_disable_here'] ) ) {
		$crp_disable_here = $crp_post_meta['crp_disable_here'];
	} else {
		$crp_disable_here = 0;
	}

	if ( $crp_disable_here ) {
		return $content;
	}

	// Else add the content.
	if ( ( ( is_single() ) && ( $crp_settings['add_to_content'] ) ) ||
	( ( is_page() ) && ( $crp_settings['add_to_page'] ) ) ||
	( ( is_home() ) && ( $crp_settings['add_to_home'] ) ) ||
	( ( is_category() ) && ( $crp_settings['add_to_category_archives'] ) ) ||
	( ( is_tag() ) && ( $crp_settings['add_to_tag_archives'] ) ) ||
	( ( ( is_tax() ) || ( is_author() ) || ( is_date() ) ) && ( $crp_settings['add_to_archives'] ) ) ) {

		$crp_code = get_crp( 'is_widget=0' );

		return crp_generate_content( $content, $crp_code );

	} else {
		return $content;
	}
}


/**
 * Helper for inserting crp code into or alongside content
 *
 * @since 2.3.0
 *
 * @param string $content Post content.
 * @param string $crp_code	CRP generated code.
 * @return string After the filter has been processed
 */
function crp_generate_content( $content, $crp_code ) {
	global $crp_settings;

	if ( -1 === (int) $crp_settings['insert_after_paragraph'] || ! is_numeric( $crp_settings['insert_after_paragraph'] ) ) {
		return $content . $crp_code;
	} elseif ( 0 === (int) $crp_settings['insert_after_paragraph'] ) {
		return $crp_code . $content;
	} else {
		return crp_insert_after_paragraph( $content, $crp_code, $crp_settings['insert_after_paragraph'] );
	}

}

/**
 * Helper for inserting code after a closing paragraph tag
 *
 * @since 2.3.0
 *
 * @param string $content Post content.
 * @param string $crp_code	CRP generated code.
 * @param string $paragraph_id Paragraph number to insert after.
 * @return string After the filter has been processed
 */
function crp_insert_after_paragraph( $content, $crp_code, $paragraph_id ) {
	$closing_p = '</p>';
	$paragraphs = explode( $closing_p, $content );

	if ( count( $paragraphs ) >= $paragraph_id ) {
		foreach ( $paragraphs as $index => $paragraph ) {

			if ( trim( $paragraph ) ) {
				$paragraphs[ $index ] .= $closing_p;
			}

			if ( (int) $paragraph_id === $index + 1 ) {
				$paragraphs[ $index ] .= $crp_code;
			}
		}

		return implode( '', $paragraphs );
	}

	return $content . $crp_code;
}

/**
 * Filter to add related posts to feeds.
 *
 * @since 1.8.4
 *
 * @param	string $content Post content.
 * @return	string	Formatted content
 */
function crp_rss_filter( $content ) {
	global $crp_settings;

	$limit_feed = $crp_settings['limit_feed'];
	$show_excerpt_feed = $crp_settings['show_excerpt_feed'];
	$post_thumb_op_feed = $crp_settings['post_thumb_op_feed'];

	if ( $crp_settings['add_to_feed'] ) {
		$output = $content;
		$output .= get_crp( 'is_widget=0&limit=' . $limit_feed . '&show_excerpt=' . $show_excerpt_feed . '&post_thumb_op=' . $post_thumb_op_feed );
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
 * @param string $args Array of arguments to control the output.
 */
function echo_crp( $args = array() ) {

	$defaults = array(
		'is_manual' => true,
	);

	// Parse incomming $args into an array and merge it with $defaults.
	$args = wp_parse_args( $args, $defaults );

	echo get_crp( $args ); // WPCS: XSS ok.
}


/**
 * Enqueue styles.
 *
 * @since 1.9
 */
function crp_heading_styles() {
	global $crp_settings;

	if ( 'rounded_thumbs' == $crp_settings['crp_styles'] ) {
		wp_register_style( 'crp-style-rounded-thumbs', plugins_url( 'css/default-style.css', CRP_PLUGIN_FILE ) );
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
  width: " . ( $crp_settings['thumb_width'] ) . 'px;
}
                ';

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

	$title = __( '<h3>Related Posts:</h3>', 'contextual-related-posts' );

	$blank_output_text = __( 'No related posts found', 'contextual-related-posts' );

	$thumb_default = plugins_url( 'default.png' , __FILE__ );

	// Set default post types to post and page.
	$post_types = array(
		'post' => 'post',
		'page' => 'page',
	);
	$post_types	= http_build_query( $post_types, '', '&' );

	$crp_settings = array(
		// General options.
		'cache' => false,			// Cache output for faster page load.

		'add_to_content' => true,		// Add related posts to content (only on single posts).
		'add_to_page' => true,		// Add related posts to content (only on single pages).
		'add_to_feed' => false,		// Add related posts to feed (full).
		'add_to_home' => false,		// Add related posts to home page.
		'add_to_category_archives' => false,		// Add related posts to category archives.
		'add_to_tag_archives' => false,		// Add related posts to tag archives.
		'add_to_archives' => false,		// Add related posts to other archives.

		'content_filter_priority' => 10,	// Content priority.
		'insert_after_paragraph' => -1,	// Insert after paragraph number.
		'show_metabox'	=> true,	// Show metabox to admins.
		'show_metabox_admins'	=> false,	// Limit to admins as well.

		'show_credit' => false,		// Link to this plugin's page?

		// List tuning options.
		'limit' => '6',				// How many posts to display?
		'daily_range' => '1095',				// How old posts should be displayed?

		'match_content' => true,		// Match against post content as well as title.
		'match_content_words' => '0',	// How many characters of content should be matched? 0 for all chars.

		'post_types' => $post_types,		// WordPress custom post types.

		'exclude_categories' => '',	// Exclude these categories.
		'exclude_cat_slugs' => '',	// Exclude these categories (slugs).
		'exclude_post_ids' => '',	// Comma separated list of page / post IDs that are to be excluded in the results.

		// Output options.
		'title' => $title,			// Add before the content.
		'blank_output' => true,		// Blank output?
		'blank_output_text' => $blank_output_text,		// Blank output text.

		'show_excerpt' => false,			// Show post excerpt in list item.
		'show_date' => false,			// Show date in list item.
		'show_author' => false,			// Show author in list item.
		'excerpt_length' => '10',		// Length of characters.
		'title_length' => '60',		// Limit length of post title.

		'link_new_window' => false,			// Open link in new window - Includes target="_blank" to links.
		'link_nofollow' => false,			// Includes rel="nofollow" to links.

		'before_list' => '<ul>',	// Before the entire list.
		'after_list' => '</ul>',	// After the entire list.
		'before_list_item' => '<li>',	// Before each list item.
		'after_list_item' => '</li>',	// After each list item.

		'exclude_on_post_ids' => '', 	// Comma separate list of page/post IDs to not display related posts on.
		'exclude_on_post_types' => '',		// WordPress custom post types.

		// Thumbnail options.
		'post_thumb_op' => 'inline',	// Default option to display text and no thumbnails in posts.
		'thumb_size' => 'thumbnail',	// Default thumbnail size
		'thumb_height' => '150',	// Height of thumbnails.
		'thumb_width' => '150',	// Width of thumbnails.
		'thumb_crop' => true,		// Crop mode. default is hard crop.
		'thumb_html' => 'html',		// Use HTML or CSS for width and height of the thumbnail?
		'thumb_meta' => 'post-image',	// Meta field that is used to store the location of default thumbnail image.
		'scan_images' => true,			// Scan post for images.
		'thumb_default' => $thumb_default,	// Default thumbnail image.
		'thumb_default_show' => true,	// Show default thumb if none found (if false, don't show thumb at all).

		// Feed options.
		'limit_feed' => '5',				// How many posts to display in feeds.
		'post_thumb_op_feed' => 'text_only',	// Default option to display text and no thumbnails in Feeds.
		'thumb_height_feed' => '50',	// Height of thumbnails in feed.
		'thumb_width_feed' => '50',	// Width of thumbnails in feed.
		'show_excerpt_feed' => false,			// Show description in list item in feed.

		// Custom styles.
		'custom_CSS' => '',			// Custom CSS to style the output.
		'include_default_style' => true,	// Include default style - Will be DEPRECATED in the next version.
		'crp_styles'	=> 'rounded_thumbs',// Defaault style is rounded thubnails.
	);

	/**
	 * Filters the default options array.
	 *
	 * @since	1.9.1
	 *
	 * @param	array	$crp_settings	Default options.
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

	$crp_settings = array_map( 'stripslashes', (array) get_option( 'ald_crp_settings' ) );
	unset( $crp_settings[0] ); // Produced by the (array) casting when there's nothing in the DB.

	foreach ( $defaults as $k => $v ) {
		if ( ! isset( $crp_settings[ $k ] ) ) {
			$crp_settings[ $k ] = $v;
		}
		$crp_settings_changed = true;
	}
	if ( true == $crp_settings_changed ) {
		update_option( 'ald_crp_settings', $crp_settings );
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
 */
function crp_header() {
	global $crp_settings;

	$custom_css = stripslashes( $crp_settings['custom_CSS'] );

	// Add CSS to header.
	if ( '' != $custom_css ) {
	    if ( ( is_single() ) ) {
			echo '<style type="text/css">' . $custom_css . '</style>'; // WPCS: XSS ok.
	    } elseif ( (is_page()) ) {
			echo '<style type="text/css">' . $custom_css . '</style>'; // WPCS: XSS ok.
	    } elseif ( ( is_home() ) && ( $crp_settings['add_to_home'] ) ) {
			echo '<style type="text/css">' . $custom_css . '</style>'; // WPCS: XSS ok.
	    } elseif ( ( is_category() ) && ( $crp_settings['add_to_category_archives'] ) ) {
			echo '<style type="text/css">' . $custom_css . '</style>'; // WPCS: XSS ok.
	    } elseif ( ( is_tag() ) && ( $crp_settings['add_to_tag_archives'] ) ) {
			echo '<style type="text/css">' . $custom_css . '</style>'; // WPCS: XSS ok.
	    } elseif ( ( ( is_tax() ) || ( is_author() ) || ( is_date() ) ) && ( $crp_settings['add_to_archives'] ) ) {
			echo '<style type="text/css">' . $custom_css . '</style>'; // WPCS: XSS ok.
	    } elseif ( is_active_widget( false, false, 'CRP_Widget', true ) ) {
			echo '<style type="text/css">' . $custom_css . '</style>'; // WPCS: XSS ok.
	    }
	}
}
add_action( 'wp_head', 'crp_header' );


/*
 ----------------------------------------------------------------------------*
 * Activate the plugin
 *----------------------------------------------------------------------------
 */

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-name-activator.php
 *
 * @since 2.2.0
 *
 * @param bool $network_wide Network wide flag.
 */
function activate_crp( $network_wide ) {
	require_once( CRP_PLUGIN_DIR . 'includes/plugin-activator.php' );
	crp_activate( $network_wide );
}
register_activation_hook( CRP_PLUGIN_FILE, 'activate_crp' );


/**
 * Fired when a new site is activated with a WPMU environment.
 *
 * @since 2.0.0
 *
 * @param    int $blog_id    ID of the new blog.
 */
function crp_activate_new_site( $blog_id ) {

	if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
		return;
	}

	require_once( CRP_PLUGIN_DIR . 'includes/plugin-activator.php' );

	switch_to_blog( $blog_id );
	crp_single_activate();
	restore_current_blog();

}
add_action( 'wpmu_new_blog', 'crp_activate_new_site' );


/*
 ----------------------------------------------------------------------------*
 * WordPress widget
 *----------------------------------------------------------------------------
 */

/**
 * Initialise the widget.
 *
 * @since 1.9.1
 */
function register_crp_widget() {
	require_once( CRP_PLUGIN_DIR . 'includes/modules/class-crp-widget.php' );

	register_widget( 'CRP_Widget' );
}
add_action( 'widgets_init', 'register_crp_widget' );


/*
 ----------------------------------------------------------------------------*
 * CRP modules & includes
 *----------------------------------------------------------------------------
 */

require_once( CRP_PLUGIN_DIR . 'includes/i10n.php' );
require_once( CRP_PLUGIN_DIR . 'includes/output-generator.php' );
require_once( CRP_PLUGIN_DIR . 'includes/media.php' );
require_once( CRP_PLUGIN_DIR . 'includes/tools.php' );
require_once( CRP_PLUGIN_DIR . 'includes/modules/manual-posts.php' );
require_once( CRP_PLUGIN_DIR . 'includes/modules/shortcode.php' );
require_once( CRP_PLUGIN_DIR . 'includes/modules/taxonomies.php' );
require_once( CRP_PLUGIN_DIR . 'includes/modules/exclusions.php' );


/*
 ----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------
 */

if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {

	require_once( CRP_PLUGIN_DIR . 'admin/admin.php' );
	require_once( CRP_PLUGIN_DIR . 'admin/loader.php' );
	require_once( CRP_PLUGIN_DIR . 'admin/metabox.php' );
	require_once( CRP_PLUGIN_DIR . 'admin/cache.php' );

} // End admin.inc


/*
 ----------------------------------------------------------------------------*
 * Deprecated functions
 *----------------------------------------------------------------------------
 */

require_once( CRP_PLUGIN_DIR . 'includes/deprecated.php' );


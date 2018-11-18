<?php
/**
 * Functions related to the header
 *
 * @package   Contextual_Related_Posts
 * @author    Ajay D'Souza
 * @license   GPL-2.0+
 * @link      https://webberzone.com
 * @copyright 2009-2018 Ajay D'Souza
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

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
		$exclude_categories   = explode( ',', $args['exclude_categories'] );
		$args['strict_limit'] = false;
	}
	$defaults = array(
		'is_widget'    => false,
		'is_shortcode' => false,
		'is_manual'    => false,
		'echo'         => true,
		'heading'      => true,
		'offset'       => 0,
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
	$results = get_crp_posts_id(
		array_merge(
			$args,
			array(
				'postid'       => $post->ID,
				'strict_limit' => isset( $args['strict_limit'] ) ? $args['strict_limit'] : true,
			)
		)
	);

	/**
	 * Filter to create a custom HTML output
	 *
	 * @since 2.2.3
	 *
	 * @param   mixed              Default return value
	 * @param   array   $results   Array of IDs of related posts
	 * @param   array   $args      Array of settings
	 * @return  string             Custom HTML formatted list of related posts
	 */
	$custom_template = apply_filters( 'crp_custom_template', null, $results, $args );
	if ( ! empty( $custom_template ) ) {
		if ( ! empty( $args['cache'] ) ) {
			update_post_meta( $post->ID, $meta_key, $custom_template, '' );
		}
		return $custom_template;
	}

	$widget_class    = $args['is_widget'] ? 'crp_related_widget' : 'crp_related ';
	$shortcode_class = $args['is_shortcode'] ? 'crp_related_shortcode ' : '';

	$post_classes = $widget_class . $shortcode_class;

	/**
	 * Filter the classes added to the div wrapper of the Contextual Related Posts.
	 *
	 * @since   2.2.3
	 *
	 * @param   string   $post_classes  Post classes string.
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
			 * @since   1.9
			 *
			 * @param   int $resultid   ID of the post
			 */
			$resultid = apply_filters( 'crp_post_id', $resultid );

			$result = get_post( $resultid );    // Let's get the Post using the ID.

			// Process the category exclusion if passed in the shortcode.
			if ( isset( $exclude_categories ) ) {

				$categorys = get_the_category( $result->ID );   // Fetch categories of the plugin.

				$p_in_c = false;    // Variable to check if post exists in a particular category.
				foreach ( $categorys as $cat ) {    // Loop to check if post exists in excluded category.
					$p_in_c = ( in_array( $cat->cat_ID, $exclude_categories, true ) ) ? true : false;
					if ( $p_in_c ) {
						break;  // Skip loop execution and go to the next step.
					}
				}
				if ( $p_in_c ) {
					continue;  // Skip loop execution and go to the next step.
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

			if ( absint( $args['limit'] ) === $loop_counter ) {
				break;  // End loop when related posts limit is reached.
			}
		} // End foreach.

		if ( $args['show_credit'] ) {

			$output .= crp_before_list_item( $args, $result );

			/* translators: Link to plugin home page */
			$output .= sprintf( __( 'Powered by <a href="%s" rel="nofollow">Contextual Related Posts</a>', 'contextual-related-posts' ), esc_url( 'https://webberzone.com/plugins/contextual-related-posts/' ) );

			$output .= crp_after_list_item( $args, $result );

		}

		$output .= crp_after_list( $args );

		$clearfix = '<div class="crp_clear"></div>';

		/**
		 * Filter the clearfix div tag. This is included after the closing tag to clear any miscellaneous floating elements;
		 *
		 * @since   2.0.0
		 *
		 * @param   string  $clearfix   Contains: <div style="clear:both"></div>
		 */
		$output .= apply_filters( 'crp_clearfix', $clearfix );

	} else {
		$output .= ( $args['blank_output'] ) ? ' ' : '<p>' . $args['blank_output_text'] . '</p>';
	}// End if.

	// Check if the opening list tag is missing in the output, it means all of our results were eliminated cause of the category filter.
	if ( false === ( strpos( $output, $args['before_list_item'] ) ) ) {
		$output  = '<div id="crp_related">';
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
	 * @since   1.9.1
	 *
	 * @param   string  $output Formatted list of related posts
	 * @param   array   $args   Complete set of arguments
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
	$fields       = '';
	$where        = '';
	$join         = '';
	$groupby      = '';
	$orderby      = '';
	$having       = '';
	$limits       = '';
	$match_fields = '';

	$defaults = array(
		'postid'       => false,  // Get related posts for a specific post ID.
		'strict_limit' => true, // If this is set to false, then it will fetch 5x posts.
		'offset'       => 0,  // Offset the related posts returned by this number.
	);
	$defaults = array_merge( $defaults, $crp_settings );

	// Parse incoming $args into an array and merge it with $defaults.
	$args = wp_parse_args( $args, $defaults );

	// Fix the thumb size in case it is missing.
	$crp_thumb_size = crp_get_all_image_sizes( $args['thumb_size'] );

	if ( isset( $crp_thumb_size['width'] ) ) {
		$thumb_width  = $crp_thumb_size['width'];
		$thumb_height = $crp_thumb_size['height'];
	}

	if ( empty( $thumb_width ) ) {
		$thumb_width = $crp_settings['thumb_width'];
	}

	if ( empty( $thumb_height ) ) {
		$thumb_height = $crp_settings['thumb_height'];
	}

	$source_post = ( empty( $args['postid'] ) ) ? $post : get_post( $args['postid'] );

	if ( ! $source_post ) {
		$source_post = $post;
	}

	$limit  = ( $args['strict_limit'] ) ? $args['limit'] : ( $args['limit'] * 3 );
	$offset = isset( $args['offset'] ) ? $args['offset'] : 0;

	// If post_types is empty or contains a query string then use parse_str else consider it comma-separated.
	if ( ! empty( $args['post_types'] ) && false === strpos( $args['post_types'], '=' ) ) {
		$post_types = explode( ',', $args['post_types'] );
	} else {
		parse_str( $args['post_types'], $post_types );  // Save post types in $post_types variable.
	}

	// If post_types is empty or if we want all the post types.
	if ( empty( $post_types ) || 'all' === $args['post_types'] ) {
		$post_types = get_post_types(
			array(
				'public' => true,
			)
		);
	}

	// If we only want posts from the same post type.
	if ( $args['same_post_type'] ) {
		$post_types = (array) $source_post->post_type;
	}

	/**
	 * Filter the post_type clause of the query.
	 *
	 * @since 2.2.0
	 *
	 * @param array  $post_types Array of post types to filter by.
	 * @param int    $source_post->ID Post ID.
	 * @param array  $args Arguments array.
	 */
	$post_types = apply_filters( 'crp_posts_post_types', $post_types, $source_post->ID, $args );

	// Are we matching only the title or the post content as well?
	$match_fields = array(
		'post_title',
	);

	$match_fields_content = array(
		$source_post->post_title,
	);

	if ( $args['match_content'] ) {
		$match_fields[]         = 'post_content';
		$match_fields_content[] = crp_excerpt( $source_post->ID, $args['match_content_words'], false );
	}

	/**
	 * Filter the fields that are to be matched.
	 *
	 * @since   2.2.0
	 *
	 * @param array   $match_fields Array of fields to be matched
	 * @param int      $source_post->ID Post ID
	 */
	$match_fields = apply_filters( 'crp_posts_match_fields', $match_fields, $source_post->ID );

	/**
	 * Filter the content of the fields that are to be matched.
	 *
	 * @since   2.2.0
	 *
	 * @param array $match_fields_content   Array of content of fields to be matched
	 * @param int   $source_post->ID    Post ID
	 */
	$match_fields_content = apply_filters( 'crp_posts_match_fields_content', $match_fields_content, $source_post->ID );

	// Convert our arrays into their corresponding strings after they have been filtered.
	$match_fields = implode( ',', $match_fields );
	$stuff        = implode( ' ', $match_fields_content );

	// Make sure the post is not from the future.
	$time_difference = get_option( 'gmt_offset' );
	$now             = gmdate( 'Y-m-d H:i:s', ( time() + ( $time_difference * 3600 ) ) );

	// Limit the related posts by time.
	$current_time = current_time( 'timestamp', 0 );
	$from_date    = $current_time - ( absint( $args['daily_range'] ) * DAY_IN_SECONDS );
	$from_date    = gmdate( 'Y-m-d H:i:s', $from_date );

	// Create the SQL query to fetch the related posts from the database.
	if ( is_int( $source_post->ID ) ) {

		// Fields to return.
		$fields = " $wpdb->posts.ID ";

		// Create the base MATCH clause.
		$match = $wpdb->prepare( ' AND MATCH (' . $match_fields . ') AGAINST (%s) ', $stuff ); // WPCS: unprepared SQL ok.

		/**
		 * Filter the MATCH clause of the query.
		 *
		 * @since   2.1.0
		 *
		 * @param string   $match       The MATCH section of the WHERE clause of the query
		 * @param string   $stuff       String to match fulltext with
		 * @param int      $source_post->ID Post ID
		 */
		$match = apply_filters( 'crp_posts_match', $match, $stuff, $source_post->ID );

		// Create the maximum date limit. Show posts before today.
		$now_clause = $wpdb->prepare( " AND $wpdb->posts.post_date < %s ", $now );

		/**
		 * Filter the Maximum date clause of the query.
		 *
		 * @since   2.1.0
		 *
		 * @param string   $now_clause  The Maximum date of the WHERE clause of the query.
		 * @param int      $source_post->ID Post ID
		 */
		$now_clause = apply_filters( 'crp_posts_now_date', $now_clause, $source_post->ID );

		// Create the minimum date limit. Show posts after the date specified.
		$from_clause = ( 0 === absint( $args['daily_range'] ) ) ? '' : $wpdb->prepare( " AND $wpdb->posts.post_date >= %s ", $from_date );

		/**
		 * Filter the Maximum date clause of the query.
		 *
		 * @since   2.1.0
		 *
		 * @param string   $from_clause  The Minimum date of the WHERE clause of the query.
		 * @param int      $source_post->ID Post ID
		 */
		$from_clause = apply_filters( 'crp_posts_from_date', $from_clause, $source_post->ID );

		// Create the base WHERE clause.
		$where  = $match;
		$where .= $now_clause;
		$where .= $from_clause;
		$where .= " AND $wpdb->posts.post_status = 'publish' ";                 // Only show published posts.
		$where .= $wpdb->prepare( " AND {$wpdb->posts}.ID != %d ", $source_post->ID );  // Show posts after the date specified.

		// Convert exclude post IDs string to array so it can be filtered.
		$exclude_post_ids = explode( ',', $args['exclude_post_ids'] );

		/**
		 * Filter exclude post IDs array.
		 *
		 * @since 2.3.0
		 *
		 * @param array   $exclude_post_ids  Array of post IDs.
		 */
		$exclude_post_ids = apply_filters( 'crp_exclude_post_ids', $exclude_post_ids );

		// Convert it back to string.
		$exclude_post_ids = implode( ',', array_filter( $exclude_post_ids ) );

		if ( '' != $exclude_post_ids ) {
			$where .= " AND $wpdb->posts.ID NOT IN ({$exclude_post_ids}) ";
		}

		$where .= " AND $wpdb->posts.post_type IN ('" . join( "', '", $post_types ) . "') ";    // Array of post types.

		// Create the base LIMITS clause.
		$limits .= $wpdb->prepare( ' LIMIT %d, %d ', $offset, $limit );

		/**
		 * Filter the SELECT clause of the query.
		 *
		 * @since   2.0.0
		 *
		 * @param string   $fields  The SELECT clause of the query.
		 * @param int      $source_post->ID Post ID
		 */
		$fields = apply_filters( 'crp_posts_fields', $fields, $source_post->ID );

		/**
		 * Filter the JOIN clause of the query.
		 *
		 * @since   2.0.0
		 *
		 * @param string   $join  The JOIN clause of the query.
		 * @param int      $source_post->ID Post ID
		 */
		$join = apply_filters( 'crp_posts_join', $join, $source_post->ID );

		/**
		 * Filter the WHERE clause of the query.
		 *
		 * @since   2.0.0
		 *
		 * @param string   $where  The WHERE clause of the query.
		 * @param int      $source_post->ID Post ID
		 */
		$where = apply_filters( 'crp_posts_where', $where, $source_post->ID );

		/**
		 * Filter the GROUP BY clause of the query.
		 *
		 * @since   2.0.0
		 *
		 * @param string   $groupby  The GROUP BY clause of the query.
		 * @param int      $source_post->ID Post ID
		 */
		$groupby = apply_filters( 'crp_posts_groupby', $groupby, $source_post->ID );

		/**
		 * Filter the HAVING clause of the query.
		 *
		 * @since   2.2.0
		 *
		 * @param string  $having  The HAVING clause of the query.
		 * @param int       $source_post->ID    Post ID
		 */
		$having = apply_filters( 'crp_posts_having', $having, $source_post->ID );

		/**
		 * Filter the ORDER BY clause of the query.
		 *
		 * @since   2.0.0
		 *
		 * @param string   $orderby  The ORDER BY clause of the query.
		 * @param int      $source_post->ID Post ID
		 */
		$orderby = apply_filters( 'crp_posts_orderby', $orderby, $source_post->ID );

		/**
		 * Filter the LIMIT clause of the query.
		 *
		 * @since   2.0.0
		 *
		 * @param string   $limits  The LIMIT clause of the query.
		 * @param int      $source_post->ID Post ID
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

		$results = $wpdb->get_results( $sql ); // WPCS: unprepared SQL ok.

		if ( $args['random_order'] ) {
			$results_array = (array) $results;
			shuffle( $results_array );
			$results = (object) $results_array;
		}
	} else {
		$results = false;
	}// End if.

	/**
	 * Filter object containing the post IDs.
	 *
	 * @since   1.9
	 *
	 * @param   object   $results  Object containing the related post IDs
	 */
	return apply_filters( 'get_crp_posts_id', $results );
}


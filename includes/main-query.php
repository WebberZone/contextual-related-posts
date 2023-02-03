<?php
/**
 * Functions related to the header
 *
 * @package   Contextual_Related_Posts
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

	if ( ! $post ) {
		return '';
	}

	$crp_settings = crp_get_settings();

	$defaults = array(
		'is_widget'      => false,
		'is_shortcode'   => false,
		'is_manual'      => false,
		'is_block'       => false,
		'echo'           => true,
		'heading'        => true,
		'offset'         => 0,
		'extra_class'    => '',
		'more_link_text' => '',
	);
	$defaults = array_merge( $defaults, crp_settings_defaults(), $crp_settings );

	// Parse incomming $args into an array and merge it with $defaults.
	$args = wp_parse_args( $args, $defaults );

	// Short circuit flag.
	$short_circuit = false;

	/**
	 * Allow a short circuit flag to be set to exit at this stage. Set to true to exit.
	 *
	 * @since 2.9.0
	 *
	 * @param bool   $short_circuit Short circuit filter.
	 * @param object $post          Current Post object.
	 * @param array  $args          Arguments array.
	 */
	$short_circuit = apply_filters( 'get_crp_short_circuit', $short_circuit, $post, $args );

	if ( $short_circuit ) {
		return ''; // Exit without adding related posts.
	}

	// Check exclusions.
	if ( crp_exclude_on( $post, $args ) ) {
		return ''; // Exit without adding related posts.
	}

	// WPML & PolyLang support - change strict limit to false.
	if ( class_exists( 'SitePress' ) || function_exists( 'pll_get_post' ) ) {
		$args['strict_limit'] = false;
	}

	// Support caching to speed up retrieval.
	if ( ! empty( $args['cache'] ) && empty( $args['cache_posts'] ) && ! ( is_preview() || is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) ) {
		$meta_key = crp_cache_get_key( $args );
		$output   = get_crp_cache( $post->ID, $meta_key );
		if ( $output ) {
			return $output;
		}
	}

	// Get thumbnail size.
	list( $args['thumb_width'], $args['thumb_height'] ) = crp_get_thumb_size( $args['thumb_size'] );

	// Retrieve the list of posts.
	$results = get_crp_posts(
		array_merge(
			array(
				'postid'       => $post->ID,
				'strict_limit' => isset( $args['strict_limit'] ) ? $args['strict_limit'] : true,
			),
			$args
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
		if ( ! empty( $args['cache'] ) && empty( $args['cache_posts'] ) && ! ( is_preview() || is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) ) {
			set_crp_cache( $post->ID, $meta_key, $custom_template );
		}
		return $custom_template;
	}

	if ( 'text_only' === $args['post_thumb_op'] || 'text_only' === $args['crp_styles'] ) {
		$args['crp_styles']    = 'text_only';
		$args['post_thumb_op'] = 'text_only';
	}
	$style_array = crp_get_style( $args['crp_styles'] );

	$post_classes = array(
		'main'        => 'crp_related',
		'widget'      => $args['is_widget'] ? 'crp_related_widget' : '',
		'shortcode'   => $args['is_shortcode'] ? 'crp_related_shortcode ' : '',
		'block'       => $args['is_block'] ? 'crp_related_block ' : '',
		'extra_class' => $args['extra_class'],
		'style'       => ! empty( $style_array['name'] ) ? 'crp-' . $style_array['name'] : '',
	);
	$post_classes = join( ' ', $post_classes );

	/**
	 * Filter the classes added to the div wrapper of the Contextual Related Posts.
	 *
	 * @since 2.2.3
	 * @since 2.9.3 Added $args
	 * @since 3.2.0 Added $post
	 *
	 * @param string  $post_classes Post classes string.
	 * @param array   $args         Arguments array.
	 * @param WP_Post $post         WP_Post object.
	 */
	$post_classes = apply_filters( 'crp_post_class', $post_classes, $args, $post );

	$output = '<div class="' . $post_classes . '">';

	if ( $results ) {
		$loop_counter = 0;

		$output .= crp_heading_title( $args );

		$output .= crp_before_list( $args );

		foreach ( $results as $result ) {

			$result = get_post( $result );

			$output .= crp_before_list_item( $args, $result );

			$output .= crp_list_link( $args, $result );

			if ( $args['show_author'] ) {
				$output .= crp_author( $args, $result );
			}

			if ( ! empty( $args['show_date'] ) ) {
				$output .= '<span class="crp_date"> ' . crp_date( $args, $result ) . '</span> ';
			}

			if ( ! empty( $args['show_primary_term'] ) ) {
				$post_taxonomies = get_object_taxonomies( $result );
				if ( ! empty( $post_taxonomies[0] ) ) {
					$output .= '<span class="crp_primary_term"> ' . crp_get_primary_term_name( $result, $post_taxonomies[0] ) . '</span> ';
				}
			}

			if ( ! empty( $args['show_excerpt'] ) ) {
				$output .= '<span class="crp_excerpt"> ' . crp_excerpt( $result->ID, $args['excerpt_length'], true, $args['more_link_text'] ) . '</span>';
			}

			$loop_counter++;

			$output .= crp_after_list_item( $args, $result );

			if ( absint( $args['limit'] ) === $loop_counter ) {
				break;  // End loop when related posts limit is reached.
			}
		} // End foreach.

		$output .= crp_after_list( $args );

		$clearfix = '<div class="crp_clear"></div>';

		/**
		 * Filter the clearfix div tag. This is included after the closing tag to clear any miscellaneous floating elements;
		 *
		 * @since 2.0.0
		 * @since 2.9.3 Added $args
		 *
		 * @param string $clearfix Contains: <div style="clear:both"></div>
		 * @param array  $args     Arguments array.
		 */
		$output .= apply_filters( 'crp_clearfix', $clearfix, $args );

	} else {
		$output .= ( 'blank' === $args['blank_output'] ) ? ' ' : '<p>' . $args['blank_output_text'] . '</p>';
	}// End if.

	if ( $args['show_credit'] ) {

		$output .= '<p class="crp_class_credit"><small>';

		/* translators: Link to plugin home page */
		$output .= sprintf( __( 'Powered by <a href="%s" rel="nofollow" style="float:none">Contextual Related Posts</a>', 'contextual-related-posts' ), esc_url( 'https://webberzone.com/plugins/contextual-related-posts/' ) );

		$output .= '</small></p>';

	}

	// Check if the opening list tag is missing in the output, it means all of our results were eliminated cause of the category filter.
	if ( ! empty( $args['before_list_item'] ) && false === strpos( $output, $args['before_list_item'] ) ) {
		$output  = '<div id="crp_related">';
		$output .= ( 'blank' === $args['blank_output'] ) ? ' ' : '<p>' . $args['blank_output_text'] . '</p>';
	}

	$output .= '</div>'; // Closing div of 'crp_related'.

	// Support caching to speed up retrieval.
	if ( ! empty( $args['cache'] ) && empty( $args['cache_posts'] ) && ! ( is_preview() || is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) ) {
		set_crp_cache( $post->ID, $meta_key, $output );
	}

	/**
	 * Filter the output
	 *
	 * @since   1.9.1
	 *
	 * @param   string  $output Formatted list of related posts.
	 * @param   array   $args   Arguments array.
	 */
	return apply_filters( 'get_crp', $output, $args );
}


/**
 * Fetch related posts IDs.
 *
 * @since 1.9
 * @deprecated 3.2.0
 *
 * @param array $args Arguments array.
 * @return object $results Array of related post objects
 */
function get_crp_posts_id( $args = array() ) {
	global $wpdb, $post, $crp_settings;

	_deprecated_function( __FUNCTION__, '3.2.0', 'get_crp_posts' );

	$crp_settings = crp_get_settings();

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
		'postid'       => false, // Get related posts for a specific post ID.
		'strict_limit' => true,  // If this is set to false, then it will fetch 3x posts.
		'offset'       => 0,     // Offset the related posts returned by this number.
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

	$random_order = ( $args['random_order'] || ( isset( $args['ordering'] ) && 'random' === $args['ordering'] ) ) ? true : false;

	// If we need to order randomly then set strict_limit to false.
	if ( $random_order ) {
		$args['strict_limit'] = false;
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

	/** This filter has been documented in class-crp-query.php */
	$post_types = apply_filters( 'crp_posts_post_types', $post_types, $source_post, $args );

	// Are we matching only the title or the post content as well?
	$match_fields = array(
		'post_title',
	);

	$match_fields_content = array(
		$source_post->post_title,
	);

	if ( $args['match_content'] ) {
		$match_fields[]         = 'post_content';
		$match_fields_content[] = crp_excerpt( $source_post->ID, min( $args['match_content_words'], CRP_MAX_WORDS ), false );
	}

	// If keyword is entered, override the matching content.
	$crp_post_meta = get_post_meta( $source_post->ID, 'crp_post_meta', true );

	if ( isset( $crp_post_meta['keyword'] ) ) {
		$match_fields_content = array(
			$crp_post_meta['keyword'],
		);
	}

	/** This filter has been documented in class-crp-query.php */
	$match_fields = apply_filters( 'crp_posts_match_fields', $match_fields, $source_post, $args );

	/** This filter has been documented in class-crp-query.php */
	$match_fields_content = apply_filters( 'crp_posts_match_fields_content', $match_fields_content, $source_post, $args );

	// Convert our arrays into their corresponding strings after they have been filtered.
	$match_fields = implode( ',', $match_fields );
	$stuff        = implode( ' ', $match_fields_content );

	// Make sure the post is not from the future.
	$time_difference = get_option( 'gmt_offset' );
	$now             = gmdate( 'Y-m-d H:i:s', ( time() + ( $time_difference * 3600 ) ) );

	// Limit the related posts by time.
	$current_time = strtotime( current_time( 'mysql' ) );
	$from_date    = $current_time - ( absint( $args['daily_range'] ) * DAY_IN_SECONDS );
	$from_date    = gmdate( 'Y-m-d H:i:s', $from_date );

	// Create the SQL query to fetch the related posts from the database.
	if ( is_int( $source_post->ID ) ) {

		// Fields to return.
		$fields = " $wpdb->posts.* ";

		// Set order by in case of date.
		if ( isset( $args['ordering'] ) && 'date' === $args['ordering'] ) {
			$orderby = " $wpdb->posts.post_date DESC ";
		}

		// Create the base MATCH clause.
		$match = $wpdb->prepare( ' AND MATCH (' . $match_fields . ') AGAINST (%s) ', $stuff ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		/** This filter has been documented in class-crp-query.php */
		$match = apply_filters( 'crp_posts_match', $match, $stuff, $source_post, $match_fields, $args );

		// Create the maximum date limit. Show posts before today.
		$now_clause = $wpdb->prepare( " AND $wpdb->posts.post_date < %s ", $now );

		/**
		 * Filter the Maximum date clause of the query.
		 *
		 * @since 2.1.0
		 * @since 2.9.3 Added $args
		 *
		 * @param string $now_clause      The Maximum date of the WHERE clause of the query.
		 * @param int    $source_post->ID Post ID
		 * @param array  $args            Arguments array.
		 */
		$now_clause = apply_filters( 'crp_posts_now_date', $now_clause, $source_post->ID, $args );

		// Create the minimum date limit. Show posts after the date specified.
		$from_clause = ( 0 === absint( $args['daily_range'] ) ) ? '' : $wpdb->prepare( " AND $wpdb->posts.post_date >= %s ", $from_date );

		/**
		 * Filter the Maximum date clause of the query.
		 *
		 * @since 2.1.0
		 * @since 2.9.3 Added $args
		 *
		 * @param string $from_clause     The Minimum date of the WHERE clause of the query.
		 * @param int    $source_post->ID Post ID
		 * @param array  $args            Arguments array.
		 */
		$from_clause = apply_filters( 'crp_posts_from_date', $from_clause, $source_post->ID, $args );

		// Create the base WHERE clause.
		$where  = $match;
		$where .= $now_clause;
		$where .= $from_clause;
		$where .= " AND $wpdb->posts.post_status IN ('publish','inherit') "; // Only show published posts or attachments.
		$where .= $wpdb->prepare( " AND {$wpdb->posts}.ID != %d ", $source_post->ID );  // Don't include the current ID.

		if ( isset( $args['same_author'] ) && $args['same_author'] ) {
			$where .= $wpdb->prepare( " AND {$wpdb->posts}.post_author = %d ", $source_post->post_author );  // Show posts of same author.
		}

		// Convert exclude post IDs string to array so it can be filtered.
		$exclude_post_ids = explode( ',', $args['exclude_post_ids'] );

		/** This filter has been documented in class-crp-query.php */
		$exclude_post_ids = apply_filters( 'crp_exclude_post_ids', $exclude_post_ids, $args, $source_post );

		// Convert it back to string.
		$exclude_post_ids = implode( ',', array_filter( array_filter( $exclude_post_ids, 'absint' ) ) );

		if ( '' != $exclude_post_ids ) { // phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
			$where .= " AND $wpdb->posts.ID NOT IN ({$exclude_post_ids}) ";
		}

		$where .= " AND $wpdb->posts.post_type IN ('" . join( "', '", $post_types ) . "') ";    // Array of post types.

		if ( isset( $args['include_cat_ids'] ) && ! empty( $args['include_cat_ids'] ) ) {
			$include_cat_ids = $args['include_cat_ids'];

			$where .= " AND $wpdb->posts.ID IN ( SELECT object_id FROM $wpdb->term_relationships WHERE term_taxonomy_id IN ($include_cat_ids) )";
		}

		// Create the base LIMITS clause.
		$limits .= $wpdb->prepare( ' LIMIT %d, %d ', $offset, $limit );

		/**
		 * Filter the SELECT clause of the query.
		 *
		 * @since 2.0.0
		 * @since 2.9.3 Added $args
		 *
		 * @param string $fields          The SELECT clause of the query.
		 * @param int    $source_post->ID Post ID
		 * @param array  $args            Arguments array.
		 */
		$fields = apply_filters( 'crp_posts_fields', $fields, $source_post->ID, $args );

		/**
		 * Filter the JOIN clause of the query.
		 *
		 * @since 2.0.0
		 * @since 2.9.3 Added $args
		 *
		 * @param string $join            The JOIN clause of the query.
		 * @param int    $source_post->ID Post ID
		 * @param array  $args            Arguments array.
		 */
		$join = apply_filters( 'crp_posts_join', $join, $source_post->ID, $args );

		/**
		 * Filter the WHERE clause of the query.
		 *
		 * @since 2.0.0
		 * @since 2.9.3 Added $args
		 *
		 * @param string $where           The WHERE clause of the query.
		 * @param int    $source_post->ID Post ID
		 * @param array  $args            Arguments array.
		 */
		$where = apply_filters( 'crp_posts_where', $where, $source_post->ID, $args );

		/**
		 * Filter the GROUP BY clause of the query.
		 *
		 * @since 2.0.0
		 * @since 2.9.3 Added $args
		 *
		 * @param string $groupby         The GROUP BY clause of the query.
		 * @param int    $source_post->ID Post ID
		 * @param array  $args            Arguments array.
		 */
		$groupby = apply_filters( 'crp_posts_groupby', $groupby, $source_post->ID, $args );

		/**
		 * Filter the HAVING clause of the query.
		 *
		 * @since 2.2.0
		 * @since 2.9.3 Added $args
		 *
		 * @param string $having          The HAVING clause of the query.
		 * @param int    $source_post->ID Post ID
		 * @param array  $args            Arguments array.
		 */
		$having = apply_filters( 'crp_posts_having', $having, $source_post->ID, $args );

		/**
		 * Filter the ORDER BY clause of the query.
		 *
		 * @since 2.0.0
		 * @since 2.9.3 Added $args
		 *
		 * @param string $orderby         The ORDER BY clause of the query.
		 * @param int    $source_post->ID Post ID
		 * @param array  $args            Arguments array.
		 */
		$orderby = apply_filters( 'crp_posts_orderby', $orderby, $source_post->ID, $args );

		/**
		 * Filter the LIMIT clause of the query.
		 *
		 * @since 2.0.0
		 * @since 2.9.3 Added $args
		 *
		 * @param string $limits          The LIMIT clause of the query.
		 * @param int    $source_post->ID Post ID
		 * @param array  $args            Arguments array.
		 */
		$limits = apply_filters( 'crp_posts_limits', $limits, $source_post->ID, $args );

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

		// Short circuit flag.
		$short_circuit = false;

		/**
		 * Allow a short circuit flag to be set to exit at this stage. Set to true to exit.
		 *
		 * @since 2.9.0
		 *
		 * @param bool   $short_circuit Short circuit filter.
		 * @param object $source_post   Current Post object.
		 * @param array  $args          Arguments array.
		 * @param string $sql           SQL clause.
		 * @param string $fields        The SELECT clause of the query.
		 * @param string $join          The JOIN clause of the query.
		 * @param string $where         The WHERE clause of the query.
		 * @param string $groupby       The GROUP BY clause of the query.
		 * @param string $having        The HAVING clause of the query.
		 * @param string $orderby       The ORDER BY clause of the query.
		 * @param string $limits        The LIMIT clause of the query.
		 */
		$short_circuit = apply_filters( 'get_crp_posts_id_short_circuit', $short_circuit, $source_post, $args, $sql, $fields, $join, $where, $groupby, $having, $orderby, $limits );

		if ( $short_circuit ) {
			return false;
		}

		// Support caching to speed up retrieval.
		if ( ! empty( $args['cache_posts'] ) && ! ( is_preview() || is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) ) {

			$meta_key = crp_cache_get_key( $args );
			$results  = get_crp_cache( $post->ID, $meta_key );
		}

		if ( empty( $results ) ) {
			$results = $wpdb->get_results( $sql ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
		}

		// Support caching to speed up retrieval.
		if ( ! empty( $args['cache_posts'] ) && ! ( is_preview() || is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) ) {
			set_crp_cache( $post->ID, $meta_key, $results );
		}

		if ( $random_order ) {
			$results_array = (array) $results;
			shuffle( $results_array );
			$results = (object) $results_array;
		}
	} else {
		$results = false;
	}

	/**
	 * Filter object containing the post IDs.
	 *
	 * @since 1.9
	 * @since 2.9.3 Added $args
	 *
	 * @param object $results Object containing the related post IDs
	 * @param array  $args    Arguments array.
	 */
	return apply_filters( 'get_crp_posts_id', $results, $args );
}


/**
 * Retrieves an array of the related posts.
 *
 * The defaults are as follows:
 *
 * @since 1.8.6
 * @since 3.0.0 Parameters have been dropped for a single $args parameter.
 *
 * @see CRP_Query::prepare_query_args()
 *
 * @param array $args Optional. Arguments to retrieve posts. See WP_Query::parse_query() for all available arguments.
 * @return WP_Post[]|int[] Array of post objects or post IDs.
 */
function get_crp_posts( $args = array() ) {
	// Backcompat if postid was passed in the pre-3.0.0 version.
	if ( is_int( $args ) ) {
		$args = array(
			'postid' => $args,
		);
	}

	$get_crp_posts = new CRP_Query( $args );

	/**
	 * Filter array of post IDs or objects.
	 *
	 * @since 1.9
	 *
	 * @param WP_Post[]|int[] $posts Array of post objects or post IDs.
	 * @param array           $args  Arguments to retrieve posts.
	 */
	return apply_filters( 'get_crp_posts', $get_crp_posts->posts, $args );
}

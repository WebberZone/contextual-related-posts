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
 * @param string|array $args Parameters in a query string format.
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
	 * @param   mixed   $template  Default return value
	 * @param   array   $results   Array of IDs of related posts
	 * @param   array   $args      Array of settings
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

			++$loop_counter;

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

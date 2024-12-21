<?php
/**
 * Functions to fetch and display the posts.
 *
 * @package Contextual_Related_Posts
 */

namespace WebberZone\Contextual_Related_Posts\Frontend;

use WebberZone\Contextual_Related_Posts\Util\Cache;
use WebberZone\Contextual_Related_Posts\Util\Helpers;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Display class
 *
 * @since 3.5.0
 */
class Display {

	/**
	 * Constructor class.
	 *
	 * @since 3.5.0
	 */
	public function __construct() {
	}


	/**
	 * Retrieves and displays related posts based on the given arguments.
	 *
	 * @since 3.5.0
	 *
	 * @param array $args {
	 *    Optional. Arguments to retrieve related posts.
	 *
	 *    @type bool        $is_widget       Whether the related posts are being displayed in a widget. Default is false.
	 *    @type bool        $is_shortcode    Whether the related posts are being displayed via a shortcode. Default is false.
	 *    @type bool        $is_manual       Whether the related posts are being displayed manually. Default is false.
	 *    @type bool        $is_block        Whether the related posts are being displayed in a block. Default is false.
	 *    @type bool        $echo            Whether to echo the output or return it. Default is true.
	 *    @type bool        $heading         Whether to display the heading. Default is true.
	 *    @type int         $offset          Offset the related posts by this number. Default is 0.
	 *    @type string      $extra_class     Extra class to add to the wrapper div. Default is empty string.
	 *    @type string      $more_link_text  Text to display in the more link. Default is empty string.
	 * }
	 * @return string The formatted list of related posts.
	 */
	public static function related_posts( $args = array() ) {
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
		 * @param bool     $short_circuit Short circuit filter.
		 * @param \WP_Post $post          Current Post object.
		 * @param array    $args          Arguments array.
		 */
		$short_circuit = apply_filters( 'get_crp_short_circuit', $short_circuit, $post, $args );

		if ( $short_circuit ) {
			return ''; // Exit without adding related posts.
		}

		// Check exclusions.
		if ( self::exclude_on( $post, $args ) ) {
			return ''; // Exit without adding related posts.
		}

		// WPML & PolyLang support - change strict limit to false.
		if ( class_exists( 'SitePress' ) || function_exists( 'pll_get_post' ) ) {
			$args['strict_limit'] = false;
		}

		// Support caching to speed up retrieval.
		if ( self::should_cache( $args ) ) {
			$meta_key = Cache::get_key( $args );
			$output   = Cache::get_cache( $post->ID, $meta_key );
			if ( $output ) {
				return $output;
			}
		}

		// Get thumbnail size.
		list( $args['thumb_width'], $args['thumb_height'] ) = Media_Handler::get_thumb_size( $args['thumb_size'] );

		// Retrieve the list of posts.
		$results = self::get_posts(
			array_merge(
				array(
					'postid'       => isset( $args['postid'] ) ? $args['postid'] : $post->ID,
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
			if ( self::should_cache( $args ) ) {
				Cache::set_cache( $post->ID, $meta_key, $custom_template );
			}
			return $custom_template;
		}

		if ( 'text_only' === $args['post_thumb_op'] || 'text_only' === $args['crp_styles'] ) {
			$args['crp_styles']    = 'text_only';
			$args['post_thumb_op'] = 'text_only';
		}
		$style_array = Styles_Handler::get_style( $args['crp_styles'] );

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
		 * @param \WP_Post $post         \WP_Post object.
		 */
		$post_classes = apply_filters( 'crp_post_class', $post_classes, $args, $post );

		$output = '<div class="' . $post_classes . '">';

		if ( $results ) {
			$loop_counter = 0;

			$output .= self::heading_title( $args );

			$output .= self::before_list( $args );

			foreach ( $results as $result ) {

				$result = get_post( $result );

				$output .= self::before_list_item( $args, $result );

				$output .= self::list_link( $args, $result );

				if ( $args['show_author'] ) {
					$output .= self::get_the_author( $args, $result );
				}

				if ( ! empty( $args['show_date'] ) ) {
					$output .= '<span class="crp_date"> ' . self::get_the_date( $args, $result ) . '</span> ';
				}

				if ( ! empty( $args['show_primary_term'] ) ) {
					$post_taxonomies = get_object_taxonomies( $result );
					if ( ! empty( $post_taxonomies[0] ) ) {
						$output .= '<span class="crp_primary_term"> ' . self::get_primary_term_name( $result, $post_taxonomies[0] ) . '</span> ';
					}
				}

				if ( ! empty( $args['show_excerpt'] ) ) {
					$output .= '<span class="crp_excerpt"> ' . self::get_the_excerpt( $result->ID, $args['excerpt_length'], true, $args['more_link_text'] ) . '</span>';
				}

				++$loop_counter;

				$output .= self::after_list_item( $args, $result );

				if ( absint( $args['limit'] ) === $loop_counter ) {
					break;  // End loop when related posts limit is reached.
				}
			} // End foreach.

			$output .= self::after_list( $args );

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
		if ( self::should_cache( $args ) ) {
			Cache::set_cache( $post->ID, $meta_key, $output );
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
	 * @since 3.5.0
	 *
	 * @see CRP_Query::prepare_query_args()
	 *
	 * @param array $args Optional. Arguments to retrieve posts. See WP_Query::parse_query() for all available arguments.
	 * @return \WP_Post[]|int[] Array of post objects or post IDs.
	 */
	public static function get_posts( $args = array() ) {
		$get_crp_posts = new \CRP_Query( $args );

		/**
		 * Filter array of post IDs or objects.
		 *
		 * @since 1.9
		 *
		 * @param \WP_Post[]|int[] $posts Array of post objects or post IDs.
		 * @param array            $args  Arguments to retrieve posts.
		 */
		return apply_filters( 'get_crp_posts', $get_crp_posts->posts, $args );
	}

	/**
	 * Processes exclusion settings to return if the related posts should not be displayed on the current post.
	 *
	 * @since 3.5.0
	 *
	 * @param int|\WP_Post|null $post Post ID or post object. Defaults to global $post. Default null.
	 * @param array             $args Parameters in a query string format.
	 * @return bool True if any exclusion setting is matched.
	 */
	public static function exclude_on( $post = null, $args = array() ) {
		$post = get_post( $post );
		if ( ! $post ) {
			return false;
		}

		// If this post ID is in the DO NOT DISPLAY list.
		$exclude_on_post_ids = isset( $args['exclude_on_post_ids'] ) ? $args['exclude_on_post_ids'] : \crp_get_option( 'exclude_on_post_ids' );
		$exclude_on_post_ids = explode( ',', $exclude_on_post_ids );
		if ( in_array( $post->ID, $exclude_on_post_ids ) ) { // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
			return true;
		}

		// If this post type is in the DO NOT DISPLAY list.
		// If post_types is empty or contains a query string then use parse_str else consider it comma-separated.
		$exclude_on_post_types = isset( $args['exclude_on_post_types'] ) ? $args['exclude_on_post_types'] : \crp_get_option( 'exclude_on_post_types' );
		if ( $exclude_on_post_types && false === strpos( $exclude_on_post_types, '=' ) ) {
			$exclude_on_post_types = explode( ',', $exclude_on_post_types );
		} else {
			parse_str( $exclude_on_post_types, $exclude_on_post_types );    // Save post types in $exclude_on_post_types variable.
		}

		if ( in_array( $post->post_type, $exclude_on_post_types, true ) ) {
			return true;
		}

		// If this post's category is in the DO NOT DISPLAY list.
		$exclude_on_categories = isset( $args['exclude_on_categories'] ) ? $args['exclude_on_categories'] : \crp_get_option( 'exclude_on_categories' );
		$exclude_on_categories = explode( ',', $exclude_on_categories );
		$post_categories       = get_the_terms( $post->ID, 'category' );
		$categories            = array();
		if ( ! empty( $post_categories ) && ! is_wp_error( $post_categories ) ) {
			$categories = wp_list_pluck( $post_categories, 'term_taxonomy_id' );
		}
		if ( ! empty( array_intersect( $exclude_on_categories, $categories ) ) ) {
			return true;
		}

		// If the DO NOT DISPLAY meta field is set.
		if ( ( isset( $args['is_shortcode'] ) && ! $args['is_shortcode'] ) &&
		( isset( $args['is_manual'] ) && ! $args['is_manual'] ) &&
		( isset( $args['is_block'] ) && ! $args['is_block'] ) ) {
			$crp_post_meta = get_post_meta( $post->ID, 'crp_post_meta', true );

			if ( isset( $crp_post_meta['crp_disable_here'] ) ) {
				$crp_disable_here = $crp_post_meta['crp_disable_here'];
			} else {
				$crp_disable_here = 0;
			}

			if ( $crp_disable_here ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Check if the output should be cached.
	 *
	 * @since 3.5.0
	 *
	 * @param array $args Arguments to retrieve posts.
	 * @return bool True if the output should be cached.
	 */
	public static function should_cache( $args ) {
		return ! empty( $args['cache'] ) &&
				! ( is_preview() || is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) );
	}

	/**
	 * Function to create an excerpt for the post.
	 *
	 * @since 3.5.0
	 *
	 * @param int|\WP_Post $post            Post ID or WP_Post instance.
	 * @param int|string   $excerpt_length  Length of the excerpt in words.
	 * @param bool         $use_excerpt     Use excerpt instead of content.
	 * @param string       $more_link_text  Content for when there is more text. Default is null.
	 * @param bool         $strip_stopwords Strip stopwords from the excerpt. Default is false.
	 * @return string Excerpt
	 */
	public static function get_the_excerpt( $post, $excerpt_length = 0, $use_excerpt = true, $more_link_text = '', $strip_stopwords = false ) {
		$content = '';

		$post = get_post( $post );
		if ( empty( $post ) ) {
			return '';
		}
		if ( $use_excerpt ) {
			$content = $post->post_excerpt;
		}
		if ( empty( $content ) ) {
			$content = $post->post_content;
		}

		$output = strip_shortcodes( $content );
		$output = wp_strip_all_tags( $output, true );

		if ( $strip_stopwords ) {
			$output = Helpers::strip_stopwords( $output );
		}

		/**
		 * Filters excerpt generated by CRP before it is trimmed.
		 *
		 * @since 2.3.0
		 * @since 2.9.0 Added $content parameter
		 * @since 3.0.0 Changed second parameter to WP_Post instance instead of ID.
		 *
		 * @param string    $output         Formatted excerpt.
		 * @param \WP_Post  $post           Source Post instance.
		 * @param int       $excerpt_length Length of the excerpt.
		 * @param boolean   $use_excerpt    Use the excerpt?
		 * @param string    $content        Content that is used to create the excerpt.
		 */
		$output = apply_filters( 'crp_excerpt_pre_trim', $output, $post, $excerpt_length, $use_excerpt, $content );

		if ( 0 === (int) $excerpt_length || CRP_MAX_WORDS < (int) $excerpt_length ) {
			$excerpt_length = CRP_MAX_WORDS;
		}

		/**
		 * Filters the Read More text of the CRP excerpt.
		 *
		 * @since 3.0.0
		 *
		 * @param string   $more_link_text    Read More text.
		 * @param \WP_Post $post              Source Post instance.
		 */
		$more_link_text = apply_filters( 'crp_excerpt_more_link_text', $more_link_text, $post );

		if ( null === $more_link_text ) {
			$more_link_text = sprintf(
				'<span aria-label="%1$s">%2$s</span>',
				sprintf(
				/* translators: %s: Post title. */
					__( 'Continue reading %s', 'contextual-related-posts' ),
					the_title_attribute(
						array(
							'echo' => false,
							'post' => $post,
						)
					)
				),
				__( '(more&hellip;)', 'contextual-related-posts' )
			);
		}

		if ( ! empty( $more_link_text ) ) {
			$more_link_element = ' <a href="' . get_permalink( $post ) . "#more-{$post->ID}\" class=\"crp_read_more_link\">$more_link_text</a>";
		} else {
			$more_link_element = '';
		}

		/**
		 * Filters the Read More link text of the CRP excerpt.
		 *
		 * @since 3.0.0
		 *
		 * @param string   $more_link_element Read More link element.
		 * @param string   $more_link_text    Read More text.
		 * @param \WP_Post $post              Source Post instance.
		 */
		$more_link_element = apply_filters( 'crp_excerpt_more_link', $more_link_element, $more_link_text, $post );

		if ( $excerpt_length > 0 ) {
			$more_link_element = empty( $more_link_element ) ? null : $more_link_element;

			$output = wp_trim_words( $output, $excerpt_length, $more_link_element );
		}

		if ( post_password_required( $post ) ) {
			$output = __( 'There is no excerpt because this is a protected post.', 'contextual-related-posts' );
		}

		/**
		 * Filters excerpt generated by CRP.
		 *
		 * @since 1.9
		 * @since 3.0.0 Changed second parameter to WP_Post instance instead of ID.
		 *
		 * @param string   $output         Formatted excerpt.
		 * @param \WP_Post $post           Source Post instance.
		 * @param int      $excerpt_length Length of the excerpt.
		 * @param boolean  $use_excerpt    Use the excerpt?
		 */
		return apply_filters( 'crp_excerpt', $output, $post, $excerpt_length, $use_excerpt );
	}

	/**
	 * Get the default thumbnail.
	 *
	 * @since 3.6.0
	 *
	 * @return string Default thumbnail.
	 */
	public static function get_default_thumbnail() {
		return CRP_PLUGIN_URL . 'default.png';
	}

	/**
	 * Returns the link attributes.
	 *
	 * @since 3.5.0
	 *
	 * @param array    $args   Array of arguments.
	 * @param \WP_Post $result \WP_Post object.
	 * @return  string  Space separated list of link attributes.
	 */
	public static function link_attributes( $args, $result ) {

		$rel_attribute    = ( $args['link_nofollow'] ) ? ' rel="nofollow" ' : ' ';
		$target_attribute = ( $args['link_new_window'] ) ? ' target="_blank" ' : ' ';

		$link_attributes = array(
			'rel_attribute'    => $rel_attribute,
			'target_attribute' => $target_attribute,
		);

		/**
		 * Filter the title of the Related Posts list
		 *
		 * @since 2.2.0
		 * @since 3.2.0 Added $result parameter
		 *
		 * @param array   $link_attributes    Array of link attributes
		 * @param array   $args   Array of arguments
		 * @param \WP_Post $result \WP_Post object.
		 */
		$link_attributes = apply_filters( 'crp_link_attributes', $link_attributes, $args, $result );

		// Convert it to a string.
		$link_attributes = implode( ' ', $link_attributes );

		return $link_attributes;
	}


	/**
	 * Returns the heading of the related posts.
	 *
	 * @since 3.5.0
	 *
	 * @param   array $args   Array of arguments.
	 * @return  string  Space separated list of link attributes
	 */
	public static function heading_title( $args ) {
		global $post;

		$title = '';

		if ( $args['heading'] && ! $args['is_widget'] ) {
			$title = empty( $args['title'] ) ? \crp_get_option( 'title', '' ) : $args['title'];
			$title = str_replace( '%postname%', $post->post_title, $title );    // Replace %postname% with the title of the current post.
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
	 * @since 3.5.0
	 *
	 * @param   array $args   Array of arguments.
	 * @return  string  Space separated list of link attributes
	 */
	public static function before_list( $args ) {

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
	 * @since 3.5.0
	 *
	 * @param   array $args   Array of arguments.
	 * @return  string  Space separated list of link attributes
	 */
	public static function after_list( $args ) {

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
	 * @since 3.5.0
	 *
	 * @param   array    $args   Array of arguments.
	 * @param   \WP_Post $result \WP_Post object.
	 * @return  string  Space separated list of link attributes
	 */
	public static function before_list_item( $args, $result ) {

		$before_list_item = $args['before_list_item'];

		/**
		 * Filter the opening tag of each list item.
		 *
		 * @since   1.9
		 *
		 * @param   string  $before_list_item Tag before each list item. Can be defined in the Settings page.
		 * @param   \WP_Post $result           \WP_Post object.
		 * @param   array   $args             Array of arguments
		 */
		return apply_filters( 'crp_before_list_item', $before_list_item, $result, $args );
	}


	/**
	 * Returns the closing tag of each list item.
	 *
	 * @since 3.5.0
	 *
	 * @param   array    $args   Array of arguments.
	 * @param   \WP_Post $result \WP_Post object.
	 * @return  string  Space separated list of link attributes
	 */
	public static function after_list_item( $args, $result ) {

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
	 * @since 3.5.0
	 *
	 * @param   array    $args   Array of arguments.
	 * @param   \WP_Post $result \WP_Post object.
	 * @return  string  Space separated list of link attributes
	 */
	public static function get_the_title( $args, $result ) {

		$title = Helpers::trim_char( $result->post_title, $args['title_length'] );  // Get the post title and crop it if needed.

		/**
		 * Filter the title of each list item.
		 *
		 * @since   1.9
		 *
		 * @param   string  $title  Title of the post.
		 * @param   \WP_Post $result \WP_Post object.
		 * @param   array   $args   Array of arguments
		 */
		return apply_filters( 'crp_title', $title, $result, $args );
	}


	/**
	 * Returns the author of each list item.
	 *
	 * @since 3.5.0
	 *
	 * @param   array    $args   Array of arguments.
	 * @param   \WP_Post $result \WP_Post object.
	 * @return  string  Space separated list of link attributes
	 */
	public static function get_the_author( $args, $result ) {

		$author_info = get_userdata( (int) $result->post_author );
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
		 * @param   \WP_Post $result \WP_Post object.
		 * @param   array   $args   Array of arguments
		 */
		return apply_filters( 'crp_author', $crp_author, $author_info, $result, $args );
	}


	/**
	 * Returns the permalink of each list item.
	 *
	 * @since   2.5.0
	 *
	 * @param   array    $args   Array of arguments.
	 * @param   \WP_Post $result \WP_Post object.
	 * @return  string  Space separated list of link attributes
	 */
	public static function get_permalink( $args, $result ) {

		$link = get_permalink( $result->ID );

		/**
		 * Filter the title of each list item.
		 *
		 * @since   2.5.0
		 *
		 * @param   string  $title  Permalink of the post.
		 * @param   \WP_Post $result \WP_Post object.
		 * @param   array   $args   Array of arguments
		 */
		return apply_filters( 'crp_permalink', $link, $result, $args );
	}


	/**
	 * Returns the formatted list item with link and and thumbnail for each list item.
	 *
	 * @since 3.5.0
	 *
	 * @param   array    $args   Array of arguments.
	 * @param   \WP_Post $result \WP_Post object.
	 * @return  string Space separated list of link attributes
	 */
	public static function list_link( $args, $result ) {

		$output          = '';
		$title           = self::get_the_title( $args, $result );
		$link            = self::get_permalink( $args, $result );
		$link_attributes = self::link_attributes( $args, $result );

		$output .= '<a href="' . $link . '" ' . $link_attributes . ' class="crp_link ' . $result->post_type . '-' . $result->ID . '">';

		if ( 'after' === $args['post_thumb_op'] ) {
			$output .= '<span class="crp_title">' . $title . '</span>'; // Add title when required by settings.
		}

		if ( 'inline' === $args['post_thumb_op'] || 'after' === $args['post_thumb_op'] || 'thumbs_only' === $args['post_thumb_op'] ) {
			$output .= '<figure>';
			$output .= Media_Handler::get_the_post_thumbnail(
				array(
					'post'               => $result,
					'size'               => $args['thumb_size'],
					'thumb_meta'         => $args['thumb_meta'],
					'thumb_html'         => $args['thumb_html'],
					'thumb_default'      => $args['thumb_default'],
					'thumb_default_show' => $args['thumb_default_show'],
					'scan_images'        => $args['scan_images'],
					'class'              => 'crp_thumb',
				)
			);
			$output .= '</figure>';
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
		 * @param   \WP_Post $result \WP_Post object.
		 * @param   array   $args   Array of arguments
		 */
		return apply_filters( 'crp_list_link', $output, $result, $args );
	}

	/**
	 * Returns the date.
	 *
	 * @since 3.5.0
	 *
	 * @param  array    $args   Array of arguments.
	 * @param  \WP_Post $result \WP_Post object.
	 * @return string Space separated list of link attributes
	 */
	public static function get_the_date( $args, $result ) {

		$output = mysql2date( get_option( 'date_format', 'd/m/y' ), $result->post_date );

		/**
		 * Filter Formatted list item with link and and thumbnail.
		 *
		 * @since 3.2.0
		 *
		 * @param string  $output Formatted list item with link and and thumbnail
		 * @param \WP_Post $result \WP_Post object.
		 * @param array   $args   Array of arguments
		 */
		return apply_filters( 'crp_date', $output, $result, $args );
	}


	/**
	 * Display the primary term for a given post.
	 *
	 * @since 3.5.0
	 *
	 * @param int|\WP_Post $post         Post ID or \WP_Post object.
	 * @param string       $term         Term name.
	 * @param bool         $echo_value   Echo or return.
	 * @return string|void Term name if $echo_value is true, void if false.
	 */
	public static function get_primary_term_name( $post, $term = 'category', $echo_value = false ) {
		$output       = '';
		$primary_term = Helpers::get_primary_term( $post, $term );
		if ( ! empty( $primary_term['primary'] ) ) {
			$output = $primary_term['primary']->name;
		}
		if ( $echo_value ) {
			echo esc_html( $output );
		} else {
			return $output;
		}
	}

	/**
	 * Filter for 'the_content' to add the related posts.
	 *
	 * @since 3.5.0
	 *
	 * @param string $content Post content.
	 * @return string After the filter has been processed
	 */
	public static function content_filter( $content ) {

		global $post, $crp_settings, $wp_filters;

		// Track the number of times this function  is called.
		static $filter_calls = 0;
		++$filter_calls;

		// Return if it's not in the loop or in the main query.
		if ( ! ( in_the_loop() && is_main_query() && (int) get_queried_object_id() === (int) $post->ID ) ) {
			return $content;
		}

		// Check if this is the last call of the_content.
		if ( doing_filter( 'the_content' ) && isset( $wp_filters['the_content'] ) && (int) $wp_filters['the_content'] !== $filter_calls ) {
			return $content;
		}

		// Return if this is a mobile device and disable on mobile option is enabled.
		if ( wp_is_mobile() && \crp_get_option( 'disable_on_mobile' ) ) {
			return $content;
		}

		// Return if this is an amp page and disable on amp option is enabled.
		if ( function_exists( 'is_amp_endpoint' ) && \is_amp_endpoint() && \crp_get_option( 'disable_on_amp' ) ) {
			return $content;
		}

		// Check exclusions.
		if ( self::exclude_on( $post, $crp_settings ) ) {
			return $content;
		}

		$add_to = \crp_get_option( 'add_to', false );

		// Else add the content.
		switch ( true ) {
			case is_single() && ! empty( $add_to['single'] ):
			case is_page() && ! empty( $add_to['page'] ):
			case is_home() && ! empty( $add_to['home'] ):
			case is_category() && ! empty( $add_to['category_archives'] ):
			case is_tag() && ! empty( $add_to['tag_archives'] ):
			case ( is_tax() || is_author() || is_date() ) && ! empty( $add_to['other_archives'] ):
				return self::generate_content( $content, self::related_posts() );
			default:
				return $content;
		}
	}


	/**
	 * Helper for inserting crp code into or alongside content
	 *
	 * @since 3.5.0
	 *
	 * @param string $content Post content.
	 * @param string $crp_code  CRP generated code.
	 * @return string After the filter has been processed
	 */
	public static function generate_content( $content, $crp_code ) {

		$insert_after_paragraph = \crp_get_option( 'insert_after_paragraph' );

		if ( ! is_numeric( $insert_after_paragraph ) || -1 === (int) $insert_after_paragraph ) {
			return $content . $crp_code;
		} elseif ( 0 === (int) $insert_after_paragraph ) {
			return $crp_code . $content;
		} else {
			return self::insert_after_paragraph( $content, $crp_code, $insert_after_paragraph );
		}
	}

	/**
	 * Helper for inserting code after a closing paragraph tag
	 *
	 * @since 3.5.0
	 *
	 * @param string $content Post content.
	 * @param string $crp_code CRP generated code.
	 * @param int    $paragraph_id Paragraph number to insert after. A negative value indicates counting from the end.
	 * @return string After the filter has been processed
	 */
	public static function insert_after_paragraph( $content, $crp_code, $paragraph_id ) {
		$closing_p  = '</p>';
		$paragraphs = explode( $closing_p, $content );

		// Adjust paragraph_id if it's negative.
		if ( $paragraph_id < 0 ) {
			$paragraph_id = count( $paragraphs ) + $paragraph_id - 1;
		} else {
			--$paragraph_id; // Adjust for zero index.
		}

		if ( count( $paragraphs ) >= abs( $paragraph_id ) ) {
			foreach ( $paragraphs as $index => &$paragraph ) {
				if ( trim( $paragraph ) ) {
					$paragraph .= $closing_p;
				}

				if ( abs( $paragraph_id ) === $index ) {
					$paragraph .= $crp_code;
				}
			}

			$content = implode( '', $paragraphs );
		} else {
			$content .= $crp_code;
		}

		return $content;
	}

	/**
	 * Filter to add related posts to feeds.
	 *
	 * @since 3.5.0
	 *
	 * @param   string $content Post content.
	 * @return  string  Formatted content
	 */
	public static function rss_filter( $content ) {

		$add_to = \crp_get_option( 'add_to', false );

		$limit_feed         = \crp_get_option( 'limit_feed' );
		$show_excerpt_feed  = \crp_get_option( 'show_excerpt_feed' );
		$post_thumb_op_feed = \crp_get_option( 'post_thumb_op_feed' );

		if ( isset( $add_to['feed'] ) && $add_to['feed'] ) {
			$output  = $content;
			$output .= get_crp( 'is_widget=0&limit=' . $limit_feed . '&show_excerpt=' . $show_excerpt_feed . '&post_thumb_op=' . $post_thumb_op_feed );
			return $output;
		} else {
			return $content;
		}
	}
}

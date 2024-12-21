<?php
/**
 * Helper functions
 *
 * @package Contextual_Related_Posts
 */

namespace WebberZone\Contextual_Related_Posts\Util;

use WP_Query;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Admin Columns Class.
 *
 * @since 3.3.0
 */
class Helpers {

	/**
	 * Constructor class.
	 *
	 * @since 3.5.0
	 */
	public function __construct() {
	}

	/**
	 * Convert a string to CSV.
	 *
	 * @since 3.5.0
	 *
	 * @param array  $input_array Input string.
	 * @param string $delimiter Delimiter.
	 * @param string $enclosure Enclosure.
	 * @param string $terminator Terminating string.
	 * @return string CSV string.
	 */
	public static function str_putcsv( $input_array, $delimiter = ',', $enclosure = '"', $terminator = "\n" ) {
		// First convert associative array to numeric indexed array.
		$work_array = array();
		foreach ( $input_array as $key => $value ) {
			$work_array[] = $value;
		}

		$output     = '';
		$array_size = count( $work_array );

		for ( $i = 0; $i < $array_size; $i++ ) {
			// Nested array, process nest item.
			if ( is_array( $work_array[ $i ] ) ) {
				$output .= self::str_putcsv( $work_array[ $i ], $delimiter, $enclosure, $terminator );
			} else {
				switch ( gettype( $work_array[ $i ] ) ) {
					// Manually set some strings.
					case 'NULL':
						$sp_format = '';
						break;
					case 'boolean':
						$sp_format = ( true === $work_array[ $i ] ) ? 'true' : 'false';
						break;
					// Make sure sprintf has a good datatype to work with.
					case 'integer':
						$sp_format = '%i';
						break;
					case 'double':
						$sp_format = '%0.2f';
						break;
					case 'string':
						$sp_format        = '%s';
						$work_array[ $i ] = str_replace( "$enclosure", "$enclosure$enclosure", $work_array[ $i ] );
						break;
					// Unknown or invalid items for a csv - note: the datatype of array is already handled above, assuming the data is nested.
					case 'object':
					case 'resource':
					default:
						$sp_format = '';
						break;
				}
				$output .= sprintf( '%2$s' . $sp_format . '%2$s', $work_array[ $i ], $enclosure );
				$output .= ( $i < ( $array_size - 1 ) ) ? $delimiter : $terminator;
			}
		}

		return $output;
	}


	/**
	 * Truncate a string to a certain length.
	 *
	 * @since 3.5.0
	 *
	 * @param  string $input      String to truncate.
	 * @param  int    $count       Maximum number of characters to take.
	 * @param  string $more        What to append if $input needs to be trimmed.
	 * @param  bool   $break_words Optionally choose to break words.
	 * @return string Truncated string.
	 */
	public static function trim_char( $input, $count = 60, $more = '&hellip;', $break_words = false ) {

		$output = wp_strip_all_tags( $input, true );
		$count  = absint( $count );

		if ( 0 === $count ) {
			return $input;
		}
		if ( mb_strlen( $output ) > $count ) {
			$count -= min( $count, mb_strlen( $more ) );
			if ( ! $break_words ) {
				$output = preg_replace( '/\s+?(\S+)?$/u', '', mb_substr( $output, 0, $count + 1 ) );
			}
			$output = mb_substr( $output, 0, $count ) . $more;
		}

		return $output;
	}

	/**
	 * Get the primary term for a given post.
	 *
	 * @since 3.5.0
	 *
	 * @param int|\WP_Post $post       Post ID or WP_Post object.
	 * @param string       $term       Term name.
	 * @param bool         $return_all Whether to return all categories.
	 * @return array Primary term object at `primary` and array of term
	 *               objects at `all` if $return_all is true.
	 */
	public static function get_primary_term( $post, $term = 'category', $return_all = false ) {
		$return = array(
			'primary' => '',
			'all'     => array(),
		);

		$post = get_post( $post );
		if ( empty( $post ) ) {
			return $return;
		}

		// Yoast primary term.
		if ( class_exists( 'WPSEO_Primary_Term' ) ) {
			$wpseo_primary_term = new \WPSEO_Primary_Term( $term, $post->ID );
			$primary_term       = $wpseo_primary_term->get_primary_term();
			$primary_term       = get_term( $wpseo_primary_term->get_primary_term() );

			if ( ! is_wp_error( $primary_term ) ) {
				$return['primary'] = $primary_term;
			}
		}

		// Rank Math SEO primary term.
		if ( class_exists( 'RankMath' ) ) {
			$primary_term = get_term( get_post_meta( $post->ID, "rank_math_primary_{$term}", true ) );
			if ( ! is_wp_error( $primary_term ) ) {
				$return['primary'] = $primary_term;
			}
		}

		// The SEO Framework primary term.
		if ( function_exists( 'the_seo_framework' ) ) {
			$primary_term = get_term( get_post_meta( $post->ID, "_primary_term_{$term}", true ) );
			if ( ! is_wp_error( $primary_term ) ) {
				$return['primary'] = $primary_term;
			}
		}

		// SEOPress primary term.
		if ( function_exists( 'seopress_init' ) ) {
			$primary_term = get_term( get_post_meta( $post->ID, '_seopress_robots_primary_cat', true ) );
			if ( ! is_wp_error( $primary_term ) ) {
				$return['primary'] = $primary_term;
			}
		}

		if ( empty( $return['primary'] ) || $return_all ) {
			$categories = get_the_terms( $post, $term );

			if ( ! empty( $categories ) ) {
				if ( empty( $return['primary'] ) ) {
					$return['primary'] = $categories[0];
				}
				$return['all'] = ( $return_all ) ? $categories : array();
			}
		}

		/**
		 * Filters the primary category/term for the given post.
		 *
		 * @since 3.2.0
		 *
		 * @param array        $return Primary term object at `primary` and optionally
		 *                            array of term objects at `all`.
		 * @param int|\WP_Post $post   Post ID or WP_Post object.
		 * @param string       $term   Term name.
		 */
		return apply_filters( 'crp_get_primary_term', $return, $post, $term );
	}

	/**
	 * Get all terms of a post.
	 *
	 * @since 3.5.0
	 *
	 * @param int|\WP_Post $post Post ID or WP_Post object.
	 * @return array Array of taxonomies.
	 */
	public static function get_all_terms( $post ) {
		$taxonomies = array();

		if ( ! empty( $post ) ) {
			$post = get_post( $post );
		}

		if ( ! empty( $post ) ) {
			$taxonomies = get_object_taxonomies( $post );
		}

		$all_terms = array();

		// Loop through the taxonomies and get the terms for the post for each taxonomy.
		foreach ( $taxonomies as $taxonomy ) {
			$terms = get_the_terms( $post, $taxonomy );
			if ( $terms && ! is_wp_error( $terms ) ) {
				$all_terms = array_merge( $all_terms, $terms );
			}
		}

		return $all_terms;
	}

	/**
	 * Strip stopwords from text.
	 *
	 * @since 3.1.0
	 *
	 * @param string|array $subject The string or an array with strings to search and replace. .
	 * @param string|array $search  The pattern to search for. It can be either a string or an array with strings.
	 * @param string|array $replace The string or an array with strings to replace.
	 */
	public static function strip_stopwords( $subject = '', $search = '', $replace = '' ) {

		$pattern = array();
		if ( empty( $search ) ) {
			$get_search_stopwords = new \ReflectionMethod( 'WP_Query', 'get_search_stopwords' );
			$get_search_stopwords->setAccessible( true );
			$search = $get_search_stopwords->invoke( new WP_Query() );

			array_push( $search, 'from', 'where' );
		}

		foreach ( (array) $search as $s ) {
			$pattern[] = '/\b' . $s . '\b/ui';
		}
		$output = preg_replace( $pattern, $replace, $subject );
		$output = preg_replace( '/\b[a-z\-]\b/i', '', $output );
		$output = preg_replace( '/\s+/', ' ', $output );

		return $output;
	}
}

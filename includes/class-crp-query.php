<?php
/**
 * Query API: CRP_Query class
 *
 * @package Contextual_Related_Posts
 * @subpackage CRP_Query
 * @since 3.0.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'CRP_Query' ) ) :
	/**
	 * Query API: CRP_Query class.
	 *
	 * @since 3.0.0
	 */
	class CRP_Query extends WP_Query {

		/**
		 * Source Post to find related posts for.
		 *
		 * @var WP_Post A WP_Post instance.
		 */
		public $source_post;

		/**
		 * Query vars, before parsing
		 *
		 * @since 3.0.2
		 * @var array
		 */
		public $input_query_args = array();

		/**
		 * Query vars, after parsing
		 *
		 * @since 3.0.0
		 * @var array
		 */
		public $query_args = array();

		/**
		 * Flag to turn relevance matching ON or OFF.
		 *
		 * @since 3.0.0
		 * @var bool
		 */
		public $enable_relevance = true;

		/**
		 * Random order flag.
		 *
		 * @since 3.0.0
		 * @var bool
		 */
		public $random_order = false;

		/**
		 * CRP Post Meta.
		 *
		 * @since 3.0.0
		 * @var mixed
		 */
		public $crp_post_meta;

		/**
		 * Array of manual related post IDs.
		 *
		 * @since 3.3.0
		 * @var array
		 */
		public $manual_related = array();

		/**
		 * Number of manual related posts.
		 *
		 * @since 3.3.0
		 * @var int
		 */
		public $no_of_manual_related = 0;

		/**
		 * Fields to be matched.
		 *
		 * @since 3.0.0
		 * @var string
		 */
		public $match_fields;

		/**
		 * Holds the text to be matched.
		 *
		 * @since 3.0.0
		 * @var string
		 */
		public $stuff;

		/**
		 * Cache set flag.
		 *
		 * @since 3.0.0
		 * @var bool
		 */
		public $in_cache = false;

		/**
		 * Main constructor.
		 *
		 * @since 3.0.0
		 *
		 * @param array|string $args The Query variables. Accepts an array or a query string.
		 */
		public function __construct( $args = array() ) {
			$this->prepare_query_args( $args );

			add_filter( 'posts_fields', array( $this, 'posts_fields' ), 10, 2 );
			add_filter( 'posts_join', array( $this, 'posts_join' ), 10, 2 );
			add_filter( 'posts_where', array( $this, 'posts_where' ), 10, 2 );
			add_filter( 'posts_orderby', array( $this, 'posts_orderby' ), 10, 2 );
			add_filter( 'posts_request', array( $this, 'posts_request' ), 10, 2 );
			add_filter( 'posts_pre_query', array( $this, 'posts_pre_query' ), 10, 2 );
			add_filter( 'the_posts', array( $this, 'the_posts' ), 10, 2 );

			parent::__construct( $this->query_args );

			// Remove filters after use.
			remove_filter( 'posts_fields', array( $this, 'posts_fields' ) );
			remove_filter( 'posts_join', array( $this, 'posts_join' ) );
			remove_filter( 'posts_where', array( $this, 'posts_where' ) );
			remove_filter( 'posts_orderby', array( $this, 'posts_orderby' ) );
			remove_filter( 'posts_request', array( $this, 'posts_request' ) );
			remove_filter( 'posts_pre_query', array( $this, 'posts_pre_query' ) );
			remove_filter( 'the_posts', array( $this, 'the_posts' ) );
		}

		/**
		 * Prepare the query variables.
		 *
		 * @since 3.0.0
		 * @see WP_Query::parse_query()
		 * @see crp_get_registered_settings()
		 *
		 * @param string|array $args {
		 *     Optional. Array or string of Query parameters.
		 *
		 *     @type array|string  $include_cat_ids  An array or comma-separated string of category or custom taxonomy term_taxonoy_id.
		 *     @type array|string  $include_post_ids An array or comma-separated string of post IDs.
		 *     @type bool          $offset           Offset the related posts returned by this number.
		 *     @type int           $postid           Get related posts for a specific post ID.
		 *     @type bool          $strict_limit     If this is set to false, then it will fetch 3x posts.
		 * }
		 */
		public function prepare_query_args( $args = array() ) {
			global $post;
			$crp_settings = crp_get_settings();

			$defaults = array(
				'include_cat_ids'  => 0,
				'include_post_ids' => 0,
				'offset'           => 0,
				'postid'           => false,
				'strict_limit'     => true,
			);
			$defaults = array_merge( $defaults, $crp_settings );
			$args     = wp_parse_args( $args, $defaults );

			// Set necessary variables.
			$args['crp_query']           = true;
			$args['suppress_filters']    = false;
			$args['ignore_sticky_posts'] = true;
			$args['no_found_rows']       = true;

			// Store query args before we manipulate them.
			$this->input_query_args = $args;

			// Set the source post.
			$source_post = empty( $args['postid'] ) ? $post : get_post( $args['postid'] );
			if ( ! $source_post ) {
				$source_post = $post;
			}
			$this->source_post = $source_post;

			// Save post meta into a class-wide variable.
			$this->crp_post_meta = get_post_meta( $source_post->ID, 'crp_post_meta', true );

			if ( ! empty( $this->crp_post_meta['manual_related'] ) ) {
				$this->manual_related = wp_parse_id_list( $this->crp_post_meta['manual_related'] );
			}
			if ( ! empty( $args['include_post_ids'] ) ) {
				$include_post_ids     = wp_parse_id_list( $args['include_post_ids'] );
				$this->manual_related = array_merge( $this->manual_related, $include_post_ids );
			}
			$this->no_of_manual_related = count( $this->manual_related );

			// Set the random order and save it in a class-wide variable.
			$random_order = ( $args['random_order'] || ( isset( $args['ordering'] ) && 'random' === $args['ordering'] ) ) ? true : false;
			// If we need to order randomly then set strict_limit to false.
			if ( $random_order ) {
				$args['strict_limit'] = false;
			}
			$this->random_order = $random_order;

			// Set the number of posts to be retrieved. Use posts_per_page if set else use limit.
			if ( empty( $args['posts_per_page'] ) ) {
				$args['posts_per_page'] = ( $args['strict_limit'] ) ? absint( $args['limit'] ) : ( absint( $args['limit'] ) * 3 );
			}

			if ( empty( $args['post_type'] ) ) {

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
				 * Filter the post_types passed to the query.
				 *
				 * @since 2.2.0
				 * @since 3.0.0 Changed second argument from post ID to WP_Post object.
				 *
				 * @param array   $post_types  Array of post types to filter by.
				 * @param WP_Post $source_post Source Post instance.
				 * @param array   $args        Arguments array.
				 */
				$args['post_type'] = apply_filters( 'crp_posts_post_types', $post_types, $source_post, $args );

			}

			// Tax Query.
			if ( ! empty( $args['tax_query'] ) && is_array( $args['tax_query'] ) ) {
				$tax_query = $args['tax_query'];
			} else {
				$tax_query = array();
			}

			if ( ! empty( $args['include_cat_ids'] ) ) {
				$tax_query[] = array(
					'field'            => 'term_taxonomy_id',
					'terms'            => wp_parse_id_list( $args['include_cat_ids'] ),
					'include_children' => false,
				);
			}

			if ( ! empty( $args['exclude_categories'] ) ) {
				$tax_query[] = array(
					'field'            => 'term_taxonomy_id',
					'terms'            => wp_parse_id_list( $args['exclude_categories'] ),
					'operator'         => 'NOT IN',
					'include_children' => false,
				);
			}

			if ( ! empty( $args['primary_term'] ) ) {
				// Get the taxonomies used by the post type.
				$post_taxonomies = get_object_taxonomies( $source_post );

				foreach ( (array) $post_taxonomies as $term ) {
					if ( empty( $primary_term['primary'] ) ) {
						$primary_term = crp_get_primary_term( $source_post, $term );
					}
				}

				if ( ! empty( $primary_term['primary'] ) ) {

					$tax_query[] = array(
						'field'            => 'term_taxonomy_id',
						'terms'            => wp_parse_id_list( $primary_term['primary']->term_taxonomy_id ),
						'include_children' => false,
					);
				}
			}

			// Process same taxonomies option.
			if ( isset( $args['same_taxes'] ) && $args['same_taxes'] ) {
				$taxonomies = explode( ',', $args['same_taxes'] );

				// Get the taxonomies used by the post type.
				if ( empty( $post_taxonomies ) ) {
					$post_taxonomies = get_object_taxonomies( $source_post );
				}

				// Only limit the taxonomies to what is selected for the current post.
				$current_taxonomies = array_values( array_intersect( $taxonomies, $post_taxonomies ) );

				// Store the number of common taxonomies.
				$args['taxonomy_count'] = count( $current_taxonomies );

				// Get the terms for the current post.
				$terms = wp_get_object_terms( $source_post->ID, (array) $current_taxonomies );
				if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
					$term_taxonomy_ids = array_unique( wp_list_pluck( $terms, 'term_taxonomy_id' ) );

					$tax_query[] = array(
						'field'            => 'term_taxonomy_id',
						'terms'            => wp_parse_id_list( $term_taxonomy_ids ),
						'operator'         => 'IN',
						'include_children' => false,
					);
				}
			}

			/**
			 * Filter the tax_query passed to the query.
			 *
			 * @since 3.0.0
			 *
			 * @param array   $tax_query   Array of tax_query parameters.
			 * @param WP_Post $source_post Source Post instance.
			 * @param array   $args        Arguments array.
			 */
			$tax_query = apply_filters( 'crp_query_tax_query', $tax_query, $source_post, $args );

			// Add a relation key if more than one $tax_query.
			if ( count( $tax_query ) > 1 ) {
				$tax_query['relation'] = 'AND';
			}

			$args['tax_query'] = $tax_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query

			// Set date_query.
			$date_query = array(
				array(
					'after'     => ( 0 === absint( $args['daily_range'] ) ) ? '' : gmdate( 'Y-m-d', strtotime( current_time( 'mysql' ) ) - ( absint( $args['daily_range'] ) * DAY_IN_SECONDS ) ),
					'before'    => current_time( 'mysql' ),
					'inclusive' => true,
				),
			);

			/**
			 * Filter the date_query passed to WP_Query.
			 *
			 * @since 3.3.0
			 *
			 * @param array   $date_query Array of date parameters to be passed to WP_Query.
			 * @param array   $args       Arguments array.
			 */
			$args['date_query'] = apply_filters( 'crp_query_date_query', $date_query, $args );

			/**
			 * Filter the meta_query passed to WP_Query.
			 *
			 * @since 3.3.0
			 *
			 * @param array   $meta_query Array of meta_query parameters.
			 * @param array   $args       Arguments array.
			 */
			$meta_query = apply_filters( 'crp_query_meta_query', array(), $args ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query

			// Add a relation key if more than one $meta_query.
			if ( count( $meta_query ) > 1 ) {
				/**
				 * Filter the meta_query relation parameter.
				 *
				 * @since 3.3.0
				 *
				 * @param string  $relation The logical relationship between each inner meta_query array when there is more than one. Default is 'AND'.
				 * @param array   $args     Arguments array.
				 */
				$meta_query['relation'] = apply_filters( 'crp_query_meta_query_relation', 'AND', $args );
			}

			$args['meta_query'] = $meta_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query

			// Set post_status.
			$args['post_status'] = empty( $args['post_status'] ) ? array( 'publish', 'inherit' ) : $args['post_status'];

			// Set post__not_in for WP_Query using exclude_post_ids.
			$args['post__not_in'] = $this->exclude_post_ids( $args );

			// Same author.
			if ( isset( $args['same_author'] ) && $args['same_author'] ) {
				$args['author'] = $source_post->post_author;
			}

			// Disable contextual matching.
			if ( ! empty( $args['disable_contextual'] ) ) {
				/* If post or page and we're not disabling custom post types */
				if ( ( 'post' === $source_post->post_type || 'page' === $source_post->post_type ) && ( $args['disable_contextual_cpt'] ) ) {
					$this->enable_relevance = true;
				} else {
					$this->enable_relevance = false;
				}
			}

			// Unset what we don't need.
			unset( $args['title'] );
			unset( $args['blank_output'] );
			unset( $args['blank_output_text'] );
			unset( $args['show_excerpt'] );
			unset( $args['excerpt_length'] );
			unset( $args['show_date'] );
			unset( $args['show_author'] );
			unset( $args['title_length'] );
			unset( $args['link_new_window'] );
			unset( $args['link_nofollow'] );
			unset( $args['before_list'] );
			unset( $args['after_list'] );
			unset( $args['before_list_item'] );
			unset( $args['after_list_item'] );

			/**
			 * Filters the arguments of the query.
			 *
			 * @since 3.0.0
			 *
			 * @param array     $args The arguments of the query.
			 * @param CRP_Query $this The CRP_Query instance (passed by reference).
			 */
			$this->query_args = apply_filters_ref_array( 'crp_query_args', array( $args, &$this ) );
		}

		/**
		 * Get the MATCH sql.
		 *
		 * @since 3.0.0
		 *
		 * @return string  Updated Fields
		 */
		public function get_match_sql() {
			global $wpdb;

			// Are we matching only the title or the post content as well?
			$match_fields = array(
				"$wpdb->posts.post_title",
			);

			$match_fields_content = array(
				$this->strip_stopwords( $this->source_post->post_title ),
			);

			if ( $this->query_args['match_content'] ) {
				$match_fields[]         = "$wpdb->posts.post_content";
				$match_fields_content[] = $this->strip_stopwords( crp_excerpt( $this->source_post, min( $this->query_args['match_content_words'], CRP_MAX_WORDS ), false ) );
			}

			if ( isset( $this->crp_post_meta['keyword'] ) ) {
				$match_fields_content = array(
					$this->crp_post_meta['keyword'],
				);
			}

			/**
			 * Filter the fields that are to be matched.
			 *
			 * @since 2.2.0
			 * @since 2.9.3 Added $args
			 * @since 3.0.0 Changed second argument from post ID to WP_Post object.
			 *
			 * @param array   $match_fields Array of fields to be matched.
			 * @param WP_Post $source_post  Source Post instance.
			 * @param array   $query_args   Arguments array.
			 */
			$match_fields = apply_filters( 'crp_posts_match_fields', $match_fields, $this->source_post, $this->query_args );

			/**
			 * Filter the content of the fields that are to be matched.
			 *
			 * @since 2.2.0
			 * @since 2.9.3 Added $args
			 * @since 3.0.0 Changed second argument from post ID to WP_Post object.
			 *
			 * @param array $match_fields_content Array of content of fields to be matched
			 * @param WP_Post $source_post  Source Post instance.
			 * @param array   $query_args   Arguments array.
			 */
			$match_fields_content = apply_filters( 'crp_posts_match_fields_content', $match_fields_content, $this->source_post, $this->query_args );

			// Convert our arrays into their corresponding strings after they have been filtered.
			$this->match_fields = implode( ',', $match_fields );
			$this->stuff        = implode( ' ', $match_fields_content );

			// Create the base MATCH clause.
			$match = $wpdb->prepare( ' MATCH (' . $this->match_fields . ') AGAINST (%s) ', $this->stuff ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

			return $match;
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
		public function strip_stopwords( $subject = '', $search = '', $replace = '' ) {

			if ( empty( $search ) ) {
				$search = $this->get_search_stopwords();
				array_push( $search, 'from', 'where' );
			}

			foreach ( (array) $search as $s ) {
				$pattern[] = '/\b' . $s . '\b/ui';
			}
			$output = preg_replace( $pattern, $replace, $subject );
			$output = preg_replace( '/\s+/', ' ', $output );

			return $output;
		}

		/**
		 * Modify the SELECT clause - posts_fields.
		 *
		 * @since 3.0.0
		 *
		 * @param string   $fields The SELECT clause of the query.
		 * @param WP_Query $query  The WP_Query instance.
		 * @return string  Updated Fields
		 */
		public function posts_fields( $fields, $query ) {

			// Return if it is not a CRP_Query.
			if ( true !== $query->get( 'crp_query' ) ) {
				return $fields;
			}

			if ( $this->enable_relevance ) {
				$match   = ', ' . $this->get_match_sql() . ' as score ';
				$fields .= $match;
			}

			/**
			 * Filters the posts_fields of CRP_Query after processing and before returning.
			 *
			 * @since 3.2.0
			 *
			 * @param string   $fields The SELECT clause of the query.
			 * @param WP_Query $query  The WP_Query instance.
			 */
			$fields = apply_filters( 'crp_query_posts_fields', $fields, $query );

			return $fields;
		}

		/**
		 * Modify the posts_join clause.
		 *
		 * @since 3.0.0
		 *
		 * @param string   $join  The JOIN clause of the query.
		 * @param WP_Query $query The WP_Query instance.
		 * @return string  Updated JOIN
		 */
		public function posts_join( $join, $query ) {
			global $wpdb;

			// Return if it is not a CRP_Query.
			if ( true !== $query->get( 'crp_query' ) ) {
				return $join;
			}

			if ( ! empty( $this->query_args['match_all'] ) || ( isset( $this->query_args['no_of_common_terms'] ) && absint( $this->query_args['no_of_common_terms'] ) > 1 ) ) {
				$join .= " INNER JOIN $wpdb->term_relationships AS crp_tr ON ($wpdb->posts.ID = crp_tr.object_id) ";
				$join .= " INNER JOIN $wpdb->term_taxonomy AS crp_tt ON (crp_tr.term_taxonomy_id = crp_tt.term_taxonomy_id) ";
			}

			/**
			 * Filters the posts_join of CRP_Query after processing and before returning.
			 *
			 * @since 3.2.0
			 *
			 * @param string   $join  The JOIN clause of the query.
			 * @param WP_Query $query The WP_Query instance.
			 */
			$join = apply_filters( 'crp_query_posts_join', $join, $query );

			return $join;
		}

		/**
		 * Modify the posts_where clause.
		 *
		 * @since 3.0.0
		 *
		 * @param string   $where The WHERE clause of the query.
		 * @param WP_Query $query The WP_Query instance.
		 * @return string  Updated WHERE
		 */
		public function posts_where( $where, $query ) {
			global $wpdb;

			// Return if it is not a CRP_Query.
			if ( true !== $query->get( 'crp_query' ) ) {
				return $where;
			}

			if ( $this->enable_relevance ) {

				$match = ' AND ' . $this->get_match_sql();

				/**
				 * Filter the MATCH clause of the query.
				 *
				 * @since 2.1.0
				 * @since 2.9.0 Added $match_fields
				 * @since 2.9.3 Added $args
				 * @since 3.0.0 Changed third argument from post ID to WP_Post object.
				 *
				 * @param string  $match        The MATCH section of the WHERE clause of the query.
				 * @param string  $stuff        String to match fulltext with.
				 * @param WP_Post $source_post  Source Post instance.
				 * @param string  $match_fields Fields to match.
				 * @param array   $args         Arguments array.
				 */
				$match = apply_filters( 'crp_posts_match', $match, $this->stuff, $this->source_post, $this->match_fields, $this->query_args );

				$where .= $match;

			}

			if ( isset( $this->crp_post_meta['exclude_words'] ) ) {

				$n          = '%';
				$excludeand = '';
				$exclude    = '';

				$exclude_words = explode( ',', $this->crp_post_meta['exclude_words'] );
				$exclude_words = array_filter( $exclude_words );
				foreach ( (array) $exclude_words as $word ) {
					$like_op    = 'NOT LIKE';
					$andor_op   = 'AND';
					$like       = $n . $wpdb->esc_like( strtolower( $word ) ) . $n;
					$exclude   .= $wpdb->prepare( "{$excludeand}(({$wpdb->posts}.post_title $like_op %s) $andor_op ({$wpdb->posts}.post_content $like_op %s))", $like, $like ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					$excludeand = ' AND ';
				}

				if ( ! empty( $exclude ) ) {
					$where .= " AND ({$exclude}) ";
				}
			}

			/**
			 * Filters the posts_where of CRP_Query after processing and before returning.
			 *
			 * @since 3.2.0
			 *
			 * @param string   $where The WHERE clause of the query.
			 * @param WP_Query $query  The WP_Query instance.
			 */
			$where = apply_filters( 'crp_query_posts_where', $where, $query );

			return $where;
		}

		/**
		 * Modify the posts_orderby clause.
		 *
		 * @since 3.0.0
		 *
		 * @param string   $orderby  The ORDER BY clause of the query.
		 * @param WP_Query $query The WP_Query instance.
		 * @return string  Updated ORDER BY
		 */
		public function posts_orderby( $orderby, $query ) {
			global $wpdb;

			// Return if it is not a CRP_Query.
			if ( true !== $query->get( 'crp_query' ) ) {
				return $orderby;
			}

			// If orderby is set, then this was done intentionally and we don't make any modifications.
			if ( ! empty( $query->get( 'orderby' ) ) ) {
				return $orderby;
			}

			if ( $this->enable_relevance ) {
				$orderby = ' score DESC ';
			}

			// Set order by in case of date.
			if ( isset( $this->query_args['ordering'] ) && 'date' === $this->query_args['ordering'] ) {
				$orderby = " $wpdb->posts.post_date DESC ";
			}

			/**
			 * Filters the posts_orderby of CRP_Query after processing and before returning.
			 *
			 * @since 3.2.0
			 *
			 * @param string   $orderby The SELECT clause of the query.
			 * @param WP_Query $query   The WP_Query instance.
			 */
			$orderby = apply_filters( 'crp_query_posts_orderby', $orderby, $query );

			return $orderby;
		}

		/**
		 * Modify the completed SQL query before sending.
		 *
		 * @since 3.0.0
		 *
		 * @param string   $sql  The complete SQL query.
		 * @param WP_Query $query The WP_Query instance.
		 * @return string  Updated SQL query.
		 */
		public function posts_request( $sql, $query ) {
			global $wpdb;

			$conditions = array();

			// Return if it is not a CRP_Query.
			if ( true !== $query->get( 'crp_query' ) ) {
				return $sql;
			}

			if ( ! empty( $this->query_args['match_all'] ) && ! empty( $this->query_args['taxonomy_count'] ) ) {
				$conditions[] = $wpdb->prepare( 'COUNT(DISTINCT crp_tt.taxonomy) = %d', $this->query_args['taxonomy_count'] );
			}
			if ( isset( $this->query_args['no_of_common_terms'] ) && absint( $this->query_args['no_of_common_terms'] ) > 1 ) {
				$conditions[] = $wpdb->prepare( 'COUNT(DISTINCT crp_tt.term_id) >= %d', absint( $this->query_args['no_of_common_terms'] ) );
			}

			if ( ! empty( $conditions ) ) {
				$conditions = implode( ' AND ', $conditions );
				$having     = "HAVING ( {$conditions} ) ORDER BY";

				$sql = str_replace(
					'ORDER BY',
					$having,
					$sql
				);

			}

			/**
			 * Filters the posts_request of CRP_Query after processing and before returning.
			 *
			 * @since 3.2.0
			 *
			 * @param string   $sql   The SQL Query.
			 * @param WP_Query $query The WP_Query instance.
			 */
			$sql = apply_filters( 'crp_query_posts_request', $sql, $query );

			return $sql;
		}

		/**
		 * Filter posts_pre_query to allow caching to work.
		 *
		 * @since 3.0.0
		 *
		 * @param WP_Post[] $posts Array of post data.
		 * @param WP_Query  $query The WP_Query instance.
		 * @return WP_Post[] Updated Array of post objects.
		 */
		public function posts_pre_query( $posts, $query ) {

			// Return if it is not a CRP_Query.
			if ( true !== $query->get( 'crp_query' ) ) {
				return $posts;
			}

			$post_ids = array();

			// Check the cache if there are any posts saved.
			if ( ! empty( $this->query_args['cache_posts'] ) && ! ( is_preview() || is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) ) {

				$meta_key = crp_cache_get_key( $this->input_query_args );

				$cached_data = get_crp_cache( $this->source_post->ID, $meta_key );
				if ( ! empty( $cached_data ) ) {
					$post_ids       = $cached_data;
					$this->in_cache = true;
				}
			}

			if ( ! empty( $this->manual_related ) && ( $this->no_of_manual_related >= $this->query_args['limit'] ) ) {
				$post_ids = array_merge( $post_ids, $this->manual_related );
			}

			if ( ! empty( $post_ids ) ) {
				$posts                = get_posts(
					array(
						'post__in'    => array_unique( $post_ids ),
						'fields'      => $query->get( 'fields' ),
						'orderby'     => 'post__in',
						'numberposts' => $query->get( 'posts_per_page' ),
						'post_type'   => $query->get( 'post_type' ),
					)
				);
				$query->found_posts   = count( $posts );
				$query->max_num_pages = ceil( $query->found_posts / $query->get( 'posts_per_page' ) );
			}

			/**
			 * Filters the posts_pre_query of CRP_Query after processing and before returning.
			 *
			 * @since 3.2.0
			 *
			 * @param WP_Post[] $posts Array of post data.
			 * @param WP_Query  $query The WP_Query instance.
			 */
			$posts = apply_filters( 'crp_query_posts_pre_query', $posts, $query );

			return $posts;
		}

		/**
		 * Modify the array of retrieved posts.
		 *
		 * @since 3.0.0
		 *
		 * @param WP_Post[] $posts Array of post objects.
		 * @param WP_Query  $query The WP_Query instance (passed by reference).
		 * @return string  Updated Array of post objects.
		 */
		public function the_posts( $posts, $query ) {

			// Return if it is not a CRP_Query.
			if ( true !== $query->get( 'crp_query' ) ) {
				return $posts;
			}

			// Support caching to speed up retrieval.
			if ( ! empty( $this->query_args['cache_posts'] ) && ! $this->in_cache && ! ( is_preview() || is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) ) {
				$meta_key = crp_cache_get_key( $this->input_query_args );
				$post_ids = wp_list_pluck( $query->posts, 'ID' );

				set_crp_cache( $this->source_post->ID, $meta_key, $post_ids );
			}

			// Shuffle posts if random order is set.
			if ( $this->random_order ) {
				shuffle( $posts );
			}

			// Related posts by meta_key.
			if ( ! empty( $this->query_args['related_meta_keys'] ) ) {
				$related_meta_query = array();
				$related_meta_keys  = wp_parse_list( $this->query_args['related_meta_keys'] );
				foreach ( $related_meta_keys as $related_meta_key ) {
					$related_meta_value = (string) get_post_meta( $this->source_post->ID, $related_meta_key, true );
					if ( ! empty( $related_meta_value ) ) {
						$related_meta_query[] = array(
							'key'   => $related_meta_key,
							'value' => $related_meta_value,
						);
					}
				}

				if ( count( $related_meta_query ) > 1 ) {
					/**
					 * Filter the meta_query relation parameter for related posts by meta_key.
					 *
					 * @since 3.3.0
					 *
					 * @param string  $relation The logical relationship between each inner meta_query array when there is more than one. Default is 'OR'.
					 */
					$related_meta_query['relation'] = apply_filters( 'crp_query_related_meta_query_relation', 'OR' );
				}

				if ( ! empty( $related_meta_query ) ) {
					$meta_posts = get_posts(
						array(
							'post__not_in' => $this->exclude_post_ids( $this->query_args ),
							'fields'       => $query->get( 'fields' ),
							'numberposts'  => $query->get( 'posts_per_page' ),
							'post_type'    => $query->get( 'post_type' ),
							'meta_query'   => $related_meta_query, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
						)
					);
					$posts      = array_merge( $meta_posts, $posts );
				}
			}

			// Manual Posts (manual_related - set via the Post Meta) or Include Posts (can be set as a parameter).
			$post_ids = array();

			if ( ! empty( $this->manual_related ) && ( $this->no_of_manual_related < $this->query_args['limit'] ) ) {
				$post_ids = array_merge( $post_ids, $this->manual_related );
			}
			if ( ! empty( $post_ids ) ) {
				$extra_posts = get_posts(
					array(
						'post__in'    => array_unique( $post_ids ),
						'fields'      => $query->get( 'fields' ),
						'orderby'     => 'post__in',
						'numberposts' => '-1',
						'post_type'   => 'any',
					)
				);
				$posts       = array_merge( $extra_posts, $posts );
			}

			/**
			 * Set the flag if CRP should fill random posts if there is a shortage of related posts.
			 *
			 * @since 3.2.0
			 *
			 * @param bool      $fill_random_posts Fill random posts flag. Default false.
			 * @param WP_Post[] $posts             Array of post objects.
			 * @param WP_Query  $query             The WP_Query instance.
			 */
			$fill_random_posts = apply_filters( 'crp_fill_random_posts', false, $posts, $query );

			if ( $fill_random_posts ) {
				$no_of_random_posts = $this->query_args['limit'] - count( $posts );
				if ( $no_of_random_posts > 0 ) {
					$random_posts = get_posts(
						array(
							'fields'      => $query->get( 'fields' ),
							'orderby'     => 'rand',
							'numberposts' => $no_of_random_posts,
							'post_type'   => $query->get( 'post_type' ),
						)
					);
					$posts        = array_merge( $posts, $random_posts );
				}
			}

			/**
			 * Filter array of WP_Post objects before it is returned to the CRP_Query instance.
			 *
			 * @since 1.9
			 * @since 2.9.3 Added $args
			 *
			 * @param WP_Post[] $posts Array of post objects.
			 * @param array     $args  Arguments array.
			 * @param WP_Query  $query The WP_Query instance.
			 */
			return apply_filters( 'crp_query_the_posts', $posts, $this->query_args, $query );
		}

		/**
		 * Exclude Post IDs. Allows other plugins/functions to hook onto this and extend the list.
		 *
		 * @param array $args Array of arguments for CRP_Query.
		 * @return array Array of post IDs to exclude.
		 */
		public function exclude_post_ids( $args ) {

			$exclude_post_ids = empty( $args['exclude_post_ids'] ) ? array() : wp_parse_id_list( $args['exclude_post_ids'] );

			/**
			 * Filter exclude post IDs array.
			 *
			 * @since 2.3.0
			 * @since 2.9.3 Added $args
			 * @since 3.2.0 Added $source_post
			 *
			 * @param array   $exclude_post_ids Array of post IDs.
			 * @param array   $args             Arguments array.
			 * @param WP_Post $source_post      Source post.
			 */
			$exclude_post_ids = apply_filters( 'crp_exclude_post_ids', $exclude_post_ids, $args, $this->source_post );

			$exclude_post_ids[] = $this->source_post->ID;

			return $exclude_post_ids;
		}

	}
endif;

<?php
/**
 * Query API: CRP_Query class
 *
 * @package Contextual_Related_Posts
 * @since 3.0.0
 */

use WebberZone\Contextual_Related_Posts\CRP_Core_Query;

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
		 * Constructor.
		 *
		 * @since 3.0.0
		 *
		 * @param array $args Query arguments.
		 */
		public function __construct( $args = array() ) {
			$args = wp_parse_args( $args, array( 'is_crp_query' => true ) );

			// Get query instance.
			$crp = new CRP_Core_Query( $args );

			// Register query filters.
			$this->register_query_filters( $crp );

			parent::__construct( $crp->query_args );

			// Remove filters after use.
			$this->unregister_query_filters( $crp );
		}

		/**
		 * Register query filters.
		 *
		 * @since 4.0.0
		 *
		 * @param object $query Query instance.
		 */
		public function register_query_filters( $query ) {
			add_filter( 'pre_get_posts', array( $query, 'pre_get_posts' ), 10 );
			add_filter( 'posts_fields', array( $query, 'posts_fields' ), 10, 2 );
			add_filter( 'posts_join', array( $query, 'posts_join' ), 10, 2 );
			add_filter( 'posts_where', array( $query, 'posts_where' ), 10, 2 );
			add_filter( 'posts_orderby', array( $query, 'posts_orderby' ), 10, 2 );
			add_filter( 'posts_groupby', array( $query, 'posts_groupby' ), 10, 2 );
			add_filter( 'posts_request', array( $query, 'posts_request' ), 10, 2 );
			add_filter( 'posts_pre_query', array( $query, 'posts_pre_query' ), 10, 2 );
			add_filter( 'the_posts', array( $query, 'the_posts' ), 10, 2 );
		}

		/**
		 * Unregister query filters.
		 *
		 * @since 4.0.0
		 *
		 * @param object $query Query instance.
		 */
		public function unregister_query_filters( $query ) {
			remove_filter( 'pre_get_posts', array( $query, 'pre_get_posts' ) );
			remove_filter( 'posts_fields', array( $query, 'posts_fields' ) );
			remove_filter( 'posts_join', array( $query, 'posts_join' ) );
			remove_filter( 'posts_where', array( $query, 'posts_where' ) );
			remove_filter( 'posts_orderby', array( $query, 'posts_orderby' ) );
			remove_filter( 'posts_groupby', array( $query, 'posts_groupby' ) );
			remove_filter( 'posts_request', array( $query, 'posts_request' ) );
			remove_filter( 'posts_pre_query', array( $query, 'posts_pre_query' ) );
			remove_filter( 'the_posts', array( $query, 'the_posts' ) );
		}
	}
endif;

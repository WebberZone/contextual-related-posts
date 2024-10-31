<?php
/**
 * Query API: CRP_Query class
 *
 * @package Contextual_Related_Posts
 * @since 3.0.0
 */

use WebberZone\Contextual_Related_Posts\CRP;

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
		 * Main constructor.
		 *
		 * @since 3.0.0
		 *
		 * @param array|string $args The Query variables. Accepts an array or a query string.
		 */
		public function __construct( $args = array() ) {
			$args = wp_parse_args( $args, array( 'is_crp_query' => true ) );
			$crp  = new CRP( $args );

			add_filter( 'pre_get_posts', array( $crp, 'pre_get_posts' ), 10 );
			add_filter( 'posts_fields', array( $crp, 'posts_fields' ), 10, 2 );
			add_filter( 'posts_join', array( $crp, 'posts_join' ), 10, 2 );
			add_filter( 'posts_where', array( $crp, 'posts_where' ), 10, 2 );
			add_filter( 'posts_orderby', array( $crp, 'posts_orderby' ), 10, 2 );
			add_filter( 'posts_groupby', array( $crp, 'posts_groupby' ), 10, 2 );
			add_filter( 'posts_request', array( $crp, 'posts_request' ), 10, 2 );
			add_filter( 'posts_pre_query', array( $crp, 'posts_pre_query' ), 10, 2 );
			add_filter( 'the_posts', array( $crp, 'the_posts' ), 10, 2 );

			parent::__construct( $crp->query_args );

			// Remove filters after use.
			remove_filter( 'pre_get_posts', array( $crp, 'pre_get_posts' ) );
			remove_filter( 'posts_fields', array( $crp, 'posts_fields' ) );
			remove_filter( 'posts_join', array( $crp, 'posts_join' ) );
			remove_filter( 'posts_where', array( $crp, 'posts_where' ) );
			remove_filter( 'posts_orderby', array( $crp, 'posts_orderby' ) );
			remove_filter( 'posts_groupby', array( $crp, 'posts_groupby' ) );
			remove_filter( 'posts_request', array( $crp, 'posts_request' ) );
			remove_filter( 'posts_pre_query', array( $crp, 'posts_pre_query' ) );
			remove_filter( 'the_posts', array( $crp, 'the_posts' ) );
		}
	}
endif;

<?php
/**
 * Registers Contextual Related Posts abilities.
 *
 * @package WebberZone\Contextual_Related_Posts
 */

namespace WebberZone\Contextual_Related_Posts;

use WebberZone\Contextual_Related_Posts\Frontend\REST_API;
use WebberZone\Contextual_Related_Posts\Util\Hook_Registry;

if ( ! defined( 'WPINC' ) ) {
	exit;
}

/**
 * Registers the shared Contextual Related Posts abilities.
 *
 * @since 4.5.0
 */
class Abilities {

	/**
	 * Register ability hooks.
	 *
	 * @since 4.5.0
	 */
	public function __construct() {
		// The Abilities API arrived in WordPress 6.9; without it there is nothing to register.
		if ( ! Feature_Manager::is_enabled( 'abilities_api' ) || ! function_exists( 'wp_register_ability' ) ) {
			return;
		}

		Hook_Registry::add_action( 'wp_abilities_api_categories_init', array( $this, 'register_category' ) );
		Hook_Registry::add_action( 'wp_abilities_api_init', array( $this, 'register_abilities' ) );
	}

	/**
	 * Register the WebberZone ability category if it is not already present.
	 *
	 * @since 4.5.0
	 */
	public function register_category(): void {
		if ( \wp_has_ability_category( 'webberzone' ) ) {
			return;
		}

		\wp_register_ability_category(
			'webberzone',
			array(
				'label'       => __( 'WebberZone', 'contextual-related-posts' ),
				'description' => __( 'Abilities provided by WebberZone plugins.', 'contextual-related-posts' ),
			)
		);
	}

	/**
	 * Register the shared abilities.
	 *
	 * @since 4.5.0
	 */
	public function register_abilities(): void {
		\wp_register_ability(
			'contextual-related-posts/get-related',
			array(
				'label'               => __( 'Get Related Posts', 'contextual-related-posts' ),
				'description'         => __( 'Returns posts related to a source post. Use this to find related content, with optional post type and taxonomy filters. Results include each post ID, title, URL, and excerpt.', 'contextual-related-posts' ),
				'category'            => 'webberzone',
				'input_schema'        => array(
					'type'                 => 'object',
					'required'             => array( 'post_id' ),
					'additionalProperties' => false,
					'properties'           => array(
						'post_id'       => array(
							'type'        => 'integer',
							'minimum'     => 1,
							'description' => __( 'ID of the post to find related posts for.', 'contextual-related-posts' ),
						),
						'limit'         => array(
							'type'        => 'integer',
							'default'     => 6,
							'minimum'     => 1,
							'maximum'     => 100,
							'description' => __( 'Maximum number of related posts to return.', 'contextual-related-posts' ),
						),
						'post_types'    => array(
							'type'        => 'array',
							'items'       => array( 'type' => 'string' ),
							'description' => __( 'Post type names to include.', 'contextual-related-posts' ),
						),
						'include_terms' => array(
							'type'        => 'array',
							'items'       => array(
								'type'    => 'integer',
								'minimum' => 1,
							),
							'description' => __( 'Term taxonomy IDs that related posts must match.', 'contextual-related-posts' ),
						),
						'exclude_terms' => array(
							'type'        => 'array',
							'items'       => array(
								'type'    => 'integer',
								'minimum' => 1,
							),
							'description' => __( 'Term taxonomy IDs to exclude from related posts.', 'contextual-related-posts' ),
						),
					),
				),
				'output_schema'       => array(
					'type'  => 'array',
					'items' => array(
						'type'                 => 'object',
						'required'             => array( 'id', 'title', 'url', 'excerpt' ),
						'additionalProperties' => false,
						'properties'           => array(
							'id'      => array( 'type' => 'integer' ),
							'title'   => array( 'type' => 'string' ),
							'url'     => array(
								'type'   => 'string',
								'format' => 'uri',
							),
							'excerpt' => array( 'type' => 'string' ),
						),
					),
				),
				'execute_callback'    => array( $this, 'get_related_posts' ),
				'permission_callback' => array( $this, 'can_get_related_posts' ),
				'meta'                => array(
					'public'       => true,
					'show_in_rest' => true,
					'annotations'  => array(
						'readonly'    => true,
						'destructive' => false,
						'idempotent'  => true,
					),
				),
			)
		);
	}

	/**
	 * Check whether the current user can read the source post through the REST API.
	 *
	 * @since 4.5.0
	 *
	 * @param mixed $input Ability input.
	 * @return bool Whether the source post is readable.
	 */
	public function can_get_related_posts( $input ): bool {
		if (
			! is_array( $input )
			|| ! isset( $input['post_id'] )
			|| ! rest_is_integer( $input['post_id'] )
			|| (float) $input['post_id'] < 1
		) {
			return false;
		}

		$post = get_post( absint( $input['post_id'] ) );

		return $post instanceof \WP_Post && $this->is_readable_post( $post );
	}

	/**
	 * Get related posts for a readable source post.
	 *
	 * @since 4.5.0
	 *
	 * @param mixed $input Ability input.
	 * @return array|\WP_Error Related posts or an error.
	 */
	public function get_related_posts( $input ) {
		if (
			! is_array( $input )
			|| ! isset( $input['post_id'] )
			|| ! rest_is_integer( $input['post_id'] )
			|| (float) $input['post_id'] < 1
		) {
			return new \WP_Error(
				'crp_invalid_ability_input',
				__( 'A valid post ID is required.', 'contextual-related-posts' ),
				array( 'status' => 400 )
			);
		}

		$post = get_post( absint( $input['post_id'] ) );
		if ( ! $post instanceof \WP_Post || ! $this->is_readable_post( $post ) ) {
			return new \WP_Error(
				'ability_invalid_permissions',
				__( 'You do not have permission to read the source post.', 'contextual-related-posts' ),
				array( 'status' => 403 )
			);
		}

		$args = array(
			'post_id' => $post,
			'limit'   => isset( $input['limit'] ) ? (int) $input['limit'] : 6,
			'fields'  => 'all',
		);

		if ( isset( $input['post_types'] ) ) {
			$args['post_types'] = $input['post_types'];
		}
		if ( isset( $input['include_terms'] ) ) {
			$args['include_cat_ids'] = $input['include_terms'];
		}
		if ( isset( $input['exclude_terms'] ) ) {
			$args['exclude_categories'] = $input['exclude_terms'];
		}

		$related_posts = \get_crp_posts( $args );
		$results       = array();
		$rest_api      = new REST_API();

		foreach ( (array) $related_posts as $related_post ) {
			if ( ! $related_post instanceof \WP_Post ) {
				$related_post = get_post( $related_post );
			}

			if ( ! $related_post instanceof \WP_Post || ! $rest_api->check_read_permission( $related_post ) ) {
				continue;
			}

			$results[] = array(
				'id'      => (int) $related_post->ID,
				'title'   => $this->to_plain_text( get_the_title( $related_post ) ),
				'url'     => (string) get_permalink( $related_post ),
				'excerpt' => $this->to_plain_text( get_the_excerpt( $related_post ) ),
			);
		}

		return $results;
	}

	/**
	 * Reduce rendered post text to plain text for machine-readable output.
	 *
	 * Titles and excerpts pass through theme and core filters that add markup and HTML entities,
	 * neither of which belong in a schema an agent consumes.
	 *
	 * @since 4.5.0
	 *
	 * @param  string $text Rendered text.
	 * @return string Plain text.
	 */
	private function to_plain_text( string $text ): string {
		$text = wp_strip_all_tags( $text );
		$text = html_entity_decode( $text, ENT_QUOTES | ENT_HTML5, get_bloginfo( 'charset' ) );

		return trim( $text, " \t\n\r\0\x0B" );
	}

	/**
	 * Whether a post is readable through the Contextual Related Posts REST API.
	 *
	 * @since 4.5.0
	 *
	 * @param \WP_Post $post Post to check.
	 * @return bool Whether the post is readable.
	 */
	private function is_readable_post( \WP_Post $post ): bool {
		$rest_api = new REST_API();

		return $rest_api->check_read_permission( $post );
	}
}

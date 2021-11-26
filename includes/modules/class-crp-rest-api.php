<?php
/**
 * This class adds REST routes to update the count and return the list of related posts.
 *
 * @package CRP
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * CRP_REST_API class.
 *
 * @since 3.1.0
 */
class CRP_REST_API extends \WP_REST_Controller {

	/**
	 * Main constructor.
	 *
	 * @since 3.1.0
	 */
	public function __construct() {
		$this->namespace   = 'contextual-related-posts/v1';
		$this->posts_route = 'posts';
	}

	/**
	 * Initialises the Top 10 REST API adding the necessary routes.
	 *
	 * @since 3.1.0
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->posts_route,
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_items' ),
				'permission_callback' => array( $this, 'permissions_check' ),
				'args'                => $this->get_item_params(),
			)
		);
		register_rest_route(
			$this->namespace,
			'/' . $this->posts_route . '/(?P<id>[\d]+)',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_items' ),
				'permission_callback' => array( $this, 'permissions_check' ),
				'args'                => $this->get_item_params(),
			)
		);
	}

	/**
	 * Check if a given request has access to get items
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|bool
	 */
	public function permissions_check( $request ) {
		/**
		 * Permissions check flag for the CRP REST API.
		 *
		 * @since 3.1.0
		 *
		 * @param bool             $flag    Permisions flag.
		 * @param WP_REST_Response $request Array of post objects or post IDs.
		 */
		return apply_filters( 'crp_rest_api_permissions_check', true, $request );
	}

	/**
	 * Get related posts.
	 *
	 * @since 3.1.0
	 *
	 * @param WP_REST_Request $request WP Rest request.
	 * @return mixed|\WP_REST_Response Array of post objects or post IDs.
	 */
	public function get_items( $request ) {
		$id = absint( $request->get_param( 'id' ) );

		$error = new WP_Error(
			'rest_post_invalid_id',
			__( 'Invalid post ID.', 'contextual-related-posts' ),
			array( 'status' => 404 )
		);

		if ( $id <= 0 ) {
			return $error;
		}

		$post = get_post( $id );
		if ( empty( $post ) || empty( $post->ID ) || ! $this->check_read_permission( $post ) ) {
			return $error;
		}

		$related_posts = array();

		$args           = $request->get_params();
		$args['postid'] = $id;

		/**
		 * Filter the REST API arguments before they passed to get_crp_posts().
		 *
		 * @since 3.1.0
		 *
		 * @param array $args Arguments array.
		 * @param WP_REST_Request $request WP Rest request.
		 */
		$args = apply_filters( 'crp_rest_api_get_crp_posts_args', $args, $request );

		$results = get_crp_posts( $args );

		if ( is_array( $results ) && ! empty( $results ) ) {
			foreach ( $results as $related_post ) {
				if ( ! $this->check_read_permission( $related_post ) ) {
					continue;
				}

				$related_posts[] = $this->prepare_item( $related_post, $request );
			}
		}
		return rest_ensure_response( $related_posts );
	}

	/**
	 * Get a popular post by ID. Also includes the number of views.
	 *
	 * @since 3.1.0
	 *
	 * @param WP_Post         $related_post Popular Post object.
	 * @param WP_REST_Request $request WP Rest request.
	 * @return array|mixed   The formatted Popular Post object.
	 */
	public function prepare_item( $related_post, $request ) {

		// Need to prepare items for the rest response.
		$posts_controller = new \WP_REST_Posts_Controller( $related_post->post_type, $request );
		$data             = $posts_controller->prepare_item_for_response( $related_post, $request );

		return $this->prepare_response_for_collection( $data );
	}

	/**
	 * Get the arguments for fetching the related posts.
	 *
	 * @since 3.1.0
	 *
	 * @return array Top 10 REST API related posts arguments.
	 */
	public function get_item_params() {
		$args = array(
			'id'                 => array(
				'description' => __( 'Post ID.', 'contextual-related-posts' ),
				'type'        => 'integer',
				'required'    => true,
			),
			'limit'              => array(
				'description'       => __( 'Number of posts', 'contextual-related-posts' ),
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
			),
			'post_types'         => array(
				'description' => __( 'Post types (comma separated)', 'contextual-related-posts' ),
				'type'        => 'string',
			),
			'same_post_type'     => array(
				'description' => __( 'Same post type', 'contextual-related-posts' ),
				'type'        => 'boolean',
			),
			'same_author'        => array(
				'description' => __( 'Same author', 'contextual-related-posts' ),
				'type'        => 'boolean',
			),
			'exclude_post_ids'   => array(
				'description' => __( 'Post/page IDs to exclude (comma separated)', 'contextual-related-posts' ),
				'type'        => 'string',
			),
			'exclude_categories' => array(
				'description' => __( 'Taxonomy IDs from which posts are excluded (comma separated)', 'contextual-related-posts' ),
				'type'        => 'string',
			),
		);

		return apply_filters( 'crp_rest_api_get_item_params', $args );
	}

	/**
	 * Checks if a given post type can be viewed or managed.
	 *
	 * @since 3.1.0
	 *
	 * @param WP_Post_Type|string $post_type Post type name or object.
	 * @return bool Whether the post type is allowed in REST.
	 */
	protected function check_is_post_type_allowed( $post_type ) {
		if ( ! is_object( $post_type ) ) {
			$post_type = get_post_type_object( $post_type );
		}

		if ( ! empty( $post_type ) && ! empty( $post_type->show_in_rest ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Checks if a post can be read.
	 *
	 * Correctly handles posts with the inherit status.
	 *
	 * @since 3.1.0
	 *
	 * @param WP_Post $post Post object.
	 * @return bool Whether the post can be read.
	 */
	public function check_read_permission( $post ) {
		$post_type = get_post_type_object( $post->post_type );
		if ( ! $this->check_is_post_type_allowed( $post_type ) ) {
			return false;
		}

		// Is the post readable?
		if ( 'publish' === $post->post_status || current_user_can( 'read_post', $post->ID ) ) {
			return true;
		}

		$post_status_obj = get_post_status_object( $post->post_status );
		if ( $post_status_obj && $post_status_obj->public ) {
			return true;
		}

		// Can we read the parent if we're inheriting?
		if ( 'inherit' === $post->post_status && $post->post_parent > 0 ) {
			$parent = get_post( $post->post_parent );
			if ( $parent ) {
				return $this->check_read_permission( $parent );
			}
		}

		/*
		 * If there isn't a parent, but the status is set to inherit, assume
		 * it's published (as per get_post_status()).
		 */
		if ( 'inherit' === $post->post_status ) {
			return true;
		}

		return false;
	}
}

/**
 * Function to register our new routes from the controller.
 *
 * @since 3.1.0
 */
function crp_register_rest_routes() {
	$controller = new CRP_REST_API();
	$controller->register_routes();
}
add_action( 'rest_api_init', 'crp_register_rest_routes' );

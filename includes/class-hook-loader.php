<?php
/**
 * Hook Loader class.
 *
 * Handles all hook registrations and callbacks for the plugin.
 *
 * @package WebberZone\Contextual_Related_Posts
 */

namespace WebberZone\Contextual_Related_Posts;

use WebberZone\Contextual_Related_Posts\Admin\Activator;
use WebberZone\Contextual_Related_Posts\Frontend\Display;
use WebberZone\Contextual_Related_Posts\Util\Hook_Registry;

if ( ! defined( 'WPINC' ) ) {
	exit;
}

/**
 * Hook Loader class.
 *
 * Centralizes all hook registrations and their callback implementations.
 *
 * @since 3.5.0
 */
final class Hook_Loader {

	/**
	 * Constructor.
	 *
	 * @since 3.5.0
	 */
	public function __construct() {
		$this->register_hooks();
	}

	/**
	 * Register all plugin hooks.
	 *
	 * @since 3.5.0
	 */
	private function register_hooks(): void {
		$this->register_init_hooks();
		$this->register_content_hooks();
		$this->register_query_hooks();
	}

	/**
	 * Register initialization hooks.
	 *
	 * @since 3.5.0
	 */
	private function register_init_hooks(): void {
		Hook_Registry::add_action( 'init', array( $this, 'initiate_plugin' ) );
		Hook_Registry::add_action( 'widgets_init', array( $this, 'register_widgets' ) );
		Hook_Registry::add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
	}

	/**
	 * Register content filtering hooks.
	 *
	 * @since 3.5.0
	 */
	private function register_content_hooks(): void {
		$priority = (int) \crp_get_option( 'content_filter_priority', 10 );

		Hook_Registry::add_filter( 'the_content', array( $this, 'content_filter' ), $priority );
		Hook_Registry::add_filter( 'the_excerpt_rss', array( $this, 'content_filter' ), $priority );
		Hook_Registry::add_filter( 'the_content_feed', array( $this, 'content_filter' ), $priority );
	}

	/**
	 * Register query modification hooks.
	 *
	 * @since 3.5.0
	 */
	private function register_query_hooks(): void {
		Hook_Registry::add_action( 'parse_query', array( $this, 'parse_query' ) );
	}

	/**
	 * Initialise the plugin translations and media.
	 *
	 * @since 3.5.0
	 */
	public function initiate_plugin(): void {
		Frontend\Media_Handler::add_image_sizes();
	}

	/**
	 * Initialise the Contextual Related Posts widgets.
	 *
	 * @since 3.5.0
	 */
	public function register_widgets(): void {
		register_widget( '\WebberZone\Contextual_Related_Posts\Frontend\Widgets\Related_Posts_Widget' );
	}

	/**
	 * Function to register our new routes from the controller.
	 *
	 * @since 3.5.0
	 */
	public function register_rest_routes(): void {
		$controller = new Frontend\REST_API();
		$controller->register_routes();
	}

	/**
	 * Filter the content to add the related posts.
	 *
	 * @since 3.5.0
	 *
	 * @param string $content Post content.
	 * @return string Post content with related posts appended.
	 */
	public function content_filter( string $content ): string {
		return Display::content_filter( $content );
	}

	/**
	 * Hook into WP_Query to check if crp_query is set and is true. If so, we load the CRP query.
	 *
	 * @since 3.5.0
	 *
	 * @param \WP_Query $query The WP_Query object.
	 */
	public function parse_query( \WP_Query $query ): void {
		if ( true === $query->get( 'crp_query' ) ) {
			new CRP_Core_Query( $query->query_vars );
		}
	}
}

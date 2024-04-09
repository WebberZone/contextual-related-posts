<?php
/**
 * Main plugin class.
 *
 * @package WebberZone\Contextual_Related_Posts
 */

namespace WebberZone\Contextual_Related_Posts;

use WebberZone\Contextual_Related_Posts\Frontend\Display;

if ( ! defined( 'WPINC' ) ) {
	exit;
}

/**
 * Main plugin class.
 *
 * @since 3.5.0
 */
final class Main {
	/**
	 * The single instance of the class.
	 *
	 * @var Main
	 */
	private static $instance;

	/**
	 * Admin.
	 *
	 * @since 3.5.0
	 *
	 * @var object Admin.
	 */
	public $admin;

	/**
	 * Shortcodes.
	 *
	 * @since 3.5.0
	 *
	 * @var object Shortcodes.
	 */
	public $shortcodes;

	/**
	 * Blocks.
	 *
	 * @since 3.5.0
	 *
	 * @var object Blocks.
	 */
	public $blocks;

	/**
	 * Styles.
	 *
	 * @since 3.5.0
	 *
	 * @var object Styles.
	 */
	public $styles;

	/**
	 * Language Handler.
	 *
	 * @since 3.5.0
	 *
	 * @var object Language Handler.
	 */
	public $language;

	/**
	 * Pro modules.
	 *
	 * @since 3.5.0
	 *
	 * @var object Pro
	 */
	public $pro;

	/**
	 * Gets the instance of the class.
	 *
	 * @since 3.5.0
	 *
	 * @return Main
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
			self::$instance->init();
		}

		return self::$instance;
	}

	/**
	 * A dummy constructor.
	 *
	 * @since 3.5.0
	 */
	private function __construct() {
		// Do nothing.
	}

	/**
	 * Initializes the plugin.
	 *
	 * @since 3.5.0
	 */
	private function init() {
		$this->language   = new Frontend\Language_Handler();
		$this->styles     = new Frontend\Styles_Handler();
		$this->shortcodes = new Frontend\Shortcodes();
		$this->blocks     = new Frontend\Blocks\Blocks();
		$this->pro        = new Pro\Pro();

		$this->hooks();

		if ( is_admin() ) {
			$this->admin = new Admin\Admin();
		}
	}

	/**
	 * Run the hooks.
	 *
	 * @since 3.5.0
	 */
	public function hooks() {
		add_action( 'init', array( $this, 'initiate_plugin' ) );
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
		add_filter( 'the_content', array( $this, 'content_filter' ), \crp_get_option( 'content_filter_priority' ) );
		add_filter( 'the_excerpt_rss', array( $this, 'content_filter' ), \crp_get_option( 'content_filter_priority' ) );
		add_filter( 'the_content_feed', array( $this, 'content_filter' ), \crp_get_option( 'content_filter_priority' ) );
		add_action( 'parse_query', array( $this, 'parse_query' ) );
	}

	/**
	 * Initialise the plugin translations and media.
	 *
	 * @since 3.5.0
	 */
	public function initiate_plugin() {
		Frontend\Media_Handler::add_image_sizes();
	}

	/**
	 * Initialise the Top 10 widgets.
	 *
	 * @since 3.5.0
	 */
	public function register_widgets() {
		register_widget( '\WebberZone\Contextual_Related_Posts\Frontend\Widgets\Related_Posts_Widget' );
	}
	/**
	 * Function to register our new routes from the controller.
	 *
	 * @since 3.5.0
	 */
	public function register_rest_routes() {
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
	public function content_filter( $content ) {
		return Display::content_filter( $content );
	}

	/**
	 * Hook into WP_Query to check if crp_query is set and is true. If so, we load the CRP query.
	 *
	 * @since 3.5.0
	 *
	 * @param \WP_Query $query The WP_Query object.
	 */
	public function parse_query( $query ) {
		if ( true === $query->get( 'crp_query' ) ) {
			$crp_query = new \CRP_Query( $query->query_vars );

			add_filter( 'pre_get_posts', array( $crp_query, 'pre_get_posts' ), 10 );
			add_filter( 'posts_fields', array( $crp_query, 'posts_fields' ), 10, 2 );
			add_filter( 'posts_join', array( $crp_query, 'posts_join' ), 10, 2 );
			add_filter( 'posts_where', array( $crp_query, 'posts_where' ), 10, 2 );
			add_filter( 'posts_orderby', array( $crp_query, 'posts_orderby' ), 10, 2 );
			add_filter( 'posts_request', array( $crp_query, 'posts_request' ), 10, 2 );
			add_filter( 'posts_pre_query', array( $crp_query, 'posts_pre_query' ), 10, 2 );
			add_filter( 'the_posts', array( $crp_query, 'the_posts' ), 10, 2 );
		}
	}
}

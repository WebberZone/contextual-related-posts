<?php
/**
 * Main plugin class.
 *
 * @package WebberZone\Contextual_Related_Posts
 */

namespace WebberZone\Contextual_Related_Posts;

use WebberZone\Contextual_Related_Posts\Admin\Activator;
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
	private static ?self $instance = null;

	/**
	 * Admin.
	 *
	 * @since 3.5.0
	 *
	 * @var Admin\Admin
	 */
	public ?Admin\Admin $admin = null;

	/**
	 * Shortcodes.
	 *
	 * @since 3.5.0
	 *
	 * @var Frontend\Shortcodes
	 */
	public Frontend\Shortcodes $shortcodes;

	/**
	 * Blocks.
	 *
	 * @since 3.5.0
	 *
	 * @var Frontend\Blocks\Blocks
	 */
	public Frontend\Blocks\Blocks $blocks;

	/**
	 * Styles.
	 *
	 * @since 3.5.0
	 *
	 * @var Frontend\Styles_Handler
	 */
	public Frontend\Styles_Handler $styles;

	/**
	 * Language Handler.
	 *
	 * @since 3.5.0
	 *
	 * @var Frontend\Language_Handler
	 */
	public Frontend\Language_Handler $language;

	/**
	 * Gets the instance of the class.
	 *
	 * @since 3.5.0
	 *
	 * @return Main
	 */
	public static function get_instance(): self {
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
	private function init(): void {
		$this->language   = new Frontend\Language_Handler();
		$this->styles     = new Frontend\Styles_Handler();
		$this->shortcodes = new Frontend\Shortcodes();
		$this->blocks     = new Frontend\Blocks\Blocks();
		$this->hooks();
		if ( is_admin() ) {
			$this->admin = new Admin\Admin();
			if ( is_multisite() ) {
				new Admin\Network\Admin();
			}
		}
	}

	/**
	 * Run the hooks.
	 *
	 * @since 3.5.0
	 */
	public function hooks(): void {
		add_action( 'init', array( $this, 'initiate_plugin' ) );
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
		add_filter( 'the_content', array( $this, 'content_filter' ), \crp_get_option( 'content_filter_priority', 10 ) );
		add_filter( 'the_excerpt_rss', array( $this, 'content_filter' ), \crp_get_option( 'content_filter_priority', 10 ) );
		add_filter( 'the_content_feed', array( $this, 'content_filter' ), \crp_get_option( 'content_filter_priority', 10 ) );
		add_action( 'parse_query', array( $this, 'parse_query' ) );
		add_action(
			'activated_plugin',
			array( $this, 'activated_plugin' ),
			10,
			2
		);
		add_action( 'pre_current_active_plugins', array( $this, 'plugin_deactivated_notice' ) );
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
	 * Initialise the Top 10 widgets.
	 *
	 * @since 3.5.0
	 */
	public function register_widgets(): void {
		register_widget( '\\WebberZone\\Contextual_Related_Posts\\Frontend\\Widgets\\Related_Posts_Widget' );
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

	/**
	 * Checks if another version of CRP/CRP PRO is active and deactivates it.
	 * Hooked on `activated_plugin` so other plugin is deactivated when current plugin is activated.
	 *
	 * @since 3.5.0
	 *
	 * @param string $plugin        The plugin being activated.
	 * @param bool   $network_wide  Whether the plugin is being activated network-wide.
	 */
	public function activated_plugin( string $plugin, bool $network_wide ): void {
		if ( ! in_array( $plugin, array( 'contextual-related-posts/contextual-related-posts.php', 'contextual-related-posts-pro/contextual-related-posts.php' ), true ) ) {
			return;
		}
		Activator::activation_hook( $network_wide );
		$plugin_to_deactivate  = 'contextual-related-posts/contextual-related-posts.php';
		$deactivated_notice_id = '1';
		// If we just activated the free version, deactivate the pro version.
		if ( $plugin === $plugin_to_deactivate ) {
			$plugin_to_deactivate  = 'contextual-related-posts-pro/contextual-related-posts.php';
			$deactivated_notice_id = '2';
		}
		if ( is_multisite() && is_network_admin() ) {
			$active_plugins = (array) get_site_option( 'active_sitewide_plugins', array() );
			$active_plugins = array_keys( $active_plugins );
		} else {
			$active_plugins = (array) get_option( 'active_plugins', array() );
		}
		foreach ( $active_plugins as $plugin_basename ) {
			if ( $plugin_to_deactivate === $plugin_basename ) {
				set_transient( 'crp_deactivated_notice_id', $deactivated_notice_id, 1 * HOUR_IN_SECONDS );
				deactivate_plugins( $plugin_basename );
				return;
			}
		}
	}

	/**
	 * Displays a notice when either CRP or CRP PRO is automatically deactivated.
	 *
	 * @since 3.5.0
	 */
	public function plugin_deactivated_notice(): void {
		$deactivated_notice_id = (int) get_transient( 'crp_deactivated_notice_id' );
		if ( ! in_array( $deactivated_notice_id, array( 1, 2 ), true ) ) {
			return;
		}
		$message = __( "Contextual Related Posts and Contextual Related Posts PRO should not be active at the same time. We've automatically deactivated Contextual Related Posts.", 'contextual-related-posts' );
		if ( 2 === $deactivated_notice_id ) {
			$message = __( "Contextual Related Posts and Contextual Related Posts PRO should not be active at the same time. We've automatically deactivated Contextual Related Posts PRO.", 'contextual-related-posts' );
		}
		?>
			<div class="updated" style="border-left: 4px solid #ffba00;">
				<p>
					<?php
					echo esc_html( $message );
					?>
				</p>
			</div>
			<?php
			delete_transient( 'crp_deactivated_notice_id' );
	}
}

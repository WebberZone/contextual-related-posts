<?php
/**
 * Main plugin class.
 *
 * @package WebberZone\Contextual_Related_Posts
 */

namespace WebberZone\Contextual_Related_Posts;

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
	 * Pro modules.
	 *
	 * @since 3.5.0
	 *
	 * @var Pro\Pro|null
	 */
	public ?Pro\Pro $pro = null;

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
		// Initialize components.
		$this->language   = new Frontend\Language_Handler();
		$this->styles     = new Frontend\Styles_Handler();
		$this->shortcodes = new Frontend\Shortcodes();
		$this->blocks     = new Frontend\Blocks\Blocks();
		// Load all hooks.
		new Hook_Loader();
		// Initialize admin on init action to ensure translations are loaded.
		add_action( 'init', array( $this, 'init_admin' ) );
	}

	/**
	 * Initialize admin components.
	 *
	 * @since 4.1.0
	 */
	public function init_admin(): void {
		if ( is_admin() ) {
			$this->admin = new Admin\Admin();
			if ( is_multisite() ) {
				new Admin\Network\Admin();
			}
		}
	}
}

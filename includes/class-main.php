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
		// Initialize admin.
		if ( is_admin() ) {
			$this->admin = new Admin\Admin();
			if ( is_multisite() ) {
				new Admin\Network\Admin();
			}
		}
	}

	/**
	 * Display the pro upgrade banner.
	 *
	 * @since 4.1.0
	 *
	 * @param bool   $donate        Whether to show the donate banner.
	 * @param string $custom_text   Custom text to show in the banner.
	 */
	public static function pro_upgrade_banner( $donate = true, $custom_text = '' ) {
		if ( function_exists( __NAMESPACE__ . '\crp_freemius' ) && ! \WebberZone\Contextual_Related_Posts\crp_freemius()->is_paying() ) {
			?>
				<div id="pro-upgrade-banner">
					<div class="inside">
						<?php if ( ! empty( $custom_text ) ) : ?>
							<p><?php echo wp_kses_post( $custom_text ); ?></p>
						<?php endif; ?>

						<p><a href="https://webberzone.com/plugins/contextual-related-posts/pro/" target="_blank"><img src="<?php echo esc_url( WZ_CRP_PLUGIN_URL . 'includes/admin/images/crp-pro-banner.png' ); ?>" alt="<?php esc_html_e( 'Contextual Related Posts Pro - Buy now!', 'contextual-related-posts' ); ?>" width="300" height="300" style="max-width: 100%;" /></a></p>

						<?php if ( $donate ) : ?>							
							<p style="text-align:center;"><?php esc_html_e( 'OR', 'contextual-related-posts' ); ?></p>
							<p><a href="https://wzn.io/donate-crp" target="_blank"><img src="<?php echo esc_url( WZ_CRP_PLUGIN_URL . 'includes/admin/images/support.webp' ); ?>" alt="<?php esc_html_e( 'Support the development - Send us a donation today.', 'contextual-related-posts' ); ?>" width="300" height="169" style="max-width: 100%;" /></a></p>
						<?php endif; ?>
					</div>
				</div>
			<?php
		}
	}
}

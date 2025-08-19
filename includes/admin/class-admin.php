<?php
/**
 * Admin class.
 *
 * @package WebberZone\Contextual_Related_Posts\Admin
 */

namespace WebberZone\Contextual_Related_Posts\Admin;

use WebberZone\Contextual_Related_Posts\Util\Cache;
use WebberZone\Contextual_Related_Posts\Util\Hook_Registry;
use WebberZone\Contextual_Related_Posts\Admin\Admin_Notices;
use WebberZone\Contextual_Related_Posts\Admin\Admin_Notices_API;
use WebberZone\Contextual_Related_Posts\Admin\Settings_Wizard;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class to register the settings.
 *
 * @since 3.5.0
 */
class Admin {

	/**
	 * Settings API.
	 *
	 * @since 3.5.0
	 *
	 * @var Settings Settings API.
	 */
	public Settings $settings;

	/**
	 * Activator class.
	 *
	 * @since 3.5.0
	 *
	 * @var Activator Activator class.
	 */
	public Activator $activator;

	/**
	 * Metabox functions.
	 *
	 * @since 3.5.0
	 *
	 * @var Metabox Metabox functions.
	 */
	public Metabox $metabox;

	/**
	 * Tools page.
	 *
	 * @since 3.5.0
	 *
	 * @var Tools_Page Tools page.
	 */
	public Tools_Page $tools_page;

	/**
	 * Cache.
	 *
	 * @since 3.5.0
	 *
	 * @var Cache Cache.
	 */
	public Cache $cache;

	/**
	 * Bulk Edit.
	 *
	 * @since 3.5.0
	 *
	 * @var Bulk_Edit Bulk Edit.
	 */
	public Bulk_Edit $bulk_edit;

	/**
	 * Admin notices.
	 *
	 * @since 4.0.0
	 *
	 * @var Admin_Notices Admin notices.
	 */
	public Admin_Notices $admin_notices;

	/**
	 * Admin notices API.
	 *
	 * @since 4.0.0
	 *
	 * @var Admin_Notices_API Admin notices API.
	 */
	public Admin_Notices_API $admin_notices_api;

	/**
	 * Settings wizard.
	 *
	 * @since 4.1.0
	 *
	 * @var Settings_Wizard Settings wizard.
	 */
	public Settings_Wizard $settings_wizard;

	/**
	 * Settings Page in Admin area.
	 *
	 * @since 3.5.0
	 *
	 * @var string Settings Page.
	 */
	public string $settings_page;

	/**
	 * Prefix which is used for creating the unique filters and actions.
	 *
	 * @since 3.5.0
	 *
	 * @var string Prefix.
	 */
	public static string $prefix;

	/**
	 * Settings Key.
	 *
	 * @since 3.5.0
	 *
	 * @var string Settings Key.
	 */
	public string $settings_key;

	/**
	 * The slug name to refer to this menu by (should be unique for this menu).
	 *
	 * @since 3.5.0
	 *
	 * @var string Menu slug.
	 */
	public string $menu_slug;

	/**
	 * Main constructor class.
	 *
	 * @since 3.5.0
	 */
	public function __construct() {
		$this->hooks();

		// Initialise admin classes.
		$this->settings          = new Settings();
		$this->activator         = new Activator();
		$this->metabox           = new Metabox();
		$this->tools_page        = new Tools_Page();
		$this->cache             = new Cache();
		$this->bulk_edit         = new Bulk_Edit();
		$this->admin_notices_api = new Admin_Notices_API();
		$this->admin_notices     = new Admin_Notices();
		$this->settings_wizard   = new Settings_Wizard();
	}

	/**
	 * Run the hooks.
	 *
	 * @since 3.5.0
	 */
	public function hooks() {
		Hook_Registry::add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}

	/**
	 * Enqueue scripts in admin area.
	 *
	 * @since 3.0.0
	 */
	public function admin_enqueue_scripts() {
		$file_prefix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		wp_register_script(
			'crp-admin-js',
			WZ_CRP_PLUGIN_URL . "includes/admin/js/admin-scripts{$file_prefix}.js",
			array( 'jquery', 'jquery-ui-tabs', 'jquery-ui-datepicker' ),
			WZ_CRP_VERSION,
			true
		);
		wp_localize_script(
			'crp-admin-js',
			'crpAdmin',
			array(
				'ajaxurl'         => admin_url( 'admin-ajax.php' ),
				'nonce'           => wp_create_nonce( 'crp_admin_nonce' ),
				'copied'          => __( 'Copied!', 'contextual-related-posts' ),
				'copyToClipboard' => __( 'Copy to clipboard', 'contextual-related-posts' ),
				'copyError'       => __( 'Error copying to clipboard', 'contextual-related-posts' ),
			)
		);
		wp_register_style(
			'crp-admin-ui-css',
			WZ_CRP_PLUGIN_URL . "includes/admin/css/admin-styles{$file_prefix}.css",
			array(),
			WZ_CRP_VERSION
		);
	}

	/**
	 * Display admin sidebar.
	 *
	 * @since 3.5.0
	 */
	public static function display_admin_sidebar() {
		require_once WZ_CRP_PLUGIN_DIR . 'includes/admin/settings/sidebar.php';
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
		if ( function_exists( '\WebberZone\Contextual_Related_Posts\crp_freemius' ) && ! \WebberZone\Contextual_Related_Posts\crp_freemius()->is_paying() ) {
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

<?php
/**
 * Admin class.
 *
 * @package WebberZone\Contextual_Related_Posts\Admin
 */

namespace WebberZone\Contextual_Related_Posts\Admin;

use WebberZone\Contextual_Related_Posts\Util\Cache;

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
		$this->settings      = new Settings();
		$this->activator     = new Activator();
		$this->metabox       = new Metabox();
		$this->tools_page    = new Tools_Page();
		$this->cache         = new Cache();
		$this->bulk_edit     = new Bulk_Edit();
		$this->admin_notices = new Admin_Notices();
	}

	/**
	 * Run the hooks.
	 *
	 * @since 3.5.0
	 */
	public function hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'admin_notices', array( $this, 'fulltext_index_notice' ) );
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
			CRP_PLUGIN_URL . "includes/admin/js/admin-scripts{$file_prefix}.js",
			array( 'jquery', 'jquery-ui-tabs', 'jquery-ui-datepicker' ),
			CRP_VERSION,
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
			)
		);
		wp_register_style(
			'crp-admin-ui-css',
			CRP_PLUGIN_URL . "includes/admin/css/admin-styles{$file_prefix}.css",
			array(),
			CRP_VERSION
		);
	}

	/**
	 * Display admin sidebar.
	 *
	 * @since 3.5.0
	 */
	public static function display_admin_sidebar() {
		require_once CRP_PLUGIN_DIR . 'includes/admin/settings/sidebar.php';
	}

	/**
	 * Display admin notice if the fulltext indexes are not created.
	 *
	 * @since 4.0.0
	 */
	public function fulltext_index_notice() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Check if all indexes are installed.
		if ( ! Db::is_fulltext_index_installed() ) {
			?>
			<div class="notice notice-warning">
				<p>
					<?php esc_html_e( 'Contextual Related Posts: Some fulltext indexes are missing, which will affect the related posts.', 'contextual-related-posts' ); ?>
					<a href="<?php echo esc_url( admin_url( 'tools.php?page=crp_tools_page' ) ); ?>">
						<?php esc_html_e( 'Click here to recreate indexes.', 'contextual-related-posts' ); ?>
					</a>
				</p>
			</div>
			<?php
		}
	}
}

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
	 * @var object Settings API.
	 */
	public $settings;

	/**
	 * Activator class.
	 *
	 * @since 3.5.0
	 *
	 * @var object Activator class.
	 */
	public $activator;

	/**
	 * Metabox functions.
	 *
	 * @since 3.5.0
	 *
	 * @var object Metabox functions.
	 */
	public $metabox;

	/**
	 * Tools page.
	 *
	 * @since 3.5.0
	 *
	 * @var object Tools page.
	 */
	public $tools_page;

	/**
	 * Cache.
	 *
	 * @since 3.5.0
	 *
	 * @var object Cache.
	 */
	public $cache;

	/**
	 * Bulk Edit.
	 *
	 * @since 3.5.0
	 *
	 * @var object Bulk Edit.
	 */
	public $bulk_edit;

	/**
	 * Settings Page in Admin area.
	 *
	 * @since 3.5.0
	 *
	 * @var string Settings Page.
	 */
	public $settings_page;

	/**
	 * Prefix which is used for creating the unique filters and actions.
	 *
	 * @since 3.5.0
	 *
	 * @var string Prefix.
	 */
	public static $prefix;

	/**
	 * Settings Key.
	 *
	 * @since 3.5.0
	 *
	 * @var string Settings Key.
	 */
	public $settings_key;

	/**
	 * The slug name to refer to this menu by (should be unique for this menu).
	 *
	 * @since 3.5.0
	 *
	 * @var string Menu slug.
	 */
	public $menu_slug;

	/**
	 * Main constructor class.
	 *
	 * @since 3.5.0
	 */
	public function __construct() {
		$this->hooks();

		// Initialise admin classes.
		$this->settings   = new Settings\Settings();
		$this->activator  = new Activator();
		$this->metabox    = new Metabox();
		$this->tools_page = new Tools_Page();
		$this->cache      = new Cache();
		$this->bulk_edit  = new Bulk_Edit();
	}

	/**
	 * Run the hooks.
	 *
	 * @since 3.5.0
	 */
	public function hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
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
			'crp_admin',
			array(
				'nonce' => wp_create_nonce( 'crp_admin_nonce' ),
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
}

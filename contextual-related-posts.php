<?php
/**
 * Contextual Related Posts.
 *
 * Contextual Related Posts is the best related posts plugin for WordPress that
 * allows you to display a list of related posts on your website and in your feed.
 *
 * @package   Contextual_Related_Posts
 * @author    Ajay D'Souza
 * @license   GPL-2.0+
 * @link      https://webberzone.com
 * @copyright 2009-2023 Ajay D'Souza
 *
 * @wordpress-plugin
 * Plugin Name: Contextual Related Posts
 * Plugin URI:  https://webberzone.com/plugins/contextual-related-posts/
 * Description: Display related posts on your website or in your feed. Increase reader retention and reduce bounce rates
 * Version:     3.3.3
 * Author:      WebberZone
 * Author URI:  https://webberzone.com
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: contextual-related-posts
 * Domain Path: /languages
 * GitHub Plugin URI: https://github.com/WebberZone/contextual-related-posts/
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Holds the version of Contextual Related Posts.
 *
 * @since 2.9.3
 *
 * @var string Contextual Related Posts Version.
 */
if ( ! defined( 'CRP_VERSION' ) ) {
	define( 'CRP_VERSION', '3.3.3' );
}


/**
 * Holds the filesystem directory path (with trailing slash) for Contextual Related Posts.
 *
 * @since 2.3.0
 *
 * @var string Plugin Root File
 */
if ( ! defined( 'CRP_PLUGIN_FILE' ) ) {
	define( 'CRP_PLUGIN_FILE', __FILE__ );
}


/**
 * Holds the filesystem directory path (with trailing slash) for Contextual Related Posts.
 *
 * @since 2.3.0
 *
 * @var string Plugin folder path
 */
if ( ! defined( 'CRP_PLUGIN_DIR' ) ) {
	define( 'CRP_PLUGIN_DIR', plugin_dir_path( CRP_PLUGIN_FILE ) );
}

/**
 * Holds the filesystem directory path (with trailing slash) for Contextual Related Posts.
 *
 * @since 2.3.0
 *
 * @var string Plugin folder URL
 */
if ( ! defined( 'CRP_PLUGIN_URL' ) ) {
	define( 'CRP_PLUGIN_URL', plugin_dir_url( CRP_PLUGIN_FILE ) );
}

/**
 * Maximum words to match in the content.
 *
 * @since 2.3.0
 *
 * @var int Maximum number of words to match.
 */
if ( ! defined( 'CRP_MAX_WORDS' ) ) {
	define( 'CRP_MAX_WORDS', 100 );
}

/**
 * CRP Cache expiration time.
 *
 * @since 3.0.0
 *
 * @var int Cache time. Default is one month.
 */
if ( ! defined( 'CRP_CACHE_TIME' ) ) {
	define( 'CRP_CACHE_TIME', MONTH_IN_SECONDS );
}

/*
 *----------------------------------------------------------------------------
 * CRP modules & includes
 *----------------------------------------------------------------------------
 */

require_once CRP_PLUGIN_DIR . 'includes/admin/default-settings.php';
require_once CRP_PLUGIN_DIR . 'includes/admin/register-settings.php';
require_once CRP_PLUGIN_DIR . 'includes/plugin-activator.php';
require_once CRP_PLUGIN_DIR . 'includes/i10n.php';
require_once CRP_PLUGIN_DIR . 'includes/class-crp-query.php';
require_once CRP_PLUGIN_DIR . 'includes/main-query.php';
require_once CRP_PLUGIN_DIR . 'includes/output-generator.php';
require_once CRP_PLUGIN_DIR . 'includes/media.php';
require_once CRP_PLUGIN_DIR . 'includes/tools.php';
require_once CRP_PLUGIN_DIR . 'includes/header.php';
require_once CRP_PLUGIN_DIR . 'includes/content.php';
require_once CRP_PLUGIN_DIR . 'includes/modules/manual-posts.php';
require_once CRP_PLUGIN_DIR . 'includes/modules/cache.php';
require_once CRP_PLUGIN_DIR . 'includes/modules/shortcode.php';
require_once CRP_PLUGIN_DIR . 'includes/modules/taxonomies.php';
require_once CRP_PLUGIN_DIR . 'includes/modules/exclusions.php';
require_once CRP_PLUGIN_DIR . 'includes/modules/class-crp-rest-api.php';
require_once CRP_PLUGIN_DIR . 'includes/modules/class-crp-widget.php';
require_once CRP_PLUGIN_DIR . 'includes/blocks/register-blocks.php';


/*
 *----------------------------------------------------------------------------
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------
 */

if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {

	require_once CRP_PLUGIN_DIR . 'includes/admin/admin.php';
	require_once CRP_PLUGIN_DIR . 'includes/admin/settings-page.php';
	require_once CRP_PLUGIN_DIR . 'includes/admin/save-settings.php';
	require_once CRP_PLUGIN_DIR . 'includes/admin/help-tab.php';
	require_once CRP_PLUGIN_DIR . 'includes/admin/modules/tools.php';
	require_once CRP_PLUGIN_DIR . 'includes/admin/modules/loader.php';
	require_once CRP_PLUGIN_DIR . 'includes/admin/modules/metabox.php';
} // End if.


/*
 *----------------------------------------------------------------------------
 * Deprecated functions
 *----------------------------------------------------------------------------
 */

require_once CRP_PLUGIN_DIR . 'includes/deprecated.php';


/**
 * Global variable holding the current settings for Contextual Related Posts
 *
 * @since 1.8.10
 *
 * @var array
 */
global $crp_settings;
$crp_settings = crp_get_settings();


/**
 * Get Settings.
 *
 * Retrieves all plugin settings
 *
 * @since  2.6.0
 * @return array Contextual Related Posts settings
 */
function crp_get_settings() {

	$settings = get_option( 'crp_settings' );

	/**
	 * Settings array
	 *
	 * Retrieves all plugin settings
	 *
	 * @since 2.0.0
	 * @param array $settings Settings array
	 */
	return apply_filters( 'crp_get_settings', $settings );
}

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
 * @copyright 2009-2024 Ajay D'Souza
 *
 * @wordpress-plugin
 * Plugin Name: Contextual Related Posts
 * Plugin URI:  https://webberzone.com/plugins/contextual-related-posts/
 * Description: Display related posts on your website or in your feed. Increase reader retention and reduce bounce rates
 * Version:     3.5.1
 * Author:      WebberZone
 * Author URI:  https://webberzone.com
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: contextual-related-posts
 * Domain Path: /languages
 * GitHub Plugin URI: https://github.com/WebberZone/contextual-related-posts/
 */

namespace WebberZone\Contextual_Related_Posts;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Holds the version of Contextual Related Posts.
 *
 * @since 2.9.3
 */
if ( ! defined( 'CRP_VERSION' ) ) {
	define( 'CRP_VERSION', '3.5.0' );
}


/**
 * Holds the filesystem directory path (with trailing slash) for Contextual Related Posts.
 *
 * @since 2.3.0
 */
if ( ! defined( 'CRP_PLUGIN_FILE' ) ) {
	define( 'CRP_PLUGIN_FILE', __FILE__ );
}


/**
 * Holds the filesystem directory path (with trailing slash) for Contextual Related Posts.
 *
 * @since 2.3.0
 */
if ( ! defined( 'CRP_PLUGIN_DIR' ) ) {
	define( 'CRP_PLUGIN_DIR', plugin_dir_path( CRP_PLUGIN_FILE ) );
}

/**
 * Holds the filesystem directory path (with trailing slash) for Contextual Related Posts.
 *
 * @since 2.3.0
 */
if ( ! defined( 'CRP_PLUGIN_URL' ) ) {
	define( 'CRP_PLUGIN_URL', plugin_dir_url( CRP_PLUGIN_FILE ) );
}

/**
 * Maximum words to match in the content.
 *
 * @since 2.3.0
 */
if ( ! defined( 'CRP_MAX_WORDS' ) ) {
	define( 'CRP_MAX_WORDS', 100 );
}

/**
 * CRP Cache expiration time.
 *
 * @since 3.0.0
 */
if ( ! defined( 'CRP_CACHE_TIME' ) ) {
	define( 'CRP_CACHE_TIME', MONTH_IN_SECONDS );
}

/**
 * CRP Database version.
 *
 * @since 3.5.0
 */
if ( ! defined( 'CRP_DB_VERSION' ) ) {
	define( 'CRP_DB_VERSION', '1.0' );
}

// Load Freemius.
require_once CRP_PLUGIN_DIR . 'includes/load-freemius.php';

// Load the autoloader.
require_once CRP_PLUGIN_DIR . 'includes/autoloader.php';

if ( ! function_exists( __NAMESPACE__ . '\load' ) ) {
	/**
	 * The main function responsible for returning the one true WebberZone Snippetz instance to functions everywhere.
	 *
	 * @since 3.5.0
	 */
	function load() {
		\WebberZone\Contextual_Related_Posts\Main::get_instance();
	}
	add_action( 'plugins_loaded', __NAMESPACE__ . '\load' );
}

/*
 *----------------------------------------------------------------------------
 * Include files
 *----------------------------------------------------------------------------
 */
require_once CRP_PLUGIN_DIR . 'includes/options-api.php';
require_once CRP_PLUGIN_DIR . 'includes/class-crp-query.php';
require_once CRP_PLUGIN_DIR . 'includes/functions.php';

// Register deactivation hook.
register_deactivation_hook( __FILE__, __NAMESPACE__ . '\Admin\Activator::deactivation_hook' );

/**
 * Global variable holding the current settings for Contextual Related Posts
 *
 * @since 1.8.10
 *
 * @var array
 */
global $crp_settings;
$crp_settings = crp_get_settings();

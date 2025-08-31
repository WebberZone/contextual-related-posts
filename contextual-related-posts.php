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
 * @copyright 2009-2025 Ajay D'Souza
 *
 * @wordpress-plugin
 * Plugin Name: Contextual Related Posts
 * Plugin URI:  https://webberzone.com/plugins/contextual-related-posts/
 * Description: Display related posts on your website or in your feed. Increase reader retention and reduce bounce rates.
 * Version:     4.1.0
 * Author:      WebberZone
 * Author URI:  https://webberzone.com
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: contextual-related-posts
 * Domain Path: /languages
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
if ( ! defined( 'WZ_CRP_VERSION' ) ) {
	define( 'WZ_CRP_VERSION', '4.1.0' );
}


/**
 * Holds the filesystem directory path (with trailing slash) for Contextual Related Posts.
 *
 * @since 2.3.0
 */
if ( ! defined( 'WZ_CRP_PLUGIN_FILE' ) ) {
	define( 'WZ_CRP_PLUGIN_FILE', __FILE__ );
}


/**
 * Holds the filesystem directory path (with trailing slash) for Contextual Related Posts.
 *
 * @since 2.3.0
 */
if ( ! defined( 'WZ_CRP_PLUGIN_DIR' ) ) {
	define( 'WZ_CRP_PLUGIN_DIR', plugin_dir_path( WZ_CRP_PLUGIN_FILE ) );
}

/**
 * Holds the filesystem directory path (with trailing slash) for Contextual Related Posts.
 *
 * @since 2.3.0
 */
if ( ! defined( 'WZ_CRP_PLUGIN_URL' ) ) {
	define( 'WZ_CRP_PLUGIN_URL', plugin_dir_url( WZ_CRP_PLUGIN_FILE ) );
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
if ( ! defined( 'WZ_CRP_DB_VERSION' ) ) {
	define( 'WZ_CRP_DB_VERSION', '1.0' );
}

if ( ! function_exists( __NAMESPACE__ . '\crp_deactivate_other_instances' ) ) {
	/**
	 * Deactivate other instances of CRP when this plugin is activated.
	 *
	 * @param string $plugin The plugin being activated.
	 * @param bool   $network_wide Whether the plugin is being activated network-wide.
	 */
	function crp_deactivate_other_instances( $plugin, $network_wide = false ) {
		$free_plugin = 'contextual-related-posts/contextual-related-posts.php';
		$pro_plugin  = 'contextual-related-posts-pro/contextual-related-posts.php';

		// Only proceed if one of our plugins is being activated.
		if ( ! in_array( $plugin, array( $free_plugin, $pro_plugin ), true ) ) {
			return;
		}

		$plugins_to_deactivate = array();
		$deactivated_plugin    = '';

		// If pro is being activated, deactivate free.
		if ( $pro_plugin === $plugin ) {
			if ( is_plugin_active( $free_plugin ) || ( $network_wide && is_plugin_active_for_network( $free_plugin ) ) ) {
				$plugins_to_deactivate[] = $free_plugin;
				$deactivated_plugin      = 'Contextual Related Posts';
			}
		}

		// If free is being activated, deactivate pro.
		if ( $free_plugin === $plugin ) {
			if ( is_plugin_active( $pro_plugin ) || ( $network_wide && is_plugin_active_for_network( $pro_plugin ) ) ) {
				$plugins_to_deactivate[] = $pro_plugin;
				$deactivated_plugin      = 'Contextual Related Posts Pro';
			}
		}

		if ( ! empty( $plugins_to_deactivate ) ) {
			deactivate_plugins( $plugins_to_deactivate, false, $network_wide );
			set_transient( 'crp_deactivated_notice', $deactivated_plugin, 1 * HOUR_IN_SECONDS );
		}
	}
	add_action( 'activated_plugin', __NAMESPACE__ . '\crp_deactivate_other_instances', 10, 2 );
}

// Show admin notice about automatic deactivation.
if ( ! has_action( 'admin_notices', __NAMESPACE__ . '\crp_show_deactivation_notice' ) ) {
	add_action(
		'admin_notices',
		function () {
			$deactivated_plugin = get_transient( 'crp_deactivated_notice' );
			if ( $deactivated_plugin ) {
				/* translators: %s: Name of the deactivated plugin */
				$message = sprintf( __( "Contextual Related Posts and Contextual Related Posts PRO should not be active at the same time. We've automatically deactivated %s.", 'contextual-related-posts' ), $deactivated_plugin );
				?>
			<div class="updated" style="border-left: 4px solid #ffba00;">
				<p><?php echo esc_html( $message ); ?></p>
			</div>
				<?php
				delete_transient( 'crp_deactivated_notice' );
			}
		}
	);
}

if ( ! function_exists( __NAMESPACE__ . '\crp_freemius' ) ) {
	// Finally load Freemius integration.
	require_once plugin_dir_path( __FILE__ ) . 'load-freemius.php';
}

// Load custom autoloader.
if ( ! function_exists( __NAMESPACE__ . '\autoload' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'includes/autoloader.php';
}

if ( ! function_exists( __NAMESPACE__ . '\wz_crp' ) ) {
	/**
	 * Returns the instance of the Contextual Related Posts main class.
	 *
	 * @since 4.0.0
	 *
	 * @return \WebberZone\Contextual_Related_Posts\Main The Contextual Related Posts Main instance.
	 */
	function wz_crp() {
		return \WebberZone\Contextual_Related_Posts\Main::get_instance();
	}
}

if ( ! function_exists( __NAMESPACE__ . '\load' ) ) {
	/**
	 * The main function responsible for returning the one true WebberZone Contextual Related Posts instance to functions everywhere.
	 *
	 * @since 3.5.0
	 */
	function load(): void {
		wz_crp();
	}
	add_action( 'plugins_loaded', __NAMESPACE__ . '\load' );
}

/*
 *----------------------------------------------------------------------------
 * Include files
 *----------------------------------------------------------------------------
 */
if ( ! function_exists( 'crp_get_settings' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'includes/options-api.php';
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-crp-query.php';
	require_once plugin_dir_path( __FILE__ ) . 'includes/functions.php';
}

// Register activation hook.
register_activation_hook( __FILE__, __NAMESPACE__ . '\Admin\Activator::activation_hook' );

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
$crp_settings = \crp_get_settings();

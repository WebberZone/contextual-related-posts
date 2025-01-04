<?php
/**
 * Autoloads classes from the WebberZone\Snippetz namespace.
 *
 * @package WebberZone\Contextual_Related_Posts
 */

namespace WebberZone\Contextual_Related_Posts;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Initialize Freemius SDK.
 */
function crp_freemius() {
	global $crp_freemius;
	if ( ! isset( $crp_freemius ) ) {
		// Activate multisite network integration.
		if ( ! defined( 'WP_FS__PRODUCT_15040_MULTISITE' ) ) {
			define( 'WP_FS__PRODUCT_15040_MULTISITE', true );
		}

		// Include Freemius SDK.
		require_once CRP_PLUGIN_DIR . 'freemius/start.php';
		$crp_freemius = \fs_dynamic_init(
			array(
				'id'             => '15040',
				'slug'           => 'contextual-related-posts',
				'premium_slug'   => 'contextual-related-posts-pro',
				'type'           => 'plugin',
				'public_key'     => 'pk_4aec305b9c97637276da2e55b723f',
				'is_premium'     => false,
				'premium_suffix' => 'Pro',
				'has_addons'     => false,
				'has_paid_plans' => true,
				'menu'           => array(
					'slug'    => 'crp_options_page',
					'contact' => false,
					'support' => false,
					'parent'  => array(
						'slug' => is_multisite() ? 'admin.php' : 'options-general.php',
					),
				),
				'is_live'        => true,
			)
		);
	}
	$crp_freemius->add_filter( 'plugin_icon', __NAMESPACE__ . '\\crp_freemius_get_plugin_icon' );
	$crp_freemius->add_filter( 'after_uninstall', __NAMESPACE__ . '\\crp_freemius_uninstall' );
	return $crp_freemius;
}

/**
 * Get the plugin icon.
 *
 * @return string
 */
function crp_freemius_get_plugin_icon() {
	return __DIR__ . '/admin/images/crp-icon.png';
}

/**
 * Uninstall the plugin.
 */
function crp_freemius_uninstall() {
	require_once dirname( __DIR__ ) . '/uninstaller.php';
}

// Init Freemius.
crp_freemius();
// Signal that SDK was initiated.
do_action( 'crp_freemius_loaded' );

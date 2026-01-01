<?php
/**
 * PHPStan bootstrap file for Contextual Related Posts Pro.
 *
 * @package WebberZone\Contextual_Related_Posts
 */

// phpcs:ignoreFile

if ( ! defined( 'WZ_CRP_VERSION' ) ) {
	define( 'WZ_CRP_VERSION', '0.0.0' );
}

if ( ! defined( 'WZ_CRP_PLUGIN_FILE' ) ) {
	define( 'WZ_CRP_PLUGIN_FILE', '' );
}

if ( ! defined( 'WZ_CRP_PLUGIN_DIR' ) ) {
	define( 'WZ_CRP_PLUGIN_DIR', '' );
}

if ( ! defined( 'WZ_CRP_PLUGIN_URL' ) ) {
	define( 'WZ_CRP_PLUGIN_URL', '' );
}

if ( ! defined( 'WZ_CRP_DEFAULT_THUMBNAIL_URL' ) ) {
	define( 'WZ_CRP_DEFAULT_THUMBNAIL_URL', '' );
}

if ( ! defined( 'CRP_MAX_WORDS' ) ) {
	define( 'CRP_MAX_WORDS', 100 );
}

if ( ! defined( 'CRP_CACHE_TIME' ) ) {
	define( 'CRP_CACHE_TIME', 0 );
}

if ( ! defined( 'WZ_CRP_DB_VERSION' ) ) {
	define( 'WZ_CRP_DB_VERSION', '0.0.0' );
}

if ( ! defined( 'CRP_CACHE_TIME' ) ) {
	define( 'CRP_CACHE_TIME', 0 );
}

if ( ! defined( 'WZ_CRP_DB_VERSION' ) ) {
	define( 'WZ_CRP_DB_VERSION', '0.0.0' );
}

if ( ! function_exists( 'fs_dynamic_init' ) ) {
	/**
	 * Freemius bootstrap stub for static analysis.
	 *
	 * @param array<string, mixed> $args Freemius init args.
	 * @return object
	 */
	function fs_dynamic_init( array $args = array() ) {
		unset( $args );
		return new class() {
			/**
			 * Stub method used by the plugin.
			 *
			 * @param string $tag Hook name.
			 * @param mixed  $callback Callback.
			 * @return mixed
			 */
			public function add_filter( $tag, $callback ) {
				unset( $tag, $callback );
				return null;
			}

			/**
			 * Stub method used by the plugin.
			 *
			 * @return bool
			 */
			public function can_use_premium_code__premium_only() {
				return false;
			}
		};
	}
}

$crp_freemius = \fs_dynamic_init(
	array()
);

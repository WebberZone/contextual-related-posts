<?php
/**
 * PHPStan bootstrap file for Contextual Related Posts.
 *
 * @package WebberZone\Contextual_Related_Posts
 */

// phpcs:ignoreFile

namespace {
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

	$crp_freemius = \fs_dynamic_init( array() );
}

// When running on the free plugin (includes/pro/ removed by sync), define Pro class stubs
// so PHPStan can resolve the ?Pro\Pro $pro property and any shared code that accesses
// pro properties (e.g. ->pro->custom_tables).
namespace WebberZone\Contextual_Related_Posts\Pro\Custom_Tables {
	if ( ! is_dir( dirname( __FILE__ ) . '/includes/pro' ) ) {
		class Table_Manager {
			public static string $db_version_option = '';
			public static string $db_version = '';
			public string $content_table = '';
			/** @return int|float */
			public function get_indexing_percentage( int $blog_id = 0 ) { return 0; }
			public function get_content_count( int $blog_id = 0 ): int { return 0; }
			public function get_post_count( int $blog_id = 0 ): int { return 0; }
			public function drop_tables(): void {}
			/** @return string */
			public function create_content_table_sql() { return ''; }
			public function maybe_create_table( string $table_name, string $sql ): void {}
			public function is_table_installed( string $table_name ): bool { return false; }
		}
		class Custom_Tables_Admin {
			public \WebberZone\Contextual_Related_Posts\Pro\Custom_Tables\Table_Manager $table_manager;
			/** @return array<mixed>|false */
			public function get_reindex_state() { return false; }
		}
		class Custom_Tables {
			public \WebberZone\Contextual_Related_Posts\Pro\Custom_Tables\Custom_Tables_Admin $admin;
		}
	}
}

namespace WebberZone\Contextual_Related_Posts\Pro {
	if ( ! is_dir( dirname( __FILE__ ) . '/includes/pro' ) ) {
		class Pro {
			public ?\WebberZone\Contextual_Related_Posts\Pro\Custom_Tables\Custom_Tables $custom_tables = null;
		}
	}
}

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

	if ( ! defined( 'DB_NAME' ) ) {
		define( 'DB_NAME', '' );
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

	if ( ! function_exists( 'vc_map' ) ) {
		/**
		 * WPBakery Page Builder element-registration stub for static analysis.
		 *
		 * @param array<string, mixed> $args Element definition.
		 * @return void
		 */
		function vc_map( array $args ) {
			do_action( 'crp_wpbakery_vc_map_stub', $args );
		}
	}
}

// Elementor has no official PHPStan stub package, so declare the minimal surface CRP's
// Elementor builder module touches.
namespace Elementor {
	if ( ! class_exists( __NAMESPACE__ . '\Controls_Manager' ) ) {
		class Controls_Manager {
			const TEXT     = 'text';
			const NUMBER   = 'number';
			const SELECT   = 'select';
			const SWITCHER = 'switcher';
			const TEXTAREA = 'textarea';

			const TAB_CONTENT = 'content';
		}
	}

	if ( ! class_exists( __NAMESPACE__ . '\Widget_Base' ) ) {
		abstract class Widget_Base {
			/** @return string */
			abstract public function get_name();

			/** @return string */
			abstract public function get_title();

			/** @return string */
			abstract public function get_icon();

			/** @return string[] */
			abstract public function get_categories();

			/** @param array<string, mixed> $args */
			protected function start_controls_section( string $section_id, array $args = array() ): void {}

			protected function end_controls_section(): void {}

			/** @param array<string, mixed> $args */
			protected function add_control( string $control_id, array $args = array() ): void {}

			abstract protected function register_controls(): void;

			abstract protected function render(): void;

			/** @return array<string, mixed> */
			protected function get_settings_for_display( ?string $setting_key = null ) {
				return array();
			}
		}
	}

	if ( ! class_exists( __NAMESPACE__ . '\Elements_Manager' ) ) {
		class Elements_Manager {
			/** @param array<string, mixed> $args */
			public function add_category( string $category_id, array $args = array() ): void {}
		}
	}

	if ( ! class_exists( __NAMESPACE__ . '\Widgets_Manager' ) ) {
		class Widgets_Manager {
			public function register( Widget_Base $widget ): bool {
				return true;
			}
		}
	}

	if ( ! class_exists( __NAMESPACE__ . '\Plugin' ) ) {
		class Plugin {}
	}
}

// Bricks Builder has no official PHPStan stub package, so declare the minimal surface CRP's
// Bricks builder module, element and style handler touch.
namespace {
	if ( ! defined( 'BRICKS_DB_PAGE_CONTENT' ) ) {
		define( 'BRICKS_DB_PAGE_CONTENT', '_bricks_page_content_2' );
	}

	if ( ! defined( 'BRICKS_DB_PAGE_HEADER' ) ) {
		define( 'BRICKS_DB_PAGE_HEADER', '_bricks_page_header_2' );
	}

	if ( ! defined( 'BRICKS_DB_PAGE_FOOTER' ) ) {
		define( 'BRICKS_DB_PAGE_FOOTER', '_bricks_page_footer_2' );
	}

	if ( ! function_exists( 'bricks_is_builder' ) ) {
		/**
		 * Bricks builder-context stub for static analysis.
		 *
		 * @return bool
		 */
		function bricks_is_builder() {
			return false;
		}
	}
}

namespace Bricks {
	if ( ! class_exists( __NAMESPACE__ . '\Element' ) ) {
		abstract class Element {
			/** @var string */
			public $category = '';

			/** @var string */
			public $name = '';

			/** @var string */
			public $icon = '';

			/** @var array<string, mixed> */
			public $controls = array();

			/** @var array<string, mixed> */
			public $control_groups = array();

			/** @var array<string, mixed> */
			public $control_options = array();

			/** @var mixed Raw element settings; Bricks does not guarantee an array. */
			public $settings = array();

			/** @return string */
			public function get_label() {
				return '';
			}

			/** @return string */
			public function get_description() {
				return '';
			}

			/** @return string[] */
			public function get_keywords() {
				return array();
			}

			/** @return void */
			public function set_control_groups() {}

			/** @return void */
			public function set_controls() {}

			/** @return void */
			public function render() {}

			/**
			 * @param string $key Attribute group key.
			 * @return string
			 */
			public function render_attributes( $key = '_root' ) {
				unset( $key );
				return '';
			}

			/**
			 * @param array<string, mixed> $args Placeholder arguments.
			 * @return void
			 */
			public function render_element_placeholder( array $args = array() ) {
				unset( $args );
			}

			/**
			 * @param string $content Content containing dynamic data tags.
			 * @return string
			 */
			public function render_dynamic_data( $content = '' ) {
				return (string) $content;
			}
		}
	}

	if ( ! class_exists( __NAMESPACE__ . '\Elements' ) ) {
		class Elements {
			/**
			 * @param string $file       Path to the element class file.
			 * @param string $name       Element name.
			 * @param string $class_name Element class name.
			 * @return bool
			 */
			public static function register_element( $file, $name = '', $class_name = '' ) {
				unset( $file, $name, $class_name );
				return true;
			}
		}
	}

	if ( ! class_exists( __NAMESPACE__ . '\Helpers' ) ) {
		class Helpers {
			/** @return array<string, string> */
			public static function get_registered_post_types() {
				return array();
			}
		}
	}
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

<?php
/**
 * Admin Banner helper.
 *
 * @package WebberZone\CRP
 */

namespace WebberZone\Contextual_Related_Posts\Admin;

use WebberZone\Contextual_Related_Posts\Util\Hook_Registry;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Reusable admin banner helper that is configured via constructor values.
 *
 * Copy-paste friendly: adjust namespaces and configuration per plugin.
 *
 * @since 4.2.2
 */
class Admin_Banner {

	private const DEFAULT_STYLE_VERSION = '1.0.0';

	/**
	 * Configuration array for the banner output.
	 *
	 * @var array<string, mixed>
	 */
	private array $config = array();

	/**
	 * Derived class names keyed by component.
	 *
	 * @var array<string, array<int, string>>
	 */
	private array $class_names = array();

	/**
	 * Localized strings.
	 *
	 * @var array<string, string>
	 */
	private array $strings = array();

	/**
	 * Style configuration.
	 *
	 * @var array<string, mixed>
	 */
	private array $style = array();

	/**
	 * Base class prefix shared by all banners.
	 *
	 * @var string
	 */
	private string $base_prefix = 'wz-admin-banner';

	/**
	 * Unique class prefix derived from the provided prefix.
	 *
	 * @var string
	 */
	private string $unique_prefix = 'admin-banner';

	/**
	 * Constructor.
	 *
	 * @param array $config Configuration arguments for the banner.
	 */
	public function __construct( array $config ) {
		$defaults = array(
			'capability'           => 'manage_options',
			'allow_network'        => false,
			'prefix'               => '',
			'screen_ids'           => array(),
			'page_slugs'           => array(),
			'sections'             => array(),
			'exclude_screen_bases' => array( 'post', 'post-new' ),
			'strings'              => array(),
			'link_target'          => '_self',
			'style'                => array(),
		);

		$this->config  = wp_parse_args( $config, $defaults );
		$this->strings = $this->prepare_strings( $this->config['strings'] ?? array() );

		$this->config['sections'] = $this->sanitize_sections( $this->config['sections'] );

		$this->unique_prefix = $this->resolve_wrapper_prefix( (string) $this->config['prefix'] );
		$this->class_names   = $this->derive_class_names();
		$this->style         = $this->prepare_style_config( $this->config['style'] ?? array() );

		if ( empty( $this->config['screen_ids'] ) ) {
			$this->config['screen_ids'] = $this->collect_targets_from_sections( 'screen_ids' );
		}

		if ( empty( $this->config['page_slugs'] ) ) {
			$this->config['page_slugs'] = $this->collect_targets_from_sections( 'page_slugs' );
		}

		$this->hooks();
	}

	/**
	 * Register hooks.
	 */
	private function hooks(): void {
		Hook_Registry::add_action( 'admin_enqueue_scripts', array( $this, 'maybe_enqueue_styles' ) );
		Hook_Registry::add_action( 'in_admin_header', array( $this, 'render' ) );
	}

	/**
	 * Enqueue banner styles if required on the current screen or page slug.
	 */
	public function maybe_enqueue_styles(): void {
		if ( empty( $this->style['url'] ) ) {
			return;
		}

		$screen    = ! is_network_admin() ? get_current_screen() : null;
		$page_slug = $this->get_request_page_slug();

		if ( $screen instanceof \WP_Screen && $this->should_render_on_screen( $screen, $page_slug ) ) {
			$this->enqueue_style();
			return;
		}

		if ( '' !== $page_slug && in_array( $page_slug, $this->config['page_slugs'], true ) ) {
			$this->enqueue_style();
		}
	}

	/**
	 * Render the admin banner markup when conditions are met.
	 */
	public function render(): void {
		if ( is_network_admin() && ! $this->config['allow_network'] ) {
			return;
		}

		$screen = get_current_screen();
		if ( ! ( $screen instanceof \WP_Screen ) || ! current_user_can( $this->config['capability'] ) ) {
			return;
		}

		$page_slug = $this->get_request_page_slug();

		if ( ! $this->should_render_on_screen( $screen, $page_slug ) ) {
			return;
		}

		$current_section = $this->resolve_current_section( $screen, $page_slug );

		?>
		<div class="<?php echo esc_attr( $this->class_attr( 'wrapper' ) ); ?>" role="region" aria-label="<?php echo esc_attr( $this->strings['region_label'] ); ?>">
			<div class="<?php echo esc_attr( $this->class_attr( 'intro' ) ); ?>">
			<?php if ( ! empty( $this->strings['eyebrow'] ) ) : ?>
					<span class="<?php echo esc_attr( $this->class_attr( 'eyebrow' ) ); ?>"><?php echo esc_html( $this->strings['eyebrow'] ); ?></span>
				<?php endif; ?>
			<?php if ( ! empty( $this->strings['title'] ) ) : ?>
					<p class="<?php echo esc_attr( $this->class_attr( 'title' ) ); ?>"><?php echo esc_html( $this->strings['title'] ); ?></p>
				<?php endif; ?>
			<?php if ( ! empty( $this->strings['text'] ) ) : ?>
					<p class="<?php echo esc_attr( $this->class_attr( 'text' ) ); ?>"><?php echo esc_html( $this->strings['text'] ); ?></p>
				<?php endif; ?>
			</div>
			<nav class="<?php echo esc_attr( $this->class_attr( 'links_wrapper' ) ); ?>" aria-label="<?php echo esc_attr( $this->strings['nav_label'] ); ?>">
			<?php foreach ( $this->config['sections'] as $section_key => $section ) : ?>
					<?php
					$link_text   = $section['label'] ?? '';
					$link_url    = $section['url'] ?? '';
					$link_target = $section['target'] ?? $this->config['link_target'];
					$link_rel    = $section['rel'] ?? '';

					if ( empty( $link_text ) || empty( $link_url ) ) {
						continue;
					}

					$link_classes = $this->get_section_link_classes( $section );
					if ( $section_key === $current_section ) {
						$link_classes = array_merge( $link_classes, $this->class_names['link_current'] ?? array() );
					}
					?>
					<a class="<?php echo esc_attr( $this->implode_classes( $link_classes ) ); ?>" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"<?php echo empty( $link_rel ) ? '' : ' rel="' . esc_attr( $link_rel ) . '"'; ?>>
						<?php echo esc_html( $link_text ); ?>
					</a>
				<?php endforeach; ?>
			</nav>
		</div>
			<?php
	}

	/**
	 * Enqueue the banner stylesheet.
	 */
	private function enqueue_style(): void {
		wp_register_style(
			$this->style['handle'],
			$this->style['url'],
			(array) $this->style['deps'],
			$this->style['version']
		);
		wp_enqueue_style( $this->style['handle'] );
	}

	/**
	 * Determine whether the banner should display on the current screen.
	 *
	 * @param \WP_Screen $screen    Current admin screen.
	 * @param string     $page_slug Current request page slug.
	 */
	private function should_render_on_screen( \WP_Screen $screen, string $page_slug ): bool {
		$screen_base = (string) $screen->base;
		if ( '' !== $screen_base && in_array( $screen_base, (array) $this->config['exclude_screen_bases'], true ) ) {
			return false;
		}

		$screen_id = (string) $screen->id;
		if ( '' !== $screen_id && in_array( $screen_id, (array) $this->config['screen_ids'], true ) ) {
			return true;
		}

		if ( '' !== $page_slug && in_array( $page_slug, (array) $this->config['page_slugs'], true ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Resolve the banner section to highlight based on current screen or page slug.
	 *
	 * @param \WP_Screen $screen    Current admin screen.
	 * @param string     $page_slug Current request page slug.
	 */
	private function resolve_current_section( \WP_Screen $screen, string $page_slug ): string {
		$screen_id = (string) $screen->id;

		foreach ( $this->config['sections'] as $section_key => $section ) {
			if ( ! empty( $section['screen_ids'] ) && in_array( $screen_id, (array) $section['screen_ids'], true ) ) {
				return $section_key;
			}
		}

		foreach ( $this->config['sections'] as $section_key => $section ) {
			if ( ! empty( $section['page_slugs'] ) && in_array( $page_slug, (array) $section['page_slugs'], true ) ) {
				return $section_key;
			}
		}

		return '';
	}

	/**
	 * Prepare localized strings.
	 *
	 * @param array $strings Raw strings array.
	 */
	private function prepare_strings( array $strings ): array {
		$defaults = array(
			'region_label' => '',
			'nav_label'    => '',
			'eyebrow'      => '',
			'title'        => '',
			'text'         => '',
		);

		return wp_parse_args( $strings, $defaults );
	}

	/**
	 * Resolve the wrapper prefix based on base prefix provided.
	 *
	 * @param string $prefix Base prefix.
	 */
	private function resolve_wrapper_prefix( string $prefix ): string {
		$prefix = sanitize_key( $prefix );

		if ( '' === $prefix ) {
			return $this->base_prefix;
		}

		return false === strpos( $prefix, $this->base_prefix ) ? "{$prefix}-admin-banner" : $prefix;
	}

	/**
	 * Prepare style configuration.
	 *
	 * @param array $style Style configuration.
	 */
	private function prepare_style_config( array $style ): array {
		$defaults = array(
			'handle'   => $this->sanitize_handle( "{$this->unique_prefix}-styles" ),
			'deps'     => array(),
			'version'  => self::DEFAULT_STYLE_VERSION,
			'filename' => 'admin-banner',
			'url'      => '',
		);

		$style_config = wp_parse_args( $style, $defaults );

		if ( empty( $style_config['url'] ) ) {
			$assets_base         = trailingslashit( plugin_dir_url( __FILE__ ) ) . 'css/';
			$min_suffix          = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
			$style_config['url'] = $assets_base . $style_config['filename'] . $min_suffix . '.css';
		}

		return $style_config;
	}

	/**
	 * Sanitize the sections configuration.
	 *
	 * @param array $sections Sections configuration.
	 *
	 * @return array
	 */
	private function sanitize_sections( array $sections ): array {
		$sanitized = array();

		foreach ( $sections as $key => $section ) {
			if ( empty( $section['label'] ) || empty( $section['url'] ) ) {
				continue;
			}

			$section_key = sanitize_key( $key );

			$sanitized[ $section_key ] = array(
				'label'      => $section['label'],
				'url'        => $section['url'],
				'type'       => isset( $section['type'] ) ? sanitize_key( $section['type'] ) : 'secondary',
				'target'     => isset( $section['target'] ) ? $section['target'] : '_self',
				'rel'        => isset( $section['rel'] ) ? $section['rel'] : '',
				'screen_ids' => isset( $section['screen_ids'] ) ? (array) $section['screen_ids'] : array(),
				'page_slugs' => isset( $section['page_slugs'] ) ? array_map( 'sanitize_key', (array) $section['page_slugs'] ) : array(),
			);
		}

		return $sanitized;
	}

	/**
	 * Derive class names following the provided prefix alongside the base prefix.
	 *
	 * @return array<string, array<int, string>>
	 */
	private function derive_class_names(): array {
		$build = function ( string $suffix = '' ): array {
			$classes = array( $this->base_prefix . $suffix );

			if ( $this->unique_prefix !== $this->base_prefix ) {
				$classes[] = $this->unique_prefix . $suffix;
			}

			return $classes;
		};

		return array(
			'wrapper'        => $build(),
			'intro'          => $build( '__intro' ),
			'eyebrow'        => $build( '__eyebrow' ),
			'title'          => $build( '__title' ),
			'text'           => $build( '__text' ),
			'links_wrapper'  => $build( '__links' ),
			'link'           => $build( '__link' ),
			'link_primary'   => $build( '__link--primary' ),
			'link_secondary' => $build( '__link--secondary' ),
			'link_current'   => $build( '__link--current' ),
			'link_new'       => $build( '__link--new' ),
		);
	}

	/**
	 * Collect screen IDs or page slugs from the sections configuration.
	 *
	 * @param string $target_key screen_ids|page_slugs key.
	 *
	 * @return array
	 */
	private function collect_targets_from_sections( string $target_key ): array {
		$values = array();

		foreach ( $this->config['sections'] as $section ) {
			if ( empty( $section[ $target_key ] ) ) {
				continue;
			}
			foreach ( (array) $section[ $target_key ] as $value ) {
				$values[] = (string) $value;
			}
		}

		return array_values( array_unique( array_filter( $values ) ) );
	}

		/**
		 * Retrieve the CSS classes for a section link.
		 *
		 * @param array $section Section configuration.
		 */
	private function get_section_link_classes( array $section ): array {
		$classes  = $this->class_names['link'] ?? array();
		$type     = isset( $section['type'] ) ? sanitize_key( $section['type'] ) : 'secondary';
		$type     = '' !== $type ? $type : 'secondary';
		$type_key = "link_{$type}";

		if ( isset( $this->class_names[ $type_key ] ) ) {
			$classes = array_merge( $classes, (array) $this->class_names[ $type_key ] );
		} elseif ( isset( $this->class_names['link_secondary'] ) ) {
			$classes = array_merge( $classes, (array) $this->class_names['link_secondary'] );
		}

		return array_values( array_unique( array_filter( $classes ) ) );
	}

	/**
	 * Implode a class array into a string.
	 *
	 * @param array $classes Class list.
	 * @return string Class attribute string.
	 */
	private function implode_classes( array $classes ): string {
		return implode( ' ', array_unique( array_filter( $classes ) ) );
	}

	/**
	 * Retrieve a flattened class attribute by key.
	 *
	 * @param string $key Classes array key.
	 * @return string Class attribute string.
	 */
	private function class_attr( string $key ): string {
		return $this->implode_classes( $this->class_names[ $key ] ?? array() );
	}

	/**
	 * Sanitize a style handle.
	 *
	 * @param string $handle Raw handle.
	 */
	private function sanitize_handle( string $handle ): string {
		return sanitize_title_with_dashes( $handle );
	}

	/**
	 * Get the current page slug from the request.
	 */
	private function get_request_page_slug(): string {
		$page_param_raw = filter_input( INPUT_GET, 'page', FILTER_UNSAFE_RAW );

		if ( is_string( $page_param_raw ) && '' !== $page_param_raw ) {
			$page_raw = sanitize_text_field( $page_param_raw );
		} elseif ( isset( $_GET['page'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$page_raw = sanitize_text_field( wp_unslash( $_GET['page'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		} else {
			return '';
		}

		$page_slug = strtolower( (string) strtok( $page_raw, '&' ) );

		return sanitize_key( $page_slug );
	}
}

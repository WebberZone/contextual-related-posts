<?php
/**
 * Functions to register client-side assets (scripts and stylesheets) for the
 * Gutenberg block.
 *
 * @package Contextual_Related_Posts
 */

namespace WebberZone\Contextual_Related_Posts\Frontend\Blocks;

use WebberZone\Contextual_Related_Posts\Frontend\Styles_Handler;
use WebberZone\Contextual_Related_Posts\Admin\Settings;
use WebberZone\Contextual_Related_Posts\Util\Hook_Registry;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Registers the Contextual Related Posts blocks.
 *
 * @since 3.5.0
 */
class Blocks {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		Hook_Registry::add_action( 'init', array( $this, 'register_blocks' ) );
		Hook_Registry::add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );
	}

	/**
	 * Registers the block using the metadata loaded from the `block.json` file.
	 * Behind the scenes, it registers also all assets so they can be enqueued
	 * through the block editor in the corresponding context.
	 *
	 * @since 3.5.0
	 */
	public function register_blocks() {
		// Define an array of blocks with their paths and optional render callbacks.
		$blocks = array(
			'related-posts' => array(
				'path'            => __DIR__ . '/build/related-posts/',
				'render_callback' => array( $this, 'render_block_related_posts' ),
			),
		);

		/**
		 * Filters the blocks registered by the plugin.
		 *
		 * @since 3.6.0
		 *
		 * @param array $blocks Array of blocks registered by the plugin.
		 */
		$blocks = apply_filters( 'crp_register_blocks', $blocks );

		// Loop through each block and register it.
		foreach ( $blocks as $block_name => $block_data ) {
			$args = array();

			// If a render callback is provided, add it to the args.
			if ( isset( $block_data['render_callback'] ) ) {
				$args['render_callback'] = $block_data['render_callback'];
			}

			register_block_type( $block_data['path'], $args );
		}   }


	/**
	 * Keep only the attributes declared by the block type.
	 *
	 * Core passes undeclared attributes through to the render callback unchanged, so
	 * anything not in block.json must be dropped before it reaches the query.
	 *
	 * @since 4.5.4
	 *
	 * @param array  $attributes Block attributes.
	 * @param string $block_name Block name.
	 * @return array Declared attributes only.
	 */
	public static function filter_declared_attributes( array $attributes, string $block_name ): array {
		$block_type = \WP_Block_Type_Registry::get_instance()->get_registered( $block_name );
		$declared   = ( $block_type && is_array( $block_type->attributes ) ) ? $block_type->attributes : array();

		return array_intersect_key( $attributes, $declared );
	}

	/**
	 * Parse the `other_attributes` string against an allow-list of display and tuning keys.
	 *
	 * Mirrors the [crp] shortcode: only plugin settings keys and a few shortcode extras are
	 * accepted. Query visibility keys such as post_status, post_type and perm are never kept.
	 *
	 * @since 4.5.4
	 *
	 * @param string $other_attributes Query-string formatted attributes.
	 * @return array Allowed arguments.
	 */
	public static function parse_other_attributes( string $other_attributes ): array {
		$parsed = wp_parse_args( $other_attributes );

		$allowed = array_merge(
			\crp_settings_defaults(),
			array(
				'heading'           => 1,
				'offset'            => 0,
				'include_cat_ids'   => '',
				'include_post_ids'  => '',
				'related_meta_keys' => '',
				'include_words'     => '',
				'manual_related'    => '',
				'more_link_text'    => '',
				'post_id'           => '',
				'postid'            => '',
			)
		);

		$blocked = array( 'post_status', 'post_type', 'perm', 'is_block', 'is_widget', 'is_shortcode', 'is_manual', 'echo' );

		return array_diff_key( array_intersect_key( $parsed, $allowed ), array_flip( $blocked ) );
	}

	/**
	 * Renders the `contextual-related-posts/related-posts` block on server.
	 *
	 * @since 3.5.0
	 * @param array $attributes The block attributes.
	 *
	 * @return string Returns the post content with popular posts added.
	 */
	public static function render_block_related_posts( $attributes ) {

		$attributes = self::filter_declared_attributes( (array) $attributes, 'contextual-related-posts/related-posts' );

		$attributes['extra_class'] = isset( $attributes['className'] ) ? esc_attr( $attributes['className'] ) : '';

		$defaults = array_merge(
			\crp_settings_defaults(),
			\crp_get_settings(),
			array(
				'is_block' => 1,
			)
		);

		$arguments = wp_parse_args( $attributes, $defaults );

		if ( ! empty( $attributes['other_attributes'] ) && is_string( $attributes['other_attributes'] ) ) {
			$arguments = array_merge( $arguments, self::parse_other_attributes( $attributes['other_attributes'] ) );
		}
		$arguments['is_block'] = 1;

		/**
		 * Filters arguments passed to get_crp for the block.
		 *
		 * @since 2.8.0
		 *
		 * @param array $arguments  CRP block options array.
		 * @param array $attributes Block attributes array.
		 */
		$arguments = apply_filters( 'crp_block_options', $arguments, $attributes );

		// Enqueue the stylesheet for the selected style for this block.
		$style_array = Styles_Handler::get_style( $arguments['crp_styles'] );

		if ( ! empty( $style_array['name'] ) ) {
			$style     = $style_array['name'];
			$extra_css = $style_array['extra_css'];

			wp_register_style(
				"crp-style-{$style}",
				plugins_url( "includes/frontend/css/{$style}.min.css", WZ_CRP_PLUGIN_FILE ),
				array(),
				WZ_CRP_VERSION
			);
			wp_enqueue_style( "crp-style-{$style}" );
			wp_add_inline_style( "crp-style-{$style}", $extra_css );
		}

		$output          = get_crp( $arguments );
		$is_empty_output = empty( wp_strip_all_tags( $output ) );
		$is_backend      = defined( 'REST_REQUEST' ) && true === REST_REQUEST && 'edit' === filter_input( INPUT_GET, 'context', FILTER_UNSAFE_RAW );

		if ( $is_backend && $is_empty_output ) {
			$output  = '<h3>';
			$output .= __( 'Contextual Related Posts Block', 'contextual-related-posts' );
			$output .= '</h3>';
			$output .= '<p>';
			$output .= __( 'No related posts were found or the block rendered empty. Please modify your block settings.', 'contextual-related-posts' );
			$output .= '</p>';
		}

		return $output;
	}

	/**
	 * Enqueue scripts and styles for the block editor.
	 *
	 * @since 3.5.0
	 */
	public static function enqueue_block_editor_assets() {

		$styles      = Settings::get_styles();
		$file_prefix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		foreach ( $styles as $style ) {

			$style_array = Styles_Handler::get_style( $style['id'] );

			if ( ! empty( $style_array['name'] ) ) {
				$style     = $style_array['name'];
				$extra_css = $style_array['extra_css'];

				$base_path = 'includes/frontend/css/';
				if ( false !== strpos( $style, '-pro' ) ) {
					$base_path = 'includes/pro/frontend/css/';
				}

				wp_enqueue_style(
					"crp-block-editor-{$style}",
					plugins_url( "{$base_path}{$style}{$file_prefix}.css", WZ_CRP_PLUGIN_FILE ),
					array( 'wp-edit-blocks' ),
					WZ_CRP_VERSION
				);
				wp_add_inline_style( "crp-block-editor-{$style}", $extra_css );
			}
		}
	}
}

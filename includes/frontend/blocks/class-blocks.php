<?php
/**
 * Functions to register client-side assets (scripts and stylesheets) for the
 * Gutenberg block.
 *
 * @package Contextual_Related_Posts
 */

namespace WebberZone\Contextual_Related_Posts\Frontend\Blocks;

use WebberZone\Contextual_Related_Posts\Frontend\Styles_Handler;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Widget to display the overall count.
 *
 * @since 3.5.0
 */
class Blocks {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_blocks' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );
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
	 * Renders the `contextual-related-posts/related-posts` block on server.
	 *
	 * @since 3.5.0
	 * @param array $attributes The block attributes.
	 *
	 * @return string Returns the post content with popular posts added.
	 */
	public static function render_block_related_posts( $attributes ) {

		$attributes['extra_class'] = esc_attr( $attributes['className'] );

		$arguments = array_merge(
			$attributes,
			array(
				'is_block' => 1,
			)
		);

		$arguments = wp_parse_args( $attributes['other_attributes'], $arguments );

		/**
		 * Filters arguments passed to get_crp for the block.
		 *
		 * @since 2.8.0
		 *
		 * @param array $arguments  CRP block options array.
		 * @param array $attributes Block attributes array.
		 */
		$arguments = apply_filters( 'crp_block_options', $arguments, $attributes );

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

		$style_array = Styles_Handler::get_style();
		$file_prefix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		if ( ! empty( $style_array['name'] ) ) {
			$style     = $style_array['name'];
			$extra_css = $style_array['extra_css'];

			wp_enqueue_style(
				'related-posts-block-editor',
				plugins_url( "css/{$style}{$file_prefix}.css", CRP_PLUGIN_FILE ),
				array( 'wp-edit-blocks' ),
				CRP_VERSION
			);
			wp_add_inline_style( 'related-posts-block-editor', $extra_css );
		}
	}
}

<?php
/**
 * Functions to register client-side assets (scripts and stylesheets) for the
 * Gutenberg block.
 *
 * @package Contextual_Related_Posts
 */

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @since 3.2.0
 */
function crp_register_blocks() {
	// Register Popular Posts block.
	register_block_type_from_metadata(
		CRP_PLUGIN_DIR . 'includes/blocks/related-posts/',
		array(
			'render_callback' => 'render_crp_block',
		)
	);
}
add_action( 'init', 'crp_register_blocks' );


/**
 * Renders the `contextual-related-posts/related-posts` block on server.
 *
 * @since 2.8.0
 * @param array $attributes The block attributes.
 *
 * @return string Returns the post content with latest posts added.
 */
function render_crp_block( $attributes ) {

	$attributes['extra_class'] = $attributes['className'];

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

	return get_crp( $arguments );
}

/**
 * Enqueue scripts and styles for the block editor.
 *
 * @since 3.2.0
 */
function crp_enqueue_block_editor_assets() {

	$style_array = crp_get_style();
	$file_prefix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	if ( ! empty( $style_array['name'] ) ) {
		$style     = $style_array['name'];
		$extra_css = $style_array['extra_css'];

		wp_enqueue_style(
			'related-posts-block-editor',
			plugins_url( "css/{$style}{$file_prefix}.css", CRP_PLUGIN_FILE ),
			array( 'wp-edit-blocks' ),
			filemtime( CRP_PLUGIN_DIR . "css/{$style}{$file_prefix}.css" )
		);
		wp_add_inline_style( 'related-posts-block-editor', $extra_css );
	}
}
add_action( 'enqueue_block_editor_assets', 'crp_enqueue_block_editor_assets' );

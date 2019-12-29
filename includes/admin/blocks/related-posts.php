<?php
/**
 * Functions to register client-side assets (scripts and stylesheets) for the
 * Gutenberg block.
 *
 * @package   Contextual_Related_Posts
 */

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
 * Registers all block assets so that they can be enqueued through Gutenberg in
 * the corresponding context.
 *
 * @since 2.8.0
 */
function crp_block_init() {
	// Skip block registration if Gutenberg is not enabled/merged.
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}
	$dir = dirname( __FILE__ );

	$index_js = 'related-posts/index.min.js';
	wp_register_script( // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NotInFooter
		'related-posts-block-editor',
		plugins_url( $index_js, __FILE__ ),
		array(
			'wp-blocks',
			'wp-i18n',
			'wp-element',
			'wp-components',
			'wp-block-editor',
			'wp-editor',
		),
		filemtime( "$dir/$index_js" )
	);

	// Localize the script with new data.
	$localized_variables = array(
		'orderings' => crp_get_orderings(),
	);
	wp_localize_script( 'related-posts-block-editor', 'crp_php_variables', $localized_variables );

	if ( 'rounded_thumbs' === crp_get_option( 'crp_styles' ) ) {

		$thumb_width  = crp_get_option( 'thumb_width' );
		$thumb_height = crp_get_option( 'thumb_height' );

		wp_register_style(
			'related-posts-block-editor',
			plugins_url( 'css/default-style.css', CRP_PLUGIN_FILE ),
			array( 'wp-edit-blocks' ),
			'1.0'
		);
		$custom_css = "
.crp_related a {
  width: {$thumb_width}px;
  height: {$thumb_height}px;
  text-decoration: none;
}
.crp_related img {
  max-width: {$thumb_width}px;
  margin: auto;
}
.crp_related .crp_title {
  width: 100%;
}
                ";

		wp_add_inline_style( 'related-posts-block-editor', $custom_css );
	}

	register_block_type(
		'contextual-related-posts/related-posts',
		array(
			'editor_script'   => 'related-posts-block-editor',
			'editor_style'    => 'related-posts-block-editor',
			'render_callback' => 'render_crp_block',
			'attributes'      => array(
				'className'        => array(
					'type'    => 'string',
					'default' => '',
				),
				'heading'          => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'limit'            => array(
					'type'    => 'number',
					'default' => 6,
				),
				'offset'           => array(
					'type'    => 'number',
					'default' => 0,
				),
				'show_excerpt'     => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'show_author'      => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'show_date'        => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'post_thumb_op'    => array(
					'type'    => 'string',
					'default' => 'inline',
				),
				'ordering'         => array(
					'type'    => 'string',
					'default' => 'relevance',
				),
				'random_order'     => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'other_attributes' => array(
					'type'    => 'string',
					'default' => '',
				),
			),
		)
	);

	if ( function_exists( 'wp_set_script_translations' ) ) {
		wp_set_script_translations( 'related-posts-block-editor', 'contextual-related-posts' );
	}
}
add_action( 'init', 'crp_block_init' );

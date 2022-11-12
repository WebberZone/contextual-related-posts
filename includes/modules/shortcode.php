<?php
/**
 * Shortcode module
 *
 * @package   Contextual_Related_Posts
 * @subpackage  Shortcode
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Creates a shortcode [crp limit="5" heading="1" cache="1"].
 *
 * @since   1.8.6
 *
 * @param   array  $atts   Shortcode attributes.
 * @param   string $content Post content.
 * @return  string Related Posts
 */
function crp_shortcode( $atts, $content = null ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
	global $crp_settings;
	$crp_settings = crp_get_settings();

	$atts = shortcode_atts(
		array_merge(
			$crp_settings,
			array(
				'heading'           => 1,
				'is_shortcode'      => 1,
				'offset'            => 0,
				'include_cat_ids'   => '',
				'include_post_ids'  => '',
				'related_meta_keys' => '',
			)
		),
		$atts,
		'crp'
	);

	return get_crp( $atts );
}
add_shortcode( 'crp', 'crp_shortcode' );

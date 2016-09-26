<?php
/**
 * Shortcode module
 *
 * @package   Contextual_Related_Posts
 * @subpackage	Shortcode
 * @author    Ajay D'Souza <me@ajaydsouza.com>
 * @license   GPL-2.0+
 * @link      https://webberzone.com
 * @copyright 2009-2015 Ajay D'Souza
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Creates a shortcode [crp limit="5" heading="1" cache="1"].
 *
 * @since	1.8.6
 *
 * @param	array  $atts   Shortcode attributes.
 * @param	string $content Post content.
 * @return	Related Posts
 */
function crp_shortcode( $atts, $content = null ) {
	global $crp_settings;

	$atts = shortcode_atts( array_merge(
		$crp_settings,
		array(
			'heading' => 1,
			'is_shortcode' => 1,
			'offset' => 0,
		)
	), $atts, 'crp' );

	return get_crp( $atts );
}
add_shortcode( 'crp', 'crp_shortcode' );



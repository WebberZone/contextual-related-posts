<?php
/**
 * Functions related to the header
 *
 * @package   Contextual_Related_Posts
 * @author    Ajay D'Souza
 * @license   GPL-2.0+
 * @link      https://webberzone.com
 * @copyright 2009-2019 Ajay D'Souza
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Filter for wp_head to include the custom CSS.
 *
 * @since 1.8.4
 */
function crp_header() {

	$add_to     = crp_get_option( 'add_to', false );
	$custom_css = stripslashes( crp_get_option( 'custom_css' ) );

	// Add CSS to header.
	if ( '' != $custom_css ) { // phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
		if ( ( is_single() ) && ! empty( $add_to['single'] ) ) {
			echo '<style type="text/css">' . $custom_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} elseif ( ( is_page() ) && ! empty( $add_to['page'] ) ) {
			echo '<style type="text/css">' . $custom_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} elseif ( ( is_home() ) && ! empty( $add_to['home'] ) ) {
			echo '<style type="text/css">' . $custom_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} elseif ( ( is_category() ) && ! empty( $add_to['category_archives'] ) ) {
			echo '<style type="text/css">' . $custom_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} elseif ( ( is_tag() ) && ! empty( $add_to['tag_archives'] ) ) {
			echo '<style type="text/css">' . $custom_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} elseif ( ( ( is_tax() ) || ( is_author() ) || ( is_date() ) ) && ! empty( $add_to['other_archives'] ) ) {
			echo '<style type="text/css">' . $custom_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} elseif ( is_active_widget( false, false, 'CRP_Widget', true ) ) {
			echo '<style type="text/css">' . $custom_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
}
add_action( 'wp_head', 'crp_header' );


/**
 * Enqueue styles.
 *
 * @since 1.9
 */
function crp_heading_styles() {

	$thumb_width  = crp_get_option( 'thumb_width' );
	$thumb_height = crp_get_option( 'thumb_height' );

	if ( 'rounded_thumbs' === crp_get_option( 'crp_styles' ) ) {
		wp_register_style( 'crp-style-rounded-thumbs', plugins_url( 'css/default-style.css', CRP_PLUGIN_FILE ), array(), '1.0' );
		wp_enqueue_style( 'crp-style-rounded-thumbs' );

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

		wp_add_inline_style( 'crp-style-rounded-thumbs', $custom_css );

	}
}
add_action( 'wp_enqueue_scripts', 'crp_heading_styles' );


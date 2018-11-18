<?php
/**
 * Functions related to the header
 *
 * @package   Contextual_Related_Posts
 * @author    Ajay D'Souza
 * @license   GPL-2.0+
 * @link      https://webberzone.com
 * @copyright 2009-2018 Ajay D'Souza
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
	global $crp_settings;

	$custom_css = stripslashes( $crp_settings['custom_CSS'] );

	// Add CSS to header.
	if ( '' != $custom_css ) {
		if ( ( is_single() ) ) {
			echo '<style type="text/css">' . $custom_css . '</style>'; // WPCS: XSS ok.
		} elseif ( ( is_page() ) ) {
			echo '<style type="text/css">' . $custom_css . '</style>'; // WPCS: XSS ok.
		} elseif ( ( is_home() ) && ( $crp_settings['add_to_home'] ) ) {
			echo '<style type="text/css">' . $custom_css . '</style>'; // WPCS: XSS ok.
		} elseif ( ( is_category() ) && ( $crp_settings['add_to_category_archives'] ) ) {
			echo '<style type="text/css">' . $custom_css . '</style>'; // WPCS: XSS ok.
		} elseif ( ( is_tag() ) && ( $crp_settings['add_to_tag_archives'] ) ) {
			echo '<style type="text/css">' . $custom_css . '</style>'; // WPCS: XSS ok.
		} elseif ( ( ( is_tax() ) || ( is_author() ) || ( is_date() ) ) && ( $crp_settings['add_to_archives'] ) ) {
			echo '<style type="text/css">' . $custom_css . '</style>'; // WPCS: XSS ok.
		} elseif ( is_active_widget( false, false, 'CRP_Widget', true ) ) {
			echo '<style type="text/css">' . $custom_css . '</style>'; // WPCS: XSS ok.
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
	global $crp_settings;

	if ( 'rounded_thumbs' === $crp_settings['crp_styles'] ) {
		wp_register_style( 'crp-style-rounded-thumbs', plugins_url( 'css/default-style.css', CRP_PLUGIN_FILE ) );
		wp_enqueue_style( 'crp-style-rounded-thumbs' );

		$custom_css = "
.crp_related a {
  width: {$crp_settings['thumb_width']}px;
  height: {$crp_settings['thumb_height']}px;
  text-decoration: none;
}
.crp_related img {
  max-width: {$crp_settings['thumb_width']}px;
  margin: auto;
}
.crp_related .crp_title {
  width: " . ( $crp_settings['thumb_width'] ) . 'px;
}
                ';

		wp_add_inline_style( 'crp-style-rounded-thumbs', $custom_css );

	}
}
add_action( 'wp_enqueue_scripts', 'crp_heading_styles' );


<?php
/**
 * Functions related to the header
 *
 * @package   Contextual_Related_Posts
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

	$style_array = crp_get_style();

	if ( ! empty( $style_array['name'] ) ) {
		$style     = $style_array['name'];
		$extra_css = $style_array['extra_css'];

		wp_register_style( "crp-style-{$style}", plugins_url( "css/{$style}.min.css", CRP_PLUGIN_FILE ), array(), '1.0.1' );
		wp_enqueue_style( "crp-style-{$style}" );
		wp_add_inline_style( "crp-style-{$style}", $extra_css );
	}
}
add_action( 'wp_enqueue_scripts', 'crp_heading_styles' );


/**
 * Get the current style for the related posts.
 *
 * @since 3.0.0
 *
 * @return array Contains two elements:
 *               'name' holding style name and 'extra_css' to be added inline.
 */
function crp_get_style() {

	$style        = array();
	$thumb_width  = crp_get_option( 'thumb_width' );
	$thumb_height = crp_get_option( 'thumb_height' );
	$crp_style    = crp_get_option( 'crp_styles' );

	switch ( $crp_style ) {
		case 'rounded_thumbs':
			$style['name']      = 'rounded-thumbs';
			$style['extra_css'] = "
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
			break;

		case 'masonry':
			$style['name']      = 'masonry';
			$style['extra_css'] = '';
			break;

		case 'grid':
			$style['name']      = 'grid';
			$style['extra_css'] = "
			.crp_related ul {
				grid-template-columns: repeat(auto-fill, minmax({$thumb_width}px, 1fr));
			}
			";
			break;

		case 'thumbs_grid':
			$row_height = max( 0, $thumb_height - 50 );

			$style['name']      = 'thumbs-grid';
			$style['extra_css'] = "
			.crp_related ul li a.crp_link {
				grid-template-rows: {$row_height}px 50px;
			}
			.crp_related ul {
				grid-template-columns: repeat(auto-fill, minmax({$thumb_width}px, 1fr));
			}
			";
			break;

		default:
			$style['name']      = '';
			$style['extra_css'] = '';
			break;
	}

	return $style;
}

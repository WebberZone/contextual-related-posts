<?php
/**
 * Functions related to the content
 *
 * @package   Contextual_Related_Posts
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Content function with user defined filter.
 *
 * @since 1.9
 */
function crp_content_prepare_filter() {

	$priority = crp_get_option( 'content_filter_priority' );

	add_filter( 'the_content', 'crp_content_filter', $priority );
}
add_action( 'template_redirect', 'crp_content_prepare_filter' );


/**
 * Filter for 'the_content' to add the related posts.
 *
 * @since 1.0.1
 *
 * @param string $content Post content.
 * @return string After the filter has been processed
 */
function crp_content_filter( $content ) {

	global $post, $crp_settings;

	// Return if it's not in the loop or in the main query.
	if ( ! in_the_loop() && ! is_main_query() ) {
		return $content;
	}

	// Return if this is a mobile device and disable on mobile option is enabled.
	if ( wp_is_mobile() && crp_get_option( 'disable_on_mobile' ) ) {
		return $content;
	}

	// Return if this is an amp page and disable on amp option is enabled.
	if ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() && crp_get_option( 'disable_on_amp' ) ) {
		return $content;
	}

	// Check exclusions.
	if ( crp_exclude_on( $post, $crp_settings ) ) {
		return $content;    // Exit without adding related posts.
	}

	$add_to = crp_get_option( 'add_to', false );

	// Else add the content.
	if ( ( ( is_single() ) && ! empty( $add_to['single'] ) ) ||
	( ( is_page() ) && ! empty( $add_to['page'] ) ) ||
	( ( is_home() ) && ! empty( $add_to['home'] ) ) ||
	( ( is_category() ) && ! empty( $add_to['category_archives'] ) ) ||
	( ( is_tag() ) && ! empty( $add_to['tag_archives'] ) ) ||
	( ( ( is_tax() ) || ( is_author() ) || ( is_date() ) ) && ! empty( $add_to['other_archives'] ) ) ) {

		$crp_code = get_crp( 'is_widget=0' );

		return crp_generate_content( $content, $crp_code );

	} else {
		return $content;
	}
}


/**
 * Helper for inserting crp code into or alongside content
 *
 * @since 2.3.0
 *
 * @param string $content Post content.
 * @param string $crp_code  CRP generated code.
 * @return string After the filter has been processed
 */
function crp_generate_content( $content, $crp_code ) {

	$insert_after_paragraph = crp_get_option( 'insert_after_paragraph' );

	if ( -1 === (int) $insert_after_paragraph || ! is_numeric( $insert_after_paragraph ) ) {
		return $content . $crp_code;
	} elseif ( 0 === (int) $insert_after_paragraph ) {
		return $crp_code . $content;
	} else {
		return crp_insert_after_paragraph( $content, $crp_code, $insert_after_paragraph );
	}

}

/**
 * Helper for inserting code after a closing paragraph tag
 *
 * @since 2.3.0
 *
 * @param string $content Post content.
 * @param string $crp_code  CRP generated code.
 * @param string $paragraph_id Paragraph number to insert after.
 * @return string After the filter has been processed
 */
function crp_insert_after_paragraph( $content, $crp_code, $paragraph_id ) {
	$closing_p  = '</p>';
	$paragraphs = explode( $closing_p, $content );

	if ( count( $paragraphs ) >= $paragraph_id ) {
		foreach ( $paragraphs as $index => $paragraph ) {

			if ( trim( $paragraph ) ) {
				$paragraphs[ $index ] .= $closing_p;
			}

			if ( (int) $paragraph_id === $index + 1 ) {
				$paragraphs[ $index ] .= $crp_code;
			}
		}

		return implode( '', $paragraphs );
	}

	return $content . $crp_code;
}

/**
 * Filter to add related posts to feeds.
 *
 * @since 1.8.4
 *
 * @param   string $content Post content.
 * @return  string  Formatted content
 */
function crp_rss_filter( $content ) {

	$add_to = crp_get_option( 'add_to', false );

	$limit_feed         = crp_get_option( 'limit_feed' );
	$show_excerpt_feed  = crp_get_option( 'show_excerpt_feed' );
	$post_thumb_op_feed = crp_get_option( 'post_thumb_op_feed' );

	if ( isset( $add_to['feed'] ) && $add_to['feed'] ) {
		$output  = $content;
		$output .= get_crp( 'is_widget=0&limit=' . $limit_feed . '&show_excerpt=' . $show_excerpt_feed . '&post_thumb_op=' . $post_thumb_op_feed );
		return $output;
	} else {
		return $content;
	}
}
add_filter( 'the_excerpt_rss', 'crp_rss_filter' );
add_filter( 'the_content_feed', 'crp_rss_filter' );


/**
 * Echos the related posts. Used for manual install
 *
 * @since 1.0.1
 *
 * @param string $args Array of arguments to control the output.
 */
function echo_crp( $args = array() ) {

	$defaults = array(
		'is_manual' => true,
	);

	// Parse incomming $args into an array and merge it with $defaults.
	$args = wp_parse_args( $args, $defaults );

	echo get_crp( $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}


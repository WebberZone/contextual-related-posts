<?php
/**
 * Contextual Related Posts.
 *
 * Contextual Related Posts is the best related posts plugin for WordPress that
 * allows you to display a list of related posts on your website and in your feed.
 *
 * @package   Contextual_Related_Posts
 * @author    Ajay D'Souza
 * @license   GPL-2.0+
 * @link      https://webberzone.com
 * @copyright 2009-2018 Ajay D'Souza
 *
 * @wordpress-plugin
 * Plugin Name: Contextual Related Posts
 * Plugin URI:  https://webberzone.com/plugins/contextual-related-posts/
 * Description: Display a set of related posts on your website or in your feed. Increase reader retention and reduce bounce rates
 * Version:     2.5.0
 * Author:      WebberZone
 * Author URI:  https://webberzone.com
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: contextual-related-posts
 * Domain Path: /languages
 * GitHub Plugin URI: https://github.com/WebberZone/contextual-related-posts/
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Holds the filesystem directory path (with trailing slash) for Contextual Related Posts.
 *
 * @since 2.3.0
 *
 * @var string Plugin folder path
 */
if ( ! defined( 'CRP_PLUGIN_DIR' ) ) {
	define( 'CRP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

/**
 * Holds the filesystem directory path (with trailing slash) for Contextual Related Posts.
 *
 * @since 2.3.0
 *
 * @var string Plugin folder URL
 */
if ( ! defined( 'CRP_PLUGIN_URL' ) ) {
	define( 'CRP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

/**
 * Holds the filesystem directory path (with trailing slash) for Contextual Related Posts.
 *
 * @since 2.3.0
 *
 * @var string Plugin Root File
 */
if ( ! defined( 'CRP_PLUGIN_FILE' ) ) {
	define( 'CRP_PLUGIN_FILE', __FILE__ );
}


/**
 * Maximum words to match in the content.
 *
 * @since 2.3.0
 *
 * @var int Maximum number of words to match.
 */
if ( ! defined( 'CRP_MAX_WORDS' ) ) {
	define( 'CRP_MAX_WORDS', 500 );
}


/**
 * Global variable holding the current settings for Contextual Related Posts
 *
 * @since   1.8.10
 *
 * @var array
 */
global $crp_settings;
$crp_settings = crp_read_options();


/**
 * Default options.
 *
 * @since 1.0.1
 *
 * @return array Default options
 */
function crp_default_options() {

	$title = __( '<h3>Related Posts:</h3>', 'contextual-related-posts' );

	$blank_output_text = __( 'No related posts found', 'contextual-related-posts' );

	$thumb_default = plugins_url( 'default.png', __FILE__ );

	$crp_settings = array(
		// General options.
		'cache'                    => false,            // Cache output for faster page load.

		'add_to_content'           => true,     // Add related posts to content (only on single posts).
		'add_to_page'              => true,     // Add related posts to content (only on single pages).
		'add_to_feed'              => false,        // Add related posts to feed (full).
		'add_to_home'              => false,        // Add related posts to home page.
		'add_to_category_archives' => false,        // Add related posts to category archives.
		'add_to_tag_archives'      => false,        // Add related posts to tag archives.
		'add_to_archives'          => false,        // Add related posts to other archives.

		'content_filter_priority'  => 10,   // Content priority.
		'insert_after_paragraph'   => -1,   // Insert after paragraph number.
		'disable_on_mobile'        => false, // Disable on mobile.
		'disable_on_amp'           => false, // Disable on AMP.
		'show_metabox'             => true, // Show metabox to admins.
		'show_metabox_admins'      => false,    // Limit to admins as well.

		'show_credit'              => false,        // Link to this plugin's page?

		// List tuning options.
		'limit'                    => '6',              // How many posts to display?
		'daily_range'              => '1095',               // How old posts should be displayed?
		'random_order'             => false,    // Randomise posts.

		'match_content'            => true,     // Match against post content as well as title.
		'match_content_words'      => '0',  // How many characters of content should be matched? 0 for all chars.

		'post_types'               => 'post,page',      // WordPress custom post types.
		'same_post_type'           => false,    // Limit to the same post type.

		'exclude_categories'       => '',   // Exclude these categories.
		'exclude_cat_slugs'        => '',   // Exclude these categories (slugs).
		'exclude_post_ids'         => '',   // Comma separated list of page / post IDs that are to be excluded in the results.

		// Output options.
		'title'                    => $title,           // Add before the content.
		'blank_output'             => true,     // Blank output?
		'blank_output_text'        => $blank_output_text,       // Blank output text.

		'show_excerpt'             => false,            // Show post excerpt in list item.
		'show_date'                => false,            // Show date in list item.
		'show_author'              => false,            // Show author in list item.
		'excerpt_length'           => '10',     // Length of characters.
		'title_length'             => '60',     // Limit length of post title.

		'link_new_window'          => false,            // Open link in new window.
		'link_nofollow'            => false,            // Includes rel nofollow to links.

		'before_list'              => '<ul>',   // Before the entire list.
		'after_list'               => '</ul>',  // After the entire list.
		'before_list_item'         => '<li>',   // Before each list item.
		'after_list_item'          => '</li>',  // After each list item.

		'exclude_on_post_ids'      => '',   // Comma separate list of page/post IDs to not display related posts on.
		'exclude_on_post_types'    => '',       // WordPress custom post types.

		// Thumbnail options.
		'post_thumb_op'            => 'inline', // Default option to display text and no thumbnails in posts.
		'thumb_size'               => 'thumbnail',  // Default thumbnail size.
		'thumb_height'             => '150',    // Height of thumbnails.
		'thumb_width'              => '150',    // Width of thumbnails.
		'thumb_crop'               => true,     // Crop mode. default is hard crop.
		'thumb_html'               => 'html',       // Use HTML or CSS for width and height of the thumbnail?
		'thumb_meta'               => 'post-image', // Meta field that is used to store the location of default thumbnail image.
		'scan_images'              => true,         // Scan post for images.
		'thumb_default'            => $thumb_default,   // Default thumbnail image.
		'thumb_default_show'       => true, // Show default thumb if none found (if false, don't show thumb at all).

		// Feed options.
		'limit_feed'               => '5',          // How many posts to display in feeds.
		'post_thumb_op_feed'       => 'text_only',  // Default option to display text and no thumbnails in Feeds.
		'thumb_height_feed'        => '50', // Height of thumbnails in feed.
		'thumb_width_feed'         => '50', // Width of thumbnails in feed.
		'show_excerpt_feed'        => false,            // Show description in list item in feed.

		// Custom styles.
		'custom_CSS'               => '',           // Custom CSS to style the output.
		'include_default_style'    => true,         // Include default style - Will be DEPRECATED in the next version.
		'crp_styles'               => 'rounded_thumbs', // Defaault style is rounded thubnails.
	);

	/**
	 * Filters the default options array.
	 *
	 * @since   1.9.1
	 *
	 * @param   array   $crp_settings   Default options.
	 */
	return apply_filters( 'crp_default_options', $crp_settings );
}


/**
 * Function to read options from the database.
 *
 * @since 1.0.1
 *
 * @return array Contextual Related Posts options
 */
function crp_read_options() {
	$crp_settings_changed = false;

	$defaults = crp_default_options();

	$crp_settings = array_map( 'stripslashes', (array) get_option( 'ald_crp_settings' ) );
	unset( $crp_settings[0] ); // Produced by the (array) casting when there's nothing in the DB.

	foreach ( $defaults as $k => $v ) {
		if ( ! isset( $crp_settings[ $k ] ) ) {
			$crp_settings[ $k ] = $v;
		}
		$crp_settings_changed = true;
	}
	if ( true === $crp_settings_changed ) {
		update_option( 'ald_crp_settings', $crp_settings );
	}

	/**
	 * Filters the options array.
	 *
	 * @since   1.9.1
	 *
	 * @param   array   $crp_settings   Options read from the database
	 */
	return apply_filters( 'crp_read_options', $crp_settings );
}


/*
 ----------------------------------------------------------------------------*
 * CRP modules & includes
 *----------------------------------------------------------------------------
 */

require_once CRP_PLUGIN_DIR . 'includes/plugin-activator.php';
require_once CRP_PLUGIN_DIR . 'includes/i10n.php';
require_once CRP_PLUGIN_DIR . 'includes/output-generator.php';
require_once CRP_PLUGIN_DIR . 'includes/media.php';
require_once CRP_PLUGIN_DIR . 'includes/tools.php';
require_once CRP_PLUGIN_DIR . 'includes/header.php';
require_once CRP_PLUGIN_DIR . 'includes/content.php';
require_once CRP_PLUGIN_DIR . 'includes/main-query.php';
require_once CRP_PLUGIN_DIR . 'includes/modules/manual-posts.php';
require_once CRP_PLUGIN_DIR . 'includes/modules/shortcode.php';
require_once CRP_PLUGIN_DIR . 'includes/modules/taxonomies.php';
require_once CRP_PLUGIN_DIR . 'includes/modules/exclusions.php';
require_once CRP_PLUGIN_DIR . 'includes/modules/class-crp-widget.php';


/*
 ----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------
 */

if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {

	require_once CRP_PLUGIN_DIR . 'admin/admin.php';
	require_once CRP_PLUGIN_DIR . 'admin/loader.php';
	require_once CRP_PLUGIN_DIR . 'admin/metabox.php';
	require_once CRP_PLUGIN_DIR . 'admin/cache.php';

} // End if().


/*
 ----------------------------------------------------------------------------*
 * Deprecated functions
 *----------------------------------------------------------------------------
 */

require_once CRP_PLUGIN_DIR . 'includes/deprecated.php';


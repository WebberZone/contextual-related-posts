<?php
/*
Plugin Name: Contextual Related Posts
Version:     1.6.3
Plugin URI:  http://ajaydsouza.com/wordpress/plugins/contextual-related-posts/
Description: Show user defined number of contextually related posts. Based on the plugin by <a href="http://weblogtoolscollection.com">Mark Ghosh</a>.  <a href="options-general.php?page=crp_options">Configure...</a>
Author:      Ajay D'Souza
Author URI:  http://ajaydsouza.com/
*/

if (!defined('ABSPATH')) die("Aren't you supposed to come here via WP-Admin?");

define('ALD_crp_DIR', dirname(__FILE__));
define('CRP_LOCAL_NAME', 'crp');

// Pre-2.6 compatibility
if ( ! defined( 'WP_CONTENT_URL' ) )
      define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( ! defined( 'WP_CONTENT_DIR' ) )
      define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( ! defined( 'WP_PLUGIN_URL' ) )
      define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( ! defined( 'WP_PLUGIN_DIR' ) )
      define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );

// Guess the location
$crp_path = WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__));
$crp_url = WP_PLUGIN_URL.'/'.plugin_basename(dirname(__FILE__));

function ald_crp_init() {
	//* Begin Localization Code */
	$crp_localizationName = CRP_LOCAL_NAME;
	$crp_comments_locale = get_locale();
	$crp_comments_mofile = ALD_crp_DIR . "/languages/" . $crp_localizationName . "-". $crp_comments_locale.".mo";
	load_textdomain($crp_localizationName, $crp_comments_mofile);
	//* End Localization Code */
}
add_action('init', 'ald_crp_init');


/*********************************************************************
*				Main Function (Do not edit)							*
********************************************************************/
function ald_crp() {
	global $wpdb, $post, $single;

	$crp_settings = crp_read_options();
	$limit = (stripslashes($crp_settings['limit']));
	$exclude_categories = explode(',',$crp_settings['exclude_categories']);
	
	// Make sure the post is not from the future
	$time_difference = get_settings('gmt_offset');
	$now = gmdate("Y-m-d H:i:s",(time()+($time_difference*3600)));

	if($crp_settings['match_content']) {
		$stuff = addslashes($post->post_title. ' ' . $post->post_content);
	}
	else {
		$stuff = addslashes($post->post_title);
	}
	
	
	if ((is_int($post->ID))&&($stuff != '')) {
		$sql = "SELECT DISTINCT ID,post_title,post_date,post_content,"
		. "MATCH(post_title,post_content) AGAINST ('".$stuff."') AS score "
		. "FROM ".$wpdb->posts." WHERE "
		. "MATCH (post_title,post_content) AGAINST ('".$stuff."') "
		. "AND post_date <= '".$now."' "
		. "AND post_status = 'publish' "
		. "AND id != ".$post->ID." ";
		if ($crp_settings['exclude_pages']) $sql .= "AND post_type = 'post' ";
		$sql .= "ORDER BY score DESC ";
		
		$search_counter = 0;
		$searches = $wpdb->get_results($sql);
	} else {
		$searches = false;
	}
	
	$output = '<div id="crp_related">';
	
	if($searches){
		$output .= (stripslashes($crp_settings[title]));
		$output .= $crp_settings['before_list'];
		foreach($searches as $search) {
			$categorys = get_the_category($search->ID);	//Fetch categories of the plugin
			$p_in_c = false;	// Variable to check if post exists in a particular category
			$title = get_the_title($search->ID);
			foreach ($categorys as $cat) {	// Loop to check if post exists in excluded category
				$p_in_c = (in_array($cat->cat_ID, $exclude_categories)) ? true : false;
				if ($p_in_c) break;	// End loop if post found in category
			}

			if (!$p_in_c) {
				$output .= $crp_settings['before_list_item'];
				if (($crp_settings['post_thumb_op']=='inline')||($crp_settings['post_thumb_op']=='thumbs_only')) {
					$output .= '<a href="'.get_permalink($search->ID).'" rel="bookmark">';
					if ((function_exists('has_post_thumbnail')) && (has_post_thumbnail($search->ID))) {
						$output .= get_the_post_thumbnail( $search->ID, array($crp_settings[thumb_width],$crp_settings[thumb_height]), array('title' => $title,'alt' => $title,'class' => 'crp_thumb','border' => '0'));
					} else {
						$postimage = get_post_meta($search->ID, $crp_settings[thumb_meta], true);
						if ((!$postimage)&&($crp_settings['scan_images'])) {
							preg_match_all( '|<img.*?src=[\'"](.*?)[\'"].*?>|i', $search->post_content, $matches );
							// any image there?
							if( isset( $matches ) && $matches[1][0] ) {
								$postimage = $matches[1][0]; // we need the first one only!
							}
						}
						if (!$postimage) $postimage = $crp_settings[thumb_default];
						$output .= '<img src="'.$postimage.'" alt="'.$title.'" title="'.$title.'" width="'.$crp_settings[thumb_width].'" height="'.$crp_settings[thumb_height].'" border="0" class="crp_thumb" />';
					}
					$output .= '</a> ';
				}
				if (($crp_settings['post_thumb_op']=='inline')||($crp_settings['post_thumb_op']=='text_only')) {
					$output .= '<a href="'.get_permalink($search->ID).'" rel="bookmark" class="crp_title">'.$title.'</a>';
				}
				if ($crp_settings['show_excerpt']) {
					$output .= '<span class="crp_excerpt"> '.crp_excerpt($search->post_content,$crp_settings['excerpt_length']).'</span>';
				}
				$output .= $crp_settings['after_list_item'];
				$search_counter++; 
			}
			if ($search_counter == $limit) break;	// End loop when related posts limit is reached
		} //end of foreach loop
		if ($crp_settings['show_credit']) {
			$output .= $crp_settings['before_list_item'];
			$output .= __('Powered by',CRP_LOCAL_NAME);
			$output .= ' <a href="http://ajaydsouza.com/wordpress/plugins/contextual-related-posts/">Contextual Related Posts</a>'.$crp_settings['after_list_item'];
		}
		$output .= $crp_settings['after_list'];
	}else{
		$output .= ($crp_settings['blank_output']) ? ' ' : '<p>'.__('No related posts found',CRP_LOCAL_NAME).'</p>'; 
	}
	if ((strpos($output, $crp_settings['before_list_item'])) === false) {
		$output = '<div id="crp_related">';
		$output .= ($crp_settings['blank_output']) ? ' ' : '<p>'.__('No related posts found',CRP_LOCAL_NAME).'</p>'; 
	}
	$output .= '</div>';
	
	return $output;
}

function ald_crp_content($content) {
	
	global $single;
	$crp_settings = crp_read_options();
	$output = ald_crp();
	
    if((is_single())&&($crp_settings['add_to_content'])) {
        return $content.$output;
    } elseif((is_page())&&($crp_settings['add_to_page'])) {
        return $content.$output;
	} elseif((is_feed())&&($crp_settings['add_to_feed'])) {
        return $content.$output;
    } else {
        return $content;
    }
}
add_filter('the_content', 'ald_crp_content');

function echo_ald_crp() {
	$output = ald_crp();
	echo $output;
}

// Default Options
function crp_default_options() {
	global $crp_url;
	$title = __('<h3>Related Posts:</h3>',CRP_LOCAL_NAME);
	$thumb_default = $crp_url.'/default.png';

	$crp_settings = 	Array (
						title => $title,			// Add before the content
						add_to_content => true,		// Add related posts to content (only on single posts)
						add_to_page => false,		// Add related posts to content (only on single pages)
						add_to_feed => true,		// Add related posts to feed
						limit => '5',				// How many posts to display?
						show_credit => false,		// Link to this plugin's page?
						match_content => true,		// Match against post content as well as title
						exclude_pages => true,		// Exclude Pages
						blank_output => true,		// Blank output?
						exclude_categories => '',	// Exclude these categories
						exclude_cat_slugs => '',	// Exclude these categories (slugs)
						before_list => '<ul>',	// Before the entire list
						after_list => '</ul>',	// After the entire list
						before_list_item => '<li>',	// Before each list item
						after_list_item => '</li>',	// After each list item
						post_thumb_op => 'text_only',	// Display only text in posts
						thumb_height => '50',	// Height of thumbnails
						thumb_width => '50',	// Width of thumbnails
						thumb_meta => 'post-image',	// Meta field that is used to store the location of default thumbnail image
						thumb_default => $thumb_default,	// Default thumbnail image
						scan_images => false,			// Scan post for images
						show_excerpt => false,			// Show description in list item
						excerpt_length => '10',		// Length of characters
						);
	return $crp_settings;
}

// Function to read options from the database
function crp_read_options() 
{
	$crp_settings_changed = false;
	
	$defaults = crp_default_options();
	
	$crp_settings = array_map('stripslashes',(array)get_option('ald_crp_settings'));
	unset($crp_settings[0]); // produced by the (array) casting when there's nothing in the DB
	
	foreach ($defaults as $k=>$v) {
		if (!isset($crp_settings[$k]))
			$crp_settings[$k] = $v;
		$crp_settings_changed = true;	
	}
	if ($crp_settings_changed == true)
		update_option('ald_crp_settings', $crp_settings);
	
	return $crp_settings;

}

// Create full text index
function ald_crp_activate() {
	global $wpdb;

    $wpdb->hide_errors();
    $wpdb->query('ALTER TABLE '.$wpdb->posts.' ENGINE = MYISAM;');
    $wpdb->query('ALTER TABLE '.$wpdb->posts.' ADD FULLTEXT crp_related (post_title, post_content);');
    $wpdb->query('ALTER TABLE '.$wpdb->posts.' ADD FULLTEXT crp_related_title (post_title);');
    $wpdb->query('ALTER TABLE '.$wpdb->posts.' ADD FULLTEXT crp_related_content (post_content);');
    $wpdb->show_errors();
}
if (function_exists('register_activation_hook')) {
	register_activation_hook(__FILE__,'ald_crp_activate');
}

function crp_excerpt($content,$excerpt_length){
	$out = strip_tags($content);
	$blah = explode(' ',$out);
	if (!$excerpt_length) $excerpt_length = 10;
	if(count($blah) > $excerpt_length){
		$k = $excerpt_length;
		$use_dotdotdot = 1;
	}else{
		$k = count($blah);
		$use_dotdotdot = 0;
	}
	$excerpt = '';
	for($i=0; $i<$k; $i++){
		$excerpt .= $blah[$i].' ';
	}
	$excerpt .= ($use_dotdotdot) ? '...' : '';
	$out = $excerpt;
	return $out;
}

// This function adds an Options page in WP Admin
if (is_admin() || strstr($_SERVER['PHP_SELF'], 'wp-admin/')) {
	require_once(ALD_crp_DIR . "/admin.inc.php");

// Add meta links
function crp_plugin_actions( $links, $file ) {
	static $plugin;
	if (!$plugin) $plugin = plugin_basename(__FILE__);
 
	// create link
	if ($file == $plugin) {
		$links[] = '<a href="' . admin_url( 'options-general.php?page=crp_options' ) . '">' . __('Settings', CRP_LOCAL_NAME ) . '</a>';
		$links[] = '<a href="http://ajaydsouza.org">' . __('Support', CRP_LOCAL_NAME ) . '</a>';
		$links[] = '<a href="http://ajaydsouza.com/donate/">' . __('Donate', CRP_LOCAL_NAME ) . '</a>';
	}
	return $links;
}
global $wp_version;
if ( version_compare( $wp_version, '2.8alpha', '>' ) )
	add_filter( 'plugin_row_meta', 'crp_plugin_actions', 10, 2 ); // only 2.8 and higher
else add_filter( 'plugin_action_links', 'crp_plugin_actions', 10, 2 );

// Display message about plugin update option
function crp_check_version($file, $plugin_data) {
	global $wp_version;
	static $this_plugin;
	$wp_version = str_replace(".","",$wp_version);
	if (!$this_plugin) $this_plugin = plugin_basename(__FILE__);
	if ($file == $this_plugin){
		$current = $wp_version < 28 ? get_option('update_plugins') : get_transient('update_plugins');
		if (!isset($current->response[$file])) return false;

		$columns =  $wp_version < 28 ? 5 : 3;
		$url = 'http://svn.wp-plugins.org/contextual-related-posts/trunk/update-info.txt';
		$update = wp_remote_fopen($url);
		if ($update != "") {
			echo '<tr class="plugin-update-tr"><td colspan="'.$columns.'" class="plugin-update"><div class="update-message">';
			echo $update;
			echo '</div></td></tr>';
		}
	}
}
add_action('after_plugin_row', 'crp_check_version', 10, 2);


} // End admin.inc

?>
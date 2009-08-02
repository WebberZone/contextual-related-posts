<?php
/*
Plugin Name: Contextual Related Posts
Version:     1.4
Plugin URI:  http://ajaydsouza.com/wordpress/plugins/contextual-related-posts/
Description: Show user defined number of contextually related posts. Based on the plugin by <a href="http://weblogtoolscollection.com">Mark Ghosh</a>.  <a href="options-general.php?page=crp_options">Configure...</a>
Author:      Ajay D'Souza
Author URI:  http://ajaydsouza.com/
*/

if (!defined('ABSPATH')) die("Aren't you supposed to come here via WP-Admin?");

define('ALD_crp_DIR', dirname(__FILE__));
define('CRP_LOCAL_NAME', 'crp');

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
	$limit = $crp_settings['limit'];
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
	
	
	if (($post->ID != '')||($stuff != '')) {
		$sql = "SELECT DISTINCT ID,post_title,post_date,"
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
		$output .= $crp_settings['title'];
		$output .= '<ul>';
		foreach($searches as $search) {
			$categorys = get_the_category($search->ID);	//Fetch categories of the plugin
			$p_in_c = false;	// Variable to check if post exists in a particular category
			$title = trim(stripslashes($search->post_title));
			foreach ($categorys as $cat) {	// Loop to check if post exists in excluded category
				$p_in_c = (in_array($cat->cat_ID, $exclude_categories)) ? true : false;
				if ($p_in_c) break;	// End loop if post found in category
			}

			if (!$p_in_c) {
				$output .= '<li><a href="'.get_permalink($search->ID).'" rel="bookmark">'.$title.'</a></li>';
				$search_counter++; 
			}
			if ($search_counter == $limit) break;	// End loop when related posts limit is reached
		} //end of foreach loop
		$output .= '</ul>';
	}else{
		$output = '<div id="crp_related">';
		$output .= ($crp_settings['blank_output']) ? ' ' : '<p>'.__('No related posts found',CRP_LOCAL_NAME).'</p>'; 
	}
	if ((strpos($output, '<li>')) === false) {
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
	$title = __('<h3>Related Posts:</h3>',CRP_LOCAL_NAME);

	$crp_settings = 	Array (
						title => $title,			// Add before the content
						add_to_content => true,		// Add related posts to content (only on single posts)
						add_to_page => false,		// Add related posts to content (only on single pages)
						add_to_feed => true,		// Add related posts to feed
						limit => '5',				// How many posts to display?
						match_content => true,		// Match against post content as well as title
						exclude_pages => true,		// Exclude Pages
						blank_output => true,		// Blank output?
						exclude_categories => '',	// Exclude these categories
						exclude_cat_slugs => '',	// Exclude these categories
						);
	return $crp_settings;
}

// Function to read options from the database
function crp_read_options() 
{
	$crp_settings_changed = false;
	
	//ald_crp_activate();
	
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

// This function adds an Options page in WP Admin
if (is_admin() || strstr($_SERVER['PHP_SELF'], 'wp-admin/')) {
	require_once(ALD_crp_DIR . "/admin.inc.php");
}

// Add meta links
function crp_plugin_actions( $links, $file ) {
	$plugin = plugin_basename(__FILE__);
 
	// create link
	if ($file == $plugin) {
		$links[] = '<a href="' . admin_url( 'options-general.php?page=crp_options' ) . '">' . __('Settings', crp_LOCAL_NAME ) . '</a>';
		$links[] = '<a href="http://ajaydsouza.org">' . __('Support', CRP_LOCAL_NAME ) . '</a>';
		$links[] = '<a href="http://ajaydsouza.com/donate/">' . __('Donate', CRP_LOCAL_NAME ) . '</a>';
	}
	return $links;
}
global $wp_version;
if ( version_compare( $wp_version, '2.8alpha', '>' ) )
	add_filter( 'plugin_row_meta', 'crp_plugin_actions', 10, 2 ); // only 2.8 and higher
else add_filter( 'plugin_action_links', 'crp_plugin_actions', 10, 2 );


?>
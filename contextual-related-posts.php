<?php
/*
Plugin Name: Contextual Related Posts
Version:     1.0
Plugin URI:  http://ajaydsouza.com/wordpress/plugins/contextual-related-posts/
Description: Show user defined number of contextually related posts. Based on the plugin by <a href="http://weblogtoolscollection.com">Mark Ghosh</a>.  <a href="options-general.php?page=crp_options">Configure...</a>
Author:      Ajay D'Souza
Author URI:  http://ajaydsouza.com/
*/

if (!defined('ABSPATH')) die("Aren't you supposed to come here via WP-Admin?");

function ald_crp_init() {
     load_plugin_textdomain('myald_crp_plugin', PLUGINDIR.'/'.dirname(plugin_basename(__FILE__)));
}
add_action('init', 'ald_crp_init');

define('ALD_crp_DIR', dirname(__FILE__));

/*********************************************************************
*				Main Function (Do not edit)							*
********************************************************************/
function ald_crp() {
	global $wpdb, $post, $single;
    $poststable = $wpdb->posts;
	$crp_settings = crp_read_options();
	$limit = $crp_settings['limit'];
	
	// Make sure the post is not from the future
	$time_difference = get_settings('gmt_offset');
	$now = gmdate("Y-m-d H:i:s",(time()+($time_difference*3600)));

	$stuff = addslashes($post->post_title);
	$sql = "SELECT ID,post_title,post_content,post_excerpt,post_date,"
	. "MATCH(post_title,post_content) AGAINST ('$stuff') AS score "
	. "FROM $poststable WHERE "
	. "MATCH (post_title,post_content) AGAINST ('$stuff') "
	. "AND post_date <= '$now' "
	. "AND post_status = 'publish' "
	. "AND id != $post->ID "
	. "LIMIT 0,$limit";

	$search_counter = 0;
	$searches = $wpdb->get_results($sql);
	
	$output = '<div id="crp_related">'.$crp_settings['title'];
	
	if($searches){
		$output .= '<ul>';
		foreach($searches as $search) {
			$title = trim(stripslashes($search->post_title));
			if ($search_counter <= $limit) {
				$output .= '<li><a href="'.get_permalink($search->ID).'" rel="bookmark">'.$title.'</a></li>';
			} //end of search_counter loop
			$search_counter++; 
		} //end of foreach loop
		$output .= '</ul>';
	}else{
		$output .= '<p>'.__('No related posts found').'</p>'; 
	}
	
	$output .= '</div><br/><br/>';
	
	return $output;
}

function ald_crp_content($content) {
	
	$crp_settings = crp_read_options();
	$output = ald_crp();
	
    if((is_feed())&&($crp_settings['add_to_feed'])) {
        return $content.$output;
    } elseif(($single)&&($crp_settings['add_to_content'])) {
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
	$title = __('<h2>Related Posts:</h2>');

	$crp_settings = 	Array (
						title => $title,		// Add before the content
						add_to_content => true,		// Add related posts to content (only on single pages)
						add_to_feed => true,		// Add related posts to feed
						limit => '5'	// How many posts to display?
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
    $wpdb->show_errors();
}
if (function_exists('register_activation_hook')) {
	register_activation_hook(__FILE__,'ald_crp_activate');
}

// This function adds an Options page in WP Admin
if (is_admin() || strstr($_SERVER['PHP_SELF'], 'wp-admin/')) {
	require_once(ALD_crp_DIR . "/admin.inc.php");
}


?>
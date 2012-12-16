<?php
/*
Plugin Name: Contextual Related Posts
Version:     1.8.4
Plugin URI:  http://ajaydsouza.com/wordpress/plugins/contextual-related-posts/
Description: Displaying a set of related posts on your website or in your feed. Increase reader retention and reduce bounce rates
Author:      Ajay D'Souza
Author URI:  http://ajaydsouza.com/
*/

if (!defined('ABSPATH')) die("Aren't you supposed to come here via WP-Admin?");

define('ALD_CRP_DIR', dirname(__FILE__));
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
	$crp_comments_mofile = ALD_CRP_DIR . "/languages/" . $crp_localizationName . "-". $crp_comments_locale.".mo";
	load_textdomain($crp_localizationName, $crp_comments_mofile);
	//* End Localization Code */
}
add_action('init', 'ald_crp_init');


/*********************************************************************
*				Main Function (Do not edit)							*
********************************************************************/
function ald_crp( $args ) {
	$defaults = array(
		'is_widget' => FALSE,
		'echo' => TRUE,
	);
	$defaults = array_merge($defaults, crp_read_options());
	
	// Parse incomming $args into an array and merge it with $defaults
	$args = wp_parse_args( $args, $defaults );
	
	// OPTIONAL: Declare each item in $args as its own variable i.e. $type, $before.
	extract( $args, EXTR_SKIP );

	return get_crp($is_widget, $limit, $show_excerpt, $post_thumb_op);
}


//function get_crp($is_widget = FALSE, $limit = '5', $show_excerpt = FALSE) {
function get_crp($is_widget, $limit, $show_excerpt, $post_thumb_op) {
	global $wpdb, $post, $single;

	$crp_settings = crp_read_options();
	parse_str($crp_settings['post_types'],$post_types);
	if (empty($limit)) $limit = stripslashes($crp_settings['limit']);
	if (empty($post_thumb_op)) $post_thumb_op = stripslashes($crp_settings['post_thumb_op']);
	if (!isset($show_excerpt)) $show_excerpt = $crp_settings['show_excerpt'];
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
	
	$daily_range = $crp_settings['daily_range'] - 1;
	$current_date = strtotime ( '-'.$daily_range. ' DAY' , strtotime ( $now ) );
	$current_date = date ( 'Y-m-d H:i:s' , $current_date );
	
	if ((is_int($post->ID))&&($stuff != '')) {
		$sql = "SELECT DISTINCT ID,post_title,post_date "
		. " FROM ".$wpdb->posts." WHERE "
		. "MATCH (post_title,post_content) AGAINST ('".$stuff."') "
		. "AND post_date <= '".$now."' "
		. "AND post_date >= '".$current_date."' "
		. "AND post_status = 'publish' "
		. "AND ID != ".$post->ID." ";
		if ($crp_settings['exclude_post_ids']!='') $sql .= "AND ID NOT IN (".$crp_settings['exclude_post_ids'].") ";
		$sql .= "AND ( ";
		$multiple = false;
		foreach ($post_types as $post_type) {
			if ( $multiple ) $sql .= ' OR ';
			$sql .= " post_type = '".$post_type."' ";
			$multiple = true;
		}
		$sql .=" ) ";
		$sql .= "LIMIT ".$limit*3;
		
		$search_counter = 0;
		$searches = $wpdb->get_results($sql);
	} else {
		$searches = false;
	}
	
	$output = (is_singular()) ? '<div id="crp_related" class="crp_related">' : '<div class="crp_related">';
	
	if($searches){
		if(!$is_widget) $output .= (stripslashes($crp_settings['title']));
		$output .= $crp_settings['before_list'];
		foreach($searches as $search) {
			$categorys = get_the_category($search->ID);	//Fetch categories of the plugin
			$p_in_c = false;	// Variable to check if post exists in a particular category
			$title = crp_max_formatted_content(get_the_title($search->ID),$crp_settings['title_length']);
			foreach ($categorys as $cat) {	// Loop to check if post exists in excluded category
				$p_in_c = (in_array($cat->cat_ID, $exclude_categories)) ? true : false;
				if ($p_in_c) break;	// End loop if post found in category
			}

			if (!$p_in_c) {
				$output .= $crp_settings['before_list_item'];

				//$output .= '<a href="'.get_permalink($search->ID).'" rel="bookmark" class="crp_link">'; // Add beginning of link
				if ($post_thumb_op=='after') {
					$output .= '<a href="'.get_permalink($search->ID).'" rel="bookmark" class="crp_title">'.$title.'</a>'; // Add title if post thumbnail is to be displayed after
				}
				if ($post_thumb_op=='inline' || $post_thumb_op=='after' || $post_thumb_op=='thumbs_only') {
					$output .= '<a href="'.get_permalink($search->ID).'" rel="bookmark">';
					$output .= crp_get_the_post_thumbnail($search->ID, $crp_settings);
					$output .= '</a>';
				}
				if ($post_thumb_op=='inline' || $post_thumb_op=='text_only') {
					$output .= '<a href="'.get_permalink($search->ID).'" rel="bookmark" class="crp_title">'.$title.'</a>'; // Add title when required by settings
				}
				//$output .= '</a>'; // Close the link
				if ($show_excerpt) {
					$output .= '<span class="crp_excerpt"> '.crp_excerpt($search->ID,$crp_settings['excerpt_length']).'</span>';
				}
				$output .= $crp_settings['after_list_item'];
				$search_counter++; 
			}
			if ($search_counter == $limit) break;	// End loop when related posts limit is reached
		} //end of foreach loop
		if ($crp_settings['show_credit']) {
			$output .= $crp_settings['before_list_item'];
			$output .= __('Powered by',CRP_LOCAL_NAME);
			$output .= ' <a href="http://ajaydsouza.com/wordpress/plugins/contextual-related-posts/" rel="nofollow">Contextual Related Posts</a>'.$crp_settings['after_list_item'];
		}
		$output .= $crp_settings['after_list'];
	}else{
		$output .= ($crp_settings['blank_output']) ? ' ' : '<p>'.__('No related posts found',CRP_LOCAL_NAME).'</p>'; 
		//$output .= '<p>'.strip_tags($sql).'</p>'; 
	}
	if ((strpos($output, $crp_settings['before_list_item'])) === false) {
		$output = '<div id="crp_related">';
		$output .= ($crp_settings['blank_output']) ? ' ' : '<p>'.__('No related posts found',CRP_LOCAL_NAME).'</p>'; 
	}
	$output .= '</div>';
	
	return $output;
}

function ald_crp_content($content) {
	
	global $single, $post;
	$crp_settings = crp_read_options();
	
	
	$exclude_on_post_ids = explode(',',$crp_settings['exclude_on_post_ids']);
	//$p_in_c = (in_array($post->ID, $exclude_on_post_ids)) ? true : false;
	if (in_array($post->ID, $exclude_on_post_ids)) return $content;	// Exit without adding related posts
	
	
    if((is_single())&&($crp_settings['add_to_content'])) {
        return $content.ald_crp('is_widget=0');
    } elseif((is_page())&&($crp_settings['add_to_page'])) {
        return $content.ald_crp('is_widget=0');
    } elseif((is_home())&&($crp_settings['add_to_home'])) {
        return $content.ald_crp('is_widget=0');
    } elseif((is_category())&&($crp_settings['add_to_category_archives'])) {
        return $content.ald_crp('is_widget=0');
    } elseif((is_tag())&&($crp_settings['add_to_tag_archives'])) {
        return $content.ald_crp('is_widget=0');
    } elseif( ( (is_tax()) || (is_author()) || (is_date()) ) &&($crp_settings['add_to_archives'])) {
        return $content.ald_crp('is_widget=0');
    } else {
        return $content;
    }
}
add_filter('the_content', 'ald_crp_content');

function ald_crp_rss($content) {
	global $post;
	$crp_settings = crp_read_options();

	if($crp_settings['add_to_feed']) {
        return $content.ald_crp('is_widget=0');
    } else {
        return $content;
    }
}
add_filter('the_excerpt_rss', 'ald_crp_rss');
add_filter('the_content_feed', 'ald_crp_rss');


function echo_ald_crp() {
	echo ald_crp('is_widget=0');
}

// Create a Wordpress Widget for CRP
class WidgetCRP extends WP_Widget
{
	function WidgetCRP()
	{
		$widget_ops = array('classname' => 'widget_crp', 'description' => __( 'Display Related Posts',CRP_LOCAL_NAME) );
		$this->WP_Widget('widget_crp',__('Related Posts',CRP_LOCAL_NAME), $widget_ops);
	}
	function form($instance) {
		$title = esc_attr($instance['title']);
		$limit = esc_attr($instance['limit']);
		$show_excerpt = esc_attr($instance['show_excerpt']);
		$post_thumb_op = esc_attr($instance['post_thumb_op']);
		?>
		<p>
		<label for="<?php echo $this->get_field_id('title'); ?>">
		<?php _e('Title', CRP_LOCAL_NAME); ?>: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /> 
		</label>
		</p>
		<p>
		<label for="<?php echo $this->get_field_id('limit'); ?>">
		<?php _e('No. of posts', CRP_LOCAL_NAME); ?>: <input class="widefat" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" type="text" value="<?php echo attribute_escape($limit); ?>" /> 
		</label>
		</p>
		<p>
		<?php _e('Thumbnail options', CRP_LOCAL_NAME); ?>: <br />
		<select class="widefat" id="<?php echo $this->get_field_id('post_thumb_op'); ?>" name="<?php echo $this->get_field_name('post_thumb_op'); ?>">
		  <option value="inline" <?php if ($post_thumb_op=='inline') echo 'selected="selected"' ?>><?php _e('Thumbnails inline, before title',CRP_LOCAL_NAME); ?></option>
		  <option value="after" <?php if ($post_thumb_op=='after') echo 'selected="selected"' ?>><?php _e('Thumbnails inline, after title',CRP_LOCAL_NAME); ?></option>
		  <option value="thumbs_only" <?php if ($post_thumb_op=='thumbs_only') echo 'selected="selected"' ?>><?php _e('Only thumbnails, no text',CRP_LOCAL_NAME); ?></option>
		  <option value="text_only" <?php if ($post_thumb_op=='text_only') echo 'selected="selected"' ?>><?php _e('No thumbnails, only text.',CRP_LOCAL_NAME); ?></option>
		</select>
		</p>
		<p>
		<label for="<?php echo $this->get_field_id('show_excerpt'); ?>">
		<input id="<?php echo $this->get_field_id('show_excerpt'); ?>" name="<?php echo $this->get_field_name('show_excerpt'); ?>" type="checkbox" <?php if ($show_excerpt) echo 'checked="checked"' ?> /> <?php _e(' Show excerpt?', CRP_LOCAL_NAME); ?>
		</label>
		</p>
		<?php
	} //ending form creation
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['limit'] = ($new_instance['limit']);
		$instance['show_excerpt'] = ($new_instance['show_excerpt']);
		$instance['post_thumb_op'] = ($new_instance['post_thumb_op']);
		return $instance;
	} //ending update
	function widget($args, $instance) {
		global $wpdb;
		
		extract($args, EXTR_SKIP);
		
		$crp_settings = crp_read_options();

		$title = apply_filters('widget_title', empty($instance['title']) ? strip_tags($crp_settings['title']) : $instance['title']);
		$limit = $instance['limit'];
		$show_excerpt = $instance['show_excerpt'];
		$post_thumb_op = $instance['post_thumb_op'];
		if (empty($limit)) $limit = $crp_settings['limit'];
		
		$output = $before_widget;
		$output .= $before_title . $title . $after_title;
		$output .= ald_crp('is_widget=1&limit='.$limit.'&show_excerpt='.$show_excerpt.'&post_thumb_op='.$post_thumb_op);
		$output .= $after_widget;
	
		$exclude_on_post_ids = explode(',',$crp_settings['exclude_on_post_ids']);
		
		if( ( (is_single()) && (!is_single($exclude_on_post_ids)) ) || ( (is_page()) && (!is_page($exclude_on_post_ids)) ) ) {
			echo $output;
		}

	} //ending function widget
}
function init_ald_crp(){
	if (function_exists('register_widget')) { 
		register_widget('WidgetCRP');
	} 
}
add_action('init', 'init_ald_crp', 1); 
 

// Default Options
function crp_default_options() {
	global $crp_url;
	$title = __('<h3>Related Posts:</h3>',CRP_LOCAL_NAME);
	$thumb_default = $crp_url.'/default.png';
	// get relevant post types
	$args = array (
				'public' => true,
				'_builtin' => true
			);
	$post_types	= http_build_query(get_post_types($args), '', '&');

	$crp_settings = 	Array (
						'title' => $title,			// Add before the content
						'add_to_content' => true,		// Add related posts to content (only on single posts)
						'add_to_page' => false,		// Add related posts to content (only on single pages)
						'add_to_feed' => true,		// Add related posts to feed (full)
						'add_to_home' => false,		// Add related posts to home page
						'add_to_category_archives' => false,		// Add related posts to category archives
						'add_to_tag_archives' => false,		// Add related posts to tag archives
						'add_to_archives' => false,		// Add related posts to other archives
						'limit' => '5',				// How many posts to display?
						'daily_range' => '1095',				// How old posts should be displayed?
						'show_credit' => false,		// Link to this plugin's page?
						'match_content' => true,		// Match against post content as well as title
						'blank_output' => true,		// Blank output?
						'exclude_categories' => '',	// Exclude these categories
						'exclude_cat_slugs' => '',	// Exclude these categories (slugs)
						'exclude_post_ids' => '',	// Comma separated list of page / post IDs that are to be excluded in the results
						'exclude_on_post_ids' => '', 	// Comma separate list of page/post IDs to not display related posts on
						'before_list' => '<ul>',	// Before the entire list
						'after_list' => '</ul>',	// After the entire list
						'before_list_item' => '<li>',	// Before each list item
						'after_list_item' => '</li>',	// After each list item
						'post_thumb_op' => 'text_only',	// Display only text in posts
						'thumb_height' => '50',	// Height of thumbnails
						'thumb_width' => '50',	// Width of thumbnails
						'thumb_meta' => 'post-image',	// Meta field that is used to store the location of default thumbnail image
						'thumb_default' => $thumb_default,	// Default thumbnail image
						'thumb_default_show' => true,	// Show default thumb if none found (if false, don't show thumb at all)
						'thumb_timthumb' => true,	// Use timthumb
						'scan_images' => false,			// Scan post for images
						'show_excerpt' => false,			// Show description in list item
						'excerpt_length' => '10',		// Length of characters
						'title_length' => '60',		// Limit length of post title
						'post_types' => $post_types,		// WordPress custom post types
						'custom_CSS' => '',			// Custom CSS to style the output
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

// Header function
add_action('wp_head','crp_header');
function crp_header() {
	global $wpdb, $post, $single;

	$crp_settings = crp_read_options();
	$crp_custom_CSS = stripslashes($crp_settings['custom_CSS']);
	
	// Add CSS to header 
	if ($crp_custom_CSS != '') {
	    if((is_single())) {
			echo '<style type="text/css">'.$crp_custom_CSS.'</style>';
	    } elseif((is_page())) {
			echo '<style type="text/css">'.$crp_custom_CSS.'</style>';
	    } elseif((is_home())&&($crp_settings['add_to_home'])) {
			echo '<style type="text/css">'.$crp_custom_CSS.'</style>';
	    } elseif((is_category())&&($crp_settings['add_to_category_archives'])) {
			echo '<style type="text/css">'.$crp_custom_CSS.'</style>';
	    } elseif((is_tag())&&($crp_settings['add_to_tag_archives'])) {
			echo '<style type="text/css">'.$crp_custom_CSS.'</style>';
	    } elseif( ( (is_tax()) || (is_author()) || (is_date()) ) &&($crp_settings['add_to_archives'])) {
			echo '<style type="text/css">'.$crp_custom_CSS.'</style>';
	    } elseif ( is_active_widget( false, false, 'WidgetCRP', true ) ) {
			echo '<style type="text/css">'.$crp_custom_CSS.'</style>';
	    }
	}
}
	
// Create full text index
function ald_crp_activate() {
	global $wpdb;

    $wpdb->hide_errors();
	if(version_compare(5.6, $wpdb->db_version(), '<=')) 
		{ $wpdb->query('ALTER TABLE '.$wpdb->posts.' ENGINE = InnoDB;'); }
		else
		{ $wpdb->query('ALTER TABLE '.$wpdb->posts.' ENGINE = MYISAM;'); }
    
	$wpdb->query('ALTER TABLE '.$wpdb->posts.' ADD FULLTEXT crp_related (post_title, post_content);');
    $wpdb->query('ALTER TABLE '.$wpdb->posts.' ADD FULLTEXT crp_related_title (post_title);');
    $wpdb->query('ALTER TABLE '.$wpdb->posts.' ADD FULLTEXT crp_related_content (post_content);');
    $wpdb->show_errors();
	
}
if (function_exists('register_activation_hook')) {
	register_activation_hook(__FILE__,'ald_crp_activate');
}

// Filter function to resize post thumbnail. Filters out tp10_postimage
function crp_scale_thumbs($postimage, $thumb_width, $thumb_height, $thumb_timthumb) {
	global $crp_url;
	
	if ($thumb_timthumb) {
		$new_pi = $crp_url.'/timthumb/timthumb.php?src='.urlencode($postimage).'&amp;w='.$thumb_width.'&amp;h='.$thumb_height.'&amp;zc=1&amp;q=75';		
	} else {
		$new_pi = $postimage;
	}
	return $new_pi;
}
add_filter('crp_postimage', 'crp_scale_thumbs', 10, 4);

// Function to get the post thumbnail
function crp_get_the_post_thumbnail($postid, $settings_array) {

	global $crp_url;
	$result = get_post($postid);
	if (empty($settings_array)) $settings_array = crp_read_options();
	$output = '';
	$title = get_the_title($postid);
	
	if (function_exists('has_post_thumbnail') && ( (wp_get_attachment_image_src( get_post_thumbnail_id($result->ID) )!='') || (wp_get_attachment_image_src( get_post_thumbnail_id($result->ID) )!= false) ) ) {
		$postimage = wp_get_attachment_image_src( get_post_thumbnail_id($result->ID) );
		$postimage = apply_filters( $filter, $postimage[0], $settings_array['thumb_width'], $settings_array['thumb_height'], $settings_array['thumb_timthumb'] );
		$output .= '<img src="'.$postimage.'" alt="'.$title.'" title="'.$title.'" style="max-width:'.$settings_array['thumb_width'].'px;max-height:'.$settings_array['thumb_height'].'px;" border="0" class="crp_thumb" />';
	} else {
		$postimage = get_post_meta($result->ID, $settings_array['thumb_meta'], true);	// Check
		if (!$postimage && $settings_array['scan_images']) {
			preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $result->post_content, $matches );
			// any image there?
					if ( ( (strpos($matches[1][0], parse_url(get_option('home'),PHP_URL_HOST)) !== false) && (strpos($matches[1][0], 'http://') !== false) ) || (strpos($matches[1][0], 'http://') === false) )  {
					$postimage = $matches[1][0]; // we need the first one only!
			}
		}
		if (!$postimage) $postimage = get_post_meta($result->ID, '_video_thumbnail', true); // If no other thumbnail set, try to get the custom video thumbnail set by the Video Thumbnails plugin
		if ($settings_array['thumb_default_show'] && !$postimage) $postimage = $settings_array['thumb_default']; // If no thumb found and settings permit, use default thumb
		if ($postimage) {
			if ($settings_array['thumb_timthumb']) {
				$output .= '<img src="'.$crp_url.'/timthumb/timthumb.php?src='.urlencode($postimage).'&amp;w='.$settings_array['thumb_width'].'&amp;h='.$settings_array['thumb_height'].'&amp;zc=1&amp;q=75" alt="'.$title.'" title="'.$title.'" style="max-width:'.$settings_array['thumb_width'].'px;max-height:'.$settings_array['thumb_height'].'px;" border="0" class="crp_thumb" />';
			} else {
				$output .= '<img src="'.$postimage.'" alt="'.$title.'" title="'.$title.'" style="max-width:'.$settings_array['thumb_width'].'px;max-height:'.$settings_array['thumb_height'].'px;" border="0" class="crp_thumb" />';
			}
		}
	}
	
	return $output;
}

// Function to create an excerpt for the post
function crp_excerpt($id,$excerpt_length){
	$content = get_post($id)->post_excerpt;
	if ($content=='') $content = get_post($id)->post_content;
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

// Function to limit content by characters
function crp_max_formatted_content($content, $MaxLength = -1) {
  $content = strip_tags($content);  // Remove CRLFs, leaving space in their wake

  if (($MaxLength > 0) && (strlen($content) > $MaxLength)) {
    $aWords = preg_split("/[\s]+/", substr($content, 0, $MaxLength));

    // Break back down into a string of words, but drop the last one if it's chopped off
    if (substr($content, $MaxLength, 1) == " ") {
      $content = implode(" ", $aWords);
    } else {
      $content = implode(" ", array_slice($aWords, 0, -1)).'&hellip;';
    }
  }

  return $content;
}

// This function adds an Options page in WP Admin
if (is_admin() || strstr($_SERVER['PHP_SELF'], 'wp-admin/')) {
	require_once(ALD_CRP_DIR . "/admin.inc.php");

// Add meta links
function crp_plugin_actions( $links, $file ) {
	static $plugin;
	if (!$plugin) $plugin = plugin_basename(__FILE__);
 
	// create link
	if ($file == $plugin) {
		$links[] = '<a href="' . admin_url( 'options-general.php?page=crp_options' ) . '">' . __('Settings', CRP_LOCAL_NAME ) . '</a>';
		$links[] = '<a href="http://wordpress.org/support/plugin/contextual-related-posts">' . __('Support', CRP_LOCAL_NAME ) . '</a>';
		$links[] = '<a href="http://ajaydsouza.com/donate/">' . __('Donate', CRP_LOCAL_NAME ) . '</a>';
	}
	return $links;
}
global $wp_version;
if ( version_compare( $wp_version, '2.8alpha', '>' ) )
	add_filter( 'plugin_row_meta', 'crp_plugin_actions', 10, 2 ); // only 2.8 and higher
else add_filter( 'plugin_action_links', 'crp_plugin_actions', 10, 2 );


} // End admin.inc

?>
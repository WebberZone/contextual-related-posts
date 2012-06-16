<?php
/**********************************************************************
*					Admin Page										*
*********************************************************************/
if (!defined('ABSPATH')) die("Aren't you supposed to come here via WP-Admin?");

function crp_options() {
	
	global $wpdb;
    $poststable = $wpdb->posts;

	$crp_settings = crp_read_options();
	parse_str($crp_settings['post_types'],$post_types);
	$wp_post_types	= get_post_types( array(
		'public'	=> true,
	) );
	$posts_types_inc = array_intersect($wp_post_types, $post_types);


	if($_POST['crp_save']){
		$crp_settings[title] = ($_POST['title']);
		$crp_settings[limit] = intval($_POST['limit']);
		$crp_settings[exclude_cat_slugs] = ($_POST['exclude_cat_slugs']);
		$crp_settings[add_to_content] = (($_POST['add_to_content']) ? true : false);
		$crp_settings[add_to_page] = (($_POST['add_to_page']) ? true : false);
		$crp_settings[add_to_feed] = (($_POST['add_to_feed']) ? true : false);
		$crp_settings[match_content] = (($_POST['match_content']) ? true : false);
		$crp_settings[blank_output] = (($_POST['blank_output'] == 'blank' ) ? true : false);
		$crp_settings[blank_output_text] = $_POST['blank_output_text'];
		$crp_settings[post_thumb_op] = $_POST['post_thumb_op'];
		$crp_settings[before_list] = $_POST['before_list'];
		$crp_settings[after_list] = $_POST['after_list'];
		$crp_settings[before_list_item] = $_POST['before_list_item'];
		$crp_settings[after_list_item] = $_POST['after_list_item'];
		$crp_settings[thumb_meta] = $_POST['thumb_meta'];
		$crp_settings[thumb_default] = $_POST['thumb_default'];
		$crp_settings[thumb_height] = intval($_POST['thumb_height']);
		$crp_settings[thumb_width] = intval($_POST['thumb_width']);
		$crp_settings[thumb_default_show] = (($_POST['thumb_default_show']) ? true : false);
		$crp_settings[thumb_timthumb] = (($_POST['thumb_timthumb']) ? true : false);
		$crp_settings[scan_images] = (($_POST['scan_images']) ? true : false);
		$crp_settings[show_excerpt] = (($_POST['show_excerpt']) ? true : false);
		$crp_settings[excerpt_length] = intval($_POST['excerpt_length']);
		$crp_settings[show_credit] = (($_POST['show_credit']) ? true : false);
		$crp_settings[custom_CSS] = $_POST['custom_CSS'];
		$crp_settings[exclude_post_ids] = $_POST['exclude_post_ids'];
		
		$exclude_categories_slugs = explode(", ",$crp_settings[exclude_cat_slugs]);
		
		$exclude_categories = '';
		foreach ($exclude_categories_slugs as $exclude_categories_slug) {
			$catObj = get_category_by_slug($exclude_categories_slug);
			$exclude_categories .= $catObj->term_id . ',';
		}
		$crp_settings[exclude_categories] = substr($exclude_categories, 0, -2);

		$wp_post_types	= get_post_types( array(
			'public'	=> true,
		) );
		$post_types_arr = (is_array($_POST['post_types'])) ? $_POST['post_types'] : array('post' => 'post');
		$post_types = array_intersect($wp_post_types, $post_types_arr);
		$crp_settings[post_types] = http_build_query($post_types, '', '&');

		update_option('ald_crp_settings', $crp_settings);
		
		$crp_settings = crp_read_options();
		parse_str($crp_settings['post_types'],$post_types);
		$posts_types_inc = array_intersect($wp_post_types, $post_types);

		$str = '<div id="message" class="updated fade"><p>'. __('Options saved successfully.',CRP_LOCAL_NAME) .'</p></div>';
		echo $str;
	}
	
	if ($_POST['crp_default']){
		delete_option('ald_crp_settings');
		$crp_settings = crp_default_options();
		update_option('ald_crp_settings', $crp_settings);
		
		$crp_settings = crp_read_options();
		parse_str($crp_settings['post_types'],$post_types);
		$wp_post_types	= get_post_types( array(
			'public'	=> true,
		) );
		$posts_types_inc = array_intersect($wp_post_types, $post_types);

		$str = '<div id="message" class="updated fade"><p>'. __('Options set to Default.',CRP_LOCAL_NAME) .'</p></div>';
		echo $str;
	}
	if ($_POST['crp_recreate']){
		$sql = "ALTER TABLE $poststable DROP INDEX crp_related";
		$wpdb->query($sql);
		
		$sql = "ALTER TABLE $poststable DROP INDEX crp_related_title";
		$wpdb->query($sql);
		
		$sql = "ALTER TABLE $poststable DROP INDEX crp_related_content";
		$wpdb->query($sql);
		
		ald_crp_activate();
		
		$str = '<div id="message" class="updated fade"><p>'. __('Index recreated',CRP_LOCAL_NAME) .'</p></div>';
		echo $str;
	}
?>

<div class="wrap">
	<div id="page-wrap">
	<div id="inside">
		<div id="header">
		<h2>Contextual Related Posts</h2>
		</div>
	  <div id="side">
		<div class="side-widget">
			<span class="title"><?php _e('Support the development',CRP_LOCAL_NAME) ?></span>
			<div id="donate-form">
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
				<input type="hidden" name="cmd" value="_xclick">
				<input type="hidden" name="business" value="donate@ajaydsouza.com">
				<input type="hidden" name="lc" value="IN">
				<input type="hidden" name="item_name" value="Donation for Contextual Related Posts">
				<input type="hidden" name="item_number" value="crp">
				<strong><?php _e('Enter amount in USD: ',CRP_LOCAL_NAME) ?></strong> <input name="amount" value="10.00" size="6" type="text"><br />
				<input type="hidden" name="currency_code" value="USD">
				<input type="hidden" name="button_subtype" value="services">
				<input type="hidden" name="bn" value="PP-BuyNowBF:btn_donate_LG.gif:NonHosted">
				<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="<?php _e('Send your donation to the author of',CRP_LOCAL_NAME) ?> Contextual Related Posts" title="<?php _e('Send your donation to the author of',CRP_LOCAL_NAME) ?> Contextual Related Posts">
				<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
				</form>
			</div>
		</div>
		<div class="side-widget">
		<iframe src="//www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2Fajaydsouzacom&amp;width=292&amp;height=62&amp;colorscheme=light&amp;show_faces=false&amp;border_color&amp;stream=false&amp;header=true&amp;appId=113175385243" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:292px; height:62px;" allowTransparency="true"></iframe>
		</div>
		<div class="side-widget">
		<span class="title"><?php _e('Quick links') ?></span>				
		<ul>
			<li><a href="http://ajaydsouza.com/wordpress/plugins/contextual-related-posts/"><?php _e('Contextual Related Posts ');_e('plugin page',CRP_LOCAL_NAME) ?></a></li>
			<li><a href="http://ajaydsouza.com/wordpress/plugins/"><?php _e('Other plugins',CRP_LOCAL_NAME) ?></a></li>
			<li><a href="http://ajaydsouza.com/"><?php _e('Ajay\'s blog',CRP_LOCAL_NAME) ?></a></li>
			<li><a href="http://wordpress.org/support/plugin/contextual-related-posts"><?php _e('Support',CRP_LOCAL_NAME) ?></a></li>
			<li><a href="http://twitter.com/ajaydsouza"><?php _e('Follow @ajaydsouza on Twitter',CRP_LOCAL_NAME) ?></a></li>
		</ul>
		</div>
		<div class="side-widget">
		<span class="title"><?php _e('Recent developments',CRP_LOCAL_NAME) ?></span>				
		<?php require_once(ABSPATH . WPINC . '/rss.php'); wp_widget_rss_output('http://ajaydsouza.com/archives/category/wordpress/plugins/feed/', array('items' => 5, 'show_author' => 0, 'show_date' => 1));
		?>
		</div>
	  </div>

	  <div id="options-div">
	  <form method="post" id="crp_options" name="crp_options" onsubmit="return checkForm()">
		<fieldset class="options">
		<div class="tabber">
		<div class="tabbertab">
		<h3>
		  <?php _e('General options',CRP_LOCAL_NAME); ?>
		</h3>
			<table class="form-table">
			<tr style="vertical-align: top;"><th scope="row"><label for="limit"><?php _e('Number of related posts to display: ',CRP_LOCAL_NAME); ?></label></th>
			<td><input type="textbox" name="limit" id="limit" value="<?php echo attribute_escape(stripslashes($crp_settings[limit])); ?>"></td>
			</tr>
			<tr style="vertical-align: top;"><th scope="row"><?php _e('Post types to include in results (including custom post types)',CRP_LOCAL_NAME); ?></th>
			<td>
				<select name="post_types[]" multiple="multiple" size="<?php echo min(20,count($wp_post_types)); ?>">
					<?php foreach ($wp_post_types as $wp_post_type) {
						$post_type_op = '<option value="'.$wp_post_type.'"';
						if (in_array($wp_post_type, $posts_types_inc)) $post_type_op .= 'selected="selected"';
						$post_type_op .= '>'.$wp_post_type.'</option>'; 
						echo $post_type_op;
					}
					?>
				</select>
				<br /><?php _e('Use CTRL on Windows and COMMAND on Mac to select multiple items',CRP_LOCAL_NAME); ?>
			</td>
			</tr>
			<tr style="vertical-align: top;"><th scope="row"><label for="match_content"><?php _e('Find related posts based on content as well as title',CRP_LOCAL_NAME); ?></label></th>
			<td><input type="checkbox" name="match_content" id="match_content" <?php if ($crp_settings[match_content]) echo 'checked="checked"' ?> /> <br /><?php _e('If unchecked, only posts titles are used. (I recommend using a caching plugin if you enable this)',CRP_LOCAL_NAME); ?></td>
			</tr>
			<tr style="vertical-align: top;"><th scope="row"><label for="exclude_post_ids"><?php _e('List of post or page IDs to exclude from the results: ',CRP_LOCAL_NAME); ?></label></th>
			<td><input type="textbox" name="exclude_post_ids" id="exclude_post_ids" value="<?php echo attribute_escape(stripslashes($crp_settings[exclude_post_ids])); ?>"  style="width:250px"></td>
			</tr>
			<tr style="vertical-align: top;"><th scope="row"><label for="exclude_cat_slugs"><?php _e('Exclude Categories: ',CRP_LOCAL_NAME); ?></label></th>
			<td>
				<div style="position:relative;text-align:left">
					<table id="MYCUSTOMFLOATER" class="myCustomFloater" style="position:absolute;top:50px;left:0;background-color:#cecece;display:none;visibility:hidden">
					<tr><td><!--
							please see: http://chrisholland.blogspot.com/2004/09/geekstuff-css-display-inline-block.html
							to explain why i'm using a table here.
							You could replace the table/tr/td with a DIV, but you'd have to specify it's width and height
							-->
						<div class="myCustomFloaterContent">
						you should never be seeing this
						</div>
					</td></tr>
					</table>
					<textarea class="wickEnabled:MYCUSTOMFLOATER" cols="50" rows="3" wrap="virtual" name="exclude_cat_slugs"><?php echo (stripslashes($crp_settings[exclude_cat_slugs])); ?></textarea>
				</div>
			</td>
			</tr>
			<tr style="vertical-align: top;"><th scope="row"><label for="add_to_content"><?php _e('Add related posts to the post content on single posts',CRP_LOCAL_NAME); ?></label></th>
			<td><input type="checkbox" name="add_to_content" id="add_to_content" <?php if ($crp_settings[add_to_content]) echo 'checked="checked"' ?> /> <br /><?php _e('If you choose to disable this, please add <code>&lt;?php if(function_exists(\'echo_ald_crp\')) echo_ald_crp(); ?&gt;</code> to your template file where you want it displayed',CRP_LOCAL_NAME); ?></td>
			</tr>
			<tr style="vertical-align: top;"><th scope="row"><label for="add_to_page"><?php _e('Add related posts to pages',CRP_LOCAL_NAME); ?></label></th>
			<td><input type="checkbox" name="add_to_page" id="add_to_page" <?php if ($crp_settings[add_to_page]) echo 'checked="checked"' ?> /> <br /><?php _e('If you choose to disable this, please add <code>&lt;?php if(function_exists(\'echo_ald_crp\')) echo_ald_crp(); ?&gt;</code> to your template file where you want it displayed',CRP_LOCAL_NAME); ?></td>
			</tr>
			<tr style="vertical-align: top;"><th scope="row"><label for="add_to_feed"><?php _e('Add related posts to feed',CRP_LOCAL_NAME); ?></label></th>
			<td><input type="checkbox" name="add_to_feed" id="add_to_feed" <?php if ($crp_settings[add_to_feed]) echo 'checked="checked"' ?> /></td>
			</tr>
			<tr style="vertical-align: top;"><th scope="row"><label for="show_credit"><?php _e('Add a link to the plugin page as a final item in the list',CRP_LOCAL_NAME); ?></label></th>
			<td><input type="checkbox" name="show_credit" id="show_credit" <?php if ($crp_settings[show_credit]) echo 'checked="checked"' ?> /> <?php _e(' <em>Optional</em>',CRP_LOCAL_NAME); ?></td>
			</tr>

			</table>		

		</div>
		<div class="tabbertab">
		<h3>
		  <?php _e('Output Options',CRP_LOCAL_NAME); ?>
		</h3>
			<table class="form-table">
			<tr style="vertical-align: top;"><th scope="row"><label for="title"><?php _e('Title of related posts: ',CRP_LOCAL_NAME); ?></label></th>
			<td><input type="textbox" name="title" id="title" value="<?php echo attribute_escape(stripslashes($crp_settings[title])); ?>"  style="width:250px" /></td>
			</tr>
			<tr style="vertical-align: top;"><th scope="row"><label for="blank_output"><?php _e('When there are no posts, what should be shown?',CRP_LOCAL_NAME); ?></label></th>
			<td>
				<label>
				<input type="radio" name="blank_output" value="blank" id="blank_output_0" <?php if ($crp_settings['blank_output']) echo 'checked="checked"' ?> />
				<?php _e('Blank Output',CRP_LOCAL_NAME); ?></label>
				<br />
				<label>
				<input type="radio" name="blank_output" value="noposts" id="blank_output_1" <?php if (!$crp_settings['blank_output']) echo 'checked="checked"' ?> />
				<?php _e('Display "No Related Posts"',CRP_LOCAL_NAME); ?>
				</label>
			</td>
			</tr>
			<tr style="vertical-align: top;"><th scope="row"><label for="show_excerpt"><?php _e('Show post excerpt in list?',CRP_LOCAL_NAME); ?></label></th>
			<td><input type="checkbox" name="show_excerpt" id="show_excerpt" <?php if ($crp_settings[show_excerpt]) echo 'checked="checked"' ?> /></td>
			</tr>
			<tr style="vertical-align: top;"><th scope="row"><label for="excerpt_length"><?php _e('Length of excerpt (in words): ',CRP_LOCAL_NAME); ?></label></th>
			<td><input type="textbox" name="excerpt_length" id="excerpt_length" value="<?php echo stripslashes($crp_settings[excerpt_length]); ?>" /></td>
			</tr>
			<tr style="vertical-align: top; background: #eee"><th scope="row" colspan="2"><?php _e('Customize the output:',CRP_LOCAL_NAME); ?></th>
			</tr>
			<tr style="vertical-align: top;"><th scope="row"><label for="before_list"><?php _e('HTML to display before the list of posts: ',CRP_LOCAL_NAME); ?></label></th>
			<td><input type="textbox" name="before_list" id="before_list" value="<?php echo attribute_escape(stripslashes($crp_settings[before_list])); ?>" style="width:250px" /></td>
			</tr>
			<tr style="vertical-align: top;"><th scope="row"><label for="before_list_item"><?php _e('HTML to display before each list item: ',CRP_LOCAL_NAME); ?></label></th>
			<td><input type="textbox" name="before_list_item" id="before_list_item" value="<?php echo attribute_escape(stripslashes($crp_settings[before_list_item])); ?>" style="width:250px" /></td>
			</tr>
			<tr style="vertical-align: top;"><th scope="row"><label for="after_list_item"><?php _e('HTML to display after each list item: ',CRP_LOCAL_NAME); ?></label></th>
			<td><input type="textbox" name="after_list_item" id="after_list_item" value="<?php echo attribute_escape(stripslashes($crp_settings[after_list_item])); ?>" style="width:250px" /></td>
			</tr>
			<tr style="vertical-align: top;"><th scope="row"><label for="after_list"><?php _e('HTML to display after the list of posts: ',CRP_LOCAL_NAME); ?></label></th>
			<td><input type="textbox" name="after_list" id="after_list" value="<?php echo attribute_escape(stripslashes($crp_settings[after_list])); ?>" style="width:250px" /></td>
			</tr>
			<tr style="vertical-align: top; background: #eee"><th scope="row" colspan="2"><?php _e('Post thumbnail options:',CRP_LOCAL_NAME); ?></th>
			</tr>
			<tr style="vertical-align: top;"><th scope="row"><label for="post_thumb_op"><?php _e('Location of post thumbnail:',CRP_LOCAL_NAME); ?></label></th>
			<td>
				<label>
				<input type="radio" name="post_thumb_op" value="inline" id="post_thumb_op_0" <?php if ($crp_settings['post_thumb_op']=='inline') echo 'checked="checked"' ?> />
				<?php _e('Display thumbnails inline with posts, before title',CRP_LOCAL_NAME); ?></label>
				<br />
				<label>
				<input type="radio" name="post_thumb_op" value="after" id="post_thumb_op_1" <?php if ($crp_settings['post_thumb_op']=='after') echo 'checked="checked"' ?> />
				<?php _e('Display thumbnails inline with posts, after title',CRP_LOCAL_NAME); ?></label>
				<br />
				<label>
				<input type="radio" name="post_thumb_op" value="thumbs_only" id="post_thumb_op_2" <?php if ($crp_settings['post_thumb_op']=='thumbs_only') echo 'checked="checked"' ?> />
				<?php _e('Display only thumbnails, no text',CRP_LOCAL_NAME); ?></label>
				<br />
				<label>
				<input type="radio" name="post_thumb_op" value="text_only" id="post_thumb_op_3" <?php if ($crp_settings['post_thumb_op']=='text_only') echo 'checked="checked"' ?> />
				<?php _e('Do not display thumbnails, only text.',CRP_LOCAL_NAME); ?></label>
				<br />
			</td>
			</tr>
			<tr style="vertical-align: top;"><th scope="row"><label for="thumb_width"><?php _e('Maximum width of the thumbnail: ',CRP_LOCAL_NAME); ?></label></th>
			<td><input type="textbox" name="thumb_width" id="thumb_width" value="<?php echo attribute_escape(stripslashes($crp_settings[thumb_width])); ?>" style="width:30px" />px</td>
			</tr>
			<tr style="vertical-align: top;"><th scope="row"><label for="thumb_height"><?php _e('Maximum height of the thumbnail: ',CRP_LOCAL_NAME); ?></label></th>
			<td><input type="textbox" name="thumb_height" id="thumb_height" value="<?php echo attribute_escape(stripslashes($crp_settings[thumb_height])); ?>" style="width:30px" />px</td>
			</tr>
			<tr style="vertical-align: top;"><th scope="row"><label for="thumb_timthumb"><?php _e('Use timthumb to generate thumbnails? ',CRP_LOCAL_NAME); ?></label></th>
			<td><input type="checkbox" name="thumb_timthumb" id="thumb_timthumb" <?php if ($crp_settings[thumb_timthumb]) echo 'checked="checked"' ?> /> <br /><?php _e('If checked, <a href="http://www.binarymoon.co.uk/projects/timthumb/">timthumb</a> will be used to generate thumbnails',CRP_LOCAL_NAME); ?></td>
			</tr>
			<tr style="vertical-align: top;"><th scope="row"><label for="thumb_meta"><?php _e('Post thumbnail meta field name: ',CRP_LOCAL_NAME); ?></label></th>
			<td><input type="textbox" name="thumb_meta" id="thumb_meta" value="<?php echo attribute_escape(stripslashes($crp_settings[thumb_meta])); ?>"> <br /><?php _e('The value of this field should contain the image source and is set in the <em>Add New Post</em> screen',CRP_LOCAL_NAME); ?></td>
			</tr>
			<tr style="vertical-align: top;"><th scope="row"><label for="scan_images"><?php _e('If the postmeta is not set, then should the plugin extract the first image from the post?',CRP_LOCAL_NAME); ?></label></th>
			<td><input type="checkbox" name="scan_images" id="scan_images" <?php if ($crp_settings[scan_images]) echo 'checked="checked"' ?> /> <br /><?php _e('This can slow down the loading of your page if the first image in the related posts is large in file-size',CRP_LOCAL_NAME); ?></td>
			</tr>
			<tr style="vertical-align: top;"><th scope="row"><label for="thumb_default_show"><?php _e('Use default thumbnail? ',CRP_LOCAL_NAME); ?></label></th>
			<td><input type="checkbox" name="thumb_default_show" id="thumb_default_show" <?php if ($crp_settings[thumb_default_show]) echo 'checked="checked"' ?> /> <br /><?php _e('If checked, when no thumbnail is found, show a default one from the URL below. If not checked and no thumbnail is found, no image will be shown.',CRP_LOCAL_NAME); ?></td>
			</tr>
			<tr style="vertical-align: top;"><th scope="row"><label for="thumb_default"><?php _e('Default thumbnail: ',CRP_LOCAL_NAME); ?></label></th>
			<td><input type="textbox" name="thumb_default" id="thumb_default" value="<?php echo attribute_escape(stripslashes($crp_settings[thumb_default])); ?>" style="width:500px"> <br /><?php _e('The plugin will first check if the post contains a thumbnail. If it doesn\'t then it will check the meta field. If this is not available, then it will show the default image as specified above',CRP_LOCAL_NAME); ?></td>
			</tr>
			</table>
		</div>
		<div class="tabbertab">
		<h3>
		  <?php _e('Custom Styles',CRP_LOCAL_NAME); ?>
		</h3>
			<table class="form-table">
			<tr style="vertical-align: top; "><th scope="row" colspan="2"><?php _e('Custom CSS to add to header:',CRP_LOCAL_NAME); ?></th>
			</tr>
			<tr style="vertical-align: top; "><td scope="row" colspan="2"><textarea name="custom_CSS" id="custom_CSS" rows="15" cols="80"><?php echo stripslashes($crp_settings[custom_CSS]); ?></textarea>
			<br /><em><?php _e('Do not include <code>style</code> tags. Check out the <a href="http://wordpress.org/extend/plugins/contextual-related-posts/faq/">FAQ</a> for available CSS classes to style.',CRP_LOCAL_NAME); ?></em></td></tr>
			</table>		
		</div>
		<p>
		  <input type="submit" name="crp_save" id="crp_save" value="<?php _e('Save Options',CRP_LOCAL_NAME); ?>" style="border:#0C0 1px solid" />
		  <input name="crp_default" type="submit" id="crp_default" value="<?php _e('Default Options',CRP_LOCAL_NAME); ?>" style="border:#F00 1px solid" onclick="if (!confirm('<?php _e('Do you want to set options to Default?',CRP_LOCAL_NAME); ?>')) return false;" />
		  <input name="crp_recreate" type="submit" id="crp_recreate" value="<?php _e('Recreate Index',CRP_LOCAL_NAME); ?>" style="border:#00c 1px solid" onclick="if (!confirm('<?php _e('Are you sure you want to recreate the index?',CRP_LOCAL_NAME); ?>')) return false;" />
		</p>
		</fieldset>
	  </form>
	</div>

	  </div>
	  <div style="clear: both;"></div>
	</div>
</div>
<?php

}


function crp_adminmenu() {
	if (function_exists('current_user_can')) {
		// In WordPress 2.x
		if (current_user_can('manage_options')) {
			$crp_is_admin = true;
		}
	} else {
		// In WordPress 1.x
		global $user_ID;
		if (user_can_edit_user($user_ID, 0)) {
			$crp_is_admin = true;
		}
	}

	if ((function_exists('add_options_page'))&&($crp_is_admin)) {
		$plugin_page = add_options_page(__("Contextual Related Posts", CRP_LOCAL_NAME), __("Related Posts", CRP_LOCAL_NAME), 9, 'crp_options', 'crp_options');
		add_action( 'admin_head-'. $plugin_page, 'crp_adminhead' );
	}
	
}
add_action('admin_menu', 'crp_adminmenu');

// Admin notices
function crp_admin_notice() {
	$plugin_settings_page = '<a href="' . admin_url( 'options-general.php?page=crp_options' ) . '">' . __('plugin settings page', ATF_LOCAL_NAME ) . '</a>';

	if ( !current_user_can( 'manage_options' ) ) return;

    echo '<div class="error">
       <p>'.__('Contextual Related Posts plugin has just been installed / upgraded. Please visit the ', ATF_LOCAL_NAME ).$plugin_settings_page.__(' to configure.', ATF_LOCAL_NAME ).'</p>
    </div>';
}
// add_action('admin_notices', 'crp_admin_notice');

function crp_adminhead() {
	global $crp_url;

?>
<link rel="stylesheet" type="text/css" href="<?php echo $crp_url ?>/wick/wick.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $crp_url ?>/admin-styles.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $crp_url ?>/tabber/tabber.css" />
<script type="text/javascript" language="JavaScript">
function checkForm() {
answer = true;
if (siw && siw.selectingSomething)
	answer = false;
return answer;
}//
</script>
<script type="text/javascript" src="<?php echo $crp_url ?>/wick/sample_data.js.php"></script>
<script type="text/javascript" src="<?php echo $crp_url ?>/wick/wick.js"></script>
<script type="text/javascript" src="<?php echo $crp_url ?>/tabber/tabber.js"></script>
<?php }

?>
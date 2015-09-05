<?php
/**
 * Represents the view for the administration dashboard.
 *
 * @package   Contextual_Related_Posts
 * @author    Ajay D'Souza <me@ajaydsouza.com>
 * @license   GPL-2.0+
 * @link      https://webberzone.com
 * @copyright 2009-2015 Ajay D'Souza
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

?>

<div class="wrap">
	<h1>Contextual Related Posts</h1>

	<ul class="subsubsub">
		<?php
			/**
			 * Fires before the navigation bar in the Settings page
			 *
			 * @since 2.0.0
			 */
			do_action( 'crp_admin_nav_bar_before' )
		?>

	  	<li><a href="#genopdiv"><?php _e( 'General options', CRP_LOCAL_NAME ); ?></a> | </li>
		<li><a href="#tuneopdiv"><?php _e( 'List tuning options', CRP_LOCAL_NAME ); ?></a> | </li>
	  	<li><a href="#outputopdiv"><?php _e( 'Output options', CRP_LOCAL_NAME ); ?></a> | </li>
	  	<li><a href="#thumbopdiv"><?php _e( 'Thumbnail options', CRP_LOCAL_NAME ); ?></a> | </li>
	  	<li><a href="#customcssdiv"><?php _e( 'Styles', CRP_LOCAL_NAME ); ?></a> | </li>
	  	<li><a href="#feedopdiv"><?php _e( 'Feed options', CRP_LOCAL_NAME ); ?></a></li>

		<?php
			/**
			 * Fires after the navigation bar in the Settings page
			 *
			 * @since 2.0.0
			 */
			do_action( 'crp_admin_nav_bar_after' )
		?>
	</ul>

	<div id="poststuff">
	<div id="post-body" class="metabox-holder columns-2">
	<div id="post-body-content">
	  <form method="post" id="crp_options" name="crp_options" onsubmit="return checkForm()">

	    <div id="genopdiv" class="postbox"><div class="handlediv" title="Click to toggle"><br /></div>
	      <h3 class='hndle'><span><?php _e( 'General options', CRP_LOCAL_NAME ); ?></span></h3>
	      <div class="inside">

			<table class="form-table">

				<?php
					/**
					 * Fires before General options block.
					 *
					 * @since 2.0.0
					 *
					 * @param	array	$crp_settings	Contextual Related Posts settings array
					 */
					do_action( 'crp_admin_general_options_before', $crp_settings );
				?>

				<tr><th scope="row"><label for="cache"><?php _e( 'Cache output?', CRP_LOCAL_NAME ); ?></label></th>
					<td><input type="checkbox" name="cache" id="cache" <?php if ( $crp_settings['cache'] ) echo 'checked="checked"' ?> />
						<p class="description"><?php _e( 'Enabling this option will cache the related posts output when the post is visited the first time. The cache is cleaned when you save this page.', CRP_LOCAL_NAME ); ?></p>
						<p class="description"><?php _e( 'The CRP cache works independently and in addition to any of your caching plugins like WP Super Cache or W3 Total Cache. It is recommended that you enable this on your blog.', CRP_LOCAL_NAME ); ?></p>
						<p><input type="button" value="<?php _e( 'Clear cache', CRP_LOCAL_NAME ) ?>" onclick="return clearCache();" class="button-secondary" /></p>
					</td>
				</tr>

				<tr><th scope="row"><?php _e( 'Automatically add related posts to:', CRP_LOCAL_NAME ); ?></th>
					<td>
						<label><input type="checkbox" name="add_to_content" id="add_to_content" <?php if ( $crp_settings['add_to_content'] ) echo 'checked="checked"' ?> /> <?php _e( 'Posts', CRP_LOCAL_NAME ); ?></label><br />
						<label><input type="checkbox" name="add_to_page" id="add_to_page" <?php if ( $crp_settings['add_to_page'] ) echo 'checked="checked"' ?> /> <?php _e( 'Pages', CRP_LOCAL_NAME ); ?></label><br />
						<label><input type="checkbox" name="add_to_home" id="add_to_home" <?php if ( $crp_settings['add_to_home'] ) echo 'checked="checked"' ?> /> <?php _e( 'Home page', CRP_LOCAL_NAME ); ?></label></label><br />
						<label><input type="checkbox" name="add_to_feed" id="add_to_feed" <?php if ( $crp_settings['add_to_feed'] ) echo 'checked="checked"' ?> /> <?php _e( 'Feeds', CRP_LOCAL_NAME ); ?></label></label><br />
						<label><input type="checkbox" name="add_to_category_archives" id="add_to_category_archives" <?php if ( $crp_settings['add_to_category_archives'] ) echo 'checked="checked"' ?> /> <?php _e( 'Category archives', CRP_LOCAL_NAME ); ?></label><br />
						<label><input type="checkbox" name="add_to_tag_archives" id="add_to_tag_archives" <?php if ( $crp_settings['add_to_tag_archives'] ) echo 'checked="checked"' ?> /> <?php _e( 'Tag archives', CRP_LOCAL_NAME ); ?></label></label><br />
						<label><input type="checkbox" name="add_to_archives" id="add_to_archives" <?php if ( $crp_settings['add_to_archives'] ) echo 'checked="checked"' ?> /> <?php _e( 'Other archives', CRP_LOCAL_NAME ); ?></label></label>
						<p class="description"><?php _e( "If you choose to disable this, please add <code>&lt;?php if ( function_exists( 'echo_ald_crp' ) ) echo_ald_crp(); ?&gt;</code> to your template file where you want it displayed", CRP_LOCAL_NAME ); ?></p>
					</td>
				</tr>

				<tr><th scope="row"><label for="content_filter_priority"><?php _e( 'Display location priority:', CRP_LOCAL_NAME ); ?></label></th>
					<td>
						<input type="textbox" name="content_filter_priority" id="content_filter_priority" value="<?php echo esc_attr( stripslashes( $crp_settings['content_filter_priority'] ) ); ?>" />
						<p class="description"><?php _e( 'If you select to automatically add the related posts, CRP will hook into the Content Filter at a priority as specified in this option.', CRP_LOCAL_NAME ); ?></p>
						<p class="description"><?php _e( 'A higher number will cause the related posts to be processed later and move their display further down after the post content. Any number below 10 is not recommended.', CRP_LOCAL_NAME ); ?></p>
					</td>
				</tr>

				<tr><th scope="row"><label for="show_metabox"><?php _e( "Show metabox:", CRP_LOCAL_NAME ); ?></label></th>
					<td>
						<input type="checkbox" name="show_metabox" id="show_metabox" <?php if ( $crp_settings['show_metabox'] ) echo 'checked="checked"' ?> />
						<p class="description"><?php _e( 'This will add the Contextual Related Posts metabox on Edit Posts or Add New Posts screens. Also applies to Pages and Custom Post Types.', CRP_LOCAL_NAME ); ?></p>
					</td>
				</tr>

				<tr><th scope="row"><label for="show_metabox_admins"><?php _e( "Limit metabox to Admins only:", CRP_LOCAL_NAME ); ?></label></th>
					<td>
						<input type="checkbox" name="show_metabox_admins" id="show_metabox_admins" <?php if ( $crp_settings['show_metabox_admins'] ) echo 'checked="checked"' ?> />
						<p class="description"><?php _e( 'If this is selected, the metabox will be hidden from anyone who is not an Admin. Otherwise, by default, Contributors and above will be able to see the metabox. This applies only if the above option is selected.', CRP_LOCAL_NAME ); ?></p>
					</td>
				</tr>

				<tr><th scope="row"><label for="show_credit"><?php _e( "Tell the world you're using Contextual Related Posts:", CRP_LOCAL_NAME ); ?></label></th>
					<td>
						<input type="checkbox" name="show_credit" id="show_credit" <?php if ( $crp_settings['show_credit'] ) echo 'checked="checked"' ?> /> <?php _e( ' <em>Optional</em>', CRP_LOCAL_NAME ); ?>
						<p class="description"><?php _e( 'Adds a nofollow link to Contextual Related Posts homepage as the last time in the list.', CRP_LOCAL_NAME ); ?></p>
					</td>
				</tr>

				<?php
					/**
					 * Fires after General options block.
					 *
					 * @since 2.0.0
					 *
					 * @param	array	$crp_settings	Contextual Related Posts settings array
					 */
					do_action( 'crp_admin_general_options_after', $crp_settings );
				?>

			</table>

			<p>
			  <input type="submit" name="crp_save" id="crp_genop_save" value="<?php _e( 'Save Options', CRP_LOCAL_NAME ); ?>" class="button button-primary" />
			</p>

	      </div> <!-- // inside -->
	    </div> <!-- // genopdiv -->

	    <div id="tuneopdiv" class="postbox"><div class="handlediv" title="Click to toggle"><br /></div>
	      <h3 class='hndle'><span><?php _e( 'List tuning options', CRP_LOCAL_NAME ); ?></span></h3>
	      <div class="inside">

			<table class="form-table">

				<?php
					/**
					 * Fires before Tuning options block.
					 *
					 * @since 2.1.0
					 *
					 * @param	array	$crp_settings	Contextual Related Posts settings array
					 */
					do_action( 'crp_admin_tuning_options_before', $crp_settings );
				?>

				<tr><th scope="row"><label for="limit"><?php _e( 'Number of related posts to display: ', CRP_LOCAL_NAME ); ?></label></th>
					<td>
						<input type="textbox" name="limit" id="limit" value="<?php echo esc_attr( stripslashes( $crp_settings['limit'] ) ); ?>">
						<p class="description"><?php _e( 'Maximum number of posts that will be displayed. The actual number may be smaller if less related posts are found.', CRP_LOCAL_NAME ); ?></p>
					</td>
				</tr>

				<tr><th scope="row"><label for="daily_range"><?php _e( 'Related posts should be newer than:', CRP_LOCAL_NAME ); ?></label></th>
					<td>
						<input type="textbox" name="daily_range" id="daily_range" value="<?php echo esc_attr( stripslashes( $crp_settings['daily_range'] ) ); ?>"><?php _e( 'days', CRP_LOCAL_NAME ); ?>
						<p class="description"><?php _e( 'This sets the cutoff period for which posts will be displayed. e.g. setting it to 365 will show related posts from the last year only. Set to 0 to disable limiting posts by date.', CRP_LOCAL_NAME ); ?></p>
					</td>
				</tr>

				<tr><th scope="row"><label for="match_content"><?php _e( 'Find related posts based on content as well as title:', CRP_LOCAL_NAME ); ?></label></th>
					<td><input type="checkbox" name="match_content" id="match_content" <?php if ( $crp_settings['match_content'] ) echo 'checked="checked"' ?> />
						<p class="description"><?php _e( 'If unchecked, only posts titles are used. I recommend using a caching plugin or enabling "Cache output" above if you enable this. Each site is different, so toggle this option to see which setting gives you better quality related posts.', CRP_LOCAL_NAME ); ?></p>
					</td>
				</tr>

				<tr><th scope="row"><label for="match_content_words"><?php _e( 'Limit content to be compared:', CRP_LOCAL_NAME ); ?></label></th>
					<td><input type="textbox" name="match_content_words" id="match_content_words" value="<?php echo esc_attr(stripslashes($crp_settings['match_content_words'])); ?>">
						<p class="description"><?php _e( 'This sets the maximum words of the content that will be matched. Set to 0 for no limit. Only applies if you active the above option.', CRP_LOCAL_NAME ); ?></p>
					</td>
				</tr>

				<tr><th scope="row"><?php _e( 'Post types to include in results:', CRP_LOCAL_NAME ); ?></th>
					<td>
						<?php foreach ( $wp_post_types as $wp_post_type ) { ?>
							<label>
								<input type="checkbox" name="post_types[]" value="<?php echo $wp_post_type; ?>" <?php if ( in_array( $wp_post_type, $posts_types_inc ) ) { echo ' checked="checked" '; } ?> /><?php echo $wp_post_type; ?>
							</label>&nbsp;&nbsp;
						<?php } ?>
						<p class="description"><?php _e( 'These post types will be displayed in the list. Includes custom post types.', CRP_LOCAL_NAME ); ?></p>
					</td>
				</tr>

				<tr><th scope="row"><label for="exclude_post_ids"><?php _e( 'List of post or page IDs to exclude from the results:', CRP_LOCAL_NAME ); ?></label></th>
					<td><input type="textbox" name="exclude_post_ids" id="exclude_post_ids" value="<?php echo esc_attr( stripslashes( $crp_settings['exclude_post_ids'] ) ); ?>" style="width:250px">
						<p class="description"><?php _e( 'Comma separated list of post, page or custom post type IDs. e.g. 188,320,500', CRP_LOCAL_NAME ); ?></p>
					</td>
				</tr>

				<tr><th scope="row"><label for="exclude_cat_slugs"><?php _e( 'Categories to exclude from the results: ', CRP_LOCAL_NAME ); ?></label></th>
					<td>
						<div style="position:relative;text-align:left">
							<table id="MYCUSTOMFLOATER" class="myCustomFloater" style="position:absolute;top:50px;left:0;background-color:#cecece;display:none;visibility:hidden">
							<tr><td>
								<div class="myCustomFloaterContent">
								you should never be seeing this
								</div>
							</td></tr>
							</table>
							<textarea class="wickEnabled:MYCUSTOMFLOATER" cols="50" rows="3" wrap="virtual" name="exclude_cat_slugs"><?php echo ( stripslashes( $crp_settings['exclude_cat_slugs'] ) ); ?></textarea>
						</div>
						<p class="description"><?php _e( 'Comma separated list of category slugs. The field above has an autocomplete so simply start typing in the beginning of your category name and it will prompt you with options.', CRP_LOCAL_NAME ); ?></p>
						<p class="description highlight"><?php _e( "Excluded category IDs are:", CRP_LOCAL_NAME ); echo " " . $crp_settings['exclude_categories']; ?></p>
					</td>
				</tr>

				<?php
					/**
					 * Fires after Tuning options block.
					 *
					 * @since 2.1.0
					 *
					 * @param	array	$crp_settings	Contextual Related Posts settings array
					 */
					do_action( 'crp_admin_tuning_options_after', $crp_settings );
				?>

			</table>

			<p>
			  <input type="submit" name="crp_save" id="crp_tuneop_save" value="<?php _e( 'Save Options', CRP_LOCAL_NAME ); ?>" class="button button-primary" />
			</p>

	      </div> <!-- // inside -->
	    </div> <!-- // tuneopdiv -->

	    <div id="outputopdiv" class="postbox"><div class="handlediv" title="Click to toggle"><br /></div>
	      <h3 class='hndle'><span><?php _e( 'Output options', CRP_LOCAL_NAME ); ?></span></h3>
	      <div class="inside">

			<table class="form-table">

				<?php
					/**
					 * Fires before output options main block.
					 *
					 * @since 2.0.0
					 *
					 * @param	array	$crp_settings	Contextual Related Posts settings array
					 */
					do_action( 'crp_admin_output_options_before', $crp_settings );
				?>

				<tr><th scope="row"><label for="title"><?php _e( 'Title of related posts:', CRP_LOCAL_NAME ); ?></label></th>
					<td>
						<input type="textbox" name="title" id="title" value="<?php echo esc_attr( stripslashes( $crp_settings['title'] ) ); ?>"  style="width:250px" />
						<p class="description"><?php _e( 'This is the main heading of the related posts. You can also display the current post title by using <code>%postname%</code>. e.g. <code>Related Posts to %postname%</code>', CRP_LOCAL_NAME ); ?></p>
					</td>
				</tr>

				<tr><th scope="row"><label for="blank_output"><?php _e( 'When there are no posts, what should be shown?', CRP_LOCAL_NAME ); ?></label></th>
					<td>
						<label>
							<input type="radio" name="blank_output" value="blank" id="blank_output_0" <?php if ( $crp_settings['blank_output'] ) echo 'checked="checked"' ?> />
							<?php _e( 'Blank Output', CRP_LOCAL_NAME ); ?>
						</label>
						<br />
						<label>
							<input type="radio" name="blank_output" value="customs" id="blank_output_1" <?php if ( ! $crp_settings['blank_output'] ) echo 'checked="checked"' ?> />
							<?php _e( 'Display:', CRP_LOCAL_NAME ); ?>
						</label>
						<input type="textbox" name="blank_output_text" id="blank_output_text" value="<?php echo esc_attr( stripslashes( $crp_settings['blank_output_text'] ) ); ?>"  style="width:250px" />
					</td>
				</tr>

				<tr><th scope="row"><label for="show_excerpt"><?php _e( 'Show post excerpt in list?', CRP_LOCAL_NAME ); ?></label></th>
					<td>
						<input type="checkbox" name="show_excerpt" id="show_excerpt" <?php if ( $crp_settings['show_excerpt'] ) echo 'checked="checked"' ?> />
						<p class="description"><?php printf( __( "Displays the excerpt of the post. If you do not provide an explicit excerpt to a post (in the post editor's optional excerpt field), it will display an automatic excerpt which refers to the first %d words of the post's content", CRP_LOCAL_NAME ), $crp_settings['excerpt_length'] ); ?></p>

						<?php if ( 'rounded_thumbs' == $crp_settings['crp_styles'] ) { ?>
							<p style="color: #F00"><?php _e( "Rounded Thumbnails style selected under the Custom Styles. Excerpt display is disabled.", CRP_LOCAL_NAME ); ?></p>
						<?php } ?>
					</td>
				</tr>

				<tr><th scope="row"><label for="excerpt_length"><?php _e( 'Length of excerpt (in words):', CRP_LOCAL_NAME ); ?></label></th>
					<td>
						<input type="textbox" name="excerpt_length" id="excerpt_length" value="<?php echo stripslashes( $crp_settings['excerpt_length'] ); ?>" />
					</td>
				</tr>

				<tr><th scope="row"><label for="show_author"><?php _e( 'Show post author in list?', CRP_LOCAL_NAME ); ?></label></th>
					<td>
						<input type="checkbox" name="show_author" id="show_author" <?php if ( $crp_settings['show_author'] ) echo 'checked="checked"' ?> />
						<p class="description"><?php _e( 'Displays the author name prefixed with "by". e.g. by John Doe', CRP_LOCAL_NAME ); ?></p>

						<?php if ( 'rounded_thumbs' == $crp_settings['crp_styles'] ) { ?>
							<p style="color: #F00"><?php _e( "Rounded Thumbnails style selected under the Custom Styles. Author display is disabled.", CRP_LOCAL_NAME ); ?></p>
						<?php } ?>
					</td>
				</tr>

				<tr><th scope="row"><label for="show_date"><?php _e( 'Show post date in list?', CRP_LOCAL_NAME ); ?></label></th>
					<td>
						<input type="checkbox" name="show_date" id="show_date" <?php if ( $crp_settings['show_date'] ) echo 'checked="checked"' ?> />
						<p class="description"><?php _e( "Displays the date of the post. Uses the same date format set in General Options", CRP_LOCAL_NAME ); ?></p>

						<?php if ( 'rounded_thumbs' == $crp_settings['crp_styles'] ) { ?>
							<p style="color: #F00"><?php _e( "Rounded Thumbnails style selected under the Custom Styles. Date display is disabled.", CRP_LOCAL_NAME ); ?></p>
						<?php } ?>
					</td>
				</tr>

				<tr><th scope="row"><label for="title_length"><?php _e( 'Limit post title length (in characters)', CRP_LOCAL_NAME ); ?></label></th>
					<td>
						<input type="textbox" name="title_length" id="title_length" value="<?php echo stripslashes( $crp_settings['title_length'] ); ?>" />
							<p class="description"><?php _e( "Any title longer than the number of characters set above will be cut and appended with an ellipsis (&hellip;)", CRP_LOCAL_NAME ); ?></p>
					</td>
				</tr>

				<tr><th scope="row"><label for="link_new_window"><?php _e( 'Open links in new window', CRP_LOCAL_NAME ); ?></label></th>
					<td>
						<input type="checkbox" name="link_new_window" id="link_new_window" <?php if ( $crp_settings['link_new_window'] ) echo 'checked="checked"' ?> /
					></td>
				</tr>

				<tr><th scope="row"><label for="link_nofollow"><?php _e( 'Add nofollow attribute to links in the list', CRP_LOCAL_NAME ); ?></label></th>
					<td>
						<input type="checkbox" name="link_nofollow" id="link_nofollow" <?php if ( $crp_settings['link_nofollow'] ) echo 'checked="checked"' ?> />
					</td>
				</tr>

				<?php
					/**
					 * Fires after Output options main block.
					 *
					 * @since 2.0.0
					 *
					 * @param	array	$crp_settings	Contextual Related Posts settings array
					 */
					do_action( 'crp_admin_output_options_after', $crp_settings );
				?>

			</table>
			<hr />
			<table class="form-table">

				<?php
					/**
					 * Fires before Exclusion options block under Output options.
					 *
					 * @since 2.0.0
					 *
					 * @param	array	$crp_settings	Contextual Related Posts settings array
					 */
					do_action( 'crp_admin_exclusion_options_before', $crp_settings );
				?>

				<tr>
					<th scope="row" colspan="2" style="background: #eee; padding-left: 5px;"><?php _e( 'Exclusion settings:', CRP_LOCAL_NAME ); ?></th>
				</tr>
				<tr><th scope="row"><label for="exclude_on_post_ids"><?php _e( 'Exclude display of related posts on these posts / pages', CRP_LOCAL_NAME ); ?></label></th>
					<td>
						<input type="textbox" name="exclude_on_post_ids" id="exclude_on_post_ids" value="<?php echo esc_attr( stripslashes( $crp_settings['exclude_on_post_ids'] ) ); ?>"  style="width:250px">
						<p class="description"><?php _e( 'Comma separated list of post, page or custom post type IDs. e.g. 188,320,500', CRP_LOCAL_NAME ); ?></p>
					</td>
				</tr>
				<tr><th scope="row"><?php _e( 'Exclude display of related posts on these post types.', CRP_LOCAL_NAME ); ?></th>
					<td>
						<?php foreach ( $wp_post_types as $wp_post_type ) { ?>
							<label>
								<input type="checkbox" name="exclude_on_post_types[]" value="<?php echo $wp_post_type; ?>" <?php if ( in_array( $wp_post_type, $posts_types_excl ) ) { echo ' checked="checked" '; } ?> /><?php echo $wp_post_type; ?>
							</label>&nbsp;&nbsp;
						<?php } ?>
						<p class="description"><?php _e( 'The related posts will not display on any of the above selected post types', CRP_LOCAL_NAME ); ?></p>
					</td>
				</tr>

				<?php
					/**
					 * Fires after Exclusion options block under Output options.
					 *
					 * @since 2.0.0
					 *
					 * @param	array	$crp_settings	Contextual Related Posts settings array
					 */
					do_action( 'crp_admin_exclusion_options_after', $crp_settings );
				?>

			</table>
			<hr />
			<table class="form-table">

				<?php
					/**
					 * Fires before Customize options block under Output options.
					 *
					 * @since 2.0.0
					 *
					 * @param	array	$crp_settings	Contextual Related Posts settings array
					 */
					do_action( 'crp_admin_customize_options_before', $crp_settings );
				?>

				<tr><th scope="row" colspan="2" style="background: #eee; padding-left: 5px;"><?php _e( 'Customize the output:', CRP_LOCAL_NAME ); ?></th>
				</tr>
				<tr><th scope="row"><label for="before_list"><?php _e( 'HTML to display before the list of posts: ', CRP_LOCAL_NAME ); ?></label></th>
					<td><input type="textbox" name="before_list" id="before_list" value="<?php echo esc_attr( stripslashes( $crp_settings['before_list'] ) ); ?>" style="width:250px" /></td>
				</tr>
				<tr><th scope="row"><label for="before_list_item"><?php _e( 'HTML to display before each list item: ', CRP_LOCAL_NAME ); ?></label></th>
					<td><input type="textbox" name="before_list_item" id="before_list_item" value="<?php echo esc_attr( stripslashes( $crp_settings['before_list_item'] ) ); ?>" style="width:250px" /></td>
				</tr>
				<tr><th scope="row"><label for="after_list_item"><?php _e( 'HTML to display after each list item: ', CRP_LOCAL_NAME ); ?></label></th>
					<td><input type="textbox" name="after_list_item" id="after_list_item" value="<?php echo esc_attr( stripslashes( $crp_settings['after_list_item'] ) ); ?>" style="width:250px" /></td>
				</tr>
				<tr><th scope="row"><label for="after_list"><?php _e( 'HTML to display after the list of posts: ', CRP_LOCAL_NAME ); ?></label></th>
					<td><input type="textbox" name="after_list" id="after_list" value="<?php echo esc_attr( stripslashes( $crp_settings['after_list'] ) ); ?>" style="width:250px" /></td>
				</tr>

				<?php
					/**
					 * Fires after Customize options block under Output options.
					 *
					 * @since 2.0.0
					 *
					 * @param	array	$crp_settings	Contextual Related Posts settings array
					 */
					do_action( 'crp_admin_customize_options_after', $crp_settings );
				?>

			</table>

			<p>
			  <input type="submit" name="crp_save" id="crp_outputop_save" value="<?php _e( 'Save Options', CRP_LOCAL_NAME ); ?>" class="button button-primary" />
			</p>

	      </div> <!-- // inside -->
	    </div> <!-- // outputopdiv -->

	    <div id="thumbopdiv" class="postbox"><div class="handlediv" title="Click to toggle"><br /></div>
	      <h3 class='hndle'><span><?php _e( 'Thumbnail options', CRP_LOCAL_NAME ); ?></span></h3>
	      <div class="inside">

			<table class="form-table">

				<?php
					/**
					 * Fires before Thumbnail options block under Output options.
					 *
					 * @since 2.0.0
					 *
					 * @param	array	$crp_settings	Contextual Related Posts settings array
					 */
					do_action( 'crp_admin_thumb_options_before', $crp_settings );
				?>

				<tr><th scope="row"><label for="post_thumb_op"><?php _e( 'Location of post thumbnail:', CRP_LOCAL_NAME ); ?></label></th>
					<td>
						<label>
						<input type="radio" name="post_thumb_op" value="inline" id="post_thumb_op_0" <?php if ( 'inline' == $crp_settings['post_thumb_op'] ) echo 'checked="checked"' ?> />
						<?php _e( 'Display thumbnails inline with posts, before title', CRP_LOCAL_NAME ); ?></label>
						<br />
						<label>
						<input type="radio" name="post_thumb_op" value="after" id="post_thumb_op_1" <?php if ( 'after' == $crp_settings['post_thumb_op'] ) echo 'checked="checked"' ?> />
						<?php _e( 'Display thumbnails inline with posts, after title', CRP_LOCAL_NAME ); ?></label>
						<br />
						<label>
						<input type="radio" name="post_thumb_op" value="thumbs_only" id="post_thumb_op_2" <?php if ( 'thumbs_only' == $crp_settings['post_thumb_op'] ) echo 'checked="checked"' ?> />
						<?php _e( 'Display only thumbnails, no text', CRP_LOCAL_NAME ); ?></label>
						<br />
						<label>
						<input type="radio" name="post_thumb_op" value="text_only" id="post_thumb_op_3" <?php if ( 'text_only' == $crp_settings['post_thumb_op'] ) echo 'checked="checked"' ?> />
						<?php _e( 'Do not display thumbnails, only text.', CRP_LOCAL_NAME ); ?></label>

						<?php if ( 'no_style' != $crp_settings['crp_styles'] ) { ?>
							<p style="color: #F00"><?php _e( "This setting cannot be changed because an inbuilt style has been selected under the Styles section. If you would like to change this option, please select <strong>No styles</strong> under the Styles section.", CRP_LOCAL_NAME ); ?></p>
						<?php } ?>
					</td>
				</tr>
				<tr><th scope="row"><?php _e( 'Thumbnail size:', CRP_LOCAL_NAME ); ?></th>
					<td>
						<?php
							$crp_get_all_image_sizes = crp_get_all_image_sizes();
							if ( isset( $crp_get_all_image_sizes['crp_thumbnail'] ) ) {
								unset( $crp_get_all_image_sizes['crp_thumbnail'] );
							}

							foreach( $crp_get_all_image_sizes as $size ) :
						?>
							<label>
								<input type="radio" name="thumb_size" value="<?php echo $size['name'] ?>" id="<?php echo $size['name'] ?>" <?php if ( $crp_settings['thumb_size'] == $size['name'] ) echo 'checked="checked"' ?> />
								<?php echo $size['name']; ?> ( <?php echo $size['width']; ?>x<?php echo $size['height']; ?>
								<?php
									if ( $size['crop'] ) {
										echo "cropped";
									}
								?>
								)
							</label>
							<br />
						<?php endforeach; ?>

							<label>
								<input type="radio" name="thumb_size" value="crp_thumbnail" id="crp_thumbnail" <?php if ( $crp_settings['thumb_size'] == 'crp_thumbnail' ) echo 'checked="checked"' ?> /> <?php _e( 'Custom size', CRP_LOCAL_NAME ); ?>
							</label>
							<p class="description">
								<?php _e( 'You can choose from existing image sizes above or create a custom size.', CRP_LOCAL_NAME ); ?><br /><br />
								<?php _e( 'If you choose an existing size, then the width, height and crop mode settings in the three options below will be automatically updated to reflect the correct dimensions of the setting.', CRP_LOCAL_NAME ); ?><br />
								<?php _e( "If you have chosen Custom size above, then enter the width, height and crop settings below. For best results, use a cropped image with the same width and height. The default setting is 150x150 cropped image.", CRP_LOCAL_NAME ); ?><br /><br />
								<?php _e( "Any changes to the thumbnail settings doesn't automatically resize existing images.", CRP_LOCAL_NAME ); ?>
								<?php printf( __( "I recommend using <a href='%s' class='thickbox'>OTF Regenerate Thumbnails</a> or <a href='%s' class='thickbox'>Regenerate Thumbnails</a> to regenerate all image sizes.", CRP_LOCAL_NAME ), self_admin_url( 'plugin-install.php?tab=plugin-information&amp;plugin=otf-regenerate-thumbnails&amp;TB_iframe=true&amp;width=600&amp;height=550' ), self_admin_url( 'plugin-install.php?tab=plugin-information&amp;plugin=regenerate-thumbnails&amp;TB_iframe=true&amp;width=600&amp;height=550' ) ); ?>
							</p>
					</td>
				</tr>
				<tr><th scope="row"><label for="thumb_width"><?php _e( 'Width of the thumbnail:', CRP_LOCAL_NAME ); ?></label></th>
					<td><input type="textbox" name="thumb_width" id="thumb_width" value="<?php echo esc_attr( stripslashes( $crp_settings['thumb_width'] ) ); ?>" style="width:50px" />px</td>
				</tr>
				<tr><th scope="row"><label for="thumb_height"><?php _e( 'Height of the thumbnail: ', CRP_LOCAL_NAME ); ?></label></th>
					<td>
						<input type="textbox" name="thumb_height" id="thumb_height" value="<?php echo esc_attr( stripslashes( $crp_settings['thumb_height'] ) ); ?>" style="width:50px" />px
					</td>
				</tr>
				<tr><th scope="row"><label for="thumb_crop"><?php _e( 'Crop mode:', CRP_LOCAL_NAME ); ?></label></th>
					<td>
						<input type="checkbox" name="thumb_crop" id="thumb_crop" <?php if ( $crp_settings['thumb_crop'] ) echo 'checked="checked"' ?> />
						<p class="description">
							<?php _e( "By default, thumbnails will be hard cropped. Uncheck this box to proportionately/soft crop the thumbnails.", CRP_LOCAL_NAME ); ?>
							<?php printf( __( "<a href='%s' target='_blank'>Difference between soft and hard crop</a>", CRP_LOCAL_NAME ), 'http://www.davidtan.org/wordpress-hard-crop-vs-soft-crop-difference-comparison-example/' ); ?>
						</p>
					</td>
				</tr>
				<tr><th scope="row"><label for="thumb_html"><?php _e( 'Image size attributes:', CRP_LOCAL_NAME ); ?></label></th>
					<td>
						<label>
							<input type="radio" name="thumb_html" value="css" id="thumb_html_0" <?php if ( 'css' == $crp_settings['thumb_html'] ) echo 'checked="checked"' ?> />
							<?php _e( 'Style attributes are used for width and height.', CRP_LOCAL_NAME ); echo ' <code>style="max-width:' . $crp_settings['thumb_width'] . 'px;max-height:' . $crp_settings['thumb_height'] . 'px;"</code>'; ?>
						</label>
						<br />
						<label>
							<input type="radio" name="thumb_html" value="html" id="thumb_html_1" <?php if ( 'html' == $crp_settings['thumb_html'] ) echo 'checked="checked"' ?> />
							<?php _e( 'HTML width and height attributes are used for width and height.', CRP_LOCAL_NAME ); echo ' <code>width="' . $crp_settings['thumb_width'] . '" height="' . $crp_settings['thumb_height'] . '"</code>'; ?>
						</label>
						<br />
						<label>
							<input type="radio" name="thumb_html" value="none" id="thumb_html_1" <?php if ( 'none' == $crp_settings['thumb_html'] ) echo 'checked="checked"' ?> />
							<?php _e( 'No HTML or Style attributes set for width and height', CRP_LOCAL_NAME ); ?>
						</label>
						<br />
					</td>
				</tr>
				<tr><th scope="row"><label for="thumb_meta"><?php _e( 'Post thumbnail meta field name:', CRP_LOCAL_NAME ); ?></label></th>
					<td><input type="textbox" name="thumb_meta" id="thumb_meta" value="<?php echo esc_attr( stripslashes( $crp_settings['thumb_meta'] ) ); ?>">
						<p class="description"><?php _e( 'The value of this field should contain a direct link to the image. This is set in the meta box in the <em>Add New Post</em> screen.', CRP_LOCAL_NAME ); ?></p>
					</td>
				</tr>
				<tr><th scope="row"><label for="scan_images"><?php _e( 'Extract the first image from the post?', CRP_LOCAL_NAME ); ?></label></th>
					<td><input type="checkbox" name="scan_images" id="scan_images" <?php if ( $crp_settings['scan_images'] ) echo 'checked="checked"' ?> />
						<p class="description"><?php _e( 'This will only happen if there is no post thumbnail set and no image URL is specified in the meta field.', CRP_LOCAL_NAME ); ?></p>
					</td>
				</tr>
				<tr><th scope="row"><label for="thumb_default_show"><?php _e( 'Use default thumbnail?', CRP_LOCAL_NAME ); ?></label></th>
					<td><input type="checkbox" name="thumb_default_show" id="thumb_default_show" <?php if ( $crp_settings['thumb_default_show'] ) echo 'checked="checked"' ?> />
						<p class="description"><?php _e( 'If checked, when no thumbnail is found, show a default one from the URL below. If not checked and no thumbnail is found, no image will be shown.', CRP_LOCAL_NAME ); ?></p>
					</td>
				</tr>
				<tr><th scope="row"><label for="thumb_default"><?php _e( 'Default thumbnail:', CRP_LOCAL_NAME ); ?></label></th>
					<td><input type="textbox" name="thumb_default" id="thumb_default" value="<?php echo esc_attr( stripslashes( $crp_settings['thumb_default'] ) ); ?>" style="width:100%">
					  	<?php if( '' != $crp_settings['thumb_default'] ) echo "<img src='{$crp_settings['thumb_default']}' style='max-width:200px' />"; ?>
						<p class="description"><?php _e( "The plugin will first check if the post contains a thumbnail. If it doesn't then it will check the meta field. If this is not available, then it will show the default image as specified above.", CRP_LOCAL_NAME ); ?></p>
					</td>
				</tr>

				<?php
					/**
					 * Fires after Thumbnail options block under Output options.
					 *
					 * @since 2.0.0
					 *
					 * @param	array	$crp_settings	Contextual Related Posts settings array
					 */
					do_action( 'crp_admin_thumb_options_after', $crp_settings );
				?>

			</table>

			<p>
			  <input type="submit" name="crp_save" id="crp_thumbop_save" value="<?php _e( 'Save Options', CRP_LOCAL_NAME ); ?>" class="button button-primary" />
			</p>

	      </div> <!-- // inside -->
	    </div> <!-- // outputopdiv -->

	    <div id="customcssdiv" class="postbox"><div class="handlediv" title="Click to toggle"><br /></div>
	      <h3 class='hndle'><span><?php _e( 'Styles', CRP_LOCAL_NAME ); ?></span></h3>
	      <div class="inside">

			<table class="form-table">

				<?php
					/**
					 * Fires before Custom styles options block.
					 *
					 * @since 2.0.0
					 *
					 * @param	array	$crp_settings	Contextual Related Posts settings array
					 */
					do_action( 'crp_admin_custom_styles_before', $crp_settings );
				?>

				<tr><th scope="row"><?php _e( 'Style of the related posts:', CRP_LOCAL_NAME ); ?></th>
				  <td>
					<label>
						<input type="radio" name="crp_styles" value="no_style" id="crp_styles_1" <?php if ( 'no_style' == $crp_settings['crp_styles'] ) echo 'checked="checked"' ?> /> <?php _e( 'No styles', CRP_LOCAL_NAME ); ?>
					</label>
					<p class="description"><?php _e( 'Select this option if you plan to add your own styles', CRP_LOCAL_NAME ); ?></p>
					<br />

					<label>
						<input type="radio" name="crp_styles" value="rounded_thumbs" id="crp_styles_0" <?php if ( $crp_settings['include_default_style'] && ( 'rounded_thumbs' == $crp_settings['crp_styles'] ) ) echo 'checked="checked"' ?> /> <?php _e( 'Rounded Thumbnails', CRP_LOCAL_NAME ); ?>
					</label>
					<p class="description"><img src="<?php echo plugins_url( 'admin/images/crp-rounded-thumbs.png', dirname( __FILE__ ) ); ?>" /></p>
					<p class="description"><?php _e( 'Enabling this option will turn on the thumbnails and set their width and height to 150px. It will also turn off the display of the author, excerpt and date if already enabled. Disabling this option will not revert any settings.', CRP_LOCAL_NAME ); ?></p>
					<p class="description"><?php printf( __( 'You can view the default style at <a href="%1$s" target="_blank">%1$s</a>', CRP_LOCAL_NAME ), esc_url( 'https://github.com/WebberZone/contextual-related-posts/blob/master/css/default-style.css' ) ); ?></p>
					<br />

					<label>
						<input type="radio" name="crp_styles" value="text_only" id="crp_styles_1" <?php if ( 'text_only' == $crp_settings['crp_styles'] ) echo 'checked="checked"' ?> /> <?php _e( 'Text only', CRP_LOCAL_NAME ); ?>
					</label>
					<p class="description"><?php _e( 'Enabling this option will disable thumbnails and no longer include the default style sheet included in the plugin.', CRP_LOCAL_NAME ); ?></p>

					<?php
						/**
						 * Fires after style checkboxes which allows an addon to add more styles.
						 *
						 * @since 2.2.0
						 *
						 * @param	array	$crp_settings	Contextual Related Posts settings array
						 */
						do_action( 'crp_admin_crp_styles', $crp_settings );
					?>

				  </td>
				</tr>
				<tr><th scope="row" colspan="2"><?php _e( 'Custom CSS to add to header:', CRP_LOCAL_NAME ); ?></th>
				</tr>
				<tr>
				  <td scope="row" colspan="2"><textarea name="custom_CSS" id="custom_CSS" rows="15" cols="80" style="width:100%"><?php echo stripslashes( $crp_settings['custom_CSS'] ); ?></textarea>
				  <p class="description"><?php _e( 'Do not include <code>style</code> tags. Check out the <a href="http://wordpress.org/extend/plugins/contextual-related-posts/faq/" target="_blank">FAQ</a> for available CSS classes to style.', CRP_LOCAL_NAME ); ?></p>
				</td></tr>

				<?php
					/**
					 * Fires after Custom styles options block.
					 *
					 * @since 2.0.0
					 *
					 * @param	array	$crp_settings	Contextual Related Posts settings array
					 */
					do_action( 'crp_admin_custom_styles_after', $crp_settings );
				?>

			</table>

			<p>
			  <input type="submit" name="crp_save" id="crp_customcss_save" value="<?php _e( 'Save Options', CRP_LOCAL_NAME ); ?>" class="button button-primary" />
			</p>

	      </div> <!-- // inside -->
	    </div> <!-- // customcssdiv -->

	    <div id="feedopdiv" class="postbox"><div class="handlediv" title="Click to toggle"><br /></div>
	      <h3 class='hndle'><span><?php _e( 'Feed options', CRP_LOCAL_NAME ); ?></span></h3>
	      <div class="inside">

			<table class="form-table">

				<?php
					/**
					 * Fires before Feed options block.
					 *
					 * @since 2.0.0
					 *
					 * @param	array	$crp_settings	Contextual Related Posts settings array
					 */
					do_action( 'crp_admin_feed_options_before', $crp_settings );
				?>

				<tr><th scope="row" colspan="2"><?php _e( 'Below options override the related posts settings for your blog feed. These only apply if you have selected to add related posts to Feeds in the General Options tab.', CRP_LOCAL_NAME ); ?></th>
				</tr>
				<tr><th scope="row"><label for="limit_feed"><?php _e( 'Number of related posts to display: ', CRP_LOCAL_NAME ); ?></label></th>
					<td><input type="textbox" name="limit_feed" id="limit_feed" value="<?php echo esc_attr( stripslashes( $crp_settings['limit_feed'] ) ); ?>"></td>
				</tr>
				<tr><th scope="row"><label for="show_excerpt_feed"><?php _e( 'Show post excerpt in list?', CRP_LOCAL_NAME ); ?></label></th>
					<td><input type="checkbox" name="show_excerpt_feed" id="show_excerpt_feed" <?php if ( $crp_settings['show_excerpt_feed'] ) echo 'checked="checked"' ?> /></td>
				</tr>
				<tr><th scope="row"><label for="post_thumb_op_feed"><?php _e( 'Location of post thumbnail:', CRP_LOCAL_NAME ); ?></label></th>
					<td>
						<label>
						<input type="radio" name="post_thumb_op_feed" value="inline" id="post_thumb_op_feed_0" <?php if ( 'inline' == $crp_settings['post_thumb_op_feed'] ) echo 'checked="checked"' ?> />
						<?php _e( 'Display thumbnails inline with posts, before title', CRP_LOCAL_NAME ); ?></label>
						<br />
						<label>
						<input type="radio" name="post_thumb_op_feed" value="after" id="post_thumb_op_feed_1" <?php if ( 'after' == $crp_settings['post_thumb_op_feed'] ) echo 'checked="checked"' ?> />
						<?php _e( 'Display thumbnails inline with posts, after title', CRP_LOCAL_NAME ); ?></label>
						<br />
						<label>
						<input type="radio" name="post_thumb_op_feed" value="thumbs_only" id="post_thumb_op_feed_2" <?php if ( 'thumbs_only' == $crp_settings['post_thumb_op_feed'] ) echo 'checked="checked"' ?> />
						<?php _e( 'Display only thumbnails, no text', CRP_LOCAL_NAME ); ?></label>
						<br />
						<label>
						<input type="radio" name="post_thumb_op_feed" value="text_only" id="post_thumb_op_feed_3" <?php if ( 'text_only' == $crp_settings['post_thumb_op_feed'] ) echo 'checked="checked"' ?> />
						<?php _e( 'Do not display thumbnails, only text.', CRP_LOCAL_NAME ); ?></label>
						<br />
					</td>
				</tr>
				<tr><th scope="row"><label for="thumb_width_feed"><?php _e( 'Maximum width of the thumbnail: ', CRP_LOCAL_NAME ); ?></label></th>
					<td><input type="textbox" name="thumb_width_feed" id="thumb_width_feed" value="<?php echo esc_attr( stripslashes( $crp_settings['thumb_width_feed'] ) ); ?>" style="width:50px" />px</td>
				</tr>
				<tr><th scope="row"><label for="thumb_height_feed"><?php _e( 'Maximum height of the thumbnail: ', CRP_LOCAL_NAME ); ?></label></th>
					<td><input type="textbox" name="thumb_height_feed" id="thumb_height_feed" value="<?php echo esc_attr( stripslashes( $crp_settings['thumb_height_feed'] ) ); ?>" style="width:50px" />px</td>
				</tr>

				<?php
					/**
					 * Fires after Feed options block.
					 *
					 * @since 2.0.0
					 *
					 * @param	array	$crp_settings	Contextual Related Posts settings array
					 */
					do_action( 'crp_admin_feed_options_after', $crp_settings );
				?>

			</table>

			<p>
			  <input type="submit" name="crp_save" id="crp_feedop_save" value="<?php _e( 'Save Options', CRP_LOCAL_NAME ); ?>" class="button button-primary" />
			</p>

	      </div> <!-- // inside -->
	    </div> <!-- // feedopdiv -->

		<?php
			/**
			 * Fires after all the options are displayed. Allows a custom function to add a new option block.
			 *
			 * @since 2.0.0
			 */
			do_action( 'crp_admin_more_options' )
		?>

		<p>
		  <input type="submit" name="crp_save" id="crp_save" value="<?php _e( 'Save Options', CRP_LOCAL_NAME ); ?>" class="button button-primary" />
		  <input name="crp_default" type="submit" id="crp_default" value="<?php _e( 'Default Options', CRP_LOCAL_NAME ); ?>" class="button button-secondary" onclick="if (!confirm('<?php _e( "Do you want to set options to Default?", CRP_LOCAL_NAME ); ?>')) return false;" />
		  <input name="crp_recreate" type="submit" id="crp_recreate" value="<?php _e( 'Recreate Index', CRP_LOCAL_NAME ); ?>" class="button button-secondary" onclick="if (!confirm('<?php _e( "Are you sure you want to recreate the index?", CRP_LOCAL_NAME ); ?>')) return false;" />
		</p>

		<?php if ( ! $wpdb->get_results( "SHOW INDEX FROM {$wpdb->posts} where Key_name = 'crp_related'" ) || ! $wpdb->get_results( "SHOW INDEX FROM {$wpdb->posts} where Key_name = 'crp_related_title'" ) || ! $wpdb->get_results( "SHOW INDEX FROM {$wpdb->posts} where Key_name = 'crp_related_content'" ) ) { ?>
			<div class="notice error">
				<?php _e( 'One or more FULLTEXT indices are missing. Please hit the <a href="#crp_recreate">Recreate Index button</a> at the bottom of the page to fix this.', CRP_LOCAL_NAME ); ?>
			</div>
		<?php } ?>
		<?php wp_nonce_field( 'crp-plugin-settings' ) ?>
	  </form>
	</div><!-- /post-body-content -->
	<div id="postbox-container-1" class="postbox-container">
	  <div id="side-sortables" class="meta-box-sortables ui-sortable">

		  <?php include_once( 'sidebar-view.php' ); ?>

	  </div><!-- /side-sortables -->
	</div><!-- /postbox-container-1 -->
	</div><!-- /post-body -->
	<br class="clear" />
	</div><!-- /poststuff -->
</div><!-- /wrap -->

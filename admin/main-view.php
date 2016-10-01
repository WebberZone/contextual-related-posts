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

	  	<li><a href="#genopdiv"><?php esc_html_e( 'General options', 'contextual-related-posts' ); ?></a> | </li>
		<li><a href="#tuneopdiv"><?php esc_html_e( 'List tuning options', 'contextual-related-posts' ); ?></a> | </li>
	  	<li><a href="#outputopdiv"><?php esc_html_e( 'Output options', 'contextual-related-posts' ); ?></a> | </li>
	  	<li><a href="#thumbopdiv"><?php esc_html_e( 'Thumbnail options', 'contextual-related-posts' ); ?></a> | </li>
	  	<li><a href="#customcssdiv"><?php esc_html_e( 'Styles', 'contextual-related-posts' ); ?></a> | </li>
	  	<li><a href="#feedopdiv"><?php esc_html_e( 'Feed options', 'contextual-related-posts' ); ?></a></li>

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

	    <div id="genopdiv" class="postbox"><div class="handlediv" title="<?php esc_attr_e( 'Click to toggle', 'contextual-related-posts' ); ?>"><br /></div>
	      <h3 class='hndle'><span><?php esc_html_e( 'General options', 'contextual-related-posts' ); ?></span></h3>
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

				<tr><th scope="row"><label for="cache"><?php esc_html_e( 'Cache output?', 'contextual-related-posts' ); ?></label></th>
					<td><input type="checkbox" name="cache" id="cache" <?php checked( true, $crp_settings['cache'] ); ?> />
						<p class="description"><?php esc_html_e( 'Enabling this option will cache the related posts output when the post is visited the first time. The cache is cleaned when you save this page.', 'contextual-related-posts' ); ?></p>
						<p class="description"><?php esc_html_e( 'The CRP cache works independently and in addition to any of your caching plugins like WP Super Cache or W3 Total Cache. It is recommended that you enable this on your blog.', 'contextual-related-posts' ); ?></p>
						<p><input type="button" value="<?php esc_html_e( 'Clear cache', 'contextual-related-posts' ) ?>" onclick="return clearCache();" class="button-secondary" /></p>
					</td>
				</tr>

				<tr><th scope="row"><?php esc_html_e( 'Automatically add related posts to:', 'contextual-related-posts' ); ?></th>
					<td>
						<label><input type="checkbox" name="add_to_content" id="add_to_content" <?php checked( true, $crp_settings['add_to_content'] ); ?> /> <?php esc_html_e( 'Posts', 'contextual-related-posts' ); ?></label><br />
						<label><input type="checkbox" name="add_to_page" id="add_to_page" <?php checked( true, $crp_settings['add_to_page'] ); ?> /> <?php esc_html_e( 'Pages', 'contextual-related-posts' ); ?></label><br />
						<label><input type="checkbox" name="add_to_home" id="add_to_home" <?php checked( true, $crp_settings['add_to_home'] ); ?> /> <?php esc_html_e( 'Home page', 'contextual-related-posts' ); ?></label></label><br />
						<label><input type="checkbox" name="add_to_feed" id="add_to_feed" <?php checked( true, $crp_settings['add_to_feed'] ); ?> /> <?php esc_html_e( 'Feeds', 'contextual-related-posts' ); ?></label></label><br />
						<label><input type="checkbox" name="add_to_category_archives" id="add_to_category_archives" <?php checked( true, $crp_settings['add_to_category_archives'] ); ?> /> <?php esc_html_e( 'Category archives', 'contextual-related-posts' ); ?></label><br />
						<label><input type="checkbox" name="add_to_tag_archives" id="add_to_tag_archives" <?php checked( true, $crp_settings['add_to_tag_archives'] ); ?> /> <?php esc_html_e( 'Tag archives', 'contextual-related-posts' ); ?></label></label><br />
						<label><input type="checkbox" name="add_to_archives" id="add_to_archives" <?php checked( true, $crp_settings['add_to_archives'] ); ?> /> <?php esc_html_e( 'Other archives', 'contextual-related-posts' ); ?></label></label>
						<p class="description"><?php printf( esc_html__( 'If you choose to disable this, please add %1$s to your template file where you want it displayed', 'contextual-related-posts' ), "<code>&lt;?php if ( function_exists( 'echo_ald_crp' ) ) echo_ald_crp(); ?&gt;</code>" ); ?></p>
					</td>
				</tr>

				<tr><th scope="row"><label for="content_filter_priority"><?php esc_html_e( 'Display location priority:', 'contextual-related-posts' ); ?></label></th>
					<td>
						<input type="textbox" name="content_filter_priority" id="content_filter_priority" value="<?php echo esc_attr( $crp_settings['content_filter_priority'] ); ?>" />
						<p class="description"><?php esc_html_e( 'If you select to automatically add the related posts, CRP will hook into the Content Filter at a priority as specified in this option.', 'contextual-related-posts' ); ?></p>
						<p class="description"><?php esc_html_e( 'A higher number will cause the related posts to be processed later and move their display further down after the post content. Any number below 10 is not recommended.', 'contextual-related-posts' ); ?></p>
					</td>
				</tr>

				<tr><th scope="row"><label for="insert_after_paragraph"><?php esc_html_e( 'Insert after paragraph number', 'contextual-related-posts' ); ?>:</label></th>
					<td>
						<input type="textbox" name="insert_after_paragraph" id="insert_after_paragraph" value="<?php echo esc_attr( $crp_settings['insert_after_paragraph'] ); ?>" />
						<p class="description"><?php esc_html_e( 'Enter 0 to display the related posts before the post content, -1 to display this at the end or a number to insert it after that paragraph number. If your post has less paragraphs, related posts will be displayed at the end.', 'contextual-related-posts' ); ?></p>
					</td>
				</tr>

				<tr><th scope="row"><label for="show_metabox"><?php esc_html_e( 'Show metabox:', 'contextual-related-posts' ); ?></label></th>
					<td>
						<input type="checkbox" name="show_metabox" id="show_metabox" <?php checked( true, $crp_settings['show_metabox'] ); ?> />
						<p class="description"><?php esc_html_e( 'This will add the Contextual Related Posts metabox on Edit Posts or Add New Posts screens. Also applies to Pages and Custom Post Types.', 'contextual-related-posts' ); ?></p>
					</td>
				</tr>

				<tr><th scope="row"><label for="show_metabox_admins"><?php esc_html_e( 'Limit metabox to Admins only:', 'contextual-related-posts' ); ?></label></th>
					<td>
						<input type="checkbox" name="show_metabox_admins" id="show_metabox_admins" <?php checked( true, $crp_settings['show_metabox_admins'] ); ?> />
						<p class="description"><?php esc_html_e( 'If this is selected, the metabox will be hidden from anyone who is not an Admin. Otherwise, by default, Contributors and above will be able to see the metabox. This applies only if the above option is selected.', 'contextual-related-posts' ); ?></p>
					</td>
				</tr>

				<tr><th scope="row"><label for="show_credit"><?php esc_html_e( "Tell the world you're using Contextual Related Posts:", 'contextual-related-posts' ); ?></label></th>
					<td>
						<input type="checkbox" name="show_credit" id="show_credit" <?php checked( true, $crp_settings['show_credit'] ); ?> /> <em><?php esc_html_e( 'Optional', 'contextual-related-posts' ); ?></em>
						<p class="description"><?php esc_html_e( 'Adds a nofollow link to Contextual Related Posts homepage as the last time in the list.', 'contextual-related-posts' ); ?></p>
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
			  <input type="submit" name="crp_save" id="crp_genop_save" value="<?php esc_attr_e( 'Save Options', 'contextual-related-posts' ); ?>" class="button button-primary" />
			</p>

	      </div> <!-- // inside -->
	    </div> <!-- // genopdiv -->

	    <div id="tuneopdiv" class="postbox"><div class="handlediv" title="<?php esc_attr_e( 'Click to toggle', 'contextual-related-posts' ); ?>"><br /></div>
	      <h3 class='hndle'><span><?php esc_html_e( 'List tuning options', 'contextual-related-posts' ); ?></span></h3>
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

				<tr><th scope="row"><label for="limit"><?php esc_html_e( 'Number of related posts to display: ', 'contextual-related-posts' ); ?></label></th>
					<td>
						<input type="textbox" name="limit" id="limit" value="<?php echo esc_attr( stripslashes( $crp_settings['limit'] ) ); ?>">
						<p class="description"><?php esc_html_e( 'Maximum number of posts that will be displayed. The actual number may be smaller if less related posts are found.', 'contextual-related-posts' ); ?></p>
					</td>
				</tr>

				<tr><th scope="row"><label for="daily_range"><?php esc_html_e( 'Related posts should be newer than:', 'contextual-related-posts' ); ?></label></th>
					<td>
						<input type="textbox" name="daily_range" id="daily_range" value="<?php echo esc_attr( stripslashes( $crp_settings['daily_range'] ) ); ?>"><?php esc_html_e( 'days', 'contextual-related-posts' ); ?>
						<p class="description"><?php esc_html_e( 'This sets the cutoff period for which posts will be displayed. e.g. setting it to 365 will show related posts from the last year only. Set to 0 to disable limiting posts by date.', 'contextual-related-posts' ); ?></p>
					</td>
				</tr>

				<tr><th scope="row"><label for="match_content"><?php esc_html_e( 'Find related posts based on content as well as title:', 'contextual-related-posts' ); ?></label></th>
					<td><input type="checkbox" name="match_content" id="match_content" <?php checked( $crp_settings['match_content'] ); ?> />
						<p class="description"><?php esc_html_e( 'If unchecked, only posts titles are used. I recommend using a caching plugin or enabling "Cache output" above if you enable this. Each site is different, so toggle this option to see which setting gives you better quality related posts.', 'contextual-related-posts' ); ?></p>
					</td>
				</tr>

				<tr><th scope="row"><label for="match_content_words"><?php esc_html_e( 'Limit content to be compared:', 'contextual-related-posts' ); ?></label></th>
					<td><input type="textbox" name="match_content_words" id="match_content_words" value="<?php echo esc_attr( stripslashes( $crp_settings['match_content_words'] ) ); ?>">
						<p class="description"><?php esc_html_e( 'This sets the maximum words of the content that will be matched. Set to 0 for no limit. Max value: 2,000. Only applies if you activate the above option.', 'contextual-related-posts' ); ?></p>
					</td>
				</tr>

				<tr><th scope="row"><?php esc_html_e( 'Post types to include in results:', 'contextual-related-posts' ); ?></th>
					<td>
						<?php foreach ( $wp_post_types as $wp_post_type ) { ?>
							<label>
								<input type="checkbox" name="post_types[]" value="<?php echo esc_attr( $wp_post_type ); ?>" <?php checked( true, in_array( $wp_post_type, $posts_types_inc, true ) ); ?> /><?php echo esc_attr( $wp_post_type ); ?>
							</label>&nbsp;&nbsp;
						<?php } ?>
						<p class="description"><?php esc_html_e( 'These post types will be displayed in the list. Includes custom post types.', 'contextual-related-posts' ); ?></p>
					</td>
				</tr>

				<tr><th scope="row"><label for="exclude_post_ids"><?php esc_html_e( 'List of post or page IDs to exclude from the results:', 'contextual-related-posts' ); ?></label></th>
					<td><input type="textbox" name="exclude_post_ids" id="exclude_post_ids" value="<?php echo esc_attr( stripslashes( $crp_settings['exclude_post_ids'] ) ); ?>" style="width:250px">
						<p class="description"><?php esc_html_e( 'Comma separated list of post, page or custom post type IDs. e.g. 188,320,500', 'contextual-related-posts' ); ?></p>
					</td>
				</tr>

				<tr><th scope="row"><label for="exclude_cat_slugs"><?php esc_html_e( 'Categories to exclude from the results: ', 'contextual-related-posts' ); ?></label></th>
					<td>
						<label><input type="textbox" name="exclude_cat_slugs" id="exclude_cat_slugs" value="<?php echo esc_attr( $crp_settings['exclude_cat_slugs'] ); ?>" onfocus="setSuggest('exclude_cat_slugs', 'category');" class="widefat"></label>
						<p class="description"><?php esc_html_e( 'Comma separated list of category slugs. The field above has an autocomplete so simply start typing in the beginning of your category name and it will prompt you with options.', 'contextual-related-posts' ); ?></p>
						<p class="description highlight">
							<?php
								esc_html_e( 'Excluded category IDs are:', 'contextual-related-posts' );
								esc_html_e( ' ' . $crp_settings['exclude_categories'] );
							?>
						</p>
						<p class="description">
							<?php
								printf( esc_html__( 'These might differ from the IDs visible in the Categories page which use the %1$s. CRP uses the %2$s which is unique to this taxonomy.', 'contextual-related-posts' ), '<code>term_id</code>', '<code>term_taxonomy_id</code>' );
							?>
						</p>
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
			  <input type="submit" name="crp_save" id="crp_tuneop_save" value="<?php esc_attr_e( 'Save Options', 'contextual-related-posts' ); ?>" class="button button-primary" />
			</p>

	      </div> <!-- // inside -->
	    </div> <!-- // tuneopdiv -->

	    <div id="outputopdiv" class="postbox"><div class="handlediv" title="<?php esc_attr_e( 'Click to toggle', 'contextual-related-posts' ); ?>"><br /></div>
	      <h3 class='hndle'><span><?php esc_html_e( 'Output options', 'contextual-related-posts' ); ?></span></h3>
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

				<tr><th scope="row"><label for="title"><?php esc_html_e( 'Title of related posts:', 'contextual-related-posts' ); ?></label></th>
					<td>
						<input type="textbox" name="title" id="title" value="<?php echo esc_attr( stripslashes( $crp_settings['title'] ) ); ?>"  style="width:250px" />
						<p class="description"><?php printf( esc_html__( 'This is the main heading of the related posts. You can also display the current post title by using %1$s', 'contextual-related-posts' ), '<code>%postname%</code>' ); ?></p>
					</td>
				</tr>

				<tr><th scope="row"><label for="blank_output"><?php esc_html_e( 'When there are no posts, what should be shown?', 'contextual-related-posts' ); ?></label></th>
					<td>
						<label>
							<input type="radio" name="blank_output" value="blank" id="blank_output_0" <?php checked( true, $crp_settings['blank_output'] ); ?> />
							<?php esc_html_e( 'Blank Output', 'contextual-related-posts' ); ?>
						</label>
						<br />
						<label>
							<input type="radio" name="blank_output" value="customs" id="blank_output_1" <?php checked( false, $crp_settings['blank_output'] ); ?> />
							<?php esc_html_e( 'Display:', 'contextual-related-posts' ); ?>
						</label>
						<input type="textbox" name="blank_output_text" id="blank_output_text" value="<?php echo esc_attr( stripslashes( $crp_settings['blank_output_text'] ) ); ?>"  style="width:250px" />
					</td>
				</tr>

				<tr><th scope="row"><label for="show_excerpt"><?php esc_html_e( 'Show post excerpt in list?', 'contextual-related-posts' ); ?></label></th>
					<td>
						<input type="checkbox" name="show_excerpt" id="show_excerpt" <?php checked( true, $crp_settings['show_excerpt'] ); ?> />
						<p class="description"><?php printf( esc_html__( "Displays the excerpt of the post. If you do not provide an explicit excerpt to a post (in the post editor's optional excerpt field), it will display an automatic excerpt which refers to the first %d words of the post's content", 'contextual-related-posts' ), esc_html_e( $crp_settings['excerpt_length'] ) ); ?></p>

						<?php if ( 'rounded_thumbs' === $crp_settings['crp_styles'] ) { ?>
							<p style="color: #F00"><?php esc_html_e( 'Rounded Thumbnails style selected under the Custom Styles. Excerpt display is disabled.', 'contextual-related-posts' ); ?></p>
						<?php } ?>
					</td>
				</tr>

				<tr><th scope="row"><label for="excerpt_length"><?php esc_html_e( 'Length of excerpt (in words):', 'contextual-related-posts' ); ?></label></th>
					<td>
						<input type="textbox" name="excerpt_length" id="excerpt_length" value="<?php echo esc_attr( $crp_settings['excerpt_length'] ); ?>" />
					</td>
				</tr>

				<tr><th scope="row"><label for="show_author"><?php esc_html_e( 'Show post author in list?', 'contextual-related-posts' ); ?></label></th>
					<td>
						<input type="checkbox" name="show_author" id="show_author" <?php checked( true, $crp_settings['show_author'] ); ?> />
						<p class="description"><?php esc_html_e( 'Displays the author name prefixed with "by". e.g. by John Doe', 'contextual-related-posts' ); ?></p>

						<?php if ( 'rounded_thumbs' === $crp_settings['crp_styles'] ) { ?>
							<p style="color: #F00"><?php esc_html_e( 'Rounded Thumbnails style selected under the Custom Styles. Author display is disabled.', 'contextual-related-posts' ); ?></p>
						<?php } ?>
					</td>
				</tr>

				<tr><th scope="row"><label for="show_date"><?php esc_html_e( 'Show post date in list?', 'contextual-related-posts' ); ?></label></th>
					<td>
						<input type="checkbox" name="show_date" id="show_date" <?php checked( true, $crp_settings['show_date'] ); ?> />
						<p class="description"><?php esc_html_e( 'Displays the date of the post. Uses the same date format set in General Options', 'contextual-related-posts' ); ?></p>

						<?php if ( 'rounded_thumbs' === $crp_settings['crp_styles'] ) { ?>
							<p style="color: #F00"><?php esc_html_e( 'Rounded Thumbnails style selected under the Custom Styles. Date display is disabled.', 'contextual-related-posts' ); ?></p>
						<?php } ?>
					</td>
				</tr>

				<tr><th scope="row"><label for="title_length"><?php esc_html_e( 'Limit post title length (in characters)', 'contextual-related-posts' ); ?></label></th>
					<td>
						<input type="textbox" name="title_length" id="title_length" value="<?php echo esc_attr( $crp_settings['title_length'] ); ?>" />
							<p class="description"><?php esc_html_e( 'Any title longer than the number of characters set above will be cut and appended with an ellipsis (&hellip;)', 'contextual-related-posts' ); ?></p>
					</td>
				</tr>

				<tr><th scope="row"><label for="link_new_window"><?php esc_html_e( 'Open links in new window', 'contextual-related-posts' ); ?></label></th>
					<td>
						<input type="checkbox" name="link_new_window" id="link_new_window" <?php checked( true, $crp_settings['link_new_window'] ); ?> /
					></td>
				</tr>

				<tr><th scope="row"><label for="link_nofollow"><?php esc_html_e( 'Add nofollow attribute to links in the list', 'contextual-related-posts' ); ?></label></th>
					<td>
						<input type="checkbox" name="link_nofollow" id="link_nofollow" <?php checked( true, $crp_settings['link_nofollow'] ); ?> />
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
					<th scope="row" colspan="2" style="background: #eee; padding-left: 5px;"><?php esc_html_e( 'Exclusion settings:', 'contextual-related-posts' ); ?></th>
				</tr>
				<tr><th scope="row"><label for="exclude_on_post_ids"><?php esc_html_e( 'Exclude display of related posts on these posts / pages', 'contextual-related-posts' ); ?></label></th>
					<td>
						<input type="textbox" name="exclude_on_post_ids" id="exclude_on_post_ids" value="<?php echo esc_attr( $crp_settings['exclude_on_post_ids'] ); ?>"  style="width:250px">
						<p class="description"><?php esc_html_e( 'Comma separated list of post, page or custom post type IDs. e.g. 188,320,500', 'contextual-related-posts' ); ?></p>
					</td>
				</tr>
				<tr><th scope="row"><?php esc_html_e( 'Exclude display of related posts on these post types.', 'contextual-related-posts' ); ?></th>
					<td>
						<?php foreach ( $wp_post_types as $wp_post_type ) { ?>
							<label>
								<input type="checkbox" name="exclude_on_post_types[]" value="<?php echo esc_attr( $wp_post_type ); ?>" <?php checked( true, in_array( $wp_post_type, $posts_types_excl, true ) ); ?> /><?php echo esc_attr( $wp_post_type ); ?>
							</label>&nbsp;&nbsp;
						<?php } ?>
						<p class="description"><?php esc_html_e( 'The related posts will not display on any of the above selected post types', 'contextual-related-posts' ); ?></p>
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

				<tr><th scope="row" colspan="2" style="background: #eee; padding-left: 5px;"><?php esc_html_e( 'Customize the output:', 'contextual-related-posts' ); ?></th>
				</tr>
				<tr><th scope="row"><label for="before_list"><?php esc_html_e( 'HTML to display before the list of posts: ', 'contextual-related-posts' ); ?></label></th>
					<td><input type="textbox" name="before_list" id="before_list" value="<?php echo esc_attr( $crp_settings['before_list'] ); ?>" style="width:250px" /></td>
				</tr>
				<tr><th scope="row"><label for="before_list_item"><?php esc_html_e( 'HTML to display before each list item: ', 'contextual-related-posts' ); ?></label></th>
					<td><input type="textbox" name="before_list_item" id="before_list_item" value="<?php echo esc_attr( $crp_settings['before_list_item'] ); ?>" style="width:250px" /></td>
				</tr>
				<tr><th scope="row"><label for="after_list_item"><?php esc_html_e( 'HTML to display after each list item: ', 'contextual-related-posts' ); ?></label></th>
					<td><input type="textbox" name="after_list_item" id="after_list_item" value="<?php echo esc_attr( $crp_settings['after_list_item'] ); ?>" style="width:250px" /></td>
				</tr>
				<tr><th scope="row"><label for="after_list"><?php esc_html_e( 'HTML to display after the list of posts: ', 'contextual-related-posts' ); ?></label></th>
					<td><input type="textbox" name="after_list" id="after_list" value="<?php echo esc_attr( $crp_settings['after_list'] ); ?>" style="width:250px" /></td>
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
			  <input type="submit" name="crp_save" id="crp_outputop_save" value="<?php esc_attr_e( 'Save Options', 'contextual-related-posts' ); ?>" class="button button-primary" />
			</p>

	      </div> <!-- // inside -->
	    </div> <!-- // outputopdiv -->

	    <div id="thumbopdiv" class="postbox"><div class="handlediv" title="<?php esc_attr_e( 'Click to toggle', 'contextual-related-posts' ); ?>"><br /></div>
	      <h3 class='hndle'><span><?php esc_html_e( 'Thumbnail options', 'contextual-related-posts' ); ?></span></h3>
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

				<tr><th scope="row"><label for="post_thumb_op"><?php esc_html_e( 'Location of post thumbnail:', 'contextual-related-posts' ); ?></label></th>
					<td>
						<label>
							<input type="radio" name="post_thumb_op" value="inline" id="post_thumb_op_0" <?php checked( 'inline',  $crp_settings['post_thumb_op'], true ); ?> />
							<?php esc_html_e( 'Display thumbnails inline with posts, before title', 'contextual-related-posts' ); ?>
						</label>
						<br />
						<label>
							<input type="radio" name="post_thumb_op" value="after" id="post_thumb_op_1" <?php checked( 'after',  $crp_settings['post_thumb_op'], true ); ?> />
							<?php esc_html_e( 'Display thumbnails inline with posts, after title', 'contextual-related-posts' ); ?>
						</label>
						<br />
						<label>
							<input type="radio" name="post_thumb_op" value="thumbs_only" id="post_thumb_op_2" <?php checked( 'thumbs_only',  $crp_settings['post_thumb_op'], true ); ?> />
							<?php esc_html_e( 'Display only thumbnails, no text', 'contextual-related-posts' ); ?>
						</label>
						<br />
						<label>
							<input type="radio" name="post_thumb_op" value="text_only" id="post_thumb_op_3" <?php checked( 'text_only',  $crp_settings['post_thumb_op'], true ); ?> />
							<?php esc_html_e( 'Do not display thumbnails, only text.', 'contextual-related-posts' ); ?>
						</label>

						<?php if ( 'no_style' !== $crp_settings['crp_styles'] ) { ?>
							<p style="color: #F00"><?php printf( esc_html__( 'This setting cannot be changed because an inbuilt style has been selected under the Styles section. If you would like to change this option, please select %1$s under the Styles section.', 'contextual-related-posts' ), '<strong>' . esc_html__( 'No styles', 'contextual-related-posts' ) . '</strong>' ); ?></p>
						<?php } ?>
					</td>
				</tr>
				<tr><th scope="row"><?php esc_html_e( 'Thumbnail size:', 'contextual-related-posts' ); ?></th>
					<td>
						<?php
						$crp_get_all_image_sizes = crp_get_all_image_sizes();

						if ( isset( $crp_get_all_image_sizes['crp_thumbnail'] ) ) {
							unset( $crp_get_all_image_sizes['crp_thumbnail'] );
						}

						foreach ( $crp_get_all_image_sizes as $size ) :
						?>
						<label>
							<input type="radio" name="thumb_size" value="<?php esc_attr_e( $size['name'] ) ?>" id="<?php esc_attr_e( $size['name'] ) ?>" <?php checked( $crp_settings['thumb_size'], $size['name'] ); ?> />
							<?php esc_html_e( $size['name'] ); ?> ( <?php esc_html_e( $size['width'] ); ?>x<?php esc_html_e( $size['height'] ); ?>
							<?php
							if ( $size['crop'] ) {
								echo 'cropped';
							}
								?>
								)
							</label>
							<br />
						<?php endforeach; ?>

							<label>
								<input type="radio" name="thumb_size" value="crp_thumbnail" id="crp_thumbnail" <?php checked( $crp_settings['thumb_size'], 'crp_thumbnail' ); ?> /> <?php esc_html_e( 'Custom size', 'contextual-related-posts' ); ?>
							</label>
							<p class="description">
								<?php esc_html_e( 'You can choose from existing image sizes above or create a custom size.', 'contextual-related-posts' ); ?><br /><br />
								<?php esc_html_e( 'If you choose an existing size, then the width, height and crop mode settings in the three options below will be automatically updated to reflect the correct dimensions of the setting.', 'contextual-related-posts' ); ?><br />
								<?php esc_html_e( 'If you have chosen Custom size above, then enter the width, height and crop settings below. For best results, use a cropped image with the same width and height. The default setting is 150x150 cropped image.', 'contextual-related-posts' ); ?><br /><br />
								<?php esc_html_e( "Any changes to the thumbnail settings doesn't automatically resize existing images.", 'contextual-related-posts' ); ?>
									<?php printf(
										esc_html__( 'I recommend using %1$s or %2$s to regenerate all image sizes.', 'contextual-related-posts' ),
										'<a href="' . esc_url( network_admin_url( 'plugin-install.php?tab=plugin-information&amp;plugin=otf-regenerate-thumbnails&amp;TB_iframe=true&amp;width=600&amp;height=550' ) ) . '" class="thickbox">OTF Regenerate Thumbnails</a>',
										'<a href="' . esc_url( network_admin_url( 'plugin-install.php?tab=plugin-information&amp;plugin=regenerate-thumbnails&amp;TB_iframe=true&amp;width=600&amp;height=550' ) ) . '" class="thickbox">Regenerate Thumbnails</a>'
									); ?>
							</p>
					</td>
				</tr>
				<tr><th scope="row"><label for="thumb_width"><?php esc_html_e( 'Width of the thumbnail:', 'contextual-related-posts' ); ?></label></th>
					<td><input type="textbox" name="thumb_width" id="thumb_width" value="<?php echo esc_attr( $crp_settings['thumb_width'] ); ?>" style="width:50px" />px</td>
				</tr>
				<tr><th scope="row"><label for="thumb_height"><?php esc_html_e( 'Height of the thumbnail: ', 'contextual-related-posts' ); ?></label></th>
					<td>
						<input type="textbox" name="thumb_height" id="thumb_height" value="<?php echo esc_attr( $crp_settings['thumb_height'] ); ?>" style="width:50px" />px
					</td>
				</tr>
				<tr><th scope="row"><label for="thumb_crop"><?php esc_html_e( 'Crop mode:', 'contextual-related-posts' ); ?></label></th>
					<td>
						<input type="checkbox" name="thumb_crop" id="thumb_crop" <?php checked( true, $crp_settings['thumb_crop'] ); ?> />
						<p class="description">
							<?php esc_html_e( 'By default, thumbnails will be hard cropped. Uncheck this box to proportionately/soft crop the thumbnails.', 'contextual-related-posts' ); ?>
						</p>
					</td>
				</tr>
				<tr><th scope="row"><label for="thumb_html"><?php esc_html_e( 'Image size attributes:', 'contextual-related-posts' ); ?></label></th>
					<td>
						<label>
							<input type="radio" name="thumb_html" value="css" id="thumb_html_0" <?php checked( 'css', $crp_settings['thumb_html'], true ); ?> />
							<?php esc_html_e( 'Style attributes. e.g.', 'contextual-related-posts' );
							echo ' <code>style="max-width:' . esc_attr( $crp_settings['thumb_width'] ) . 'px;max-height:' . esc_attr( $crp_settings['thumb_height'] ) . 'px;"</code>'; ?>
						</label>
						<br />
						<label>
							<input type="radio" name="thumb_html" value="html" id="thumb_html_1" <?php checked( 'html', $crp_settings['thumb_html'], true ); ?> />
							<?php esc_html_e( 'HTML width and height attributes. e.g.', 'contextual-related-posts' );
							echo ' <code>width="' . esc_attr( $crp_settings['thumb_width'] ) . '" height="' . esc_attr( $crp_settings['thumb_height'] ) . '"</code>' ?>
						</label>
						<br />
						<label>
							<input type="radio" name="thumb_html" value="none" id="thumb_html_1" <?php checked( 'none', $crp_settings['thumb_html'], true ); ?> />
							<?php esc_html_e( 'No HTML or Style attributes', 'contextual-related-posts' ); ?>
						</label>
						<br />
					</td>
				</tr>
				<tr><th scope="row"><label for="thumb_meta"><?php esc_html_e( 'Post thumbnail meta field name:', 'contextual-related-posts' ); ?></label></th>
					<td><input type="textbox" name="thumb_meta" id="thumb_meta" value="<?php echo esc_attr( $crp_settings['thumb_meta'] ); ?>">
						<p class="description"><?php printf( esc_html__( 'The value of this field should contain a direct link to the image. This is set in the meta box in the %1$s screen.', 'contextual-related-posts' ), '<strong>' . esc_html__( 'Add New Post', 'contextual-related-posts' ) . '</strong>' ); ?></p>
					</td>
				</tr>
				<tr><th scope="row"><label for="scan_images"><?php esc_html_e( 'Extract the first image from the post?', 'contextual-related-posts' ); ?></label></th>
					<td><input type="checkbox" name="scan_images" id="scan_images" <?php checked( true, $crp_settings['scan_images'] ); ?> />
						<p class="description"><?php esc_html_e( 'This will only happen if there is no post thumbnail set and no image URL is specified in the meta field.', 'contextual-related-posts' ); ?></p>
					</td>
				</tr>
				<tr><th scope="row"><label for="thumb_default_show"><?php esc_html_e( 'Use default thumbnail?', 'contextual-related-posts' ); ?></label></th>
					<td><input type="checkbox" name="thumb_default_show" id="thumb_default_show" <?php checked( true, $crp_settings['thumb_default_show'] ); ?> />
						<p class="description"><?php esc_html_e( 'If checked, when no thumbnail is found, show a default one from the URL below. If not checked and no thumbnail is found, no image will be shown.', 'contextual-related-posts' ); ?></p>
					</td>
				</tr>
				<tr><th scope="row"><label for="thumb_default"><?php esc_html_e( 'Default thumbnail:', 'contextual-related-posts' ); ?></label></th>
					<td><input type="textbox" name="thumb_default" id="thumb_default" value="<?php echo esc_attr( $crp_settings['thumb_default'] ); ?>" style="width:100%">
					  	<?php
						if ( ! empty( $crp_settings['thumb_default'] ) ) {
							printf( '<img src="%1$s" style="max-width:200px" />', esc_url( $crp_settings['thumb_default'] ) );
						}
						?>
						<p class="description"><?php esc_html_e( "The plugin will first check if the post contains a thumbnail. If it doesn't then it will check the meta field. If this is not available, then it will show the default image as specified above.", 'contextual-related-posts' ); ?></p>
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
			  <input type="submit" name="crp_save" id="crp_thumbop_save" value="<?php esc_attr_e( 'Save Options', 'contextual-related-posts' ); ?>" class="button button-primary" />
			</p>

	      </div> <!-- // inside -->
	    </div> <!-- // outputopdiv -->

	    <div id="customcssdiv" class="postbox"><div class="handlediv" title="<?php esc_attr_e( 'Click to toggle', 'contextual-related-posts' ); ?>"><br /></div>
	      <h3 class='hndle'><span><?php esc_html_e( 'Styles', 'contextual-related-posts' ); ?></span></h3>
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

				<tr><th scope="row"><?php esc_html_e( 'Style of the related posts:', 'contextual-related-posts' ); ?></th>
				  <td>
					<label>
						<input type="radio" name="crp_styles" value="no_style" id="crp_styles_1" <?php checked( 'no_style', $crp_settings['crp_styles'] ); ?> /> <?php esc_html_e( 'No styles', 'contextual-related-posts' ); ?>
					</label>
					<p class="description"><?php esc_html_e( 'Select this option if you plan to add your own styles', 'contextual-related-posts' ); ?></p>
					<br />

					<label>
						<input type="radio" name="crp_styles" value="rounded_thumbs" id="crp_styles_0" <?php if ( $crp_settings['include_default_style'] && ( 'rounded_thumbs' === $crp_settings['crp_styles'] ) ) { echo 'checked="checked"'; } ?> /> <?php esc_html_e( 'Rounded Thumbnails', 'contextual-related-posts' ); ?>
					</label>
					<p class="description"><img src="<?php echo esc_url( plugins_url( 'admin/images/crp-rounded-thumbs.png', CRP_PLUGIN_FILE ) ); ?>" /></p>
					<p class="description"><?php esc_html_e( 'Enabling this option will turn on the thumbnails and set their width and height to 150px. It will also turn off the display of the author, excerpt and date if already enabled. Disabling this option will not revert any settings.', 'contextual-related-posts' ); ?></p>
					<p class="description"><?php printf(
						esc_html__( 'You can view the default style at %s', 'contextual-related-posts' ),
						'<a href="' . esc_url( 'https://github.com/WebberZone/contextual-related-posts/blob/master/css/default-style.css' ) . '" target="_blank">' . esc_url( 'https://github.com/WebberZone/contextual-related-posts/blob/master/css/default-style.css' ) . '</a>'
					); ?></p>
					<br />

					<label>
						<input type="radio" name="crp_styles" value="text_only" id="crp_styles_1" <?php checked( 'text_only', $crp_settings['crp_styles'] ); ?> /> <?php esc_html_e( 'Text only', 'contextual-related-posts' ); ?>
					</label>
					<p class="description"><?php esc_html_e( 'Enabling this option will disable thumbnails and no longer include the default style sheet included in the plugin.', 'contextual-related-posts' ); ?></p>

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
				<tr><th scope="row" colspan="2"><?php esc_html_e( 'Custom CSS to add to header:', 'contextual-related-posts' ); ?></th>
				</tr>
				<tr>
				  <td scope="row" colspan="2">
					<textarea name="custom_CSS" id="custom_CSS" rows="15" cols="80" style="width:100%"><?php esc_html_e( $crp_settings['custom_CSS'] ); ?></textarea>
					<p class="description"><?php printf( esc_html__( 'Do not include %1$s tags. Check out the %2$s for available CSS classes to style.', 'contextual-related-posts' ), '<code>style</code>', '<a href="http://wordpress.org/extend/plugins/contextual-related-posts/faq/" target="_blank">FAQ</a>' ); ?></p>
				  </td>
				</tr>

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
			  <input type="submit" name="crp_save" id="crp_customcss_save" value="<?php esc_attr_e( 'Save Options', 'contextual-related-posts' ); ?>" class="button button-primary" />
			</p>

	      </div> <!-- // inside -->
	    </div> <!-- // customcssdiv -->

	    <div id="feedopdiv" class="postbox"><div class="handlediv" title="<?php esc_attr_e( 'Click to toggle', 'contextual-related-posts' ); ?>"><br /></div>
	      <h3 class='hndle'><span><?php esc_html_e( 'Feed options', 'contextual-related-posts' ); ?></span></h3>
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

				<tr><th scope="row" colspan="2"><?php esc_html_e( 'Below options override the related posts settings for your blog feed. These only apply if you have selected to add related posts to Feeds in the General Options tab.', 'contextual-related-posts' ); ?></th>
				</tr>
				<tr><th scope="row"><label for="limit_feed"><?php esc_html_e( 'Number of related posts to display: ', 'contextual-related-posts' ); ?></label></th>
					<td><input type="textbox" name="limit_feed" id="limit_feed" value="<?php echo esc_attr( $crp_settings['limit_feed'] ); ?>"></td>
				</tr>
				<tr><th scope="row"><label for="show_excerpt_feed"><?php esc_html_e( 'Show post excerpt in list?', 'contextual-related-posts' ); ?></label></th>
					<td><input type="checkbox" name="show_excerpt_feed" id="show_excerpt_feed" <?php checked( true, $crp_settings['show_excerpt_feed'] ); ?> /></td>
				</tr>
				<tr><th scope="row"><label for="post_thumb_op_feed"><?php esc_html_e( 'Location of post thumbnail:', 'contextual-related-posts' ); ?></label></th>
					<td>
						<label>
						<input type="radio" name="post_thumb_op_feed" value="inline" id="post_thumb_op_feed_0" <?php checked( 'inline', $crp_settings['post_thumb_op_feed'] ); ?> />
						<?php esc_html_e( 'Display thumbnails inline with posts, before title', 'contextual-related-posts' ); ?></label>
						<br />
						<label>
						<input type="radio" name="post_thumb_op_feed" value="after" id="post_thumb_op_feed_1" <?php checked( 'after', $crp_settings['post_thumb_op_feed'] ); ?> />
						<?php esc_html_e( 'Display thumbnails inline with posts, after title', 'contextual-related-posts' ); ?></label>
						<br />
						<label>
						<input type="radio" name="post_thumb_op_feed" value="thumbs_only" id="post_thumb_op_feed_2" <?php checked( 'thumbs_only', $crp_settings['post_thumb_op_feed'] ); ?> />
						<?php esc_html_e( 'Display only thumbnails, no text', 'contextual-related-posts' ); ?></label>
						<br />
						<label>
						<input type="radio" name="post_thumb_op_feed" value="text_only" id="post_thumb_op_feed_3" <?php checked( 'text_only', $crp_settings['post_thumb_op_feed'] ); ?> />
						<?php esc_html_e( 'Do not display thumbnails, only text.', 'contextual-related-posts' ); ?></label>
						<br />
					</td>
				</tr>
				<tr><th scope="row"><label for="thumb_width_feed"><?php esc_html_e( 'Maximum width of the thumbnail: ', 'contextual-related-posts' ); ?></label></th>
					<td><input type="textbox" name="thumb_width_feed" id="thumb_width_feed" value="<?php echo esc_attr( $crp_settings['thumb_width_feed'] ); ?>" style="width:50px" />px</td>
				</tr>
				<tr><th scope="row"><label for="thumb_height_feed"><?php esc_html_e( 'Maximum height of the thumbnail: ', 'contextual-related-posts' ); ?></label></th>
					<td><input type="textbox" name="thumb_height_feed" id="thumb_height_feed" value="<?php echo esc_attr( $crp_settings['thumb_height_feed'] ); ?>" style="width:50px" />px</td>
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
			  <input type="submit" name="crp_save" id="crp_feedop_save" value="<?php esc_attr_e( 'Save Options', 'contextual-related-posts' ); ?>" class="button button-primary" />
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
		  <input type="submit" name="crp_save" id="crp_save" value="<?php esc_attr_e( 'Save Options', 'contextual-related-posts' ); ?>" class="button button-primary" />
		  <input name="crp_default" type="submit" id="crp_default" value="<?php esc_attr_e( 'Default Options', 'contextual-related-posts' ); ?>" class="button button-secondary" onclick="if (!confirm('<?php esc_attr_e( 'Do you want to set options to Default?', 'contextual-related-posts' ); ?>')) return false;" />
		  <input name="crp_recreate" type="submit" id="crp_recreate" value="<?php esc_attr_e( 'Recreate Index', 'contextual-related-posts' ); ?>" class="button button-secondary" onclick="if (!confirm('<?php esc_attr_e( 'Are you sure you want to recreate the index?', 'contextual-related-posts' ); ?>')) return false;" />
		</p>

		<?php if ( ! $wpdb->get_results( "SHOW INDEX FROM {$wpdb->posts} where Key_name = 'crp_related'" ) || ! $wpdb->get_results( "SHOW INDEX FROM {$wpdb->posts} where Key_name = 'crp_related_title'" ) || ! $wpdb->get_results( "SHOW INDEX FROM {$wpdb->posts} where Key_name = 'crp_related_content'" ) ) { ?>
			<div class="notice error">
				<?php printf( esc_html__( 'One or more FULLTEXT indices are missing. Please hit the %1$s at the bottom of the page to fix this.', 'contextual-related-posts' ), '<a href="#crp_recreate">' . esc_html__( 'Recreate Index button', 'contextual-related-posts' ) . '</a>' ); ?>
			</div>
		<?php } ?>

		<div class="inside">
			<p><?php esc_html_e( 'If the Recreate Index button fails, please run the following queries in phpMyAdmin or Adminer', 'contextual-related-posts' ); ?></p>
			<p>
				<code>ALTER TABLE <?php esc_attr_e( $wpdb->posts ); ?> DROP INDEX crp_related;</code><br />
				<code>ALTER TABLE <?php esc_attr_e( $wpdb->posts ); ?> DROP INDEX crp_related_title;</code><br />
				<code>ALTER TABLE <?php esc_attr_e( $wpdb->posts ); ?> DROP INDEX crp_related_content;</code><br />
				<code>ALTER TABLE <?php esc_attr_e( $wpdb->posts ); ?> ADD FULLTEXT crp_related (post_title, post_content);</code><br />
				<code>ALTER TABLE <?php esc_attr_e( $wpdb->posts ); ?> ADD FULLTEXT crp_related_title (post_title);</code><br />
				<code>ALTER TABLE <?php esc_attr_e( $wpdb->posts ); ?> ADD FULLTEXT crp_related_content (post_content);</code><br />
			</p>
		</div>

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

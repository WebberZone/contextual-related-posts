<?php
/**
 * Represents the view for the administration dashboard.
 *
 * @package   Contextual_Related_Posts
 * @author    Ajay D'Souza <me@ajaydsouza.com>
 * @license   GPL-2.0+
 * @link      http://ajaydsouza.com
 * @copyright 2009-2014 Ajay D'Souza
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

?>

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

	<tr><th scope="row"><label for="title"><?php _e( 'Title of related posts: ', CRP_LOCAL_NAME ); ?></label></th>
		<td>
			<input type="textbox" name="title" id="title" value="<?php echo esc_attr( stripslashes( $crp_settings['title'] ) ); ?>"  style="width:250px" />
			<p class="description"><?php _e( 'This is the main heading of the related posts. You can also display the current post title by using <code>%postname%</code>. e.g. <code>Related Posts to %postname%</code>', CRP_LOCAL_NAME ); ?></p>
		</td>
	</tr>

	<tr><th scope="row"><label for="blank_output"><?php _e( 'When there are no posts, what should be shown?', CRP_LOCAL_NAME ); ?></label></th>
		<td>
			<label>
			<input type="radio" name="blank_output" value="blank" id="blank_output_0" <?php if ( $crp_settings['blank_output'] ) echo 'checked="checked"' ?> />
			<?php _e( 'Blank Output', CRP_LOCAL_NAME ); ?></label>
			<br />
			<label>
			<input type="radio" name="blank_output" value="customs" id="blank_output_1" <?php if ( ! $crp_settings['blank_output'] ) echo 'checked="checked"' ?> />
			<?php _e( 'Display:', CRP_LOCAL_NAME ); ?></label>
			<input type="textbox" name="blank_output_text" id="blank_output_text" value="<?php echo esc_attr( stripslashes( $crp_settings['blank_output_text'] ) ); ?>"  style="width:250px" />
		</td>
	</tr>

	<tr><th scope="row"><label for="show_excerpt"><?php _e( 'Show post excerpt in list?', CRP_LOCAL_NAME ); ?></label></th>
		<td>
			<input type="checkbox" name="show_excerpt" id="show_excerpt" <?php if ( $crp_settings['show_excerpt'] ) echo 'checked="checked"' ?> />
			<p class="description"><?php printf( __( "Displays the excerpt of the post. If you do not provide an explicit excerpt to a post (in the post editor's optional excerpt field), it will display an automatic excerpt which refers to the first %d words of the post's content", CRP_LOCAL_NAME ), $crp_settings['excerpt_length'] ); ?></p>
		</td>
	</tr>

	<tr><th scope="row"><label for="excerpt_length"><?php _e( 'Length of excerpt (in words): ', CRP_LOCAL_NAME ); ?></label></th>
		<td>
			<input type="textbox" name="excerpt_length" id="excerpt_length" value="<?php echo stripslashes( $crp_settings['excerpt_length'] ); ?>" />
		</td>
	</tr>

	<tr><th scope="row"><label for="show_author"><?php _e( 'Show post author in list?', CRP_LOCAL_NAME ); ?></label></th>
		<td>
			<input type="checkbox" name="show_author" id="show_author" <?php if ( $crp_settings['show_author'] ) echo 'checked="checked"' ?> />
			<p class="description"><?php _e( 'Displays the author name prefixed with "by". e.g. by John Doe', CRP_LOCAL_NAME ); ?></p>
		</td>
	</tr>

	<tr><th scope="row"><label for="show_date"><?php _e( 'Show post date in list?', CRP_LOCAL_NAME ); ?></label></th>
		<td>
			<input type="checkbox" name="show_date" id="show_date" <?php if ( $crp_settings['show_date'] ) echo 'checked="checked"' ?> />
			<p class="description"><?php _e( "Displays the date of the post. Uses the same date format set in General Options", CRP_LOCAL_NAME ); ?></p>
		</td>
	</tr>

	<tr><th scope="row"><label for="title_length"><?php _e( 'Limit post title length (in characters)', CRP_LOCAL_NAME ); ?></label></th>
		<td>
			<input type="textbox" name="title_length" id="title_length" value="<?php echo stripslashes( $crp_settings['title_length'] ); ?>" />
				<p class="description"><?php _e( "Any title longer than the number of characters set above will be cut and appended with a &helip;", CRP_LOCAL_NAME ); ?></p>
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
	<tr><th scope="row"><label for="exclude_post_ids"><?php _e( 'List of post or page IDs to exclude from the results:', CRP_LOCAL_NAME ); ?></label></th>
		<td><input type="textbox" name="exclude_post_ids" id="exclude_post_ids" value="<?php echo esc_attr( stripslashes( $crp_settings['exclude_post_ids'] ) ); ?>" style="width:250px">
			<p class="description"><?php _e( 'Comma separated list of post, page or custom post type IDs. e.g. 188,320,500', CRP_LOCAL_NAME ); ?></p>
		</td>
	</tr>
	<tr><th scope="row"><label for="exclude_on_post_ids"><?php _e( 'Exclude display of related posts on these posts / pages', CRP_LOCAL_NAME ); ?></label></th>
		<td>
			<input type="textbox" name="exclude_on_post_ids" id="exclude_on_post_ids" value="<?php echo esc_attr( stripslashes( $crp_settings['exclude_on_post_ids'] ) ); ?>"  style="width:250px">
			<p class="description"><?php _e( 'Comma separated list of post, page or custom post type IDs. e.g. 188,320,500', CRP_LOCAL_NAME ); ?></p>
		</td>
	</tr>
	<tr><th scope="row"><label for="exclude_cat_slugs"><?php _e( 'Categories to exclude from the results: ', CRP_LOCAL_NAME ); ?></label></th>
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
				<textarea class="wickEnabled:MYCUSTOMFLOATER" cols="50" rows="3" wrap="virtual" name="exclude_cat_slugs"><?php echo ( stripslashes( $crp_settings['exclude_cat_slugs'] ) ); ?></textarea>
			</div>
			<p class="description"><?php _e( 'Comma separated list of category slugs. The field above has an autocomplete so simply start typing in the beginning of your category name and it will prompt you with options.', CRP_LOCAL_NAME ); ?></p>
		</td>
	</tr>
	<tr><th scope="row"><?php _e( 'Exclude display of related posts on these post types.', CRP_LOCAL_NAME ); ?></th>
		<td>
			<?php foreach ( $wp_post_types as $wp_post_type ) {
				$post_type_op = '<label><input type="checkbox" name="exclude_on_post_types[]" value="' . $wp_post_type . '" ';
				if ( in_array( $wp_post_type, $posts_types_excl ) ) $post_type_op .= ' checked="checked" ';
				$post_type_op .= ' />' . $wp_post_type . '</label>&nbsp;&nbsp;';
				echo $post_type_op;
			}
			?>
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
<hr />
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

	<tr><th scope="row" colspan="2" style="background: #eee; padding-left: 5px;"><?php _e( 'Post thumbnail options:', CRP_LOCAL_NAME ); ?></th>
	</tr>
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
			<br />
		</td>
	</tr>
	<tr><td scope="row" colspan="2">
			<p class="description">
				<?php _e( "Contextual Related Posts adds a new image size with the below dimensions.", CRP_LOCAL_NAME ); ?>
				<?php _e( "If you change the width and/or height below, existing images will not be automatically resized.", CRP_LOCAL_NAME ); ?>
				<?php printf( __( "I recommend using <a href='%s' target='_blank'>Force Regenerate Thumbnails</a> to regenerate all image sizes.", CRP_LOCAL_NAME ), 'https://wordpress.org/plugins/force-regenerate-thumbnails/' ); ?>
			</p>
		</th>
	</tr>
	<tr><th scope="row"><label for="thumb_width"><?php _e( 'Maximum width of the thumbnail:', CRP_LOCAL_NAME ); ?></label></th>
		<td><input type="textbox" name="thumb_width" id="thumb_width" value="<?php echo esc_attr( stripslashes( $crp_settings['thumb_width'] ) ); ?>" style="width:50px" />px</td>
	</tr>
	<tr><th scope="row"><label for="thumb_height"><?php _e( 'Maximum height of the thumbnail: ', CRP_LOCAL_NAME ); ?></label></th>
		<td>
			<input type="textbox" name="thumb_height" id="thumb_height" value="<?php echo esc_attr( stripslashes( $crp_settings['thumb_height'] ) ); ?>" style="width:50px" />px
			<?php if ( $crp_settings['include_default_style'] ) { ?>
				<p class="description"><?php _e( "Since you're using the default styles set under the Custom Styles section, the width and height is fixed at 150px", CRP_LOCAL_NAME ); ?></p>
			<?php } ?>
		</td>
	</tr>
	<tr><th scope="row"><label for="thumb_crop"><?php _e( 'Crop mode:', CRP_LOCAL_NAME ); ?></label></th>
		<td>
			<input type="checkbox" name="thumb_crop" id="thumb_crop" <?php if ( $crp_settings['thumb_crop'] ) echo 'checked="checked"' ?> />
			<p class="description">
				<?php _e( "By default, thumbnails will be proportionately cropped. Check this box to hard crop the thumbnails.", CRP_LOCAL_NAME ); ?>
				<?php printf( __( "<a href='%s' target='_blank'>Difference between soft and hard crop</a>", CRP_LOCAL_NAME ), 'http://www.davidtan.org/wordpress-hard-crop-vs-soft-crop-difference-comparison-example/' ); ?>
			</p>
		</td>
	</tr>
	<tr><th scope="row"><label for="thumb_html"><?php _e( 'Style attributes / Width and Height HTML attributes:', CRP_LOCAL_NAME ); ?></label></th>
		<td>
			<label>
			<input type="radio" name="thumb_html" value="css" id="thumb_html_0" <?php if ( 'css' == $crp_settings['thumb_html'] ) echo 'checked="checked"' ?> />
			<?php _e( 'Style attributes are used for width and height.', CRP_LOCAL_NAME ); echo ' <code>style="max-width:' . $crp_settings['thumb_width'] . 'px;max-height:' . $crp_settings['thumb_height'] . 'px;"</code>'; ?></label>
			<br />
			<label>
			<input type="radio" name="thumb_html" value="html" id="thumb_html_1" <?php if ( 'html' == $crp_settings['thumb_html'] ) echo 'checked="checked"' ?> />
			<?php _e( 'HTML width and height attributes are used for width and height.', CRP_LOCAL_NAME ); echo ' <code>width="' . $crp_settings['thumb_width'] . '" height="' . $crp_settings['thumb_height'] . '"</code>'; ?></label>
			<br />
		</td>
	</tr>
	<tr><th scope="row"><label for="thumb_timthumb"><?php _e( 'Use timthumb to generate thumbnails? ', CRP_LOCAL_NAME ); ?></label></th>
		<td><input type="checkbox" name="thumb_timthumb" id="thumb_timthumb" <?php if ( $crp_settings['thumb_timthumb'] ) echo 'checked="checked"' ?> />
			<p class="description"><?php _e( 'If checked, <a href="http://www.binarymoon.co.uk/projects/timthumb/" target="_blank">timthumb</a> will be used to generate thumbnails', CRP_LOCAL_NAME ); ?></p>
		</td>
	</tr>
	<tr><th scope="row"><label for="thumb_timthumb_q"><?php _e( 'Quality of thumbnails generated by timthumb:', CRP_LOCAL_NAME ); ?></label></th>
		<td>
			<input type="textbox" name="thumb_timthumb_q" id="thumb_timthumb_q" value="<?php echo esc_attr( stripslashes( $crp_settings['thumb_timthumb_q'] ) ); ?>" style="width:50px" />
			<p class="description"><?php _e( 'Enter values between 0 and 100 only. 100 is highest quality and the highest file size. Suggested maximum value is 95. CRP default is 75.', CRP_LOCAL_NAME ); ?></p>
		</td>
	</tr>
	<tr><th scope="row"><label for="thumb_meta"><?php _e( 'Post thumbnail meta field name:', CRP_LOCAL_NAME ); ?></label></th>
		<td><input type="textbox" name="thumb_meta" id="thumb_meta" value="<?php echo esc_attr( stripslashes( $crp_settings['thumb_meta'] ) ); ?>">
			<p class="description"><?php _e( 'The value of this field should contain the image source and is set in the <em>Add New Post</em> screen', CRP_LOCAL_NAME ); ?></p>
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

<?php
/**
 * Represents the view for the Feeds Options set.
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

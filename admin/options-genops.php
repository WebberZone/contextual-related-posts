<?php
/**
 * Represents the view for the General Options.
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
			<p><input type="button" value="<?php _e( 'Clear cache', CRP_LOCAL_NAME ) ?>" onclick="return clearCache();" class="button-secondary" /></p>
		</td>
	</tr>

	<tr><th scope="row"><label for="limit"><?php _e( 'Number of related posts to display: ', CRP_LOCAL_NAME ); ?></label></th>
		<td>
			<input type="textbox" name="limit" id="limit" value="<?php echo esc_attr( stripslashes( $crp_settings['limit'] ) ); ?>">
			<p class="description"><?php _e( 'Maximum number of posts that will be displayed. The actual number may be smaller if less related posts are found.', CRP_LOCAL_NAME ); ?></p>
		</td>
	</tr>

	<tr><th scope="row"><label for="daily_range"><?php _e( 'Related posts should be newer than:', CRP_LOCAL_NAME ); ?></label></th>
		<td>
			<input type="textbox" name="daily_range" id="daily_range" value="<?php echo esc_attr( stripslashes( $crp_settings['daily_range'] ) ); ?>"><?php _e( 'days', CRP_LOCAL_NAME ); ?>
			<p class="description"><?php _e( 'This sets the cutoff period for which posts will be displayed. e.g. setting it to 365 will show related posts from the last year only.', CRP_LOCAL_NAME ); ?></p>
		</td>
	</tr>

	<tr><th scope="row"><?php _e( 'Post types to include in results:', CRP_LOCAL_NAME ); ?></th>
		<td>
			<?php foreach ( $wp_post_types as $wp_post_type ) {
				$post_type_op = '<label><input type="checkbox" name="post_types[]" value="' . $wp_post_type . '" ';
				if ( in_array( $wp_post_type, $posts_types_inc ) ) {
					$post_type_op .= ' checked="checked" ';
				}
				$post_type_op .= ' />'.$wp_post_type.'</label>&nbsp;&nbsp;';
				echo $post_type_op;
			}
			?>
			<p class="description"><?php _e( 'These post types will be displayed in the list. Includes custom post types.', CRP_LOCAL_NAME ); ?></p>
		</td>
	</tr>

	<tr><th scope="row"><label for="match_content"><?php _e( 'Find related posts based on content as well as title:', CRP_LOCAL_NAME ); ?></label></th>
		<td><input type="checkbox" name="match_content" id="match_content" <?php if ( $crp_settings['match_content'] ) echo 'checked="checked"' ?> />
			<p class="description"><?php _e( 'If unchecked, only posts titles are used. I recommend using a caching plugin or enabling "Cache output" above if you enable this.', CRP_LOCAL_NAME ); ?></p>
		</td>
	</tr>

	<tr><th scope="row"><label for="match_content_words"><?php _e( 'Limit content to be compared', CRP_LOCAL_NAME ); ?></label></th>
		<td><input type="textbox" name="match_content_words" id="match_content_words" value="<?php echo esc_attr(stripslashes($crp_settings['match_content_words'])); ?>">
			<p class="description"><?php _e( 'This sets the maximum words of the content that will be matched. 0 means no limit.', CRP_LOCAL_NAME ); ?></p>
		</td>
	</tr>

	<tr><th scope="row"><?php _e( 'Add related posts to:', CRP_LOCAL_NAME ); ?></th>
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

	<tr><th scope="row"><label for="content_filter_priority"><?php _e( 'Content filter priority:', CRP_LOCAL_NAME ); ?></label></th>
		<td>
			<input type="textbox" name="content_filter_priority" id="content_filter_priority" value="<?php echo esc_attr( stripslashes( $crp_settings['content_filter_priority'] ) ); ?>" />
			<p class="description"><?php _e( 'A higher number will cause the content above to be processed after other filters. Number below 10 is not recommended.', CRP_LOCAL_NAME ); ?></p>
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
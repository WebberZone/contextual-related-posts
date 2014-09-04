<?php
/**
 * Represents the view for the Custom styles option set.
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
		 * Fires before Custom styles options block.
		 *
		 * @since 2.0.0
		 *
		 * @param	array	$crp_settings	Contextual Related Posts settings array
		 */
		do_action( 'crp_admin_custom_styles_before', $crp_settings );
	?>

	<tr><th scope="row"><label for="include_default_style"><?php _e( 'Use default style included in the plugin?', CRP_LOCAL_NAME ); ?></label></th>
	  <td>
	  	<input type="checkbox" name="include_default_style" id="include_default_style" <?php if ( $crp_settings['include_default_style'] ) echo 'checked="checked"' ?> />
	  	<p class="description"><?php _e( 'Contextual Related Posts includes a default style that makes your popular posts list to look beautiful. Check the box above if you want to use this.', CRP_LOCAL_NAME ); ?></p>
	  	<p class="description"><?php _e( 'Enabling this option will turn on the thumbnails and set their width and height to 150px. It will also turn off the display of the author, excerpt and date if already enabled. Disabling this option will not revert any settings.', CRP_LOCAL_NAME ); ?></p>
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

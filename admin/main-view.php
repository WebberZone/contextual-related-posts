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

<div class="wrap">
	<h2><?php _e( 'Contextual Related Posts', CRP_LOCAL_NAME ); ?></h2>

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
	  	<li><a href="#outputopdiv"><?php _e( 'Output options', CRP_LOCAL_NAME ); ?></a> | </li>
	  	<li><a href="#feedopdiv"><?php _e( 'Feed options', CRP_LOCAL_NAME ); ?></a> | </li>
	  	<li><a href="#customcssdiv"><?php _e( 'Custom styles', CRP_LOCAL_NAME ); ?></a></li>
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

	      	<?php include_once( 'options-genops.php' ); ?>

	      </div> <!-- // inside -->
	    </div> <!-- // genopdiv -->

	    <div id="outputopdiv" class="postbox"><div class="handlediv" title="Click to toggle"><br /></div>
	      <h3 class='hndle'><span><?php _e( 'Output options', CRP_LOCAL_NAME ); ?></span></h3>
	      <div class="inside">

	      	<?php include_once( 'options-output.php' ); ?>

	      </div> <!-- // inside -->
	    </div> <!-- // outputopdiv -->

	    <div id="feedopdiv" class="postbox"><div class="handlediv" title="Click to toggle"><br /></div>
	      <h3 class='hndle'><span><?php _e( 'Feed options', CRP_LOCAL_NAME ); ?></span></h3>
	      <div class="inside">

	      	<?php include_once( 'options-feed.php' ); ?>

	      </div>
	    </div>
	    <div id="customcssdiv" class="postbox"><div class="handlediv" title="Click to toggle"><br /></div>
	      <h3 class='hndle'><span><?php _e( 'Custom styles', CRP_LOCAL_NAME ); ?></span></h3>
	      <div class="inside">

	      	<?php include_once( 'options-custom-styles.php' ); ?>

	      </div>
	    </div>

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

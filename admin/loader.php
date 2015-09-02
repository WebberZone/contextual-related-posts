<?php
/**
 * Contextual Related Posts Admin Loader.
 *
 * @package   Contextual_Related_Posts
 * @author    Ajay D'Souza <me@ajaydsouza.com>
 * @license   GPL-2.0+
 * @link      https://webberzone.com
 * @copyright 2009-2015 Ajay D'Souza
 */


/**
 * Add link to WordPress plugin action links.
 *
 * @version	1.8.10
 *
 * @param	array	$links
 * @return	array	Links array with our settings link added
 */
function crp_plugin_actions_links( $links ) {

	return array_merge( array(
			'settings' => '<a href="' . admin_url( 'options-general.php?page=crp_options' ) . '">' . __( 'Settings', CRP_LOCAL_NAME ) . '</a>'
		), $links );

}
add_filter( 'plugin_action_links_' . plugin_basename( plugin_dir_path( __DIR__ ) . 'contextual-related-posts.php' ), 'crp_plugin_actions_links' );


/**
 * Add links to the plugin action row.
 *
 * @since	1.4
 *
 * @param	array	$links
 * @param	array	$file
 * @return	array	Links array with our links added
 */
function crp_plugin_actions( $links, $file ) {

	$plugin = plugin_basename( plugin_dir_path( __DIR__ ) . 'contextual-related-posts.php' );

	/**** Add links ****/
	if ( $file == $plugin ) {
		$links[] = '<a href="http://wordpress.org/support/plugin/contextual-related-posts">' . __( 'Support', CRP_LOCAL_NAME ) . '</a>';
		$links[] = '<a href="https://ajaydsouza.com/donate/">' . __( 'Donate', CRP_LOCAL_NAME ) . '</a>';
		$links[] = '<a href="https://github.com/WebberZone/contextual-related-posts">' . __( 'Contribute', CRP_LOCAL_NAME ) . '</a>';
	}
	return $links;
}
add_filter( 'plugin_row_meta', 'crp_plugin_actions', 10, 2 ); // only 2.8 and higher



/**
 * Function to add a notice to the admin page.
 *
 * @since	1.8
 *
 * @return	string	Echoed string
 */
function crp_admin_notice() {
	$plugin_settings_page = '<a href="' . admin_url( 'options-general.php?page=crp_options' ) . '">' . __( 'plugin settings page', CRP_LOCAL_NAME ) . '</a>';

	if ( ! current_user_can( 'manage_options' ) ) return;

    echo '<div class="error">
       <p>' . __( "Contextual Related Posts plugin has just been installed / upgraded. Please visit the {$plugin_settings_page} to configure.", CRP_LOCAL_NAME ).'</p>
    </div>';
}
// add_action( 'admin_notices', 'crp_admin_notice' );



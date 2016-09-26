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
 * @param	array $links Links array.
 * @return	array	Links array with our settings link added
 */
function crp_plugin_actions_links( $links ) {

	return array_merge( array(
		'settings' => '<a href="' . admin_url( 'options-general.php?page=crp_options' ) . '">' . __( 'Settings', 'contextual-related-posts' ) . '</a>',
	), $links );

}
add_filter( 'plugin_action_links_' . plugin_basename( plugin_dir_path( __DIR__ ) . 'contextual-related-posts.php' ), 'crp_plugin_actions_links' );


/**
 * Add links to the plugin action row.
 *
 * @since	1.4
 *
 * @param array  $links Links array.
 * @param string $file Plugin file name.
 * @return array Links array with our links added
 */
function crp_plugin_actions( $links, $file ) {

	$plugin = plugin_basename( plugin_dir_path( __DIR__ ) . 'contextual-related-posts.php' );

	/**** Add links ****/
	if ( $file === $plugin ) {
		$links[] = '<a href="http://wordpress.org/support/plugin/contextual-related-posts">' . __( 'Support', 'contextual-related-posts' ) . '</a>';
		$links[] = '<a href="https://ajaydsouza.com/donate/">' . __( 'Donate', 'contextual-related-posts' ) . '</a>';
		$links[] = '<a href="https://github.com/WebberZone/contextual-related-posts">' . __( 'Contribute', 'contextual-related-posts' ) . '</a>';
	}
	return $links;
}
add_filter( 'plugin_row_meta', 'crp_plugin_actions', 10, 2 );


<?php
/**
 * Contextual Related Posts Admin Loader.
 *
 * @package   Contextual_Related_Posts
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Add link to WordPress plugin action links.
 *
 * @version 1.8.10
 *
 * @param array $links Links array.
 * @return array Links array with our settings link added
 */
function crp_plugin_actions_links( $links ) {

	return array_merge(
		array(
			'settings' => '<a href="' . admin_url( 'options-general.php?page=crp_options_page' ) . '">' . esc_html__( 'Settings', 'contextual-related-posts' ) . '</a>',
		),
		$links
	);

}
add_filter( 'plugin_action_links_' . plugin_basename( CRP_PLUGIN_FILE ), 'crp_plugin_actions_links' );


/**
 * Add links to the plugin action row.
 *
 * @since 1.4
 *
 * @param array  $links Links array.
 * @param string $file Plugin file name.
 * @return array Links array with our links added
 */
function crp_plugin_actions( $links, $file ) {

	if ( plugin_basename( CRP_PLUGIN_FILE ) === $file ) {

		$new_links = array(
			'support'    => '<a href = "http://wordpress.org/support/plugin/contextual-related-posts">' . esc_html__( 'Support', 'contextual-related-posts' ) . '</a>',
			'donate'     => '<a href = "https://ajaydsouza.com/donate/">' . esc_html__( 'Donate', 'contextual-related-posts' ) . '</a>',
			'contribute' => '<a href = "https://github.com/WebberZone/contextual-related-posts">' . esc_html__( 'Contribute', 'contextual-related-posts' ) . '</a>',
		);

		$links = array_merge( $links, $new_links );
	}
	return $links;
}
add_filter( 'plugin_row_meta', 'crp_plugin_actions', 10, 2 );


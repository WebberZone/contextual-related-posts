<?php
/**
 * PHPUnit bootstrap file
 *
 * @package Contextual_Related_Posts
 */

$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = '/tmp/wordpress-tests-lib';
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually load the plugin being tested.
 */
function _manually_load_plugin() {
	require dirname( dirname( __FILE__ ) ) . '/contextual-related-posts.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';

activate_plugin( 'contextual-related-posts/contextual-related-posts.php' );

echo "Installing Contextual Related Posts...\n";

global $crp_settings, $current_user;

activate_crp( true );

$crp_settings = crp_read_options();

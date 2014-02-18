<?php
if ( !defined('ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') ) {
    exit();
}
	global $wpdb;
    $poststable = $wpdb->posts;

	$sql = "ALTER TABLE $poststable DROP INDEX crp_related";
	$wpdb->query($sql);
	
	$sql = "ALTER TABLE $poststable DROP INDEX crp_related_title";
	$wpdb->query($sql);
	
	$sql = "ALTER TABLE $poststable DROP INDEX crp_related_content";
	$wpdb->query($sql);
	
	$sql = "ALTER TABLE $poststable DROP INDEX crp_related_content";
	$wpdb->query($sql);
	
	$wpdb->query("
		DELETE FROM " . $wpdb->postmeta . "
		WHERE meta_key='crp_related_posts'
	");

	$wpdb->query("
		DELETE FROM " . $wpdb->postmeta . "
		WHERE meta_key='crp_related_posts_widget'
	");

	delete_option('ald_crp_settings');
?>
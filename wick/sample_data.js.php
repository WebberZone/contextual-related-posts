<?php
//"sample_data.js.php" List of categories
Header("content-type: application/x-javascript");

if (!function_exists('add_action')) {
	$wp_root = '../../../..';
	if (file_exists($wp_root.'/wp-load.php')) {
		require_once($wp_root.'/wp-load.php');
	} else {
		require_once($wp_root.'/wp-config.php');
	}
}

// Ajax Increment Counter
wick_data();
function wick_data() {
	global $wpdb;
	
	$categories = get_categories('hide_empty=0');
	$str = 'collection = [';
	foreach ($categories as $cat) {
		$str .= "'".$cat->slug."',";
	}
	$str = substr($str, 0, -1);	// Remove trailing comma
	$str .= '];';
	
	echo $str;
}


?>
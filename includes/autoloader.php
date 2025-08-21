<?php
/**
 * Autoloads classes from the WebberZone\Contextual_Related_Posts namespace.
 *
 * @package WebberZone\Contextual_Related_Posts
 */

namespace WebberZone\Contextual_Related_Posts;

defined( 'ABSPATH' ) || exit;

/**
 * Autoloader for WebberZone\Contextual_Related_Posts classes.
 *
 * @param string $class_name The name of the class to load.
 */
function autoload( $class_name ) {
	$namespace         = __NAMESPACE__;
	$classes_subfolder = 'includes';

	if ( class_exists( $class_name, false ) ) {
		return;
	}

	if ( false !== strpos( $class_name, $namespace ) ) {
		$classes_dir = realpath( dirname( __DIR__ ) ) . DIRECTORY_SEPARATOR . $classes_subfolder . DIRECTORY_SEPARATOR;

		// Project namespace.
		$project_namespace = $namespace . '\\';
		$length            = strlen( $project_namespace );

		$class_file = substr( $class_name, $length ); // Remove top level namespace (that is the current dir).
		$class_file = str_replace( '_', '-', strtolower( $class_file ) ); // Swap underscores for dashes and lowercase.

		// Prepend `class-` to the filename (last class part).
		$class_parts                = explode( '\\', $class_file ); // Split the class name into parts.
		$last_index                 = count( $class_parts ) - 1; // Get the last index.
		$class_parts[ $last_index ] = 'class-' . $class_parts[ $last_index ]; // Replace the last part with `class-`.

		// Join everything back together and add the file extension.
		$class_file = implode( DIRECTORY_SEPARATOR, $class_parts ) . '.php';
		$location   = $classes_dir . $class_file;

		if ( ! is_file( $location ) ) {
			return;
		}

		require_once $location;
	}
}
$autoload_functions = spl_autoload_functions();
if ( ! in_array( __NAMESPACE__ . '\autoload', $autoload_functions ? $autoload_functions : array(), true ) ) {
	spl_autoload_register( __NAMESPACE__ . '\autoload' );
}

<?php
/**
 * Hook Registry class.
 *
 * @package WebberZone\Contextual_Related_Posts
 */

namespace WebberZone\Contextual_Related_Posts\Util;

if ( ! defined( 'WPINC' ) ) {
	exit;
}

/**
 * Hook Registry class for managing WordPress actions and filters.
 *
 * @since 4.0.0
 */
class Hook_Registry {

	/**
	 * Registered hooks.
	 *
	 * @var array
	 */
	private static $hooks = array();

	/**
	 * Register a hook (action or filter).
	 *
	 * @param string   $hook_type Either 'action' or 'filter'.
	 * @param string   $hook_name The hook name.
	 * @param callable $callback  The callback function.
	 * @param int      $priority  Priority of the hook.
	 * @param int      $args      Number of arguments.
	 *
	 * @return bool True if registered, false if duplicate or invalid hook.
	 */
	public static function register( $hook_type, $hook_name, $callback, $priority = 10, $args = 1 ) {
		if ( ! in_array( $hook_type, array( 'action', 'filter' ), true ) ) {
			return false;
		}
		if ( ! is_string( $hook_name ) || empty( trim( $hook_name ) ) ) {
			return false;
		}
		if ( ! is_callable( $callback ) ) {
			return false;
		}
		if ( ! is_int( $priority ) || $priority < 0 ) {
			return false;
		}
		if ( ! is_int( $args ) || $args < 1 ) {
			return false;
		}

		$closure_id = '';
		if ( $callback instanceof \Closure ) {
			$closure_id = uniqid( 'closure_', true );
		}

		$key = self::create_hook_key( $hook_name, $callback, $priority, $closure_id );

		if ( isset( self::$hooks[ $key ] ) ) {
			return false;
		}

		// Store the hook details.
		self::$hooks[ $key ] = array(
			'type'       => $hook_type,
			'name'       => $hook_name,
			'callback'   => $callback,
			'priority'   => $priority,
			'args'       => $args,
			'closure_id' => $closure_id,
		);

		// Register with WordPress.
		if ( 'action' === $hook_type ) {
			add_action( $hook_name, $callback, $priority, $args );
		} else {
			add_filter( $hook_name, $callback, $priority, $args );
		}

		return true;
	}

	/**
	 * Register an action.
	 *
	 * @param string   $hook_name The hook name.
	 * @param callable $callback  The callback function.
	 * @param int      $priority  Priority of the hook.
	 * @param int      $args      Number of arguments.
	 *
	 * @return bool True if registered, false if duplicate or invalid hook.
	 */
	public static function add_action( $hook_name, $callback, $priority = 10, $args = 1 ) {
		return self::register( 'action', $hook_name, $callback, $priority, $args );
	}

	/**
	 * Register a filter.
	 *
	 * @param string   $hook_name The hook name.
	 * @param callable $callback  The callback function.
	 * @param int      $priority  Priority of the hook.
	 * @param int      $args      Number of arguments.
	 *
	 * @return bool True if registered, false if duplicate or invalid hook.
	 */
	public static function add_filter( $hook_name, $callback, $priority = 10, $args = 1 ) {
		return self::register( 'filter', $hook_name, $callback, $priority, $args );
	}

	/**
	 * Remove a hook (action or filter).
	 *
	 * Note: Closures are removable only if registered via this class.
	 *
	 * @param string   $hook_type Either 'action' or 'filter'.
	 * @param string   $hook_name The hook name.
	 * @param callable $callback  The callback function.
	 * @param int      $priority  Priority of the hook.
	 *
	 * @return bool True if removed, false if not found or removal failed.
	 */
	public static function remove( $hook_type, $hook_name, $callback, $priority = 10 ) {
		$closure_id = '';
		if ( $callback instanceof \Closure ) {
			// Find the closure_id for this callback.
			foreach ( self::$hooks as $hook ) {
				if ( $hook['name'] === $hook_name &&
					$hook['priority'] === $priority &&
					$hook['type'] === $hook_type &&
					$hook['callback'] === $callback ) {
					$closure_id = $hook['closure_id'];
					break;
				}
			}
		}

		$key = self::create_hook_key( $hook_name, $callback, $priority, $closure_id );

		if ( ! isset( self::$hooks[ $key ] ) || self::$hooks[ $key ]['type'] !== $hook_type ) {
			return false;
		}

		$removed = false;
		if ( 'action' === $hook_type ) {
			$removed = remove_action( $hook_name, $callback, $priority );
		} else {
			$removed = remove_filter( $hook_name, $callback, $priority );
		}

		if ( $removed ) {
			unset( self::$hooks[ $key ] );
		}

		return $removed;
	}

	/**
	 * Remove an action.
	 *
	 * @param string   $hook_name The hook name.
	 * @param callable $callback  The callback function.
	 * @param int      $priority  Priority of the hook.
	 *
	 * @return bool True if removed, false if not found or invalid hook.
	 */
	public static function remove_action( $hook_name, $callback, $priority = 10 ) {
		return self::remove( 'action', $hook_name, $callback, $priority );
	}

	/**
	 * Remove a filter.
	 *
	 * @param string   $hook_name The hook name.
	 * @param callable $callback  The callback function.
	 * @param int      $priority  Priority of the hook.
	 *
	 * @return bool True if removed, false if not found or removal failed.
	 */
	public static function remove_filter( $hook_name, $callback, $priority = 10 ) {
		return self::remove( 'filter', $hook_name, $callback, $priority );
	}

	/**
	 * Get all registered hooks.
	 *
	 * @return array Array of registered hooks.
	 */
	public static function get_hooks() {
		return self::$hooks;
	}

	/**
	 * Remove all registered hooks.
	 *
	 * @return void
	 */
	public static function remove_all_hooks() {
		foreach ( self::$hooks as $key => $hook ) {
			if ( 'action' === $hook['type'] ) {
				remove_action( $hook['name'], $hook['callback'], $hook['priority'] );
			} else {
				remove_filter( $hook['name'], $hook['callback'], $hook['priority'] );
			}
			unset( self::$hooks[ $key ] );
		}
	}

	/**
	 * Create a unique key for a hook registration.
	 *
	 * @param string   $hook_name  The hook name.
	 * @param callable $callback   The callback function.
	 * @param int      $priority   Priority of the hook.
	 * @param string   $closure_id Unique ID for closures.
	 *
	 * @return string Unique key.
	 */
	private static function create_hook_key( $hook_name, $callback, $priority, $closure_id = '' ) {
		return md5( $hook_name . self::callback_to_string( $callback ) . $priority . $closure_id );
	}

	/**
	 * Convert a callback to a string representation.
	 *
	 * @param callable $callback The callback to convert.
	 *
	 * @return string String representation of the callback.
	 */
	private static function callback_to_string( $callback ) {
		if ( is_string( $callback ) ) {
			return $callback;
		}

		if ( is_array( $callback ) ) {
			if ( is_object( $callback[0] ) ) {
				return get_class( $callback[0] ) . '::' . $callback[1];
			}
			return $callback[0] . '::' . $callback[1];
		}

		if ( $callback instanceof \Closure ) {
			return 'closure';
		}

		return 'unknown';
	}
}

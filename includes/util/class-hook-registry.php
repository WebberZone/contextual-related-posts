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
	 * Remove a registered hook.
	 *
	 * @param string   $hook_type Either 'action' or 'filter'.
	 * @param string   $hook_name The hook name.
	 * @param callable $callback  The callback function.
	 * @param int      $priority  Priority of the hook.
	 *
	 * @return bool True if removed, false if not found or invalid hook.
	 */
	public static function remove( $hook_type, $hook_name, $callback, $priority = 10 ) {
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

		$key = self::create_hook_key( $hook_name, $callback, $priority );

		if ( ! isset( self::$hooks[ $key ] ) ) {
			return false;
		}

		unset( self::$hooks[ $key ] );

		// Remove from WordPress.
		if ( 'action' === $hook_type ) {
			return remove_action( $hook_name, $callback, $priority );
		} else {
			return remove_filter( $hook_name, $callback, $priority );
		}
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
	 * @return bool True if removed, false if not found or invalid hook.
	 */
	public static function remove_filter( $hook_name, $callback, $priority = 10 ) {
		return self::remove( 'filter', $hook_name, $callback, $priority );
	}

	/**
	 * Create a unique key for a hook.
	 *
	 * @param string   $hook_name  The hook name.
	 * @param callable $callback   The callback function.
	 * @param int      $priority   Priority of the hook.
	 * @param string   $closure_id Optional closure ID.
	 *
	 * @return string Hook key.
	 */
	private static function create_hook_key( $hook_name, $callback, $priority, $closure_id = '' ) {
		if ( is_array( $callback ) ) {
			$callback_str = is_object( $callback[0] ) ? get_class( $callback[0] ) . ':' . $callback[1] : $callback[0] . ':' . $callback[1];
		} elseif ( is_object( $callback ) && $callback instanceof \Closure ) {
			$callback_str = 'closure:' . $closure_id;
		} else {
			$callback_str = strval( $callback );
		}

		return $hook_name . ':' . $callback_str . ':' . $priority;
	}
}

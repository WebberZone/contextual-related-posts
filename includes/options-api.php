<?php
/**
 * Contextual Related Posts Options API
 *
 * @since 3.5.0
 *
 * @package Contextual_Related_Posts
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Get Settings.
 *
 * Retrieves all plugin settings
 *
 * @since  2.6.0
 * @return array Contextual Related Posts settings
 */
function crp_get_settings() {

	$settings = get_option( 'crp_settings', array() );

	/**
	 * Settings array
	 *
	 * Retrieves all plugin settings
	 *
	 * @since 2.0.0
	 * @param array $settings Settings array
	 */
	return apply_filters( 'crp_get_settings', $settings );
}


/**
 * Get an option
 *
 * Looks to see if the specified setting exists, returns default if not
 *
 * @since 2.6.0
 *
 * @param string $key           Key of the option to fetch.
 * @param mixed  $default_value Default value to fetch if option is missing.
 * @return mixed
 */
function crp_get_option( $key = '', $default_value = null ) {
	$crp_settings = crp_get_settings();

	if ( null === $default_value ) {
		$default_value = crp_get_default_option( $key );
	}

	$value = $crp_settings[ $key ] ?? $default_value;

	/**
	 * Filter the value for the option being fetched.
	 *
	 * @since 2.6.0
	 *
	 * @param mixed   $value   Value of the option
	 * @param mixed   $key     Name of the option
	 * @param mixed   $default_value Default value
	 */
	$value = apply_filters( 'crp_get_option', $value, $key, $default_value );

	/**
	 * Key specific filter for the value of the option being fetched.
	 *
	 * @since 2.6.0
	 *
	 * @param mixed   $value   Value of the option
	 * @param mixed   $key     Name of the option
	 * @param mixed   $default_value Default value
	 */
	return apply_filters( 'crp_get_option_' . $key, $value, $key, $default_value );
}

/**
 * Get an option from a specific blog in a multisite network.
 *
 * @since 4.1.0
 *
 * @param int    $blog_id       Blog ID to fetch the option from.
 * @param string $key           Key of the option to fetch.
 * @param mixed  $default_value Default value to fetch if option is missing.
 * @return mixed
 */
function crp_get_blog_option( $blog_id, $key = '', $default_value = false ) {

	$blog_id = (int) $blog_id;

	if ( empty( $blog_id ) ) {
		$blog_id = get_current_blog_id();
	}

	if ( get_current_blog_id() === $blog_id ) {
		return crp_get_option( $key, $default_value );
	}

	if ( is_multisite() ) {
		switch_to_blog( $blog_id );
		$value = crp_get_option( $key, $default_value );
		restore_current_blog();
	} else {
		$value = crp_get_option( $key, $default_value );
	}

	/**
	 * Filters a blog option value.
	 *
	 * @since 4.1.0
	 *
	 * @param mixed  $value   The option value.
	 * @param int    $blog_id Blog ID.
	 * @param string $key     Option key.
	 */
	return apply_filters( "crp_blog_option_{$key}", $value, $blog_id, $key );
}


/**
 * Update an option
 *
 * Updates an crp setting value in both the db and the global variable.
 * Warning: Passing in a null value will remove
 *          the key from the crp_options array.
 *
 * @since 2.6.0
 *
 * @param string          $key   The Key to update.
 * @param string|bool|int $value The value to set the key to.
 * @return boolean   True if updated, false if not.
 */
function crp_update_option( $key = '', $value = null ) {

	// If no key, exit.
	if ( empty( $key ) ) {
		return false;
	}

	// If null value, delete.
	if ( is_null( $value ) ) {
		$remove_option = crp_delete_option( $key );
		return $remove_option;
	}

	// First let's grab the current settings.
	$options = get_option( 'crp_settings' );

	/**
	 * Filters the value before it is updated
	 *
	 * @since 2.6.0
	 *
	 * @param string|bool|int $value The value to set the key to
	 * @param string  $key   The Key to update
	 */
	$value = apply_filters( 'crp_update_option', $value, $key );

	// Next let's try to update the value.
	$options[ $key ] = $value;
	$did_update      = update_option( 'crp_settings', $options );

	// If it updated, let's update the global variable.
	if ( $did_update ) {
		global $crp_settings;
		$crp_settings[ $key ] = $value;
	}
	return $did_update;
}


/**
 * Remove an option
 *
 * Removes an crp setting value in both the db and the global variable.
 *
 * @since 2.6.0
 *
 * @param string $key The Key to update.
 * @return boolean   True if updated, false if not.
 */
function crp_delete_option( $key = '' ) {

	// If no key, exit.
	if ( empty( $key ) ) {
		return false;
	}

	// First let's grab the current settings.
	$options = get_option( 'crp_settings' );

	// Next let's try to update the value.
	if ( isset( $options[ $key ] ) ) {
		unset( $options[ $key ] );
	}

	$did_update = update_option( 'crp_settings', $options );

	// If it updated, let's update the global variable.
	if ( $did_update ) {
		global $crp_settings;
		$crp_settings = $options;
	}
	return $did_update;
}


/**
 * Flattens crp_get_registered_settings() into $setting[id] => $setting[type] format.
 *
 * @since 2.6.0
 *
 * @return array Default settings
 */
function crp_get_registered_settings_types() {

	$options = array();

	// Populate some default values.
	foreach ( \WebberZone\Contextual_Related_Posts\Admin\Settings::get_registered_settings() as $tab => $settings ) {
		foreach ( $settings as $option ) {
			$options[ $option['id'] ] = $option['type'];
		}
	}

	/**
	 * Filters the settings array.
	 *
	 * @since 2.6.0
	 *
	 * @param array   $options Default settings.
	 */
	return apply_filters( 'crp_get_settings_types', $options );
}


/**
 * Default settings.
 *
 * @since 2.6.0
 *
 * @return array Default settings
 */
function crp_settings_defaults() {

	$options = array();

	// Populate some default values.
	foreach ( \WebberZone\Contextual_Related_Posts\Admin\Settings::get_registered_settings() as $tab => $settings ) {
		foreach ( $settings as $option ) {
			// When checkbox is set to true, set this to 1.
			if ( 'checkbox' === $option['type'] && ! empty( $option['options'] ) ) {
				$options[ $option['id'] ] = 1;
			} else {
				$options[ $option['id'] ] = 0;
			}
			// If an option is set.
			if ( in_array( $option['type'], array( 'textarea', 'css', 'html', 'text', 'url', 'csv', 'color', 'numbercsv', 'postids', 'posttypes', 'number', 'wysiwyg', 'file', 'password' ), true ) && isset( $option['options'] ) ) {
				$options[ $option['id'] ] = $option['options'];
			}
			if ( in_array( $option['type'], array( 'multicheck', 'radio', 'select', 'radiodesc', 'thumbsizes' ), true ) && isset( $option['default'] ) ) {
				$options[ $option['id'] ] = $option['default'];
			}
		}
	}

	/**
	 * Filters the default settings array.
	 *
	 * @since 2.6.0
	 *
	 * @param array   $options Default settings.
	 */
	return apply_filters( 'crp_settings_defaults', $options );
}


/**
 * Get the default option for a specific key
 *
 * @since 2.6.0
 *
 * @param string $key Key of the option to fetch.
 * @return mixed
 */
function crp_get_default_option( $key = '' ) {

	$default_settings = crp_settings_defaults();

	if ( array_key_exists( $key, $default_settings ) ) {
		return $default_settings[ $key ];
	} else {
		return false;
	}
}

/**
 * Reset settings.
 *
 * @since 2.6.0
 *
 * @return void
 */
function crp_settings_reset() {
	delete_option( 'crp_settings' );
}

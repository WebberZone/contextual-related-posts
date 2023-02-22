<?php
/**
 * Save settings.
 *
 * Functions to register, read, write and update settings.
 * Portions of this code have been inspired by Easy Digital Downloads, WordPress Settings Sandbox, etc.
 *
 * @link  https://webberzone.com
 * @since 2.6.0
 *
 * @package    Contextual Related Posts
 * @subpackage Admin/Save_Settings
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Sanitize the form data being submitted.
 *
 * @since 2.6.0
 * @param  array $input Input unclean array.
 * @return array Sanitized array
 */
function crp_settings_sanitize( $input = array() ) {

	// First, we read the options collection.
	global $crp_settings;

	// This should be set if a form is submitted, so let's save it in the $referrer variable.
	if ( empty( $_POST['_wp_http_referer'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return $input;
	}

	parse_str( sanitize_text_field( wp_unslash( $_POST['_wp_http_referer'] ) ), $referrer ); // phpcs:ignore WordPress.Security.NonceVerification.Missing

	// Get the various settings we've registered.
	$settings       = crp_get_registered_settings();
	$settings_types = crp_get_registered_settings_types();

	// Check if we need to set to defaults.
	$reset = isset( $_POST['settings_reset'] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing

	if ( $reset ) {
		crp_settings_reset();
		$crp_settings = crp_get_settings();

		add_settings_error( 'crp-notices', 'crp_reset', __( 'Settings have been reset to their default values. Reload this page to view the updated settings', 'contextual-related-posts' ), 'error' );

		return $crp_settings;
	}

	// Get the tab. This is also our settings' section.
	$tab = isset( $referrer['tab'] ) ? $referrer['tab'] : 'general';

	$input = $input ? $input : array();

	/**
	 * Filter the settings for the tab. e.g. crp_settings_general_sanitize.
	 *
	 * @since 2.6.0
	 * @param  array $input Input unclean array
	 */
	$input = apply_filters( 'crp_settings_' . $tab . '_sanitize', $input );

	// Create out output array by merging the existing settings with the ones submitted.
	$output = array_merge( $crp_settings, $input );

	// Loop through each setting being saved and pass it through a sanitization filter.
	foreach ( $settings_types as $key => $type ) {

		/**
		 * Skip settings that are not really settings.
		 *
		 * @since 2.6.0
		 * @param  array $non_setting_types Array of types which are not settings.
		 */
		$non_setting_types = apply_filters( 'crp_non_setting_types', array( 'header', 'descriptive_text' ) );

		if ( in_array( $type, $non_setting_types, true ) ) {
			continue;
		}

		if ( array_key_exists( $key, $output ) ) {

			/**
			 * Field type filter.
			 *
			 * @since 2.6.0
			 * @param array $output[$key] Setting value.
			 * @param array $key Setting key.
			 */
			$output[ $key ] = apply_filters( 'crp_settings_sanitize_' . $type, $output[ $key ], $key );
		}

		/**
		 * Field type filter for a specific key.
		 *
		 * @since 2.6.0
		 * @param array $output[$key] Setting value.
		 * @param array $key Setting key.
		 */
		$output[ $key ] = apply_filters( 'crp_settings_sanitize' . $key, $output[ $key ], $key );

		// Delete any key that is not present when we submit the input array.
		if ( ! isset( $input[ $key ] ) ) {
			unset( $output[ $key ] );
		}
		// Delete any settings that are no longer part of our registered settings.
		if ( array_key_exists( $key, $output ) && ! array_key_exists( $key, $settings_types ) ) {
			unset( $output[ $key ] );
		}
	}

	add_settings_error( 'crp-notices', 'crp-updated', __( 'Settings updated.', 'contextual-related-posts' ), 'updated' );

	// Overwrite settings if rounded thumbnail style is selected.
	if ( 'rounded_thumbs' === $output['crp_styles'] || 'thumbs_grid' === $output['crp_styles'] ) {
		add_settings_error( 'crp-notices', 'crp-styles', __( 'Rounded Thumbnails style selected in Styles tab. Post author, excerpt and date disabled. Thumbnail location set to either "inline before text" or "only thumbnails, no text".', 'contextual-related-posts' ), 'updated' );
	}
	// Overwrite settings if text_only thumbnail style is selected.
	if ( 'text_only' === $output['crp_styles'] ) {
		add_settings_error( 'crp-notices', 'crp-styles', __( 'Text only style selected in Styles tab. Thumbnail location set to text only.', 'contextual-related-posts' ), 'updated' );
	}

	/**
	 * Filter the settings array before it is returned.
	 *
	 * @since 2.6.0
	 * @param array $output Settings array.
	 * @param array $input Input settings array.
	 */
	return apply_filters( 'crp_settings_sanitize', $output, $input );

}


/**
 * Sanitize text fields
 *
 * @since 2.6.0
 *
 * @param  array $value The field value.
 * @return string  $value  Sanitized value
 */
function crp_sanitize_text_field( $value ) {
	return wp_kses_post( wp_unslash( $value ) );
}
add_filter( 'crp_settings_sanitize_text', 'crp_sanitize_text_field' );


/**
 * Sanitize number fields
 *
 * @since 2.6.0
 *
 * @param  array $value The field value.
 * @return string  $value  Sanitized value
 */
function crp_sanitize_number_field( $value ) {
	return filter_var( $value, FILTER_SANITIZE_NUMBER_INT );
}
add_filter( 'crp_settings_sanitize_number', 'crp_sanitize_number_field' );


/**
 * Sanitize CSV fields
 *
 * @since 2.6.0
 *
 * @param  array $value The field value.
 * @return string  $value  Sanitized value
 */
function crp_sanitize_csv_field( $value ) {

	return implode( ',', array_map( 'trim', explode( ',', sanitize_text_field( wp_unslash( $value ) ) ) ) );
}
add_filter( 'crp_settings_sanitize_csv', 'crp_sanitize_csv_field' );


/**
 * Sanitize CSV fields which hold numbers e.g. IDs
 *
 * @since 2.6.0
 *
 * @param  array $value The field value.
 * @return string  $value  Sanitized value
 */
function crp_sanitize_numbercsv_field( $value ) {

	return implode( ',', array_filter( array_map( 'absint', explode( ',', sanitize_text_field( wp_unslash( $value ) ) ) ) ) );
}
add_filter( 'crp_settings_sanitize_numbercsv', 'crp_sanitize_numbercsv_field' );


/**
 * Sanitize textarea fields
 *
 * @since 2.6.0
 *
 * @param  array $value The field value.
 * @return string  $value  Sanitized value
 */
function crp_sanitize_textarea_field( $value ) {

	global $allowedposttags;

	// We need more tags to allow for script and style.
	$moretags = array(
		'script' => array(
			'type'    => true,
			'src'     => true,
			'async'   => true,
			'defer'   => true,
			'charset' => true,
			'lang'    => true,
		),
		'style'  => array(
			'type'   => true,
			'media'  => true,
			'scoped' => true,
			'lang'   => true,
		),
		'link'   => array(
			'rel'      => true,
			'type'     => true,
			'href'     => true,
			'media'    => true,
			'sizes'    => true,
			'hreflang' => true,
		),
	);

	$allowedtags = array_merge( $allowedposttags, $moretags );

	/**
	 * Filter allowed tags allowed when sanitizing text and textarea fields.
	 *
	 * @since 2.6.0
	 *
	 * @param array $allowedtags Allowed tags array.
	 * @param array $value The field value.
	 */
	$allowedtags = apply_filters( 'crp_sanitize_allowed_tags', $allowedtags, $value );

	return wp_kses( wp_unslash( $value ), $allowedtags );

}
add_filter( 'crp_settings_sanitize_textarea', 'crp_sanitize_textarea_field' );


/**
 * Sanitize checkbox fields
 *
 * @since 2.6.0
 *
 * @param  array $value The field value.
 * @return string|int  $value  Sanitized value
 */
function crp_sanitize_checkbox_field( $value ) {

	$value = ( -1 === (int) $value ) ? 0 : 1;

	return $value;
}
add_filter( 'crp_settings_sanitize_checkbox', 'crp_sanitize_checkbox_field' );


/**
 * Sanitize post_types fields
 *
 * @since 2.6.0
 *
 * @param  array $value The field value.
 * @return string  $value  Sanitized value
 */
function crp_sanitize_posttypes_field( $value ) {

	$post_types = is_array( $value ) ? array_map( 'sanitize_text_field', wp_unslash( $value ) ) : array();

	return implode( ',', $post_types );
}
add_filter( 'crp_settings_sanitize_posttypes', 'crp_sanitize_posttypes_field' );


/**
 * Sanitize taxonomies fields
 *
 * @since 2.6.0
 *
 * @param  array $value The field value.
 * @return string  $value  Sanitized value
 */
function crp_sanitize_taxonomies_field( $value ) {

	$taxonomies = is_array( $value ) ? array_map( 'sanitize_text_field', wp_unslash( $value ) ) : array();

	return implode( ',', $taxonomies );
}
add_filter( 'crp_settings_sanitize_taxonomies', 'crp_sanitize_taxonomies_field' );


/**
 * Modify settings when they are being saved.
 *
 * @since 2.6.0
 *
 * @param  array $settings Settings array.
 * @return string  $settings  Sanitized settings array.
 */
function crp_change_settings_on_save( $settings ) {

	// Sanitize exclude_cat_slugs to save a new entry of exclude_categories.
	if ( isset( $settings['exclude_cat_slugs'] ) ) {

		$exclude_cat_slugs = array_unique( str_getcsv( $settings['exclude_cat_slugs'] ) );

		foreach ( $exclude_cat_slugs as $slug ) {
			// Pattern is Name (taxonomy:term_taxonomy_id).
			preg_match( '/(.*)\((.*):(\d+)\)/i', $slug, $matches );
			if ( isset( $matches[3] ) ) { // This holds the term_taxonomy_id.
				$term = get_term_by( 'term_taxonomy_id', $matches[3] );
			} else {
				$term = get_term_by( 'name', $slug, 'category' );

				// Fall back to slugs since that was the default format before v2.4.0.
				if ( false === $term ) {
					$term = get_term_by( 'slug', $slug, 'category' );
				}
			}
			if ( isset( $term->term_taxonomy_id ) ) {
				$exclude_categories[]       = $term->term_taxonomy_id;
				$exclude_categories_slugs[] = "{$term->name} ({$term->taxonomy}:{$term->term_taxonomy_id})";
			}
		}
		$settings['exclude_categories'] = isset( $exclude_categories ) ? join( ',', $exclude_categories ) : '';
		$settings['exclude_cat_slugs']  = isset( $exclude_categories_slugs ) ? crp_str_putcsv( $exclude_categories_slugs ) : '';
	}

	// Sanitize exclude_on_cat_slugs to save a new entry of exclude_on_categories.
	if ( isset( $settings['exclude_on_cat_slugs'] ) ) {

		$exclude_on_cat_slugs = array_unique( str_getcsv( $settings['exclude_on_cat_slugs'] ) );

		foreach ( $exclude_on_cat_slugs as $slug ) {
			// Pattern is Name (taxonomy:term_taxonomy_id).
			preg_match( '/(.*)\((.*):(\d+)\)/i', $slug, $matches );
			if ( isset( $matches[3] ) ) { // This holds the term_taxonomy_id.
				$term = get_term_by( 'term_taxonomy_id', $matches[3] );
			} else {
				$term = get_term_by( 'name', $slug, 'category' );
			}
			if ( isset( $term->term_taxonomy_id ) ) {
				$exclude_on_categories[]       = $term->term_taxonomy_id;
				$exclude_on_categories_slugs[] = "{$term->name} ({$term->taxonomy}:{$term->term_taxonomy_id})";
			}
		}
		$settings['exclude_on_categories'] = isset( $exclude_on_categories ) ? join( ',', $exclude_on_categories ) : '';
		$settings['exclude_on_cat_slugs']  = isset( $exclude_on_categories_slugs ) ? crp_str_putcsv( $exclude_on_categories_slugs ) : '';

	}

	// Overwrite settings if rounded thumbnail style is selected.
	if ( 'rounded_thumbs' === $settings['crp_styles'] || 'thumbs_grid' === $settings['crp_styles'] ) {
		$settings['show_excerpt'] = 0;
		$settings['show_author']  = 0;
		$settings['show_date']    = 0;

		if ( 'inline' !== $settings['post_thumb_op'] && 'thumbs_only' !== $settings['post_thumb_op'] ) {
			$settings['post_thumb_op'] = 'inline';
		}
	}
	// Overwrite settings if text_only thumbnail style is selected.
	if ( 'text_only' === $settings['crp_styles'] ) {
		$settings['post_thumb_op'] = 'text_only';
	}

	return $settings;
}
add_filter( 'crp_settings_sanitize', 'crp_change_settings_on_save' );

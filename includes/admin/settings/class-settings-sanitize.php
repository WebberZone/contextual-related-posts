<?php
/**
 * Functions to sanitize settings.
 *
 * @link  https://webberzone.com
 *
 * @package WebberZone\Contextual_Related_Posts
 */

namespace WebberZone\Contextual_Related_Posts\Admin\Settings;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Settings Sanitize Class.
 */
class Settings_Sanitize {

	/**
	 * Settings Key.
	 *
	 * @var string Settings Key.
	 */
	public $settings_key;

	/**
	 * Prefix which is used for creating the unique filters and actions.
	 *
	 * @var string Prefix.
	 */
	public $prefix;

	/**
	 * Main constructor class.
	 *
	 * @param mixed $args {
	 *    Array or string of arguments. Default is blank array.
	 *     @type string  $settings_key          Settings key.
	 *     @type string  $prefix                Prefix.
	 * }
	 */
	public function __construct( $args ) {
		$defaults = array(
			'settings_key' => '',
			'prefix'       => '',
		);
		$args     = wp_parse_args( $args, $defaults );

		foreach ( $args as $name => $value ) {
			$this->$name = $value;
		}
	}

	/**
	 * Get the value of a settings field.
	 *
	 * @param string $option         Settings field name.
	 * @param mixed  $default_value  Default value if option is not found.
	 * @return mixed
	 */
	public function get_option( $option, $default_value = '' ) {
		$options = \get_option( $this->settings_key );

		if ( isset( $options[ $option ] ) ) {
			return $options[ $option ];
		}

		return $default_value;
	}

	/**
	 * Miscellaneous sanitize function
	 *
	 * @param mixed $value Setting Value.
	 * @return string Sanitized value.
	 */
	public function sanitize_missing( $value ) {
		return $value;
	}

	/**
	 * Sanitize text fields
	 *
	 * @param string $value The field value.
	 * @return string Sanitizied value
	 */
	public function sanitize_text_field( $value ) {
		return $this->sanitize_textarea_field( $value );
	}

	/**
	 * Sanitize number fields
	 *
	 * @param  string $value The field value.
	 * @return string Sanitized value
	 */
	public function sanitize_number_field( $value ) {
		return filter_var( $value, FILTER_SANITIZE_NUMBER_INT );
	}

	/**
	 * Sanitize CSV fields
	 *
	 * @param string $value The field value.
	 * @return string Sanitizied value
	 */
	public function sanitize_csv_field( $value ) {
		return implode( ',', array_map( 'trim', explode( ',', sanitize_text_field( wp_unslash( $value ) ) ) ) );
	}

	/**
	 * Sanitize CSV fields which hold numbers
	 *
	 * @param string $value The field value.
	 * @return string Sanitized value
	 */
	public function sanitize_numbercsv_field( $value ) {
		return implode( ',', array_filter( array_map( 'absint', explode( ',', sanitize_text_field( wp_unslash( $value ) ) ) ) ) );
	}

	/**
	 * Sanitize CSV fields which hold post IDs
	 *
	 * @param string $value The field value.
	 * @return string Sanitized value
	 */
	public function sanitize_postids_field( $value ) {
		$ids = array_filter( array_map( 'absint', explode( ',', sanitize_text_field( wp_unslash( $value ) ) ) ) );

		foreach ( $ids as $key => $value ) {
			if ( false === get_post_status( $value ) ) {
				unset( $ids[ $key ] );
			}
		}

		return implode( ',', $ids );
	}

	/**
	 * Sanitize textarea fields
	 *
	 * @param string $value The field value.
	 * @return string Sanitized value
	 */
	public function sanitize_textarea_field( $value ) {

		global $allowedposttags;

		// We need more tags to allow for script and style.
		$moretags = array(
			'script' => array(
				'type'    => true,
				'src'     => true,
				'async'   => true,
				'defer'   => true,
				'charset' => true,
			),
			'style'  => array(
				'type'   => true,
				'media'  => true,
				'scoped' => true,
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
		 * @param array $allowedtags Allowed tags array.
		 */
		$allowedtags = apply_filters( $this->prefix . '_sanitize_allowed_tags', $allowedtags );

		return wp_kses( wp_unslash( $value ), $allowedtags );
	}

	/**
	 * Sanitize checkbox fields
	 *
	 * @param mixed $value The field value.
	 * @return int  Sanitized value
	 */
	public function sanitize_checkbox_field( $value ) {
		$value = in_array( (int) $value, array( 0, -1 ), true ) ? 0 : 1;

		return $value;
	}

	/**
	 * Sanitize multicheck fields
	 *
	 * @param  array|int $value The field value.
	 * @return string  $value  Sanitized value
	 */
	public function sanitize_multicheck_field( $value ) {
		$values = ( -1 === (int) $value ) ? array() : array_map( 'sanitize_text_field', (array) wp_unslash( $value ) );

		return implode( ',', $values );
	}

	/**
	 * Sanitize post_types fields
	 *
	 * @param  array|int $value The field value.
	 * @return string  $value  Sanitized value
	 */
	public function sanitize_posttypes_field( $value ) {
		return $this->sanitize_multicheck_field( $value );
	}

	/**
	 * Sanitize post_types fields
	 *
	 * @param  array|int $value The field value.
	 * @return string  $value  Sanitized value
	 */
	public function sanitize_taxonomies_field( $value ) {
		return $this->sanitize_multicheck_field( $value );
	}

	/**
	 * Sanitize color fields.
	 *
	 * @param  string $value The field value.
	 * @return string Sanitized value
	 */
	public function sanitize_color_field( $value ) {
		return sanitize_hex_color( $value );
	}

	/**
	 * Sanitize email fields.
	 *
	 * @param  string $value The field value.
	 * @return string Sanitized value
	 */
	public function sanitize_email_field( $value ) {
		return sanitize_email( $value );
	}

	/**
	 * Sanitize URL fields.
	 *
	 * @param  string $value The field value.
	 * @return string Sanitized value
	 */
	public function sanitize_url_field( $value ) {
		return esc_url_raw( $value );
	}

	/**
	 * Sanitize sensitive fields.
	 *
	 * @param  string       $value The field value.
	 * @param  string|array $key   The field key.
	 * @return string Sanitized value
	 */
	public function sanitize_sensitive_field( $value, $key ) {
		if ( is_array( $key ) ) {
			if ( isset( $key['id'] ) ) {
				$key = $key['id'];
			} else {
				return $value;
			}
		}

		$stored_encrypted_key = $this->get_option( $key );

		// If input is masked, return existing encrypted key.
		if ( empty( $value ) || strpos( $value, '**' ) !== false ) {
			return $stored_encrypted_key;
		}

		return Settings_API::encrypt_api_key( $value );
	}

	/**
	 * Sanitize repeater field.
	 *
	 * @param array $value Array of repeater values.
	 * @param array $field Field configuration array.
	 * @return array Sanitized array
	 */
	public function sanitize_repeater_field( $value, $field = array() ) {
		if ( ! is_array( $value ) ) {
			return array();
		}

		$sanitized_value = array();

		// Get the subfields configuration.
		$subfields = ! empty( $field['fields'] ) ? $field['fields'] : array();

		foreach ( $value as $index => $row ) {
			// Ensure we have a valid row structure.
			if ( ! isset( $row['fields'] ) || ! is_array( $row['fields'] ) ) {
				continue;
			}

			$sanitized_row = array(
				'fields' => array(),
			);

			foreach ( $row['fields'] as $field_key => $field_value ) {
				$field_key = sanitize_key( $field_key );

				// Skip if field_key is not in our subfields configuration.
				$field_config = null;
				foreach ( $subfields as $subfield ) {
					if ( isset( $subfield['id'] ) && $subfield['id'] === $field_key ) {
						$field_config = $subfield;
						break;
					}
				}

				if ( null === $field_config ) {
					continue;
				}

				// Get the field type from the subfield configuration.
				$field_type = isset( $field_config['type'] ) ? $field_config['type'] : 'text';

				// Call the appropriate sanitization method.
				$sanitize_method = 'sanitize_' . $field_type . '_field';
				if ( method_exists( $this, $sanitize_method ) ) {
					$sanitized_row['fields'][ $field_key ] = $this->$sanitize_method( $field_value, $field_config );
				} else {
					$sanitized_row['fields'][ $field_key ] = $this->sanitize_text_field( $field_value );
				}
			}

			if ( ! empty( $sanitized_row['fields'] ) ) {
				$sanitized_value[ $index ] = $sanitized_row;
			}
		}

		return $sanitized_value;
	}

	/**
	 * Convert a string to CSV.
	 *
	 * @param array  $input_array Input string.
	 * @param string $delimiter Delimiter.
	 * @param string $enclosure Enclosure.
	 * @param string $terminator Terminating string.
	 * @return string CSV string.
	 */
	public static function str_putcsv( $input_array, $delimiter = ',', $enclosure = '"', $terminator = "\n" ) {
		// First convert associative array to numeric indexed array.
		$work_array = array();
		foreach ( $input_array as $key => $value ) {
			$work_array[] = $value;
		}

		$output     = '';
		$array_size = count( $work_array );

		for ( $i = 0; $i < $array_size; $i++ ) {
			// Nested array, process nest item.
			if ( is_array( $work_array[ $i ] ) ) {
				$output .= self::str_putcsv( $work_array[ $i ], $delimiter, $enclosure, $terminator );
			} else {
				switch ( gettype( $work_array[ $i ] ) ) {
					// Manually set some strings.
					case 'NULL':
						$sp_format = '';
						break;
					case 'boolean':
						$sp_format = ( true === $work_array[ $i ] ) ? 'true' : 'false';
						break;
					// Make sure sprintf has a good datatype to work with.
					case 'integer':
						$sp_format = '%i';
						break;
					case 'double':
						$sp_format = '%0.2f';
						break;
					case 'string':
						$sp_format        = '%s';
						$work_array[ $i ] = str_replace( "$enclosure", "$enclosure$enclosure", $work_array[ $i ] );
						break;
					// Unknown or invalid items for a csv - note: the datatype of array is already handled above, assuming the data is nested.
					case 'object':
					case 'resource':
					default:
						$sp_format = '';
						break;
				}
				$output .= sprintf( '%2$s' . $sp_format . '%2$s', $work_array[ $i ], $enclosure );
				$output .= ( $i < ( $array_size - 1 ) ) ? $delimiter : $terminator;
			}
		}

		return $output;
	}

	/**
	 * Processes category/taxonomy slugs and adds a new element to the settings array containing the term taxonomy IDs.
	 *
	 * @param array  $settings The settings array containing the taxonomy slugs to sanitize.
	 * @param string $source_key The key in the settings array containing the slugs. Pattern is Name (taxonomy:term_taxonomy_id).
	 * @param string $target_key The key in the settings array to store the sanitized term taxonomy IDs.
	 * @return void
	 */
	public static function sanitize_tax_slugs( &$settings, $source_key, $target_key ) {
		if ( isset( $settings[ $source_key ] ) ) {
			$slugs = array_unique( str_getcsv( $settings[ $source_key ], ',', '"', '' ) );

			foreach ( $slugs as $slug ) {
				// Pattern is Name (taxonomy:term_taxonomy_id).
				preg_match( '/(.*)\((.*):(\d+)\)/i', (string) $slug, $matches );
				if ( isset( $matches[3] ) ) {
					$term = get_term_by( 'term_taxonomy_id', $matches[3] );
				} else {
					// Fallback to fetching the category as this was the original format.
					$term = get_term_by( 'name', $slug, 'category' );
				}
				if ( isset( $term->term_taxonomy_id ) ) {
					$tax_ids[]   = $term->term_taxonomy_id;
					$tax_slugs[] = "{$term->name} ({$term->taxonomy}:{$term->term_taxonomy_id})";
				}
			}

			$settings[ $target_key ] = isset( $tax_ids ) ? join( ',', $tax_ids ) : '';
			$settings[ $source_key ] = isset( $tax_slugs ) ? self::str_putcsv( $tax_slugs ) : '';
		}
	}
}

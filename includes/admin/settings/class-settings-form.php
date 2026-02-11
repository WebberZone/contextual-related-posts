<?php
/**
 * Generates the settings form.
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
 * Generates the settings form.
 */
class Settings_Form {

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
	 * Translation strings.
	 *
	 * @var array Translation strings.
	 */
	public $translation_strings;

	/**
	 * Main constructor class.
	 *
	 * @param mixed $args {
	 *    Array or string of arguments. Default is blank array.
	 *     @type string  $settings_key        Settings key.
	 *     @type string  $prefix              Prefix.
	 *     @type array   $translation_strings Translation strings.
	 * }
	 */
	public function __construct( $args ) {
		$defaults = array(
			'settings_key'        => '',
			'prefix'              => '',
			'translation_strings' => array(),
		);
		$args     = wp_parse_args( $args, $defaults );

		foreach ( $args as $name => $value ) {
			$this->$name = $value;
		}
	}

	/**
	 * Get field description for display.
	 *
	 * @param array $args settings Arguments array.
	 *
	 * @return string Description of the field.
	 */
	public function get_field_description( $args ) {
		$desc = ! empty( $args['desc'] ) ? '<p class="description">' . wp_kses_post( $args['desc'] ) . '</p>' : '';

		/**
		 * After Settings Output filter
		 *
		 * @param string $desc Description of the field.
		 * @param array  $args Arguments array.
		 */
		$desc = apply_filters( $this->prefix . '_setting_field_description', $desc, $args ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound

		return $desc;
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
	 * Get field ID and name attributes.
	 *
	 * @param array $args Field arguments.
	 * @return array Array containing field_id and field_name.
	 */
	protected function get_field_attributes( $args ) {
		$id = sanitize_key( $args['id'] );
		if ( isset( $args['_repeater_id'] ) && isset( $args['_index'] ) ) {
			$field_id   = sprintf(
				'%s-%s-%s-fields-%s',
				$this->settings_key,
				$args['_repeater_id'],
				$args['_index'],
				$id
			);
			$field_name = sprintf(
				'%s[%s][%s][fields][%s]',
				$this->settings_key,
				$args['_repeater_id'],
				$args['_index'],
				$id
			);
		} else {
			$field_id   = $this->settings_key . '-' . $id;
			$field_name = $this->settings_key . '[' . $id . ']';
		}

		return array(
			'field_id'   => $field_id,
			'field_name' => $field_name,
		);
	}
	/**
	 * Miscellaneous callback funcion
	 *
	 * @param array $args Arguments array.
	 * @return void
	 */
	public function callback_missing( $args ) {
		/* translators: 1: Code. */
		printf( 'The callback function used for the %1$s setting is missing.', '<strong>' . esc_attr( $args['id'] ) . '</strong>' );
	}

	/**
	 * Header Callback
	 *
	 * Renders the header.
	 *
	 * @param array $args Arguments passed by the setting.
	 * @return void
	 */
	public function callback_header( $args ) {
		$html = $this->get_field_description( $args );

		/**
		 * After Settings Output filter
		 *
		 * @param string $html HTML string.
		 * @param array  $args Arguments array.
		 */
		echo apply_filters( $this->prefix . '_after_setting_output', $html, $args ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound,WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Descriptive text callback.
	 *
	 * Renders descriptive text onto the settings field.
	 *
	 * @param array $args Array of arguments.
	 * @return void
	 */
	public function callback_descriptive_text( $args ) {
		$this->callback_header( $args );
	}

	/**
	 * Display text fields.
	 *
	 * @param array $args Array of arguments.
	 */
	public function callback_text( $args ) {
		$value       = $args['value'] ?? $this->get_option( $args['id'], $args['default'] );
		$size        = sanitize_html_class( $args['size'] ?? 'regular' );
		$class       = sanitize_html_class( $args['field_class'] );
		$placeholder = empty( $args['placeholder'] ) ? '' : ' placeholder="' . $args['placeholder'] . '"';
		$disabled    = ( ! empty( $args['disabled'] ) || $args['pro'] ) ? ' disabled="disabled"' : '';
		$readonly    = ( isset( $args['readonly'] ) && true === $args['readonly'] ) ? ' readonly="readonly"' : '';
		$required    = ( isset( $args['required'] ) && true === $args['required'] ) ? ' required' : '';
		$attributes  = $disabled . $readonly . $required;

		foreach ( (array) $args['field_attributes'] as $attribute => $val ) {
			$attributes .= sprintf( ' %1$s="%2$s"', $attribute, esc_attr( $val ) );
		}

		$field_attributes = $this->get_field_attributes( $args );

		$html  = sprintf(
			'<input type="text" id="%1$s" name="%2$s" class="%3$s" value="%4$s" %5$s %6$s />',
			$field_attributes['field_id'],
			$field_attributes['field_name'],
			$class . ' ' . $size . '-text',
			esc_attr( stripslashes( $value ) ),
			$attributes,
			$placeholder
		);
		$html .= $this->get_field_description( $args );

		/** This filter has been defined in class-settings-api.php */
		echo apply_filters( $this->prefix . '_after_setting_output', $html, $args ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound,WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Display url fields.
	 *
	 * @param array $args Array of arguments.
	 */
	public function callback_url( $args ) {
		$this->callback_text( $args );
	}

	/**
	 * Display csv fields.
	 *
	 * @param array $args Array of arguments.
	 */
	public function callback_csv( $args ) {
		$this->callback_text( $args );
	}

	/**
	 * Display color fields.
	 *
	 * @param array $args Array of arguments.
	 */
	public function callback_color( $args ) {
		$this->callback_text( $args );
	}

	/**
	 * Display numbercsv fields.
	 *
	 * @param array $args Array of arguments.
	 */
	public function callback_numbercsv( $args ) {
		$this->callback_text( $args );
	}

	/**
	 * Display postids fields.
	 *
	 * @param array $args Array of arguments.
	 */
	public function callback_postids( $args ) {
		$this->callback_text( $args );
	}

	/**
	 * Display textarea.
	 *
	 * @param array $args Array of arguments.
	 * @return void
	 */
	public function callback_textarea( $args ) {

		$value       = $args['value'] ?? $this->get_option( $args['id'], $args['default'] );
		$class       = sanitize_html_class( $args['field_class'] );
		$placeholder = empty( $args['placeholder'] ) ? '' : ' placeholder="' . $args['placeholder'] . '"';
		$disabled    = ( ! empty( $args['disabled'] ) || $args['pro'] ) ? ' disabled="disabled"' : '';
		$readonly    = ( isset( $args['readonly'] ) && true === $args['readonly'] ) ? ' readonly="readonly"' : '';
		$required    = ( isset( $args['required'] ) && true === $args['required'] ) ? ' required' : '';
		$attributes  = $disabled . $readonly . $required;

		$field_attributes = $this->get_field_attributes( $args );

		$html  = sprintf(
			'<textarea class="%4$s" cols="50" rows="5" id="%1$s" name="%2$s" %5$s %6$s>%3$s</textarea>',
			$field_attributes['field_id'],
			$field_attributes['field_name'],
			esc_textarea( stripslashes( $value ) ),
			'large-text ' . $class,
			$attributes,
			$placeholder
		);
		$html .= $this->get_field_description( $args );

		/** This filter has been defined in class-settings-api.php */
		echo apply_filters( $this->prefix . '_after_setting_output', $html, $args ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound,WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Display CSS fields.
	 *
	 * @param array $args Array of arguments.
	 * @return void
	 */
	public function callback_css( $args ) {
		$this->callback_textarea( $args );
	}

	/**
	 * Display HTML fields.
	 *
	 * @param array $args Array of arguments.
	 * @return void
	 */
	public function callback_html( $args ) {
		$this->callback_textarea( $args );
	}

	/**
	 * Display checkboxes.
	 *
	 * @param array $args Array of arguments.
	 * @return void
	 */
	public function callback_checkbox( $args ) {

		$value    = $args['value'] ?? $this->get_option( $args['id'], $args['default'] );
		$checked  = ! empty( $value ) ? checked( 1, $value, false ) : '';
		$default  = isset( $args['default'] ) ? (int) $args['default'] : '';
		$disabled = ( ! empty( $args['disabled'] ) || $args['pro'] ) ? ' disabled="disabled"' : '';

		$field_attributes = $this->get_field_attributes( $args );

		$html              = sprintf(
			'<input type="hidden" name="%1$s" value="-1" />',
			$field_attributes['field_name']
		);
		$html             .= sprintf(
			'<input type="checkbox" id="%1$s" name="%2$s" value="1" %3$s %4$s />',
			$field_attributes['field_id'],
			$field_attributes['field_name'],
			$checked,
			$disabled
		);
		$checkbox_modified = $this->translation_strings['checkbox_modified'] ?? 'Modified from default setting';
		$html             .= ( (bool) $value !== (bool) $default ) ? '<em style="color:#9B0800">' . $checkbox_modified . '</em>' : '';
		$html             .= $this->get_field_description( $args );

		/** This filter has been defined in class-settings-api.php */
		echo apply_filters( $this->prefix . '_after_setting_output', $html, $args ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound,WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Multicheck Callback
	 *
	 * Renders multiple checkboxes.
	 *
	 * @param array $args Array of arguments.
	 * @return void
	 */
	public function callback_multicheck( $args ) {
		$html = '';

		$value       = $args['value'] ?? $this->get_option( $args['id'], $args['default'] );
		$value_array = wp_parse_list( $value );
		$disabled    = ( ! empty( $args['disabled'] ) || $args['pro'] ) ? ' disabled="disabled"' : '';

		$field_attributes = $this->get_field_attributes( $args );

		if ( ! empty( $args['options'] ) ) {
			$html .= sprintf(
				'<input type="hidden" name="%1$s" value="-1" />',
				$field_attributes['field_name']
			);

			foreach ( $args['options'] as $key => $option ) {
				if ( in_array( $key, $value_array, true ) ) {
					$enabled = $key;
				} else {
					$enabled = null;
				}

				$option_id   = $field_attributes['field_id'] . '-' . sanitize_key( $key );
				$option_name = $field_attributes['field_name'] . '[' . sanitize_key( $key ) . ']';

				$html .= sprintf(
					'<input name="%1$s" id="%2$s" type="checkbox" value="%3$s" %4$s %5$s /> ',
					$option_name,
					$option_id,
					esc_attr( $key ),
					checked( $key, $enabled, false ),
					$disabled
				);
				$html .= sprintf(
					'<label for="%1$s">%2$s</label> <br />',
					$option_id,
					$option
				);
			}
		}
		$html .= $this->get_field_description( $args );

		/** This filter has been defined in class-settings-api.php */
		echo apply_filters( $this->prefix . '_after_setting_output', $html, $args ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound,WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Radio Callback
	 *
	 * Renders radio boxes.
	 *
	 * @param array $args Array of arguments.
	 * @return void
	 */
	public function callback_radio( $args ) {
		$html = '';

		$value    = $args['value'] ?? $this->get_option( $args['id'], $args['default'] );
		$disabled = ( ! empty( $args['disabled'] ) || $args['pro'] ) ? ' disabled="disabled"' : '';

		$field_attributes = $this->get_field_attributes( $args );

		foreach ( $args['options'] as $key => $option ) {
			$option_id = $field_attributes['field_id'] . '-' . $key;

			$html .= sprintf(
				'<input name="%1$s" id="%2$s" type="radio" value="%3$s" %4$s %5$s /> ',
				$field_attributes['field_name'],
				$option_id,
				$key,
				checked( $value, $key, false ),
				$disabled
			);
			$html .= sprintf(
				'<label for="%1$s">%2$s</label> <br />',
				$option_id,
				$option
			);
		}

		$html .= $this->get_field_description( $args );

		/** This filter has been defined in class-settings-api.php */
		echo apply_filters( $this->prefix . '_after_setting_output', $html, $args ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound,WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Radio callback with description.
	 *
	 * Renders radio boxes with each item having it separate description.
	 *
	 * @param array $args Array of arguments.
	 * @return void
	 */
	public function callback_radiodesc( $args ) {
		$html = '';

		$value    = $args['value'] ?? $this->get_option( $args['id'], $args['default'] );
		$disabled = ( ! empty( $args['disabled'] ) || $args['pro'] ) ? ' disabled="disabled"' : '';

		$field_attributes = $this->get_field_attributes( $args );

		foreach ( $args['options'] as $option ) {
			$option_id = $field_attributes['field_id'] . '-' . $option['id'];

			$html .= sprintf(
				'<input name="%1$s" id="%2$s" type="radio" value="%3$s" %4$s %5$s /> ',
				$field_attributes['field_name'],
				$option_id,
				$option['id'],
				checked( $value, $option['id'], false ),
				$disabled
			);
			$html .= sprintf(
				'<label for="%1$s">%2$s: <em>%3$s</em></label>',
				$option_id,
				$option['name'],
				wp_kses_post( $option['description'] )
			);

			$html .= '<br />';
		}

		$html .= $this->get_field_description( $args );

		/** This filter has been defined in class-settings-api.php */
		echo apply_filters( $this->prefix . '_after_setting_output', $html, $args ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound,WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Radio callback with description.
	 *
	 * Renders radio boxes with each item having it separate description.
	 *
	 * @param array $args Array of arguments.
	 * @return void
	 */
	public function callback_thumbsizes( $args ) {
		$html = '';

		$thumb_size = $this->prefix . '_thumbnail';

		if ( ! isset( $args['options'][ $thumb_size ] ) ) {
			$args['options'][ $thumb_size ] = array(
				'name'   => $thumb_size,
				'width'  => call_user_func_array( $this->prefix . '_get_option', array( 'thumb_width', 150 ) ),
				'height' => call_user_func_array( $this->prefix . '_get_option', array( 'thumb_height', 150 ) ),
				'crop'   => call_user_func_array( $this->prefix . '_get_option', array( 'thumb_crop', true ) ),
			);
		}

		$value = $args['value'] ?? $this->get_option( $args['id'], $args['default'] );

		$field_attributes = $this->get_field_attributes( $args );

		foreach ( $args['options'] as $name => $option ) {
			$option_id = $field_attributes['field_id'] . '-' . $name;

			$html .= sprintf(
				'<input name="%1$s" id="%2$s" type="radio" value="%3$s" %4$s /> ',
				$field_attributes['field_name'],
				$option_id,
				$name,
				checked( $value, $name, false )
			);
			$html .= sprintf(
				'<label for="%1$s">%2$s (%3$sx%4$s%5$s)</label> <br />',
				$option_id,
				$name,
				(int) $option['width'],
				(int) $option['height'],
				(bool) $option['crop'] ? ' cropped' : ''
			);
		}

		$html .= $this->get_field_description( $args );

		/** This filter has been defined in class-settings-api.php */
		echo apply_filters( $this->prefix . '_after_setting_output', $html, $args ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound,WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Number Callback
	 *
	 * Renders number fields.
	 *
	 * @param array $args Array of arguments.
	 * @return void
	 */
	public function callback_number( $args ) {
		$value       = $args['value'] ?? $this->get_option( $args['id'], $args['default'] );
		$max         = isset( $args['max'] ) ? intval( $args['max'] ) : 999999;
		$min         = isset( $args['min'] ) ? intval( $args['min'] ) : 0;
		$step        = isset( $args['step'] ) ? intval( $args['step'] ) : 1;
		$size        = $args['size'] ?? 'regular';
		$placeholder = empty( $args['placeholder'] ) ? '' : ' placeholder="' . esc_attr( $args['placeholder'] ) . '"';
		$disabled    = ( ! empty( $args['disabled'] ) || $args['pro'] ) ? ' disabled="disabled"' : '';
		$readonly    = ( isset( $args['readonly'] ) && true === $args['readonly'] ) ? ' readonly="readonly"' : '';
		$required    = ( isset( $args['required'] ) && true === $args['required'] ) ? ' required' : '';
		$attributes  = $disabled . $readonly . $required;

		$field_attributes = $this->get_field_attributes( $args );

		$html  = sprintf(
			'<input type="number" step="%1$s" max="%2$s" min="%3$s" class="%4$s" id="%5$s" name="%6$s" value="%7$s" %8$s %9$s />',
			esc_attr( (string) $step ),
			esc_attr( (string) $max ),
			esc_attr( (string) $min ),
			sanitize_html_class( $size ) . '-text',
			$field_attributes['field_id'],
			$field_attributes['field_name'],
			esc_attr( stripslashes( $value ) ),
			$placeholder,
			$attributes
		);
		$html .= $this->get_field_description( $args );

		/** This filter has been defined in class-settings-api.php */
		echo apply_filters( $this->prefix . '_after_setting_output', $html, $args ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound,WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Select Callback
	 *
	 * Renders select fields.
	 *
	 * @param array $args Array of arguments.
	 * @return void
	 */
	public function callback_select( $args ) {
		$value      = $args['value'] ?? $this->get_option( $args['id'], $args['default'] );
		$class      = sanitize_html_class( $args['field_class'] );
		$disabled   = ( ! empty( $args['disabled'] ) || $args['pro'] ) ? ' disabled="disabled"' : '';
		$required   = ( isset( $args['required'] ) && true === $args['required'] ) ? ' required' : '';
		$attributes = $disabled . $required;

		foreach ( (array) $args['field_attributes'] as $attribute => $val ) {
			$attributes .= sprintf( ' %1$s="%2$s"', $attribute, esc_attr( $val ) );
		}

		if ( isset( $args['chosen'] ) ) {
			$class .= ' chosen';
		}

		$field_attributes = $this->get_field_attributes( $args );

		$html = sprintf(
			'<select id="%1$s" name="%2$s" class="%3$s" %4$s />',
			$field_attributes['field_id'],
			$field_attributes['field_name'],
			$class,
			$attributes
		);

		foreach ( (array) $args['options'] as $option => $name ) {
			$html .= sprintf( '<option value="%1$s" %2$s>%3$s</option>', sanitize_key( $option ), selected( $option, $value, false ), $name );
		}

		$html .= '</select>';
		$html .= $this->get_field_description( $args );

		/** This filter has been defined in class-settings-api.php */
		echo apply_filters( $this->prefix . '_after_setting_output', $html, $args ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound,WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Display posttypes fields.
	 *
	 * @param array $args Array of arguments.
	 * @return void
	 */
	public function callback_posttypes( $args ) {
		$html = '';

		$options  = $args['value'] ?? $this->get_option( $args['id'], $args['default'] );
		$disabled = ( ! empty( $args['disabled'] ) || $args['pro'] ) ? ' disabled="disabled"' : '';

		// If post_types contains a query string then parse it with wp_parse_args.
		if ( is_string( $options ) && strpos( $options, '=' ) ) {
			$post_types = wp_parse_args( $options );
		} else {
			$post_types = wp_parse_list( $options );
		}

		$wp_post_types = get_post_types(
			array(
				'public' => true,
			),
			'objects'
		);

		$posts_types_inc = array_intersect( wp_list_pluck( $wp_post_types, 'name' ), $post_types );

		$field_attributes = $this->get_field_attributes( $args );

		$html .= sprintf(
			'<input type="hidden" name="%1$s" value="-1" />',
			$field_attributes['field_name']
		);

		foreach ( $wp_post_types as $wp_post_type ) {
			$option_id   = $field_attributes['field_id'] . '-' . esc_attr( $wp_post_type->name );
			$option_name = $field_attributes['field_name'] . '[' . esc_attr( $wp_post_type->name ) . ']';

			$html .= sprintf(
				'<label for="%1$s"><input name="%2$s" id="%1$s" type="checkbox" value="%3$s" %4$s %5$s /> %6$s</label><br />',
				$option_id,
				$option_name,
				esc_attr( $wp_post_type->name ),
				checked( true, in_array( $wp_post_type->name, $posts_types_inc, true ), false ),
				$disabled,
				$wp_post_type->label
			);

		}

		$html .= $this->get_field_description( $args );

		/** This filter has been defined in class-settings-api.php */
		echo apply_filters( $this->prefix . '_after_setting_output', $html, $args ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound,WordPress.Security.EscapeOutput.OutputNotEscaped
	}


	/**
	 * Display taxonomies fields.
	 *
	 * @param array $args Array of arguments.
	 * @return void
	 */
	public function callback_taxonomies( $args ) {
		$html = '';

		$options = $args['value'] ?? $this->get_option( $args['id'], $args['default'] );

		// If taxonomies contains a query string then parse it with wp_parse_args.
		if ( is_string( $options ) && strpos( $options, '=' ) ) {
			$taxonomies = wp_parse_args( $options );
		} else {
			$taxonomies = wp_parse_list( $options );
		}

		/* Fetch taxonomies */
		$argsc         = array(
			'public' => true,
		);
		$output        = 'objects';
		$operator      = 'and';
		$wp_taxonomies = get_taxonomies( $argsc, $output, $operator );

		$taxonomies_inc = array_intersect( wp_list_pluck( (array) $wp_taxonomies, 'name' ), $taxonomies );

		$field_attributes = $this->get_field_attributes( $args );

		$html .= sprintf( '<input type="hidden" name="%1$s" value="-1" />', $field_attributes['field_name'] );

		foreach ( $wp_taxonomies as $wp_taxonomy ) {
			$option_id   = $field_attributes['field_id'] . '-' . esc_attr( $wp_taxonomy->name );
			$option_name = $field_attributes['field_name'] . '[' . esc_attr( $wp_taxonomy->name ) . ']';

			$html .= sprintf(
				'<label for="%1$s"><input name="%2$s" id="%1$s" type="checkbox" value="%3$s" %4$s /> %5$s (%3$s)</label><br />',
				$option_id,
				$option_name,
				esc_attr( $wp_taxonomy->name ),
				checked( true, in_array( $wp_taxonomy->name, $taxonomies_inc, true ), false ),
				$wp_taxonomy->labels->name
			);

		}

		$html .= $this->get_field_description( $args );

		/** This filter has been defined in class-settings-api.php */
		echo apply_filters( $this->prefix . '_after_setting_output', $html, $args ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound,WordPress.Security.EscapeOutput.OutputNotEscaped
	}


	/**
	 * Displays a rich text textarea for a settings field.
	 *
	 * @param array $args Array of arguments.
	 */
	public function callback_wysiwyg( $args ) {

		$value = $args['value'] ?? $this->get_option( $args['id'], $args['default'] );
		$size  = $args['size'] ?? '500px';

		$field_attributes = $this->get_field_attributes( $args );

		// wp_editor requires a unique ID without brackets.
		$editor_id = sanitize_key( str_replace( array( '[', ']' ), array( '-', '' ), $field_attributes['field_id'] ) );

		printf( '<div style="max-width: %1$s;">', esc_attr( $size ) );

		$editor_settings = array(
			'teeny'         => true,
			'textarea_name' => $field_attributes['field_name'],
			'textarea_rows' => 10,
		);

		if ( isset( $args['options'] ) && is_array( $args['options'] ) ) {
			$editor_settings = array_merge( $editor_settings, $args['options'] );
		}

		wp_editor( $value, $editor_id, $editor_settings );

		printf( '</div>' );

		echo $this->get_field_description( $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Displays a file upload field for a settings field.
	 *
	 * @param array $args Array of arguments.
	 */
	public function callback_file( $args ) {

		$value = $args['value'] ?? $this->get_option( $args['id'], $args['default'] );
		$size  = sanitize_html_class( $args['size'] ?? 'regular' );
		$class = sanitize_html_class( $args['field_class'] );
		$label = $args['options']['button_label'] ?? $this->translation_strings['button_label'];

		$field_attributes = $this->get_field_attributes( $args );

		$html  = sprintf(
			'<input type="text" class="%1$s" id="%2$s" name="%3$s" value="%4$s"/>',
			$class . ' ' . $size . '-text file-url',
			$field_attributes['field_id'],
			$field_attributes['field_name'],
			esc_attr( $value )
		);
		$html .= sprintf( '<input type="button" class="button button-secondary file-browser" value="%s" />', $label );
		$html .= $this->get_field_description( $args );

		/** This filter has been defined in class-settings-api.php */
		echo apply_filters( $this->prefix . '_after_setting_output', $html, $args ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound,WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Displays a password field for a settings field.
	 *
	 * @param array $args Array of arguments.
	 */
	public function callback_password( $args ) {

		$value = $args['value'] ?? $this->get_option( $args['id'], $args['default'] );
		$size  = sanitize_html_class( $args['size'] ?? 'regular' );
		$class = sanitize_html_class( $args['field_class'] );

		$field_attributes = $this->get_field_attributes( $args );

		$html  = sprintf(
			'<input type="password" class="%1$s" id="%2$s" name="%3$s" value="%4$s" %5$s />',
			"$class $size-text",
			$field_attributes['field_id'],
			$field_attributes['field_name'],
			esc_attr( $value ),
			! empty( $value ) ? 'placeholder="' . esc_attr( $this->translation_strings['previous_saved'] ) . '"' : ''
		);
		$html .= $this->get_field_description( $args );

		/** This filter has been defined in class-settings-api.php */
		echo apply_filters( $this->prefix . '_after_setting_output', $html, $args ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound,WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Callback for repeater field.
	 *
	 * @param array $args Array of arguments.
	 * @return void
	 */
	public function callback_repeater( $args ) {
		$value = isset( $args['value'] ) ? (array) $args['value'] : $this->get_option( $args['id'], array() );
		$value = ! empty( $value ) && is_array( $value ) ? $value : array();

		$class      = ! empty( $args['field_class'] ) ? sanitize_html_class( $args['field_class'] ) : '';
		$disabled   = ( ! empty( $args['disabled'] ) || ! empty( $args['pro'] ) ) ? ' disabled="disabled"' : '';
		$readonly   = ( isset( $args['readonly'] ) && true === $args['readonly'] ) ? ' readonly="readonly"' : '';
		$attributes = $disabled . $readonly;

		// Process additional field attributes.
		foreach ( (array) $args['field_attributes'] as $attribute => $val ) {
			$attributes .= sprintf( ' %1$s="%2$s"', sanitize_key( $attribute ), esc_attr( $val ) );
		}

		ob_start();
		?>
		<div class="<?php echo esc_attr( $class ); ?> wz-repeater-wrapper" id="<?php echo esc_attr( $args['id'] ); ?>-wrapper" <?php echo $attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<div class="<?php echo esc_attr( $args['id'] ); ?>-items">
				<?php
				if ( ! empty( $value ) ) {
					foreach ( array_values( $value ) as $index => $item ) {
						$this->render_repeater_item( $args, $index, $item );
					}
				}
				?>
			</div>
			<button type="button" class="button add-item" data-target="<?php echo esc_attr( $args['id'] ); ?>">
				<?php echo esc_html( ! empty( $args['add_button_text'] ) ? $args['add_button_text'] : 'Add Item' ); ?>
			</button>

			<script type="text/template" class="repeater-template" data-id="<?php echo esc_attr( $args['id'] ); ?>">
				<?php $this->render_repeater_item( $args, '{{INDEX}}' ); ?>
			</script>
		</div>

		<script>
		jQuery(document).ready(function($) {
			var wrapper = $('#<?php echo esc_js( $args['id'] ); ?>-wrapper');
			var itemsContainer = wrapper.find('.<?php echo esc_js( $args['id'] ); ?>-items');
			var index = <?php echo esc_js( (string) count( $value ) ); ?>;

			// Add Item
			wrapper.on('click', '.add-item', function() {
				var template = wrapper.find('.repeater-template').html();
				template = template.replace(/{{INDEX}}/g, index);
				itemsContainer.append(template);
				index++;

				// Ensure the toggle icon for the new item is set to the collapsed state (▲)
				itemsContainer.find('.repeater-item-header:last .toggle-icon').text('▲');

				// Ensure that .repeater-item-content is set to display:block
				itemsContainer.find('.repeater-item-content:last').css('display', 'block');
			});

			// Remove Item
			wrapper.on('click', '.remove-item', function() {
				$(this).closest('.wz-repeater-item').remove();
				reindexItems();
			});

			// Move Up
			wrapper.on('click', '.move-up', function() {
				var item = $(this).closest('.wz-repeater-item');
				var prev = item.prev();
				if (prev.length) {
					item.insertBefore(prev);
					reindexItems();
				}
			});

			// Move Down
			wrapper.on('click', '.move-down', function() {
				var item = $(this).closest('.wz-repeater-item');
				var next = item.next();
				if (next.length) {
					item.insertAfter(next);
					reindexItems();
				}
			});

			// Toggle Accordion
			wrapper.on('click', '.repeater-item-header', function() {
				var $this = $(this);
				var $toggleIcon = $this.find('.toggle-icon');
				var $content = $this.next('.repeater-item-content');

				// Check if content is currently visible or hidden, and toggle accordingly
				if ($content.is(':visible')) {
					$content.slideUp();
					$toggleIcon.text('▼');  // Expanded state
				} else {
					$content.slideDown();
					$toggleIcon.text('▲');  // Collapsed state
				}
			});

			// Reindex Items After Adding, Removing, or Moving
			function reindexItems() {
				itemsContainer.find('.wz-repeater-item').each(function(idx) {
					$(this).find(':input').each(function() {
						var name = $(this).attr('name');
						if (name) {
							name = name.replace(/\[\d+\]/, '[' + idx + ']');
							$(this).attr('name', name);
						}
					});
				});
			}
		});
		</script>
		<?php
		$html  = ob_get_clean();
		$html .= $this->get_field_description( $args );

		/** This filter has been defined in class-settings-api.php */
		echo apply_filters( $this->prefix . '_after_setting_output', $html, $args ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound,WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Render a single repeater item.
	 *
	 * @param array      $args  Repeater field arguments.
	 * @param string|int $index Current item index.
	 * @param array|null $item  Item data if exists.
	 * @return void
	 */
	private function render_repeater_item( $args, $index, $item = null ) {
		if ( empty( $args['fields'] ) || ! is_array( $args['fields'] ) ) {
			return;
		}

		?>
	<div class="wz-repeater-item">
		<div class="repeater-item-header">
			<?php
			$display_field = ! empty( $args['live_update_field'] ) ? $args['live_update_field'] : 'name';
			?>
			<span class="repeater-title"><?php echo esc_html( ! empty( $item['fields'][ $display_field ] ) ? $item['fields'][ $display_field ] : $this->translation_strings['repeater_new_item'] ); ?></span>
			<span class="toggle-icon">▼</span>
		</div>
		<div class="repeater-item-content" style="display: none;">
			<?php
			foreach ( $args['fields'] as $field ) {
				$field_id = sanitize_key( $field['id'] );

				$field_args = array_merge(
					(array) $field,
					array(
						'value'        => isset( $item['fields'][ $field_id ] ) ? $item['fields'][ $field_id ] : ( isset( $field['default'] ) ? $field['default'] : '' ),
						'_repeater_id' => $args['id'],
						'_index'       => $index,
					)
				);
				$field_args = Settings_API::parse_field_args( $field_args, $args['section'] );

				if ( ! isset( $field['type'] ) || ! is_string( $field['type'] ) ) {
					continue;
				}
				?>
				<?php $repeater_field_attributes = $this->get_field_attributes( $field_args ); ?>
				<div class="wz-repeater-field">
					<div class="wz-repeater-field-header">
						<label class="wz-repeater-field-label" for="<?php echo esc_attr( $repeater_field_attributes['field_id'] ); ?>">
							<?php echo esc_html( $field['name'] ); ?>
							<?php if ( ! empty( $field['required'] ) ) : ?>
								<span class="required" title="<?php echo esc_attr( $this->translation_strings['required_label'] ); ?>">*</span>
							<?php endif; ?>
						</label>
					</div>

					<div class="wz-repeater-field-input">
						<?php
						$callback = 'callback_' . $field['type'];

						if ( method_exists( $this, $callback ) ) {
							$this->$callback( $field_args );
						} else {
							do_action( "{$this->prefix}_repeater_field_{$field['type']}", $field_args, $index ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
						}
						?>
					</div>
				</div>
			<?php } ?>
		</div>

		<div class="repeater-item-footer">
			<div class="repeater-item-actions">
				<button type="button" class="button button-secondary move-up">
					<span class="dashicons dashicons-arrow-up-alt2"></span>
				</button>
				<button type="button" class="button button-secondary move-down">
					<span class="dashicons dashicons-arrow-down-alt2"></span>
				</button>
				<button type="button" class="button button-secondary remove-item">
					<span class="dashicons dashicons-trash"></span>
				</button>
			</div>
		</div>
	</div>

	<script>
	jQuery(document).ready(function($) {
		var wrapper = $('#<?php echo esc_js( $args['id'] ); ?>-wrapper');
		var itemsContainer = wrapper.find('.<?php echo esc_js( $args['id'] ); ?>-items');

		// Live update repeater title when the specified field changes
		var liveUpdateField = '<?php echo esc_js( ! empty( $args['live_update_field'] ) ? $args['live_update_field'] : 'name' ); ?>';
		wrapper.on('input', '.wz-repeater-item input[name$="[fields][' + liveUpdateField + ']"]', function() {
			var $this = $(this);
			var newName = $this.val();
			var $repeaterTitle = $this.closest('.wz-repeater-item').find('.repeater-title');
			$repeaterTitle.text(newName || '<?php echo esc_js( $this->translation_strings['repeater_new_item'] ); ?>'); // Update title or set default if empty
		});
	});
	</script>
		<?php
	}



	/**
	 * Display sensitive fields.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Array of arguments.
	 */
	public function callback_sensitive( $args ) {
		$encrypted_key = $args['value'] ?? $this->get_option( $args['id'], $args['default'] );
		$decrypted_key = Settings_API::decrypt_api_key( $encrypted_key );

		$args['value'] = $decrypted_key ? str_repeat( '*', strlen( $decrypted_key ) - 4 ) . substr( $decrypted_key, -4 ) : '';

		$this->callback_text( $args );
	}
}

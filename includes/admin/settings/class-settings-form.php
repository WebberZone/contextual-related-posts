<?php
/**
 * Generates the settings form.
 *
 * @link  https://webberzone.com
 * @since 2.0.0
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
	 * Text to show to indicate a checkbox has been modified from its default value.
	 *
	 * @var string Checkbox Modified Text.
	 */
	public $checkbox_modified_text;

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
			'settings_key'           => '',
			'prefix'                 => '',
			'checkbox_modified_text' => '',
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
		if ( ! empty( $args['desc'] ) ) {
			$desc = '<p class="description">' . wp_kses_post( $args['desc'] ) . '</p>';
		} else {
			$desc = '';
		}

		/**
		 * After Settings Output filter
		 *
		 * @param string $desc Description of the field.
		 * @param array  $args Arguments array.
		 */
		$desc = apply_filters( $this->prefix . '_setting_field_description', $desc, $args );
		return $desc;
	}

	/**
	 * Get the value of a settings field.
	 *
	 * @param string $option  Settings field name.
	 * @param string $default_value Default text if it's not found.
	 * @return string
	 */
	public function get_option( $option, $default_value = '' ) {

		$options = \get_option( $this->settings_key );

		if ( isset( $options[ $option ] ) ) {
			return $options[ $option ];
		}

		return $default_value;
	}

	/**
	 * Miscellaneous callback funcion
	 *
	 * @param array $args Arguments array.
	 * @return void
	 */
	public function callback_missing( $args ) {
		/* translators: 1: Code. */
		printf(
			'The callback function used for the %1$s setting is missing.',
			'<strong>' . esc_attr( $args['id'] ) . '</strong>'
		);
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
		echo apply_filters( $this->prefix . '_after_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
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
		$attributes  = $disabled . $readonly;

		foreach ( (array) $args['field_attributes'] as $attribute => $val ) {
			$attributes .= sprintf( ' %1$s="%2$s"', $attribute, esc_attr( $val ) );
		}

		$html  = sprintf(
			'<input type="text" id="%1$s[%2$s]" name="%1$s[%2$s]" class="%3$s" value="%4$s" %5$s %6$s />',
			$this->settings_key,
			sanitize_key( $args['id'] ),
			$class . ' ' . $size . '-text',
			esc_attr( stripslashes( $value ) ),
			$attributes,
			$placeholder
		);
		$html .= $this->get_field_description( $args );

		/** This filter has been defined in class-settings-api.php */
		echo apply_filters( $this->prefix . '_after_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
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
		$attributes  = $disabled . $readonly;

		$html  = sprintf(
			'<textarea class="%4$s" cols="50" rows="5" id="%1$s[%2$s]" name="%1$s[%2$s]" %5$s %6$s>%3$s</textarea>',
			$this->settings_key,
			sanitize_key( $args['id'] ),
			esc_textarea( stripslashes( $value ) ),
			'large-text ' . $class,
			$attributes,
			$placeholder
		);
		$html .= $this->get_field_description( $args );

		/** This filter has been defined in class-settings-api.php */
		echo apply_filters( $this->prefix . '_after_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
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

		$html  = sprintf(
			'<input type="hidden" name="%1$s[%2$s]" value="-1" />',
			$this->settings_key,
			sanitize_key( $args['id'] )
		);
		$html .= sprintf(
			'<input type="checkbox" id="%1$s[%2$s]" name="%1$s[%2$s]" value="1" %3$s %4$s />',
			$this->settings_key,
			sanitize_key( $args['id'] ),
			$checked,
			$disabled
		);
		$html .= ( (bool) $value !== (bool) $default ) ? '<em style="color:#9B0800">' . $this->checkbox_modified_text . '</em>' : '';
		$html .= $this->get_field_description( $args );

		/** This filter has been defined in class-settings-api.php */
		echo apply_filters( $this->prefix . '_after_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
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

		if ( ! empty( $args['options'] ) ) {
			$html .= sprintf( '<input type="hidden" name="%1$s[%2$s]" value="-1" />', $this->settings_key, sanitize_key( $args['id'] ) );

			foreach ( $args['options'] as $key => $option ) {
				if ( in_array( $key, $value_array, true ) ) {
					$enabled = $key;
				} else {
					$enabled = null;
				}

				$html .= sprintf(
					'<input name="%1$s[%2$s][%3$s]" id="%1$s[%2$s][%3$s]" type="checkbox" value="%4$s" %5$s %6$s /> ',
					$this->settings_key,
					sanitize_key( $args['id'] ),
					sanitize_key( $key ),
					esc_attr( $key ),
					checked( $key, $enabled, false ),
					$disabled
				);
				$html .= sprintf(
					'<label for="%1$s[%2$s][%3$s]">%4$s</label> <br />',
					$this->settings_key,
					sanitize_key( $args['id'] ),
					sanitize_key( $key ),
					$option
				);
			}
		}
		$html .= $this->get_field_description( $args );

		/** This filter has been defined in class-settings-api.php */
		echo apply_filters( $this->prefix . '_after_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
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

		foreach ( $args['options'] as $key => $option ) {
			$html .= sprintf(
				'<input name="%1$s[%2$s]" id="%1$s[%2$s][%3$s]" type="radio" value="%3$s" %4$s %5$s /> ',
				$this->settings_key,
				sanitize_key( $args['id'] ),
				$key,
				checked( $value, $key, false ),
				$disabled
			);
			$html .= sprintf(
				'<label for="%1$s[%2$s][%3$s]">%4$s</label> <br />',
				$this->settings_key,
				sanitize_key( $args['id'] ),
				$key,
				$option
			);
		}

		$html .= $this->get_field_description( $args );

		/** This filter has been defined in class-settings-api.php */
		echo apply_filters( $this->prefix . '_after_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
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

		foreach ( $args['options'] as $option ) {
			$html .= sprintf(
				'<input name="%1$s[%2$s]" id="%1$s[%2$s][%3$s]" type="radio" value="%3$s" %4$s %5$s /> ',
				$this->settings_key,
				sanitize_key( $args['id'] ),
				$option['id'],
				checked( $value, $option['id'], false ),
				$disabled
			);
			$html .= sprintf(
				'<label for="%1$s[%2$s][%3$s]">%4$s: <em>%5$s</em></label>',
				$this->settings_key,
				sanitize_key( $args['id'] ),
				$option['id'],
				$option['name'],
				wp_kses_post( $option['description'] )
			);

			$html .= '<br />';
		}

		$html .= $this->get_field_description( $args );

		/** This filter has been defined in class-settings-api.php */
		echo apply_filters( $this->prefix . '_after_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
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

		foreach ( $args['options'] as $name => $option ) {
			$html .= sprintf(
				'<input name="%1$s[%2$s]" id="%1$s[%2$s][%3$s]" type="radio" value="%3$s" %4$s /> ',
				$this->settings_key,
				sanitize_key( $args['id'] ),
				$name,
				checked( $value, $name, false )
			);
			$html .= sprintf(
				'<label for="%1$s[%2$s][%3$s]">%3$s (%4$sx%5$s%6$s)</label> <br />',
				$this->settings_key,
				sanitize_key( $args['id'] ),
				$name,
				(int) $option['width'],
				(int) $option['height'],
				(bool) $option['crop'] ? ' cropped' : ''
			);
		}

		$html .= $this->get_field_description( $args );

		/** This filter has been defined in class-settings-api.php */
		echo apply_filters( $this->prefix . '_after_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
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

		$html  = sprintf(
			'<input type="number" step="%1$s" max="%2$s" min="%3$s" class="%4$s" id="%8$s[%5$s]" name="%8$s[%5$s]" value="%6$s" %7$s %9$s />',
			esc_attr( (string) $step ),
			esc_attr( (string) $max ),
			esc_attr( (string) $min ),
			sanitize_html_class( $size ) . '-text',
			sanitize_key( $args['id'] ),
			esc_attr( stripslashes( $value ) ),
			$placeholder,
			$this->settings_key,
			$disabled
		);
		$html .= $this->get_field_description( $args );

		/** This filter has been defined in class-settings-api.php */
		echo apply_filters( $this->prefix . '_after_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
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
		$value    = $args['value'] ?? $this->get_option( $args['id'], $args['default'] );
		$disabled = ( ! empty( $args['disabled'] ) || $args['pro'] ) ? ' disabled="disabled"' : '';

		if ( isset( $args['chosen'] ) ) {
			$chosen = 'class="chosen"';
		} else {
			$chosen = '';
		}

		$html = sprintf(
			'<select id="%1$s[%2$s]" name="%1$s[%2$s]" %3$s %4$s />',
			$this->settings_key,
			sanitize_key( $args['id'] ),
			$chosen,
			$disabled
		);

		foreach ( $args['options'] as $option => $name ) {
			$html .= sprintf( '<option value="%1$s" %2$s>%3$s</option>', sanitize_key( $option ), selected( $option, $value, false ), $name );
		}

		$html .= '</select>';
		$html .= $this->get_field_description( $args );

		/** This filter has been defined in class-settings-api.php */
		echo apply_filters( $this->prefix . '_after_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
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

		$html .= sprintf(
			'<input type="hidden" name="%1$s[%2$s]" value="-1" />',
			$this->settings_key,
			sanitize_key( $args['id'] )
		);

		foreach ( $wp_post_types as $wp_post_type ) {

			$html .= sprintf(
				'<label for="%4$s[%1$s][%2$s]"><input name="%4$s[%1$s][%2$s]" id="%4$s[%1$s][%2$s]" type="checkbox" value="%2$s" %3$s %6$s /> %5$s</label><br />',
				sanitize_key( $args['id'] ),
				esc_attr( $wp_post_type->name ),
				checked( true, in_array( $wp_post_type->name, $posts_types_inc, true ), false ),
				$this->settings_key,
				$wp_post_type->label,
				$disabled
			);

		}

		$html .= $this->get_field_description( $args );

		/** This filter has been defined in class-settings-api.php */
		echo apply_filters( $this->prefix . '_after_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
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

		$html .= sprintf( '<input type="hidden" name="%1$s[%2$s]" value="-1" />', $this->settings_key, sanitize_key( $args['id'] ) );

		foreach ( $wp_taxonomies as $wp_taxonomy ) {

			$html .= sprintf(
				'<label for="%4$s[%1$s][%2$s]"><input name="%4$s[%1$s][%2$s]" id="%4$s[%1$s][%2$s]" type="checkbox" value="%2$s" %3$s /> %5$s (%2$s)</label><br />',
				sanitize_key( $args['id'] ),
				esc_attr( $wp_taxonomy->name ),
				checked( true, in_array( $wp_taxonomy->name, $taxonomies_inc, true ), false ),
				$this->settings_key,
				$wp_taxonomy->labels->name
			);

		}

		$html .= $this->get_field_description( $args );

		/** This filter has been defined in class-settings-api.php */
		echo apply_filters( $this->prefix . '_after_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}


	/**
	 * Displays a rich text textarea for a settings field.
	 *
	 * @param array $args Array of arguments.
	 */
	public function callback_wysiwyg( $args ) {

		$value = $args['value'] ?? $this->get_option( $args['id'], $args['default'] );
		$size  = $args['size'] ?? '500px';

		echo '<div style="max-width: ' . esc_attr( $size ) . ';">';

		$editor_settings = array(
			'teeny'         => true,
			'textarea_name' => $args['section'] . '[' . $args['id'] . ']',
			'textarea_rows' => 10,
		);

		if ( isset( $args['options'] ) && is_array( $args['options'] ) ) {
			$editor_settings = array_merge( $editor_settings, $args['options'] );
		}

		wp_editor( $value, $args['section'] . '-' . $args['id'], $editor_settings );

		echo '</div>';

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
		$label = $args['options']['button_label'] ?? 'Choose File';

		$html  = sprintf(
			'<input type="text" class="%1$s" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s"/>',
			$class . ' ' . $size . '-text file-url',
			$this->settings_key,
			sanitize_key( $args['id'] ),
			esc_attr( $value )
		);
		$html .= '<input type="button" class="button button-secondary file-browser" value="' . $label . '" />';
		$html .= $this->get_field_description( $args );

		/** This filter has been defined in class-settings-api.php */
		echo apply_filters( $this->prefix . '_after_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
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

		$html  = sprintf(
			'<input type="password" class="%1$s" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s"/>',
			$class . ' ' . $size . '-text',
			$this->settings_key,
			sanitize_key( $args['id'] ),
			esc_attr( $value )
		);
		$html .= $this->get_field_description( $args );

		/** This filter has been defined in class-settings-api.php */
		echo apply_filters( $this->prefix . '_after_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

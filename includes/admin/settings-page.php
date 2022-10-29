<?php
/**
 * Renders the settings page.
 * Portions of this code have been inspired by Easy Digital Downloads, WordPress Settings Sandbox, etc.
 *
 * @link https://webberzone.com
 * @since 2.6.0
 *
 * @package Contextual_Related_Posts
 * @subpackage Admin/Settings
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Render the settings page.
 *
 * @since 2.6.0
 *
 * @return void
 */
function crp_options_page() {
	$active_tab = isset( $_GET['tab'] ) && array_key_exists( sanitize_key( wp_unslash( $_GET['tab'] ) ), crp_get_settings_sections() ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'general'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	ob_start();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Contextual Related Posts Settings', 'contextual-related-posts' ); ?></h1>

		<p>
			<a class="crp_button" href="<?php echo admin_url( 'tools.php?page=crp_tools_page' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>">
				<?php esc_html_e( 'Visit the Tools page', 'autoclose' ); ?>
			</a>
		<p>

		<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
		<div id="post-body-content">

			<ul class="nav-tab-wrapper" style="padding:0">
				<?php
				foreach ( crp_get_settings_sections() as $tab_id => $tab_name ) {

					$active = $active_tab === $tab_id ? ' ' : '';

					echo '<li><a href="#' . esc_attr( $tab_id ) . '" title="' . esc_attr( $tab_name ) . '" class="nav-tab ' . sanitize_html_class( $active ) . '">';
						echo esc_html( $tab_name );
					echo '</a></li>';

				}
				?>
			</ul>

			<form method="post" action="options.php">

				<?php settings_fields( 'crp_settings' ); ?>

				<?php foreach ( crp_get_settings_sections() as $tab_id => $tab_name ) : ?>

				<div id="<?php echo esc_attr( $tab_id ); ?>">
					<table class="form-table">
					<?php
						do_settings_fields( 'crp_settings_' . $tab_id, 'crp_settings_' . $tab_id );
					?>
					</table>
					<p>
					<?php
						// Default submit button.
						submit_button(
							__( 'Save Changes', 'contextual-related-posts' ),
							'primary',
							'submit',
							false
						);

						echo '&nbsp;&nbsp;';

						// Reset button.
						$confirm = esc_js( __( 'Do you really want to reset all these settings to their default values?', 'contextual-related-posts' ) );
						submit_button(
							__( 'Reset all settings', 'contextual-related-posts' ),
							'secondary',
							'settings_reset',
							false,
							array(
								'onclick' => "return confirm('{$confirm}');",
							)
						);
					?>
					</p>
				</div><!-- /#tab_id-->

				<?php endforeach; ?>

			</form>

		</div><!-- /#post-body-content -->

		<div id="postbox-container-1" class="postbox-container">

			<div id="side-sortables" class="meta-box-sortables ui-sortable">
				<?php include_once 'sidebar.php'; ?>
			</div><!-- /#side-sortables -->

		</div><!-- /#postbox-container-1 -->
		</div><!-- /#post-body -->
		<br class="clear" />
		</div><!-- /#poststuff -->

	</div><!-- /.wrap -->

	<?php
	echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Array containing the settings' sections.
 *
 * @since 2.6.0
 *
 * @return array Settings array
 */
function crp_get_settings_sections() {
	$crp_settings_sections = array(
		'general'   => __( 'General', 'contextual-related-posts' ),
		'list'      => __( 'List tuning', 'contextual-related-posts' ),
		'output'    => __( 'Output', 'contextual-related-posts' ),
		'thumbnail' => __( 'Thumbnail', 'contextual-related-posts' ),
		'styles'    => __( 'Styles', 'contextual-related-posts' ),
		'feed'      => __( 'Feed', 'contextual-related-posts' ),
	);

	/**
	 * Filter the array containing the settings' sections.
	 *
	 * @since 2.6.0
	 *
	 * @param array $crp_settings_sections Settings array
	 */
	return apply_filters( 'crp_settings_sections', $crp_settings_sections );

}


/**
 * Miscellaneous callback funcion
 *
 * @since 2.6.0
 *
 * @param array $args Arguments passed by the setting.
 * @return void
 */
function crp_missing_callback( $args ) {
	/* translators: %s: Setting ID. */
	printf( esc_html__( 'The callback function used for the <strong>%s</strong> setting is missing.', 'contextual-related-posts' ), esc_html( $args['id'] ) );
}


/**
 * Header Callback
 *
 * Renders the header.
 *
 * @since 2.6.0
 *
 * @param array $args Arguments passed by the setting.
 * @return void
 */
function crp_header_callback( $args ) {

	$html = '<p class="description">' . wp_kses_post( $args['desc'] ) . '</p>';

	/**
	 * After Settings Output filter
	 *
	 * @since 2.6.0
	 * @param string $html HTML string.
	 * @param array  $args Arguments array.
	 */
	echo apply_filters( 'crp_after_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}


/**
 * Display text fields.
 *
 * @since 2.6.0
 *
 * @param array $args Array of arguments.
 * @return void
 */
function crp_text_callback( $args ) {

	// First, we read the options collection.
	global $crp_settings;

	if ( isset( $crp_settings[ $args['id'] ] ) ) {
		$value = $crp_settings[ $args['id'] ];
	} else {
		$value = isset( $args['options'] ) ? $args['options'] : '';
	}

	$size = sanitize_html_class( ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular' );

	$class = sanitize_html_class( $args['field_class'] );

	$disabled = ! empty( $args['disabled'] ) ? ' disabled="disabled"' : '';
	$readonly = ( isset( $args['readonly'] ) && true === $args['readonly'] ) ? ' readonly="readonly"' : '';

	$attributes = $disabled . $readonly;

	foreach ( (array) $args['field_attributes'] as $attribute => $val ) {
		$attributes .= sprintf( ' %1$s="%2$s"', $attribute, esc_attr( $val ) );
	}

	$html  = sprintf( '<input type="text" id="crp_settings[%1$s]" name="crp_settings[%1$s]" class="%2$s" value="%3$s" %4$s />', sanitize_key( $args['id'] ), $class . ' ' . $size . '-text', esc_attr( stripslashes( $value ) ), $attributes );
	$html .= '<p class="description">' . wp_kses_post( $args['desc'] ) . '</p>';

	/** This filter has been defined in settings-page.php */
	echo apply_filters( 'crp_after_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}


/**
 * Display csv fields.
 *
 * @since 2.6.0
 *
 * @param array $args Array of arguments.
 * @return void
 */
function crp_csv_callback( $args ) {

	crp_text_callback( $args );
}


/**
 * Display CSV fields of numbers.
 *
 * @since 2.6.0
 *
 * @param array $args Array of arguments.
 * @return void
 */
function crp_numbercsv_callback( $args ) {

	crp_csv_callback( $args );
}


/**
 * Display textarea.
 *
 * @since 2.6.0
 *
 * @param array $args Array of arguments.
 * @return void
 */
function crp_textarea_callback( $args ) {

	// First, we read the options collection.
	global $crp_settings;

	if ( isset( $crp_settings[ $args['id'] ] ) ) {
		$value = $crp_settings[ $args['id'] ];
	} else {
		$value = isset( $args['options'] ) ? $args['options'] : '';
	}

	$class = sanitize_html_class( $args['field_class'] );

	$html  = sprintf( '<textarea class="%3$s" cols="50" rows="10" id="crp_settings[%1$s]" name="crp_settings[%1$s]">%2$s</textarea>', sanitize_key( $args['id'] ), esc_textarea( stripslashes( $value ) ), 'large-text ' . $class );
	$html .= '<p class="description">' . wp_kses_post( $args['desc'] ) . '</p>';

	/** This filter has been defined in settings-page.php */
	echo apply_filters( 'crp_after_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}


/**
 * Display CSS fields.
 *
 * @since 2.6.0
 *
 * @param array $args Array of arguments.
 * @return void
 */
function crp_css_callback( $args ) {

	crp_textarea_callback( $args );
}


/**
 * Display checboxes.
 *
 * @since 2.6.0
 *
 * @param array $args Array of arguments.
 * @return void
 */
function crp_checkbox_callback( $args ) {

	// First, we read the options collection.
	global $crp_settings;

	$default = isset( $args['options'] ) ? $args['options'] : '';
	$set     = isset( $crp_settings[ $args['id'] ] ) ? $crp_settings[ $args['id'] ] : crp_get_default_option( $args['id'] );
	$checked = ! empty( $set ) ? checked( 1, (int) $set, false ) : '';

	$html  = sprintf( '<input type="hidden" name="crp_settings[%1$s]" value="-1" />', sanitize_key( $args['id'] ) );
	$html .= sprintf( '<input type="checkbox" id="crp_settings[%1$s]" name="crp_settings[%1$s]" value="1" %2$s />', sanitize_key( $args['id'] ), $checked );
	$html .= ( $set <> $default ) ? '<em style="color:orange"> ' . esc_html__( 'Modified from default setting', 'contextual-related-posts' ) . '</em>' : ''; // phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
	$html .= '<p class="description">' . wp_kses_post( $args['desc'] ) . '</p>';

	/** This filter has been defined in settings-page.php */
	echo apply_filters( 'crp_after_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}


/**
 * Multicheck Callback
 *
 * Renders multiple checkboxes.
 *
 * @since 2.6.0
 *
 * @param array $args Array of arguments.
 * @return void
 */
function crp_multicheck_callback( $args ) {
	global $crp_settings;
	$html = '';

	if ( ! empty( $args['options'] ) ) {
		$html .= sprintf( '<input type="hidden" name="crp_settings[%1$s]" value="-1" />', sanitize_key( $args['id'] ) );

		foreach ( $args['options'] as $key => $option ) {
			if ( isset( $crp_settings[ $args['id'] ][ $key ] ) ) {
				$enabled = $key;
			} else {
				$enabled = null;
			}

			$html .= sprintf( '<input name="crp_settings[%1$s][%2$s]" id="crp_settings[%1$s][%2$s]" type="checkbox" value="%3$s" %4$s /> ', sanitize_key( $args['id'] ), sanitize_key( $key ), esc_attr( $key ), checked( $key, $enabled, false ) );
			$html .= sprintf( '<label for="crp_settings[%1$s][%2$s]">%3$s</label> <br />', sanitize_key( $args['id'] ), sanitize_key( $key ), $option );
		}

		$html .= '<p class="description">' . wp_kses_post( $args['desc'] ) . '</p>';
	}

	/** This filter has been defined in settings-page.php */
	echo apply_filters( 'crp_after_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}


/**
 * Radio Callback
 *
 * Renders radio boxes.
 *
 * @since 2.6.0
 *
 * @param array $args Array of arguments.
 * @return void
 */
function crp_radio_callback( $args ) {
	global $crp_settings;
	$html = '';

	foreach ( $args['options'] as $key => $option ) {
		$checked = false;

		if ( isset( $crp_settings[ $args['id'] ] ) && $crp_settings[ $args['id'] ] === $key ) {
			$checked = true;
		} elseif ( isset( $args['default'] ) && $args['default'] === $key && ! isset( $crp_settings[ $args['id'] ] ) ) {
			$checked = true;
		}

		$html .= sprintf( '<input name="crp_settings[%1$s]" id="crp_settings[%1$s][%2$s]" type="radio" value="%2$s" %3$s /> ', sanitize_key( $args['id'] ), $key, checked( true, $checked, false ) );
		$html .= sprintf( '<label for="crp_settings[%1$s][%2$s]">%3$s</label> <br />', sanitize_key( $args['id'] ), $key, $option );
	}

	$html .= '<p class="description">' . wp_kses_post( $args['desc'] ) . '</p>';

	/** This filter has been defined in settings-page.php */
	echo apply_filters( 'crp_after_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}


/**
 * Radio callback with description.
 *
 * Renders radio boxes with each item having it separate description.
 *
 * @since 2.6.0
 *
 * @param array $args Array of arguments.
 * @return void
 */
function crp_radiodesc_callback( $args ) {
	global $crp_settings;
	$html = '';

	foreach ( $args['options'] as $option ) {
		$checked = false;

		if ( isset( $crp_settings[ $args['id'] ] ) && $crp_settings[ $args['id'] ] === $option['id'] ) {
			$checked = true;
		} elseif ( isset( $args['default'] ) && $args['default'] === $option['id'] && ! isset( $crp_settings[ $args['id'] ] ) ) {
			$checked = true;
		}

		$html .= sprintf( '<input name="crp_settings[%1$s]" id="crp_settings[%1$s][%2$s]" type="radio" value="%2$s" %3$s /> ', sanitize_key( $args['id'] ), $option['id'], checked( true, $checked, false ) );
		$html .= sprintf( '<label for="crp_settings[%1$s][%2$s]">%3$s</label>', sanitize_key( $args['id'] ), $option['id'], $option['name'] );
		$html .= ': <em>' . wp_kses_post( $option['description'] ) . '</em> <br />';
	}

	$html .= '<p class="description">' . wp_kses_post( $args['desc'] ) . '</p>';

	/** This filter has been defined in settings-page.php */
	echo apply_filters( 'crp_after_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}


/**
 * Callback for thumbnail sizes
 *
 * Renders list of radio boxes with various thumbnail sizes.
 *
 * @since 2.6.0
 *
 * @param array $args Array of arguments.
 * @return void
 */
function crp_thumbsizes_callback( $args ) {
	global $crp_settings;
	$html = '';

	if ( ! isset( $args['options']['crp_thumbnail'] ) ) {
		$args['options']['crp_thumbnail'] = array(
			'name'   => 'crp_thumbnail',
			'width'  => crp_get_option( 'thumb_width', 150 ),
			'height' => crp_get_option( 'thumb_height', 150 ),
			'crop'   => crp_get_option( 'thumb_crop', true ),
		);
	}

	foreach ( $args['options'] as $name => $option ) {
		$checked = false;

		if ( isset( $crp_settings[ $args['id'] ] ) && $crp_settings[ $args['id'] ] === $name ) {
			$checked = true;
		} elseif ( isset( $args['default'] ) && $args['default'] === $name && ! isset( $crp_settings[ $args['id'] ] ) ) {
			$checked = true;
		}
		$cropped = $option['crop'] ? __( ' cropped', 'contextual-related-posts' ) : '';

		$html .= sprintf(
			'<input name="crp_settings[%1$s]" id="crp_settings[%1$s][%2$s]" type="radio" value="%2$s" %3$s /> ',
			sanitize_key( $args['id'] ),
			$name,
			checked( true, $checked, false )
		);
		$html .= sprintf(
			'<label for="crp_settings[%1$s][%2$s]">%3$s</label> <br />',
			sanitize_key( $args['id'] ),
			$name,
			$name . ' (' . $option['width'] . 'x' . $option['height'] . $cropped . ')'
		);
	}

	$html .= '<p class="description">' . wp_kses_post( $args['desc'] ) . '</p>';

	/** This filter has been defined in settings-page.php */
	echo apply_filters( 'crp_after_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}


/**
 * Number Callback
 *
 * Renders number fields.
 *
 * @since 2.6.0
 *
 * @param array $args Array of arguments.
 * @return void
 */
function crp_number_callback( $args ) {
	global $crp_settings;

	if ( isset( $crp_settings[ $args['id'] ] ) ) {
		$value = $crp_settings[ $args['id'] ];
	} else {
		$value = isset( $args['options'] ) ? $args['options'] : '';
	}

	$max  = isset( $args['max'] ) ? $args['max'] : 999999;
	$min  = isset( $args['min'] ) ? $args['min'] : 0;
	$step = isset( $args['step'] ) ? $args['step'] : 1;

	$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';

	$html  = sprintf( '<input type="number" step="%1$s" max="%2$s" min="%3$s" class="%4$s" id="crp_settings[%5$s]" name="crp_settings[%5$s]" value="%6$s"/>', esc_attr( $step ), esc_attr( $max ), esc_attr( $min ), sanitize_html_class( $size ) . '-text', sanitize_key( $args['id'] ), esc_attr( stripslashes( $value ) ) );
	$html .= '<p class="description">' . wp_kses_post( $args['desc'] ) . '</p>';

	/** This filter has been defined in settings-page.php */
	echo apply_filters( 'crp_after_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}


/**
 * Select Callback
 *
 * Renders select fields.
 *
 * @since 2.6.0
 *
 * @param array $args Array of arguments.
 * @return void
 */
function crp_select_callback( $args ) {
	global $crp_settings;

	if ( isset( $crp_settings[ $args['id'] ] ) ) {
		$value = $crp_settings[ $args['id'] ];
	} else {
		$value = isset( $args['default'] ) ? $args['default'] : '';
	}

	if ( isset( $args['chosen'] ) ) {
		$chosen = 'class="crp-chosen"';
	} else {
		$chosen = '';
	}

	$html = sprintf( '<select id="crp_settings[%1$s]" name="crp_settings[%1$s]" %2$s />', sanitize_key( $args['id'] ), $chosen );

	foreach ( $args['options'] as $option => $name ) {
		$html .= sprintf( '<option value="%1$s" %2$s>%3$s</option>', sanitize_key( $option ), selected( $option, $value, false ), $name );
	}

	$html .= '</select>';
	$html .= '<p class="description">' . wp_kses_post( $args['desc'] ) . '</p>';

	/** This filter has been defined in settings-page.php */
	echo apply_filters( 'crp_after_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}


/**
 * Descriptive text callback.
 *
 * Renders descriptive text onto the settings field.
 *
 * @since 2.6.0
 *
 * @param array $args Array of arguments.
 * @return void
 */
function crp_descriptive_text_callback( $args ) {
	$html = '<p class="description">' . wp_kses_post( $args['desc'] ) . '</p>';

	/** This filter has been defined in settings-page.php */
	echo apply_filters( 'crp_after_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}


/**
 * Display post types fields.
 *
 * @since 2.6.0
 *
 * @param array $args Array of arguments.
 * @return void
 */
function crp_posttypes_callback( $args ) {

	global $crp_settings;
	$html = '';

	if ( isset( $crp_settings[ $args['id'] ] ) ) {
		$options = $crp_settings[ $args['id'] ];
	} else {
		$options = isset( $args['options'] ) ? $args['options'] : '';
	}

	// If post_types is empty or contains a query string then use parse_str else consider it comma-separated.
	if ( is_array( $options ) ) {
		$post_types = $options;
	} elseif ( ! is_array( $options ) && false === strpos( $options, '=' ) ) {
		$post_types = explode( ',', $options );
	} else {
		parse_str( $options, $post_types );
	}

	$wp_post_types   = get_post_types(
		array(
			'public' => true,
		)
	);
	$posts_types_inc = array_intersect( $wp_post_types, $post_types );

	$html .= sprintf( '<input type="hidden" name="crp_settings[%1$s]" value="-1" />', sanitize_key( $args['id'] ) );

	foreach ( $wp_post_types as $wp_post_type ) {

		$html .= sprintf( '<input name="crp_settings[%1$s][%2$s]" id="crp_settings[%1$s][%2$s]" type="checkbox" value="%2$s" %3$s /> ', sanitize_key( $args['id'] ), esc_attr( $wp_post_type ), checked( true, in_array( $wp_post_type, $posts_types_inc, true ), false ) );
		$html .= sprintf( '<label for="crp_settings[%1$s][%2$s]">%2$s</label> <br />', sanitize_key( $args['id'] ), $wp_post_type );

	}

	$html .= '<p class="description">' . wp_kses_post( $args['desc'] ) . '</p>';

	/** This filter has been defined in settings-page.php */
	echo apply_filters( 'crp_after_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}


/**
 * Display taxonomies fields.
 *
 * @since 2.6.0
 *
 * @param array $args Array of arguments.
 * @return void
 */
function crp_taxonomies_callback( $args ) {

	global $crp_settings;
	$html = '';

	if ( isset( $crp_settings[ $args['id'] ] ) ) {
		$options = $crp_settings[ $args['id'] ];
	} else {
		$options = isset( $args['options'] ) ? $args['options'] : '';
	}

	// If taxonomies is empty or contains a query string then use parse_str else consider it comma-separated.
	if ( is_array( $options ) ) {
		$taxonomies = $options;
	} elseif ( ! is_array( $options ) && false === strpos( $options, '=' ) ) {
		$taxonomies = explode( ',', $options );
	} else {
		parse_str( $options, $taxonomies );
	}

	/* Fetch taxonomies */
	$argsc         = array(
		'public' => true,
	);
	$output        = 'objects';
	$operator      = 'and';
	$wp_taxonomies = get_taxonomies( $argsc, $output, $operator );

	$taxonomies_inc = array_intersect( wp_list_pluck( (array) $wp_taxonomies, 'name' ), $taxonomies );

	$html .= sprintf( '<input type="hidden" name="crp_settings[%1$s]" value="-1" />', sanitize_key( $args['id'] ) );

	foreach ( $wp_taxonomies as $wp_taxonomy ) {

		$html .= sprintf( '<input name="crp_settings[%1$s][%2$s]" id="crp_settings[%1$s][%2$s]" type="checkbox" value="%2$s" %3$s /> ', sanitize_key( $args['id'] ), esc_attr( $wp_taxonomy->name ), checked( true, in_array( $wp_taxonomy->name, $taxonomies_inc, true ), false ) );
		$html .= sprintf( '<label for="crp_settings[%1$s][%2$s]">%3$s (%4$s)</label> <br />', sanitize_key( $args['id'] ), esc_attr( $wp_taxonomy->name ), $wp_taxonomy->labels->name, $wp_taxonomy->name );

	}

	$html .= '<p class="description">' . wp_kses_post( $args['desc'] ) . '</p>';

	/** This filter has been defined in settings-page.php */
	echo apply_filters( 'crp_after_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}


/**
 * Function to add an action to search for tags using Ajax.
 *
 * @since 2.6.0
 *
 * @return void
 */
function crp_tags_search() {

	if ( ! isset( $_REQUEST['tax'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		wp_die( 0 );
	}

	$tax      = '';
	$taxonomy = sanitize_key( $_REQUEST['tax'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! empty( $taxonomy ) ) {
		$tax = get_taxonomy( $taxonomy );
		if ( ! $tax ) {
			wp_die( 0 );
		}

		if ( ! current_user_can( $tax->cap->assign_terms ) ) {
			wp_die( -1 );
		}
	}
	$s = isset( $_REQUEST['q'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['q'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	$comma = _x( ',', 'tag delimiter' );
	if ( ',' !== $comma ) {
		$s = str_replace( $comma, ',', $s );
	}
	if ( false !== strpos( $s, ',' ) ) {
		$s = explode( ',', $s );
		$s = $s[ count( $s ) - 1 ];
	}
	$s = trim( $s );

	/** This filter has been defined in /wp-admin/includes/ajax-actions.php */
	$term_search_min_chars = (int) apply_filters( 'term_search_min_chars', 2, $tax, $s );

	/*
	 * Require $term_search_min_chars chars for matching (default: 2)
	 * ensure it's a non-negative, non-zero integer.
	 */
	if ( ( 0 === $term_search_min_chars ) || ( strlen( $s ) < $term_search_min_chars ) ) {
		wp_die();
	}

	$terms = get_terms(
		array(
			'taxonomy'   => ! empty( $taxonomy ) ? $taxonomy : null,
			'name__like' => $s,
			'hide_empty' => false,
		)
	);

	$results = array();
	foreach ( (array) $terms as $term ) {
		$results[] = "{$term->name} ({$term->taxonomy}:{$term->term_taxonomy_id})";
	}

	echo wp_json_encode( $results );
	wp_die();
}
add_action( 'wp_ajax_nopriv_crp_tag_search', 'crp_tags_search' );
add_action( 'wp_ajax_crp_tag_search', 'crp_tags_search' );


/**
 * Display the default thumbnail below the setting.
 *
 * @since 2.6.0
 *
 * @param  string $html Current HTML.
 * @param  array  $args Argument array of the setting.
 * @return string
 */
function crp_admin_thumbnail( $html, $args ) {

	$thumb_default = crp_get_option( 'thumb_default' );

	if ( 'thumb_default' === $args['id'] && '' !== $thumb_default ) {
		$html .= '<br />';
		$html .= sprintf( '<img src="%1$s" style="max-width:200px" title="%2$s" alt="%2$s" />', esc_attr( $thumb_default ), esc_html__( 'Default thumbnail', 'contextual-related-posts' ) );
	}

	return $html;
}
add_filter( 'crp_after_setting_output', 'crp_admin_thumbnail', 10, 2 );


/**
 * Output messages when a specific style is selected.
 *
 * @since 2.8.0
 *
 * @param  string $html Current HTML.
 * @param  array  $args Argument array of the setting.
 * @return string
 */
function crp_styles_messages( $html, $args ) {

	$crp_styles = crp_get_option( 'crp_styles' );

	if ( in_array( $crp_styles, array( 'rounded_thumbs', 'thumbs_grid' ), true ) && ( 'show_excerpt' === $args['id'] || 'show_author' === $args['id'] || 'show_date' === $args['id'] ) ) {
		$html .= '<span style="color:red">' . esc_html__( 'This option cannot be changed because of the selected related posts style. To modify this option, you will need to select No styles or Text only in the Styles tab', 'contextual-related-posts' ) . '</span>';
	}

	if ( in_array( $crp_styles, array( 'rounded_thumbs', 'thumbs_grid', 'text_only' ), true ) && 'post_thumb_op' === $args['id'] ) {
		$html .= '<span style="color:red">' . esc_html__( 'This option cannot be changed because of the selected related posts style. To modify this option, you will need to select No styles in the Styles tab', 'contextual-related-posts' ) . '</span>';
	}

	return $html;
}
add_filter( 'crp_after_setting_output', 'crp_styles_messages', 10, 2 );

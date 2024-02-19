<?php
/**
 * Class to display and save a Metabox.
 *
 * @since 3.5.0
 *
 * @package WebberZone\Contextual_Related_Posts
 */

namespace WebberZone\Contextual_Related_Posts\Admin\Settings;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * ATA Metabox class to register the metabox for ata_snippets post type.
 *
 * @since 3.5.0
 */
#[\AllowDynamicProperties]
class Metabox_API {

	/**
	 * Current version number
	 *
	 * @var   string
	 */
	const VERSION = '2.3.0';

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
	 * Name of the Post type.
	 *
	 * @var string Post type.
	 */
	protected $post_type;

	/**
	 * Title of the Metabox.
	 *
	 * @var string Post type.
	 */
	protected $title;

	/**
	 * Text to show to indicate a checkbox has been modified from its default value.
	 *
	 * @var string Checkbox Modified Text.
	 */
	public $checkbox_modified_text;

	/**
	 * Array containing the settings' fields.
	 *
	 * @var array Settings fields array.
	 */
	protected $registered_settings = array();

	/**
	 * Main constructor class.
	 *
	 * @param array|string $args {
	 *     Array or string of arguments. Default is blank array.
	 *
	 *     @type string                     $settings_key           Settings key - is used to prepare the form fields. It is not the meta key.
	 *     @type string                     $prefix                 Used to create the meta keys. The meta key format is _{$prefix}_{$setting_id}.
	 *     @type string|array|\WP_Screen    $post_type              The post type(s) on which to show the box.
	 *     @type array                      $registered_settings    Settings fields array.
	 *     @type string                     $checkbox_modified_text Text to show to indicate a checkbox has been modified from its default value.
	 * }
	 */
	public function __construct( $args ) {
		$defaults = array(
			'settings_key'           => '',
			'prefix'                 => '',
			'post_type'              => '',
			'title'                  => '',
			'registered_settings'    => array(),
			'checkbox_modified_text' => '',
		);

		$args = wp_parse_args( $args, $defaults );

		foreach ( $args as $name => $value ) {
			$this->$name = $value;
		}

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( "save_post_{$this->post_type}", array( $this, 'save' ) );
	}

	/**
	 * Function to add the metabox.
	 */
	public function add_meta_boxes() {
		add_meta_box(
			$this->prefix . '_metabox_id',
			$this->title,
			array( $this, 'html' ),
			$this->post_type,
			'advanced',
			'high'
		);
	}

	/**
	 * Enqueue scripts and styles.
	 *
	 * @param string $hook The current admin page.
	 */
	public function admin_enqueue_scripts( $hook ) {
		if ( in_array( $hook, array( 'post.php', 'post-new.php' ), true ) || get_current_screen()->post_type === $this->post_type ) {
			self::enqueue_scripts_styles();
		}
	}

	/**
	 * Enqueues all scripts, styles, settings, and templates necessary to use the Settings API.
	 */
	public static function enqueue_scripts_styles() {

		$minimize = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		wp_enqueue_style( 'wp-color-picker' );

		wp_enqueue_media();
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-autocomplete' );
		wp_enqueue_script( 'jquery-ui-tabs' );

		wp_enqueue_code_editor(
			array(
				'type'       => 'text/html',
				'codemirror' => array(
					'indentUnit' => 2,
					'tabSize'    => 2,
				),
			)
		);

		wp_enqueue_script(
			'wz-admin-js',
			plugins_url( 'js/admin-scripts' . $minimize . '.js', __FILE__ ),
			array( 'jquery' ),
			self::VERSION,
			true
		);
		wp_enqueue_script(
			'wz-codemirror-js',
			plugins_url( 'js/apply-codemirror' . $minimize . '.js', __FILE__ ),
			array( 'jquery' ),
			self::VERSION,
			true
		);
		wp_enqueue_script(
			'wz-taxonomy-suggest-js',
			plugins_url( 'js/taxonomy-suggest' . $minimize . '.js', __FILE__ ),
			array( 'jquery' ),
			self::VERSION,
			true
		);
		wp_enqueue_script(
			'wz-media-selector-js',
			plugins_url( 'js/media-selector' . $minimize . '.js', __FILE__ ),
			array( 'jquery' ),
			self::VERSION,
			true
		);
	}

	/**
	 * Function to save the metabox.
	 *
	 * @param int|string $post_id Post ID.
	 */
	public function save( $post_id ) {

		$post_meta = array();

		// Bail if we're doing an auto save.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// If our nonce isn't there, or we can't verify it, bail.
		if ( ! isset( $_POST[ $this->prefix . '_meta_box_nonce' ] ) || ! wp_verify_nonce( sanitize_key( $_POST[ $this->prefix . '_meta_box_nonce' ] ), $this->prefix . '_meta_box' ) ) {
			return;
		}

		// If our current user can't edit this post, bail.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( empty( $_POST[ $this->settings_key ] ) ) {
			return;
		}

		$settings_sanitize = new Settings_Sanitize();

		$posted = $_POST[ $this->settings_key ]; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.ValidatedSanitizedInput.MissingUnslash

		foreach ( $this->registered_settings as $setting ) {
			$id   = $setting['id'];
			$type = isset( $setting['type'] ) ? $setting['type'] : 'text';

			/**
			 * Skip settings that are not really settings.
			 *
			 * @param array $non_setting_types Array of types which are not settings.
			 */
			$non_setting_types = apply_filters( $this->prefix . '_metabox_non_setting_types', array( 'header', 'descriptive_text' ) );

			if ( in_array( $type, $non_setting_types, true ) ) {
				continue;
			}

			if ( isset( $posted[ $id ] ) ) {
				$value             = $posted[ $id ];
				$sanitize_callback = is_callable( array( $settings_sanitize, "sanitize_{$type}_field" ) ) ? array( $settings_sanitize, "sanitize_{$type}_field" ) : array( $settings_sanitize, 'sanitize_missing' );
				$post_meta[ $id ]  = call_user_func( $sanitize_callback, $value );
			}
		}

		// Run the array through a generic function that allows access to all of the settings.
		$post_meta = call_user_func( array( $this, 'sanitize_post_meta' ), $post_meta );

		/**
		 * Filter the post meta array which contains post-specific settings.
		 *
		 * @param array $post_meta Array of ATA metabox settings.
		 * @param int   $post_id   Post ID
		 */
		$post_meta = apply_filters( "{$this->prefix}_meta_key", $post_meta, $post_id );

		// Now loop through the settings array and either save or delete the meta key.
		foreach ( $this->registered_settings as $setting ) {
			if ( empty( $post_meta[ $setting['id'] ] ) ) {
				delete_post_meta( $post_id, "_{$this->prefix}_{$setting['id']}" );
			}
		}

		foreach ( $post_meta as $setting => $value ) {
			if ( empty( $post_meta[ $setting ] ) ) {
				delete_post_meta( $post_id, "_{$this->prefix}_$setting" );
			} else {
				update_post_meta( $post_id, "_{$this->prefix}_$setting", $value );
			}
		}
	}

	/**
	 * Function to display the metabox.
	 *
	 * @param \WP_Post $post Post object.
	 */
	public function html( $post ) {
		// Add an nonce field so we can check for it later.
		wp_nonce_field( $this->prefix . '_meta_box', $this->prefix . '_meta_box_nonce' );

		$settings_form = new Settings_Form(
			array(
				'settings_key'           => $this->settings_key,
				'prefix'                 => $this->prefix,
				'checkbox_modified_text' => $this->checkbox_modified_text,
			)
		);

		echo '<table class="form-table">';
		foreach ( $this->registered_settings as $setting ) {

			$args = wp_parse_args(
				$setting,
				array(
					'id'               => null,
					'name'             => '',
					'desc'             => '',
					'type'             => null,
					'default'          => '',
					'options'          => '',
					'max'              => null,
					'min'              => null,
					'step'             => null,
					'size'             => null,
					'field_class'      => '',
					'field_attributes' => '',
					'placeholder'      => '',
				)
			);

			$id            = $args['id'];
			$value         = get_post_meta( $post->ID, "_{$this->prefix}_{$id}", true );
			$args['value'] = ! empty( $value ) ? $value : ( isset( $args['default'] ) ? $args['default'] : $args['options'] );
			$type          = isset( $args['type'] ) ? $args['type'] : 'text';
			$callback      = method_exists( $settings_form, "callback_{$type}" ) ? array( $settings_form, "callback_{$type}" ) : array( $settings_form, 'callback_missing' );

			echo '<tr>';
			echo '<th scope="row">' . $args['name'] . '</th>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '<td>';
			call_user_func( $callback, $args );
			echo '</td>';
			echo '</tr>';
		}
		echo '</table>';

		/**
		 * Action triggered when displaying Top 10 meta box.
		 *
		 * @param object $post  Post object.
		 */
		do_action( $this->prefix . '_meta_box', $post );
	}

	/**
	 * Sanitize Post Meta array.
	 *
	 * @param array $settings Post meta settings array.
	 * @return array Sanitized value.
	 */
	public function sanitize_post_meta( $settings ) {

		// This array holds a list of keys that will be passed through our category/tags loop to determine the ids.
		$keys = array(
			'include_on_category' => array(
				'tax'       => 'category',
				'ids_field' => 'include_on_category_ids',
			),
			'include_on_post_tag' => array(
				'tax'       => 'post_tag',
				'ids_field' => 'include_on_post_tag_ids',
			),
		);

		foreach ( $keys as $key => $fields ) {
			if ( isset( $settings[ $key ] ) ) {
				$ids   = array();
				$names = array();

				$taxes = array_unique( str_getcsv( $settings[ $key ] ) );

				foreach ( $taxes as $tax ) {
					$tax_name = get_term_by( 'name', $tax, $fields['tax'] );

					if ( isset( $tax_name->term_taxonomy_id ) ) {
						$ids[]   = $tax_name->term_taxonomy_id;
						$names[] = $tax_name->name;
					}
				}
				$settings[ $fields['ids_field'] ] = join( ',', $ids );
				$settings[ $key ]                 = Settings_Sanitize::str_putcsv( $names );
			} else {
				$settings[ $fields['ids_field'] ] = '';
			}
		}

		return $settings;
	}
}

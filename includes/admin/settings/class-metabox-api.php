<?php
/**
 * Class to display and save a Metabox.
 *
 * @package WebberZone\Contextual_Related_Posts
 */

namespace WebberZone\Contextual_Related_Posts\Admin\Settings;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Metabox API class.
 */
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
	 * Translation strings.
	 *
	 * @var array Translation strings.
	 */
	public $translation_strings;

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
	 *     @type string                     $prefix              Used to create the meta keys. The meta key format is _{$prefix}_{$setting_id}.
	 *     @type string|array|\WP_Screen    $post_type           The post type(s) on which to show the box.
	 *     @type array                      $registered_settings Settings fields array.
	 *     @type array                      $translation_strings Translation strings.
	 * }
	 */
	public function __construct( $args ) {
		$defaults = array(
			'settings_key'        => '',
			'prefix'              => '',
			'post_type'           => '',
			'title'               => '',
			'registered_settings' => array(),
			'translation_strings' => array(),
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
			$args = array(
				'strings' => array(
					'no_results' => isset( $this->translation_strings['tom_select_no_results'] ) ? esc_html( $this->translation_strings['tom_select_no_results'] ) : 'No results found for "%s"',
				),
			);
			self::enqueue_scripts_styles( $this->prefix, $args );
		}
	}

	/**
	 * Enqueues all scripts, styles, settings, and templates necessary to use the Settings API.
	 *
	 * @param string $prefix Prefix which is used for creating the unique filters and actions.
	 * @param array  $args   Array of arguments.
	 */
	public static function enqueue_scripts_styles( $prefix, $args = array() ) {
		Settings_API::enqueue_scripts_styles( $prefix, $args );
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

		$settings_sanitize = new Settings_Sanitize(
			array(
				'settings_key' => $this->settings_key,
				'prefix'       => $this->prefix,
			)
		);

		$posted = $_POST[ $this->settings_key ]; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.ValidatedSanitizedInput.MissingUnslash

		foreach ( $this->registered_settings as $setting ) {
			$id   = $setting['id'];
			$type = $setting['type'] ?? 'text';

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
		 * @param array $post_meta Array of metabox settings.
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
				'settings_key'        => $this->settings_key,
				'prefix'              => $this->prefix,
				'translation_strings' => $this->translation_strings,
			)
		);

		echo '<table class="form-table">';
		foreach ( $this->registered_settings as $setting ) {

			$args = Settings_API::parse_field_args( $setting );

			$id            = $args['id'];
			$value         = get_post_meta( $post->ID, "_{$this->prefix}_{$id}", true );
			$args['value'] = ! empty( $value ) ? $value : ( $args['default'] ?? '' );
			$type          = $args['type'] ?? 'text';
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
		 * Action triggered when displaying the meta box.
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

				$taxes = array_unique( str_getcsv( $settings[ $key ], ',', '"', '' ) );

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

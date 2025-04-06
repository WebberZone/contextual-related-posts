<?php
/**
 * Register Settings.
 *
 * @link  https://webberzone.com
 * @since 3.5.0
 *
 * @package WebberZone\Contextual_Related_Posts\Admin
 */

namespace WebberZone\Contextual_Related_Posts\Admin;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class to register the settings.
 *
 * @since   3.5.0
 */
class Settings {

	/**
	 * Settings API instance.
	 *
	 * @since 3.5.0
	 *
	 * @var Settings\Settings_API
	 */
	public Settings\Settings_API $settings_api;

	/**
	 * Prefix which is used for creating the unique filters and actions.
	 *
	 * @since 3.5.0
	 *
	 * @var string Prefix.
	 */
	public static string $prefix = 'crp';

	/**
	 * Settings Key.
	 *
	 * @since 3.5.0
	 *
	 * @var string Settings Key.
	 */
	public string $settings_key = 'crp_settings';

	/**
	 * The slug name to refer to this menu by (should be unique for this menu).
	 *
	 * @since 3.5.0
	 *
	 * @var string Menu slug.
	 */
	public string $menu_slug = 'crp_options_page';

	/**
	 * Main constructor class.
	 *
	 * @since 3.5.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'initialise_settings' ) );
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 11, 2 );
		add_filter( 'plugin_action_links_' . plugin_basename( CRP_PLUGIN_FILE ), array( $this, 'plugin_actions_links' ) );
		add_filter( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 99 );
		add_filter( self::$prefix . '_settings_sanitize', array( $this, 'change_settings_on_save' ), 99 );
		add_filter( self::$prefix . '_after_setting_output', array( $this, 'display_admin_thumbnail' ), 10, 2 );
		add_filter( self::$prefix . '_setting_field_description', array( $this, 'reset_default_thumb_setting' ), 10, 2 );

		add_action( 'wp_ajax_nopriv_' . self::$prefix . '_tag_search', array( $this, 'tags_search' ) );
		add_action( 'wp_ajax_' . self::$prefix . '_tag_search', array( $this, 'tags_search' ) );
		add_action( self::$prefix . '_settings_page_header', array( $this, 'settings_page_header' ) );
		add_filter( self::$prefix . '_after_setting_output', array( $this, 'after_setting_output' ), 10, 2 );
	}

	/**
	 * Initialise the settings API.
	 *
	 * @since 3.5.0
	 */
	public function initialise_settings() {
		$props = array(
			'default_tab'       => 'general',
			'help_sidebar'      => $this->get_help_sidebar(),
			'help_tabs'         => $this->get_help_tabs(),
			'admin_footer_text' => $this->get_admin_footer_text(),
			'menus'             => $this->get_menus(),
		);

		$args = array(
			'props'               => $props,
			'translation_strings' => $this->get_translation_strings(),
			'settings_sections'   => $this->get_settings_sections(),
			'registered_settings' => $this->get_registered_settings(),
			'upgraded_settings'   => array(),
		);

		$this->settings_api = new Settings\Settings_API( $this->settings_key, self::$prefix, $args );
	}

	/**
	 * Array containing the translation strings.
	 *
	 * @since 1.8.0
	 *
	 * @return array Translation strings.
	 */
	public function get_translation_strings() {
		$strings = array(
			'page_header'          => esc_html__( 'Contextual Related Posts Settings', 'contextual-related-posts' ),
			'reset_message'        => esc_html__( 'Settings have been reset to their default values. Reload this page to view the updated settings.', 'contextual-related-posts' ),
			'success_message'      => esc_html__( 'Settings updated.', 'contextual-related-posts' ),
			'save_changes'         => esc_html__( 'Save Changes', 'contextual-related-posts' ),
			'reset_settings'       => esc_html__( 'Reset all settings', 'contextual-related-posts' ),
			'reset_button_confirm' => esc_html__( 'Do you really want to reset all these settings to their default values?', 'contextual-related-posts' ),
			'checkbox_modified'    => esc_html__( 'Modified from default setting', 'contextual-related-posts' ),
		);

		/**
		 * Filter the array containing the settings' sections.
		 *
		 * @since 3.5.0
		 *
		 * @param array $strings Translation strings.
		 */
		return apply_filters( self::$prefix . '_translation_strings', $strings );
	}

	/**
	 * Get the admin menus.
	 *
	 * @return array Admin menus.
	 */
	public function get_menus() {
		$menus = array();

		// Settings menu.
		$menus[] = array(
			'settings_page' => true,
			'type'          => 'options',
			'page_title'    => esc_html__( 'Contextual Related Posts Settings', 'contextual-related-posts' ),
			'menu_title'    => esc_html__( 'Related Posts', 'contextual-related-posts' ),
			'menu_slug'     => $this->menu_slug,
		);

		return $menus;
	}

	/**
	 * Array containing the settings' sections.
	 *
	 * @since 3.5.0
	 *
	 * @return array Settings array
	 */
	public static function get_settings_sections() {
		$settings_sections = array(
			'general'     => __( 'General', 'contextual-related-posts' ),
			'performance' => __( 'Performance', 'contextual-related-posts' ),
			'list'        => __( 'List tuning', 'contextual-related-posts' ),
			'output'      => __( 'Output', 'contextual-related-posts' ),
			'thumbnail'   => __( 'Thumbnail', 'contextual-related-posts' ),
			'styles'      => __( 'Styles', 'contextual-related-posts' ),
			'feed'        => __( 'Feed', 'contextual-related-posts' ),
		);

		/**
		 * Filter the array containing the settings' sections.
		 *
		 * @since 3.5.0
		 *
		 * @param array $settings_sections Settings array
		 */
		return apply_filters( self::$prefix . '_settings_sections', $settings_sections );
	}

	/**
	 * Retrieve the array of plugin settings
	 *
	 * @since 3.5.0
	 *
	 * @return array Settings array
	 */
	public static function get_registered_settings() {
		$settings = array();
		$sections = self::get_settings_sections();

		foreach ( $sections as $section => $value ) {
			$method_name = 'settings_' . $section;
			if ( method_exists( __CLASS__, $method_name ) ) {
				$settings[ $section ] = self::$method_name();
			}
		}

		/**
		 * Filters the settings array
		 *
		 * @since 3.5.0
		 *
		 * @param array $crp_setings Settings array
		 */
		return apply_filters( self::$prefix . '_registered_settings', $settings );
	}

	/**
	 * Retrieve the array of General settings
	 *
	 * @since 3.5.0
	 *
	 * @return array General settings array
	 */
	public static function settings_general() {
		$settings = array(
			'list_general_header'          => array(
				'id'   => 'list_general_header',
				'name' => '<h3>' . esc_html__( 'General settings', 'contextual-related-posts' ) . '</h3>',
				'desc' => esc_html__( 'General settings for the related posts', 'contextual-related-posts' ),
				'type' => 'header',
			),
			'add_to'                       => array(
				'id'      => 'add_to',
				'name'    => esc_html__( 'Automatically add related posts to', 'contextual-related-posts' ),
				/* translators: 1: Code. */
				'desc'    => sprintf( esc_html__( 'If you choose to disable this, please add %1$s to your template file where you want it displayed', 'contextual-related-posts' ), "<code>&lt;?php if ( function_exists( 'echo_crp' ) ) { echo_crp(); } ?&gt;</code>" ),
				'type'    => 'multicheck',
				'default' => array(
					'single' => 'single',
					'page'   => 'page',
				),
				'options' => array(
					'single'            => esc_html__( 'Posts', 'contextual-related-posts' ),
					'page'              => esc_html__( 'Pages', 'contextual-related-posts' ),
					'home'              => esc_html__( 'Home page', 'contextual-related-posts' ),
					'feed'              => esc_html__( 'Feeds', 'contextual-related-posts' ),
					'category_archives' => esc_html__( 'Category archives', 'contextual-related-posts' ),
					'tag_archives'      => esc_html__( 'Tag archives', 'contextual-related-posts' ),
					'other_archives'    => esc_html__( 'Other archives', 'contextual-related-posts' ),
				),
			),
			'content_filter_priority'      => array(
				'id'      => 'content_filter_priority',
				'name'    => esc_html__( 'Display location priority', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'Select the relative position of the related posts in the post content. A higher number pushes the related posts later in the content. Any number below 10 is not recommended.', 'contextual-related-posts' ),
				'type'    => 'number',
				'options' => '999',
			),
			'insert_after_paragraph'       => array(
				'id'      => 'insert_after_paragraph',
				'name'    => esc_html__( 'Insert after paragraph number', 'contextual-related-posts' ),
				'desc'    => esc_html__( "Enter 0 to display the related posts before the post content, -1 to display this at the end, or a number to insert after a specific paragraph. For negative numbers, the count starts from the end. If the paragraph number exceeds the post's count, they'll appear at the end.", 'contextual-related-posts' ) . '<br /><em>' . esc_html__( 'This ignores any other HTML tags and can get confused by p tags within other tags. Use with caution.', 'contextual-related-posts' ) . '</em>',
				'type'    => 'number',
				'options' => '-1',
				'min'     => '-500',
			),
			'disable_on_mobile'            => array(
				'id'      => 'disable_on_mobile',
				'name'    => esc_html__( 'Disable on mobile devices', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'Disable display of related posts on mobile devices. Might not always work with caching plugins.', 'contextual-related-posts' ),
				'type'    => 'checkbox',
				'options' => false,
			),
			'disable_on_amp'               => array(
				'id'      => 'disable_on_amp',
				'name'    => esc_html__( 'Disable on AMP pages', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'Disable display of related posts on AMP pages.', 'contextual-related-posts' ),
				'type'    => 'checkbox',
				'options' => false,
			),
			'uninstall_settings_header'    => array(
				'id'   => 'uninstall_settings_header',
				'name' => '<h3>' . esc_html__( 'Uninstall settings', 'contextual-related-posts' ) . '</h3>',
				'desc' => esc_html__( 'Settings for uninstalling the plugin', 'contextual-related-posts' ),
				'type' => 'header',
			),
			'uninstall_options'            => array(
				'id'      => 'uninstall_options',
				'name'    => esc_html__( 'Delete options on uninstall', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'If this is checked, all settings related to Contextual Related Posts are removed from the database if you choose to uninstall/delete the plugin.', 'contextual-related-posts' ),
				'type'    => 'checkbox',
				'options' => true,
			),
			'uninstall_indices'            => array(
				'id'      => 'uninstall_indices',
				'name'    => esc_html__( 'Delete FULLTEXT indices on uninstall', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'If this is checked, FULLTEXT indices generated by Contextual Related Posts are removed from the database if you choose to uninstall/delete the plugin.', 'contextual-related-posts' ),
				'type'    => 'checkbox',
				'options' => true,
			),
			'uninstall_tables'             => array(
				'id'      => 'uninstall_tables',
				'name'    => esc_html__( 'Delete custom tables on uninstall', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'If this is checked, custom tables created by Contextual Related Posts Pro will be deleted when you uninstall/delete the plugin.', 'contextual-related-posts' ),
				'type'    => 'checkbox',
				'options' => true,
			),
			'uninstall_indices_deactivate' => array(
				'id'      => 'uninstall_indices_deactivate',
				'name'    => esc_html__( 'Delete FULLTEXT indices on deactivate', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'If this is checked, FULLTEXT indices generated by Contextual Related Posts are removed from the database if you choose to deactivate the plugin.', 'contextual-related-posts' ),
				'type'    => 'checkbox',
				'options' => false,
			),
			'misc_general_header'          => array(
				'id'   => 'misc_general_header',
				'name' => '<h3>' . esc_html__( 'Miscellaneous', 'contextual-related-posts' ) . '</h3>',
				'desc' => esc_html__( 'Miscellaneous settings', 'contextual-related-posts' ),
				'type' => 'header',
			),
			'show_metabox'                 => array(
				'id'      => 'show_metabox',
				'name'    => esc_html__( 'Show metabox', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'This will add the Contextual Related Posts metabox on Edit Posts or Add New Posts screens. Also applies to Pages and Custom Post Types.', 'contextual-related-posts' ),
				'type'    => 'checkbox',
				'options' => true,
			),
			'show_metabox_admins'          => array(
				'id'      => 'show_metabox_admins',
				'name'    => esc_html__( 'Limit meta box to Admins only', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'If selected, the meta box will be hidden from anyone who is not an Admin. By default, Contributors and above will be able to see the meta box. Applies only if the above option is selected.', 'contextual-related-posts' ),
				'type'    => 'checkbox',
				'options' => false,
			),
			'show_credit'                  => array(
				'id'      => 'show_credit',
				'name'    => esc_html__( 'Link to Contextual Related Posts plugin page', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'A no-follow link to the plugin homepage will be added as the last item of the related posts.', 'contextual-related-posts' ),
				'type'    => 'checkbox',
				'options' => false,
			),
		);

		/**
		 * Filters the General settings array
		 *
		 * @since 2.6.0
		 *
		 * @param array $settings General settings array
		 */
		return apply_filters( self::$prefix . '_settings_general', $settings );
	}

	/**
	 * Retrieve the array of Output settings
	 *
	 * @since 3.5.0
	 *
	 * @return array Output settings array
	 */
	public static function settings_output() {
		$settings = array(
			'title'                 => array(
				'id'      => 'title',
				'name'    => esc_html__( 'Heading of posts', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'Displayed before the list of the posts as a master heading', 'contextual-related-posts' ),
				'type'    => 'text',
				'options' => '<h2>' . esc_html__( 'Related Posts', 'contextual-related-posts' ) . ':</h2>',
				'size'    => 'large',
			),
			'blank_output'          => array(
				'id'      => 'blank_output',
				'name'    => esc_html__( 'Show when no posts are found', 'contextual-related-posts' ),
				/* translators: 1: Code. */
				'desc'    => '',
				'type'    => 'radio',
				'default' => 'blank',
				'options' => array(
					'blank'       => esc_html__( 'Blank output', 'contextual-related-posts' ),
					'custom_text' => esc_html__( 'Display custom text', 'contextual-related-posts' ),
				),
			),
			'blank_output_text'     => array(
				'id'      => 'blank_output_text',
				'name'    => esc_html__( 'Custom text', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'Enter the custom text that will be displayed if the second option is selected above.', 'contextual-related-posts' ),
				'type'    => 'textarea',
				'options' => esc_html__( 'No related posts found', 'contextual-related-posts' ),
			),
			'show_excerpt'          => array(
				'id'      => 'show_excerpt',
				'name'    => esc_html__( 'Show post excerpt', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'If the post does not have an excerpt, the plugin will automatically create one containing the number of words specified in the next option.', 'contextual-related-posts' ),
				'type'    => 'checkbox',
				'options' => false,
			),
			'excerpt_length'        => array(
				'id'      => 'excerpt_length',
				'name'    => esc_html__( 'Length of excerpt (in words)', 'contextual-related-posts' ),
				'desc'    => '',
				'type'    => 'number',
				'options' => '10',
				'min'     => '0',
				'size'    => 'small',
			),
			'show_date'             => array(
				'id'      => 'show_date',
				'name'    => esc_html__( 'Show date', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'Displays the date of the post. Uses the same date format set in General Options.', 'contextual-related-posts' ),
				'type'    => 'checkbox',
				'options' => false,
			),
			'show_author'           => array(
				'id'      => 'show_author',
				'name'    => esc_html__( 'Show author', 'contextual-related-posts' ),
				'desc'    => '',
				'type'    => 'checkbox',
				'options' => false,
			),
			'show_primary_term'     => array(
				'id'      => 'show_primary_term',
				'name'    => esc_html__( 'Show primary category/term', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'This will display the primary category/term. This is usually set via your SEO plugin and will default to the first category/term returned by WordPress', 'contextual-related-posts' ),
				'type'    => 'checkbox',
				'options' => false,
			),
			'title_length'          => array(
				'id'      => 'title_length',
				'name'    => esc_html__( 'Limit post title length (in characters)', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'Any title longer than the number of characters set above will be cut and appended with an ellipsis (&hellip;)', 'contextual-related-posts' ),
				'type'    => 'number',
				'options' => '60',
				'min'     => '0',
				'size'    => 'small',
			),
			'link_new_window'       => array(
				'id'      => 'link_new_window',
				'name'    => esc_html__( 'Open links in new window', 'contextual-related-posts' ),
				'desc'    => '',
				'type'    => 'checkbox',
				'options' => false,
			),
			'link_nofollow'         => array(
				'id'      => 'link_nofollow',
				'name'    => esc_html__( 'Add nofollow to links', 'contextual-related-posts' ),
				'desc'    => '',
				'type'    => 'checkbox',
				'options' => false,
			),
			'track_pixels'          => array(
				'id'      => 'track_pixels',
				'name'    => esc_html__( 'Add tracking parameters to URLs', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'Adds tracking parameters to the URLs so you can track when they are clicked in Google Analytics.', 'contextual-related-posts' ),
				'type'    => 'checkbox',
				'options' => false,
				'pro'     => true,
			),
			'exclude_output_header' => array(
				'id'   => 'exclude_output_header',
				'name' => '<h3>' . esc_html__( 'Exclusion settings', 'contextual-related-posts' ) . '</h3>',
				'desc' => '',
				'type' => 'header',
			),
			'exclude_on_post_ids'   => array(
				'id'      => 'exclude_on_post_ids',
				'name'    => esc_html__( 'Exclude display on these posts', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'Comma separated list of post, page or custom post type IDs. e.g. 188,320,500', 'contextual-related-posts' ),
				'type'    => 'numbercsv',
				'options' => '',
			),
			'exclude_on_post_types' => array(
				'id'      => 'exclude_on_post_types',
				'name'    => esc_html__( 'Exclude display on these post types', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'The related posts will not display on any of the above selected post types.', 'contextual-related-posts' ),
				'type'    => 'posttypes',
				'options' => '',
			),
			'exclude_on_cat_slugs'  => array(
				'id'          => 'exclude_on_cat_slugs',
				'name'        => esc_html__( 'Exclude on Terms', 'contextual-related-posts' ),
				'desc'        => esc_html__( 'The field above has an autocomplete so simply start typing in the starting letters and it will prompt you with options. This field requires a specific format as displayed by the autocomplete.', 'contextual-related-posts' ),
				'type'        => 'csv',
				'options'     => '',
				'size'        => 'large',
				'field_class' => 'category_autocomplete',
			),
			'html_wrapper_header'   => array(
				'id'   => 'html_wrapper_header',
				'name' => '<h3>' . esc_html__( 'HTML to display', 'contextual-related-posts' ) . '</h3>',
				'desc' => '',
				'type' => 'header',
			),
			'before_list'           => array(
				'id'      => 'before_list',
				'name'    => esc_html__( 'Before the list of posts', 'contextual-related-posts' ),
				'desc'    => '',
				'type'    => 'text',
				'options' => '<ul>',
			),
			'after_list'            => array(
				'id'      => 'after_list',
				'name'    => esc_html__( 'After the list of posts', 'contextual-related-posts' ),
				'desc'    => '',
				'type'    => 'text',
				'options' => '</ul>',
			),
			'before_list_item'      => array(
				'id'      => 'before_list_item',
				'name'    => esc_html__( 'Before each list item', 'contextual-related-posts' ),
				'desc'    => '',
				'type'    => 'text',
				'options' => '<li>',
			),
			'after_list_item'       => array(
				'id'      => 'after_list_item',
				'name'    => esc_html__( 'After each list item', 'contextual-related-posts' ),
				'desc'    => '',
				'type'    => 'text',
				'options' => '</li>',
			),
		);

		/**
		 * Filters the Output settings array
		 *
		 * @since 2.5.0
		 *
		 * @param array $settings Output settings array
		 */
		return apply_filters( self::$prefix . '_settings_output', $settings );
	}

	/**
	 * Retrieve the array of List Tuning settings
	 *
	 * @since 3.5.0
	 *
	 * @return array List Tuning settings array
	 */
	public static function settings_list() {
		$settings = array(
			'list_general_header'       => array(
				'id'   => 'list_general_header',
				'name' => '<h3>' . esc_html__( 'General List Settings', 'contextual-related-posts' ) . '</h3>',
				'desc' => esc_html__( 'General settings for the related posts list', 'contextual-related-posts' ),
				'type' => 'header',
			),
			'use_global_settings'       => array(
				'id'      => 'use_global_settings',
				'name'    => esc_html__( 'Use global settings in block', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'If activated, the settings from this page are automatically inserted in the Related Posts block. This also applies to existing blocks which do not have any attributes set if the post is edited.', 'contextual-related-posts' ),
				'type'    => 'checkbox',
				'options' => false,
				'pro'     => true,
			),
			'limit'                     => array(
				'id'      => 'limit',
				'name'    => esc_html__( 'Number of posts to display', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'Maximum number of posts that will be displayed in the list. This option is used if you do not specify the number of posts in the widget or shortcodes', 'contextual-related-posts' ),
				'type'    => 'number',
				'options' => '6',
				'min'     => '0',
				'size'    => 'small',
			),
			'daily_range'               => array(
				'id'      => 'daily_range',
				'name'    => esc_html__( 'Related posts should be newer than', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'This sets the cut-off period for which posts will be displayed. e.g. setting it to 365 will show related posts from the last year only. Set to 0 to disable limiting posts by date.', 'contextual-related-posts' ),
				'type'    => 'number',
				'options' => '0',
				'min'     => '0',
			),
			'ordering'                  => array(
				'id'      => 'ordering',
				'name'    => esc_html__( 'Order posts', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'Select how you want the related posts to be ordered. Randomly will shuffle the related posts and does not work if you have HTML caching enabled.', 'contextual-related-posts' ),
				'type'    => 'radio',
				'default' => 'relevance',
				'options' => self::get_orderings(),
			),
			'random_order'              => array(
				'id'      => 'random_order',
				'name'    => esc_html__( 'Randomize posts', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'This shuffles the selected related posts, similar to choosing Randomly in the above option. If you select to order by date in the previous option, then the related posts will first be sorted by date and the selected ones are shuffled. Does not work if Cache HTML output is enabled.', 'contextual-related-posts' ),
				'type'    => 'checkbox',
				'options' => false,
			),
			'relevance_header'          => array(
				'id'   => 'relevance_header',
				'name' => '<h3>' . esc_html__( 'Relevance Matching', 'contextual-related-posts' ) . '</h3>',
				'desc' => esc_html__( 'Settings for content matching and relevance scoring', 'contextual-related-posts' ),
				'type' => 'header',
			),
			'weight_title'              => array(
				'id'      => 'weight_title',
				'name'    => __( 'Weight for post title', 'contextual-related-posts' ),
				'desc'    => __( 'The weight to give to the post title when calculating the relevance of the post.', 'contextual-related-posts' ),
				'type'    => 'number',
				'options' => 10,
				'min'     => '0',
				'size'    => 'small',
				'pro'     => true,
			),
			'weight_content'            => array(
				'id'      => 'weight_content',
				'name'    => __( 'Weight for post content', 'contextual-related-posts' ),
				'desc'    => __( 'The weight to give to the post content when calculating the relevance of the post. This may make your query take longer to process.', 'contextual-related-posts' ),
				'type'    => 'number',
				'options' => 0,
				'min'     => '0',
				'size'    => 'small',
				'pro'     => true,
			),
			'weight_excerpt'            => array(
				'id'      => 'weight_excerpt',
				'name'    => __( 'Weight for post excerpt', 'contextual-related-posts' ),
				'desc'    => __( 'The weight to give to the post excerpt when calculating the relevance of the post.', 'contextual-related-posts' ),
				'type'    => 'number',
				'options' => 0,
				'min'     => '0',
				'size'    => 'small',
				'pro'     => true,
			),
			'weight_taxonomy_category'  => array(
				'id'      => 'weight_taxonomy_category',
				'name'    => __( 'Weight for categories', 'contextual-related-posts' ),
				'desc'    => __( 'Weight to give category matches when calculating relevance.', 'contextual-related-posts' ),
				'type'    => 'number',
				'options' => 0,
				'min'     => '0',
				'size'    => 'small',
				'pro'     => true,
			),
			'weight_taxonomy_post_tag'  => array(
				'id'      => 'weight_taxonomy_post_tag',
				'name'    => __( 'Weight for tags', 'contextual-related-posts' ),
				'desc'    => __( 'Weight to give tag matches when calculating relevance.', 'contextual-related-posts' ),
				'type'    => 'number',
				'options' => 0,
				'min'     => '0',
				'size'    => 'small',
				'pro'     => true,
			),
			'weight_taxonomy_default'   => array(
				'id'      => 'weight_taxonomy_default',
				'name'    => __( 'Default taxonomy weight', 'contextual-related-posts' ),
				'desc'    => __( 'Weight to give other taxonomy matches when calculating relevance.', 'contextual-related-posts' ),
				'type'    => 'number',
				'options' => 0,
				'min'     => '0',
				'size'    => 'small',
				'pro'     => true,
			),
			'weight_primary_term_boost' => array(
				'id'      => 'weight_primary_term_boost',
				'name'    => __( 'Primary term boost', 'contextual-related-posts' ),
				'desc'    => __( 'Additional weight multiplier for primary terms.', 'contextual-related-posts' ),
				'type'    => 'number',
				'options' => 0,
				'min'     => '0',
				'size'    => 'small',
				'pro'     => true,
			),
			'use_precomputed_tax_score' => array(
				'id'      => 'use_precomputed_tax_score',
				'name'    => __( 'Use precomputed taxonomy score', 'contextual-related-posts' ),
				'desc'    => __( 'Enable to use precomputed taxonomy score for relevance calculation. This can improve performance but will ignore the above weights for taxonomies when running live queries.', 'contextual-related-posts' ),
				'type'    => 'checkbox',
				'options' => false,
				'pro'     => true,
			),
			'match_content'             => array(
				'id'      => 'match_content',
				'name'    => esc_html__( 'Related posts based on title and content', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'If unchecked, only posts titles are used. Enable the cache if enabling this option for better performance. Each site is different, so toggle this option to see which setting gives you better quality related posts.', 'contextual-related-posts' ),
				'type'    => 'checkbox',
				'options' => true,
			),
			'match_content_words'       => array(
				'id'      => 'match_content_words',
				'name'    => esc_html__( 'Limit content to be compared', 'contextual-related-posts' ),
				/* translators: 1: Number. */
				'desc'    => sprintf( esc_html__( 'This sets the maximum words of the post content that will be matched. Set to 0 for no limit. Max value: %1$s.', 'contextual-related-posts' ), CRP_MAX_WORDS ),
				'type'    => 'number',
				'options' => '0',
				'min'     => '0',
				'max'     => CRP_MAX_WORDS,
			),
			'post_filter_header'        => array(
				'id'   => 'post_filter_header',
				'name' => '<h3>' . esc_html__( 'Post Selection Criteria', 'contextual-related-posts' ) . '</h3>',
				'desc' => esc_html__( 'Settings for controlling which posts to include', 'contextual-related-posts' ),
				'type' => 'header',
			),
			'post_types'                => array(
				'id'      => 'post_types',
				'name'    => esc_html__( 'Post types to include', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'At least one option should be selected above. Select which post types you want to include in the list of posts. This field can be overridden using a comma separated list of post types when using the manual display.', 'contextual-related-posts' ),
				'type'    => 'posttypes',
				'options' => 'post,page',
			),
			'cornerstone_post_ids'      => array(
				'id'      => 'cornerstone_post_ids',
				'name'    => esc_html__( 'Cornerstone IDs', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'Comma separated list of post/page or custom post type IDs to be used as cornerstone posts. Posts with these IDs will be randomly selected and then included in the list of related posts. Roughly 20% of the related posts will be selected from this list.', 'contextual-related-posts' ),
				'type'    => 'numbercsv',
				'options' => '',
				'pro'     => true,
			),
			'same_post_type'            => array(
				'id'      => 'same_post_type',
				'name'    => esc_html__( 'Limit to same post type', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'If checked, the related posts will only be selected from the same post type of the current post.', 'contextual-related-posts' ),
				'type'    => 'checkbox',
				'options' => false,
			),
			'same_author'               => array(
				'id'      => 'same_author',
				'name'    => esc_html__( 'Limit to same author', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'If checked, the related posts will only be selected from the same author of the current post.', 'contextual-related-posts' ),
				'type'    => 'checkbox',
				'options' => false,
			),
			'taxonomy_header'           => array(
				'id'   => 'taxonomy_header',
				'name' => '<h3>' . esc_html__( 'Taxonomy & Term Filtering', 'contextual-related-posts' ) . '</h3>',
				'desc' => esc_html__( 'Settings for controlling taxonomy and term relationships', 'contextual-related-posts' ),
				'type' => 'header',
			),
			'primary_term'              => array(
				'id'      => 'primary_term',
				'name'    => esc_html__( 'Limit to same primary term', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'If enabled, then it will only select posts from the primary category/term. This is usually set via your SEO plugin and will default to the first category/term returned by WordPress', 'contextual-related-posts' ),
				'type'    => 'checkbox',
				'options' => false,
			),
			'same_taxes'                => array(
				'id'      => 'same_taxes',
				'name'    => esc_html__( 'Only from same', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'Limit the related posts only to the categories, tags, and/or taxonomies of the current post.', 'contextual-related-posts' ),
				'type'    => 'taxonomies',
				'options' => '',
			),
			'match_all'                 => array(
				'id'      => 'match_all',
				'name'    => esc_html__( 'Match all taxonomies', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'If enabled, then it will only select posts that match all the above selected taxonomies. This can result in no related posts being found.', 'contextual-related-posts' ),
				'type'    => 'checkbox',
				'options' => false,
			),
			'no_of_common_terms'        => array(
				'id'      => 'no_of_common_terms',
				'name'    => esc_html__( 'Number of common terms', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'Enter the minimum number of common terms that have to be matched before a post is considered related.', 'contextual-related-posts' ),
				'type'    => 'number',
				'options' => '1',
				'min'     => '1',
			),
			'related_meta_keys'         => array(
				'id'      => 'related_meta_keys',
				'name'    => esc_html__( 'Related Meta Keys', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'Enter a comma-separated list of meta keys. Posts that match the same value of the meta key are displayed before the other related posts', 'contextual-related-posts' ),
				'type'    => 'csv',
				'options' => '',
				'size'    => 'large',
			),
			'exclusion_header'          => array(
				'id'   => 'exclusion_header',
				'name' => '<h3>' . esc_html__( 'Exclusion Rules', 'contextual-related-posts' ) . '</h3>',
				'desc' => esc_html__( 'Settings for excluding posts and terms', 'contextual-related-posts' ),
				'type' => 'header',
			),
			'exclude_post_ids'          => array(
				'id'      => 'exclude_post_ids',
				'name'    => esc_html__( 'Post/page IDs to exclude', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'Comma-separated list of post or page IDs to exclude from the list. e.g. 188,320,500', 'contextual-related-posts' ),
				'type'    => 'numbercsv',
				'options' => '',
			),
			'exclude_cat_slugs'         => array(
				'id'          => 'exclude_cat_slugs',
				'name'        => esc_html__( 'Exclude Terms', 'contextual-related-posts' ),
				'desc'        => esc_html__( 'The field above has an autocomplete so simply start typing in the starting letters and it will prompt you with options. This field requires a specific format as displayed by the autocomplete.', 'contextual-related-posts' ),
				'type'        => 'csv',
				'options'     => '',
				'size'        => 'large',
				'field_class' => 'category_autocomplete',
			),
			'exclude_categories'        => array(
				'id'       => 'exclude_categories',
				'name'     => esc_html__( 'Exclude Term Taxonomy IDs', 'contextual-related-posts' ),
				'desc'     => esc_html__( 'This is a readonly field that is automatically populated based on the above input when the settings are saved. These might differ from the IDs visible in the Categories page which use the term_id. Contextual Related Posts uses the term_taxonomy_id which is unique to this taxonomy.', 'contextual-related-posts' ),
				'type'     => 'text',
				'options'  => '',
				'readonly' => true,
			),
			'advanced_header'           => array(
				'id'   => 'advanced_header',
				'name' => '<h3>' . esc_html__( 'Advanced Options', 'contextual-related-posts' ) . '</h3>',
				'desc' => esc_html__( 'Advanced settings for contextual matching', 'contextual-related-posts' ),
				'type' => 'header',
			),
			'disable_contextual'        => array(
				'id'      => 'disable_contextual',
				'name'    => esc_html__( 'Disable contextual matching', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'Select to disable contextual matching. This will disable the content matching described above. You can choose to fallback to just the first X posts from the selected categories mentioned below.', 'contextual-related-posts' ),
				'type'    => 'checkbox',
				'options' => false,
			),
			'disable_contextual_cpt'    => array(
				'id'          => 'disable_contextual_cpt',
				'name'        => esc_html__( 'Disable contextual matching ONLY for custom post types', 'contextual-related-posts' ),
				'desc'        => esc_html__( 'Checking this option will disable contextual matching only for custom post types. For WordPress inbuilt post types, the plugin will continue as per your settings above. If you enable this option, make sure that Manual related posts or Randomize posts are selected above for meaningful results.', 'contextual-related-posts' ),
				'type'        => 'checkbox',
				'options'     => false,
				'field_class' => 'crp_admin_cascading',
			),
			'include_words'             => array(
				'id'      => 'include_words',
				'name'    => esc_html__( 'Include only posts that contain these words:', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'If entered, the related posts will include only posts that contain any of the specified words. Separate words with commas and no spaces. e.g. samsung,apple,nokia', 'contextual-related-posts' ),
				'type'    => 'csv',
				'options' => '',
			),
			'exclude_words'             => array(
				'id'      => 'exclude_words',
				'name'    => esc_html__( 'Exclude posts that contain these words:', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'If entered, the related posts will exclude posts that contain any of the specified words. Separate words with commas and no spaces. e.g. samsung,apple,nokia', 'contextual-related-posts' ),
				'type'    => 'csv',
				'options' => '',
			),
		);

		/**
		 * Filters the List Tuning settings array.
		 *
		 * @since 2.6.0
		 *
		 * @param array $settings List Tuning settings array.
		 */
		return apply_filters( self::$prefix . '_settings_list', $settings );
	}

	/**
	 * Retrieve the array of Thumbnail settings
	 *
	 * @since 3.5.0
	 *
	 * @return array Thumbnail settings array
	 */
	public static function settings_thumbnail() {
		$settings = array(
			'post_thumb_op'      => array(
				'id'      => 'post_thumb_op',
				'name'    => esc_html__( 'Location of the post thumbnail', 'contextual-related-posts' ),
				'desc'    => '',
				'type'    => 'radio',
				'default' => 'text_only',
				'options' => array(
					'inline'      => esc_html__( 'Display thumbnails inline with posts, before title', 'contextual-related-posts' ),
					'after'       => esc_html__( 'Display thumbnails inline with posts, after title', 'contextual-related-posts' ),
					'thumbs_only' => esc_html__( 'Display only thumbnails, no text', 'contextual-related-posts' ),
					'text_only'   => esc_html__( 'Do not display thumbnails, only text', 'contextual-related-posts' ),
				),
			),
			'thumb_size'         => array(
				'id'      => 'thumb_size',
				'name'    => esc_html__( 'Thumbnail size', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'You can choose from existing image sizes above or create a custom size. If you have chosen Custom size above, then enter the width, height and crop settings below. For best results, use a cropped image. If you change the width and/or height below, existing images will not be automatically resized. You will ned to regenerate the images using a plugin or using WP CLI: wp media regenerate.', 'contextual-related-posts' ),
				'type'    => 'thumbsizes',
				'default' => 'crp_thumbnail',
				'options' => \WebberZone\Contextual_Related_Posts\Frontend\Media_Handler::get_all_image_sizes(),
			),
			'thumb_width'        => array(
				'id'      => 'thumb_width',
				'name'    => esc_html__( 'Thumbnail width', 'contextual-related-posts' ),
				'desc'    => '',
				'type'    => 'number',
				'options' => '150',
				'min'     => '0',
				'size'    => 'small',
			),
			'thumb_height'       => array(
				'id'      => 'thumb_height',
				'name'    => esc_html__( 'Thumbnail height', 'contextual-related-posts' ),
				'desc'    => '',
				'type'    => 'number',
				'options' => '150',
				'min'     => '0',
				'size'    => 'small',
			),
			'thumb_crop'         => array(
				'id'      => 'thumb_crop',
				'name'    => esc_html__( 'Hard crop thumbnails', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'Check this box to hard crop the thumbnails. i.e. force the width and height above vs. maintaining proportions.', 'contextual-related-posts' ),
				'type'    => 'checkbox',
				'options' => true,
			),
			'thumb_create_sizes' => array(
				'id'      => 'thumb_create_sizes',
				'name'    => esc_html__( 'Generate thumbnail sizes', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'If you select this option and Custom size is selected above, the plugin will register the image size with WordPress to create new thumbnails. Does not update old images as explained above.', 'contextual-related-posts' ),
				'type'    => 'checkbox',
				'options' => true,
			),
			'thumb_html'         => array(
				'id'      => 'thumb_html',
				'name'    => esc_html__( 'Thumbnail size attributes', 'contextual-related-posts' ),
				'desc'    => '',
				'type'    => 'radio',
				'default' => 'html',
				'options' => array(
					/* translators: %s: Code. */
					'css'  => sprintf( esc_html__( 'Use CSS to set the width and height: e.g. %s', 'contextual-related-posts' ), '<code>style="max-width:250px;max-height:250px"</code>' ),
					/* translators: %s: Code. */
					'html' => sprintf( esc_html__( 'Use HTML attributes to set the width and height: e.g. %s', 'contextual-related-posts' ), '<code>width="250" height="250"</code>' ),
					'none' => esc_html__( 'No width or height set. You will need to use external styles to force any width or height of your choice.', 'contextual-related-posts' ),
				),
			),
			'thumb_meta'         => array(
				'id'      => 'thumb_meta',
				'name'    => esc_html__( 'Thumbnail meta field name', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'The value of this field should contain the URL of the image and can be set in the metabox in the Edit Post screen', 'contextual-related-posts' ),
				'type'    => 'text',
				'options' => 'post-image',
			),
			'scan_images'        => array(
				'id'      => 'scan_images',
				'name'    => esc_html__( 'Get first image', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'The plugin will fetch the first image in the post content if this is enabled. This can slow down the loading of your page if the first image in the followed posts is large in file-size.', 'contextual-related-posts' ),
				'type'    => 'checkbox',
				'options' => true,
			),
			'thumb_default_show' => array(
				'id'      => 'thumb_default_show',
				'name'    => esc_html__( 'Use default thumbnail?', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'If checked, when no thumbnail is found, show a default one from the URL below. If not checked and no thumbnail is found, no image will be shown.', 'contextual-related-posts' ),
				'type'    => 'checkbox',
				'options' => true,
			),
			'thumb_default'      => array(
				'id'      => 'thumb_default',
				'name'    => esc_html__( 'Default thumbnail', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'Enter the full URL of the image that you wish to display if no thumbnail is found. This image will be displayed below.', 'contextual-related-posts' ),
				'type'    => 'file',
				'options' => CRP_PLUGIN_URL . 'default.png',
				'size'    => 'large',
			),
		);

		/**
		 * Filters the Thumbnail settings array
		 *
		 * @since 2.6.0
		 *
		 * @param array $settings Thumbnail settings array
		 */
		return apply_filters( self::$prefix . '_settings_thumbnail', $settings );
	}

	/**
	 * Retrieve the array of Styles settings
	 *
	 * @since 3.5.0
	 *
	 * @return array Styles settings array
	 */
	public static function settings_styles() {
		$settings = array(
			'crp_styles' => array(
				'id'      => 'crp_styles',
				'name'    => esc_html__( 'Related Posts style', 'contextual-related-posts' ),
				'desc'    => '',
				'type'    => 'radiodesc',
				'default' => 'rounded_thumbs',
				'options' => self::get_styles(),
			),
			'custom_css' => array(
				'id'          => 'custom_css',
				'name'        => esc_html__( 'Custom CSS', 'contextual-related-posts' ),
				/* translators: 1: Opening a tag, 2: Closing a tag, 3: Opening code tage, 4. Closing code tag. */
				'desc'        => sprintf( esc_html__( 'Do not include %3$sstyle%4$s tags. Check out the %1$sFAQ%2$s for available CSS classes to style.', 'contextual-related-posts' ), '<a href="' . esc_url( 'http://wordpress.org/plugins/contextual-related-posts/faq/' ) . '" target="_blank">', '</a>', '<code>', '</code>' ),
				'type'        => 'css',
				'options'     => '',
				'field_class' => 'codemirror_css',
			),
		);

		/**
		 * Filters the Styles settings array
		 *
		 * @since 2.6.0
		 *
		 * @param array $settings Styles settings array
		 */
		return apply_filters( self::$prefix . '_settings_styles', $settings );
	}

	/**
	 * Retrieve the array of Feed settings
	 *
	 * @since 3.5.0
	 *
	 * @return array Feed settings array
	 */
	public static function settings_feed() {
		$settings = array(
			'feed_options_desc'  => array(
				'id'   => 'feed_options_desc',
				'name' => '<strong>' . esc_html__( 'About this tab', 'contextual-related-posts' ) . '</strong>',
				'desc' => esc_html__( 'Below options override the related posts settings for your blog feed. These only apply if you have selected to add related posts to Feeds in the General Options tab. It is recommended to not display thumbnails as there is no easy way to style the related posts in the feed.', 'contextual-related-posts' ),
				'type' => 'descriptive_text',
			),
			'limit_feed'         => array(
				'id'      => 'limit_feed',
				'name'    => esc_html__( 'Number of posts to display', 'contextual-related-posts' ),
				'desc'    => '',
				'type'    => 'number',
				'options' => '5',
				'min'     => '0',
				'size'    => 'small',
			),
			'show_excerpt_feed'  => array(
				'id'      => 'show_excerpt_feed',
				'name'    => esc_html__( 'Show post excerpt', 'contextual-related-posts' ),
				'desc'    => '',
				'type'    => 'checkbox',
				'options' => false,
			),
			'post_thumb_op_feed' => array(
				'id'      => 'post_thumb_op_feed',
				'name'    => esc_html__( 'Location of the post thumbnail', 'contextual-related-posts' ),
				'desc'    => '',
				'type'    => 'radio',
				'default' => 'text_only',
				'options' => array(
					'inline'      => esc_html__( 'Display thumbnails inline with posts, before title', 'contextual-related-posts' ),
					'after'       => esc_html__( 'Display thumbnails inline with posts, after title', 'contextual-related-posts' ),
					'thumbs_only' => esc_html__( 'Display only thumbnails, no text', 'contextual-related-posts' ),
					'text_only'   => esc_html__( 'Do not display thumbnails, only text', 'contextual-related-posts' ),
				),
			),
			'thumb_width_feed'   => array(
				'id'      => 'thumb_width_feed',
				'name'    => esc_html__( 'Thumbnail width', 'contextual-related-posts' ),
				'desc'    => '',
				'type'    => 'number',
				'options' => '250',
				'min'     => '0',
				'size'    => 'small',
			),
			'thumb_height_feed'  => array(
				'id'      => 'thumb_height_feed',
				'name'    => esc_html__( 'Thumbnail height', 'contextual-related-posts' ),
				'desc'    => '',
				'type'    => 'number',
				'options' => '250',
				'min'     => '0',
				'size'    => 'small',
			),
		);

		/**
		 * Filters the Feed settings array
		 *
		 * @since 2.6.0
		 *
		 * @param array $settings Feed settings array
		 */
		return apply_filters( self::$prefix . '_settings_feed', $settings );
	}

	/**
	 * Retrieve the array of Performance settings
	 *
	 * @since 4.0.0
	 *
	 * @return array Performance settings array
	 */
	public static function settings_performance() {
		$custom_tables_desc = sprintf(
			/* translators: 1: Opening a tag, 2: Closing a tag */
			esc_html__( 'Efficient Content Storage and Indexing (ECSI) creates a dedicated database table optimized for related content queries. This enhances performance, particularly on sites with a large number of posts or high traffic. To create the ECSI tables, visit the %1$sTools tab%2$s.', 'contextual-related-posts' ),
			'<a href="' . esc_url( admin_url( 'tools.php?page=crp_tools_page' ) ) . '" target="_blank">',
			'</a>'
		);

		if ( is_admin() ) {
			$mysql_message = \WebberZone\Contextual_Related_Posts\Util\Helpers::get_database_compatibility_message();

			if ( $mysql_message ) {
				$custom_tables_desc .= '<br /><br /><span style="color: #9B0800;">' . $mysql_message . '</span>';
			}
		}

		$settings = array(
			'custom_tables_header' => array(
				'id'   => 'custom_tables_header',
				'name' => '<h3>' . esc_html__( 'Efficient Content Storage and Indexing (ECSI)', 'contextual-related-posts' ) . '</h3>',
				'desc' => $custom_tables_desc,
				'type' => 'header',
			),
			'use_custom_tables'    => array(
				'id'      => 'use_custom_tables',
				'name'    => esc_html__( 'Use Custom Tables', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'Use dedicated custom tables for related posts queries. This can significantly improve performance on large sites with many posts.', 'contextual-related-posts' ),
				'type'    => 'checkbox',
				'options' => false,
				'default' => false,
				'pro'     => true,
			),
			'optimization_header'  => array(
				'id'   => 'optimization_header',
				'name' => '<h3>' . esc_html__( 'Optimization', 'contextual-related-posts' ) . '</h3>',
				'desc' => esc_html__( 'Settings for optimizing performance', 'contextual-related-posts' ),
				'type' => 'header',
			),
			'cache_posts'          => array(
				'id'      => 'cache_posts',
				'name'    => esc_html__( 'Cache posts only', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'Enabling this will only cache the related posts but not the entire HTML output. This gives you more flexibility at marginally lower performance. Use this if you only have the related posts called with the same set of parameters.', 'contextual-related-posts' ),
				'type'    => 'checkbox',
				'options' => true,
			),
			'cache'                => array(
				'id'      => 'cache',
				'name'    => esc_html__( 'Cache HTML output', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'Enabling this will cache the entire HTML generated when the post is visited the first time. The cache is cleaned when you save this page. Highly recommended particularly on busy sites. Default is true.', 'contextual-related-posts' ),
				'type'    => 'checkbox',
				'options' => true,
			),
			'cache_time'           => array(
				'id'      => 'cache_time',
				'name'    => esc_html__( 'Cache Time', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'How long should the related posts be cached for. Default is 30 days.', 'contextual-related-posts' ),
				'type'    => 'select',
				'default' => MONTH_IN_SECONDS,
				'options' => array(
					0                    => esc_html__( 'No expiry', 'contextual-related-posts' ),
					HOUR_IN_SECONDS      => esc_html__( '1 Hour', 'contextual-related-posts' ),
					6 * HOUR_IN_SECONDS  => esc_html__( '6 Hours', 'contextual-related-posts' ),
					12 * HOUR_IN_SECONDS => esc_html__( '12 Hours', 'contextual-related-posts' ),
					DAY_IN_SECONDS       => esc_html__( '1 Day', 'contextual-related-posts' ),
					3 * DAY_IN_SECONDS   => esc_html__( '3 Days', 'contextual-related-posts' ),
					WEEK_IN_SECONDS      => esc_html__( '1 Week', 'contextual-related-posts' ),
					2 * WEEK_IN_SECONDS  => esc_html__( '2 Weeks', 'contextual-related-posts' ),
					MONTH_IN_SECONDS     => esc_html__( '30 Days', 'contextual-related-posts' ),
					2 * MONTH_IN_SECONDS => esc_html__( '60 Days', 'contextual-related-posts' ),
					3 * MONTH_IN_SECONDS => esc_html__( '90 Days', 'contextual-related-posts' ),
					YEAR_IN_SECONDS      => esc_html__( '1 Year', 'contextual-related-posts' ),
				),
				'pro'     => true,
			),
			'max_execution_time'   => array(
				'id'      => 'max_execution_time',
				'name'    => esc_html__( 'Max Execution Time', 'top-10' ),
				'desc'    => esc_html__( 'Maximum execution time for MySQL queries in milliseconds. Set to 0 to disable. Default is 3000 (3 seconds).', 'top-10' ),
				'type'    => 'number',
				'options' => 3000,
				'min'     => 0,
				'step'    => 100,
				'pro'     => true,
			),
		);

		/**
		 * Filters the Performance settings array
		 *
		 * @since 4.0.0
		 *
		 * @param array $settings Performance settings array
		 */
		return apply_filters( self::$prefix . '_settings_performance', $settings );
	}

	/**
	 * Get the various styles.
	 *
	 * @since 3.5.0
	 * @return array Associative array of styles.
	 */
	public static function get_styles() {

		$styles = array(
			array(
				'id'          => 'no_style',
				'name'        => esc_html__( 'No styles', 'contextual-related-posts' ),
				'description' => esc_html__( 'Select this option if you plan to add your own styles', 'contextual-related-posts' ) . '<br />',
			),
			array(
				'id'          => 'text_only',
				'name'        => esc_html__( 'Text only', 'contextual-related-posts' ),
				'description' => esc_html__( 'Disable thumbnails and no longer include the default style sheet', 'contextual-related-posts' ) . '<br />',
			),
			array(
				'id'          => 'rounded_thumbs',
				'name'        => esc_html__( 'Rounded thumbnails', 'contextual-related-posts' ),
				'description' => esc_html__( 'Enabling this option will turn on the thumbnails. It will also turn off the display of the author, excerpt and date if already enabled. Disabling this option will not revert any settings.', 'contextual-related-posts' ) . '<br />',
			),
			array(
				'id'          => 'masonry',
				'name'        => esc_html__( 'Masonry', 'contextual-related-posts' ),
				'description' => esc_html__( 'Enables a masonry style layout similar to one made famous by Pinterest.', 'contextual-related-posts' ) . '<br />',
			),
			array(
				'id'          => 'grid',
				'name'        => esc_html__( 'Grid', 'contextual-related-posts' ),
				'description' => esc_html__( 'Uses CSS Grid for display. Might not work on older browsers.', 'contextual-related-posts' ) . '<br />',
			),
			array(
				'id'          => 'thumbs_grid',
				'name'        => esc_html__( 'Rounded thumbnails with CSS grid', 'contextual-related-posts' ),
				'description' => esc_html__( 'Uses CSS grid. It will also turn off the display of the author, excerpt and date if already enabled. Disabling this option will not revert any settings.', 'contextual-related-posts' ) . '<br />',
			),
		);

		/**
		 * Filter the array containing the styles to add your own.
		 *
		 * @since 2.6.0
		 *
		 * @param array $styles Associative array of styles.
		 */
		return apply_filters( self::$prefix . '_get_styles', $styles );
	}

	/**
	 * Get the various order settings.
	 *
	 * @since 3.5.0
	 * @return array Order settings.
	 */
	public static function get_orderings() {

		$orderings = array(
			'relevance' => esc_html__( 'By relevance', 'contextual-related-posts' ),
			'random'    => esc_html__( 'Randomly', 'contextual-related-posts' ),
			'date'      => esc_html__( 'By date', 'contextual-related-posts' ),
		);

		/**
		 * Filter the array containing the order settings.
		 *
		 * @since 2.8.0
		 *
		 * @param array $orderings Order settings.
		 */
		return apply_filters( 'crp_get_orderings', $orderings );
	}

	/**
	 * Adding WordPress plugin action links.
	 *
	 * @since 3.5.0
	 *
	 * @param array $links Array of links.
	 * @return array
	 */
	public function plugin_actions_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->menu_slug ) . '">' . esc_html__( 'Settings', 'contextual-related-posts' ) . '</a>',
			),
			$links
		);
	}

	/**
	 * Add meta links on Plugins page.
	 *
	 * @since 3.5.0
	 *
	 * @param array  $links Array of Links.
	 * @param string $file Current file.
	 * @return array
	 */
	public function plugin_row_meta( $links, $file ) {

		if ( false !== strpos( $file, 'contextual-related-posts.php' ) ) {
			$new_links = array(
				'support'    => '<a href = "https://wordpress.org/support/plugin/contextual-related-posts">' . esc_html__( 'Support', 'contextual-related-posts' ) . '</a>',
				'donate'     => '<a href = "https://ajaydsouza.com/donate/">' . esc_html__( 'Donate', 'contextual-related-posts' ) . '</a>',
				'contribute' => '<a href = "https://github.com/WebberZone/contextual-related-posts">' . esc_html__( 'Contribute', 'contextual-related-posts' ) . '</a>',
			);

			$links = array_merge( $links, $new_links );
		}
		return $links;
	}

	/**
	 * Get the help sidebar content to display on the plugin settings page.
	 *
	 * @since 1.8.0
	 */
	public function get_help_sidebar() {
		$help_sidebar =
			/* translators: 1: Plugin support site link. */
			'<p>' . sprintf( __( 'For more information or how to get support visit the <a href="%s" target="_blank">support site</a>.', 'contextual-related-posts' ), esc_url( 'https://webberzone.com/support/' ) ) . '</p>' .
			'<p>' . sprintf(
				/* translators: 1: Github issues link, 2: Github plugin page link. */
				__( '<a href="%1$s" target="_blank">Post an issue</a> on <a href="%2$s" target="_blank">GitHub</a> (bug reports only).', 'contextual-related-posts' ),
				esc_url( 'https://github.com/WebberZone/contextual-related-posts/issues' ),
				esc_url( 'https://github.com/WebberZone/contextual-related-posts' )
			) . '</p>';

		/**
		 * Filter to modify the help sidebar content.
		 *
		 * @since 3.5.0
		 *
		 * @param string $help_sidebar Help sidebar content.
		 */
		return apply_filters( self::$prefix . '_settings_help', $help_sidebar );
	}

	/**
	 * Get the help tabs to display on the plugin settings page.
	 *
	 * @since 3.5.0
	 */
	public function get_help_tabs() {
		$help_tabs = array(
			array(
				'id'      => 'crp-settings',
				'title'   => __( 'Settings', 'contextual-related-posts' ),
				'content' =>
				'<p>' . __( 'This screen provides the various settings for configuring Contextual Related Posts.', 'contextual-related-posts' ) . '</p>' .
				'<p>' . sprintf(
				/* translators: 1: Link to Knowledge Base article. */
					__( 'You can find detailed information on each of the settings in these <a href="%1$s" target="_blank">knowledgebase articles</a>.', 'contextual-related-posts' ),
					esc_url( 'https://webberzone.com/support/product/contextual-related-posts/01-crp-getting-started/' )
				) . '</p>',
			),
			array(
				'id'      => 'crp-settings-tools',
				'title'   => __( 'Tools', 'contextual-related-posts' ),
				'content' =>
				'<p>' . __( 'This screen provides some tools that help maintain certain features of Contextual Related Posts.', 'contextual-related-posts' ) . '</p>' .
					'<p>' . __( 'Clear the cache, recreate the fulltext indices (including code to manually run this in phpMyAdmin), export/import settings and delete the older settings.', 'contextual-related-posts' ) . '</p>' .
					'<p>' . sprintf(
					/* translators: 1: Link to Knowledge Base article. */
						__( 'You can find more information on each of these tools in this <a href="%1$s" target="_blank">knowledgebase article</a>.', 'contextual-related-posts' ),
						esc_url( 'https://webberzone.com/support/knowledgebase/contextual-related-posts-settings-tools/' )
					) . '</p>',
			),
		);

		/**
		 * Filter to add more help tabs.
		 *
		 * @since 3.5.0
		 *
		 * @param array $help_tabs Associative array of help tabs.
		 */
		return apply_filters( self::$prefix . '_settings_help', $help_tabs );
	}

	/**
	 * Add footer text on the plugin page.
	 *
	 * @since 2.0.0
	 */
	public static function get_admin_footer_text() {
		return sprintf(
			/* translators: 1: Opening achor tag with Plugin page link, 2: Closing anchor tag, 3: Opening anchor tag with review link. */
			__( 'Thank you for using %1$sContextual Related Posts by WebberZone%2$s! Please %3$srate us%2$s on %3$sWordPress.org%2$s', 'knowledgebase' ),
			'<a href="https://webberzone.com/plugins/contextual-related-posts/" target="_blank">',
			'</a>',
			'<a href="https://wordpress.org/support/plugin/contextual-related-posts/reviews/#new-post" target="_blank">'
		);
	}

	/**
	 * Modify settings when they are being saved.
	 *
	 * @since 3.5.0
	 *
	 * @param  array $settings Settings array.
	 * @return array Sanitized settings array.
	 */
	public function change_settings_on_save( $settings ) {

		// Sanitize exclude_cat_slugs to save a new entry of exclude_categories.
		Settings\Settings_Sanitize::sanitize_tax_slugs( $settings, 'exclude_cat_slugs', 'exclude_categories' );

		// Sanitize exclude_on_cat_slugs to save a new entry of exclude_on_categories.
		Settings\Settings_Sanitize::sanitize_tax_slugs( $settings, 'exclude_on_cat_slugs', 'exclude_on_categories' );

		// Overwrite settings if rounded thumbnail style is selected.
		if ( 'rounded_thumbs' === $settings['crp_styles'] || 'thumbs_grid' === $settings['crp_styles'] ) {
			$settings['show_excerpt'] = 0;
			$settings['show_author']  = 0;
			$settings['show_date']    = 0;

			if ( 'inline' !== $settings['post_thumb_op'] && 'thumbs_only' !== $settings['post_thumb_op'] ) {
				$settings['post_thumb_op'] = 'inline';
			}

			add_settings_error( $this->prefix . '-notices', '', 'Note: Display of the author, excerpt and date has been disabled as the Thumbnail style is set to Rounded Thumbnails or Rounded Thumbnails with Grid. You can change the style in the Styles tab.', 'updated' );
		}
		// Overwrite settings if text_only thumbnail style is selected.
		if ( 'text_only' === $settings['crp_styles'] ) {
			$settings['post_thumb_op'] = 'text_only';

			add_settings_error( $this->prefix . '-notices', '', 'Note: Thumbnail location set to Text Only as the Thumbnail style is set to Text Only. You can change the style in the Styles tab.', 'updated' );
		}

		// Force thumb_width and thumb_height if either are zero.
		if ( empty( $settings['thumb_width'] ) || empty( $settings['thumb_height'] ) ) {
			list( $settings['thumb_width'], $settings['thumb_height'] ) = \WebberZone\Contextual_Related_Posts\Frontend\Media_Handler::get_thumb_size( $settings['thumb_size'] );
		}

		return $settings;
	}

	/**
	 * Display the default thumbnail below the setting.
	 *
	 * @since 3.5.0
	 *
	 * @param  string $html Current HTML.
	 * @param  array  $args Argument array of the setting.
	 * @return string
	 */
	public function display_admin_thumbnail( $html, $args ) {

		$thumb_default = \crp_get_option( 'thumb_default' );

		if ( 'thumb_default' === $args['id'] && '' !== $thumb_default ) {
			$html .= '<br />';
			$html .= sprintf( '<img src="%1$s" style="max-width:200px" title="%2$s" alt="%2$s" />', esc_attr( $thumb_default ), esc_html__( 'Default thumbnail', 'contextual-related-posts' ) );
		}

		return $html;
	}

	/**
	 * Display the default thumbnail below the setting.
	 *
	 * @since 3.5.0
	 *
	 * @param  string $html Current HTML.
	 * @param  array  $args Argument array of the setting.
	 * @return string
	 */
	public function reset_default_thumb_setting( $html, $args ) {

		$thumb_default = \crp_get_option( 'thumb_default' );

		if ( 'thumb_default' === $args['id'] && CRP_PLUGIN_URL . 'default.png' !== $thumb_default ) {
			$html = '<span class="dashicons dashicons-undo reset-default-thumb" style="cursor: pointer;" title="' . __( 'Reset' ) . '"></span> <br />' . $html;
		}

		return $html;
	}

	/**
	 * Enqueue scripts and styles.
	 *
	 * @since 3.5.0
	 *
	 * @param string $hook The current admin page.
	 */
	public function admin_enqueue_scripts( $hook ) {
		$file_prefix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		if ( ! isset( $this->settings_api->settings_page ) || $this->settings_api->settings_page !== $hook ) {
			return;
		}

		wp_enqueue_style(
			'crp-admin-styles',
			CRP_PLUGIN_URL . "includes/admin/css/admin-styles{$file_prefix}.css",
			array(),
			CRP_VERSION
		);
		wp_localize_script(
			'wz-admin-js',
			'crp_admin',
			array(
				'thumb_default' => CRP_PLUGIN_URL . 'default.png',
			)
		);

		wp_enqueue_script( 'crp-admin-js' );
		wp_localize_script(
			'crp-admin-js',
			'crp_admin_data',
			array(
				'security' => wp_create_nonce( 'crp-admin' ),
			)
		);
	}

	/**
	 * Function to add an action to search for tags using Ajax.
	 *
	 * @since 3.5.0
	 *
	 * @return void
	 */
	public static function tags_search() {

		if ( ! isset( $_REQUEST['tax'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			wp_die();
		}

		$tax      = '';
		$taxonomy = sanitize_key( $_REQUEST['tax'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! empty( $taxonomy ) ) {
			$tax = get_taxonomy( $taxonomy );
			if ( ! $tax ) {
				wp_die();
			}

			if ( ! current_user_can( $tax->cap->assign_terms ) ) {
				wp_die();
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

	/**
	 * Add a link to the Tools page from the settings page.
	 *
	 * @since 3.5.0
	 */
	public static function settings_page_header() {
		global $crp_freemius;
		?>
		<p>
			<a class="crp_button crp_button_green" href="<?php echo esc_url( admin_url( 'tools.php?page=crp_tools_page' ) ); ?>">
				<?php esc_html_e( 'Visit the Tools page', 'contextual-related-posts' ); ?>
			</a>
			<?php if ( ! $crp_freemius->is_paying() ) { ?>
			<a class="crp_button crp_button_gold" href="<?php echo esc_url( $crp_freemius->get_upgrade_url() ); ?>">
				<?php esc_html_e( 'Upgrade to Pro', 'contextual-related-posts' ); ?>
			</a>
			<?php } ?>
		</p>

		<?php
	}

	/**
	 * Updated the settings fields to display a pro version link.
	 *
	 * @param string $output Settings field HTML.
	 * @param array  $args   Settings field arguments.
	 * @return string Updated HTML.
	 */
	public static function after_setting_output( $output, $args ) {
		if ( isset( $args['pro'] ) && $args['pro'] ) {
			$output .= '<a class="crp_button crp_button_gold" target="_blank" href="https://webberzone.com/plugins/contextual-related-posts/pro/" title="' . esc_attr__( 'Upgrade to Pro', 'contextual-related-posts' ) . '">' . esc_html__( 'Upgrade to Pro', 'contextual-related-posts' ) . '</a>';
		}

		// If $args['id'] is show_excerpt, show_author, show_date and global $crp_settings['crp_styles'] is rounded_thumbs or thumbs_grid then display a notice saying these can't be changed and the style can be changed in the Styles tab.
		if ( in_array( $args['id'], array( 'show_excerpt', 'show_author', 'show_date' ), true ) ) {
			global $crp_settings;
			if ( in_array( $crp_settings['crp_styles'], array( 'rounded_thumbs', 'thumbs_grid' ), true ) ) {
				$output .= '<p class="description" style="color:#9B0800;">' . esc_html__( 'Note: This setting cannot be changed as the Thumbnail style is set to Rounded Thumbnails or Rounded Thumbnails with Grid. You can change the style in the Styles tab.', 'contextual-related-posts' ) . '</p>';
			}
		}

		if ( in_array( $args['id'], array( 'post_thumb_op' ), true ) ) {
			global $crp_settings;
			if ( in_array( $crp_settings['crp_styles'], array( 'text_only' ), true ) ) {
				$output .= '<p class="description" style="color:#9B0800;">' . esc_html__( 'Note: This setting cannot be changed as the Thumbnail style is set to Text Only. You can change the style in the Styles tab.', 'contextual-related-posts' ) . '</p>';
			}
			if ( in_array( $crp_settings['crp_styles'], array( 'rounded_thumbs', 'thumbs_grid' ), true ) ) {
				$output .= '<p class="description" style="color:#9B0800;">' . esc_html__( 'Note: This setting cannot be changed as the Thumbnail style is set to Rounded Thumbnails or Rounded Thumbnails with Grid. You can change the style in the Styles tab.', 'contextual-related-posts' ) . '</p>';
			}
		}

		return $output;
	}
}

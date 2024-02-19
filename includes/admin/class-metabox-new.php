<?php
/**
 * Contextual Related Posts Metabox interface.
 *
 * @package Contextual_Related_Posts
 */

namespace WebberZone\Contextual_Related_Posts\Admin;

use WebberZone\Contextual_Related_Posts\Admin\Settings\Metabox_API;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Metabox class to register the metabox for the plugin.
 *
 * @since 3.5.0
 */
class Metabox_New {

	/**
	 * Metabox API.
	 *
	 * @var Metabox_API[] Metabox API objects.
	 */
	public $metabox_api;

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
	 */
	public function __construct() {
		$this->settings_key = 'crp_meta';
		$this->prefix       = 'crp';

		// If metaboxes are disabled, then exit.
		if ( ! \crp_get_option( 'show_metabox' ) ) {
			return;
		}

		// If current user isn't an admin and we're restricting metaboxes to admins only, then exit.
		if ( ! current_user_can( 'manage_options' ) && \crp_get_option( 'show_metabox_admins' ) ) {
			return;
		}

		$post_types = get_post_types(
			array(
				'public' => true,
			)
		);

		foreach ( $post_types as $post_type ) {

			$this->metabox_api[ $post_type ] = new Metabox_API(
				array(
					'settings_key'           => $this->settings_key,
					'prefix'                 => $this->prefix,
					'post_type'              => $post_type,
					'title'                  => __( 'Contextual Related Posts', 'contextual-related-posts' ),
					'registered_settings'    => $this->get_registered_settings(),
					'checkbox_modified_text' => __( 'Modified from default', 'contextual-related-posts' ),
				)
			);
		}
	}

	/**
	 * Get registered settings for metabox.
	 *
	 * @return array Registered settings.
	 */
	public function get_registered_settings() {

		$thumb_meta = \crp_get_option( 'thumb_meta' );

		$settings = array(
			'snippet_type'         => array(
				'id'      => 'snippet_type',
				'name'    => __( 'Snippet Type', 'contextual-related-posts' ),
				'desc'    => __( 'Select the type of snippet you want to add. You will need to update/save this page in order to update the editor format above.', 'contextual-related-posts' ),
				'type'    => 'select',
				'default' => 'html',
				'options' => array(
					'html' => __( 'HTML', 'contextual-related-posts' ),
					'js'   => __( 'Javascript', 'contextual-related-posts' ),
					'css'  => __( 'CSS', 'contextual-related-posts' ),
				),
			),
			'step1_header'         => array(
				'id'   => 'step1_header',
				'name' => '<h3>' . esc_html__( 'Step 1: Where to display this', 'contextual-related-posts' ) . '</h3>',
				'desc' => '',
				'type' => 'header',
			),
			'add_to_header'        => array(
				'id'      => 'add_to_header',
				'name'    => __( 'Add to Header', 'contextual-related-posts' ),
				'desc'    => '',
				'type'    => 'checkbox',
				'options' => false,
			),
			'add_to_footer'        => array(
				'id'      => 'add_to_footer',
				'name'    => __( 'Add to Footer', 'contextual-related-posts' ),
				'desc'    => '',
				'type'    => 'checkbox',
				'options' => false,
			),
			'content_before'       => array(
				'id'      => 'content_before',
				'name'    => __( 'Add before Content', 'contextual-related-posts' ),
				'desc'    => __( 'When enabled the contents of this snippet are automatically added before the content of posts based on the selection below.', 'contextual-related-posts' ),
				'type'    => 'checkbox',
				'options' => false,
			),
			'content_after'        => array(
				'id'      => 'content_after',
				'name'    => esc_html__( 'Add after Content', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'When enabled the contents of this snippet are automatically added after the content of posts based on the selection below.', 'contextual-related-posts' ),
				'type'    => 'checkbox',
				'options' => false,
			),
			'step2_header'         => array(
				'id'   => 'step2_header',
				'name' => '<h3>' . esc_html__( 'Step 2: Conditions', 'contextual-related-posts' ) . '</h3>',
				'desc' => esc_html__( 'Select at least one condition below to display the contents of this snippet. Leaving any of the conditions blank will ignore it. Leaving all blank will ignore the snippet. If you want to include the snippet on all posts, then you can use the Global Settings.', 'contextual-related-posts' ),
				'type' => 'header',
			),
			'include_relation'     => array(
				'id'      => 'include_relation',
				'name'    => esc_html__( 'The logical relationship between each condition below', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'Selecting OR would match any of the condition below and selecting AND would match all the conditions below.', 'contextual-related-posts' ),
				'type'    => 'radio',
				'default' => 'or',
				'options' => array(
					'or'  => esc_html__( 'OR', 'contextual-related-posts' ),
					'and' => esc_html__( 'AND', 'contextual-related-posts' ),
				),
			),
			'include_on_posttypes' => array(
				'id'      => 'include_on_posttypes',
				'name'    => esc_html__( 'Include on these post types', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'Select on which post types to display the contents of this snippet.', 'contextual-related-posts' ),
				'type'    => 'posttypes',
				'options' => '',
			),
			'include_on_posts'     => array(
				'id'      => 'include_on_posts',
				'name'    => esc_html__( 'Include on these Post IDs', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'Enter a comma-separated list of post, page or custom post type IDs on which to include the code. Any incorrect ids will be removed when saving.', 'contextual-related-posts' ),
				'size'    => 'large',
				'type'    => 'postids',
				'options' => '',
			),
			'include_on_category'  => array(
				'id'               => 'include_on_category',
				'name'             => esc_html__( 'Include on these Categories', 'contextual-related-posts' ),
				'desc'             => esc_html__( 'Comma separated list of category slugs. The field above has an autocomplete so simply start typing in the starting letters and it will prompt you with options. Does not support custom taxonomies.', 'contextual-related-posts' ),
				'type'             => 'csv',
				'options'          => '',
				'size'             => 'large',
				'field_class'      => 'category_autocomplete',
				'field_attributes' => array(
					'data-wp-taxonomy' => 'category',
				),
			),
			'include_on_post_tag'  => array(
				'id'               => 'include_on_post_tag',
				'name'             => esc_html__( 'Include on these Tags', 'contextual-related-posts' ),
				'desc'             => esc_html__( 'Comma separated list of tag slugs. The field above has an autocomplete so simply start typing in the starting letters and it will prompt you with options. Does not support custom taxonomies.', 'contextual-related-posts' ),
				'type'             => 'csv',
				'options'          => '',
				'size'             => 'large',
				'field_class'      => 'category_autocomplete',
				'field_attributes' => array(
					'data-wp-taxonomy' => 'post_tag',
				),
			),
			'step3_header'         => array(
				'id'   => 'step3_header',
				'name' => '<h3>' . esc_html__( 'Step 3: Priority', 'contextual-related-posts' ) . '</h3>',
				'desc' => '',
				'type' => 'header',
			),
			'include_priority'     => array(
				'id'      => 'include_priority',
				'name'    => esc_html__( 'Priority', 'contextual-related-posts' ),
				'desc'    => esc_html__( 'Used to specify the order in which the code snippets are added to the content. Lower numbers correspond with earlier addition, and functions with the same priority are added in the order in which they were added, typically by post ID.', 'contextual-related-posts' ),
				'type'    => 'number',
				'size'    => 'small',
				'min'     => 0,
				'default' => 10,
				'options' => '',
			),
		);

		/**
		 * Filter array of registered settings for metabox.
		 *
		 * @param array $settings Registered settings.
		 */
		$settings = apply_filters( $this->prefix . '_metabox_settings', $settings );

		return $settings;
	}
}

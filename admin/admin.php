<?php
/**
 * Contextual Related Posts Admin interface.
 *
 * This page is accessible via Settings > Contextual Related Posts
 *
 * @package   Contextual_Related_Posts
 * @author    Ajay D'Souza <me@ajaydsouza.com>
 * @license   GPL-2.0+
 * @link      https://webberzone.com
 * @copyright 2009-2015 Ajay D'Souza
 */

/**** If this file is called directly, abort. ****/
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Function generates the plugin settings page.
 *
 * @since	1.0.1
 */
function crp_options() {

	global $wpdb;

	$crp_settings = crp_read_options();

	$wp_post_types	= get_post_types( array(
		'public'	=> true,
	) );
	parse_str( $crp_settings['post_types'], $post_types );
	$posts_types_inc = array_intersect( $wp_post_types, $post_types );

	parse_str( $crp_settings['exclude_on_post_types'], $exclude_on_post_types );
	$posts_types_excl = array_intersect( $wp_post_types, $exclude_on_post_types );

	// Temporary check if default styles are off and rounded thumbnails are selected - will be eventually deprecated.
	// This is a mismatch, so we force it to no style.
	if ( ( ! $crp_settings['include_default_style'] ) && ( 'rounded_thumbs' === $crp_settings['crp_styles'] ) ) {
		$crp_settings['crp_styles'] = 'no_style';
		update_option( 'ald_crp_settings', $crp_settings );
	}
	if ( ( $crp_settings['include_default_style'] ) && ( 'rounded_thumbs' !== $crp_settings['crp_styles'] ) ) {
		$crp_settings['crp_styles'] = 'rounded_thumbs';
		update_option( 'ald_crp_settings', $crp_settings );
	}

	if ( ( isset( $_POST['crp_save'] ) ) && ( check_admin_referer( 'crp-plugin-settings' ) ) ) { // Input var okay.

		/**** General options ***/
		$crp_settings['cache'] = isset( $_POST['cache'] ) ? true : false;
		$crp_settings['limit'] = absint( $_POST['limit'] );
		$crp_settings['daily_range'] = absint( $_POST['daily_range'] );
		$crp_settings['match_content'] = isset( $_POST['match_content'] ) ? true : false;
		$crp_settings['match_content_words'] = min( CRP_MAX_WORDS, absint( $_POST['match_content_words'] ) );

		$crp_settings['add_to_content'] = ( isset( $_POST['add_to_content'] ) ? true : false );
		$crp_settings['add_to_page'] = ( isset( $_POST['add_to_page'] ) ? true : false );
		$crp_settings['add_to_feed'] = ( isset( $_POST['add_to_feed'] ) ? true : false );
		$crp_settings['add_to_home'] = ( isset( $_POST['add_to_home'] ) ? true : false );
		$crp_settings['add_to_category_archives'] = ( isset( $_POST['add_to_category_archives'] ) ? true : false );
		$crp_settings['add_to_tag_archives'] = ( isset( $_POST['add_to_tag_archives'] ) ? true : false );
		$crp_settings['add_to_archives'] = ( isset( $_POST['add_to_archives'] ) ? true : false );

		$crp_settings['content_filter_priority'] = absint( $_POST['content_filter_priority'] );
		$crp_settings['insert_after_paragraph'] = ( -1 === $_POST['insert_after_paragraph'] || '' === $_POST['insert_after_paragraph'] ) ? -1 : intval( $_POST['insert_after_paragraph'] );
		$crp_settings['show_metabox'] = ( isset( $_POST['show_metabox'] ) ? true : false );
		$crp_settings['show_metabox_admins'] = ( isset( $_POST['show_metabox_admins'] ) ? true : false );
		$crp_settings['show_credit'] = ( isset( $_POST['show_credit'] ) ? true : false );

		/**** Output options ****/
		$crp_settings['title'] = wp_kses_post( wp_unslash( $_POST['title'] ) );
		$crp_settings['blank_output'] = 'blank' === $_POST['blank_output'] ? true : false;
		$crp_settings['blank_output_text'] = wp_kses_post( wp_unslash( $_POST['blank_output_text'] ) );

		$crp_settings['show_excerpt'] = ( isset( $_POST['show_excerpt'] ) ? true : false );
		$crp_settings['show_date'] = ( isset( $_POST['show_date'] ) ? true : false );
		$crp_settings['show_author'] = ( isset( $_POST['show_author'] ) ? true : false );
		$crp_settings['excerpt_length'] = absint( $_POST['excerpt_length'] );
		$crp_settings['title_length'] = absint( $_POST['title_length'] );

		$crp_settings['link_new_window'] = ( isset( $_POST['link_new_window'] ) ? true : false );
		$crp_settings['link_nofollow'] = ( isset( $_POST['link_nofollow'] ) ? true : false );

		$crp_settings['before_list'] = wp_kses_post( wp_unslash( $_POST['before_list'] ) );
		$crp_settings['after_list'] = wp_kses_post( wp_unslash( $_POST['after_list'] ) );
		$crp_settings['before_list_item'] = wp_kses_post( wp_unslash( $_POST['before_list_item'] ) );
		$crp_settings['after_list_item'] = wp_kses_post( wp_unslash( $_POST['after_list_item'] ) );

		$crp_settings['exclude_on_post_ids'] = empty( $_POST['exclude_on_post_ids'] ) ? '' : implode( ',', array_map( 'absint', explode( ',', sanitize_text_field( wp_unslash( $_POST['exclude_on_post_ids'] ) ) ) ) );
		$crp_settings['exclude_post_ids'] = empty( $_POST['exclude_post_ids'] ) ? '' : implode( ',', array_map( 'absint', explode( ',', sanitize_text_field( wp_unslash( $_POST['exclude_post_ids'] ) ) ) ) );

		/**** Thumbnail options ****/
		$crp_settings['post_thumb_op'] = sanitize_text_field( wp_unslash( $_POST['post_thumb_op'] ) );

		$crp_settings['thumb_size'] = sanitize_text_field( wp_unslash( $_POST['thumb_size'] ) );

		if ( 'crp_thumbnail' !== $crp_settings['thumb_size'] ) {
			$crp_thumb_size = crp_get_all_image_sizes( $crp_settings['thumb_size'] );

			$crp_settings['thumb_height'] = absint( $crp_thumb_size['height'] );
			$crp_settings['thumb_width'] = absint( $crp_thumb_size['width'] );
			$crp_settings['thumb_crop'] = $crp_thumb_size['crop'];
		} else {
			$crp_settings['thumb_height'] = absint( $_POST['thumb_height'] );
			$crp_settings['thumb_width'] = absint( $_POST['thumb_width'] );
			$crp_settings['thumb_crop'] = ( isset( $_POST['thumb_crop'] ) ? true : false );
		}

		$crp_settings['thumb_html'] = sanitize_text_field( wp_unslash( $_POST['thumb_html'] ) );

		$crp_settings['thumb_meta'] = empty( $_POST['thumb_meta'] ) ? 'post-image' : sanitize_text_field( wp_unslash( $_POST['thumb_meta'] ) );
		$crp_settings['scan_images'] = ( isset( $_POST['scan_images'] ) ? true : false );
		$crp_settings['thumb_default'] = ( ( '' === esc_url_raw( $_POST['thumb_default'] ) ) || ( '/default.png' === esc_url_raw( $_POST['thumb_default'] ) ) ) ? CRP_PLUGIN_URL . '/default.png' : esc_url_raw( $_POST['thumb_default'] );
		$crp_settings['thumb_default_show'] = ( isset( $_POST['thumb_default_show'] ) ? true : false );

		/**** Feed options ****/
		$crp_settings['limit_feed'] = absint( $_POST['limit_feed'] );
		$crp_settings['post_thumb_op_feed'] = sanitize_text_field( wp_unslash( $_POST['post_thumb_op_feed'] ) );
		$crp_settings['thumb_height_feed'] = absint( $_POST['thumb_height_feed'] );
		$crp_settings['thumb_width_feed'] = absint( $_POST['thumb_width_feed'] );
		$crp_settings['show_excerpt_feed'] = ( isset( $_POST['show_excerpt_feed'] ) ? true : false );

		/**** Styles ****/
		$crp_settings['custom_CSS'] = wp_kses_post( wp_unslash( $_POST['custom_CSS'] ) );

		$crp_settings['crp_styles'] = sanitize_text_field( wp_unslash( $_POST['crp_styles'] ) );

		if ( 'rounded_thumbs' === $crp_settings['crp_styles'] ) {
			$crp_settings['include_default_style'] = true;
			$crp_settings['post_thumb_op'] = 'inline';
			$crp_settings['show_excerpt'] = false;
			$crp_settings['show_author'] = false;
			$crp_settings['show_date'] = false;
		} elseif ( 'text_only' === $crp_settings['crp_styles'] ) {
			$crp_settings['include_default_style'] = false;
			$crp_settings['post_thumb_op'] = 'text_only';
		} else {
			$crp_settings['include_default_style'] = false;
		}

		/**** Exclude categories ****/
		$exclude_categories_slugs = array_map( 'trim', explode( ',', sanitize_text_field( wp_unslash( $_POST['exclude_cat_slugs'] ) ) ) );

		foreach ( $exclude_categories_slugs as $exclude_categories_slug ) {
			$category_obj = get_term_by( 'name', $exclude_categories_slug, 'category' );

			// Fall back to slugs since that was the default format before v2.4.0.
			if ( false === $category_obj ) {
				$category_obj = get_term_by( 'slug', $exclude_categories_slug, 'category' );
			}
			if ( isset( $category_obj->term_taxonomy_id ) ) {
				$exclude_categories[] = $category_obj->term_taxonomy_id;
				$exclude_cat_slugs[] = $category_obj->name;
			}
		}
		$crp_settings['exclude_categories'] = isset( $exclude_categories ) ? join( ',', $exclude_categories ) : '';
		$crp_settings['exclude_cat_slugs'] = isset( $exclude_cat_slugs ) ? join( ',', $exclude_cat_slugs ) : '';

		/**** Post types to include ****/
		$wp_post_types	= get_post_types( array(
			'public'	=> true,
		) );
		$post_types_arr = ( isset( $_POST['post_types'] ) && is_array( $_POST['post_types'] ) ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['post_types'] ) ) : array( 'post' => 'post' );
		$post_types = array_intersect( $wp_post_types, $post_types_arr );
		$crp_settings['post_types'] = http_build_query( $post_types, '', '&' );

		/**** Post types to exclude display on ****/
		$post_types_excl_arr = ( isset( $_POST['exclude_on_post_types'] ) && is_array( $_POST['exclude_on_post_types'] ) ) ? $_POST['exclude_on_post_types'] : array();
		$exclude_on_post_types = array_intersect( $wp_post_types, $post_types_excl_arr );
		$crp_settings['exclude_on_post_types'] = http_build_query( $exclude_on_post_types, '', '&' );

		/**
		 * Filters $crp_settings before it is saved into the database
		 *
		 * @since	2.0.0
		 *
		 * @param	array	$crp_settings	CRP settings
		 * @param	array	$_POST			POST array that consists of the saved settings
		 */
		$crp_settings = apply_filters( 'crp_save_options', $crp_settings, $_POST );

		/**** Update CRP options into the database ****/
		update_option( 'ald_crp_settings', $crp_settings );
		$crp_settings = crp_read_options();

		parse_str( $crp_settings['post_types'], $post_types );
		$posts_types_inc = array_intersect( $wp_post_types, $post_types );

		parse_str( $crp_settings['exclude_on_post_types'], $exclude_on_post_types );
		$posts_types_excl = array_intersect( $wp_post_types, $exclude_on_post_types );

		// Delete the cache.
		crp_cache_delete();

		/* Echo a success message */
		$str = '<div id="message" class="notice is-dismissible updated"><p>' . __( 'Options saved successfully. If enabled, the cache has been cleared.', 'contextual-related-posts' ) . '</p>';

		if ( 'rounded_thumbs' === $crp_settings['crp_styles'] ) {
			$str .= '<p>' . __( 'Rounded Thumbnails style selected. Author, Excerpt and Date will not be displayed.', 'contextual-related-posts' ) . '</p>';
		}
		if ( 'text_only' === $crp_settings['crp_styles'] ) {
			$str .= '<p>' . __( 'Text Only style selected. Thumbnails will not be displayed.', 'contextual-related-posts' ) . '</p>';
		}
		if ( 'crp_thumbnail' !== $crp_settings['thumb_size'] ) {
			$str .= '<p>' . sprintf( __( 'Pre-built thumbnail size selected. Thumbnail set to %1$d x %1$d.', 'contextual-related-posts' ), $crp_settings['thumb_width'], $crp_settings['thumb_height'] ) . '</p>';
		}

		$str .= '</div>';

		echo $str; // WPCS: XSS OK.
	}

	if ( ( isset( $_POST['crp_default'] ) ) && ( check_admin_referer( 'crp-plugin-settings' ) ) ) {
		delete_option( 'ald_crp_settings' );
		$crp_settings = crp_default_options();
		update_option( 'ald_crp_settings', $crp_settings );

		$crp_settings = crp_read_options();

		$wp_post_types	= get_post_types( array(
			'public'	=> true,
		) );
		parse_str( $crp_settings['post_types'], $post_types );
		$posts_types_inc = array_intersect( $wp_post_types, $post_types );

		parse_str( $crp_settings['exclude_on_post_types'], $exclude_on_post_types );
		$posts_types_excl = array_intersect( $wp_post_types, $exclude_on_post_types );

		$str = '<div id="message" class="updated fade"><p>' . __( 'Options set to Default.', 'contextual-related-posts' ) . '</p></div>';
		echo $str; // WPCS: XSS ok.
	}

	if ( ( isset( $_POST['crp_recreate'] ) ) && ( check_admin_referer( 'crp-plugin-settings' ) ) ) {

		crp_delete_index();
		crp_create_index();

		$str = '<div id="message" class="updated fade"><p>' . __( 'Index recreated', 'contextual-related-posts' ) . '</p></div>';
		echo $str; // WPCS: XSS ok.
	}

	/**** Include the views page ****/
	include_once( 'main-view.php' );
}


/**
 * Add a link under Settings to the plugins settings page.
 *
 * @version 1.0.1
 */
function crp_adminmenu() {
	$plugin_page = add_options_page(
		'Contextual Related Posts',
		__( 'Related Posts', 'contextual-related-posts' ),
		'manage_options',
		'crp_options',
		'crp_options'
	);
	add_action( 'admin_head-' . $plugin_page, 'crp_adminhead' );
}
add_action( 'admin_menu', 'crp_adminmenu' );


/**
 * Function to add CSS and JS to the Admin header.
 *
 * @since 1.2
 */
function crp_adminhead() {

	wp_enqueue_script( 'common' );
	wp_enqueue_script( 'wp-lists' );
	wp_enqueue_script( 'postbox' );
	wp_enqueue_script( 'plugin-install' );
	wp_enqueue_script( 'suggest' );

	add_thickbox();

?>
	<style type="text/css">
		.postbox .handlediv:before {
			right:12px;
			font:400 20px/1 dashicons;
			speak:none;
			display:inline-block;
			top:0;
			position:relative;
			-webkit-font-smoothing:antialiased;
			-moz-osx-font-smoothing:grayscale;
			text-decoration:none!important;
			content:'\f142';
			padding:8px 10px;
		}
		.postbox.closed .handlediv:before {
			content: '\f140';
		}
		.wrap h1:before {
		    content: "\f237";
		    display: inline-block;
		    -webkit-font-smoothing: antialiased;
		    font: normal 29px/1 'dashicons';
		    vertical-align: middle;
		    margin-right: 0.3em;
		}
	</style>

	<script type="text/javascript">
		//<![CDATA[
		jQuery(document).ready( function($) {
			/**** close postboxes that should be closed ****/
			$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
			/**** postboxes setup ****/
			postboxes.add_postbox_toggles('crp_options');
		});

	    // Function to add auto suggest.
	    function setSuggest( id, taxonomy ) {
	        jQuery('#' + id).suggest("<?php echo admin_url( 'admin-ajax.php?action=ajax-tag-search&tax=' ); ?>" + taxonomy, {multiple:true, multipleSep: ","});
	    }

		function clearCache() {
			jQuery.post(ajaxurl, {action: 'crp_clear_cache'}, function(response, textStatus, jqXHR) {
				alert( response.message );
			}, 'json');
		}

		function checkForm() {
			answer = true;
			if (siw && siw.selectingSomething)
				answer = false;
			return answer;
		}//

	//]]>
	</script>
<?php
}


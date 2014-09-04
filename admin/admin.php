<?php
/**
 * Contextual Related Posts Admin interface.
 *
 * This page is accessible via Settings > Contextual Related Posts
 *
 * @package   Contextual_Related_Posts
 * @author    Ajay D'Souza <me@ajaydsouza.com>
 * @license   GPL-2.0+
 * @link      http://ajaydsouza.com
 * @copyright 2009-2014 Ajay D'Souza
 */

/**** If this file is called directly, abort. ****/
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Function generates the plugin settings page.
 *
 * @since	1.0.1
 *
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

	if ( ( isset( $_POST['crp_save'] ) ) && ( check_admin_referer( 'crp-plugin-settings' ) ) ) {

		/**** General options ***/
		$crp_settings['cache'] = ( isset( $_POST['cache'] ) ? true : false );
		$crp_settings['limit'] = intval( $_POST['limit'] );
		$crp_settings['daily_range'] = intval( $_POST['daily_range'] );
		$crp_settings['match_content'] = ( isset( $_POST['match_content'] ) ? true : false );
		$crp_settings['match_content_words'] = intval( $_POST['match_content_words'] );

		$crp_settings['add_to_content'] = ( isset( $_POST['add_to_content'] ) ? true : false );
		$crp_settings['add_to_page'] = ( isset( $_POST['add_to_page'] ) ? true : false );
		$crp_settings['add_to_feed'] = ( isset( $_POST['add_to_feed'] ) ? true : false );
		$crp_settings['add_to_home'] = ( isset( $_POST['add_to_home'] ) ? true : false );
		$crp_settings['add_to_category_archives'] = ( isset( $_POST['add_to_category_archives'] ) ? true : false );
		$crp_settings['add_to_tag_archives'] = ( isset( $_POST['add_to_tag_archives'] ) ? true : false );
		$crp_settings['add_to_archives'] = ( isset( $_POST['add_to_archives'] ) ? true : false );

		$crp_settings['content_filter_priority'] = intval( $_POST['content_filter_priority'] );
		$crp_settings['show_credit'] = ( isset( $_POST['show_credit'] ) ? true : false );

		/**** Output options ****/
		$crp_settings['title'] = wp_kses_post( $_POST['title'] );
		$crp_settings['blank_output'] = ( ( $_POST['blank_output'] == 'blank' ) ? true : false );
		$crp_settings['blank_output_text'] = wp_kses_post( $_POST['blank_output_text'] );

		$crp_settings['show_excerpt'] = ( isset( $_POST['show_excerpt'] ) ? true : false );
		$crp_settings['show_date'] = ( isset( $_POST['show_date'] ) ? true : false );
		$crp_settings['show_author'] = ( isset( $_POST['show_author'] ) ? true : false );
		$crp_settings['excerpt_length'] = intval( $_POST['excerpt_length'] );
		$crp_settings['title_length'] = intval( $_POST['title_length'] );

		$crp_settings['link_new_window'] = ( isset( $_POST['link_new_window'] ) ? true : false );
		$crp_settings['link_nofollow'] = ( isset( $_POST['link_nofollow'] ) ? true : false );

		$crp_settings['before_list'] = wp_kses_post( $_POST['before_list'] );
		$crp_settings['after_list'] = wp_kses_post( $_POST['after_list'] );
		$crp_settings['before_list_item'] = wp_kses_post( $_POST['before_list_item'] );
		$crp_settings['after_list_item'] = wp_kses_post( $_POST['after_list_item'] );

		$crp_settings['exclude_on_post_ids'] = $_POST['exclude_on_post_ids'] == '' ? '' : implode( ',', array_map( 'intval', explode( ",", $_POST['exclude_on_post_ids'] ) ) );
		$crp_settings['exclude_post_ids'] = $_POST['exclude_post_ids'] == '' ? '' : implode( ',', array_map( 'intval', explode( ",", $_POST['exclude_post_ids'] ) ) );

		$crp_settings['post_thumb_op'] = wp_kses_post( $_POST['post_thumb_op'] );
		$crp_settings['thumb_height'] = intval( $_POST['thumb_height'] );
		$crp_settings['thumb_width'] = intval( $_POST['thumb_width'] );
		$crp_settings['thumb_crop'] = ( isset( $_POST['thumb_crop'] ) ? true : false );
		$crp_settings['thumb_html'] = $_POST['thumb_html'];
		$crp_settings['thumb_meta'] = ( '' == $_POST['thumb_meta'] ? 'post-image' : wp_kses_post( $_POST['thumb_meta'] ) );
		$crp_settings['scan_images'] = ( isset( $_POST['scan_images'] ) ? true : false );
		$crp_settings['thumb_default'] = wp_kses_post( $_POST['thumb_default'] );
		$crp_settings['thumb_default_show'] = ( isset( $_POST['thumb_default_show'] ) ? true : false );
		$crp_settings['thumb_timthumb'] = ( isset( $_POST['thumb_timthumb'] ) ? true : false );
		$crp_settings['thumb_timthumb_q'] = intval( $_POST['thumb_timthumb_q'] );

		/**** Feed options ****/
		$crp_settings['limit_feed'] = intval( $_POST['limit_feed'] );
		$crp_settings['post_thumb_op_feed'] = wp_kses_post( $_POST['post_thumb_op_feed'] );
		$crp_settings['thumb_height_feed'] = intval( $_POST['thumb_height_feed'] );
		$crp_settings['thumb_width_feed'] = intval( $_POST['thumb_width_feed'] );
		$crp_settings['show_excerpt_feed'] = ( isset( $_POST['show_excerpt_feed'] ) ? true : false );

		/**** Custom styles ****/
		$crp_settings['custom_CSS'] = wp_kses_post( $_POST['custom_CSS'] );

		if ( isset( $_POST['include_default_style'] ) ) {
			$crp_settings['include_default_style'] = true;
			$crp_settings['post_thumb_op'] = 'inline';
			$crp_settings['thumb_height'] = 150;
			$crp_settings['thumb_width'] = 150;
			$crp_settings['show_excerpt'] = false;
			$crp_settings['show_author'] = false;
			$crp_settings['show_date'] = false;
		} else {
			$crp_settings['include_default_style'] = false;
		}

		/**** Exclude categories ****/
		$crp_settings['exclude_cat_slugs'] = wp_kses_post( $_POST['exclude_cat_slugs'] );
		$exclude_categories_slugs = explode( ", ", $crp_settings['exclude_cat_slugs'] );

		foreach ( $exclude_categories_slugs as $exclude_categories_slug ) {
			$catObj = get_category_by_slug( $exclude_categories_slug );
			if ( isset( $catObj->term_id ) ) $exclude_categories[] = $catObj->term_id;
		}
		$crp_settings['exclude_categories'] = ( isset( $exclude_categories ) ) ? join( ',', $exclude_categories ) : '';

		/**** Post types to include ****/
		$wp_post_types	= get_post_types( array(
			'public'	=> true,
		) );
		$post_types_arr = ( isset( $_POST['post_types'] ) && is_array( $_POST['post_types'] ) ) ? $_POST['post_types'] : array( 'post' => 'post' );
		$post_types = array_intersect( $wp_post_types, $post_types_arr );
		$crp_settings['post_types'] = http_build_query( $post_types, '', '&' );

		/**** Post types to exclude display on ****/
		$post_types_excl_arr = ( isset( $_POST['exclude_on_post_types'] ) && is_array( $_POST['exclude_on_post_types'] ) ) ? $_POST['exclude_on_post_types'] : array();
		$exclude_on_post_types = array_intersect( $wp_post_types, $post_types_excl_arr );
		$crp_settings['exclude_on_post_types'] = http_build_query( $exclude_on_post_types, '', '&' );

		/**
		 * Filters $crp_settings before it is saved into the database
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

		delete_post_meta_by_key( 'crp_related_posts' ); // Delete the cache
		delete_post_meta_by_key( 'crp_related_posts_widget' ); // Delete the cache

		$str = '<div id="message" class="updated fade"><p>'. __( 'Options saved successfully.', CRP_LOCAL_NAME ) .'</p></div>';
		echo $str;
	}

	if ( ( isset($_POST['crp_default'] ) ) && ( check_admin_referer( 'crp-plugin-settings' ) ) ) {
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

		$str = '<div id="message" class="updated fade"><p>'. __( 'Options set to Default.', CRP_LOCAL_NAME ) .'</p></div>';
		echo $str;
	}

	if ( ( isset( $_POST['crp_recreate'] ) ) && ( check_admin_referer( 'crp-plugin-settings' ) ) ) {
		$wpdb->query( "ALTER TABLE " . $wpdb->posts . " DROP INDEX crp_related" );
		$wpdb->query( "ALTER TABLE " . $wpdb->posts . " DROP INDEX crp_related_title" );
		$wpdb->query( "ALTER TABLE " . $wpdb->posts . " DROP INDEX crp_related_content" );

		crp_single_activate();

		$str = '<div id="message" class="updated fade"><p>'. __( 'Index recreated', CRP_LOCAL_NAME ) .'</p></div>';
		echo $str;
	}

	/**** Include the views page ****/
	include_once( 'main-view.php' );
}


/**
 * Add a link under Settings to the plugins settings page.
 *
 * @version 1.0.1
 *
 */
function crp_adminmenu() {
	$plugin_page = add_options_page(
		__( "Contextual Related Posts", CRP_LOCAL_NAME ),
		__( "Related Posts", CRP_LOCAL_NAME ),
		'manage_options',
		'crp_options',
		'crp_options'
	);
	add_action( 'admin_head-'. $plugin_page, 'crp_adminhead' );
}
add_action( 'admin_menu', 'crp_adminmenu' );


/**
 * Function to add CSS and JS to the Admin header.
 *
 * @since 1.2
 *
 */
function crp_adminhead() {
	global $crp_url;
	wp_enqueue_script( 'common' );
	wp_enqueue_script( 'wp-lists' );
	wp_enqueue_script( 'postbox' );
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
	.wrap h2:before {
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
		//]]>
	</script>

	<link rel="stylesheet" type="text/css" href="<?php echo $crp_url ?>/admin/wick/wick.css" />
	<script type="text/javascript" language="JavaScript">
		//<![CDATA[
		function clearCache() {
			/**** since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php ****/
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

		<?php
		function wick_data() {
			global $wpdb;

			$categories = get_categories( 'hide_empty=0' );
			$str = 'collection = [';
			foreach ( $categories as $cat ) {
				$str .= "'" . $cat->slug . "',";
			}
			$str = substr( $str, 0, -1 );	// Remove trailing comma
			$str .= '];';

			echo $str;
		}
		wick_data();
		?>
	//]]>
	</script>
	<script type="text/javascript" src="<?php echo $crp_url ?>/admin/wick/wick.js"></script>
<?php
}


/**
 * Add link to WordPress plugin action links.
 *
 * @version	1.8.10
 *
 * @param	array	$links
 * @return	array	Links array with our settings link added
 */
function crp_plugin_actions_links( $links ) {

	return array_merge( array(
			'settings' => '<a href="' . admin_url( 'options-general.php?page=crp_options' ) . '">' . __('Settings', CRP_LOCAL_NAME ) . '</a>'
		), $links );

}
add_filter( 'plugin_action_links_' . plugin_basename( plugin_dir_path( __DIR__ ) . 'contextual-related-posts.php' ), 'crp_plugin_actions_links' );


/**
 * Add links to the plugin action row.
 *
 * @since	1.4
 *
 * @param	array	$links
 * @param	array	$file
 * @return	array	Links array with our links added
 */
function crp_plugin_actions( $links, $file ) {

	$plugin = plugin_basename( plugin_dir_path( __DIR__ ) . 'contextual-related-posts.php' );

	/**** Add links ****/
	if ( $file == $plugin ) {
		$links[] = '<a href="http://wordpress.org/support/plugin/contextual-related-posts">' . __( 'Support', CRP_LOCAL_NAME ) . '</a>';
		$links[] = '<a href="http://ajaydsouza.com/donate/">' . __( 'Donate', CRP_LOCAL_NAME ) . '</a>';
		$links[] = '<a href="http://github.com/ajaydsouza/contextual-related-posts">' . __( 'Contribute', CRP_LOCAL_NAME ) . '</a>';
	}
	return $links;
}
add_filter( 'plugin_row_meta', 'crp_plugin_actions', 10, 2 ); // only 2.8 and higher



/**
 * Function to add a notice to the admin page.
 *
 * @since	1.8
 *
 * @return	string	Echoed string
 */
function crp_admin_notice() {
	$plugin_settings_page = '<a href="' . admin_url( 'options-general.php?page=crp_options' ) . '">' . __( 'plugin settings page', CRP_LOCAL_NAME ) . '</a>';

	if ( ! current_user_can( 'manage_options' ) ) return;

    echo '<div class="error">
       <p>' . __( "Contextual Related Posts plugin has just been installed / upgraded. Please visit the {$plugin_settings_page} to configure.", CRP_LOCAL_NAME ).'</p>
    </div>';
}
// add_action( 'admin_notices', 'crp_admin_notice' );


/**
 * Function to clear the CRP Cache with Ajax.
 *
 * @since	1.8.10
 *
 */
function crp_ajax_clearcache() {
	global $wpdb;

	$rows = $wpdb->query( "
		DELETE FROM " . $wpdb->postmeta . "
		WHERE meta_key='crp_related_posts'
	" );

	$rows2 = $wpdb->query( "
		DELETE FROM " . $wpdb->postmeta . "
		WHERE meta_key='crp_related_posts_widget'
	" );

	/**** Did an error occur? ****/
	if ( ( $rows === false ) && ( $rows2 === false ) ) {
		exit( json_encode( array(
			'success' => 0,
			'message' => __('An error occurred clearing the cache. Please contact your site administrator.\n\nError message:\n', CRP_LOCAL_NAME) . $wpdb->print_error(),
		) ) );
	} else {	// No error, return the number of
		exit( json_encode( array(
			'success' => 1,
			'message' => ($rows+$rows2) . __(' cached row(s) cleared', CRP_LOCAL_NAME),
		) ) );
	}
}
add_action( 'wp_ajax_crp_clear_cache', 'crp_ajax_clearcache' );


/**
 * Function to add meta box in Write screens of Post, Page and Custom Post Types.
 *
 * @since	1.9.1
 *
 * @param	text	$post_type
 * @param	object	$post
 */
function crp_add_meta_box( $post_type, $post ) {

    	add_meta_box(
    		'crp_metabox',
    		__( 'Contextual Related Posts', CRP_LOCAL_NAME ),
    		'crp_call_meta_box',
    		$post_type,
    		'advanced',
    		'default'
    	);

}
add_action( 'add_meta_boxes', 'crp_add_meta_box' , 10, 2 );


/**
 * Function to call the meta box.
 *
 * @since	1.9.1
 *
 */
function crp_call_meta_box() {
	global $post, $crp_settings;

	/**** Add an nonce field so we can check for it later. ****/
	wp_nonce_field( 'crp_meta_box', 'crp_meta_box_nonce' );

	$results = get_post_meta( $post->ID, $crp_settings['thumb_meta'], true );
	$value = ( $results ) ? $results : '';
?>
	<p>
		<label for="thumb_meta"><?php _e( "Location of thumbnail:", CRP_LOCAL_NAME ); ?></label>
		<input type="text" id="thumb_meta" name="thumb_meta" value="<?php echo esc_attr( $value ) ?>" style="width:100%" />
		<em><?php _e( "Enter the full URL to the image (JPG, PNG or GIF) you'd like to use. This image will be used for the post. It will be resized to the thumbnail size set under Settings &raquo; Related Posts &raquo; Output Options", CRP_LOCAL_NAME ); ?></em>
		<em><?php _e( "The URL above is saved in the meta field: ", CRP_LOCAL_NAME ); ?></em><strong><?php echo $crp_settings['thumb_meta']; ?></strong>
	</p>

	<?php
	if ( $results ) {
		echo '<img src="' . $value . '" style="max-width:100%" />';
	}
}


/**
 * Function to save the meta box.
 *
 * @since	1.9.1
 *
 * @param mixed $post_id
 */
function crp_save_meta_box( $post_id ) {
	global $crp_settings;

    /**** Bail if we're doing an auto save ****/
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

    /**** if our nonce isn't there, or we can't verify it, bail ****/
    if ( ! isset( $_POST['crp_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['crp_meta_box_nonce'], 'crp_meta_box' ) ) return;

    /**** if our current user can't edit this post, bail ****/
    if ( ! current_user_can( 'edit_posts' ) ) return;

    if ( isset( $_POST['thumb_meta'] ) ) {
    	$thumb_meta = $_POST['thumb_meta'] == '' ? '' : $_POST['thumb_meta'];
    }

	$crp_post_meta = get_post_meta( $post_id, $crp_settings['thumb_meta'], true );
	if ( $crp_post_meta && '' != $crp_post_meta ) {
		$gotmeta = true;
	} else {
		$gotmeta = false;
	}

	if ( $gotmeta && '' != $thumb_meta ) {
		update_post_meta( $post_id, $crp_settings['thumb_meta'], $thumb_meta );
	} elseif ( ! $gotmeta && '' != $thumb_meta ) {
		add_post_meta( $post_id, $crp_settings['thumb_meta'], $thumb_meta );
	} else {
		delete_post_meta( $post_id, $crp_settings['thumb_meta'] );
	}

}
add_action( 'save_post', 'crp_save_meta_box' );


?>
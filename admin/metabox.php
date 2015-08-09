<?php
/**
 * Contextual Related Posts Metabox interface.
 *
 *
 * @package   Contextual_Related_Posts
 * @author    Ajay D'Souza <me@ajaydsouza.com>
 * @license   GPL-2.0+
 * @link      http://ajaydsouza.com
 * @copyright 2009-2015 Ajay D'Souza
 */

/**** If this file is called directly, abort. ****/
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Function to add meta box in Write screens of Post, Page and Custom Post Types.
 *
 * @since	1.9.1
 *
 * @param	text	$post_type
 * @param	object	$post
 */
function crp_add_meta_box( $post_type, $post ) {
	global $crp_settings;

	// If metaboxes are disabled, then exit
    if ( ! $crp_settings['show_metabox'] ) return;

	// If current user isn't an admin and we're restricting metaboxes to admins only, then exit
	if ( ! current_user_can( 'manage_options' ) && $crp_settings['show_metabox_admins'] ) return;

	$args = array(
	   'public'   => true,
	);
	$post_types = get_post_types( $args );

	/**
	 * Filter post types on which the meta box is displayed
	 *
	 * @since	2.2.0
	 *
	 * @param	array	$post_types	Array of post types
	 */
	$post_types = apply_filters( 'crp_meta_box_post_types', $post_types );

	if ( in_array( $post_type, $post_types ) ) {

    	add_meta_box(
    		'crp_metabox',
    		__( 'Contextual Related Posts', CRP_LOCAL_NAME ),
    		'crp_call_meta_box',
    		$post_type,
    		'advanced',
    		'default'
    	);
	}
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
		<em><?php _e( "The URL above is saved in the meta field:", CRP_LOCAL_NAME ); ?></em> <strong><?php echo $crp_settings['thumb_meta']; ?></strong>
	</p>

	<?php
	if ( $results ) {
		echo '<img src="' . $value . '" style="max-width:100%" />';
	}

	/**
	 * Action triggered when displaying Contextual Related Posts meta box
	 *
	 * @since	2.2
	 *
	 * @param	object	$post	Post object
	 */
	do_action( 'crp_call_meta_box', $post );
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

	/**
	 * Action triggered when saving Contextual Related Posts meta box settings
	 *
	 * @since	2.2
	 *
	 * @param	object	$post	Post object
	 */
	do_action( 'crp_save_meta_box', $post );
}
add_action( 'save_post', 'crp_save_meta_box' );


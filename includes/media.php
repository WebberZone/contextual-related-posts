<?php
/**
 * Image handling functions
 *
 * @package   Contextual_Related_Posts
 * @author    Ajay D'Souza <me@ajaydsouza.com>
 * @license   GPL-2.0+
 * @link      https://webberzone.com
 * @copyright 2009-2015 Ajay D'Souza
 */

/**
 * Add custom image size of thumbnail. Filters `init`.
 *
 * @since 2.0.0
 */
function crp_add_image_sizes() {
	global $crp_settings;

	if ( ! in_array( $crp_settings['thumb_size'], get_intermediate_image_sizes() ) ) {
		$crp_settings['thumb_size'] = 'crp_thumbnail';
	}

	// Add image sizes if 'crp_thumbnail' is selected or the selected thumbnail size is no longer valid.
	if ( 'crp_thumbnail' === $crp_settings['thumb_size'] ) {
		$width = empty( $crp_settings['thumb_width'] ) ? 150 : $crp_settings['thumb_width'];
		$height = empty( $crp_settings['thumb_height'] ) ? 150 : $crp_settings['thumb_height'];
		$crop = isset( $crp_settings['thumb_crop'] ) ? $crp_settings['thumb_crop'] : false;

		add_image_size( 'crp_thumbnail', $width, $height, $crop );
	}
}
add_action( 'init', 'crp_add_image_sizes' );


/**
 * Function to get the post thumbnail.
 *
 * @since 1.7
 *
 * @param array|string $args Array / Query string with arguments post thumbnails.
 * @return string Output with the post thumbnail
 */
function crp_get_the_post_thumbnail( $args = array() ) {

	global $crp_settings;

	$defaults = array(
		'postid' => '',
		'thumb_height' => '150',			// Max height of thumbnails.
		'thumb_width' => '150',			// Max width of thumbnails.
		'thumb_meta' => 'post-image',		// Meta field that is used to store the location of default thumbnail image.
		'thumb_html' => 'html',		// HTML / CSS for width and height attributes.
		'thumb_default' => '',	// Default thumbnail image.
		'thumb_default_show' => true,	// Show default thumb if none found (if false, don't show thumb at all).
		'scan_images' => false,			// Scan post for images.
		'class' => 'crp_thumb',			// Class of the thumbnail.
	);

	// Parse incomming $args into an array and merge it with $defaults.
	$args = wp_parse_args( $args, $defaults );

	// Issue notice for deprecated arguments.
	if ( isset( $args['thumb_timthumb'] ) ) {
		_deprecated_argument( __FUNCTION__, '2.1', __( 'thumb_timthumb argument has been deprecated', 'contextual-related-posts' ) );
	}

	if ( isset( $args['thumb_timthumb_q'] ) ) {
		_deprecated_argument( __FUNCTION__, '2.1', __( 'thumb_timthumb_q argument has been deprecated', 'contextual-related-posts' ) );
	}

	if ( isset( $args['filter'] ) ) {
		_deprecated_argument( __FUNCTION__, '2.1', __( 'filter argument has been deprecated', 'contextual-related-posts' ) );
	}

	$result = get_post( $args['postid'] );
	$post_title = get_the_title( $args['postid'] );

	/**
	 * Filters the title and alt message for thumbnails.
	 *
	 * @since	2.2.2
	 *
	 * @param	string	$post_title		Post tile used as thumbnail alt and title
	 * @param	object	$result			Post Object
	 */
	$post_title = apply_filters( 'crp_thumb_title', $post_title, $result );

	$output = '';
	$postimage = '';

	// Let's start fetching the thumbnail. First place to look is in the post meta defined in the Settings page.
	if ( ! $postimage ) {
		$postimage = get_post_meta( $result->ID, $args['thumb_meta'], true );
		$pick = 'meta';
	}

	// If there is no thumbnail found, check the post thumbnail.
	if ( ! $postimage ) {
		if ( false != get_post_thumbnail_id( $result->ID ) ) {
			$postthumb = wp_get_attachment_image_src( get_post_thumbnail_id( $result->ID ), $crp_settings['thumb_size'] );
			$postimage = $postthumb[0];
		}
		$pick = 'featured';
	}

	// If there is no thumbnail found, fetch the first image in the post, if enabled.
	if ( ! $postimage && $args['scan_images'] ) {

		/**
		 * Filters the post content that is used to scan for images.
		 *
		 * A filter function can be tapped into this to execute shortcodes, modify content, etc.
		 *
		 * @since	2.2.2
		 *
		 * @param	string	$result->post_content	Post content
		 * @param	object	$result		Post Object
		 */
		$post_content = apply_filters( 'crp_thumb_post_content', $result->post_content, $result );

		preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post_content, $matches );

		if ( isset( $matches[1][0] ) && $matches[1][0] ) {
			$postimage = $matches[1][0]; // We need the first one only!
		}
		if ( $postimage ) {
			$postimage_id = crp_get_attachment_id_from_url( $postimage );

			if ( false != wp_get_attachment_image_src( $postimage_id, $crp_settings['thumb_size'] ) ) {
				$postthumb = wp_get_attachment_image_src( $postimage_id, $crp_settings['thumb_size'] );
				$postimage = $postthumb[0];
			}
			$pick = 'correct';
		}
		$pick .= 'first';
	}

	// If there is no thumbnail found, fetch the first child image.
	if ( ! $postimage ) {
		$postimage = crp_get_first_image( $result->ID );
		$pick = 'firstchild';
	}

	// If no other thumbnail set, try to get the custom video thumbnail set by the Video Thumbnails plugin.
	if ( ! $postimage ) {
		$postimage = get_post_meta( $result->ID, '_video_thumbnail', true );
		$pick = 'video';
	}

	// If no thumb found and settings permit, use default thumb.
	if ( ! $postimage && $args['thumb_default_show'] ) {
		$postimage = $args['thumb_default'];
		$pick = 'default';
	}

	// Hopefully, we've found a thumbnail by now. If so, run it through the custom filter, check for SSL and create the image tag.
	if ( $postimage ) {

		/**
		 * Filters the thumbnail image URL.
		 *
		 * Use this filter to modify the thumbnail URL that is automatically created
		 * Before v2.1 this was used for cropping the post image using timthumb
		 *
		 * @since	2.1.0
		 *
		 * @param	string	$postimage		URL of the thumbnail image
		 * @param	int		$thumb_width	Thumbnail width
		 * @param	int		$thumb_height	Thumbnail height
		 * @param	object	$result			Post Object
		 */
		$postimage = apply_filters( 'crp_thumb_url', $postimage, $args['thumb_width'], $args['thumb_height'], $result );

		/* Backward compatibility */
		$thumb_timthumb = false;
		$thumb_timthumb_q = 75;

		/**
		 * Filters the thumbnail image URL.
		 *
		 * @since	1.8.10
		 * @deprecated	2.1	Use crp_thumb_url instead.
		 *
		 * @param	string	$postimage		URL of the thumbnail image
		 * @param	int		$thumb_width	Thumbnail width
		 * @param	int		$thumb_height	Thumbnail height
		 * @param	boolean	$thumb_timthumb	Enable timthumb?
		 * @param	int		$thumb_timthumb_q	Quality of timthumb thumbnail.
		 * @param	object	$result			Post Object
		 */
		$postimage = apply_filters( 'crp_postimage', $postimage, $args['thumb_width'], $args['thumb_height'], $thumb_timthumb, $thumb_timthumb_q, $result );

		if ( is_ssl() ) {
		    $postimage = preg_replace( '~http://~', 'https://', $postimage );
		}

		if ( 'css' == $args['thumb_html'] ) {
			$thumb_html = 'style="max-width:' . $args['thumb_width'] . 'px;max-height:' . $args['thumb_height'] . 'px;"';
		} elseif ( 'html' == $args['thumb_html'] ) {
			$thumb_html = 'width="' . $args['thumb_width'] . '" height="' . $args['thumb_height'] . '"';
		} else {
			$thumb_html = '';
		}

		/**
		 * Filters the thumbnail HTML and allows a filter function to add any more HTML if needed.
		 *
		 * @since	2.2.0
		 *
		 * @param	string	$thumb_html	Thumbnail HTML
		 */
		$thumb_html = apply_filters( 'crp_thumb_html', $thumb_html );

		$class = $args['class'] . ' crp_' . $pick;

		/**
		 * Filters the thumbnail classes and allows a filter function to add any more classes if needed.
		 *
		 * @since	2.2.2
		 *
		 * @param	string	$thumb_html	Thumbnail HTML
		 */
		$class = apply_filters( 'crp_thumb_class', $class );

		$output .= '<img src="' . $postimage . '" alt="' . $post_title . '" title="' . $post_title . '" ' . $thumb_html . ' class="' . $class . '" />';
	}

	/**
	 * Filters post thumbnail created for CRP.
	 *
	 * @since	1.9
	 *
	 * @param	array	$output	Formatted output
	 * @param	array	$args	Argument list
	 */
	return apply_filters( 'crp_get_the_post_thumbnail', $output, $args );
}


/**
 * Get the first child image in the post.
 *
 * @since 1.8.9
 *
 * @param mixed $post_id	Post ID.
 * @return string
 */
function crp_get_first_image( $post_id ) {
	global $crp_settings;

	$args = array(
		'numberposts' => 1,
		'order' => 'ASC',
		'post_mime_type' => 'image',
		'post_parent' => $post_id,
		'post_status' => null,
		'post_type' => 'attachment',
	);

	$attachments = get_children( $args );

	if ( $attachments ) {
		foreach ( $attachments as $attachment ) {
			$image_attributes = wp_get_attachment_image_src( $attachment->ID, $crp_settings['thumb_size'] )  ? wp_get_attachment_image_src( $attachment->ID, $crp_settings['thumb_size'] ) : wp_get_attachment_image_src( $attachment->ID, 'full' );

			/**
			 * Filters first child image from the post.
			 *
			 * @since	2.0.0
			 *
			 * @param	array	$image_attributes[0]	URL of the image
			 * @param	int		$post_id					Post ID
			 */
			return apply_filters( 'crp_get_first_image', $image_attributes[0], $post_id );
		}
	} else {
		return false;
	}
}


/**
 * Function to get the attachment ID from the attachment URL.
 *
 * @since 2.1
 *
 * @param	string $attachment_url Attachment URL.
 * @return	int		Attachment ID
 */
function crp_get_attachment_id_from_url( $attachment_url = '' ) {

	global $wpdb;
	$attachment_id = false;

	// If there is no url, return.
	if ( '' == $attachment_url ) {
		return;
	}

	// Get the upload directory paths.
	$upload_dir_paths = wp_upload_dir();

	// Make sure the upload path base directory exists in the attachment URL, to verify that we're working with a media library image.
	if ( false !== strpos( $attachment_url, $upload_dir_paths['baseurl'] ) ) {

		// If this is the URL of an auto-generated thumbnail, get the URL of the original image.
		$attachment_url = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url );

		// Remove the upload path base directory from the attachment URL.
		$attachment_url = str_replace( $upload_dir_paths['baseurl'] . '/', '', $attachment_url );

		// Finally, run a custom database query to get the attachment ID from the modified attachment URL.
		$attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $attachment_url ) );

	}

	/**
	 * Filters attachment ID generated from URL.
	 *
	 * @since	2.1.0
	 *
	 * @param	int		$attachment_id	Attachment ID
	 * @param	string	$attachment_url	Attachment URL
	 */
	return apply_filters( 'crp_get_attachment_id_from_url', $attachment_id, $attachment_url );
}


/**
 * Get all image sizes.
 *
 * @since	2.0.0
 * @param	string $size   Get specific image size.
 * @return	array	Image size names along with width, height and crop setting
 */
function crp_get_all_image_sizes( $size = '' ) {
	global $_wp_additional_image_sizes;

	/* Get the intermediate image sizes and add the full size to the array. */
	$intermediate_image_sizes = get_intermediate_image_sizes();

	foreach ( $intermediate_image_sizes as $_size ) {
		if ( in_array( $_size, array( 'thumbnail', 'medium', 'large' ) ) ) {

			$sizes[ $_size ]['name'] = $_size;
			$sizes[ $_size ]['width'] = get_option( $_size . '_size_w' );
			$sizes[ $_size ]['height'] = get_option( $_size . '_size_h' );
			$sizes[ $_size ]['crop'] = (bool) get_option( $_size . '_crop' );

	        if ( ( 0 == $sizes[ $_size ]['width'] ) && ( 0 == $sizes[ $_size ]['height'] ) ) {
	            unset( $sizes[ $_size ] );
	        }
		} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {

			$sizes[ $_size ] = array(
	            'name' => $_size,
				'width' => $_wp_additional_image_sizes[ $_size ]['width'],
				'height' => $_wp_additional_image_sizes[ $_size ]['height'],
				'crop' => (bool) $_wp_additional_image_sizes[ $_size ]['crop'],
			);
		}
	}

	/* Get only 1 size if found */
	if ( $size ) {
		if ( isset( $sizes[ $size ] ) ) {
			return $sizes[ $size ];
		} else {
			return false;
		}
	}

	/**
	 * Filters array of image sizes.
	 *
	 * @since	2.0
	 *
	 * @param	array	$sizes	Image sizes
	 */
	return apply_filters( 'crp_get_all_image_sizes', $sizes );
}



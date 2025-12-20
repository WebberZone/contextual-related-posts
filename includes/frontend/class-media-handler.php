<?php
/**
 * Media handler
 *
 * @package Contextual_Related_Posts
 */

namespace WebberZone\Contextual_Related_Posts\Frontend;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Media Handler class.
 *
 * @since 3.5.0
 */
class Media_Handler {

	/**
	 * Prefix.
	 *
	 * @var string $prefix Prefix.
	 */
	private static $prefix = 'crp';

	/**
	 * Default thumbnail URL.
	 *
	 * @var string
	 */
	protected static $default_thumb_url = WZ_CRP_DEFAULT_THUMBNAIL_URL;

	/**
	 * Posts currently being processed to prevent infinite recursion.
	 *
	 * @var array
	 */
	private static $processing_ids = array();

	/**
	 * Add custom image size of thumbnail. Filters `init`.
	 */
	public static function add_image_sizes() {
		$get_option_callback = self::$prefix . '_get_option';

		if ( ! call_user_func( $get_option_callback, 'thumb_create_sizes' ) ) {
			return;
		}
		$thumb_size      = call_user_func( $get_option_callback, 'thumb_size' );
		$thumb_size_name = self::$prefix . '_thumbnail';

		if ( ! in_array( $thumb_size, get_intermediate_image_sizes() ) ) { // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
			$thumb_size = $thumb_size_name;
		}

		// Add image sizes if $thumb_size_name is selected or the selected thumbnail size is no longer valid.
		if ( $thumb_size_name === $thumb_size ) {
			$width  = call_user_func_array( $get_option_callback, array( 'thumb_width', 150 ) );
			$height = call_user_func_array( $get_option_callback, array( 'thumb_height', 150 ) );
			$crop   = call_user_func_array( $get_option_callback, array( 'thumb_crop', true ) );

			add_image_size( $thumb_size_name, $width, $height, $crop );
		}
	}

	/**
	 * Function to get the post thumbnail.
	 *
	 * @param string|array $args {
	 *     Optional. Array or string of Query parameters.
	 *
	 *     @type int|\WP_Post $post               Post ID or \WP_Post object.
	 *     @type string      $size               Thumbnail size. Should be a pre-defined image size.
	 *     @type string      $thumb_meta         Meta field that is used to store the location of default thumbnail image.
	 *     @type string      $thumb_html         Accepted arguments are `html` or `css`.
	 *     @type string      $thumb_default      Default thumbnail image.
	 *     @type bool        $thumb_default_show Show default thumb if none found.
	 *     @type int         $scan_images        Get related posts for a specific post ID.
	 *     @type string      $class              Class of the thumbnail.
	 * }
	 * @return string  Image tag
	 */
	public static function get_the_post_thumbnail( $args = array() ) {
		$get_option_callback = self::$prefix . '_get_option';

		$defaults = array(
			'post'               => '',
			'size'               => 'thumbnail',
			'thumb_meta'         => 'post-image',
			'thumb_html'         => 'html',
			'thumb_default'      => call_user_func( $get_option_callback, 'thumb_default', '' ),
			'thumb_default_show' => true,
			'scan_images'        => true,
			'use_site_icon'      => true,
			'class'              => self::$prefix . '_thumb',
			'style'              => '',
		);

		// Parse incomming $args into an array and merge it with $defaults.
		$args = wp_parse_args( $args, $defaults );

		$result = get_post( $args['post'] );

		if ( empty( $result ) ) {
			return '';
		}

		// Recursion protection - prevent infinite loops when shortcodes trigger nested thumbnail generation.
		if ( isset( self::$processing_ids[ $result->ID ] ) ) {
			return '';
		}
		self::$processing_ids[ $result->ID ] = true;

		try {

			if ( is_string( $args['size'] ) ) {
				list( $args['thumb_width'], $args['thumb_height'] ) = self::get_thumb_size( $args['size'] );
			} else {
				$args['thumb_width']  = $args['size'][0];
				$args['thumb_height'] = $args['size'][1];
				$args['size']         = self::get_appropriate_image_size( $args['size'][0], $args['size'][1] );
			}

			$post_title = esc_attr( $result->post_title );

			$output        = '';
			$postimage     = '';
			$pick          = '';
			$attachment_id = 0;
			$extracted_alt = '';
			$alt           = '';

			// Let's start fetching the thumbnail. First place to look is in the post meta defined in the Settings page.
			$postimage = get_post_meta( $result->ID, $args['thumb_meta'], true );
			$postimage = filter_var( $postimage, FILTER_VALIDATE_URL );
			if ( $postimage ) {
				$pick          = 'meta';
				$attachment_id = self::get_cached_attachment_id( $postimage );

				$postthumb = wp_get_attachment_image_src( $attachment_id, $args['size'] );
				if ( false !== $postthumb ) {
					$postimage = $postthumb[0];
					$pick     .= 'correct';
				}
			}

			// If there is no thumbnail found, check the post thumbnail.
			if ( ! $postimage ) {
				if ( false !== get_post_thumbnail_id( $result->ID ) ) {
					$attachment_id = ( 'attachment' === $result->post_type ) ? $result->ID : get_post_thumbnail_id( $result->ID );

					$postthumb = wp_get_attachment_image_src( $attachment_id, $args['size'] );
					if ( false !== $postthumb ) {
						$postimage = $postthumb[0];
						$pick      = 'featured';
					}
				}
			}

			// If there is no thumbnail found, fetch the first image in the post, if enabled.
			if ( ! $postimage && $args['scan_images'] ) {

				// Skip content scanning for very large posts to prevent memory exhaustion.
				if ( strlen( $result->post_content ) > 50000 ) { // 50KB limit.
					$post_content = '';
				} else {
					/**
					 * Filters the post content that is used to scan for images.
					 *
					 * A filter function can be tapped into this to execute shortcodes, modify content, etc.
					 *
					 * @param string   $post_content Post content
					 * @param \WP_Post $result       Post Object
					 */
					$post_content = apply_filters( self::$prefix . '_thumb_post_content', $result->post_content, $result );
				}

				preg_match_all( '/<img\s[^>]*src=[\'"]([^\'"]+)[\'"][^>]*>/i', $post_content, $matches );
				if ( isset( $matches[1][0] ) && $matches[1][0] ) {
					$postimage     = $matches[1][0];
					$extracted_alt = self::get_alt_from_img_tag( $matches[0][0] );
					$pick          = 'first';

					$attachment_id = self::get_cached_attachment_id( $postimage );
					$postthumb     = wp_get_attachment_image_src( $attachment_id, $args['size'] );

					if ( false !== $postthumb ) {
						$postimage = $postthumb[0];
						$pick     .= 'correct';
					} else {
						// Fallback: Try to resize the original URL if no attachment found.
						$resized_url = self::resize_external_image( $postimage, $args['size'] );
						if ( $resized_url ) {
							$postimage = $resized_url;
							$pick     .= 'resized';
						}
					}
				}
			}

			// If there is no thumbnail found, fetch the first child image.
			if ( ! $postimage ) {
				$dimensions = self::get_thumb_size( $args['size'] );
				$postimage  = self::get_first_image( $result->ID, $dimensions[0], $dimensions[1] );  // Get the first image.
				$pick       = 'firstchild';
			}

			// If no other thumbnail set, try to get the custom video thumbnail set by the Video Thumbnails plugin.
			if ( ! $postimage ) {
				$postimage = get_post_meta( $result->ID, '_video_thumbnail', true );
				$postimage = filter_var( $postimage, FILTER_VALIDATE_URL );
				if ( $postimage ) {
					$pick = 'video_thumb';
				}
			}

			// If no thumb found and settings permit, use default thumb.
			if ( ! $postimage && $args['thumb_default_show'] && $args['thumb_default'] ) {
				$postimage = $args['thumb_default'];
				$pick      = 'default_thumb';

				if ( self::$default_thumb_url !== $postimage ) {
					$attachment_id = self::get_cached_attachment_id( $postimage );
					$postthumb     = wp_get_attachment_image_src( $attachment_id, $args['size'] );
					if ( false !== $postthumb ) {
						$postimage = $postthumb[0];
						$pick     .= 'correct';
					}
				}
			}

			// If no thumb found, use site icon.
			if ( ! $postimage && $args['use_site_icon'] ) {
				$postimage = get_site_icon_url( max( $args['thumb_width'], $args['thumb_height'] ) );
				$pick      = 'site_icon_max';

				// Fallback to min size if max size not available.
				if ( ! $postimage ) {
					$postimage = get_site_icon_url( min( $args['thumb_width'], $args['thumb_height'] ) );
					$pick      = 'site_icon_min';
				}
			}

			// Hopefully, we've found a thumbnail by now. If so, run it through the custom filter, check for SSL and create the image tag.
			if ( $postimage ) {

				/**
				 * Filters the thumbnail image URL.
				 *
				 * Use this filter to modify the thumbnail URL that is automatically created
				 * Before v2.1 this was used for cropping the post image using timthumb
				 *
				 * @param string   $postimage URL of the thumbnail image
				 * @param array    $args      Arguments array.
				 * @param \WP_Post $result    Post Object
				 */
				$postimage = apply_filters( self::$prefix . '_thumb_url', $postimage, $args, $result );

				if ( is_ssl() ) {
					$postimage = preg_replace( '~http://~', 'https://', $postimage );
				}

				$class = self::$prefix . "_{$pick} {$args['class']} {$args['size']}";

				if ( empty( $attachment_id ) && ! in_array( $pick, array( 'video_thumb', 'default_thumb', 'site_icon_max', 'site_icon_min' ), true ) ) {
					$attachment_id = self::get_cached_attachment_id( $postimage );
				}

				/**
				 * Flag to use the image's alt text as the thumbnail alt text.
				 *
				 * @param bool $use_image_alt Flag to use the image's alt text as the thumbnail alt text.
				 */
				$use_image_alt = apply_filters( self::$prefix . '_thumb_use_image_alt', true );

				/**
				 * Flag to use the post title as the thumbnail alt text if no alt text is found.
				 *
				 * @param bool $alt_fallback Flag to use the post title as the thumbnail alt text if no alt text is found.
				 */
				$alt_fallback = apply_filters( self::$prefix . '_thumb_alt_fallback_post_title', true );

				if ( ! empty( $attachment_id ) && $use_image_alt ) {
					$alt = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
				}

				if ( empty( $alt ) && $extracted_alt ) {
					$alt = $extracted_alt;
				}

				// If empty alt then try to get the title of the attachment.
				if ( empty( $alt ) && ! empty( $attachment_id ) ) {
					$alt = get_post_field( 'post_title', $attachment_id );
				}

				if ( empty( $alt ) ) {
					$alt = $alt_fallback ? $post_title : '';
				}

				/**
				 * Filters the thumbnail styles attribute.
				 *
				 * @param string $styles Thumbnail styles
				 */
				$attr['style'] = apply_filters( self::$prefix . '_thumb_styles', $args['style'] );

				/**
				 * Filters the thumbnail classes and allows a filter function to add any more classes if needed.
				 *
				 * @param string $class Thumbnail Class
				 */
				$attr['class'] = apply_filters( self::$prefix . '_thumb_class', $class );

				/**
				 * Filters the thumbnail alt.
				 *
				 * @param string $alt Thumbnail alt attribute
				 */
				$attr['alt'] = apply_filters( self::$prefix . '_thumb_alt', $alt );

				/**
				 * Filters the thumbnail title.
				 *
				 * @param string $post_title Thumbnail title attribute
				 */
				$attr['title'] = apply_filters( self::$prefix . '_thumb_title', $post_title );

				$attr['thumb_html']   = $args['thumb_html'];
				$attr['thumb_width']  = $args['thumb_width'];
				$attr['thumb_height'] = $args['thumb_height'];

				$output .= self::get_image_html( $postimage, $attr, $attachment_id, $args['size'] );

				if ( function_exists( 'wp_img_tag_add_srcset_and_sizes_attr' ) && ! empty( $attachment_id ) ) {
					$output = \wp_img_tag_add_srcset_and_sizes_attr( $output, self::$prefix . '_thumbnail', $attachment_id );
				}

				if ( function_exists( 'wp_img_tag_add_loading_optimization_attrs' ) ) {
					$output = \wp_img_tag_add_loading_optimization_attrs( $output, self::$prefix . '_thumbnail' );
				}
			}

			/**
			 * Filters post thumbnail HTML.
			 *
			 * @param string $output    HTML output.
			 * @param array  $args      Argument list
			 * @param string $postimage Thumbnail URL
			 */
			return apply_filters( self::$prefix . '_get_the_post_thumbnail', $output, $args, $postimage );

		} finally {
			// Clean up recursion protection - guaranteed to run even if exceptions occur.
			unset( self::$processing_ids[ $result->ID ] );
		}
	}

	/**
	 * Resize external image when attachment ID is not found.
	 *
	 * @param string $image_url Original image URL.
	 * @param string $size      Target image size.
	 * @return string|false Resized image URL or false on failure.
	 */
	private static function resize_external_image( $image_url, $size ) {
		// Check if this is a local image that can be resized.
		$upload_dir = wp_upload_dir();
		if ( empty( $upload_dir['baseurl'] ) ) {
			return false;
		}
		$base_url = $upload_dir['baseurl'];

		// Only attempt resizing for local uploads directory images.
		if ( strpos( $image_url, $base_url ) !== 0 ) {
			return false;
		}

		// Strip any existing size suffix to get the base/original image URL.
		$base_image_url = self::get_base_image_url( $image_url );

		// Convert URL to file path.
		$image_path = str_replace( $base_url, $upload_dir['basedir'], $base_image_url );
		$image_path = urldecode( $image_path ); // Handle URL-encoded characters (spaces, special chars).

		// If base image doesn't exist, try the original URL path (might be the actual original).
		if ( ! file_exists( $image_path ) ) {
			$image_path = str_replace( $base_url, $upload_dir['basedir'], $image_url );
			$image_path = urldecode( $image_path );

			if ( ! file_exists( $image_path ) ) {
				return false;
			}
		}

		// Security: Validate path stays within uploads directory (after confirming file exists).
		$real_image_path = realpath( $image_path );
		$real_upload_dir = realpath( $upload_dir['basedir'] );

		if ( false === $real_image_path || false === $real_upload_dir ||
			0 !== strpos( $real_image_path, $real_upload_dir ) ) {
			return false;
		}
		$image_path = $real_image_path;

		// Get image dimensions for the target size.
		$dimensions = self::get_thumb_size( $size );
		$width      = $dimensions[0];
		$height     = $dimensions[1];

		// Generate resized filename from the BASE image (without any size suffix).
		$path_info = pathinfo( $image_path );

		// Strip any existing size suffix from the filename to ensure clean base name.
		$base_filename    = preg_replace( '/-\d+x\d+$/', '', $path_info['filename'] );
		$resized_filename = $base_filename . "-{$width}x{$height}." . $path_info['extension'];
		$resized_path     = $path_info['dirname'] . '/' . $resized_filename;
		$resized_url      = str_replace( $upload_dir['basedir'], $upload_dir['baseurl'], $resized_path );

		// Security: Validate resized output path stays within uploads directory.
		$resized_dir      = dirname( $resized_path );
		$real_resized_dir = realpath( $resized_dir );
		if ( false === $real_resized_dir ||
			0 !== strpos( $real_resized_dir, $real_upload_dir ) ) {
			return false;
		}

		// Return existing resized image if it exists.
		if ( file_exists( $resized_path ) ) {
			return $resized_url;
		}

		// Attempt to create resized image from the original/base image.
		$image_editor = wp_get_image_editor( $image_path );
		if ( is_wp_error( $image_editor ) ) {
			return false;
		}

		// Security: Check original image dimensions to prevent memory exhaustion.
		$original_size = $image_editor->get_size();
		if ( ! is_array( $original_size ) ) {
			return false;
		}

		// Reject images larger than 10000x10000 pixels (adjustable via filter).
		$max_dimension = apply_filters( self::$prefix . '_max_image_dimension', 10000 );
		if ( $original_size['width'] > $max_dimension || $original_size['height'] > $max_dimension ) {
			return false;
		}

		$resized = $image_editor->resize( $width, $height, true );
		if ( is_wp_error( $resized ) ) {
			return false;
		}

		$saved = $image_editor->save( $resized_path );
		if ( is_wp_error( $saved ) ) {
			return false;
		}

		return $resized_url;
	}

	/**
	 * Get an HTML img element
	 *
	 * @param string       $attachment_url    Image URL.
	 * @param array        $attr              Attributes for the image markup.
	 * @param int          $attachment_id     Attachment ID.
	 * @param string|int[] $size              Image size.
	 * @return string HTML img element or empty string on failure.
	 */
	public static function get_image_html( $attachment_url, $attr = array(), $attachment_id = 0, $size = '' ) {
		// If there is an attachment ID, use wp_get_attachment_image().
		if ( $attachment_id ) {
			unset( $attr['thumb_html'], $attr['thumb_width'], $attr['thumb_height'] );
			return wp_get_attachment_image( $attachment_id, $size, false, $attr );
		}

		// If there is no URL, return an empty string.
		if ( empty( $attachment_url ) ) {
			return '';
		}

		$get_option_callback = self::$prefix . '_get_option';

		// Define default attributes.
		$default_attr = array(
			'src'          => $attachment_url,
			'thumb_html'   => call_user_func( $get_option_callback, 'thumb_html', 'html' ),
			'thumb_width'  => call_user_func( $get_option_callback, 'thumb_width', 150 ),
			'thumb_height' => call_user_func( $get_option_callback, 'thumb_height', 150 ),
			'class'        => "attachment-$size size-$size",
		);

		// Merge default attributes with provided attributes.
		$attr = wp_parse_args( $attr, $default_attr );
		$attr = self::ensure_loading_and_decoding_attrs( $attr );

		// Generate width and height string.
		$hwstring = self::get_image_hwstring( $attr );

		// Omit the `decoding` attribute if the value is invalid according to the spec.
		if ( empty( $attr['decoding'] ) || ! in_array( $attr['decoding'], array( 'async', 'sync', 'auto' ), true ) ) {
			unset( $attr['decoding'] );
		}

		/*
		 * If the default value of `lazy` for the `loading` attribute is overridden
		 * to omit the attribute for this image, ensure it is not included.
		 */
		if ( isset( $attr['loading'] ) && ! $attr['loading'] ) {
			unset( $attr['loading'] );
		}

		// If the `fetchpriority` attribute is overridden and set to false or an empty string.
		if ( isset( $attr['fetchpriority'] ) && ! $attr['fetchpriority'] ) {
			unset( $attr['fetchpriority'] );
		}

		// Generate 'srcset' and 'sizes' if not already present.
		if ( empty( $attr['srcset'] ) ) {
			$image_meta = wp_get_attachment_metadata( $attachment_id );

			if ( is_array( $image_meta ) ) {
				$size_array = array( absint( $attr['thumb_width'] ), absint( $attr['thumb_height'] ) );
				$srcset     = wp_calculate_image_srcset( $size_array, $attachment_url, $image_meta, $attachment_id );
				$sizes      = wp_calculate_image_sizes( $size_array, $attachment_url, $image_meta, $attachment_id );

				if ( $srcset && ( $sizes || ! empty( $attr['sizes'] ) ) ) {
					$attr['srcset'] = $srcset;

					if ( empty( $attr['sizes'] ) ) {
						$attr['sizes'] = $sizes;
					}
				}
			}
		}

		// Unset attributes not needed in the final img tag.
		unset( $attr['thumb_html'], $attr['thumb_width'], $attr['thumb_height'] );

		/**
		 * Filters the list of attachment image attributes.
		 *
		 * @param array  $attr Attributes for the image markup.
		 * @param string $attachment_url Image URL.
		 */
		$attr = apply_filters( self::$prefix . '_get_image_attributes', $attr, $attachment_url );
		$attr = array_map( 'esc_attr', $attr );

		// Construct the HTML img tag.
		$html = '<img ' . $hwstring;
		foreach ( $attr as $name => $value ) {
			if ( '' !== $value ) {
				$html .= " $name=\"$value\"";
			}
		}
		$html .= ' />';

		/**
		 * Filters the img tag.
		 *
		 * @param string $html           HTML img element or empty string on failure.
		 * @param string $attachment_url Image URL.
		 * @param array  $attr           Attributes for the image markup.
		 */
		return apply_filters( self::$prefix . '_get_image_html', $html, $attachment_url, $attr );
	}

	/**
	 * Ensures the loading/decoding attributes are set consistently for all thumbnails.
	 *
	 * @param array $attr Attributes array.
	 * @return array
	 */
	protected static function ensure_loading_and_decoding_attrs( array $attr ): array {
		if ( empty( $attr['loading'] ) ) {
			/**
			 * Filters the default loading attribute applied to Contextual Related Posts thumbnails.
			 *
			 * @param string|null $loading Loading attribute value or null to omit.
			 * @param array       $attr    Thumbnail attributes.
			 */
			$attr['loading'] = apply_filters( self::$prefix . '_thumbnail_loading_attribute', 'lazy', $attr );
		}

		if ( empty( $attr['decoding'] ) ) {
			/**
			 * Filters the default decoding attribute applied to Contextual Related Posts thumbnails.
			 *
			 * @param string|null $decoding Decoding attribute value or null to omit.
			 * @param array       $attr     Thumbnail attributes.
			 */
			$attr['decoding'] = apply_filters( self::$prefix . '_thumbnail_decoding_attribute', 'async', $attr );
		}

		return $attr;
	}

	/**
	 * Extract alt text from an image tag string.
	 *
	 * @param string $img_tag Image tag HTML.
	 * @return string Sanitized alt text or empty string if none found.
	 */
	private static function get_alt_from_img_tag( string $img_tag ): string {
		if ( ! preg_match( '/\salt=(\"|\')(.*?)\1/i', $img_tag, $matches ) ) {
			return '';
		}

		$alt = wp_specialchars_decode( $matches[2], ENT_QUOTES );
		$alt = sanitize_text_field( $alt );

		return $alt;
	}

	/**
	 * Retrieve width and height attributes using given width and height values.
	 *
	 * @param array $args Argument array.
	 * @return string Height-width string.
	 */
	public static function get_image_hwstring( $args = array() ) {
		$get_option_callback = self::$prefix . '_get_option';

		$default_args = array(
			'thumb_html'   => call_user_func( $get_option_callback, 'thumb_html', 'html' ),
			'thumb_width'  => call_user_func( $get_option_callback, 'thumb_width', 150 ),
			'thumb_height' => call_user_func( $get_option_callback, 'thumb_height', 150 ),
		);

		$args = wp_parse_args( $args, $default_args );

		if ( 'css' === $args['thumb_html'] ) {
			$thumb_html = ' style="max-width:' . $args['thumb_width'] . 'px;max-height:' . $args['thumb_height'] . 'px;" ';
		} elseif ( 'html' === $args['thumb_html'] ) {
			$thumb_html = ' width="' . $args['thumb_width'] . '" height="' . $args['thumb_height'] . '" ';
		} else {
			$thumb_html = '';
		}

		/**
		 * Filters the thumbnail HTML and allows a filter function to add any more HTML if needed.
		 *
		 * @param string $thumb_html Thumbnail HTML.
		 * @param array  $args       Argument array.
		 */
		return apply_filters( self::$prefix . '_thumb_html', $thumb_html, $args );
	}

	/**
	 * Get the first child image in the post.
	 *
	 * @param int|\WP_Post $postid       Post ID or WP_Post object.
	 * @param int          $thumb_width  Thumb width.
	 * @param int          $thumb_height Thumb height.
	 * @return string Location of thumbnail.
	 */
	public static function get_first_image( $postid, int $thumb_width, int $thumb_height ): string {
		$args = array(
			'numberposts'    => 1,
			'order'          => 'ASC',
			'post_mime_type' => 'image',
			'post_parent'    => $postid,
			'post_status'    => null,
			'post_type'      => 'attachment',
		);

		$attachments = get_children( $args );

		if ( empty( $attachments ) ) {
			return '';
		}

		$attachment = reset( $attachments );
		$image_size = array( $thumb_width, $thumb_height );

		if ( 0 < $attachment->ID ) {
			$image_attributes = wp_get_attachment_image_src( $attachment->ID, $image_size );

			if ( empty( $image_attributes ) ) {
				$image_attributes = wp_get_attachment_image_src( $attachment->ID, 'full' );
			}

			if ( ! empty( $image_attributes ) ) {
				/**
				 * Filter the first child image URL.
				 *
				 * @param string       $image_url     URL of the image.
				 * @param int|\WP_Post $postid        Post ID or WP_Post object.
				 * @param int          $thumb_width   Thumb width.
				 * @param int          $thumb_height  Thumb height.
				 */
				return apply_filters(
					self::$prefix . '_get_first_image',
					$image_attributes[0],
					$postid,
					$thumb_width,
					$thumb_height
				);
			}
		}

		return '';
	}

	/**
	 * Function to get the attachment ID from the attachment URL.
	 *
	 * @param string $attachment_url Attachment URL.
	 * @return int Attachment ID.
	 */
	public static function get_attachment_id_from_url( $attachment_url = '' ) {

		$attachment_id = 0;

		// If there is no URL, return.
		if ( ! $attachment_url ) {
			return $attachment_id;
		}

		// Attempt to retrieve the attachment ID from the URL.
		$attachment_id = attachment_url_to_postid( $attachment_url );

		/**
		 * Filter the attachment ID from the attachment URL.
		 *
		 * @param int    $attachment_id  Attachment ID.
		 * @param string $attachment_url Attachment URL.
		 */
		return apply_filters( self::$prefix . '_get_attachment_id_from_url', $attachment_id, $attachment_url );
	}

	/**
	 * Get cached attachment ID from URL to prevent database exhaustion.
	 *
	 * @param string $attachment_url Attachment URL.
	 * @return int Attachment ID.
	 */
	public static function get_cached_attachment_id( $attachment_url = '' ) {
		$attachment_id = 0;

		// If there is no URL, return.
		if ( ! $attachment_url ) {
			return $attachment_id;
		}

		// Check cache first.
		$cache_key = self::$prefix . '_attachment_id_' . get_current_blog_id() . '_' . hash( 'sha256', $attachment_url );
		$cached_id = wp_cache_get( $cache_key, self::$prefix . '_media' );

		if ( false !== $cached_id ) {
			return (int) $cached_id;
		}

		// Attempt to retrieve the attachment ID from the URL.
		$attachment_id = attachment_url_to_postid( $attachment_url );

		// If not found, try stripping the size suffix (e.g., -150x150, -1024x768) and lookup base URL.
		if ( 0 === $attachment_id ) {
			$base_url = self::get_base_image_url( $attachment_url );
			if ( $base_url !== $attachment_url ) {
				$attachment_id = attachment_url_to_postid( $base_url );
			}
		}

		// Cache the result for 1 hour.
		wp_cache_set( $cache_key, $attachment_id, self::$prefix . '_media', HOUR_IN_SECONDS );

		/**
		 * Filter the cached attachment ID from the attachment URL.
		 *
		 * @param int    $attachment_id  Attachment ID.
		 * @param string $attachment_url Attachment URL.
		 */
		return apply_filters( self::$prefix . '_get_cached_attachment_id', $attachment_id, $attachment_url );
	}

	/**
	 * Get the base image URL by stripping WordPress size suffixes.
	 *
	 * Converts URLs like image-150x150.jpg or image-1024x768.jpg to image.jpg
	 *
	 * @param string $url Image URL potentially with size suffix.
	 * @return string Base image URL without size suffix.
	 */
	public static function get_base_image_url( $url ) {
		// Remove WordPress size suffix (e.g., -150x150) while retaining filename and extension.
		return preg_replace( '/-\d+x\d+(?=\.[^.]+$)/', '', $url );
	}

	/**
	 * Function to get the correct height and width of the thumbnail.
	 *
	 * @param string $size Image size.
	 * @return array Width and height. If no width and height is found, then 150 is returned for each.
	 */
	public static function get_thumb_size( $size = 'thumbnail' ) {

		// Get thumbnail size.
		$thumb_size_array = self::get_all_image_sizes( $size );

		if ( isset( $thumb_size_array['width'] ) ) {
			$thumb_width  = $thumb_size_array['width'];
			$thumb_height = $thumb_size_array['height'];
		}

		if ( isset( $thumb_width ) && isset( $thumb_height ) ) {
			$thumb_size = array( $thumb_width, $thumb_height );
		} else {
			$thumb_size = array( 150, 150 );
		}

		/**
		 * Filter array of thumbnail size.
		 *
		 * @param array $thumb_size Array with width and height of thumbnail.
		 */
		return apply_filters( self::$prefix . '_get_thumb_size', $thumb_size );
	}

	/**
	 * Get all image sizes.
	 *
	 * @param string|int[] $size Image size.
	 * @return array|bool If a single size is specified, then the array with width, height and crop status
	 *                    or false if size is not found;
	 *                    If no size is specified then an Associative array of the registered image sub-sizes.
	 */
	public static function get_all_image_sizes( $size = '' ) {

		if ( is_array( $size ) ) {
			$size = self::get_appropriate_image_size( $size[0], $size[1] );
		}

		$sizes = wp_get_registered_image_subsizes();

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
		 * @param array $sizes Image sizes.
		 */
		return apply_filters( self::$prefix . '_get_all_image_sizes', $sizes );
	}

	/**
	 * Get the most appropriate image size based on the given thumbnail width and height.
	 *
	 * @param int $thumb_width  Thumbnail width.
	 * @param int $thumb_height Thumbnail height.
	 * @return string|bool Image size name if found, false otherwise.
	 */
	public static function get_appropriate_image_size( $thumb_width, $thumb_height ) {
		$sizes = wp_get_registered_image_subsizes();

		$closest_size     = false;
		$closest_distance = PHP_INT_MAX;

		foreach ( $sizes as $size_name => $size_info ) {
			$size_width  = $size_info['width'];
			$size_height = $size_info['height'];
			$distance    = sqrt( pow( $thumb_width - $size_width, 2 ) + pow( $thumb_height - $size_height, 2 ) );

			if ( $distance < $closest_distance ) {
				$closest_distance = $distance;
				$closest_size     = $size_name;
			}
		}

		return $closest_size;
	}
}

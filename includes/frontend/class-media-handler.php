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
	 * @since 3.5.0
	 *
	 * @var string $prefix Prefix.
	 */
	private static $prefix = 'crp';

	/**
	 * Constructor class.
	 *
	 * @since 3.5.0
	 */
	public function __construct() {
	}

	/**
	 * Add custom image size of thumbnail. Filters `init`.
	 *
	 * @since 2.0.0
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
	 * @since 3.5.0
	 *
	 * @param string|array $args {
	 *     Optional. Array or string of Query parameters.
	 *
	 *     @type int|WP_Post $post               Post ID or WP_Post object.
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

		// Let's start fetching the thumbnail. First place to look is in the post meta defined in the Settings page.
		$postimage = get_post_meta( $result->ID, $args['thumb_meta'], true );
		$postimage = filter_var( $postimage, FILTER_VALIDATE_URL );
		$pick      = 'meta';
		if ( $postimage ) {
			$attachment_id = self::get_attachment_id_from_url( $postimage );

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
			$pick = 'featured';
		}

		// If there is no thumbnail found, fetch the first image in the post, if enabled.
		if ( ! $postimage && $args['scan_images'] ) {

			/**
			 * Filters the post content that is used to scan for images.
			 *
			 * A filter function can be tapped into this to execute shortcodes, modify content, etc.
			 *
			 * @since 3.1.0
			 *
			 * @param string   $post_content Post content
			 * @param \WP_Post $result       Post Object
			 */
			$post_content = apply_filters( self::$prefix . '_thumb_post_content', $result->post_content, $result );

			preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post_content, $matches );
			if ( isset( $matches[1][0] ) && $matches[1][0] ) {          // any image there?
				$postimage = $matches[1][0]; // we need the first one only!
			}
			$pick = 'first';
			if ( $postimage ) {
				$attachment_id = self::get_attachment_id_from_url( $postimage );

				$postthumb = wp_get_attachment_image_src( $attachment_id, $args['size'] );
				if ( false !== $postthumb ) {
					$postimage = $postthumb[0];
					$pick     .= 'correct';
				}
			}
		}

		// If there is no thumbnail found, fetch the first child image.
		if ( ! $postimage ) {
			$postimage = self::get_first_image( $result->ID, $args['thumb_width'], $args['thumb_height'] );  // Get the first image.
			$pick      = 'firstchild';
		}

		// If no other thumbnail set, try to get the custom video thumbnail set by the Video Thumbnails plugin.
		if ( ! $postimage ) {
			$postimage = get_post_meta( $result->ID, '_video_thumbnail', true );
			$pick      = 'video_thumb';
		}

		// If no thumb found and settings permit, use default thumb.
		if ( ! $postimage && $args['thumb_default_show'] && $args['thumb_default'] ) {
			$postimage = $args['thumb_default'];
			$pick      = 'default_thumb';

			if ( Display::get_default_thumbnail() !== $postimage ) {
				$attachment_id = self::get_attachment_id_from_url( $postimage );
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
		}

		if ( ! $postimage && $args['use_site_icon'] ) {
			$postimage = get_site_icon_url( min( $args['thumb_width'], $args['thumb_height'] ) );
			$pick      = 'site_icon_min';
		}

		// Hopefully, we've found a thumbnail by now. If so, run it through the custom filter, check for SSL and create the image tag.
		if ( $postimage ) {

			/**
			 * Filters the thumbnail image URL.
			 *
			 * Use this filter to modify the thumbnail URL that is automatically created
			 * Before v2.1 this was used for cropping the post image using timthumb
			 *
			 * @since 2.1.0
			 * @since 3.1.0 Second argument changed to $args array and third argument changed to Post object.
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
				$attachment_id = self::get_attachment_id_from_url( $postimage );
			}

			/**
			 * Flag to use the image's alt text as the thumbnail alt text.
			 *
			 * @since 3.5.0
			 *
			 * @param bool $use_image_alt Flag to use the image's alt text as the thumbnail alt text.
			 */
			$use_image_alt = apply_filters( self::$prefix . '_thumb_use_image_alt', true );

			/**
			 * Flag to use the post title as the thumbnail alt text if no alt text is found.
			 *
			 * @since 3.5.0
			 *
			 * @param bool $alt_fallback Flag to use the post title as the thumbnail alt text if no alt text is found.
			 */
			$alt_fallback = apply_filters( self::$prefix . '_thumb_alt_fallback_post_title', true );

			if ( ! empty( $attachment_id ) && $use_image_alt ) {
				$alt = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
			}

			// If empty alt then try to get the title of the attachment.
			if ( empty( $alt ) && ! empty( $attachment_id ) ) {
				$alt = get_the_title( $attachment_id );
			}

			if ( empty( $alt ) ) {
				$alt = $alt_fallback ? $post_title : '';
			}

			/**
			 * Filters the thumbnail styles attribute.
			 *
			 * @since 3.6.0
			 *
			 * @param string $styles Thumbnail styles
			 */
			$attr['style'] = apply_filters( self::$prefix . '_thumb_styles', $args['style'] );

			/**
			 * Filters the thumbnail classes and allows a filter function to add any more classes if needed.
			 *
			 * @since 2.2.2
			 *
			 * @param string $class Thumbnail Class
			 */
			$attr['class'] = apply_filters( self::$prefix . '_thumb_class', $class );

			/**
			 * Filters the thumbnail alt.
			 *
			 * @since 2.5.0
			 *
			 * @param string $alt Thumbnail alt attribute
			 */
			$attr['alt'] = apply_filters( self::$prefix . '_thumb_alt', $alt );

			/**
			 * Filters the thumbnail title.
			 *
			 * @since 2.6.0
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
		 * @since   1.9
		 *
		 * @param   string  $output     HTML output.
		 * @param   array   $args       Argument list
		 * @param   string  $postimage  Thumbnail URL
		 */
		return apply_filters( self::$prefix . '_get_the_post_thumbnail', $output, $args, $postimage );
	}

	/**
	 * Get an HTML img element
	 *
	 * @since 3.5.0
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
		 * @since 2.6.0
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
		 * @since 2.7.0
		 *
		 * @param string $html           HTML img element or empty string on failure.
		 * @param string $attachment_url Image URL.
		 * @param array  $attr           Attributes for the image markup.
		 */
		return apply_filters( self::$prefix . '_get_image_html', $html, $attachment_url, $attr );
	}


	/**
	 * Retrieve width and height attributes using given width and height values.
	 *
	 * @since 3.5.0
	 *
	 * @param array $args Argument array.
	 *
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
		 * @since   2.2.0
		 *
		 * @param string $thumb_html Thumbnail HTML.
		 * @param array  $args       Argument array.
		 */
		return apply_filters( self::$prefix . '_thumb_html', $thumb_html, $args );
	}


	/**
	 * Get the first child image in the post.
	 *
	 * @since   3.5.0
	 * @param   mixed $postid Post ID.
	 * @param   int   $thumb_width Thumb width.
	 * @param   int   $thumb_height Thumb height.
	 * @return  string  Location of thumbnail
	 */
	public static function get_first_image( $postid, $thumb_width, $thumb_height ) {
		$args = array(
			'numberposts'    => 1,
			'order'          => 'ASC',
			'post_mime_type' => 'image',
			'post_parent'    => $postid,
			'post_status'    => null,
			'post_type'      => 'attachment',
		);

		$attachments = get_children( $args );

		if ( $attachments ) {
			foreach ( $attachments as $attachment ) {
				$image_attributes = wp_get_attachment_image_src( $attachment->ID, array( $thumb_width, $thumb_height ) ) ? wp_get_attachment_image_src( $attachment->ID, array( $thumb_width, $thumb_height ) ) : wp_get_attachment_image_src( $attachment->ID, 'full' );

				/**
				 * Filters first child attachment from the post.
				 *
				 * @since 2.0.0
				 *
				 * @param string $image_attributes[0] URL of the image
				 * @param int    $postid              Post ID
				 * @param int    $thumb_width         Thumb width
				 * @param int    $thumb_height        Thumb height
				 */
				return apply_filters( self::$prefix . '_get_first_image', $image_attributes[0], $postid, $thumb_width, $thumb_height );
			}
		} else {
			return '';
		}
	}


	/**
	 * Function to get the attachment ID from the attachment URL.
	 *
	 * @since 3.5.0
	 *
	 * @param   string $attachment_url Attachment URL.
	 * @return  int     Attachment ID
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
		 * @since 2.1.0
		 *
		 * @param   int     $attachment_id  Attachment ID
		 * @param   string  $attachment_url Attachment URL
		 */
		return apply_filters( self::$prefix . '_get_attachment_id_from_url', $attachment_id, $attachment_url );
	}


	/**
	 * Function to get the correct height and width of the thumbnail.
	 *
	 * @since 3.5.0
	 *
	 * @param  string $size Image size.
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
		 * @since   2.9.0
		 *
		 * @param   array   $thumb_size Array with width and height of thumbnail
		 */
		return apply_filters( self::$prefix . '_get_thumb_size', $thumb_size );
	}


	/**
	 * Get all image sizes.
	 *
	 * @since 3.5.0
	 *
	 * @param string|int[] $size Image size.
	 * @return array|bool  If a single size is specified, then the array with width, height and crop status
	 *                     or false if size is not found;
	 *                     If no size is specified then an Associative array of the registered image sub-sizes.
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
		 * @since 2.0.0
		 *
		 * @param array   $sizes  Image sizes
		 */
		return apply_filters( self::$prefix . '_get_all_image_sizes', $sizes );
	}

	/**
	 * Get the most appropriate image size based on the given thumbnail width and height.
	 *
	 * @since 3.5.0
	 *
	 * @param int $thumb_width  Thumbnail width.
	 * @param int $thumb_height Thumbnail height.
	 *
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

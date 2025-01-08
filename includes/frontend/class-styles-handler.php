<?php
/**
 * Functions dealing with styles.
 *
 * @package   Contextual_Related_Posts
 */

namespace WebberZone\Contextual_Related_Posts\Frontend;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Admin Columns Class.
 *
 * @since 3.3.0
 */
class Styles_Handler {

	/**
	 * Constructor class.
	 *
	 * @since 3.3.0
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'register_styles' ) );
	}

	/**
	 * Enqueue styles.
	 */
	public static function register_styles() {

		// Register crp-custom-style as a placeholder to insert custom styles.
		wp_register_style(
			'crp-custom-style',
			false,
			array(),
			CRP_VERSION
		);

		$style_array = self::get_style();

		if ( ! empty( $style_array['name'] ) ) {
			$style     = $style_array['name'];
			$extra_css = $style_array['extra_css'];

			wp_register_style(
				"crp-style-{$style}",
				plugins_url( "css/{$style}.min.css", CRP_PLUGIN_FILE ),
				array(),
				CRP_VERSION
			);
			wp_enqueue_style( "crp-style-{$style}" );
			wp_add_inline_style( "crp-style-{$style}", $extra_css );
		}

		// Add custom CSS to header.
		$add_to     = crp_get_option( 'add_to', false );
		$custom_css = stripslashes( crp_get_option( 'custom_css' ) );
		if ( $custom_css ) {
			$enqueue_style = false;

			if ( is_single() && ! empty( $add_to['single'] ) ) {
				$enqueue_style = true;
			} elseif ( is_page() && ! empty( $add_to['page'] ) ) {
				$enqueue_style = true;
			} elseif ( is_home() && ! empty( $add_to['home'] ) ) {
				$enqueue_style = true;
			} elseif ( is_category() && ! empty( $add_to['category_archives'] ) ) {
				$enqueue_style = true;
			} elseif ( is_tag() && ! empty( $add_to['tag_archives'] ) ) {
				$enqueue_style = true;
			} elseif ( ( is_tax() || is_author() || is_date() ) && ! empty( $add_to['other_archives'] ) ) {
				$enqueue_style = true;
			} elseif ( is_active_widget( false, false, 'CRP_Widget', true ) ) {
				$enqueue_style = true;
			}

			if ( $enqueue_style ) {
				wp_enqueue_style( 'crp-custom-style' );
				wp_add_inline_style( 'crp-custom-style', $custom_css );
			}
		}
	}

	/**
	 * Get the current style for the related posts.
	 *
	 * @since 3.5.0
	 *
	 * @param string $style Style parameter.
	 *
	 * @return array Contains two elements:
	 *               'name' holding style name and 'extra_css' to be added inline.
	 */
	public static function get_style( $style = '' ) {

		$style_array  = array();
		$thumb_width  = crp_get_option( 'thumb_width', 150 );
		$thumb_height = crp_get_option( 'thumb_height', 150 );
		$crp_style    = ! empty( $style ) ? $style : crp_get_option( 'crp_styles' );

		switch ( $crp_style ) {
			case 'rounded_thumbs':
				$style_array['name']      = 'rounded-thumbs';
				$style_array['extra_css'] = "
			.crp_related.crp-rounded-thumbs a {
				width: {$thumb_width}px;
                height: {$thumb_height}px;
				text-decoration: none;
			}
			.crp_related.crp-rounded-thumbs img {
				max-width: {$thumb_width}px;
				margin: auto;
			}
			.crp_related.crp-rounded-thumbs .crp_title {
				width: 100%;
			}
			";
				break;

			case 'masonry':
			case 'text_only':
				$style_array['name']      = str_replace( '_', '-', $crp_style );
				$style_array['extra_css'] = '';
				break;

			case 'grid':
				$style_array['name']      = 'grid';
				$style_array['extra_css'] = "
			.crp_related.crp-grid ul li a.crp_link {
				grid-template-rows: {$thumb_height}px auto;
			}
			.crp_related.crp-grid ul {
				grid-template-columns: repeat(auto-fill, minmax({$thumb_width}px, 1fr));
			}
			";
				break;

			case 'thumbs_grid':
				$row_height = max( 0, (int) $thumb_height - 50 );

				$style_array['name']      = 'thumbs-grid';
				$style_array['extra_css'] = "
			.crp_related.crp-thumbs-grid ul li a.crp_link {
				grid-template-rows: {$row_height}px auto;
			}
			.crp_related.crp-thumbs-grid ul {
				grid-template-columns: repeat(auto-fill, minmax({$thumb_width}px, 1fr));
			}
			";
				break;

			default:
				$style_array['name']      = '';
				$style_array['extra_css'] = '';
				break;
		}

		/**
		 * Filter the style array which contains the name and extra_css.
		 *
		 * @since 3.2.0
		 *
		 * @param array  $style_array  Style array containing name and extra_css.
		 * @param string $crp_style    Style name.
		 * @param int    $thumb_width  Thumbnail width.
		 * @param int    $thumb_height Thumbnail height.
		 */
		return apply_filters( 'crp_get_style', $style_array, $crp_style, $thumb_width, $thumb_height );
	}
}

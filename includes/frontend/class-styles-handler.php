<?php
/**
 * Functions dealing with styles.
 *
 * @package Contextual_Related_Posts
 */

namespace WebberZone\Contextual_Related_Posts\Frontend;

use WebberZone\Contextual_Related_Posts\Admin\Settings;
use WebberZone\Contextual_Related_Posts\Util\Hook_Registry;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Registers and enqueues the Contextual Related Posts front-end styles.
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
		Hook_Registry::add_action( 'wp_enqueue_scripts', array( $this, 'register_styles' ) );
	}

	/**
	 * Register and enqueue a single style's CSS by ID.
	 *
	 * @since 4.4.0
	 *
	 * @param string $style_id Style ID (see Settings::get_styles()).
	 */
	public static function enqueue_style( $style_id ) {
		$style_array = self::get_style( $style_id );

		if ( empty( $style_array['name'] ) ) {
			return;
		}

		$style     = $style_array['name'];
		$extra_css = $style_array['extra_css'];

		wp_register_style(
			"crp-style-{$style}",
			plugins_url( "includes/frontend/css/{$style}.min.css", WZ_CRP_PLUGIN_FILE ),
			array(),
			WZ_CRP_VERSION
		);
		wp_enqueue_style( "crp-style-{$style}" );
		wp_add_inline_style( "crp-style-{$style}", $extra_css );
	}

	/**
	 * Render-time safety net for builders whose widget tree lives outside the queried post — e.g.
	 * a theme-builder single/archive template, or a header/footer template — where the widget's
	 * own post ID is never the one `get_style_ids_to_load()` scans. Called directly
	 * from the widget's own render(), where `wp_head` (and therefore `wp_print_styles()`) has
	 * already fired, so on top of the normal registration this also prints the `<link>`/inline CSS
	 * immediately if it wasn't already output — `wp_print_styles()` no-ops on a handle already
	 * printed, so this is safe to call unconditionally alongside the `wp_enqueue_scripts`-time path.
	 *
	 * @since 4.4.0
	 *
	 * @param string $style_id Style ID (see Settings::get_styles()).
	 */
	public static function enqueue_style_now( $style_id ) {
		self::enqueue_style( $style_id );

		if ( ! did_action( 'wp_head' ) ) {
			return;
		}

		$style_array = self::get_style( $style_id );

		if ( empty( $style_array['name'] ) ) {
			return;
		}

		wp_print_styles( array( "crp-style-{$style_array['name']}" ) );
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
			WZ_CRP_VERSION
		);

		foreach ( self::get_style_ids_to_load() as $style_id ) {
			self::enqueue_style( $style_id );
		}

		$custom_css = stripslashes( crp_get_option( 'custom_css' ) );
		if ( $custom_css ) {
			wp_enqueue_style( 'crp-custom-style' );
			wp_add_inline_style( 'crp-custom-style', $custom_css );
		}
	}

	/**
	 * Get the style IDs whose CSS needs to be enqueued for the current request.
	 *
	 * In the WPBakery frontend editor every style is loaded, since the user can switch
	 * between them live without a page reload.
	 * On a normal front end request, only the site-wide default plus any style explicitly
	 * requested by a `[crp]`/`[crp_related_posts]` shortcode on the queried post is loaded —
	 * Gutenberg block instances aren't scanned here, as their style is only known once the
	 * block itself renders.
	 *
	 * @since 4.4.0
	 *
	 * @return string[] Style IDs.
	 */
	protected static function get_style_ids_to_load() {
		if ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) {
			return wp_list_pluck( Settings::get_styles(), 'id' );
		}

		$style_ids = array( crp_get_option( 'crp_styles' ) );

		$post = get_post();
		if ( ! $post ) {
			return array_unique( $style_ids );
		}

		if ( preg_match_all( '/\[(?:crp|crp_related_posts)\b[^\]]*\]/', $post->post_content, $shortcodes ) ) {
			foreach ( $shortcodes[0] as $shortcode ) {
				// Mirrors Display::related_posts()'s own override: post_thumb_op="text_only" forces the
				// text_only style regardless of what crp_styles says, so it has to win here too.
				if ( preg_match( '/\bpost_thumb_op=["\']text_only["\']/', $shortcode ) ) {
					$style_ids[] = 'text_only';
				} elseif ( preg_match( '/\bcrp_styles=["\']([a-z_]+)["\']/', $shortcode, $match ) ) {
					$style_ids[] = $match[1];
				}
			}
		}

		return array_unique( $style_ids );
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
		$thumb_width  = (int) crp_get_option( 'thumb_width', 150 );
		$thumb_height = (int) crp_get_option( 'thumb_height', 150 );
		$aspect_ratio = $thumb_width / $thumb_height;
		$crp_style    = ! empty( $style ) ? $style : crp_get_option( 'crp_styles' );

		switch ( $crp_style ) {
			case 'rounded_thumbs':
				$style_array['name']      = 'rounded-thumbs';
				$style_array['extra_css'] = "
					.crp_related.crp-rounded-thumbs {
						--crp-thumb-width: {$thumb_width}px;
						--crp-thumb-height: {$thumb_height}px;
						--crp-aspect-ratio: {$aspect_ratio};
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
			.crp_related.crp-grid {
				--crp-grid-column-min: {$thumb_width}px;
				--crp-grid-card-min-height: " . ( $thumb_height + 80 ) . "px;
				--crp-grid-thumb-aspect-ratio: {$aspect_ratio};
				--crp-grid-title-line-clamp: 3;
				--crp-grid-title-line-height: 1.2em;
			}
			";
				break;

			case 'thumbs_grid':
				$style_array['name']      = 'thumbs-grid';
				$style_array['extra_css'] = "
			.crp_related.crp-thumbs-grid {
				--crp-thumb-width: {$thumb_width}px;
				--crp-thumb-height: {$thumb_height}px;
				--crp-thumb-min-width: " . max( 120, $thumb_width * 0.8 ) . 'px;
				--crp-aspect-ratio: ' . $aspect_ratio . ';
			}
			';
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

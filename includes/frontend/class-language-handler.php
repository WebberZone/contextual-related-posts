<?php
/**
 * Language handler
 *
 * @package Contextual_Related_Posts
 */

namespace WebberZone\Contextual_Related_Posts\Frontend;

use WebberZone\Contextual_Related_Posts\Util\Hook_Registry;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Language handler class.
 *
 * @since 3.5.0
 */
class Language_Handler {

	/**
	 * Constructor.
	 *
	 * @since 3.5.0
	 */
	public function __construct() {
		Hook_Registry::add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		Hook_Registry::add_filter( 'crp_query_the_posts', array( $this, 'translate_ids' ), 999 );
		Hook_Registry::add_filter( 'rest_pre_echo_response', array( $this, 'translate_rest_response' ), 10, 3 );
	}

	/**
	 * Initialises text domain for l10n.
	 *
	 * @since 3.5.0
	 *
	 * @return void
	 */
	public static function load_plugin_textdomain() {
		load_plugin_textdomain( 'contextual-related-posts', false, dirname( plugin_basename( WZ_CRP_PLUGIN_FILE ) ) . '/languages/' );
	}

	/**
	 * Get the ID of a post in the current language. Works with WPML and PolyLang.
	 *
	 * @since 3.5.0
	 *
	 * @param  int[] $results Arry of Posts.
	 * @return \WP_Post[] Updated array of WP_Post objects.
	 */
	public static function translate_ids( $results ) {
		global $post;

		$processed_ids     = ! empty( $post->ID ) ? array( $post->ID ) : array();
		$processed_results = array();

		foreach ( $results as $result ) {

			$result = self::object_id_cur_lang( $result );
			if ( ! $result ) {
				continue;
			}

			// If this is NULL or already processed ID or matches current post then skip processing this loop.
			if ( ! $result->ID || in_array( $result->ID, $processed_ids ) ) { // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
				continue;
			}

			// Push the current ID into the array to ensure we're not repeating it.
			array_push( $processed_ids, $result->ID );

			// Let's get the Post using the ID.
			$result = get_post( $result );
			array_push( $processed_results, $result );
		}
		return $processed_results;
	}

	/**
	 * Returns the object identifier for the current language (WPML).
	 *
	 * @since 3.5.0
	 *
	 * @param  int|string|\WP_Post $post Post object or Post ID.
	 * @return \WP_Post|array|null Post opbject, updated if needed.
	 */
	public static function object_id_cur_lang( $post ) {

		$return_original_if_missing = false;

		$post         = get_post( $post );
		$current_lang = apply_filters( 'wpml_current_language', null );

		// Polylang implementation.
		if ( function_exists( 'pll_get_post' ) ) {
			$post = \pll_get_post( $post->ID );
			$post = get_post( $post );
		}

		// WPML implementation.
		if ( class_exists( 'SitePress' ) ) {
			/**
			 * Filter to modify if the original language ID is returned.
			 *
			 * @since 2.2.3
			 *
			 * @param bool $return_original_if_missing Flag to return original post ID if translated post ID is missing.
			 * @param int  $id                         Post ID
			 */
			$return_original_if_missing = apply_filters( 'crp_wpml_return_original', $return_original_if_missing, $post->ID );

			$post = apply_filters( 'wpml_object_id', $post->ID, $post->post_type, $return_original_if_missing, $current_lang );
			$post = get_post( $post );
		}

		/**
		 * Filters Post object for current language.
		 *
		 * @since 2.1.0
		 *
		 * @param \WP_Post|array|null $id Post object.
		 */
		return apply_filters( 'crp_object_id_cur_lang', $post );
	}

	/**
	 * Whether TranslatePress is active and exposes the API this integration needs.
	 *
	 * @since 4.4.2
	 *
	 * @return bool True if TranslatePress can be used.
	 */
	public static function is_translatepress_active(): bool {
		return function_exists( 'trp_translate' ) && class_exists( 'TRP_Translate_Press' );
	}

	/**
	 * Fetch a TranslatePress component instance.
	 *
	 * @since 4.4.2
	 *
	 * @param  string $component Component name, e.g. `url_converter`.
	 * @return object|null Component instance or null when unavailable.
	 */
	public static function get_trp_component( string $component ) {
		if ( ! self::is_translatepress_active() ) {
			return null;
		}

		$trp = \TRP_Translate_Press::get_trp_instance();
		if ( ! is_object( $trp ) || ! method_exists( $trp, 'get_component' ) ) {
			return null;
		}

		$instance = $trp->get_component( $component );

		return is_object( $instance ) ? $instance : null;
	}

	/**
	 * Get the TranslatePress settings array.
	 *
	 * @since 4.4.2
	 *
	 * @return array TranslatePress settings.
	 */
	public static function get_trp_settings(): array {
		$settings = get_option( 'trp_settings', array() );

		return is_array( $settings ) ? $settings : array();
	}

	/**
	 * Get the TranslatePress language the current front-end request is rendering in.
	 *
	 * @since 4.4.2
	 *
	 * @return string Language code, or an empty string when TranslatePress is inactive
	 *                or the request is in the default language.
	 */
	public static function get_trp_current_language(): string {
		if ( ! self::is_translatepress_active() ) {
			return '';
		}

		global $TRP_LANGUAGE; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase -- TranslatePress global.

		$language = is_string( $TRP_LANGUAGE ) ? $TRP_LANGUAGE : ''; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase -- TranslatePress global.
		$settings = self::get_trp_settings();
		$default  = isset( $settings['default-language'] ) ? (string) $settings['default-language'] : '';

		return ( '' === $language || $language === $default ) ? '' : $language;
	}

	/**
	 * Get a language identifier for cache keys.
	 *
	 * Rendered output is language-specific — TranslatePress filters `home_url()`, and
	 * WPML/Polylang resolve different post IDs — so cached HTML and post lists must not
	 * be shared between languages.
	 *
	 * @since 4.4.2
	 *
	 * @return string Current language code, or an empty string when the site is monolingual.
	 */
	public static function get_cache_language(): string {
		$language = self::get_trp_current_language();

		if ( '' === $language && function_exists( 'pll_current_language' ) ) {
			$language = (string) \pll_current_language( 'locale' );
		}

		if ( '' === $language && class_exists( 'SitePress' ) ) {
			$language = (string) apply_filters( 'wpml_current_language', null );
		}

		/**
		 * Filters the language component added to CRP cache keys.
		 *
		 * @since 4.4.2
		 *
		 * @param string $language Current language code, or an empty string.
		 */
		return (string) apply_filters( 'crp_cache_language', $language );
	}

	/**
	 * Get the referring URL, but only when it points at this site.
	 *
	 * TranslatePress resolves a language from any URL containing a known slug, so an
	 * off-site referer would otherwise let a cross-site request choose the language.
	 *
	 * @since 4.4.2
	 *
	 * @return string Referring URL on this host, or an empty string.
	 */
	protected static function get_same_origin_referer(): string {
		$referer = function_exists( 'wp_get_raw_referer' ) ? wp_get_raw_referer() : '';

		if ( ! is_string( $referer ) || '' === $referer ) {
			return '';
		}

		$referer_host = wp_parse_url( $referer, PHP_URL_HOST );
		$home_host    = wp_parse_url( home_url(), PHP_URL_HOST );

		if ( empty( $referer_host ) || empty( $home_host ) || strtolower( (string) $referer_host ) !== strtolower( (string) $home_host ) ) {
			return '';
		}

		return $referer;
	}

	/**
	 * Resolve the TranslatePress language a REST request should be rendered in.
	 *
	 * TranslatePress derives the language from the page URL, but REST routes carry no
	 * language prefix, so the language is taken from an explicit `lang` parameter and
	 * falls back to the referring front-end URL.
	 *
	 * @since 4.4.2
	 *
	 * @param  \WP_REST_Request|null $request REST request.
	 * @return string Language code, or an empty string when no translation is needed.
	 */
	public static function get_trp_rest_language( $request = null ): string {
		if ( ! self::is_translatepress_active() ) {
			return '';
		}

		$settings = self::get_trp_settings();
		$default  = isset( $settings['default-language'] ) ? (string) $settings['default-language'] : '';

		// Mirrors TRP_Url_Converter::get_lang_from_url_string(): unpublished languages are for translators only.
		$available = isset( $settings['publish-languages'] ) ? (array) $settings['publish-languages'] : array();

		if ( current_user_can( (string) apply_filters( 'trp_translating_capability', 'manage_options' ) ) ) {
			$available = array_merge( $available, isset( $settings['translation-languages'] ) ? (array) $settings['translation-languages'] : array() );
		}

		$language = '';

		if ( $request instanceof \WP_REST_Request ) {
			$language = (string) $request->get_param( 'lang' );
		}

		if ( '' === $language ) {
			$url_converter = self::get_trp_component( 'url_converter' );

			if ( $url_converter && method_exists( $url_converter, 'get_lang_from_url_string' ) ) {
				// TranslatePress filters home_url(), so rest_url() carries the language prefix of the page that enqueued it.
				if ( method_exists( $url_converter, 'cur_page_url' ) ) {
					$language = (string) $url_converter->get_lang_from_url_string( $url_converter->cur_page_url() );
				}

				if ( '' === $language ) {
					$referer = self::get_same_origin_referer();

					if ( '' !== $referer ) {
						$language = (string) $url_converter->get_lang_from_url_string( $referer );
					}
				}
			}
		}

		/**
		 * Filters the TranslatePress language used to render CRP REST responses.
		 *
		 * @since 4.4.2
		 *
		 * @param string                $language Language code resolved from the request.
		 * @param \WP_REST_Request|null $request  REST request.
		 */
		$language = (string) apply_filters( 'crp_trp_rest_language', $language, $request );

		if ( '' === $language || $language === $default || ! in_array( $language, $available, true ) ) {
			return '';
		}

		return $language;
	}

	/**
	 * Translate a string with TranslatePress.
	 *
	 * @since 4.4.2
	 *
	 * @param  string $content  Content in the default language. Text or HTML.
	 * @param  string $language Target language code.
	 * @return string Translated content.
	 */
	public static function trp_translate_content( string $content, string $language ): string {
		if ( '' === $content || '' === $language || ! self::is_translatepress_active() ) {
			return $content;
		}

		// $prevent_over_translation wraps the return in a span; REST output never passes through TranslatePress's page buffer.
		return (string) \trp_translate( $content, $language, false );
	}

	/**
	 * Convert a URL to its TranslatePress equivalent in the given language.
	 *
	 * @since 4.4.2
	 *
	 * @param  string $url      URL in the default language.
	 * @param  string $language Target language code.
	 * @return string Converted URL.
	 */
	public static function trp_translate_url( string $url, string $language ): string {
		if ( '' === $url || '' === $language ) {
			return $url;
		}

		$url_converter = self::get_trp_component( 'url_converter' );

		if ( ! $url_converter || ! method_exists( $url_converter, 'get_url_for_language' ) ) {
			return $url;
		}

		$converted = $url_converter->get_url_for_language( $language, $url, '' );

		return is_string( $converted ) && '' !== $converted ? $converted : $url;
	}

	/**
	 * Translate the CRP REST API response with TranslatePress.
	 *
	 * TranslatePress's own page output buffer never runs for REST requests, so the
	 * related posts returned to block editors and the lazy load script would otherwise
	 * always be served in the default language.
	 *
	 * @since 4.4.2
	 *
	 * @param  mixed $result  Response data to send to the client.
	 * @param  mixed $server  Server instance.
	 * @param  mixed $request Request used to generate the response.
	 * @return mixed Response data, translated where applicable.
	 */
	public static function translate_rest_response( $result, $server, $request ) {
		if ( ! $request instanceof \WP_REST_Request ) {
			return $result;
		}

		if ( ! is_array( $result ) && ! is_string( $result ) ) {
			return $result;
		}

		if ( false === strpos( (string) $request->get_route(), self::get_rest_namespace() ) ) {
			return $result;
		}

		$language = self::get_trp_rest_language( $request );

		if ( '' === $language ) {
			return $result;
		}

		// Some routes return a bare HTML string rather than a structure of fields.
		if ( is_string( $result ) ) {
			return self::trp_translate_content( $result, $language );
		}

		return self::translate_rest_data( $result, $language );
	}

	/**
	 * The REST namespace whose responses this plugin translates.
	 *
	 * @since 4.4.2
	 *
	 * @return string REST namespace.
	 */
	protected static function get_rest_namespace(): string {
		return 'contextual-related-posts/v1';
	}

	/**
	 * Walk a CRP REST payload translating the fields TranslatePress can handle.
	 *
	 * @since 4.4.2
	 *
	 * @param  array  $data     Response data.
	 * @param  string $language Target language code.
	 * @param  int    $depth    Current recursion depth.
	 * @return array Translated response data.
	 */
	protected static function translate_rest_data( array $data, string $language, int $depth = 0 ): array {
		if ( $depth > 5 ) {
			return $data;
		}

		/**
		 * Filters the response keys CRP translates with TranslatePress.
		 *
		 * @since 4.4.2
		 *
		 * @param array $keys Associative array of `content` and `url` key names.
		 */
		$keys = apply_filters(
			'crp_trp_rest_translatable_keys',
			array(
				'content' => array( 'html', 'title', 'excerpt', 'content', 'name', 'description' ),
				// `guid` is deliberately absent: it is an immutable identifier, not a navigable URL.
				'url'     => array( 'link', 'permalink' ),
			)
		);

		foreach ( $data as $key => $value ) {
			if ( is_array( $value ) ) {
				if ( isset( $value['rendered'] ) && is_string( $value['rendered'] ) ) {
					if ( in_array( (string) $key, $keys['content'], true ) ) {
						$data[ $key ]['rendered'] = self::trp_translate_content( $value['rendered'], $language );
						continue;
					}
					if ( in_array( (string) $key, $keys['url'], true ) ) {
						$data[ $key ]['rendered'] = self::trp_translate_url( $value['rendered'], $language );
						continue;
					}
				}

				$data[ $key ] = self::translate_rest_data( $value, $language, $depth + 1 );
				continue;
			}

			if ( ! is_string( $value ) || '' === $value ) {
				continue;
			}

			if ( in_array( (string) $key, $keys['content'], true ) ) {
				$data[ $key ] = self::trp_translate_content( $value, $language );
			} elseif ( in_array( (string) $key, $keys['url'], true ) ) {
				$data[ $key ] = self::trp_translate_url( $value, $language );
			}
		}

		return $data;
	}
}

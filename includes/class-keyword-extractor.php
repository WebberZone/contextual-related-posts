<?php
/**
 * Opt-in keyword extraction shared by related-post queries.
 *
 * @package WebberZone\Contextual_Related_Posts
 */

namespace WebberZone\Contextual_Related_Posts;

use WebberZone\Contextual_Related_Posts\Frontend\Display;
use WebberZone\Contextual_Related_Posts\Util\Helpers;
use WebberZone\Contextual_Related_Posts\Util\Hook_Registry;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Selects frequent terms without changing the SQL builder.
 *
 * @since 4.5.3
 */
class Keyword_Extractor {
	/** Register extraction and cache hooks. */
	public function __construct() {
		Hook_Registry::add_filter( 'crp_query_posts_match_fields_content', array( __CLASS__, 'filter_fields' ), 10, 3 );
		Hook_Registry::add_filter( 'crp_cache_busting_settings', array( __CLASS__, 'cache_settings' ) );
	}

	/**
	 * Settings whose changes invalidate matches and output.
	 *
	 * @param array $keys Existing keys.
	 * @return array Setting keys.
	 */
	public static function cache_settings( $keys ): array {
		return array_merge( $keys, array( 'frequency_keywords', 'keyword_count', 'keyword_stopwords', 'keyword_keepwords', 'exclude_phrases', 'archive_recommendations', 'add_to' ) );
	}

	/**
	 * Whether an alternative extraction strategy is active.
	 *
	 * @param array $args Query arguments.
	 * @return bool Whether extraction changes the legacy path.
	 */
	public static function is_active( array $args ): bool {
		return (bool) apply_filters( 'crp_keyword_extraction_active', ! empty( $args['frequency_keywords'] ), $args );
	}

	/**
	 * Filter the fields used by the core query. Explicit keywords win.
	 *
	 * @param array    $fields Existing match text.
	 * @param \WP_Post $post Source post.
	 * @param array    $args Query arguments.
	 * @return array Match text.
	 */
	public static function filter_fields( $fields, $post, $args ): array {
		if ( ! empty( $args['keyword'] ) || ! self::is_active( $args ) ) {
			return $fields;
		}
		$result = array( self::process( $post->post_title, $post, $args ) );
		if ( ! empty( $args['match_content'] ) ) {
			$result[] = self::process( self::content( $post, $args ), $post, $args );
		}
		return $result;
	}

	/**
	 * Resolve bounded source content without running content filters or shortcodes.
	 *
	 * @param \WP_Post $post Source post.
	 * @param array    $args Query arguments.
	 * @return string Source content.
	 */
	public static function content( \WP_Post $post, array $args ): string {
		if ( empty( $args['frequency_keywords'] ) ) {
			return Display::get_the_excerpt( $post, min( (int) ( $args['match_content_words'] ?? 0 ), CRP_MAX_WORDS ), false );
		}
		// Inspect beyond the opening paragraph, with a hard bound on per-query work.
		return wp_check_invalid_utf8( substr( $post->post_content, 0, 100000 ), true );
	}

	/**
	 * Lowercase text without requiring the optional mbstring extension.
	 *
	 * @param string $text Text.
	 * @return string Lowercase text.
	 */
	public static function lowercase( string $text ): string {
		return function_exists( 'mb_strtolower' ) ? mb_strtolower( $text, 'UTF-8' ) : strtolower( $text );
	}

	/**
	 * Process one field for core, weighted queries and previews.
	 *
	 * @param string   $text Source text.
	 * @param \WP_Post $post Source post.
	 * @param array    $args Query arguments.
	 * @return string Match text.
	 */
	public static function process( string $text, \WP_Post $post, array $args ): string {
		if ( ! self::is_active( $args ) ) {
			return Helpers::strip_stopwords( $text );
		}
		// strip_tags() treats a literal comparison such as <0.10 as an unfinished tag and drops the rest.
		$text      = (string) preg_replace( '/<(?![a-z\/!?])/i', '&lt;', strip_shortcodes( $text ) );
		$text      = html_entity_decode( wp_strip_all_tags( $text ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
		$text      = (string) apply_filters( 'crp_keyword_source_text', $text, $post, $args );
		$query     = new \WP_Query();
		$getter    = \Closure::bind(
			function () {
				return $this->get_search_stopwords();
			},
			$query,
			\WP_Query::class
		);
		$stopwords = array_merge( $getter(), array( 'from', 'where' ) );
		$stopwords = (array) apply_filters( 'crp_keyword_stopwords', $stopwords, $post, $args );
		// Apply the effective list directly: Helpers also strips single-letter words,
		// which would silently discard a default stopword explicitly restored by Pro.
		$stopwords = array_filter(
			array_map( 'strval', $stopwords ),
			static function ( $word ) {
				return '' !== $word;
			}
		);
		if ( ! empty( $stopwords ) ) {
			$quoted = array_map(
				static function ( $word ) {
					return preg_quote( $word, '/' );
				},
				$stopwords
			);
			$text   = preg_replace( '/(?<![\p{L}\p{N}])(' . implode( '|', $quoted ) . ')(?![\p{L}\p{N}])/iu', ' ', $text ) ?? $text;
		}
		$text = trim( preg_replace( '/\s+/u', ' ', $text ) ?? $text, " \t\n\r\0\x0B" );
		if ( empty( $args['frequency_keywords'] ) ) {
			return $text;
		}
		preg_match_all( '/[\p{L}\p{N}]+(?:[\x{2019}\x{0027}-][\p{L}\p{N}]+)*/u', self::lowercase( $text ), $matches );
		$counts = array_count_values( $matches[0] );
		// Resolve frequency ties deterministically on PHP 7.4 as well as PHP 8.
		uksort(
			$counts,
			static function ( $a, $b ) use ( $counts ) {
				$comparison = $counts[ $b ] <=> $counts[ $a ];
				return 0 !== $comparison ? $comparison : strcmp( (string) $a, (string) $b );
			}
		);
		$count = max( 1, min( 100, (int) apply_filters( 'crp_keyword_count', 20, $post, $args ) ) );
		return implode( ' ', array_slice( array_keys( $counts ), 0, $count ) );
	}
}

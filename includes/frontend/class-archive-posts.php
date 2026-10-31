<?php
/**
 * Context-aware archive lists without borrowing a loop post.
 *
 * @package WebberZone\Contextual_Related_Posts
 */

namespace WebberZone\Contextual_Related_Posts\Frontend;

use WebberZone\Contextual_Related_Posts\Util\Helpers;
use WebberZone\Contextual_Related_Posts\Util\Hook_Registry;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Archive widget queries and backwards-compatible archive controls.
 *
 * @since 4.5.3
 */
class Archive_Posts {
	/** Register compatibility mapping. */
	public function __construct() {
		Hook_Registry::add_filter( 'option_crp_settings', array( __CLASS__, 'migrate_locations' ) );
	}

	/**
	 * Expand the old Other archives selection on read, so it survives the next settings save.
	 *
	 * @param mixed $settings Stored settings.
	 * @return mixed Mapped settings.
	 */
	public static function migrate_locations( $settings ) {
		if ( ! is_array( $settings ) || empty( $settings['add_to'] ) ) {
			return $settings;
		}
		$locations = wp_parse_list( $settings['add_to'] );
		if ( in_array( 'other_archives', $locations, true ) ) {
			$locations = array_diff( $locations, array( 'other_archives' ) );
			// Preserve the three contexts the old checkbox actually handled.
			$locations          = array_merge( $locations, array( 'taxonomy_archives', 'author_archives', 'date_archives' ) );
			$settings['add_to'] = implode( ',', array_unique( $locations ) );
		}
		return $settings;
	}

	/**
	 * Build an archive query; a synthetic source prevents leakage from the last loop post.
	 *
	 * @param array $args Display/query arguments.
	 * @return array Query arguments or empty for unsupported contexts.
	 */
	public static function query_args( array $args ): array {
		if ( ! is_archive() || is_admin() || is_feed() ) {
			return array();
		}
		$args  = array_merge( Display::get_default_args(), $args );
		$types = Helpers::parse_post_types( $args['post_types'] ?? '' );
		$types = array_values( array_intersect( $types, get_post_types( array( 'public' => true ) ) ) );
		if ( empty( $types ) ) {
			return array();
		}
		$context = get_queried_object();
		$query   = array_merge(
			$args,
			array(
				'post_id'                => new \WP_Post(
					(object) array(
						'ID'           => 0,
						'post_title'   => '',
						'post_content' => '',
						'post_excerpt' => '',
						'post_type'    => 'post',
						'post_author'  => 0,
					)
				),
				'post_type'              => $types,
				'post_status'            => 'publish',
				'has_password'           => false,
				'limit'                  => max( 1, min( 100, (int) $args['limit'] ) ),
				'offset'                 => max( 0, (int) $args['offset'] ),
				'cache'                  => false,
				'cache_posts'            => false,
				'disable_contextual'     => true,
				'disable_contextual_cpt' => false,
				'same_author'            => false,
				'same_post_type'         => false,
				'same_taxes'             => '',
				'match_all'              => false,
				'no_of_common_terms'     => 1,
				'primary_term'           => false,
				'manual_related'         => '0',
				'include_post_ids'       => '0',
				'keyword'                => '',
				'cornerstone_post_ids'   => '',
				'related_meta_keys'      => '',
				'ordering'               => 'date' === $args['ordering'] ? 'date' : 'random',
				'random_order'           => 'date' !== $args['ordering'],
				'_crp_archive_context'   => true,
			)
		);
		if ( $context instanceof \WP_Term ) {
			$taxonomy = get_taxonomy( $context->taxonomy );
			if ( ! $taxonomy || ! $taxonomy->public ) {
				return array();
			}
			$query['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- Restricts the list to its archive context.
				array(
					'taxonomy' => $context->taxonomy,
					'field'    => 'term_id',
					'terms'    => array( $context->term_id ),
				),
			);
			if ( ! empty( $args['tax_query'] ) && is_array( $args['tax_query'] ) ) {
				$query['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- Preserve caller filters within the archive.
					'relation' => 'AND',
					$args['tax_query'],
					$query['tax_query'],
				);
			}
		} elseif ( is_author() && $context instanceof \WP_User ) {
			$query['author'] = $context->ID;
		} elseif ( is_date() ) {
			foreach ( array( 'year', 'monthnum', 'day', 'm', 'w' ) as $part ) {
				if ( get_query_var( $part ) ) {
					$query[ $part ] = absint( get_query_var( $part ) );
				}
			}
		} elseif ( is_post_type_archive() && $context instanceof \WP_Post_Type && $context->public ) {
			$query['post_type'] = array_values( array_intersect( $types, array( $context->name ) ) );
			if ( empty( $query['post_type'] ) ) {
				return array();
			}
		} else {
			return array();
		}
		return (array) apply_filters( 'crp_archive_query_args', $query, $context );
	}

	/**
	 * Render archive results with the normal list templates, without persistent post caches.
	 *
	 * @param array $args Display arguments.
	 * @return string List markup.
	 */
	public static function render( array $args = array() ): string {
		$args  = Helpers::sanitize_args( array_merge( Display::get_default_args(), $args ) );
		$query = self::query_args( $args );
		if ( empty( $query ) || ( wp_is_mobile() && ! empty( $args['disable_on_mobile'] ) ) || ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() && ! empty( $args['disable_on_amp'] ) ) ) {
			return '';
		}
		$context = get_queried_object();
		if ( ( $context instanceof \WP_Term && in_array( (int) $context->term_taxonomy_id, wp_parse_id_list( $args['exclude_on_categories'] ?? '' ), true ) ) || ( $context instanceof \WP_Post_Type && in_array( $context->name, Helpers::parse_post_types( $args['exclude_on_post_types'] ?? '' ), true ) ) || apply_filters( 'get_crp_short_circuit', false, $query['post_id'], $query ) ) {
			return '';
		}
		$results = array_values(
			array_filter(
				Display::get_posts( $query ),
				static function ( $result ) {
					$post = get_post( $result );
					return $post && is_post_publicly_viewable( $post ) && 'publish' === $post->post_status && '' === $post->post_password;
				}
			)
		);
		if ( empty( $results ) ) {
			return '';
		}
		$args['_crp_archive_context']                       = true;
		$args['post_id']                                    = 0;
		$args['title']                                      = str_replace( '%postname%', wp_strip_all_tags( get_the_archive_title() ), $args['title'] );
		list( $args['thumb_width'], $args['thumb_height'] ) = Media_Handler::get_thumb_size( $args['thumb_size'] );
		$template = apply_filters( 'crp_custom_template', null, $results, $args );
		$output   = $template ? (string) $template : Display::render_results( $results, $args, $query['post_id'] );
		ob_start();
		Styles_Handler::enqueue_style_now( $args['crp_styles'] );
		$styles = ob_get_clean();
		return $styles . (string) apply_filters( 'get_crp', $output, $args );
	}
}

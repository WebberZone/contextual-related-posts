<?php
/**
 * Migration Service for CRP meta data migration.
 *
 * @package WebberZone\Contextual_Related_Posts\Util
 * @since 4.2.0
 */

namespace WebberZone\Contextual_Related_Posts\Util;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Migration Service class.
 *
 * @since 4.2.0
 */
class Migration_Service {

	/**
	 * Valid meta key pattern for CRP migration.
	 *
	 * @since 4.2.0
	 */
	private const META_KEY_PATTERN = '/^[a-z][a-z0-9_]*$/';

	/**
	 * Get migration status information.
	 *
	 * @since 4.2.0
	 *
	 * @return array Migration status.
	 */
	public static function get_status(): array {
		global $wpdb;

		$status = array(
			'complete'      => get_option( 'crp_meta_migration_done', false ),
			'total_entries' => 0,
			'total_posts'   => 0,
		);

		if ( ! $status['complete'] ) {
			$status['total_entries'] = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->postmeta} WHERE meta_key = %s", 'crp_post_meta' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$status['total_posts']   = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(DISTINCT post_id) FROM {$wpdb->postmeta} WHERE meta_key = %s", 'crp_post_meta' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		}

		return $status;
	}

	/**
	 * Migrate a batch of meta entries from crp_post_meta array to individual _crp_* keys.
	 *
	 * @since 4.2.0
	 *
	 * @param int  $last_id     Last processed meta_id.
	 * @param int  $batch_size  Number of entries to process.
	 * @param bool $dry_run    Whether this is a dry run.
	 * @return array Migration results.
	 */
	public static function migrate_batch( int $last_id = 0, int $batch_size = 100, bool $dry_run = false ): array {
		global $wpdb;

		$results = array(
			'migrated'  => 0,
			'last_id'   => $last_id,
			'remaining' => 0,
			'complete'  => false,
			'errors'    => array(),
		);

		// Get batch of entries.
		$posts = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->prepare(
				"SELECT meta_id, post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_id > %d ORDER BY meta_id ASC LIMIT %d",
				'crp_post_meta',
				$last_id,
				$batch_size
			)
		);

		if ( empty( $posts ) ) {
			$results['complete'] = true;
			if ( ! $dry_run ) {
				update_option( 'crp_meta_migration_done', true, false );
			}
			return $results;
		}

		$migrated_count = 0;

		foreach ( $posts as $post ) {
			$results['last_id'] = (int) $post->meta_id;
			$meta_value         = maybe_unserialize( $post->meta_value );

			// Skip invalid data.
			if ( is_object( $meta_value ) ) {
				$results['errors'][] = sprintf( 'Skipping object data for post %d', $post->post_id );
				continue;
			}

			if ( is_array( $meta_value ) ) {
				$should_delete = true;
				foreach ( $meta_value as $key => $value ) {
					if ( ! preg_match( self::META_KEY_PATTERN, $key ) ) {
						$results['errors'][] = sprintf( 'Invalid meta key "%s" for post %d', $key, $post->post_id );
						$should_delete       = false;
						continue;
					}

					if ( ! $dry_run ) {
						update_post_meta( $post->post_id, "_crp_{$key}", $value );
					}
				}

				if ( $should_delete && ! $dry_run ) {
					delete_post_meta( $post->post_id, 'crp_post_meta' );
					++$migrated_count;
				} elseif ( $should_delete ) {
					++$migrated_count;
				}
			}
		}

		$results['migrated'] = $migrated_count;

		// Calculate remaining.
		$remaining            = $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_id > %d", 'crp_post_meta', $results['last_id'] )
		);
		$results['remaining'] = (int) $remaining;

		return $results;
	}

	/**
	 * Rollback a batch of meta entries from individual _crp_* keys back to crp_post_meta array.
	 *
	 * @since 4.2.0
	 *
	 * @param int  $last_id     Last processed post_id.
	 * @param int  $batch_size  Number of posts to process.
	 * @param bool $dry_run    Whether this is a dry run.
	 * @return array Rollback results.
	 */
	public static function rollback_batch( int $last_id = 0, int $batch_size = 100, bool $dry_run = false ): array {
		global $wpdb;

		$results = array(
			'rolled_back' => 0,
			'last_id'     => $last_id,
			'remaining'   => 0,
			'complete'    => false,
			'errors'      => array(),
		);

		$like       = $wpdb->esc_like( '_crp_' ) . '%';
		$cache_like = $wpdb->esc_like( '_crp_cache' ) . '%';

		// Get batch of posts.
		$post_ids = $wpdb->get_col( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->prepare(
				"SELECT DISTINCT post_id FROM {$wpdb->postmeta} WHERE meta_key LIKE %s AND meta_key NOT LIKE %s AND post_id > %d ORDER BY post_id ASC LIMIT %d",
				$like,
				$cache_like,
				$last_id,
				$batch_size
			)
		);

		if ( empty( $post_ids ) ) {
			$results['complete'] = true;
			if ( ! $dry_run ) {
				delete_option( 'crp_meta_migration_done' );
			}
			return $results;
		}

		$rolled_back_count = 0;

		foreach ( $post_ids as $post_id ) {
			$results['last_id'] = (int) $post_id;

			// Get all _crp_ meta for this post.
			$meta_keys = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$wpdb->prepare(
					"SELECT meta_key, meta_value FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key LIKE %s AND meta_key NOT LIKE %s",
					$post_id,
					$like,
					$cache_like
				)
			);

			if ( empty( $meta_keys ) ) {
				continue;
			}

			// Build the legacy array.
			$legacy_data = array();
			foreach ( $meta_keys as $meta ) {
				$key                 = substr( $meta->meta_key, 5 ); // Remove '_crp_' prefix.
				$legacy_data[ $key ] = maybe_unserialize( $meta->meta_value );
			}

			if ( ! $dry_run ) {
				// Save legacy data.
				update_post_meta( $post_id, 'crp_post_meta', $legacy_data );

				// Delete individual meta entries.
				foreach ( $meta_keys as $meta ) {
					delete_post_meta( $post_id, $meta->meta_key );
				}
			}

			++$rolled_back_count;
		}

		$results['rolled_back'] = $rolled_back_count;

		// Calculate remaining.
		$remaining            = $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->prepare(
				"SELECT COUNT(DISTINCT post_id) FROM {$wpdb->postmeta} WHERE meta_key LIKE %s AND meta_key NOT LIKE %s AND post_id > %d",
				$like,
				$cache_like,
				$results['last_id']
			)
		);
		$results['remaining'] = (int) $remaining;

		return $results;
	}
}

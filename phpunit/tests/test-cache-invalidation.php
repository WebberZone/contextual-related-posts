<?php

use WebberZone\Contextual_Related_Posts\Admin\DB;
use WebberZone\Contextual_Related_Posts\CRP_Core_Query;
use WebberZone\Contextual_Related_Posts\Util\Cache;

class CacheInvalidationTest extends WP_UnitTestCase {

	/** The reverse query needs the plugin's FULLTEXT indexes, which activation normally creates. */
	public static function set_up_before_class() {
		parent::set_up_before_class();
		DB::create_fulltext_indexes();
	}

	/** @var int[] Posts for the batch escalation test, created once for the class. */
	private static $batch_posts = array();

	public static function wpSetUpBeforeClass( WP_UnitTest_Factory $factory ) {
		self::$batch_posts = $factory->post->create_many( 6 );
	}

	public function set_up() {
		parent::set_up();
		update_option( 'crp_settings', crp_settings_defaults() );
		crp_update_option( 'cache', 1 );
		crp_update_option( 'cache_posts', 1 );
		crp_update_option( 'post_types', 'post,page' );
		$this->reset_cache_statics();
	}

	/** Both statics are per-request state, so they outlive a test method without this. */
	private function reset_cache_statics() {
		foreach ( array( 'queue', 'flush_needed' ) as $name ) {
			$property = new ReflectionProperty( Cache::class, $name );

			if ( PHP_VERSION_ID < 80100 ) {
				$property->setAccessible( true );
			}

			$property->setValue( null, array() );
		}
	}

	public function tear_down() {
		remove_all_filters( 'get_crp_posts' );
		remove_all_filters( 'crp_clear_related_cache_on_save' );
		remove_all_filters( 'crp_related_cache_clear_batch_limit' );
		remove_all_filters( 'crp_is_importing' );
		remove_all_filters( 'crp_related_cache_clear_post_types' );
		remove_all_filters( 'get_post_metadata' );

		while ( $timestamp = wp_next_scheduled( 'crp_deferred_cache_flush' ) ) {
			wp_unschedule_event( $timestamp, 'crp_deferred_cache_flush' );
		}

		$this->reset_cache_statics();
		delete_option( 'crp_settings' );
		parent::tear_down();
	}

	private function seed_cache( $post_id, array $related = array() ) {
		Cache::set_cache( $post_id, 'testkey', $related, HOUR_IN_SECONDS, 'posts' );
		Cache::set_cache( $post_id, 'testkey', '<div>cached</div>', HOUR_IN_SECONDS, 'html' );
		wp_cache_delete( $post_id, 'post_meta' );
	}

	private function cache_rows( $post_id ) {
		global $wpdb;

		return (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key LIKE %s",
				$post_id,
				$wpdb->esc_like( '_crp_cache_' ) . '%'
			)
		);
	}

	/** Count only cron writes that add or remove the CRP flush, not core's own (do_pings etc.). */
	private function count_flush_cron_writes( &$writes ) {
		add_filter(
			'pre_update_option_cron',
			function ( $value, $old_value ) use ( &$writes ) {
				if ( $this->flush_cron_signature( $value ) !== $this->flush_cron_signature( $old_value ) ) {
					++$writes;
				}

				return $value;
			},
			99,
			2
		);
	}

	/** Read one of Cache's private static properties. */
	/** Timestamps at which the CRP flush is scheduled in a cron array. */
	private function flush_cron_signature( $cron ) {
		$found = array();

		if ( ! is_array( $cron ) ) {
			return $found;
		}

		foreach ( $cron as $timestamp => $hooks ) {
			if ( is_array( $hooks ) && isset( $hooks['crp_deferred_cache_flush'] ) ) {
				$found[] = $timestamp;
			}
		}

		sort( $found );

		return $found;
	}

	/** Stub the reverse query: real FULLTEXT relevance is not reliable against a few fixtures. */
	private function stub_related( array $ids ) {
		add_filter(
			'get_crp_posts',
			static function () use ( $ids ) {
				return $ids;
			},
			99
		);
	}

	public function test_save_clears_the_post_own_cache() {
		$post_id = self::factory()->post->create();
		Cache::process_queue();
		$this->seed_cache( $post_id );

		wp_update_post(
			array(
				'ID'         => $post_id,
				'post_title' => 'Edited',
			)
		);

		$this->assertSame( 0, $this->cache_rows( $post_id ) );
	}

	public function test_revision_does_not_clear_the_cache() {
		$post_id = self::factory()->post->create( array( 'post_content' => 'One' ) );
		Cache::process_queue();
		$this->seed_cache( $post_id );

		wp_save_post_revision( $post_id );

		$this->assertSame( 4, $this->cache_rows( $post_id ) );
	}

	public function test_related_posts_cache_is_cleared_on_save() {
		$target  = self::factory()->post->create();
		$post_id = self::factory()->post->create();
		Cache::process_queue();

		$this->seed_cache( $target );
		$this->stub_related( array( $target ) );

		wp_update_post(
			array(
				'ID'         => $post_id,
				'post_title' => 'Edited',
			)
		);
		Cache::process_queue();

		$this->assertSame( 0, $this->cache_rows( $target ) );
	}

	public function test_stale_ids_survive_a_second_save_in_the_same_request() {
		$target  = self::factory()->post->create();
		$post_id = self::factory()->post->create();
		Cache::process_queue();

		$this->seed_cache( $target );
		$this->seed_cache( $post_id, array( $target ) );
		$this->stub_related( array() );

		wp_update_post(
			array(
				'ID'         => $post_id,
				'post_title' => 'First',
			)
		);
		wp_update_post(
			array(
				'ID'         => $post_id,
				'post_title' => 'Second',
			)
		);
		Cache::process_queue();

		$this->assertSame( 0, $this->cache_rows( $target ) );
	}

	public function test_oversized_batch_schedules_a_flush_instead_of_clearing_each_post() {
		$target = self::factory()->post->create();
		Cache::process_queue();
		$this->seed_cache( $target );
		$this->stub_related( array( $target ) );

		add_filter(
			'crp_related_cache_clear_batch_limit',
			static function () {
				return 2;
			},
			99
		);

		foreach ( self::factory()->post->create_many( 3 ) as $post_id ) {
			wp_update_post(
				array(
					'ID'         => $post_id,
					'post_title' => 'Batch ' . $post_id,
				)
			);
		}
		Cache::process_queue();

		$this->assertNotFalse( wp_next_scheduled( 'crp_deferred_cache_flush' ) );
		$this->assertSame( 4, $this->cache_rows( $target ) );
	}

	public function test_oversized_batch_writes_the_cron_option_once() {
		$writes = 0;
		$this->count_flush_cron_writes( $writes );

		$this->stub_related( array() );
		add_filter(
			'crp_related_cache_clear_batch_limit',
			static function () {
				return 1;
			},
			99
		);

		foreach ( self::factory()->post->create_many( 4 ) as $post_id ) {
			wp_update_post(
				array(
					'ID'         => $post_id,
					'post_title' => 'Batch ' . $post_id,
				)
			);
		}
		Cache::process_queue();

		remove_all_filters( 'pre_update_option_cron' );

		$this->assertSame( 1, $writes );
		$this->assertNotFalse( wp_next_scheduled( 'crp_deferred_cache_flush' ) );
	}

	public function test_deferred_flush_empties_the_cache() {
		$post_id = self::factory()->post->create();
		Cache::process_queue();
		$this->seed_cache( $post_id );

		Cache::deferred_flush();

		$this->assertSame( 0, $this->cache_rows( $post_id ) );
	}

	public function test_delete_by_post_ids_counts_entries_and_leaves_others_alone() {
		$posts = self::factory()->post->create_many( 3 );
		Cache::process_queue();
		foreach ( $posts as $post_id ) {
			$this->seed_cache( $post_id );
		}

		$deleted = Cache::delete_by_post_ids( array( $posts[0], $posts[1] ) );

		$this->assertSame( 4, $deleted );
		$this->assertSame( 0, $this->cache_rows( $posts[0] ) );
		$this->assertSame( 0, $this->cache_rows( $posts[1] ) );
		$this->assertSame( 4, $this->cache_rows( $posts[2] ) );
	}

	public function test_ledger_is_written_for_an_anonymous_html_cacheable_request() {
		crp_update_option( 'cache', 1 );
		crp_update_option( 'cache_posts', 0 );
		wp_set_current_user( 0 );

		$query = new CRP_Core_Query( array( 'post_id' => self::factory()->post->create() ) );

		$this->assertTrue( $query->should_cache_posts_list() );
	}

	public function test_ledger_is_not_written_for_a_logged_in_html_only_request() {
		crp_update_option( 'cache', 1 );
		crp_update_option( 'cache_posts', 0 );
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'editor' ) ) );

		$query = new CRP_Core_Query( array( 'post_id' => self::factory()->post->create() ) );

		$this->assertFalse( $query->should_cache_posts_list() );
		wp_set_current_user( 0 );
	}

	public function test_ledger_is_not_written_for_a_password_cookie_html_only_request() {
		crp_update_option( 'cache', 1 );
		crp_update_option( 'cache_posts', 0 );
		wp_set_current_user( 0 );
		$_COOKIE[ 'wp-postpass_' . COOKIEHASH ] = 'secret';

		$query = new CRP_Core_Query( array( 'post_id' => self::factory()->post->create() ) );

		$this->assertFalse( $query->should_cache_posts_list() );
		unset( $_COOKIE[ 'wp-postpass_' . COOKIEHASH ] );
	}

	public function test_import_defers_a_single_flush_to_the_shutdown_drain() {
		add_filter( 'crp_is_importing', '__return_true', 99 );
		$this->stub_related( array() );

		$writes = 0;
		$this->count_flush_cron_writes( $writes );

		foreach ( self::factory()->post->create_many( 5 ) as $post_id ) {
			wp_update_post(
				array(
					'ID'         => $post_id,
					'post_title' => 'Imported ' . $post_id,
				)
			);
		}

		$this->assertSame( 0, $writes, 'No cron write during the import saves.' );
		$this->assertFalse( wp_next_scheduled( 'crp_deferred_cache_flush' ), 'Nothing scheduled mid-import.' );

		$drained_at = time();
		Cache::process_queue();

		$scheduled = wp_next_scheduled( 'crp_deferred_cache_flush' );

		remove_all_filters( 'pre_update_option_cron' );
		remove_all_filters( 'crp_is_importing' );

		$this->assertSame( 1, $writes, 'Exactly one cron write, at the drain.' );
		$this->assertNotFalse( $scheduled );
		$this->assertGreaterThanOrEqual( $drained_at + ( 5 * MINUTE_IN_SECONDS ), $scheduled, 'Delay runs from the drain, not the first save.' );
	}
}

<?php

use WebberZone\Contextual_Related_Posts\CRP_Core_Query;
use WebberZone\Contextual_Related_Posts\Util\Cache;

class QueryDefaultsTest extends WP_UnitTestCase {

	private function query( array $args = array() ): CRP_Core_Query {
		$id = self::factory()->post->create();
		return new CRP_Core_Query( array_merge( array( 'post_id' => $id ), $args ) );
	}

	public function test_missing_settings_use_query_and_cache_defaults() {
		delete_option( 'crp_settings' );
		$query = $this->query();
		$this->assertSame( 6, (int) $query->query_args['limit'] );
		$this->assertFalse( $query->random_order );
		$this->assertSame( 0, (int) $query->query_args['daily_range'] );
		$this->assertSame( 0, (int) $query->query_args['same_post_type'] );
		$this->assertSame( array( 'post', 'page' ), $query->query_args['post_type'] );
		$this->assertTrue( $query->should_cache() );
		$this->assertSame( 604800, (int) $query->query_args['cache_time'] );
		$this->assertFalse( get_option( 'crp_settings' ) );
	}
	public function test_saved_settings_and_caller_arguments_override_defaults() {
		update_option( 'crp_settings', array( 'limit' => 3, 'cache_posts' => 0, 'same_post_type' => 1 ) );
		$query = $this->query();
		$this->assertSame( 3, (int) $query->query_args['limit'] );
		$this->assertFalse( $query->should_cache() );
		$this->assertSame( array( 'post' ), $query->query_args['post_type'] );
		$override = $this->query( array( 'limit' => 2, 'cache_posts' => 1 ) );
		$this->assertSame( 2, (int) $override->query_args['limit'] );
		$this->assertTrue( $override->should_cache() );
	}
	public function test_default_post_cache_is_written_and_reused() {
		delete_option( 'crp_settings' );
		$source = self::factory()->post->create();
		$related = self::factory()->post->create();
		$args = array( 'post_id' => $source, 'manual_related' => array( $related ), 'limit' => 1 );
		$core = new CRP_Core_Query( $args );
		$cold = get_crp_posts( $args );
		$this->assertSame( array( $related ), wp_list_pluck( $cold, 'ID' ) );
		$key = Cache::get_key( $core->input_query_args );
		$this->assertSame( array( $related ), Cache::get_cache( $source, $key, 'posts', $core->input_query_args ) );
		$hit = false;
		$capture = static function ( $posts, $query, $instance ) use ( &$hit ) {
			$hit = $instance->in_cache;
			return $posts;
		};
		add_filter( 'crp_query_posts_pre_query', $capture, 10, 3 );
		try {
			$warm = get_crp_posts( $args );
		} finally {
			remove_filter( 'crp_query_posts_pre_query', $capture );
		}
		$this->assertTrue( $hit );
		$this->assertSame( array( $related ), wp_list_pluck( $warm, 'ID' ) );
	}

	public function test_new_subsite_defaults_do_not_inherit_another_sites_settings() {
		if ( ! is_multisite() ) {
			$this->markTestSkipped( 'Requires multisite.' );
		}
		update_option( 'crp_settings', array( 'limit' => 2, 'cache_posts' => 0 ) );
		$blog_id = self::factory()->blog->create();
		switch_to_blog( $blog_id );
		try {
			delete_option( 'crp_settings' );
			$query = $this->query();
			$this->assertSame( 6, (int) $query->query_args['limit'] );
			$this->assertTrue( $query->should_cache() );
			$this->assertFalse( get_option( 'crp_settings' ) );
		} finally {
			restore_current_blog();
		}
		$this->assertSame( 2, (int) $this->query()->query_args['limit'] );
		$this->assertFalse( $this->query()->should_cache() );
	}
}

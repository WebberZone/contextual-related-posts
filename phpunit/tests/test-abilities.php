<?php

use WebberZone\Contextual_Related_Posts\Main;
use WebberZone\Contextual_Related_Posts\Util\Cache;

class AbilitiesTest extends WP_UnitTestCase {

	private $test_filters = array();

	public function set_up() {
		parent::set_up();

		if ( ! function_exists( 'wp_get_ability' ) ) {
			$this->markTestSkipped( 'Requires the WordPress Abilities API.' );
		}

		update_option( 'crp_settings', crp_settings_defaults() );
		crp_update_option( 'cache', 0 );
		crp_update_option( 'cache_posts', 0 );
		crp_update_option( 'post_types', 'post,page' );
		Cache::delete();
	}

	public function tear_down() {
		foreach ( $this->test_filters as $filter ) {
			remove_filter( $filter[0], $filter[1], $filter[2] );
		}

		$this->test_filters = array();
		Cache::delete();
		wp_set_current_user( 0 );

		parent::tear_down();
	}

	private function add_test_filter( $hook, $callback, $priority = 10, $accepted_args = 1 ) {
		add_filter( $hook, $callback, $priority, $accepted_args );
		$this->test_filters[] = array( $hook, $callback, $priority );
	}

	public function test_related_input_schema_rejects_invalid_values_and_accepts_arrays() {
		$ability = wp_get_ability( 'contextual-related-posts/get-related' );
		$schema  = $ability->get_input_schema();

		$this->assertWPError( rest_validate_value_from_schema( array(), $schema ) );
		$this->assertWPError(
			rest_validate_value_from_schema(
				array(
					'post_id' => 1,
					'limit'   => 101,
				),
				$schema
			)
		);
		$this->assertNotWPError(
			rest_validate_value_from_schema(
				array(
					'post_id'       => 1,
					'post_types'    => array( 'post', 'page' ),
					'include_terms' => array( 1, 2 ),
					'exclude_terms' => array( 3 ),
				),
				$schema
			)
		);
	}

	public function test_related_posts_use_crp_arguments_and_filter_unreadable_results() {
		$source_id  = self::factory()->post->create();
		$visible_id = self::factory()->post->create(
			array(
				'post_title'   => 'Visible related post',
				'post_excerpt' => 'A visible excerpt.',
			)
		);
		$private_id = self::factory()->post->create(
			array(
				'post_status' => 'private',
				'post_title'  => 'Private related post',
			)
		);
		$subscriber  = self::factory()->user->create( array( 'role' => 'subscriber' ) );
		$received    = array();

		$this->add_test_filter(
			'get_crp_posts',
			static function ( $posts, $args ) use ( &$received, $visible_id, $private_id ) {
				$received = $args;
				return array( $visible_id, $private_id );
			},
			99,
			2
		);

		wp_set_current_user( $subscriber );
		$results = wp_get_ability( 'contextual-related-posts/get-related' )->execute(
			array(
				'post_id'       => $source_id,
				'limit'         => 3,
				'post_types'    => array( 'post' ),
				'include_terms' => array( 1 ),
				'exclude_terms' => array( 2 ),
			)
		);

		$this->assertNotWPError( $results );
		$this->assertCount( 1, $results );
		$this->assertSame(
			array(
				'id'      => $visible_id,
				'title'   => get_the_title( $visible_id ),
				'url'     => get_permalink( $visible_id ),
				'excerpt' => get_the_excerpt( $visible_id ),
			),
			$results[0]
		);
		$this->assertSame( array( 'post' ), $received['post_types'] );
		$this->assertSame( array( 1 ), $received['include_cat_ids'] );
		$this->assertSame( array( 2 ), $received['exclude_categories'] );
		$this->assertSame( 3, $received['limit'] );
		$this->assertSame( $source_id, $received['post_id']->ID );
	}

	public function test_related_posts_return_plain_text_titles_and_excerpts() {
		$source_id = self::factory()->post->create();
		$related_id = self::factory()->post->create(
			array(
				'post_title'   => 'Widgets &#8211; the "best" ones',
				'post_excerpt' => 'A <strong>bold</strong> take on widgets &#8212; read on&#8230;',
			)
		);

		$this->add_test_filter(
			'get_crp_posts',
			static function () use ( $related_id ) {
				return array( $related_id );
			},
			99,
			2
		);
		$this->add_test_filter(
			'excerpt_more',
			static function () {
				return ' <a class="read-more" href="">Read more</a>';
			},
			99
		);

		$results = wp_get_ability( 'contextual-related-posts/get-related' )->execute( array( 'post_id' => $source_id ) );

		$this->assertNotWPError( $results );
		$this->assertCount( 1, $results );

		foreach ( array( 'title', 'excerpt' ) as $field ) {
			$this->assertStringNotContainsString( '<', $results[0][ $field ] );
			$this->assertStringNotContainsString( '&#', $results[0][ $field ] );
			$this->assertStringNotContainsString( '&amp;', $results[0][ $field ] );
		}

		$this->assertSame(
			html_entity_decode( get_the_title( $related_id ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ),
			$results[0]['title']
		);
	}

	public function test_related_ability_denies_an_unreadable_source_post() {
		$private_id = self::factory()->post->create( array( 'post_status' => 'private' ) );
		$subscriber = self::factory()->user->create( array( 'role' => 'subscriber' ) );

		wp_set_current_user( $subscriber );

		$this->assertFalse( Main::get_instance()->abilities->can_get_related_posts( array( 'post_id' => $private_id ) ) );
		$this->assertWPError(
			wp_get_ability( 'contextual-related-posts/get-related' )->execute( array( 'post_id' => $private_id ) )
		);

		$request = new WP_REST_Request( 'GET', '/wp-abilities/v1/abilities/contextual-related-posts/get-related/run' );
		$request->set_query_params( array( 'input' => array( 'post_id' => (string) $private_id ) ) );
		$this->assertSame( 403, rest_do_request( $request )->get_status() );
	}
}

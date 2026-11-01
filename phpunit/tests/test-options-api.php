<?php

class OptionsApiTest extends WP_UnitTestCase {

	public function tear_down() {
		delete_option( 'crp_settings' );
		parent::tear_down();
	}
	public function test_update_option_recovers_from_a_non_array_setting() {
		update_option( 'crp_settings', 'not-an-array' );

		$this->assertTrue( crp_update_option( 'cache', 1 ) );

		$settings = get_option( 'crp_settings' );

		$this->assertIsArray( $settings );
		$this->assertSame( 1, $settings['cache'] );
	}
	public function test_delete_option_removes_an_existing_key() {
		update_option(
			'crp_settings',
			array(
				'cache' => 1,
				'limit' => 6,
			)
		);

		$this->assertTrue( crp_delete_option( 'cache' ) );

		$settings = get_option( 'crp_settings' );

		$this->assertArrayNotHasKey( 'cache', $settings );
		$this->assertSame( 6, $settings['limit'] );
	}
}

<?php

class OptionsApiTest extends WP_UnitTestCase {

	public function tear_down() {
		delete_option( 'crp_settings' );
		parent::tear_down();
	}

	public function test_update_option_creates_the_settings_when_missing() {
		delete_option( 'crp_settings' );

		$this->assertTrue( crp_update_option( 'cache', 1 ) );

		$settings = get_option( 'crp_settings' );

		$this->assertIsArray( $settings );
		$this->assertSame( 1, $settings['cache'] );
	}

	public function test_update_option_recovers_from_a_non_array_setting() {
		update_option( 'crp_settings', 'not-an-array' );

		$this->assertTrue( crp_update_option( 'cache', 1 ) );

		$settings = get_option( 'crp_settings' );

		$this->assertIsArray( $settings );
		$this->assertSame( 1, $settings['cache'] );
	}

	public function test_update_option_recovers_from_a_false_setting() {
		update_option( 'crp_settings', false );

		crp_update_option( 'cache', 1 );

		$settings = get_option( 'crp_settings' );

		$this->assertIsArray( $settings );
		$this->assertSame( 1, $settings['cache'] );
	}

	public function test_delete_option_with_missing_settings_does_not_error() {
		delete_option( 'crp_settings' );

		crp_delete_option( 'cache' );

		$this->assertIsArray( get_option( 'crp_settings', array() ) );
	}

	public function test_delete_option_with_a_non_array_setting_does_not_error() {
		update_option( 'crp_settings', 'not-an-array' );

		crp_delete_option( 'cache' );

		$this->assertIsArray( get_option( 'crp_settings' ) );
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

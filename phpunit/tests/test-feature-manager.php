<?php

use WebberZone\Contextual_Related_Posts\Admin\Settings;
use WebberZone\Contextual_Related_Posts\Feature_Manager;

class FeatureManagerTest extends WP_UnitTestCase {

	/**
	 * Ensure every feature has a matching setting and UI field.
	 */
	public function test_features_have_settings_toggles() {
		$features = Feature_Manager::get_features();
		$settings = Settings::settings_features();

		$expected = array(
			'blocks'                  => 'enable_blocks',
			'feed'                    => 'enable_feed',
			'legacy_widgets'          => 'enable_legacy_widgets',
			'rest_api'               => 'enable_rest_api',
			'abilities_api'          => 'enable_abilities_api',
			'advanced_block'          => 'enable_advanced_block',
			'query_block'             => 'enable_query_block',
			'featured_image_block'    => 'enable_featured_image_block',
			'related_posts_pro_block' => 'enable_related_posts_pro_block',
			'page_builders'           => 'enable_page_builders',
			'bot_protection'          => 'enable_bot_protection',
			'lazy_load'               => 'enable_lazy_load',
			'custom_tables'           => 'enable_custom_tables',
			'woocommerce'             => 'enable_woocommerce',
		);

		$this->assertSame( array_keys( $expected ), array_keys( $features ) );

		foreach ( $expected as $feature => $setting ) {
			$this->assertSame( $setting, $features[ $feature ]['setting'] );
			$this->assertTrue( $features[ $feature ]['default'] );
			$this->assertArrayHasKey( $setting, $settings );
			$this->assertSame( 'checkbox', $settings[ $setting ]['type'] );
		}
	}

	/**
	 * Ensure the features section is present and comes first in settings navigation.
	 */
	public function test_features_section_is_first() {
		$sections = Settings::get_settings_sections();

		$this->assertSame( 'features', array_key_first( $sections ) );
		$this->assertArrayHasKey( 'features', Settings::get_registered_settings() );
	}
}

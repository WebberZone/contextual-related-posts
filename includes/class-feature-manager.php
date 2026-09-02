<?php
/**
 * Feature Manager.
 *
 * Central gate that decides which optional plugin features are loaded.
 *
 * @package WebberZone\Contextual_Related_Posts
 */

namespace WebberZone\Contextual_Related_Posts;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Feature Manager class.
 *
 * All features default to enabled — a missing settings key means the feature is on, so
 * upgrades see no change in behaviour. Reads the raw settings option directly instead
 * of crp_get_option(), which would load the admin Settings class on every request;
 * this makes the check safe to call at plugins_loaded.
 *
 * @since 4.4.0
 */
class Feature_Manager {

	/**
	 * Cached copy of the settings option.
	 *
	 * @since 4.4.0
	 *
	 * @var array|null
	 */
	private static $settings = null;

	/**
	 * Get the map of toggleable features.
	 *
	 * @since 4.4.0
	 *
	 * @return array Associative array of feature ID => array with 'setting' and 'default' keys.
	 */
	public static function get_features(): array {
		$features = array(
			'blocks'                  => array(
				'setting' => 'enable_blocks',
				'default' => true,
			),
			'feed'                    => array(
				'setting' => 'enable_feed',
				'default' => true,
			),
			'legacy_widgets'          => array(
				'setting' => 'enable_legacy_widgets',
				'default' => true,
			),
			'query_block'             => array(
				'setting' => 'enable_query_block',
				'default' => true,
			),
			'featured_image_block'    => array(
				'setting' => 'enable_featured_image_block',
				'default' => true,
			),
			'related_posts_pro_block' => array(
				'setting' => 'enable_related_posts_pro_block',
				'default' => true,
			),
			'page_builders'           => array(
				'setting' => 'enable_page_builders',
				'default' => true,
			),
			'bot_protection'          => array(
				'setting' => 'enable_bot_protection',
				'default' => true,
			),
			'lazy_load'               => array(
				'setting' => 'enable_lazy_load',
				'default' => true,
			),
			'custom_tables'           => array(
				'setting' => 'enable_custom_tables',
				'default' => true,
			),
			'woocommerce'             => array(
				'setting' => 'enable_woocommerce',
				'default' => true,
			),
		);

		/**
		 * Filter the map of toggleable features.
		 *
		 * @since 4.4.0
		 *
		 * @param array $features Associative array of feature ID => array with 'setting' and 'default' keys.
		 */
		return apply_filters( 'crp_features', $features );
	}

	/**
	 * Whether a feature is enabled.
	 *
	 * A feature is enabled when its setting is missing (default) or truthy.
	 * Unknown feature IDs are treated as enabled.
	 *
	 * @since 4.4.0
	 *
	 * @param string $feature Feature ID.
	 * @return bool Whether the feature is enabled.
	 */
	public static function is_enabled( string $feature ): bool {
		$features = self::get_features();

		if ( isset( $features[ $feature ] ) ) {
			$setting = $features[ $feature ]['setting'];
			$default = ! empty( $features[ $feature ]['default'] );

			if ( null === self::$settings ) {
				$settings       = get_option( 'crp_settings' );
				self::$settings = is_array( $settings ) ? $settings : array();
			}

			$enabled = isset( self::$settings[ $setting ] ) ? ! empty( self::$settings[ $setting ] ) : $default;
		} else {
			$enabled = true;
		}

		/**
		 * Filter whether a feature is enabled.
		 *
		 * @since 4.4.0
		 *
		 * @param bool   $enabled Whether the feature is enabled.
		 * @param string $feature Feature ID.
		 */
		return (bool) apply_filters( 'crp_feature_enabled', $enabled, $feature );
	}
}

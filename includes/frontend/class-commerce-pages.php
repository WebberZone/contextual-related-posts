<?php
/**
 * Commerce page detection for automatic related posts.
 *
 * @package Contextual_Related_Posts
 */

namespace WebberZone\Contextual_Related_Posts\Frontend;

if ( ! defined( 'WPINC' ) ) {
	exit;
}

/**
 * Detect transactional commerce pages independently of premium integrations.
 *
 * @since 4.5.3
 */
class Commerce_Pages {

	/**
	 * Whether automatic related posts should be excluded on this post.
	 *
	 * @since 4.5.3
	 * @param \WP_Post $post Post being displayed.
	 * @return bool
	 */
	public static function exclude_automatic_output( \WP_Post $post ): bool {
		$exclude = false;

		if ( class_exists( 'WooCommerce' ) ) {
			$page_ids = array_map( 'wc_get_page_id', array( 'cart', 'checkout', 'myaccount' ) );
			$exclude  = in_array( (int) $post->ID, $page_ids, true );
			if ( (int) get_queried_object_id() === (int) $post->ID ) {
				$exclude = $exclude || is_cart() || is_checkout() || is_account_page();
			}
			$exclude = $exclude || self::has_components(
				$post->post_content,
				array( 'woocommerce_cart', 'woocommerce_checkout', 'woocommerce_my_account' ),
				array( 'woocommerce/cart', 'woocommerce/checkout' )
			);
		}

		if ( ! $exclude && class_exists( '\FluentCart\Api\StoreSettings' ) ) {
			$settings = get_option( 'fluent_cart_store_settings', array() );
			$settings = is_array( $settings ) ? $settings : array();
			foreach ( array( 'cart', 'checkout', 'receipt', 'customer_profile', 'order_review', 'custom_payment', 'login', 'registration' ) as $page ) {
				$page_id = $settings[ $page . '_page_id' ] ?? 0;
				if ( is_scalar( $page_id ) && absint( $page_id ) === (int) $post->ID ) {
					$exclude = true;
					break;
				}
			}
			$exclude = $exclude || self::has_components(
				$post->post_content,
				array( 'fluent_cart_cart', 'fluent_cart_checkout', 'fluent_cart_receipt', 'fluent_cart_customer_profile', 'fluent_cart_order_review', 'fluent_cart_login_form', 'fluent_cart_registration_form' ),
				array( 'fluent-cart/cart', 'fluent-cart/checkout', 'fluent-cart/customer-profile', 'fluent-cart/login-form', 'fluent-cart/registration-form' )
			);
		}

		/**
		 * Filter commerce exclusions for automatically inserted related posts.
		 *
		 * @since 4.5.3
		 * @param bool     $exclude Whether to suppress automatic output.
		 * @param \WP_Post $post    Post being displayed.
		 */
		return (bool) apply_filters( 'crp_exclude_automatic_on_commerce_page', $exclude, $post );
	}

	/**
	 * Detect commerce components, including those inside synced patterns.
	 *
	 * @since 4.5.3
	 * @param string   $content    Stored post content.
	 * @param string[] $shortcodes Shortcode names.
	 * @param string[] $blocks     Block names.
	 * @param int[]    $visited    Previously visited synced patterns.
	 * @return bool
	 */
	private static function has_components( string $content, array $shortcodes, array $blocks, array $visited = array() ): bool {
		foreach ( $shortcodes as $shortcode ) {
			if ( has_shortcode( $content, $shortcode ) ) {
				return true;
			}
		}
		foreach ( $blocks as $block ) {
			if ( has_block( $block, $content ) ) {
				return true;
			}
		}
		if ( ! has_block( 'core/block', $content ) ) {
			return false;
		}

		$pending = parse_blocks( $content );
		while ( $pending ) {
			$block   = array_pop( $pending );
			$pending = array_merge( $pending, $block['innerBlocks'] );
			if ( 'core/block' !== $block['blockName'] ) {
				continue;
			}
			$ref = $block['attrs']['ref'] ?? 0;
			$ref = is_scalar( $ref ) ? absint( $ref ) : 0;
			if ( ! $ref || in_array( $ref, $visited, true ) ) {
				continue;
			}
			$visited[] = $ref;
			$pattern   = get_post( $ref );
			if ( $pattern && 'wp_block' === $pattern->post_type && self::has_components( $pattern->post_content, $shortcodes, $blocks, $visited ) ) {
				return true;
			}
		}

		return false;
	}
}

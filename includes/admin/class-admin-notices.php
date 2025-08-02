<?php
/**
 * Controls admin notices.
 *
 * @package WebberZone\Contextual_Related_Posts
 */

namespace WebberZone\Contextual_Related_Posts\Admin;

use WebberZone\Contextual_Related_Posts\Util\Hook_Registry;
use function WebberZone\Contextual_Related_Posts\wz_crp;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Admin Notices Class.
 *
 * @since 4.0.0
 */
class Admin_Notices {

	/**
	 * Admin Notices API instance.
	 *
	 * @since 4.0.0
	 *
	 * @var Admin_Notices_API
	 */
	private ?Admin_Notices_API $admin_notices_api = null;

	/**
	 * Constructor class.
	 *
	 * @since 4.0.0
	 */
	public function __construct() {
		// Add initialization hook that runs after full plugin setup.
		Hook_Registry::add_action( 'admin_init', array( $this, 'init' ), 5 );
	}

	/**
	 * Initialize the notices API reference after full plugin initialization.
	 *
	 * @since 4.0.0
	 */
	public function init() {
		$this->admin_notices_api = wz_crp()->admin->admin_notices_api;
		$this->register_notices();
	}

	/**
	 * Register all notices with the API.
	 *
	 * @since 4.0.0
	 */
	private function register_notices() {
		// Only register notices if the API is available.
		if ( ! $this->admin_notices_api ) {
			return;
		}

		$this->register_fulltext_index_notice();
		$this->register_missing_table_notice();
	}

	/**
	 * Register fulltext index notice.
	 *
	 * @since 4.0.0
	 */
	private function register_fulltext_index_notice() {
		// Check if admin_notices_api is available.
		if ( ! $this->admin_notices_api ) {
			return;
		}

		$this->admin_notices_api->register_notice(
			array(
				'id'          => 'crp_missing_fulltext_index',
				'message'     => sprintf(
					'<p>%s <a href="%s">%s</a></p>',
					esc_html__( 'Contextual Related Posts: Some fulltext indexes are missing, which will affect the related posts.', 'contextual-related-posts' ),
					esc_url( admin_url( 'tools.php?page=crp_tools_page' ) ),
					esc_html__( 'Click here to recreate indexes.', 'contextual-related-posts' )
				),
				'type'        => 'warning',
				'dismissible' => true,
				'capability'  => 'manage_options',
				'conditions'  => array(
					function () {
						return current_user_can( 'manage_options' ) &&
								! \WebberZone\Contextual_Related_Posts\Admin\Db::is_fulltext_index_installed();
					},
				),
			)
		);
	}

	/**
	 * Register missing table notice.
	 *
	 * @since 4.0.0
	 */
	private function register_missing_table_notice() {
		// Check if admin_notices_api is available.
		if ( ! $this->admin_notices_api ) {
			return;
		}

		$this->admin_notices_api->register_notice(
			array(
				'id'          => 'crp_missing_custom_tables',
				'message'     => sprintf(
					'<p>%s <a href="%s">%s</a></p>',
					esc_html__( 'Contextual Related Posts: Custom tables are missing, which will affect related posts performance.', 'contextual-related-posts' ),
					esc_url( admin_url( 'tools.php?page=crp_tools_page' ) ),
					esc_html__( 'Click here to recreate tables.', 'contextual-related-posts' )
				),
				'type'        => 'warning',
				'dismissible' => true,
				'capability'  => 'manage_options',
				'conditions'  => array(
					function () {
						return current_user_can( 'manage_options' ) &&
								class_exists( '\WebberZone\Contextual_Related_Posts\Pro\Custom_Tables\Table_Manager' ) &&
								! ( new \WebberZone\Contextual_Related_Posts\Pro\Custom_Tables\Table_Manager() )->is_table_installed(
									( new \WebberZone\Contextual_Related_Posts\Pro\Custom_Tables\Table_Manager() )->content_table
								);
					},
				),
			)
		);
	}
}

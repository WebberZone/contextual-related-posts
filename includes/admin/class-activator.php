<?php
/**
 * Functions run on activation / deactivation.
 *
 * @package Contextual_Related_Posts
 */

namespace WebberZone\Contextual_Related_Posts\Admin;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Activator class
 *
 * @since 3.5.0
 */
class Activator {

	/**
	 * Constructor class.
	 *
	 * @since 3.5.0
	 */
	public function __construct() {
		add_action( 'wp_initialize_site', array( $this, 'activate_new_site' ) );
		add_action( 'init', array( $this, 'update_db_check' ) );
	}

	/**
	 * Fired when the plugin is Network Activated.
	 *
	 * @since 3.5.0
	 *
	 * @param    boolean $network_wide    True if WPMU superadmin uses
	 *                                    "Network Activate" action, false if
	 *                                    WPMU is disabled or plugin is
	 *                                    activated on an individual blog.
	 */
	public static function activation_hook( $network_wide ) {

		if ( is_multisite() && $network_wide ) {
			$sites = get_sites(
				array(
					'archived' => 0,
					'spam'     => 0,
					'deleted'  => 0,
				)
			);

			foreach ( $sites as $site ) {
				switch_to_blog( (int) $site->blog_id );
				self::single_activate();
			}

			// Switch back to the current blog.
			restore_current_blog();

		} else {
			self::single_activate();
		}
	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since 3.5.0
	 */
	public static function single_activate() {
		global $wpdb;

		// Create FULLTEXT indexes.
		$wpdb->hide_errors();
		Db::create_fulltext_indexes();
		$wpdb->show_errors();

		// Set the database version.
		update_option( 'crp_db_version', CRP_DB_VERSION );

		// Create PRO custom tables if the class exists.
		if ( class_exists( '\WebberZone\Contextual_Related_Posts\Pro\Custom_Tables\Table_Manager' ) ) {
			$table_manager = new \WebberZone\Contextual_Related_Posts\Pro\Custom_Tables\Table_Manager();
			$table_manager->maybe_create_table( $table_manager->content_table, $table_manager->create_content_table_sql() );

			// Update the Pro extension's DB version.
			update_option(
				\WebberZone\Contextual_Related_Posts\Pro\Custom_Tables\Table_Manager::$db_version_option,
				\WebberZone\Contextual_Related_Posts\Pro\Custom_Tables\Table_Manager::$db_version
			);
		}
	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since 3.5.0
	 *
	 * @param  int|\WP_Site $blog WordPress 5.1 passes a WP_Site object.
	 */
	public static function activate_new_site( $blog ) {

		if ( ! is_plugin_active_for_network( plugin_basename( CRP_PLUGIN_FILE ) ) ) {
			return;
		}

		if ( ! is_int( $blog ) ) {
			$blog = $blog->id;
		}

		switch_to_blog( $blog );
		self::single_activate();
		restore_current_blog();
	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since 3.5.0
	 *
	 * @param    boolean $network_wide    True if WPMU superadmin uses
	 *                                    "Network Deactivate" action, false if
	 *                                    WPMU is disabled or plugin is
	 *                                    deactivated on an individual blog.
	 */
	public static function deactivation_hook( $network_wide ) {

		if ( is_multisite() && $network_wide ) {

			// Get all blogs in the network and activate plugin on each one.
			$sites = get_sites(
				array(
					'archived' => 0,
					'spam'     => 0,
					'deleted'  => 0,
				)
			);

			foreach ( $sites as $site ) {
				switch_to_blog( (int) $site->blog_id );
				self::single_deactivate();
			}

			// Switch back to the current blog.
			restore_current_blog();

		} else {
			self::single_deactivate();
		}
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since 3.5.0
	 */
	public static function single_deactivate() {
		$settings = get_option( 'crp_settings' );

		if ( ! empty( $settings['uninstall_indices_deactivate'] ) ) {
			Db::delete_fulltext_indexes();
			delete_option( 'crp_db_version' );
		}
	}

	/**
	 * Function to call install function if needed.
	 *
	 * @since 3.5.0
	 */
	public static function update_db_check() {
		global $network_wide;

		if ( get_option( 'crp_db_version' ) !== CRP_DB_VERSION ) {
			self::activation_hook( $network_wide );
		}
	}
}

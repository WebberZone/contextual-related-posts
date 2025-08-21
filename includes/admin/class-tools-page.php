<?php
/**
 * Generates the Tools page.
 *
 * @since 3.5.0
 *
 * @package Contextual_Related_Posts
 */

namespace WebberZone\Contextual_Related_Posts\Admin;

use WebberZone\Contextual_Related_Posts\Util\Hook_Registry;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Tools page class.
 */
class Tools_Page {

	/**
	 * Parent Menu ID.
	 *
	 * @since 3.5.0
	 *
	 * @var string Parent Menu ID.
	 */
	public $parent_id;

	/**
	 * Constructor.
	 */
	public function __construct() {
		Hook_Registry::add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		Hook_Registry::add_action( 'admin_init', array( $this, 'process_settings_export' ) );
		Hook_Registry::add_action( 'admin_init', array( $this, 'process_settings_import' ), 9 );
		Hook_Registry::add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}

	/**
	 * Admin Menu.
	 *
	 * @since 3.5.0
	 */
	public function admin_menu() {

		$this->parent_id = add_management_page(
			esc_html__( 'Contextual Related Posts Tools', 'contextual-related-posts' ),
			esc_html__( 'Related Posts Tools', 'contextual-related-posts' ),
			'manage_options',
			'crp_tools_page',
			array( $this, 'render_page' )
		);
	}

	/**
	 * Render the tools settings page.
	 *
	 * @since 3.5.0
	 *
	 * @return void
	 */
	public static function render_page() {

		/* Recreate indices */
		if ( ( isset( $_POST['crp_recreate_indices'] ) ) && ( check_admin_referer( 'crp-tools-settings' ) ) ) {
			Db::delete_fulltext_indexes();
			Db::create_fulltext_indexes();
			add_settings_error( 'crp-notices', '', esc_html__( 'Indices have been recreated', 'contextual-related-posts' ), 'success' );
		}

		/* Message for successful file import */
		if ( isset( $_GET['settings_import'] ) && 'success' === $_GET['settings_import'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			add_settings_error( 'crp-notices', '', esc_html__( 'Settings have been imported successfully', 'contextual-related-posts' ), 'updated' );
		}

		ob_start();
		?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Contextual Related Posts Tools', 'contextual-related-posts' ); ?></h1>
		<?php do_action( 'crp_tools_page_header' ); ?>
		<p>
			<a class="crp_button crp_button_blue" href="<?php echo esc_url( admin_url( 'options-general.php?page=crp_options_page' ) ); ?>">
			<?php esc_html_e( 'Visit the Settings page', 'contextual-related-posts' ); ?>
			</a>
		</p>

		<?php settings_errors(); ?>

		<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
		<div id="post-body-content">

			<div class="postbox">
				<h2><span><?php esc_html_e( 'Clear cache', 'contextual-related-posts' ); ?></span></h2>
				<div class="inside">
					<p>
						<button type="button" name="cache_clear" id="cache_clear" class="button button-secondary" onclick="return crpClearCache();">
							<?php esc_html_e( 'Clear cache', 'contextual-related-posts' ); ?>
						</button>
					</p>
					<p class="description">
					<?php esc_html_e( 'Clear the Contextual Related Posts cache. This might take a while if you have a lot of posts.', 'contextual-related-posts' ); ?>
					</p>
				</div>
			</div>

			<div class="postbox">
				<h2><span><?php esc_html_e( 'Recreate FULLTEXT index', 'contextual-related-posts' ); ?></span></h2>
				<div class="inside">
					<form method="post">
						<p>
							<?php
								printf(
									'<input name="crp_recreate_indices" type="submit" id="crp_recreate_indices" class="button button-secondary" value="%2$s" onclick="if ( ! confirm(\'%1$s\') ) return false;" />',
									esc_attr__( 'Are you sure you want to recreate the index?', 'contextual-related-posts' ),
									esc_attr__( 'Recreate Index', 'contextual-related-posts' )
								);
							?>
						</p>
						<p class="description">
							<?php esc_html_e( 'Recreate the FULLTEXT index that Contextual Related Posts uses to get the relevant related posts. This might take a lot of time to regenerate if you have a lot of posts.', 'contextual-related-posts' ); ?>
						</p>
						<p class="description"><?php esc_html_e( 'If the Recreate Index button fails, please run the following queries in phpMyAdmin or Adminer. Remember to backup your database first!', 'contextual-related-posts' ); ?></p>
						<div class="crp-code-wrapper">
							<?php $sql_queries = self::recreate_indices_sql(); ?>
							<pre id="crp-indices-sql"><code><?php echo implode( "\n", array_map( 'esc_html', $sql_queries ) ); ?></code></pre>
						</div>
						<script>
							jQuery(document).ready(function($) {
								crpAddCopyButton('crp-indices-sql');
							});
						</script>
						<?php wp_nonce_field( 'crp-tools-settings' ); ?>
					</form>
				</div>
			</div>

			<div class="postbox">
				<h2><span><?php esc_html_e( 'Export/Import settings', 'contextual-related-posts' ); ?></span></h2>
				<div class="inside">
					<form method="post">
						<p class="description">
						<?php esc_html_e( 'Export the plugin settings for this site as a .json file. This allows you to easily import the configuration into another site.', 'contextual-related-posts' ); ?>
						</p>
						<p><input type="hidden" name="crp_action" value="export_settings" /></p>
						<p>
						<?php submit_button( esc_html__( 'Export Settings', 'contextual-related-posts' ), 'primary', 'crp_export_settings', false ); ?>
						</p>
						<?php wp_nonce_field( 'crp_export_settings_nonce', 'crp_export_settings_nonce' ); ?>
					</form>

					<form method="post" enctype="multipart/form-data">
						<p class="description">
						<?php esc_html_e( 'Import the plugin settings from a .json file. This file can be obtained by exporting the settings on another site using the form above.', 'contextual-related-posts' ); ?>
						</p>
						<p>
							<input type="file" name="import_settings_file" />
						</p>
						<p>
						<?php submit_button( esc_html__( 'Import Settings', 'contextual-related-posts' ), 'primary', 'crp_import_settings', false ); ?>
						</p>
						<input type="hidden" name="crp_action" value="import_settings" />
						<?php wp_nonce_field( 'crp_import_settings_nonce', 'crp_import_settings_nonce' ); ?>
					</form>
				</div>
			</div>

			<?php
			/**
			 * Action hook to add additional tools page content.
			 *
			 * @since 4.0.0
			 */
			do_action( 'crp_admin_tools_page_content' );
			?>

		</div><!-- /#post-body-content -->

		<div id="postbox-container-1" class="postbox-container">

			<div id="side-sortables" class="meta-box-sortables ui-sortable">
			<?php include_once 'settings/sidebar.php'; ?>
			</div><!-- /#side-sortables -->

		</div><!-- /#postbox-container-1 -->
		</div><!-- /#post-body -->
		<br class="clear" />
		</div><!-- /#poststuff -->

	</div><!-- /.wrap -->

		<?php
		echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Retrieves the SQL code to recreate the fulltext indexes.
	 *
	 * @since 3.5.0
	 */
	public static function recreate_indices_sql() {
		global $wpdb;

		$old_indexes = Db::get_old_fulltext_indexes();
		$new_indexes = Db::get_fulltext_indexes();
		$all_indexes = array_keys( array_merge( $old_indexes, $new_indexes ) );

		$sql = array();

		// Add DROP statements for all possible indexes.
		foreach ( $all_indexes as $index ) {
			if ( Db::is_index_installed( $index ) ) {
				$sql[] = "ALTER TABLE {$wpdb->posts} DROP INDEX {$index};";
			}
		}

		// Add ADD statements only for the new indexes.
		if ( ! empty( $new_indexes ) ) {
			foreach ( $new_indexes as $index => $value ) {
				$sql[] = "ALTER TABLE {$wpdb->posts} ADD FULLTEXT {$index} {$value};";
			}
		}

		/**
		 * Filter the SQL code to recreate the Fulltext indices.
		 *
		 * @since 3.5.0
		 *
		 * @param array $sql Array of SQL queries.
		 */
		$sql = apply_filters( 'crp_recreate_indices_sql', $sql );

		return $sql;
	}


	/**
	 * Process a settings export that generates a .json file of the shop settings
	 *
	 * @since 2.9.0
	 */
	public static function process_settings_export() {

		if ( empty( $_POST['crp_action'] ) || 'export_settings' !== $_POST['crp_action'] ) {
			return;
		}

		if ( ! isset( $_POST['crp_export_settings_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['crp_export_settings_nonce'] ), 'crp_export_settings_nonce' ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$settings = get_option( 'crp_settings' );

		ignore_user_abort( true );

		nocache_headers();
		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=crp-settings-export-' . gmdate( 'm-d-Y' ) . '.json' );
		header( 'Expires: 0' );

		echo wp_json_encode( $settings );
		exit;
	}

	/**
	 * Process a settings import from a json file
	 *
	 * @since 2.9.0
	 */
	public static function process_settings_import() {

		if ( empty( $_POST['crp_action'] ) || 'import_settings' !== $_POST['crp_action'] ) {
			return;
		}

		if ( ! isset( $_POST['crp_import_settings_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['crp_import_settings_nonce'] ), 'crp_import_settings_nonce' ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$filename = 'import_settings_file';

		$tmp       = isset( $_FILES[ $filename ]['name'] ) ? explode( '.', sanitize_file_name( wp_unslash( $_FILES[ $filename ]['name'] ) ) ) : array();
		$extension = end( $tmp );

		if ( 'json' !== $extension ) {
			wp_die( esc_html__( 'Please upload a valid .json file', 'contextual-related-posts' ) );
		}

		$import_file = isset( $_FILES[ $filename ]['tmp_name'] ) ? ( wp_unslash( $_FILES[ $filename ]['tmp_name'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		if ( empty( $import_file ) ) {
			wp_die( esc_html__( 'Please upload a file to import', 'contextual-related-posts' ) );
		}

		// Retrieve the settings from the file and convert the json object to an array.
		$settings = (array) json_decode( file_get_contents( $import_file ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

		update_option( 'crp_settings', $settings );

		wp_safe_redirect(
			add_query_arg(
				array(
					'page'            => 'crp_tools_page',
					'settings_import' => 'success',
				),
				admin_url( 'tools.php' )
			)
		);
		exit;
	}

	/**
	 * Enqueue scripts and styles.
	 *
	 * @since 3.5.0
	 *
	 * @param string $hook The current screen hook.
	 */
	public function admin_enqueue_scripts( $hook ) {
		$screen = get_current_screen();

		if ( $this->parent_id === $screen->id || $this->parent_id === $hook ) {
			wp_enqueue_script( 'crp-admin-js' );
			wp_enqueue_style( 'crp-admin-ui-css' );
			wp_localize_script(
				'crp-admin-js',
				'crp_admin_data',
				array(
					'security'       => wp_create_nonce( 'crp-admin' ),
					'clear_cache'    => __( 'Clear cache', 'contextual-related-posts' ),
					'clearing_cache' => __( 'Clearing cache', 'contextual-related-posts' ),
				)
			);
		}
	}
}

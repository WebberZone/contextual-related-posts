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
use WebberZone\Contextual_Related_Posts\Util\Migration_Service;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Tools page class.
 */
class Tools_Page {

	const BATCH_SIZE = 100;

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
		Hook_Registry::add_action( 'wp_ajax_crp_migrate_meta', array( $this, 'migrate_meta_batch' ) );
		Hook_Registry::add_action( 'wp_ajax_crp_undo_migrate_meta', array( $this, 'undo_migrate_meta_batch' ) );
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

		global $wpdb;

		// Get counts for migration.
		$migration_count = self::get_migration_count();
		$undo_count      = self::get_undo_count();

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

			<?php if ( ! get_option( 'crp_meta_migration_done', false ) ) : ?>
			<div class="postbox">
				<h2><span><?php esc_html_e( 'Migrate Post Meta', 'contextual-related-posts' ); ?></span></h2>
				<div class="inside">
					<p class="description">
					<?php esc_html_e( 'This tool migrates the old crp_post_meta array storage to individual meta keys for better performance and cleaner data structure.', 'contextual-related-posts' ); ?>
					<?php if ( $migration_count > 0 ) : ?>
						<br />
						<?php
						$count = number_format_i18n( $migration_count );
						/* translators: %s: number of posts */
						printf( esc_html__( '%s posts need migration.', 'contextual-related-posts' ), esc_html( $count ) );
						?>
					<?php endif; ?>
					</p>
					<p>
						<button type="button" id="crp_migrate_meta" class="button button-primary">
							<?php esc_html_e( 'Start Migration', 'contextual-related-posts' ); ?>
						</button>
					</p>
					<div id="crp-migration-progress" style="display: none;">
						<p><?php esc_html_e( 'Migration in progress...', 'contextual-related-posts' ); ?></p>
						<div class="progress-bar" style="width: 100%; background-color: #f1f1f1; border: 1px solid #ddd; height: 20px; margin: 10px 0;">
							<div id="crp-migration-bar" style="height: 100%; background-color: #007cba; width: 0%;"></div>
						</div>
						<p id="crp-migration-status"></p>
					</div>
					<?php wp_nonce_field( 'crp_migrate_meta_nonce', 'crp_migrate_meta_nonce' ); ?>
					<script>
						jQuery(document).ready(function($) {
							var lastId = 0;
							var limit = <?php echo absint( self::BATCH_SIZE ); ?>;
							var totalMigrated = 0;

							$('#crp_migrate_meta').on('click', function() {
								$(this).prop('disabled', true);
								$('#crp-migration-progress').show();
								migrateBatch();
							});

							function migrateBatch() {
								$.ajax({
									url: ajaxurl,
									type: 'POST',
									data: {
										action: 'crp_migrate_meta',
										security: $('#crp_migrate_meta_nonce').val(),
										last_id: lastId,
										limit: limit
									},
									success: function(response) {
										if (response.success) {
											totalMigrated += response.data.migrated;
											$('#crp-migration-status').text(response.data.message);
											if (response.data.last_id !== undefined) {
												lastId = response.data.last_id;
											}
											if (response.data.last_id !== undefined) {
												undoLastId = response.data.last_id;
											}
											if (response.data.complete) {
												$('#crp-migration-bar').css('width', '100%');
												$('#crp_migrate_meta').text('<?php esc_html_e( 'Migration Complete', 'contextual-related-posts' ); ?>').prop('disabled', true);
												setTimeout(function() {
													location.reload();
												}, 2000);
											} else {
												var progress = Math.min((totalMigrated / (totalMigrated + response.data.remaining)) * 100, 100);
												$('#crp-migration-bar').css('width', progress + '%');
												migrateBatch();
											}
										} else {
											$('#crp-migration-status').text('<?php esc_html_e( 'Migration failed. Please try again.', 'contextual-related-posts' ); ?>');
											$('#crp_migrate_meta').prop('disabled', false);
										}
									},
									error: function() {
										$('#crp-migration-status').text('<?php esc_html_e( 'Migration failed. Please try again.', 'contextual-related-posts' ); ?>');
										$('#crp_migrate_meta').prop('disabled', false);
									}
								});
							}
						});
					</script>
				</div>
			</div>
			<?php elseif ( get_option( 'crp_meta_migration_done', false ) ) : ?>
			<div class="postbox">
				<h2><span><?php esc_html_e( 'Migration Status', 'contextual-related-posts' ); ?></span></h2>
				<div class="inside">
					<p class="description">
					<?php esc_html_e( 'Migration completed. You can undo if needed.', 'contextual-related-posts' ); ?>
					<?php if ( $undo_count > 0 ) : ?>
						<br />
						<?php
						$count = number_format_i18n( $undo_count );
						/* translators: %s: number of posts */
						printf( esc_html__( '%s posts can be undone.', 'contextual-related-posts' ), esc_html( $count ) );
						?>
					<?php endif; ?>
					</p>
					<p>
						<button type="button" id="crp_undo_migration" class="button button-secondary">
							<?php esc_html_e( 'Undo Migration', 'contextual-related-posts' ); ?>
						</button>
					</p>
					<div id="crp-undo-progress" style="display: none;">
						<p><?php esc_html_e( 'Undo in progress...', 'contextual-related-posts' ); ?></p>
						<div class="progress-bar" style="width: 100%; background-color: #f1f1f1; border: 1px solid #ddd; height: 20px; margin: 10px 0;">
							<div id="crp-undo-bar" style="height: 100%; background-color: #dc3232; width: 0%;"></div>
						</div>
						<p id="crp-undo-status"></p>
					</div>
					<?php wp_nonce_field( 'crp_undo_migrate_meta_nonce', 'crp_undo_migrate_meta_nonce' ); ?>
					<script>
						jQuery(document).ready(function($) {
							var undoLastId = 0;
							var undoLimit = <?php echo absint( self::BATCH_SIZE ); ?>;
							var totalUndone = 0;

							$('#crp_undo_migration').on('click', function() {
								if (!confirm('<?php esc_html_e( 'Are you sure you want to undo the migration? This will revert to the old array storage.', 'contextual-related-posts' ); ?>')) {
									return;
								}
								$(this).prop('disabled', true);
								$('#crp-undo-progress').show();
								undoBatch();
							});

							function undoBatch() {
								$.ajax({
									url: ajaxurl,
									type: 'POST',
									data: {
										action: 'crp_undo_migrate_meta',
										security: $('#crp_undo_migrate_meta_nonce').val(),
										last_id: undoLastId,
										limit: undoLimit
									},
									success: function(response) {
										if (response.success) {
											totalUndone += response.data.undone;
											$('#crp-undo-status').text(response.data.message);
											if (response.data.complete) {
												$('#crp-undo-bar').css('width', '100%');
												$('#crp_undo_migration').text('<?php esc_html_e( 'Undo Complete', 'contextual-related-posts' ); ?>').prop('disabled', true);
												setTimeout(function() {
													location.reload();
												}, 2000);
											} else {
												var progress = Math.min((totalUndone / (totalUndone + response.data.remaining)) * 100, 100);
												$('#crp-undo-bar').css('width', progress + '%');
												undoBatch();
											}
										} else {
											$('#crp-undo-status').text('<?php esc_html_e( 'Undo failed. Please try again.', 'contextual-related-posts' ); ?>');
											$('#crp_undo_migration').prop('disabled', false);
										}
									},
									error: function() {
										$('#crp-undo-status').text('<?php esc_html_e( 'Undo failed. Please try again.', 'contextual-related-posts' ); ?>');
										$('#crp_undo_migration').prop('disabled', false);
									}
								});
							}
						});
					</script>
				</div>
			</div>
			<?php endif; ?>

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
			<?php include_once 'sidebar.php'; ?>
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
			wp_enqueue_style( 'wp-spinner' );
			wp_localize_script(
				'crp-admin-js',
				'crp_admin_data',
				array(
					'security' => wp_create_nonce( 'crp-admin' ),
					'strings'  => array(
						'clear_cache'    => __( 'Clear cache', 'contextual-related-posts' ),
						'clearing_cache' => __( 'Clearing cache', 'contextual-related-posts' ),
					),
				)
			);
		}
	}

	/**
	 * Get the number of posts that need migration.
	 *
	 * @since 4.2.0
	 *
	 * @return int Number of posts with crp_post_meta.
	 */
	public static function get_migration_count() {
		global $wpdb;
		return $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->postmeta} WHERE meta_key = 'crp_post_meta'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
	}

	/**
	 * Get the number of posts that can be undone.
	 *
	 * @since 4.2.0
	 *
	 * @return int Number of distinct post_ids with _crp_ keys excluding cache.
	 */
	public static function get_undo_count() {
		global $wpdb;
		$like       = $wpdb->esc_like( '_crp_' ) . '%';
		$cache_like = $wpdb->esc_like( '_crp_cache' ) . '%';
		return $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(DISTINCT post_id) FROM {$wpdb->postmeta} WHERE meta_key LIKE %s AND meta_key NOT LIKE %s", $like, $cache_like ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
	}

	/**
	 * Migrate meta data from crp_post_meta array to individual _crp_* keys.
	 *
	 * @since 4.2.0
	 */
	public static function migrate_meta_batch() {

		check_ajax_referer( 'crp_migrate_meta_nonce', 'security' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to perform this action.', 'contextual-related-posts' ) );
		}

		$last_id = isset( $_POST['last_id'] ) ? absint( $_POST['last_id'] ) : 0;
		$limit   = isset( $_POST['limit'] ) ? absint( $_POST['limit'] ) : self::BATCH_SIZE;

		$results = Migration_Service::migrate_batch( $last_id, $limit, false );

		wp_send_json_success(
			array(
				'migrated'  => $results['migrated'],
				'remaining' => $results['remaining'],
				'complete'  => $results['complete'],
				'message'   => $results['complete']
					? esc_html__( 'Migration completed successfully!', 'contextual-related-posts' )
					: sprintf(
						/* translators: %d: Number of posts migrated. */
						esc_html__( 'Migrated %d posts. Continuing...', 'contextual-related-posts' ),
						$results['migrated']
					),
			)
		);
	}

	/**
	 * Undo migrate meta data from individual _crp_* keys back to crp_post_meta array.
	 *
	 * @since 4.2.0
	 */
	public static function undo_migrate_meta_batch() {

		check_ajax_referer( 'crp_undo_migrate_meta_nonce', 'security' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to perform this action.', 'contextual-related-posts' ) );
		}

		$offset = isset( $_POST['offset'] ) ? absint( $_POST['offset'] ) : 0;
		$limit  = isset( $_POST['limit'] ) ? absint( $_POST['limit'] ) : self::BATCH_SIZE;

		global $wpdb;

		// Get total count first time.
		static $total_count = null;
		if ( null === $total_count ) {
			$total_count = self::get_undo_count();
		}

		// Get distinct post_ids with _crp_ keys excluding cache.
		$like       = $wpdb->esc_like( '_crp_' ) . '%';
		$cache_like = $wpdb->esc_like( '_crp_cache' ) . '%';
		$post_ids   = $wpdb->get_col( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->prepare(
				"SELECT DISTINCT post_id FROM {$wpdb->postmeta} WHERE meta_key LIKE %s AND meta_key NOT LIKE %s LIMIT %d OFFSET %d",
				$like,
				$cache_like,
				$limit,
				$offset
			)
		);

		$undone_count = 0;

		foreach ( $post_ids as $post_id ) {
			$meta_data = array();
			$meta_keys = get_post_meta( $post_id );

			foreach ( $meta_keys as $key => $value ) {
				if ( strpos( $key, '_crp_' ) === 0 && strpos( $key, '_crp_cache' ) !== 0 ) {
					$meta_data[ substr( $key, 5 ) ] = $value[0]; // Remove _crp_ prefix.
					delete_post_meta( $post_id, $key );
				}
			}

			if ( ! empty( $meta_data ) ) {
				update_post_meta( $post_id, 'crp_post_meta', $meta_data );
				++$undone_count;
			}
		}

		$remaining = $total_count - $offset - $undone_count;

		if ( $remaining <= 0 ) {
			delete_option( 'crp_meta_migration_done' ); // Non-autoload.
			wp_send_json_success(
				array(
					'undone'    => $undone_count,
					'remaining' => 0,
					'complete'  => true,
					'message'   => esc_html__( 'Undo completed successfully!', 'contextual-related-posts' ),
				)
			);
		} else {
			wp_send_json_success(
				array(
					'undone'    => $undone_count,
					'remaining' => $remaining,
					'complete'  => false,
					'message'   => sprintf(
						/* translators: %d: Number of posts undone. */
						esc_html__( 'Undone %d posts. Continuing...', 'contextual-related-posts' ),
						$undone_count
					),
				)
			);
		}
	}

	/**
	 * Rollback meta data from individual _crp_* keys back to crp_post_meta array.
	 *
	 * @since 4.2.0
	 */
	public static function rollback_meta_batch() {

		check_ajax_referer( 'crp_rollback_meta_nonce', 'security' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to perform this action.', 'contextual-related-posts' ) );
		}

		$last_id = isset( $_POST['last_id'] ) ? absint( $_POST['last_id'] ) : 0;
		$limit   = isset( $_POST['limit'] ) ? absint( $_POST['limit'] ) : self::BATCH_SIZE;

		$results = Migration_Service::rollback_batch( $last_id, $limit, false );

		wp_send_json_success(
			array(
				'rolled_back' => $results['rolled_back'],
				'remaining'   => $results['remaining'],
				'complete'    => $results['complete'],
				'message'     => $results['complete']
					? esc_html__( 'Rollback completed successfully!', 'contextual-related-posts' )
					: sprintf(
						/* translators: %d: Number of posts rolled back. */
						esc_html__( 'Rolled back %d posts. Continuing...', 'contextual-related-posts' ),
						$results['rolled_back']
					),
			)
		);
	}
}

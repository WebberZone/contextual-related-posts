<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link  https://webberzone.com
 * @since 2.6.0
 *
 * @package    Contextual Related Posts
 * @subpackage Admin/Tools
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Render the tools settings page.
 *
 * @since 2.6.0
 *
 * @return void
 */
function crp_tools_page() {

	/* Delete old settings */
	if ( ( isset( $_POST['crp_delete_old_settings'] ) ) && ( check_admin_referer( 'crp-tools-settings' ) ) ) {
		$old_settings = get_option( 'ald_crp_settings' );

		if ( empty( $old_settings ) ) {
			add_settings_error( 'crp-notices', '', esc_html__( 'Old settings key does not exist', 'autoclose' ), 'error' );
		} else {
			delete_option( 'ald_crp_settings' );
			add_settings_error( 'crp-notices', '', esc_html__( 'Old settings key has been deleted', 'autoclose' ), 'updated' );
		}
	}

	/* Recreate indices */
	if ( ( isset( $_POST['crp_recreate_indices'] ) ) && ( check_admin_referer( 'crp-tools-settings' ) ) ) {
		crp_delete_index();
		crp_create_index();
		add_settings_error( 'crp-notices', '', esc_html__( 'Indices have been recreated', 'contextual-related-posts' ), 'updated' );
	}

	/* Message for successful file import */
	if ( isset( $_GET['settings_import'] ) && 'success' === $_GET['settings_import'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		add_settings_error( 'crp-notices', '', esc_html__( 'Settings have been imported successfully', 'contextual-related-posts' ), 'updated' );
	}

	ob_start();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Contextual Related Posts Tools', 'contextual-related-posts' ); ?></h1>

		<p>
			<a class="crp_button" href="<?php echo admin_url( 'options-general.php?page=crp_options_page' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>">
				<?php esc_html_e( 'Visit the Settings page', 'autoclose' ); ?>
			</a>
		<p>

		<?php settings_errors(); ?>

		<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
		<div id="post-body-content">

			<form method="post" >

				<h2 style="padding-left:0px"><?php esc_html_e( 'Clear cache', 'contextual-related-posts' ); ?></h2>
				<p>
					<input type="button" name="cache_clear" id="cache_clear"  value="<?php esc_attr_e( 'Clear cache', 'contextual-related-posts' ); ?>" class="button button-secondary" onclick="return crpClearCache();" />
				</p>
				<p class="description">
					<?php esc_html_e( 'Clear the Contextual Related Posts cache. This will also be cleared automatically when you save the settings page.', 'contextual-related-posts' ); ?>
				</p>

				<h2 style="padding-left:0px"><?php esc_html_e( 'Recreate Indices', 'contextual-related-posts' ); ?></h2>
				<p>
					<input name="crp_recreate_indices" type="submit" id="crp_recreate_indices" value="<?php esc_attr_e( 'Recreate Indices', 'contextual-related-posts' ); ?>" class="button button-secondary" />
				</p>
				<p class="description">
					<?php esc_html_e( 'Deletes and recreates the FULLTEXT index in the posts table. If the above function gives an error, then you can run the below code in phpMyAdmin or Adminer. Remember to backup your database first!', 'contextual-related-posts' ); ?>
				</p>
				<p>
					<code style="display:block;"><?php echo crp_recreate_indices_sql(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></code>
				</p>

				<?php wp_nonce_field( 'crp-tools-settings' ); ?>
			</form>

			<form method="post">

				<h2 style="padding-left:0px"><?php esc_html_e( 'Export/Import settings', 'contextual-related-posts' ); ?></h2>
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

			<form method="post">

				<h2 style="padding-left:0px"><?php esc_html_e( 'Other tools', 'contextual-related-posts' ); ?></h2>
				<p>
					<input name="crp_delete_old_settings" type="submit" id="crp_delete_old_settings" value="<?php esc_attr_e( 'Delete old settings', 'contextual-related-posts' ); ?>" class="button button-secondary" onclick="if (!confirm('<?php esc_attr_e( 'This will delete the settings before v2.6.x. Proceed?', 'contextual-related-posts' ); ?>')) return false;" />
				</p>
				<p class="description">
					<?php esc_html_e( 'From v2.6.x, Contextual Related Posts stores the settings in a new key in the database. This will delete the old settings for the current blog. It is recommended that you do this at the earliest after upgrade. However, you should do this only if you are comfortable with the new settings.', 'contextual-related-posts' ); ?>
				</p>

				<?php wp_nonce_field( 'crp-tools-settings' ); ?>
			</form>

		</div><!-- /#post-body-content -->

		<div id="postbox-container-1" class="postbox-container">

			<div id="side-sortables" class="meta-box-sortables ui-sortable">
				<?php include_once CRP_PLUGIN_DIR . 'includes/admin/sidebar.php'; ?>
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
 * Retrieves the SQL code to recreate the PRIMARY KEY.
 *
 * @since 2.6.0
 */
function crp_recreate_indices_sql() {
	global $wpdb;

	$sql  = "ALTER TABLE {$wpdb->posts} DROP INDEX crp_related;";
	$sql .= '<br />';
	$sql .= "ALTER TABLE {$wpdb->posts} DROP INDEX crp_related_title;";
	$sql .= '<br />';
	$sql .= "ALTER TABLE {$wpdb->posts} ADD FULLTEXT crp_related (post_title, post_content);";
	$sql .= '<br />';
	$sql .= "ALTER TABLE {$wpdb->posts} ADD FULLTEXT crp_related_title (post_title);";

	/**
	 * Filters the SQL code to recreate the PRIMARY KEY.
	 *
	 * @since 2.6.0
	 * @param string $sql SQL code to recreate PRIMARY KEY.
	 */
	return apply_filters( 'crp_recreate_indices_sql', $sql );
}

/**
 * Process a settings export that generates a .json file of the shop settings
 *
 * @since 2.9.0
 */
function crp_process_settings_export() {

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
add_action( 'admin_init', 'crp_process_settings_export' );

/**
 * Process a settings import from a json file
 *
 * @since 2.9.0
 */
function crp_process_settings_import() {

	if ( empty( $_POST['crp_action'] ) || 'import_settings' !== $_POST['crp_action'] ) {
		return;
	}

	if ( ! isset( $_POST['crp_import_settings_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['crp_import_settings_nonce'] ), 'crp_import_settings_nonce' ) ) {
		return;
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$filename  = 'import_settings_file';
	$extension = isset( $_FILES[ $filename ]['name'] ) ? end( explode( '.', sanitize_file_name( wp_unslash( $_FILES[ $filename ]['name'] ) ) ) ) : '';

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
			admin_url( 'admin.php' )
		)
	);
	exit;

}
add_action( 'admin_init', 'crp_process_settings_import', 9 );


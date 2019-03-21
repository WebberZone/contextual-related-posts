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

	ob_start();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Contextual Related Posts Tools', 'contextual-related-posts' ); ?></h1>

		<p>
			<a href="<?php echo admin_url( 'options-general.php?page=crp_options_page' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>">
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
					<input type="button" name="cache_clear" id="cache_clear"  value="<?php esc_attr_e( 'Clear cache', 'contextual-related-posts' ); ?>" class="button button-secondary" onclick="return clearCache();" />
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
	$sql .= "ALTER TABLE {$wpdb->posts} DROP INDEX crp_related_content;";
	$sql .= '<br />';
	$sql .= "ALTER TABLE {$wpdb->posts} ADD FULLTEXT crp_related (post_title, post_content);";
	$sql .= '<br />';
	$sql .= "ALTER TABLE {$wpdb->posts} ADD FULLTEXT crp_related_title (post_title);";
	$sql .= '<br />';
	$sql .= "ALTER TABLE {$wpdb->posts} ADD FULLTEXT crp_related_content (post_content);";

	/**
	 * Filters the SQL code to recreate the PRIMARY KEY.
	 *
	 * @since 2.6.0
	 * @param string $sql SQL code to recreate PRIMARY KEY.
	 */
	return apply_filters( 'crp_recreate_indices_sql', $sql );
}

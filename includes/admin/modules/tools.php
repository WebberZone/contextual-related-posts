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

	/* Truncate overall posts table */
	if ( ( isset( $_POST['crp_recreate_primary_key'] ) ) && ( check_admin_referer( 'crp-tools-settings' ) ) ) {
		crp_recreate_primary_key();
		add_settings_error( 'crp-notices', '', esc_html__( 'Primary Key has been recreated', 'contextual-related-posts' ), 'error' );
	}

	/* Truncate overall posts table */
	if ( ( isset( $_POST['crp_trunc_all'] ) ) && ( check_admin_referer( 'crp-tools-settings' ) ) ) {
		crp_trunc_count( false );
		add_settings_error( 'crp-notices', '', esc_html__( 'Contextual Related Posts reset', 'contextual-related-posts' ), 'error' );
	}

	/* Truncate daily posts table */
	if ( ( isset( $_POST['crp_trunc_daily'] ) ) && ( check_admin_referer( 'crp-tools-settings' ) ) ) {
		crp_trunc_count( true );
		add_settings_error( 'crp-notices', '', esc_html__( 'Contextual Related Posts daily related posts reset', 'contextual-related-posts' ), 'error' );
	}

	/* Delete old settings */
	if ( ( isset( $_POST['crp_delete_old_settings'] ) ) && ( check_admin_referer( 'crp-tools-settings' ) ) ) {
		delete_option( 'ald_crp_settings' );
		add_settings_error( 'crp-notices', '', esc_html__( 'Old settings key has been deleted', 'contextual-related-posts' ), 'error' );
	}

	/* Clean duplicates */
	if ( ( isset( $_POST['crp_clean_duplicates'] ) ) && ( check_admin_referer( 'crp-tools-settings' ) ) ) {
		crp_clean_duplicates( true );
		crp_clean_duplicates( false );
		add_settings_error( 'crp-notices', '', esc_html__( 'Duplicate rows cleaned from the tables', 'contextual-related-posts' ), 'error' );
	}

	/* Merge blog IDs */
	if ( ( isset( $_POST['crp_merge_blogids'] ) ) && ( check_admin_referer( 'crp-tools-settings' ) ) ) {
		crp_merge_blogids( true );
		crp_merge_blogids( false );
		add_settings_error( 'crp-notices', '', esc_html__( 'Post counts across blog IDs 0 and 1 have been merged', 'contextual-related-posts' ), 'error' );
	}

	ob_start();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Contextual Related Posts Tools', 'contextual-related-posts' ); ?></h1>

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

				<h2 style="padding-left:0px"><?php esc_html_e( 'Recreate Primary Key', 'contextual-related-posts' ); ?></h2>
				<p>
					<input name="crp_recreate_primary_key" type="submit" id="crp_recreate_primary_key" value="<?php esc_attr_e( 'Recreate Primary Key', 'contextual-related-posts' ); ?>" class="button button-secondary" />
				</p>
				<p class="description">
					<?php esc_html_e( 'Deletes and reinitializes the primary key in the database tables. If the above function gives an error, then you can run the below code in phpMyAdmin or Adminer. Remember to backup your database first!', 'contextual-related-posts' ); ?>
				</p>
				<p>
					<code style="display:block;"><?php echo crp_recreate_primary_key_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></code>
				</p>

				<h2 style="padding-left:0px"><?php esc_html_e( 'Reset database', 'contextual-related-posts' ); ?></h2>
				<p>
					<input name="crp_trunc_all" type="submit" id="crp_trunc_all" value="<?php esc_attr_e( 'Reset related posts Network-wide', 'contextual-related-posts' ); ?>" class="button button-secondary" style="color:#f00" onclick="if (!confirm('<?php esc_attr_e( 'Are you sure you want to reset the related posts?', 'contextual-related-posts' ); ?>')) return false;" />
					<input name="crp_trunc_daily" type="submit" id="crp_trunc_daily" value="<?php esc_attr_e( 'Reset Daily related posts Network-wide', 'contextual-related-posts' ); ?>" class="button button-secondary" style="color:#f00" onclick="if (!confirm('<?php esc_attr_e( 'Are you sure you want to reset the daily related posts?', 'contextual-related-posts' ); ?>')) return false;" />
				</p>
				<p class="description">
					<?php esc_html_e( 'This will reset the Contextual Related Posts tables. If you are running Contextual Related Posts on multisite then it will delete the related posts across the entire network. This cannot be reversed. Make sure that your database has been backed up before proceeding', 'contextual-related-posts' ); ?>
				</p>

				<h2 style="padding-left:0px"><?php esc_html_e( 'Other tools', 'contextual-related-posts' ); ?></h2>
				<p>
					<input name="crp_delete_old_settings" type="submit" id="crp_delete_old_settings" value="<?php esc_attr_e( 'Delete old settings', 'contextual-related-posts' ); ?>" class="button button-secondary" onclick="if (!confirm('<?php esc_attr_e( 'This will delete the settings before v2.5.x. Proceed?', 'contextual-related-posts' ); ?>')) return false;" />
				</p>
				<p class="description">
					<?php esc_html_e( 'From v2.5.x, Contextual Related Posts stores the settings in a new key in the database. This will delete the old settings for the current blog. It is recommended that you do this at the earliest after upgrade. However, you should do this only if you are comfortable with the new settings.', 'contextual-related-posts' ); ?>
				</p>

				<p>
					<input name="crp_merge_blogids" type="submit" id="crp_merge_blogids" value="<?php esc_attr_e( 'Merge blog ID 0 and 1 post counts', 'contextual-related-posts' ); ?>" class="button button-secondary" onclick="if (!confirm('<?php esc_attr_e( 'This will merge post counts for blog IDs 0 and 1. Proceed?', 'contextual-related-posts' ); ?>')) return false;" />
				</p>
				<p class="description">
					<?php esc_html_e( 'This will merge post counts for posts with table entries of 0 and 1', 'contextual-related-posts' ); ?>
				</p>

				<p>
					<input name="crp_clean_duplicates" type="submit" id="crp_clean_duplicates" value="<?php esc_attr_e( 'Merge duplicates across blog IDs', 'contextual-related-posts' ); ?>" class="button button-secondary" onclick="if (!confirm('<?php esc_attr_e( 'This will delete the duplicate entries in the tables. Proceed?', 'contextual-related-posts' ); ?>')) return false;" />
				</p>
				<p class="description">
					<?php esc_html_e( 'In older versions, the plugin created entries with duplicate post IDs. Clicking the button below will merge these duplicate IDs', 'contextual-related-posts' ); ?>
				</p>
				<?php wp_nonce_field( 'crp-tools-settings' ); ?>
			</form>

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
 * Function to delete all duplicate rows in the posts table.
 *
 * @since 2.6.0
 *
 * @param   bool $daily  Daily flag.
 */
function crp_clean_duplicates( $daily = false ) {
	global $wpdb;

	$table_name = $wpdb->base_prefix . 'top_ten';
	if ( $daily ) {
		$table_name .= '_daily';
	}

	$wpdb->query( 'CREATE TEMPORARY TABLE ' . $table_name . '_temp AS SELECT * FROM ' . $table_name . ' GROUP BY postnumber' ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.SchemaChange
	$wpdb->query( "TRUNCATE TABLE $table_name" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$wpdb->query( 'INSERT INTO ' . $table_name . ' SELECT * FROM ' . $table_name . '_temp' ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
}


/**
 * Function to merge counts with post numbers of blog ID 0 and 1 respectively.
 *
 * @since 2.6.0
 *
 * @param   bool $daily  Daily flag.
 */
function crp_merge_blogids( $daily = false ) {
	global $wpdb;

	$table_name = $wpdb->base_prefix . 'top_ten';
	if ( $daily ) {
		$table_name .= '_daily';
	}

	if ( $daily ) {
		$wpdb->query( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			"
            INSERT INTO `$table_name` (postnumber, cntaccess, dp_date, blog_id) (
                SELECT
                    postnumber,
                    SUM(cntaccess) as sumCount,
                    dp_date,
                    1
                FROM `$table_name`
                WHERE blog_ID IN (0,1)
                GROUP BY postnumber, dp_date
            ) ON DUPLICATE KEY UPDATE cntaccess = VALUES(cntaccess);
        "
		);
	} else {
		$wpdb->query( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			"
            INSERT INTO `$table_name` (postnumber, cntaccess, blog_id) (
                SELECT
                    postnumber,
                    SUM(cntaccess) as sumCount,
                    1
                FROM `$table_name`
                WHERE blog_ID IN (0,1)
                GROUP BY postnumber
            ) ON DUPLICATE KEY UPDATE cntaccess = VALUES(cntaccess);
        "
		);
	}

	$wpdb->query( "DELETE FROM $table_name WHERE blog_id = 0" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
}

/**
 * Function to delete and create the primary keys in the database table.
 *
 * @since 2.6.0
 */
function crp_recreate_primary_key() {
	global $wpdb;

	$table_name       = $wpdb->base_prefix . 'top_ten';
	$table_name_daily = $wpdb->base_prefix . 'top_ten_daily';

	$wpdb->hide_errors();

	if ( $wpdb->query( $wpdb->prepare( "SHOW INDEXES FROM {$table_name} WHERE Key_name = %s", 'PRIMARY' ) ) ) { // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$wpdb->query( 'ALTER TABLE ' . $table_name . ' DROP PRIMARY KEY ' ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.SchemaChange
	}
	if ( $wpdb->query( $wpdb->prepare( "SHOW INDEXES FROM {$table_name_daily} WHERE Key_name = %s", 'PRIMARY' ) ) ) { // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$wpdb->query( 'ALTER TABLE ' . $table_name_daily . ' DROP PRIMARY KEY ' ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.SchemaChange
	}

	$wpdb->query( 'ALTER TABLE ' . $table_name . ' ADD PRIMARY KEY(postnumber, blog_id) ' ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.SchemaChange
	$wpdb->query( 'ALTER TABLE ' . $table_name_daily . ' ADD PRIMARY KEY(postnumber, dp_date, blog_id) ' ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.SchemaChange

	$wpdb->show_errors();
}

/**
 * Retrieves the SQL code to recreate the PRIMARY KEY.
 *
 * @since 2.6.0
 */
function crp_recreate_primary_key_html() {
	global $wpdb;

	$table_name       = $wpdb->base_prefix . 'top_ten';
	$table_name_daily = $wpdb->base_prefix . 'top_ten_daily';

	$sql  = 'ALTER TABLE ' . $table_name . ' DROP PRIMARY KEY; ';
	$sql .= '<br />';
	$sql .= 'ALTER TABLE ' . $table_name_daily . ' DROP PRIMARY KEY; ';
	$sql .= '<br />';
	$sql .= 'ALTER TABLE ' . $table_name . ' ADD PRIMARY KEY(postnumber, blog_id); ';
	$sql .= '<br />';
	$sql .= 'ALTER TABLE ' . $table_name_daily . ' ADD PRIMARY KEY(postnumber, dp_date, blog_id); ';

	/**
	 * Filters the SQL code to recreate the PRIMARY KEY.
	 *
	 * @since 2.6.0
	 * @param string $sql SQL code to recreate PRIMARY KEY.
	 */
	return apply_filters( 'crp_recreate_primary_key_html', $sql );
}

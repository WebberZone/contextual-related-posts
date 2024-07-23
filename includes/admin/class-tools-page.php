<?php
/**
 * Generates the Tools page.
 *
 * @since 3.5.0
 *
 * @package Contextual_Related_Posts
 */

namespace WebberZone\Contextual_Related_Posts\Admin;

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
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'process_settings_export' ) );
		add_action( 'admin_init', array( $this, 'process_settings_import' ), 9 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
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
			<a class="crp_button crp_button_blue" href="<?php echo esc_url( admin_url( 'options-general.php?page=crp_options_page' ) ); ?>">
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
				<?php esc_html_e( 'Clear the Contextual Related Posts cache. This might take a while if you have a lot of posts.', 'contextual-related-posts' ); ?>
				</p>

				<h2 style="padding-left:0px"><?php esc_html_e( 'Recreate Indices', 'contextual-related-posts' ); ?></h2>
				<p>
					<input name="crp_recreate_indices" type="submit" id="crp_recreate_indices" value="<?php esc_attr_e( 'Recreate Indices', 'contextual-related-posts' ); ?>" class="button button-secondary" />
				</p>
				<p class="description">
				<?php esc_html_e( 'Deletes and recreates the FULLTEXT index in the posts table. If the above function gives an error, then you can run the below code in phpMyAdmin or Adminer. Remember to backup your database first!', 'contextual-related-posts' ); ?>
				</p>
				<p>
					<code style="display:block;"><?php echo self::recreate_indices_sql(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></code>
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
	 * Retrieves the SQL code to recreate the PRIMARY KEY.
	 *
	 * @since 3.5.0
	 */
	public static function recreate_indices_sql() {
		global $wpdb;

		$sql = array(
			"ALTER TABLE {$wpdb->posts} DROP INDEX crp_related",
			"ALTER TABLE {$wpdb->posts} ADD FULLTEXT crp_related (post_title, post_content)",
			"ALTER TABLE {$wpdb->posts} DROP INDEX crp_related_title",
			"ALTER TABLE {$wpdb->posts} ADD FULLTEXT crp_related_title (post_title)",
		);

		/**
		 * Filter the SQL code to recreate the Fulltext indices.
		 *
		 * @since 3.5.0
		 *
		 * @param array $sql Array of SQL queries.
		 */
		$sql = apply_filters( 'crp_recreate_indices_sql', $sql );

		return implode( '<br />', $sql );
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
	 */
	public function admin_enqueue_scripts() {
		$file_prefix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		$screen = get_current_screen();

		if ( $this->parent_id === $screen->id ) {
			wp_enqueue_script( 'crp-admin-js' );
			wp_enqueue_style( 'crp-admin-ui-css' );
			wp_localize_script(
				'crp-admin-js',
				'crp_admin_data',
				array(
					'security' => wp_create_nonce( 'crp-admin' ),
				)
			);
		}
	}
}
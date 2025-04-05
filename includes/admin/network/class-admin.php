<?php
/**
 * Register Settings.
 *
 * @since 4.0.0
 *
 * @package WebberZone\Contextual_Related_Posts\Admin\Network
 */

namespace WebberZone\Contextual_Related_Posts\Admin\Network;

use WebberZone\Contextual_Related_Posts\Main;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class to register the settings.
 *
 * @since 4.0.0
 */
class Admin {

	/**
	 * Parent ID.
	 *
	 * @var string
	 */
	public $parent_id;

	/**
	 * Tools page.
	 *
	 * @since 4.0.0
	 *
	 * @var Tools_Page
	 */
	public Tools_Page $tools_page;

	/**
	 * Main constructor class.
	 *
	 * @since 4.0.0
	 */
	public function __construct() {
		$this->tools_page = new Tools_Page();

		$this->hooks();
	}

	/**
	 * Run the hooks.
	 *
	 * @since 4.0.0
	 */
	public function hooks() {
		add_action( 'network_admin_menu', array( $this, 'network_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'admin_init', array( $this, 'process_network_settings' ) );
	}

	/**
	 * Process network settings form submission.
	 *
	 * @since 4.0.0
	 */
	public function process_network_settings() {
		if ( ! isset( $_POST['crp_network_settings_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['crp_network_settings_nonce'] ), 'crp_network_settings' ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_network_options' ) ) {
			return;
		}

		$ecsi_setting = isset( $_POST['crp_ecsi_setting'] ) ? sanitize_key( $_POST['crp_ecsi_setting'] ) : 'individual';

		// Get all sites.
		$sites = get_sites();

		if ( 'individual' !== $ecsi_setting ) {
			$enable_ecsi = 'enable' === $ecsi_setting;

			foreach ( $sites as $site ) {
				switch_to_blog( (int) $site->blog_id );
				crp_update_option( 'use_custom_tables', $enable_ecsi );
				restore_current_blog();
			}
		}

		add_settings_error(
			'crp_network_settings',
			'crp_network_settings_updated',
			__( 'Network settings updated successfully.', 'contextual-related-posts' ),
			'success'
		);
	}

	/**
	 * Add the network admin menu.
	 *
	 * @since 4.0.0
	 */
	public function network_admin_menu() {
		$this->parent_id = add_menu_page(
			esc_html__( 'Contextual Related Posts Multisite Dashboard', 'contextual-related-posts' ),
			esc_html__( 'Contextual Related Posts', 'contextual-related-posts' ),
			'manage_network_options',
			'crp_options_page',
			array( $this, 'render_page' ),
			'dashicons-list-view'
		);

		add_submenu_page(
			'crp_options_page',
			esc_html__( 'Contextual Related Posts Multisite Settings', 'contextual-related-posts' ),
			esc_html__( 'Settings', 'contextual-related-posts' ),
			'manage_network_options',
			'crp_options_page',
			array( $this, 'render_page' )
		);
	}

	/**
	 * Render the page.
	 *
	 * @since 4.0.0
	 */
	public function render_page() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Contextual Related Posts Multisite Settings', 'contextual-related-posts' ); ?></h1>
			<p><?php esc_html_e( 'This page allows you to configure the settings for Contextual Related Posts on your multisite network.', 'contextual-related-posts' ); ?></p>

			<?php settings_errors(); ?>

			<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
			<div id="post-body-content">
				<form method="post" action="">
					<?php wp_nonce_field( 'crp_network_settings', 'crp_network_settings_nonce' ); ?>
					
					<div class="postbox">
						<h3 class="hndle"><?php esc_html_e( 'Performance Settings', 'contextual-related-posts' ); ?></h3>
						<div class="inside">
							<table class="form-table">
								<tr>
									<th scope="row"><?php esc_html_e( 'ECSI Settings', 'contextual-related-posts' ); ?></th>
									<td>
										<fieldset>
											<legend class="screen-reader-text">
												<?php esc_html_e( 'ECSI Settings', 'contextual-related-posts' ); ?>
											</legend>
											<p>
												<label>
													<input type="radio" name="crp_ecsi_setting" value="enable" />
													<?php esc_html_e( 'Enable Enhanced Content Search Index on all sites', 'contextual-related-posts' ); ?>
												</label>
											</p>
											<p>
												<label>
													<input type="radio" name="crp_ecsi_setting" value="disable" />
													<?php esc_html_e( 'Disable Enhanced Content Search Index on all sites', 'contextual-related-posts' ); ?>
												</label>
											</p>
											<p>
												<label>
													<input type="radio" name="crp_ecsi_setting" value="individual" checked="checked" />
													<?php esc_html_e( 'Let each site control its own ECSI setting', 'contextual-related-posts' ); ?>
												</label>
											</p>
											<p class="description">
												<?php esc_html_e( 'Choose how to manage Enhanced Content Search Index (ECSI) across your network. Sites will need to reindex their content after enabling this option.', 'contextual-related-posts' ); ?>
											</p>
										</fieldset>
									</td>
								</tr>
							</table>
						</div>
					</div>

					<p class="submit">
						<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_attr_e( 'Save Changes', 'contextual-related-posts' ); ?>" />
					</p>
				</form>

				<?php
				/**
				 * Action hook to add additional settings page content.
				 *
				 * @since 4.0.0
				 */
				do_action( 'crp_network_admin_settings_page_content' );
				?>

			</div><!-- /#post-body-content -->

			<div id="postbox-container-1" class="postbox-container">

				<div id="side-sortables" class="meta-box-sortables ui-sortable">
				<?php \WebberZone\Contextual_Related_Posts\Admin\Admin::display_admin_sidebar(); ?>
				</div><!-- /#side-sortables -->

			</div><!-- /#postbox-container-1 -->
			</div><!-- /#post-body -->
			<br class="clear" />
			</div><!-- /#poststuff -->
		</div>
		<?php
	}

	/**
	 * Enqueue scripts and styles.
	 *
	 * @since 4.0.0
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
					'security' => wp_create_nonce( 'crp-admin' ),
				)
			);
		}
	}
}

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
			<?php do_action( 'crp_network_admin_settings_page_content_header' ); ?>

			<p><?php esc_html_e( 'This page allows you to configure the settings for Contextual Related Posts on your multisite network.', 'contextual-related-posts' ); ?></p>

			<?php settings_errors(); ?>

			<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
			<div id="post-body-content">
				<?php
					\WebberZone\Contextual_Related_Posts\Admin\Admin::pro_upgrade_banner(
						false,
						sprintf(
							/* translators: 1: link to Network Plugins page, 2: link to account page */
							__( 'If you are running Contextual Related Posts Pro and see the upgrade banner instead of the settings, you may need to activate your license. Go to the %1$s, locate Contextual Related Posts Pro, and activate your license from there. View your %2$s to check the status of your license after activation.', 'contextual-related-posts' ),
							'<a href="' . esc_url( network_admin_url( 'plugins.php' ) ) . '" target="_blank">' . esc_html__( 'Network Plugins page', 'contextual-related-posts' ) . '</a>',
							'<a href="' . esc_url( \WebberZone\Contextual_Related_Posts\crp_freemius()->get_account_url() ) . '" target="_blank">' . esc_html__( 'account page', 'contextual-related-posts' ) . '</a>'
						)
					);
				?>

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
					'security'       => wp_create_nonce( 'crp-admin' ),
					'clear_cache'    => __( 'Clear cache', 'contextual-related-posts' ),
					'clearing_cache' => __( 'Clearing cache', 'contextual-related-posts' ),
				)
			);
		}
	}
}

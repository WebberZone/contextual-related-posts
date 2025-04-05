<?php
/**
 * Generates the Tools page for the network.
 *
 * @since 4.0.0
 *
 * @package Contextual_Related_Posts
 */

namespace WebberZone\Contextual_Related_Posts\Admin\Network;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Tools page class.
 *
 * @since 4.0.0
 */
class Tools_Page {

	/**
	 * Parent Menu ID.
	 *
	 * @since 4.0.0
	 *
	 * @var string Parent Menu ID.
	 */
	public $parent_id;

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'network_admin_menu', array( $this, 'network_admin_menu' ), 11 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}

	/**
	 * Network Admin Menu.
	 *
	 * @since 4.0.0
	 */
	public function network_admin_menu() {
		$this->parent_id = add_submenu_page(
			'crp_options_page',
			esc_html__( 'Contextual Related Posts Multisite Tools', 'contextual-related-posts' ),
			esc_html__( 'Tools', 'contextual-related-posts' ),
			'manage_network_options',
			'crp_tools_page',
			array( $this, 'render_page' )
		);
	}

	/**
	 * Render the tools settings page.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public static function render_page() {

		ob_start();
		?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Contextual Related Posts Multisite Tools', 'contextual-related-posts' ); ?></h1>
		<?php do_action( 'crp_tools_network_page_header' ); ?>

		<?php settings_errors(); ?>

		<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
		<div id="post-body-content">

			<?php
			/**
			 * Action hook to add additional tools page content.
			 *
			 * @since 4.0.0
			 */
			do_action( 'crp_network_admin_tools_page_content' );
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

	</div><!-- /.wrap -->

		<?php
		echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
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

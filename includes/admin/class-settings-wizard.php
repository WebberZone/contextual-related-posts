<?php
/**
 * Settings Wizard for Contextual Related Posts.
 *
 * Provides a guided setup experience for new users.
 *
 * @since 4.1.0
 *
 * @package WebberZone\Contextual_Related_Posts
 */

namespace WebberZone\Contextual_Related_Posts\Admin;

use WebberZone\Contextual_Related_Posts\Util\Hook_Registry;
use WebberZone\Contextual_Related_Posts\Admin\Settings\Settings_Wizard_API;
use WebberZone\Contextual_Related_Posts\Admin\Settings;
use function WebberZone\Contextual_Related_Posts\wz_crp;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Settings Wizard class for Contextual Related Posts.
 *
 * @since 4.1.0
 */
class Settings_Wizard extends Settings_Wizard_API {

	/**
	 * Main constructor class.
	 *
	 * @since 4.1.0
	 */
	public function __construct() {
		$settings_key = 'crp_settings';
		$prefix       = 'crp';

		$args = array(
			'steps'               => $this->get_wizard_steps(),
			'translation_strings' => $this->get_translation_strings(),
			'page_slug'           => 'crp_wizard',
			'menu_args'           => array(
				'parent'     => 'crp_options_page',
				'capability' => 'manage_options',
			),
		);

		parent::__construct( $settings_key, $prefix, $args );

		$this->additional_hooks();
	}

	/**
	 * Additional hooks specific to Contextual Related Posts.
	 *
	 * @since 4.1.0
	 */
	protected function additional_hooks() {
		Hook_Registry::add_action( 'crp_activate', array( $this, 'trigger_wizard_on_activation' ) );
		Hook_Registry::add_action( 'admin_init', array( $this, 'register_wizard_notice' ) );
		Hook_Registry::add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_custom_scripts' ) );
	}

	/**
	 * Get wizard steps configuration.
	 *
	 * @since 4.1.0
	 *
	 * @return array Wizard steps.
	 */
	public function get_wizard_steps() {
		$all_settings_grouped = Settings::get_registered_settings();
		$all_settings         = array();
		foreach ( $all_settings_grouped as $section_settings ) {
			$all_settings = array_merge( $all_settings, $section_settings );
		}

		$basic_settings_keys = array(
			'add_to',
			'limit',
			'post_types',
			'daily_range',
		);

		$display_settings_keys = array(
			'title',
			'crp_styles',
			'show_excerpt',
			'show_author',
			'show_date',
			'post_thumb_op',
			'thumb_size',
		);

		$content_tuning_keys = array(
			'match_content',
			'same_post_type',
			'exclude_post_ids',
			'exclude_cat_slugs',
		);

		$pro_features_keys = array(
			'use_custom_tables',
			'weight_title',
			'weight_content',
			'weight_excerpt',
			'weight_taxonomy_category',
			'weight_taxonomy_post_tag',
			'weight_taxonomy_default',
			'cornerstone_post_ids',
		);

		$steps = array(
			'welcome'         => array(
				'title'       => __( 'Welcome to Contextual Related Posts', 'contextual-related-posts' ),
				'description' => __( 'Thank you for installing Contextual Related Posts! This wizard will help you configure the essential settings to get your related posts working perfectly.', 'contextual-related-posts' ),
				'settings'    => array(),
			),
			'basic_settings'  => array(
				'title'       => __( 'Basic Settings', 'contextual-related-posts' ),
				'description' => __( 'Configure the fundamental behavior of your related posts display.', 'contextual-related-posts' ),
				'settings'    => $this->build_step_settings( $basic_settings_keys, $all_settings ),
			),
			'display_options' => array(
				'title'       => __( 'Display Options', 'contextual-related-posts' ),
				'description' => __( 'Customize how your related posts will look and what information to display.', 'contextual-related-posts' ),
				'settings'    => $this->build_step_settings( $display_settings_keys, $all_settings ),
			),
			'content_tuning'  => array(
				'title'       => __( 'Content Tuning', 'contextual-related-posts' ),
				'description' => __( 'Fine-tune which content is included and how related posts are matched.', 'contextual-related-posts' ),
				'settings'    => $this->build_step_settings( $content_tuning_keys, $all_settings ),
			),
			'pro_settings'    => array(
				'title'       => __( 'Pro Settings', 'contextual-related-posts' ),
				'description' => __( 'Upgrade to Contextual Related Posts Pro to unlock advanced features such as custom tables, advanced weighting, and more. <strong>Take your related posts to the next level!</strong>', 'contextual-related-posts' ) . '<br /><br /><a href="https://webberzone.com/plugins/contextual-related-posts/pro/" target="_blank" class="button button-primary">' . __( 'Learn more about Pro', 'contextual-related-posts' ) . '</a>',
				'settings'    => $this->build_step_settings( $pro_features_keys, $all_settings ),
			),
		);

		// Add custom tables indexing step if custom tables are enabled.
		if ( crp_get_option( 'use_custom_tables', false ) ) {
			$steps['custom_tables_index'] = array(
				'title'       => __( 'Index Custom Tables', 'contextual-related-posts' ),
				'description' => __( 'Custom tables have been enabled. Index your content to improve search performance and enable advanced features.', 'contextual-related-posts' ),
				'settings'    => array(),
				'custom_step' => true, // Flag to indicate this needs custom rendering.
			);
		}

		/**
		 * Filter wizard steps.
		 *
		 * @param array $steps Wizard steps.
		 */
		return apply_filters( 'crp_wizard_steps', $steps );
	}

	/**
	 * Build settings array for a wizard step from keys.
	 *
	 * @since 4.1.0
	 *
	 * @param array $keys Setting keys for this step.
	 * @param array $all_settings All settings array.
	 * @return array
	 */
	protected function build_step_settings( $keys, $all_settings ) {
		$step_settings = array();

		foreach ( $keys as $key ) {
			if ( isset( $all_settings[ $key ] ) ) {
				$step_settings[ $key ] = $all_settings[ $key ];
			}
		}

		return $step_settings;
	}

	/**
	 * Get translation strings for the wizard.
	 *
	 * @since 4.1.0
	 *
	 * @return array Translation strings.
	 */
	public function get_translation_strings() {
		return array(
			'page_title'      => __( 'Contextual Related Posts Setup Wizard', 'contextual-related-posts' ),
			'menu_title'      => __( 'Setup Wizard', 'contextual-related-posts' ),
			'next_step'       => __( 'Next Step', 'contextual-related-posts' ),
			'previous_step'   => __( 'Previous Step', 'contextual-related-posts' ),
			'finish_setup'    => __( 'Finish Setup', 'contextual-related-posts' ),
			'skip_wizard'     => __( 'Skip Wizard', 'contextual-related-posts' ),
			/* translators: %1$d: Current step number, %2$d: Total number of steps */
			'step_of'         => __( 'Step %1$d of %2$d', 'contextual-related-posts' ),
			'wizard_complete' => __( 'Setup Complete!', 'contextual-related-posts' ),
			'setup_complete'  => __( 'Your Contextual Related Posts plugin has been configured successfully. You can now start seeing related posts on your site!', 'contextual-related-posts' ),
			'go_to_settings'  => __( 'Go to Settings', 'contextual-related-posts' ),
		);
	}

	/**
	 * Trigger wizard on plugin activation.
	 *
	 * @since 4.1.0
	 */
	public function trigger_wizard_on_activation() {
		// Set a transient that will trigger the wizard on first admin page visit.
		// This works better than an option because it's temporary and won't persist
		// if the wizard is never accessed.
		set_transient( 'crp_show_wizard_activation_redirect', true, HOUR_IN_SECONDS );

		// Also set an option for more persistent storage in multisite environments.
		update_option( 'crp_show_wizard', true );
	}

	/**
	 * Register the wizard notice with the Admin_Notices_API.
	 *
	 * @since 4.1.0
	 */
	public function register_wizard_notice() {
		// Get the Admin_Notices_API instance.
		$admin_notices_api = wz_crp()->admin->admin_notices_api;
		if ( ! $admin_notices_api ) {
			return;
		}

		$admin_notices_api->register_notice(
			array(
				'id'          => 'crp_wizard_notice',
				'message'     => sprintf(
					'<p>%s</p><p><a href="%s" class="button button-primary">%s</a></p>',
					esc_html__( 'Welcome to Contextual Related Posts! Would you like to run the setup wizard to configure the plugin?', 'contextual-related-posts' ),
					esc_url( admin_url( 'admin.php?page=crp_wizard' ) ),
					esc_html__( 'Run Setup Wizard', 'contextual-related-posts' )
				),
				'type'        => 'info',
				'dismissible' => true,
				'capability'  => 'manage_options',
				'conditions'  => array(
					function () {
						// Only show if wizard is not completed, not dismissed, and activation flag is set.
						// Check both transient and option to ensure it works in multisite environments.
						return ! $this->is_wizard_completed() &&
							! get_option( 'crp_wizard_notice_dismissed', false ) &&
							( get_transient( 'crp_show_wizard_activation_redirect' ) || get_option( 'crp_show_wizard', false ) );
					},
				),
			)
		);
	}

	/**
	 * Get the URL to redirect to after wizard completion.
	 *
	 * @since 4.1.0
	 *
	 * @return string Redirect URL.
	 */
	protected function get_completion_redirect_url() {
		return admin_url( 'options-general.php?page=crp_options_page' );
	}

	/**
	 * Enqueue custom scripts for the wizard.
	 *
	 * @since 4.1.0
	 *
	 * @param string $hook Current admin page hook.
	 */
	public function enqueue_custom_scripts( $hook ) {
		if ( false === strpos( $hook, $this->page_slug ) ) {
			return;
		}

		// Check if we're on the custom tables indexing step.
		$step_config = $this->get_current_step_config();
		if ( ! empty( $step_config['custom_step'] ) ) {
			// Enqueue the reindex script from custom tables admin.
			wp_enqueue_script(
				'crp-reindex',
				WZ_CRP_PLUGIN_URL . 'includes/pro/custom-tables/admin/js/reindex.js',
				array( 'jquery' ),
				WZ_CRP_VERSION,
				true
			);

			// Localize script with necessary data.
			wp_localize_script(
				'crp-reindex',
				'crpReindexSettings',
				array(
					'ajaxurl'        => admin_url( 'admin-ajax.php' ),
					'nonce'          => wp_create_nonce( 'crp_reindex_nonce' ),
					'strings'        => array(
						'starting'    => __( 'Starting reindex process...', 'contextual-related-posts' ),
						'completed'   => __( 'Reindexing complete!', 'contextual-related-posts' ),
						'error'       => __( 'An error occurred during reindexing. Please try again.', 'contextual-related-posts' ),
						'buttonText'  => __( 'Reindex Custom Tables', 'contextual-related-posts' ),
						'clickToStop' => __( 'Reindexing... Click to Stop', 'contextual-related-posts' ),
					),
					'isNetworkAdmin' => is_multisite() && is_network_admin(),
				)
			);
		}
	}

	/**
	 * Override render_wizard_page to handle custom steps.
	 *
	 * @since 4.1.0
	 */
	public function render_wizard_page() {
		$this->current_step = $this->get_current_step();
		$step_config        = $this->get_current_step_config();

		if ( empty( $step_config ) ) {
			$this->render_completion_page();
			return;
		}

		// Check if this is a custom step.
		if ( ! empty( $step_config['custom_step'] ) ) {
			$this->render_custom_tables_step( $step_config );
			return;
		}

		// Use parent method for regular steps.
		parent::render_wizard_page();
	}

	/**
	 * Render the custom tables indexing step.
	 *
	 * @since 4.1.0
	 *
	 * @param array $step_config Step configuration.
	 */
	protected function render_custom_tables_step( $step_config ) {
		?>
		<div class="wrap wizard-wrap">
			<h1><?php echo esc_html( $this->translation_strings['wizard_title'] ); ?></h1>

			<div class="wizard-progress">
				<div class="wizard-progress-bar">
					<div class="wizard-progress-fill" style="width: <?php echo esc_attr( (string) ( ( $this->current_step / $this->total_steps ) * 100 ) ); ?>%;"></div>
				</div>
				<p class="wizard-step-counter">
					<?php
					printf(
						esc_html( $this->translation_strings['step_of'] ),
						esc_html( (string) $this->current_step ),
						esc_html( (string) $this->total_steps )
					);
					?>
				</p>
			</div>

			<div class="wizard-content">
				<div class="wizard-step">
					<h2><?php echo esc_html( $step_config['title'] ?? '' ); ?></h2>
					
					<?php if ( ! empty( $step_config['description'] ) ) : ?>
						<p class="wizard-step-description"><?php echo wp_kses_post( $step_config['description'] ); ?></p>
					<?php endif; ?>

					<form method="post" action="">
						<?php wp_nonce_field( "{$this->prefix}_wizard_nonce", "{$this->prefix}_wizard_nonce" ); ?>
						
						<div class="wizard-fields">
							<?php $this->render_custom_tables_interface(); ?>
						</div>

						<div class="wizard-actions">
							<?php $this->render_wizard_buttons(); ?>
						</div>
					</form>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render the custom tables indexing interface.
	 *
	 * @since 4.1.0
	 */
	protected function render_custom_tables_interface() {
		// Get custom tables admin instance if available.
		if ( ! class_exists( '\WebberZone\Contextual_Related_Posts\Pro\Custom_Tables\Custom_Tables_Admin' ) ) {
			?>
			<div class="notice notice-error inline">
				<p><?php esc_html_e( 'Custom tables functionality is not available.', 'contextual-related-posts' ); ?></p>
			</div>
			<?php
			return;
		}

		// Get table manager instance with lazy admin initialization.
		$custom_tables = wz_crp()->pro->custom_tables ?? null;
		if ( ! $custom_tables ) {
			?>
			<div class="notice notice-error inline">
				<p><?php esc_html_e( 'Custom tables are not available.', 'contextual-related-posts' ); ?></p>
			</div>
			<?php
			return;
		}

		$table_manager = $custom_tables->admin->table_manager;
		$content_count = $table_manager->get_content_count();
		$post_count    = $table_manager->get_post_count();
		$percentage    = $post_count > 0 ? min( 100, round( ( $content_count / $post_count ) * 100 ) ) : 0;

		// Check if indexing is in progress.
		$reindex_state = $custom_tables->admin->get_reindex_state();
		$is_running    = false;
		$progress      = 0;

		if ( false !== $reindex_state && isset( $reindex_state['status'] ) && 'running' === $reindex_state['status'] ) {
			$progress   = $reindex_state['total'] > 0 ? round( ( $reindex_state['offset'] / $reindex_state['total'] ) * 100 ) : 0;
			$is_running = true;
		}
		?>
		<div class="crp-wizard-reindex">
			<div class="crp-index-status-wrapper">
				<h3><?php esc_html_e( 'Current Index Status', 'contextual-related-posts' ); ?></h3>
				<p>
					<?php
					printf(
						/* translators: 1: Number of posts in the content table */
						esc_html__( 'Content Table: %1$d entries', 'contextual-related-posts' ),
						intval( $content_count )
					);
					echo '<br />';
					printf(
						/* translators: 1: Number of published posts, 2: Percentage of posts indexed */
						esc_html__( 'Published Posts: %1$d, Index Status: %2$d%%', 'contextual-related-posts' ),
						intval( $post_count ),
						absint( $percentage )
					);
					?>
				</p>

				<div class="crp-index-status">
					<div class="crp-index-bar" style="width: <?php echo esc_attr( (string) $percentage ); ?>%; background-color: <?php echo $percentage >= 80 ? '#00a32a' : ( $percentage >= 40 ? '#dba617' : '#d63638' ); ?>;"></div>
					<span><?php echo absint( $percentage ); ?>%</span>
				</div>
			</div>

			<div class="crp-reindex-controls">
				<h3><?php esc_html_e( 'Index Management', 'contextual-related-posts' ); ?></h3>
				<p><?php esc_html_e( 'Click the button below to start indexing your content for improved performance.', 'contextual-related-posts' ); ?></p>

			<!-- Use exact DOM element IDs expected by reindex.js -->
			<div class="crp-reindex-button-wrapper">
				<button type="button" id="crp-start-reindex" class="button button-primary">
					<?php esc_html_e( 'Start Indexing', 'contextual-related-posts' ); ?>
				</button>
				<label for="crp_force_reindex" style="margin-left: 15px;">
					<input type="checkbox" id="crp_force_reindex" name="crp_force_reindex" value="1" />
					<?php esc_html_e( 'Force reindex (clear existing data)', 'contextual-related-posts' ); ?>
				</label>
			</div>

			<!-- Progress container with exact IDs expected by reindex.js -->
			<div id="crp-reindex-progress-container" style="display: none; margin-top: 20px;">
				<div class="crp-progress-wrapper">
					<div id="crp-progress-bar" style="width: 0%; height: 20px; background: #0073aa; border-radius: 3px; transition: width 0.3s ease;"></div>
				</div>
				<p>
					<span id="crp-progress-text">0%</span>
					<span id="crp-reindex-status"></span>
				</p>
			</div>
			</div>

			<div class="crp-wizard-note">
				<p><strong><?php esc_html_e( 'Note:', 'contextual-related-posts' ); ?></strong> <?php esc_html_e( 'You can skip this step and index your content later from the Tools page if needed.', 'contextual-related-posts' ); ?></p>
			</div>
		</div>

		<style>
		.crp-wizard-reindex {
			max-width: 600px;
			margin: 0 auto;
		}
		.crp-index-status-wrapper,
		.crp-reindex-controls {
			margin-bottom: 30px;
			padding: 20px;
			border: 1px solid #ddd;
			background: #f9f9f9;
			border-radius: 4px;
		}
		.crp-index-status {
			position: relative;
			height: 20px;
			background: #e0e0e0;
			border-radius: 10px;
			margin: 10px 0;
			overflow: hidden;
		}
		.crp-index-bar {
			height: 100%;
			transition: width 0.3s ease;
			border-radius: 10px;
		}
		.crp-index-status span {
			position: absolute;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
			font-size: 12px;
			font-weight: bold;
			color: #333;
		}
		.crp-progress-bar {
			height: 20px;
			background: #e0e0e0;
			border-radius: 10px;
			overflow: hidden;
			margin: 10px 0;
		}
		.crp-progress-fill {
			height: 100%;
			background: #0073aa;
			transition: width 0.3s ease;
			border-radius: 10px;
		}
		.crp-wizard-note {
			margin-top: 20px;
			padding: 15px;
			background: #fff3cd;
			border: 1px solid #ffeaa7;
			border-radius: 4px;
		}
		.crp-reindex-button-wrapper {
			margin: 15px 0;
		}
		</style>
		<?php
	}

	/**
	 * Override the render completion page to show CRP specific content.
	 *
	 * @since 4.1.0
	 */
	protected function render_completion_page() {
		?>
		<div class="wrap wizard-wrap wizard-complete">
			<div class="wizard-completion-header">
				<h1><?php echo esc_html( $this->translation_strings['wizard_complete'] ); ?></h1>
				<p class="wizard-completion-message">
					<?php echo esc_html( $this->translation_strings['setup_complete'] ); ?>
				</p>
			</div>

			<div class="wizard-completion-content">
				<div class="wizard-completion-features">
					<h3><?php esc_html_e( "What's Next?", 'contextual-related-posts' ); ?></h3>
					<ul>
						<li><?php esc_html_e( 'Visit your site to see related posts in action', 'contextual-related-posts' ); ?></li>
						<li><?php esc_html_e( 'Customize the display using the settings page', 'contextual-related-posts' ); ?></li>
						<li><?php esc_html_e( 'Check the Tools page for maintenance options', 'contextual-related-posts' ); ?></li>
					</ul>
				</div>

				<div class="wizard-completion-actions">
					<a href="<?php echo esc_url( $this->get_completion_redirect_url() ); ?>" class="button button-primary button-large">
						<?php esc_html_e( 'Go to Settings', 'contextual-related-posts' ); ?>
					</a>
					<a href="<?php echo esc_url( admin_url( 'tools.php?page=crp_tools_page' ) ); ?>" class="button button-secondary">
						<?php esc_html_e( 'View Tools', 'contextual-related-posts' ); ?>
					</a>
					<a href="<?php echo esc_url( home_url() ); ?>" class="button button-secondary" target="_blank">
						<?php esc_html_e( 'Visit Site', 'contextual-related-posts' ); ?>
					</a>
				</div>
			</div>
		</div>
		<?php
	}
}

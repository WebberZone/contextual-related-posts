<?php
/**
 * Settings Wizard API.
 *
 * A reusable API class for creating multi-step settings wizards.
 * This class provides the framework for creating guided setup experiences.
 *
 * @since 4.1.0
 *
 * @package WebberZone\Contextual_Related_Posts
 */

namespace WebberZone\Contextual_Related_Posts\Admin\Settings;

use WebberZone\Contextual_Related_Posts\Admin\Settings\Settings_Sanitize;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Settings Wizard API class
 *
 * @since 4.1.0
 */
class Settings_Wizard_API {

	/**
	 * Current version number
	 *
	 * @var string
	 */
	public const VERSION = '1.0.0';

	/**
	 * Settings sanitizer instance.
	 *
	 * @var Settings_Sanitize
	 */
	protected $settings_sanitize;

	/**
	 * Settings Key.
	 *
	 * @var string Settings Key.
	 */
	public $settings_key;

	/**
	 * Prefix which is used for creating the unique filters and actions.
	 *
	 * @var string Prefix.
	 */
	public $prefix;

	/**
	 * Menu arguments for the wizard.
	 *
	 * @var array Menu arguments array with parent and capability.
	 */
	protected $menu_args;

	/**
	 * Wizard steps configuration.
	 *
	 * @var array Wizard steps.
	 */
	protected $steps = array();

	/**
	 * Current step number.
	 *
	 * @var int Current step.
	 */
	protected $current_step = 1;

	/**
	 * Total number of steps.
	 *
	 * @var int Total steps.
	 */
	protected $total_steps = 0;

	/**
	 * Translation strings.
	 *
	 * @var array Translation strings.
	 */
	public $translation_strings;

	/**
	 * Wizard page slug.
	 *
	 * @var string Wizard page slug.
	 */
	public $page_slug;

	/**
	 * Wizard page id.
	 *
	 * @var string Wizard page id.
	 */
	public $page_id;

	/**
	 * Settings form.
	 *
	 * @var object Settings form.
	 */
	public $settings_form;

	/**
	 * Args.
	 *
	 * @var array Args.
	 */
	public $args;

	/**
	 * Main constructor class.
	 *
	 * @param string $settings_key Settings key.
	 * @param string $prefix       Prefix. Used for actions and filters.
	 * @param array  $args         {
	 *     Array of arguments.
	 *     @type array  $steps                Array of wizard steps.
	 *     @type array  $translation_strings  Translation strings.
	 *     @type string $page_slug            Wizard page slug.
	 *     @type array  $menu_args           Menu arguments array with parent and capability.
	 * }
	 */
	public function __construct( $settings_key, $prefix, $args = array() ) {

		$this->settings_key = $settings_key;
		$this->prefix       = $prefix;

		$defaults   = array(
			'steps'               => array(),
			'translation_strings' => array(),
			'admin_menu_position' => 999,
			'page_slug'           => "{$prefix}_wizard",
			'menu_args'           => array(
				'parent'     => '', // Empty for dashboard, or parent slug for submenu.
				'capability' => 'manage_options',
			),
		);
		$args       = wp_parse_args( $args, $defaults );
		$this->args = $args;

		$this->page_slug = $args['page_slug'];
		$this->menu_args = $args['menu_args'];
		$this->set_translation_strings( $args['translation_strings'] );
		$this->set_steps( $args['steps'] );

		// Initialize settings form.
		$this->settings_form = new Settings_Form(
			array(
				'settings_key' => $this->settings_key,
				'prefix'       => $this->prefix,
			)
		);

		$this->settings_sanitize = new Settings_Sanitize();

		$this->hooks();
	}

	/**
	 * Adds the functions to the appropriate WordPress hooks.
	 */
	public function hooks() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ), $this->args['admin_menu_position'] );
		add_action( 'admin_init', array( $this, 'process_step' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Sets translation strings.
	 *
	 * @param array $strings Translation strings.
	 */
	public function set_translation_strings( $strings ) {
		$defaults = array(
			'page_title'      => 'Setup Wizard',
			'menu_title'      => 'Setup Wizard',
			'wizard_title'    => 'Setup Wizard',
			'next_step'       => 'Next Step',
			'previous_step'   => 'Previous Step',
			'finish_setup'    => 'Finish Setup',
			'skip_wizard'     => 'Skip Wizard',
			'step_of'         => 'Step %1$d of %2$d',
			'wizard_complete' => 'Wizard Complete!',
			'setup_complete'  => 'Setup has been completed successfully.',
			'go_to_settings'  => 'Go to Settings',
		);

		$this->translation_strings = wp_parse_args( $strings, $defaults );
	}

	/**
	 * Set wizard steps.
	 *
	 * @param array $steps Array of wizard steps.
	 * @return object Class object.
	 */
	public function set_steps( $steps ) {
		$this->steps       = $steps;
		$this->total_steps = count( $steps );
		return $this;
	}

	/**
	 * Add admin menu for the wizard.
	 */
	public function admin_menu() {
		$capability = ! empty( $this->menu_args['capability'] ) ? $this->menu_args['capability'] : 'manage_options';
		$parent     = ! empty( $this->menu_args['parent'] ) ? $this->menu_args['parent'] : 'index.php';

		$this->page_id = add_submenu_page(
			$this->is_wizard_completed() ? 'options.php' : $parent,
			$this->translation_strings['page_title'],
			$this->translation_strings['menu_title'],
			$capability,
			$this->page_slug,
			array( $this, 'render_wizard_page' )
		);
	}

	/**
	 * Enqueue scripts and styles for the wizard.
	 *
	 * @param string $hook Current admin page hook.
	 */
	public function enqueue_scripts( $hook ) {
		if ( false === strpos( $hook, $this->page_slug ) ) {
			return;
		}

		$minimize = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		// Core scripts and styles.
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_media();
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-autocomplete' );
		wp_enqueue_script( 'jquery-ui-tabs' );

		// Wizard styles.
		wp_enqueue_style(
			"{$this->prefix}-wizard-css",
			plugins_url( 'css/wizard' . $minimize . '.css', __FILE__ ),
			array( 'wp-color-picker' ),
			$this->get_version(),
			'all'
		);
	}

	/**
	 * Process wizard step submission.
	 */
	public function process_step() {
		if ( empty( $_POST['wizard_action'] ) ) { // Don't run on every admin_init, only on our form submission.
			return;
		}

		$nonce_value = isset( $_POST[ $this->prefix . '_wizard_nonce' ] ) ? sanitize_text_field( wp_unslash( $_POST[ $this->prefix . '_wizard_nonce' ] ) ) : '';
		if ( empty( $nonce_value ) || ! wp_verify_nonce( $nonce_value, $this->prefix . '_wizard_nonce' ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Initialise the current step based on the URL or stored option before processing the action.
		$this->current_step = $this->get_current_step();

		$action = isset( $_POST['wizard_action'] ) ? sanitize_text_field( wp_unslash( $_POST['wizard_action'] ) ) : '';

		switch ( $action ) {
			case 'next_step':
				$this->process_current_step();
				$this->next_step();
				$this->redirect_to_step( $this->current_step );
				break;

			case 'previous_step':
				$this->previous_step();
				$this->redirect_to_step( $this->current_step );
				break;

			case 'finish_setup':
				$this->process_current_step();
				$this->mark_wizard_completed();
				$this->redirect_to_step( $this->total_steps + 1 );
				break;

			case 'skip_wizard':
				$this->complete_wizard();
				$this->redirect_to_admin();
				break;
			default:
				break;
		}
	}

	/**
	 * Process the current step's form data.
	 */
	protected function process_current_step() {
		$current_step_config = $this->get_current_step_config();

		if ( empty( $current_step_config['settings'] ) ) {
			return;
		}

		$settings = array();

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST[ $this->settings_key ] ) && is_array( $_POST[ $this->settings_key ] ) ) {
			foreach ( $current_step_config['settings'] as $setting_id => $setting_config ) {
				// phpcs:ignore WordPress.Security.NonceVerification.Missing
				if ( isset( $_POST[ $this->settings_key ][ $setting_id ] ) ) {
					// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.NonceVerification.Missing
					$value                   = $this->sanitize_setting_value( wp_unslash( $_POST[ $this->settings_key ][ $setting_id ] ), $setting_config );
					$settings[ $setting_id ] = $value;
				}
			}
		}

		// Save settings for this step.
		$this->save_step_settings( $settings );

		/**
		 * Action fired after processing a wizard step.
		 *
		 * @param int   $step     Current step number.
		 * @param array $settings Settings data for this step.
		 */
		do_action( $this->prefix . '_wizard_step_processed', $this->current_step, $settings );
	}

	/**
	 * Sanitize setting value based on its type.
	 *
	 * @param mixed $value          Setting value.
	 * @param array $setting_config Setting configuration.
	 * @return mixed Sanitized value.
	 */
	protected function sanitize_setting_value( $value, $setting_config ) {
		$type = $setting_config['type'] ?? 'text';

		// Use the Settings_Sanitize class for proper sanitization.
		$settings_sanitize = $this->settings_sanitize;

		// Check if we have a specific sanitizer for this type.
		if ( is_callable( array( $settings_sanitize, "sanitize_{$type}_field" ) ) ) {
			return call_user_func( array( $settings_sanitize, "sanitize_{$type}_field" ), $value );
		}

		// Fallback to basic sanitization.
		if ( is_array( $value ) ) {
			return array_map( 'sanitize_text_field', $value );
		}

		return sanitize_text_field( $value );
	}

	/**
	 * Save settings for the current step.
	 *
	 * @param array $settings Settings to save.
	 */
	protected function save_step_settings( $settings ) {
		$existing_settings = get_option( $this->settings_key, array() );
		$updated_settings  = array_merge( $existing_settings, $settings );
		update_option( $this->settings_key, $updated_settings );
	}

	/**
	 * Move to the next step.
	 */
	protected function next_step() {
		if ( $this->current_step < $this->total_steps ) {
			++$this->current_step;
			$this->update_current_step();
		}
	}

	/**
	 * Move to the previous step.
	 */
	protected function previous_step() {
		if ( $this->current_step > 1 ) {
			--$this->current_step;
			$this->update_current_step();
		}
	}

	/**
	 * Redirect to a specific wizard step.
	 *
	 * @param int $step Step number to redirect to.
	 */
	protected function redirect_to_step( $step ) {
		$url = add_query_arg(
			array(
				'page' => $this->page_slug,
				'step' => $step,
			),
			admin_url( 'admin.php' )
		);
		wp_safe_redirect( $url );
		exit;
	}

	/**
	 * Redirect to the admin page after wizard completion.
	 */
	protected function redirect_to_admin() {
		$url = add_query_arg(
			array(
				'page'             => str_replace( '_wizard', '', $this->page_slug ),
				'wizard-completed' => '1',
			),
			admin_url( 'admin.php' )
		);
		wp_safe_redirect( $url );
		exit;
	}

	/**
	 * Mark the wizard as completed without redirecting.
	 */
	protected function mark_wizard_completed() {
		update_option( "{$this->prefix}_wizard_completed", true );
		update_option( "{$this->prefix}_wizard_completed_date", current_time( 'mysql' ) );

		// Clean up the transient and option that triggered the wizard.
		delete_transient( "{$this->prefix}_show_wizard_activation_redirect" );
		delete_option( "{$this->prefix}_show_wizard" );

		/**
		 * Action fired when the wizard is completed.
		 *
		 * @param string $prefix Plugin prefix.
		 */
		do_action( "{$this->prefix}_wizard_completed", $this->prefix );
	}

	/**
	 * Complete the wizard.
	 */
	protected function complete_wizard() {
		$this->mark_wizard_completed();

		// Redirect to completion page or main settings.
		wp_safe_redirect( $this->get_completion_redirect_url() );
		exit;
	}

	/**
	 * Get the current step number.
	 *
	 * @return int Current step number.
	 */
	public function get_current_step() {
		// Check if we have a step parameter in the URL first.
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['step'] ) ) {
			$step = absint( $_GET['step'] );
			if ( $step > 0 && $step <= $this->total_steps + 1 ) {
				$this->current_step = $step;
				$this->update_current_step();
				return $this->current_step;
			}
		}
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		// Fall back to the database value.
		if ( ! $this->current_step ) {
			$this->current_step = get_option( "{$this->prefix}_wizard_current_step", 1 );
		}
		return $this->current_step;
	}

	/**
	 * Update the current step in the database.
	 */
	protected function update_current_step() {
		update_option( "{$this->prefix}_wizard_current_step", $this->current_step );
	}

	/**
	 * Get the current step configuration.
	 *
	 * @return array Current step configuration.
	 */
	public function get_current_step_config() {
		$keys  = array_keys( $this->steps );
		$index = $this->get_current_step() - 1;

		// Return empty array if steps is empty or index is out of bounds.
		if ( empty( $keys ) || ! isset( $keys[ $index ] ) ) {
			return array();
		}

		return $this->steps[ $keys[ $index ] ] ?? array();
	}

	/**
	 * Check if the wizard has been completed.
	 *
	 * @return bool True if wizard is completed.
	 */
	public function is_wizard_completed() {
		return (bool) get_option( "{$this->prefix}_wizard_completed", false );
	}

	/**
	 * Render the wizard page.
	 */
	public function render_wizard_page() {

		$this->current_step = $this->get_current_step();
		$step_config        = $this->get_current_step_config();

		if ( empty( $step_config ) ) {
			$this->render_completion_page();
			return;
		}

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
							<?php if ( ! empty( $step_config['settings'] ) ) : ?>
							<table class="form-table">
								<?php
								foreach ( $step_config['settings'] as $setting_id => $field ) {
									$args = wp_parse_args(
										$field,
										array(
											'id'          => null,
											'name'        => '',
											'desc'        => '',
											'type'        => null,
											'default'     => '',
											'options'     => '',
											'max'         => null,
											'min'         => null,
											'step'        => null,
											'size'        => null,
											'field_class' => '',
											'field_attributes' => '',
											'placeholder' => '',
											'pro'         => false,
										)
									);

									// Get all settings from the main settings array.
									$all_settings = get_option( $this->settings_key, array() );

									// Check if this setting exists in the saved settings.
									$value = isset( $all_settings[ $setting_id ] ) ? $all_settings[ $setting_id ] : null;

									// Use saved value if it exists, otherwise use default.
									$args['value'] = ( null !== $value ) ? $value : ( isset( $args['default'] ) ? $args['default'] : '' );
									$type          = $args['type'] ?? 'text';
									$callback      = method_exists( $this->settings_form, "callback_{$type}" ) ? array( $this->settings_form, "callback_{$type}" ) : array( $this->settings_form, 'callback_missing' );

									echo '<tr>';
									echo '<th scope="row">';
									if ( ! empty( $args['name'] ) ) {
										echo '<label for="' . esc_attr( $setting_id ) . '">' . esc_html( $args['name'] ) . '</label>';
									}
									echo '</th>';
									echo '<td>';
									call_user_func( $callback, $args );
									echo '</td>';
									echo '</tr>';
								}
								?>
							</table>
						<?php endif; ?>
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
	 * Get the current value for a setting.
	 *
	 * @param string $setting_id Setting ID.
	 * @return mixed Setting value.
	 */
	protected function get_setting_value( $setting_id ) {
		$settings = get_option( $this->settings_key, array() );
		return $settings[ $setting_id ] ?? '';
	}

	/**
	 * Render wizard navigation buttons.
	 */
	protected function render_wizard_buttons() {
		?>
		<div class="wizard-button-group">
			<?php if ( $this->current_step > 1 ) : ?>
				<button type="submit" name="wizard_action" value="previous_step" class="button button-secondary">
					<?php echo esc_html( $this->translation_strings['previous_step'] ); ?>
				</button>
			<?php endif; ?>

			<?php if ( $this->current_step < $this->total_steps ) : ?>
				<button type="submit" name="wizard_action" value="next_step" class="button button-primary">
					<?php echo esc_html( $this->translation_strings['next_step'] ); ?>
				</button>
			<?php else : ?>
				<button type="submit" name="wizard_action" value="finish_setup" class="button button-primary">
					<?php echo esc_html( $this->translation_strings['finish_setup'] ); ?>
				</button>
			<?php endif; ?>

			<button type="submit" name="wizard_action" value="skip_wizard" class="button button-link">
				<?php echo esc_html( $this->translation_strings['skip_wizard'] ); ?>
			</button>
		</div>
		<?php
	}

	/**
	 * Render the completion page.
	 */
	protected function render_completion_page() {
		/**
		 * Fires before the wizard completion page content.
		 */
		do_action( "{$this->prefix}_wizard_completion_before" );
		?>
		<div class="wrap wizard-wrap wizard-complete">
			<h1><?php echo esc_html( $this->translation_strings['wizard_complete'] ); ?></h1>
			<p><?php echo esc_html( $this->translation_strings['setup_complete'] ); ?></p>

			<?php
			/**
			 * Fires after the wizard completion message.
			 */
			do_action( "{$this->prefix}_wizard_completion_message" );
			?>

			<p class="wizard-actions">
				<?php
				$buttons = $this->get_completion_buttons();
				foreach ( $buttons as $button ) :
					$class = isset( $button['primary'] ) && $button['primary'] ? 'button-primary' : 'button-secondary';
					?>
					<a href="<?php echo esc_url( $button['url'] ); ?>" class="button <?php echo esc_attr( $class ); ?>">
						<?php echo esc_html( $button['text'] ); ?>
					</a>
				<?php endforeach; ?>
			</p>
		</div>
		<?php
		/**
		 * Fires after the wizard completion page content.
		 */
		do_action( "{$this->prefix}_wizard_completion_after" );
	}

	/**
	 * Get the URL to redirect to after wizard completion.
	 *
	 * @return string Redirect URL.
	 */
	protected function get_completion_redirect_url() {
		/**
		 * Filter the URL to redirect to after wizard completion.
		 *
		 * @param string $url    The URL to redirect to.
		 * @param string $prefix Plugin prefix.
		 */
		return apply_filters(
			"{$this->prefix}_wizard_completion_url",
			admin_url( "admin.php?page={$this->prefix}_settings" ),
			$this->prefix
		);
	}

	/**
	 * Get the completion page buttons.
	 *
	 * @return array Array of button configurations.
	 */
	protected function get_completion_buttons() {
		$buttons = array(
			array(
				'url'     => $this->get_completion_redirect_url(),
				'text'    => $this->translation_strings['go_to_settings'],
				'primary' => true,
			),
		);

		/**
		 * Filter the completion page buttons.
		 *
		 * @param array  $buttons Array of button configurations.
		 * @param string $prefix  Plugin prefix.
		 */
		return apply_filters( "{$this->prefix}_wizard_completion_buttons", $buttons, $this->prefix );
	}

	/**
	 * Get the version for cache busting.
	 *
	 * @return string Version number.
	 */
	protected function get_version() {
		/**
		 * Filter the version number used for cache busting.
		 *
		 * @param string $version Version number.
		 * @param string $prefix  Plugin prefix.
		 */
		return apply_filters( "{$this->prefix}_wizard_version", self::VERSION, $this->prefix );
	}

	/**
	 * Reset the wizard to allow it to run again.
	 */
	public function reset_wizard() {
		delete_option( "{$this->prefix}_wizard_completed" );
		delete_option( "{$this->prefix}_wizard_completed_date" );
		delete_option( "{$this->prefix}_wizard_current_step" );
	}

	/**
	 * Check if the wizard should be shown (e.g., on first activation).
	 *
	 * @return bool True if wizard should be shown.
	 */
	public function should_show_wizard() {
		// Show wizard if it hasn't been completed and it's been triggered.
		return ! $this->is_wizard_completed() && get_option( "{$this->prefix}_show_wizard", false );
	}

	/**
	 * Trigger the wizard to be shown.
	 */
	public function trigger_wizard() {
		update_option( "{$this->prefix}_show_wizard", true );
	}
}

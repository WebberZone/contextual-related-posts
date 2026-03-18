<?php
/**
 * Admin notices API.
 *
 * @package WebberZone\Knowledge_Base\Admin
 */

namespace WebberZone\Contextual_Related_Posts\Admin;

use WebberZone\Contextual_Related_Posts\Util\Hook_Registry;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class to handle admin notices.
 */
class Admin_Notices_API {

	/**
	 * Plugin prefix used for AJAX actions, nonces, and storage keys.
	 *
	 * @var string
	 */
	private string $prefix;

	/**
	 * Array of registered notices.
	 *
	 * @var array Registered notices.
	 */
	private array $notices = array();

	/**
	 * Constructor class.
	 *
	 * @param string $prefix Plugin prefix for AJAX actions, nonces, and storage keys. Default 'wzkb'.
	 */
	public function __construct( string $prefix = 'crp' ) {
		$this->prefix = $prefix;

		Hook_Registry::add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		Hook_Registry::add_action( 'admin_notices', array( $this, 'display_notices' ) );
		Hook_Registry::add_action( "wp_ajax_{$this->prefix}_dismiss_notice", array( $this, 'handle_notice_dismissal' ) );
	}

	/**
	 * Register and enqueue the dismiss script, pushing this instance's config
	 * into the shared window.adminNoticesConfigs array.
	 */
	public function enqueue_scripts() {
		$minimize = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		$handle   = "{$this->prefix}-admin-notices";

		wp_register_script(
			$handle,
			plugins_url( "js/admin-notices{$minimize}.js", __FILE__ ),
			array( 'jquery' ),
			WZ_CRP_VERSION,
			true
		);

		$config = wp_json_encode(
			array(
				'prefix' => $this->prefix,
				'action' => "{$this->prefix}_dismiss_notice",
				'nonce'  => wp_create_nonce( "{$this->prefix}_dismiss_notice" ),
			)
		);

		wp_add_inline_script(
			$handle,
			'window.adminNoticesConfigs = window.adminNoticesConfigs || []; window.adminNoticesConfigs.push(' . $config . ');',
			'before'
		);

		wp_enqueue_script( $handle );
	}

	/**
	 * Register a new notice.
	 *
	 * @param array $notice {
	 *     Notice arguments.
	 *
	 *     @type string  $id           Unique notice ID.
	 *     @type string  $message      Notice message.
	 *     @type string  $type         Notice type. Either 'error', 'warning', 'success' or 'info'.
	 *     @type bool    $dismissible  Whether the notice is dismissible.
	 *     @type int     $dismiss_time Dismiss time in seconds. Default 0 (permanent).
	 *     @type array   $screens      Array of screens to show notice on. Empty means all screens.
	 *     @type string  $capability   Capability required to see the notice.
	 *     @type array   $conditions   Array of callbacks to determine if notice should show.
	 * }
	 */
	public function register_notice( array $notice ) {
		$default_notice = array(
			'id'           => '',
			'message'      => '',
			'type'         => 'info',
			'dismissible'  => true,
			'dismiss_time' => 0,
			'screens'      => array(),
			'capability'   => 'manage_options',
			'conditions'   => array(),
		);

		$notice = wp_parse_args( $notice, $default_notice );

		if ( empty( $notice['id'] ) || empty( $notice['message'] ) ) {
			return;
		}

		$this->notices[ $notice['id'] ] = $notice;
	}

	/**
	 * Display registered notices.
	 */
	public function display_notices() {
		$screen = get_current_screen();
		if ( null === $screen ) {
			return;
		}

		foreach ( $this->notices as $notice ) {
			// Skip if user doesn't have capability.
			if ( ! current_user_can( $notice['capability'] ) ) {
				continue;
			}

			// Skip if not on correct screen.
			if ( ! empty( $notice['screens'] ) && ! in_array( $screen->id, $notice['screens'], true ) ) {
				continue;
			}

			// Check conditions.
			foreach ( $notice['conditions'] as $condition ) {
				if ( is_callable( $condition ) && ! call_user_func( $condition ) ) {
					continue 2;
				}
			}

			// Skip if notice is dismissed.
			if ( $this->is_notice_dismissed( $notice['id'] ) ) {
				continue;
			}

			$class = 'notice notice-' . $notice['type'];
			if ( $notice['dismissible'] ) {
				$class .= ' is-dismissible';
			}

			printf(
				'<div class="%1$s" data-notice-id="%2$s" data-dismiss-time="%3$s" data-notice-prefix="%4$s">%5$s</div>',
				esc_attr( $class ),
				esc_attr( $notice['id'] ),
				esc_attr( $notice['dismiss_time'] ),
				esc_attr( $this->prefix ),
				wp_kses_post( $notice['message'] )
			);
		}
	}

	/**
	 * Handle notice dismissal via AJAX.
	 */
	public function handle_notice_dismissal() {
		check_ajax_referer( "{$this->prefix}_dismiss_notice", 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die();
		}

		$notice_id    = isset( $_POST['notice_id'] ) ? sanitize_key( $_POST['notice_id'] ) : '';
		$dismiss_time = isset( $_POST['dismiss_time'] ) ? absint( $_POST['dismiss_time'] ) : 0;

		if ( ! $notice_id ) {
			wp_die();
		}

		$key = "{$this->prefix}_notice_dismissed_{$notice_id}";

		if ( $dismiss_time ) {
			set_transient( $key, true, $dismiss_time );
		} else {
			update_user_meta( get_current_user_id(), $key, true );
		}

		wp_die();
	}

	/**
	 * Check if a notice has been dismissed.
	 *
	 * @param string $notice_id Notice ID.
	 * @return bool Whether the notice has been dismissed.
	 */
	private function is_notice_dismissed( $notice_id ) {
		$notice = $this->notices[ $notice_id ] ?? null;

		if ( ! $notice ) {
			return false;
		}

		$key = "{$this->prefix}_notice_dismissed_{$notice_id}";

		if ( $notice['dismiss_time'] ) {
			return (bool) get_transient( $key );
		}

		return (bool) get_user_meta( get_current_user_id(), $key, true );
	}
}

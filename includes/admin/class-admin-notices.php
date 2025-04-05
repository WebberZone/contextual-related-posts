<?php
/**
 * Admin notices.
 *
 * @package WebberZone\Contextual_Related_Posts\Admin
 */

namespace WebberZone\Contextual_Related_Posts\Admin;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class to handle admin notices.
 *
 * @since 4.0.0
 */
class Admin_Notices {

	/**
	 * Array of registered notices.
	 *
	 * @since 4.0.0
	 *
	 * @var array Registered notices.
	 */
	private array $notices = array();

	/**
	 * Constructor class.
	 *
	 * @since 4.0.0
	 */
	public function __construct() {
		add_action( 'admin_notices', array( $this, 'display_notices' ) );
		add_action( 'wp_ajax_crp_dismiss_notice', array( $this, 'handle_notice_dismissal' ) );
	}

	/**
	 * Register a new notice.
	 *
	 * @since 4.0.0
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
	 *
	 * @since 4.0.0
	 */
	public function display_notices() {
		$screen = get_current_screen();

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
				'<div class="%1$s" data-notice-id="%2$s" data-dismiss-time="%3$s">%4$s</div>',
				esc_attr( $class ),
				esc_attr( $notice['id'] ),
				esc_attr( $notice['dismiss_time'] ),
				wp_kses_post( $notice['message'] )
			);
		}

		$this->print_scripts();
	}

	/**
	 * Print scripts for notice dismissal.
	 *
	 * @since 4.0.0
	 */
	private function print_scripts() {
		static $printed = false;

		if ( $printed ) {
			return;
		}

		?>
		<script>
		jQuery(document).ready(function($) {
			$('.notice[data-notice-id]').on('click', '.notice-dismiss', function() {
				var $notice = $(this).closest('.notice');
				var noticeId = $notice.data('notice-id');
				var dismissTime = $notice.data('dismiss-time');

				$.post(ajaxurl, {
					action: 'crp_dismiss_notice',
					notice_id: noticeId,
					dismiss_time: dismissTime,
					nonce: '<?php echo esc_js( wp_create_nonce( 'crp_dismiss_notice' ) ); ?>'
				});
			});
		});
		</script>
		<?php

		$printed = true;
	}

	/**
	 * Handle notice dismissal via AJAX.
	 *
	 * @since 4.0.0
	 */
	public function handle_notice_dismissal() {
		check_ajax_referer( 'crp_dismiss_notice', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die();
		}

		$notice_id    = isset( $_POST['notice_id'] ) ? sanitize_key( $_POST['notice_id'] ) : '';
		$dismiss_time = isset( $_POST['dismiss_time'] ) ? absint( $_POST['dismiss_time'] ) : 0;

		if ( ! $notice_id ) {
			wp_die();
		}

		if ( $dismiss_time ) {
			set_transient( "crp_notice_dismissed_{$notice_id}", true, $dismiss_time );
		} else {
			update_user_meta( get_current_user_id(), "crp_notice_dismissed_{$notice_id}", true );
		}

		wp_die();
	}

	/**
	 * Check if a notice has been dismissed.
	 *
	 * @since 4.0.0
	 *
	 * @param string $notice_id Notice ID.
	 * @return bool Whether the notice has been dismissed.
	 */
	private function is_notice_dismissed( $notice_id ) {
		$notice = $this->notices[ $notice_id ] ?? null;

		if ( ! $notice ) {
			return false;
		}

		if ( $notice['dismiss_time'] ) {
			return (bool) get_transient( "crp_notice_dismissed_{$notice_id}" );
		}

		return (bool) get_user_meta( get_current_user_id(), "crp_notice_dismissed_{$notice_id}", true );
	}
}

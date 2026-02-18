<?php
/**
 * Contextual Related Posts Bulk Edit functionality.
 *
 * @package Contextual_Related_Posts
 */

namespace WebberZone\Contextual_Related_Posts\Admin;

use WebberZone\Contextual_Related_Posts\Util\Hook_Registry;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Bulk Edit functionality.
 *
 * @since 3.4.0
 */
class Bulk_Edit {

	/**
	 * CRP_Bulk_Edit constructor.
	 */
	public function __construct() {
		Hook_Registry::add_action( 'init', array( $this, 'add_custom_columns' ), 99 );
		Hook_Registry::add_action( 'bulk_edit_custom_box', array( $this, 'quick_edit_custom_box' ) );
		Hook_Registry::add_action( 'quick_edit_custom_box', array( $this, 'quick_edit_custom_box' ) );
		Hook_Registry::add_action( 'save_post', array( $this, 'save_post_meta' ) );
		Hook_Registry::add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		Hook_Registry::add_action( 'wp_ajax_crp_save_bulk_edit', array( $this, 'save_bulk_edit' ) );
	}

	/**
	 * Enqueue scripts and styles.
	 *
	 * @param string $hook The current admin page.
	 */
	public function enqueue_scripts( $hook ) {
		if ( 'edit.php' !== $hook ) {
			return;
		}

		$file_prefix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		wp_enqueue_script(
			'crp-bulk-edit',
			WZ_CRP_PLUGIN_URL . "includes/admin/js/bulk-edit{$file_prefix}.js",
			array( 'jquery' ),
			WZ_CRP_VERSION,
			true
		);
		wp_localize_script(
			'crp-bulk-edit',
			'crp_bulk_edit',
			array(
				'nonce' => wp_create_nonce( 'crp_bulk_edit_nonce' ),
			)
		);

		// Enqueue inline CSS to ensure minimum width for CRP column.
		wp_add_inline_style( 'wp-admin', '.column-crp_columns { min-width: 250px; }' );
	}

	/**
	 * Add custom columns to the posts list table.
	 */
	public function add_custom_columns() {
		// Get all post types present on the site.
		$post_types = get_post_types( array( 'public' => true ) );

		// For each post type, add the bulk edit functionality and the columns.
		foreach ( $post_types as $post_type ) {
			Hook_Registry::add_filter( 'manage_' . $post_type . '_posts_columns', array( $this, 'add_admin_columns' ) );
			Hook_Registry::add_action( 'manage_' . $post_type . '_posts_custom_column', array( $this, 'populate_custom_columns' ), 10, 2 );
		}
	}

	/**
	 * Add custom columns to the posts list table.
	 *
	 * @param array $columns The existing columns.
	 * @return array The modified columns.
	 */
	public function add_admin_columns( $columns ) {
		$columns['crp_columns'] = __( 'Contextual Related Posts', 'contextual-related-posts' );
		return $columns;
	}

	/**
	 * Populate the custom columns with data.
	 *
	 * @param string $column_name The name of the column.
	 * @param int    $post_id The ID of the post.
	 */
	public function populate_custom_columns( $column_name, $post_id ) {
		switch ( $column_name ) {
			case 'crp_columns':
				// Get related posts specific meta using backward compatible helper.
				$manual_related       = crp_get_meta( $post_id, 'manual_related' );
				$manual_related_array = wp_parse_id_list( $manual_related );

				// For each of the manual related posts, display the post ID with a link to open this in a new tab.
				if ( ! empty( $manual_related_array ) ) {
					$html = '<p>' . __( 'Manual related posts:', 'contextual-related-posts' );
					foreach ( $manual_related_array as $related_post_id ) {
						$html .= ' <a href="' . esc_url( get_permalink( $related_post_id ) ) . '" target="_blank">' . esc_html( (string) $related_post_id ) . '</a>,';
					}
					$html  = rtrim( $html, ',' );
					$html .= '<div class="crp_manual_related hidden">' . $manual_related . '</div>';
					$html .= '</p>';

					echo wp_kses_post( $html );
				}

				// Exclude this post.
				$exclude_this_post = crp_get_meta( $post_id, 'exclude_this_post' );

				// Display the checkbox.
				echo '<p>';
				esc_html_e( 'Exclude from list:', 'contextual-related-posts' );
				echo wp_kses_post( $exclude_this_post ? '<span class="dashicons dashicons-yes" style="color:green"></span>' : '<span class="dashicons dashicons-no" style="color:red"></span>' );
				echo '<input type="hidden" class="crp_exclude_this_post" value="' . esc_attr( $exclude_this_post ) . '" />';
				echo '</p>';

				break;
		}
	}

	/**
	 * Add custom field to quick edit screen.
	 *
	 * @param string $column_name The name of the column.
	 */
	public function quick_edit_custom_box( $column_name ) {

		switch ( $column_name ) {
			case 'crp_columns':
				if ( current_filter() === 'quick_edit_custom_box' ) {
					wp_nonce_field( 'crp_quick_edit_nonce', 'crp_quick_edit_nonce' );
				} else {
					wp_nonce_field( 'crp_bulk_edit_nonce', 'crp_bulk_edit_nonce' );
				}
				?>
				<fieldset class="inline-edit-col-left inline-edit-crp">
					<div class="inline-edit-col column-<?php echo esc_attr( $column_name ); ?>">
						<label class="inline-edit-group">
							<?php esc_html_e( 'Manual Related Posts', 'contextual-related-posts' ); ?>
							<?php
							if ( current_filter() === 'bulk_edit_custom_box' ) {
								echo ' ' . esc_html__( '(0 to clear the manual posts)', 'contextual-related-posts' );
							}
							?>
							<input type="text" name="crp_manual_related" class="widefat" value="">
						</label>
						<em><?php esc_html_e( 'Comma-separated list of post IDs', 'contextual-related-posts' ); ?></em>
						<label class="inline-edit-group">
							<?php if ( current_filter() === 'quick_edit_custom_box' ) { ?>
								<input type="checkbox" name="crp_exclude_this_post"><?php esc_html_e( 'Exclude this post from related posts', 'contextual-related-posts' ); ?>								
							<?php } else { ?>
								<?php esc_html_e( 'Exclude from related posts', 'contextual-related-posts' ); ?>
								<select name="crp_exclude_this_post">
									<option value="-1"><?php esc_html_e( '&mdash; No Change &mdash;', 'contextual-related-posts' ); ?></option>
									<option value="1"><?php esc_html_e( 'Exclude', 'contextual-related-posts' ); ?></option>
									<option value="0"><?php esc_html_e( 'Include', 'contextual-related-posts' ); ?></option>
								</select>
							<?php } ?>
						</label>
					</div>
				</fieldset>
				<?php
				break;
		}
	}

	/**
	 * Save custom field data.
	 *
	 * @param int $post_id The post ID.
	 */
	public function save_post_meta( $post_id ) {
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		if ( ! isset( $_REQUEST['crp_quick_edit_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['crp_quick_edit_nonce'] ) ), 'crp_quick_edit_nonce' ) ) {
			return;
		}

		if ( isset( $_REQUEST['crp_manual_related'] ) ) {
			$manual_related_array = wp_parse_id_list( sanitize_text_field( wp_unslash( $_REQUEST['crp_manual_related'] ) ) );

			foreach ( $manual_related_array as $key => $value ) {
				if ( 'publish' !== get_post_status( $value ) ) {
					unset( $manual_related_array[ $key ] );
				}
			}
			$manual_related = implode( ',', $manual_related_array );
			if ( $manual_related ) {
				update_post_meta( $post_id, '_crp_manual_related', $manual_related );
			} else {
				delete_post_meta( $post_id, '_crp_manual_related' );
			}
		} else {
			delete_post_meta( $post_id, '_crp_manual_related' );
		}

		if ( isset( $_REQUEST['crp_exclude_this_post'] ) ) {
			update_post_meta( $post_id, '_crp_exclude_this_post', 1 );
		} else {
			delete_post_meta( $post_id, '_crp_exclude_this_post' );
		}

		// Delete old array key if it exists to avoid conflicts.
		delete_post_meta( $post_id, 'crp_post_meta' );
	}

	/**
	 * Save bulk edit data.
	 */
	public function save_bulk_edit() {
		// Security check.
		check_ajax_referer( 'crp_bulk_edit_nonce', 'crp_bulk_edit_nonce' );

		// Get the post IDs.
		$post_ids = isset( $_POST['post_ids'] ) ? wp_parse_id_list( wp_unslash( $_POST['post_ids'] ) ) : array();

		// Get the post meta.
		$post_meta = array();

		if ( isset( $_POST['crp_manual_related'] ) ) {
			$manual_related_input = sanitize_text_field( wp_unslash( $_POST['crp_manual_related'] ) );

			// Handle special case: '0' or empty input means clear manual related posts.
			if ( '0' === $manual_related_input || '' === trim( $manual_related_input ) ) {
				$post_meta['manual_related'] = '';
			} else {
				$manual_related_array = wp_parse_id_list( $manual_related_input );

				if ( ! empty( $manual_related_array ) ) {
					foreach ( $manual_related_array as $key => $value ) {
						if ( 'publish' !== get_post_status( $value ) ) {
							unset( $manual_related_array[ $key ] );
						}
					}
					$manual_related              = implode( ',', $manual_related_array );
					$post_meta['manual_related'] = $manual_related;
				} else {
					// If array is empty after parsing, clear manual related posts.
					$post_meta['manual_related'] = '';
				}
			}
		}

		if ( isset( $_POST['crp_exclude_this_post'] ) && -1 !== (int) $_POST['crp_exclude_this_post'] ) {
			$post_meta['exclude_this_post'] = intval( wp_unslash( $_POST['crp_exclude_this_post'] ) );
		}

		// Now we can start saving.
		foreach ( $post_ids as $post_id ) {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				continue;
			}

			// Save manual_related if set.
			if ( isset( $post_meta['manual_related'] ) ) {
				if ( $post_meta['manual_related'] ) {
					update_post_meta( $post_id, '_crp_manual_related', $post_meta['manual_related'] );
				} else {
					delete_post_meta( $post_id, '_crp_manual_related' );
				}
			}

			// Save exclude_this_post if set.
			if ( isset( $post_meta['exclude_this_post'] ) ) {
				if ( $post_meta['exclude_this_post'] ) {
					update_post_meta( $post_id, '_crp_exclude_this_post', $post_meta['exclude_this_post'] );
				} else {
					delete_post_meta( $post_id, '_crp_exclude_this_post' );
				}
			}

			// Delete old array key if it exists to avoid conflicts.
			delete_post_meta( $post_id, 'crp_post_meta' );
		}

		wp_send_json_success();
	}
}

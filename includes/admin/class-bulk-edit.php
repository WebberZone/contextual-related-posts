<?php
/**
 * Contextual Related Posts Bulk Edit functionality.
 *
 * @package Contextual_Related_Posts
 */

namespace WebberZone\Contextual_Related_Posts\Admin;

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
		add_action( 'init', array( $this, 'add_custom_columns' ), 99 );
		add_action( 'bulk_edit_custom_box', array( $this, 'quick_edit_custom_box' ) );
		add_action( 'quick_edit_custom_box', array( $this, 'quick_edit_custom_box' ) );
		add_action( 'save_post', array( $this, 'save_post_meta' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_ajax_crp_save_bulk_edit', array( $this, 'save_bulk_edit' ) );
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
			CRP_PLUGIN_URL . "includes/admin/js/bulk-edit{$file_prefix}.js",
			array( 'jquery' ),
			CRP_VERSION,
			true
		);
		wp_localize_script(
			'crp-bulk-edit',
			'crp_bulk_edit',
			array(
				'nonce' => wp_create_nonce( 'crp_bulk_edit_nonce' ),
			)
		);
	}

	/**
	 * Add custom columns to the posts list table.
	 */
	public function add_custom_columns() {
		// Get all post types present on the site.
		$post_types = get_post_types( array( 'public' => true ) );

		// For each post type, add the bulk edit functionality and the columns.
		foreach ( $post_types as $post_type ) {
			add_filter( 'manage_' . $post_type . '_posts_columns', array( $this, 'add_admin_columns' ) );
			add_action( 'manage_' . $post_type . '_posts_custom_column', array( $this, 'populate_custom_columns' ), 10, 2 );
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
				// Get related posts specific meta.
				$post_meta = get_post_meta( $post_id, 'crp_post_meta', true );

				// Manual related.
				$manual_related       = isset( $post_meta['manual_related'] ) ? $post_meta['manual_related'] : '';
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
				$exclude_this_post = isset( $post_meta['exclude_this_post'] ) ? $post_meta['exclude_this_post'] : 0;

				// Display the checkbox.
				echo '<p>';
				esc_html_e( 'Exclude from list:', 'contextual-related-posts' );
				echo wp_kses_post( $exclude_this_post ? '<span class="dashicons dashicons-yes" style="color:green"></span>' : '<span class="dashicons dashicons-no" style="color:red"></span>' );
				echo '<div class="hidden"><div class="crp_exclude_this_post">' . esc_attr( $exclude_this_post ) . '</div></div>';
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
								' ' . esc_html_e( '(0 to clear the manual posts)', 'contextual-related-posts' );
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
									<option value="-1"><?php esc_html_e( '&mdash; No Change &mdash;' ); ?></option>
									<option value="1"><?php esc_html_e( 'Exclude' ); ?></option>
									<option value="0"><?php esc_html_e( 'Include' ); ?></option>
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

		$post_meta = array();

		if ( isset( $_REQUEST['crp_manual_related'] ) ) {
			$manual_related_array = wp_parse_id_list( sanitize_text_field( wp_unslash( $_REQUEST['crp_manual_related'] ) ) );

			foreach ( $manual_related_array as $key => $value ) {
				if ( 'publish' !== get_post_status( $value ) ) {
					unset( $manual_related_array[ $key ] );
				}
			}
			$manual_related              = implode( ',', $manual_related_array );
			$post_meta['manual_related'] = $manual_related;
		}

		if ( isset( $_REQUEST['crp_exclude_this_post'] ) ) {
			$post_meta['exclude_this_post'] = 1;
		} else {
			$post_meta['exclude_this_post'] = 0;
		}

		$meta = get_post_meta( $post_id, 'crp_post_meta', true );
		if ( $meta ) {
			$post_meta = array_merge( $meta, $post_meta );
		}

		$post_meta_filtered = array_filter( $post_meta );

		/**** Now we can start saving */
		if ( empty( $post_meta_filtered ) ) {   // Checks if all the array items are 0 or empty.
			delete_post_meta( $post_id, 'crp_post_meta' );  // Delete the post meta if no options are set.
		} else {
			update_post_meta( $post_id, 'crp_post_meta', $post_meta_filtered );
		}
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
			$manual_related_array = wp_parse_id_list( wp_unslash( $_POST['crp_manual_related'] ) );

			if ( ! empty( $manual_related_array ) ) {
				foreach ( $manual_related_array as $key => $value ) {
					if ( 'publish' !== get_post_status( $value ) ) {
						unset( $manual_related_array[ $key ] );
					}
				}
				$manual_related              = implode( ',', $manual_related_array );
				$post_meta['manual_related'] = $manual_related;
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
			$meta          = get_post_meta( $post_id, 'crp_post_meta', true );
			$meta          = $meta ? array_merge( $meta, $post_meta ) : $post_meta;
			$meta_filtered = array_filter( $meta );

			if ( empty( $meta_filtered ) ) {   // Checks if all the array items are 0 or empty.
				delete_post_meta( $post_id, 'crp_post_meta' );  // Delete the post meta if no options are set.
			} else {
				update_post_meta( $post_id, 'crp_post_meta', $meta_filtered );
			}
		}

		wp_send_json_success();
	}
}

<?php
/**
 * Contextual Related Posts Metabox interface.
 *
 * @package Contextual_Related_Posts
 */

namespace WebberZone\Contextual_Related_Posts\Admin;

use WebberZone\Contextual_Related_Posts\Util\Cache;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Metabox class to register the metabox for the plugin.
 *
 * @since 3.5.0
 */
class Metabox {

	/**
	 * Main constructor class.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ), 10, 2 );
		add_action( 'save_post', array( $this, 'save_meta_box' ) );
		add_action( 'edit_attachment', array( $this, 'save_meta_box' ) );
		add_action( 'wp_ajax_crp_get_posts_action', array( $this, 'get_posts_action' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}

	/**
	 * Function to add meta box in Write screens of Post, Page and Custom Post Types.
	 *
	 * @since 3.5.0
	 *
	 * @param string   $post_type Post Type.
	 * @param \WP_Post $post      Post object.
	 */
	public static function add_meta_box( $post_type, $post ) {

		// If metaboxes are disabled, then exit.
		if ( ! \crp_get_option( 'show_metabox' ) ) {
			return;
		}

		// If current user isn't an admin and we're restricting metaboxes to admins only, then exit.
		if ( ! current_user_can( 'manage_options' ) && \crp_get_option( 'show_metabox_admins' ) ) {
			return;
		}

		/**
		 * Filters whether to show the Contextual Related Posts meta box.
		 *
		 * @since 3.5.0
		 *
		 * @param bool $show_meta_box Whether the Contextual Related Posts meta box should be shown. Default true.
		 */
		$show_meta_box = apply_filters( 'crp_show_meta_box', true );

		if ( ! $show_meta_box ) {
			return;
		}

		$args       = array(
			'public' => true,
		);
		$post_types = get_post_types( $args );

		/**
		 * Filter post types on which the meta box is displayed
		 *
		 * @since 2.2.0
		 *
		 * @param array    $post_types   Array of post types.
		 * @param \WP_Post $post         Post object.
		 */
		$post_types = apply_filters( 'crp_meta_box_post_types', $post_types, $post );

		if ( in_array( $post_type, $post_types, true ) ) {

			add_meta_box(
				'crp_metabox',
				'Contextual Related Posts',
				array( __CLASS__, 'call_meta_box' ),
				$post_type,
				'advanced',
				'default'
			);
		}
	}


	/**
	 * Function to call the meta box.
	 *
	 * @since 3.5.0
	 */
	public static function call_meta_box() {
		global $post;

		/**** Add an nonce field so we can check for it later. */
		wp_nonce_field( 'crp_meta_box', 'crp_meta_box_nonce' );

		// Get the thumbnail settings. The name of the meta key is defined in thumb_meta parameter of the CRP Settings array.
		$thumb_meta = get_post_meta( $post->ID, crp_get_option( 'thumb_meta' ), true );
		$value      = ( $thumb_meta ) ? $thumb_meta : '';

		// Get related posts specific meta.
		$post_meta = get_post_meta( $post->ID, 'crp_post_meta', true );

		// Disable display option.
		$disable_here = isset( $post_meta['crp_disable_here'] ) ? $post_meta['crp_disable_here'] : 0;

		// Exclude this post.
		$exclude_this_post = isset( $post_meta['exclude_this_post'] ) ? $post_meta['exclude_this_post'] : 0;

		// Manual related.
		$manual_related       = isset( $post_meta['manual_related'] ) ? $post_meta['manual_related'] : '';
		$manual_related_array = array_map( 'absint', explode( ',', $manual_related ) );

		// Exclude post IDs.
		$exclude_post_ids       = isset( $post_meta['exclude_post_ids'] ) ? $post_meta['exclude_post_ids'] : '';
		$exclude_post_ids_array = array_map( 'absint', explode( ',', $exclude_post_ids ) );

		// Keyword - word or phrase.
		$keyword = isset( $post_meta['keyword'] ) ? $post_meta['keyword'] : '';

		// Exclude terms.
		$exclude_words = isset( $post_meta['exclude_words'] ) ? $post_meta['exclude_words'] : '';

		/**
		 * Filter the relevance of manual related posts.
		 *
		 * @since 3.6.0
		 *
		 * @param int $manual_related_relevance Search for related posts using relevance or not. Default 1.
		 */
		$manual_related_relevance = apply_filters( 'crp_meta_box_manual_related_relevance', 1 );

		?>
	<p>
		<label for="crp_disable_here"><strong><?php esc_html_e( 'Disable Related Posts display:', 'contextual-related-posts' ); ?></strong></label>
		<input type="checkbox" id="crp_disable_here" name="crp_disable_here" <?php checked( 1, $disable_here, true ); ?> />
		<br />
		<em><?php esc_html_e( 'If this is checked, then Contextual Related Posts will not automatically insert the related posts at the end of post content.', 'contextual-related-posts' ); ?></em>
	</p>

	<p>
		<label for="crp_exclude_this_post"><strong><?php esc_html_e( 'Exclude this post from the related posts list:', 'contextual-related-posts' ); ?></strong></label>
		<input type="checkbox" id="crp_exclude_this_post" name="crp_exclude_this_post" <?php checked( 1, $exclude_this_post, true ); ?> />
		<br />
		<em><?php esc_html_e( 'If this is checked, then this post will be excluded from the popular posts list.', 'contextual-related-posts' ); ?></em>
	</p>

	<p>
		<label for="keyword"><strong><?php esc_html_e( 'Keyword:', 'contextual-related-posts' ); ?></strong></label>
		<textarea class="large-text" cols="50" rows="5" id="crp_keyword" name="crp_keyword"><?php echo esc_textarea( stripslashes( $keyword ) ); ?></textarea>
		<em><?php esc_html_e( 'Enter either a word or a phrase that will be used to find related posts. If entered, the plugin will continue to search the `post_title` and `post_content` fields but will use this keyword instead of the values of the title and content of this post.', 'contextual-related-posts' ); ?></em>
	</p>

	<p>
		<label for="exclude_words"><strong><?php esc_html_e( 'Exclude terms:', 'contextual-related-posts' ); ?></strong></label>
		<textarea class="large-text" cols="50" rows="5" id="crp_exclude_words" name="crp_exclude_words"><?php echo esc_textarea( stripslashes( $exclude_words ) ); ?></textarea>
		<em><?php esc_html_e( "Enter a comma-separated list of terms. If a related post's title or content contains any of these terms then it will be excluded from the results.", 'contextual-related-posts' ); ?></em>
	</p>

	<p>
		<label for="manual_related"><strong><?php esc_html_e( 'Manual related posts:', 'contextual-related-posts' ); ?></strong></label>
		<input type="text" id="crp-manual-related" name="manual-related-posts" value="" class="widefat" placeholder="<?php esc_attr_e( 'Start typing to find related posts', 'contextual-related-posts' ); ?>" data-wp-relevance="<?php echo absint( $manual_related_relevance ); ?>" />
		<input type="hidden" id="crp-manual-related-csv" name="manual_related" value="<?php echo esc_attr( $manual_related ); ?>" class="widefat" />
	</p>
	<ul id="crp-post-list">
		<?php
		if ( ! empty( $manual_related ) ) {
			foreach ( $manual_related_array as $manual_related_post ) {
				printf(
					'<li class="widefat post-%1$d"><span class="crp-drag-handle dashicons dashicons-menu" title="%3$s"></span><button class="ntdelbutton button-link" type="button"></button> %2$s (%1$d)</li>',
					absint( $manual_related_post ),
					esc_html( get_the_title( $manual_related_post ) ),
					esc_attr__( 'Drag to reorder', 'contextual-related-posts' )
				);
			}
		}
		?>
	</ul>
	<p>
		<label for="exclude_post_ids"><strong><?php esc_html_e( 'Exclude post IDs:', 'contextual-related-posts' ); ?></strong></label>
		<input type="text" id="exclude_post_ids" name="exclude_post_ids" value="<?php echo esc_attr( $exclude_post_ids ); ?>" class="widefat" />
		<em><?php esc_html_e( 'Comma separated list of post, page or custom post type IDs. e.g. 188,320,500.', 'contextual-related-posts' ); ?></em>
	</p>

		<?php if ( ! empty( $exclude_post_ids ) ) { ?>

		<strong><?php esc_html_e( 'Excluded posts:', 'contextual-related-posts' ); ?></strong>
		<ol>
			<?php
			foreach ( $exclude_post_ids_array as $exclude_post_ids_post ) {

				echo '<li>';

				$title = get_the_title( $exclude_post_ids_post );
				echo '<a href="' . esc_url( get_permalink( $exclude_post_ids_post ) ) . '" target="_blank" title="' . esc_attr( $title ) . '">' . esc_attr( $title ) . '</a>. ';
				printf(
				/* translators: Post type name */
					esc_html__( 'This post type is: %s', 'contextual-related-posts' ),
					'<em>' . esc_html( get_post_type( $exclude_post_ids_post ) ) . '</em>'
				);

				echo '</li>';
			}
			?>
		</ol>
	<?php } ?>

	<p>
		<label for="crp_thumb_meta"><strong><?php esc_html_e( 'Location of thumbnail', 'contextual-related-posts' ); ?>:</strong></label>
		<input type="text" id="crp_thumb_meta" name="crp_thumb_meta" value="<?php echo esc_attr( $value ); ?>" class="widefat" />
		<em><?php esc_html_e( "Enter the full URL to the image (JPG, PNG or GIF) you'd like to use. This image will be used for the post. It will be resized to the thumbnail size set under Settings &raquo; Related Posts &raquo; Output Options", 'contextual-related-posts' ); ?></em>
		<em><?php esc_html_e( 'The URL above is saved in the meta field:', 'contextual-related-posts' ); ?></em> <strong><?php echo esc_html( crp_get_option( 'thumb_meta' ) ); ?></strong>
	</p>

	<p>
		<?php if ( function_exists( 'tptn_get_option' ) ) { ?>
			<em style="color:red"><?php esc_html_e( "You have Top 10 WordPress Plugin installed. If you are trying to modify the thumbnail, then you'll need to make the same change in the Top 10 meta box on this page.", 'contextual-related-posts' ); ?></em>
		<?php } ?>
	</p>

		<?php
		if ( $thumb_meta ) {
			echo '<img src="' . esc_attr( $value ) . '" style="max-width:100%" />';
		}
		?>

		<?php
		/**
		 * Action triggered when displaying Contextual Related Posts meta box
		 *
		 * @since   2.2
		 *
		 * @param   object  $post   Post object
		 */
		do_action( 'crp_call_meta_box', $post );
	}


	/**
	 * Function to save the meta box.
	 *
	 * @since 3.5.0
	 *
	 * @param int $post_id Post ID.
	 */
	public static function save_meta_box( $post_id ) {

		$post_meta = array();

		// Bail if we're doing an auto save.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// If our nonce isn't there, or we can't verify it, bail.
		if ( ! isset( $_POST['crp_meta_box_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['crp_meta_box_nonce'] ), 'crp_meta_box' ) ) { // Input var okay.
			return;
		}

		// If our current user can't edit this post, bail.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Update the thumbnail URL.
		if ( isset( $_POST['crp_thumb_meta'] ) ) {
			$thumb_meta = empty( $_POST['crp_thumb_meta'] ) ? '' : sanitize_text_field( wp_unslash( $_POST['crp_thumb_meta'] ) ); // Input var okay.
		}

		if ( ! empty( $thumb_meta ) ) {
			update_post_meta( $post_id, crp_get_option( 'thumb_meta' ), $thumb_meta );
		} else {
			delete_post_meta( $post_id, crp_get_option( 'thumb_meta' ) );
		}

		// Disable posts.
		if ( isset( $_POST['crp_disable_here'] ) ) {
			$post_meta['crp_disable_here'] = 1;
		} else {
			$post_meta['crp_disable_here'] = 0;
		}

		if ( isset( $_POST['crp_exclude_this_post'] ) ) {
			$post_meta['exclude_this_post'] = 1;
		} else {
			$post_meta['exclude_this_post'] = 0;
		}

		if ( isset( $_POST['crp_keyword'] ) ) {
			$post_meta['keyword'] = sanitize_text_field( wp_unslash( $_POST['crp_keyword'] ) );
		}

		if ( isset( $_POST['crp_exclude_words'] ) ) {
			$post_meta['exclude_words'] = implode( ',', array_map( 'trim', explode( ',', sanitize_text_field( wp_unslash( $_POST['crp_exclude_words'] ) ) ) ) );
		}

		// Save Manual related posts.
		if ( isset( $_POST['manual_related'] ) ) {

			$manual_related_array = array_map( 'absint', explode( ',', sanitize_text_field( wp_unslash( $_POST['manual_related'] ) ) ) );

			foreach ( $manual_related_array as $key => $value ) {
				if ( 'publish' !== get_post_status( $value ) ) {
					unset( $manual_related_array[ $key ] );
				}
			}
			$post_meta['manual_related'] = implode( ',', $manual_related_array );
		}

		// Save Manual related posts.
		if ( isset( $_POST['exclude_post_ids'] ) ) {

			$exclude_post_ids_array = array_map( 'absint', explode( ',', sanitize_text_field( wp_unslash( $_POST['exclude_post_ids'] ) ) ) );

			foreach ( $exclude_post_ids_array as $key => $value ) {
				if ( 'publish' !== get_post_status( $value ) ) {
					unset( $exclude_post_ids_array[ $key ] );
				}
			}
			$post_meta['exclude_post_ids'] = implode( ',', $exclude_post_ids_array );
		}

		/**
		 * Filter the CRP Post meta variable which contains post-specific settings
		 *
		 * @since   2.2.0
		 *
		 * @param   array   $post_meta  CRP post-specific settings
		 * @param   int $post_id    Post ID
		 */
		$post_meta = apply_filters( 'crp_post_meta', $post_meta, $post_id );

		$post_meta_filtered = array_filter( $post_meta );

		/**** Now we can start saving */
		if ( empty( $post_meta_filtered ) ) {   // Checks if all the array items are 0 or empty.
			delete_post_meta( $post_id, 'crp_post_meta' );  // Delete the post meta if no options are set.
		} else {
			update_post_meta( $post_id, 'crp_post_meta', $post_meta_filtered );
		}

		// Clear cache of current post.
		Cache::delete_by_post_id( $post_id );

		/**
		 * Action triggered when saving Contextual Related Posts meta box settings
		 *
		 * @since   2.2
		 *
		 * @param   int $post_id    Post ID
		 */
		do_action( 'crp_save_meta_box', $post_id );
	}

	/**
	 * Get posts based on the search term.
	 *
	 * @since 3.5.0
	 */
	public static function get_posts_action() {

		// Check the nonce.
		check_ajax_referer( 'crp_get_posts_nonce', 'crp_get_posts_nonce' );

		$search_term      = isset( $_POST['search_term'] ) ? sanitize_text_field( wp_unslash( $_POST['search_term'] ) ) : '';
		$postid           = isset( $_POST['postid'] ) ? absint( $_POST['postid'] ) : 0;
		$exclude_post_ids = isset( $_POST['exclude_post_ids'] ) ? wp_parse_id_list( wp_unslash( $_POST['exclude_post_ids'] ) ) : array();
		$relevance        = isset( $_POST['relevance'] ) ? (bool) $_POST['relevance'] : true;

		if ( empty( $search_term ) || empty( $postid ) ) {
			wp_send_json_error();
		}

		if ( ! $relevance ) {
			$args = array(
				'post_type'      => 'post',
				'post_status'    => 'publish',
				'posts_per_page' => 7,
				's'              => $search_term,
				'post__not_in'   => array_merge( array( $postid ), $exclude_post_ids ),
			);
			if ( is_numeric( $search_term ) ) {
				$args['p'] = absint( $search_term );
				unset( $args['s'] );
			}
			$posts = get_posts( $args );
		} else {
			$args = array(
				'postid'           => $postid,
				'posts_per_page'   => 7,
				'keyword'          => $search_term,
				'exclude_post_ids' => $exclude_post_ids,
				'manual_related'   => 0,
				'include_words'    => $search_term,
				'match_content'    => false,
			);
			if ( is_numeric( $search_term ) ) {
				$args['include_post_ids'] = array( $search_term );
			}
			$posts = \get_crp_posts( $args );
		}

		$result = array();
		foreach ( $posts as $post ) {
			$result[] = array(
				'id'    => $post->ID,
				'title' => sprintf( '%1$s (%2$s)', $post->post_title, $post->ID ),
			);
		}

		echo wp_json_encode( $result );
		wp_die();
	}

	/**
	 * Enqueue admin scripts for metabox. Only on edit.php screens when the metabox is loaded.
	 *
	 * @since 3.4.0
	 */
	public static function admin_enqueue_scripts() {

		$file_prefix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		// If metaboxes are disabled, then exit.
		if ( ! crp_get_option( 'show_metabox' ) ) {
			return;
		}

		// If current user isn't an admin and we're restricting metaboxes to admins only, then exit.
		if ( ! current_user_can( 'manage_options' ) && crp_get_option( 'show_metabox_admins' ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( 'post' === $screen->base ) {
			wp_enqueue_script(
				'crp-admin-metabox',
				WZ_CRP_PLUGIN_URL . "includes/admin/js/metabox{$file_prefix}.js",
				array( 'jquery', 'jquery-ui-autocomplete', 'jquery-ui-sortable' ),
				WZ_CRP_VERSION,
				true
			);
			wp_localize_script(
				'crp-admin-metabox',
				'crp_metabox',
				array(
					'nonce' => wp_create_nonce( 'crp_get_posts_nonce' ),
				)
			);
			wp_enqueue_style(
				'crp-admin-styles',
				WZ_CRP_PLUGIN_URL . "includes/admin/css/admin-styles{$file_prefix}.css",
				array( 'dashicons' ),
				WZ_CRP_VERSION
			);
			wp_enqueue_script(
				'wz-taxonomy-suggest-js',
				WZ_CRP_PLUGIN_URL . "includes/admin/settings/js/taxonomy-suggest{$file_prefix}.js",
				array( 'jquery' ),
				WZ_CRP_VERSION,
				true
			);
		}
	}
}
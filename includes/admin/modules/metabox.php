<?php
/**
 * Contextual Related Posts Metabox interface.
 *
 * @package   Contextual_Related_Posts
 */

/**** If this file is called directly, abort. ****/
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Function to add meta box in Write screens of Post, Page and Custom Post Types.
 *
 * @since   1.9.1
 *
 * @param text   $post_type Post Type.
 * @param object $post Post object.
 */
function crp_add_meta_box( $post_type, $post ) {

	// If metaboxes are disabled, then exit.
	if ( ! crp_get_option( 'show_metabox' ) ) {
		return;
	}

	// If current user isn't an admin and we're restricting metaboxes to admins only, then exit.
	if ( ! current_user_can( 'manage_options' ) && crp_get_option( 'show_metabox_admins' ) ) {
		return;
	}

	$args       = array(
		'public' => true,
	);
	$post_types = get_post_types( $args );

	/**
	 * Filter post types on which the meta box is displayed
	 *
	 * @since   2.2.0
	 *
	 * @param array $post_types Array of post types.
	 * @param array $post_types Post object.
	 */
	$post_types = apply_filters( 'crp_meta_box_post_types', $post_types, $post );

	if ( in_array( $post_type, $post_types, true ) ) {

		add_meta_box(
			'crp_metabox',
			'Contextual Related Posts',
			'crp_call_meta_box',
			$post_type,
			'advanced',
			'default'
		);
	}
}
add_action( 'add_meta_boxes', 'crp_add_meta_box', 10, 2 );


/**
 * Function to call the meta box.
 *
 * @since   1.9.1
 */
function crp_call_meta_box() {
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
	$manual_related_array = explode( ',', $manual_related );

	// Exclude post IDs.
	$exclude_post_ids       = isset( $post_meta['exclude_post_ids'] ) ? $post_meta['exclude_post_ids'] : '';
	$exclude_post_ids_array = explode( ',', $exclude_post_ids );

	// Keyword - word or phrase.
	$keyword = isset( $post_meta['keyword'] ) ? $post_meta['keyword'] : '';

	// Exclude terms.
	$exclude_words = isset( $post_meta['exclude_words'] ) ? $post_meta['exclude_words'] : '';

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
		<input type="text" id="manual_related" name="manual_related" value="<?php echo esc_attr( $manual_related ); ?>" style="width:100%" />
		<em><?php esc_html_e( 'Comma separated list of post, page or custom post type IDs. e.g. 188,320,500. These will be given preference over the related posts generated by the plugin.', 'contextual-related-posts' ); ?></em>
		<em><?php esc_html_e( 'Once you enter the list above and save this page, the plugin will display the titles of the posts below for your reference. Only IDs corresponding to published posts or custom post types will be retained.', 'contextual-related-posts' ); ?></em>
	</p>

	<?php if ( ! empty( $manual_related ) ) { ?>

		<strong><?php esc_html_e( 'Manual related posts:', 'contextual-related-posts' ); ?></strong>
		<ol>
		<?php
		foreach ( $manual_related_array as $manual_related_post ) {

			echo '<li>';

			$title = get_the_title( $manual_related_post );
			echo '<a href="' . esc_url( get_permalink( $manual_related_post ) ) . '" target="_blank" title="' . esc_attr( $title ) . '">' . esc_attr( $title ) . '</a>. ';
			printf(
				/* translators: Post type name */
				esc_html__( 'This post type is: %s', 'contextual-related-posts' ),
				'<em>' . esc_html( get_post_type( $manual_related_post ) ) . '</em>'
			);

			echo '</li>';
		}
		?>
		</ol>
	<?php } ?>

	<p>
		<label for="exclude_post_ids"><strong><?php esc_html_e( 'Exclude post IDs:', 'contextual-related-posts' ); ?></strong></label>
		<input type="text" id="exclude_post_ids" name="exclude_post_ids" value="<?php echo esc_attr( $exclude_post_ids ); ?>" style="width:100%" />
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
		<input type="text" id="crp_thumb_meta" name="crp_thumb_meta" value="<?php echo esc_attr( $value ); ?>" style="width:100%" />
		<em><?php esc_html_e( "Enter the full URL to the image (JPG, PNG or GIF) you'd like to use. This image will be used for the post. It will be resized to the thumbnail size set under Settings &raquo; Related Posts &raquo; Output Options", 'contextual-related-posts' ); ?></em>
		<em><?php esc_html_e( 'The URL above is saved in the meta field:', 'contextual-related-posts' ); ?></em> <strong><?php echo esc_html( crp_get_option( 'thumb_meta' ) ); ?></strong>
	</p>

	<p>
		<?php if ( function_exists( 'tptn_add_viewed_count' ) ) { ?>
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
 * @since   1.9.1
 *
 * @param mixed $post_id Post ID.
 */
function crp_save_meta_box( $post_id ) {

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

	/**
	 * Action triggered when saving Contextual Related Posts meta box settings
	 *
	 * @since   2.2
	 *
	 * @param   int $post_id    Post ID
	 */
	do_action( 'crp_save_meta_box', $post_id );
}
add_action( 'save_post', 'crp_save_meta_box' );
add_action( 'edit_attachment', 'crp_save_meta_box' );

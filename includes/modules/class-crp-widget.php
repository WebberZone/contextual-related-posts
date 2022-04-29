<?php
/**
 * Widget class.
 *
 * @package   Contextual_Related_Posts
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'CRP_Widget' ) ) :
	/**
	 * Create a WordPress Widget for CRP.
	 *
	 * @since 1.9
	 *
	 * @extends WP_Widget
	 */
	class CRP_Widget extends WP_Widget {

		/**
		 * Register widget with WordPress.
		 */
		public function __construct() {
			parent::__construct(
				'widget_crp',
				__( 'Related Posts [CRP]', 'contextual-related-posts' ),
				array(
					'description'                 => __( 'Display Related Posts', 'contextual-related-posts' ),
					'customize_selective_refresh' => true,
				)
			);
		}

		/**
		 * Back-end widget form.
		 *
		 * @see WP_Widget::form()
		 *
		 * @param   array $instance   Previously saved values from database.
		 */
		public function form( $instance ) {
			$title           = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
			$limit           = isset( $instance['limit'] ) ? esc_attr( $instance['limit'] ) : '';
			$offset          = isset( $instance['offset'] ) ? esc_attr( $instance['offset'] ) : '';
			$show_excerpt    = isset( $instance['show_excerpt'] ) ? esc_attr( $instance['show_excerpt'] ) : '';
			$show_author     = isset( $instance['show_author'] ) ? esc_attr( $instance['show_author'] ) : '';
			$show_date       = isset( $instance['show_date'] ) ? esc_attr( $instance['show_date'] ) : '';
			$post_thumb_op   = isset( $instance['post_thumb_op'] ) ? esc_attr( $instance['post_thumb_op'] ) : '';
			$thumb_height    = isset( $instance['thumb_height'] ) ? esc_attr( $instance['thumb_height'] ) : '';
			$thumb_width     = isset( $instance['thumb_width'] ) ? esc_attr( $instance['thumb_width'] ) : '';
			$ordering        = isset( $instance['ordering'] ) ? esc_attr( $instance['ordering'] ) : '';
			$random_order    = isset( $instance['random_order'] ) ? esc_attr( $instance['random_order'] ) : '';
			$include_cat_ids = isset( $instance['include_cat_ids'] ) ? esc_attr( $instance['include_cat_ids'] ) : '';

			// Parse the Post types.
			$post_types = array();

			// If post_types is empty or contains a query string then use parse_str else consider it comma-separated.
			if ( ! empty( $instance['post_types'] ) && false === strpos( $instance['post_types'], '=' ) ) {
				$post_types = explode( ',', $instance['post_types'] );
			} elseif ( ! empty( $instance['post_types'] ) ) {
				parse_str( $instance['post_types'], $post_types );  // Save post types in $post_types variable.
			}

			$wp_post_types   = get_post_types(
				array(
					'public' => true,
				)
			);
			$posts_types_inc = array_intersect( $wp_post_types, $post_types );

			// Get the different ordering settings.
			$orderings = crp_get_orderings();

			?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
				<?php esc_html_e( 'Title', 'contextual-related-posts' ); ?>: <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
				</label>
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>">
				<?php esc_html_e( 'No. of posts', 'contextual-related-posts' ); ?>: <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'limit' ) ); ?>" type="text" value="<?php echo esc_attr( $limit ); ?>" />
				</label>
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'offset' ) ); ?>">
				<?php esc_html_e( 'Offset', 'contextual-related-posts' ); ?>: <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'offset' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'offset' ) ); ?>" type="text" value="<?php echo esc_attr( $offset ); ?>" />
				</label>
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'show_excerpt' ) ); ?>">
				<input id="<?php echo esc_attr( $this->get_field_id( 'show_excerpt' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_excerpt' ) ); ?>" type="checkbox" <?php checked( true, $show_excerpt, true ); ?> /> <?php esc_html_e( ' Show excerpt?', 'contextual-related-posts' ); ?>
				</label>
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'show_author' ) ); ?>">
				<input id="<?php echo esc_attr( $this->get_field_id( 'show_author' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_author' ) ); ?>" type="checkbox" <?php checked( true, $show_author, true ); ?> /> <?php esc_html_e( ' Show author?', 'contextual-related-posts' ); ?>
				</label>
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'show_date' ) ); ?>">
				<input id="<?php echo esc_attr( $this->get_field_id( 'show_date' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_date' ) ); ?>" type="checkbox" <?php checked( true, $show_date, true ); ?> /> <?php esc_html_e( ' Show date?', 'contextual-related-posts' ); ?>
				</label>
			</p>
			<p>
				<?php esc_html_e( 'Thumbnail options', 'contextual-related-posts' ); ?>: <br />
				<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'post_thumb_op' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_thumb_op' ) ); ?>">
					<option value="inline" <?php selected( 'inline', $post_thumb_op, true ); ?>><?php esc_html_e( 'Thumbnails inline, before title', 'contextual-related-posts' ); ?></option>
					<option value="after" <?php selected( 'after', $post_thumb_op, true ); ?>><?php esc_html_e( 'Thumbnails inline, after title', 'contextual-related-posts' ); ?></option>
					<option value="thumbs_only" <?php selected( 'thumbs_only', $post_thumb_op, true ); ?>><?php esc_html_e( 'Only thumbnails, no text', 'contextual-related-posts' ); ?></option>
					<option value="text_only" <?php selected( 'text_only', $post_thumb_op, true ); ?>><?php esc_html_e( 'No thumbnails, only text.', 'contextual-related-posts' ); ?></option>
				</select>
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'thumb_height' ) ); ?>">
				<?php esc_html_e( 'Thumbnail height', 'contextual-related-posts' ); ?>: <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'thumb_height' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'thumb_height' ) ); ?>" type="text" value="<?php echo esc_attr( $thumb_height ); ?>" />
				</label>
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'thumb_width' ) ); ?>">
				<?php esc_html_e( 'Thumbnail width', 'contextual-related-posts' ); ?>: <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'thumb_width' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'thumb_width' ) ); ?>" type="text" value="<?php echo esc_attr( $thumb_width ); ?>" />
				</label>
			</p>
			<p><?php esc_html_e( 'Order posts', 'contextual-related-posts' ); ?>:<br />

				<?php foreach ( $orderings as $order => $label ) { ?>

					<label>
						<input id="<?php echo esc_attr( $this->get_field_id( 'ordering' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'ordering' ) ); ?>" type="radio" value="<?php echo esc_attr( $order ); ?>" <?php checked( $order === $ordering ); ?> />
						<?php echo esc_attr( $label ); ?>
					</label>
					<br />

				<?php } ?>
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'random_order' ) ); ?>">
				<input id="<?php echo esc_attr( $this->get_field_id( 'random_order' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'random_order' ) ); ?>" type="checkbox" <?php checked( true, $random_order, true ); ?> /> <?php esc_html_e( ' Randomize posts', 'contextual-related-posts' ); ?>
				</label>
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'include_cat_ids' ) ); ?>">
					<?php esc_html_e( 'Only from categories (comma-separated list of term taxonomy IDs)', 'contextual-related-posts' ); ?>:
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'include_cat_ids' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'include_cat_ids' ) ); ?>" type="text" value="<?php echo esc_attr( $include_cat_ids ); ?>" />
				</label>
			</p>
			<p><?php esc_html_e( 'Post types to include', 'contextual-related-posts' ); ?>:<br />

				<?php foreach ( $wp_post_types as $wp_post_type ) { ?>

					<label>
						<input id="<?php echo esc_attr( $this->get_field_id( 'post_types' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_types' ) ); ?>[]" type="checkbox" value="<?php echo esc_attr( $wp_post_type ); ?>" <?php checked( true, in_array( $wp_post_type, $posts_types_inc, true ) ); ?> />
						<?php echo esc_attr( $wp_post_type ); ?>
					</label>
					<br />

				<?php } ?>
			</p>

			<?php
			/**
			 * Fires after Contextual Related Posts widget options.
			 *
			 * @since 2.1.0
			 *
			 * @param   array   $instance   Widget options array
			 */
			do_action( 'crp_widget_options_after', $instance );
			?>

			<?php
		} //ending form creation

		/**
		 * Sanitize widget form values as they are saved.
		 *
		 * @see WP_Widget::update()
		 *
		 * @param   array $new_instance Values just sent to be saved.
		 * @param   array $old_instance Previously saved values from database.
		 *
		 * @return  array   Updated safe values to be saved.
		 */
		public function update( $new_instance, $old_instance ) {
			$instance                  = $old_instance;
			$instance                  = array();
			$instance['title']         = ( ! empty( $new_instance['title'] ) ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
			$instance['limit']         = ( ! empty( $new_instance['limit'] ) ) ? intval( $new_instance['limit'] ) : '';
			$instance['post_thumb_op'] = $new_instance['post_thumb_op'];
			$instance['thumb_width']   = ( ! empty( $new_instance['thumb_width'] ) ) ? intval( $new_instance['thumb_width'] ) : '';
			$instance['thumb_height']  = ( ! empty( $new_instance['thumb_height'] ) ) ? intval( $new_instance['thumb_height'] ) : '';
			$instance['show_excerpt']  = isset( $new_instance['show_excerpt'] ) ? (bool) $new_instance['show_excerpt'] : false;
			$instance['show_author']   = isset( $new_instance['show_author'] ) ? (bool) $new_instance['show_author'] : false;
			$instance['show_date']     = isset( $new_instance['show_date'] ) ? (bool) $new_instance['show_date'] : false;
			$instance['offset']        = ( ! empty( $new_instance['offset'] ) ) ? intval( $new_instance['offset'] ) : '';
			$instance['ordering']      = isset( $new_instance['ordering'] ) ? $new_instance['ordering'] : '';
			$instance['random_order']  = isset( $new_instance['random_order'] ) ? (bool) $new_instance['show_date'] : false;

			// Process post types to be selected.
			$wp_post_types          = get_post_types(
				array(
					'public' => true,
				)
			);
			$post_types             = isset( $new_instance['post_types'] ) ? $new_instance['post_types'] : array();
			$post_types             = array_intersect( $wp_post_types, (array) $post_types );
			$instance['post_types'] = implode( ',', $post_types );

			// Save include_categories.
			$include_categories = wp_parse_id_list( $new_instance['include_cat_ids'] );

			foreach ( $include_categories as $cat_name ) {
				$cat = get_term_by( 'term_taxonomy_id', $cat_name );

				if ( isset( $cat->term_taxonomy_id ) ) {
					$include_cat_ids[]   = $cat->term_taxonomy_id;
					$include_cat_names[] = $cat->name;
				}
			}
			$instance['include_cat_ids']    = isset( $include_cat_ids ) ? join( ',', $include_cat_ids ) : '';
			$instance['include_categories'] = isset( $include_cat_names ) ? crp_str_putcsv( $include_cat_names ) : '';

			delete_post_meta_by_key( 'crp_related_posts_widget' ); // Delete the cache.

			/**
			 * Filters Update widget options array.
			 *
			 * @since 2.0.0
			 *
			 * @param array $instance Widget options array
			 * @param array $new_instance Values just sent to be saved.
			 * @param array $old_instance Previously saved values from database.
			 */
			return apply_filters( 'crp_widget_options_update', $instance, $new_instance, $old_instance );
		} //ending update

		/**
		 * Front-end display of widget.
		 *
		 * @see WP_Widget::widget()
		 *
		 * @param   array $args   Widget arguments.
		 * @param   array $instance   Saved values from database.
		 */
		public function widget( $args, $instance ) {
			global $post;

			// Get the post meta.
			if ( isset( $post ) ) {
				$crp_post_meta = get_post_meta( $post->ID, 'crp_post_meta', true );

				if ( isset( $crp_post_meta['disable_here'] ) && $crp_post_meta['disable_here'] ) {
					return;
				}
			}

			// If post_types is empty or contains a query string then use parse_str else consider it comma-separated.
			if ( crp_get_option( 'exclude_on_post_types' ) && false === strpos( crp_get_option( 'exclude_on_post_types' ), '=' ) ) {
				$exclude_on_post_types = explode( ',', crp_get_option( 'exclude_on_post_types' ) );
			} else {
				parse_str( crp_get_option( 'exclude_on_post_types' ), $exclude_on_post_types );    // Save post types in $exclude_on_post_types variable.
			}

			if ( is_object( $post ) && ( in_array( $post->post_type, $exclude_on_post_types, true ) ) ) {
				return 0;   // Exit without adding related posts.
			}

			$exclude_on_post_ids = explode( ',', crp_get_option( 'exclude_on_post_ids' ) );

			if ( ( ( is_single() ) && ( ! is_single( $exclude_on_post_ids ) ) ) || ( ( is_page() ) && ( ! is_page( $exclude_on_post_ids ) ) ) ) {

				$title = empty( $instance['title'] ) ? wp_strip_all_tags( str_replace( '%postname%', $post->post_title, crp_get_option( 'title' ) ) ) : $instance['title'];

				/**
				 * Filters the widget title.
				 *
				 * @since 2.6.0
				 *
				 * @param string $title    The widget title. Default 'Pages'.
				 * @param array  $instance Array of settings for the current widget.
				 * @param mixed  $id_base  The widget ID.
				 */
				$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

				$limit = isset( $instance['limit'] ) ? $instance['limit'] : crp_get_option( 'limit' );
				if ( empty( $limit ) ) {
					$limit = crp_get_option( 'limit' );
				}
				$offset = isset( $instance['offset'] ) ? $instance['offset'] : 0;

				$post_thumb_op   = isset( $instance['post_thumb_op'] ) ? esc_attr( $instance['post_thumb_op'] ) : 'text_only';
				$thumb_height    = isset( $instance['thumb_height'] ) && ! empty( $instance['thumb_height'] ) ? esc_attr( $instance['thumb_height'] ) : crp_get_option( 'thumb_height' );
				$thumb_width     = isset( $instance['thumb_width'] ) && ! empty( $instance['thumb_width'] ) ? esc_attr( $instance['thumb_width'] ) : crp_get_option( 'thumb_width' );
				$show_excerpt    = isset( $instance['show_excerpt'] ) ? esc_attr( $instance['show_excerpt'] ) : '';
				$show_author     = isset( $instance['show_author'] ) ? esc_attr( $instance['show_author'] ) : '';
				$show_date       = isset( $instance['show_date'] ) ? esc_attr( $instance['show_date'] ) : '';
				$ordering        = isset( $instance['ordering'] ) ? esc_attr( $instance['ordering'] ) : '';
				$random_order    = isset( $instance['random_order'] ) ? esc_attr( $instance['random_order'] ) : '';
				$post_types      = isset( $instance['post_types'] ) && ! empty( $instance['post_types'] ) ? $instance['post_types'] : crp_get_option( 'post_types' );
				$include_cat_ids = isset( $instance['include_cat_ids'] ) ? esc_attr( $instance['include_cat_ids'] ) : '';

				$arguments = array(
					'is_widget'       => 1,
					'instance_id'     => $this->number,
					'limit'           => $limit,
					'offset'          => $offset,
					'show_excerpt'    => $show_excerpt,
					'show_author'     => $show_author,
					'show_date'       => $show_date,
					'post_thumb_op'   => $post_thumb_op,
					'thumb_height'    => $thumb_height,
					'thumb_width'     => $thumb_width,
					'ordering'        => $ordering,
					'random_order'    => $random_order,
					'post_types'      => $post_types,
					'include_cat_ids' => $include_cat_ids,
				);

				/**
				 * Filters arguments passed to get_crp for the widget.
				 *
				 * @since 2.0.0
				 *
				 * @param array $arguments CRP widget options array.
				 * @param array $args      Widget arguments.
				 * @param array $instance  Saved values from database.
				 * @param mixed $id_base   The widget ID.
				 */
				$arguments = apply_filters( 'crp_widget_options', $arguments, $args, $instance, $this->id_base );

				$related_posts = get_crp( $arguments );
				if ( ! empty( wp_strip_all_tags( $related_posts, true ) ) ) {
					$output  = $args['before_widget'];
					$output .= $args['before_title'] . $title . $args['after_title'];
					$output .= $related_posts;
					$output .= $args['after_widget'];

					echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
			}// End if.
		} // Ending function widget.
	}


	/**
	 * Initialise the widget.
	 *
	 * @since 1.9.1
	 */
	function register_crp_widget() {
		register_widget( 'CRP_Widget' );
	}
	add_action( 'widgets_init', 'register_crp_widget' );

endif;

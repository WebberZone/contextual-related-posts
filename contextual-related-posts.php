<?php
/**
 * Contextual Related Posts.
 *
 * Contextual Related Posts is the best related posts plugin for WordPress that
 * allows you to display a list of related posts on your website and in your feed.
 *
 * @package   Contextual_Related_Posts
 * @author    Ajay D'Souza <me@ajaydsouza.com>
 * @license   GPL-2.0+
 * @link      http://ajaydsouza.com
 * @copyright 2009-2014 Ajay D'Souza
 *
 * @wordpress-plugin
 * Plugin Name:	Contextual Related Posts
 * Plugin URI:	http://ajaydsouza.com/wordpress/plugins/contextual-related-posts/
 * Description:	Display a set of related posts on your website or in your feed. Increase reader retention and reduce bounce rates
 * Version: 	2.0.1
 * Author: 		Ajay D'Souza
 * Author URI: 	http://ajaydsouza.com
 * Text Domain:	crp
 * License: 	GPL-2.0+
 * License URI:	http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:	/languages
 * GitHub Plugin URI: https://github.com/ajaydsouza/contextual-related-posts/
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Holds the text domain.
 *
 * @since	1.4
 */
define( 'CRP_LOCAL_NAME', 'crp' );

/**
 * Holds the filesystem directory path (with trailing slash) for CRP
 *
 * @since	1.2
 *
 * @var string
 */
$crp_path = plugin_dir_path( __FILE__ );

/**
 * Holds the URL for CRP
 *
 * @since	1.2
 *
 * @var string
 */
$crp_url = plugins_url() . '/' . plugin_basename( dirname( __FILE__ ) );

/**
 * Global variable holding the current settings for Contextual Related Posts
 *
 * @since	1.8.10
 *
 * @var array
 */
global $crp_settings;
$crp_settings = crp_read_options();


/**
 * Initialises text domain for l10n.
 *
 * @since 1.9
 */
function ald_crp_lang_init() {
	load_plugin_textdomain( CRP_LOCAL_NAME, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'ald_crp_lang_init' );


/**
 * Main function to generate the related posts output
 *
 * @since 1.0.1
 *
 * @param	array	$args	Parameters in a query string format
 * @return	string			HTML formatted list of related posts
 */
function ald_crp( $args = array() ) {
	global $wpdb, $post, $single, $crp_settings;

	$defaults = array(
		'is_widget' => FALSE,
		'echo' => TRUE,
	);
	$defaults = array_merge( $defaults, $crp_settings );

	// Parse incomming $args into an array and merge it with $defaults
	$args = wp_parse_args( $args, $defaults );

	// Declare each item in $args as its own variable i.e. $type, $before.
	extract( $args, EXTR_SKIP );

	//Support caching to speed up retrieval
	if ( ! empty( $cache ) ) {
		$output = ( $is_widget ) ? get_post_meta( $post->ID, 'crp_related_posts_widget', true ) : get_post_meta( $post->ID, 'crp_related_posts', true );
		if ( $output ) {
			return $output;
		}
	}

	$exclude_categories = explode( ',', $exclude_categories );

	$rel_attribute = ( $link_nofollow ) ? ' rel="nofollow" ' : ' ';
	$target_attribute = ( $link_new_window ) ? ' target="_blank" ' : ' ';

	// Retrieve the list of posts
	$results = get_crp_posts_id( array_merge( $args, array(
		'postid' => $post->ID,
		'strict_limit' => TRUE,
	) ) );

	$output = ( is_singular() ) ? '<div id="crp_related" class="crp_related' . ( $is_widget ? '_widget' : '' ) . '">' : '<div class="crp_related' . ( $is_widget ? '_widget' : '' ) . '">';

	if ( $results ) {
		$loop_counter = 0;

		if ( ! $is_widget ) {
			$title = str_replace( "%postname%", $post->post_title, $title );	// Replace %postname% with the title of the current post

			/**
			 * Filter the title of the Related Posts list
			 *
			 * @since	1.9
			 *
			 * @param	string	$title	Title/heading of the Related Posts list
			 */
			$output .= apply_filters( 'crp_heading_title', $title );
		}

		/**
		 * Filter the opening tag of the related posts list
		 *
		 * @since	1.9
		 *
		 * @param	string	$before_list	Opening tag set in the Settings Page
		 */
		$output .= apply_filters( 'crp_before_list', $before_list );

		foreach ( $results as $result ) {

			/**
			 * Filter the post ID for each result. Allows a custom function to hook in and change the ID if needed.
			 *
			 * @since	1.9
			 *
			 * @param	int	$result->ID	ID of the post
			 */
			$resultid = apply_filters( 'crp_post_id', $result->ID );

			$result = get_post( $resultid );	// Let's get the Post using the ID

			/**
			 * Filter the post ID for each result. This filtered ID is passed as a parameter to fetch categories.
			 *
			 * This is useful since you might want to fetch a different set of categories for a linked post ID,
			 * typically in the case of plugins that let you set mutiple languages
			 *
			 * @since	1.9
			 *
			 * @param	int	$result->ID	ID of the post
			 */
			$resultid = apply_filters( 'crp_post_cat_id', $result->ID );

			$categorys = get_the_category( $resultid );	//Fetch categories of the plugin

			$p_in_c = false;	// Variable to check if post exists in a particular category
			foreach ( $categorys as $cat ) {	// Loop to check if post exists in excluded category
				$p_in_c = ( in_array( $cat->cat_ID, $exclude_categories ) ) ? true : false;
				if ( $p_in_c ) break;	// End loop if post found in category
			}

			if ( ! $p_in_c ) {

				/**
				 * Filter the opening tag of each list item.
				 *
				 * @since	1.9
				 *
				 * @param	string	$before_list_item	Tag before each list item. Can be defined in the Settings page.
				 * @param	object	$result	Object of the current post result
				 */
				$output .= apply_filters( 'crp_before_list_item', $before_list_item, $result );	// Pass the post object to the filter

				$title = crp_max_formatted_content( get_the_title( $result->ID ), $title_length );	// Get the post title and crop it if needed

				/**
				 * Filter the title of each list item.
				 *
				 * @since	1.9
				 *
				 * @param	string	$title	Title of the post.
				 * @param	object	$result	Object of the current post result
				 */
				$title = apply_filters( 'crp_title', $title, $result );

				if ( 'after' == $post_thumb_op ) {
					$output .= '<a href="' . get_permalink( $result->ID ) . '" ' . $rel_attribute . ' ' . $target_attribute . 'class="crp_title">' . $title . '</a>'; // Add title if post thumbnail is to be displayed after
				}
				if ( 'inline' == $post_thumb_op || 'after' == $post_thumb_op || 'thumbs_only' == $post_thumb_op ) {
					$output .= '<a href="' . get_permalink( $result->ID ) . '" ' . $rel_attribute . ' ' . $target_attribute . '>';
					$output .= crp_get_the_post_thumbnail( array(
						'postid' => $result->ID,
						'thumb_height' => $thumb_height,
						'thumb_width' => $thumb_width,
						'thumb_meta' => $thumb_meta,
						'thumb_html' => $thumb_html,
						'thumb_default' => $thumb_default,
						'thumb_default_show' => $thumb_default_show,
						'thumb_timthumb' => $thumb_timthumb,
						'thumb_timthumb_q' => $thumb_timthumb_q,
						'scan_images' => $scan_images,
						'class' => 'crp_thumb',
						'filter' => 'crp_postimage',
					) );
					$output .= '</a>';
				}
				if ( 'inline' == $post_thumb_op || 'text_only' == $post_thumb_op ) {
					$output .= '<a href="' . get_permalink( $result->ID ) . '" ' . $rel_attribute . ' ' . $target_attribute . ' class="crp_title">' . $title . '</a>'; // Add title when required by settings
				}
				if ( $show_author ) {
					$author_info = get_userdata( $result->post_author );
					$author_link = get_author_posts_url( $author_info->ID );
					$author_name = ucwords( trim( stripslashes( $author_info->display_name ) ) );

					/**
					 * Filter the author name.
					 *
					 * @since	1.9.1
					 *
					 * @param	string	$author_name	Proper name of the post author.
					 * @param	object	$author_info	WP_User object of the post author
					 */
					$author_name = apply_filters( 'crp_author_name', $author_name, $author_info );

					$crp_author .= '<span class="crp_author"> ' . __( ' by ', CRP_LOCAL_NAME ).'<a href="' . $author_link . '">' . $author_name . '</a></span> ';

					/**
					 * Filter the text with the author details.
					 *
					 * @since	2.0.0
					 *
					 * @param	string	$crp_author	Formatted string with author details and link
					 * @param	object	$author_info	WP_User object of the post author
					 */
					$crp_author = apply_filters( 'crp_author', $crp_author, $author_info);

					$output .= $crp_author;
				}
				if ( $show_date ) {
					$output .= '<span class="crp_date"> ' . mysql2date( get_option( 'date_format', 'd/m/y' ), $result->post_date ) . '</span> ';
				}
				if ( $show_excerpt ) {
					$output .= '<span class="crp_excerpt"> ' . crp_excerpt( $result->ID, $excerpt_length ) . '</span>';
				}
				$loop_counter++;

				/**
				 * Filter the closing tag of each list item.
				 *
				 * @since	1.9
				 *
				 * @param	string	$after_list_item	Tag after each list item. Can be defined in the Settings page.
				 * @param	object	$result	Object of the current post result
				 */
				$output .= apply_filters( 'crp_after_list_item', $after_list_item, $result );
			}
			if ( $loop_counter == $limit ) break;	// End loop when related posts limit is reached
		} //end of foreach loop
		if ( $show_credit ) {

			/** This filter is documented in contextual-related-posts.php */
			$output .= apply_filters( 'crp_before_list_item', $before_list_item, $result );	// Pass the post object to the filter

			$output .= sprintf( __( 'Powered by <a href="%s" rel="nofollow">Contextual Related Posts</a>', CRP_LOCAL_NAME ), esc_url( 'http://ajaydsouza.com/wordpress/plugins/contextual-related-posts/' ) );

			/** This filter is documented in contextual-related-posts.php */
			$output .= apply_filters( 'crp_after_list_item', $after_list_item, $result );

		}

		/**
		 * Filter the closing tag of the related posts list
		 *
		 * @since	1.9
		 *
		 * @param	string	$after_list	Closing tag set in the Settings Page
		 */
		$output .= apply_filters( 'crp_after_list', $after_list );

		$clearfix = '<div style="clear:both"></div>';

		/**
		 * Filter the clearfix div tag. This is included after the closing tag to clear any miscellaneous floating elements;
		 *
		 * @since	2.0.0
		 *
		 * @param	string	$clearfix	Contains: <div style="clear:both"></div>
		 */
		$output .= apply_filters( 'crp_clearfix', $clearfix );

	} else {
		$output .= ( $blank_output ) ? ' ' : '<p>' . $blank_output_text . '</p>';
	}

	if ( false === ( strpos( $output, $before_list_item ) ) ) {
		$output = '<div id="crp_related">';
		$output .= ($blank_output) ? ' ' : '<p>' . $blank_output_text . '</p>';
	}

	$output .= '</div>'; // closing div of 'crp_related'


	//Support caching to speed up retrieval
	if ( ! empty( $cache ) ) {
		if ( $is_widget ) {
			update_post_meta( $post->ID, 'crp_related_posts_widget', $output, '' );
		} else {
			update_post_meta( $post->ID, 'crp_related_posts', $output, '' );
		}
	}

	/**
	 * Filter the output
	 *
	 * @since	1.9.1
	 *
	 * @param	string	$output	Formatted list of related posts
	 * @param	array	$args	Complete set of arguments
	 */
	return apply_filters( 'ald_crp', $output, $args );
}


/**
 * Fetch related posts IDs.
 *
 * @since 1.9
 *
 * @param array $args
 * @return object $results
 */
function get_crp_posts_id( $args = array() ) {
	global $wpdb, $post, $single, $crp_settings;

	// Initialise some variables
	$fields = '';
	$where = '';
	$join = '';
	$groupby = '';
	$orderby = '';
	$limits = '';
	$match_fields = '';

	$defaults = array(
		'postid' => FALSE,
		'strict_limit' => FALSE,
	);
	$defaults = array_merge( $defaults, $crp_settings );

	// Parse incomming $args into an array and merge it with $defaults
	$args = wp_parse_args( $args, $defaults );

	// Declare each item in $args as its own variable i.e. $type, $before.
	extract( $args, EXTR_SKIP );

	$post = ( empty( $postid ) ) ? $post : get_post( $postid );

	$limit = ( $strict_limit ) ? $limit : ( $limit * 3 );

	parse_str( $post_types, $post_types );	// Save post types in $post_types variable

	// Are we matching only the title or the post content as well?
	if( $match_content ) {
		$stuff = $post->post_title . ' ' . crp_excerpt( $post->ID, $match_content_words, false );
		$match_fields = "post_title,post_content";
	} else {
		$stuff = $post->post_title;
		$match_fields = "post_title";
	}

	// Make sure the post is not from the future
	$time_difference = get_option( 'gmt_offset' );
	$now = gmdate( "Y-m-d H:i:s", ( time() + ( $time_difference * 3600 ) ) );

	// Limit the related posts by time
	$daily_range = $daily_range - 1;
	$from_date = strtotime( '-' . $daily_range . ' DAY' , strtotime( $now ) );
	$from_date = date ( 'Y-m-d H:i:s' , $from_date );

	// Create the SQL query to fetch the related posts from the database
	if ( ( is_int( $post->ID ) ) && ( '' != $stuff ) ) {

		// Fields to return
		$fields = " $wpdb->posts.ID ";

		// Create the base WHERE clause
		$where .= $wpdb->prepare( " AND MATCH (" . $match_fields . ") AGAINST ('%s') ", $stuff );	// FULLTEXT matching algorithm
		$where .= $wpdb->prepare( " AND $wpdb->posts.post_date < '%s' ", $now );		// Show posts before today
		$where .= $wpdb->prepare( " AND $wpdb->posts.post_date >= '%s' ", $from_date );	// Show posts after the date specified
		$where .= " AND $wpdb->posts.post_status = 'publish' ";					// Only show published posts
		$where .= $wpdb->prepare( " AND $wpdb->posts.ID != %d ", $post->ID );	// Show posts after the date specified
		if ( '' != $exclude_post_ids ) {
			$where .= " AND $wpdb->posts.ID NOT IN (" . $exclude_post_ids . ") ";
		}
		$where .= " AND $wpdb->posts.post_type IN ('" . join( "', '", $post_types ) . "') ";	// Array of post types

		// Create the base LIMITS clause
		$limits .= $wpdb->prepare( " LIMIT %d ", $limit );

		/**
		 * Filter the SELECT clause of the query.
		 *
		 * @param string   $fields  The SELECT clause of the query.
		 */
		$fields = apply_filters( 'crp_posts_fields', $fields, $post->ID );

		/**
		 * Filter the JOIN clause of the query.
		 *
		 * @param string   $join  The JOIN clause of the query.
		 */
 		$join = apply_filters( 'crp_posts_join', $join, $post->ID );

		/**
		 * Filter the WHERE clause of the query.
		 *
		 * @param string   $where  The WHERE clause of the query.
		 */
		$where = apply_filters( 'crp_posts_where', $where, $post->ID );

		/**
		 * Filter the GROUP BY clause of the query.
		 *
		 * @param string   $groupby  The GROUP BY clause of the query.
		 */
		$groupby = apply_filters( 'crp_posts_groupby', $groupby, $post->ID );


		/**
		 * Filter the ORDER BY clause of the query.
		 *
		 * @param string   $orderby  The ORDER BY clause of the query.
		 */
		$orderby = apply_filters( 'crp_posts_orderby', $orderby, $post->ID );

		/**
		 * Filter the LIMIT clause of the query.
		 *
		 * @param string   $limits  The LIMIT clause of the query.
		 */
		$limits = apply_filters( 'crp_posts_limits', $limits, $post->ID );

		if ( ! empty( $groupby ) ) {
			$groupby = 'GROUP BY ' . $groupby;
		}
		if ( !empty( $orderby ) ) {
			$orderby = 'ORDER BY ' . $orderby;
		}
		$sql = "SELECT DISTINCT $fields FROM $wpdb->posts $join WHERE 1=1 $where $groupby $orderby $limits";

		$results = $wpdb->get_results( $sql );
	} else {
		$results = false;
	}

	/**
	 * Filter object containing the post IDs.
	 *
	 * @since	1.9
	 *
	 * @param 	object   $results  Object containing the related post IDs
	 */
	return apply_filters( 'get_crp_posts_id', $results );
}


/**
 * Content function with user defined filter.
 *
 * @since 1.9
 *
 */
function crp_content_prepare_filter() {
	global $crp_settings;

    $priority = isset ( $crp_settings['content_filter_priority'] ) ? $crp_settings['content_filter_priority'] : 10;

    add_filter( 'the_content', 'ald_crp_content', $priority );
}
add_action( 'template_redirect', 'crp_content_prepare_filter' );


/**
 * Filter for 'the_content' to add the related posts.
 *
 * @since 1.0.1
 *
 * @param string $content
 * @return string After the filter has been processed
 */
function ald_crp_content( $content ) {

	global $single, $post, $crp_settings;

	if ( ! in_the_loop() ) return $content;

	$exclude_on_post_ids = explode( ',', $crp_settings['exclude_on_post_ids'] );
	if ( in_array( $post->ID, $exclude_on_post_ids ) ) return $content;	// Exit without adding related posts

	parse_str( $crp_settings['exclude_on_post_types'], $exclude_on_post_types );	// Save post types in $exclude_on_post_types variable
	if ( in_array( $post->post_type, $exclude_on_post_types ) ) return $content;	// Exit without adding related posts

    if ( ( is_single() ) && ( $crp_settings['add_to_content'] ) ) {
        return $content.ald_crp( 'is_widget=0' );
    } elseif ( ( is_page() ) && ( $crp_settings['add_to_page'] ) ) {
        return $content.ald_crp( 'is_widget=0' );
    } elseif ( ( is_home() ) && ( $crp_settings['add_to_home'] ) ) {
        return $content.ald_crp( 'is_widget=0' );
    } elseif ( ( is_category() ) && ( $crp_settings['add_to_category_archives'] ) ) {
        return $content.ald_crp( 'is_widget=0' );
    } elseif ( ( is_tag() ) && ( $crp_settings['add_to_tag_archives'] ) ) {
        return $content.ald_crp( 'is_widget=0' );
    } elseif ( ( ( is_tax() ) || ( is_author() ) || ( is_date() ) ) && ( $crp_settings['add_to_archives'] ) ) {
        return $content.ald_crp( 'is_widget=0' );
    } else {
        return $content;
    }
}


/**
 * Filter to add related posts to feeds.
 *
 * @since 1.8.4
 *
 * @param	string	$content
 * @return	string	Formatted content
 */
function ald_crp_rss( $content ) {
	global $post, $crp_settings;

	$limit_feed = $crp_settings['limit_feed'];
	$show_excerpt_feed = $crp_settings['show_excerpt_feed'];
	$post_thumb_op_feed = $crp_settings['post_thumb_op_feed'];

	if ( $crp_settings['add_to_feed'] ) {
		$output = $content;
		$output .= ald_crp( 'is_widget=0&limit='.$limit_feed.'&show_excerpt='.$show_excerpt_feed.'&post_thumb_op='.$post_thumb_op_feed );
		return $output;
	} else {
        return $content;
    }
}
add_filter( 'the_excerpt_rss', 'ald_crp_rss' );
add_filter( 'the_content_feed', 'ald_crp_rss' );


/**
 * Manual install of the related posts.
 *
 * @since 1.0.1
 *
 * @param	string	List of arguments to control the output
 */
function echo_ald_crp( $args = array() ) {
	echo ald_crp( $args );
}


/**
 * Create a Wordpress Widget for CRP.
 *
 * @since 1.9
 *
 * @extends WP_Widget
 */
class CRP_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'widget_crp', // Base ID
			__( 'Related Posts [CRP]', CRP_LOCAL_NAME ), // Name
			array( 'description' => __( 'Display Related Posts', CRP_LOCAL_NAME ), ) // Args
		);
	}

	/**
	 * Back-end widget form.
	 *
	 * @see	WP_Widget::form()
	 *
	 * @param	array	$instance	Previously saved values from database.
	 */
	public function form( $instance ) {
		$title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$limit = isset( $instance['limit'] ) ? esc_attr( $instance['limit'] ) : '';
		$show_excerpt = isset( $instance['show_excerpt'] ) ? esc_attr( $instance['show_excerpt'] ) : '';
		$show_author = isset( $instance['show_author'] ) ? esc_attr( $instance['show_author'] ) : '';
		$show_date = isset( $instance['show_date'] ) ? esc_attr( $instance['show_date'] ) : '';
		$post_thumb_op = isset( $instance['post_thumb_op'] ) ? esc_attr( $instance['post_thumb_op'] ) : '';
		$thumb_height = isset( $instance['thumb_height'] ) ? esc_attr( $instance['thumb_height'] ) : '';
		$thumb_width = isset( $instance['thumb_width'] ) ? esc_attr( $instance['thumb_width'] ) : '';
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">
			<?php _e( 'Title', CRP_LOCAL_NAME ); ?>: <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'limit' ); ?>">
			<?php _e( 'No. of posts', CRP_LOCAL_NAME ); ?>: <input class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" type="text" value="<?php echo esc_attr( $limit ); ?>" />
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'show_excerpt' ); ?>">
			<input id="<?php echo $this->get_field_id( 'show_excerpt' ); ?>" name="<?php echo $this->get_field_name( 'show_excerpt' ); ?>" type="checkbox" <?php if ( $show_excerpt ) echo 'checked="checked"' ?> /> <?php _e( ' Show excerpt?', CRP_LOCAL_NAME ); ?>
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'show_author' ); ?>">
			<input id="<?php echo $this->get_field_id( 'show_author' ); ?>" name="<?php echo $this->get_field_name( 'show_author' ); ?>" type="checkbox" <?php if ( $show_author ) echo 'checked="checked"' ?> /> <?php _e( ' Show author?', CRP_LOCAL_NAME ); ?>
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'show_date' ); ?>">
			<input id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" type="checkbox" <?php if ( $show_date ) echo 'checked="checked"' ?> /> <?php _e( ' Show date?', CRP_LOCAL_NAME ); ?>
			</label>
		</p>
		<p>
			<?php _e( 'Thumbnail options', CRP_LOCAL_NAME ); ?>: <br />
			<select class="widefat" id="<?php echo $this->get_field_id( 'post_thumb_op' ); ?>" name="<?php echo $this->get_field_name( 'post_thumb_op' ); ?>">
			  <option value="inline" <?php if ( 'inline' == $post_thumb_op ) echo 'selected="selected"' ?>><?php _e( 'Thumbnails inline, before title', CRP_LOCAL_NAME ); ?></option>
			  <option value="after" <?php if ( 'after' == $post_thumb_op ) echo 'selected="selected"' ?>><?php _e( 'Thumbnails inline, after title', CRP_LOCAL_NAME ); ?></option>
			  <option value="thumbs_only" <?php if ( 'thumbs_only' == $post_thumb_op ) echo 'selected="selected"' ?>><?php _e( 'Only thumbnails, no text', CRP_LOCAL_NAME ); ?></option>
			  <option value="text_only" <?php if ( 'text_only' == $post_thumb_op ) echo 'selected="selected"' ?>><?php _e( 'No thumbnails, only text.', CRP_LOCAL_NAME ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'thumb_height' ); ?>">
			<?php _e( 'Thumbnail height', CRP_LOCAL_NAME ); ?>: <input class="widefat" id="<?php echo $this->get_field_id( 'thumb_height' ); ?>" name="<?php echo $this->get_field_name( 'thumb_height' ); ?>" type="text" value="<?php echo esc_attr( $thumb_height ); ?>" />
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'thumb_width' ); ?>">
			<?php _e( 'Thumbnail width', CRP_LOCAL_NAME ); ?>: <input class="widefat" id="<?php echo $this->get_field_id( 'thumb_width' ); ?>" name="<?php echo $this->get_field_name( 'thumb_width' ); ?>" type="text" value="<?php echo esc_attr( $thumb_width ); ?>" />
			</label>
		</p>
		<?php
	} //ending form creation

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param 	array	$new_instance Values just sent to be saved.
	 * @param 	array	$old_instance Previously saved values from database.
	 *
	 * @return 	array	Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['limit'] = $new_instance['limit'];
		$instance['show_excerpt'] = $new_instance['show_excerpt'];
		$instance['show_author'] = $new_instance['show_author'];
		$instance['show_date'] = $new_instance['show_date'];
		$instance['post_thumb_op'] = $new_instance['post_thumb_op'];
		$instance['thumb_height'] = $new_instance['thumb_height'];
		$instance['thumb_width'] = $new_instance['thumb_width'];
		delete_post_meta_by_key( 'crp_related_posts_widget' ); // Delete the cache
		return $instance;
	} //ending update

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param	array	$args	Widget arguments.
	 * @param	array	$instance	Saved values from database.
	 */
	public function widget( $args, $instance ) {
		global $wpdb, $post;

		extract( $args, EXTR_SKIP );

		global $crp_settings;

		parse_str( $crp_settings['exclude_on_post_types'], $exclude_on_post_types );	// Save post types in $exclude_on_post_types variable
		if ( in_array( $post->post_type, $exclude_on_post_types ) ) return 0;	// Exit without adding related posts

		$exclude_on_post_ids = explode( ',', $crp_settings['exclude_on_post_ids'] );

		if ( ( ( is_single() ) && ( ! is_single( $exclude_on_post_ids ) ) ) || ( ( is_page() ) && ( ! is_page( $exclude_on_post_ids ) ) ) ) {

			$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? strip_tags( str_replace( "%postname%", $post->post_title, $crp_settings['title'] ) ) : $instance['title'] );
			$limit = $instance['limit'];
			if ( empty( $limit ) ) $limit = $crp_settings['limit'];

			$output = $before_widget;
			$output .= $before_title . $title . $after_title;
			$output .= ald_crp( array(
				'is_widget' => 1,
				'limit' => $limit,
				'show_excerpt' => $instance['show_excerpt'],
				'show_author' => $instance['show_author'],
				'show_date' => $instance['show_date'],
				'post_thumb_op' => $instance['post_thumb_op'],
				'thumb_height' => $instance['thumb_height'],
				'thumb_width' => $instance['thumb_width'],
			) );

			$output .= $after_widget;

			echo $output;
		}
	} //ending function widget
}


/**
 * Initialise the widgets.
 *
 * @since 1.9.1
 *
 * @access public
 * @return void
 */
function register_crp_widget() {
	register_widget( 'CRP_Widget' );
}
add_action( 'widgets_init', 'register_crp_widget' );


/**
 * Enqueue styles.
 *
 * @since 1.9
 *
 */
function crp_heading_styles() {
	global $crp_settings;

	if ( $crp_settings['include_default_style'] ) {
		wp_register_style( 'crp_list_style', plugins_url( 'css/default-style.css', __FILE__ ) );
		wp_enqueue_style( 'crp_list_style' );
	}
}
add_action( 'wp_enqueue_scripts', 'crp_heading_styles' );


/**
 * Creates a shortcode [crp limit="5" heading="1" cache="1"].
 *
 * @since 1.8.6
 *
 * @param array $atts
 * @param string $content (default: null)
 * @return Related Posts
 */
function crp_shortcode( $atts, $content = null ) {
	global $crp_settings;
	$atts = shortcode_atts( array_merge(
		$crp_settings,
		array( 'heading' => 1 )
	), $atts, 'crp' );

	$atts['is_widget'] = 1 - $atts['heading'];

	return ald_crp( $atts );
}
add_shortcode( 'crp', 'crp_shortcode' );


/**
 * Default options.
 *
 * @since 1.0.1
 *
 * @return array Default options
 */
function crp_default_options() {

	$title = __( '<h3>Related Posts:</h3>', CRP_LOCAL_NAME );

	$blank_output_text = __( 'No related posts found', CRP_LOCAL_NAME );

	$thumb_default = plugins_url( 'default.png' , __FILE__ );

	// get relevant post types
	$args = array(
		'public' => true,
		'_builtin' => true
	);
	$post_types = array(
		'post' => 'post',
		'page' => 'page',
	);
	$post_types	= http_build_query( $post_types, '', '&' );
	// $post_types	= http_build_query( get_post_types( $args ), '', '&' );

	$crp_settings = array(
		// General options
		'cache' => false,			// Cache output for faster page load
		'limit' => '5',				// How many posts to display?
		'daily_range' => '1095',				// How old posts should be displayed?
		'post_types' => $post_types,		// WordPress custom post types
		'match_content' => true,		// Match against post content as well as title
		'match_content_words' => '0',	// How many characters of content should be matched? 0 for all chars

		'add_to_content' => true,		// Add related posts to content (only on single posts)
		'add_to_page' => true,		// Add related posts to content (only on single pages)
		'add_to_feed' => false,		// Add related posts to feed (full)
		'add_to_home' => false,		// Add related posts to home page
		'add_to_category_archives' => false,		// Add related posts to category archives
		'add_to_tag_archives' => false,		// Add related posts to tag archives
		'add_to_archives' => false,		// Add related posts to other archives

		'content_filter_priority' => 10,	// Content priority
		'show_credit' => false,		// Link to this plugin's page?

		// Output options
		'title' => $title,			// Add before the content
		'blank_output' => true,		// Blank output?
		'blank_output_text' => $blank_output_text,		// Blank output text

		'show_excerpt' => false,			// Show post excerpt in list item
		'show_date' => false,			// Show date in list item
		'show_author' => false,			// Show author in list item
		'excerpt_length' => '10',		// Length of characters
		'title_length' => '60',		// Limit length of post title

		'link_new_window' => false,			// Open link in new window - Includes target="_blank" to links
		'link_nofollow' => false,			// Includes rel="nofollow" to links

		'before_list' => '<ul>',	// Before the entire list
		'after_list' => '</ul>',	// After the entire list
		'before_list_item' => '<li>',	// Before each list item
		'after_list_item' => '</li>',	// After each list item

		'exclude_categories' => '',	// Exclude these categories
		'exclude_cat_slugs' => '',	// Exclude these categories (slugs)
		'exclude_post_ids' => '',	// Comma separated list of page / post IDs that are to be excluded in the results
		'exclude_on_post_ids' => '', 	// Comma separate list of page/post IDs to not display related posts on
		'exclude_on_post_types' => '',		// WordPress custom post types

		'post_thumb_op' => 'text_only',	// Default option to display text and no thumbnails in posts
		'thumb_height' => '150',	// Height of thumbnails
		'thumb_width' => '150',	// Width of thumbnails
		'thumb_crop' => false,		// Crop mode. default is soft proportional
		'thumb_html' => 'html',		// Use HTML or CSS for width and height of the thumbnail?
		'thumb_meta' => 'post-image',	// Meta field that is used to store the location of default thumbnail image
		'scan_images' => false,			// Scan post for images
		'thumb_default' => $thumb_default,	// Default thumbnail image
		'thumb_default_show' => true,	// Show default thumb if none found (if false, don't show thumb at all)
		'thumb_timthumb' => false,	// Use timthumb
		'thumb_timthumb_q' => '75',	// Quality attribute for timthumb

		// Feed options
		'limit_feed' => '5',				// How many posts to display in feeds
		'post_thumb_op_feed' => 'text_only',	// Default option to display text and no thumbnails in Feeds
		'thumb_height_feed' => '50',	// Height of thumbnails in feed
		'thumb_width_feed' => '50',	// Width of thumbnails in feed
		'show_excerpt_feed' => false,			// Show description in list item in feed

		// Custom styles
		'custom_CSS' => '',			// Custom CSS to style the output
		'include_default_style' => false,	// Include without VAT
	);


	/**
	 * Filters the default options array.
	 *
	 * @since	1.9.1
	 *
	 * @param	array	$crp_settings	Default options
	 */
	return apply_filters( 'crp_default_options', $crp_settings );
}


/**
 * Function to read options from the database.
 *
 * @since 1.0.1
 *
 * @return array Contextual Related Posts options
 */
function crp_read_options() {
	$crp_settings_changed = false;

	$defaults = crp_default_options();

	$crp_settings = array_map( 'stripslashes', (array) get_option( 'ald_crp_settings') );
	unset( $crp_settings[0] ); // produced by the (array) casting when there's nothing in the DB

	foreach ( $defaults as $k=>$v ) {
		if ( ! isset( $crp_settings[ $k ] ) ) {
			$crp_settings[ $k ] = $v;
		}
		$crp_settings_changed = true;
	}
	if ( true == $crp_settings_changed ) {
		update_option('ald_crp_settings', $crp_settings);
	}

	/**
	 * Filters the options array.
	 *
	 * @since	1.9.1
	 *
	 * @param	array	$crp_settings	Options read from the database
	 */
	return apply_filters( 'crp_read_options', $crp_settings );
}


/**
 * Filter for wp_head to include the custom CSS.
 *
 * @since 1.8.4
 *
 * @return	string	Echoed string with the CSS output in the Header
 */
function crp_header() {
	global $wpdb, $post, $single, $crp_settings;

	$crp_custom_CSS = stripslashes( $crp_settings['custom_CSS'] );

	// Add CSS to header
	if ( '' != $crp_custom_CSS ) {
	    if ( ( is_single() ) ) {
			echo '<style type="text/css">'.$crp_custom_CSS.'</style>';
	    } elseif((is_page())) {
			echo '<style type="text/css">'.$crp_custom_CSS.'</style>';
	    } elseif ( ( is_home() ) && ( $crp_settings['add_to_home'] ) ) {
			echo '<style type="text/css">'.$crp_custom_CSS.'</style>';
	    } elseif ( ( is_category() ) && ( $crp_settings['add_to_category_archives'] ) ) {
			echo '<style type="text/css">'.$crp_custom_CSS.'</style>';
	    } elseif ( ( is_tag() ) && ( $crp_settings['add_to_tag_archives'] ) ) {
			echo '<style type="text/css">'.$crp_custom_CSS.'</style>';
	    } elseif( ( ( is_tax() ) || ( is_author() ) || ( is_date() ) ) && ( $crp_settings['add_to_archives'] ) ) {
			echo '<style type="text/css">'.$crp_custom_CSS.'</style>';
	    } elseif ( is_active_widget( false, false, 'CRP_Widget', true ) ) {
			echo '<style type="text/css">'.$crp_custom_CSS.'</style>';
	    }
	}
}
add_action( 'wp_head', 'crp_header' );


/**
 * Fired for each blog when the plugin is activated.
 *
 * @since 1.0.1
 *
 * @param    boolean    $network_wide    True if WPMU superadmin uses
 *                                       "Network Activate" action, false if
 *                                       WPMU is disabled or plugin is
 *                                       activated on an individual blog.
 */
function crp_activate( $network_wide ) {
    global $wpdb;

    if ( is_multisite() && $network_wide ) {

        // Get all blogs in the network and activate plugin on each one
        $blog_ids = $wpdb->get_col( "
        	SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0' AND deleted = '0'
		" );
        foreach ( $blog_ids as $blog_id ) {
        	switch_to_blog( $blog_id );
			crp_single_activate();
        }

        // Switch back to the current blog
        restore_current_blog();

    } else {
        crp_single_activate();
    }
}
register_activation_hook( __FILE__, 'crp_activate' );


/**
 * Fired for each blog when the plugin is activated.
 *
 * @since 2.0.0
 *
 */
function crp_single_activate() {
	global $wpdb;

	$crp_settings = crp_read_options();

    $wpdb->hide_errors();

	// If we're running mySQL v5.6, convert the WPDB posts table to InnoDB, since InnoDB supports FULLTEXT from v5.6 onwards
	if ( version_compare( 5.6, $wpdb->db_version(), '<=' ) ) {
		$wpdb->query( 'ALTER TABLE ' . $wpdb->posts . ' ENGINE = InnoDB;' );
	} else {
		$wpdb->query( 'ALTER TABLE ' . $wpdb->posts . ' ENGINE = MYISAM;' );
	}

	$wpdb->query( 'ALTER TABLE ' . $wpdb->posts . ' ADD FULLTEXT crp_related (post_title, post_content);' );
    $wpdb->query( 'ALTER TABLE ' . $wpdb->posts . ' ADD FULLTEXT crp_related_title (post_title);' );
    $wpdb->query( 'ALTER TABLE ' . $wpdb->posts . ' ADD FULLTEXT crp_related_content (post_content);' );
    $wpdb->show_errors();

}


/**
 * Fired when a new site is activated with a WPMU environment.
 *
 * @since 2.0.0
 *
 * @param    int    $blog_id    ID of the new blog.
 */
function crp_activate_new_site( $blog_id ) {

	if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
		return;
	}

	switch_to_blog( $blog_id );
	crp_single_activate();
	restore_current_blog();

}
add_action( 'wpmu_new_blog', 'crp_activate_new_site' );


/**
 * Add custom image size of thumbnail. Filters `init`.
 *
 * @since 2.0.0
 *
 */
function crp_add_image_sizes() {
	global $crp_settings;

	$width = empty( $crp_settings['thumb_width'] ) ? 150 : $crp_settings['thumb_width'];
	$height = empty( $crp_settings['thumb_height'] ) ? 150 : $crp_settings['thumb_height'];
	$crop = isset( $crp_settings['thumb_crop'] ) ? $crp_settings['thumb_crop'] : false;

	add_image_size( 'crp_thumbnail', $width, $height, $crop );
}
add_action( 'init', 'crp_add_image_sizes' );


/**
 * Filter function to resize post thumbnail. Filters: crp_postimage.
 *
 * @since 1.8.4
 *
 * @param 	string		$postimage
 * @param	string|int	$thumb_width
 * @param	string|int	$thumb_height
 * @param	boolean		$thumb_timthumb
 * @param	string|int	$thumb_timthumb_q
 * @return	string 		Post image output
 */
function crp_scale_thumbs( $postimage, $thumb_width, $thumb_height, $thumb_timthumb, $thumb_timthumb_q, $post ) {
	global $crp_url;

	if ( $thumb_timthumb ) {
		$new_pi = $crp_url . '/timthumb/timthumb.php?src=' . urlencode( $postimage ) . '&amp;w=' . $thumb_width . '&amp;h=' . $thumb_height . '&amp;zc=1&amp;q=' . $thumb_timthumb_q;
	} else {
		$new_pi = $postimage;
	}
	return $new_pi;
}
add_filter( 'crp_postimage', 'crp_scale_thumbs', 10, 6 );


/**
 * Function to get the post thumbnail.
 *
 * @since 1.7
 *
 * @param 	array|string 	$args	Array / Query string with arguments post thumbnails
 * @return 	string 					Output with the post thumbnail
 */
function crp_get_the_post_thumbnail( $args = array() ) {

	$defaults = array(
		'postid' => '',
		'thumb_height' => '50',			// Max height of thumbnails
		'thumb_width' => '50',			// Max width of thumbnails
		'thumb_meta' => 'post-image',		// Meta field that is used to store the location of default thumbnail image
		'thumb_html' => 'html',		// HTML / CSS for width and height attributes
		'thumb_default' => '',	// Default thumbnail image
		'thumb_default_show' => true,	// Show default thumb if none found (if false, don't show thumb at all)
		'thumb_timthumb' => true,	// Use timthumb
		'thumb_timthumb_q' => '75',	// Quality attribute for timthumb
		'scan_images' => false,			// Scan post for images
		'class' => 'crp_thumb',			// Class of the thumbnail
		'filter' => 'crp_postimage',			// Class of the thumbnail
	);

	// Parse incomming $args into an array and merge it with $defaults
	$args = wp_parse_args( $args, $defaults );

	// Declare each item in $args as its own variable i.e. $type, $before.
	extract( $args, EXTR_SKIP );

	$result = get_post( $postid );
	$title = get_the_title( $postid );

	$output = '';
	$thumb_html = ( 'css' == $thumb_html ) ? 'style="max-width:' . $thumb_width . 'px;max-height:' . $thumb_height . 'px;"' : 'width="' . $thumb_width . '" height="' .$thumb_height . '"';

	// Let's start fetching the thumbnail. First place to look is in the post meta defined in the Settings page
	$postimage = get_post_meta( $result->ID, $thumb_meta, true );	// Check the post meta first

	// If there is no thumbnail found, check the post thumbnail
	if ( ! $postimage ) {
		if ( function_exists( 'has_post_thumbnail' ) && ( ( '' != wp_get_attachment_image_src( get_post_thumbnail_id( $result->ID ) ) ) || ( false != wp_get_attachment_image_src( get_post_thumbnail_id( $result->ID ) ) ) ) ) {
			$postthumb = wp_get_attachment_image_src( get_post_thumbnail_id( $result->ID ), 'crp_thumbnail' );
			$postimage = $postthumb[0];
		}
	}

	// If there is no thumbnail found, fetch the first image in the post, if enabled
	if ( ! $postimage && $scan_images ) {
		preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $result->post_content, $matches );
		if ( isset( $matches[1][0] ) && $matches[1][0] ) { 			// any image there?
			$postimage = $matches[1][0]; // we need the first one only!
		}
	}

	// If there is no thumbnail found, fetch the first child image
	if ( ! $postimage ) {
		$postimage = crp_get_first_image($result->ID);	// Get the first image
	}

	// If no other thumbnail set, try to get the custom video thumbnail set by the Video Thumbnails plugin
	if ( ! $postimage ) {
		$postimage = get_post_meta( $result->ID, '_video_thumbnail', true );
	}

	// If no thumb found and settings permit, use default thumb
	if ( $thumb_default_show && ! $postimage ) {
		$postimage = $thumb_default;
	}

	// Hopefully, we've found a thumbnail by now. If so, run it through the custom filter, check for SSL and create the image tag
	if ( $postimage ) {

		/**
		 * Get the first image in the post.
		 *
		 * @since 1.8.10
		 *
		 * @param mixed $postID	Post ID
		 */
		$postimage = apply_filters( $filter, $postimage, $thumb_width, $thumb_height, $thumb_timthumb, $thumb_timthumb_q, $result );
		if ( is_ssl() ) {
		    $postimage = preg_replace( '~http://~', 'https://', $postimage );
		}
		$output .= '<img src="'.$postimage.'" alt="'.$title.'" title="'.$title.'" '.$thumb_html.' class="'.$class.'" />';
	}

	/**
	 * Filters post thumbnail created for CRP.
	 *
	 * @since	1.9
	 *
	 * @param	array	$output	Formatted output
	 */
	return apply_filters( 'crp_get_the_post_thumbnail', $output );
}


/**
 * Get the first image in the post.
 *
 * @since 1.8.9
 *
 * @param mixed $postID	Post ID
 * @return string
 */
function crp_get_first_image( $postID ) {
	$args = array(
		'numberposts' => 1,
		'order' => 'ASC',
		'post_mime_type' => 'image',
		'post_parent' => $postID,
		'post_status' => null,
		'post_type' => 'attachment',
	);

	$attachments = get_children( $args );

	if ( $attachments ) {
		foreach ( $attachments as $attachment ) {
			$image_attributes = wp_get_attachment_image_src( $attachment->ID, 'crp_thumbnail' );

			/**
			 * Filters first child attachment from the post.
			 *
			 * @since	2.0.0
			 *
			 * @param	array	$image_attributes[0]	URL of the image
			 * @param	int		$postID					Post ID
			 */
			return apply_filters( 'crp_get_first_image', $image_attributes[0], $postID );
		}
	} else {
		return false;
	}
}


/**
 * Function to create an excerpt for the post.
 *
 * @since 1.6
 *
 * @param int $id Post ID
 * @param int|string $excerpt_length Length of the excerpt in words
 * @return string Excerpt
 */
function crp_excerpt( $id, $excerpt_length = 0, $use_excerpt = true ) {
	$content = $excerpt = '';

	if ( $use_excerpt ) {
		$content = get_post( $id )->post_excerpt;
	}
	if ( '' == $content ) {
		$content = get_post( $id )->post_content;
	}

	$output = strip_tags( strip_shortcodes( $content ) );

	if ( $excerpt_length > 0 ) {
		$output = wp_trim_words( $output, $excerpt_length );
	}

	/**
	 * Filters excerpt generated by CRP.
	 *
	 * @since	1.9
	 *
	 * @param	array	$output			Formatted excerpt
	 * @param	int		$id				Post ID
	 * @param	int		$excerpt_length	Length of the excerpt
	 * @param	boolean	$use_excerpt	Use the excerpt?
	 */
	return apply_filters( 'crp_excerpt', $output, $id, $excerpt_length, $use_excerpt );
}


/**
 * Function to limit content by characters.
 *
 * @since 1.8.4
 *
 * @param	string 	$content 	Content to be used to make an excerpt
 * @param	int 	$no_of_char	Maximum length of excerpt in characters
 * @return 	string				Formatted content
 */
function crp_max_formatted_content( $content, $no_of_char = -1 ) {
	$content = strip_tags( $content );  // Remove CRLFs, leaving space in their wake

	if ( ( $no_of_char > 0 ) && ( strlen( $content ) > $no_of_char ) ) {
		$aWords = preg_split( "/[\s]+/", substr( $content, 0, $no_of_char ) );

		// Break back down into a string of words, but drop the last one if it's chopped off
		if ( substr( $content, $no_of_char, 1 ) == " " ) {
		  $content = implode( " ", $aWords );
		} else {
		  $content = implode( " ", array_slice( $aWords, 0, -1 ) ) .'&hellip;';
		}
	}

	/**
	 * Filters formatted content after cropping.
	 *
	 * @since	1.9
	 *
	 * @param	string	$content	Formatted content
	 * @param	int		$no_of_char	Maximum length of excerpt in characters
	 */
	return apply_filters( 'crp_max_formatted_content' , $content, $no_of_char );
}


/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

if ( is_admin() || strstr( $_SERVER['PHP_SELF'], 'wp-admin/' ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/admin.php' );

} // End admin.inc


/*----------------------------------------------------------------------------*
 * Deprecated functions
 *----------------------------------------------------------------------------*/

	require_once( plugin_dir_path( __FILE__ ) . 'deprecated.php' );

?>
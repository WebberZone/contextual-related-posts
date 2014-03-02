<?php
/**********************************************************************
*					Admin Page										*
*********************************************************************/
if ( ! defined( 'ABSPATH' ) ) die( "Aren't you supposed to come here via WP-Admin?" );

/**
 * Plugin settings page.
 * 
 * @access public
 * @return void
 */
function crp_options() {
	
	global $wpdb;
    $poststable = $wpdb->posts;

	$crp_settings = crp_read_options();
	$wp_post_types	= get_post_types( array(
		'public'	=> true,
	) );
	parse_str( $crp_settings['post_types'], $post_types );
	$posts_types_inc = array_intersect( $wp_post_types, $post_types );

	parse_str( $crp_settings['exclude_on_post_types'], $exclude_on_post_types );
	$posts_types_excl = array_intersect( $wp_post_types, $exclude_on_post_types );

	if ( ( isset( $_POST['crp_save'] ) ) && ( check_admin_referer( 'crp-plugin' ) ) ) {
		$crp_settings['title'] = wp_kses_post( $_POST['title'] );
		$crp_settings['limit'] = intval( $_POST['limit'] );
		$crp_settings['daily_range'] = intval( $_POST['daily_range'] );
		$crp_settings['exclude_on_post_ids'] = $_POST['exclude_on_post_ids'] == '' ? '' : implode( ',', array_map( 'intval', explode( ",", $_POST['exclude_on_post_ids'] ) ) );
		$crp_settings['exclude_post_ids'] = $_POST['exclude_post_ids'] == '' ? '' : implode( ',', array_map( 'intval', explode( ",", $_POST['exclude_post_ids'] ) ) );
		$crp_settings['match_content'] = ( isset( $_POST['match_content'] ) ? true : false );
		$crp_settings['match_content_words'] = intval( $_POST['match_content_words'] );
		$crp_settings['cache'] = ( isset( $_POST['cache'] ) ? true : false );
		$crp_settings['content_filter_priority'] = intval( $_POST['content_filter_priority'] );

		$crp_settings['add_to_content'] = ( isset( $_POST['add_to_content'] ) ? true : false );
		$crp_settings['add_to_page'] = ( isset( $_POST['add_to_page'] ) ? true : false );
		$crp_settings['add_to_feed'] = ( isset( $_POST['add_to_feed'] ) ? true : false );
		$crp_settings['add_to_home'] = ( isset( $_POST['add_to_home'] ) ? true : false );
		$crp_settings['add_to_category_archives'] = ( isset( $_POST['add_to_category_archives'] ) ? true : false );
		$crp_settings['add_to_tag_archives'] = ( isset( $_POST['add_to_tag_archives'] ) ? true : false );
		$crp_settings['add_to_archives'] = ( isset( $_POST['add_to_archives'] ) ? true : false );

		$crp_settings['title_length'] = intval( $_POST['title_length'] );
		$crp_settings['blank_output'] = ( ( $_POST['blank_output'] == 'blank' ) ? true : false );
		$crp_settings['blank_output_text'] = wp_kses_post( $_POST['blank_output_text'] );
		$crp_settings['before_list'] = wp_kses_post( $_POST['before_list'] );
		$crp_settings['after_list'] = wp_kses_post( $_POST['after_list'] );
		$crp_settings['before_list_item'] = wp_kses_post( $_POST['before_list_item'] );
		$crp_settings['after_list_item'] = wp_kses_post( $_POST['after_list_item'] );

		$crp_settings['post_thumb_op'] = wp_kses_post( $_POST['post_thumb_op'] );
		$crp_settings['thumb_meta'] = ( '' == $_POST['thumb_meta'] ? 'post-image' : wp_kses_post( $_POST['thumb_meta'] ) );
		$crp_settings['thumb_default'] = wp_kses_post( $_POST['thumb_default'] );
		$crp_settings['thumb_height'] = intval( $_POST['thumb_height'] );
		$crp_settings['thumb_width'] = intval( $_POST['thumb_width'] );
		$crp_settings['thumb_default_show'] = ( isset( $_POST['thumb_default_show'] ) ? true : false );
		$crp_settings['thumb_html'] = $_POST['thumb_html'];
		$crp_settings['thumb_timthumb'] = ( isset( $_POST['thumb_timthumb'] ) ? true : false );
		$crp_settings['thumb_timthumb_q'] = intval( $_POST['thumb_timthumb_q'] );
		$crp_settings['scan_images'] = ( isset( $_POST['scan_images'] ) ? true : false );

		$crp_settings['show_excerpt'] = ( isset( $_POST['show_excerpt'] ) ? true : false );
		$crp_settings['excerpt_length'] = intval( $_POST['excerpt_length'] );
		$crp_settings['show_date'] = ( isset( $_POST['show_date'] ) ? true : false );
		$crp_settings['show_author'] = ( isset( $_POST['show_author'] ) ? true : false );
		$crp_settings['show_credit'] = ( isset( $_POST['show_credit'] ) ? true : false );

		$crp_settings['custom_CSS'] = wp_kses_post( $_POST['custom_CSS'] );
		
		if ( isset( $_POST['include_default_style'] ) ) {
			$crp_settings['include_default_style'] = true;
			$crp_settings['post_thumb_op'] = 'inline';
			$crp_settings['thumb_height'] = 150;
			$crp_settings['thumb_width'] = 150;
		} else {
			$crp_settings['include_default_style'] = false;
		}

		$crp_settings['link_new_window'] = ( isset( $_POST['link_new_window'] ) ? true : false );
		$crp_settings['link_nofollow'] = ( isset( $_POST['link_nofollow'] ) ? true : false );
		
		$crp_settings['limit_feed'] = intval( $_POST['limit_feed'] );
		$crp_settings['post_thumb_op_feed'] = wp_kses_post( $_POST['post_thumb_op_feed'] );
		$crp_settings['thumb_height_feed'] = intval( $_POST['thumb_height_feed'] );
		$crp_settings['thumb_width_feed'] = intval( $_POST['thumb_width_feed'] );
		$crp_settings['show_excerpt_feed'] = ( isset( $_POST['show_excerpt_feed'] ) ? true : false );


		$crp_settings['exclude_cat_slugs'] = wp_kses_post( $_POST['exclude_cat_slugs'] );
		$exclude_categories_slugs = explode( ", ", $crp_settings['exclude_cat_slugs'] );
		
		foreach ( $exclude_categories_slugs as $exclude_categories_slug ) {
			$catObj = get_category_by_slug( $exclude_categories_slug );
			if ( isset( $catObj->term_id ) ) $exclude_categories[] = $catObj->term_id;
		}
		$crp_settings['exclude_categories'] = ( isset( $exclude_categories ) ) ? join( ',', $exclude_categories ) : '';

		// Post types to include
		$wp_post_types	= get_post_types( array(
			'public'	=> true,
		) );
		$post_types_arr = ( isset( $_POST['post_types'] ) && is_array( $_POST['post_types'] ) ) ? $_POST['post_types'] : array( 'post' => 'post' );
		$post_types = array_intersect( $wp_post_types, $post_types_arr );
		$crp_settings['post_types'] = http_build_query( $post_types, '', '&' );

		// Post types to exclude display on
		$post_types_excl_arr = ( isset( $_POST['exclude_on_post_types'] ) && is_array( $_POST['exclude_on_post_types'] ) ) ? $_POST['exclude_on_post_types'] : array();
		$exclude_on_post_types = array_intersect( $wp_post_types, $post_types_excl_arr );
		$crp_settings['exclude_on_post_types'] = http_build_query( $exclude_on_post_types, '', '&' );

		update_option( 'ald_crp_settings', $crp_settings );
		$crp_settings = crp_read_options();
		
		parse_str( $crp_settings['post_types'], $post_types );
		$posts_types_inc = array_intersect( $wp_post_types, $post_types );

		parse_str( $crp_settings['exclude_on_post_types'], $exclude_on_post_types );
		$posts_types_excl = array_intersect( $wp_post_types, $exclude_on_post_types );

		delete_post_meta_by_key( 'crp_related_posts' ); // Delete the cache
		delete_post_meta_by_key( 'crp_related_posts_widget' ); // Delete the cache
		
		$str = '<div id="message" class="updated fade"><p>'. __( 'Options saved successfully.', CRP_LOCAL_NAME ) .'</p></div>';
		echo $str;
	}
	
	if ( ( isset($_POST['crp_default'] ) ) && ( check_admin_referer( 'crp-plugin' ) ) ) {
		delete_option( 'ald_crp_settings' );
		$crp_settings = crp_default_options();
		update_option( 'ald_crp_settings', $crp_settings );
		
		$crp_settings = crp_read_options();

		$wp_post_types	= get_post_types( array(
			'public'	=> true,
		) );
		parse_str( $crp_settings['post_types'], $post_types );
		$posts_types_inc = array_intersect( $wp_post_types, $post_types );

		parse_str( $crp_settings['exclude_on_post_types'], $exclude_on_post_types );
		$posts_types_excl = array_intersect( $wp_post_types, $exclude_on_post_types );

		$str = '<div id="message" class="updated fade"><p>'. __( 'Options set to Default.', CRP_LOCAL_NAME ) .'</p></div>';
		echo $str;
	}

	if ( ( isset( $_POST['crp_recreate'] ) ) && ( check_admin_referer( 'crp-plugin' ) ) ) {
		$sql = "ALTER TABLE $poststable DROP INDEX crp_related";
		$wpdb->query( $sql );
		
		$sql = "ALTER TABLE $poststable DROP INDEX crp_related_title";
		$wpdb->query( $sql );
		
		$sql = "ALTER TABLE $poststable DROP INDEX crp_related_content";
		$wpdb->query( $sql );
		
		ald_crp_activate();
		
		$str = '<div id="message" class="updated fade"><p>'. __( 'Index recreated', CRP_LOCAL_NAME ) .'</p></div>';
		echo $str;
	}
?>

<div class="wrap">
	<h2>Contextual Related Posts</h2>
	<div id="poststuff">
	<div id="post-body" class="metabox-holder columns-2">
	<div id="post-body-content">
	  <form method="post" id="crp_options" name="crp_options" onsubmit="return checkForm()">
	    <div id="genopdiv" class="postbox closed"><div class="handlediv" title="Click to toggle"><br /></div>
	      <h3 class='hndle'><span><?php _e( 'General options', CRP_LOCAL_NAME ); ?></span></h3>
	      <div class="inside">
			<table class="form-table">
			<tr><th scope="row"><label for="cache"><?php _e( 'Cache output?', CRP_LOCAL_NAME ); ?></label></th>
				<td><input type="checkbox" name="cache" id="cache" <?php if ($crp_settings['cache']) echo 'checked="checked"' ?> />
					<p class="description"><?php _e( 'Enabling this option will cache the related posts output when the post is visited the first time. The cache is cleaned when you save this page.', CRP_LOCAL_NAME ); ?></p>
					<p><input type="button" value="<?php _e( 'Clear cache', CRP_LOCAL_NAME ) ?>" onclick="return clearCache();" class="button-secondary" /></p>
				</td>
			</tr>
			<tr><th scope="row"><label for="limit"><?php _e( 'Number of related posts to display: ', CRP_LOCAL_NAME ); ?></label></th>
				<td>
					<input type="textbox" name="limit" id="limit" value="<?php echo esc_attr(stripslashes($crp_settings['limit'])); ?>">
					<p class="description"><?php _e( 'Maximum number of posts that will be displayed. The actual number may be smaller if less related posts are found.', CRP_LOCAL_NAME ); ?></p>
				</td>
			</tr>
			<tr><th scope="row"><label for="daily_range"><?php _e( 'Related posts should be newer than:', CRP_LOCAL_NAME ); ?></label></th>
				<td>
					<input type="textbox" name="daily_range" id="daily_range" value="<?php echo esc_attr( stripslashes( $crp_settings['daily_range'] ) ); ?>"><?php _e( 'days', CRP_LOCAL_NAME ); ?>
					<p class="description"><?php _e( 'This sets the cutoff period for which posts will be displayed. e.g. setting it to 365 will show related posts from the last year only.', CRP_LOCAL_NAME ); ?></p>
				</td>
			</tr>
			<tr><th scope="row"><?php _e( 'Post types to include in results.', CRP_LOCAL_NAME ); ?></th>
				<td>
					<?php foreach ( $wp_post_types as $wp_post_type ) {
						$post_type_op = '<label><input type="checkbox" name="post_types[]" value="' . $wp_post_type . '" ';
						if ( in_array( $wp_post_type, $posts_types_inc ) ) $post_type_op .= ' checked="checked" ';
						$post_type_op .= ' />'.$wp_post_type.'</label>&nbsp;&nbsp;';
						echo $post_type_op;
					}
					?>
					<p class="description"><?php _e( 'These post types will be displayed in the list. Includes custom post types.', CRP_LOCAL_NAME ); ?></p>
				</td>
			</tr>
			<tr><th scope="row"><label for="match_content"><?php _e( 'Find related posts based on content as well as title', CRP_LOCAL_NAME ); ?></label></th>
				<td><input type="checkbox" name="match_content" id="match_content" <?php if ( $crp_settings['match_content'] ) echo 'checked="checked"' ?> /> 
					<p class="description"><?php _e( 'If unchecked, only posts titles are used. I recommend using a caching plugin or enabling "Cache output" above if you enable this.', CRP_LOCAL_NAME ); ?></p>
				</td>
			</tr>
			<tr><th scope="row"><label for="match_content_words"><?php _e( 'Limit content to be compared', CRP_LOCAL_NAME ); ?></label></th>
				<td><input type="textbox" name="match_content_words" id="match_content_words" value="<?php echo esc_attr(stripslashes($crp_settings['match_content_words'])); ?>">
					<p class="description"><?php _e( 'This sets the maximum words of the content that will be matched. 0 means no limit.', CRP_LOCAL_NAME ); ?></p>
				</td>
			</tr>
			<tr><th scope="row"><label for="exclude_post_ids"><?php _e( 'List of post or page IDs to exclude from the results: ', CRP_LOCAL_NAME ); ?></label></th>
				<td><input type="textbox" name="exclude_post_ids" id="exclude_post_ids" value="<?php echo esc_attr( stripslashes( $crp_settings['exclude_post_ids'] ) ); ?>" style="width:250px">
					<p class="description"><?php _e( 'Comma separated list of post, page or custom post type IDs. e.g. 188,320,500', CRP_LOCAL_NAME ); ?></p>
				</td>
			</tr>
			<tr><th scope="row"><label for="exclude_cat_slugs"><?php _e( 'Categories to exclude from the results: ', CRP_LOCAL_NAME ); ?></label></th>
				<td>
					<div style="position:relative;text-align:left">
						<table id="MYCUSTOMFLOATER" class="myCustomFloater" style="position:absolute;top:50px;left:0;background-color:#cecece;display:none;visibility:hidden">
						<tr><td><!--
								please see: http://chrisholland.blogspot.com/2004/09/geekstuff-css-display-inline-block.html
								to explain why i'm using a table here.
								You could replace the table/tr/td with a DIV, but you'd have to specify it's width and height
								-->
							<div class="myCustomFloaterContent">
							you should never be seeing this
							</div>
						</td></tr>
						</table>
						<textarea class="wickEnabled:MYCUSTOMFLOATER" cols="50" rows="3" wrap="virtual" name="exclude_cat_slugs"><?php echo ( stripslashes( $crp_settings['exclude_cat_slugs'] ) ); ?></textarea>
					</div>
					<p class="description"><?php _e( 'Comma separated list of category slugs. The field above has an autocomplete so simply start typing in the starting letters and it will prompt you with options', CRP_LOCAL_NAME ); ?></p>
				</td>
			</tr>
			<tr><th scope="row"><?php _e( 'Add related posts to:', CRP_LOCAL_NAME ); ?></th>
				<td>
					<label><input type="checkbox" name="add_to_content" id="add_to_content" <?php if ( $crp_settings['add_to_content'] ) echo 'checked="checked"' ?> /> <?php _e( 'Posts', CRP_LOCAL_NAME ); ?></label><br />
					<label><input type="checkbox" name="add_to_page" id="add_to_page" <?php if ( $crp_settings['add_to_page'] ) echo 'checked="checked"' ?> /> <?php _e( 'Pages', CRP_LOCAL_NAME ); ?></label><br />
					<label><input type="checkbox" name="add_to_home" id="add_to_home" <?php if ( $crp_settings['add_to_home'] ) echo 'checked="checked"' ?> /> <?php _e( 'Home page', CRP_LOCAL_NAME ); ?></label></label><br />
					<label><input type="checkbox" name="add_to_feed" id="add_to_feed" <?php if ( $crp_settings['add_to_feed'] ) echo 'checked="checked"' ?> /> <?php _e( 'Feeds', CRP_LOCAL_NAME ); ?></label></label><br />
					<label><input type="checkbox" name="add_to_category_archives" id="add_to_category_archives" <?php if ( $crp_settings['add_to_category_archives'] ) echo 'checked="checked"' ?> /> <?php _e( 'Category archives', CRP_LOCAL_NAME ); ?></label><br />
					<label><input type="checkbox" name="add_to_tag_archives" id="add_to_tag_archives" <?php if ( $crp_settings['add_to_tag_archives'] ) echo 'checked="checked"' ?> /> <?php _e( 'Tag archives', CRP_LOCAL_NAME ); ?></label></label><br />
					<label><input type="checkbox" name="add_to_archives" id="add_to_archives" <?php if ( $crp_settings['add_to_archives'] ) echo 'checked="checked"' ?> /> <?php _e( 'Other archives', CRP_LOCAL_NAME ); ?></label></label>
					<p class="description"><?php _e( "If you choose to disable this, please add <code>&lt;?php if ( function_exists( 'echo_ald_crp' ) ) echo_ald_crp(); ?&gt;</code> to your template file where you want it displayed", CRP_LOCAL_NAME ); ?></p>
				</td>
			</tr>
			<tr><th scope="row"><label for="content_filter_priority"><?php _e( 'Content filter priority:', CRP_LOCAL_NAME ); ?></label></th>
				<td>
					<input type="textbox" name="content_filter_priority" id="content_filter_priority" value="<?php echo esc_attr( stripslashes( $crp_settings['content_filter_priority'] ) ); ?>" />
					<p class="description"><?php _e( 'A higher number will cause the content above to be processed after other filters. Number below 10 is not recommended', CRP_LOCAL_NAME ); ?></p>
				</td>
			</tr>
			<tr><th scope="row"><label for="show_credit"><?php _e( 'Add a link to the plugin page as a final item in the list', CRP_LOCAL_NAME ); ?></label></th>
				<td>
					<input type="checkbox" name="show_credit" id="show_credit" <?php if ( $crp_settings['show_credit'] ) echo 'checked="checked"' ?> /> <?php _e( ' <em>Optional</em>', CRP_LOCAL_NAME ); ?>
					<p class="description"><?php _e( 'Adds a nofollow link to Contextual Related Posts homepage.', CRP_LOCAL_NAME ); ?></p>
				</td>
			</tr>
			</table>		
	      </div>
	    </div>
	    <div id="outputopdiv" class="postbox closed"><div class="handlediv" title="Click to toggle"><br /></div>
	      <h3 class='hndle'><span><?php _e( 'Output options', CRP_LOCAL_NAME ); ?></span></h3>
	      <div class="inside">
			<table class="form-table">
			<tr><th scope="row"><label for="title"><?php _e( 'Title of related posts: ', CRP_LOCAL_NAME ); ?></label></th>
				<td>
					<input type="textbox" name="title" id="title" value="<?php echo esc_attr( stripslashes( $crp_settings['title'] ) ); ?>"  style="width:250px" />
					<p class="description"><?php _e( 'This is the main heading of the related posts. You can also display the current post title by using <code>%postname%</code>. e.g. <code>Related Posts to %postname%</code>', CRP_LOCAL_NAME ); ?></p>
				</td>
			</tr>
			<tr><th scope="row"><label for="blank_output"><?php _e( 'When there are no posts, what should be shown?', CRP_LOCAL_NAME ); ?></label></th>
				<td>
					<label>
					<input type="radio" name="blank_output" value="blank" id="blank_output_0" <?php if ( $crp_settings['blank_output'] ) echo 'checked="checked"' ?> />
					<?php _e( 'Blank Output', CRP_LOCAL_NAME ); ?></label>
					<br />
					<label>
					<input type="radio" name="blank_output" value="customs" id="blank_output_1" <?php if ( ! $crp_settings['blank_output'] ) echo 'checked="checked"' ?> />
					<?php _e( 'Display:', CRP_LOCAL_NAME ); ?></label>
					<input type="textbox" name="blank_output_text" id="blank_output_text" value="<?php echo esc_attr( stripslashes( $crp_settings['blank_output_text'] ) ); ?>"  style="width:250px" />
				</td>
			</tr>
			<tr><th scope="row"><label for="show_excerpt"><?php _e( 'Show post excerpt in list?', CRP_LOCAL_NAME ); ?></label></th>
				<td><input type="checkbox" name="show_excerpt" id="show_excerpt" <?php if ( $crp_settings['show_excerpt'] ) echo 'checked="checked"' ?> /></td>
			</tr>
			<tr><th scope="row"><label for="excerpt_length"><?php _e( 'Length of excerpt (in words): ', CRP_LOCAL_NAME ); ?></label></th>
				<td><input type="textbox" name="excerpt_length" id="excerpt_length" value="<?php echo stripslashes( $crp_settings['excerpt_length'] ); ?>" /></td>
			</tr>
			<tr><th scope="row"><label for="show_author"><?php _e( 'Show post author in list?', CRP_LOCAL_NAME ); ?></label></th>
				<td><input type="checkbox" name="show_author" id="show_author" <?php if ( $crp_settings['show_author'] ) echo 'checked="checked"' ?> /></td>
			</tr>
			<tr><th scope="row"><label for="show_date"><?php _e( 'Show post date in list?', CRP_LOCAL_NAME ); ?></label></th>
				<td><input type="checkbox" name="show_date" id="show_date" <?php if ( $crp_settings['show_date'] ) echo 'checked="checked"' ?> /></td>
			</tr>
			<tr><th scope="row"><label for="title_length"><?php _e( 'Limit post title length (in characters)', CRP_LOCAL_NAME ); ?></label></th>
				<td><input type="textbox" name="title_length" id="title_length" value="<?php echo stripslashes( $crp_settings['title_length'] ); ?>" /></td>
			</tr>
			<tr><th scope="row"><label for="link_new_window"><?php _e( 'Open links in new window', CRP_LOCAL_NAME ); ?></label></th>
				<td><input type="checkbox" name="link_new_window" id="link_new_window" <?php if ( $crp_settings['link_new_window'] ) echo 'checked="checked"' ?> /></td>
			</tr>
			<tr><th scope="row"><label for="link_nofollow"><?php _e( 'Add nofollow attribute to links in the list', CRP_LOCAL_NAME ); ?></label></th>
				<td><input type="checkbox" name="link_nofollow" id="link_nofollow" <?php if ( $crp_settings['link_nofollow'] ) echo 'checked="checked"' ?> /></td>
			</tr>
			<tr><th scope="row"><label for="exclude_on_post_ids"><?php _e( 'Exclude display of related posts on these posts / pages', CRP_LOCAL_NAME ); ?></label></th>
				<td>
					<input type="textbox" name="exclude_on_post_ids" id="exclude_on_post_ids" value="<?php echo esc_attr( stripslashes( $crp_settings['exclude_on_post_ids'] ) ); ?>"  style="width:250px">
					<p class="description"><?php _e( 'Comma separated list of post, page or custom post type IDs. e.g. 188,320,500', CRP_LOCAL_NAME ); ?></p>
				</td>
			</tr>
			<tr><th scope="row"><?php _e( 'Exclude display of related posts on these post types.', CRP_LOCAL_NAME ); ?></th>
				<td>
					<?php foreach ( $wp_post_types as $wp_post_type ) {
						$post_type_op = '<label><input type="checkbox" name="exclude_on_post_types[]" value="' . $wp_post_type . '" ';
						if ( in_array( $wp_post_type, $posts_types_excl ) ) $post_type_op .= ' checked="checked" ';
						$post_type_op .= ' />' . $wp_post_type . '</label>&nbsp;&nbsp;';
						echo $post_type_op;
					}
					?>
					<p class="description"><?php _e( 'The related posts will not display on any of the above selected post types', CRP_LOCAL_NAME ); ?></p>
				</td>
			</tr>
			<tr style="vertical-align: top; background: #eee"><th scope="row" colspan="2"><?php _e( 'Customize the output:', CRP_LOCAL_NAME ); ?></th>
			</tr>
			<tr><th scope="row"><label for="before_list"><?php _e( 'HTML to display before the list of posts: ', CRP_LOCAL_NAME ); ?></label></th>
				<td><input type="textbox" name="before_list" id="before_list" value="<?php echo esc_attr( stripslashes( $crp_settings['before_list'] ) ); ?>" style="width:250px" /></td>
			</tr>
			<tr><th scope="row"><label for="before_list_item"><?php _e( 'HTML to display before each list item: ', CRP_LOCAL_NAME ); ?></label></th>
				<td><input type="textbox" name="before_list_item" id="before_list_item" value="<?php echo esc_attr( stripslashes( $crp_settings['before_list_item'] ) ); ?>" style="width:250px" /></td>
			</tr>
			<tr><th scope="row"><label for="after_list_item"><?php _e( 'HTML to display after each list item: ', CRP_LOCAL_NAME ); ?></label></th>
				<td><input type="textbox" name="after_list_item" id="after_list_item" value="<?php echo esc_attr( stripslashes( $crp_settings['after_list_item'] ) ); ?>" style="width:250px" /></td>
			</tr>
			<tr><th scope="row"><label for="after_list"><?php _e( 'HTML to display after the list of posts: ', CRP_LOCAL_NAME ); ?></label></th>
				<td><input type="textbox" name="after_list" id="after_list" value="<?php echo esc_attr( stripslashes( $crp_settings['after_list'] ) ); ?>" style="width:250px" /></td>
			</tr>
			<tr style="vertical-align: top; background: #eee"><th scope="row" colspan="2"><?php _e( 'Post thumbnail options:', CRP_LOCAL_NAME ); ?></th>
			</tr>
			<tr><th scope="row"><label for="post_thumb_op"><?php _e( 'Location of post thumbnail:', CRP_LOCAL_NAME ); ?></label></th>
				<td>
					<label>
					<input type="radio" name="post_thumb_op" value="inline" id="post_thumb_op_0" <?php if ( 'inline' == $crp_settings['post_thumb_op'] ) echo 'checked="checked"' ?> />
					<?php _e( 'Display thumbnails inline with posts, before title', CRP_LOCAL_NAME ); ?></label>
					<br />
					<label>
					<input type="radio" name="post_thumb_op" value="after" id="post_thumb_op_1" <?php if ( 'after' == $crp_settings['post_thumb_op'] ) echo 'checked="checked"' ?> />
					<?php _e( 'Display thumbnails inline with posts, after title', CRP_LOCAL_NAME ); ?></label>
					<br />
					<label>
					<input type="radio" name="post_thumb_op" value="thumbs_only" id="post_thumb_op_2" <?php if ( 'thumbs_only' == $crp_settings['post_thumb_op'] ) echo 'checked="checked"' ?> />
					<?php _e( 'Display only thumbnails, no text', CRP_LOCAL_NAME ); ?></label>
					<br />
					<label>
					<input type="radio" name="post_thumb_op" value="text_only" id="post_thumb_op_3" <?php if ( 'text_only' == $crp_settings['post_thumb_op'] ) echo 'checked="checked"' ?> />
					<?php _e( 'Do not display thumbnails, only text.', CRP_LOCAL_NAME ); ?></label>
					<br />
				</td>
			</tr>
			<tr><th scope="row"><label for="thumb_width"><?php _e( 'Maximum width of the thumbnail: ', CRP_LOCAL_NAME ); ?></label></th>
				<td><input type="textbox" name="thumb_width" id="thumb_width" value="<?php echo esc_attr(stripslashes($crp_settings['thumb_width'])); ?>" style="width:50px" />px</td>
			</tr>
			<tr><th scope="row"><label for="thumb_height"><?php _e( 'Maximum height of the thumbnail: ', CRP_LOCAL_NAME ); ?></label></th>
				<td><input type="textbox" name="thumb_height" id="thumb_height" value="<?php echo esc_attr(stripslashes($crp_settings['thumb_height'])); ?>" style="width:50px" />px</td>
			</tr>
			<tr><th scope="row"><label for="thumb_html"><?php _e( 'Style attributes / Width and Height HTML attributes:', CRP_LOCAL_NAME ); ?></label></th>
				<td>
					<label>
					<input type="radio" name="thumb_html" value="css" id="thumb_html_0" <?php if ( 'css' == $crp_settings['thumb_html'] ) echo 'checked="checked"' ?> />
					<?php _e( 'Style attributes are used for width and height. <code>style="max-width:' . $crp_settings['thumb_width'] . 'px;max-height:' . $crp_settings['thumb_height'] . 'px;"</code>', CRP_LOCAL_NAME ); ?></label>
					<br />
					<label>
					<input type="radio" name="thumb_html" value="html" id="thumb_html_1" <?php if ( 'html' == $crp_settings['thumb_html'] ) echo 'checked="checked"' ?> />
					<?php _e( 'HTML width and height attributes are used for width and height. <code>width="' . $crp_settings['thumb_width'] . '" height="' . $crp_settings['thumb_height'] . '"</code>', CRP_LOCAL_NAME ); ?></label>
					<br />
				</td>
			</tr>
			<tr><th scope="row"><label for="thumb_timthumb"><?php _e( 'Use timthumb to generate thumbnails? ', CRP_LOCAL_NAME ); ?></label></th>
				<td><input type="checkbox" name="thumb_timthumb" id="thumb_timthumb" <?php if ( $crp_settings['thumb_timthumb'] ) echo 'checked="checked"' ?> /> 
					<p class="description"><?php _e( 'If checked, <a href="http://www.binarymoon.co.uk/projects/timthumb/">timthumb</a> will be used to generate thumbnails', CRP_LOCAL_NAME ); ?></p>
				</td>
			</tr>
			<tr><th scope="row"><label for="thumb_timthumb_q"><?php _e( 'Quality of thumbnails generated by timthumb', CRP_LOCAL_NAME ); ?></label></th>
				<td>
					<input type="textbox" name="thumb_timthumb_q" id="thumb_timthumb_q" value="<?php echo esc_attr( stripslashes( $crp_settings['thumb_timthumb_q'] ) ); ?>" style="width:30px" />
					<p class="description"><?php _e( 'Enter values between 0 and 100 only. 100 is highest quality, however, it is also the highest file size. Suggested maximum value is 95. CRP default is 75.', CRP_LOCAL_NAME ); ?></p>
				</td>
			</tr>
			<tr><th scope="row"><label for="thumb_meta"><?php _e( 'Post thumbnail meta field name: ', CRP_LOCAL_NAME ); ?></label></th>
				<td><input type="textbox" name="thumb_meta" id="thumb_meta" value="<?php echo esc_attr( stripslashes( $crp_settings['thumb_meta'] ) ); ?>"> 
					<p class="description"><?php _e( 'The value of this field should contain the image source and is set in the <em>Add New Post</em> screen', CRP_LOCAL_NAME ); ?></p>
				</td>
			</tr>
			<tr><th scope="row"><label for="scan_images"><?php _e( 'If the postmeta is not set, then should the plugin extract the first image from the post?', CRP_LOCAL_NAME ); ?></label></th>
				<td><input type="checkbox" name="scan_images" id="scan_images" <?php if ( $crp_settings['scan_images'] ) echo 'checked="checked"' ?> /> 
					<p class="description"><?php _e( 'This can slow down the loading of your page if the first image in the related posts is large in file-size', CRP_LOCAL_NAME ); ?></p>
				</td>
			</tr>
			<tr><th scope="row"><label for="thumb_default_show"><?php _e( 'Use default thumbnail? ', CRP_LOCAL_NAME ); ?></label></th>
				<td><input type="checkbox" name="thumb_default_show" id="thumb_default_show" <?php if ( $crp_settings['thumb_default_show'] ) echo 'checked="checked"' ?> /> 
					<p class="description"><?php _e( 'If checked, when no thumbnail is found, show a default one from the URL below. If not checked and no thumbnail is found, no image will be shown.', CRP_LOCAL_NAME ); ?></p>
				</td>
			</tr>
			<tr><th scope="row"><label for="thumb_default"><?php _e( 'Default thumbnail: ', CRP_LOCAL_NAME ); ?></label></th>
				<td><input type="textbox" name="thumb_default" id="thumb_default" value="<?php echo esc_attr( stripslashes( $crp_settings['thumb_default'] ) ); ?>" style="width:100%"> 
				  	<?php if( '' != $crp_settings['thumb_default'] ) echo "<img src='{$crp_settings['thumb_default']}' style='max-width:200px' />"; ?>
					<p class="description"><?php _e( 'The plugin will first check if the post contains a thumbnail. If it doesn\'t then it will check the meta field. If this is not available, then it will show the default image as specified above', CRP_LOCAL_NAME ); ?></p>
				</td>
			</tr>
			</table>
	      </div>
	    </div>
	    <div id="feedopdiv" class="postbox closed"><div class="handlediv" title="Click to toggle"><br /></div>
	      <h3 class='hndle'><span><?php _e( 'Feed options', CRP_LOCAL_NAME ); ?></span></h3>
	      <div class="inside">
			<table class="form-table">
			<tr><th scope="row" colspan="2"><?php _e( 'Below options override the related posts settings for your blog feed. These only apply if you have selected to add related posts to Feeds in the General Options tab.', CRP_LOCAL_NAME ); ?></th>
			</tr>
			<tr><th scope="row"><label for="limit_feed"><?php _e( 'Number of related posts to display: ', CRP_LOCAL_NAME ); ?></label></th>
				<td><input type="textbox" name="limit_feed" id="limit_feed" value="<?php echo esc_attr( stripslashes( $crp_settings['limit_feed'] ) ); ?>"></td>
			</tr>
			<tr><th scope="row"><label for="show_excerpt_feed"><?php _e( 'Show post excerpt in list?', CRP_LOCAL_NAME ); ?></label></th>
				<td><input type="checkbox" name="show_excerpt_feed" id="show_excerpt_feed" <?php if ( $crp_settings['show_excerpt_feed'] ) echo 'checked="checked"' ?> /></td>
			</tr>
			<tr><th scope="row"><label for="post_thumb_op_feed"><?php _e( 'Location of post thumbnail:', CRP_LOCAL_NAME ); ?></label></th>
				<td>
					<label>
					<input type="radio" name="post_thumb_op_feed" value="inline" id="post_thumb_op_feed_0" <?php if ( 'inline' == $crp_settings['post_thumb_op_feed'] ) echo 'checked="checked"' ?> />
					<?php _e( 'Display thumbnails inline with posts, before title', CRP_LOCAL_NAME ); ?></label>
					<br />
					<label>
					<input type="radio" name="post_thumb_op_feed" value="after" id="post_thumb_op_feed_1" <?php if ( 'after' == $crp_settings['post_thumb_op_feed'] ) echo 'checked="checked"' ?> />
					<?php _e( 'Display thumbnails inline with posts, after title', CRP_LOCAL_NAME ); ?></label>
					<br />
					<label>
					<input type="radio" name="post_thumb_op_feed" value="thumbs_only" id="post_thumb_op_feed_2" <?php if ( 'thumbs_only' == $crp_settings['post_thumb_op_feed'] ) echo 'checked="checked"' ?> />
					<?php _e( 'Display only thumbnails, no text', CRP_LOCAL_NAME ); ?></label>
					<br />
					<label>
					<input type="radio" name="post_thumb_op_feed" value="text_only" id="post_thumb_op_feed_3" <?php if ( 'text_only' == $crp_settings['post_thumb_op_feed'] ) echo 'checked="checked"' ?> />
					<?php _e( 'Do not display thumbnails, only text.', CRP_LOCAL_NAME ); ?></label>
					<br />
				</td>
			</tr>
			<tr><th scope="row"><label for="thumb_width_feed"><?php _e( 'Maximum width of the thumbnail: ', CRP_LOCAL_NAME ); ?></label></th>
				<td><input type="textbox" name="thumb_width_feed" id="thumb_width_feed" value="<?php echo esc_attr( stripslashes( $crp_settings['thumb_width_feed'] ) ); ?>" style="width:30px" />px</td>
			</tr>
			<tr><th scope="row"><label for="thumb_height_feed"><?php _e( 'Maximum height of the thumbnail: ', CRP_LOCAL_NAME ); ?></label></th>
				<td><input type="textbox" name="thumb_height_feed" id="thumb_height_feed" value="<?php echo esc_attr( stripslashes( $crp_settings['thumb_height_feed'] ) ); ?>" style="width:30px" />px</td>
			</tr>
			</table>		
	      </div>
	    </div>
	    <div id="customcssdiv" class="postbox closed"><div class="handlediv" title="Click to toggle"><br /></div>
	      <h3 class='hndle'><span><?php _e( 'Custom styles', CRP_LOCAL_NAME ); ?></span></h3>
	      <div class="inside">
			<table class="form-table">
				<tr><th scope="row"><label for="include_default_style"><?php _e( 'Use default style included in the plugin?', CRP_LOCAL_NAME ); ?></label></th>
				  <td>
				  	<input type="checkbox" name="include_default_style" id="include_default_style" <?php if ( $crp_settings['include_default_style'] ) echo 'checked="checked"' ?> /> 
				  	<p class="description"><?php _e( 'Contextual Related Posts includes a default style that makes your popular posts list to look pretty. Check the box above if you want to use this.', CRP_LOCAL_NAME ); ?></p>
				  	<p class="description"><?php _e( 'Enabling this option will automatically turn on the thumbnails and set their width and height to 150px. Disabling this will not turn off thumbnails or change their dimensions.', CRP_LOCAL_NAME ); ?></p>
				  </td>
				</tr>
				<tr><th scope="row" colspan="2"><?php _e( 'Custom CSS to add to header:', CRP_LOCAL_NAME ); ?></th>
				</tr>
				<tr>
				  <td scope="row" colspan="2"><textarea name="custom_CSS" id="custom_CSS" rows="15" cols="80" style="width:100%"><?php echo stripslashes( $crp_settings['custom_CSS'] ); ?></textarea>
				  <p class="description"><?php _e( 'Do not include <code>style</code> tags. Check out the <a href="http://wordpress.org/extend/plugins/contextual-related-posts/faq/" target="_blank">FAQ</a> for available CSS classes to style.', CRP_LOCAL_NAME ); ?></p>
				</td></tr>
			</table>		
	      </div>
	    </div>

		<p>
		  <input type="submit" name="crp_save" id="crp_save" value="<?php _e( 'Save Options', CRP_LOCAL_NAME ); ?>" class="button button-primary" />
		  <input name="crp_default" type="submit" id="crp_default" value="<?php _e( 'Default Options', CRP_LOCAL_NAME ); ?>" class="button button-secondary" onclick="if (!confirm('<?php _e( "Do you want to set options to Default?", CRP_LOCAL_NAME ); ?>')) return false;" />
		  <input name="crp_recreate" type="submit" id="crp_recreate" value="<?php _e( 'Recreate Index', CRP_LOCAL_NAME ); ?>" class="button button-secondary" onclick="if (!confirm('<?php _e( "Are you sure you want to recreate the index?", CRP_LOCAL_NAME ); ?>')) return false;" />
		</p>
		<?php wp_nonce_field( 'crp-plugin' ) ?>
	  </form>
	</div><!-- /post-body-content -->
	<div id="postbox-container-1" class="postbox-container">
	  <div id="side-sortables" class="meta-box-sortables ui-sortable">
	    <div id="donatediv" class="postbox"><div class="handlediv" title="Click to toggle"><br /></div>
	      <h3 class='hndle'><span><?php _e( 'Support the development', CRP_LOCAL_NAME ); ?></span></h3>
	      <div class="inside">
			<div id="donate-form">
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
				<input type="hidden" name="cmd" value="_xclick">
				<input type="hidden" name="business" value="donate@ajaydsouza.com">
				<input type="hidden" name="lc" value="IN">
				<input type="hidden" name="item_name" value="Donation for Contextual Related Posts">
				<input type="hidden" name="item_number" value="crp">
				<strong><?php _e( 'Enter amount in USD: ', CRP_LOCAL_NAME ); ?></strong> <input name="amount" value="10.00" size="6" type="text"><br />
				<input type="hidden" name="currency_code" value="USD">
				<input type="hidden" name="button_subtype" value="services">
				<input type="hidden" name="bn" value="PP-BuyNowBF:btn_donate_LG.gif:NonHosted">
				<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="<?php _e( 'Send your donation to the author of', CRP_LOCAL_NAME ); ?> Contextual Related Posts?">
				<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
				</form>
			</div>
	      </div>
	    </div>
	    <div id="followdiv" class="postbox"><div class="handlediv" title="Click to toggle"><br /></div>
	      <h3 class='hndle'><span><?php _e( 'Follow me', CRP_LOCAL_NAME ); ?></span></h3>
	      <div class="inside">
			<div id="follow-us">
				<iframe src="//www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2Fajaydsouzacom&amp;width=292&amp;height=62&amp;colorscheme=light&amp;show_faces=false&amp;border_color&amp;stream=false&amp;header=true&amp;appId=113175385243" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:292px; height:62px;" allowTransparency="true"></iframe>
				<div style="text-align:center"><a href="https://twitter.com/ajaydsouza" class="twitter-follow-button" data-show-count="false" data-size="large" data-dnt="true">Follow @ajaydsouza</a>
				<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script></div>
			</div>
	      </div>
	    </div>
	    <div id="qlinksdiv" class="postbox"><div class="handlediv" title="Click to toggle"><br /></div>
	      <h3 class='hndle'><span><?php _e( 'Quick links', CRP_LOCAL_NAME ); ?></span></h3>
	      <div class="inside">
	        <div id="quick-links">
				<ul>
					<li><a href="http://ajaydsouza.com/wordpress/plugins/contextual-related-posts/"><?php _e( 'Contextual Related Posts plugin page', CRP_LOCAL_NAME ); ?></a></li>
					<li><a href="http://ajaydsouza.com/wordpress/plugins/"><?php _e( 'Other plugins', CRP_LOCAL_NAME ); ?></a></li>
					<li><a href="http://ajaydsouza.com/"><?php _e( 'Ajay\'s blog', CRP_LOCAL_NAME ); ?></a></li>
					<li><a href="https://wordpress.org/plugins/contextual-related-posts/faq/"><?php _e( 'FAQ', CRP_LOCAL_NAME ); ?></a></li>
					<li><a href="http://wordpress.org/support/plugin/contextual-related-posts"><?php _e( 'Support', CRP_LOCAL_NAME ); ?></a></li>
					<li><a href="https://wordpress.org/support/view/plugin-reviews/contextual-related-posts"><?php _e( 'Reviews', CRP_LOCAL_NAME ); ?></a></li>
				</ul>
	        </div>
	      </div>
	    </div>
	  </div><!-- /side-sortables -->
	</div><!-- /postbox-container-1 -->
	</div><!-- /post-body -->
	<br class="clear" />
	</div><!-- /poststuff -->
</div><!-- /wrap -->
<?php
}


/**
 * Add a link under Settings to the plugins settings page.
 * 
 * @access public
 * @return void
 */
function crp_adminmenu() {
	if ( ( function_exists( 'add_options_page' ) ) ) {
		$plugin_page = add_options_page( __( "Contextual Related Posts", CRP_LOCAL_NAME ), __( "Related Posts", CRP_LOCAL_NAME ), 'manage_options', 'crp_options', 'crp_options' );
		add_action( 'admin_head-'. $plugin_page, 'crp_adminhead' );
	}
}
add_action( 'admin_menu', 'crp_adminmenu' );


/**
 * Function to add CSS and JS to the Admin header.
 * 
 * @access public
 * @return void
 */
function crp_adminhead() {
	global $crp_url;
	wp_enqueue_script( 'common' );
	wp_enqueue_script( 'wp-lists' );
	wp_enqueue_script( 'postbox' );
?>
	<style type="text/css">
	.postbox .handlediv:before {
		right:12px;
		font:400 20px/1 dashicons;
		speak:none;
		display:inline-block;
		top:0;
		position:relative;
		-webkit-font-smoothing:antialiased;
		-moz-osx-font-smoothing:grayscale;
		text-decoration:none!important;
		content:'\f142';
		padding:8px 10px;
	}
	.postbox.closed .handlediv:before {
		content: '\f140';
	}
	.wrap h2:before {
	    content: "\f237";
	    display: inline-block;
	    -webkit-font-smoothing: antialiased;
	    font: normal 29px/1 'dashicons';
	    vertical-align: middle;
	    margin-right: 0.3em;
	}
	</style>

	<script type="text/javascript">
		//<![CDATA[
		jQuery(document).ready( function($) {
			// close postboxes that should be closed
			$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
			// postboxes setup
			postboxes.add_postbox_toggles('crp_options');
		});
		//]]>
	</script>
	
	<link rel="stylesheet" type="text/css" href="<?php echo $crp_url ?>/wick/wick.css" />
	<script type="text/javascript" language="JavaScript">
		//<![CDATA[
		function clearCache() {
			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			jQuery.post(ajaxurl, {action: 'crp_clear_cache'}, function(response, textStatus, jqXHR) {
				alert( response.message );
			}, 'json');
		}
		
		function checkForm() {
		answer = true;
		if (siw && siw.selectingSomething)
			answer = false;
		return answer;
		}//
	
		<?php
		function wick_data() {
			global $wpdb;
			
			$categories = get_categories( 'hide_empty=0' );
			$str = 'collection = [';
			foreach ( $categories as $cat ) {
				$str .= "'" . $cat->slug . "',";
			}
			$str = substr( $str, 0, -1 );	// Remove trailing comma
			$str .= '];';
			
			echo $str;
		}
		wick_data();
		?>
	//]]>
	</script>
	<script type="text/javascript" src="<?php echo $crp_url ?>/wick/wick.js"></script>
<?php 
}


/**
 * Function to add a notice to the admin page.
 * 
 * @access public
 * @return string Echoed string
 */
function crp_admin_notice() {
	$plugin_settings_page = '<a href="' . admin_url( 'options-general.php?page=crp_options' ) . '">' . __( 'plugin settings page', CRP_LOCAL_NAME ) . '</a>';

	if ( ! current_user_can( 'manage_options' ) ) return;

    echo '<div class="error">
       <p>' . __( "Contextual Related Posts plugin has just been installed / upgraded. Please visit the {$plugin_settings_page} to configure.", CRP_LOCAL_NAME ).'</p>
    </div>';
}
// add_action( 'admin_notices', 'crp_admin_notice' );


/**
 * Function to clear the CRP Cache with Ajax.
 * 
 * @access public
 * @return void
 */
function crp_ajax_clearcache() {
	global $wpdb; // this is how you get access to the database

	$rows = $wpdb->query( "
		DELETE FROM " . $wpdb->postmeta . "
		WHERE meta_key='crp_related_posts'
	" );

	$rows2 = $wpdb->query( "
		DELETE FROM " . $wpdb->postmeta . "
		WHERE meta_key='crp_related_posts_widget'
	" );

	// Did an error occur?
	if ( ( $rows === false ) && ( $rows2 === false ) ) {
		exit( json_encode( array(
			'success' => 0,
			'message' => __('An error occurred clearing the cache. Please contact your site administrator.\n\nError message:\n', CRP_LOCAL_NAME) . $wpdb->print_error(),
		) ) );
	} else {	// No error, return the number of
		exit( json_encode( array(
			'success' => 1,
			'message' => ($rows+$rows2) . __(' cached row(s) cleared', CRP_LOCAL_NAME),
		) ) );
	}
}
add_action( 'wp_ajax_crp_clear_cache', 'crp_ajax_clearcache' );


?>
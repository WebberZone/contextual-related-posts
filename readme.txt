=== Contextual Related Posts ===
Tags: related posts, related, related articles, contextual related posts, similar posts, related posts widget
Contributors: webberzone, Ajay
Donate link: https://ajaydsouza.com/donate/
Stable tag: 3.1.1
Requires at least: 5.3
Tested up to: 5.9
Requires PHP: 7.1
License: GPLv2 or later

Add related posts to your WordPress site with inbuilt caching. Supports thumbnails, shortcodes, widgets and custom post types!

== Description ==

[Contextual Related Posts](https://webberzone.com/plugins/contextual-related-posts/) is a powerful plugin for WordPress that allows you to display a list of related posts on your website and in your feed.

The list is based on the content of the title and/or content of the posts which makes them more relevant and more likely to be of interest to your readers. This allows you to retain visitors, reduce bounce rates and refresh old entries.

Although several similar plugins exist today, Contextual Related Posts is one of the most feature rich plugins with support for thumbnails, shortcodes, widgets, custom post types and CSS styles. The inbuilt cache ensures that you have related posts without excessive load on your server.

And the default inbuilt styles allow you to switch between gorgeous thumbnail-rich related posts or a similar text display!

= Key features =

* **Automatic**: CRP will start displaying related posts on your site and feed automatically after the content when you activate the plugin. No need to edit template files
* **Manual install**: Want more control over placement? Check the [FAQ](http://wordpress.org/extend/plugins/contextual-related-posts/faq/) on which functions are available for manual install
* **Gutenberg / Block Editor support**: You can find a block called "Related Posts [CRP]" with its own configurable set of options
* **Widgets**: Add related posts to widgetized area of your theme. Lots of options available
* **Shortcode**: Use `[crp]` to display the posts anywhere you want in the post content
* **REST API**: Fetch related posts at `contextual-related-posts/v1/posts/<id>/`
* **The algorithm**: Find related posts by title and/or content of the current post
* **Caching**: Related posts output is automatically cached as visitors browse through your site
* **Exclusions**: Exclude posts from categories from being displayed in the list. Or you can exclude posts or pages by ID
* **Custom post types**: The related posts list lets you include posts, pages, attachments or any other custom post type!
* **Thumbnail support**:
    * Support for WordPress post thumbnails. CRP will create a custom image size (`crp_thumbnail`) with the dimensions specified in the Settings page
    * Auto-extract the first image in your post to be displayed as a thumbnail
    * Manually enter the URL of the thumbnail via [WordPress meta fields](http://codex.wordpress.org/Custom_Fields). Specify this using the meta box in your Edit screens.
    * Optionally, use timthumb to resize images or use your own filter function to resize post images
* **Styles**: The output is wrapped in CSS classes which allows you to easily style the list. You can enter your custom CSS styles from within WordPress Admin area or use the style included.
* **Customisable output**:
    * Display excerpts in post. You can select the length of the excerpt in words
    * Customise which HTML tags to use for displaying the output in case you don't prefer the default `list` format
* **Extendable code**: CRP has tonnes of filters and actions that allow any developer to easily add features, edit outputs, etc.

= mySQL FULLTEXT indices =

On activation, the plugin creates three mySQL FULLTEXT indices (or indexes) that are then used to find the related posts in the `*_posts`. These are for `post_content`, `post_title` and `(post_title,post_content)`. If you're running a multisite installation, then this is created for each of the blogs on activation. All these indices occupy space in your mySQL database but are essential for the plugin to run.

You have two sets of options in the settings page which allows you to remove these indices when you deactivate or delete the plugin. The latter is true by default.

= GDPR =
Contextual Related Posts is GDPR compliant as it doesn't collect any personal data about your visitors when installed out of the box. All posts are processed on your site and not sent to any external service.

YOU ARE RESPONSIBLE FOR ENSURING THAT ALL GDPR REQUIREMENTS ARE MET ON YOUR WEBSITE.

= Donations =

I spend a significant amount of my free time maintaining, updating and more importantly supporting this plugin. Those who have sought support in the support forums know that I have done my best to answer your question and solve your problem.
If you have been using this plugin and find this useful, do consider making a donation. This helps me pay for my hosting and domains.

= Contribute =

Contextual Related Posts is also available on [Github](https://github.com/WebberZone/contextual-related-posts).
So, if you've got some cool feature that you'd like to implement into the plugin or a bug you've been able to fix, consider forking the project and sending me a pull request.

Bug reports are [welcomed on GitHub](https://github.com/WebberZone/contextual-related-posts/issues). Please note GitHub is _not_ a support forum and issues that aren't properly qualified as bugs will be closed.

= Translations =

Contextual Related Posts is available for [translation directly on WordPress.org](https://translate.wordpress.org/projects/wp-plugins/contextual-related-posts). Check out the official [Translator Handbook](https://make.wordpress.org/polyglots/handbook/rosetta/theme-plugin-directories/) to contribute.

== Installation ==

= WordPress install (The easy way) =

1. Navigate to Plugins within your WordPress Admin Area

2. Click "Add new" and in the search box enter "Contextual Related Posts"

3. Find the plugin in the list (usually the first result) and click "Install Now"

= Manual install =

1. Download the plugin

2. Extract the contents of contextual-related-posts.zip to wp-content/plugins/ folder. You should get a folder called contextual-related-posts.

3. Activate the Plugin in WP-Admin under the Plugins screen


== Screenshots ==

1. CRP options in WP-Admin - General options
2. CRP options in WP-Admin - List tuning options
3. CRP options in WP-Admin - Output options
4. CRP options in WP-Admin - Thumbnail options
5. CRP options in WP-Admin - Styles
6. CRP options in WP-Admin - Feed options
7. Default style of Related Posts
8. Contextual Related Post metabox in the Edit Posts screen
9. CRP Widget
10. Tools page
11. Gutenberg block - Settings sidebar

== Frequently Asked Questions ==

If your question isn't listed here, please create a new post at the [WordPress.org support forum](http://wordpress.org/support/plugin/contextual-related-posts). It is the fastest way to get support as I monitor the forums regularly. I also provide [premium *paid* support via email](https://webberzone.com/support/).

= How can I customise the output? =

Contextual Related Posts is highly customizable. There are several configurable options in the Settings page and you can use CSS to customize the outputs. Learn more by reading [this article](https://webberzone.com/support/knowledgebase/customising-the-output-of-contextual-related-posts/).

= How does the plugin select thumbnails? =

If you enable thumbnails, the plugin will try to find the correct thumbnail in this order:

1. Post meta field: This is the meta field value you can use when editing your post. The default is `post-image`. Change it in the Settings page

2. Post Thumbnail image: The image that you can set under Featured Image

3. First image in the post: The plugin will try to fetch the first image in the post. Toggle this in the Settings page

4. First child image attached to the post

5. Video Thumbnails: Meta field set by [Video Thumbnails plugin](https://wordpress.org/plugins/video-thumbnails/)

6. Site Icon: this is typically set using Customizer

7. Default Thumbnail: If enabled, it will use the default thumbnail that you specify in the Settings page

= Template tags =

The following functions are available in case you wish to do a manual install of the posts by editing the theme files.

**echo_crp( $args = array() )**

Echoes the list of posts wherever you add the this function. You can also use this function to display related posts on any type of page generated by WordPress including homepage and archive pages.

Usage: `<?php if ( function_exists( 'echo_crp' ) ) { echo_crp(); } ?>` to your template file where you want the related posts to be displayed.

**get_crp_posts_id( $args = array() )**

Takes a post ID and returns the related post IDs as an object.

Usage: `<?php if ( function_exists( 'get_crp_posts_id' ) ) { get_crp_posts_id( array(
	'postid' => $postid,
	'limit' => $limit,
) ); } ?>`

Parameters:

*$postid* : The ID of the post you'd like to fetch. By default the current post is fetched. Use within the Loop for best results.

*$limit* : Maximum number of posts to return. The actual number displayed may be lower depending on the matching algorithm and the category / post exclusion settings.

This is not an exhaustive set of Parameters. For the full list of Parameters check out the shortcode FAQ below.

= Shortcodes =

You can insert the related posts anywhere in your post using the `[crp]` shortcode. View [this article in the knowledge base](https://webberzone.com/support/knowledgebase/contextual-related-posts-shortcode/) for more details.


== Changelog ==

= 3.1.1 =

Release post: [https://webberzone.com/blog/contextual-related-posts-v3-1-0/](https://webberzone.com/blog/contextual-related-posts-v3-1-0/)

* Enhancements:
    * Don't clear cache when saving settings. The cache can be cleared in the Tools page
    * Default thumbnail is now prioritized over the site icon

* Bug fixes:
    * Limiting of characters didn't work properly
    * Fixed link to Tools menu under Settings. Tools button link is better displayed
    * Fixed activation when new blog is created on multisite

= 3.1.0 =

* Features:
    * REST API support - you can now fetch the related posts via the REST API. Fetch posts at `/contextual-related-posts/v1/posts/<id>`
    * New setting in the metabox to exclude specific terms

* Enhancements/modifications:
    * Thumbnail function uses the size instead of exact array of sizes to better select the appropriate thumbnail image size
    * Use site icon if no other thumbnail is found
    * Use both `post_title` and `post_content` fields for matching even when match content setting is off
    * Passing `post_type` and `posts_per_page` arguments will be respected instead of being overridden

* Bug fixes:
    * Don't enqueue wp-editor on widgets.php
    * [WP_Query stopwords](https://developer.wordpress.org/reference/classes/wp_query/get_search_stopwords/) are stripped from content that is matched
    * Manual posts are added after the automatic posts are shuffled

= Earlier versions =

For the changelog of earlier versions, please refer to the separate changelog.txt file or the [releases page on Github](https://github.com/WebberZone/contextual-related-posts/releases).


== Upgrade Notice ==

= 3.1.1 =
Bug fixes. Please read the release post on https://webberzone.com

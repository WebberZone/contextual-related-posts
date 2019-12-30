=== Contextual Related Posts ===
Tags: related posts, related, related articles, contextual related posts, similar posts, related posts widget
Contributors: webberzone, Ajay
Donate link: https://ajaydsouza.com/donate/
Stable tag: trunk
Requires at least: 4.8
Tested up to: 5.3
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

= Extensions/Addons =

* [Related Posts by Categories and Tags](https://webberzone.com/downloads/crp-taxonomy/)

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

Several customization options are available via the Settings page in WordPress Admin. You can access this via <strong>Settings &raquo; Related Posts</strong>

The plugin also provides you with a set of CSS classes that allow you to style your posts by adding code to the *style.css* sheet.

The main CSS classes / IDs are:

* **crp_related**: ID of the main wrapper `div`. This is only displayed on singular pages, i.e. post, page and attachment

* **crp_related**: Class of the main wrapper `div`. If you are displaying the related posts on non-singular pages, then you should style this

* **crp_title**: Class of the `span` tag for title of the post

* **crp_excerpt**: Class of the `span` tag for excerpt (if included)

* **crp_thumb**: Class of the post thumbnail `img` tag


= How does the plugin select thumbnails? =

The plugin selects thumbnails in the following order:

1. Post meta field: This is the meta field value you can use when editing your post. The default is `post-image`

2. Post Thumbnail image: The image that you can set while editing your post in WordPress &raquo; New Post screen

3. First image in the post: The plugin will try to fetch the first image in the post

3. Video Thumbnails: Meta field set by <a href="https://wordpress.org/extend/plugins/video-thumbnails/">Video Thumbnails</a>

4. Default Thumbnail: If enabled, it will use the default thumbnail that you specify in the Settings screen


= Manual install =

The following functions are available in case you wish to do a manual install of the posts by editing the theme files.

**echo_crp( $args = array() )**

Echoes the list of posts wherever you add the this function. You can also use this function to display related posts on any type of page generated by WordPress including homepage and archive pages.

Usage: `<?php if ( function_exists( 'echo_crp' ) ) { echo_crp(); } ?>` to your template file where you want the related posts to be displayed.


**get_crp_posts_id()**

Takes a post ID and returns the related post IDs as an object.

Usage: `<?php if ( function_exists( 'get_crp_posts_id' ) ) { get_crp_posts_id( array(
	'postid' => $postid,
	'limit' => $limit,
) ); } ?>`

Parameters:

*$postid* : The ID of the post you'd like to fetch. By default the current post is fetched. Use within the Loop for best results.

*$limit* : Maximum number of posts to return. The actual number displayed may be lower depending on the matching algorithm and the category / post exclusion settings.


= Shortcodes =

You can insert the related posts anywhere in your post using the `[crp]` shortcode. View [this article in the knowledge base](https://webberzone.com/support/knowledgebase/contextual-related-posts-shortcode/) for more details.


== Changelog ==

= 2.8.0 =

Release post: [https://webberzone.com/blog/contextual-related-posts-v2-8-0/](https://webberzone.com/blog/contextual-related-posts-v2-8-0/)

* Features:
    * New block for Gutenberg aka the block editor. The block is called **Related Posts [CRP]** and you can find it under the widgets category
    * Two new settings in the widget: **Order posts** and **Randomize order** that allows the global setting to be overridden
    * New setting called **Keyword** in the Meta box where you can enter a word or a phrase to find related posts. If entered, the plugin will continue to search the `post_title` and `post_content` fields but will use this keyword instead of the values of the title and content of the source post

* Enhancements:
    * Show author, Show date, Show post excerpt and Post thumbnail settings will show a message that they cannnot be modified in case the Rounded thumbnails or No text styles are selected

* Bug fixes:
	* Selecting date order now orders the related posts by newest first
    * Fixed PHP warning in the widget
    * Stop using `current_time( 'timestamp' )`
    * Fixes incorrect thumbnail image displayed for attachments in the related posts list

= Earlier versions =

For the changelog of earlier versions, please refer to the separate changelog.txt file.


== Upgrade Notice ==

= 2.8.0 =
New block for Gutenberg. Enhancements to the widget. New Keyword setting.
Check the Changelog for more details or view the release post on [https://webberzone.com](https://webberzone.com)

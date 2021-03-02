=== Contextual Related Posts ===
Tags: related posts, related, related articles, contextual related posts, similar posts, related posts widget
Contributors: webberzone, Ajay
Donate link: https://ajaydsouza.com/donate/
Stable tag: 3.0.7
Requires at least: 5.0
Tested up to: 5.7
Requires PHP: 5.6
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

= 3.0.7 =

Release post: [https://webberzone.com/blog/contextual-related-posts-v3-0-0/](https://webberzone.com/blog/contextual-related-posts-v3-0-0/)

* Bug fix:
    * Replicate old style of exclusion checking of option set in meta. Using the shortcode, manual or block will ignore the meta option to "Disable Related Posts display"

= 3.0.6 =

* Enhancement:
    * Defining `CRP_CACHE_TIME` to `false` will disable expiry
    * Introduced wpml-config.xml file. Title and Custom text for blank output can now be translated with Polylang (and potentially WPML)

* Bug fix:
    * Exclude on categories did not work
    * Posts would trigger a "SHOW FULL COLUMNS FROM" error if they had ' from' in the title
    * Manual posts did not work properly - all post types and all posts are properly fetched now

= 3.0.5 =

* Bug fix:
    * Certain posts would trigger a "SHOW FULL COLUMNS FROM" error
    * Forced `.crp_related figure` margin to 0

= 3.0.4 =

* Enhancement/Modifications:
    * `include_cat_ids` and `exclude_categories` will also accept custom taxonomy `term_taxonomy_id`s
    * Thumbnail's `img` tag is wrapped in `<figure>`
    * Remove extra checking for `exclude_categories` in `get_crp`
    * Optimise deleting of cache entries when updating a post - post saving should be significantly faster

= 3.0.3 =

* Enhancement/Modifications:
    * Grid style minimum width is now decided by the width of the thumbnail and long words are wrapped

* Bug fixes:
    * Selecting No style created a 404 error
    * Fixed issue with $attachment_id not being declared in some cases

= 3.0.2 =

* Bug fixes:
    * Fixed issue where Related Posts newer than was set to 0 caused no posts to display
    * Use the original arguments when setting the cache key for CRP_Query
    * Selecting "Blank Output" didn't work

= 3.0.1 =

* Bug fixes:
    * Fixed issue with help tab that broke some sites

= 3.0.0 =

* Features:
    * New CRP_Query class for fetching related posts. This replaces `get_crp_posts_id()` which will be deprecated in a future version
    * CRP Thumbnails now include the `loading="lazy"` attribute added in WordPress 5.5
    * New parameter `more_link_text` that can be passed to `get_crp()` which holds the "read more". Recommended option to customize the more link text using the filter `crp_excerpt_more_link_text` or the more link element using `crp_excerpt_more_link`
    * Three new styles: "Masonry" (like Pinterest), "Grid" and "Rounded thumbnails with CSS grid". Might not work with older browsers
    * Imported settings of [Related Posts by Categories and Tags](https://webberzone.com/downloads/crp-taxonomy/). That plugin is now deprecated with this release.

* Enhancement/Modifications:
    * If WPML or PolyLang are active, `get_crp_posts_id()` and `CRP_Query` will return the translated set of post IDs and external processing is no longer needed
    * Use `wp_img_tag_add_srcset_and_sizes_attr()` to generate srcset and sizes attributes. The original code to display the srcset and sizes attributes will continue to be used
    * Improved caching with inbuilt expiry. Use CRP_CACHE_TIME in your wp-config.php to set how long the cache should be set for. Default is one month
    * CRP_MAX_WORDS has been reduced to 100
    * Dropped the need for FULLTEXT index on post_content which should save some database space
    * Deprecated the following filters: `get_crp_posts_id`, `crp_posts_now_date`, `crp_posts_from_date`, `crp_posts_fields`, `crp_posts_join`, `crp_posts_where`, `crp_posts_groupby`, `crp_posts_having`, `crp_posts_orderby`, `crp_posts_limits`, `get_crp_posts_id_short_circuit`

* Bug fixes:
    * In the settings page, only built-in taxonomies were being incorrectly displayed
    * If "before list item" is empty, then the output was blanked out
    * Settings help has been fixed
    * `crp_get_option` would return an incorrect value if $crp_settings global variable was not set

= Earlier versions =

For the changelog of earlier versions, please refer to the separate changelog.txt file or the [releases page on Github](https://github.com/WebberZone/contextual-related-posts/releases).


== Upgrade Notice ==

= 3.0.7 =
Bug fixes. Please read the release post on https://webberzone.com

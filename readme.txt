=== Contextual Related Posts ===
Tags: related posts, related, similar posts, posts, post, feed, feeds, rss, widget, thumbnail, shortcodes
Contributors: webberzone, Ajay
Donate link: https://ajaydsouza.com/donate/
Stable tag: trunk
Requires at least: 4.1
Tested up to: 4.7
License: GPLv2 or later

Display related posts on your WordPress blog and feed. Supports thumbnails, shortcodes, widgets and custom post types!

== Description ==

[Contextual Related Posts](https://webberzone.com/plugins/contextual-related-posts/) is a powerful plugin for WordPress that allows you to display a list of related posts on your website and in your feed.

The list is based on the content of the title and/or content of the posts which makes them more relevant and more likely to be of interest to your readers. This allows you to retain visitors, reduce bounce rates and refresh old entries.

Although several similar plugins exist today, Contextual Related Posts is one of the most feature rich plugins with support for thumbnails, shortcodes, widgets, custom post types and CSS styles. The inbuilt cache ensures that you have related posts without excessive load on your server.

And the default inbuilt styles allow you to switch between gorgeous thumbnail-rich related posts or a similar text display!

= Key features =

* **Automatic**: CRP will start displaying related posts on your site and feed automatically after the content when you activate the plugin. No need to edit template files
* **Manual install**: Want more control over placement? Check the [FAQ](http://wordpress.org/extend/plugins/contextual-related-posts/faq/) on which functions are available for manual install.
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

= Extensions =

* [CRP Taxonomy Extender](https://webberzone.com/downloads/crp-taxonomy/)

= Donations =

I spend a significant amount of my free time maintaining, updating and more importantly supporting this plugin. Those who have sought support in the support forums know that I have done my best to answer your question and solve your problem.
If you have been using this plugin and find this useful, do consider making a donation. This helps me pay for my hosting and domains.

= Contribute =

Contextual Related Posts is also available on [Github](https://github.com/WebberZone/contextual-related-posts).
So, if you've got some cool feature that you'd like to implement into the plugin or a bug you've been able to fix, consider forking the project and sending me a pull request.

Bug reports are [welcomed on GitHub](https://github.com/WebberZone/contextual-related-posts/issues). Please note GitHub is _not_ a support forum and issues that aren't properly qualified as bugs will be closed.

= Translations =

Contextual Related Posts is now on Transifex with several translations made available by the [WP Translations](http://wp-translations.org). If you're a translator, do consider joining the WP Translations team and contribute towards this and a huge number of WordPress plugins.

Visit [Contextual Related Posts on Transifex](https://www.transifex.com/projects/p/contextual-related-posts/).


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

Usage: `<?php if ( function_exists( 'get_crp_posts_id' ) ) { get_crp_posts_id( array( 'postid' => $postid, 'limit' => $limit ) ); } ?>`

Parameters:

*$postid* : The ID of the post you'd like to fetch. By default the current post is fetched. Use within the Loop for best results.

*$limit* : Maximum number of posts to return. The actual number displayed may be lower depending on the matching algorithm and the category / post exclusion settings.


= Shortcodes =

You can insert the related posts anywhere in your post using the `[crp]` shortcode. The plugin takes three optional attributes `limit`, `heading` and `cache` as follows:

`[crp limit="5" heading="1" cache="1"]`

*limit* : Maximum number of posts to return. The actual number displayed may be lower depending on the matching algorithm and the category / post exclusion settings.

*heading* : By default, the heading you specify in **Title of related posts:** under **Output options** will be displayed. You can override this by specifying your own heading e.g.

`
<h3>Similar posts</h3>
[crp limit="2" heading="0"]
`
*cache* : Cache the output or not? By default the output will be cached for the post you add the shortcode in. You can override this by specifying `cache=0`

In addition to the above, the shortcode takes every option that the plugin supports. See `crp_default_options()` function to see the options that can be set.


== Changelog ==

= 2.3.1 =

* Bug fixes:
	* Replaced `.crp_title` wrapper from `div` to `span`. Empty the cache via the settings page and also your browser cache

= 2.3.0 =

* Features:
	* Shortcode and the widget now have an added parameter for 'offset'. This is useful if you would like to display different widgets/shortcodes but not always start from the first post
	* New option in metabox: "Exclude this post from the related posts list"
	* New option: Insert after nth paragraph

* Enhancements:
	* The generated HTML code uses a single `a href` tag rather than two separate ones per item which is usually better for SEO. If you're not using the Rounded Thumbnail style and using your own custom style, then you might need to reconfigure this
	* New constant `CRP_MAX_WORDS` (default 500) limits the post content to be compared. Add this to your `wp-config.php` file to overwrite

* Bug fixes:
	* Language files initialisation had the wrong text domain
	* Stop updating the thumb settings if the existing value isn't found. Caused incorrect changes in certain installations
	* Force link text to white when using Rounded Thumb style
	* The plugin will no longer generate any notices if post author is missing

* Deprecated:
	* Removed wick for exclude categories auto-suggest. Plugin now uses jQuery Suggest that is included in WordPress. When you re-save plugin options, the field will convert the slugs to the category name
	* Deprecated `$crp_url`. Use the new constants `CRP_PLUGIN_DIR`, `CRP_PLUGIN_URL` and `CRP_PLUGIN_FILE`

= 2.2.3 =

* Enhancements:
	* Changed text domain to `contextual-related-posts` in advance of translate.wordpress.org translation system
	* Improved support for WPML. If available, same language posts will be pulled by default. To restrict to the same language [add this code](https://gist.github.com/ajaydsouza/9b1bc56cec79295e784c) to your theme's functions.php file
	* Removed `id` tag from related posts HTML output to make it W3C compliant. If you're using the id with your custom styles, please change this to classes i.e. change `#crp_related` to `.crp_related` and it should work

* Bug fixes:
	* All cache entries were not deleted on uninstall

= 2.2.2 =

* Features:
	* Preliminary support for WPML

* Enhancements:
	* Recreate Index and Activation will not try to alter the table engine if not needed

* Bug fixes:
	* All thumbnail classes were not properly applied + new thumbnail class filter
	* Shortcode with "exclude_categories" argument works again

= 2.2.1 =

* Bug fixes:
	* "No styles" would not get selected if "Rounded thumbnails' was enabled
	* "Recreate Index" caused a fatal error: Call to undefined function `crp_single_activate()`
	* Excerpt shortening was not working correctly
	* Exclude categories wasn't working in some cases
	* Additional check to see if default styles are off, then force No style

= 2.2.0 =

* Features:
	* Manual posts can now be set in the meta box in the Edit Post screens which will be displayed before the related posts fetched by the plugin
	* Choose between No style, Rounded thumbnails (previously called default style) and Text only style options under the Styles box in the plugin settings page
	* Option to turn off the Contextual Related metabox on Edit Posts screens or limit it to Admins only. Also applies to Pages and Custom Post Types
	* Filter `crp_link_attributes` that allows a user to add or remove attributes for the `a` tag
	* Notice is displayed at the top of the Settings page if there are any missing of the FULLTEXT indices missing
	* Option in the Contextual Related Posts meta box to disable the related posts on the selected post
	* Select post type in the Related Posts Widget

* Enhancements:
	* Optimised number of queries for exclude categories option. Those not using this option will see the greatest savings
	* Select a pre-built thumbnail size will automatically update the width, height and crop settings. The default style will no longer enforce the 150x150 thumbnail size.
	* `strict_limit` argument in `get_crp_posts_id` is now TRUE by default
	* `get_crp` takes an additional argument: `heading` (default is TRUE) that controls the display of the main heading (**Related Posts**)
	* Output of `echo_crp` will be cached in a separate meta key

* Bug fixes:
	* First child now gets the correct thumbnail size

* Deprecated:
	* `ald_crp()` - `Use get_crp()` instead
	* `ald_crp_content()` - `Use crp_content_filter()` instead
	* `ald_crp_rss()` - `Use crp_rss_filter()` instead
	* `echo_ald_crp()` - `Use echo_crp()` instead

= 2.1.1 =

* Enhancements:
	* Settings page now clearly highlights what options cannot be changed if the default styles are enabled, i.e. thumbnail settings and no excerpt, author or date

= 2.1.0 =

* Features:
	* Separate cache for related posts added to feeds. Prevents conflict with the cache for normal related posts
	* Timthumb has been deprecated
	* Setting "Related posts should be newer than:" to 0 to disable limiting posts by age
	* Filters `crp_posts_match`, `crp_posts_now_date`, `crp_posts_from_date` to modify the WHERE clause

* Enhancements:
	* `thumb_timthumb`, `thumb_timthumb_q` and `filter` attributes for the function `crp_get_the_post_thumbnail` have been deprecated. If you're using this function, an entry will be created for the deprecated log
	* Reset default thumbnail URL location to plugin default if the field is blank or only contains `/default.png`
	* Meta-box will no longer be displayed on non-public post types
	* For first image, the plugin will attempt to seek the correct thumbnail size if available

* Bug fixes:
	* Author link was incorrectly displayed multiple times in the list when Show Author was enabled
	* WP Notice Errors when using the Widget via the Customizer menu in WordPress
	* Incorrect thumbnail was pulled on attachment pages

= 2.0.1 =

* Bug fixes:
	* Clear Cache button which broke in 2.0.0

= 2.0.0 =

* Features:
	* Multi-site support. Now you can Network Activate the plugin and all users will see related posts!
	* Thumbnails are registered as an image size in WordPress. This means WordPress will create a copy of the image with the specified dimensions when a new image is uploaded. For your existing images, I recommend using <a href="https://wordpress.org/plugins/force-regenerate-thumbnails/">Force Regenerate Thumbnails</a>
	* Completely filterable mySQL query to fetch the posts. You can write your own functions to filter the fields, orderby, groupby, join and limits clauses

* Enhancements:
	* Lookup priority for thumbnails. The thumbnail URL set in the Contextual Related Posts meta box is given first priority
	* Removed `border=0` attribute from `img` tag for HTML5 validation support
	* Default option for timthumb is disabled
	* Default option for post types to include is post and page
	* `get_crp_posts` has been deprecated. See `get_crp_posts_id` instead
	* Turning on the Default style will switch on thumbnails, correctly resize them and will also hide authors, excerpts and the post date

* Bug fixes:
	* Post image will now be loaded over https if the visitor visits your site on https

= Earlier versions =

For the changelog of earlier versions, please refer to the separate changelog.txt file.


== Upgrade Notice ==

= 2.3.1 =
* New features. Deprecated functions. Upgrade highly recommended. Please do verify your settings after the upgrade.
Check the Changelog for more details


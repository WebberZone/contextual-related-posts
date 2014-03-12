=== Contextual Related Posts ===
Tags: related posts, related, similar posts, posts, post, feed, feeds, rss, widget, thumbnail
Contributors: Ajay
Donate link: http://ajaydsouza.com/donate/
Stable tag: trunk
Requires at least: 3.0
Tested up to: 3.9
License: GPLv2 or later

Display related posts on your WordPress blog and feed. Supports thumbnails, shortcodes, widgets and custom post types!

== Description ==

<a href="http://ajaydsouza.com/wordpress/plugins/contextual-related-posts/">Contextual Related Posts</a> is a powerful plugin for WordPress that allows you to display a list of related posts on your website and in your feed. 

The list is based on the content of the title and/or content of the posts which makes them more relevant and more likely to be of interest to your readers. This allows you to retain visitors, reduce bounce rates and refresh old entries.

Contextual Related Posts is one of the most feature rich related posts plugins for WordPress with support for thumbnails, shortcodes, widgets, custom post types, caching and CSS styles.

The plugin also comes with it's very own inbuilt stylesheet that let's your related posts look awesome!

= Key features =

* **Automatic**: CRP will start displaying related posts on your site and feed automatically after the content when you activate the plugin. No need to edit template files
* **Manual install**: Want more control over placement? Check the <a href="http://wordpress.org/extend/plugins/contextual-related-posts/faq/">FAQ</a> on which functions are available for manual install.
* **Widgets**: Add related posts to widgetized area of your theme. Lots of options available
* **Shortcode**: Use `[crp]` to display the posts anywhere you want in the post content
* **The algorithm**: Find related posts by title and/or content of the current post
* **Caching**: Related posts output is automatically cached as visitors browse through your site
* **Exclusions**: Exclude posts from categories from being displayed in the list. Or you can exclude posts or pages by ID
* **Custom post types**: The related posts list lets you include posts, pages, attachments or any other custom post type!
* **Thumbnail support**:
	* Support for WordPress post thumbnails
	* Auto-extract the first image in your post to be displayed as a thumbnail
	* Manually enter the URL of the thumbnail via <a href="http://codex.wordpress.org/Custom_Fields">WordPress meta fields</a>
	* Use timthumb to resize images or use your own filter function to resize post images
* **Styles**: The output is wrapped in CSS classes which allows you to easily style the list. You can enter your custom CSS styles from within WordPress Admin area or use the style included.
* **Customisable output**:
	* Display excerpts in post. You can select the length of the excerpt in words
	* Customise which HTML tags to use for displaying the output in case you don't prefer the default `list` format

= Donations =

I spend a significant amount of my free time maintaing, updating and more importantly supporting this plugin. Those who have sought support in the support forums know that I have done by best to answer your question and solve your problem.
If you have been using this plugin and find this useful, do consider making a donation. This helps me pay for my hosting and domains.

= Contribute =

Contextual Related Posts is also available on Github at https://github.com/ajaydsouza/contextual-related-posts
So, if you've got some cool feature that you'd like to implement into the plugin or a bug you've been able to fix, consider forking the project and sending me a pull request.


== Upgrade Notice ==

= 1.9.0.1 =
* Fixed: Add to feeds was broken in v1.9


== Changelog ==

= 1.9.0.1 =
* Fixed: Add to feeds was broken in v1.9

= 1.9 =
* New: Default style to make those related posts look awesome! You can find this option in the Custom styles section
* New: Option to change the priority of the content filter. Now you can choose at what stage after the content the related posts are added.
* New: Option to exclude the display on custom post types. Find this under Output Options
* New: Redesigned settings page to be more responsive on mobile devices and better integrated in the WordPress Dashboard design
* New: Function `get_crp_posts_id` can be used to fetch the IDs of related posts. Check out the FAQ on details of this
* Modified: Shortcode now considers a wider range of attributes

= 1.8.10.2 =
* Fixed: Potential SQL vulnerability - Thanks to <a href="http://www.flynsarmy.com/">flynsarmy</a> for highlighting this
* Modified: Minor performance improvements in initialisation of the widget
* Modified: Plugin now checks if it is within the loop when any option under "Add related posts to:" is selected. Minor performance increase to avoid the plugin being called unnecessarily in secondary loops.

= 1.8.10.1 =
* Fixed: Manual install caused a PHP error in 1.8.10

= 1.8.10 =
* New: Limit the numbers of characters of the content being compared. This can give a marginal boost to performance
* New: You can now choose to display the Post title in the Title of the Related posts, by using %postname%
* Modified: Widget class has been changed to `crp_related_widget`
* Modified: Including the author in the list will now use the Display Name which is set under “Display name publicly as” in the User Profile page
* Modified: Updated timthumb
* Modified: Better handling of `echo_ald_crp` - Thanks to <a href="http://www.flynsarmy.com/">flynsarmy</a> for this patch
* Fixed: If the Thumbnail meta field is omitted under Output Options, the plugin will automatically revert to its default value i.e. "post-image"
* Modified: More descriptions for the various options

= 1.8.9.1 =
* Fixed: PHP error when fetching thumbnail for gallery posts

= 1.8.9 =
* New: Option to choose between using CSS styles or HTML attributes for thumbnail width and height. *HTML width and height attributes are default* 
* New: Filters `crp_title` and `crp_heading_title` can be used to customise the Title of the posts and Heading Title of posts list respectively. Check out the FAQ for further information
* New: Option to add the author to the list of posts
* New: Options in the widget to show author and date
* New: Bypass cache option for `echo_ald_crp`. This is the default option. You can force the cache using `echo_ald_crp($cache=true)`
* Modified: Shortcodes are now stripped from excerpts
* Fixed: Lists for the widget and for in-post have independent caches to prevent overlap
* Modified: Saving widgets settings will clear the widget cache
* Fixed: Plugin will now create thumbnails from the first image in gallery custom posts
* Fixed: Uninstall script should now clean the cache as well

= 1.8.8 =
* New: Clear cache button
* New: Option to add the date before the post title 

= 1.8.7 =
* Important security update: Potential XSS vulnerability fixed. Thanks to Charlie Eriksen via Secunia SVCRP for reporting this
* Added Latvian translation

= 1.8.6 =
* New: Related posts are now cached
* New: New function <code>get_crp_posts</code> to get only the list of posts in an array. Check the <a href="http://wordpress.org/extend/plugins/contextual-related-posts/faq/">FAQ</a> on how to use it.
* New: Timthumb will now resize images on wordpress.org, wordpress.com and wp.com. The latter two are useful if you're running Jetpack
* New: Shortcode functionality. You can use the shortcode `[crp]` to display the related posts. Check the FAQ for further details.
* Fixed: Exclude category option missed the last category in the list
* Fixed: Open links in new window option was not working

= 1.8.5 =
* New: Option to open links in new window - Find this under Output Options
* New: Option to add <code>rel="nofollow"</code> to links - Find this under Output Options
* New: Option to set quality of thumbnails - Find this under Output Options
* New: Custom text to display if no related posts are found - Find this under Output Options
* New: Thumbnail height and width can now be configured for the widget
* Fixed: Filter (<code>crp_postimage</code>) added for WordPress Post Thumbnails to allow you to modify your image with your own script. Plugin comes inbuilt with thumbnail resizing using timthumb
* Fixed: PHP notices when WP_DEBUG is set to true

= 1.8.4 =
* New: Option to exclude display of related posts on certain posts/pages. This option is available under "Output Options"
* New: Options to display related posts on home page, category archives, tag archives and other archives
* New: Option to set how recent the related posts should be
* New: Option to limit post title length
* Modified: Filter (<code>crp_postimage</code>) added for WordPress Post Thumbnails to allow you to modify your image with your own script. Plugin comes inbuilt with thumbnail resizing using timthumb
* Modified: If the option to scan for the first image in the post is set to ON, then only images from the same domain as your blog are used as thumbnails. External images are ignored.
* Modified: Updated to latest version of timthumb
* Fixed: Widget now displays on posts and page correctly. Previously displaying the widget resulted in duplicate display of related posts
* Fixed: Related Posts now display correctly in feeds when feed content is set to "Summary"
* Fixed: Fixed PHP Notices: "Use of undefined constant"
* Fixed: Custom CSS styles will be included in the header of all posts and pages. On archives it will be included depending on the settings

= 1.8.3 =
* Fixed: PHP warning errors on manual code for sites with PHP error reporting turn on in strict mode
* Modified: Fixed some language related issues. If you would like to translate the plugin or update a translation, please <a href="http://ajaydsouza.com/contact/">contact me</a>.

= 1.8.2 =
* Fixed: PHP warning errors for sites with PHP error reporting turn on in strict mode

= 1.8.1 =
* New: Widget support. Widget display follows the same settings as having the related posts after content.
* New: Exclude posts and pages by ID
* Modified: Optimised performance when automatic insertion into content is turned off
* Modified: Plugin uses the default excerpt if it is set, else it creates one based on content. Both settings respect the excerpt length set in the plugin
* Modified: Fixed some language related issues. If you would like to translate the plugin or update a translation, please <a href="http://ajaydsouza.com/contact/">contact me</a>.
* Deleted: Redundant option to exclude display of the related posts on pages. You can use the custom post types feature instead
* New: Chinese language file

= 1.8 =
* Modified: Replaced `id` attribute with `class` tag on non-singular pages. On singular pages it will display both `id` and `class`
* New: Option to use timthumb to create thumbnail images (turned ON by default)
* New: Support for WordPress Custom Post Types
* New: New `Custom Styles` tab to allow you to easily style the output
* Modified: New "default.png" file based on from KDE’s <a href="http://www.oxygen-icons.org/">Oxygen icon set</a>

= 1.7.3 =
* Fixed: Donation link

= 1.7.2 =
* Fixed: Title attribute was missing for the thumbnails
* Modified: Reverted the output code to v1.6.5 style with `img` wrapped in its own `a` tag

= 1.7.1 =
* Fixed: Minor bug fix for location of thumbnail

= 1.7 =
* New: New function <code>related posts()</code> that allows you to manually add posts to your theme
* New: Support for <a href="https://wordpress.org/extend/plugins/video-thumbnails/">Video Thumbnails</a> plugin
* New: Thumbnail settings now reflect max width and max height instead of fixed width and height
* New: Option to display thumbnails before or after the title
* New: Option to not display thumbnails instead of the default thumbnail
* New: Plugin now uses InnoDB instead of MyISAM if your server is running mySQL v5.6 or higher
* Modified: Cleaner Settings page interface
* Modified: Updated <a href="http://wordpress.org/extend/plugins/contextual-related-posts/faq/">FAQ page</a>

= 1.6.5 =
* Fixed: Few code tweaks to optimise MySQL performance
* New: Dutch and Spanish language files

= 1.6.4 =
* Fixed: Undefined constants PHP errors

= 1.6.3 =
* Fixed: The plugin will now display a list of changes in the WordPress Admin > Plugins area whenever an update is available

= 1.6.2 =
* Turned the credit option to false by default. This setting won't effect current users.
* Turned off borders on post thumbnails. You can customise the CSS class "crp_thumb" to style the post thumbnail
* From the next version, the plugin will display update information in your WP-Admin

= 1.6.1 =
* Fixed: Custom output was not detecting styles properly
* Fixed: Incorrect XHTML code was being generated when using special characters in the post title

= 1.6 =
* New: The plugin extracts the first image in the post and displays that if the post thumbnail and the post-image meta field is missing
* New: Display excerpts in the list
* New: Credit link to the CRP page added. You can choose to turn this off, though I would appreciate if you leave it on.
* Updated: All parts of the list are now wrapped in classes for easy CSS customisation

= 1.5.2 =
* Fixed: Fixed display of post thumbnails using postmeta field

= 1.5.1 =
* Fixed: Numeric options were not being saved correctly

= 1.5 =
* Added an Option to display post thumbnails
* The output can be completely customised now

= 1.4.2 =
* Fixed: Using doublequotes in the title would mess up the screen
* Fixed: Errors when the mySQL index was being regenerated

= 1.4.1 =
* Added Italian and Danish languages
* Minor fix for text in the admin page

= 1.4 =
* Added complete localization support
* Added button to recreate the mySQL FULLTEXT index

= 1.3.1 =
* Fixed bug that didn't blank out posts even when option was selected

= 1.3 =
* Better optimization in WP-Admin area. 
* Fixed compatibility problems with Simple Tags plugin
* Fixed large number of queries being generated

= 1.2.2 =
* Minor bug fixed about limit of posts

= 1.2.1 =
* Bug fixed to support PHP4

= 1.2 =
* Option to blank output in case nothing is found
* Exclude posts from certain categories
* Exclude pages
* Option to choose if you want related posts to be displayed on pages

= 1.1.1 =
* Now you can optionally choose if you want to use the post content to search for related posts

= 1.1 =
* Fixed MySQL index key conflicts by using a more unique index key name.

= 1.0.1 =
* Release

== Installation ==

= WordPress install =
1. Navigate to Plugins within your WordPress Admin Area

2. Click "Add new" and in the search box enter "Contextual Related Posts" and select "Keyword" from the dropdown

3. Find the plugin in the list (usually the first result) and click "Install Now"

= Manual install =
1. Download the plugin

2. Extract the contents of contextual-related-posts.zip to wp-content/plugins/ folder. You should get a folder called contextual-related-posts.

3. Activate the Plugin in WP-Admin. 

4. Goto **Settings &raquo; Related Posts** to configure

5. Optionally visit the **Custom Styles** tab to add any custom CSS styles. These are added to `wp_head` on the pages where the posts are displayed

== Screenshots ==

1. CRP options in WP-Admin - General options
2. CRP options in WP-Admin - Output options
3. CRP options in WP-Admin - Feed options
4. CRP options in WP-Admin - Custom styles
5. CRP Widget


== Frequently Asked Questions ==

If your question isn't listed here, please post a comment at the <a href="http://wordpress.org/support/plugin/contextual-related-posts">WordPress.org support forum</a>. I monitor the forums on an ongoing basis. If you're looking for more advanced support, please see <a href="http://ajaydsouza.com/support/">details here</a>.

= How can I customise the output? =

Several customization options are available via the Settings page in WordPress Admin. You can access this via <strong>Settings &raquo; Related Posts</strong>

The plugin also provides you with a set of CSS classes that allow you to style your posts by adding code to the *style.css* sheet. In a future version, I will be adding in CSS support within the plugins Settings page.

The following CSS classes / IDs are available:

* **crp_related**: ID of the main wrapper `div`. This is only displayed on singular pages, i.e. post, page and attachment

* **crp_related**: Class of the main wrapper `div`. If you are displaying the related posts on non-singular pages, then you should style this

* **crp_title**: Class of the `span` tag for title of the post

* **crp_excerpt**: Class of the `span` tag for excerpt (if included)

* **crp_thumb**: Class of the post thumbnail `img` tag

For more information, please visit http://ajaydsouza.com/wordpress/plugins/contextual-related-posts/

= How does the plugin select thumbnails? =

The plugin selects thumbnails in the following order:

1. Post Thumbnail image: The image that you can set while editing your post in WordPress &raquo; New Post screen

2. Post meta field: This is the meta field value you can use when editing your post. The default is `post-image`

3. First image in the post: The plugin will try to fetch the first image in the post

3. Video Thumbnails: Meta field set by <a href="https://wordpress.org/extend/plugins/video-thumbnails/">Video Thumbnails</a>

4. Default Thumbnail: If enabled, it will use the default thumbnail that you specify in the Settings screen

The plugin uses <a href="http://www.binarymoon.co.uk/projects/timthumb/">timthumb</a> to generate thumbnails by default. Depending on the configuration of your webhost you might run into certain problems. Please check out <a href="http://www.binarymoon.co.uk/2010/11/timthumb-hints-tips/">the timthumb troubleshooting page</a> regarding permission settings for the folder and files.

= Manual install =

The following functions are available in case you wish to do a manual install of the posts by editing the theme files.

**echo_ald_crp( $args = array() )**

Echoes the list of posts wherever you add the this function. You can also use this function to display related posts on any type of page generated by WordPress including homepage and archive pages.

Usage: `<?php if(function_exists('echo_ald_crp')) echo_ald_crp(); ?>` to your template file where you want the related posts to be displayed.


**get_crp_posts_id()**

Takes a post ID and returns the related post IDs as an object. 

Usage: `<?php if(function_exists('get_crp_posts_id')) get_crp_posts_id( array( 'postid' => $postid, 'limit' => $limit ) ); ?>`

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

= Filters =

The plugin includes the following filters that allows you to customise the output for several section using <a href="http://codex.wordpress.org/Function_Reference/add_filter">add_filter</a>.

*crp_heading_title* : Filter for heading title of the posts. This is the text that you enter under *Output options > Title of related posts*

*crp_title* : Filter for the post title for each of the related posts

I'll be adding more filters eventually. If you are looking for any particular filter do raise a post in the <a href="http://wordpress.org/support/plugin/contextual-related-posts">support forum</a> requesting the same.


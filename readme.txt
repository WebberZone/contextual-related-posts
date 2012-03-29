=== Contextual Related Posts ===
Tags: related posts, related, similar posts, posts, post, feed, feeds, rss, widget, thumbnail
Contributors: Ajay, Mark Ghosh
Donate link: http://ajaydsouza.com/donate/
Stable tag: trunk
Requires at least: 2.5
Tested up to: 3.4


Increase reader retention and reduce bounce rates by displaying a set of related posts on your website or in your feed

== Description ==

<a href="http://ajaydsouza.com/wordpress/plugins/contextual-related-posts/">Contextual Related Posts</a> is a powerful plugin for WordPress that allows you to display a list of related posts on your website and in your feed. 

The list is based on the content of the title and/or content of the posts which makes them more relevant and more likely to be of interest to your readers. This allows you to retain visitors, reduce bounce rates and refresh old entries.


= Key features =

* Display Related Posts automatically after the content on your website or in the feed without the need to edit template files
* Manual install available to select the exact placement of the posts. This will require you to edit your theme files
* Relevancy matching based on title and/or content of the post
* Exclude posts from categories from being displayed in the list
* Exclude display of related posts on Pages 
* Support for WordPress post thumbnails
* Auto-extract the first image in your post to be displayed as a thumbnail
* Manually enter the URL of the thumbnail via <a href="http://codex.wordpress.org/Custom_Fields">WordPress meta fields</a>
* Display excerpts in post. You can select the length of the excerpt in words
* Output wrapped in CSS class that allows you to style the list
* Select what tags you would like to wrap the text in

== Upgrade Notice ==

= 1.7.3 =
* Fixed: Donation link


== Changelog ==

= 1.7.3 =
* Fixed: Donation link

= 1.7.2 =
* Fixed: Title attribute was missing for the thumbnails
* Modified: Reverted the output code to v1.6.5 style with `img` wrapped in its own `a` tag

= 1.7.1 =
* Fixed: Minor bug fix for location of thumbnail

= 1.7 =
* Added: New function <code>related posts()</code> that allows you to manually add posts to your theme
* Added: Support for <a href="https://wordpress.org/extend/plugins/video-thumbnails/">Video Thumbnails</a> plugin
* Added: Thumbnail settings now reflect max width and max height instead of fixed width and height
* Added: Option to display thumbnails before or after the title
* Added: Option to not display thumbnails instead of the default thumbnail
* Added: Plugin now uses InnoDB instead of MyISAM if your server is running mySQL v5.6 or higher
* Modified: Cleaner Settings page interface
* Modified: Updated <a href="http://wordpress.org/extend/plugins/contextual-related-posts/faq/">FAQ page</a>

= 1.6.5 =
* Fixed: Few code tweaks to optimise MySQL performance
* Added: Dutch and Spanish language files

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

1. Download the plugin

2. Extract the contents of contextual-related-posts.zip to wp-content/plugins/ folder. You should get a folder called contextual-related-posts.

3. Activate the Plugin in WP-Admin. 

4. Goto Settings > Related Posts to configure

== Screenshots ==

1. CRP options in WP-Admin - General Options
2. CRP options in WP-Admin - Output Options


== Frequently Asked Questions ==

If your question isn't listed here, please post a comment at the <a href="http://wordpress.org/tags/contextual-related-posts?forum_id=10">WordPress.org support forum</a>. I monitor the forums on an ongoing basis. If you're looking for more advanced support, please see <a href="http://ajaydsouza.com/support/">details here</a>.

= How can I customise the output? =

Several customization options are available via the Settings page in WordPress Admin. You can access this via <strong>Settings » Related Posts</strong>

The plugin also provides you with a set of CSS classes that allow you to style your posts by adding code to the *style.css* sheet. In a future version, I will be adding in CSS support within the plugins Settings page.

The following CSS styles are available:

* **crp_related**: ID of the main wrapper `div`

* **crp_title**: Class of the `span` tag for title of the post

* **crp_excerpt**: Class of the `span` tag for excerpt (if included)

* **crp_thumb**: Class of the post thumbnail `img` tag

For more information, please visit http://ajaydsouza.com/wordpress/plugins/contextual-related-posts/

= How does the plugin select thumbnails? =

The plugin selects thumbnails in the following order:

1. Post Thumbnail image: The image that you can set while editing your post in WordPress > New Post screen

2. Post meta field: This is the meta field value you can use when editing your post. The default is `post-image`

3. First image in the post: The plugin will try to fetch the first image in the post

3. Video Thumbnails: Meta field set by <a href="https://wordpress.org/extend/plugins/video-thumbnails/">Video Thumbnails</a>

4. Default Thumbnail: If enabled, it will use the default thumbnail that you specify in the Settings screen

== Wishlist ==

Below are a few features that I plan on implementing in future versions of the plugin. However, there is no fixed time-frame for this and largely depends on how much time I can contribute to development.

* Select random posts if there are no similar posts
* Widget to display the related posts
* Shortcode support
* Exclude display on select categories
* Exclude display on select posts 
* Caching related posts
* Better relevance tweaking
* Custom post support

If you would like a feature to be added, or if you already have the code for the feature, you can let us know by <a href="http://wordpress.org/tags/contextual-related-posts?forum_id=10">posting in this forum</a>.


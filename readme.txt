=== Contextual Related Posts ===
Tags: related posts, related, related articles, contextual related posts, similar posts, related posts widget
Contributors: webberzone, ajay
Donate link: https://ajaydsouza.com/donate/
Stable tag: 3.4.2
Requires at least: 5.9
Tested up to: 6.4
Requires PHP: 7.4
License: GPLv2 or later

Related posts for your WordPress site with inbuilt caching. Supports blocks, shortcodes, widgets and custom post types!

== Description ==

[Contextual Related Posts](https://wordpress.org/plugins/contextual-related-posts/) is a powerful WordPress plugin that helps you increase your site's engagement by displaying a list of related posts that are relevant and interesting to your readers.

Contextual Related Posts uses a smart algorithm that analyzes the title and/or content of your posts to find the most related ones. This way, you can show your visitors more of your awesome content and keep them on your site longer.

Contextual Related Posts also comes with many features and options that let you customize the look and feel of the related posts list. You can choose between different styles, layouts, thumbnails, excerpts, and more. You can also use widgets, shortcodes, Gutenberg blocks, or REST API to display the related posts anywhere on your site or in your feed.

With Contextual Related Posts, you can easily boost your site's traffic, reduce bounce rates, and refresh old entries. It's fast, flexible, and easy to use. Try it today and see the difference for yourself!

### Key features

* __Automatic__: Just activate the plugin and Contextual Related Posts will automatically display related posts on your site and feed after the content. No need to edit any template files.
* __Manual install__: If you want more control over the placement of the related posts, you can use the FAQ to learn about the functions available for manual install.
* __Gutenberg / Block Editor support__: You can easily add a block called "Related Posts [CRP]" to any post or page, with its own set of options and settings.
* __Widgets__: Add related posts to any widgetized area of your theme, such as the sidebar or footer. You can configure the widget options to suit your needs.
* __Shortcode__: Use `[crp]` to display the related posts anywhere within the post content.
* __REST API__: Fetch related posts for any post ID using `contextual-related-posts/v1/posts/<id>/`. You can also use query parameters to filter or sort the results.
* __The algorithm__: Find related posts based on the title and/or content of the current post. You can also find posts by tags, categories and selected custom fields.
* __Caching__: Related posts output is automatically cached as visitors browse through your site, reducing the load on your server and improving performance.
* __Exclusions__: Exclude posts from certain categories or tags from being displayed in the related posts list. Or you can exclude specific posts or pages by ID using a meta box in the edit screen.
* __Custom post types__: The related posts list supports posts, pages, attachments, or any other custom post type that you have on your site.
* __Thumbnail support__: Display thumbnails or not!
* __Styles__: The output of the related posts list is wrapped in CSS classes that allow you to easily style it using custom CSS code. You can enter your custom CSS code from within the WordPress admin area or use one of the default styles included with the plugin.
* __Customisable output__: Display post excerpts in the related posts list. You can set the length of the excerpt in words and also strip HTML tags if needed. Customise the HTML tags and attributes used for displaying the output of the related posts list. For example, you can use an ordered or unordered list, a div container, a span element, etc.
* __Extendable code__: Contextual Related Posts has many filters and actions that allow developers to easily add features, modify outputs, or integrate with other plugins.

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

1. Related Posts (Rounded Thumbnails)
2. Related Posts (Masonry)
3. Related Posts (Grid)


== Frequently Asked Questions ==

Visit the Contextual Related Posts [Knowledge Base](https://webberzone.com/support/knowledgebase/category/contextual-related-posts/) for FAQs. If your question isn't listed there, please create a new post at the [WordPress.org support forum](http://wordpress.org/support/plugin/contextual-related-posts). It is the fastest way to get support as I monitor the forums regularly.

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

= Shortcodes =

You can insert the related posts anywhere in your post using the `[crp]` shortcode. View [this article in the knowledge base](https://webberzone.com/support/knowledgebase/contextual-related-posts-shortcode/) for more details.


== Other Notes ==

Contextual Related Posts is one of the many plugins developed by WebberZone. Check out our other plugins:

* [Top 10](https://wordpress.org/plugins/top-10/) - Track daily and total visits on your blog posts and display the popular and trending posts
* [WebberZone Snippetz](https://wordpress.org/plugins/add-to-all/) - The ultimate snippet manager for WordPress to create and manage custom HTML, CSS or JS code snippets
* [Knowledge Base](https://wordpress.org/plugins/knowledgebase/) - Create a knowledge base or FAQ section on your WordPress site
* [Better Search](https://wordpress.org/plugins/better-search/) - Enhance the default WordPress search with contextual results sorted by relevance
* [Auto-Close](https://wordpress.org/plugins/autoclose/) - Automatically close comments, pingbacks and trackbacks and manage revisions

== Changelog ==

= 3.4.2 =

Release post: [https://webberzone.com/blog/contextual-related-posts-v3-4-0/](https://webberzone.com/blog/contextual-related-posts-v3-4-0/)

* Enhancements:
    * Live Search in the Manual Related Posts field now searches by post ID if you enter a number
    * Live Search will only search for posts titles and not content
    * Include Words feature will now try to sort the posts by the number of words matched in the title, content and excerpt

* Bug fix:
    * Bug in Include Words functionality where all post types were incorrectly included
    * Compatibility issue with PolyLang. Return the default post if pll_get_post returns false

= 3.4.1 =

* Bug fix:
    * Fixed Request-URI Too Long error when searching for pages/posts
    * Related Posts block threw an error when using on the widgets page

= 3.4.0 =

* Features:
    * Bulk edit posts, pages and custom post types to add the manual relatd posts and/or exclude posts from the related posts list
    * New argument `include_words` to include posts that match the words in the title and/or content.

* Enhancements/Modifications:
    * The Manual Related Posts field in the meta box allows a user to live search for related posts
    * Caching of the entire HTML output is enabled by default. You can disable it in the settings page. This will reduce the number of database queries and improve performance. If you have customised the output, you will need to clear the cache for the changes to take effect. Applies to new installs and when you reset the settings
    * The plugin no longer check for pre v2.5 settings key
    * The Media Handler will check the title of the image in case the alt tag text is empty before defaulting to the post title
    * All the inbuilt styles have been updated for the `a` tags to have `:focus-visible` declared for accessibility
    * The `Heading of posts` setting will now use `<h2>` instead of `<h3>` for the heading by default

* Bug Fixes:
    * The post cache was not always cleared when a post was updated

* Deprecated:
    * `get_crp_posts_id` has been completed deprecated and will use `get_crp_posts` instead. The function will continue to work but will be removed in a future version

= 3.3.4 =

Release post: [https://webberzone.com/blog/contextual-related-posts-v3-3-0/](https://webberzone.com/blog/contextual-related-posts-v3-3-0/)

* Enhancements/Modifications:
	* When displaying the post thumbnail, the Media Handler will first use the image's alt tag set in the Media editor. If alt tag is empty, then it will use the post title as a fallback. Filter `crp_thumb_use_image_alt` and set it to false to not use the alt tag. Filter `crp_thumb_alt_fallback_post_title` and set it to false to disable the alt tag
	* Orderby clause modified to ensure compatibility if any other plugin rewrites the WP_Query fields

* Bug Fixes:
    * Fix duplicate display of related posts when using reusable blocks or a plugin that inserts pages
    * `meta_query` argument was ignored

= 3.3.3 =

* Fixes permission issues with HTML settings

= 3.3.2 =

* Clearing cache security fix

= 3.3.1 =

* Security fix in block

= 3.3.0 =

* Features:
    * Related posts block allows you to set a custom header above the related posts. Leave blank to get the one from the main settings page.
    * New option "Related Meta Keys" under the List Tuning tab. You can enter a comma-separted list of meta keys. Posts that match the same value of the meta key are displayed before the other related posts.

* Enhancements/modifications:
    * If the number of "Manual related posts" is greater than the number of related posts, then the database query is bypassed drastically improving perfomance
    * Moved Related Posts Tools page under Tools menu
    * Related Posts block is now wrapped in `Disabled` tags to prevent accidental clicking of links in the block editor

* Bug fixes:
    * Thumb width and height defaults to 150 in case the settings are missing
    * Setting the style to be text_only didn't enforce no thumbnail

* Developer:
    * New filters: `crp_query_date_query`, `crp_query_meta_query`, `crp_query_meta_query_relation`

= Earlier versions =

For the changelog of earlier versions, please refer to the separate changelog.txt file or the [releases page on Github](https://github.com/WebberZone/contextual-related-posts/releases).


== Upgrade Notice ==

= 3.4.2 =
Major release: Bulk edit posts, new features, enhancements and bug fixes. Please read the release post for more details.

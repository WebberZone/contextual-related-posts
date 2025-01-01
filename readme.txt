=== Contextual Related Posts ===
Tags: related posts, related, contextual related posts, similar posts, seo
Contributors: webberzone, ajay
Donate link: https://wzn.io/donate-crp
Stable tag: 3.6.1
Requires at least: 6.3
Tested up to: 6.7
Requires PHP: 7.4
License: GPLv2 or later

Display related posts for your WordPress site with inbuilt caching. It supports blocks, shortcodes, widgets, and custom post types!

== Description ==

[Contextual Related Posts](https://wordpress.org/plugins/contextual-related-posts/) is a powerful WordPress plugin that helps you increase your site's engagement by displaying a list of related posts that are relevant and interesting to your readers.

Contextual Related Posts uses an intelligent algorithm that analyzes your posts' title and/or content to find the most related ones. This way, you can show your visitors more awesome content and keep them on your site longer.

Contextual Related Posts also comes with many features and options that let you customize the look and feel of the related posts list. You can choose between different styles, layouts, thumbnails, excerpts, and more. You can also use widgets, shortcodes, Gutenberg blocks, or REST API to display the related posts anywhere on your site or in your feed.

With Contextual Related Posts, you can quickly boost your site's traffic, reduce bounce rates, and refresh old entries. It's fast, flexible, and easy to use. Try it today and see the difference for yourself!

= Key features =

* __Automatic__: Activate the plugin. Contextual Related Posts automatically displays related posts on your site and feed after the content. There is no need to edit any template files.
* __Manual install__: If you want more control over the placement of the related posts, you can use the FAQ to learn about the functions available for manual installation.
* __Gutenberg / Block Editor support__: You can easily add a "Related Posts [CRP]" block to any post or page with its options and settings.
* __Widgets__: Add related posts to any widgetized theme area, such as the sidebar or footer. You can configure the widget options to suit your needs.
* __Shortcode__: Use `[crp]` to display the related posts anywhere within the post content.
* __REST API__: Fetch related posts for any post ID using `contextual-related-posts/v1/posts/<id>/`. You can also use query parameters to filter or sort the results.
* __The algorithm__: Find related posts based on the current post's title and/or content. You can also find posts by tags, categories and selected custom fields.
* __Caching__: Related posts output is automatically cached as visitors browse through your site, reducing the load on your server and improving performance.
* __Exclusions__: Exclude posts from specific categories or tags from being displayed in the related posts list. You can exclude specific posts or pages by ID using a meta box on the edit screen.
* __Custom post types__: The related posts list supports posts, pages, attachments, or any other custom post type on your site.
* __Thumbnail support__: Display thumbnails or not!
* __Styles__: The output of the related posts list is wrapped in CSS classes that allow you to style it easily using custom CSS code. You can enter your custom CSS code from within the WordPress admin area or use one of the default styles included with the plugin.
* __Customizable output__: Display post excerpts in the related posts list. You can set the excerpt's length in words and strip HTML tags if needed. Customize the HTML tags and attributes used to display the output of the related posts list. For example, you can use an ordered or unordered list, a div container, a span element, etc.
* __Extendable code__: Contextual Related Posts has many filters and actions that allow developers to easily add features, modify outputs, or integrate with other plugins.

= Features in Contextual Related Posts Pro =

[CRP Pro](https://webberzone.com/plugins/contextual-related-posts/pro/) enhances your experience with an advanced query block, offering more precise customization options, additional shortcode functionalities, and enhanced meta box settings.

* [Advanced Algorith](https://webberzone.com/support/knowledgebase/contextual-related-posts-algorithm/): Set the relative weight of the post title, post content and post excerpt. This feature overrides the default equal weight algorithm of the free version and provides a greater degree of fine-tuning.
* [Query Loop Block](https://webberzone.com/support/knowledgebase/contextual-related-posts-blocks/#contextual-related-posts-query-loop-block): An advanced block that allows you to display the related posts based on specified parameters. You can use the pre-built block patterns or create your block patterns for use within posts or the site editor.
* [Extra shortcode parameters](https://webberzone.com/support/knowledgebase/contextual-related-posts-shortcode/): Additional parameters for the shortcode that allow you to customize the output of the related posts list.
* [Additional Metabox settings](https://webberzone.com/support/knowledgebase/contextual-related-posts-metabox/): Additional settings in the post edit screen that allow you to customize the related posts output for each post.

= mySQL FULLTEXT indices =

On activation, the plugin creates three mySQL FULLTEXT indices (or indexes), which are leveraged to find the related posts in the `*_posts`. These are for `post_content`, `post_title` and `(post_title,post_content)`. The Pro version also has an index for `post_excerpt`.

If you're running a multisite installation, this is created for each blog on activation. All these indices occupy space in your mySQL database but are essential for running the plugin.

Two options on the settings page allow you to remove these indices when you deactivate or delete the plugin. The latter is true by default.

You can turn off contextual matching on the settings page if you do not wish to use these indices. You must turn on related posts by category, tags and/or custom taxonomies.

= GDPR =

Contextual Related Posts is GDPR compliant as it doesn't collect personal data about your visitors when installed out of the box. All posts are processed on your site and not sent to any external service.

YOU ARE RESPONSIBLE FOR ENSURING THAT YOU MEET ALL GDPR REQUIREMENTS ON YOUR WEBSITE.

= Donations =

I spend much of my free time maintaining, updating and, more importantly, supporting this plugin. Those who have sought support in the support forums know I have done my best to answer your question and solve your problem.
Consider donating if you have been using this plugin and find it helpful. All donations help me pay for my hosting and domains.
Alternatively, please look at Contextual Related Posts Pro, which will allow me to sustain the plugin's development.

= Contribute =

Contextual Related Posts is also available on [Github](https://github.com/WebberZone/contextual-related-posts).
So, if you've got some cool feature you'd like to implement into the plugin or a bug you've been able to fix, consider forking the project and sending me a pull request.

Bug reports are [welcomed on GitHub](https://github.com/WebberZone/contextual-related-posts/issues). Please note GitHub is _not_ a support forum, and issues that aren't suitably qualified as bugs will be closed.

= Translations =

Contextual Related Posts is available for [translation directly on WordPress.org](https://translate.wordpress.org/projects/wp-plugins/contextual-related-posts). Check out the official [Translator Handbook](https://make.wordpress.org/polyglots/handbook/rosetta/theme-plugin-directories/) to contribute.

= Other Plugins by WebberZone =

Contextual Related Posts is one of the many plugins developed by WebberZone. Check out our other plugins:

* [Top 10](https://wordpress.org/plugins/top-10/) - Track daily and total visits to your blog posts and display the popular and trending posts
* [WebberZone Snippetz](https://wordpress.org/plugins/add-to-all/) - The ultimate snippet manager for WordPress to create and manage custom HTML, CSS or JS code snippets
* [Knowledge Base](https://wordpress.org/plugins/knowledgebase/) - Create a knowledge base or FAQ section on your WordPress site
* [Better Search](https://wordpress.org/plugins/better-search/) - Enhance the default WordPress search with contextual results sorted by relevance
* [Auto-Close](https://wordpress.org/plugins/autoclose/) - Automatically close comments, pingbacks and trackbacks and manage revisions
* [Popular Authors](https://wordpress.org/plugins/popular-authors/) - Display popular authors in your WordPress widget
* [Followed Posts](https://wordpress.org/plugins/where-did-they-go-from-here/) - Show a list of related posts based on what your users have read

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

Check out the [FAQ on the plugin page](https://wordpress.org/plugins/contextual-related-posts/faq/) or the [Knowledge Base](https://webberzone.com/support/product/contextual-related-posts/).

If your question isn't listed here, please create a new post at the [WordPress.org support forum](https://wordpress.org/support/plugin/contextual-related-posts).

Support for products sold and distributed by WebberZone is only available for those with an active, paid extension license. You can [access our support form here](https://webberzone.com/request-support/).

= How can I customize the output? =

Contextual Related Posts is highly customizable. There are several configurable options on the Settings page, and you can use CSS to customize the outputs. Learn more by reading [this article](https://webberzone.com/support/knowledgebase/customising-the-output-of-contextual-related-posts/).

= Shortcodes =

You can insert the related posts anywhere in your post using the `[crp]` shortcode. View [this article in the knowledge base](https://webberzone.com/support/knowledgebase/contextual-related-posts-shortcode/) for more details.

= How can I report security bugs? =

You can report security bugs through the Patchstack Vulnerability Disclosure Program. The Patchstack team help validate, triage and handle any security vulnerabilities. [Report a security vulnerability.](https://patchstack.com/database/vdp/contextual-related-posts)

= How does the plugin select thumbnails? =

If you enable thumbnails, the plugin will try to find the correct thumbnail in this order:

1. Post meta field: This is the value you can use when editing your post. The default is `post-image`. Change it in the Settings page.

2. Post Thumbnail image: You can set the image under Featured Image.

3. First image in the post: The plugin will try to fetch the first image in the post. Toggle this on the Settings page.

4. The first child image is attached to the post.

5. Site Icon: Set this using Customizer or under General Settings.

6. Default Thumbnail: If enabled, it will use the default thumbnail you specify in the Settings page.

== Changelog ==

= 3.6.1 =

* Bug fix: Corrected thumbnail images to return the specified image size instead of the full size.

= 3.6.0 =

Release post: [https://webberzone.com/announcements/contextual-related-posts-v3-6-0/](https://webberzone.com/announcements/contextual-related-posts-v3-6-0/)

* Features:
    * [Pro] New block: Contextual Related Posts Featured Image is an advanced featured image block that falls back to the first image, meta key, or a default image if no featured image is set.
	* [Pro] Related Posts block now includes:
		* Buttons to save and clear default block settings.
		* Auto-insertion of default and global settings attributes, with an option to disable this in the **List tuning** tab.

* Modifications:
    * The "Manual related posts" field in the meta box can now be customized to perform searches without relevance. To disable relevance in searches, use the filter `crp_meta_box_manual_related_relevance` with the following code: `add_filter('crp_meta_box_manual_related_relevance', '__return_false');`.
    * Contextual Related Posts Block's Other attributes moved to Advanced panel.
    * Show admin notices when the Style is set to "Rounded Thumbnails", "Rounded Thumbnails with Grid", or "Text Only" in the Settings page. A notice will appear below the affected settings, indicating that these options cannot be modified.
    * Updated "Rounded Thumbnails" style's post title line height to 1.1em and removed the padding.
    * Updated Freemius SDK to 2.10.1.

* Bug fixes:
    * Fixed warning message about language files being initiated too early.

= 3.5.5 =

* Modifications:
    * Updated Freemius SDK to 2.9.0

= 3.5.4 =

Release post: [https://webberzone.com/announcements/contextual-related-posts-v3-5-0/](https://webberzone.com/announcements/contextual-related-posts-v3-5-0/)

* Fixes:
    * `include_post_ids` and `manual_related` were not being passed correctly to the query

= 3.5.3 =

* Modifications:
    * Updated Freemius SDK to 2.7.3
    * Updated Contextual Related Posts block apiVersion to 3
    * Increase background opacity for the Rounded Thumbs style's post title for better readability

* Fixes:
    * Allow `manual_related` attribute in shortcode
    * Correctly handle manual_related and include_post_ids arguments when set to 0. Ensure manual_related is set to an empty array when 0 is passed.
    * Correctly check if `relation` attribute is set for meta_query and tax_query
    * Fixed links to settings and tools page in the admin area
    * Pass `post_status` when using `get_posts()`
    * Remove `the_posts` filter in CRP_Query
    * [Pro] Fixed bug in the Query Loop where custom post types didn't appear in the dropdown

= 3.5.2 =

* Modifications:
    * [Pro] If "Only from" same category/tag/taxonomy is enabled, the plugin will sort results by the number of matched taxonomies first, then optimize the related posts further. The free version will continue to sort by date
    * [Pro] If any of the weights are 0, then the algorithm will not consider that field for matching

* Bug fix:
    * Custom styles did not get enqueued on the front end
    * Fixed PHP error on the Network admin page
    * Fixed bug where post types and taxonomies settings retained the previous values if no option was selected

= 3.5.1 =

* Bug fix:
    * Fixed memory issue when using the crp shortcode

= 3.5.0 =

Complete code rewrite using OOP, namespacing and autoloading. This will make it easier to maintain and extend the plugin in the future.

* Features:
    * Enter a negative number in the "Insert after paragraph number" setting to insert the related posts from the bottom of the post
    * [Pro] New Related Posts Query Block that allows you to query related posts using a block in the block or site editor
    * [Pro] New feature to set the weight of the title, content and excerpt in the related posts algorithm
    * [Pro] New parameter `display_only_on_tax_ids` to display related posts only on specific taxonomy terms
    * [Pro] New option added to the Edit Post meta box mapped to `include_cat_ids` to include related posts from specific categories only
    * [Pro] New *Clear cache* button in the settings page to clear the cache

* Enhancements:
    * The plugin supports `WP_Query` directly if `crp_query` is set in the query arguments
    * Optimized media handler to reduce the number of queries
    * _Cache posts only_ setting is changed to be true by default
    * _Cache HTML output_ will now cache the HTML output of the related posts list, superseding the _Cache posts only_ setting

* Bug fix:
    * `trim_char` function returned a blank string instead of the original string if the length was 0
    * Insert after paragraph used to insert after an extra paragraph than what was specified

= Earlier versions =

For the changelog of earlier versions, please refer to the separate changelog.txt file or the [releases page on Github](https://github.com/WebberZone/contextual-related-posts/releases).


== Upgrade Notice ==

= 3.6.1 =
New features and enhancements in this release. Please read the changelog for more details.

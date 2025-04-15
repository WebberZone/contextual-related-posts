=== Contextual Related Posts ===
Tags: related posts, related, contextual related posts, similar posts, seo
Contributors: webberzone, ajay
Donate link: https://wzn.io/donate-crp
Stable tag: 4.0.1
Requires at least: 6.3
Tested up to: 6.8
Requires PHP: 7.4
License: GPLv2 or later

Keep visitors on your site longer with intelligent, fast-loading, contextually related posts. Block, shortcode, custom post type and widget ready.

== Description ==

[Contextual Related Posts](https://wordpress.org/plugins/contextual-related-posts/) is a powerful WordPress plugin that displays fast, intelligent related posts to keep users on your site longer. Improve SEO, increase pageviews, and lower bounce rates — no setup needed.

### Key features

* __Activate and Forget__: Activate the plugin. Contextual Related Posts automatically displays related posts on your site and feed after the content. There is no need to edit any template files.
* __Custom Control with Manual Install__: Want placement control? You have multiple options available:
    * __Gutenberg / Block Editor support__: You can easily add a "Related Posts [CRP]" block to any post or page with its options and settings.
    * __Widgets__: Add related posts to any widgetized theme area, such as the sidebar or footer. You can configure the widget options to suit your needs.
    * __Shortcode__: Use `[crp]` to display the related posts anywhere within the post content.
* __REST API__: Fetch related posts for any post ID using `contextual-related-posts/v1/posts/<id>/`. You can also use query parameters to filter or sort the results.
* __The algorithm__: Find related posts based on the current post's title and/or content. You can also find posts by tags, categories and selected custom fields.
* __Caching__: Related posts output is automatically cached as visitors browse through your site, reducing the load on your server and improving performance.

**[View Demo](https://demo.webberzone.com)**

Contextual Related Posts uses an intelligent algorithm that analyzes your post's title and/or content to find the most related ones. This way, you can show your visitors more awesome content and keep them on your site longer.

With Contextual Related Posts, you can quickly boost your site's traffic, reduce bounce rates, and refresh old entries. It's fast, flexible, and easy to use. Try it today and see the difference for yourself!

Additional features include:

* __Exclusions__: Exclude posts from specific categories or tags from being displayed in the related posts list.
* __Custom post types__: The related posts list supports posts, pages, attachments, or any other custom post type on your site.
* __Thumbnail support__: Display thumbnails or not!
* __Styles__: The output of the related posts list is wrapped in CSS classes that allow you to style it easily using custom CSS code.
* __Customizable output__: Display post excerpts in the related posts list. Customize the HTML tags and attributes used to display the output.
* __Extendable code__: Many filters and actions allow developers to easily add features, modify outputs, or integrate with other plugins.

### MySQL FULLTEXT indices

On activation, the plugin creates three MySQL FULLTEXT indices (or indexes), which are leveraged to find the related posts. [Learn more about how the algorithm works](https://webberzone.com/support/knowledgebase/contextual-related-posts-algorithm/).

If you're running a multisite installation, an index is created for each blog upon activation. These indices occupy space in your MySQL database but are essential for running the plugin.

Two options on the settings page allow you to remove these indices when deactivating or deleting the plugin. The latter is true by default.

### 💼 Features Exclusive to CRP Pro

[CRP Pro](https://webberzone.com/plugins/contextual-related-posts/pro/) supercharges your related posts with advanced customization, better performance, and powerful content strategy tools.

#### 🚀 Performance Enhancements

* [Efficient Content Storage and Indexing](https://webberzone.com/support/knowledgebase/efficient-content-storage-and-indexing/): Speed up your site with optimized custom tables and efficient database indices for lightning-fast queries.
* [Cache Setting](https://webberzone.com/support/knowledgebase/caching-in-contextual-related-posts/): Fine-tune your performance with configurable cache times from 1 hour to 1 year.

#### 🎯 Smarter Content Matching

* [Advanced Algorithm](https://webberzone.com/support/knowledgebase/contextual-related-posts-algorithm/): Control exactly how relevant content is found by adjusting weights for title, content, and excerpt.
* [Taxonomy Weight System](https://webberzone.com/support/knowledgebase/contextual-related-posts-algorithm/#weighting-categories-tags-and-taxonomies): Refine your matches with precise taxonomy weighting for perfect content relationships.

#### 🎨 Advanced Design & Display Options

* [Block Editor Integration](https://webberzone.com/support/knowledgebase/contextual-related-posts-blocks/): Create beautiful layouts with the Query Loop Block and ready-to-use block patterns including Grid, Image with Title, and more.
* [Extra Shortcode Parameters](https://webberzone.com/support/knowledgebase/contextual-related-posts-shortcode/): Unlock additional customization options for complete control over your related posts display.

#### 📊 Analytics & Content Strategy Tools

* [Tracking Parameters](https://webberzone.com/support/knowledgebase/tracking-parameters/): Measure content performance with automatic UTM tracking for all related post clicks.
* [Cornerstone Posts](https://webberzone.com/support/knowledgebase/cornerstone-posts-in-contextual-related-posts/): Guide visitors to your most important content by featuring key articles in your related posts lists.
* [Additional Metabox Settings](https://webberzone.com/support/knowledgebase/contextual-related-posts-metabox/): Control related content at the individual post level for perfect content relationships.

### GDPR

Contextual Related Posts doesn’t collect personal data or send information to external services — making it GDPR-friendly by default.

⚠️ You’re responsible for ensuring your site’s overall GDPR compliance.

### Donations

Love Contextual Related Posts? Help keep it alive!

You can [donate](https://wzn.io/donate-crp) or upgrade to [CRP Pro](https://webberzone.com/plugins/contextual-related-posts/pro/) — both help support development and support.

### Contribute

Contextual Related Posts is also available on [Github](https://github.com/WebberZone/contextual-related-posts).
So, if you've got some cool feature you'd like to implement into the plugin or a bug you've been able to fix, consider forking the project and sending me a pull request.

Bug reports are [welcomed on Github](https://github.com/WebberZone/contextual-related-posts/issues). Please note Github is _not_ a support forum, and issues that aren't suitably qualified as bugs will be closed.

### Translations

Contextual Related Posts is available for [translation directly on WordPress.org](https://translate.wordpress.org/projects/wp-plugins/contextual-related-posts). Check out the official [Translator Handbook](https://make.wordpress.org/polyglots/handbook/rosetta/theme-plugin-directories/) to contribute.

### Other Plugins by WebberZone

Contextual Related Posts is one of the many plugins developed by WebberZone. Check out our other plugins:

* [Top 10](https://wordpress.org/plugins/top-10/) - Track daily and total visits to your blog posts and display the popular and trending posts
* [WebberZone Snippetz](https://wordpress.org/plugins/add-to-all/) - The ultimate snippet manager for WordPress to create and manage custom HTML, CSS or JS code snippets
* [Knowledge Base](https://wordpress.org/plugins/knowledgebase/) - Create a knowledge base or FAQ section on your WordPress site
* [Better Search](https://wordpress.org/plugins/better-search/) - Enhance the default WordPress search with contextual results sorted by relevance
* [Auto-Close](https://wordpress.org/plugins/autoclose/) - Automatically close comments, pingbacks and trackbacks and manage revisions
* [Popular Authors](https://wordpress.org/plugins/popular-authors/) - Display popular authors in your WordPress widget
* [Followed Posts](https://wordpress.org/plugins/where-did-they-go-from-here/) - Show a list of related posts based on what your users have read

== Installation ==

### WordPress install (the easy way)

1. Navigate to Plugins within your WordPress Admin Area
2. Click "Add new" and in the search box enter "Contextual Related Posts"
3. Find the plugin in the list (usually the first result) and click "Install Now"

### Manual install

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

= 4.0.1 =

* Modifications:
    * Added new admin notice if any fulltext indexes are missing.

* Bug fixes:
    * The `post_content` index was not created.

= 4.0.0 =

Release post: [https://webberzone.com/blog/contextual-related-posts-v4-0-0/](https://webberzone.com/blog/contextual-related-posts-v4-0-0/)

* Features:
    * [Pro] New: Efficient Content Storage and Indexing - Custom tables implementation for better performance and query optimization while maintaining the same relevance algorithm
    * [Pro] Added MAX_EXECUTION_TIME hint to MySQL queries. You can now set the maximum execution time for CRP queries in the Settings page.
    * [Pro] New setting to configure cache time for related posts with options ranging from 1 hour to 1 year.
    * [Pro] Added new setting to add tracking parameters to related post URLs. The following UTM parameters will be added:
        * `utm_source`: `contextual-related-posts`
        * `utm_medium`: `related-posts`
        * `utm_campaign`: `crp-plugin`
        * `utm_content`: `{source-post-id}-to-{target-post-id}`
    * [Pro] Introduced a feature to designate cornerstone posts, which are key articles or pages on your site. These cornerstone posts will appear at random positions in the related posts list, ensuring they receive more visibility and help guide your audience to the most important content on your site.
    * [Pro] New: Taxonomy weight system for more precise matching

* Enhancements/Modifications:
    * Enabled drag-and-drop functionality for reordering manual posts in the Metabox.
    * Added new `parse_wp_query_arguments` helper method to parse the comma-separated list of IDs and convert them to arrays for WP_Query compatibility
    * Renamed class CRP() to CRP_Core_Query()

= Earlier versions =

For the changelog of earlier versions, please refer to the separate changelog.txt file or the [releases page on Github](https://github.com/WebberZone/contextual-related-posts/releases).


== Upgrade Notice ==

= 4.0.1 =
Major release. Please read the changelog and the release post for more details.

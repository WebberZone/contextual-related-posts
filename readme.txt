=== Contextual Related Posts ===
Tags: related posts, related, related articles, contextual related posts, similar posts, related posts widget
Contributors: webberzone, Ajay
Donate link: https://ajaydsouza.com/donate/
Stable tag: 3.3.3
Requires at least: 5.6
Tested up to: 6.2
Requires PHP: 7.2
License: GPLv2 or later

Related posts for your WordPress site with inbuilt caching. Supports blocks, shortcodes, widgets and custom post types!

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

= Shortcodes =

You can insert the related posts anywhere in your post using the `[crp]` shortcode. View [this article in the knowledge base](https://webberzone.com/support/knowledgebase/contextual-related-posts-shortcode/) for more details.


== Changelog ==

= 3.3.3 =

Release post: [https://webberzone.com/blog/contextual-related-posts-v3-3-0/](https://webberzone.com/blog/contextual-related-posts-v3-3-0/)

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

= 3.2.3 =

Release post: [https://webberzone.com/blog/contextual-related-posts-v3-2-0/](https://webberzone.com/blog/contextual-related-posts-v3-2-0/)

* Enhancements:
    * Block shows a placeholder when used in non-Edit screens or when no content is generated

* Bug fix:
    * Missing text-only.min.css file
    * Block settings were not saved

= 3.2.2 =

* Bug fix:
    * Widget options were not saved properly
    * Stylesheets and header styles have been fixed to be more specific

= 3.2.1 =

* Enhancements/modifications:
    * If thumbnail is set as `text only`, then the style is also set as `text only` at runtime

* Bug fix:
    * PHP error thrown when using `get_crp_posts_id()`

* Deprecated:
    * `get_crp_posts_id()`. Use `get_crp_posts()` instead

= 3.2.0 =

* New feature:
    * New option to limit posts to the primary category/term. The plugin checks if either Yoast, Rank Math, The SEO Framework or SEOPress are active. If none of these are active, the plugin will pick the first category provided by `get_the_terms()`
    * New option to show the primary category/term
    * New option in metabox to enter a comma-separated list of post IDs to exclude from the related posts
    * New filter `crp_fill_random_posts` (default:false) which can be used to fill random posts if the number of related posts is less than the limit set

* Enhancements/modifications:
    * No widget is displayed if `get_crp()` is empty i.e. no related posts are found
    * `post_title` and `post_content` fields are only used if *Use content* option is set
    * Gutenberg block updated to the latest Blocks API
    * The widget's "Only from categories" autocomplete replaced by ID list
    * Wrapper `div` now always includes `crp_related` class name. Stylesheets have been updated to have more specific styles e.g. `.crp_related.crp-rounded-thumbs`
    * *Exclude categories* and *Exclude on categories* renamed to *Exclude terms* and *Exclude on terms* as they now support all taxonomies and the accepted format has changed to "Term Name (taxonomy:term_taxonomy_id)"

* Bug fixes:
    * Current post was incorrectly being excluded in the translation functions

= Earlier versions =

For the changelog of earlier versions, please refer to the separate changelog.txt file or the [releases page on Github](https://github.com/WebberZone/contextual-related-posts/releases).


== Upgrade Notice ==

= 3.3.3 =
Security fix

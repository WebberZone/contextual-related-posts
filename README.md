# Contextual Related Posts

[![WordPress Plugin Version](https://github.com/WebberZone/contextual-related-posts/blob/master/wporg-assets/banner-1544x500.png)](https://wordpress.org/plugins/contextual-related-posts/)

[![WordPress Plugin Version](https://img.shields.io/wordpress/plugin/v/contextual-related-posts.svg?style=flat-square)](https://wordpress.org/plugins/contextual-related-posts/)
[![License](https://img.shields.io/badge/license-GPL_v2%2B-orange.svg?style=flat-square)](http://opensource.org/licenses/GPL-2.0)
[![WordPress Tested](https://img.shields.io/wordpress/v/contextual-related-posts.svg?style=flat-square)](https://wordpress.org/plugins/contextual-related-posts/)
[![Required PHP](https://img.shields.io/wordpress/plugin/required-php/contextual-related-posts?style=flat-square)](https://wordpress.org/plugins/contextual-related-posts/)
[![Active installs](https://img.shields.io/wordpress/plugin/installs/contextual-related-posts?style=flat-square)](https://wordpress.org/plugins/contextual-related-posts/)

__Requires:__ 6.3

__Tested up to:__ 6.7

__Requires PHP:__ 7.4

__License:__ [GPL-2.0+](http://www.gnu.org/licenses/gpl-2.0.html)

__Plugin page:__ [Contextual Related Posts home page](https://webberzone.com/plugins/contextual-related-posts/) | [WordPress.org listing](https://wordpress.org/plugins/contextual-related-posts/)

Display related posts for your WordPress site with inbuilt caching. It supports blocks, shortcodes, widgets, and custom post types!

## Description

[Contextual Related Posts](https://wordpress.org/plugins/contextual-related-posts/) is a powerful WordPress plugin that helps you increase your site's engagement by displaying a list of related posts that are relevant and interesting to your readers.

Contextual Related Posts uses an intelligent algorithm that analyzes your posts' title and/or content to find the most related ones. This way, you can show your visitors more awesome content and keep them on your site longer.

Contextual Related Posts also comes with many features and options that let you customize the look and feel of the related posts list. You can choose between different styles, layouts, thumbnails, excerpts, and more. You can also use widgets, shortcodes, Gutenberg blocks, or REST API to display the related posts anywhere on your site or in your feed.

With Contextual Related Posts, you can quickly boost your site's traffic, reduce bounce rates, and refresh old entries. It's fast, flexible, and easy to use. Try it today and see the difference for yourself!

### Key features

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

### Features in Contextual Related Posts Pro

[CRP Pro](https://webberzone.com/plugins/contextual-related-posts/pro/) enhances your experience with an advanced query block, offering more precise customization options, additional shortcode functionalities, and enhanced meta box settings.

* [Advanced Algorith](https://webberzone.com/support/knowledgebase/contextual-related-posts-algorithm/): Set the relative weight of the post title, post content and post excerpt. This feature overrides the default equal weight algorithm of the free version and provides a greater degree of fine-tuning.
* [Query Loop Block](https://webberzone.com/support/knowledgebase/contextual-related-posts-blocks/#contextual-related-posts-query-loop-block): An advanced block that allows you to display the related posts based on specified parameters. You can use the pre-built block patterns or create your block patterns for use within posts or the site editor.
* [Extra shortcode parameters](https://webberzone.com/support/knowledgebase/contextual-related-posts-shortcode/): Additional parameters for the shortcode that allow you to customize the output of the related posts list.
* [Additional Metabox settings](https://webberzone.com/support/knowledgebase/contextual-related-posts-metabox/): Additional settings in the post edit screen that allow you to customize the related posts output for each post.

### mySQL FULLTEXT indices

On activation, the plugin creates three mySQL FULLTEXT indices (or indexes), which are leveraged to find the related posts in the `*_posts`. These are for `post_content`, `post_title` and `(post_title,post_content)`. The Pro version also has an index for `post_excerpt`.

If you're running a multisite installation, this is created for each blog on activation. All these indices occupy space in your mySQL database but are essential for running the plugin.

Two options on the settings page allow you to remove these indices when you deactivate or delete the plugin. The latter is true by default.

You can turn off contextual matching on the settings page if you do not wish to use these indices. You must turn on related posts by category, tags and/or custom taxonomies.

### GDPR

Contextual Related Posts is GDPR compliant as it doesn't collect personal data about your visitors when installed out of the box. All posts are processed on your site and not sent to any external service.

YOU ARE RESPONSIBLE FOR ENSURING THAT YOU MEET ALL GDPR REQUIREMENTS ON YOUR WEBSITE.

### Donations

I spend much of my free time maintaining, updating and, more importantly, supporting this plugin. Those who have sought support in the support forums know I have done my best to answer your question and solve your problem.
Consider donating if you have been using this plugin and find it helpful. All donations help me pay for my hosting and domains.
Alternatively, please look at Contextual Related Posts Pro, which will allow me to sustain the plugin's development.

### Translations

Contextual Related Posts is available for [translation directly on WordPress.org](https://translate.wordpress.org/projects/wp-plugins/contextual-related-posts). Check out the official [Translator Handbook](https://make.wordpress.org/polyglots/handbook/rosetta/theme-plugin-directories/) to contribute.

## Screenshots

![General Options](https://raw.github.com/WebberZone/contextual-related-posts/master/wporg-assets/screenshot-1.png)
*Contextual Related Posts - General Options*

More screenshots are available on the [WordPress plugin page](https://wordpress.org/plugins/contextual-related-posts/screenshots/)

## Other plugins by WebberZone

Contextual Related Posts is one of the many plugins developed by WebberZone. Check out our other plugins:

* [Top 10](https://wordpress.org/plugins/top-10/) - Track daily and total visits to your blog posts and display the popular and trending posts
* [WebberZone Snippetz](https://wordpress.org/plugins/add-to-all/) - The ultimate snippet manager for WordPress to create and manage custom HTML, CSS or JS code snippets
* [Knowledge Base](https://wordpress.org/plugins/knowledgebase/) - Create a knowledge base or FAQ section on your WordPress site
* [Better Search](https://wordpress.org/plugins/better-search/) - Enhance the default WordPress search with contextual results sorted by relevance
* [Auto-Close](https://wordpress.org/plugins/autoclose/) - Automatically close comments, pingbacks and trackbacks and manage revisions
* [Popular Authors](https://wordpress.org/plugins/popular-authors/) - Display popular authors in your WordPress widget
* [Followed Posts](https://wordpress.org/plugins/where-did-they-go-from-here/) - Show a list of related posts based on what your users have read

## Installation

### WordPress install (the easy way)

1. Navigate to Plugins within your WordPress Admin Area
2. Click "Add new" and in the search box enter "Contextual Related Posts"
3. Find the plugin in the list (usually the first result) and click "Install Now"

### Manual install

1. Download the plugin
2. Extract the contents of contextual-related-posts.zip to wp-content/plugins/ folder. You should get a folder called contextual-related-posts.
3. Activate the Plugin in WP-Admin under the Plugins screen

## Frequently Asked Questions

Check out the [FAQ on the plugin page](https://wordpress.org/plugins/contextual-related-posts/faq/) or the [Knowledge Base](https://webberzone.com/support/product/contextual-related-posts/).

If your question isn't listed there, please create a new post at the [WordPress.org support forum](https://wordpress.org/support/plugin/contextual-related-posts). It is the fastest way to get support as I monitor the forums regularly.

Support for products sold and distributed by WebberZone is only available for those who have an active, paid extension license. You can [access our support form here](https://webberzone.com/request-support/).

## How can I report security bugs?

You can report security bugs through the Patchstack Vulnerability Disclosure Program. The Patchstack team help validate, triage and handle any security vulnerabilities. [Report a security vulnerability.](https://patchstack.com/database/vdp/contextual-related-posts)

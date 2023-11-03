# Contextual Related Posts

[![WordPress Plugin Version](https://github.com/WebberZone/contextual-related-posts/blob/03f671821226a6e32a7f6b09b12a7c3f138f17a1/wporg-assets/banner-1544x500.png)](https://wordpress.org/plugins/contextual-related-posts/)

[![WordPress Plugin Version](https://img.shields.io/wordpress/plugin/v/contextual-related-posts.svg?style=flat-square)](https://wordpress.org/plugins/contextual-related-posts/)
[![License](https://img.shields.io/badge/license-GPL_v2%2B-orange.svg?style=flat-square)](http://opensource.org/licenses/GPL-2.0)
[![WordPress Tested](https://img.shields.io/wordpress/v/contextual-related-posts.svg?style=flat-square)](https://wordpress.org/plugins/contextual-related-posts/)
[![Required PHP](https://img.shields.io/wordpress/plugin/required-php/contextual-related-posts?style=flat-square)](https://wordpress.org/plugins/contextual-related-posts/)
[![Active installs](https://img.shields.io/wordpress/plugin/installs/contextual-related-posts?style=flat-square)](https://wordpress.org/plugins/contextual-related-posts/)

__Requires:__ 5.9

__Tested up to:__ 6.4

__Requires PHP:__ 7.4

__License:__ [GPL-2.0+](http://www.gnu.org/licenses/gpl-2.0.html)

__Plugin page:__ [Contextual Related Posts home page](https://webberzone.com/plugins/contextual-related-posts/) | [WordPress.org listing](https://wordpress.org/plugins/contextual-related-posts/)

Related posts for your WordPress site with inbuilt caching. Supports blocks, shortcodes, widgets and custom post types!

## Description

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

### mySQL FULLTEXT indices

On activation, the plugin creates three mySQL FULLTEXT indices (or indexes) that are then used to find the related posts in the `*_posts`. These are for `post_content`, `post_title` and `(post_title,post_content)`. If you're running a multisite installation, then this is created for each of the blogs on activation. All these indices occupy space in your mySQL database but are essential for the plugin to run.

You have two sets of options in the settings page which allows you to remove these indices when you deactivate or delete the plugin. The latter is true by default.

### GDPR

Contextual Related Posts is GDPR compliant as it doesn't collect any personal data about your visitors when installed out of the box. All posts are processed on your site and not sent to any external service.

YOU ARE RESPONSIBLE FOR ENSURING THAT ALL GDPR REQUIREMENTS ARE MET ON YOUR WEBSITE.

### Donations

I spend a significant amount of my free time maintaining, updating and more importantly supporting this plugin. Those who have sought support in the support forums know that I have done my best to answer your question and solve your problem.
If you have been using this plugin and find this useful, do consider making a donation. This helps me pay for my hosting and domains.

### Translations

Contextual Related Posts is available for [translation directly on WordPress.org](https://translate.wordpress.org/projects/wp-plugins/contextual-related-posts). Check out the official [Translator Handbook](https://make.wordpress.org/polyglots/handbook/rosetta/theme-plugin-directories/) to contribute.

## Screenshots

![General Options](https://raw.github.com/WebberZone/contextual-related-posts/master/wporg-assets/screenshot-1.png)
*Contextual Related Posts - General Options*

More screenshots are available on the [WordPress plugin page](https://wordpress.org/plugins/contextual-related-posts/screenshots/)

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

Check out the [FAQ on the plugin page](https://wordpress.org/plugins/contextual-related-posts/faq/) or the [knowledge base](https://webberzone.com/support/section/contextual-related-posts/)

If your question isn't listed there, please create a new post at the [WordPress.org support forum](https://wordpress.org/support/plugin/contextual-related-posts). It is the fastest way to get support as I monitor the forums regularly.

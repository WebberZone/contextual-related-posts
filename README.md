# Contextual Related Posts

[![WordPress Plugin Version](https://img.shields.io/wordpress/plugin/v/contextual-related-posts.svg?style=flat-square)](https://wordpress.org/plugins/contextual-related-posts/)
[![License](https://img.shields.io/badge/license-GPL_v2%2B-orange.svg?style=flat-square)](http://opensource.org/licenses/GPL-2.0)
[![WordPress Tested](https://img.shields.io/wordpress/v/contextual-related-posts.svg?style=flat-square)](https://wordpress.org/plugins/contextual-related-posts/)
[![Required PHP](https://img.shields.io/wordpress/plugin/required-php/contextual-related-posts?style=flat-square)](https://wordpress.org/plugins/contextual-related-posts/)
[![Active installs](https://img.shields.io/wordpress/plugin/installs/contextual-related-posts?style=flat-square)](https://wordpress.org/plugins/contextual-related-posts/)

__Requires:__ 5.6

__Tested up to:__ 6.1

__License:__ [GPL-2.0+](http://www.gnu.org/licenses/gpl-2.0.html)

__Plugin page:__ [Contextual Related Posts home page](https://webberzone.com/plugins/contextual-related-posts/) | [WordPress.org listing](https://wordpress.org/plugins/contextual-related-posts/)

Related posts for your WordPress site with inbuilt caching. Supports blocks, shortcodes, widgets and custom post types!

## Description

[Contextual Related Posts](https://webberzone.com/plugins/contextual-related-posts/) is a powerful plugin for WordPress that allows you to display a list of related posts on your website and in your feed.

The list is based on the content of the title and/or content of the posts which makes them more relevant and more likely to be of interest to your readers. This allows you to retain visitors, reduce bounce rates and refresh old entries.

Although several similar plugins exist today, Contextual Related Posts is one of the most feature rich plugins with support for thumbnails, shortcodes, widgets, custom post types and CSS styles. The inbuilt cache ensures that you have related posts without excessive load on your server.

And the default inbuilt styles allow you to switch between gorgeous thumbnail-rich related posts or a similar text display!

### Key features

* __Automatic__: CRP will start displaying related posts on your site and feed automatically after the content when you activate the plugin. No need to edit template files
* __Manual install__: Want more control over placement? Check the [FAQ](http://wordpress.org/extend/plugins/contextual-related-posts/faq/) on which functions are available for manual install
* __Gutenberg / Block Editor support__: You can find a block called "Related Posts [CRP]" with its own configurable set of options
* __Widgets__: Add related posts to widgetized area of your theme. Lots of options available
* __Shortcode__: Use `[crp]` to display the posts anywhere you want in the post content
* __REST API__: Fetch related posts at `contextual-related-posts/v1/posts/<id>/`
* __The algorithm__: Find related posts by title and/or content of the current post
* __Caching__: Related posts output is automatically cached as visitors browse through your site
* __Exclusions__: Exclude posts from categories from being displayed in the list. Or you can exclude posts or pages by ID
* __Custom post types__: The related posts list lets you include posts, pages, attachments or any other custom post type!
* __Thumbnail support__:
  * Support for WordPress post thumbnails. CRP will create a custom image size (`crp_thumbnail`) with the dimensions specified in the Settings page
  * Auto-extract the first image in your post to be displayed as a thumbnail
  * Manually enter the URL of the thumbnail via [WordPress meta fields](http://codex.wordpress.org/Custom_Fields). Specify this using the meta box in your Edit screens.
  * Optionally, use timthumb to resize images or use your own filter function to resize post images
* __Styles__: The output is wrapped in CSS classes which allows you to easily style the list. You can enter your custom CSS styles from within WordPress Admin area or use the style included.
* __Customisable output__:
  * Display excerpts in post. You can select the length of the excerpt in words
  * Customise which HTML tags to use for displaying the output in case you don't prefer the default `list` format
* __Extendable code__: CRP has tonnes of filters and actions that allow any developer to easily add features, edit outputs, etc.

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

If your question isn't listed there, please create a new post at the [WordPress.org support forum](https://wordpress.org/support/plugin/contextual-related-posts). It is the fastest way to get support as I monitor the forums regularly. I also provide [premium *paid* support via email](https://webberzone.com/support/).

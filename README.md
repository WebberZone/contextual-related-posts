# Contextual Related Posts

[![WordPress Plugin Version](https://github.com/WebberZone/contextual-related-posts/blob/master/wporg-assets/banner-1544x500.png)](https://wordpress.org/plugins/contextual-related-posts/)

[![WordPress Plugin Version](https://img.shields.io/wordpress/plugin/v/contextual-related-posts.svg?style=flat-square)](https://wordpress.org/plugins/contextual-related-posts/)
[![License](https://img.shields.io/badge/license-GPL_v2%2B-orange.svg?style=flat-square)](http://opensource.org/licenses/GPL-2.0)
[![WordPress Tested](https://img.shields.io/wordpress/v/contextual-related-posts.svg?style=flat-square)](https://wordpress.org/plugins/contextual-related-posts/)
[![Required PHP](https://img.shields.io/wordpress/plugin/required-php/contextual-related-posts?style=flat-square)](https://wordpress.org/plugins/contextual-related-posts/)
[![Active installs](https://img.shields.io/wordpress/plugin/installs/contextual-related-posts?style=flat-square)](https://wordpress.org/plugins/contextual-related-posts/)

__Requires:__ 6.3

__Tested up to:__ 6.8

__Requires PHP:__ 7.4

__License:__ [GPL-2.0+](http://www.gnu.org/licenses/gpl-2.0.html)

__Plugin page:__ [Contextual Related Posts home page](https://webberzone.com/plugins/contextual-related-posts/) | [WordPress.org listing](https://wordpress.org/plugins/contextual-related-posts/)

Keep visitors on your site longer with intelligent, fast-loading contextually related posts. Block, shortcode, custom post type and widget ready.

## Description

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

__[View Demo](https://demo.webberzone.com)__

Contextual Related Posts uses an intelligent algorithm that analyzes your post's title and/or content to find the most related ones. This way, you can show your visitors more awesome content and keep them on your site longer.

With Contextual Related Posts, you can quickly boost your site's traffic, reduce bounce rates, and refresh old entries. It's fast, flexible, and easy to use. Try it today and see the difference for yourself!

Additional features include:

* __Exclusions__: Exclude posts from specific categories or tags from being displayed in the related posts list.
* __Custom post types__: The related posts list supports posts, pages, attachments, or any other custom post type on your site.
* __Thumbnail support__: Display thumbnails or not!
* __Styles__: The output of the related posts list is wrapped in CSS classes that allow you to style it easily using custom CSS code.
* __Customizable output__: Display post excerpts in the related posts list. Customize the HTML tags and attributes used to display the output.
* __Extendable code__: Many filters and actions allow developers to easily add features, modify outputs, or integrate with other plugins.

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

### MySQL FULLTEXT indices

On activation, the plugin creates three MySQL FULLTEXT indices (or indexes), which are leveraged to find the related posts. [Learn more about how the algorithm works](https://webberzone.com/support/knowledgebase/contextual-related-posts-algorithm/).

If you're running a multisite installation, an index is created for each blog upon activation. These indices occupy space in your MySQL database but are essential for running the plugin.

Two options on the settings page allow you to remove these indices when deactivating or deleting the plugin. The latter is true by default.

### GDPR

Contextual Related Posts doesn’t collect personal data or send information to external services — making it GDPR-friendly by default.

⚠️ You’re responsible for ensuring your site’s overall GDPR compliance.

### Donations

Love Contextual Related Posts? Help keep it alive!

You can [donate](https://wzn.io/donate-crp) or upgrade to [CRP Pro](https://webberzone.com/plugins/contextual-related-posts/pro/) — both help support development and support.

### Translations

Contextual Related Posts is available for [translation directly on WordPress.org](https://translate.wordpress.org/projects/wp-plugins/contextual-related-posts). Check out the official [Translator Handbook](https://make.wordpress.org/polyglots/handbook/rosetta/theme-plugin-directories/) to contribute.

## Screenshots

![General Options](https://raw.github.com/WebberZone/contextual-related-posts/master/wporg-assets/screenshot-1.png)
_Contextual Related Posts - General Options_

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

## How can I report security bugs?

You can report security bugs through the Patchstack Vulnerability Disclosure Program. The Patchstack team help validate, triage and handle any security vulnerabilities. [Report a security vulnerability.](https://patchstack.com/database/vdp/contextual-related-posts)

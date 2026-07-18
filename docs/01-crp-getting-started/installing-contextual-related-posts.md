---
slug: installing-contextual-related-posts
title: "Installing and Using Contextual Related Posts"
products: [contextual-related-posts]
sections: ["01-crp-getting-started"]
tags: [contextual-related-posts, installation]
status: publish
order: 0
---

[Contextual Related Posts](https://wordpress.org/plugins/contextual-related-posts/) core plugin is hosted on WordPress.org. This makes installing it on your site extremely easy and just like any other plugin.

## WordPress install (The easy way)

1. Navigate to “Plugins” within your WordPress Admin Area
2. Click “Add new” and in the search box enter “Contextual Related Posts”
3. Find the plugin in the list (usually the first result) and click “Install Now”
4. Activate or Network activate the Plugin in WP-Admin under the Plugins screen

![Installing Contextual Related Posts](https://webberzone.com/wp-content/uploads/2015/07/Installing-CRP.png)

## Manual install

1. Download the plugin
2. Extract the contents of contextual-related-posts.zip to wp-content/plugins/ folder. You should get a folder called contextual-related-posts.
3. Activate or Network activate the Plugin in WP-Admin under the Plugins screen

## Installing via WP CLI

If you’re using [WP CLI](http://wp-cli.org/), you can install and activate this plugin by running:

`wp plugin install contextual-related-posts --activate`

This plugin can also be network activated using:

`wp plugin install contextual-related-posts --activate-network`

## Using Contextual Related Posts

Contextual Related Posts can be used in these ways to display the related posts:

1. **Automatically**: This is the default option. You can enable/disable this using the setting *Automatically add related posts to* in [General Settings](https://webberzone.com/support/knowledgebase/contextual-related-posts-general-settings/).
2. **Blocks:** Contextual Related Posts includes blocks that allow you to insert the related posts in the Block and Site Editors. [Read more details of the blocks](https://webberzone.com/support/knowledgebase/contextual-related-posts-blocks/).
3. **Widget**: Drag and drop “Related Posts [CRP]” widget into your theme’s sidebar and configure it.
4. **Shortcode**: `[crp]`, so you can embed it inside a post or a page. [View details on the shortcode](https://webberzone.com/support/knowledgebase/contextual-related-posts-shortcode/).
5. **Template tags**: Use `echo_crp()` to display the related posts anywhere on your theme.
6. **CRP_Query**: You can use this for a more advanced implementation. [Read more details on CRP_Query](https://webberzone.com/support/knowledgebase/crp-query/).

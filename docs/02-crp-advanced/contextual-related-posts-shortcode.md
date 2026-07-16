---
slug: contextual-related-posts-shortcode
title: "Contextual Related Posts shortcode"
products: [contextual-related-posts]
sections: ["02-crp-advanced"]
tags: [contextual-related-posts, shortcode]
status: publish
order: 0
---

[Contextual Related Posts](https://webberzone.com/plugins/contextual-related-posts/) includes a shortcode to display the related posts list. If you’re not familiar with shortcodes, please read [this article in the WordPress Codex](https://codex.wordpress.org/Shortcode).

## [crp]

This shortcode lets you insert the contextually related posts anywhere in your post content. It takes the following optional attributes:

| Parameter | Type | Description |
| --- | --- | --- |
| limit | Integer | Maximum number of posts to return. The actual number displayed may be lower depending on the category / post exclusion settings. |
| heading | Boolean | Set to 0 to disable the heading specified in Title of related posts: under Output options. |
| show_author | Integer | Display the author of the post. 1 to display, 0 to hide. |
| show_date | Boolean | Display the published date of the post. 1 to display, 0 to hide. |
| show_excerpt | Boolean | Display the excerpt of the post. 1 to display, 0 to hide. |
| title_length | Integer | Limit the length of titles in the display. This sets the maximum number of characters in the title. |
| offset | Integer | Number of posts to displace or pass over. |
| post_types | String | Comma-separated list of post types from which to select related posts. |
| exclude_post_ids | String | Comma-separated list of post, page or custom post type IDs to exclude from selection. |
| thumb_size | String | Name of the thumbnail size to display the related posts. This can be the default WordPress media sizes or any custom size set by a theme or plugin. The default size set by Contextual Related Posts is crp_thumbnail. |
| thumb_width | Integer | Width of the thumbnail image. This might get overridden if the image of the exact size isn’t found. |
| thumb_height | Integer | Height of the thumbnail image. This might get overridden if the image of the exact size isn’t found. |
| post_thumb_op | String | Location of the post thumbnail. Values: inline, after, text_only, thumbs_only. |
| link_nofollow | Boolean | Add nofollow attribute to links. 1 to add, 0 to exclude. |
| link_new_window | Boolean | Add _blank attribute to links. 1 to add, 0 to exclude. |
| daily_range | Integer | Sets the oldest date of related posts (in days). |
| ordering | String | Options for ordering related posts: relevance, random, date. |
| same_post_type | Boolean | Limit to the same post type. 1 to include, 0 to exclude. |
| same_author | Boolean | Limit to the same post author. 1 to include, 0 to exclude. |
| include_cat_ids | String | Comma-separated list of term_taxonomy_id for categories and custom taxonomies. Related posts will only be selected from these taxonomies. If you have an older WordPress install, this might differ from the term_id. |
| include_post_ids | String | Comma-separated list of post IDs to always include in the selection. |
| display_only_on_tax_ids *(pro only)* | String | Comma-separated list of term_taxonomy_id for categories and custom taxonomies. Limit the display of the related posts to only these taxonomies. If you have an older WordPress install, this might differ from the term_id. |
| lazy_load *(pro only)* | Boolean | Override the global [Lazy load related posts](https://webberzone.com/support/knowledgebase/lazy-loading-related-posts/) setting for this instance. 1 to lazy load, 0 to render inline. Omit to follow the global setting. |

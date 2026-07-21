---
slug: contextual-related-posts-list-tuning-settings
title: "Contextual Related Posts Settings – List tuning"
products: [contextual-related-posts]
sections: ["01-crp-getting-started"]
tags: [contextual-related-posts, settings]
status: publish
order: 0
---

[kbtoc]

The **List Tuning** is the second tab in the [Contextual Related Posts](https://webberzone.com/plugins/contextual-related-posts/) settings screen and provides you with options that allow you to fine-tune the items in the related posts list.

## General List Settings

### Use global settings in block *(Pro only)*

If activated, the settings from this page are automatically inserted in the Related Posts block. This also applies to existing blocks which do not have any attributes set if the post is edited.

### Number of posts to display

The maximum number of posts that will be displayed in the list. This option is used if you do not specify the number of posts in the widget or shortcodes.

### Related posts should be newer than

Sets the cut-off period for which posts will be displayed. For example, setting it to 365 will show related posts from the last year only. Set to 0 to disable limiting posts by date.

### Order posts

Select how you want the related posts to be ordered. Selecting “Randomly” will not work with Caching HTML and could also increase the time it takes to execute the query.

### Randomize posts

Shuffles the selected related posts, similar to choosing “Randomly” in the above option. If you select to order by date in the previous option, then the related posts will first be sorted by date, and the selected ones are shuffled. Does not work if “Cache HTML” output is enabled.

## Relevance Matching

### Related posts based on title and content (Free version)

If unchecked, only the post titles are used. Each site is different, so toggle this option to see which setting gives you better quality related posts. Sometimes, fewer words will provide more relevant results.

### Weight for post title *(Pro only)*

The weight to give to the post title when calculating the relevance of the post.

### Weight for post content *(Pro only)*

The weight to give to the post content when calculating the relevance of the post. This may make the query take longer to process.

### Weight for post excerpt *(Pro only)*

The weight to give to the post excerpt when calculating the relevance of the post.

### Weight for categories *(Pro only)*

Weight to give category matches when calculating relevance.

### Weight for tags *(Pro only)*

Weight to give tag matches when calculating relevance.

### Default taxonomy weight *(Pro only)*

Weight to give other taxonomy matches when calculating relevance.

### Primary term boost *(Pro only)*

Additional weight multiplier for primary terms. This is usually set using your SEO plugin and will default to the first category/term returned by WordPress. CRP supports Yoast, Rank Math SEO, The SEO Framework and SEOExpress plugins that allow you to set a primary category.

### Use precomputed taxonomy score *(Pro only)*

Enable the use of the precomputed taxonomy score for relevance calculation. This can improve performance, but will ignore the above weights for taxonomies when running live queries. This only works if you have ECSI enabled in the [Performance tab](https://webberzone.com/support/knowledgebase/contextual-related-posts-performance-settings/).

### Limit the content to be compared

Sets the maximum words of the post content that will be matched. Set to 0 for no limit. The plugin constant `CRP_MAX_WORDS` defines the maximum value.

## Post Selection Criteria

### Post types to include

Select which post types you want to include in the list of posts. At least one option should be selected. This field can be overridden using a comma-separated list of post types when using the manual display.

### Cornerstone IDs *(Pro only)*

Comma-separated list of post/page or custom post type IDs to be used as cornerstone posts. Posts with these IDs will be randomly selected and then included in the list of related posts. Roughly 20% of the related posts will be chosen from this list. [Learn more about Cornerstone Posts](https://webberzone.com/support/knowledgebase/cornerstone-posts-in-contextual-related-posts/).

### Limit to the same post type

If checked, the related posts will only be selected from the same post type as the current post.

### Limit to the same author

If checked, the related posts will only be selected from the same author as the current post.

## Taxonomy &amp; Term Filtering

### Limit to the same primary term

If enabled, then it will only select posts from the primary category/term. This is usually set via your SEO plugin and will default to the first category/term returned by WordPress.

### Only from the same

Limit the related posts only to the categories, tags, and/or taxonomies of the current post.

### Match all taxonomies

If enabled, then it will only select posts that match all the above selected taxonomies. This can result in no related posts being found.

### Number of common terms

Enter the minimum number of common terms that have to be matched before a post is considered related.

### Related Meta Keys

Enter a comma-separated list of meta keys. Posts that match the exact value of the meta key are displayed before the other related posts.

## Exclusion Rules

### Post/page IDs to exclude

Comma-separated list of post or page IDs to exclude from the list. e.g. 188,320,500

### Exclude Categories

The field above has an autocomplete. Start typing in the starting letters, and it will prompt you with options. This field requires a specific format as displayed by the autocomplete.

## Advanced Options

### Disable contextual matching

This will disable the content matching described above. You can choose to fall back to just the first X posts from the selected categories mentioned below.

### Disable contextual matching ONLY for custom post types

Checking this option will disable contextual matching only for custom post types. For WordPress’ built-in post types, the plugin will continue as per your settings above. If you enable this option, ensure that you select either Manual related posts or Randomize posts above to achieve meaningful results.

### Include only posts that contain these words:

If entered, the related posts will include only posts that contain any of the specified words. Separate words with commas and no spaces. e.g. samsung,apple,nokia

### Exclude posts that contain these words:

If entered, the related posts will exclude posts that contain any of the specified words. Separate words with commas and no spaces. e.g. samsung,apple,nokia

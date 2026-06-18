---
slug: contextual-related-posts-performance-settings
title: "Contextual Related Posts Settings – Performance"
products: [contextual-related-posts]
sections: ["01-crp-getting-started"]
tags: [contextual-related-posts,performance,settings]
status: publish
order: 0
---

[kbtoc]

The **Performance** tab in [Contextual Related Posts](https://webberzone.com/plugins/contextual-related-posts/) includes options designed to optimize how related posts are queried and displayed, especially for high-traffic or large sites. This section provides options for using custom database tables, caching, and fine-tuning query performance.

## Efficient Content Storage and Indexing (ECSI)

**Description:**
ECSI creates a dedicated database table optimized for related content queries. This significantly enhances performance, particularly on sites with many posts or high traffic.

**How to enable:**
To create the ECSI tables, visit the [**Tools** page](https://webberzone.com/support/knowledgebase/contextual-related-posts-settings-tools/) in the plugin settings.

**Compatibility:**
If your database does not support the required features, a message will be displayed here with further instructions.

### Use Custom Tables

- **Type:** Checkbox (Pro)
- **Description:**
Enable this to use dedicated custom tables for related post queries. This can improve performance on large sites.

## Optimization Settings

> [!TIP]
> ✅ For most busy sites, enabling **Cache HTML output** and setting a reasonable **Cache Time** will provide the best balance of speed and flexibility.

### Lazy Load Related Posts

- **Type:** Checkbox (Pro)
- **Default:** Disabled
- **Description:**
Loads the related posts using JavaScript only when they are about to enter the viewport. This speeds up the initial page load and works well with page caching plugins. Search engines may not index the related posts links when this is enabled. Applies to all display methods: content, shortcode, widget, and block; use `lazy_load="0"` in the shortcode to disable it per instance. Not applied on feeds and AMP pages. [Read more about lazy loading](https://webberzone.com/support/knowledgebase/lazy-loading-related-posts/).

### Cache Posts Only

- **Type:** Checkbox
- **Default:** Enabled
- **Description:**
Caches only the related post IDs, not the full HTML output. This offers flexibility with slightly lower performance. Use this if you call related posts with the same parameters.

### Cache HTML Output

- **Type:** Checkbox
- **Default:** Enabled
- **Description:**
Caches the entire HTML generated for related posts when a post is first visited. The cache is cleared when you save the settings page. Highly recommended for busy sites.

### Clear Cache on Trash or Restore

- **Type:** Checkbox
- **Default:** Disabled
- **Description:**
When enabled, the entire CRP cache is cleared whenever a post is moved to Trash or restored from Trash. Useful if you want related posts to update immediately after content is removed or recovered, rather than waiting for the cache to expire naturally.

### Cache Time

- **Type:** Select (Pro)
- **Default:** 1 week
- **Description:**
Sets how long the related posts cache should last. Options range from “No expiry” to “1 Year”.

### Max Execution Time

- **Type:** Number (Pro)
- **Default:** 3000 ms (3 seconds)
- **Description:**
Sets the maximum time (in milliseconds) allowed for MySQL queries. Set to 0 to disable the limit.

### Server Load Threshold

- **Type:** Number (Pro)
- **Default:** 0 (disabled)
- **Description:**
Sets the maximum number of active MySQL threads (`Threads_running`) allowed before CRP skips generating related posts for that request. Set to 0 to disable this check. [Read more about this setting](https://webberzone.com/support/knowledgebase/server-load-threshold-setting-in-contextual-related-posts-pro/).

### Meta Keys to Index

- **Type:** Text (Pro)
- **Description:**
Comma-separated list of post meta keys whose values should be included when indexing content into the custom ECSI tables. Useful for indexing custom field content alongside post titles and body text.

## Need help?

If you encounter compatibility messages or performance issues, check your database configuration or [contact support](https://webberzone.com/request-support/).

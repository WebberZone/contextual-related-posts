---
slug: caching-in-contextual-related-posts
title: "Caching in Contextual Related Posts"
products: [contextual-related-posts]
sections: ["01-crp-getting-started"]
tags: [cache, contextual-related-posts]
status: publish
order: 0
---

To improve the performance of your WordPress site, the [Contextual Related Posts](https://webberzone.com/plugins/contextual-related-posts/) plugin offers two inbuilt caching options: **Cache Posts only** and **Cache HTML output**. This guide will walk you through enabling and configuring these settings.

## Why Enable Caching?

Caching helps reduce the load on your database and speeds up the delivery of related posts to your users. By enabling caching, you ensure that your website runs efficiently, even under heavy traffic.

- **Cache Posts only** allows you to cache the output of the main related posts query which subsequently reduces future page loads.
- **Cache HTML output** is more aggressive as it caches the HTML generated for the related posts and thereby reduces all the queries needed to look up the posts, thumbnails, etc.

## Enabling/disabling caching

Both settings are enabled by default to optimize the related posts out-of-the-box. You can toggle the settings by navigating to **Settings** > **Related Posts** and the [**Performance** tab](https://webberzone.com/support/knowledgebase/contextual-related-posts-performance-settings/).

The **Cache HTML output** setting will supersede **Cache Posts only** and is recommended particularly on busy sites. This setting will not work with the [Related Posts Query Loop Block](https://webberzone.com/support/knowledgebase/contextual-related-posts-blocks/#contextual-related-posts-query-loop-block) and so it is recommended that you keep **Cache Posts only** enabled.

## Clearing the cache

Contextual Related Posts caches all related posts for one week by default.

You can manually clear the plugin cache by visiting the [Tools page](https://webberzone.com/support/knowledgebase/contextual-related-posts-settings-tools/) and using the **Clear Cache** button. Contextual Related Posts Pro users also have a dedicated **Clear cache** button in the Settings page at the bottom.

The cache of a post is also cleared when it is edited. This is to ensure that the related posts are generated using the latest content.

## Changing the caching duration

The duration of the cache can be modified by changing the constant `CRP_CACHE_TIME`, which is set to be one week by default. The easiest way to modify this constant is by setting it to a different period in your **wp-config.php**. e.g. the below will set it to a week using the inbuilt WordPress constant.

```php
define( 'CRP_CACHE_TIME', WEEK_IN_SECONDS );
```

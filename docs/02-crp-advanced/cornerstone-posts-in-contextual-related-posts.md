---
slug: cornerstone-posts-in-contextual-related-posts
title: "Cornerstone Posts in Contextual Related Posts Pro"
products: [contextual-related-posts]
sections: ["02-crp-advanced"]
tags: [contextual-related-posts, pro, related-posts, settings]
status: publish
order: 0
---

## What Are Cornerstone Posts?

Cornerstone posts are the foundation of your website’s content strategy — your evergreen guides, key landing pages, or flagship blog posts. These pages explain core concepts, drive traffic, or convert readers into subscribers, customers, or loyal fans.

Most cornerstone content is well over 2,000 words long, though some can be over 5,000 and plays a significant role in any SEO strategy.

By default, Contextual Related Posts shows posts that are algorithmically similar to the current one. But not all important content naturally ranks high in related results — especially newer posts, static pages, or niche articles.

That’s where the cornerstone posts feature introduced in [Contextual Related Posts Pro](https://webberzone.com/plugins/contextual-related-posts/pro/) v4 comes in.

CRP Pro automatically includes a few of your chosen cornerstone posts in each related posts list when enabled. They’re inserted at random positions to blend naturally with the rest, giving your key content more visibility without overwhelming the reader or skewing relevance.

**In short:** if there are articles you want your audience to keep seeing — no matter what they’re reading — this feature ensures they show up regularly, subtly, and effectively.

## How It Works

Here’s how Contextual Related Posts Pro selects your cornerstone posts.

- 20% of the related posts are reserved for cornerstone content by default.
- A minimum of one cornerstone post is always included.
- Posts are randomly selected from the list you provide.
- Cornerstone posts are randomly inserted within the related posts list. If caching is enabled, this will not change on page reloads.

## Setting Up Cornerstone Posts

### Admin Configuration

1. Go to **Settings → Related Posts**
2. Under the **List tuning** section
3. Find the **Cornerstone IDs** field
4. Enter a comma-separated list of post, page, or custom post type IDs

> [!NOTE]
> ⓘ These IDs will be randomly picked and inserted into the final list of related posts. By default, ~20% of the slots will be used.

## Customization

### Using a Filter

You can modify the default percentage of cornerstone posts using the `crp_cornerstone_percentage` filter.

```php
/**
 * Filter the default cornerstone percentage.
 *
 * @param int $percentage Percentage of related posts to be replaced with cornerstone posts.
 * @return int Modified percentage.
 */
add_filter( 'crp_cornerstone_percentage', 'modify_crp_cornerstone_percentage' );

function modify_crp_cornerstone_percentage( $percentage ) {
    return 30; // Use 30% instead of the default 20%.
}
```

### Passing IDs via Query Arguments

Alternatively, specify cornerstone post IDs dynamically when calling CRP:

```php
$args = array(
    'cornerstone_post_ids'   => '1,2,3', // Comma-separated list of post IDs.
    'cornerstone_percentage' => 25,      // Custom percentage for this specific query.
);
```

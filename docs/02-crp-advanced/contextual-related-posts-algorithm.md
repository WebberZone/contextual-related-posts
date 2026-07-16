---
slug: contextual-related-posts-algorithm
title: "How the Contextual Related Posts Algorithm Works"
products: [contextual-related-posts]
sections: ["02-crp-advanced"]
tags: [contextual-related-posts, related-posts]
status: publish
order: 0
---

[kbtoc]

[Contextual Related Posts](https://webberzone.com/plugins/contextual-related-posts/) (CRP) boosts user engagement by displaying relevant posts at the end of each article. Its algorithm taps into MySQL’s full-text search to deliver smart, context-aware suggestions.

## Core Functionality

At its heart, CRP uses MySQL FULLTEXT indexes to analyze post content. CRP examines the posts’ fields, including the **post title** and **content**. This allows the algorithm to identify relevant keywords and phrases within the content, matching related posts based on textual similarity.

Results are ordered by relevance score. From v4.3.0, posts with equal scores are ordered by post date (newest first) and then by post ID, so the list is stable across page loads instead of varying with the database's internal row order.

### Free Version

In the free version of CRP, enabling **Related posts based on title and content** in the [List Tuning Tab](https://webberzone.com/support/knowledgebase/contextual-related-posts-list-tuning-settings/) gives equal importance to both fields. CRP treats a match in the title the same as one in the content. For content-heavy sites, experiment with this toggle to see what works best.

### Pro Version

CRP Pro enhances the algorithm with:

- A dedicated FULLTEXT index for `post_excerpt`.
- Custom weight settings for each field — title, content, and excerpt.

**Example:** You can assign a higher weight to `post_excerpt` to favor posts with similar summaries, while downplaying content matches. This lets you tailor results to your content strategy — e.g., favoring concise summaries or catchy titles.

## Weighting Categories, Tags, and Taxonomies

CRP Pro v4 introduced **taxonomy weighting** — giving you finer control over contextual matching beyond text.

In the **List Tuning** tab, you can assign weights to:

- Categories
- Tags
- Custom taxonomies (e.g., genres, topics)

CRP looks at shared terms between the current post and candidates, applying the weight accordingly.

**How it works:**

- Posts sharing terms from high-weighted taxonomies rank higher.
- You can combine this with content weighting for hybrid relevance.

**Example:**

| Element | Weight |
| --- | --- |
| Post title | 3 |
| Post content | 1 |
| Category match | 2 |

A post with a matching category and partial content match will outrank one that only matches by content.

This system is ideal for:

- Niche blogs with defined topic clusters
- Product or review sites grouped by taxonomy
- Publishers who rely on SEO plugins to set primary terms

> [!NOTE]
> ⓘ CRP reads the *primary term* set by your SEO plugin (e.g. Yoast, The SEO Framework or Rank Math). If none is set, it falls back to the first term.

## Advanced Filtering Options

Beyond weights, CRP supports filters to limit the post pool:

1. **Post types**: Limit related posts to posts, pages and/or custom post types. You can also limit it to the same post type.
2. **Author**: Limit related posts to those written by the same author, helping to maintain consistency in content style and tone.
3. **Category**: Restrict related posts to those in the same category, ensuring thematic relevance and coherence.
4. **Tag or Custom Taxonomy**: Filter related posts based on shared tags or custom taxonomies, facilitating content discovery within specific topics or themes. You can use this option if you do not want to use the full-text indexes. If enabled, it will only select posts from the same primary category/term. This is usually set using your SEO plugin and will default to the first category/term returned by WordPress.
5. **Meta Field Value**: Narrow down related posts based on the value of custom meta fields, providing even more granular control over post relationships.

By leveraging these advanced filtering options, users can tailor the related posts algorithm to suit their site’s content structure and audience preferences, resulting in more targeted and meaningful content recommendations.

## Tuning the Algorithm

Go to **Settings → CRP → List Tuning**:

- Adjust weights for each content field (Pro)
- Set taxonomy weights (Pro)
- Configure filtering rules

Tweak these to align with your editorial goals or user behavior.

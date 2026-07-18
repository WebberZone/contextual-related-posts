---
slug: wp-rest-api-integration
title: "WP REST API Integration"
products: [contextual-related-posts]
sections: ["03-crp-developer-docs"]
tags: [contextual-related-posts, wp-rest-api]
status: publish
order: 0
---

Since v3.1.0, [Contextual Related Posts](https://webberzone.com/plugins/contextual-related-posts/) has included support for viewing the related posts via the [WordPress REST API](https://developer.wordpress.org/rest-api/).

The plugin registers one namespace i.e. `contextual-related-posts/v1` that can be used to retrieve the related posts for a particular post ID.

[kbtoc]

## Requirements

To use the latest version of the REST API you must be using:

- Contextual Related Posts 3.1+.
- WordPress 6.6+.
- Pretty permalinks in `Settings > Permalinks` so that the custom endpoints are supported. **Default permalinks will not work.**
- You may access the API over either HTTP or HTTPS, but *HTTPS is recommended where possible*.

## Endpoint

The plugin registers one namespace `contextual-related-posts/v1` and currently, there is a single endpoint available at `posts`.

```text
GET https://example.com/wp-json/contextual-related-posts/v1/posts/<id>/
```

## Arguments

| Parameter | Description |
| --- | --- |
| id | Post ID. This is also passed as part of the path as noted above. This is mandatory |
| limit | Number of posts to retrieve. You can also use posts_per_page instead |
| post_types | Comma-separated list of post types to which the related posts belong to. Alternatively use post_type |
| same_post_type | Only retrieve posts from the same post type as that of the post with the id as above |
| same_author | Only retrieve posts from the same author as that of the post with the id as above |
| exclude_post_ids | Comma-separated list of post IDs to exclude |
| exclude_categories | Comma-separated list of Taxonomy IDs from which posts are excluded |

## HTML endpoint (Pro)

Contextual Related Posts Pro registers a second endpoint that returns the rendered related posts HTML instead of a list of post objects:

```text
GET https://example.com/wp-json/contextual-related-posts/v1/posts/<id>/html
```

This endpoint powers the [Lazy Load Related Posts](https://webberzone.com/support/knowledgebase/lazy-loading-related-posts/) feature, so display arguments (`args`) are only honored when accompanied by a valid `sig` — an HMAC signature generated server-side when the placeholder is rendered. Requests without a matching signature fall back to the default arguments for that post; there is no way to render arbitrary arguments for a post from the client side.

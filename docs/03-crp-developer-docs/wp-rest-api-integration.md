---
slug: wp-rest-api-integration
title: "WP REST API Integration"
products: [contextual-related-posts]
sections: ["03-crp-developer-docs"]
tags: [contextual-related-posts, wp-rest-api]
status: publish
order: 0
toc: true
---

Since v3.1.0, [Contextual Related Posts](https://webberzone.com/plugins/contextual-related-posts/) has included support for viewing the related posts via the [WordPress REST API](https://developer.wordpress.org/rest-api/).

The plugin registers the `contextual-related-posts/v1` namespace to retrieve related posts for a particular post ID. Pro features also register specialized endpoints in this namespace for rendered HTML and block-editor previews.

Since v4.5.0, the plugin also registers abilities through the WordPress Abilities API. These are separate from the `contextual-related-posts/v1` endpoints documented here. See [Contextual Related Posts Abilities API](https://webberzone.com/support/knowledgebase/contextual-related-posts-abilities-api/) for the ability names, inputs, and permissions.

[toc]

## Requirements

To use the latest version of the REST API you must be using:

- Contextual Related Posts 3.1+.
- WordPress 6.9+.
- Pretty permalinks in `Settings > Permalinks` so that the custom endpoints are supported. **Default permalinks will not work.**
- You may access the API over either HTTP or HTTPS, but *HTTPS is recommended where possible*.

## Endpoint

The related-posts data endpoint is:

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
| lang | TranslatePress language code to render the response in |

The `lang` parameter is used only with TranslatePress. It does not change which posts are returned; it selects the language the response fields are rendered in. On TranslatePress sites, CRP translates the REST response before it is sent — TranslatePress's page output buffer does not run for REST requests, so CRP hooks into `rest_pre_echo_response` and translates titles, excerpts, links, and permalinks. When `lang` is omitted, CRP falls back to the language of the referring page. Use the `crp_trp_rest_language` filter to override the resolved language.

## Advanced block editor preview (Pro)

The Related Posts Advanced block uses this editor-only endpoint to load live results in the block editor:

```text
POST https://example.com/wp-json/contextual-related-posts/v1/advanced-preview
```

The request includes a `sourceId` and may include the block's `query` and `imageSettings`. The current user must be able to edit posts, pages, or theme options. The source post must be publicly viewable or editable by that user. This route serves the block editor preview; use the `posts` endpoint to retrieve related-post data for an integration.

## HTML endpoint (Pro)

Contextual Related Posts Pro registers a second endpoint that returns the rendered related posts HTML instead of a list of post objects:

```text
GET https://example.com/wp-json/contextual-related-posts/v1/posts/<id>/html
```

This endpoint powers the [Lazy Load Related Posts](https://webberzone.com/support/knowledgebase/lazy-loading-related-posts/) feature, so display arguments (`args`) are only honored when accompanied by a valid `sig` — an HMAC signature generated server-side when the placeholder is rendered. Requests without a matching signature fall back to the default arguments for that post; there is no way to render arbitrary arguments for a post from the client side.

## Limiting the number of posts returned

The `limit` parameter is capped at 100 by default. Use the `crp_rest_api_max_limit` filter to change the maximum:

```php
/**
 * Filters the maximum number of related posts the REST API will return.
 *
 * @param int $max Maximum allowed limit. Default 100.
 */
add_filter( 'crp_rest_api_max_limit', function ( $max ) {
    return 20;
} );
```

## Enable or disable CRP endpoints

The **REST API endpoints** setting under **Settings → Related Posts → Features → API integrations** is enabled by default. Turning it off unregisters Contextual Related Posts routes, including the related-posts data endpoint and Pro routes for lazy loading and block previews. It does not disable the WordPress REST API itself. When endpoints are disabled, lazy loading renders related posts inline and the Advanced block cannot load live editor results. The WordPress Abilities API has its own setting; see [Contextual Related Posts Abilities API](https://webberzone.com/support/knowledgebase/contextual-related-posts-abilities-api/).

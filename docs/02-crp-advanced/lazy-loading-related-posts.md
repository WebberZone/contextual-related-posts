---
slug: lazy-loading-related-posts
title: "Lazy Loading Related Posts"
products: [contextual-related-posts]
sections: ["02-crp-advanced"]
tags: [contextual-related-posts, pro, performance, lazy-load]
status: publish
order: 0
---

Lazy loading, introduced in [Contextual Related Posts Pro](https://webberzone.com/plugins/contextual-related-posts/) v4.3.0, defers rendering the related posts until the visitor is about to scroll them into view. The page is served without running the related posts query, and the list is fetched in the background via the REST API.

## Why lazy load?

- **Faster initial page load.** The related posts query — usually the most expensive part of CRP — runs only when the list is actually needed.
- **Better with page caching.** Cached pages no longer bake in a stale related posts list; each visitor fetches a fresh list when they scroll to it.
- **No layout surprises.** A placeholder occupies the list's position and announces the load to screen readers while the content is fetched.

The trade-off: the related posts links are inserted by JavaScript, so search engines may not index them. If internal linking for SEO is the main reason you use related posts, leave lazy loading off.

## Enabling lazy loading

Enable **Lazy load related posts** *(Pro only)* in the [Performance tab](https://webberzone.com/support/knowledgebase/contextual-related-posts-performance-settings/) of the settings page. The setting applies to all display methods: the content filter, shortcode, widget, and block.

Lazy loading is automatically skipped where it cannot work — feeds, AMP pages, admin requests, REST, AJAX and cron requests, and WP-CLI — and the related posts render inline instead.

## Per-instance override

The shortcode accepts a `lazy_load` parameter that overrides the global setting for that instance:

```text
[crp lazy_load="0"]
```

Use `lazy_load="1"` to lazy load a single shortcode while the global setting is off, or `lazy_load="0"` to render one instance inline while everything else lazy loads.

## How it works

When lazy loading applies, CRP outputs a placeholder `<div class="crp_related crp-lazy-load">` carrying the post ID and an HMAC-signed copy of the display arguments. A small script watches the placeholder with an IntersectionObserver and, shortly before it enters the viewport, requests the rendered HTML from a REST endpoint (`contextual-related-posts/v1/posts/<id>/html`). The signature ensures the display arguments cannot be tampered with in transit.

If the arguments payload is too large to round-trip safely, CRP falls back to rendering that instance inline.

## Developer filters

| Filter | Description |
| --- | --- |
| `crp_lazy_load` | Whether a given instance should lazy load. Receives the current decision, the post object, and the arguments array. |
| `crp_lazy_load_placeholder` | The placeholder HTML. Receives the HTML, post object, and arguments array. |
| `crp_lazy_load_root_margin` | How far before the viewport the fetch starts. Default `200px`. |

For example, to start fetching earlier on long pages:

```php
add_filter( 'crp_lazy_load_root_margin', fn() => '400px' );
```

To disable lazy loading on a specific post type:

```php
add_filter(
    'crp_lazy_load',
    function ( $lazy_load, $post ) {
        if ( $post instanceof WP_Post && 'product' === $post->post_type ) {
            return false;
        }
        return $lazy_load;
    },
    10,
    2
);
```

## Styling the placeholder

The placeholder uses the classes `crp_related crp-lazy-load`, so existing styles targeting `crp_related` apply to it. Target `.crp-lazy-load` to style the loading state — for example, to reserve a minimum height and avoid layout shift:

```css
.crp-lazy-load {
    min-height: 200px;
}
```

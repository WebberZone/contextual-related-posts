---
slug: cache-invalidation-filters
title: "Cache invalidation filters"
products: [contextual-related-posts]
sections: ["03-crp-developer-docs"]
tags: [cache, contextual-related-posts, filters, performance]
status: publish
order: 0
featured_image: "https://webberzone.com/wp-content/uploads/2019/02/WZLogo-white-1.png"
---

[Contextual Related Posts](https://webberzone.com/plugins/contextual-related-posts/) caches its related posts so that repeat visitors do not re-run the query. Since 4.5.0 the cache is kept fresh automatically when you save a post, and this page documents the filters that control that behavior.

## What happens when you save a post

Saving a post does two things:

1. **Clears that post's own cached related posts.** This runs on every save path — the block editor, the classic editor, Quick Edit, Bulk Edit, WP-CLI, the REST API, a scheduled post going live, and any plugin calling `wp_update_post()`. It has no opt-out filter, because a post's own cache is always wrong once its content changes; the only post types excluded from it are the bookkeeping ones listed under `crp_internal_post_types` below.
2. **Clears the cache of the posts it is related to.** This lets a newly published post appear in older posts' lists without waiting for the cache to expire. It runs on `shutdown`, after the request's other work is done, and it is what the filters below control.

Both parts skip revisions, autosaves and auto-drafts.

### Best-effort, not a reverse index

Step 2 finds the posts to clear by running the related posts query once for the saved post. That is an approximation, and it misses two cases:

- **Ranking is not symmetric.** Post B may be in post A's top results while A sits well down B's own list. Only the top matches are cleared, so a post that ranks below the cut-off keeps its cached list until it expires. Raise `crp_related_cache_clear_limit` to widen the net.
- **A post that has never been rendered has no record of what it used to match.** Editing such a post so that it is no longer related to another leaves that other post's cached list showing it until the cache expires.

Neither case affects the common one: a post becoming newly related still appears straight away.

## Filters

### `crp_clear_related_cache_on_save`

Whether saving a post clears the cache of the posts it is related to.

| | |
|---|---|
| `$clear` | `bool` — whether to clear. Default `true`. |
| `$post_id` | `int` — the post being saved. |
| `$post` | `WP_Post` — the post object being saved. |

This is the opt-out. Returning `false` leaves other posts serving their cached related posts until the cache expires, so a newly published post will not appear in their lists until then. Useful on very large sites, on a site that has both cache settings turned off, or around a migration that does not define `WP_IMPORTING`.

```php
add_filter( 'crp_clear_related_cache_on_save', '__return_false' );
```

Clearing the saved post's *own* cache is not affected by this filter.

### `crp_related_cache_clear_limit`

How many related posts are looked up when deciding whose cache to clear.

| | |
|---|---|
| `$limit` | `int` — number of posts. Defaults to the **Number of related posts to display** setting. |
| `$post_id` | `int` — the post being saved. |

Raise it to catch posts where the saved post ranks lower, at the cost of more rows deleted per save.

```php
add_filter(
    'crp_related_cache_clear_limit',
    function ( $limit ) {
        return $limit * 3;
    }
);
```

### `crp_related_cache_clear_ids`

The final list of post IDs whose cache is about to be cleared, after the current and previously cached relations have been merged and the saved post removed.

| | |
|---|---|
| `$ids` | `array` — post IDs. |
| `$post_id` | `int` — the post being saved. |

Use it to add posts the query cannot know about, such as a landing page that always shows a fixed list, or to remove posts you never want invalidated.

```php
add_filter(
    'crp_related_cache_clear_ids',
    function ( $ids ) {
        $ids[] = 42; // A hub page that lists everything.
        return $ids;
    }
);
```

### `crp_related_cache_clear_post_types`

The post types treated as able to appear in another post's related posts list.

| | |
|---|---|
| `$post_types` | `array` — post type names. Defaults to the **Post types to include** setting. |
| `$post` | `WP_Post` — the post object being saved. |

The shortcode and the block accept a per-instance `post_types` attribute that can include a post type the setting excludes. The plugin cannot see those attributes when a post is saved, so add such a post type here for its lists to be cleared.

```php
// [[crp post_types="post,recipe"]] used somewhere on the site.
add_filter(
    'crp_related_cache_clear_post_types',
    function ( $post_types ) {
        $post_types[] = 'recipe';
        return $post_types;
    }
);
```

### `crp_internal_post_types`

Post types whose saves never touch the cache at all.

| | |
|---|---|
| `$internal` | `array` — post type names. |

Defaults to WordPress' own bookkeeping post types: `nav_menu_item`, `customize_changeset`, `custom_css`, `oembed_cache`, `user_request`, `wp_block`, `wp_global_styles`, `wp_navigation`, `wp_template`, `wp_template_part`, `wp_font_family` and `wp_font_face`. These cannot display related posts, so skipping them keeps menu and template saves cheap.

Add your own bookkeeping post type here if it is saved often and never shows related posts. Do not add a post type you render related posts for, including one you render by passing an explicit `post_id` — its cache would then never be cleared.

## Changing ranking settings

Saving the plugin settings clears the entire cache when a setting that affects ranking changed, and resetting the settings to their defaults does the same. Cached lists are keyed on the query arguments rather than the settings, so without this the old ranking would be served until each entry expired. The list of settings that trigger the flush is filterable.

### `crp_cache_busting_settings`

The settings whose change flushes the cache on save.

| | |
|---|---|
| `$keys` | `string[]` — setting keys that invalidate cached results. |
| `$settings` | `array` — the sanitized settings being saved. |

Defaults to `relevance_threshold`, `weight_title`, `weight_content`, `weight_excerpt`, `weight_taxonomy_category`, `weight_taxonomy_post_tag`, `weight_taxonomy_default`, `weight_primary_term_boost`, `weight_recency`, and `recency_halflife`.

Add a key here for any custom setting that changes which posts a query returns, or in which order:

```php
add_filter(
    'crp_cache_busting_settings',
    function ( $keys ) {
        $keys[] = 'my_custom_ranking_setting';
        return $keys;
    }
);
```

## Bulk saves and imports

Clearing one post's related posts at a time is fine for editorial work, but not for a bulk operation. Above a threshold the plugin stops doing step 2 post by post and schedules a single flush of the whole cache instead, a few minutes after the request ends. Step 1 continues either way, so every saved post's own cache is cleared immediately. After a large import the entire cache is stale anyway, because every existing post's list was computed without the imported posts, so one flush is both cheaper and more accurate.

### `crp_related_cache_clear_batch_limit`

The number of posts saved in a single request above which the plugin escalates to that single flush.

| | |
|---|---|
| `$batch_limit` | `int` — number of posts. Default `20`. |

Set it to `0` to never escalate, so every save clears its related posts individually no matter how many posts a request touches.

```php
add_filter(
    'crp_related_cache_clear_batch_limit',
    function () {
        return 50;
    }
);
```

### `crp_deferred_cache_flush_delay`

How long after the request ends the deferred flush runs.

| | |
|---|---|
| `$delay` | `int` — seconds. Default `300` (5 minutes). Values below 60 are raised to 60. |

The event is scheduled when the request ends rather than when the first post is saved, and each subsequent request pushes it further out. A long import therefore produces one flush, once the imports stop.

### `crp_is_importing`

Whether the current request is treated as a bulk import.

| | |
|---|---|
| `$importing` | `bool` — defaults to the value of the `WP_IMPORTING` constant. |

During an import the plugin skips the per-post related posts lookup and clearing, and schedules the same single flush instead. Step 1 is unaffected: each saved post still has its own cached related posts cleared as it is saved, so an import that updates existing posts still runs one delete per updated post. Posts being inserted have no cache yet, so nothing is deleted for them. WordPress' own importer and `wp import` define `WP_IMPORTING`, but many third-party importers and custom migration scripts do not. Return `true` from this filter to get the same treatment.

```php
// A custom migration routine that does not define WP_IMPORTING.
add_filter( 'crp_is_importing', '__return_true' );
```

A migration that runs one post per HTTP request never reaches the batch threshold, so this filter — or `crp_clear_related_cache_on_save` — is the way to keep it fast.

## Related

- [Caching in Contextual Related Posts](https://webberzone.com/support/knowledgebase/caching-in-contextual-related-posts/)
- [Display related posts with CRP_Query](https://webberzone.com/support/knowledgebase/crp-query/)

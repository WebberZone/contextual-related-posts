---
slug: contextual-related-posts-multilingual
title: "Contextual Related Posts and Multilingual Sites"
products: [contextual-related-posts]
sections: ["02-crp-advanced"]
tags: [contextual-related-posts, multilingual]
status: publish
order: 0
toc: true
---

[toc]

[Contextual Related Posts](https://webberzone.com/plugins/contextual-related-posts/) works with **WPML**, **Polylang**, and **TranslatePress**, and no configuration is needed for any of them. On a multilingual site, related posts are matched to the language being viewed, and cached output is stored per language so visitors never see another language's titles or links.

## Supported plugins

| Plugin | How related posts are handled |
| --- | --- |
| **WPML** | Each language has its own posts. CRP maps each related post to its equivalent in the current language. |
| **Polylang** | Each language has its own posts. CRP uses Polylang's post mapping to return the translated equivalents. |
| **TranslatePress** | One set of posts is translated on the fly. CRP returns the visitor's language, including over the REST API. |

WPML and Polylang support is built into Contextual Related Posts; TranslatePress support was added in v4.4.2.

## WPML

WPML stores each language's posts separately, so the related posts the query finds are usually in the site's default language. CRP reads the current language with the `wpml_current_language` filter and passes each related post through the `wpml_object_id` filter to find its equivalent in the language being viewed.

By default, a related post with no translation in the current language is left out of the list. To keep the original post instead, add the `crp_wpml_return_original` filter to your theme's `functions.php`:

```php
add_filter(
    'crp_wpml_return_original',
    function ( $return_original, $post_id ) {
        return true;
    },
    10,
    2
);
```

## Polylang

Polylang also stores each language's posts separately. CRP calls Polylang's `pll_get_post` function to map each related post to its current-language equivalent and reads the language with `pll_current_language` when it builds the cache key.

This behavior is automatic whenever Polylang is active; there is nothing to enable in CRP.

## TranslatePress

TranslatePress keeps one set of posts and translates the text on the fly. CRP detects TranslatePress when the `trp_translate` function and the `TRP_Translate_Press` class are available, and it reads the current language from the `TRP_LANGUAGE` global.

TranslatePress's page output buffer does not run for REST requests, so CRP hooks into `rest_pre_echo_response` and translates the REST response itself. This covers related posts requested over the REST API — such as those served to the block editor or to [lazy loading](https://webberzone.com/support/knowledgebase/lazy-loading-related-posts/) *(Pro only)* — which TranslatePress cannot reach on its own.

### REST API `lang` parameter

REST URLs carry no language prefix for CRP to detect, so pass the TranslatePress language code with the `lang` parameter:

```text
GET https://example.com/wp-json/contextual-related-posts/v1/posts/123/?lang=de
```

The `lang` parameter only controls TranslatePress translation; it does not change which posts are returned. When it is omitted, CRP falls back to the language of the referring page. You can override the resolved language with the `crp_trp_rest_language` filter:

```php
add_filter(
    'crp_trp_rest_language',
    function ( $language, $request ) {
        return 'fr'; // A valid published TranslatePress language code.
    },
    10,
    2
);
```

## Language-isolated caching

CRP adds the current language to every cache key through `Language_Handler::get_cache_language()`, so **Cache Posts only** and **Cache HTML output** are stored separately for each language. A visitor is never served another language's titles, excerpts, or links, even on a cached page.

`get_cache_language()` resolves the language from TranslatePress first, then Polylang, then WPML. On a single-language site it returns an empty string and the cache key is unchanged. You can override the language used in the cache key with the `crp_cache_language` filter.

After you update to v4.4.2, your multilingual site rebuilds its related posts cache once because the cache key format changed.

## See also

- [Caching in Contextual Related Posts](https://webberzone.com/support/knowledgebase/caching-in-contextual-related-posts/)
- [WP REST API Integration](https://webberzone.com/support/knowledgebase/wp-rest-api-integration/)

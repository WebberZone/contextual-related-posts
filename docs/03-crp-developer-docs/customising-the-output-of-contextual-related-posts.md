---
slug: customising-the-output-of-contextual-related-posts
title: "Customizing the output of Contextual Related Posts"
products: [contextual-related-posts]
sections: ["03-crp-developer-docs"]
tags: [contextual-related-posts, css, customisation, styles]
status: publish
order: 0
featured_image: "https://webberzone.com/wp-content/uploads/2019/02/WZLogo-white-1.png"
---

[Contextual Related Posts](https://webberzone.com/plugins/contextual-related-posts/) has several customization options available via the [Settings page](https://webberzone.com/support/knowledgebase/contextual-related-posts-general-settings/) in WordPress Admin. You can access this via **Settings » Related Posts**.

A typical HTML output for the plugin is below. The plugin also provides you with a set of CSS classes that allow you to style your posts.

```html
<div class="crp_related crp-style-name ">
    <h3>Related Posts:</h3>
    <ul>
        <li>
            <a href="https://webberzone.com/techtites/2006/12/17/tectites-daily-summary-sunday-2/" rel="nofollow" target="_blank" class="post-184"><img src="https://webberzone.com/techtites/wp-content/uploads/sites/3/2014/02/sunday11-150x100.png" alt="Tectites Daily: Summary Sunday" title="Tectites Daily: Summary Sunday" width="150" height="150"><span class="crp_title">Tectites Daily: Summary Sunday</span></a>
        </li>
    </ul>
    <div class="crp_clear"></div>
    <p class="crp_class_credit"><small>Powered by <a href="https://webberzone.com/plugins/contextual-related-posts/" rel="nofollow" style="float:none">Contextual Related Posts</a></small></p>
</div>
```

The main CSS classes are:

- **crp_related**: Class of the main wrapper `div`
- **crp-style-name**: An additional class for the main `div` when a custom style is selected in the [Styles tab](https://webberzone.com/support/knowledgebase/contextual-related-posts-styles-settings/)
- **crp_title**: Class of the `span` tag for title of the post
- **crp_thumb**: Class of the post thumbnail `img` tag
- **crp_excerpt**: Class of the `span` tag for excerpt (if enabled)
- **crp_author**: Class of the `span` tag for author (if enabled)
- **crp_date**: Class of the `span` tag for date (if enabled)
- **crp_related_shortcode**: Additional class of the main wrapper `div` when the related posts are displayed via a shortcode
- **crp_related_block**: Additional class of the main wrapper `div` when the related posts are displayed via the Gutenberg block
- **crp_related_widget**: Additional class added to the main wrapper `div` alongside **crp_related** when the related posts are displayed via the widget

You can add CSS styles for these classes either in the [Styles tab](https://webberzone.com/support/knowledgebase/contextual-related-posts-styles-settings/) or in your theme’s *style.css*. If you’re adding additional styles for a specific custom style, it is recommended to use a selector like `.crp_related.crp-style-name` e.g. `.crp_related.crp-rounded-thumbs`.

## Choose the right customization point

| If you need to… | Use… |
| --- | --- |
| Configure built-in output options or change appearance only | The [Settings page](https://webberzone.com/support/knowledgebase/contextual-related-posts-general-settings/) and CSS. See [Styles settings](https://webberzone.com/support/knowledgebase/contextual-related-posts-styles-settings/). |
| Suppress or replace output before CRP queries for related posts | [`crp_pre_related_posts`](https://webberzone.dev/contextual-related-posts/hooks/crp_pre_related_posts/) |
| Keep CRP’s query results but replace the generated HTML | `crp_custom_template` |
| Control which posts are queried and render your own loop | [`CRP_Query` or `get_crp_posts()`](https://webberzone.com/support/knowledgebase/crp-query/) |

## Filter hooks

### [`crp_pre_related_posts`](https://webberzone.dev/contextual-related-posts/hooks/crp_pre_related_posts/)

Short-circuits the related posts rendering. Return a non-null value to replace the output entirely — the query and the default rendering are skipped. Runs for all display methods: the content filter, shortcode, widget, block, and manual calls. Contextual Related Posts Pro uses this internally to swap in the [lazy load placeholder](https://webberzone.com/support/knowledgebase/lazy-loading-related-posts/).

```php
add_filter(
    'crp_pre_related_posts',
    function ( $pre, $args, $post ) {
        if ( 42 === $post->ID ) {
            return ''; // Suppress related posts on this post.
        }
        return $pre;
    },
    10,
    3
);
```

**Parameters:**

- `$pre` *(string|null)* — Pre-rendered output. Default `null` (continue with the default rendering).
- `$args` *(array)* — Fully parsed arguments array.
- `$post` *(WP_Post)* — Post object the related posts are generated for.

**Returns:** `string|null` — Return a string to use it as the output; return `null` to continue normally.

### `crp_custom_template`

Use this filter to keep CRP’s related-post query and replace its default HTML. It runs after CRP retrieves the related posts and before the built-in renderer runs. Return a non-empty HTML string to replace the complete default output, including its wrapper and heading. Return the incoming `$template` value to use the built-in renderer; it is `null` by default. An empty string does not replace the output.

The filter runs for automatic content output, the shortcode, widget, native Related Posts block, and manual `get_crp()` or `echo_crp()` calls. Direct `CRP_Query` and `get_crp_posts()` calls retrieve posts without using this renderer, so render those results yourself.

**Parameters:**

- `$template` *(string|null)* — Default return value. Initially `null`.
- `$results` *(WP_Post[]|int[])* — Related posts as post objects or IDs, matching the return type of `get_crp_posts()`.
- `$args` *(array)* — Fully parsed display arguments.

**Returns:** `string|null` — Return a non-empty HTML string to replace the default output, or return `$template` to continue with the built-in renderer.

This example supports either post objects or IDs in `$results`. It escapes the link and title and allows the safe image markup generated for the thumbnail.

```php
add_filter(
    'crp_custom_template',
    function ( $template, $results, $args ) {
        $items = array();

        foreach ( $results as $result ) {
            $related_post = get_post( $result );
            if ( ! $related_post instanceof \WP_Post ) {
                continue;
            }

            $thumbnail = get_the_post_thumbnail( $related_post->ID, 'thumbnail' );
            $items[]   = sprintf(
                '<li><a href="%1$s">%2$s<span>%3$s</span></a></li>',
                esc_url( get_permalink( $related_post ) ),
                wp_kses_post( $thumbnail ),
                esc_html( get_the_title( $related_post ) )
            );
        }

        if ( empty( $items ) ) {
            return $template;
        }

        return '<ul class="crp-custom-template">' . implode( '', $items ) . '</ul>';
    },
    10,
    3
);
```

CRP can cache this HTML for eligible requests when HTML caching is enabled. If your markup varies by user or request context, disable HTML caching in the arguments for that display call with `'cache' => 0`. This does not disable related-post ID caching, which is controlled separately by `cache_posts`.

## PHP wrapper functions

### `Display::get_default_args()`

Returns the full default arguments array for the related posts display — the built-in defaults merged with the saved plugin settings. Use it to build a complete `$args` array before calling the rendering or query functions.

```php
$args = WebberZone\Contextual_Related_Posts\Frontend\Display::get_default_args();
```

**Returns:** `array` — Default arguments including all saved settings.

## See also

- [`crp_pre_related_posts`](https://webberzone.dev/contextual-related-posts/hooks/crp_pre_related_posts/)
- [Display related posts with CRP_Query](https://webberzone.com/support/knowledgebase/crp-query/)
- [Contextual Related Posts Styles settings](https://webberzone.com/support/knowledgebase/contextual-related-posts-styles-settings/)

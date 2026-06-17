---
slug: customising-the-output-of-contextual-related-posts
title: "Customizing the output of Contextual Related Posts"
products: [contextual-related-posts]
sections: ["03-crp-developer-docs"]
tags: [contextual-related-posts, css, customisation, styles]
status: publish
order: 0
---

Contextual Related Posts has several customization options are available via the [Settings page](https://webberzone.com/support/knowledgebase/contextual-related-posts-general-settings/) in WordPress Admin. You can access this via **Settings » Related Posts**

A typical HTML output for the plugin is below. The plugin also provides you with a set of CSS classes that allow you to style your posts.

```html
<div class="crp_related crp-style-name ">
    <h3>Related Posts:</h3>
    <ul>
        <li>
            <a href="https://webberzone.com/techtites/2006/12/17/tectites-daily-summary-sunday-2/" rel="nofollow" target="_blank" class="post-184"><img src="https://webberzone.com/techtites/wp-content/uploads/sites/3/2014/02/sunday11-150x100.png" class="crp_thumb crp_featured" alt="Tectites Daily: Summary Sunday" title="Tectites Daily: Summary Sunday" width="150" height="150"><span class="crp_title">Tectites Daily: Summary Sunday</span></a>
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

## Filter hooks

### `crp_pre_related_posts`

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

## PHP wrapper functions

### `Display::get_default_args()`

Returns the full default arguments array for the related posts display — the built-in defaults merged with the saved plugin settings. Use it to build a complete `$args` array before calling the rendering or query functions.

```php
$args = \WebberZone\Contextual_Related_Posts\Frontend\Display::get_default_args();
```

**Returns:** `array` — Default arguments including all saved settings.

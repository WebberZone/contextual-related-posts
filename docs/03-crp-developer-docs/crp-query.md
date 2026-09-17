---
slug: crp-query
title: "Display related posts with CRP_Query"
products: [contextual-related-posts]
sections: ["03-crp-developer-docs"]
tags: [contextual-related-posts, crp_query]
status: publish
order: 0
---

[Contextual Related Posts](https://webberzone.com/plugins/contextual-related-posts/) v3.0.0 introduced CRP_Query, which works as a wrapper for [WP_Query](https://developer.wordpress.org/reference/classes/wp_query/). This brings all the power and flexibility of WP_Query to Contextual Related Posts. If you’re not familiar with WP_Query, I recommend [reading docs](https://developer.wordpress.org/reference/classes/wp_query/).

## Standard Loop

```php
<?php

// The Query.
$the_query = new CRP_Query( $args );

// The Loop.
if ( $the_query->have_posts() ) {
    echo '<ul>';
    while ( $the_query->have_posts() ) {
        $the_query->the_post();
        echo '<li>' . get_the_title() . '</li>';
    }
    echo '</ul>';
} else {
    // no posts found.
}
/* Restore original Post Data */
wp_reset_postdata();
```

## get_crp_posts()

get_crp_posts() is a wrapper for CRP_Query and can be used to retrieve an array of related posts. It also accepts the same `$args` as CRP_Query.

## Parameters

In addition to the [WP_Query parameters](https://developer.wordpress.org/reference/classes/wp_query/#parameters), CRP_Query also takes these additional parameters.

`$args`

*(array) (Optional)* Arguments to retrieve posts. See `Settings::get_registered_settings()` for all available arguments.

- **‘post_id’**
*(int)* Get related posts for a specific post ID or WP_Post object. If you are using CRP_Query or get_crp_posts() outside the loop, you will need to pass this, or you might get errors when fetching related posts.
- **‘include_cat_ids’**
*(array|string)* An array or comma-separated string of category IDs. This should be the term_taxonomy_id.
- **‘include_post_ids’**
*(array|string)* An array or comma-separated string of post IDs.
- **‘offset’**
*(int)* number of posts to displace or pass over. Setting the offset parameter overrides/ignores the paged parameter, breaking pagination. The `'offset'` parameter is ignored when `'posts_per_page'=>-1` (show all posts) is used.
- **‘weight_recency’** *(Pro only)*
*(int)* Recency boost percentage from 0 to 200. A value of 0 disables recency weighting.
- **‘recency_halflife’** *(Pro only)*
*(int)* Number of days over which half of the available recency boost decays. Values below 1 are clamped to 1. The settings field accepts up to 36,500 days. Values below 7 use hourly age calculations.
- **`relevance_threshold`**
*(int)* Minimum percentage of the strongest eligible match's score that a scored candidate must reach. Values are clamped to 0–100; 0 disables the threshold. If omitted, the saved List Tuning setting is used.

### Minimum relevance threshold

The `relevance_threshold` argument applies a per-query cutoff based on the strongest eligible scored match for that source post. CRP applies it before the final result limit. For example, a value of 80 keeps scored matches with at least 80% of the strongest eligible score.

Use the `crp_relevance_threshold` filter to change the effective threshold. It receives the current percentage and the `CRP_Core_Query` instance, and its return value is clamped to 0–100.

```php
add_filter(
	'crp_relevance_threshold',
	function ( $threshold, $instance ) {
		return 80;
	},
	10,
	2
);
```

Only the setting or query argument is included in the cache key. If the filter returns different thresholds based on request context, disable the relevant CRP caching for those results so a cached list is not reused under a different threshold.

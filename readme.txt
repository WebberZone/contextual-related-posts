=== Contextual Related Posts ===
Tags: related posts, related, contextual related posts, similar posts, seo
Contributors: webberzone, ajay
Donate link: https://wzn.io/donate-crp
Stable tag: 4.4.2
Requires at least: 6.6
Tested up to: 7.1
Requires PHP: 7.4
License: GPLv2 or later

Keep visitors on your site longer with intelligent, fast-loading, contextually related posts. Block, shortcode, custom post type and widget ready.

== Description ==

[Contextual Related Posts](https://wordpress.org/plugins/contextual-related-posts/) is a powerful WordPress plugin that displays fast, intelligent related posts to keep users on your site longer. Improve SEO, increase pageviews, and lower bounce rates — no setup needed.

### Key features

* __Activate and Forget__: Activate the plugin. Contextual Related Posts automatically displays related posts on your site and in your feed after the content. There is no need to edit any template files.
* __Custom Control with Manual Install__: Want placement control? You have multiple options available:
    * __Gutenberg / Block Editor support__: You can easily add a "Related Posts [CRP]" block to any post or page with its options and settings.
    * __Widgets__: Add related posts to any widgetized theme area, such as the sidebar or footer. You can configure the widget options to suit your needs.
    * __Shortcode__: Use `[crp]` to display the related posts anywhere within the post content.
* __REST API__: Fetch related posts for any post ID using `contextual-related-posts/v1/posts/<id>/`. You can also use query parameters to filter or sort the results.
* __The algorithm__: Find related posts based on the current post's title and/or content. You can also find posts by tags, categories and selected custom fields.
* __Caching__: Related posts output is automatically cached as visitors browse through your site, reducing the load on your server and improving performance.

**[View Demo](https://demo.webberzone.com)**

Contextual Related Posts uses an intelligent algorithm that analyzes your post's title and/or content to find the most related ones. This way, you can show your visitors more awesome content and keep them on your site longer.

With Contextual Related Posts, you can quickly boost your site's traffic, reduce bounce rates, and refresh old entries. It's fast, flexible, and easy to use. Try it today and see the difference for yourself!

Additional features include:

* __Exclusions__: Exclude posts from specific categories or tags from being displayed in the related posts list.
* __Custom post types__: The related posts list supports posts, pages, attachments, or any other custom post type on your site.
* __Thumbnail support__: Display thumbnails or not!
* __Styles__: The output of the related posts list is wrapped in CSS classes that allow you to style it easily using custom CSS code.
* __Customizable output__: Display post excerpts in the related posts list. Customize the HTML tags and attributes used to display the output.
* __Extendable code__: Many filters and actions allow developers to easily add features, modify outputs, or integrate with other plugins.

### MySQL FULLTEXT indices

On activation, the plugin creates three MySQL FULLTEXT indices (or indexes), which are leveraged to find the related posts. [Learn more about how the algorithm works](https://webberzone.com/support/knowledgebase/contextual-related-posts-algorithm/).

If you're running a multisite installation, an index is created for each blog upon activation. These indices occupy space in your MySQL database but are essential for running the plugin.

Two options on the settings page allow you to remove these indices when deactivating or deleting the plugin. The latter is true by default.

### 💼 Features Exclusive to CRP Pro

[CRP Pro](https://webberzone.com/plugins/contextual-related-posts/pro/) supercharges your related posts with advanced customization, better performance, and powerful content strategy tools.

#### 🚀 Performance Enhancements

* [Efficient Content Storage and Indexing](https://webberzone.com/support/knowledgebase/efficient-content-storage-and-indexing/): Speed up your site with optimized custom tables and efficient database indices for lightning-fast queries.
* [Cache Setting](https://webberzone.com/support/knowledgebase/caching-in-contextual-related-posts/): Fine-tune your performance with configurable cache times from 1 hour to 1 year.
* [Server Load Threshold](https://webberzone.com/support/knowledgebase/server-load-threshold-setting-in-contextual-related-posts-pro/): Prevent CRP from running queries when the database is under heavy load.
* [Bot Protection](https://webberzone.com/support/knowledgebase/contextual-related-posts-bot-protection/): Skip CRP processing for known bots and crawlers using an extensible signature list, saving server resources.
* __Lazy Loading__: Load related posts via JavaScript only when they are about to enter the viewport, speeding up the initial page load. Works across the content, shortcode, widget and block display methods and plays well with page caching plugins.

#### 🎯 Smarter Content Matching

* [Advanced Algorithm](https://webberzone.com/support/knowledgebase/contextual-related-posts-algorithm/): Control exactly how relevant content is found by adjusting weights for title, content, and excerpt.
* [Taxonomy Weight System](https://webberzone.com/support/knowledgebase/contextual-related-posts-algorithm/#weighting-categories-tags-and-taxonomies): Refine your matches with precise taxonomy weighting for perfect content relationships.
* __Keyword Override for Blocks__: Set a word or phrase on the Related Posts block or the CRP Query Loop block to find related posts using that keyword instead of the current post's title and content.

#### 🧩 Page Builder Integrations (experimental)

These integrations are new in 4.4.0 and marked experimental while they get real-world use. They are safe to use on a live site, but if one of them misbehaves in your setup, please [report it on Github](https://github.com/WebberZone/contextual-related-posts/issues).

* __WPBakery Page Builder__: A native "Related Posts (CRP)" element under its own "WebberZone" tab in the Add Element panel, covering the same options as the `[crp]` shortcode plus custom CSS class/CSS — works in both Classic Mode and the Frontend Editor.
* __Elementor__: A native "Related Posts (CRP)" widget under its own "WebberZone" category, editable live from the widget panel and preview.
* __Bricks Builder__: A native "Related Posts (CRP)" element under its own "WebberZone" category, covering the same options as the `[crp]` shortcode with live dynamic data support.

#### 🛒 WooCommerce Integration

* __Related Products for WooCommerce__: Seamlessly integrate with WooCommerce to show related products.
* __Product Matching & Filtering__: Index SKUs and attributes, filter by stock status, and use category-based recommendations with native WooCommerce styling.
* __Display Customization__: Toggle prices, ratings, and choose to replace or complement WooCommerce's related products.
* __Cart Related Products__: Nudge customers toward free shipping by showing contextually related products priced within the gap between the cart total and the free shipping threshold. Uses CRP relevance matching anchored to the most expensive cart item, with a configurable price band, product count, heading, and cart page hook position.

[📖 WooCommerce Related Products Documentation](https://webberzone.com/support/knowledgebase/woocommerce-related-products/)

### WP-CLI Support

Contextual Related Posts Pro includes comprehensive WP-CLI commands for advanced management and automation. Perfect for developers, agencies, and site administrators who need powerful command-line tools.

**Key WP-CLI Features:**

* **Database Management**: Migrate post meta, check index status, and manage database operations
* **Cache Control**: Clear, warm, enable/disable cache with multisite support
* **Custom Table Operations**: Sync content and manage FULLTEXT indexes for optimal performance
* **Content Processing**: Reprocess posts and manage related content in bulk
* **Multisite Ready**: All commands support `--network` flag for multisite installations

[📖 Complete CLI Documentation](https://webberzone.com/support/knowledgebase/contextual-related-posts-wp-cli/)

#### 🎨 Advanced Design & Display Options

* [Block Editor Integration](https://webberzone.com/support/knowledgebase/contextual-related-posts-blocks/): Create beautiful layouts with the Query Loop Block and ready-to-use block patterns including Grid, Image with Title, and more.
* [Extra Shortcode Parameters](https://webberzone.com/support/knowledgebase/contextual-related-posts-shortcode/): Unlock additional customization options for complete control over your related posts display.

#### 📊 Analytics & Content Strategy Tools

* [Tracking Parameters](https://webberzone.com/support/knowledgebase/tracking-parameters/): Measure content performance with automatic UTM tracking for all related post clicks.
* [Cornerstone Posts](https://webberzone.com/support/knowledgebase/cornerstone-posts-in-contextual-related-posts/): Guide visitors to your most important content by featuring key articles in your related posts lists.
* [Additional Metabox Settings](https://webberzone.com/support/knowledgebase/contextual-related-posts-metabox/): Control related content at the individual post level for perfect content relationships.

### GDPR

Contextual Related Posts doesn’t collect personal data or send information to external services — making it GDPR-friendly by default.

⚠️ You’re responsible for ensuring your site’s overall GDPR compliance.

### Donations

Love Contextual Related Posts? Help keep it alive!

You can [donate](https://wzn.io/donate-crp) or upgrade to [CRP Pro](https://webberzone.com/plugins/contextual-related-posts/pro/) — both help support development and support.

### Contribute

Contextual Related Posts is also available on [Github](https://github.com/WebberZone/contextual-related-posts).
So, if you've got some cool feature you'd like to implement into the plugin or a bug you've been able to fix, consider forking the project and sending me a pull request.

Bug reports are [welcomed on Github](https://github.com/WebberZone/contextual-related-posts/issues). Please note Github is _not_ a support forum, and issues that aren't suitably qualified as bugs will be closed.

### Multilingual sites

Contextual Related Posts works with WPML, Polylang and TranslatePress, and no configuration is needed for any of them.

On WPML and Polylang, where each language has its own posts, the related posts list is mapped to the equivalent post in the language being viewed. On TranslatePress, which translates one set of posts on the fly, related posts pick up the visitor's language along with the rest of the page — including when they are served through the REST API or loaded lazily, which TranslatePress cannot reach on its own.

Cached related posts are stored per language, so visitors are never served another language's titles or links.

### Translations

Contextual Related Posts is available for [translation directly on WordPress.org](https://translate.wordpress.org/projects/wp-plugins/contextual-related-posts). Check out the official [Translator Handbook](https://make.wordpress.org/polyglots/handbook/rosetta/theme-plugin-directories/) to contribute.

### Other Plugins by WebberZone

Contextual Related Posts is one of the many plugins developed by WebberZone. Check out our other plugins:

* [Top 10](https://wordpress.org/plugins/top-10/) - Track daily and total visits to your blog posts and display the popular and trending posts
* [WebberZone Snippetz](https://wordpress.org/plugins/add-to-all/) - The ultimate snippet manager for WordPress to create and manage custom HTML, CSS or JS code snippets
* [Knowledge Base](https://wordpress.org/plugins/knowledgebase/) - Create a knowledge base or FAQ section on your WordPress site
* [Better Search](https://wordpress.org/plugins/better-search/) - Enhance the default WordPress search with contextual results sorted by relevance
* [Auto-Close](https://wordpress.org/plugins/autoclose/) - Automatically close comments, pingbacks and trackbacks and manage revisions
* [Popular Authors](https://wordpress.org/plugins/popular-authors/) - Display popular authors in your WordPress widget
* [Followed Posts](https://wordpress.org/plugins/where-did-they-go-from-here/) - Show a list of related posts based on what your users have read
* [WebberZone Link Warnings](https://wordpress.org/plugins/webberzone-link-warnings/) - Add accessible warnings for external links and target="_blank" links

== Installation ==

### WordPress install (the easy way)

1. Navigate to Plugins within your WordPress Admin Area
2. Click "Add new" and in the search box enter "Contextual Related Posts"
3. Find the plugin in the list (usually the first result) and click "Install Now"

### Manual install

1. Download the plugin
2. Extract the contents of contextual-related-posts.zip to wp-content/plugins/ folder. You should get a folder called contextual-related-posts.
3. Activate the Plugin in WP-Admin under the Plugins screen

== Screenshots ==

1. Related Posts (Rounded Thumbnails)
2. Related Posts (Masonry)
3. Related Posts (Grid)

== Frequently Asked Questions ==

Check out the [FAQ on the plugin page](https://wordpress.org/plugins/contextual-related-posts/faq/) or the [Knowledge Base](https://webberzone.com/support/product/contextual-related-posts/).

If your question isn't listed here, please create a new post at the [WordPress.org support forum](https://wordpress.org/support/plugin/contextual-related-posts).

= How can I customize the output? =

Contextual Related Posts is highly customizable. There are several configurable options on the Settings page, and you can use CSS to customize the outputs. Learn more by reading [this article](https://webberzone.com/support/knowledgebase/customising-the-output-of-contextual-related-posts/).

= Shortcodes =

You can insert the related posts anywhere in your post using the `[crp]` shortcode. View [this article in the knowledge base](https://webberzone.com/support/knowledgebase/contextual-related-posts-shortcode/) for more details.

= How can I report security bugs? =

You can report security bugs through the Patchstack Vulnerability Disclosure Program. The Patchstack team help validate, triage and handle any security vulnerabilities. [Report a security vulnerability.](https://patchstack.com/database/vdp/contextual-related-posts)

= How does the plugin select thumbnails? =

If you enable thumbnails, the plugin will try to find the correct thumbnail in this order:

1. **Post Meta Field**: The image URL specified in the custom field (default is `post-image`). Set this when editing your post.

2. **Featured Image**: The image set as the post's Featured Image.

3. **First Image in Post Content**: The first image found in the post content (if enabled in settings).

4. **First Child Image**: The first image attached to the post.

5. **Video Thumbnail**: If using the Video Thumbnails plugin, its generated thumbnail.

6. **Default Thumbnail**: If enabled in settings, the default thumbnail you specify.

7. **Site Icon**: The site's icon set in Customizer or General Settings.

The plugin also handles SSL, resizing, and fallback mechanisms automatically for each step.

== Changelog ==

= 4.5.0 =

Release date: 15 September 2026

**Added**

* [Pro] Added optional recency weighting for related posts with a configurable boost and half-life, off by default.
* [Pro] Added `post_date_gmt` to the shared index for recency-aware search ranking in Better Search Pro.
* Added the WordPress Abilities API for retrieving related posts. Needs WordPress 6.9 or later; on older versions the abilities are not registered and nothing else changes.
* [Pro] Added abilities to clear the related posts cache and set post exclusions.
* Added `crp_cache_time` and `crp_cache_busting_settings` filters for cache lifetime and invalidation.
* Added a "Minimum relevance (% of best match)" setting under List Tuning > Relevance Matching. For each source post, candidates scoring below that percentage of its strongest match are dropped before the display limit is applied, so a sparse post shows a shorter list instead of one padded with weak matches. Because the bar is relative to each post's own best match, one value works across a site whatever the absolute scores are. Disabled by default (0), so existing sites are unchanged. Manual posts, cornerstone posts and posts matched by "Related posts by meta key" are editorial choices that the relevance query never scores, so they stay in the list regardless; the random fallback (`crp_fill_random_posts`) is skipped while a threshold is on, since padding with unrelated posts is what the threshold exists to prevent. When the query is ordered by relevance the cutoff is applied to the posts already fetched; any other ordering (by date, an explicit `orderby`, or include words) runs one extra query to read the top score and applies the cutoff in SQL, so ordering still returns the right posts rather than the best of a truncated pool. Filterable with `crp_relevance_threshold`. Not available on SQLite, where relevance is a yes/no match.
* Saving a post now clears the cached related posts of the posts it is related to, so a newly published post appears in their lists without waiting for the cache to expire. Filterable with `crp_clear_related_cache_on_save`, `crp_related_cache_clear_limit`, `crp_related_cache_clear_ids`, `crp_related_cache_clear_post_types`, `crp_internal_post_types`, `crp_related_cache_clear_batch_limit`, `crp_deferred_cache_flush_delay` and `crp_is_importing`. Clearing is best effort rather than a full reverse index, so some lists still wait for the cache to expire: it covers the top matches for the saved post, so a post that ranks outside them in another post's list is missed (raise `crp_related_cache_clear_limit` to widen it), and a post that has never been rendered has no record of what it used to match, so editing it to become unrelated leaves the lists that still show it. Only post types in the "Post types to include" setting are treated as able to appear in another post's list, so a shortcode or block using a per-instance `post_types` override that includes an excluded type must add it through the new `crp_related_cache_clear_post_types` filter for those lists to be cleared. Clearing is on by default; `add_filter( 'crp_clear_related_cache_on_save', '__return_false' )` turns it off, for instance on a site that caches nothing.
* TranslatePress support: related posts served through the REST API are now returned in the visitor's language, with language-specific permalinks.
* [Pro] Lazy-loaded related posts are now rendered in the visitor's TranslatePress language.

**Changed**

* Saving more than 20 posts in a single request now schedules one cache flush a few minutes after the request ends, rather than looking up and clearing each post's related posts, so imports and large bulk edits stay fast. Each saved post still has its own cached related posts cleared immediately. Filterable with `crp_related_cache_clear_batch_limit` and `crp_deferred_cache_flush_delay`. Saves made while `WP_IMPORTING` is set schedule the same single flush instead of clearing each post's related posts; importers that do not define that constant can return true from the new `crp_is_importing` filter to get the same treatment.
* Moving a post to Trash now clears the cache of the posts it was related to, whatever the "Clear cache when a post is trashed or restored" setting is. That setting continues to control the full cache flush on its own. Restoring from Trash returns a post to Draft, so its related posts are cleared when it is published again rather than at the moment of restoring.
* `Cache::delete_by_post_id()` now deletes in a single query. It returns a count of cache entries rather than of individual metadata rows, so `wp crp cache clear <id>` reports roughly half its previous number for the same cache.
* Saving one of WordPress' own bookkeeping post types — menu items, templates, reusable blocks and the like — no longer touches the CRP cache at all, so saving a large menu no longer runs a delete per item. The list is filterable with `crp_internal_post_types`.
* The list of related post IDs is now saved on requests that cache the HTML output too, rather than only when "Cache posts only" is on. It is still never read back unless that setting is enabled; saving it is what lets a save clear the cache of posts the saved post is no longer related to. In that HTML-only mode the list follows the HTML cache, so requests that cannot write it — logged-in visitors and password-protected posts — do not write the list either; with "Cache posts only" enabled the list is written for those requests as before.
* The related posts cache key now includes the current language, so multilingual sites rebuild their cached output once after updating.

**Fixed**

* Cached related posts remained stale after ranking settings changed or settings were reset.
* A post's own cached related posts were only cleared when the Contextual Related Posts meta box was submitted with the save. Quick Edit, Bulk Edit, WP-CLI, scheduled publishing, REST clients and sites with the meta box disabled all left it stale until the cache expired. Every save path now clears it.
* Cached related posts output was shared between languages on WPML, Polylang and TranslatePress sites, so visitors could be served another language's titles and links.

= 4.4.2 =

*Release Date - 19 September 2026*

* Fixed:
	* Security: Hardened thumbnail dimension handling to prevent stored Cross-Site Scripting by an Author-level user via block attributes. Reported by Athiwat Tiprasaharn (Jitlada) and Itthidej Aramsri (Boeing777) via Wordfence.

= 4.4.1 =

Release date: 5 September 2026

**Added**

* Features tab and Feature Manager for disabling unused plugin components without changing existing defaults.
* [Pro] Independent controls for the Query Loop, Featured Image, Related Posts Pro, page builder, bot protection, lazy loading, custom tables and WooCommerce modules.

**Security**

* Hardened settings sanitization for users without the `unfiltered_html` capability.
* Hardened Query Loop REST meta filtering and taxonomy searches against unauthorized or private data access.
* Password-protected post excerpts were served through the shared HTML cache.

**Fixed**

* Cache invalidation, dry-run cleanup and persistent object-cache invalidation were incorrect, and ordered IDs and taxonomy slugs could collide.
* [Pro] Custom-table indexing did not run after REST metadata and taxonomy updates, and large term refreshes now run in bounded background batches.
* [Pro] Query Loop block callbacks accumulated across repeated renders.

= 4.4.0 =

Release date: 29 August 2026
Release post: https://webberzone.com/contextual-related-posts-v4-4/

**Added**

* [Pro] WPBakery, Elementor and Bricks Builder integrations (experimental), with a native "Related Posts (CRP)" element in each builder carrying the full set of CRP options.
* [Pro] "Use precomputed taxonomy score" setting, which reads the taxonomy score from the indexed `tax_score` column instead of calculating it per query, at the cost of ignoring per-taxonomy weights in live queries.

**Changed**

* The site-wide "Exclude terms" setting is now applied to the related posts query; it was previously only honored per post in the metabox.
* "Exclude terms" now splits on commas only, so `black friday` is matched as a phrase rather than as two separate words.
* The REST API `limit` parameter is now capped at 100. Use `crp_rest_api_max_limit` to change it.
* Renamed "Include only posts that contain these words" to "Also match posts that contain these words" to match what the option actually does.

**Fixed**

* Plugin data was deleted when uninstalling one version while its paired free or Pro counterpart was active.
* Schema changes did not reach existing installs; `dbDelta()` now runs on version upgrades instead of on activation only.
* The per-request post meta cache was keyed on post ID alone, so the same post ID on two sites of a multisite network shared one cache entry.
* HTML entities survived tag stripping, so `&amp;`, `&nbsp;` and `&hellip;` were indexed as the words "amp", "nbsp" and "hellip".
* Stopword stripping failed when the stopword list contained a `/`.
* "Exclude terms" ignored the post content when content matching was enabled.
* Style stylesheets were always enqueued for the default style instead of the requested one.
* Feed thumbnail size settings were ignored; both width and height must now be greater than 0 for a size to be applied.
* The contextual match SQL was built twice on every query.
* [Pro] The cache collided across differently-configured shortcode, widget, block and builder calls on the same post.
* [Pro] "Order by: Date" was overridden by relevance ordering, and an `Unknown column 'score'` error occurred when contextual matching was disabled with Include words set.
* [Pro] `orderby="relevance"` used the unweighted core match instead of the Pro weighted score.
* [Pro] Taxonomy term-count sorting was applied after the date sort instead of before it.

= Earlier versions =

For the changelog of earlier versions, please refer to the separate changelog.txt file or the [releases page on GitHub](https://github.com/WebberZone/contextual-related-posts/releases).


== Upgrade Notice ==

= 4.5.0 =
Adds the WordPress Abilities API for related posts, optional Pro recency weighting, and refreshes cached related posts when ranking settings change. The abilities need WordPress 6.9 or later; on older versions the rest of the plugin is unaffected.

= 4.4.2 =
Security release. Fixes a stored Cross-Site Scripting vulnerability that could be exploited by users with Author-level access or above. Update immediately.

= 4.4.1 =
Adds the Features tab and Feature Manager for selectively disabling unused plugin components. All features remain enabled by default.

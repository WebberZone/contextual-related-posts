---
slug: contextual-related-posts-features-settings
title: "Contextual Related Posts Settings – Features"
products: [contextual-related-posts]
sections: ["01-crp-getting-started"]
tags: [contextual-related-posts, features, settings]
status: publish
order: 0
toc: true
---

[toc]

The **Features** tab, introduced in [Contextual Related Posts](https://webberzone.com/plugins/contextual-related-posts/) 4.4.1, lets you turn off the parts of the plugin you are not using. When a feature is turned off, its code is never loaded on any request.

Features is the first tab you land on when you open the settings screen. All features are enabled by default, so upgrading changes nothing until you turn something off. A block you disable is unavailable in the block editor and renders empty on the front end.

## Content and display

These settings control the blocks, widget, and page builder integrations that Contextual Related Posts registers.

### Related Posts block

Registers the Related Posts block for the block editor.

### Legacy widget

Registers the classic Related Posts widget. Turn this off if you use blocks or shortcodes instead.

### Query Loop block *(Pro only)*

Registers the Contextual Related Posts Query Loop variation and its block patterns.

### Featured Image block *(Pro only)*

Extends the core Featured Image block with Contextual Related Posts image fallbacks.

### Related Posts Pro block *(Pro only)*

Registers the Related Posts Pro block for the block editor.

### Page builder integrations *(Pro only)*

Registers the Related Posts elements for Elementor, Bricks Builder, and WPBakery Page Builder.

## Optimization and feeds

### Related posts in feeds

Adds related posts to feeds when feeds are selected in the [General settings tab](https://webberzone.com/support/knowledgebase/contextual-related-posts-general-settings/).

### Bot protection module *(Pro only)*

Loads the bot detection module. Turning this on makes the module available; you also need to turn on **Enable bot protection** on the General settings tab to use it.

### Lazy load module *(Pro only)*

Loads the lazy loading module. Turning this on makes the module available; you also need to turn on **Lazy load related posts** on the [Performance tab](https://webberzone.com/support/knowledgebase/contextual-related-posts-performance-settings/) to use it. See [Lazy loading related posts](https://webberzone.com/support/knowledgebase/lazy-loading-related-posts/).

## Integrations and storage

### Custom tables module *(Pro only)*

Loads [Efficient Content Storage and Indexing (ECSI)](https://webberzone.com/support/knowledgebase/efficient-content-storage-and-indexing/) and its related tools. Turning this on makes the module available; you also need to turn on **Use Custom Tables** on the Performance tab to use it.

### WooCommerce integration *(Pro only)*

Loads the [related products and cart-related products](https://webberzone.com/support/knowledgebase/woocommerce-related-products/) integration when WooCommerce is active.

## Module toggles versus behavior settings

Three of the Pro toggles control whether a module is *loaded*, not whether its behavior is *active*. Each one has a matching setting elsewhere that switches the behavior on:

| Features tab toggle | Behavior setting | Where |
| --- | --- | --- |
| Bot protection module | Enable bot protection | General tab |
| Lazy load module | Lazy load related posts | Performance tab |
| Custom tables module | Use Custom Tables | Performance tab |

The WooCommerce toggle has no separate behavior setting — the integration runs whenever WooCommerce is active and the module is loaded.

Leave the module toggle on unless you are certain you will never use the feature. Turning the module off while its behavior setting is on means the behavior stops working.

## How feature gating works

Turning a feature off stops the plugin from loading that feature's PHP classes on the next request. Turning it back on restores the feature and all of its settings immediately. You can change these toggles at any time without losing configuration.

Developers can change which features are toggleable with the `crp_features` filter, or override an individual decision with the `crp_feature_enabled` filter.

```php
// Force the WooCommerce integration off, whatever the setting says.
add_filter(
	'crp_feature_enabled',
	function ( $enabled, $feature ) {
		return 'woocommerce' === $feature ? false : $enabled;
	},
	10,
	2
);
```

## See also

- [Contextual Related Posts Settings – General](https://webberzone.com/support/knowledgebase/contextual-related-posts-general-settings/)
- [Contextual Related Posts Settings – Performance](https://webberzone.com/support/knowledgebase/contextual-related-posts-performance-settings/)

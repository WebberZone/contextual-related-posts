---
slug: page-builder-integrations
title: "Page Builder Integrations for Contextual Related Posts Pro"
products: [contextual-related-posts]
sections: ["02-crp-advanced"]
tags: [contextual-related-posts, pro, bricks, elementor, wpbakery, page-builder]
status: publish
order: 0
toc: true
featured_image: "https://webberzone.com/wp-content/uploads/2026/08/crp-440-elementor-widget.webp"
---

[toc]

[Contextual Related Posts Pro](https://webberzone.com/plugins/contextual-related-posts/pro/) v4.4.0 adds native "Related Posts (CRP)" elements for three popular page builders: **WPBakery Page Builder**, **Elementor**, and **Bricks Builder**. Each integration is *experimental* — the underlying rendering is stable (it reuses the `[[crp]]` shortcode), but the builder-side controls are new and feedback is welcome while they settle in.

> [!NOTE]
> ⓘ These integrations only appear when the corresponding builder plugin/theme is active, and only in Contextual Related Posts Pro. No configuration is needed to enable them — the element registers itself automatically.

## WPBakery Page Builder

If WPBakery Page Builder (`js_composer`) is active, CRP Pro registers a **Related Posts (CRP)** element under its own **WebberZone** tab in the Add Element panel. It works in both Classic Mode and the Frontend Editor.

### Adding the element

1. Edit a post or page with WPBakery Page Builder.
2. Click the **Add Element** (+) button to open the Add Element panel.
3. Select the **WebberZone** tab.
4. Click **Related Posts (CRP)** to insert the element.

![Add Element panel with the WebberZone tab selected, showing the Related Posts (CRP) element](https://webberzone.com/wp-content/uploads/2026/08/crp-440-wpbakery-add-element.webp)

### Configuring the element

The element's settings panel is organized into tabs: **General**, **Query**, **Display**, **Advanced**, and **Design Options**. The first four map to the [shared controls](#shared-controls) below. **Design Options** is WPBakery's standard tab for every element and holds the **Extra class name** and **Custom CSS** editor controls.

![Related Posts (CRP) element settings dialog, Query tab, showing Post types, Order by, Randomize order, Include words, Exclude post IDs, and Exclude category slugs](https://webberzone.com/wp-content/uploads/2026/08/crp-440-wpbakery-element.webp)

## Elementor

If Elementor is active, CRP Pro registers a **Related Posts (CRP)** widget under its own **WebberZone** category in the widget panel. Settings apply live in the editor preview.

### Adding the widget

1. Edit a page with Elementor.
2. Open the widget panel and search for "Related Posts", or browse to the **WebberZone** category.
3. Drag the **Related Posts (CRP)** widget onto the page.

### Configuring the widget

The widget's **Content** tab contains the **General**, **Query**, **Display**, and **Advanced** sections described in [shared controls](#shared-controls) below. Elementor's own **Advanced** tab at the top of the panel (margins, motion effects, custom CSS classes) is separate from CRP's **Advanced** section inside **Content** — don't confuse the two when looking for the **Other attributes** control.

![Elementor editor with the Related Posts (CRP) widget selected, General section expanded, and the rendered preview on the canvas](https://webberzone.com/wp-content/uploads/2026/08/crp-440-elementor-widget.webp)

## Bricks Builder

If the Bricks theme is active, CRP Pro registers a **Related Posts (CRP)** element under its own **WebberZone** category, with live dynamic data support.

### Adding the element

1. Edit a page with the Bricks builder.
2. Open the Elements panel and search for "Related Posts", or browse to the **WebberZone** category.
3. Drag the **Related Posts (CRP)** element onto the canvas.

### Configuring the element

The element's **Content** tab exposes the same **General**, **Query**, **Display**, and **Advanced** sections as the other two builders — see [shared controls](#shared-controls) below. The **Style** tab holds Bricks' own styling controls, separate from CRP's own **Style** (preset) dropdown inside **Display**.

The **Title** field supports Bricks dynamic tags — click the lightning-bolt icon to insert one, e.g. `More like {post_title}`.

![Bricks editor with the Related Posts (CRP) element selected, Title field set to a dynamic tag, and the rendered preview](https://webberzone.com/wp-content/uploads/2026/08/crp-440-bricks-element.webp)

## Shared controls

All three elements expose the same set of controls, grouped the same way:

**General**

- **Title** — heading text shown above the list.
- **Show heading** — toggle the heading on or off.
- **Number of posts** — maximum related posts to display.
- **Offset** — number of posts to skip from the top.

**Query**

- **Post types** — comma-separated list of post types to include.
- **Order by** — relevance, random, or date.
- **Randomize order** — shuffle the results.
- **Include words** — only match posts containing these words.
- **Exclude post IDs** — comma-separated post/page IDs to exclude.
- **Exclude category slugs** — comma-separated category/taxonomy term slugs to exclude.
- **Restrict to taxonomy term IDs** — limit display to specific `term_taxonomy_id` values.

**Display**

- **Style** — one of the plugin's built-in styles (same list as the [Styles tab](https://webberzone.com/support/knowledgebase/contextual-related-posts-styles-settings/)).
- **Thumbnail placement** — before title, after title, thumbnail only, or text only.
- **Thumbnail size** — an existing WordPress image size or a custom size name.
- **Show excerpt** and **Excerpt length (words)**.
- **Show author**.
- **Show date**.

**Advanced**

- **Other attributes** — a free-text field accepting arbitrary `[[crp]]` shortcode attributes as `attribute="value"` pairs, for options not exposed as a dedicated control.
- WPBakery additionally exposes **Extra class name** and a **Custom CSS** editor, under its **Design Options** tab rather than **Advanced**.

## How controls fall back to your saved settings

Every control left blank (or, for a checkbox/switcher, left unchecked as "off") falls back to the plugin's saved [List tuning](https://webberzone.com/support/knowledgebase/contextual-related-posts-list-tuning-settings/), [Output](https://webberzone.com/support/knowledgebase/contextual-related-posts-output-settings/), and [Styles](https://webberzone.com/support/knowledgebase/contextual-related-posts-styles-settings/) settings — the same way an omitted `[[crp]]` shortcode attribute does. Only an explicit value typed into a control overrides the saved setting for that element instance.

## Styling in theme builder templates

If you place a Related Posts element inside a template used by a theme builder — a single/archive template, a header/footer, a Bricks popup, or a WPBakery template — CRP automatically enqueues the correct style CSS for that element at render time, even though the template itself isn't the post being viewed.

## Troubleshooting

- **The element doesn't appear in the panel.** Confirm the relevant builder plugin/theme is active, and that Contextual Related Posts Pro (not the free version) is active.
- **A setting doesn't seem to apply.** Check whether the control was left blank/unchecked — it will use your saved plugin settings instead. Set an explicit value on the element to override it.
- **No related posts are shown.** See [Troubleshooting Related Posts do not display](https://webberzone.com/support/knowledgebase/related-posts-do-not-display/) — the same query and settings apply regardless of how the element is inserted.

## See also

- [Contextual Related Posts shortcode](https://webberzone.com/support/knowledgebase/contextual-related-posts-shortcode/)
- [Contextual Related Posts Blocks](https://webberzone.com/support/knowledgebase/contextual-related-posts-blocks/)
- [Contextual Related Posts Settings – List tuning](https://webberzone.com/support/knowledgebase/contextual-related-posts-list-tuning-settings/)

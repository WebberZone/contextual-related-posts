---
slug: contextual-related-posts-blocks
title: "Contextual Related Posts Blocks"
products: [contextual-related-posts]
sections: ["02-crp-advanced"]
tags: [block, contextual-related-posts, query-loop, related-posts]
status: publish
order: 0
toc: true
featured_image: "https://webberzone.com/wp-content/uploads/2024/05/Choose-a-Related-Posts-Pattern-scaled.webp"
---

[toc]

[Contextual Related Posts](https://webberzone.com/plugins/contextual-related-posts/) includes a Related Posts block that can replace the widget or shortcode. You can use it in posts, pages, custom post types, and block theme templates.

[Contextual Related Posts Pro](https://webberzone.com/plugins/contextual-related-posts/pro/) adds two more choices: a Contextual Related Posts variation of the core Query Loop and a standalone Related Posts Advanced block. Both include patterns. The Advanced block has its own query controls and editable card template.

## Adding the Blocks

To add a Contextual Related Posts block, click the plus (+) icon in the block editor and search for “Related Posts.” Insert the **Related Posts** block for the basic block. Pro users can also insert **Contextual Related Posts Query Loop** or **Related Posts — Advanced**.

![Insert Contextual Related Posts block](https://webberzone.com/wp-content/uploads/2024/05/Insert-Contextual-Related-Posts-block.webp)

## Configuring the basic Gutenberg Block

The Related Posts block lets you preview the related posts directly in the block editor. You can customize various aspects of the block using the sidebar as follows:

| Setting | Type | Description |
| --- | --- | --- |
| Show Heading | Toggle (ON/OFF) | This displays a heading before the Related Posts. If you toggle this on, you can then modify the text of the heading. Default is Related Posts. |
| Number of Posts | Number | The maximum number of related posts that will be displayed by the plugin. |
| Offset | Number | Number of posts to skip from the top. |
| Show excerpt | Toggle (ON/OFF) | Displays the excerpt of each related post. By default, Contextual Related Posts will use the post excerpt that is manually created. If no post excerpt is found, the plugin will generate the excerpt from the post content based on the excerpt length set in the global Output settings panel. |
| Show author | Toggle (ON/OFF) | Displays the author for each related post. The author’s name is preceded with the text “by”. e.g. by Doctor Watson. |
| Show date | Toggle (ON/OFF) | Displays the published date of each related post. |
| Thumbnail option | Dropdown | This provides four self-explanatory options. “Before title”, “After title”, “Only thumbnail, “Only text”. |
| Order posts | Radio selector | Choose between ordering the posts by relevance, randomly or by date. This option directly modifies the related posts SQL query. Selecting random above will randomize the posts that are fetched from the database. This option will not take effect if you Cache the HTML output on the settings page. |
| Randomize posts | Toggle (ON/OFF) | Shuffle the related posts on each page load. This option will not take effect if you Cache the HTML output on the settings page. |
| Other attributes | Textarea field | Enter other attributes in a URL-style string-query. It supports any of the plugin’s global settings, e.g. post_types=post,page&link_nofollow=1&exclude_post_ids=5,6. |

### Pro Settings for the Related Posts block

Contextual Related Posts Pro users will see an additional section in the block settings sidebar that allows them to save the existing block settings as default or clear the defaults.

Pro users also get a **Keyword** field in the block sidebar. Enter a word or phrase here and the plugin uses it instead of the current post's title and content to find related posts — the same behavior as the Keyword field in the post-edit metabox.

## Using the Contextual Related Posts Query Loop Block

The Contextual Related Posts Query Loop is a Pro variation of WordPress’s core Query Loop. It lets you use the core Query Loop structure with Contextual Related Posts query settings and related-post patterns.

### 1. Configuring the Query Loop Block

The Query Loop block allows you to customize the query that will be used to retrieve the related posts. You can configure the following settings:

- **Number of Posts**: Enter the number of posts to display per page.
- **Post Types**: Select one or multiple post types to include in the related posts.
- **Offset**: Set the number of posts to skip from the beginning of the results.
- **Order By**: Choose how the results should be sorted (e.g., relevance, date, title, author, etc.).
- **Order**: Toggle between Ascending and Descending.
- **Filters – Taxonomy**: Filter the results by specific taxonomies (e.g., category, tag).
- **Filters – Authors**: Filter the results by specific authors.
- **Keyword**: Enter a word or phrase to find related posts using that keyword instead of the current post's title and content.
- **Post Meta Query**: Filter the results by one or more custom field values. See below.

#### Post Meta Query

The **Post Meta Query** panel filters related posts by custom field values. Each row has three parts:

- **Meta Key** — chosen from the meta keys registered for the selected post type, including Advanced Custom Fields keys.
- **Meta Value** — the value to compare against.
- **Meta Compare** — the comparison operator: `=`, `!=`, `>`, `>=`, `<`, `<=`, `LIKE`, `NOT LIKE`, `IN`, `NOT IN`, `BETWEEN`, `NOT BETWEEN`, `EXISTS`, `NOT EXISTS`, `REGEXP`, `NOT REGEXP`, and `RLIKE`.

Add more rows to build a compound filter. With two or more rows, a **Query Relationship** selector appears so you can join them with `AND` or `OR`. Changing the post type clears the meta query, since the available keys differ per post type.

The preview inside the block editor is generated over the REST API and applies the same permission rules WordPress applies elsewhere. For anyone who is not an administrator, the preview only filters on meta keys that are registered, exposed to the REST API, not protected (keys beginning with an underscore), and editable by that user on the source post. Keys that fail those checks are dropped from the preview query. Administrators see the preview unfiltered. Front-end rendering is unaffected — this applies to the editor preview only.

### 2. Customize the Layout

The Query Loop variation includes six patterns: **Related Posts Grid**, **Related Posts in a Thumbnail Grid**, **Related Posts: Image, Title, Excerpt**, **Related Posts: Left Thumbnails**, **Rounded Thumbs**, and an unordered list of titles. Select the Query Loop variation, then use its pattern or Replace control to choose a layout.

To select a pattern, you can select the block using the navigation bar at the bottom left of the editor or the Parent block in the top/hover toolbar.

![Block Editor Toolbar with the Replace button](https://webberzone.com/wp-content/uploads/2024/05/Block-Editor-Toolbar-with-the-Replace-button-1024x114.webp)

Once you do so, you’ll see the “Replace” button, allowing you to select from the different patterns.

![Choose a Related Posts Pattern](https://webberzone.com/wp-content/uploads/2024/05/Choose-a-Related-Posts-Pattern-1024x527.webp)

### 3. Add Additional Blocks

Within the Core Query Loop block, you can add additional blocks to display specific content for each post or page, such as:

- **Post Title**: Show the title of the post or page.
- **Post Content**: Display the full content of the post or page.
- **Post Date**: Show the date the post or page was published.
- **Post Featured Image**: Display the featured image of the post or page.

You can arrange and style these blocks to create a visually appealing and informative layout for your content.

## Contextual Related Posts Featured Image Block (Pro version)

Contextual Related Posts Pro offers enhanced flexibility and reliability for displaying featured images in your posts. This can be used for the related posts list and across your WordPress site that uses the Block or the Site editor.

If a featured image is not explicitly set for a post, the plugin will automatically fall back to the following configurable options:

1. **Custom Image**: Select an image from the Media Library as the default featured image.
2. **First Image in the Post Content:** If the post contains images, the first image encountered will be used as the featured image.
3. **Meta Key:** If a specific meta key is defined, the value associated with that key will be used as the featured image URL. The meta key needs to contain the full URL of the image to be used.
4. **Default Image:** The default image can be specified if no image is found using the above methods.
5. **Site Icon**: Use the site icon configured in Settings > General.

This feature ensures that your popular posts always have visually appealing featured images, even if a featured image hasn’t been set.

## Using the standalone Related Posts Advanced block *(Pro only)*

The Advanced block is a separate block from the Query Loop variation. Choose it when you want a related-posts query with a card template you can edit directly. You can insert it in post content or a block theme template, including a template part used for a sidebar or footer.

### Choose a pattern

When you insert an empty Advanced block, choose one of its six starting patterns:

- **Text list** — linked titles in a simple list.
- **Small image beside title** — a compact image and title layout.
- **Image above title** — a card grid with an image above each title.
- **Image, title and excerpt** — cards with an image, title, date, and excerpt.
- **Horizontal image and text** — an image beside the title, date, and excerpt.
- **Rounded thumbnails** — round images beside linked titles.

Use **Change pattern** in the block toolbar or Layout controls to replace the card layout. The block keeps its source, query settings, heading, and empty-state content. The change can be undone in the editor.

### Settings and Styles tabs

The block’s inspector separates its controls into the **Settings** and **Styles** tabs.

In **Settings**, choose whether results relate to the current post or a specific post. You can set a keyword override, number of posts, offset, order, post types, authors, included or excluded posts, taxonomy filters, and custom-field conditions. You can also choose whether to hide the section or show an empty-state message when no related posts are found.

In **Styles**, set the maximum number of columns, minimum card width, and gap between cards. The columns range from 1 to 6, card width from 120 to 600 pixels, and gap from 0 to 100 pixels.

### Edit the repeated card

The **Related Posts Template** block contains one editable card layout that repeats for every result. Add or arrange **Related Post Title**, **Related Post Image**, **Related Post Date**, and **Related Post Excerpt** blocks, along with supported core blocks such as Groups, Columns, Headings, Paragraphs, Separators, and Spacers. Changes to the template update every card.

The Related Post Image block uses the image fallback settings from Contextual Related Posts. You can use a first image in the post, an image stored in a meta field, a selected custom image, the configured default image, or the site icon.

### Choose the source and preview

The block uses the current post when it has a post context, such as in a single post or a Query Loop item. Choose **Specific post** to keep a fixed source post, which can be useful in a sidebar or footer that has no current-post context. In the editor, **Preview with post** lets you preview results when the editor has no source post.

The live editor preview uses the Contextual Related Posts REST API. Keep **REST API endpoints** enabled on the [Features settings tab](https://webberzone.com/support/knowledgebase/contextual-related-posts-features-settings/) to load related results in the editor. If the API is disabled, the block remains editable but shows a design preview instead of live results.

### Convert a compatible Query Loop

WordPress can transform a compatible Contextual Related Posts Query Loop variation into the Advanced block. The transform carries supported query settings and the post-template contents into the new block. It applies only to the Contextual Related Posts variation, not to every core Query Loop or every older Related Posts block. Pagination, unsupported inner blocks, and unsupported query settings prevent conversion.

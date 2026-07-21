---
slug: contextual-related-posts-thumbnail-settings
title: "Contextual Related Posts Settings – Thumbnail"
products: [contextual-related-posts]
sections: ["01-crp-getting-started"]
tags: [contextual-related-posts, settings]
status: publish
order: 0
---

[kbtoc]

The **Thumbnail options** section in [Contextual Related Posts](https://webberzone.com/plugins/contextual-related-posts/) contains a set of options that allow you to fine-tune the thumbnails displayed in the related posts. These are global settings, and many of these can be overridden in the widget or the shortcode.

## Location of the post thumbnail

Choose where the thumbnail is displayed in relation to the post:

- Display thumbnails inline with posts, before the title.
- Display thumbnails inline with posts, after the title.
- Display only thumbnails, no text.
- Do not display thumbnails, only text.

> [!NOTE]
> ⓘ This setting cannot be changed as the Thumbnail style is set to Rounded Thumbnails or Rounded Thumbnails with Grid. You can change the style in the Styles tab.

## Thumbnail size

Select from existing image sizes or create a custom size. If using a custom size, enter the width, height, and crop settings below. For best results, use a cropped image. Changing the width or height will not automatically resize existing images; you will need to regenerate them using a plugin or WP CLI.

## Thumbnail width

Set the width of the thumbnail (in pixels).

## Thumbnail height

Set the height of the thumbnail (in pixels).

## Hard crop thumbnails

Check this box to hard crop the thumbnails, forcing the width and height above instead of maintaining proportions.

## Generate thumbnail sizes

If enabled and a custom size is selected above, the plugin will register the image size with WordPress to create new thumbnails. Does not update old images.

## Thumbnail size attributes

Choose how to set the width and height of the thumbnail:

- Use CSS to set the width and height (e.g. `style="max-width:250px;max-height:250px"`)
- Use HTML attributes to set the width and height (e.g. `width="250" height="250"`)
- No width or height set (use external styles as needed)

## Thumbnail meta field name

The value of this field should contain the URL of the image and can be set in the metabox in the Edit Post screen.

## ACF field name *(Pro only)*

If you use [Advanced Custom Fields (ACF)](https://www.advancedcustomfields.com/), enter the ACF field name that contains the thumbnail image. Supports Image Array, Image ID, Image URL, and Text field return formats. This is checked after the standard thumbnail meta field above.

## Get the first image

If enabled, the plugin will fetch the first image in the post content. This can slow down page loading if the image is large.

## Use default thumbnail?

If checked, when no thumbnail is found, a default one from the URL below will be shown. If not checked and no thumbnail is found, no image will be displayed.

## Default thumbnail

Enter the full URL of the image to display if no thumbnail is found. This image will be displayed as the fallback.

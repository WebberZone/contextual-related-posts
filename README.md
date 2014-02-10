# Contextual Related Posts

Licensed under GPLv2 or later

Display related posts on your WordPress blog and feed. Supports thumbnails, shortcodes, widgets and custom post types!

## Description

<a href="http://ajaydsouza.com/wordpress/plugins/contextual-related-posts/">Contextual Related Posts</a> is a powerful plugin for WordPress that allows you to display a list of related posts on your website and in your feed. 

The list is based on the content of the title and/or content of the posts which makes them more relevant and more likely to be of interest to your readers. This allows you to retain visitors, reduce bounce rates and refresh old entries.

Contextual Related Posts is one of the most feature rich related posts plugins for WordPress with support for thumbnails, shortcodes, widgets, custom post types, caching and CSS styles.

### Key features

* **Automatic**: CRP will start displaying related posts on your site and feed automatically after the content when you activate the plugin. No need to edit template files
* **Manual install**: Want more control over placement? Check the <a href="http://wordpress.org/extend/plugins/contextual-related-posts/faq/">FAQ</a> on which functions are available for manual install.
* **Widgets**: Add related posts to widgetized area of your theme. Lots of options available
* **Shortcode**: Use `[crp]` to display the posts anywhere you want in the post content
* **The algorithm**: Find related posts by title and/or content of the current post
* **Caching**: Related posts output is automatically cached as visitors browse through your site
* **Exclusions**: Exclude posts from categories from being displayed in the list. Or you can exclude posts or pages by ID
* **Custom post types**: The related posts list lets you include posts, pages, attachments or any other custom post type!
* **Thumbnail support**:
	* Support for WordPress post thumbnails
	* Auto-extract the first image in your post to be displayed as a thumbnail
	* Manually enter the URL of the thumbnail via <a href="http://codex.wordpress.org/Custom_Fields">WordPress meta fields</a>
	* Use timthumb to resize images or use your own filter function to resize post images
* **Styles**: The output is wrapped in CSS classes which allows you to easily style the list. You can enter your custom CSS styles from within WordPress Admin area
* **Customisable output**:
	* Display excerpts in post. You can select the length of the excerpt in words
	* Customise which HTML tags to use for displaying the output in case you don't prefer the default `list` format

### Donations

I spend a significant amount of my free time maintaing, updating and more importantly supporting this plugin. Those who have sought support in the support forums know that I have done by best to answer your question and solve your problem.
If you have been using this plugin and find this useful, do consider making a donation. This helps me pay for my hosting and domains.


## Installation

### WordPress install
1. Navigate to Plugins within your WordPress Admin Area

2. Click "Add new" and in the search box enter "Contextual Related Posts" and select "Keyword" from the dropdown

3. Find the plugin in the list (usually the first result) and click "Install Now"

### Manual install
1. Download the plugin

2. Extract the contents of contextual-related-posts.zip to wp-content/plugins/ folder. You should get a folder called contextual-related-posts.

3. Activate the Plugin in WP-Admin. 

4. Goto **Settings &raquo; Related Posts** to configure

5. Optionally visit the **Custom Styles** tab to add any custom CSS styles. These are added to `wp_head` on the pages where the posts are displayed


## Screenshots
<img src="https://github.com/ajaydsouza/contextual-related-posts/blob/master/screenshot-1.png" />

## Frequently Asked Questions

If your question isn't listed here, please post a comment at the <a href="http://wordpress.org/support/plugin/contextual-related-posts">WordPress.org support forum</a>. I monitor the forums on an ongoing basis. If you're looking for more advanced support, please see <a href="http://ajaydsouza.com/support/">details here</a>.

### How can I customise the output?

Several customization options are available via the Settings page in WordPress Admin. You can access this via <strong>Settings &raquo; Related Posts</strong>

The plugin also provides you with a set of CSS classes that allow you to style your posts by adding code to the *style.css* sheet. In a future version, I will be adding in CSS support within the plugins Settings page.

The following CSS classes / IDs are available:

* **crp_related**: ID of the main wrapper `div`. This is only displayed on singular pages, i.e. post, page and attachment

* **crp_related**: Class of the main wrapper `div`. If you are displaying the related posts on non-singular pages, then you should style this

* **crp_title**: Class of the `span` tag for title of the post

* **crp_excerpt**: Class of the `span` tag for excerpt (if included)

* **crp_thumb**: Class of the post thumbnail `img` tag

For more information, please visit http://ajaydsouza.com/wordpress/plugins/contextual-related-posts/

### How does the plugin select thumbnails?

The plugin selects thumbnails in the following order:

1. Post Thumbnail image: The image that you can set while editing your post in WordPress &raquo; New Post screen

2. Post meta field: This is the meta field value you can use when editing your post. The default is `post-image`

3. First image in the post: The plugin will try to fetch the first image in the post

3. Video Thumbnails: Meta field set by <a href="https://wordpress.org/extend/plugins/video-thumbnails/">Video Thumbnails</a>

4. Default Thumbnail: If enabled, it will use the default thumbnail that you specify in the Settings screen

The plugin uses <a href="http://www.binarymoon.co.uk/projects/timthumb/">timthumb</a> to generate thumbnails by default. Depending on the configuration of your webhost you might run into certain problems. Please check out <a href="http://www.binarymoon.co.uk/2010/11/timthumb-hints-tips/">the timthumb troubleshooting page</a> regarding permission settings for the folder and files.

### Manual install

The following functions are available in case you wish to do a manual install of the posts by editing the theme files.

**echo_ald_crp( $args = array() )**

Echoes the list of posts wherever you add the this function. You can also use this function to display related posts on any type of page generated by WordPress including homepage and archive pages.

Usage: `<?php if(function_exists('echo_ald_crp')) echo_ald_crp(); ?>` to your template file where you want the related posts to be displayed.


**get_crp_posts()**

Takes a post ID and returns an array of related post IDs. 

Usage: `<?php if(function_exists('get_crp_posts')) get_crp_posts($postid, $limit) ?>`

Parameters:

*$postid* : The ID of the post you'd like to fetch. By default the current post is fetched. Use within the Loop for best results.

*$limit* : Maximum number of posts to return. The actual number displayed may be lower depending on the matching algorithm and the category / post exclusion settings.

### Shortcodes

You can insert the related posts anywhere in your post using the `[crp]` shortcode. The plugin takes three optional attributes `limit`, `heading` and `cache` as follows:

`[crp limit="5" heading="1" cache="1"]`

*limit* : Maximum number of posts to return. The actual number displayed may be lower depending on the matching algorithm and the category / post exclusion settings.

*heading* : By default, the heading you specify in **Title of related posts:** under **Output options** will be displayed. You can override this by specifying your own heading e.g.

`
<h3>Similar posts</h3>
[crp limit="2" heading="0"] 
`
*cache* : Cache the output or not? By default the output will be cached for the post you add the shortcode in. You can override this by specifying `cache=0`

### Filters

The plugin includes the following filters that allows you to customise the output for several section using <a href="http://codex.wordpress.org/Function_Reference/add_filter">add_filter</a>.

*crp_heading_title* : Filter for heading title of the posts. This is the text that you enter under *Output options > Title of related posts*

*crp_title* : Filter for the post title for each of the related posts

I'll be adding more filters eventually. If you are looking for any particular filter do raise a post in the <a href="http://wordpress.org/support/plugin/contextual-related-posts">support forum</a> requesting the same.

## Wishlist

Below are a few features that I plan on implementing in future versions of the plugin. However, there is no fixed time-frame for this and largely depends on how much time I can contribute to development.

* Select random posts if there are no similar posts
* Exclude display on select categories and tags
* Restrict related posts to same category
* Better relevance tweaking
* Limit characters in content that is compared
* Improved Custom post support
* Multi-site support
* Ready-made styles
* Upload your own default thumbnail
    

If you would like a feature to be added, or if you already have the code for the feature, you can let me know by <a href="http://wordpress.org/support/plugin/contextual-related-posts">posting in this forum</a> or fork this Github project and let me know.

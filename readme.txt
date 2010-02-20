=== Contextual Related Posts ===
Tags: related posts, similar posts
Contributors: Ajay, Mark Ghosh
Donate link: http://ajaydsouza.com/donate/
Stable tag: trunk
Requires at least: 2.5
Tested up to: 2.9.2


Show user defined number of contextually related posts

== Description ==

Display a list of contextually related posts for the current post. 

You can select the number of posts to display and if you want to automatically display the related posts in your content / feed.

Now, you can choose to exclude posts from certain categories as well as exclude pages.


= Features =

* Display Related Posts automatically in content / feed, no need to edit template files 
* Doesn't require the post to be tagged in order to display related posts 
* You can manually add code to your template where you want the related posts to be displayed 
* Exclude posts from categories 
* Exclude display of related posts on Pages 
* Exclude links pages in Related Posts list 
* Find related posts based on content and post title 
* Option to display post thumbnails. WordPress 2.9 thumbnails need to be activated in your themes "functions.php"
* Display excerpts in post. You can customize the length of the excerpt

== Changelog ==

= 1.6.3 =
* Fixed: The plugin will now display a list of changes in the WordPress Admin > Plugins area whenever an update is available

= 1.6.2 =
* Turned the credit option to false by default. This setting won't effect current users.
* Turned off borders on post thumbnails. You can customise the CSS class "crp_thumb" to style the post thumbnail
* From the next version, the plugin will display update information in your WP-Admin

= 1.6.1 =
* Fixed: Custom output was not detecting styles properly
* Fixed: Incorrect XHTML code was being generated when using special characters in the post title

= 1.6 =
* New: The plugin extracts the first image in the post and displays that if the post thumbnail and the post-image meta field is missing
* New: Display excerpts in the list
* New: Credit link to the CRP page added. You can choose to turn this off, though I would appreciate if you leave it on.
* Updated: All parts of the list are now wrapped in classes for easy CSS customisation

= 1.5.2 =
* Fixed: Fixed display of post thumbnails using postmeta field

= 1.5.1 =
* Fixed: Numeric options were not being saved correctly

= 1.5 =
* Added an Option to display post thumbnails
* The output can be completely customised now

= 1.4.2 =
* Fixed: Using doublequotes in the title would mess up the screen
* Fixed: Errors when the mySQL index was being regenerated

= 1.4.1 =
* Added Italian and Danish languages
* Minor fix for text in the admin page

= 1.4 =
* Added complete localization support
* Added button to recreate the mySQL FULLTEXT index

= 1.3.1 =
* Fixed bug that didn't blank out posts even when option was selected

= 1.3 =
* Better optimization in WP-Admin area. 
* Fixed compatibility problems with Simple Tags plugin
* Fixed large number of queries being generated

= 1.2.2 =
* Minor bug fixed about limit of posts

= 1.2.1 =
* Bug fixed to support PHP4

= 1.2 =
* Option to blank output in case nothing is found
* Exclude posts from certain categories
* Exclude pages
* Option to choose if you want related posts to be displayed on pages

= 1.1.1 =
* Now you can optionally choose if you want to use the post content to search for related posts

= 1.1 =
* Fixed MySQL index key conflicts by using a more unique index key name.

= 1.0.1 =
* Release

== Upgrade Notice ==

= 1.6.3 =
* Turned the credit option to false by default. This setting won't effect current users.
* Turned off borders on post thumbnails. You can customise the CSS class "crp_thumb" to style the post thumbnail.
* The plugin will now display a list of changes in the WordPress Admin > Plugins area whenever an update is available

== Installation ==

1. Download the plugin

2. Extract the contents of contextual-related-posts.zip to wp-content/plugins/ folder. You should get a folder called contextual-related-posts.

3. Activate the Plugin in WP-Admin. 

4. Goto Settings > Related Posts to configure


== Frequently Asked Questions ==

= What are the requirements for this plugin? =

WordPress 2.5 or above


= Can I customize what is displayed? =

All options can be customized within the Options page in WP-Admin itself

The plugin uses the css class `crp_related` in the `div` that surrounds the list items. So, if you are interested, you can add code to your *style.css* file of your theme to style the related posts list.

For more information, please visit http://ajaydsouza.com/wordpress/plugins/contextual-related-posts/

= Support =

All questions need to be redirected at the Support Forum at http://ajaydsouza.org/

No support questions will be entertained in the comments or via email.

=== Cherry Team Members ===

Contributors: TemplateMonster 2002
Tags: custom post type, team, cherry-framework
Requires at least: 4.5
Tested up to: 4.6.1
Stable tag: 1.0.4
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Cherry Team Members plugin allows you to showcase your team and personnel.

== Description ==

The plugin is specially designed to make it easier for the businesses to display info about their team and personnel. It contains a full set of options and tools that will help adjust the profile in accordance with the skills and the position of the members.

== Installation ==

1. Upload "Cherry Team Members" folder to the "/wp-content/plugins/" directory
2. Activate the plugin through the "Plugins" menu in WordPress
3. Navigate to the "Cherry Team Members" page available through the left menu

== Screenshots ==
1. Settings page.
2. Post edit page.

== Configuration ==

= Plugin Options =
All plugin options are gathered in Team -> Settings

* Select team archive page - Choose the archive page for Team posts.
* Set posts number per archive page - Set the number of posts to display on the archive page and on the Team category pages. This option is not included into the shortcode.
* Select archive page columns number - Number of columns for the posts on the archive page and Team  category pages.  This option is not included into the shortcode (4 max).
* Select template for single team member page - Choose a proper template for a single Team member page.
* Select image size for single team member page - Choose a featured image size for a single team member page. In the dropdown menu you can choose from all available sizes. It is strongly recommended to use the Regenerate Thumbnails plugin before changing this option.
* Select template for team listing page - Choose a proper template for displaying Team posts items. (Works for archives page and category pages).
* Select image size for listing team member page - Choose featured image size for items in Team posts listing type. (Works for archives page and category pages). In the dropdown menu you can choose from all available sizes. It is strongly recommended to use the Regenerate Thumbnails plugin before changing this option.


= Shortcode =
Shortcode is used to display the posts list with set parameters. Shortcode attributes:

* super_title (default = '') - Additional block title with the posts list. Displayed above the major title.
* title (default = '') - Main block title with the posts list.
* subtitle (default = '') - Additional block title with the posts list. Displayed under the major title.
* columns (default = 3) - Number of columns for desktop (6 col - max).
* columns_tablet (default = 2) - Number of columns for tablet.
* columns_phone (default = 1) -  Number of columns for phones
* posts_per_page (default = 6) - Number of posts per page.
* group (default = '') - Choose posts from a certain group. If you need to render more than one group, the slugs are divided by commas.
* id (default = '') - Show posts at a certain ID.
* more (default = true) - Show or hide More button under the posts list in shortcode.
* more_text (default = 'More') - More button text.
* more_url (default = '#') - More button URL.
* ajax_more (default = true) - Use More button as AJAX load more button
* pagination (default = false) - Show/hide pagination.
* show_filters (default = false) - Show/hide group AJAX filter before products list in shortcode.
* show_name (default = true) - Show/hide person's name in the list.
* show_photo (default = true) - Show/hide photo (featured image ).
* show_desc (default = true) - Show/hide short description.
* excerpt_length (default = 20) - Max word length in short description.
* show_position (default = true) - Show/hide position.
* show_social (default = true) - Show/hide list of social links.
* image_size (default = 'thumbnail') - Choose the size of the image displayed in the posts list.
* template (default = 'default') - Choose posts list display template.
* use_space (default = true) - Add 30px horizontal spaces between the columns.
* use_rows_space (default = true) - Add vertical spaces between items in posts lists.


= Templates =
The plugin offers simplified templating system for .tmpl files. 3 templates are available by default:

* default.tmpl - Main template for displaying posts on the archive page and in the shortcode.
* grid-boxes.tmpl - Alternative template for displaying posts on the archive page and in the shortcode.
* single.tmpl - Single post page template.

Standard templates can be rewritten in the theme. For that you need to create cherry-team folder in the root catalog of the theme and copy the necessary templates in there. You can also add your own templates. For that you need to create a file with .tmpl extension in the same folder.

== Changelog ==

= 1.0.0 =

* Initial release

= 1.0.1 =

* UPD: framework version

= 1.0.3 =

* UPD: getting templates method
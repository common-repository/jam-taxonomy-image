=== Jam Taxonomy Image ===
Contributors: mcjambi
Tags: Taxonomy thumbnail, thumbnail, Category Image, Category thumbnail, Tag image, Tag thumbnail, Tag, Category, Taxonomy, Category Widget, Taxonomy Widget, Widget, List Category
Requires at least: 3.0
Tested up to: 4.3
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Donate link: http://www.jamviet.com/


Jam Taxonomy Image will help you have a nicer Category/Tag/Custom Post type Page with banner, and have a nice and powerful Taxonomy Widget

== Description ==

This plugin will help you add a image ( thumbnail ) to Taxonomy like Category, Tag, Custom Taxonomy and display image via Widget, or function name 'get_taxonomy_image($term_id)' return URL image.

* Note: I do not add any CSS to Your theme header, and of course Image will have full size as you upload, so if you want to have a small thumbnail, you can add CSS or just upload a small image, if you want to display a banner for one category, you can upload a big picture or banner !

* This plugin include a nice Taxonomy Widget in Admin > Apearance > Widget, please check out !

* This plugin Using plugin Taxonomy Metadata (Michael Yoshitaka Erlewine) !

<h3>How to Use ?</h3>

First, you need to install this plugin successfully from admin
Then, please go to Setting > Taxonomy Image to active which taxonomy you wish to add image, Tag, Category or Custom taxonomy
And, after that, in edit tag/category screen will have a form to add image/thumbnail/banner
Finnal, Add this code 
`<img src="'. get_taxonomy_image(get_query_var('cat')) . " />`
to category.php in your theme, please place it before H1 tag or anywhere you like, you will see big banner or thumbnail as you uploaded before.


== Installation ==
Just unzip plugin and move all of theme to plugins folder inside Wordpress directory, then go to Admin > Plugins to active it, Done !


== Screenshots ==
1. Setting page: choose a Taxonomy, include Custom Taxonomy
2. Add image in Category Page
3. Add image in Custom Taxonomy Page
4. Amazing Widget like Category list, but add display description and image feature !


== Changelog ==
= 1.0 =
First version release.

== Upgrade Notice ==
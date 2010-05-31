=== File Gallery ===
Contributors: aesqe
Donate link: http://www.amazon.com/gp/registry/wishlist/1IU6F22QPQX2Y/
Tags: attachment, attachments, gallery, galleries, template, templates, shortcode, file, files, attach, detach, unattach, copy, media, tags, library
Requires at least: 2.9.2
Tested up to: 3.0-rc1
Stable tag: trunk

File Gallery extends WordPress media (attachments) capabilities 
by adding a new gallery shortcode handler with templating support 
(and much more).

== Description ==

"File Gallery" extends WordPress' media (attachments) capabilities 
by adding a new gallery shortcode handler with templating support, 
a new interface for attachment handling when editing posts, and much 
more... Here's the full list of features:

**Features:**

1.  multiple galleries per post with custom attachment order
2.  a basic templating system = choose a different template for each 
    gallery, even within the same post
3.  simple, easy to use UI with drag and drop sorting shows attachment 
    thumbnails beneath text editor: everything attachments-related is on 
    the same screen you're editing your post on
4.  settings page extends the default media settings page
5.  attach copies of items from media library to current post (copies 
    data only, not the file)
6.  copy all attachments from another post
7.  unattach (detach) items from current post
8.  media tags = tag your attachments and then use those tags to choose 
    which attachments you want to display in your gallery or to filter 
	your media library items.
9.  different background colors for items in media library depending 
    on their status = completely unattached (white), attached to other 
    posts (red), or attached to current post (yellow)
10. compatible with "WordPress Mobile Edition" and "Media Tags" plugins	
11. basic caching of gallery output and frequent queries (transients)
12. various smaller modifications described in help files (coming soon!) 

== Screenshots ==

1. File Gallery main box on editing screen
2. Edit attachment data
3. Copy all attachments from another post
4. Deleting attachments that have copies
5. Build a gallery by choosing media tags
6. Settings page
7. Post thumb and number of attachments as extras in this view
8. Copying attachments from media library

== Installation == 

1.	Place the whole 'file-gallery' folder into your WordPress 
	installation folder (usually under 'wp-content\plugins').
2.	Go to WordPress administration -> plugins page and activate 
	"File Gallery" plugin.
3.	You're done - go edit or add a new post to see how it works :)

== Changelog ==

= 1.5.3 =
* May 31st, 2010
* fixed a bug where attachment class value would be appended to 
  ad infinitum, when inserting multiple single attachments

= 1.5.2 =
* May 30th, 2010
* added image align options for single image inserts
* added option to link images to parent post
* muchly improved file_gallery_list_tags output
* fixed the url to 'crystal' icons for non-image file types
* added support for alt text for images
* support for lightbox-type scripts:
  - choose which link classes trigger auto-enqueueing of 
    scripts and styles (a script should be registered beforehand 
	for this to work)
  - added filters to modify image class, link class, and link rel 
    attribute
* a bunch of bugfixes

= 1.5.1 =
* May 4th, 2010
* added option to filter out duplicate attachments (copies) when
  browsing media library
* post thumbnail is now removed when that attachment is deleted or
  detached
* and finally, the help file is included :)

= 1.5 =
* April 25th, 2010
* fixed _has_copies meta handling when deleting a copy
* copied log writing function from Decategorizer
* some minor improvements
* first release sent to the WordPress plugin repository

= 1.5rc1 =
* April 16th, 2010
* set / unset post thumbnail with one click in the File Gallery box
* when copying all attachments from another post, if current post has 
  no attachments of its own, automatically set post thumb to be the same 
  as for the post we're copying attachments from
* if you have WPML plugin installed and you're editing a post that is a 
  translation, you'll notice a bluish link at the bottom of the Language 
  metabox. It allows you to copy all attachments from the original post 
  in just two clicks :)
* some minor improvements

= 1.5b3 =
* April 5th, 2010
* javascript bugfixes: attachment copying, clickable media tags, insert 
  single images into post... happens when one is not doing the debugging 
  part right from the beginning... :|

= 1.5b2 =
* April 4th, 2010
* bugfix: options would be reset on reactivation
* compatibility with custom post types in WordPress 3.0 (set it up on
  media options page, right under "File Gallery" heading)
* added option to delete all options on deactivation (for uninstall)
* check user capabilities before any delete or edit action

= 1.5b =
* March 20th, 2010
* bugfix: attachment data didn't get saved when edited inside File 
  Gallery metabox (a slight javascript oops, sorry about that)
* caching via WordPress transients
* states (shown/hidden) of insert options fieldsets are now saved 
  and preserved in options automatically
* new options:
	- enable/disable insert options fieldsets (displays 
	  attachment list only)
	- enable caching
	- cache non-html output
	- cache expiry time
	- clear cache
	- what additional columns to show on post/page edit screens
* some minor css fixes

= 1.5a =
* February 8th, 2010
* added ability to copy all attachments from another post
* added a list of media tags for each attachment (media library screen).
  this also means you can click on a tag's name and you'll get a list 
  of all attachments using that tag
* attachment edit screen: added default icon for non images (for now)
* more icon types in file gallery metabox :D

= 1.4 =
* January 19th, 2010
* more code optimizations, especially javascript
* added buttons to toggle gallery / single image options
* image zoom now opens fullsize image in a jQuery UI dialog
  (works fine in all browsers except Opera - does not get css position,
  will fix for 1.5)
* fixed theme folder url, custom templates now work (just place them in
  a folder named "file-gallery-templates" within your theme's folder)

= 1.3 =
* massive reorder / rewrite of the code, especially the javascript
  part which now performs much better, even in internet explorer
* complete rethink of the way attachment copying works + added a dialog
  box to warn the user of consequences if an attachment that is marked 
  for deletion has copies, with multiple choices on how to proceed
* some preliminary tinymce integration (click on gallery 
  placeholder image in visual editor and those attachments present in
  that gallery will get checked - currently only works for galleries 
  already present in tinymce editor when opening a post for editing)
* each attachment that has copies gets a new meta key->value pair:
  "_has_copies"->array(ids of attachment copies)

= 1.2 =
* December 30th, 2009
* nonced everything for security reasons
* color differentiation for items in media library
* converted options from variables to constants
* improved performance when copying items from media library
* moved attachment file url field on item edit screen to bottom of 
  the form
* added a "cancel and return" button on item edit screen
* each copied attachment now gets a new meta key->value pair:
  "_is_copy_of"->"original_attachment_id"

= 1.1 =
* December 12th, 2009
* Rewritten a lot of stuff for better WordPress compliance :)

== More info ==

This plugin uses icons from the awesome famfamfam Silk icon set by 
Mark James :)

"Silk" can be found at: http://famfamfam.com/lab/icons/silk/

Plugin settings are integrated into media settings page.

Help file is included, you'll find it in the "help" subfolder :)

== Plans for the next version? ==

sooner (before 1.6):

- cleanup script for media tag database taxonomy name(s)
- content type filtering
- audio and video preview on editing screens

later:

- better tinymce integration

maybe:

- ability to load attachments from other posts

probably not but who knows:

- when detaching / deleting attachments, just remove them from DOM
  without refreshing the entire file gallery box - need to think of 
  a nice, elegant solution? any ideas? :)


And thank you for reading this :)

aesqe

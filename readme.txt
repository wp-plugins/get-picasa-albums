=== Get Picasa Albums ===
Contributors: lepolt
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=1176262
Tags: photos, albums, photo albums, picasa, google,
Requires at least: 2.6.3
Tested up to: 2.6.3
Stable tag: trunk

Gets a listing of Picasa Web Albums and displays album thumbnails on your page.

== Description ==

Get Picasa Albums will take a given Picasa username and fetch a listing of all public albums for the given user. Thumbnails to the individual albums will be displayed in a table on the specified WordPress page. Each thumbnail is a link to the actual Picasa album hosted by Google. The user can specify the number of columns to display, so that the resulting table matches the width on the webpage. The user may also specify if random thumnails are used instead of the Picasa thumbs already specified. 

If random thumnails are used, a local cache file for each Picasa album will be stored in a sub-folder of the plugin. The cache files are updated if the timestamp on an album changes. If the current stamp matches an existing file, the existing file will be used to fetch a random thumbnail. 

== Installation ==

1. Upload the 'GetPicasaAlbums' directory to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Place [GetPicasaAlbums user="username" col="x" rand="y/n"] in the content of the page
   user: Picasa username
   col:  Number of columns to display (not required; default=2)
   rand: Display random thumbnails (not required; default=y)

== Frequently Asked Questions ==
= Question 1 =
Answer to question 1. Sorry, no questions yet..

== Screenshots ==

1. Sorry, no screenshots
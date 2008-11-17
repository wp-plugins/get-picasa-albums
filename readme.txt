=== Get Picasa Albums ===
Contributors: lepolt
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=1176262
Tags: photos, albums, photo albums, picasa, google,
Requires at least: 2.5
Tested up to: 2.6.3
Stable tag: trunk

Gets a listing of Picasa Web Albums and displays album thumbnails on your page.

== Description ==

Get Picasa Albums will take a given Picasa username and fetch a listing of all public albums for the given user. Thumbnails to the individual albums will be displayed in a table on the specified WordPress page. Each thumbnail is a link to the actual Picasa album hosted by Google. The user can specify the number of columns to display, so that the resulting table matches the width on the webpage. The user may also specify if random thumnails are used instead of the Picasa thumbs already specified. 

If random thumnails are used, a local cache file for each Picasa album will be stored in a sub-folder of the plugin. The cache files are updated if the timestamp on an album changes. If the current stamp matches an existing file, the existing file will be used to fetch a random thumbnail. 

== Installation ==
1. Extract zip file (should create get-picasa-albums directory)
2. Upload the 'get-picasa-albums' directory to the '/wp-content/plugins/' directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Place [GetPicasaAlbums user="username" col="x" random="y/n"] in the content of the page

== Frequently Asked Questions ==
= Why aren't my thumbnails showing up? =
1. Make sure that your syntax is correct
2. Make sure that the 'PicasaAlbumCache' directory is located within the 'get-picasa-albums' directory
3. Make sure read/write privledges are available on the 'PicasaAlbumCache' directory 
4. Make sure author didn't code it up wrong (whoops, should be fixed)

== Screenshots ==
1. Sorry, no screenshots

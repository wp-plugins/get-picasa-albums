<?php
/*
Plugin Name: Get Picasa Albums
Plugin URI: http://www.lepolt.com/blog/downloads/get-picasa-albums/
Description: Gets a listing of Picasa Web Albums and displays album thumbnails on your page
Author: Jonathan Lepolt
Version: 1.0
Author URI: http://www.lepolt.com
*/

 
/*Copyright (c) 2008, Jonathan Lepolt
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:
    * Redistributions of source code must retain the above copyright
      notice, this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright
      notice, this list of conditions and the following disclaimer in the
      documentation and/or other materials provided with the distribution.
    * Neither the name of Jonathan Lepolt nor the
      names of its contributors may be used to endorse or promote products
      derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY Jonathan Lepolt ''AS IS'' AND ANY
EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL Jonathan Lepolt BE LIABLE FOR ANY
DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.*/

////////////////////////////////////////////////////////////////////////////////////////////////////
// Filename:      GetPicasaAlbums.php
// Creation date: 07 November 2008
//
// Usage:
//   This script will parse the Google Picasa Web Album feed for the given user and generate a table
//   containing thumbnail links to each public album found. 
//   Some 3rd party helper code was found here: 
//      http://www.ibm.com/developerworks/library/x-picasalbum/
//
// Version history:
//   1.0 - 15 November 2008: Initial release
//
////////////////////////////////////////////////////////////////////////////////////////////////////

// Entry point from WordPress
add_shortcode('GetPicasaAlbums', 'main');

//--------------------------------------------------------------------------------------------------
// Function: main
//    Main entry point for the script. Does most of the work.
//
// Parameters
//    None.
//
// Returns:
//    None. 
//--------------------------------------------------------------------------------------------------
function main($atts)
{
   // Extract parameters
   extract(shortcode_atts(array('user' => 'INVALID', 'col' => '2', 'random' => 'y',), $atts));

   // Make sure they entered a Picasa username
   if( $user == 'INVALID' )
   {
      echo 'Picasa username required.<br>';
      echo 'Usage: [GetPicasaAlbums user="myUsername" col="x" random="y/n"]<br>';
      exit;
   }

   // Make sure there is an appropriate number of columns to display
   if( $col < 1 )
      $col = 2;

   // Create default variables
   $userid = "$user%40googlemail.com";    // picasa_user_id@gmail.com
   $errorFlag = false;

   // build feed URL
   $feedURL = "http://picasaweb.google.com/data/feed/api/user/$userid?kind=album";
   $picasaURL = "http://picasaweb.google.com/$user/";
   $picasaAlbumCache = getcwd()  . '/wp-content/plugins/GetPicasaAlbums/PicasaAlbumCache';

   // Read feed into SimpleXML object
   $sxml = simplexml_load_file($feedURL);
       
   $albumCount = 0;
   $titleArray = array();
   $rand = 1;
   
   if( $random == 'n' )
      $rand = -1;

   echo "<table>\n"; // Start building the table

   // Loop through and get info about each album
   foreach( $sxml->entry as $entry )
   {
      $title = $entry->title;          // October 11, 2008: Cross Country - NLL's
      $gphoto = $entry->children('http://schemas.google.com/photos/2007');
      $numphotos = $gphoto->numphotos; // Number of photos in this album
      $albumid = $gphoto->id;          // 5257119638771949313
      $linkName = $gphoto->name;       // October112008CrossCountryNLLS
      $href = $picasaURL . $linkName;  // http://picasaweb.google.com/lepolt/October112008CrossCountryNLLS
      $albumfeedURL = "http://picasaweb.google.com/data/feed/api/user/$user/albumid/$albumid";
      $curTimestamp = $entry->updated; // 2008-10-16T11:53:56.000Z
      $thumbToUse = '';                // http://lh3.ggpht.com/lepolt/SPUK9utDpGI/AAAAAAAABQ8/1asIvwXaOYE/s144/2008_101115.JPG
      
      // If we're using random thumbnails, get one or update cache files
      if( $rand != -1 )
      {
         // Get random number from 0 to $numphotos - 1
         $rand = mt_rand(0, ((int)$numphotos - 1));
         
         // Check to see if this albumid file exists...if so, open it and check timestamp
         $albumFile = "$picasaAlbumCache/$albumid-$curTimestamp";
         if( file_exists($albumFile) )
         {
            // Open it into an array
            if( $fileArray = file($albumFile) )
            {
               // We're up to date, grab a thumbnail
               $thumbToUse = $fileArray[$rand];
            }
            else
            {
               // Could not read file into array...
            }
         } // END if( file_exists($albumFile) )
         else
         {
            // Create new file and write all the thumbnail links to the file
            UpdateCacheFile($albumFile, $albumfeedURL, $rand, $thumbToUse);
         }
      } // END if( $rand != 1 )
      else
      {
         $media = $entry->children('http://search.yahoo.com/mrss/');
         $thumbnail = $media->group->thumbnail;

         // Direct address to thumbnail
         $thumbToUse = $thumbnail->attributes()->{'url'};
      }

      // Build the table dynamically
      if( $albumCount % $col == 0 )
      {
         // Set title for left column
         $titleArray[] = $title;
   
         // This is a new row, create it
         echo "<tr>\n";
      }

      createCell($thumbToUse, $href);

      if( $albumCount % $col !== 0 )
      {
         // Set title for next column
         $titleArray[] = $title;
      }
      
      if( ($albumCount+1) % $col == 0 && $albumCount > 0 )
      {
         // This is the far right column, close the row tag
         echo "</tr>\n";

         // Add row with gallery title info 
         setGalleryTitles($titleArray);

         // Clear array
         unset($titleArray);

         // Add blank row for separation between thumbs
         echo "<tr><td>&nbsp;</td></tr>\n";
      }
      
      $albumCount++;
   
   } // END foreach( $sxml->entry as $entry )
   
   // One more row? Yes, if we have an odd number of albums
   if( $albumCount % $col !== 0 )
   {
      echo "</tr>\n";
      while( $albumCount % $col !== 0 )
      {
         $titleArray[] = '';
         $albumCount++;
      }
      
      setGalleryTitles($titleArray);
   }
            
   // End table.
   echo "</table>\n";
   
} // END main()

//--------------------------------------------------------------------------------------------------
// Function: UpdateCacheFile
//    Updates the stored cache file with thumbnail links based on albumid and timestamp
//
// Parameters
//    albumFile    - Filename of local cache file: 5257119638771949313-2008-10-16T11:53:56.000Z
//    albumfeedURL - Full path to album feed on Google
//    rand         - Random number zero to number of photos in album
//    &oFile       - Reference to filename for thumbnail link
//
// Returns:
//    None. oFile will have the filename of a thumbnail to use
//--------------------------------------------------------------------------------------------------
function UpdateCacheFile($albumFile, $albumfeedURL, $rand, &$oFile)
{
   $count = 0;
   if( $fileHandle = @fopen($albumFile, 'w') )
   {
      // Loop through album information and store thumbnail links
      // read feed into SimpleXML object (individual albums)
      $sxml_album = simplexml_load_file($albumfeedURL);
      foreach( $sxml_album->entry as $album_photo )
      {         
         // Write thumbnail to file
         $media = $album_photo->children('http://search.yahoo.com/mrss/');
         $thumbnail = $media->group->thumbnail[1];
   
         // Direct address to thumbnail
         $thumbAddy = $thumbnail->attributes()->{'url'};
         fwrite($fileHandle, "$thumbAddy\n");
         
         // Get our random image now
         if( $rand == $count )
         {
            $oFile = $thumbAddy;
         }
         
         $count++;
      }

      // Close the file
      fclose($fileHandle);
   }
   else
   {
      // Cannot open albumFile for writing
   }
}

//--------------------------------------------------------------------------------------------------
// Function: CreateCell
//    Creates a cell in the table with <td></td>
//
// Parameters
//    fullImagePath - Full http path to the thumbnail image to use
//    href          - Path to link to
//
// Returns:
//    None.
//--------------------------------------------------------------------------------------------------
function CreateCell($fullImagePath, $href)
{
   echo '<td align="center" width="200">';
   echo "<a href=\"$href\" target=\"_blank\">";
   echo "<img src=\"$fullImagePath\" border=\"0\" alt=\"\" />";
   echo '</a>';
   echo "</td>\n";

}

//--------------------------------------------------------------------------------------------------
// Function: setGalleryTitles
//    Sets the text to use under each thumbnail. Creates the row and two columns
//
// Parameters
//    title1 - Title of the gallery on the left
//    title2 - Title of the gallery on the right
//
// Returns:
//    None.
//--------------------------------------------------------------------------------------------------
function setGalleryTitles($inArray)
{
   echo "<tr>\n";

   for( $i = 0; $i < sizeof($inArray); $i++ )
   {
      // Set <td> with date and description
      echo "<td align=\"center\">$inArray[$i]</td>\n";
   }
   
   echo "</tr>\n";
}

?>
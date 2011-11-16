<?php
define('INCLUDE_PATH', '../include/');
require(INCLUDE_PATH.'vitals.inc.php');
admin_authenticate();

require(INCLUDE_PATH.'admin_header.inc.php'); 


if (isset($_REQUEST['cancel'])) {
	header('Location: settings.php');
	exit;
}
else if (!empty($_REQUEST['submit'])) {
   $vidfilesarray = searchUploadedFiles(array("mp4"));
}

?>

<html>
   <body>
      <h1>Regenerate video thumbnails</h1><br /><br />
      <form id="regenForm" action="" method="post" > <!-- a blank action method is perfectly fine to submit the form to the same page -->
         <input type="submit" name="submit" id="submit" value="Regenerate all thumbnails now" class="submitBtn" />
      </form>

      <ul style="list-style:none;display:block;line-height:200%;">
         <?php
            foreach ($vidfilesarray as $filestring) {
               // delete any existing thumbnails in the source folder
               
               // recreate the thumbnails
               // function make_video_thumbnails($videoPath, $videoDirectoryPath, $thumbprefix="", $largesize="144x112", $smallsize="96x74", $timecode=1)
               // 
               // if ($filestring=="title.mp4") {
               //    $thumbs = make_video_thumbnails($filestring, dirname($filestring), "title");
               //    overlay_play_btn_jpg($thumbs["small"]);
               //    overlay_play_btn_jpg($thumbs["large"]);
               // }
               // else if ($filestring == "message.mp4") {
               //    $thumbs = make_video_thumbnails($filestring, dirname($filestring), "message");
               //    overlay_play_btn_jpg($thumbs["small"]);
               //    overlay_play_btn_jpg($thumbs["large"]);
               // }
               
               // and overlay_play_btn_jpg
               
               // make sure to do this for both title and message files
               
               print "<li>".$filestring."</li>"; 
            }
         ?>
      </ul>
      <br />
      <a href="settings.php">Go back</a>
   </body>
</html>

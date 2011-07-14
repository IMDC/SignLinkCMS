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
               print "<li>".$filestring."</li>"; 
            }
         ?>
      </ul>
      <br />
      <a href="settings.php">Go back</a>
   </body>
</html>

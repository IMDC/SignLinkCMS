<?php 
define('INCLUDE_PATH', '../include/');
require(INCLUDE_PATH.'vitals.inc.php');

require(INCLUDE_PATH.'header.inc.php'); 
?>

<img src="thumb.jpg">

<?php 
   $testimg = "thumb.jpg";
   add_play_button_overlay(dirname($testimg) );
?>

<img src="thumb_play.jpg">


<?php
require(INCLUDE_PATH.'footer.inc.php');
?>

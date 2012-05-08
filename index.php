<?php
   require('include/header.inc.php');
   require('include/functions.inc.php');

   $introvid = "videos/admintoc.swf";
?>

<div id="top-pane">
   <img id="nuitlogo" src="images/nuitblanchelogo.jpg" alt="Nuit Blanche" width="100px;" height="70px;" />
   <div id="intro-container" class="centered"> 
      <img id="topbarbackground" class="centered" src="images/black_bar_background.gif" alt="black top bar" /> 

      <object width="565" height="455"
         classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"
         codebase="http://fpdownload.macromedia.com/pub/
         shockwave/cabs/flash/swflash.cab#version=8,0,0,0">
         <param name="movie" value="<?php echo $introvid; ?>"/>
         <param name="autoplay" value="false"/>
         <embed src="<?php echo $introvid; ?>" width="565" height="455"
         type="application/x-shockwave-flash" pluginspage=
         "http://www.macromedia.com/go/getflashplayer" />
      </object>
   </div>
</div>
<div id="textgreet" class="centered">
   <h1>Check out these Signlink Studio videos from Nuit Blanche visitors!</h1>
   <!-- <span class="autoplayspan">Autoplay <a href="#" id="autoplaybtnon">on</a>&nbsp;&nbsp;<a href="#" id="autoplaybtnoff">off</a></span> -->
</div>
<div id="vid-list" class="centered">
   <div id="vid-list-container" class="centered">
      <?php
         /*
         $dir = 'videos';
         $files = scandir($dir);
         foreach ($files as $fileOrDir) {
            if ($fileOrDir=="." || $fileOrDir=="..") {
               continue;
            }
         */

         //$files = `ls -ltm1 videos/*.swf`;
         // path to samba share is /media/nuitBlancheFiles/
         // path to samba share is /media/Nuit/
	 $vidpath = "/nuitBlancheFiles/";
         $dirs = `ls -r /media/nuitBlancheFiles/`;

         //$foo = preg_split('/\s+/', $files);
         $foo = preg_split('/\s+/', $dirs);

			// get rid of blank one
			array_pop($foo);
         //echo "<p>" . print_r($foo) . "</p>";

			/*
         $complete = array();
	// strip off "videos/" from filename
         foreach ($foo as $toolong) {
            array_push($complete, substr($toolong, 7));
         }
*/
         //foreach ($complete as $fileOrDir) {
         
			foreach ($foo as $slsVidPath) {
				$mp4match = $vidpath . $slsVidPath . "/" . $slsVidPath . ".mp4";
				$swfmatch = $vidpath . $slsVidPath . "/" . $slsVidPath . ".swf";
				$jpgmatch = $vidpath . $slsVidPath . "/" . $slsVidPath . ".jpg";

				//make sure there is a matching .jpg file with the same name
				if (!file_exists("/media" . $jpgmatch)) {
					createThumbnail($mp4match, "144x112");
				}
				$anchorHTML = '<div class="vid-div"><a rel="vidgroup" class="slolightbox" href="' . $swfmatch . '"><img src="' . $jpgmatch . '" /></a><span class="vidtime">' . date('g:i a, M j',filemtime("/media".$mp4match)) . '</span><div class="vid-num">'. substr($slsVidPath, -3) . '</div></div>';
				echo $anchorHTML;

/*
            //$fexplode = explode(".", $fileOrDir);
            //$fnamext = end($fexplode);
            if ( $fnamext == "swf") { // if file is a .swf file
               //print "<p>" . $fileOrDir . "</p>";
               $noext = strip_ext($fileOrDir);
               $mp4match = "/media/nuitBlancheFiles/" . trim($noext) . ".mp4";
               $jpgmatch = $noext . ".jpg";
               // make sure this a matching .mp4 with same name
               if (!file_exists($mp4match)) {
                  //print "<p>".$noext.".mp4</p>";
                  //print "<p>no matching mp4 file for " . $fileOrDir . "</p>";
                  exit;
               }
               else {
                  //make sure there is a matching .jpg file with the same name
                  if (!file_exists($jpgmatch)) {
                     createThumbnail($mp4match, "144x112");
                  }

*/
                  // create an anchor that will lightbox to the Signlink Object
			}
         //print_r($files);
      ?>
   </div>
</div>
<div style="clear:both;background:#000;padding-bottom:60px;"></div>

<?php
   require('include/footer.inc.php');
?>

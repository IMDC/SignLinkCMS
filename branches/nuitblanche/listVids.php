<?php
	require('include/functions.inc.php');

	$vidpath = "/nuitBlancheFiles/";
	$dirs = `ls -r /media/nuitBlancheFiles/`;

	$foo = preg_split('/\s+/', $dirs);

	// get rid of blank one
	array_pop($foo);
		
	$numVids = sizeof($foo);	

	if ( isset($_SESSION['vidcount']) && ($numVids == $_SESSION['vidcount']) ) {
			echo $_SESSION['vidoutput'];
			return;
	}
	else {
		$_SESSION['vidcount'] = $numVids;
		$_SESSION['vidoutput'] = "";

		// rescan the directory and output the new files
		foreach ($foo as $slsVidPath) {
			$mp4match = $vidpath . $slsVidPath . "/" . $slsVidPath . ".mp4";
			$swfmatch = $vidpath . $slsVidPath . "/" . $slsVidPath . ".swf";
			$jpgmatch = $vidpath . $slsVidPath . "/" . $slsVidPath . ".jpg";

			//make sure there is a matching .jpg file with the same name
			if (!file_exists("/media" . $jpgmatch)) {
				createThumbnail($mp4match, "144x112");
			}
			/*
			$anchorHTML = '<div class="vid-div">';
				$anchorHTML .= '<a rel="vidgroup" class="slolightbox" href="' . $swfmatch . '">';
					$anchorHTML .= '<img src="' . $jpgmatch . '"></img>';
				$anchorHTML .= '</a>';
				$anchorHTML .= '<span class="vidtime">' . date('g:i a, M j',filemtime("/media".$mp4match)) . '</span>';
				$anchorHTML .= '<div class="vid-num">'. substr($slsVidPath, -3) . '</div>';
			$anchorHTML .= '</div>';
			*/
		
			$anchorHTML = '<div class="vid-div"><a rel="vidgroup" class="slolightbox" href="' . $swfmatch . '"><img src="' . $jpgmatch . '" /></a><span class="vidtime">' . date('g:i a, M j',filemtime("/media".$mp4match)) . '</span><div class="vid-num">'. substr($slsVidPath, -3) . '</div></div>';

			$_SESSION['vidoutput'] .= $anchorHTML;
			echo $anchorHTML;
		}
	}
?>

<?php
/* general functions */

function debug($var, $title='') {
	echo '<pre style="border: 1px black solid; padding: 0px; margin: 10px;" title="debugging box">';
	if ($title) {
		echo '<h4>'.$title.'</h4>';
	}
	
	ob_start();
	print_r($var);
	$str = ob_get_contents();
	ob_end_clean();

	$str = str_replace('<', '&lt;', $str);

	$str = str_replace('[', '<span style="color: red; font-weight: bold;">[', $str);
	$str = str_replace(']', ']</span>', $str);
	$str = str_replace('=>', '<span style="color: blue; font-weight: bold;">=></span>', $str);
	$str = str_replace('Array', '<span style="color: purple; font-weight: bold;">Array</span>', $str);
	echo $str;
	echo '</pre>';
}

if(!function_exists('scandir')) {
    function scandir($dir, $sortorder = 0) {
        if(is_dir($dir) && $dirlist = @opendir($dir)) {
            while(($file = readdir($dirlist)) !== false) {
                $files[] = $file;
            }
            closedir($dirlist);
            ($sortorder == 0) ? asort($files) : rsort($files); // arsort was replaced with rsort
            return $files;
        } else return false;
    }
}

/* returns html-encoded title (image or video or text) - things that have titles: forum, thread, page.  */
function get_title($location, $id, $size='reg') {				
	global $db, $filetypes_video;
	
	$level = '';
	$depth = substr_count(INCLUDE_PATH, '/');
	for ($i=1; $i<$depth; $i++) {
		$level .= "../";
	}

	switch ($location) {
		case 'forum':
			$sql = "SELECT subject, subject_alt FROM forums WHERE forum_id=".$id;
			$title_path = $level.'uploads/forums/'.$id.'/';
			break;
		case 'post':
			$sql = "SELECT subject, subject_alt FROM forums_posts WHERE post_id=".$id;
			$title_path = $level.'uploads/posts/'.$id.'/';
			break;
		case 'page':
			$sql = "SELECT title, title_alt FROM pages WHERE page_id=".$id;
			$title_path = $level.'uploads/pages/'.$id.'/';		
			break;
		case 'vlog':
			$sql = "SELECT title, title_alt FROM vlogs WHERE vlog_id=".$id;
			$title_path = $level.'uploads/vlogs/'.$id.'/';		
			break;
		case 'entry':
			$sql = "SELECT title, title_alt FROM vlogs_entries WHERE entry_id=".$id;
			$title_path = $level.'uploads/entries/'.$id.'/';		
			break;			
	}
	$result = mysql_query($sql, $db);
	if ($result) {
		$row = mysql_fetch_row($result);
		if (!empty($row[0])) {
			//the title is plain text
			$text_container = '<div class="text_title">';
			$title = $text_container . $row[0] . '</div>';
		} else {
			//the title is a file
			
			//get files
			$dir_files = @scandir($title_path);

			if(!empty($dir_files)) {
			
				foreach ($dir_files as $dir_file) {
					if (substr($dir_file,0, 5) == "title") {
						$title_file = $dir_file;
						break;
					}
				}

				$ext = end(explode('.',$title_file));

				if ($size == 'small') {
					$height='75';
					$width = '96';
					$style="style='height:75px;width:96px;'";
				} else {
					$height='113';
					$width='145';
					$style= "style='height:145px;width:113px;'";
				}
				
				
				if (in_array($ext, $filetypes_video)) {
					// file is a video	
				
					/*
					$title = '<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B"
					id="clip" width="'.$width.'" height="'.$height.'" codebase="http://www.apple.com/qtactivex/qtplugin.cab">
						<param name="src" value="'.$title_path.$title_file.'"/>
						<param name="autoplay" value="false"/>
						<param name="controller" value="true"/>
						<param name="scale" value="tofit"/>
						<embed src="'.$title_path.$title_file.'" width="'.$width.'" height="'.$height.'" name="clip"
						autoplay="false" controller="true" enablejavascript="true" scale="tofit"
						alt="Quicktime ASL video"
						pluginspage="http://www.apple.com/quicktime/download/" />
					</object>';
					*/
					
					/*   ****** code below loads the full video upon page load
					$title = '
						<a  
							 href="'.$title_path.$title_file.'"
							 style="display:block;width:'.$width.';height:'.$height.'px"  
							 id="'.$title_path.'"> 
						</a> 
						*/
						
						/* the code below uses the flowplayer (www.flowplayer.org) flash player to play the video
							when you click on the thumbnail .jpg that is initially displayed.
							loads 'thumb.jpg' in same folder on page load instead of the whole video file
							previous version of flowplayer line below
							flowplayer("'.$title_path.'", "flash/flowplayer-3.1.1.swf", {
						*/

                  if ($size == 'small') {
                     if (file_exists($title_path . "thumbsmall_play.jpg")) {
                        $thumbjpg = $title_path . "thumbsmall_play.jpg";
                     }
                     else if ( file_exists($title_path . "thumb_small_play.jpg") ) {
                        $thumbjpg = $title_path . "thumb_small_play.jpg"; 
                     }
                     else if ( file_exists($title_path . "thumbsmall.jpg") ) {
                        $thumbjpg = $title_path . "thumbsmall.jpg"; 
                     }
                     else if ( file_exists($title_path . "thumb.jpg") ) {
                        $thumbjpg = $title_path . "thumb.jpg";
                     }
                     else {
                        $thumbjpg = "images/default_movie_icon_small.png";
                     }
                  }
                  else {
                     if (file_exists($title_path . "thumb_play.jpg")) {
                        $thumbjpg = $title_path . "thumb_play.jpg";
                     }
                     else if ( file_exists($title_path . "thumb.jpg") ) {
                        $thumbjpg = $title_path . "thumb.jpg";
                     }
                     else {
                        $thumbjpg = "images/default_movie_icon.png";
                     }

                  }

                  /*
						if ( !file_exists($title_path . "thumb_play.jpg") ) {
							if ($size == 'small') {
                        $thumbjpg = $title_path . "thumbsmall.jpg";
                     }
                     else {
                        $thumbjpg = $title_path . "thumb.jpg";
                     }
						}
						else {
                     if ($size == 'small') {
                        $thumbjpg = $title_path . "thumbsmall_play.jpg";
                     }
                     else {
							   $thumbjpg = $title_path . "thumb_play.jpg";
                     }
						}
                  */
						
				   
        // this is from about 9 lines below, the img src code    
        // <img src="'.$thumbjpg.'" alt="'.$title_path.'" />


					$title = '
						<a  
							 href="'.$title_path.$title_file.'"
							 class = "flash_player_holder" 
							 style="display:block;width:'.$width.';height:'.$height.'px;margin-left:auto;margin-right:auto;"  
							 id="'.$title_path.'">
							 <img src="'.$thumbjpg.'" height="'.$height.'" width="'.$width.'" alt="'.$row[1].'" />
						</a> 
						<script>
							flowplayer("'.$title_path.'", "flash/flowplayer-3.1.5.swf", {
								clip: {
										url: \''.$title_path.$title_file.'\',
										autoPlay: true,
										autoBuffering: true
								}, 
								plugins: {
									controls: {
										backgroundColor: \'#000000\',
										backgroundGradient: \'low\',
										autoHide: \'always\',
                              hideDelay: 2000,
										all: false,
										scrubber: true,
										//mute: true,
										fullscreen: true,
										height: 14,
										progressColor: \'#FFFF00\',
                              progressGradient: \'medium\',
										bufferColor: \'#333333\'
									}
								}
							});
						</script>';
				}
				// else file is an image
				else {
					$title = '<img src="'.$title_path.$title_file.'" alt="'.$row[0].'" title="'.$row[0].'" '.$style.' />';
				}
			}
		}
	}
	return $title;
}


/* saves uploaded image 

location - forum, post or page
type - title, description, subject, message, content, etc. - this will be the name of the file when saved
tmp_file - the file sent through the form
id - id of the forum, post, or page

*/
function save_image($location, $type, $file, $id) {
	global $db;
	
	$tmp_file = $_FILES[$file]['tmp_name'];
	$ext = end(explode('.',$_FILES[$file]['name']));

	$level = '';
	$depth = substr_count(INCLUDE_PATH, '/');
	for ($i=1; $i<$depth; $i++) {
		$level .= "../";
	}

	switch ($location) {
		case 'forum':
			if(!file_exists($level.UPLOAD_DIR.'forums/'.$id.'/')) {
				mkdir($level.UPLOAD_DIR.'forums/'.$id.'/');
			}
			$newfile = $level.UPLOAD_DIR.'forums/'.$id.'/'.$type.'.'.$ext;
			break;
		case 'post':
			if(!file_exists($level.UPLOAD_DIR.'posts/'.$id.'/')) {
				mkdir($level.UPLOAD_DIR.'posts/'.$id.'/');
			}
			$newfile = $level.UPLOAD_DIR.'posts/'.$id.'/'.$type.'.'.$ext;
			break;
		case 'page':
			if(!file_exists($level.UPLOAD_DIR.'pages/'.$id.'/')) {
				mkdir($level.UPLOAD_DIR.'pages/'.$id.'/');
			}
			$newfile = $level.UPLOAD_DIR.'pages/'.$id.'/'.$type.'.'.$ext;
			break;
		case 'vlog':
			if(!file_exists($level.UPLOAD_DIR.'vlogs/'.$id.'/')) {
				mkdir($level.UPLOAD_DIR.'vlogs/'.$id.'/');
			}
			$newfile = $level.UPLOAD_DIR.'vlogs/'.$id.'/'.$type.'.'.$ext;
			break;		
		case 'entry':
			if(!file_exists($level.UPLOAD_DIR.'entries/'.$id.'/')) {
				mkdir($level.UPLOAD_DIR.'entries/'.$id.'/');
			}
			$newfile = $level.UPLOAD_DIR.'entries/'.$id.'/'.$type.'.'.$ext;
			break;				
		case 'comment':
			if(!file_exists($level.UPLOAD_DIR.'comments/'.$id.'/')) {
				mkdir($level.UPLOAD_DIR.'comments/'.$id.'/');
			}
			$newfile = $level.UPLOAD_DIR.'comments/'.$id.'/'.$type.'.'.$ext;
			break;				
	}

	//if image, resize 
	list($width, $height) = getimagesize($tmp_file); 

	if ($width>BLOCK_WIDTH || $height>BLOCK_WIDTH) {
		if ($width >= $height && $width > BLOCK_WIDTH) {
			$percent = BLOCK_WIDTH/$width;
		} else if ($height > $width && $height > BLOCK_WIDTH) {
			$percent = BLOCK_WIDTH/$height;
		} 

		$newwidth = round($width * $percent);
		$newheight = round($height * $percent);

		if ($ext == "jpg" || $ext=='jpeg') {
			$smaller = imagecreatetruecolor($newwidth, $newheight);
			$source = imagecreatefromjpeg($tmp_file); 
		} elseif ($ext == "gif") {
			$smaller = imagecreate($newwidth, $newheight);
			$source = imagecreatefromgif($tmp_file);
		} elseif ($ext == 'png') {
			$smaller = imagecreatetruecolor($newwidth, $newheight);
			$source = imagecreatefrompng($tmp_file);
		}

		if (!imagecopyresized($smaller, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height)) {
			print "Error Uploading File.";
			exit();
		}

		if ($ext == "jpg" or $ext=='jpeg') {
			imagejpeg($smaller, $newfile);
		} elseif ($ext == "gif") {
			imagegif($smaller, $newfile); 
		} elseif ($ext == 'png') {
			imagepng($smaller, $newfile); 
		}			  
	}

	//if (!@copy(trim($newfile), trim($tmp_file))) {
	//echo substr(sprintf('%o', fileperms($level.UPLOAD_DIR.'posts/'.$id.'/')), -4);
	//echo '<br>'.$newfile.'<br>'.$tmp_file;
	
	
	if (!@move_uploaded_file($tmp_file,$newfile)) {
	  print "Error Uploading File - check directory permissions.";
	  exit;
	} 
}

/* saves uploaded video 

location - forum, post or page
type - title, description, subject, message, content, etc. - this will be the name of the file when saved
tmp_file - the file sent through the form
id - id of the forum, post, or page

*/
function save_video($location, $type, $file, $id) {
	global $db;

	$ext = end(explode('.',$_FILES[$file]['name']));
	$level = '';
	$depth = substr_count(INCLUDE_PATH, '/');
	for ($i=1; $i<$depth; $i++) {
		$level .= "../";
	}

	switch ($location) {
		case 'forum':
			if(!file_exists($level.UPLOAD_DIR.'forums/'.$id.'/')) {
				mkdir($level.UPLOAD_DIR.'forums/'.$id.'/');
			}
			$newfile = $level.UPLOAD_DIR.'forums/'.$id.'/'.$type.'.'.$ext;
			break;
		case 'post':
			if(!file_exists($level.UPLOAD_DIR.'posts/'.$id.'/')) {
				mkdir($level.UPLOAD_DIR.'posts/'.$id.'/');
			}
			$newfile = $level.UPLOAD_DIR.'posts/'.$id.'/'.$type.'.'.$ext;
			break;
		case 'page':
			if(!file_exists($level.UPLOAD_DIR.'pages/'.$id.'/')) {
				mkdir($level.UPLOAD_DIR.'pages/'.$id.'/');
			}
			$newfile = $level.UPLOAD_DIR.'pages/'.$id.'/'.$type.'.'.$ext;
			break;		
		case 'vlog':
			if(!file_exists($level.UPLOAD_DIR.'vlogs/'.$id.'/')) {
				mkdir($level.UPLOAD_DIR.'vlogs/'.$id.'/');
			}
			$newfile = $level.UPLOAD_DIR.'vlogs/'.$id.'/'.$type.'.'.$ext;
			break;	
		case 'entry':
			if(!file_exists($level.UPLOAD_DIR.'entries/'.$id.'/')) {
				echo mkdir($level.UPLOAD_DIR.'entries/'.$id.'/');
			}
			$newfile = $level.UPLOAD_DIR.'entries/'.$id.'/'.$type.'.'.$ext;
			break;	
		case 'comment':
			if(!file_exists($level.UPLOAD_DIR.'comments/'.$id.'/')) {
				mkdir($level.UPLOAD_DIR.'comments/'.$id.'/');
			}
			$newfile = $level.UPLOAD_DIR.'comments/'.$id.'/'.$type.'.'.$ext;
			break;					
	}


   /* disabling this to see if mp4 files will work now */
   if (!@move_uploaded_file($_FILES[$file]['tmp_name'], $newfile)) {
      print "Error Uploading File - check directory permissions.";
      exit;
   }

   if ( strcmp($ext,  'mp4') != 0 ) { // file is NOT an mp4 file
      //print $newfile . " is not a mp4 file, attempting to convert<br />";
      // convert to mp4 using ffmpeg
	   $extension = end( explode('.', $newfile) );
      $newfileNoExtension = substr( $newfile, 0, (strlen($newfile) - strlen($extension)) );
      $newfileMP4Extension = $newfileNoExtension . 'mp4';
      //echo $newfileMP4Extension . "<br /><br />";

	   // shell_exec("ffmpeg -i " . $videoPath . " -ss 1 -f image2 -vframes 1 -s 144x112 " . $videoDirectoryPath . "/thumb.jpg 2>&1");
      $convertOutput = shell_exec("include/ffmpeg/ffmpeg -i " . $newfile . " -acodec libfaac -ab 64k -ar 22050 -async 22050 -r 15 -aspect 4:3 -s 320x240 -vcodec libx264 -b 400k -flags +loop -cmp +chroma -partitions +parti4x4+partp8x8+partb8x8 -subq 1 -trellis 0 -refs 1 -bf 16 -b_strategy 1 -coder 1 -me_range 16 -g 3 -keyint_min 25 -sc_threshold 40 -i_qfactor 0.71 -bt 175k -qcomp 0.6 -qmin 10 -qmax 51 -qdiff 4 -y " . $newfileMP4Extension . " 2>&1");
      
      //process output of ffmpeg using $convertOutput
      
      //echo "<pre>$convertOutput</pre><br /><br />";
      
      make_video_thumbnail($newfile, dirname($newfile));
      overlay_play_btn(dirname($newfile));

      /* assuming everything went okay, we can delete the .avi file that we don't need now */
      if(file_exists($newfile)) {
        unlink($newfile);
      }
   }
   else { // file IS a mp4 file, no conversion necessary
      
      // creates the 'thumb.jpg' thumbnail from the first second of the video uploaded
      make_video_thumbnail($newfile, dirname($newfile));
      
      // adds the transparent png play button to the thumbnail
      overlay_play_btn(dirname($newfile));
	}


}

/* saves signlink file 

location - forum, post or page
type - title, description, subject, message, content, etc. - this will be the name of the file when saved
tmp_file - the file sent through the form
id - id of the forum, post, or page

*/

function save_signlink ($location, $type, $file, $id) {
	global $db;

	$ext = end(explode('.',$_FILES[$file]['name']));
	if ($ext == "mp4") {
		$filename = $_FILES[$file]['name'];
	} else {
		$filename = $type.'.'.$ext;
	}

	$level = '';
	$depth = substr_count(INCLUDE_PATH, '/');
	for ($i=1; $i<$depth; $i++) {
		$level .= "../";
	}

	switch ($location) {
		case 'forum':
			if(!file_exists($level.UPLOAD_DIR.'forums/'.$id.'/')) {
				mkdir($level.UPLOAD_DIR.'forums/'.$id.'/');
			}
			$newfile = $level.UPLOAD_DIR.'forums/'.$id.'/'.$filename;
			break;
		case 'post':
			if(!file_exists($level.UPLOAD_DIR.'posts/'.$id.'/')) {
				mkdir($level.UPLOAD_DIR.'posts/'.$id.'/');
			}
			$newfile = $level.UPLOAD_DIR.'posts/'.$id.'/'.$filename;
			break;
		case 'page':
			if(!file_exists($level.UPLOAD_DIR.'pages/'.$id.'/')) {
				mkdir($level.UPLOAD_DIR.'pages/'.$id.'/');
			}
			$newfile = $level.UPLOAD_DIR.'pages/'.$id.'/'.$filename;
			break;
		case 'vlog':
			if(!file_exists($level.UPLOAD_DIR.'vlogs/'.$id.'/')) {
				mkdir($level.UPLOAD_DIR.'vlogs/'.$id.'/');
			}
			$newfile = $level.UPLOAD_DIR.'vlogs/'.$id.'/'.$filename;
			break;
		case 'entry':
			if(!file_exists($level.UPLOAD_DIR.'entries/'.$id.'/')) {
				mkdir($level.UPLOAD_DIR.'entries/'.$id.'/');
			}
			$newfile = $level.UPLOAD_DIR.'entries/'.$id.'/'.$filename;
			break;			
		case 'comment':
			if(!file_exists($level.UPLOAD_DIR.'comments/'.$id.'/')) {
				mkdir($level.UPLOAD_DIR.'comments/'.$id.'/');
			}
			$newfile = $level.UPLOAD_DIR.'comments/'.$id.'/'.$filename;
			break;					
	}

	if (!move_uploaded_file($_FILES[$file]['tmp_name'], $newfile)) {
	  print "Error Uploading File.";
	  exit;
	} 
}

function check_uploads() {

	foreach ($_FILES as $file) {
		if (!empty($file['name'])) {
			switch ($file['error']) {  
				case 1:
					   $_SESSION['errors'][] = 'The file is bigger than this PHP installation allows.';
					   break;
				case 2:
					   $_SESSION['errors'][] = 'The file is bigger than this form allows.';
					   break;
				case 3:
					   $_SESSION['errors'][] = 'Only part of the file was uploaded.';
					   break;
				case 4:
					   $_SESSION['errors'][] = 'No file was uploaded.';
					   break;
			}
		}
	}
	return;
}

function save_avatar($id) {
	global $db;
		
	$tmp_file = $_FILES['avatar']['tmp_name'];
	$ext = end(explode('.',$_FILES['avatar']['name']));

	$level = '';
	$depth = substr_count(INCLUDE_PATH, '/');
	for ($i=1; $i<$depth; $i++) {
		$level .= "../";
	}
	
	if(!file_exists($level.UPLOAD_DIR.'members/'.$id.'/')) {
		mkdir($level.UPLOAD_DIR.'members/'.$id.'/');
	} else { 
		delete_avatar($id);
	}
	
	$newfile = $level.UPLOAD_DIR.'members/'.$id.'/avatar.'.$ext;
		
	//if image, resize 
	list($width, $height) = getimagesize($tmp_file); 

	if ($width>120 || $height>120) {
	
		if ($width >= $height && $width > 120) {
			$percent = 120/$width;
		} else if ($height > $width && $height > 120) {
			$percent = 120/$height;
		} 

		$newwidth = round($width * $percent);
		$newheight = round($height * $percent);

		if ($ext == "jpg" || $ext=='jpeg') { 
			$smaller = imagecreatetruecolor($newwidth, $newheight);	
			$source = imagecreatefromjpeg($tmp_file); 
		} elseif ($ext == "gif") {
			$smaller = imagecreate($newwidth, $newheight);
			$source = imagecreatefromgif($tmp_file);
		} elseif ($ext == 'png') {
			$smaller = imagecreatetruecolor($newwidth, $newheight);
			$source = imagecreatefrompng($tmp_file);
		}

		if (!imagecopyresized($smaller, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height)) {
			print "Error Uploading File.";
			exit();
		}

		if ($ext == "jpg" or $ext=='jpeg') {
			imagejpeg($smaller, $newfile);
		} elseif ($ext == "gif") {
			imagegif($smaller, $newfile); 
		} elseif ($ext == 'png') {
			imagepng($smaller, $newfile); 
		}	
	}

	unset($_FILES);

	if (!copy($newfile, $tmp_file)) {
	  print "Error Uploading File.";
	  exit;
	} 
}

function delete_avatar($id) {
	$dir = INCLUDE_PATH.'../'.UPLOAD_DIR.'members/'.$id.'/';
	$dh = @opendir($dir);
	while ( $file = @readdir($dh) ) {
		if ( $file != '.' || $file != '..') {
			@unlink($dir.$file);
		}
	}
	@closedir ($dh);
	@rmdir($dir);	

	/*$av_path = INCLUDE_DIR.'../'.UPLOAD_DIR.'members/'.$id.'/';
	$dir_files = @scandir($av_path);		
	if(!empty($dir_files)) {
		foreach ($dir_files as $dir_file) {
			if (substr($dir_file,0, 6) == "avatar") {
				$av_path .= $dir_file;
				unlink($av_path); 
				break;
			}
		}
	}*/	
}

function get_avatar($id) {
	global $db;
	
	$level = '';
	$depth = substr_count(INCLUDE_PATH, '/');
	for ($i=1; $i<$depth; $i++) {
		$level .= "../";
	}
				
	//get file
	$av_path = $level.UPLOAD_DIR.'members/'.$id.'/';
	$dir_files = @scandir($av_path);
	$av_file = '';
	if(!empty($dir_files)) {
		foreach ($dir_files as $dir_file) {
			if (substr($dir_file,0, 6) == "avatar") {
				$av_file = $dir_file;
				break;
			}
		}
	}

	if ($av_file) {
		echo '<img id="avatar" src="uploads/members/'.$id.'/'.$av_file.'" alt="'.$_SESSION['login'].'\'s avatar" />';
	} else {
		echo '<img id="avatar" src="images/no_avatar.jpg" alt="No avatar" />';
	}

	return;
}

/*
* location: directory in /uploads - forums, members, pages, or posts
* id
* type: title or message
*/

function delete_files($location, $id, $type="message") {
	global $db;
	
	$level = '';
	$depth = substr_count(INCLUDE_PATH, '/');
	for ($i=1; $i<$depth; $i++) {
		$level .= "../";
	}
	
	$path = $level.UPLOAD_DIR.$location.'/'.$id.'/';
	
	$dir_files = @scandir($path);		
	if(!empty($dir_files)) {
		foreach ($dir_files as $dir_file) {		
			if ( (substr($dir_file,0,5) == substr($type,0,5)) || (substr($dir_file,-3,3) =="flv")) {
				unlink($path.$dir_file); 
			}
		}
	}	
	return;
}

/* get members */

function get_login($id) {
	global $db;
	
	$sql = "SELECT login FROM members WHERE member_id=".$id;
	$result = mysql_query($sql, $db);
	$row = @mysql_fetch_assoc($result);
			
	return $row['login'];
}

function print_members_dropdown() {
	global $db;
	
	$sql = "SELECT member_id, login, name FROM members WHERE login!='admin'";
	$result = mysql_query($sql, $db);
	if (@mysql_num_rows($result) != 0) {
		echo '<select name="member">';
		echo '<option value="0">---Choose a member---</option>';
		while($row = mysql_fetch_assoc($result)) {
			echo '<option value='.$row['member_id'].'>'.$row['name'].' ('.$row['login'].')</option>';
		}
		echo '</select>';
	} else {
		echo "No members.";
	}
}

// draws a white play button over top of a video thumbnail
function overlay_play_btn($fullDestImagePath) {

	//make sure destination image full path includes the slash at the end
	$pathLength = strlen($fullDestImagePath);
	if ($pathLength == 0) {
		print 'Error with filename length creating play button overlay';
	}
	
	if ( substr($fullDestImagePath, -1) !== '/' ) {
		// add the slash because it's missing
		$fullDestImagePath = $fullDestImagePath . '/';
		//print 'added slash to dir name';
		//echo '<br />' . $fullDestImagePath;
	}
	
	$playImagePath = 'images/';
	
	// use the thumbnail created during upload
	$image = imagecreatefromjpeg($fullDestImagePath . 'thumb.jpg');
	$imagesmall = imagecreatefromjpeg($fullDestImagePath . 'thumbsmall.jpg');
	
	if ( !$image ) {
		print 'Error finding bigger thumbnail';
	}
	if ( !$imagesmall ) {
		print 'Error finding smaller thumbnail';
	}
	
	
	// for the play button overlay, use 'play_btn.png' in the images/ folder 
	$watermark = imagecreatefrompng($playImagePath . 'play_btn.png');
	
	if ( !$watermark ) {
		print 'Error finding play button overlay image';
	}
	
	imagealphablending($image, true);
	imagealphablending($imagesmall, true);
	imagealphablending($watermark, true); 
	
	// render play button .png file on top of thumb.jpg file
	imagecopy($image, $watermark, imagesx($image)/2-22, imagesy($image)/2-22, 0, 0, imagesx($watermark), imagesy($watermark));
	imagecopy($imagesmall, $watermark, imagesx($imagesmall)/2-22, imagesy($imagesmall)/2-22, 0, 0, imagesx($watermark), imagesy($watermark)); 

	
	// create new thumbnail with play button overlayed on top in the same folder
	if ( !imagejpeg($image, $fullDestImagePath . 'thumb_play.jpg') ) {
		print "\n**ERROR** - Error creating new thumbnail jpeg file, possibly check directory permissions";
	}
	if ( !imagejpeg($imagesmall, $fullDestImagePath . 'thumb_small_play.jpg') ) {
		print "\n**ERROR** - Error creating new thumbnail jpeg file, possibly check directory permissions";
	}
	
	imagedestroy($image);
	imagedestroy($imagesmall);
	imagedestroy($watermark);
}

// creates smaller jpg thumbnails from video files, used as image placeholders before videos load into flowplayer
function make_video_thumbnail($videoPath, $videoDirectoryPath) {
   // create a regular sized thumbnail
	shell_exec("include/ffmpeg/ffmpeg -i " . $videoPath . " -ss 1 -f image2 -vframes 1 -s 144x112 " . $videoDirectoryPath . "/thumb.jpg 2>&1");
   // create a small sized thumbnail
	shell_exec("include/ffmpeg/ffmpeg -i " . $videoPath . " -ss 1 -f image2 -vframes 1 -s 74x96  " . $videoDirectoryPath . "/thumbsmall.jpg 2>&1");

}

?>

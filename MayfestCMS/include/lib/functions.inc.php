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
			$sql = "SELECT subject, subject_alt, last_post FROM forums WHERE forum_id=".$id;
			$title_path = $level.'uploads/forums/'.$id.'/';
			break;
		case 'post':
			$sql = "SELECT subject, subject_alt, date FROM forums_posts WHERE post_id=".$id;
			$title_path = $level.'uploads/posts/'.$id.'/';
			break;
		case 'page':
			$sql = "SELECT title, title_alt, last_modified FROM pages WHERE page_id=".$id;
			$title_path = $level.'uploads/pages/'.$id.'/';		
			break;
		case 'vlog':
			$sql = "SELECT title, title_alt, last_entry FROM vlogs WHERE vlog_id=".$id;
			$title_path = $level.'uploads/vlogs/'.$id.'/';		
			break;
		case 'entry':
			$sql = "SELECT title, title_alt, date FROM vlogs_entries WHERE entry_id=".$id;
			$title_path = $level.'uploads/entries/'.$id.'/';		
			break;			
	}
	$result = mysqli_query($db, $sql);
	
  if ($result) {	
    $row = mysqli_fetch_row($result);
		if (!empty($row[0])) {
			//the title is plain text
			$text_container = '<div class="text_title">';
			$title = $text_container . $row[0] . '</div>';
		}  
    else {
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
					$style= "style='height:113px;width:145px;'";
				}
				
				
				if (in_array($ext, $filetypes_video)) {
					// file is a video						
						/* the code below uses the flowplayer (www.flowplayer.org) flash player to play the video
							when you click on the thumbnail .jpg that is initially displayed.
							loads 'thumb.jpg' in same folder on page load instead of the whole video file
						*/
            // check size of video file to use the appropriate thumbnail
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
          * If useragent of browser is detected as a mobile device, serve up a simple link instead of flash
          * testing on Android phone plays the video just fine using video's native player
          * NOTE: video encoded with handbrake on iPod settings, m4v file produced
          */
          /*
          if (detectMobile()) {
            $title = '<video src="' . $title_path . $title_file . '" poster="'.$thumbjpg.'" height="'.$height.'px" width="'.$width.'px" onclick="this.play();" />';
          }
          else {
          */
          /*
						<a  
							 href="'.$title_path.$title_file.'"
							 class = "flash_player_holder"
                      alt="'.$row[1].'"
							 style="border:1px solid #cdcdcd;background-image:url(\''.$thumbjpg.'\');width:'.$width.'px;height:'.$height.'px;margin-left:5px;text-align:center;"
							 id="'.$title_path.'title">
							 <img src="images/play_large.png" style="border:0 none;margin-top:30px;width:40px;height:40px;" />
						</a> 
          */



            $title = '  
            <a
							 href="'.$title_path.$title_file.'"
						   class = "flash_player_holder"
							 style="width:'.$width.'px;height:'.$height.'px;margin-left:auto;margin-right:auto;"
							 id="'.$title_path.'title">
						   <img style="margin-left:-3px;" src="'.$thumbjpg.'" height="'.$height.'px" width="'.$width.'px" alt="'.$row[1].'" />
						</a>
            <script type="text/javascript">
							flowplayer("'.$title_path.'title", "flash/flowplayer-3.2.7.swf", {
								clip: {
										url: \''.$title_path.$title_file.'\',
										autoPlay: true,
										autoBuffering: true
								}, 
								plugins: {controls: conf.small
                        ';
//                  if ($size == 'small'){
//                      $title = $title . "controls: conf.small";
//                  } else {
//                      // originally designed for a diff size title option, not used right now
//                      $title = $title . "controls: conf.small";
//                  }
                  $title = $title . '
								}
							});
						</script>';
          //}
				}
				// else file is an image
				else {
                     /* An extra div with class "imgzoom_container" is added to each post with an image title to enable lightbox style img zooming */
					 $title = '<div class="imgzoom_container">
                             <img class="expand" src="'.$title_path.$title_file.'" alt="'.$row[0].'" title="'.$row[0].'" '.$style.' />
                                <a class="quickViewLink" href="'.$title_path.$title_file.'">
                                   <img class="quickView" src="images/search_button_green_32.png" />
                                </a>
                          </div>';
					 //$title = '<a href="'.$title_path.$title_file.'" class="thickbox"><img src="'.$title_path.$title_file.'" alt="'.$row[0].'" title="'.$row[0].'" '.$style.' /></a>';
				}
			}
		}
        /* This div creates displays the time and date of the post at the bottom of each title */
        $title = $title . '<div class="cont-date">' . date("H:s M j Y", $row[2]) . '</div>';
	}
  @mysqli_free_result($result);
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
	$ext = strtolower(end(explode('.',$_FILES[$file]['name'])));

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

    /**  todo: **/
    /**   CHANGE THIS TO DETECT THE CODEC USING FFMPEG AS FILE EXTENSION CHECK IS UNSAFE!!! **/
    /**   ************************************************************************************/
    /**  this would be a call to shell_exec("ffmpeg -i INPUTFILE") and parsing the output to find the video codec **/
   if ( strcmp($ext,  'mp4') != 0 ) { // file is NOT an mp4 file
      //print $newfile . " is not a mp4 file, attempting to convert<br />";
      // convert to mp4 using ffmpeg
      $extension = end( explode('.', $newfile) );
      $newfileNoExtension = substr( $newfile, 0, (strlen($newfile) - strlen($extension)) );
      $newfileMP4Extension = $newfileNoExtension . 'mp4';
      //echo $newfileMP4Extension . "<br /><br />";

	   // shell_exec("ffmpeg -i " . $videoPath . " -ss 1 -f image2 -vframes 1 -s 144x112 " . $videoDirectoryPath . "/thumb.jpg 2>&1");
     /** todo: consider using escapeshellarg($dirname) for the paths of files?  **/
      $convertOutput = shell_exec("../include/ffmpeg/ffmpeg -i " . $newfile . " -acodec libfaac -ab 64k -ar 22050 -async 22050 -r 15 -aspect 4:3 -s 320x240 -vcodec libx264 -b 400k -flags +loop -cmp +chroma -partitions +parti4x4+partp8x8+partb8x8 -subq 1 -trellis 0 -refs 1 -bf 16 -b_strategy 1 -coder 1 -me_range 16 -g 3 -keyint_min 25 -sc_threshold 40 -i_qfactor 0.71 -bt 175k -qcomp 0.6 -qmin 10 -qmax 51 -qdiff 4 -y " . $newfileMP4Extension . " 2>&1");
	//$_SESSION['feedback'][] = shell_exec("pwd");
      //$_SESSION['feedback'][] = $convertOutput; 
      //process output of ffmpeg using $convertOutput
      
      //echo "<pre>$convertOutput</pre><br /><br />";
      
      make_video_thumbnail($newfile, dirname($newfile));
      /** replacing this overlay call with some 'background-image' css on the
       * <a> element in the 'get_title' method. Should be less work server-side
       * and less prone to failing
       */
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

// using member id, locates avatar file inside uploads/members/ directory and returns path
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
*  Performs an update operation on the 'last_login_ts' field in the members table
*  Used to update the last time the member successfully logged in to the CMS
*  Returns true or false
*  Input - the member id
*/
function update_member_last_login($id) {
    global $db;
    $id = intval($id);
    $id = mysqli_real_escape_string($db, $id);
   // TODO: Should this be implemented as a new DB connection with de-elevated privileges?

   $sql = "UPDATE members set last_login_ts = NOW() WHERE member_id = $id";
   $result = mysqli_query($db, $sql);
   return $result;
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
	$result = mysqli_query($db, $sql);
	$row = @mysqli_fetch_assoc($result);
			
	return $row['login'];
}

function print_members_dropdown() {
	global $db;
	
	$sql = "SELECT member_id, login, name FROM members WHERE login!='admin'";
	$result = mysqli_query($db, $sql);
	if (@mysqli_num_rows($result) != 0) {
		echo '<select name="member">';
		echo '<option value="0">---Choose a member---</option>';
		while($row = mysqli_fetch_assoc($result)) {
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
		$_SESSION['errors'][] = 'Error with filename length creating play button overlay';
	}
	
	if ( substr($fullDestImagePath, -1) !== '/' ) {
		// add the slash because it's missing
		$fullDestImagePath = $fullDestImagePath . '/';
	}
	
	$playImagePath = '../images/';
	
	// use the thumbnail created during upload
	$image = imagecreatefromjpeg($fullDestImagePath . 'thumb.jpg');
	$imagesmall = imagecreatefromjpeg($fullDestImagePath . 'thumbsmall.jpg');
	
	if ( !$image ) {
		$_SESSION['errors'][] = 'Error finding bigger thumbnail';
	}
	if ( !$imagesmall ) {
		$_SESSION['errors'][] = 'Error finding smaller thumbnail';
	}
	
	
	// for the play button overlay, use 'play_btn.png' in the images/ folder 
	$watermark = imagecreatefrompng($playImagePath . 'play_btn.png');
	
	if ( !$watermark ) {
		$_SESSION['errors'][] = '<span style="color:#ff0000;size:1.4em;">Error finding play button overlay image</span>';
	}
	
	imagealphablending($image, true);
	imagealphablending($imagesmall, true);
	imagealphablending($watermark, true); 
	
	// render play button .png file on top of thumb.jpg file
	imagecopy($image, $watermark, imagesx($image)/2-22, imagesy($image)/2-22, 0, 0, imagesx($watermark), imagesy($watermark));
	imagecopy($imagesmall, $watermark, imagesx($imagesmall)/2-22, imagesy($imagesmall)/2-22, 0, 0, imagesx($watermark), imagesy($watermark)); 

	
	// create new thumbnail with play button overlayed on top in the same folder
	if ( !imagejpeg($image, $fullDestImagePath . 'thumb_play.jpg') ) {
		$_SESSION['errors'][] = 'Error creating new thumbnail, check dir permissions';
      print "\n**ERROR** - Error creating new thumbnail jpeg file, possibly check directory permissions";
	}
	if ( !imagejpeg($imagesmall, $fullDestImagePath . 'thumb_small_play.jpg') ) {
      $_SESSION['errors'][] = 'Error creating new thumbnail, check dir permissions';
		print "\n**ERROR** - Error creating new thumbnail jpeg file, possibly check directory permissions";
	}
	
	imagedestroy($image);
	imagedestroy($imagesmall);
	imagedestroy($watermark);
}

// creates smaller jpg thumbnails from video files, used as image placeholders before videos load into flowplayer
function make_video_thumbnail($videoPath, $videoDirectoryPath) {
   // create a regular sized thumbnail
	//$_SESSION['feedback'][] = shell_exec("include/ffmpeg/ffmpeg -i " . $videoPath . " -ss 1 -f image2 -vframes 1 -s 144x112 " . $videoDirectoryPath . "/thumb.jpg 2>&1");
	shell_exec("../include/ffmpeg/ffmpeg -i " . $videoPath . " -ss 1 -f image2 -vframes 1 -s 144x112 " . $videoDirectoryPath . "/thumb.jpg 2>&1");
   // create a small sized thumbnail
	//$_SESSION['feedback'][] = shell_exec("include/ffmpeg/ffmpeg -i " . $videoPath . " -ss 1 -f image2 -vframes 1 -s 96x74  " . $videoDirectoryPath . "/thumbsmall.jpg 2>&1");
	shell_exec("../include/ffmpeg/ffmpeg -i " . $videoPath . " -ss 1 -f image2 -vframes 1 -s 96x74  " . $videoDirectoryPath . "/thumbsmall.jpg 2>&1");

}


function generateHash($plainText, $salt = null) {
  if ($salt === null) {
    $salt = substr(md5(uniqid(mt_rand(), true)), 0, SALT_LENGTH);
  }
  else {
    $salt = substr($salt, 0, SALT_LENGTH);
  }

  return $salt . sha1($salt . $plainText);

}

function detectMobile() {

  $useragent=$_SERVER['HTTP_USER_AGENT'];
  
  if(preg_match('/android|avantgo|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))) {
    return true;
  }
  else {
    return false;
  }
}

?>

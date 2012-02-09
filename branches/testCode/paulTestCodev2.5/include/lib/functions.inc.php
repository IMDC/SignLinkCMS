<?php

date_default_timezone_set(DEFAULT_TIMEZONE);

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

/**
 *  Returns html-encoded title (image or video or text) - things that have titles: forum, thread, page.
 *
 * @global type $db
 * @global type $filetypes_video
 * @param type $location forum, post, page, vlog, or entry
 * @param type $id the id inside the location, ex) forum id or post id
 * @param type $size size of the title to return, default value is 'reg' and currently only used for video thumbnail sizes
 * @return string 
 */
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
                  if (file_exists($title_path . "title_thumbsmall_play.jpg") ) {
                     $thumbjpg = $title_path . "title_thumbsmall_play.jpg";
                  }
                  else if ( file_exists($title_path . "title_thumbsmall.jpg") ) {
                     $thumbjpg = $title_path . "title_thumbsmall.jpg";
                  }
                  else if (file_exists($title_path . "titlethumbsmall.jpg") ) {
                     $thumbjpg = $title_path . "titlethumbsmall.jpg";
                  }
/*                  else if ( file_exists($title_path . "titlethumb_small_play.jpg") ) {
                     $thumbjpg = $title_path . "titlethumb_small_play.jpg"; 
                  }
                  else if ( file_exists($title_path . "thumbsmall_play.jpg")) {
                     $thumbjpg = $title_path . "thumbsmall_play.jpg";
                  }
                  else if ( file_exists($title_path . "thumb_small_play.jpg")) {
                     $thumbjpg = $title_path . "thumb_small_play.jpg";
                  }
                  else if ( file_exists($title_path . "thumbsmall.jpg") ) {
                     $thumbjpg = $title_path . "thumbsmall.jpg"; 
                  }
                  else if ( file_exists($title_path . "thumb.jpg") ) {
                     $thumbjpg = $title_path . "thumb.jpg";
                  }
                  else if ( file_exists($title_path . "titlethumb.jpg") ) {
                     $thumbjpg = $title_path . "titlethumb.jpg";
                  }
                  else if ( file_exists($title_path . "thumb_play.jpg") ) {
                     $thumbjpg = $title_path . "thumb_play.jpg";
                  }
 */
                  else {
                     $thumbjpg = "images/default_movie_icon_small.png";
                  }
               }
               else {
                  if (file_exists($title_path . "title_thumb_play.jpg")) {
                     $thumbjpg = $title_path . "title_thumb_play.jpg";
                  }
                  else if ( file_exists($title_path . "title_thumb.jpg") ) {
                     $thumbjpg = $title_path . "title_thumb.jpg";
                  }
/*                  else if ( file_exists($title_path . "title_thumb_play.jpg")) {
                     $thumbjpg = $title_path . "title_thumb_play.jpg";
                  }
                  else if (file_exists($title_path . "thumb_play.jpg")) {
                     $thumbjpg = $title_path . "thumb_play.jpg";
                  }
                  else if ( file_exists($title_path . "thumb.jpg") ) {
                     $thumbjpg = $title_path . "thumb.jpg";
                  }*/
                  else {
                     $thumbjpg = "images/default_movie_icon.png";
                  }

               }

            
               /*  May not be needed now as flowplayer supposedly detects iOS or android devices and serves content accordingly
               *  (original notes below)
               * -----
               * If useragent of browser is detected as a mobile device, serve up a simple link instead of flash
               * testing on Android phone plays the video just fine using video's native player
               * NOTE: video encoded with handbrake on iPod settings, m4v file produced
               * 
               if (detectMobile()) {
                  $title = '<video src="' . $title_path . $title_file . '" poster="'.$thumbjpg.'" height="'.$height.'px" width="'.$width.'px" onclick="this.play();" />';
               }
               else {
                  <a  
                      href="'.$title_path.$title_file.'"
                      class = "flash_player_holder"
                      alt="'.$row[1].'"
                      style="border:1px solid #cdcdcd;background-image:url(\''.$thumbjpg.'\');width:'.$width.'px;height:'.$height.'px;margin-left:5px;text-align:center;"
                      id="'.$title_path.'title">
                      <img src="images/play_large.png" style="border:0 none;margin-top:30px;width:40px;height:40px;" />
                  </a>
               }
               */

               $title = '  
               <a href="'.$title_path.$title_file.'"
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
                        
                        /*  This was originally used to show a smaller sized control bar for
                         * video playback on small sized titles, but is not used right now
                         * if ($size == 'small'){
                         *    $title = $title . "controls: conf.small";
                         * } 
                         * else {
                         *    $title = $title . "controls: conf.big";
                         * }
                         */
               $title = $title . '}});</script>';

				} // end of code if ext is in filetypes_video array

            else { // else file is an image
               // An extra div with class "imgzoom_container" is added to each post with an image title to enable lightbox style img zooming
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
      /* This div displays the time and date of the post at the bottom of each title */
      $title = $title . '<div class="cont-date">' . date("H:s M j Y", intval($row[2])) . '</div>';
   }
   @mysqli_free_result($result);
	return $title;
}


/**
 *  Saves uploaded image 
 * 
 * @global type $db
 * @param type $location forum, post or page
 * @param type $type title, drescription, subject, message, contect, etc. - this will be the name of the file when saved
 * @param type $file the file sent through the form
 * @param type $id the id of the forum, post, or page 
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

/**
 * Saves uploaded video
 * 
 * @global type $db
 * @param string $location heading denoting where the file should be saved - ex) forum, post or page
 * @param string $type title, description, subject, message, content, etc. - this will be the name of the file when saved
 * @param string $file the file sent through the form
 * @param type $id id of the forum, post, or page
 * @param string $vidaspect desired aspect ratio (specified as W:H) of the converted video, default value is "4:3"
 * @param string $vidsize desired size (specified as WxH) of the converted video, default value is "320x240"
 */
function save_video($location, $type, $file, $id, $vidaspect="4:3", $vidsize="320x240") {
	global $db;
   $id = intval($id);

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

   if (!@move_uploaded_file($_FILES[$file]['tmp_name'], $newfile)) {
      print "Error Uploading File - check directory permissions.";
      exit;
   }

    /**  TODO: change this to detect the video codec using ffmpeg, as file 
     * extension regular expression check is simply unsafe if this is the
     * only check
     */
    /**   CHANGE THIS TO DETECT THE CODEC USING FFMPEG AS FILE EXTENSION CHECK IS UNSAFE!!! **/
    /**   ************************************************************************************/
    /**  this would be a call to shell_exec("ffmpeg -i INPUTFILE") and parsing the output to find the video codec **/
   
   if ( strcmp(strtolower($ext),  'mp4') != 0 ) { // file is NOT an mp4 file, conversion is necessary

      $extension = end( explode('.', $newfile) );
      $newfileNoExtension = substr( $newfile, 0, (strlen($newfile) - strlen($extension)) );
      $newfileMP4Extension = $newfileNoExtension . 'mp4';

     /** todo: consider using escapeshellarg($dirname) for the paths of files?  **/
      // convert to mp4 using ffmpeg
      //$convertOutput = shell_exec(FFMPEG_PATH . " -i " . $newfile . " -acodec libfaac -ab 64k -ar 22050 -async 22050 -r 15 -aspect 4:3 -s 320x240 -vcodec libx264 -b 400k -flags +loop -cmp +chroma -partitions +parti4x4+partp8x8+partb8x8 -subq 1 -trellis 0 -refs 1 -bf 16 -b_strategy 1 -coder 1 -me_range 16 -g 3 -keyint_min 25 -sc_threshold 40 -i_qfactor 0.71 -bt 175k -qcomp 0.6 -qmin 10 -qmax 51 -qdiff 4 -y " . $newfileMP4Extension . " 2>&1");
      $convertOutput = shell_exec(INCLUDE_PATH . FFMPEG_PATH . " -i " . $newfile . " -acodec libfaac -ab 64k -ar 22050 -async 22050 -r 15 -aspect " . escapeshellarg($vidaspect) . " -s " . escapeshellarg($vidsize) . " -vcodec libx264 -b 400k -flags +loop -cmp +chroma -partitions +parti4x4+partp8x8+partb8x8 -subq 1 -trellis 0 -refs 1 -bf 16 -b_strategy 1 -coder 1 -me_range 16 -g 3 -keyint_min 25 -sc_threshold 40 -i_qfactor 0.71 -bt 175k -qcomp 0.6 -qmin 10 -qmax 51 -qdiff 4 -y " . $newfileMP4Extension . " 2>&1");
	
      // use ffmpeg to make 2 differently sized jpg thumbnails from the uploaded video
      // $_SESSION['feedback'][] = "vid thumbnail call with params: " . $newfile . ", " . dirname($newfile);
      
      $thumbs = make_video_thumbnails($newfile, dirname($newfile), $type);
      overlay_play_btn_jpg($thumbs["small"]);
      overlay_play_btn_jpg($thumbs["large"]);
      
      
//      switch ($type) {
//         case 'title':
//            // create thumbnails with prefix of 'title'
//            $thumbs = make_video_thumbnails($newfile, dirname($newfile), "title");
//            // overlay the play button png image on the thumbnails
////            overlay_play_btn(dirname($newfile));
////            overlay_play_btn($newfile);
//            overlay_play_btn_jpg($thumbs["small"]);
//            overlay_play_btn_jpg($thumbs["large"]);
//            break;
//         
//         case 'message':
//            // create thumbnails with prefix of 'message'
//            $thumbs = make_video_thumbnails($newfile, dirname($newfile), "message");
//            // overlay the play button png image on the thumbnails
////            overlay_play_btn(dirname($newfile));
////            overlay_play_btn($newfile);
//            overlay_play_btn($thumbs["small"]);
//            overlay_play_btn($thumbs["large"]);
//            break;
//         
//         default:
////            $thumbs = make_video_thumbnails($newfile, dirname($newfile));
//            make_video_thumbnails($newfile, dirname($newfile));
////            overlay_play_btn(dirname($newfile));
//            overlay_play_btn($thumbs["small"]);
//            overlay_play_btn($thumbs["large"]);
//            break;
//      }
      //make_video_thumbnails($newfile, dirname($newfile));
      
      /** 
       * TODO: we could replace this overlay_play_btn call with some 'background-image' css on the
       * <a> element in the 'get_title' method. Could be less work server-side
       * and less prone to errors/failing due to incorrectly setup gd extensions
       * Looks like this, code taken from get_title method and flowplayer website
       *          <a  
                      href="'.$title_path.$title_file.'"
                      class = "flash_player_holder"
                      alt="'.$row[1].'"
                      style="border:1px solid #cdcdcd;background-image:url(\''.$thumbjpg.'\');width:'.$width.'px;height:'.$height.'px;margin-left:5px;text-align:center;"
                      id="'.$title_path.'title">
                      <img src="images/play_large.png" style="border:0 none;margin-top:30px;width:40px;height:40px;" />
                  </a>
       * 
       * The problem with using this method is that when we display the full
       * sized video thumbnail (when the video is the main content of the post)
       * the background-image thumbnail technique will tile the small sized
       * thumbnail and the play button will not be centered. This could be 
       * worked out with more work on the code for the play button style
       *  but for now we default by calling overlay_play_btn
       */
     //overlay_play_btn(dirname($newfile));

      // TODO: sanity checks to see if thumbnails etc were created successfully
      
      /* assuming everything went okay, we can delete the old (.avi ?) file that we don't need now */
      if(file_exists($newfile)) {
         unlink($newfile);
      }
   }
   else { // file IS a mp4 file, no conversion necessary
      // creates the 'thumb.jpg' thumbnail from the first second of the video uploaded
      $thumbs = make_video_thumbnails($newfile, dirname($newfile), $type);
      overlay_play_btn_jpg($thumbs["small"]);
      overlay_play_btn_jpg($thumbs["large"]);
      
//      switch ($type) {
//         case 'title':
//            // create thumbnails with prefix of 'title'
////            make_video_thumbnails($newfile, dirname($newfile), "title");
//            $thumbs = make_video_thumbnails($newfile, dirname($newfile), "title");
////            overlay_play_btn_jpg(dirname($newfile) . "titlethumb.jpg");;
//            overlay_play_btn_jpg($thumbs["small"]);
//            overlay_play_btn_jpg($thumbs["large"]);
//            break;
//         case 'message':
//            // create thumbnails with prefix of 'message'
//            make_video_thumbnails($newfile, dirname($newfile), "message");
//            overlay_play_btn_jpg($newfile);
//            break;
//         default:
//            make_video_thumbnails($newfile, dirname($newfile));
//            overlay_play_btn(dirname($newfile));
//            break;
//      }
      
      //make_video_thumbnails($newfile, dirname($newfile));
      
      // adds the transparent png play button to the thumbnail
      //overlay_play_btn(dirname($newfile));
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
     trigger_error("Error in the save_signlink function call, check directory permissions", E_USER_WARNING);
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
	global $db, $filetypes_image;
		
	$tmp_file = $_FILES['avatar']['tmp_name'];
	$ext = strtolower(end(explode('.',$_FILES['avatar']['name'])));
   trigger_error("extension from save_avatar is: " . $ext, E_USER_WARNING);

   $allowable=0;
   // add this code to check if filetype of image uploaded is allowed
   // by the $filetypes_image variable defined in constants.inc.php
   foreach($filetypes_image as $goodimgext) {
      $goodimgextlower = strtolower($goodimgext);
      if($goodimgextlower==$ext) {
         // filetype is allowed, continue processing
         $allowable=1;
         break;
      }
   }
   
   if ($allowable != 1) {
      trigger_error("***SIGNLINKCMS ERROR***: Extension of file uploaded for user avatar is not permitted", E_USER_WARNING);
      return;
   }
   
	$level = '';
	$depth = substr_count(INCLUDE_PATH, '/');
	for ($i=1; $i<$depth; $i++) {
		$level .= "../";
	}
	
	if(!file_exists($level.UPLOAD_DIR.'members/'.$id.'/')) {
		mkdir($level.UPLOAD_DIR.'members/'.$id.'/');
	} else { 
		delete_avatar($id);
      clearstatcache();
      mkdir($level.UPLOAD_DIR.'members/'.$id.'/');
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

		if ($ext=='jpg' || $ext=='jpeg') { 
			$smaller = imagecreatetruecolor($newwidth, $newheight) or die('Cannot Initialize new GD image sream');	
			$source = imagecreatefromjpeg($tmp_file); 
		}
      elseif ($ext == "gif") {
			$smaller = imagecreate($newwidth, $newheight);
			$source = imagecreatefromgif($tmp_file);
		}
      elseif ($ext == 'png') {
			$smaller = imagecreatetruecolor($newwidth, $newheight);
			$source = imagecreatefrompng($tmp_file);
		}

		if (!imagecopyresized($smaller, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height)) {
			$_SESSION['errors'][] = "Error performing file conversion, please try again later.";
         trigger_error("Problem creating user avatar after upload, check directory permissions or picture file size", E_USER_WARNING);
			//exit();
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
	  $_SESSION['errors'][] = "Error Uploading File. Please try again later";
     trigger_error("Error in copying user avatar to uploads directory, check permissions", E_USER_WARNING);
	  //exit;
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


/**
 * Using member id, locates avatar file inside uploads/members/ directory and returns path
 * @global type $db reference to the database connection
 * @param type $id the member_id of the user
 * @param type $loginname the name to display in the alternative text of the avatar image
 * @return type 
 * 
 */
function get_avatar($id, $loginname="") {
	global $db;
	$id = intval($id);
   
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

   if (isset($_SESSION['login'])) {
      $alttext = $_SESSION['login'];
   }
   else if (!empty($loginname)) {
      $alttext = $loginname;
   }
   else {
      $alttext = "user";
   }
   $alttext .= "'s avatar";
   
	if ($av_file) {
		echo '<img id="avatar" src="uploads/members/'.$id.'/'.$av_file.'" alt="'.$alttext . '" />';
	}
   else {
		echo '<img id="avatar" src="images/no_avatar.jpg" alt="User has not selected an avatar" />';
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

/**
 * Increases the value of the users 'post_count' column in the members db table
 * by one.
 *
 * @global type $db the mysqli database connection
 * @param int $member_id the member_id of the user to get +1 to post_count
 * @param int $increase_by the number of posts to add, default is one
 */
function updateMemberPostCount($member_id, $increase_by=1) {
   global $db;
   $member_id = intval($member_id);
   $increase_by = intval($increase_by);
   $sql = "UPDATE members SET post_count=post_count+".$increase_by." WHERE member_id=".$member_id;
   if (!$result = mysqli_query($db, $sql)) {
      trigger_error('User post count not updated, member id:' . $member_id, E_USER_WARNING);
      $_SESSION['feedback'][] = 'Warning, post count not updated, please contact your administrator';
   }
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
			if ( (substr($dir_file,0,5) == substr($type,0,5)) || (substr($dir_file,0,5) == "thumb") || (substr($dir_file,-3,3) =="flv")) {
				unlink($path.$dir_file); 
			}
		}
	}	
	return;
}


/*
* location: directory in /uploads - forums, members, pages, or posts
* id
*/

function delete_folder($location, $id) {
        global $db;
        
        $level = '';
	$depth = substr_count(INCLUDE_PATH, '/');
	for ($i=1; $i<$depth; $i++) {
		$level .= "../";
	}
	
	$path = $level.UPLOAD_DIR.$location.'/'.$id.'/';
        
        if (file_exists($path)) {
            //delete files
            $dir_files = @scandir($path);		
            foreach ($dir_files as $dir_file) {
                if(!is_dir($dir_file))
                    unlink($path.$dir_file); 
            }
            //delete directory
            rmdir($path);
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

/**
 * Duplicate of overlay_play_btn method that will accept a single .jpg file reference as a string
 *
 * @param string $jpgFile the full path to the jpg thumbnail file that will have a play button image overlayed on to it
 */
function overlay_play_btn_jpg($jpgFile) {

	//make sure destination image full path includes the slash at the end
	$pathLength = strlen($jpgFile);
	if ($pathLength == 0) {
		$_SESSION['errors'][] = 'Error with filename passed to overlay_play_btn method while creating play button overlay';
      trigger_error("Error with filename passed to overlay_play_btn method while creating play button overlay. Filename: " . $jpgFile, E_USER_WARNING);
	}
	
   // check extension of file argument
	if ( strtolower(end(explode('.', $jpgFile))) != 'jpg' ) {
      $_SESSION['errors'][] = 'Error with filename passed to overlay_play_btn method while creating play button overlay';
      trigger_error("Error with filename passed to overlay_play_btn method while creating play button overlay. Filename: " . $jpgFile, E_USER_WARNING);
	}
	
   
	// use the image file reference
	$image = imagecreatefromjpeg($jpgFile);
	
	if ( !$image ) {
		$_SESSION['errors'][] = 'Generated video thumbnail (regular size) image not found, please ' . printAdminMailToLink("notify", "error") . ' your administrator';
      trigger_error("User video thumbnail image not found while creating play button overlay. File path: " . $jpgFile . "thumb.jpg", E_USER_WARNING);
	}

	// to change the play button overlay image, change this value (PLAYOVERLAY_IMAGE) in the config.inc.php file
   if (!defined(IMAGE_FOLDER)) {
      $level = '';
      $depth = substr_count(INCLUDE_PATH, '/');
      for ($i=1; $i<$depth; $i++) {
         $level .= "../";
      }

      $path = $level.'images/';
      $pathToDefImage = $path . PLAYOVERLAY_IMAGE;
   }
   else {
      $pathToDefImage = IMAGE_FOLDER . PLAYOVERLAY_IMAGE;
   }
   
   $watermark = imagecreatefrompng($pathToDefImage);
   //$_SESSION['feedback'][] = "Path printed from overlay_play_btn: " . $pathToDefImage;
	
	if ( !$watermark ) {
		$_SESSION['errors'][] = '<span style="color:#ff0000;size:1.4em;">Default play button overlay image not found, please notify your administrator</span>';
	}
	
	imagealphablending($image, true);
//	imagealphablending($imagesmall, true);
	imagealphablending($watermark, true);
	
	// render play button .png file on top of thumb.jpg file
	imagecopy($image,      $watermark, imagesx($image)/2-22,      imagesy($image)/2-22,      0, 0, imagesx($watermark), imagesy($watermark));
//	imagecopy($imagesmall, $watermark, imagesx($imagesmall)/2-22, imagesy($imagesmall)/2-22, 0, 0, imagesx($watermark), imagesy($watermark)); 

   // strip the .jpg ending from the original thumbnail filename so
   // we can add the _play suffix
	$newThumbFilename = str_replace(".jpg", "", $jpgFile);
   
	// write the new thumbnail in the source folder with _play as suffix
	if ( !imagejpeg($image, $newThumbFilename . '_play.jpg') ) {
		$_SESSION['errors'][] = 'Error creating new thumbnail, check dir permissions';
      //print "\n**ERROR** - Error creating new thumbnail jpeg file, possibly check directory permissions";
	}
//	if ( !imagejpeg($imagesmall, $jpgFile . 'thumb_small_play.jpg') ) {
//      $_SESSION['errors'][] = 'Error creating new thumbnail, check dir permissions';
//		//print "\n**ERROR** - Error creating new thumbnail jpeg file, possibly check directory permissions";
//	}
	
	imagedestroy($image);
//	imagedestroy($imagesmall);
	imagedestroy($watermark);
}

/**
 *  Uses the gd image library to overlay a play button image (defined by PLAYOVERLAY_IMAGE)
 * onto the jpg thumbnail files generated by the make_video_thumbnail function
 *
 * @param string $fullDestImagePath  the full path to the directory where the video thumbnail image is located
 * including the trailing '/'
 */
function overlay_play_btn($fullDestImagePath) {

	// make sure destination image full path includes the slash at the end
	$pathLength = strlen($fullDestImagePath);
	if ($pathLength == 0) {
		$_SESSION['errors'][] = 'Error with filename passed to overlay_play_btn method while creating play button overlay';
      trigger_error("Error with filename passed to overlay_play_btn method while creating play button overlay. Filename: " . $fullDestImagePath, E_USER_WARNING);
	}
	
	if ( substr($fullDestImagePath, -1) !== '/' ) {
		// add the slash because it's missing
		$fullDestImagePath = $fullDestImagePath . '/';
	}
	
	// use the thumbnails created during upload
	$image = imagecreatefromjpeg($fullDestImagePath . 'thumb.jpg');
	$imagesmall = imagecreatefromjpeg($fullDestImagePath . 'thumbsmall.jpg');
	
	if ( !$image ) {
		$_SESSION['errors'][] = 'Generated video thumbnail (regular size) image not found, please ' . printAdminMailToLink("notify", "error") . ' your administrator';
      trigger_error("User video thumbnail image not found while creating play button overlay. File path: " . $fullDestImagePath . "thumb.jpg", E_USER_WARNING);
	}
	if ( !$imagesmall ) {
		$_SESSION['errors'][] = 'Generated video thumbnail (small size) image not found, please ' . printAdminMailToLink("notify", "error") . ' your administrator';
      trigger_error("User video thumbnail image not found while creating play button overlay. File path: " . $fullDestImagePath . "thumbsmall.jpg", E_USER_WARNING);
	}
	
	
	// to change the play button overlay image, change this value (PLAYOVERLAY_IMAGE) in the config.inc.php file
   if (!defined(IMAGE_FOLDER)) {
      $level = '';
      $depth = substr_count(INCLUDE_PATH, '/');
      for ($i=1; $i<$depth; $i++) {
         $level .= "../";
      }

      $path = $level.'images/';
      $pathToDefImage = $path . PLAYOVERLAY_IMAGE;
   }
   else {
      $pathToDefImage = IMAGE_FOLDER . PLAYOVERLAY_IMAGE;
   }
   
   $watermark = imagecreatefrompng($pathToDefImage);
   //$_SESSION['feedback'][] = "Path printed from overlay_play_btn: " . $pathToDefImage;
	
	if ( !$watermark ) {
		$_SESSION['errors'][] = '<span style="color:#ff0000;size:1.4em;">Default play button overlay image not found, please notify your administrator</span>';
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
      //print "\n**ERROR** - Error creating new thumbnail jpeg file, possibly check directory permissions";
	}
	if ( !imagejpeg($imagesmall, $fullDestImagePath . 'thumb_small_play.jpg') ) {
      $_SESSION['errors'][] = 'Error creating new thumbnail, check dir permissions';
		//print "\n**ERROR** - Error creating new thumbnail jpeg file, possibly check directory permissions";
	}
	
	imagedestroy($image);
	imagedestroy($imagesmall);
	imagedestroy($watermark);
}

/**
 * Uses the ffmpeg binary and a call to shell_exec to create a video thumbnail
 * for a supplied movie file. Creates a large sized thumbnail named 'thumb.jpg'
 * and a smaller thumbnail named 'thumbsmall.jpg' from the image in the video
 * specified by the timecode paramater
 *
 * @param string $videoPath  the direct and complete path to a video file
 * @param string $videoDirectoryPath  the directory where you want the thumbnails created
 * @param string $thumbpreix  the file name prefix you want before 'thumb.jpg' and 'thumbsmall.jpg', used to differentiate between title and message thumbnails
 * @param string $largesize  the size of the large thumbnail desired, specified as "WxH" ex) "144x112", defaults to 144x112
 * @param string $smallsize  the size of the small thumbnail desired, specified as "WxH" ex) "96x74", defaults to 96x74
 * @param int $timecode  the time in seconds that the thumbnail image should be taken from the video, default value is 1 (second)
 * 
 * @return array  an associative array containing both the 'small' and 'large' filenames that were just created
 */
function make_video_thumbnails($videoPath, $videoDirectoryPath, $thumbprefix="", $largesize="144x112", $smallsize="96x74", $timecode=1) {

   $dirpath = escapeshellarg($videoDirectoryPath);
   $small = $dirpath . "/" . $thumbprefix . "_thumbsmall.jpg";
   $large = $dirpath . "/" . $thumbprefix . "_thumb.jpg";
   
   
   
   /* TODO: perform a check to ensure the movie duration is longer or the same length as the timecode value supplied so no errors occur from ffmpeg */
   
   // create a regular sized thumbnail
//   $stringToExecuteRegular = INCLUDE_PATH . FFMPEG_PATH . " -i " . escapeshellarg($videoPath) . " -ss " . escapeshellarg(intval($timecode)) . " -f image2 -vframes 1 -s " . escapeshellarg($largesize) . " " . escapeshellarg($videoDirectoryPath) . "/" . $thumbprefix . "thumb.jpg 2>&1";
   $stringToExecuteRegular = INCLUDE_PATH . FFMPEG_PATH . " -i " . escapeshellarg($videoPath) . " -ss " . escapeshellarg(intval($timecode)) . " -f image2 -vframes 1 -s " . escapeshellarg($largesize) . " " . $large . " 2>&1";
   //$_SESSION['feedback'][] = $stringToExecuteRegular;
   shell_exec($stringToExecuteRegular);
   
   // create a small sized thumbnail
   $stringToExecuteSmall = INCLUDE_PATH . FFMPEG_PATH . " -i " . escapeshellarg($videoPath) . " -ss " . escapeshellarg(intval($timecode)) . " -f image2 -vframes 1 -s " . escapeshellarg($smallsize) . " " . $small . " 2>&1";
   //$_SESSION['feedback'][] = $stringToExecuteSmall;
   shell_exec($stringToExecuteSmall);
   
   // strip the quotes that are present in the directory path thanks to escapeshellarg
   $createdFilenameArray = array("small" => str_replace("'", "", $small), "large" => str_replace("'", "", $large));
   return $createdFilenameArray;
   
}

/**
 * Currently un-used
 *
 * @param type $plainText
 * @param type $salt
 * @return type 
 */
function generateHash($plainText, $salt = null) {
  if ($salt === null) {
    $salt = substr(md5(uniqid(mt_rand(), true)), 0, SALT_LENGTH);
  }
  else {
    $salt = substr($salt, 0, SALT_LENGTH);
  }

  return $salt . sha1($salt . $plainText);

}

/**
 * Tries to match the user agent of a browser to common mobile ones
 *
 * @return boolean returns true if user agent of browser matches commonly
 * used and known mobile browser user agents
 */
function detectMobile() {

  $useragent=$_SERVER['HTTP_USER_AGENT'];
  
  if(preg_match('/android|avantgo|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))) {
    return true;
  }
  else {
    return false;
  }
}

/**
 * Performs a regular expression validation on an email address type string.
 * By default the function will return false.
 * NOTE: This will return false even if email is valid according to regular expression
 * if unique parameter is set to 1 and the email already exists in the members 
 * table in the database
 * 
 * @param type $email The complete email address in a string format
 * @param type $unique Set to 0 if duplicate emails are allowed, 1 to enforce unique email in the members table in the database, default value is 1
 * @return false if email is not correctly formatted or duplicated in table
 * @return true if email is correctly formatted and unique in members table
 */
function validateEmail($email, $unique=1) {
   global $db;
   $reg_ok = 0;
   
   // enforce integer only for this parameter
   $unique = intval($unique);
   
   // remove whitespace and escape nasty chars
   $email = trim(mysqli_real_escape_string($db,$email));
   // check length make sure it is valid, set flag if valid
   if (eregi("^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,6}$", $email)) {
      $reg_ok = 1;
   }
   else {
      return false;
   }
   
   // do we need to check if email is unique?
   if ($unique > 0) {
      // check that email is unique in database
      $query = "SELECT * FROM members WHERE email='" . $email . "'";
      $result = mysqli_query($db, $query);
      if (mysqli_num_rows($result) == 0) {
         mysqli_free_result($result);
         return true;
      }
      else {
         mysqli_free_result();
         return false;
      }
   }
   else {
      if ($reg_ok == 1) return true;
      else return false;
   }
}


/**
 * Performs a regular expression match on the supplied string to enforce
 * conformity to the 'login' field from the members table in the database
 *
 * @global type $db
 * @param type $username  the desired user's login
 * @return boolean true if user login conforms to regex, false if not
 */
function validateUserLogin($username) {
   global $db;
   
   $username = mysqli_real_escape_string($db, trim($username));
   
   if (preg_match("/^[a-zA-Z]{3,}[a-zA-Z_1234567890]*[a-zA-Z1234567890]{1,}$", $username) > 0) {
      return true;
   }
   else {
      return false;
   }
}

/**
 * Tests whether it is possible to access the FFmpeg binary using the
 * predefined path in the 'config.inc.php' file using a regular expression
 * match against the output of just calling ffmpeg with no parameters, looks
 * specifically for the libx264 codec to be compiled with ffmpeg
 * @return true if access and libx264 library present
 * @return false if no access or no libx264 library present
 */
function ffmpeg_access_enabled() {
   $output = shell_exec(INCLUDE_PATH . FFMPEG_PATH . " -version");
   if (preg_match("/libavutil/i", $output) > 0)
      return true;
   else
      return false;
}


/**
 * Test function to determine if write permissions are granted for the userid
 * that runs PHP. Tries to create a new hidden directory in the members folder
 * and removes it upon success
 * @return true if directory is created successfully
 * @return false if directory creation is not permitted or unsuccessful
 */
function directory_write_permission_enabled() {
   // directory write access
   $level = '';
   $depth = substr_count(INCLUDE_PATH, '/');
   for ($i=1; $i<$depth; $i++) {
      $level .= "../";
   }
   $testdir = $level.UPLOAD_DIR.'members/.accesstestfolder/';

   if (mkdir($testdir)) {
      @rmdir($testdir);
      return true;
   }
   else 
      return false;
}

/**
 * Test function to determine if the GD image library has been loaded as an
 * extension to PHP. The GD library is used to manipulate images such as user
 * avatars
 * @return true if the GD library is present
 * @return false if PHP does not have the GD library installed as an extension
 */
function gd_library_present() {
   if (extension_loaded('gd') && function_exists('gd_info'))
      return true;
   else
      return false;
}


/**
 * Test function to determine if the creation of a new image GD object is 
 * possible using the admin determined PLAYOVERLAY_IMAGE variable. If the
 * variable truly points to a valid image and GD is present than this tests 
 * will be successfuly
 * @return true if GD is working and PLAYOVERLAY_IMAGE points to an image
 * @return false if GD is not working or the PLAYOVERLAY_IMAGE does not point to an image
 */
function playbutton_overlay_config_successful() {
	if (!defined(IMAGE_FOLDER)) {
      $level = '';
      $depth = substr_count(INCLUDE_PATH, '/');
      for ($i=1; $i<$depth; $i++) {
         $level .= "../";
      }

      $path = $level.'images/';
      $pathToDefImage = $path . PLAYOVERLAY_IMAGE;
   }
   else {
      $pathToDefImage = IMAGE_FOLDER . PLAYOVERLAY_IMAGE;
   }

   $watermark = imagecreatefrompng($pathToDefImage);
   if ($watermark)
      return true;
   else
      return false;
}


/**
 * Method to recursively traverse the directory specified by the UPLOAD_DIR
 * define filtered by file extensions specified in the extensions array 
 * 
 * @param array $extensions array of file extension strings, ex array('mp4','mov')
 * @param string $function Optional - the name of an optional function to be called on each image
 * file. The function is called using the image pathname string as the first parameter, default value is NULL
 * @param array $functionparams Optional - an array of parameters to pass to the optional $function as the second parameter
 * @return array An array of file paths, array(0=>filepath1,1=>filepath2...)
 */
function searchUploadedFiles($extensions, $function=NULL, $functionparams=NULL) {
   $vidfilesfound = array();
   
   $level = '';
   $depth = substr_count(INCLUDE_PATH, '/');
   for ($i=1; $i<$depth; $i++) {
      $level .= "../";
   }
   
   $updir = $level.UPLOAD_DIR;
   
   $dir = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(($updir), true));
   
   foreach ( $dir as $file ) {
      if ($file->isFile()) {
         $ext = strtolower(array_pop(explode('.', $file)));
         foreach ($extensions as $ext_str) {
            $ext_str = strtolower($ext_str);
            if ($ext == $ext_str) {
               array_push($vidfilesfound, $file->getPathname());
               //print "Video file found: " . $file->getPathname() . "<br />";
               if ($function) {
                  $function($file->getPathname(), $functionparams);
               }
               break;
            }
         }
      }
   }
   return $vidfilesfound;
}


/**
 * Creates a mailto link with supplied text and css class by pulling the 
 * admin's contact info from the settings table in the database
 *
 * @global type $db
 * @param string $linktext The desired text to display for the mail to link
 * @param string $cssclass The desired class to be applied to the link
 * 
 * @return string the complete html code for a mailto anchor to the admin
 */
function printAdminMailToLink($linktext, $cssclass) {
   global $db;
   $linktext = mysqli_real_escape_string($db, $linktext);
   $cssclass = mysqli_real_escape_string($db, $cssclass);
   
   $sql = "SELECT value FROM settings where name='contact' LIMIT 1";
   $result = mysqli_query($db, $sql);
   
   $row = mysqli_fetch_row($result);
   if (!empty($row[0])) {
      $returnString = "<a href='mailto:" . $row[0] . "' class='" . $cssclass . "'>" . $linktext . "</a>";
      return $returnString;
   }
}

/**
 * This functions takes the member_id and the login name of a registered
 * user and prints a div hierarchy to contain the user's avatar,
 * and some info about the user including their join date, their status,
 * and their post count
 *
 * @param int $member_id  the member_id of the user
 * @param string $user_login  the login name of the user
 * @return string  the html output for the reply-area avatar
 */
function printUserAvatarDiv($member_id) {
   global $db;
   $member_id = intval($member_id);
   $sql = "SELECT member_id, login, created_ts, status, post_count FROM members where member_id=" . $member_id . " LIMIT 1";
   $result = mysqli_query($db, $sql);
   
   $row = mysqli_fetch_row($result);
   if (!empty($row)) {
      $output += '<div class="reply-avatar-container">';
      $output += '<div style="height:90%;text-align:center;">';
      $output += '<div style="text-align:center;">'.$row['login'].'</div>';
      $output += '<div style="">'.get_avatar($row['member_id'], $row['login']).'</div>';
      $output +=  '<div style="text-align:center;font-size:0.8em;">Member since ' . 
                     date('M j Y, h:ia', strtotime($row['created_ts'])) . 
                  '</div>';
/*      if ($_SESSION['login'] == $row['login'] && $row['locked'] != 1) {
         $output +=  "<div class='post-reply-tools'>";
         $output +=     "<li style='display:inline;padding:8px;'><a href='forum_post_edit.php?f=" . $row['forum_id']."&p=".$row['post_id']."&parent=".$row['parent_id']."'><img src='images/comment_edit.png' alt='Edit' title='Edit' /></a></li>";
         $output +=     "<li style='display:inline;padding:8xp;'><a href='forum_post_delete.php?f=".$row['forum_id']."&p=".$row['post_id']."&parent=".$row['parent_id']."&m=".$_SESSION['member_id']."'><img src='images/comment_delete.png' alt='Delete' title='Delete' /></a></li>";
         $output +=  "</div>";
      }
*/	
      $output += '<div class="user-status-div">';
//      if 
      $output +=  '</div>';
      $output +=  '</div>';
      $output +=  '</div>';
  
      return $output;
   }
   else {
      $output = '<div class="reply-avatar-container">';
      $output += '<span style="font-color:red;">User not found</span>';
      $output += '</div>';
      return $output;
   }
}

/**
 * Fixes paths and script references to media (including images & videos) that
 * are incorrectly referenced from the ADMIN section of the site. Uses regular
 * expressions and replaces ids referencing "../uploads/" with "uploads/",
 * flowplayer path reference from "flash/flowplayer" to "../flash/flowplayer", 
 * "images/" with "../images/". NOTE: this does not change the href attribute
 * of the anchor element containing the absolute path to movies in the 
 * "../uploads/" directory, only the ID of the anchor element - flowplayer does
 * not like id's that start with "../" 
 * @param string $input  the html input, usually the output of a call to get_title
 * or get_message from functions.inc.php or forums.inc.php
 * @return string the same html but with paths and id references changed
 */
function adminMediaPathFix($input) {
   
   $adminfixpatterns = array();
      $adminfixpatterns[0] = '/id="..\/uploads\/posts/';
      $adminfixpatterns[1] = '/id="..\/uploads\/pages/';
      $adminfixpatterns[2] = '/id="..\/uploads\/vlogs/';
      $adminfixpatterns[3] = '/flowplayer\("..\/uploads\/posts/';
      $adminfixpatterns[4] = '/flowplayer\("..\/uploads\/pages/';
      $adminfixpatterns[5] = '/flowplayer\("..\/uploads\/vlogs/';
      $adminfixpatterns[6] = '/"flash\/flowplayer/';
      $adminfixpatterns[7] = '/images\/post-deleted/';
      $adminfixpatterns[8] = '/img class="quickView" src="images/';
   $adminfixreplacements = array();
      $adminfixreplacements[0] = 'id="uploads/posts';
      $adminfixreplacements[1] = 'id="uploads/pages';
      $adminfixreplacements[2] = 'id="uploads/vlogs';
      $adminfixreplacements[3] = 'flowplayer("uploads/posts';
      $adminfixreplacements[4] = 'flowplayer("uploads/pages';
      $adminfixreplacements[5] = 'flowplayer("uploads/vlogs';
      $adminfixreplacements[6] = '"../flash/flowplayer';
      $adminfixreplacements[7] = '../images/post-deleted';
      $adminfixreplacements[8] = 'img class="quickView" src="../images';
            
      return preg_replace($adminfixpatterns, $adminfixreplacements, $input);
}

/**
 * A method to quickly determine a PHP installation's maximum filesize variable.
 * Checks 3 values and returns the smallest
 *
 * @return int the maximum upload value determined by php.ini in megabytes
 */
function get_maximum_php_installation_file_upload_size_mb() {
   $max_upload = (int)(ini_get('upload_max_filesize'));
   $max_post = (int)(ini_get('post_max_size'));
   $memory_limit = (int)(ini_get('memory_limit'));
   $upload_mb = min($max_upload, $max_post, $memory_limit);
   
   return intval($upload_mb);
}


/**
 * Returns the value of the max_upload_size field in the settings database table
 * This setting is set and controlled by the site administrator and can be changed in the
 * admin settings interface.  This value is not a hard-limit, as a file upload size
 * can also be limited by the PHP installation value. Use 
 * 'get_maximum_php_installation_file_upload_size' to determine the hard limit 
 * as set by the PHP installation.
 * @global type $db 
 * @return int the maximum upload size in bytes, or 0 if there was an error
 */
function get_maximum_admin_setting_file_upload_size_mb() {
   global $db;
   
   $sql = "SELECT value as bits FROM settings where name='max_upload_size' LIMIT 1";
   $result = mysqli_query($db, $sql);
   
   $row = mysqli_fetch_assoc($result);
   if (!empty($row)) {
      return intval(floor($row['bits']/1024/1024));
   }
   else {
      return -1;
   }
   
}

/**
 * Convenience function to determine the maximum size of file upload
 * the site will allow. Does this by comparing the max size of the PHP
 * installation's limit as set in PHP.ini and the setting controlled by the
 * site admin and stored in the settings table. Logically returns the min of
 * these two values as they are co-dependent. 
 * 
 * @return int the maximum file upload size in bytes
 */
function get_maximum_file_upload_size_overall_mb() {
   $max_install_size = get_maximum_php_installation_file_upload_size_mb();
   $max_admin_size = get_maximum_admin_setting_file_upload_size_mb();
   
   return min($max_install_size, $max_admin_size);
}
   
?>

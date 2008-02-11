<?php

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
function get_title($location, $id) {			
	
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
			break;
	}

	$result = mysql_query($sql, $db);
	if ($result) {
		$row = mysql_fetch_row($result);

		if (!empty($row[0])) {
			//the title is plain text
			$title = $row[0];
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

				if (in_array($ext, $filetypes_video)) {
					$title = '<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B"
					id="clip" width="BLOCK_WIDTH" height="113" codebase="http://www.apple.com/qtactivex/qtplugin.cab">
						<param name="src" value="'.$title_path.$title_file.'"/>
						<param name="autoplay" value="false"/>
						<param name="controller" value="true"/>
						<param name="scale" value="tofit"/>
						<embed src="'.$title_path.$title_file.'" width="145" height="109" name="clip"
						autoplay="false" controller="true" enablejavascript="true" scale="tofit"
						alt="Quicktime ASL video"
						pluginspage="http://www.apple.com/quicktime/download/"
						style="float:left;" />
					</object>';
				} else {
					$title = '<img src="'.$title_path.$title_file.'" alt="'.$row[0].'" title="'.$row[0].'" style="vertical-align:middle;" />';
				}
			}
		}
	}
	return $title;
}

function get_message($id) {			
	global $db, $filetypes_video, $filetypes_image;
	
	$level = '';
	$depth = substr_count(INCLUDE_PATH, '/');
	for ($i=1; $i<$depth; $i++) {
		$level .= "../";
	}

	$msg_path = $level.'uploads/posts/'.$id.'/';
	$sql = "SELECT login, date, msg, msg_alt FROM forums_posts WHERE post_id=".$id;

	$result = mysql_query($sql, $db);
	if ($result) {
		$msg = array();

		if (!$row = mysql_fetch_assoc($result)) {
			$msg[0] = '';
			$msg[1] = '';
			$msg[2] = "No message.";
			return $msg;
		}		

		$msg[0] = $row['login'];
		$msg[1] = date('h:ia M j, y', strtotime($row['date']));

		if (!empty($row['msg'])) {
			//the msg is plain text
			$msg[2] = $row['msg'];
		} else {
			//the msg is a file
			
			//get files
			$dir_files = @scandir($msg_path);

			if(!empty($dir_files)) {
				foreach ($dir_files as $dir_file) {
					if (substr($dir_file,0, 7) == "message") {
						$msg_file = $dir_file;
						break;
					}
				}

				$ext = end(explode('.',$msg_file));
				if (in_array($ext, $filetypes_video)) {
					$msg[2] = '<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B"
					id="clip" width="BLOCK_WIDTH" height="113" codebase="http://www.apple.com/qtactivex/qtplugin.cab">
						<param name="src" value="'.$msg_path.$msg_file.'"/>
						<param name="autoplay" value="false"/>
						<param name="controller" value="true"/>
						<param name="scale" value="tofit"/>
						<embed src="'.$msg_path.$msg_file.'" width="BLOCK_WIDTH" height="113" name="clip"
						autoplay="false" controller="true" enablejavascript="true" scale="tofit"
						alt="Quicktime ASL video"
						pluginspage="http://www.apple.com/quicktime/download/"
						style="float:left;" />
					</object>';
				} else if (in_array($ext, $filetypes_image)) {
					$msg[2] = '<img src="'.$msg_path.$msg_file.'" alt="'.$row[1].'" title="'.$row[1].'" style="vertical-align:middle;" />';
				} else { //signlink
					$msg[2] = '<object width="565" height="415"
						classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"
						codebase="http://fpdownload.macromedia.com/pub/
						shockwave/cabs/flash/swflash.cab#version=8,0,0,0">
						<param name="movie" value="'.$msg_path.$msg_file.'"/>
						<param name="autoplay" value="false"/>
						<embed src="'.$msg_path.$msg_file.'" width="565" height="415"
						type="application/x-shockwave-flash" pluginspage=
						"http://www.macromedia.com/go/getflashplayer" />
					</object>';		
				}
			}
		}
		return $msg;
	} else {
		echo 'No message.';
		return;
	}

}


function print_reply_link($id) {	
	global $db, $filetypes_video, $filetypes_image;

	$sql = "SELECT forum_id, login, date, msg, msg_alt FROM forums_posts WHERE post_id=".$id;
	$result = mysql_query($sql, $db);
	if ($result) {
		if (!$row = mysql_fetch_assoc($result)) {
			echo 'No message.';
			return;
		}		

		if (!empty($row['msg'])) {
			//the msg is plain text
			$link = substr($row['msg'],0,30).'...';
		} else {
			//the msg is a file
			$level = '';
			$depth = substr_count(INCLUDE_PATH, '/');
			for ($i=1; $i<$depth; $i++) {
				$level .= "../";
			}
			
			//get files
			$dir_files = @scandir($level.'uploads/posts/'.$id.'/');

			//pick out the "message" file and check its extension
			if (!empty($dir_files)) {
				foreach ($dir_files as $dir_file) {
					if (substr($dir_file,0, 7) == "message") {
						$msg_file = $dir_file;
						break;
					}
				}
				$ext = end(explode('.',$msg_file));
				if (in_array($ext, $filetypes_video)) {
					$link = '<img src="images/film.png" alt="movie content" style="border:0px;" />';
				} else if ($ext=="swf") {
					$link = '<img src="images/television.png" alt="signlink content" style="border:0px;" />';
				}
			}
		}
		echo '<td><a href="forum_post_view.php?f='.$row['forum_id'].'&p='.$id.'">'.$link.'</a></td>';
		echo '<td style="text-align:center;">'.$row['login'].'</td>';
	}
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

		if ($ext == "jpg" or $ext=='jpeg') {
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
			break;
	}

	if (!move_uploaded_file($_FILES[$file]['tmp_name'], $newfile)) {
	  print "Error Uploading File.";
	  exit;
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

	if ($ext == "flv") {
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
			break;
	}

	if (!move_uploaded_file($_FILES[$file]['tmp_name'], $newfile)) {
	  print "Error Uploading File.";
	  exit;
	} 
}

function check_upload($varname) {

	switch ($_FILES[$varname]['error']) {  
		case 1:
			   $_SESSION['errors'][] = 'The file is bigger than this PHP installation allows';
			   break;
		case 2:
			   $_SESSION['errors'][] = 'The file is bigger than this form allows';
			   break;
		case 3:
			   $_SESSION['errors'][] = 'Only part of the file was uploaded';
			   break;
		case 4:
			   $_SESSION['errors'][] = 'No file was uploaded';
			   break;
	}
	return;
}


?>
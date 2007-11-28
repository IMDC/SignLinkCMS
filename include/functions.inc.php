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
function get_title($type, $id) {			
	
	global $db, $filetypes_video;
	
	$level = '';
	$depth = substr_count(INCLUDE_PATH, '/');
	for ($i=1; $i<$depth; $i++) {
		$level .= "../";
	}

	switch ($type) {
		case 'forum':
			$sql = "SELECT title FROM forums WHERE forum_id=".$id;
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
			$dir_files = scandir($title_path);

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
					id="clip" width="150" height="113" codebase="http://www.apple.com/qtactivex/qtplugin.cab">
						<param name="src" value="'.$title_path.$title_file.'"/>
						<param name="autoplay" value="false"/>
						<param name="controller" value="true"/>
						<param name="scale" value="tofit"/>
						<embed src="'.$title_path.$title_file.'" width="150" height="113" name="clip"
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

function get_message($type, $id) {			
	global $db, $filetypes_video;
	
	$level = '';
	$depth = substr_count(INCLUDE_PATH, '/');
	for ($i=1; $i<$depth; $i++) {
		$level .= "../";
	}

	switch ($type) {
		case 'forum':
			$sql = "SELECT title FROM forums_posts WHERE post_id=".$id;
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

		//$login= $row['login'];
		//$date = date('h:ia | M j, y', strtotime($row['date']));
	} else {
		echo 'No message.';
	}

}


/* saves uploaded image 

location - forum, post or page
type - title, description, subject, message, content, etc. - this will be the name of the file when saved
tmp_file - the file sent through the form
id - id of the forum, post, or page

*/
function save_image($location, $type, $file, $id) {
	global $db, $_FILES;
	
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

	if ($width>150 || $height>150) {
		if ($width >= $height && $width > 150) {
			$percent = 150/$width;
		} else if ($height > $width && $height > 150) {
			$percent = 150/$height;
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
	global $db, $_FILES;

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

function save_signlink ($location, $type, $tmp_file, $id) {
	global $db, $_FILES;

	$ext = end(explode('.',$tmp_file));

	$level = '';
	$depth = substr_count(INCLUDE_PATH, '/');
	for ($i=1; $i<$depth; $i++) {
		$level .= "../";
	}

	switch ($location) {
		case 'forum':
			$newfile = $level.UPLOAD_DIR.'forums/'.$id.'/'.$type.'.'.$ext;
			break;
		case 'post':
			$newfile = $level.UPLOAD_DIR.'posts/'.$id.'/'.$type.'.'.$ext;
			break;
		case 'page':
			break;
	}

	if (!copy($newfile, $tmp_file)) {
	  print "Error Uploading File.";
	  exit;
	} 
}


?>
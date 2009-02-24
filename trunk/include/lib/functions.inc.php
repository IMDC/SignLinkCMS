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
				if ($size == 'small') {
					$height='75';
					$width = '96';
					$style="style='height:75px;'";
				} else {
					$width=BLOCK_WIDTH;
					$height='113';
					$style= '';
				}

				if (in_array($ext, $filetypes_video)) {
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
				} else {
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
				mkdir($level.UPLOAD_DIR.'pages/'.$id.'/', 0777);
			}
			$newfile = $level.UPLOAD_DIR.'pages/'.$id.'/'.$type.'.'.$ext;
			break;
		case 'vlog':
			if(!file_exists($level.UPLOAD_DIR.'vlogs/'.$id.'/')) {
				mkdir($level.UPLOAD_DIR.'vlogs/'.$id.'/', 0777);
			}
			$newfile = $level.UPLOAD_DIR.'vlogs/'.$id.'/'.$type.'.'.$ext;
			break;		
		case 'entry':
			if(!file_exists($level.UPLOAD_DIR.'entries/'.$id.'/')) {
				mkdir($level.UPLOAD_DIR.'entries/'.$id.'/', 0777);
			}
			$newfile = $level.UPLOAD_DIR.'entries/'.$id.'/'.$type.'.'.$ext;
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

	unset($_FILES);
	//if (!@copy(trim($newfile), trim($tmp_file))) {
	//echo substr(sprintf('%o', fileperms($level.UPLOAD_DIR.'pages/'.$id.'/')), -4);
	
	if (!@move_uploaded_file($newfile, $tmp_file)) {
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
				mkdir($level.UPLOAD_DIR.'entries/'.$id.'/');
			}
			$newfile = $level.UPLOAD_DIR.'entries/'.$id.'/'.$type.'.'.$ext;
			break;				
	}

	if (!@move_uploaded_file($_FILES[$file]['tmp_name'], $newfile)) {
	  print "Error Uploading File - check directory permissions.";
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
	$dh = opendir($dir);
	while ( $file = readdir($dh) ) {
		if ( $file != '.' || $file != '..') {
			@unlink($dir.$file);
		}
	}
	closedir ($dh);
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
		echo '<img src="uploads/members/'.$id.'/'.$av_file.'" alt="'.$_SESSION['login'].'\'s avatar" /><br /><br />';
	} else {
		echo '<img src="images/no_avatar.jpg" alt="No avatar" /><br /><br />';
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

function get_vlog_owner($id) {
	global $db;
	
	$sql = "SELECT member_id FROM vlogs WHERE vlog_id=".$id;
	$result = mysql_query($sql, $db);
	
	$row = @mysql_fetch_assoc($result);
			
	return $row['member_id'];
}

?>
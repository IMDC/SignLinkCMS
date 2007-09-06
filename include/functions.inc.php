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

/* returns html-encoded title (image or video or text) - things that have titles: forum, thread, page.  */
function get_title($type, $id) {			
	
	global $db;
	
	$level = '';
	$depth = substr_count(INCLUDE_PATH, '/');
	for ($i=1; $i<$depth; $i++) {
		$level .= "../";
	}

	switch ($type) {
		case 'forum':
			$sql = "SELECT title, title_file FROM forums WHERE forum_id=".$id;
			$title_path = $level.'uploads/titles/forum/';
			break;
		case 'post':
			$sql = "SELECT subject, subject_file FROM forums_posts WHERE post_id=".$id;
			$title_path = $level.'uploads/titles/post/';
			break;
		case 'page':
			break;
	}


	$result = mysql_query($sql, $db);
	if ($result) {
		$row = mysql_fetch_row($result);

		if (!empty($row[1])) {
			$title_file = $row[1];
			$file_type = explode ('.', $title_file);
			$file_type = $file_type[1];			

			if ($file_type=='mov' || $file_type=='mp4' || $file_type=='avi') {
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
		} else {
			$title = $row[0];
		}
	}
	return $title;
}

function get_msg($file) {			
}


/* saves uploaded title file */
function save_title($type, $id) {
	global $db, $_FILES;
	
	$level = '';
	$depth = substr_count(INCLUDE_PATH, '/');
	for ($i=1; $i<$depth; $i++) {
		$level .= "../";
	}

	switch ($type) {
		case 'forum':
			$title_file = $_FILES['title_file']['tmp_name'];
			$ext = end(explode('.',$_FILES['title_file']['name']));
			$newfile = $level.'uploads/titles/forum/'. $id.'.'.$ext;
			break;
		case 'post':
			$title_file = $_FILES['subject_file']['tmp_name'];
			$ext = end(explode('.',$_FILES['subject_file']['name']));
			$newfile = $level.'uploads/titles/post/'. $id.'.'.$ext;
			break;
		case 'page':
			break;
	}

	//if image, resize 
	list($width, $height) = getimagesize($title_file); 

	if ($width>150 || $height>150) {
		if ($width >= $height && $width > 150) {
			$percent = 150/$width;
		} else if ($height > $width && $height > 150) {
			$percent = 150/$height;
		} 

		$newwidth = $width * $percent;
		$newheight = $height * $percent;

		if ($ext == "gif") {
			$smaller = imagecreate($newwidth, $newheight);
		} else {
			$smaller = imagecreatetruecolor($newwidth, $newheight);
		}

		if ($ext == "jpg" or $ext=='jpeg') {
			$source = imagecreatefromjpeg($title_file);
		} elseif ($ext == "gif") {
			$source = imagecreatefromgif($title_file);
		} elseif ($ext == 'png') {
			$source = imagecreatefrompng($title_file);
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

	if (!copy($newfile, $title_file)) {
	  print "Error Uploading File.";
	  exit;
	} else {
		$sql = "UPDATE forums_posts SET subject_file='".$id.'.'.$ext."' WHERE post_id=".$id;
		$result = mysql_query($sql, $db);
	}

}

/* saves signlink file */
function save_SLfile() {
}


?>
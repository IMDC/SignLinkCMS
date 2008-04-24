<?php
/* functions related to forums only */

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
					id="clip" codebase="http://www.apple.com/qtactivex/qtplugin.cab">
						<param name="src" value="'.$msg_path.$msg_file.'"/>
						<param name="autoplay" value="false"/>
						<param name="controller" value="true"/>
						<param name="scale" value="tofit"/>
						<embed src="'.$msg_path.$msg_file.'" name="clip"
						autoplay="false" controller="true" enablejavascript="true" scale="tofit"
						alt="Quicktime ASL video"
						pluginspage="http://www.apple.com/quicktime/download/"
						style="float:left;" />
					</object>';
				} else if (in_array($ext, $filetypes_image)) {
					$msg[2] = '<img src="'.$msg_path.$msg_file.'" alt="'.$row[1].'" title="'.$row[1].'" style="vertical-align:middle;" />';
				} else { //signlink
					$msg[2] = '<object width="565" height="455"
						classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"
						codebase="http://fpdownload.macromedia.com/pub/
						shockwave/cabs/flash/swflash.cab#version=8,0,0,0">
						<param name="movie" value="'.$msg_path.$msg_file.'"/>
						<param name="autoplay" value="false"/>
						<embed src="'.$msg_path.$msg_file.'" width="565" height="455"
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

	$sql = "SELECT forum_id, parent_id, login, date, msg, msg_alt FROM forums_posts WHERE post_id=".$id;
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
		echo '<td><a href="forum_post_view.php?f='.$row['forum_id'].'&p='.$id.'&par='.$_GET['p'].'">'.$link.'</a></td>';
		echo '<td style="text-align:center;">'.$row['login'].'</td>';
	}
}


function populate_post($row, $title) {
	//subject
	if (!empty($row['subject'])) {
		$_POST['subject'] = 'text';
		$_POST['sub-text'] = $row['subject'];
	} else if (substr($title, 0, 4) == "<img") {
		$_POST['subject'] = 'image';
		$_POST['isub-alt'] = $row['subject_alt'];
	} else if (substr($title, 0, 7) == "<object") {
		$_POST['subject'] = 'video';
		$_POST['vsub-alt'] = $row['subject_alt'];
	}

	//message
	if (isset($row['msg']) || isset($row['msg_alt'])) {
		if (!empty($row['msg'])) {
			$_POST['message'] = 'text';
			$_POST['msg-text'] = $row['msg'];
		} else if (substr($title, 0, 4) == "<img") {
			$_POST['message'] = 'image';
			$_POST['imsg-alt'] = $row['msg_alt'];
		} else if (substr($title, 0, 7) == "<object") {
			$_POST['message'] = 'video';
			$_POST['vmsg-alt'] = $row['msg_alt'];
		}
	}

	return;
}

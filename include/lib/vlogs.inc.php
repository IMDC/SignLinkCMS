<?php
/* functions related to vlogs only */

function get_vlog_message($msg, $msg_alt, $type, $id) {			
	global $db, $filetypes_video, $filetypes_image;
	
	$formatted_msg = '';
	
	$level = '';
	$depth = substr_count(INCLUDE_PATH, '/');
	for ($i=1; $i<$depth; $i++) {
		$level .= "../";
	}

	$msg_path = $level.'uploads/'.$type.'/'.$id.'/';

	if (!empty($msg)) {
		//the msg is plain text
		$formatted_msg = nl2br($msg);
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
				$formatted_msg = '<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B"
				id="clip" codebase="http://www.apple.com/qtactivex/qtplugin.cab" width="320" height="260">
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
				$formatted_msg = '<img src="'.$msg_path.$msg_file.'" alt="'.$msg_alt.'" title="'.$msg_alt.'" style="vertical-align:middle;" />';
			} else { //signlink
				$formatted_msg = '<object width="565" height="455"
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
	return $formatted_msg;
}

function get_vlog_owner($id) {
	global $db;
		
	$sql = "SELECT member_id FROM vlogs WHERE vlog_id=".$id;
	$result = mysql_query($sql, $db);
	
	$row = @mysql_fetch_assoc($result);
			
	return $row['member_id'];
}

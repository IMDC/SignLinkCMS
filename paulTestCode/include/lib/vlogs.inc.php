<?php
/* functions related to vlogs only */

/**
 * Returns the html coded vlog message component of a vlog entry
 * when you pass parameters for the content, alternate content,
 * type of message category (ex. 'entries') and the id of the
 * message in the database
 * 
 * Calls to this function are preceeded by an sql call
 * along the lines of: 'select * from vlogs_entries where vlog_id=$id and entry_id=$eid
 *
 * @global type $db
 * @global type $filetypes_video
 * @global type $filetypes_image
 * @param type $msg
 * @param type $msg_alt
 * @param type $type  the type of message category, ex. 'entries'
 * @param type $id  the id of the message, ex. 'entry_id'
 * @return array  an array of message information
 */
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
		//$formatted_msg = nl2br($msg);
      $formatted_msg = nl2br($msg);
	}
   else {
		//the msg is a file
		
		//get files
		$dir_files = scandir($msg_path);

		if(!empty($dir_files)) { 

			foreach ($dir_files as $dir_file) { 
				if (substr($dir_file,0, 7) == "message") {
					$msg_file = $dir_file;
					break;
				}
			}

         $noextfile = substr($msg_file, 0, 7);
         
			$ext = strtolower(end(explode('.',$msg_file)));
			if (in_array($ext, $filetypes_video)) {
            // FILE IS A VIDEO
            
            // find the video thumbnail file
            if ($size == 'small') {
               if ( file_exists($msg_path . "message_thumbsmall_play.jpg") ) {
                $thumbjpg = $msg_path . "message_thumbsmall_play.jpg";
               }
               else if ( file_exists($msg_path . "message_thumbsmall.jpg") ) {
                  $thumbjpg = $msg_path . "message_thumbsmall.jpg";
               }
               else {
                  $thumbjpg = "images/default_movie_icon_small.png";
               }
            }
            else {
               if ( file_exists($msg_path . "message_thumb_play.jpg")) {
                  $thumbjpg = $msg_path . "message_thumb_play.jpg";
               }
               else if ( file_exists($msg_path . "message_thumb.jpg") ) {
                  $thumbjpg = $msg_path . "message_thumb.jpg";
               }
               else {
                  $thumbjpg = "images/default_movie_icon.png";
               }
            }
            
            // assemble flowplayer code
            $formatted_msg = '  
						<a  
							 href="'.$msg_path.$msg_file.'"
							 class="flash_player_holder" 
							 style="width:'.VIDEO_MSG_WIDTH.'px;height:'.VIDEO_MSG_HEIGHT.'px;"  
							 id="'.$msg_path.$noextfile.'">
							 <img src="'.$thumbjpg.'" height="'.VIDEO_MSG_HEIGHT.'px" width="'.VIDEO_MSG_WIDTH.'px" alt="'.$msg_file.'" />
						</a> 
						<script type="text/javascript">
							flowplayer("'.$msg_path.$noextfile.'", "flash/flowplayer-3.2.7.swf", {
								clip: conf.yesplay,
                    plugins: {
                       controls: conf.big
                    }
							});
						</script>';
            
//				$formatted_msg = '<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B"
//				id="clip" codebase="http://www.apple.com/qtactivex/qtplugin.cab" width="320" height="260">
//					<param name="src" value="'.$msg_path.$msg_file.'"/>
//					<param name="autoplay" value="false"/>
//					<param name="controller" value="true"/>
//					<param name="scale" value="tofit"/>
//					<embed src="'.$msg_path.$msg_file.'" name="clip"
//					autoplay="false" controller="true" enablejavascript="true" scale="tofit"
//					alt="Quicktime ASL video"
//					pluginspage="http://www.apple.com/quicktime/download/"
//					style="float:left;" />
//				</object>';
			} 
         else if (in_array($ext, $filetypes_image)) {
            // FILE IS AN IMAGE
				$formatted_msg = '<img src="'.$msg_path.$msg_file.'" alt="'.$msg_alt.'" title="'.$msg_alt.'" style="vertical-align:middle;" />';
			} 
         else { 
            // FILE IS A SIGNLINK OBJECT
//				$formatted_msg = '<object width="565" height="455"
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

function get_vlog_comment($id) {			
	global $db, $filetypes_video, $filetypes_image;
	
	$level = '';
	$depth = substr_count(INCLUDE_PATH, '/');
	for ($i=1; $i<$depth; $i++) {
		$level .= "../";
	}

	$msg_path = $level.'uploads/comments/'.$id.'/';
	$sql = "SELECT member_id, login, date, msg, msg_alt FROM forums_posts WHERE comment_id=".$id;

	$result = mysqli_query($db, $sql);
	if ($result) {
		$msg = array();

		if (!$row = mysqli_fetch_assoc($result)) {
			$msg[0] = '';
			$msg[1] = '';
			$msg[2] = "No message.";
			return $msg;
		}		

		$msg[0] = $row['login'];
		$msg[1] = date('M j Y, h:ia', strtotime($row['date']));
		$msg[3] = $row['member_id'];

		if (!empty($row['msg'])) {
			//the msg is plain text
			$msg[2] = nl2br($row['msg']);
         $msg[4] = 1;
		} 
      else {
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

				$ext = strtolower(end(explode('.',$msg_file)));
				if (in_array($ext, $filetypes_video)) {
					// message is a VIDEO
               $msg[4] = 3;
               /*
               $msg[2] = '<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B"
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
               */

               if ( !file_exists($msg_path . "thumb_play.jpg") ) {
                  if ($size == 'small') {
                     $thumbjpg = $msg_path . "thumbsmall.jpg";
                  }
                  else {
                     $thumbjpg = $msg_path . "thumb.jpg";
                  }
               }
               else {
                  if ($size == 'small') {
                     $thumbjpg = $msg_path . "thumbsmall_play";
                  }
                  else {
                     $thumbjpg = $msg_path . "thumb_play.jpg";
                  }
               }

               $noextfile = substr($msg_file, 0, 7);
               $msg[2] = '  
						<a  
							 href="'.$msg_path.$msg_file.'"
							 class="flash_player_holder" 
							 style="width:'.VIDEO_MSG_WIDTH.'px;height:'.VIDEO_MSG_HEIGHT.'px;"  
							 id="'.$msg_path.$noextfile.'">
							 <img src="'.$thumbjpg.'" height="'.VIDEO_MSG_HEIGHT.'px" width="'.VIDEO_MSG_WIDTH.'px" alt="'.$msg_file.'" />
						</a> 
						<script type="text/javascript">
							flowplayer("'.$msg_path.$noextfile.'", "flash/flowplayer-3.2.7.swf", {
								clip: conf.yesplay,
                    plugins: {
                       controls: conf.big
                    }
							});
						</script>';

				} 
            else if (in_array($ext, $filetypes_image)) {
               // file is an image
					$msg[2] = '<img src="'.$msg_path.$msg_file.'" alt="'.$row[1].'" title="'.$row[1].'" style="vertical-align:middle;" />';
               $msg[4] = 2;
				} 
            else { //signlink
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
               $msg[4] = 4;
				}
			}
         else {
            $msg[2] = "No file found";
         }
		}
		return $msg;
	}
   else {
		echo 'No message.';
      return;
	}

}


function get_vlog_owner($id) {
	global $db;
		
	$sql = "SELECT member_id FROM vlogs WHERE vlog_id=".$id;
	$result = mysqli_query($db, $sql);
	
	$row = @mysqli_fetch_assoc($result);
			
	return $row['member_id'];
}

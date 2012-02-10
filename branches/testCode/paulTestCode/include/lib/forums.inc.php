<?php
/* functions related to forums only */

define('VIDEO_MSG_HEIGHT', '260');
define('VIDEO_MSG_WIDTH', '320');


/* 
 * Returns the message content of a forum post
 * Experimenting with $msg[4] indicating type of post
 * 1 = text, 2 = image, 3 = video, 4 = signlink
 * 
*/
function get_message($id) {			
	global $db, $filetypes_video, $filetypes_image;
   $id = intval($id);
	
   /*
    * $msg array consists of 
    * msg[0] : user's login name
    * msg[1] : timestamp of message
    * msg[2] : text of message
    * msg[3] : member_id of comment
    * msg[4] : type of post *experimental* 1-text, 2-image, 3-video, 4-signlink
    */
   
	$level = '';
	$depth = substr_count(INCLUDE_PATH, '/');
	for ($i=1; $i<$depth; $i++) {
		$level .= "../";
	}

	$msg_path = $level.'uploads/posts/'.$id.'/';
	$sql = "SELECT member_id, login, date, msg, msg_alt FROM forums_posts WHERE post_id=".$id;

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
               
/*               if ( !file_exists($msg_path . "thumb_play.jpg") ) {
                  if ($size == 'small') {
                     $thumbjpg = $msg_path . "thumbsmall.jpg";
                  }
                  else if (file_exists($msg_path . "message_thumb.jpg")) {
                     $thumbjpg = $msg_path . "message_thumb.jpg";
                  }
                  else {
                     $thumbjpg = $msg_path . "thumb.jpg";
                     $thumbjpg = "images/default_movie_icon.png";
                  }
               }
               else {
                  if ($size == 'small') {
                     $thumbjpg = $msg_path . "thumbsmall_play";
                  }
                  else {
                     $thumbjpg = $msg_path . "thumb_play.jpg";
                  }
               }*/

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
                        clip: {
                           url: "'.$msg_path.$msg_file.'",
                           autoPlay: true,
                           autoBuffering: true
                        },
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
            else { // signlink object, use object code
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

function removeUnsafeAttributesAndGivenTags($input, $validTags = '') {
       $regex = '#\s*<(/?\w+)\s+(?:on\w+\s*=\s*(["\'\s])?.+?
       \(\1?.+?\1?\);?\1?|style=["\'].+?["\'])\s*>#is';
       return preg_replace($regex, '<${1}>',strip_tags($input, $validTags));
} 

/**  Prints a complete forum post reply entry row in div style. 
 * Queries the forums_posts table and prints the user 
 * avatar section, comment editing/deleting tools if user is logged in and
 * owns the post, post reply link
 *
 * @global type $db
 * @global type $filetypes_video
 * @global type $filetypes_image
 * @param int $id the post id
 * @param int $reply_number the reply number, 1 is first reply, etc
 * @return type 
 */
function print_reply_row($id, $reply_number) {	
	global $db, $filetypes_video, $filetypes_image;

   $id = intval($id);
   
	$sql = "SELECT forum_id, parent_id, member_id, last_comment, login, date, msg, msg_alt, post_id, locked FROM forums_posts WHERE post_id=".$id;
	$result = mysqli_query($db, $sql);
	if ($result) {
		if (!$row = mysqli_fetch_assoc($result)) {
			echo 'No message.';
			return;
		}		

		if (!empty($row['msg'])) {

			//the msg is plain text
			//$link = substr($row['msg'],0,30).'...';
			//$link = '<textarea class="tinymce">' . $row['msg'] . '</textarea>';
         //$link = html_specialchars_decode($row['msg']);
         $post_content = html_entity_decode(nl2br($row['msg']));
		} 
      else {
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
				$ext = strtolower(end(explode('.',$msg_file)));
				if (in_array($ext, $filetypes_video) || ($ext=="swf") ) {
					//$link = '<img src="images/film.png" alt="movie content" style="border:0px;" />';
               $ret_vid = get_message($id);
               $post_content = $ret_vid[2];
				}
//            else if ($ext=="swf") {
//					//$link = '<img src="images/television.png" alt="signed web page content" style="border:0px;" />';
//               $ret_vid = get_message($id);
//               $post_content = $ret_vid[2];
//				}
			}
		}
		//echo '<td style="text-align:center;">'.$row['login'].'</td>';
      //echo '<td style="background:#dedede;overflow:auto;vertical-align:top;padding-top:30px;">';
      echo '<div class="reply-row">';
      if ($_SESSION['login'] == $row['login'] && $row['locked'] != 1) {
         echo "<div class='post-reply-tools'>";
         echo     "<ul class='post-reply-tools-list'>";
         echo        "<li><a href='forum_post_edit.php?f=" . $row['forum_id']."&p=".$row['post_id']."&parent=".$row['parent_id']."'><img src='images/comment_edit.png' alt='Edit' title='Edit' /></a></li>";
         echo        "<li><a href='forum_post_delete.php?f=".$row['forum_id']."&p=".$row['post_id']."&parent=".$row['parent_id']."&m=".$_SESSION['member_id']."'><img src='images/comment_delete.png' alt='Delete' title='Delete' /></a></li>";
         echo     "</ul>";
         echo "</div>";
      }
      echo '<div class="avatar-wrap">';
      echo "<div class='reply-num'>";
         echo $reply_number;
      echo '</div>';
      echo '<div class="reply-avatar-container">';
         echo '<div style="height:90%;text-align:center;">';
            echo '<div style="text-align:center;"><a href="searchmember.php?name='.$row['login'].'&id='.$row['member_id'].'">'.$row['login'].'</a></div>';
            echo '<div style="">';
               echo '<a href="searchmember.php?name='.$row['login'].'&id='.$row['member_id'].'">';
                  echo get_avatar($row['member_id'], $row['login']);
               echo '</a>';
            echo '</div>';
            echo '<div style="text-align:center;font-size:0.8em;">Last activity:<br />' . date('M j Y, h:ia', strtotime($row['last_comment'])) . '</div>';
         echo '</div>';
      echo '</div>';
      echo '</div>';
      // echo post content
      echo '<div class="post-reply-content-wrap">';
         echo '<div class="post-reply-content">'.$post_content.'</div>';
      echo '</div>';
      echo '</div>';
   }
}


function print_forum_reply_icon($link_destination) {
//   echo "<div class='reply-icon-wrap shadow'>";
      //echo "<a style='text-decoration:none;color:#fff;font-size:0.7em;margin:0 auto;width:100%;text-align:center;' href='$link_destination'><img src='images/reply-add-paul4.png' alt='Reply' title='Reply to this post!' /><br />New Reply</a>";
      echo "<a style='text-decoration:none;color:#333;font-size:0.8em;padding-top:5px;width:100%;text-align:center;' href='$link_destination'><img class='shadow' src='images/reply-add-paul4.png' alt='Reply' title='Reply to this post!' /><br />New Reply</a>";
//   echo "</div>";
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
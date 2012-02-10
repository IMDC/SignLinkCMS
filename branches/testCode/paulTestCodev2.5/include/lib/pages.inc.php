<?php
/* functions related to pages only */

define('VIDEO_MSG_WIDTH', '320');
define('VIDEO_MSG_HEIGHT', '260');

function get_content($id) {
	global $db, $filetypes_video, $filetypes_image;
   $id = intval($id);
	
	$level = '';
	$depth = substr_count(INCLUDE_PATH, '/');
	for ($i=1; $i<$depth; $i++) {
		$level .= "../";
	}

	$content_path = $level.'uploads/pages/'.$id.'/';
	$sql = "SELECT content, content_alt FROM pages WHERE page_id=".$id;

	$result = mysqli_query($db, $sql);
	if ($result) {

      if (!$row = mysqli_fetch_assoc($result)) {
			$content = "No message.";
			return $content;
		}		

		if (!empty($row['content'])) {
			//plain text
         // TODO: replace this with a TinyMCE textarea and set the contents of that textarea to $row['content']
			$content = nl2br($row['content']);
			
		}
      else {
			// content is a file
			
			// get files
			$dir_files = @scandir($content_path);

			if(!empty($dir_files)) {
				foreach ($dir_files as $dir_file) {
					if (substr($dir_file, 0, 7) == "message") {
						$content_file = $dir_file;
						break;
					}
				}

				$ext = strtolower(end(explode('.',$content_file)));
				
				if (in_array($ext, $filetypes_video)) {
               // if a video file
               /*
               $content = '<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B"
               id="clip" codebase="http://www.apple.com/qtactivex/qtplugin.cab">
                  <param name="src" value="'.$content_path.$content_file.'"/>
                  <param name="autoplay" value="false"/>
                  <param name="controller" value="true"/>
                  <param name="scale" value="tofit"/>
                  <embed src="'.$content_path.$content_file.'" name="clip"
                  autoplay="false" controller="true" enablejavascript="true" scale="tofit"
                  alt="Quicktime ASL video"
                  pluginspage="http://www.apple.com/quicktime/download/"
                  style="float:left;" />
               </object>';
               */
               if ( file_exists($content_path . "thumb_play.jpg") ) {
                  $thumbjpg = $content_path . "thumb_play.jpg";
               }
               else if ( file_exists($content_path . "thumb.jpg") ) {
                  $thumbjpg = $content_path . "thumb.jpg";
               }
               else {
                  $thumbjpg = IMAGE_FOLDER . "default_movie_icon.png";
               }

              $content = '<a  
                            href="'.$content_path.$content_file.'"
                            class = "flash_player_holder" 
                            alt="'.$row[1].'" 
                            style="width:'.VIDEO_MSG_WIDTH.'px;height:'.VIDEO_MSG_HEIGHT.'px;margin-left:auto;margin-right:auto;"  
                            id="'.$content_path.'content">
                            <img style="margin-left:-3px;" src="'.$thumbjpg.'" height="'.VIDEO_MSG_HEIGHT.'px" width="'.VIDEO_MSG_WIDTH.'px" alt="'.$row[1].'" />
                          </a>
                          <script type="text/javascript">
                             flowplayer("'.$content_path.'content", "flash/flowplayer-3.2.7.swf", {
                                clip: {
                                   url: \''.$content_path.$content_file.'\',
                                   autoPlay: true,
                                   autoBuffering: true,
                                },
                                plugins: {
                                   controls: conf.big  
                                }
                             });
                          </script>';
				}
				else if (in_array($ext, $filetypes_image)) {
				// if an image file
					$content = '<img src="'.$content_path.$content_file.'" alt="'.$row[1].'" title="'.$row[1].'" style="vertical-align:middle;" />';
				}
				else { 
				// its a signlink object
					$content = '<object width="565" height="425"
						classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"
						codebase="http://fpdownload.macromedia.com/pub/
						shockwave/cabs/flash/swflash.cab#version=8,0,0,0">
						<param name="movie" value="'.$content_path.$content_file.'"/>
						<param name="autoplay" value="false"/>
						<embed src="'.$content_path.$content_file.'" width="565" height="435"
						type="application/x-shockwave-flash" pluginspage=
						"http://www.macromedia.com/go/getflashplayer" />
					</object>';		
				}
			}
		}
		return $content;
	}
   else {
		echo 'No content.';
      return;
   }	
}

function populate_page($row, $title) {
	//parent
	if (!empty($row['parent_id'])) {
		$_POST['parent'] = 1;
		$_POST['parent_id'] = $row['parent_id'];
	} 

	//title
	if (!empty($row['title'])) {
		$_POST['subject'] = 'text';
		$_POST['sub-text'] = $row['title'];
	}
   else if (substr($title, 0, 4) == "<img") {
		$_POST['subject'] = 'image';
		$_POST['isub-alt'] = $row['title_alt'];
	}
   else if (substr($title, 0, 7) == "<object") {
		$_POST['subject'] = 'video';
		$_POST['vsub-alt'] = $row['subject_alt'];
	}

	//content
	if (isset($row['content']) || isset($row['content_alt'])) {
		if (!empty($row['content'])) {
			$_POST['message'] = 'text';
			$_POST['msg-text'] = $row['content'];
		}
      else if (substr($title, 0, 4) == "<img") {
			$_POST['message'] = 'image';
			$_POST['imsg-alt'] = $row['content_alt'];
		}
      else if (substr($title, 0, 7) == "<object") {
			$_POST['message'] = 'video';
			$_POST['vmsg-alt'] = $row['subject_alt'];
		}
	}

	return;
}

function print_signlinks_to($id) {
	global $db;

	/*$sql = "SELECT workbench_links_to FROM forums_posts WHERE post_id=".$id;

	$result = mysqli_query($db, $sql);
	if ($result) {
		$msg = array();

		if (!$row = mysqli_fetch_assoc($result)) {
			$msg[0] = '';	
	
	//output list
	echo '<ul class="links-list">';
	foreach($row['id'] as $id) {
		echo "<li>".get_title('page', $id)."</li>";
	}
	echo "</ul>";	*/
}

function print_signlinks_from($id) {
	global $db;
}

function get_top_pages() {
	global $db;
	$top_pages = array();
	
	$sql = "SELECT * FROM pages WHERE parent_id=0 ORDER BY created ASC";
	$result = mysqli_query($db, $sql);
	
	if (@mysqli_num_rows($result)) { 
		while($row = mysqli_fetch_assoc($result)) {
			$top_pages[] = $row;
		}
	}
	return $top_pages;
}
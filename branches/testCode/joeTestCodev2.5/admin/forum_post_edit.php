<?php 
define('INCLUDE_PATH', '../include/');
require(INCLUDE_PATH.'vitals.inc.php');
admin_authenticate();

$post_id = intval($_REQUEST['p']);
$forum_id = intval($_REQUEST['f']);
$thread = intval($_REQUEST['thread']);
$area = $_POST['area'];

if (isset($_POST['cancel'])) {
	header('Location: forum_posts_manage.php?f='.$forum_id);
	exit;

} else if (($_POST['f'] && $_POST['p']) || $_GET['processed']) {

	//check if there are any upload errors
	if(empty($_POST)) {
		$_SESSION['errors'][] = "General error. Your URL is incorrect or you are trying to post a file that is too large for this installation.";
		require(INCLUDE_PATH.'admin_header.inc.php');
		require(INCLUDE_PATH.'footer.inc.php');
		exit;
	}
	check_uploads();

	if (!isset($_SESSION['errors'])) {	
		if ($area=="subject") {
			//error check subject
			if (empty($_POST['subject']) || (empty($_FILES['isub-file']['tmp_name']) && empty($_FILES['vsub-file']['tmp_name']) && empty($_POST['sub-text'])) ) {
				$_SESSION['errors'][] = 'Subject empty.';
				
			} else if ($_POST['subject'] == "image") {
				$ext = explode('.', $_FILES['isub-file']['name']);
				$ext = $ext[1];
				if (!in_array($ext, $filetypes_image)) {
					$_SESSION['errors'][] = 'You have chosen to use an image file for your subject - invalid file format.'. $ext;
				}
				
			} else if ($_POST['subject'] == "video") {
				$ext = explode('.', $_FILES['vsub-file']['name']);
				$ext = $ext[1];
				if (!in_array($ext, $filetypes_video)) {
					$_SESSION['errors'][] = 'You have chosen a video file for your subject - invalid file format.';
				}
				
			} else if ( ($_POST['subject'] == "text") && empty($_POST['sub-text']) ) {
				$_SESSION['errors'][] = 'You have chosen text for your subject - message cannot be empty.';
			}	
		}
		
		//error check message 
		$ext = strtolower(end(explode('.',$_FILES['sl2msg-file']['name'])));
		
		if ($area=="message") {
			if (empty($_POST['message']) || ( (empty($_FILES['sl1msg-file']['tmp_name']) && empty($_FILES['sl2msg-file']['tmp_name'])) && empty($_FILES['vmsg-file']['tmp_name']) && empty($_POST['msg-text'])) ) {
				$_SESSION['errors'][] = 'Message empty.';
			} else if ($_POST['message'] == "signlink" && ( empty($_FILES['sl1msg-file']['tmp_name']) || empty($_FILES['sl2msg-file']['tmp_name']) || $ext!="mp4") )  {
				$_SESSION['errors'][] = 'You have chosen to post a Signlink message - this requires that you submit two files: a flash file and a .mp4 file.';
				
			} else if ($_POST['message'] == "video") {
				$ext = end(explode('.', $_FILES['vmsg-file']['name']));
				if (!in_array($ext, $filetypes_video)) {
					$_SESSION['errors'][] = 'You have chosen to post a video message - invalid file format.';
				}
				
			} else if ( $_POST['message'] == "text" && empty($_POST['msg-text']) ) {
				$_SESSION['errors'][] = 'You have chosen to post a text message - message cannot be empty.';
			}	
		}
	}	
	
	if (!isset($_SESSION['errors'])) {

		$now = date('Y-m-d G:i:s');

		if ($area=="subject") {		
			//prepare to insert into db
			switch ($_POST['subject']) {
				case 'image':
					$subject = '';
					//$subject_alt = $addslashes(htmlspecialchars($_POST['isub-alt']));
					$subject_alt = mysqli_real_escape_string($db,htmlspecialchars($_POST['isub-alt']));
					break;
				case 'video':
					$subject = '';
					//$subject_alt = $addslashes(htmlspecialchars($_POST['vsub-alt']));
					$subject_alt = mysqli_real_escape_string($db,htmlspecialchars($_POST['vsub-alt']));
					break;
				case 'text':
					//$subject = $addslashes(htmlspecialchars($_POST['sub-text']));
					$subject = mysqli_real_escape_string($db,htmlspecialchars($_POST['sub-text']));
					$subject_alt = '';
					break;
			}

			$sql = "UPDATE forums_posts SET subject='$subject', subject_alt='$subject_alt' WHERE forum_id=$forum_id AND post_id=$post_id";
		}

		if ($area=="message") {
			switch ($_POST['message']) {
				case 'signlink':
					$message = '';
					$message_alt = '';
					break;
				case 'video':
					$message = '';
					//$message_alt = $addslashes(htmlspecialchars($_POST['vmsg-alt']));
					$message_alt = mysqli_real_escape_string($db,htmlspecialchars($_POST['vmsg-alt']));
					break;
				case 'text':
					//$message = $addslashes(htmlspecialchars($_POST['msg-text']));
					$message = mysqli_real_escape_string($db,htmlspecialchars($_POST['msg-text']));
					$message_alt = '';
					break;
			}

			$sql = "UPDATE forums_posts SET msg='$message', msg_alt='$message_alt' WHERE forum_id=$forum_id AND post_id=$post_id";
		}

		if (!$result = mysqli_query($db, $sql)) {
			$_SESSION['errors'][] = 'Database error.';
		} 
    else {					
			//delete old files, save new
			if ($area=="subject") { 				
				delete_files('posts', $post_id, 'title');

				switch ($_POST['subject']) {
					case 'image':
						if (is_uploaded_file($_FILES['isub-file']['tmp_name'])) {
							save_image('post', 'title', 'isub-file', $post_id);
						}
						break;
					case 'video':
						if (is_uploaded_file($_FILES['vsub-file']['tmp_name'])) {
							save_video('post', 'title', 'vsub-file', $post_id);
						}
						break;
				}
			}

			if ($area=="message") {
				delete_files('posts', $post_id);
				switch ($_POST['message']) {
					case 'signlink':
						if (is_uploaded_file($_FILES['sl1msg-file']['tmp_name']) && is_uploaded_file($_FILES['sl2msg-file']['tmp_name'])) {
							save_signlink('post', 'message', 'sl1msg-file', $post_id);
							save_signlink('post', 'message2', 'sl2msg-file', $post_id);
						}
						break;
					case 'video':
						if (is_uploaded_file($_FILES['vmsg-file']['tmp_name'])) {
							save_video('post', 'message', 'vmsg-file', $post_id);
						}
						break;
				}
			}
			$_SESSION['feedback'][] = 'Post edited successfully.';
                        header('Location: forum_posts_manage.php?f='.$forum_id);
                        exit;
		}
	}
} 

$title = get_title('post', $post_id);
$msg = get_message($post_id);

require(INCLUDE_PATH.'admin_header.inc.php');

if ($parent_id) {
	echo '<h3>Edit reply to '.get_title('post', $parent_id).'</h3>';
} else {
	echo '<h3>Edit Forum Post</h3>';
}
?>

<script type="text/javascript" src="../jscripts/forum_post.js"></script>

<?php if ($thread) { ?>

    <div class="file-info">
            <span class="bold">Subject</span><br />

            <?php echo $title; ?> 
            <p><span id="edit-subject" class="bold">Edit Subject</span></p>

            <form action="<?php echo htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES); ?>?processed=1" method="post" name="form_sub" id="form_sub" enctype="multipart/form-data" style="clear:both; padding-top:2px;">
                    <input type="hidden" name="f" value="<?php echo $forum_id; ?>" />
                    <input type="hidden" name="p" value="<?php echo $post_id; ?>" />
                    <input type="hidden" name="thread" value="<?php echo $thread; ?>" />
                    <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAX_UPLOAD_SIZE; ?>" />
                    <input type="hidden" name="area" value="subject" />

                    <div class="choice" id="edit-subject-form">
                    <p>Choose what kind of subject you would like your post to have (image, video, or plain text) then provide the appropriate details.</p>


                            <label><input type="radio" name="subject" value="image" <?php if($_POST['subject'] == "image") { echo 'checked="checked"'; }?> />Image</label>

                            <div class="choice-info" id="subject-image">
                                    <dl class="col-list">
                                            <dt>File</dt> <dd><input type="file" id="isub-file" name="isub-file" /></dd>
                                            <dt>Alt Text<dt> <dd><input type="text" id="isub-alt" name="isub-alt" size="80" value="<?php echo $_POST['isub-alt']; ?>" /></dd>
                                    </dl>
                            </div><br />

                            <label><input type="radio" name="subject" value="video" <?php if($_POST['subject'] == "video") { echo 'checked="checked"'; }?> /> Video</label>
                            <div class="choice-info" id="subject-video">
                                    <dl class="col-list">
                                            <dt>File</dt> <dd><input type="file" id="vsub-file" name="vsub-file" /></dd>
                                            <dt>Alt Text<dt> <dd><input type="text" id="vsub-alt" name="vsub-alt" size="80" value="<?php echo $_POST['vsub-alt']; ?>" /></dd>
                                    </dl>
                            </div><br />

                            <label><input type="radio" name="subject" value="text" <?php if($_POST['subject'] == "text") { echo 'checked="checked"'; }?> /> Text</label>
                            <div class="choice-info" id="subject-text">
                                    <input type="text" id="sub-text" name="sub-text" size="85" value="<?php echo $_POST['sub-text']; ?>" />
                            </div>

                            <p><div class="row" style="text-align:right;">
                                    <input type="button" onclick="validateOnSubmit('subject')" class="submitBtn" name="submit_form" id="submit_sub_form" value="Submit">
                            </div></p>
                            
                            <p><div class="row" style="text-align:right;">
                                    <input type="submit" class="cancelBtn" name="cancel" value="Finished" /> 
                            </div></p>
                            
                    </form>
                    </div>
    </div>

<?php } else { ?>

    <div class="important-info">
            <span class="bold">Message</span><br />

            <?php echo $msg[2]; ?> 
            <p><span id="edit-message" class="bold">Edit Message</span></p>

            <form action="<?php echo htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES); ?>?processed=1" method="post" name="form_msg" id="form_msg" enctype="multipart/form-data" style="clear:both; padding-top:2px;">
                    <input type="hidden" name="f" value="<?php echo $forum_id; ?>" />
                    <input type="hidden" name="p" value="<?php echo $post_id; ?>" />
                    <input type="hidden" name="thread" value="<?php echo $thread; ?>" />
                    <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAX_UPLOAD_SIZE; ?>" />
                    <input type="hidden" name="area" value="message" />

            <div class="choice" id="edit-message-form">
                    <p>Choose what kind of message you are posting (signed web page, video, or plain text) then provide the appropriate details.</p>

                    <label><input type="radio" name="message" value="signlink" <?php if($_POST['message'] == "signlink") { echo 'checked="checked"'; }?> />Signlink Object</label>
                    <div class="choice-info" id="message-sl">
                            <dl class="col-list">
            <dt>Video (.mp4) File</dt> <dd><input type="file" id="sl2msg-file" name="sl2msg-file" /></dd>
                                    <dt>Flash (.swf) File</dt> <dd><input type="file" id="sl1msg-file" name="sl1msg-file" /></dd>
                            </dl>
                    </div><br />

                    <label><input type="radio" name="message" value="video" <?php if($_POST['message'] == "video") { echo 'checked="checked"'; }?> /> Video</label>
                    <div class="choice-info" id="message-video">
                            <dl class="col-list">
                                    <dt>File</dt> <dd><input type="file" id="vmsg-file" name="vmsg-file" /></dd>
                                    <dt>Alt Text<dt> <dd><input type="text" id="vmsg-alt" name="vmsg-alt" value="<?php echo $_POST['vmsg-alt']; ?>" /></dd>
                            </dl>
                    </div><br />

                    <label><input type="radio" name="message" value="text" <?php if($_POST['message'] == "text") { echo 'checked="checked"'; }?> /> Text</label>
                    <div class="choice-info" id="message-text">
                            <textarea id="msg-text" name="msg-text" rows="25" cols="90" style="height:20em;"><?php echo $_POST['msg-text']; ?></textarea>
                    </div>

                    <p><div class="row" style="text-align:right;">
                            <input type="button" onclick="validateOnSubmit('message')" name="submit_form" class="submitBtn" value="Submit">
                    </div></p>
                    
                    <p><div class="row" style="text-align:right;">
                            <input type="submit" class="cancelBtn" name="cancel" value="Finished" /> 
                    </div></p>

            </form>
            </div>
    </div>

<?php } ?>

<?php require(INCLUDE_PATH.'footer.inc.php'); ?>

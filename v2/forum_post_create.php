<?php 
define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');

if (isset($_REQUEST['p'])) {
	$parent_id = intval($_REQUEST['p']);
}
else {
	$parent_id = 0;
}

$forum_id = intval($_REQUEST['f']);

if (isset($_POST['cancel'])) {
	if ($parent_id) {
		header('Location: forum_post_view.php?f='.$forum_id.'&p='.$parent_id.'&parent=1');
	} else {
		header('Location: forum_posts.php?f='.$forum_id);
	}
	exit;

}
else if ($_POST['f'] || $_GET['processed']) {

	/******** check if there are any UPLOAD errors   ******/
	if(empty($_POST)) {
		$_SESSION['errors'][] = "General error. Your URL is incorrect or you are trying to post a file that is too large for this installation.";
		require(INCLUDE_PATH.'header.inc.php');
		require(INCLUDE_PATH.'footer.inc.php');
		exit;
	}
	check_uploads();

	if (!isset($_SESSION['errors'])) {	
	
		/****** now error check the SUBJECT *********/
		if (empty($_POST['subject']) || (empty($_FILES['isub-file']['tmp_name']) && empty($_FILES['vsub-file']['tmp_name']) && empty($_POST['sub-text'])) ) {
			$_SESSION['errors'][] = 'Subject empty.';
			
		} 
      else if ($_POST['subject'] == "image") {
			$ext = explode('.', $_FILES['isub-file']['name']);
			$ext = strtolower($ext[1]);
			if (!in_array($ext, $filetypes_image)) {
				$_SESSION['errors'][] = 'You have chosen to use an image file for your subject - invalid file format.'. $ext;
			}
			
		} 
      else if ($_POST['subject'] == "video") {
			$ext = explode('.', $_FILES['vsub-file']['name']);
			$ext = strtolower($ext[1]);
			if (!in_array($ext, $filetypes_video)) {
				$_SESSION['errors'][] = 'You have chosen a video file for your subject - invalid file format.';
			}
			
		}
      else if ( ($_POST['subject'] == "text") && empty($_POST['sub-text']) ) {
			$_SESSION['errors'][] = 'You have chosen text for your subject, but the message cannot be empty.';
		}	
		
		/****** now error check the MESSAGE *********/
		$ext = strtolower(end(explode('.',$_FILES['sl2msg-file']['name'])));

		if (empty($_POST['message']) || ( (empty($_FILES['sl1msg-file']['tmp_name']) && empty($_FILES['sl2msg-file']['tmp_name'])) && empty($_FILES['vmsg-file']['tmp_name']) && empty($_POST['msg-text'])) ) {
			$_SESSION['errors'][] = 'Message empty.';
		} else if ($_POST['message'] == "signlink" && ( empty($_FILES['sl1msg-file']['tmp_name']) || empty($_FILES['sl2msg-file']['tmp_name']) || $ext!="mp4" ) )  {
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
	
	if (!isset($_SESSION['errors'])) {

		//prepare to insert into db
		switch ($_POST['subject']) {
			case 'image':
				$subject = '';
//				$subject_alt = $addslashes(htmlspecialchars($_POST['isub-alt']));
                $subject_alt = mysqli_real_escape_string($db, htmlspecialchars($_POST['isub-alt']));
				break;
			case 'video':
				$subject = '';
//				$subject_alt = $addslashes(htmlspecialchars($_POST['vsub-alt']));
                $subject_alt = mysqli_real_escape_string($db, htmlspecialchars($_POST['vsub-alt']));
				break;
			case 'text':
//				$subject = $addslashes(htmlspecialchars($_POST['sub-text']));
				$subject = mysqli_real_escape_string($db, htmlspecialchars($_POST['sub-text']));
                $subject_alt = '';
				break;
		}

		switch ($_POST['message']) {
			case 'signlink':
				$message = '';
				$message_alt = '';
				break;
			case 'video':
				$message = '';
//				$message_alt = $addslashes(htmlspecialchars($_POST['vmsg-alt']));
                $message_alt = mysqli_real_escape_string($db, htmlspecialchars($_POST['vmsg-alt']));
				break;
			case 'text':
//				$message = $addslashes(htmlspecialchars($_POST['msg-text']));
                $message = mysqli_real_escape_string($db, htmlspecialchars($_POST['msg-text']));
				$message_alt = '';
				break;
		}

		$now = date('Y-m-d G:i:s');

		//insert into db
		$sql = "INSERT INTO forums_posts (post_id, parent_id, member_id, forum_id, login, last_comment, subject, subject_alt, msg, msg_alt, date)
                        VALUES (NULL, '$parent_id', '$_SESSION[member_id]', '$forum_id', '$_SESSION[login]', '$now', '$subject', '$subject_alt', '$message', '$message_alt', NOW())";

		if (!$result = mysqli_query($db, $sql)) {
			$_SESSION['errors'][] = 'Database error.';
		} else {
			$post_id = mysqli_insert_id($db);

			//edit 'last comment' field for parent to be now
			if ($parent_id) {
				$sql = "UPDATE forums_posts SET last_comment='$now', num_comments=num_comments+1 WHERE post_id=$parent_id";
				$result = mysqli_query($db, $sql);
				$num_topics = '';
			} else {
				$num_topics = ", num_topics=num_topics+1";
			}
			
			//update info for forum (last post, num posts, num topics)
			$sql = "UPDATE forums SET last_post='$now', num_posts=num_posts+1 $num_topics WHERE forum_id=$forum_id";
			$result = mysqli_query($db, $sql);

			//save files			
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

			//redirect
			if ($parent_id) {
				$_SESSION['feedback'][] = 'Replied successfully.';
				header('Location: forum_post_view.php?f='.intval($_POST['f']).'&p='.$parent_id);
				exit;
			} else {
				$_SESSION['feedback'][] = 'Forum post created successfully.';
				header('Location: forum_posts.php?f='.intval($_POST['f']));
				exit;
			}
		}
	}
} 

if (!$_SESSION['valid_user']) {
	$_SESSION['errors'][] = 'You must be logged in to post a message. Please <a href="login.php?f='.intval($_REQUEST['f']).'&p='.intval($_REQUEST['p']).'">login</a>.';
	require(INCLUDE_PATH.'header.inc.php');
	require(INCLUDE_PATH.'footer.inc.php');
	exit;
}

require(INCLUDE_PATH.'header.inc.php');

if ($parent_id) {
	echo '<h3>Reply to '.get_title('post', $parent_id).'</h3>';
} else {
	echo '<h3>New Forum Post</h3>';
}
?>
<script type="text/javascript" src="jscripts/forum_post_new.js"></script>
<script type="text/javascript" src="jscripts/tiny_mce/jquery.tinymce.js"></script>
<script type="text/javascript">
$(document).ready(function() {
   $("textarea.tinymce").tinymce({
      script_url: 'jscripts/tiny_mce/tiny_mce.js',
      theme : "simple",
      theme_advanced_buttons1 : "bold,italic,underline,strikethrough",
      theme_advanced_buttons2 : "cut,copy,paste,|,image,help,|,forecolor"
   });
});
</script>


<form action="<?php echo htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES); ?>?processed=1" method="post" name="form" enctype="multipart/form-data">
	<input type="hidden" name="f" value="<?php echo $forum_id; ?>" />
	<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAX_UPLOAD_SIZE; ?>" />

	<?php if ($parent_id) { ?>
		<input type="hidden" name="p" value="<?php echo $parent_id; ?>" />
		<input type="hidden" name="subject" value="text" />
		<input type="hidden" name="sub-text" value="Re: " />
	<?php 
	} ?>

	<?php if (empty($parent_id)) { ?>
	<div class="file-info">
		<span class="bold">Subject</span><br />
			<p>Choose what kind of subject you would like your post to have (image, video, or plain text) then provide the appropriate details.</p>

			<?php if (!empty($title)) { echo $title.'<br /><br />'; } ?>

			<div class="choice">
				<label for="imagesubject"><input type="radio" name="subject" value="image" id="imagesubject" <?php if($_POST['subject'] == "image") { echo 'checked="checked"'; }?> /> Image <img src="images/image-generic-48.png" alt="" class="inlineVertMid" /></label>

				<div class="choice-info" id="subject-image">
               <label for="isub-file">File </label><input type="file" id="isub-file" name="isub-file" /><br /><br />
               <label for="isub-alt">Alternative Text (if images are disabled) </label><input type="text" id="isub-alt" name="isub-alt" size="80" value="<?php echo $_POST['isub-alt']; ?>" />
				</div><br />

				<label for="videosubject"><input type="radio" name="subject" value="video" id="videosubject" <?php if($_POST['subject'] == "video") { echo 'checked="checked"'; }?> /> Video <img src="images/film-48.png" alt="" class="inlineVertMid" /></label>
				<div class="choice-info" id="subject-video">
               <label for="vsub-file">File </label><input type="file" id="vsub-file" name="vsub-file" /><br /><br />
               <label for="vsub-alt">Alternative Text </label><input type="text" id="vsub-alt" name="vsub-alt" size="80" value="<?php echo $_POST['vsub-alt']; ?>" />
				</div><br />

				<label for="textsubject"><input type="radio" name="subject" value="text" id="textsubject" <?php if($_POST['subject'] == "text") { echo 'checked="checked"'; }?> /> Text <img src="images/keyboard-icon-48.png" alt="" class="inlineVertMid" /></label>
				<div class="choice-info" id="subject-text">
               <label for="sub-text">Enter the subject: </label><input type="text" id="sub-text" name="sub-text" size="85" value="<?php echo $_POST['sub-text']; ?>" />
				</div>
			</div>
	</div>
	<?php } ?>

	<div class="file-info">
		<span class="bold">Message</span><br />
		<?php if(!empty($msg[2])) { echo $msg[2].'<br /><br />'; } ?>

		<p>Choose what kind of message you are posting (signed web page, video (.mp4), or plain text) then provide the appropriate details.</p>

		<div class="choice">
         <label for="signlinkmessage"><input type="radio" value="signlink" name="message" id="signlinkmessage" <?php if($_POST['message'] == "signlink") { echo 'checked="checked"'; }?> />Signed Web Page <img src="images/slslogo-48.png" alt="" class="inlineVertMid" /></label>
			<div class="choice-info" id="message-sl">
            <label for="sl2msg-file">Video (.mp4) File </label><input type="file" id="sl2msg-file" name="sl2msg-file" /><br /><br />
            <label for="sl1msg-file">Flash (.swf) File </label><input type="file" id="sl1msg-file" name="sl1msg-file" />
			</div><br />

			<label for="videomessage"><input type="radio" value="video" name="message" id="videomessage" <?php if($_POST['message'] == "video") { echo 'checked="checked"'; }?> />Video <img src="images/film-48.png" alt="" class="inlineVertMid" /></label>
			<div class="choice-info" id="message-video">
            <label for="vmsg-file">File </label><input type="file" id="vmsg-file" name="vmsg-file" /><br /><br />
            <label for="vmsg-alt">Alternative Text</label><input type="text" id="vmsg-alt" name="vmsg-alt" value="<?php echo $_POST['vmsg-alt']; ?>" />
			</div><br />

			<label for="textmessage"><input type="radio" value="text" name="message" id="textmessage" <?php if($_POST['message'] == "text") { echo 'checked="checked"'; }?> />Text <img src="images/keyboard-icon-48.png" alt="" class="inlineVertMid" /></label>
			<div class="choice-info" id="message-text">
            <textarea class="tinymce" id="msg-text" id="msg-text" name="msg-text" rows="25" cols="90" style="height:20em;width:100%;"><?php echo $_POST['msg-text']; ?> </textarea>
			</div>
		</div>
	</div>

	<div class="row submitrow">
		<input type="button" class="submitBtn" onclick="<?php if($parent_id) { echo "validateOnSubmit('reply')"; } else { echo "validateOnSubmit('')"; } ?>" name="submit_form" value="Submit"> | <input type="submit" class="cancelBtn" name="cancel" value="Cancel" /> 
	</div>
</form>

<?php require(INCLUDE_PATH.'footer.inc.php'); ?>

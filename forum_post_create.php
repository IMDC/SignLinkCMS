<?php 
define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');

if (isset($_REQUEST['p'])) {
	$parent_id = intval($_REQUEST['p']);
} else {
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

} else if ($_POST['f'] || $_GET['processed']) {

	//check if there are any upload errors
	if(empty($_POST)) {
		$_SESSION['errors'][] = "General error. Your URL is incorrect or you are trying to post a file that is too large for this installation.";
		require(INCLUDE_PATH.'header.inc.php');
		require(INCLUDE_PATH.'footer.inc.php');
		exit;
	}
	check_uploads();

	if (!isset($_SESSION['errors'])) {	
	
		//now error check subject
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
		
		//error check message 
		if (empty($_POST['message']) || ( (empty($_FILES['sl1msg-file']['tmp_name']) && empty($_FILES['sl2msg-file']['tmp_name'])) && empty($_FILES['vmsg-file']['tmp_name']) && empty($_POST['msg-text'])) ) {
			$_SESSION['errors'][] = 'Message empty.';
		} else if ($_POST['message'] == "signlink" && ( empty($_FILES['sl1msg-file']['tmp_name']) || empty($_FILES['sl2msg-file']['tmp_name']) ) )  {
			$_SESSION['errors'][] = 'You have chosen to post a Signlink message - this requires that you submit two files: a flash file and a .flv file.';
			
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
				$subject_alt = $addslashes(htmlspecialchars($_POST['isub-alt']));
				break;
			case 'video':
				$subject = '';
				$subject_alt = $addslashes(htmlspecialchars($_POST['vsub-alt']));
				break;
			case 'text':
				$subject = $addslashes(htmlspecialchars($_POST['sub-text']));
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
				$message_alt = $addslashes(htmlspecialchars($_POST['vmsg-alt']));
				break;
			case 'text':
				$message = $addslashes(htmlspecialchars($_POST['msg-text']));
				$message_alt = '';
				break;
		}

		$now = date('Y-m-d G:i:s');

		//insert into db
		$sql = "INSERT INTO forums_posts VALUES (NULL, '$parent_id', '$_SESSION[member_id]', '$forum_id', '$_SESSION[login]', '$now', 0, '$subject', '$subject_alt', '$message', '$message_alt', NOW(),0, 0)";

		if (!$result = mysql_query($sql, $db)) {
			$_SESSION['errors'][] = 'Database error.';
		} else {
			$post_id = mysql_insert_id();

			//edit 'last comment' field for parent to be now
			if ($parent_id) {
				$sql = "UPDATE forums_posts SET last_comment='$now', num_comments=num_comments+1 WHERE post_id=$parent_id";
				$result = mysql_query($sql, $db);
				$num_topics = '';
			} else {
				$num_topics = ", num_topics=num_topics+1";
			}
			
			//update info for forum (last post, num posts, num topics)
			$sql = "UPDATE forums SET last_post='$now', num_posts=num_posts+1 $num_topics WHERE forum_id=$forum_id";
			$result = mysql_query($sql, $db);

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
				header('Location: forum_post_view.php?f='.intval($_POST['f']).'&p='.$parent_id.'&parent=1');
				exit;
			} else {
				$_SESSION['feedback'][] = 'Forum topic created successfully.';
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
	echo '<h2>Reply to '.get_title('post', $parent_id).'</h2>';
} else {
	echo '<h2>New Forum Topic</h2>';
}
?>
<script type="text/javascript" src="jscripts/forum_post.js"></script>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>?processed=1" method="post" name="form" enctype="multipart/form-data" style="clear:both; padding-top:2px;">
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
				<label><input type="radio" name="subject" value="image" <?php if($_POST['subject'] == "image") { echo 'checked="checked"'; }?> /> Image</label>

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
			</div>
	</div>
	<?php } ?>

	<div class="important-info">
		<span class="bold">Message</span><br />
		<?php if(!empty($msg[2])) { echo $msg[2].'<br /><br />'; } ?>

		<p>Choose what kind of message you are posting (signlink object, video, or plain text) then provide the appropriate details.</p>

		<div class="choice">
			<label><input type="radio" name="message" value="signlink" <?php if($_POST['message'] == "signlink") { echo 'checked="checked"'; }?> /> Signlink Object</label>
			<div class="choice-info" id="message-sl">
				<dl class="col-list">
					<dt>SWF File</dt> <dd><input type="file" id="sl1msg-file" name="sl1msg-file" /></dd>
					<dt>FLV File<dt> <dd><input type="file" id="sl2msg-file" name="sl2msg-file" /></dd>
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
				<textarea id="msg-text" id="msg-text" name="msg-text" rows="25" cols="90" style="height:20em;"><?php echo $_POST['msg-text']; ?></textarea>
			</div>
		</div>

	</div>

	<div class="row" style="text-align:right;">
		<input type="button" onclick="<?php if($parent_id) { echo "validateOnSubmit('reply')"; } else { echo "validateOnSubmit('')"; } ?>" name="submit_form" value="Submit"> | <input type="submit" name="cancel" value="Cancel" /> 
	</div>
</form>

<?php require(INCLUDE_PATH.'footer.inc.php'); ?>
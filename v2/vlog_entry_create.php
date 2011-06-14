<?php 
define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');
require(INCLUDE_PATH.'lib/vlogs.inc.php'); 

$vlog_id = intval($_REQUEST['v']);

if (isset($_POST['cancel'])) {
	header('Location: vlog_entries.php?v='.$vlog_id);
	exit;

} else if ($_POST['v'] || $_GET['processed']) {

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
		$ext = strtolower(end(explode('.',$_FILES['sl2msg-file']['name'])));
		
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


		//insert into db
		$now = date('Y-m-d G:i:s');
		$sql = "INSERT INTO vlogs_entries ('entry_id', 'vlog_id', 'title', 'title_alt', 'content', 'content_alt', 'date', 'num_comments') 
                        VALUES (NULL, '$vlog_id', '$subject', '$subject_alt', '$message', '$message_alt', '$now', 0)";
                
		if (!$result = mysqli_query($db, $sql)) {
			$_SESSION['errors'][] = 'Database error.';
		} else {
			$entry_id = mysqli_insert_id($db);
			
			//update info for vlog
			$sql = "UPDATE vlogs SET last_entry='$now', num_entries=num_entries+1 WHERE vlog_id='$vlog_id'";
			$result = mysqli_query($db, $sql);


			//save files			
			switch ($_POST['subject']) {
				case 'image':
					if (is_uploaded_file($_FILES['isub-file']['tmp_name'])) {
						save_image('entry', 'title', 'isub-file', $entry_id);
					}
					break;
				case 'video':
					if (is_uploaded_file($_FILES['vsub-file']['tmp_name'])) {
						save_video('entry', 'title', 'vsub-file', $entry_id);
					}
					break;
			}

			switch ($_POST['message']) {
				case 'signlink':
					if (is_uploaded_file($_FILES['sl1msg-file']['tmp_name']) && is_uploaded_file($_FILES['sl2msg-file']['tmp_name'])) {
						save_signlink('entry', 'message', 'sl1msg-file', $entry_id);
						save_signlink('entry', 'message2', 'sl2msg-file', $entry_id);
				}
					break;
				case 'video':
					
					if (is_uploaded_file($_FILES['vmsg-file']['tmp_name'])) {
						save_video('entry', 'message', 'vmsg-file', $entry_id);
					}
					break;
			}

			//redirect
			$_SESSION['feedback'][] = 'Vlog entry created successfully.';
			header('Location: vlog_entries.php?v='.intval($_POST['v']));
			exit;
		}
	}
} 

if (!$_SESSION['valid_user']) {
	$_SESSION['errors'][] = 'You must be logged in to post an entry. Please <a href="login.php?f='.intval($_REQUEST['f']).'&p='.intval($_REQUEST['p']).'">login</a>.';
	require(INCLUDE_PATH.'header.inc.php');
	require(INCLUDE_PATH.'footer.inc.php');
	exit;
}

//check if user owns this vlog
if ($_SESSION['member_id'] != get_vlog_owner($vlog_id)) {
	$_SESSION['errors'][] = "You don't have permission to add an entry to this vlog.";
	require(INCLUDE_PATH.'header.inc.php');
	require(INCLUDE_PATH.'footer.inc.php');
	exit;
}

require(INCLUDE_PATH.'header.inc.php');

echo '<h3>New Vlog Entry</h3>';

?>
<script type="text/javascript" src="jscripts/forum_post.js"></script>

<form action="<?php echo htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES); ?>?processed=1" method="post" name="form" enctype="multipart/form-data">
	<input type="hidden" name="v" value="<?php echo $vlog_id; ?>" />
	<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAX_UPLOAD_SIZE; ?>" />

	<?php if ($parent_id) { ?>
		<input type="hidden" name="p" value="<?php echo $parent_id; ?>" />
		<input type="hidden" name="subject" value="text" />
		<input type="hidden" name="sub-text" value="Re: " />
	<?php 
	} ?>

	<?php if (empty($parent_id)) { ?>
	<div class="file-info">
		<span class="bold">Title</span><br />
			<p>Choose what kind of title you would like your entry to have (image, video, or plain text) then provide the appropriate details.</p>

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
		<span class="bold">Content</span><br />
		<?php if(!empty($msg[2])) { echo $msg[2].'<br /><br />'; } ?>

		<p>Choose what kind of content you are posting (signlink object, video, or plain text) then provide the appropriate details.</p>

		<div class="choice">
			<label><input type="radio" name="message" value="signlink" <?php if($_POST['message'] == "signlink") { echo 'checked="checked"'; }?> /> Signlink Object</label> 
			<div class="choice-info" id="message-sl">
				<dl class="col-list">
					<dt>SWF File</dt> <dd><input type="file" id="sl1msg-file" name="sl1msg-file" /></dd>
					<dt>MP4 File<dt> <dd><input type="file" id="sl2msg-file" name="sl2msg-file" /></dd>
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

	<div class="row" style="text-align:right;padding-top:10px;">
		<input type="button" onclick="<?php if($parent_id) { echo "validateOnSubmit('reply')"; } else { echo "validateOnSubmit('')"; } ?>" name="submit_form" value="Submit"> | <input type="submit" name="cancel" value="Cancel" /> 
	</div>
</form>

<?php require(INCLUDE_PATH.'footer.inc.php'); ?>

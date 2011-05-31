<?php 
define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');
require(INCLUDE_PATH.'lib/vlogs.inc.php'); 

$entry_id = intval($_REQUEST['e']);
$vlog_id = intval($_REQUEST['v']);
$area = $_POST['area'];

if (isset($_POST['cancel'])) {
	header('Location: vlog_entry_view.php?v='.$vlog_id.'&e='.$entry_id);
	exit;

} else if (($_POST['v'] && $_POST['e']) || $_GET['processed']) {

	//check if there are any upload errors
	if(empty($_POST)) {
		$_SESSION['errors'][] = "General error. Your URL is incorrect or you are trying to post a file that is too large for this installation.";
		require(INCLUDE_PATH.'header.inc.php');
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

			$sql = "UPDATE vlogs_entries SET title='$subject', title_alt='$subject_alt' WHERE vlog_id=$vlog_id AND entry_id=$entry_id";
		}

		if ($area=="message") {
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

			$sql = "UPDATE vlogs_entries SET content='$message', content_alt='$message_alt' WHERE vlog_id=$vlog_id AND entry_id=$entry_id";
		}

		if (!$result = mysqli_query($db, $sql)) {
			$_SESSION['errors'][] = 'Database error.';
		} else {					
			//delete old files, save new
			if ($area=="subject") { 				
				delete_files('entries', $entry_id, 'title');

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
			}

			if ($area=="message") {
				delete_files('entries', $entry_id);
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
			}
			$_SESSION['feedback'][] = 'Entry edited successfully.';
		}
	}
} 

$title = get_title('entry', $entry_id);

$sql = "SELECT * FROM vlogs_entries WHERE vlog_id=$vlog_id AND entry_id=$entry_id";
if (!$result = mysqli_query($db, $sql)) {
	$_SESSION['errors'][] = 'Database error.';
} else {
	$row = mysqli_fetch_assoc($result);
}

//check if user has logged in and can post
if (!$_SESSION['valid_user']) {
	$_SESSION['errors'][] = 'You must be logged in to write an entry. Please <a href="login.php?v='.intval($_REQUEST['v']).'">login</a>.';
	require(INCLUDE_PATH.'header.inc.php');
	require(INCLUDE_PATH.'footer.inc.php');
	exit;
}

//check if user owns this vlog
if ($_SESSION['member_id'] != get_vlog_owner($vlog_id)) {
	$_SESSION['errors'][] = "You don't have permission to edit this post.";
	require(INCLUDE_PATH.'header.inc.php');
	require(INCLUDE_PATH.'footer.inc.php');
	exit;
}

require(INCLUDE_PATH.'header.inc.php');

echo '<h3>Edit Entry</h3>';

?>
<script type="text/javascript" src="jscripts/forum_post.js"></script>

<script type="text/javascript">
$(document).ready(function() {
	$("#edit-subject-form").hide();
	$("#edit-message-form").hide();

	$("#edit-subject").click(
	function() {
		$("#edit-subject-form").toggle();
	});

	$("#edit-message").click(
	function() {
		$("#edit-message-form").toggle();
	});
});
</script>

	<div class="file-info">
		<span class="bold">Title</span><br />
			<p>If you would like to change the subject of your post, choose "Edit Subject", enter the appropriate information, and use the Submit button.</p>

			<?php echo $title; ?> (<span id="edit-subject" style="color:#11568B;cursor:pointer;">Edit Title</span>)

		<form action="<?php echo $_SERVER['PHP_SELF']; ?>?processed=1" method="post" name="form_sub" id="form_sub" enctype="multipart/form-data" style="clear:both; padding-top:2px;">
			<input type="hidden" name="v" value="<?php echo $vlog_id; ?>" />
			<input type="hidden" name="e" value="<?php echo $entry_id; ?>" />
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

				<div class="row" style="text-align:right;">
					<input type="button" onclick="validateOnSubmit('subject')" name="submit_form" id="submit_sub_form" value="Submit">
				</div>
			</form>
			</div>
	</div>

	<div class="important-info">
		<span class="bold">Content</span><br />
		<p>If you would like to edit your entry's content, choose "Edit Content", enter the appropriate information, and use the Submit button.</p>

		<?php  echo get_vlog_message($row['content'], $row['content_alt'], 'entries', $entry_id); ?> (<span id="edit-message" style="color:#11568B;cursor:pointer;">Edit Content</span>)

		<form action="<?php echo $_SERVER['PHP_SELF']; ?>?processed=1" method="post" name="form_msg" id="form_msg" enctype="multipart/form-data" style="clear:both; padding-top:2px;">
			<input type="hidden" name="v" value="<?php echo $vlog_id; ?>" />
			<input type="hidden" name="e" value="<?php echo $entry_id; ?>" />
			<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAX_UPLOAD_SIZE; ?>" />
			<input type="hidden" name="area" value="message" />

		<div class="choice" id="edit-message-form">
			<p>Choose what kind of message you are posting (signlink object, video, or plain text) then provide the appropriate details.</p>

			<label><input type="radio" name="message" value="signlink" <?php if($_POST['message'] == "signlink") { echo 'checked="checked"'; }?> />Signlink Object</label>
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
		
			<div class="row" style="text-align:right;">
				<input type="button" onclick="validateOnSubmit('message')" name="submit_form" value="Submit">
			</div>

		</div>
	</div>
<div class="row" style="padding-top:10px;text-align:right;">
	<input type="submit" name="cancel" value="Finished" /> 
</div>
</form>

<?php require(INCLUDE_PATH.'footer.inc.php'); ?>

<?php 
define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');

$vlog_id = intval($_REQUEST['v']);
$entry_id = intval($_REQUEST['e']);

if (isset($_POST['cancel'])) {
	header('Location: vlog_entry_view.php?v='.$vlog_id);
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

		//insert into db
		$sql = "INSERT INTO vlogs_comments (comment_id, member_id, vlog_id, entry_id, comment, comment_alt, date) VALUES (NULL, '$_SESSION[member_id]', '$vlog_id', '$entry_id', '$message', '$message_alt', NOW())";
		if (!$result = mysqli_query($db, $sql)) {
			$_SESSION['errors'][] = 'Database error.';
		} else {
			$comment_id = mysqli_insert_id($db);
			switch ($_POST['message']) {
				case 'signlink':
					if (is_uploaded_file($_FILES['sl1msg-file']['tmp_name']) && is_uploaded_file($_FILES['sl2msg-file']['tmp_name'])) {
						save_signlink('comment', 'message', 'sl1msg-file', $comment_id);
						save_signlink('comment', 'message2', 'sl2msg-file', $comment_id);
					}
					break;
				case 'video':
					if (is_uploaded_file($_FILES['vmsg-file']['tmp_name'])) {
						save_video('comment', 'message', 'vmsg-file', $comment_id);
					}
					break;
			}

			$sql = "UPDATE vlogs_entries SET num_comments=num_comments+1";
			$result = mysqli_query($db, $sql);

			//redirect	
			$_SESSION['feedback'][] = 'Comment posted successfully.';
			header('Location: vlog_entry_view.php?v='.$vlog_id.'&e='.$entry_id);
			exit;			
		}
	}
} 

if (!$_SESSION['valid_user']) {
	$_SESSION['errors'][] = 'You must be logged in to post a comment. Please <a href="login.php?v='.intval($_REQUEST['v']).'&e='.intval($_REQUEST['e']).'">login</a>.';
	require(INCLUDE_PATH.'header.inc.php');
	require(INCLUDE_PATH.'footer.inc.php');
	exit;
}

require(INCLUDE_PATH.'header.inc.php');

?>
<h3>New Comment</h3>

<script type="text/javascript" src="jscripts/forum_post.js"></script>

<form action="<?php echo htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES); ?>?processed=1" method="post" name="form_msg" id="form_msg" enctype="multipart/form-data">
	<input type="hidden" name="e" value="<?php echo $entry_id; ?>" />
	<input type="hidden" name="v" value="<?php echo $vlog_id; ?>" />
	<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAX_UPLOAD_SIZE; ?>" />

	<div class="important-info">
		<span class="bold">Comment</span><br />
		<?php if(!empty($msg[2])) { echo $msg[2].'<br /><br />'; } ?>

		<p>Choose what kind of message you are posting (signlink object, video, or plain text) then provide the appropriate details.</p>

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
		<input type="button" onclick="validateOnSubmit('message')" name="submit_form" value="Submit"> | <input type="submit" name="cancel" value="Cancel" /> 
	</div>
</form>

<?php require(INCLUDE_PATH.'footer.inc.php'); ?>

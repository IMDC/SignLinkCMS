<?php 
define('INCLUDE_PATH', '../include/');
require(INCLUDE_PATH.'vitals.inc.php');
admin_authenticate();

$page_id = intval($_REQUEST['c']);

if (isset($_POST['cancel'])) {
	header('Location: page_manage.php');
	exit;
} else if (isset($_POST['submit']) || $_GET['processed']) {

	//check if there are any upload errors
	if(empty($_POST)) {
		$_SESSION['errors'][] = 'File too large.';
	}
	check_uploads();

	if (!isset($_SESSION['errors'])) {	
		$area = $_POST['area'];
	
		if ($area=="subject") {
			//now error check subject
			if (empty($_POST['subject']) || (empty($_FILES['isub-file']['tmp_name']) && empty($_FILES['vsub-file']['tmp_name']) && empty($_POST['sub-text'])) ) {
				$_SESSION['errors'][] = 'Title empty.';
				
			} else if ($_POST['subject'] == "image") {
				$ext = explode('.', $_FILES['isub-file']['name']);
				$ext = $ext[1];
				if (!in_array($ext, $filetypes_image)) {
					$_SESSION['errors'][] = 'You have chosen to use an image file for your title - invalid file format.'. $ext;
				}
				
			} else if ($_POST['subject'] == "video") {
				$ext = explode('.', $_FILES['vsub-file']['name']);
				$ext = $ext[1];
				if (!in_array($ext, $filetypes_video)) {
					$_SESSION['errors'][] = 'You have chosen a video file for your title - invalid file format.';
				}
				
			} else if ( ($_POST['subject'] == "text") && empty($_POST['sub-text']) ) {
				$_SESSION['errors'][] = 'You have chosen text for your title - text cannot be empty.';
			}	
		} else if ($area=="message") {	
			//error check message 
			if (empty($_POST['message']) || ( (empty($_FILES['sl1msg-file']['tmp_name']) && empty($_FILES['sl2msg-file']['tmp_name'])) && empty($_FILES['vmsg-file']['tmp_name']) && empty($_POST['msg-text'])) ) {
				$_SESSION['errors'][] = 'Content empty.';
			} else if ($_POST['message'] == "signlink" && ( empty($_FILES['sl1msg-file']['tmp_name']) || empty($_FILES['sl2msg-file']['tmp_name']) ) )  {
				$_SESSION['errors'][] = 'You have chosen to post signlink object as content - this requires that you submit two files: a flash file and a .mp4 file.';
				
			} else if ($_POST['message'] == "video") {
				$ext = end(explode('.', $_FILES['vmsg-file']['name']));
				if (!in_array($ext, $filetypes_video)) {
					$_SESSION['errors'][] = 'You have chosen to post video content - invalid file format.';
				}
				
			} else if ( $_POST['message'] == "text" && empty($_POST['msg-text']) ) {
				$_SESSION['errors'][] = 'You have chosen to post text content - message cannot be empty.';
			}		
		}
	}

	if (!isset($_SESSION['errors'])) {
		//prepare to insert into db
		if ($area=="parent") {
			if ($_POST['parent'])
				$parent_id = intval($_POST['parent_id']);
			else
				$parent_id=0;
				
			$sql = "UPDATE pages SET parent_id=".$parent_id. " WHERE page_id=".$page_id;
		} if ($area=="subject") {
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
			$sql = "UPDATE pages SET title='$subject', title_alt='$subject_alt' WHERE page_id=".$page_id;
			
		} else if ($area=="message") {
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
			$sql = "UPDATE pages SET content='$message', content_alt='$message_alt' WHERE page_id=".$page_id;
		}

		//insert into db
		if (!$result = mysql_query($sql, $db)) {
			$_SESSION['errors'][] = 'Database error.';
		} else {
			//save files	
			if ($area=="subject") {
				switch ($_POST['subject']) {
					case 'image':
						if (is_uploaded_file($_FILES['isub-file']['tmp_name'])) {
							save_image('page', 'title', 'isub-file', $page_id);
						}
						break;
					case 'video':
						if (is_uploaded_file($_FILES['vsub-file']['tmp_name'])) {
							save_video('page', 'title', 'vsub-file', $page_id);
						}
						break;
				}
	
			} else if ($area=="message") {
				switch ($_POST['message']) {
					case 'signlink':
						if (is_uploaded_file($_FILES['sl1msg-file']['tmp_name']) && is_uploaded_file($_FILES['sl2msg-file']['tmp_name'])) {
							save_signlink('page', 'message', 'sl1msg-file', $page_id);
							save_signlink('page', 'message2', 'sl2msg-file', $page_id);
						}
						break;
					case 'video':
						if (is_uploaded_file($_FILES['vmsg-file']['tmp_name'])) {
							save_video('page', 'message', 'vmsg-file', $page_id);
						}
						break;
				}
			}
			
			//redirect
			$_SESSION['feedback'][] = 'Page edited successfully.';
			header('Location: page_edit.php?c='.$page_id);
			exit;
		}
	}
} else if ($page_id) {
	$title = get_title('page', $page_id);

	$sql = "SELECT * FROM pages WHERE page_id=".$page_id;
	$result = mysql_query($sql, $db);

	if ($row = mysql_fetch_assoc($result)) {
		populate_page($row, $title);
	} else {
		$_SESSION['error'][] = 'Forum not found.';
	}
}

require(INCLUDE_PATH.'admin_header.inc.php'); ?>

<h2>Edit Page</h2>
	<script type="text/javascript" src="../jscripts/forum_post.js"></script>

	<script type="text/javascript">
	$(document).ready(function() {
		$("#edit-subject-form").hide();
		$("#edit-message-form").hide();
		$("#edit-hier-form").hide();

		$("#edit-hier").click(
		function() {
			$("#edit-hier-form").toggle();
		});
		
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
		<span class="bold">Parent</span><br />
		
		<?php if (empty($row['parent_id'])) { 
			echo 'Top page'; 
		} else {	
			echo 'Sub page under:'. get_title('page', $row['parent_id'], 'small');
		} ?>  <br />(<span id="edit-hier" style="color:#11568B;cursor:pointer;">Edit Hierarchy</span>)<br />		
			
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>?processed=1" method="post" name="form_hier" id="form_hier" enctype="multipart/form-data" style="clear:both; padding-top:2px;">
			<input type="hidden" name="c" value="<?php echo $page_id; ?>" />
			<input type="hidden" name="area" value="parent" />
		
			<div class="choice" id="edit-hier-form">
				<p>Choose if you would like to change this page to a top-level page, or a sub-page with a parent.</p>
				<label><input type="radio" name="parent" value="0" <?php if($_POST['parent'] == 0) { echo 'checked="checked"'; } ?> /> top-level page<label> <br />
				<label><input type="radio" name="parent" value="1" <?php if($_POST['parent']) { echo 'checked="checked"'; } ?> /> sub-page with parent<label><br />
					<div style="margin-left:20px; padding:5px;" id="parent-info">
					<?php $top_pages = get_top_pages();
					foreach ($top_pages as $top) {
						echo '<label><input type="radio" name="parent_id" value="'.$top['page_id'].'"';
						if ($_POST['parent_id'] == $top['page_id']) { 
							echo 'checked="checked"'; 
						}
						echo ' />';
						echo get_title('page', $top['page_id']).'</label>&nbsp;';
					}
					?>
					</div>
				<div class="row" style="text-align:right;">
					<input type="submit" name="submit" value="Submit" /> | <input type="submit" name="cancel" value="Cancel" /> 
				</div>
	
			</div>
		</form>
	</div>

	<div class="file-info">
		<span class="bold">Title</span><br />
		
		<?php if (!empty($title)) { echo $title.'<br /><br />'; } ?>  (<span id="edit-subject" style="color:#11568B;cursor:pointer;">Edit Title</span>)
	
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>?processed=1" method="post" name="form_sub" id="form_sub" enctype="multipart/form-data" style="clear:both; padding-top:2px;">
			<input type="hidden" name="c" value="<?php echo $page_id; ?>" />
			<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAX_UPLOAD_SIZE; ?>" />
			<input type="hidden" name="area" value="subject" />

			<div class="choice" id="edit-subject-form">

				<p>Choose what kind of title you would like your page to have (image, video, or plain text) then provide the appropriate details.</p>
				<div class="choice">
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
				</div>
				<div class="row" style="text-align:right;">
					<input type="submit" name="submit" value="Submit" /> | <input type="submit" name="cancel" value="Cancel" /> 
				</div>
			</div>
		</form>
	</div>

	<div class="important-info">
		<span class="bold">Content</span><br />
		<?php echo get_content($row['page_id']); ?><br /> (<span id="edit-message" style="color:#11568B;cursor:pointer;">Edit Content</span>)

		<form action="<?php echo $_SERVER['PHP_SELF']; ?>?processed=1" method="post" name="form_sub" id="form_sub" enctype="multipart/form-data" style="clear:both; padding-top:2px;">
			<input type="hidden" name="c" value="<?php echo $page_id; ?>" />
			<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAX_UPLOAD_SIZE; ?>" />
			<input type="hidden" name="area" value="message" />

			<div class="choice" id="edit-message-form">
				<p>Choose what kind of content you are posting (signlink object, video, or plain text) then provide the appropriate details.</p>
		
				<div class="choice">
					<label><input type="radio" name="message" value="signlink" <?php if($_POST['message'] == "signlink") { echo 'checked="checked"'; }?> />Signlink Object</label>
					<div class="choice-info" id="message-sl">
						<dl class="col-list">
							<dt>Flash File</dt> <dd><input type="file" id="sl1msg-file" name="sl1msg-file" /></dd>
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
				<div class="row" style="text-align:right;">
					<input type="submit" name="submit" value="Submit" /> | <input type="submit" name="cancel" value="Cancel" /> 
				</div>
			</div>
		</form>		
		
	</div>
			<div class="row" style="padding-top:10px;text-align:right;">
				<input type="button" name="cancel" onclick="javascript:location.href='page_manage.php'" value="Finished" /> 
			</div>

<?php require(INCLUDE_PATH.'footer.inc.php'); ?>

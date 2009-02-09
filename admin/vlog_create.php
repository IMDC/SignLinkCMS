<?php 
define('INCLUDE_PATH', '../include/');
require(INCLUDE_PATH.'vitals.inc.php');
admin_authenticate();

$page_id = intval($_REQUEST['p']);

if (isset($_POST['cancel'])) {
	header('Location: '.INCLUDE_PATH.'../admin/vlog_manage.php');
	exit;
} else if (isset($_POST['submit']) || $_GET['processed']) {

	//check if there are any upload errors
	if(empty($_POST)) {
		$_SESSION['errors'][] = 'File too large.';
	}
	check_uploads();

	if (!isset($_SESSION['errors'])) {	
	
		if (empty($_POST['member'])) {
			$_SESSION['errors'][] = 'Member not selected.';
		}
	
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
	}

	if (!isset($_SESSION['errors'])) {
		//prepare to insert into db
		$member = intval($_POST['member']);
		
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

		//insert into db
		$sql = "INSERT INTO vlogs VALUES (0, '$member', '$subject', '$subject_alt', 0, NOW())";

		if (!$result = mysql_query($sql, $db)) {
			$_SESSION['errors'][] = 'Database error.';
		} else {
			$vlog_id = mysql_insert_id();

			//save files			
			switch ($_POST['subject']) {
				case 'image':
					if (is_uploaded_file($_FILES['isub-file']['tmp_name'])) {
						save_image('vlog', 'title', 'isub-file', $vlog_id);
					}
					break;
				case 'video':
					if (is_uploaded_file($_FILES['vsub-file']['tmp_name'])) {
						save_video('vlog', 'title', 'vsub-file', $vlog_id);
					}
					break;
			}

			//redirect
			$_SESSION['feedback'][] = 'Vlog created successfully.';
			header('Location: vlog_manage.php');
			exit;
		}
	}
} 

require(INCLUDE_PATH.'admin_header.inc.php'); ?>

<h2>Create Vlog</h2>
<script type="text/javascript" src="../jscripts/forum_post.js"></script>
<form action ="<?php echo $_SERVER['PHP_SELF']; ?>?processed=1" method="post" name="form" enctype="multipart/form-data">
		
	<div class="file-info" style="background-color:#fff5f5;">
		<span class="bold">Owner</span><br />
		<p>Choose who will own this vlog (only the owner can post to it).</p>
		<label>Member<br /> <?php print_members_dropdown(); ?><label>
	</div>													
	
	<div class="file-info">
		<span class="bold">Title</span><br />
			<p>Choose what kind of title you would like your vlog to have (image, video, or plain text) then provide the appropriate details.</p>

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
	</div>	  
	  
	<div class="row" style="text-align:right;">
		<input type="submit" name="submit" value="Submit" /> | <input type="submit" name="cancel" value="Cancel" /> 
	</div>
</form>

<?php require(INCLUDE_PATH.'footer.inc.php'); ?>

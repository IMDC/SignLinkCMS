<?php 
define('INCLUDE_PATH', '../include/');
require(INCLUDE_PATH.'vitals.inc.php');

$page_id = intval($_REQUEST['c']);

if (isset($_POST['cancel'])) {
	header('Location: '.INCLUDE_PATH.'../admin/page_manage.php');
	exit;
} else if (isset($_POST['submit']) || $_GET['processed']) {

	//check if there are any upload errors
	if(empty($_POST)) {
		$_SESSION['errors'][] = 'File too large.';
	}
	check_uploads();

	if (!isset($_SESSION['errors'])) {	
	
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
		
		//error check message 
		if (empty($_POST['message']) || ( (empty($_FILES['sl1msg-file']['tmp_name']) && empty($_FILES['sl2msg-file']['tmp_name'])) && empty($_FILES['vmsg-file']['tmp_name']) && empty($_POST['msg-text'])) ) {
			$_SESSION['errors'][] = 'Content empty.';
		} else if ($_POST['message'] == "signlink" && ( empty($_FILES['sl1msg-file']['tmp_name']) || empty($_FILES['sl2msg-file']['tmp_name']) ) )  {
			$_SESSION['errors'][] = 'You have chosen to post signlink object as content - this requires that you submit two files: a flash file and a .flv file.';
			
		} else if ($_POST['message'] == "video") {
			$ext = end(explode('.', $_FILES['vmsg-file']['name']));
			if (!in_array($ext, $filetypes_video)) {
				$_SESSION['errors'][] = 'You have chosen to post video content - invalid file format.';
			}
			
		} else if ( $_POST['message'] == "text" && empty($_POST['msg-text']) ) {
			$_SESSION['errors'][] = 'You have chosen to post text content - message cannot be empty.';
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

		$parent_id = intval($_POST['parent_id']);
		$outline = $addslashes(htmlspecialchars($_POST['outline']));

		//insert into db
		$sql = "INSERT INTO pages VALUES (NULL, '$parent_id', '$_SESSION[member_id]', '$subject', '$subject_alt', '$message', '$message_alt', '$outline', NOW(), NOW(),'')";
		if (!$result = mysql_query($sql, $db)) {
			$_SESSION['errors'][] = 'Database error.';
		} else {
			$page_id = mysql_insert_id();

			//save files			
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

			//redirect
			$_SESSION['feedback'][] = 'Page created successfully.';
			header('Location: page_manage.php');
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

	<div class="file-info" style="background-color:#fff5f5;">
		<span class="bold">Parent</span><br />
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
	</div>

	<div class="file-info">
		<span class="bold">Title</span><br />
		
			<?php if (!empty($title)) { echo $title.'<br /><br />'; } ?>  <a href="" />Edit Title</a>
	
		<div id="edit_title">
		<form action ="<?php echo $_SERVER['PHP_SELF']; ?>?processed=1" method="post" name="form" enctype="multipart/form-data">
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
		</form>
		</div>
	</div>


	<div class="important-info">
		<span class="bold">Content</span><br />
		<?php if(!empty($msg[2])) { echo $msg[2].'<br /><br />'; } ?>

		<p>Choose what kind of content you are posting (signlink object, video, or plain text) then provide the appropriate details.</p>

		<div class="choice">
			<label><input type="radio" name="message" value="signlink" <?php if($_POST['message'] == "signlink") { echo 'checked="checked"'; }?> />Signlink Object</label>
			<div class="choice-info" id="message-sl">
				<dl class="col-list">
					<dt>Flash File</dt> <dd><input type="file" id="sl1msg-file" name="sl1msg-file" /></dd>
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

			<label><input type="radio" name="message" value="text" <?php if($_POST['message'] == "text") { echo 'checked="checked"'; }?> /> Text -- HTML content w/ wsywig editor?</label>
			<div class="choice-info" id="message-text">
				<textarea id="msg-text" id="msg-text" name="msg-text" rows="25" cols="90" style="height:20em;"><?php echo $_POST['msg-text']; ?></textarea>
			</div>
		</div>

	</div>

	<div class="workbench">
		<h3>Workbench</h3>
		<p>This area may be useful in the creation of your signlink videos. Content entered here is not viewable by others.</p>
	 
	   <div class="row">
		   <span class="bold">Content outline</span><br />
		   <p>Provide a rough outline of what will be signed in the video, making sure to include phrases that can be linked to related pages. This can then be used as a script when filming the video, and a way of planning out others.</p>
		   <textarea name="outline" cols="100" rows="10" style="width:100%; height:17em;" ><?php echo $_POST['outline']; ?></textarea>
	   </div>

		<div class="row">
		   <span class="bold">URL</span><br />
		 	The URL for this page will be <strong>http://<?php echo $_SERVER['SERVER_NAME']; ?>/filename.php</strong>. Create signlinks to this URL in related videos.
		</div>
	 
	   <div class="row">
		   <span class="bold">Signlinks on this page</span> (<a href="">Add Links</a> | <a href="">Remove Selected</a>)<br />
		   <?php @print_signlinks_from(); ?> list here.......
	   </div>
	 
	   <div class="row">
		   <span class="bold">Pages linking to this page</span><br />
		   <?php @print_signlinks_to(); ?> list here.......
	   </div>
	  
	  <br style="clear:both;" />
	</div>	  

<?php require('../include/footer.inc.php'); ?>

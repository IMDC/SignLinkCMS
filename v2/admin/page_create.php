<?php 
define('INCLUDE_PATH', '../include/');
require(INCLUDE_PATH.'vitals.inc.php');
admin_authenticate();

$page_id = intval($_REQUEST['p']);

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
				$_SESSION['errors'][] = 'You have chosen a video file for your title - however it is in an unsupported file format.';
			}
			
		} else if ( ($_POST['subject'] == "text") && empty($_POST['sub-text']) ) {
			$_SESSION['errors'][] = 'You have chosen text for your title - text cannot be empty.';
		}	
		
		//error check message 
		$ext = strtolower(end(explode('.',$_FILES['sl2msg-file']['name'])));

		if ( empty($_POST['message'])
					|| ( (empty($_FILES['sl1msg-file']['tmp_name']) && empty($_FILES['sl2msg-file']['tmp_name'])) 
					&& empty($_FILES['vmsg-file']['tmp_name']) && empty($_POST['msg-text'])) ) {
			$_SESSION['errors'][] = 'Content empty.';
		}
		else if ($_POST['message'] == "signlink" && ( empty($_FILES['sl1msg-file']['tmp_name']) || empty($_FILES['sl2msg-file']['tmp_name']) || $ext!="mp4")  )  {
			$_SESSION['errors'][] = 'You have chosen to post signlink object as content - this requires that you submit two files: a flash file and a .mp4 file.';
		}
		else if ($_POST['message'] == "video") {
			
			$ext = end(explode('.', $_FILES['vmsg-file']['name']));
			
			if (!in_array($ext, $filetypes_video)) {
				$_SESSION['errors'][] = 'You have chosen to post video content - invalid file format.';
			}	
		}
		else if ( $_POST['message'] == "text" && empty($_POST['msg-text']) ) {
			$_SESSION['errors'][] = 'You have chosen to post text content - message cannot be empty.';
		}		
	}

	if (!isset($_SESSION['errors'])) {
		//prepare to insert into db
		switch ($_POST['subject']) {
			case 'image':
				$subject = '';
				//$subject_alt = $addslashes(htmlspecialchars($_POST['isub-alt']));
            $subject_alt = mysqli_real_escape_string($db,strip_tags($_POST['isub-alt']));
				break;
			case 'video':
				$subject = '';
				//$subject_alt = $addslashes(htmlspecialchars($_POST['vsub-alt']));
            $subject_alt = mysqli_real_escape_string($db,strip_tags($_POST['vsub-alt']));
				break;
			case 'text':
				//$subject = $addslashes(htmlspecialchars($_POST['sub-text']));
            $subject = mysqli_real_escape_string($db,strip_tags($_POST['sub-text']));
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
				//$message_alt = $addslashes(htmlspecialchars($_POST['vmsg-alt']));
            $message_alt = mysqli_real_escape_string($db, strip_tags($_POST['vmsg-alt']));
				break;
			case 'text':
				//$message = $addslashes(htmlspecialchars($_POST['msg-text']));
            $message = mysqli_real_escape_string($db, htmlspecialchars($_POST['msg-text']));
				$message_alt = '';
				break;
		}
	
		if ($_POST['parent']) {
			$parent_id = intval($_POST['parent_id']);
		} else {
			$parent_id = 0;
		}
		//$outline = $addslashes(htmlspecialchars($_POST['outline']));
      $outline = mysqli_real_escape_string($db, htmlspecialchars($_POST['outline']));
      
		//insert into db
		$sql = "INSERT INTO pages (page_id, parent_id, member_id, title, title_alt, content, content_alt, outline, created, last_modified, links_to) VALUES (NULL, '$parent_id', 0, '$subject', '$subject_alt', '$message', '$message_alt', '$outline', NOW(), NOW(),'')";

		if (!$result = mysqli_query($db, $sql)) {
         //$_SESSION['errors'][] = $sql;
         //$_SESSION['errors'][] = mysqli_error($db);
			$_SESSION['errors'][] = 'Database error.';
		} else {
			$page_id = mysqli_insert_id($db);

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
} 

$title = get_title('page', $row['page_id']);

require(INCLUDE_PATH.'admin_header.inc.php'); ?>

<h2>Create Page</h2>
<!--<script type="text/javascript" src="../jscripts/forum_post.js"></script> -->

<script type="text/javascript" src="../jscripts/forum_post_new.js"></script>
<script type="text/javascript" src="../jscripts/tiny_mce/jquery.tinymce.js"></script>
<script type="text/javascript">
$(document).ready(function() {
   $("textarea.tinymce").tinymce({
      script_url: '../jscripts/tiny_mce/tiny_mce.js',
      theme : "advanced",
      theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,fontselect,fontsizeselect",
      theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,image,cleanup,help,|,forecolor,backcolor"
   });
});
</script>



<form action ="<?php echo htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES); ?>?processed=1" method="post" name="form" enctype="multipart/form-data">
	<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAX_UPLOAD_SIZE; ?>" />

<?php $top_pages = get_top_pages(); 
	if (!empty($top_pages)) { ?>			
	<div class="file-info">
		<span class="bold">Parent</span><br />
		<p>Choose if this will be a top-level page, or a sub-page with a parent.</p>
		<label><input type="radio" name="parent" value="0" checked="checked" /> top-level page<label> <br />
		
		<label><input type="radio" name="parent" value="1" /> sub-page with parent<label><br />
		<div style="margin-left:20px; padding:5px;" id="parent-info">
		<?php 
		foreach ($top_pages as $top) {
			echo '<label><input type="radio" name="parent_id" value="'.$top['page_id'].'" />';
			echo get_title('page', $top['page_id'], "small").'</label>&nbsp;';
		} ?>
		</div>													
	</div>
<?php } ?>
	
	<div class="file-info">
		<span class="bold">Title</span><br />
			<p>Choose what kind of title you would like your page to have (image, video, or plain text) then provide the appropriate details.</p>

			<?php if (!empty($title)) { echo $title.'<br /><br />'; } ?>

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

	<div class="file-info">
		<span class="bold">Content</span><br />
		<?php if(!empty($msg[2])) { echo $msg[2].'<br /><br />'; } ?>

		<p>Choose what kind of content you are posting (signed web page, video, or plain text) then provide the appropriate details.</p>

		<div class="choice">
			<label><input type="radio" name="message" value="signlink" <?php if($_POST['message'] == "signlink") { echo 'checked="checked"'; }?> />Signed Web Page</label>
			<div class="choice-info" id="message-sl">
				<dl class="col-list">
            	<dt>Video File (.mp4)<dt> <dd><input type="file" id="sl2msg-file" name="sl2msg-file" /></dd>
					<dt>Flash File (.swf)</dt> <dd><input type="file" id="sl1msg-file" name="sl1msg-file" /></dd>
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
				<!-- <textarea id="msg-text" name="msg-text" rows="25" cols="90" style="height:20em;"><?php echo $_POST['msg-text']; ?></textarea> -->
				<textarea class="tinymce" id="msg-text" name="msg-text" rows="25" cols="90" style="height:20em;width:100%;"><?php echo $_POST['msg-text']; ?> </textarea>
			</div>
		</div>

	</div>

	<!--div class="workbench">
		<h3>Workbench</h3>
		<p>This area may be useful in the creation of your signlink videos. Content entered here is not viewable by others.</p>
	 
	   <div class="row">
		   <span class="bold">Content outline</span><br />
		   <p>Provide a rough outline of what will be signed in the video, making sure to include phrases that can be linked to related pages. This can then be used as a script when filming the video, and a way of planning out others.</p>
		   <textarea name="outline" cols="100" rows="10" style="width:100%; height:17em;" ></textarea>
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
	</div -->	  
	  
	<div class="row" style="text-align:right;margin-top:20px;">
      <!-- TODO: replace these buttons with styled green/red visual cue buttons */ -->
		<input type="submit" name="submit" value="Submit" /> | <input type="submit" name="cancel" value="Cancel" /> 
	</div>
</form>

<?php require('../include/footer.inc.php'); ?>

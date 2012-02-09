<?php 
define('INCLUDE_PATH', '../include/');
require(INCLUDE_PATH.'vitals.inc.php');
admin_authenticate();

$vlog_id = intval($_REQUEST['v']); 
$entry_id = intval($_REQUEST['e']); 
$c_value = intval($_REQUEST['c']); 


require(INCLUDE_PATH.'admin_header.inc.php'); 
?>

<?php

echo '<h3>Edit Vlog Entry</h3>';

?>


<script type="text/javascript" src="../jscripts/forum_post.js"></script>

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

	<div class="submitrow" style="text-align:right;padding-top:10px;">
		<input type="submit" name="cancel" class="cancelBtn" value="Cancel" />&nbsp;&nbsp;<input type="button" class="submitBtn" onclick="<?php if($parent_id) { echo "validateOnSubmit('reply')"; } else { echo "validateOnSubmit('')"; } ?>" name="submit_form" value="Submit"> 
	</div>
</form>



<?php require(INCLUDE_PATH.'footer.inc.php'); ?>

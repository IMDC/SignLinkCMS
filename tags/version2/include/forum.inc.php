<script type="text/javascript" src="../jscripts/forum_post.js"></script>

<form action ="<?php echo $_SERVER['PHP_SELF']; ?>?processed=1" method="post" name="form" enctype="multipart/form-data">
<input type="hidden" name="f" value="<?php echo $forum_id; ?>" />

<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAX_UPLOAD_SIZE; ?>" />

<div class="file-info">
	<span class="bold"><label>Title</label></span><br />
		<?php if (!empty($title)) { echo $title.'<br /><br />'; } ?>

		<p>Choose what kind of subject you would like your forum to have (image, video, or plain text) then provide the appropriate details.</p>
		<div class="choice">
			<label><input type="radio" name="subject" value="image" <?php if($_POST['subject'] == "image") { echo 'checked="checked"'; }?> />Image</label>
			<div class="choice-info">
				<dl class="col-list" id="subject-image">
					<dt>File</dt> <dd><input type="file" id="isub-file" name="isub-file" /></dd>
					<dt>Alt Text<dt> <dd><input type="text" id="isub-alt" name="isub-alt" value="<?php echo $_POST['isub-alt']; ?>" /></dd>
				</dl>
			</div>

			<label><input type="radio" name="subject" value="video" <?php if($_POST['subject'] == "video") { echo 'checked="checked"'; }?> /> Video</label>
			<div class="choice-info">
				<dl class="col-list" id="subject-video">
					<dt>File</dt> <dd><input type="file" id="vsub-file" name="vsub-file" /></dd>
					<dt>Alt Text<dt> <dd><input type="text" id="vsub-alt" name="vsub-alt" value="<?php echo $_POST['vsub-alt']; ?>" /></dd>
				</dl>
			</div>

			<label><input type="radio" name="subject" value="text" <?php if($_POST['subject'] == "text") { echo 'checked="checked"'; }?> /> Text</label>
			<div class="choice-info" id="subject-text">
				<input type="text" id="sub-text" name="sub-text" size="80" value="<?php echo $_POST['sub-text']; ?>" />
			</div>
		</div>
</div>

<div class="row" style="text-align:right;">
	<input type="submit" name="submit" value="Submit" /> | <input type="submit" name="cancel" value="Cancel" /> 
</div>

</form>
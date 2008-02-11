<form action ="<?php echo $_SERVER['PHP_SELF']; ?>?processed=1" method="post" name="form" enctype="multipart/form-data">
<input type="hidden" name="fid" value="<?php echo $_REQUEST['fid']; ?>" />

<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAX_UPLOAD_SIZE; ?>" />

<div class="file-info">
	<span class="bold"><label>Subject</label></span><br />
		<?php 
		$img_file = '../uploads/'. $_POST['title-file'];
		if (!empty($_POST['title-file']) && file_exists($img_file)) {
			echo '<img src="'.$img_file.'" /> (<a href="forum_delete.php?id='.$_REQUEST['fid'].'&d=title">Delete</a>)<br /><br />';
		}
		?>
		<div class="choice">
			<label><input type="radio" name="subject" value="image" <?php if($_POST['subject'] == "image") { echo 'checked="checked"'; }?> />Image</label>
			<div class="choice-info">
				<dl class="col-list">
					<dt>File</dt> <dd><input type="file" id="isub-file" name="isub-file" /></dd>
					<dt>Alt Text<dt> <dd><input type="text" id="isub-alt" name="isub-alt" value="<?php echo $_POST['isub-alt']; ?>" /></dd>
				</dl>
			</div>

			<label><input type="radio" name="subject" value="video" <?php if($_POST['subject'] == "video") { echo 'checked="checked"'; }?> /> Video</label>
			<div class="choice-info">
				<dl class="col-list">
					<dt>File</dt> <dd><input type="file" id="vsub-file" name="vsub-file" /></dd>
					<dt>Alt Text<dt> <dd><input type="text" id="vsub-alt" name="vsub-alt" value="<?php echo $_POST['vsub-alt']; ?>" /></dd>
				</dl>
			</div>

			<label><input type="radio" name="subject" value="text" <?php if($_POST['subject'] == "text") { echo 'checked="checked"'; }?> /> Text</label>
			<div class="choice-info">
				<input type="text" id="sub-text" name="sub-text" size="100" value="<?php echo $_POST['sub-text']; ?>" />
			</div>
		</div>
</div>

<!-- div class="row">
	<span class="form-title">Description</span><br />
	<textarea name="descrip" rows="5" cols="90" style="margin-top:7px;"><?php echo $_POST['descrip']; ?></textarea>
</div -->

<div class="row" style="text-align:right;">
	<input type="submit" name="submit" value="Submit" /> | <input type="submit" name="cancel" value="Cancel" /> 
</div>

</form>
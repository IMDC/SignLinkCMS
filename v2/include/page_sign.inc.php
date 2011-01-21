<form action ="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form" enctype="multipart/form-data">
	<h3>Page Info</h3>

	<div class="row url-info">
		<span class="bold">URL</span><br /> 
		http://<?php echo $_SERVER['SERVER_NAME']; ?>/filename.php
	</div>

	<div class="file-info">
		<span class="bold">Subject</span><br />
			<p>Choose what kind of subject you would like your post to have (image, video, or plain text) then provide the appropriate details.</p>

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


	<div class="important-info">
		<span class="bold">Page Content</span><br />
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

			<label><input type="radio" name="message" value="text" <?php if($_POST['message'] == "text") { echo 'checked="checked"'; }?> /> Text</label>
			<div class="choice-info" id="message-text">
				<textarea id="msg-text" id="msg-text" name="msg-text" rows="25" cols="90" style="height:20em;"><?php echo $_POST['msg-text']; ?></textarea>
			</div>
		</div>

	</div>


	<h3>Signlink Workbench (not visible to users)</h3>

	<div class="notes">

		<div class="row">
			<span class="bold">Content outline</span><br />
			<textarea name="notes" cols="100" rows="6" style="width:100%;" ></textarea>
		</div>

		<div class="row" style="float:right; width:48%;">
			<span class="bold">Signlinks on this page</span> (<a href="">Add Links</a> | <a href="">Remove Selected</a>)<br />
			<ul class="links-list" style="list-style:none; margin-left:-20px;">
				<li><input type="button" name="" value="Copy URL" /> <label><input type="checkbox" name="linkto[]" /> pagename</label></li>
				<li><input type="button" name="" value="Copy URL" /> <label><input type="checkbox" name="linkto[]" /> <img src="../images/dude.jpg" style="width:100px;" /></label></li>
				<li><input type="button" name="" value="Copy URL" /> <label><input type="checkbox" name="linkto[]" /> another page</label></li>
			</ul>
		</div>

		<div class="row" style="float:left; width:48%;">
			<span class="bold">Links to this page</span> (auto-generated)<br />
			<ul class="links-list">
				<li>pagename</li>
				<li><img src="../images/dude.jpg" style="width:100px;" /></li>
				<li>another page</li>
			</ul>
		</div>
		<br style="clear:both;" />
	<div class="row" style="text-align:right;">
		<input type="submit" name="submit" value="Submit" /> | <input type="submit" name="cancel" value="Cancel" /> 
	</div>


</div>
</form>
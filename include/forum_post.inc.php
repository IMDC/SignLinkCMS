<script type="text/javascript">
	<!--
	$(document).ready(function() {
		$("#subject").click(function() {
			$("#subject-image").toggle(function(){
				$("#subject-image").hide('slow');
				},function(){
				$("#subject-image").show('fast');
			});
		});
	});

	function validateOnSubmit() {
		var er_text = new Array();
		var er_string = "";
		var count = 0;

		//check subject if not a reply

		if (typeof(document.form.subject) == "undefined") {
			if (document.form.subject[0].checked) {
				if (document.getElementById('isub-file').value==null || document.getElementById('isub-file').value=="") {
					er_text[count] = "Image subject file missing.";
					count++;
				}
				if (document.getElementById('isub-alt').value==null || document.getElementById('isub-alt').value=="") {
					er_text[count] = "Image subject alt text missing.";
					count++;
				}

			} else if (document.form.subject[1].checked) {
				if (document.getElementById('vsub-file').value==null || document.getElementById('vsub-file').value=="") {
					er_text[count] = "Video subject file missing.";
					count++;
				}
				if (document.getElementById('vsub-alt').value==null || document.getElementById('vsub-alt').value=="") {
					er_text[count] = "Video subject alt text missing.";
					count++;
				}

			} else if (document.form.subject[2].checked) {
				if (document.getElementById('sub-text').value==null || document.getElementById('sub-text').value=="") {
					er_text[count] = "Subject text missing.";
					count++;
				}
			} else {
				er_text[count] = "Subject missing.";
				count++;
			}
		}

		//check message
		if (document.form.message[0].checked) {
			if (document.getElementById('sl1msg-file').value==null || document.getElementById('sl1msg-file').value=="" ||
				document.getElementById('sl2msg-file').value==null || document.getElementById('sl2msg-file').value=="" ) {
				er_text[count] = "Signlink message file missing.";
				count++;
			}

		} else if (document.form.message[1].checked) {
			if (document.getElementById('vmsg-file').value==null || document.getElementById('vmsg-file').value=="") {
				er_text[count] = "Video message file missing.";
				count++;
			}
			if (document.getElementById('vmsg-alt').value==null || document.getElementById('vmsg-alt').value=="") {
				er_text[count] = "Video message alt text missing.";
				count++;
			}

		} else if (document.form.message[2].checked) {
			if (document.getElementById('msg-text').value==null || document.getElementById('msg-text').value=="") {
				er_text[count] = "Message text missing.";
				count++;
			}
		} else {
			er_text[count] = "Message missing.";
			count++;
		}

		if (er_text!="") {
			for (var i=0; i < er_text.length; i++) {
				er_string = er_string + '\n' + er_text[i];
			}
			alert('Corrections required: ' + er_string);
		} else {
			document.form.submit();
		}

		return;
	};

	-->
</script>


<?php if (empty($parent_id)) { ?>
<div class="file-info">
	<span class="bold">Subject</span><br />
		<?php echo $title.'<br /><br />'; ?>

		<div class="choice">
			<label><input type="radio" name="subject" id="subject" value="image" <?php if($_POST['subject'] == "image") { echo 'checked="checked"'; }?> />Image</label>

			<div class="choice-info" id="subject-image">
				<dl class="col-list">
					<dt>File</dt> <dd><input type="file" id="isub-file" name="isub-file" /></dd>
					<dt>Alt Text<dt> <dd><input type="text" id="isub-alt" name="isub-alt" size="80" value="<?php echo $_POST['isub-alt']; ?>" /></dd>
				</dl>
			</div>

			<label><input type="radio" name="subject" value="video" <?php if($_POST['subject'] == "video") { echo 'checked="checked"'; }?> /> Video</label>
			<div class="choice-info" id="subject-video">
				<dl class="col-list">
					<dt>File</dt> <dd><input type="file" id="vsub-file" name="vsub-file" /></dd>
					<dt>Alt Text<dt> <dd><input type="text" id="vsub-alt" name="vsub-alt" size="80" value="<?php echo $_POST['vsub-alt']; ?>" /></dd>
				</dl>
			</div>

			<label><input type="radio" name="subject" value="text" <?php if($_POST['subject'] == "text") { echo 'checked="checked"'; }?> /> Text</label>
			<div class="choice-info" id="subject-text">
				<input type="text" id="sub-text" name="sub-text" size="85" value="<?php echo $_POST['sub-text']; ?>" />
			</div>
		</div>
</div>
<?php } ?>

<div class="important-info">
	<span class="bold">Message</span><br />
	<?php echo $msg[2].'<br /><br />'; ?>

	<div class="choice">
		<label><input type="radio" name="message" value="signlink" <?php if($_POST['message'] == "signlink") { echo 'checked="checked"'; }?> />Signlink Object</label>
		<div class="choice-info">
			<dl class="col-list">
				<dt>Flash File</dt> <dd><input type="file" id="sl1msg-file" name="sl1msg-file" /></dd>
				<dt>FLV File<dt> <dd><input type="file" id="sl2msg-file" name="sl2msg-file" /></dd>
			</dl>
		</div>

		<label><input type="radio" name="message" value="video" <?php if($_POST['message'] == "video") { echo 'checked="checked"'; }?> /> Video</label>
		<div class="choice-info">
			<dl class="col-list">
				<dt>File</dt> <dd><input type="file" id="vmsg-file" name="vmsg-file" /></dd>
				<dt>Alt Text<dt> <dd><input type="text" id="vmsg-alt" name="vmsg-alt" value="<?php echo $_POST['vmsg-alt']; ?>" /></dd>
			</dl>
		</div>

		<label><input type="radio" name="message" value="text" <?php if($_POST['message'] == "text") { echo 'checked="checked"'; }?> /> Text</label>
		<div class="choice-info">
			<textarea id="msg-text" id="msg-text" name="msg-text" rows="25" cols="90" style="height:20em;"><?php echo $_POST['msg-text']; ?></textarea>
		</div>
	</div>

</div>

<div class="row" style="text-align:right;">
	<input type="button" onclick="validateOnSubmit()" name="submit_form" value="Submit"> | <input type="submit" name="cancel" value="Cancel" /> 
</div>
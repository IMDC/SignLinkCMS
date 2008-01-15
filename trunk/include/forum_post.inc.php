<script type="text/javascript">
	<!--
	function set_adv(adv) {
		if (document.getElementById('adv_filters').style.display == '') {
			if (adv) {
				document.getElementById('adv_filters').style.display = 'block';
				document.getElementById('advanced_search').className = 'advanced_search_open';
				return;
			} else {
				document.getElementById('adv_filters').style.display = 'none';
				document.getElementById('advanced_search').className = 'advanced_search_closed';
			}
		}
	}

	function toggle_advanced() {
		if (document.getElementById) {
			vista = (document.getElementById('adv_filters').style.display == 'none') ? 'block' : 'none';
			theclass = (document.getElementById('advanced_search').className =='advanced_search_closed') ? 'advanced_search_open' : 'advanced_search_closed';
			arrow = (document.getElementById('adv_filters').style.display == 'none') ? 'arrow_up' : 'arrow_down';
			
			//console.log(theclass);
			document.getElementById('adv_filters').style.display = vista;
			document.getElementById('advanced_search').className = theclass;
			document.getElementById('pbk-disclosure-arrow').className = arrow;
			
			//imgfile = "administrator/components/com_peoplebook/images/"+vista+".jpg";
			//document.adv_arrow.src = imgfile;
		}
	}


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


<?php if(empty($_REQUEST['parent'])) { ?>
<div class="file-info">
	<span class="bold"><label>Subject</label></span><br />
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
<?php } ?>

<div class="important-info">
	<span class="bold">Message</span>
	<div class="choice">
		<label><input type="radio" name="message" value="signlink" <?php if($_POST['message'] == "signlink") { echo 'checked="checked"'; }?> />Signlink File</label>
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
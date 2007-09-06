<div class="input-form">

<?php if(empty($_REQUEST['parent'])) { ?>
	<div class="row">
		<span class="bold">Subject</span><br />
			<div class="row">
				<?php 
				if (isset($_POST['subject_file']) && !empty($_POST['subject_file'])) {
					echo '<img src="../uploads/'.$_POST['subject_file'].'" /><br />';
					//if video...
				}
				?>
				Video <span class="italic">or</span> Image File<br />
				<input type="file" name="subject_file" value="<?php echo $_POST['subject_file']; ?>" /> 
			</div>
			<div class="row">
				Text<br />
				<input type="text" name="subject" size="100" value="<?php echo $_POST['subject']; ?>" /><br />
			</div>
	</div>
<?php } ?>

	<div class="row">
		<span class="bold">Message</span>

		<div class="row">
			SignLink File<br />
			<input type="file" name="msg_file" />
		</div>
		<div class="row">
			Text<br />
			<textarea name="msg" rows="5" cols="90"><?php echo $_POST['msg']; ?></textarea>
		</div>
	</div>

	<div class="row" style="text-align:right;">
		<input type="submit" name="submit" value="Submit" /> | <input type="submit" name="cancel" value="Cancel" /> 
	</div>

</div>

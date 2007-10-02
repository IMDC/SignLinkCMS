<div class="input-form">

<?php if(empty($_REQUEST['parent'])) { ?>
	<div class="row">
		<span class="bold">Subject</span><br />
			<div class="row">
				<textarea name="title" style="height:14em; width:14em;"></textarea>
			</div>
	</div>
<?php } ?>

	<div class="row">
		<span class="bold">Message</span>
		<div class="row">
			<textarea name="msg" rows="25" cols="90" style="height:20em;"><?php echo $_POST['msg']; ?></textarea>
		</div>

		
		<div class="row">
		<p>If you've inserted a Signlink Flash object, you must also upload the Signlink FLV file that goes with it.<p>
			SignLink FLV File<br />
			<input type="file" name="msg_file" />
		</div>
	</div>

	<div class="row" style="text-align:right;">
		<input type="submit" name="submit" value="Submit" /> | <input type="submit" name="cancel" value="Cancel" /> 
	</div>

</div>

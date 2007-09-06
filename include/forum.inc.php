<form action ="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form" enctype="multipart/form-data">
<input type="hidden" name="fid" value="<?php echo $_REQUEST['fid']; ?>" />
<fieldset>
	<legend>Title</legend>
		
		<span class="bold">Video <span class="italic">or</span> Image</span>
		<div class="row">
			<?php 
				$img_file = '../uploads/'. $_POST['title_file'];
				if (!empty($_POST['title_file']) && file_exists($img_file)) {
					echo '<img src="'.$img_file.'" /> (<a href="forum_delete.php?id='.$_REQUEST['fid'].'&d=title">Delete</a>)<br /><br />';
				}
			?>
			Upload: <input type="file" name="title_file" /> 
		</div>
			<span class="bold">Text</span>
			<div class="row">
				<input type="text" name="title_txt" size="100" value="<?php echo $_POST['title_txt']; ?>" /><br />
				(for browser page title, alternative text for the title image (if provided), or the title of the page if an image or video has not been provided) 
			</div>
</fieldset>

<div class="row">
	<span class="form-title">Description</span><br />
	<textarea name="descrip" rows="5" cols="90" style="margin-top:7px;"><?php echo $_POST['descrip']; ?></textarea>
</div>

<div class="row" style="text-align:right;">
	<input type="submit" name="submit" value="Submit" /> | <input type="submit" name="cancel" value="Cancel" /> 
</div>

</form>
<form action ="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<div class="input-form">
	<h3>Page Info</h3>

	<div class="row url-info">
		<span class="bold">URL</span><br /> 
		http://<?php echo $_SERVER['SERVER_NAME']; ?>/filename.php
	</div>

	<div class="important-info">	
		<div class="row">
			<span class="bold">Title</span><br />
				<div class="row">
				<?php 
				if (isset($_POST['title_file'])) {
					echo '<img src="../uploads/'.$_POST['title_file'].'" />';
				}
				?>
				<br />Video <span class="italic">or</span> Image<br /><input type="file" name="title_file" value="<?php echo $_POST['title_file']; ?>" /> </div>
				<div class="row">Text<br /><input type="text" name="title_txt" size="100" value="<?php echo $_POST['title_txt']; ?>" /><br />
				Title text will be used for the browser page title, alternative text for the title image (if provided), or the title of the page if an image or video has not been provided. </div>
		</div>
		<div class="row">
			<span class="bold">Parent</span><br />
				<select>
					<option>list of pages</option>
				</select>
		</div>
	</div>

	<div class="row file-info">
		<span class="bold">SignLink File</span><br />
		<input type="file" name="flash" value="<?php echo $_POST['sl_file']; ?>" /><br />
		The Signlink file is the main content for the page. We suggest using the Signlink Workbench area to help you organise your content and signlinks before you begin filming).
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
	</div>
	<div class="row" style="text-align:right;">
		<input type="submit" name="submit" value="Submit" /> | <input type="submit" name="cancel" value="Cancel" /> 
	</div>


</div>
</form>
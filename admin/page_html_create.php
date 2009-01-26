<?php 
define('INCLUDE_PATH', '../include/');
require(INCLUDE_PATH.'vitals.inc.php');
admin_authenticate();

require(INCLUDE_PATH.'admin_header.inc.php'); ?>

<h2>Regular Page: New</h2>

<form>
<div class="input-form">

	<div class="row" style="text-align:center;">
		<span class="bold">URL</span>: http://<?php echo $_SERVER['SERVER_NAME']; ?>/filename.php
	</div>
	<div class="row">
		<span class="bold">Title</span><br />
			<div class="row"><input type="text" name="title_txt" size="100" /> </div>
	</div>

	<div class="row">
		<span class="bold">Text</span><br />
		<textarea name="notes" cols="100" rows="15" style="width:100%;"></textarea>
	</div>


	<div class="row" style="text-align:right">
		<input type="submit" name="submit" value="Submit" /> 
	</div>


</div>
</form>

<?php require('../include/footer.inc.php'); ?>

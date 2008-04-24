<?php 
define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');

if (isset($_POST['cancel'])) {
	if ($parent_id) {
		header('Location: forum_post_view.php?f='.$forum_id.'&p='.$parent_id.'&parent=1');
	} else {
		header('Location: forum_posts.php?f='.$forum_id);
	}
	exit;

} else if ($_POST['f'] || $_GET['processed']) {

	//check if there are any upload errors
	if(empty($_POST)) {
		$_SESSION['errors'][] = "General error. Your URL is incorrect or you are trying to post a file that is too large for this installation.";
		require(INCLUDE_PATH.'header.inc.php');
		require(INCLUDE_PATH.'footer.inc.php');
		exit;
	}
	check_uploads();
}

if (!$_SESSION['valid_user']) {
	$_SESSION['notices'][] = 'You must be logged in to manage your preferences. Please <a href="login.php">login</a>.';
	require(INCLUDE_PATH.'header.inc.php');
	require(INCLUDE_PATH.'footer.inc.php');
	exit;
}


require(INCLUDE_PATH.'header.inc.php');

$sql = "SELECT login,name,email FROM members WHERE login='".$_SESSION['login']."' AND member_id=".intval($_SESSION['member_id']);
$result = mysql_query($sql, $db);
if ($row = mysql_fetch_assoc($result)) { 
?>

<h2>Preferences</h2>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>?processed=1" method="post" name="form" enctype="multipart/form-data" style="clear:both; padding-top:2px;">
	<input type="hidden" name="f" value="<?php echo $forum_id; ?>" />
	<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAX_UPLOAD_SIZE; ?>" />
	
	<span class="bold"><label>Login</label></span><br />
	<div class="row">
		<?php echo $row['login'] ?><br /><br />
	</div>	
	
	<span class="bold"><label>Name</label></span><br />
	<div class="row">
		<input type="text" name="" value="<?php echo $row['name'] ?>" /><br /><br />
	</div>

	<span class="bold"><label>Email</label></span><br />
	<div class="row">
		<input type="text" name="" value="<?php echo $row['email'] ?>" /><br /><br />
	</div>

	<span class="bold"><label>Avatar</label></span><br />
	<div class="row">
		<input type="text" name="" value="" /><br /><br />
	</div>

	<div class="row" style="text-align:right;">
		<input type="button" onclick="<?php if($parent_id) { echo "validateOnSubmit('reply')"; } else { echo "validateOnSubmit('')"; } ?>" name="submit_form" value="Submit"> | <input type="submit" name="cancel" value="Cancel" /> 
	</div>
	
</form>

<?php
} else {
	echo "Invalid member ID.";
}

 require('include/footer.inc.php'); ?>

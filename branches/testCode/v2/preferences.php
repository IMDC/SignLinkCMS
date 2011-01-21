<?php 
define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');

if (!$_SESSION['valid_user']) {
	$_SESSION['notices'][] = 'You must be logged in to manage your preferences. Please <a href="login.php">login</a>.';
	require(INCLUDE_PATH.'header.inc.php');
	require(INCLUDE_PATH.'footer.inc.php');
	exit;
} else if ($_POST['submit'] || $_GET['processed']) {	
	//check if there are any upload errors
	if(empty($_POST)) {
		$_SESSION['errors'][] = "General error. Your URL is incorrect or you are trying to post a file that is too large for this installation.";
		require(INCLUDE_PATH.'header.inc.php');
		require(INCLUDE_PATH.'footer.inc.php');
		exit;
	}
	check_uploads();

	if (!isset($_SESSION['errors'])) {		
		$member_id = intval($_SESSION['member_id']);
		$name = $addslashes(htmlspecialchars($_POST['name']));
		$email = $addslashes(htmlspecialchars($_POST['email']));
		
		$sql = "UPDATE members SET name='$name', email='$email' WHERE member_id=$member_id";
		$result = mysql_query($sql, $db);
		
		if (!empty($_FILES['avatar']['tmp_name'])) {
			save_avatar($member_id);
		}
		
		$_SESSION['feedback'][] = 'Preferences updated.';
		header('Location: preferences.php');
		exit;
	}
}


require(INCLUDE_PATH.'header.inc.php');

$sql = "SELECT login,name,email FROM members WHERE login='".$_SESSION['login']."' AND member_id=".intval($_SESSION['member_id']);
$result = mysql_query($sql, $db);
if ($row = mysql_fetch_assoc($result)) { 
?>

<h2><img src="images/wrenchsmall.png" alt="Preferences" title="Preferences" style="padding:3px;" /></h2>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>?processed=1" method="post" name="form" enctype="multipart/form-data" style="clear:both; padding-top:2px;">
	<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAX_UPLOAD_SIZE; ?>" />
	
	<span class="bold"><label>Login</label></span><br />
	<div class="row">
		<?php echo $row['login'] ?><br /><br />
	</div>	
	
	<span class="bold"><label>Name</label></span><br />
	<div class="row">
		<input type="text" name="name" value="<?php echo $row['name'] ?>" /><br /><br />
	</div>

	<span class="bold"><label>Email</label></span><br />
	<div class="row">
		<input type="text" name="email" value="<?php echo $row['email'] ?>" /><br /><br />
	</div>

	<span class="bold"><label>Avatar</label></span><br />
	<div class="row">
		<?php get_avatar($_SESSION['member_id']) ?>
		<input type="file" name="avatar" /><br /><br />
	</div>

	<div class="row" style="float:left;text-align:center;background-color:#CEDFF5;padding: 5px;width:80px;margin-top:40px;">
		<input type="submit" name="submit" value="Submit" />
	</div>
	
</form>

<?php
} else {
	echo "Invalid member ID.";
}

 require('include/footer.inc.php'); ?>

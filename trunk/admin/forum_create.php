<?php 
define('INCLUDE_PATH', '../include/');
require(INCLUDE_PATH.'vitals.inc.php');
//admin_authenticate(AT_ADMIN_PRIV_USERS);

if (isset($_POST['cancel'])) {
	header('Location: '.INCLUDE_PATH.'../admin/forum_manage.php');
	exit;
} else if (isset($_POST['submit'])) {
	//error check
	if (empty($_POST['title-txt']) && empty($_FILES['title-file'])) {
		$_SESSION['errors'][] = 'Please enter a title.';
	} else {
		$title = $addslashes($_POST['title-txt']);
		$title_file = $addslashes($_POST['title-file']);
	}
	
	if (isset($_POST['descrip'])) {
		$descrip = $addslashes($_POST['descrip']);
	} 

	if (!isset($_SESSION['errors'])) {

		$sql = "INSERT INTO forums VALUES ('', '$title', '$final_filename', '$descrip', '','','')";
		$result = mysql_query($sql, $db);

		$fid = mysql_insert_id();

		if (is_uploaded_file($_FILES['title-file']['tmp_name'])) {	
			save_image('forum', 'title', 'title-file', $fid);
		}
	
		//redirect
		$_SESSION['feedback'][] = 'Forum created successfully.';
		header('Location: forum_manage.php');
		exit;
	}
} 

require(INCLUDE_PATH.'admin_header.inc.php'); ?>

<h2>Forum: New</h2>

	<?php require(INCLUDE_PATH.'forum.inc.php'); ?>

<?php require('../include/footer.inc.php'); ?>

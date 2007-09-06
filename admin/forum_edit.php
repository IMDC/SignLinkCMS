<?php 
define('INCLUDE_PATH', '../include/');
require(INCLUDE_PATH.'vitals.inc.php');
//admin_authenticate(AT_ADMIN_PRIV_USERS);

if (isset($_POST['cancel'])) {
	header('Location: '.INCLUDE_PATH.'../admin/forum_manage.php');
	exit;
} else if (isset($_POST['submit'])) {
	//error check
	if (empty($_POST['title_txt']) && empty($_FILES['title_file'])) {
		$_SESSION['errors'][] = 'Please enter a title.';
	} else {
		$title = $addslashes($_POST['title_txt']);
		$title_file = $addslashes($_POST['title_file']);
	}
	
	$descrip = $addslashes($_POST['descrip']);

	if (!isset($_SESSION['errors'])) {
		$fid = intval($_POST['fid']);

		if (!empty($_FILES['title_file']['name'])) {

			if (is_uploaded_file($_FILES['title_file']['tmp_name'])) {
				save_title($_FILES['title_file']['tmp_name']);	
			}
		}
		if (isset($final_filename)) {
			$sql = "UPDATE forums SET title='$title', title_file='$final_filename', description='$descrip' WHERE forum_id=".$fid;
		} else {
			$sql = "UPDATE forums SET title='$title', description='$descrip' WHERE forum_id=".$fid;
		}
		$result = mysql_query($sql, $db);
	
		//redirect
		$_SESSION['feedback'][] = 'Forum changes saved.';
		header('Location: forum_manage.php');
		exit;
	}
} else if (isset($_GET['fid'])) {
	$sql = "SELECT * FROM forums WHERE forum_id=".intval($_GET['fid']);
	$result = mysql_query($sql, $db);

	if ($row = mysql_fetch_assoc($result)) {
		$_POST['title_txt'] = $row['title'];
		$_POST['title_file'] = $row['title_file'];
		$_POST['sl_file'] = $row['sl_file'];
		$_POST['descrip'] = $row['description'];
		//links
	} else {
		//no such page
	}
}

require(INCLUDE_PATH.'admin_header.inc.php'); ?>

<h2>Forum: Edit</h2>
	<?php require(INCLUDE_PATH.'forum.inc.php'); ?>

<?php require('../include/footer.inc.php'); ?>

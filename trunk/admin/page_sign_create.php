<?php 
define('INCLUDE_PATH', '../include/');
require(INCLUDE_PATH.'vitals.inc.php');
//admin_authenticate(AT_ADMIN_PRIV_USERS);

//echo '<pre>'.print_r($_POST).'</pre>';

if (isset($_POST['cancel'])) {
	header('Location: '.AT_BASE_HREF.'admin/users.php');
	exit;
} else if (isset($_POST['submit'])) {
	//error check
	$parent_id = intval($_POST['parent_id']);

	if (empty($_POST['title_txt']) && empty($_POST['title_file'])) {
		$_SESSION['errors'][] = 'Please enter a title.';
	} else {
		$title = $addslashes($_POST['title_txt']);
		$title_file = $addslashes($_POST['title_file']);
	}
	
	if (isset($sl_file)) {
		$sl_file = $addslashes($_POST['sl_file']);
	} 

	$notes = $addslashes($_POST['notes']);

	$links = '';

	if (!isset($_SESSION['errors'])) {
		$sql = "INSERT INTO content VALUES ('', $parent_id, 'sl', '$title', '$title_file', '', '$sl_file', NOW(), '$notes', '$links')";
		$result = mysql_query($sql, $db);
	
		//redirect
		$_SESSION['feedback'][] = 'Page created successfully.';
		header('Location: page_manage.php');
		exit;
	}
} 

require(INCLUDE_PATH.'admin_header.inc.php'); ?>

<h2>SignLink Page: New</h2>

	<?php require(INCLUDE_PATH.'page_sign.inc.php'); ?>

<?php require('../include/footer.inc.php'); ?>

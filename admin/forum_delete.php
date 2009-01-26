<?php 
define('INCLUDE_PATH', '../include/');
require(INCLUDE_PATH.'vitals.inc.php');

admin_authenticate();

$forum_id = intval($_GET['f']);

if (isset($_GET['t']) && !empty($_GET['t'])) {
	//delete title file
	$sql = "UPDATE forums SET title_file='' WHERE forum_id=".$forum_id;
	$result = mysql_query($sql, $db);
	$_SESSION['feedback'][] = 'Forum title deleted.';

} else {
	//delete threads
	//$sql = "SELECT * FROM forums_threads WHERE forum_id=".intval($_GET['fid']);
	//$result = mysql_query($sql, $db);
	

	//delete forum
	$sql = "DELETE FROM forums WHERE forum_id=".$forum_id;
	$result = mysql_query($sql, $db);
	$_SESSION['feedback'][] = 'Forum deleted.';
}

//redirect
header('Location: forum_manage.php');
exit; 
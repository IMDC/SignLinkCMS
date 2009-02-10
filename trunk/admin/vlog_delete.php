<?php 
define('INCLUDE_PATH', '../include/');
require(INCLUDE_PATH.'vitals.inc.php');

admin_authenticate();

$vlog_id = intval($_GET['v']);

if (isset($_GET['t']) && !empty($_GET['t'])) {
	//delete title file
	/*$sql = "UPDATE forums SET title_file='' WHERE forum_id=".$forum_id;
	$result = mysql_query($sql, $db);
	$_SESSION['feedback'][] = 'Forum title deleted.';*/

} else {

	//delete vlog
	$sql = "DELETE FROM vlogs WHERE vlog_id=".$vlog_id;
	$result = mysql_query($sql, $db);
	$_SESSION['feedback'][] = 'Vlog deleted.';
}

//redirect
header('Location: vlog_manage.php');
exit; 
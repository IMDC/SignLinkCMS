<?php 
define('INCLUDE_PATH', '../include/');
require(INCLUDE_PATH.'vitals.inc.php');

admin_authenticate();

$member_id = intval($_GET['m']);

//delete avatar file
delete_avatar($member_id);

//delete forum
$sql = "DELETE FROM members WHERE member_id=".$member_id;
$result = mysqli_query($db, $sql);
$_SESSION['feedback'][] = 'Member deleted.';


//redirect
header('Location: member_manage.php');
exit; 

<?php 
define('INCLUDE_PATH', '../include/');
require(INCLUDE_PATH.'vitals.inc.php');
admin_authenticate();

require(INCLUDE_PATH.'admin_header.inc.php'); 

if (isset($_GET['c']) && !empty($_GET['c'])) {
	$page_id = intval($_GET['c']);
} else {
	$page_id = 0;
}

$sql = "SELECT * FROM pages WHERE page_id=".$page_id;
$result = mysql_query($sql, $db);	
$row = mysql_fetch_assoc($result);
if ($row) {
	echo get_title('page', $row['page_id']); 
	echo '<br /><br />';			
	echo get_content($row['page_id']);

} else {
 echo "No such page";
}

echo '<br style="clear:both;" /><br />';
echo '<a href="page_manage.php" />Back</a>';

require(INCLUDE_PATH.'footer.inc.php'); 
?>
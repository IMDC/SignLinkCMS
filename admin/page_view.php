<?php 
define('INCLUDE_PATH', '../include/');
require(INCLUDE_PATH.'vitals.inc.php');
admin_authenticate();

require(INCLUDE_PATH.'admin_header.inc.php'); 

$page_id = intval($_GET['c']);

$sql = "SELECT * FROM pages WHERE page_id=".$page_id;
$result = mysql_query($sql, $db);	
$row = mysql_fetch_assoc($result);
if ($row) {
	echo '<h3>'.get_title('page', $row['page_id']).'</h3>'; 
	echo get_content($row['page_id']);

} else {
 echo "No such page";
}

echo '<br style="clear:both;" /><br />';

//get sub pages
$sql = "SELECT page_id FROM pages WHERE parent_id=".$page_id;
$result = mysql_query($sql, $db);

if (@mysql_num_rows($result)) {
	echo '<br style="clear:both;" /><h2 style="margin-top:1em;"><img src="../images/pictures.png" alt="Sub-pages" title="Sub-pages" style="padding:3px;" /></h2><div id="block-container" >';
	while ($row = mysql_fetch_assoc($result)) {
		$title = get_title('page', $row['page_id']);
		?>

		<div class="page">
			<div class="title">
				<div style="height:150px;">
					<?php echo $title; ?>
				</div>
							
				<a href="page_view.php?c=<?php echo $row['page_id']; ?>" class="goto">
					<img src="../images/hand.png" style="border:0px;padding:0px;" />
				</a>
			</div>
		</div>	
<?php
	}
	echo '</div>';
}
echo '<div style="clear:both;" /><img src="../images/arrow_left.png" alt="Back" title="Back" style="margin-top:20px" class="buttonimage" onclick="javascript:history.back(1);" /></div>';

require(INCLUDE_PATH.'footer.inc.php'); 
?>
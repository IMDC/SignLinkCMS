<?php 
define('INCLUDE_PATH', '../include/');
require(INCLUDE_PATH.'vitals.inc.php');
admin_authenticate();

require(INCLUDE_PATH.'admin_header.inc.php'); 

// display a green arrow for user back navigation
echo '<div style="clear:both;" /><img src="../images/arrow_left.png" alt="Back" title="Back" style="margin-top:20px" class="buttonimage" onclick="javascript:history.back(1);" /></div>';
$page_id = intval($_GET['c']);

$sql = "SELECT * FROM pages WHERE page_id=".$page_id;
$result = mysqli_query($db, $sql);	
$row = mysqli_fetch_assoc($result);
if ($row) {
  $pagetitle = get_title('page', $row['page_id']);
  // replace the source path to the flowplayer plugin with the correct path one dir level back
  $pagetitle = preg_replace('/flash\/flowplayer/', '../flash/flowplayer', $pagetitle);
  // replace the source path of the zoom image of pictures with the correct path one dir level back
  $pagetitle = preg_replace('/img class="quickView" src="images/', 'img class="quickView" src="../images', $pagetitle);
  echo '<h3>'.$pagetitle.'</h3>';
	//echo '<h3>'.get_title('page', $row['page_id']).'</h3>'; 

	$page_content = preg_replace('/flash\/flowplayer/', '../flash/flowplayer', get_content($row['page_id']));
  echo htmlspecialchars_decode(preg_replace('/<br\\s*?\/??>/i', '', $page_content));

} else {
 echo "No such page";
}

echo '<br style="clear:both;" /><br />';

//get sub pages
$sql = "SELECT page_id FROM pages WHERE parent_id=".$page_id;
$result = mysqli_query($db, $sql);

if (@mysqli_num_rows($result)) {
	echo '<br style="clear:both;" /><h2 style="margin-top:1em;"><img src="../images/pictures.png" alt="Sub-pages" title="Sub-pages" style="padding:3px;" /></h2><div id="block-container" >';
	while ($row = mysqli_fetch_assoc($result)) {
		//$titlenew = get_title('page', $row['page_id']);
    $title = preg_replace('/flash\/flowplayer/', '../flash/flowplayer', get_title('page', $row['page_id'])); 
    $title = preg_replace('/img class="quickView" src="images/', 'img class="quickView" src="../images', $title);
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

require(INCLUDE_PATH.'footer.inc.php'); 
?>

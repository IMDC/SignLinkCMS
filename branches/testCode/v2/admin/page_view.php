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
   /*** Page Title ***/
   $page_title = get_title('page', $row['page_id']);
   // replace the source path to the flowplayer plugin with the correct path one dir level back
   $page_title = preg_replace('/flash\/flowplayer/', '../flash/flowplayer', $page_title);
   
   // for images: 
   // replace the source path of the zoom image of pictures with the correct path one dir level back
   $page_title = preg_replace('/img class="quickView" src="images/', 'img class="quickView" src="../images', $page_title);
   
// replace the id of the title as flowplayer doesn't like id's with "../" in them
   $page_title = preg_replace('/id="..\/uploads\/pages/', 'id="uploads/pages', $page_title);
   $page_title = preg_replace('/flowplayer\("..\/uploads\/pages/', 'flowplayer("uploads/pages', $page_title);

   echo '<h3>'.$page_title.'</h3>';
   //echo '<h3>'.get_title('page', $row['page_id']).'</h3>'; 
   /*** End outputting Page Title ***/
   
   /*** Page Content ***/
   $page_content = get_content($row['page_id']);
   // replace the source path to the flowplayer plugin with the correct path one dir level back
   $page_content = preg_replace('/flash\/flowplayer/', '../flash/flowplayer', $page_content);
   
   // replace the id of the content as flowplayer doesn't like id's with "../" in them
   $page_content = preg_replace('/id="..\/uploads\/pages/', 'id="uploads/pages', $page_content);
   $page_content = preg_replace('/flowplayer\("..\/uploads\/pages/', 'flowplayer("uploads/pages', $page_content);
   
   echo htmlspecialchars_decode(preg_replace('/<br\\s*?\/??>/i', '', $page_content));
   
   /*** End outputting Page Content ***/

}
else {
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

<?php 
define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');

require(INCLUDE_PATH.'header.inc.php'); ?>

<h2 style="margin:0px;"><img src="images/application_view_tile_large.png" alt="Pages" title="Pages" /></h2>

<?php
$sql = "SELECT * FROM pages WHERE 1 ORDER BY created ASC";
$result = mysql_query($sql, $db);
if (@mysql_num_rows($result)) { 
	echo '<div id="block-container">';
	while ($row = mysql_fetch_assoc($result)) {
		$title = get_title('page', $row['page_id']);
		?>

		<div class="cat">
			<div class="title">
				<div style="height:150px;">
					<?php echo $title; ?>
				</div>
							
				<a href="page_posts.php?f=<?php echo $row['page_id']; ?>" class="goto">
					<img src="images/hand.png" style="border:0px;padding:0px;" />
				</a>
			</div>
			
			<div style="float:right; padding-right:3px;">
			<?php		
				//get sub pages
				$sql = "SELECT post_id FROM pages WHERE parent_id=".$row['page_id'];
				$result2 = mysql_query($sql, $db);
				$sub_pages = intval(@mysql_num_rows($result2));

				echo "<span style='font-size: smaller;'> $sub_pages signlinks</span>";
			echo '</div>';

		echo '</div>';

	} ?>
		<br style="clear:both" />
	</div>

<?php
} else {
	echo "None found.";
}

require(INCLUDE_PATH.'footer.inc.php'); 
?>
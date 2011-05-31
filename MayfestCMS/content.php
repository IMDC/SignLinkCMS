<?php 
define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');

/* Restrict to registered users only */
if (REG_USER_ONLY == 1){
   user_authenticate();
}

require(INCLUDE_PATH.'header.inc.php');
?>

<?php

$top_pages = get_top_pages();

if ( !empty($top_pages) ) {
	echo '<div id="block-container">';
	foreach ($top_pages as $row) {
		$title = get_title('page', $row['page_id']);
?>

		<div class="cat">
			<div class="title" onclick="location.href='page_view.php?c=<?php echo $row['page_id']; ?>'" style="cursor:pointer">
			<!--<div class="title" style="cursor:pointer">-->
				<div style="height:150px;cursor:pointer;text-align:center;">
					<?php echo $title; ?>
				</div>
							
				<a href="page_view.php?c=<?php echo $row['page_id']; ?>" class="goto">
					<img src="images/hand.png" style="border:none;padding:5px;" class="hand_icon" alt="click to view" />
				</a>
			</div>
			
			<div style="float:right; padding-right:3px;">
			<?php		
				//get sub pages
				$sql = "SELECT page_id FROM pages WHERE parent_id=".$row['page_id'];
				$result2 = mysqli_query($db, $sql);
				$sub_pages = intval(@mysqli_num_rows($result2));

				echo "<span style='font-size: smaller;'> $sub_pages <img src='images/pictures.png' alt='Sub-pages' title='Sub-pages' /></span>";
			echo '</div>';

		echo '</div>';

	} ?>
		<br style="clear:both" />
	</div>

<?php
} else {
	echo "No content found.";
}

require(INCLUDE_PATH.'footer.inc.php'); 
?>

<?php 
define('INCLUDE_PATH', '../include/');
require(INCLUDE_PATH.'vitals.inc.php');
admin_authenticate();

require(INCLUDE_PATH.'admin_header.inc.php'); ?>
<!-- css adjustment for fancybox image zoom -->
<style>
   .quickViewLink {
      left: -65px;
      top: -30px;
   }
</style>
<h2>Forums</h2>
<ul id="page-menu">
	<li><a href="forum_create.php">New Forum</a></li> 
</ul>

<?php
//get forums
$sql = "SELECT * FROM forums WHERE 1";
$result = mysqli_query($db, $sql);
$r = 1;
if (mysqli_num_rows($result)) { ?>
	<table class="manage">
	<tr>
		<th>Title</th>
		<th>#Topics</th>
		<th>#Posts</th>
		<th>Last Post</th>
		<th style="text-align:center;">Manage</th>
	</tr>
	<?php
	while ($row = mysqli_fetch_assoc($result)) {

		$title = get_title('forum', $row['forum_id'],'small');
      // fix for image path in fancybox link
      $title = preg_replace('/img class="quickView" src="images/', 'img class="quickView" src="../images', $title);

		//print forum row info
		echo '<tr class="row'.$r.'">';
		echo '<td>'.$title.'</td>'; 
		echo '<td style="text-align:center;">'.$row['num_topics'].'</td>';
		echo '<td style="text-align:center;">'.$row['num_posts'].'</td>';
		echo '<td style="text-align:center;">'.$row['last_post'].'</td>';		
		echo '<td style="text-align:center;"><a href="forum_posts_manage.php?f='.$row['forum_id'].'">Posts</a>';
		echo ' | <a href="forum_edit.php?f='.$row['forum_id'].'">Edit</a>';
		echo ' | <a href="forum_delete.php?f='.$row['forum_id'].'" onclick="return confirm(\'Are you sure you want to delete this forum?\')">Delete</a></td>';
		echo '</tr>';
		if ($r == 1) {
			$r = 2;
		} else {
			$r = 1;
		}
	}
	echo '</table>';
} else {
	echo "None found.";
}
?>

<?php require('../include/footer.inc.php'); ?>

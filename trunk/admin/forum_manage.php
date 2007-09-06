<?php 
define('INCLUDE_PATH', '../include/');
require(INCLUDE_PATH.'vitals.inc.php');

require(INCLUDE_PATH.'admin_header.inc.php'); ?>

<h2>Forums</h2>
<ul id="page-menu">
	<li><a href="forum_create.php">New Forum</a></li> 
</ul>

<?php
//get forums
$sql = "SELECT * FROM forums WHERE 1";
$result = mysql_query($sql, $db);
$r = 1;
if (mysql_num_rows($result)) { ?>
	<table class="manage">
	<tr>
		<th>Title</th>
		<th>Description</th>
		<th>#Topics</th>
		<th>#Posts</th>
		<th>Last Post</th>
		<th>Manage</th>
	</tr>
	<?php
	while ($row = mysql_fetch_assoc($result)) {
	
		$title = get_title($row['title'], $row['title_file']);

		//print forum row info
		echo '<tr class="row'.$r.'">';
		echo '<td>'.$title.'</td>'; 
		echo '<td style="text-align:center;">'.$row['description'].'</td>';  //get parent id title
		echo '<td style="text-align:center;">'.$row['num_topics'].'</td>';
		echo '<td style="text-align:center;">'.$row['num_posts'].'</td>';
		echo '<td style="text-align:center;">'.$row['last_post'].'</td>';		
		echo '<td style="text-align:center;"><a href="forum_edit.php?fid='.$row['forum_id'].'">Edit</a>';
		echo ' | <a href="forum_delete.php?fid='.$row['forum_id'].'" onclick="return confirm(\'Are you sure you want to delete this forum?\')">Delete</a></td>';
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
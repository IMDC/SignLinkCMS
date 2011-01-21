<?php 
define('INCLUDE_PATH', '../include/');
require(INCLUDE_PATH.'vitals.inc.php');
admin_authenticate();

require(INCLUDE_PATH.'admin_header.inc.php'); 

$forum_id = intval($_GET['f']);
?>

<h2>Forum Posts</h2>

<div style="padding-bottom:8px;">
<?php
echo get_title('forum', $forum_id, "small");
echo '</div>';

//get forum posts
$sql = "SELECT * FROM forums_posts WHERE forum_id=".$forum_id." ORDER BY parent_id, subject";
$result = mysqli_query($db, $sql);
$r = 1;
if (mysql_num_rows($result)) { ?> 
	<table class="manage">
	<tr>	
		<th>ID</th>
		<th style="width:40%;">Subject</th>
		<th>Author</th>
		<th>Date</th>
		<th style="text-align:center;">Manage</th>
	</tr>
	<?php
	while ($row = mysql_fetch_assoc($result)) {
	
		$title = get_title('post', $row['post_id'], 'small');

		//print forum row info
		echo '<tr class="row'.$r.'">';
		echo '<td>'.$row['post_id'].'</td>';
		echo '<td>'.$title;
		if ($row['parent_id']) { echo get_title('post', $row['parent_id'], 'small'); }	
		echo '</td>'; 
		echo '<td>'.$row['login'].'</td>';	
		echo '<td>'.$row['date'].'</td>';	
		echo '<td style="text-align:center;">';
		//echo '<a href="forum_view_posts.php?f='.$forum_id.'&p='.$row['post_id'].'">View</a>';
		echo '<a href="forum_post_delete.php?f='.$forum_id.'&p='.$row['post_id'].'" onclick="return confirm(\'Are you sure you want to delete this post?\')">Delete</a></td>';
		echo '</tr>';
		if ($r == 1) {
			$r = 2;
		} else {
			$r = 1;
		}
	}
	echo '</table>';
} else {
	echo "<br />No posts found.";
}
?>


<?php require(INCLUDE_PATH.'footer.inc.php'); ?>

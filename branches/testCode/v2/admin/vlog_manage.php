<?php 
define('INCLUDE_PATH', '../include/');
require(INCLUDE_PATH.'vitals.inc.php');
admin_authenticate();

require(INCLUDE_PATH.'admin_header.inc.php'); ?>

<h2>Vlogs</h2>
<ul id="page-menu">
	<li><a href="vlog_create.php">New Vlog</a></li> 
</ul>

<?php
//get forums
$sql = "SELECT * FROM vlogs WHERE 1";
$result = mysqli_query($db, $sql);
$r = 1;
if (mysqli_num_rows($result)) { ?>
	<table class="manage">
	<tr>
		<th>Member</th>
		<th>Title</th>
		<th>#Entries</th>
		<th>Last Entry</th>
		<th style="text-align:center;">Manage</th>
	</tr>
	<?php
	while ($row = mysqli_fetch_assoc($result)) {

		$title = get_title('vlog', $row['vlog_id'],'small');

		//print vlog row info
		echo '<tr class="row'.$r.'">';
		echo '<td>'.$row['member_id'].'</td>';
		echo '<td>'.$title.'</td>'; 
		echo '<td style="text-align:center;">'.$row['num_entries'].'</td>';
		echo '<td style="text-align:center;">'.$row['last_entry'].'</td>';		
		echo '<td style="text-align:center;">';
		//<a href="vlog_posts_manage.php?v='.$row['vlog_id'].'">Posts</a>';
		//echo ' | <a href="vlog_edit.php?v='.$row['vlog_id'].'">Edit</a>';
		echo '<a href="vlog_delete.php?v='.$row['vlog_id'].'" onclick="return confirm(\'Are you sure you want to delete this vlog?\')">Delete</a></td>';
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

<?php require(INCLUDE_PATH.'footer.inc.php'); ?>

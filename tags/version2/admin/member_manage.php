<?php 
define('INCLUDE_PATH', '../include/');
require(INCLUDE_PATH.'vitals.inc.php');

admin_authenticate();

require(INCLUDE_PATH.'admin_header.inc.php'); ?>

<h2>Members</h2>

<p>The following members have registered:</p>

<?php
//get members
$sql = "SELECT * FROM members WHERE login!='admin'";
$result = mysql_query($sql, $db);
$r = 1;
if (mysql_num_rows($result)) { ?>
	<table class="manage">
	<tr>
		<th>ID</th>
		<th>Login</th>
		<th>Name</th>
		<th>Email</th>
		<th style="text-align:center;">Manage</th>
	</tr>
	<?php
	while ($row = mysql_fetch_assoc($result)) {
		//print forum row info
		echo '<tr class="row'.$r.'">';
		echo '<td>'.$row['member_id'].'</td>'; 
		echo '<td>'.$row['login'].'</td>';
		echo '<td>'.$row['name'].'</td>';
		echo '<td>'.$row['email'].'</td>';		
		echo '<td style="text-align:center;"><a href="member_edit.php?m='.$row['member_id'].'">Edit</a>';
		echo ' | <a href="member_delete.php?m='.$row['member_id'].'" onclick="return confirm(\'Are you sure you want to delete this member?\')">Delete</a></td>';
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

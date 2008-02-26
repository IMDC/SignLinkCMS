<?php 
define('INCLUDE_PATH', '../include/');
require(INCLUDE_PATH.'vitals.inc.php');

require(INCLUDE_PATH.'admin_header.inc.php'); ?>

<h2>Pages</h2>

<ul id="page-menu">
	<li><a href="page_sign_create.php">New SignLink Page</a> | </li> 
	<li><a href="page_html_create.php">New html Page</a></li> 
</ul>

<table class="manage">
	<tr>
		<th>Title</th>
		<th>Parent</th>
		<th>Type</th>
		<th>Status</th>
		<th>Manage</th>
	</tr>
	<?php
	//get pages
	$sql = "SELECT * FROM content WHERE 1";
	$result = mysql_query($sql, $db);
	$r = 1;
	while ($row = mysql_fetch_assoc($result)) {

		$title = get_title($row['title'], $row['title_file']);

		if (empty($row['sl_file']) && $row['type']=='sl') {
			//sl file missing
			$status = '<span style="color:red;">SL file missing</span>';
		} else {
			$status = '<span style="color:green;">Complete</span>';
		}

		//print page row info
		echo '<tr class="row'.$r.'">';
		echo '<td>'.$title.'</td>'; 
		echo '<td style="text-align:center;">'.$row['parent_id'].'</td>';  //get parent id title

		if ($row['type'] == 'sl') {
			echo '<td style="text-align:center;">SignLink</td>'; 
		} else {
			echo '<td style="text-align:center;">HTML</td>'; 
		}
		echo '<td style="text-align:center;">'.$status.'</td>';
		if ($row['type'] == 'sl') {
			echo '<td style="text-align:center;"><a href="page_sign_edit.php?cid='.$row['content_id'].'">Edit</a>';
		} else {
			echo '<td style="text-align:center;"><a href="page_reg_edit.php?cid='.$row['content_id'].'">Edit</a>';
		}
		echo ' | <a href="page_delete.php?cid='.$row['content_id'].'">Delete</a></td>';
		echo '</tr>';
		if ($r == 1) {
			$r = 2;
		} else {
			$r = 1;
		}
	}
	?>

</table>


<?php require('../include/footer.inc.php'); ?>
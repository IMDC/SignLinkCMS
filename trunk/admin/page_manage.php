<?php 
define('INCLUDE_PATH', '../include/');
require(INCLUDE_PATH.'vitals.inc.php');

require(INCLUDE_PATH.'admin_header.inc.php'); ?>

<h2>Pages</h2>

<ul id="page-menu">
	<li><a href="page_create.php">New Page</a></li> 
</ul>

<?php
//get pages
$sql = "SELECT * FROM pages WHERE 1 ORDER BY title, title_alt, parent_id";
$result = mysql_query($sql, $db);
$r = 1;

if ($row = mysql_fetch_assoc($result)) {

//add author to table
?>
<table class="manage">
	<tr>
		<th style="width:1em;">Id</th>	
		<th style="text-align:left;">Title</th>
		<th>Parent</th>
		<th>Created</th>
		<th>Manage</th>
	</tr>
	<?php
	do {

		$title = get_title('page', $row['page_id']);

		if (empty($row['sl_file']) && $row['type']=='sl') {
			//sl file missing
			$status = '<span style="color:red;">SL file missing</span>';
		} else {
			$status = '<span style="color:green;">Complete</span>';
		}

		//print page row info
		echo '<tr class="row'.$r.'">';
		echo '<td style="text-align:center;">'.$row['page_id'].'</td>'; 		
		echo '<td>'.$title.'</td>'; 
		echo '<td style="text-align:center;">'.$row['parent_id'].'</td>';  //get parent id title
		echo '<td style="text-align:center;">'.$row['created'].'</td>';
		echo '<td style="text-align:center;">';
		echo '<a href="page_view.php?c='.$row['page_id'].'">View</a>';
		echo ' | <a href="page_edit.php?c='.$row['page_id'].'">Edit</a>';
		echo ' | <a href="page_delete.php?c='.$row['page_id'].'" onclick="return confirm(\'Are you sure you want to delete this page?\')">Delete</a></td>';
		echo '</tr>';
		if ($r == 1) {
			$r = 2;
		} else {
			$r = 1;
		}
	} while ($row = mysql_fetch_assoc($result));
	?>

</table>


<?php 
} else {
	echo "No pages found";
}
require('../include/footer.inc.php'); ?>
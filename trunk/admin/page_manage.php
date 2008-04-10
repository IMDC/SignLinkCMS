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
$sql = "SELECT * FROM pages WHERE 1";
$result = mysql_query($sql, $db);
$r = 1;

if ($row = mysql_fetch_assoc($result)) {

?>
<table class="manage">
	<tr>
		<th>Title</th>
		<th>Parent</th>
		<!-- th>Type</th>
		<th>Status</th -->
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
		echo '<td>'.$title.'</td>'; 
		echo '<td style="text-align:center;">'.$row['parent_id'].'</td>';  //get parent id title

		/*if ($row['type'] == 'sl') {
			echo '<td style="text-align:center;">SignLink</td>'; 
		} else {
			echo '<td style="text-align:center;">HTML</td>'; 
		}
		echo '<td style="text-align:center;">'.$status.'</td>';*/
		echo '<td style="text-align:center;">';
		echo '<a href="page_view.php?c='.$row['page_id'].'">Preview</a>';
		echo ' | <a href="page_edit.php?c='.$row['page_id'].'">Edit</a>';
		echo ' | <a href="page_delete.php?c='.$row['page_id'].'">Delete</a></td>';
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
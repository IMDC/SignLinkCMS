<?php 
define('INCLUDE_PATH', '../include/');
require(INCLUDE_PATH.'vitals.inc.php');

require(INCLUDE_PATH.'admin_header.inc.php'); ?>

<h2>Forums</h2>
<ul id="page-menu">
	<li><a href="forum_manage.php">Back to Forums</a></li>
</ul>

<?php
//get pages
$sql = "SELECT * FROM forums WHERE 1";
$result = mysql_query($sql, $db);
$r = 1;
if (mysql_num_rows($result)) { ?>
	<table class="manage">
	<tr>
		<th>Name</th>
		<th>#Forums</th>>
	</tr>
	<?php
	while ($row = mysql_fetch_assoc($result)) {

	
		if (!empty($row['title_file'])) {
			$type = explode ('.', $row['title_file']);
			$type = $type[1];

			if ($type=='mov' || $type=='mp4' || $type=='avi') {
				$title = '<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B"
				id="clip"
				width="150" height="113"
				codebase="http://www.apple.com/qtactivex/qtplugin.cab">
				<param name="src" value="../uploads/'.$row['title_file'].'"/>
				<param name="autoplay" value="false"/>
				<param name="controller" value="true"/>
				<param name="scale" value="tofit"/>
				<embed src="../uploads/'.$row['title_file'].'" width="150" height="113" name="clip"
				autoplay="false" controller="true" enablejavascript="true" scale="tofit"
				alt="Quicktime ASL video"
				pluginspage="http://www.apple.com/quicktime/download/"
				style="float:left;" />
				</object>';
			} else {
				$title = '<img src="../uploads/'.$row['title_file'].'" alt="'.$row['title'].'" />';
			}
		} else {
			$title = $row['title'];
		}

		//print forum row info
		echo '<tr class="row'.$r.'">';
		echo '<td>'.$title.'</td>'; 

		echo '<td style="text-align:center;">'.$status.'</td>';
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
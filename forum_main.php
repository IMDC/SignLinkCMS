<?php 
define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');

require(INCLUDE_PATH.'header.inc.php');
?>

<h2>Forums</h2>

<?php
$sql = "SELECT * FROM forums WHERE 1";
$result = mysql_query($sql, $db);
$r = 1;
if (mysql_num_rows($result)) { 
	echo '<div>';
	while ($row = mysql_fetch_assoc($result)) {
		$title = get_title('forum', $row['forum_id']);

		echo '<div class="cat">';
			echo '<div style="float:left">'.$title.'</div>';
			echo '<div style="float:right; font-size:small; text-align:left;">';
				echo '<a href="forum_posts.php?f='.$row['forum_id'].'"><img src="images/folder_go.png" alt="enter" /></a><br />';
				echo '<img src="images/email.png" alt="new messages: " style="margin-bottom:-6px;" /> 4 <br />';
				echo '<img src="images/email_open.png" alt="read messages: " style="margin-bottom:-6px;" /> 20';
			echo '</div>';
		echo '</div>';
	}
	echo '</div>';

} else {
	echo "None found.";
}

 require('include/footer.inc.php'); ?>

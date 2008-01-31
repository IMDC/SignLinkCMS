<?php 
define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');

require(INCLUDE_PATH.'header.inc.php');
?>

<h2>Forums</h2>

<?php
$sql = "SELECT * FROM forums WHERE 1 ORDER BY title ASC";
$result = mysql_query($sql, $db);
if (mysql_num_rows($result)) { 
	echo '<div id="block-container">';
	while ($row = mysql_fetch_assoc($result)) {
		$title = get_title('forum', $row['forum_id']);

		echo '<div class="cat">';			
			echo '<a href="forum_posts.php?f='.$row['forum_id'].'"><div class="title">'.$title.'</div></a>';
			echo '<div style="float:left;">';
				if ($new_messages) {
					echo '<img src="images/email_red.png" alt="new messages!" title="new messages!" style="margin-bottom:-6px;" /> ';
				} else {
					echo '<img src="images/email.png" alt="messages" title="new messages" style="margin-bottom:-6px;" /> ';
				}
				
				//get post info
				$sql = "SELECT post_id FROM forums_posts WHERE forum_id=".$row['forum_id'];
				$result2 = mysql_query($sql, $db);
				$posts = @mysql_num_rows($result2);

				$sql = "SELECT * FROM forums_posts WHERE forum_id=".$row['forum_id']." AND parent_id=0";
				$result2 = mysql_query($sql, $db);
				$topics = @mysql_num_rows($result2);

				echo "<span style='font-size: smaller;'> $posts posts in $topics topics</span>";
			echo '</div>';
				//echo '<div style="float:right;"><a href="forum_posts.php?f='.$row['forum_id'].'"><img src="images/arrow_right.png" alt="enter" /></a></div>';

		echo '</div>';

	} ?>
		<br style="clear:both" />
		<div id="paging">
			Page: 1, 2, 3...
		</div>
	
	</div>

<?php
} else {
	echo "None found.";
}

 require('include/footer.inc.php'); ?>

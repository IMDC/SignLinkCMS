<?php 
define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');

require(INCLUDE_PATH.'header.inc.php');

$sql = "SELECT * FROM forums WHERE 1 ORDER BY subject ASC";
$result = mysql_query($sql, $db);
if (@mysql_num_rows($result)) { 
	echo '<div id="block-container">';
	while ($row = mysql_fetch_assoc($result)) {
		$title = get_title('forum', $row['forum_id']);
		?>

		<div class="cat">
			<div class="title">
				<div style="height:150px;">
					<?php echo $title; ?>
				</div>
							
				<a href="forum_posts.php?f=<?php echo $row['forum_id']; ?>" class="goto">
					<img src="images/hand.png" style="border:0px;padding:0px;" />
				</a>
			</div>
			
			<div style="float:left;">
			<?php
				//.... finish sql: join with forums_posts			
				$sql2 = "SELECT * FROM forums_read WHERE forum_id=".$row['forum_id']." AND member_id=".intval($_SESSION['member_id']);
				$result2 = mysql_query($sql2, $db);
				if (@mysql_num_rows($result2)) { 
					echo '<img src="images/email.png" alt="messages" title="new messages" height="16" width="16" /> ';
				} else {
					echo '<img src="images/email_red.png" alt="new messages!" title="new messages!" height="16" width="16" /> ';
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

		echo '</div>';

	} ?>
		<br style="clear:both" />
	</div>

<?php
} else {
	echo "None found.";
}

 require('include/footer.inc.php'); ?>

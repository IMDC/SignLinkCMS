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
				//get post info
				$sql = "SELECT post_id FROM forums_posts WHERE forum_id=".$row['forum_id'];
				$result2 = mysql_query($sql, $db);
				$posts = @mysql_num_rows($result2);

				$sql = "SELECT * FROM forums_posts WHERE forum_id=".$row['forum_id']." AND parent_id=0";
				$result2 = mysql_query($sql, $db);
				$topics = @mysql_num_rows($result2);

				//check for new messages - get number of posts in the forums, get number of read forum posts in forum_read. if equal, no unread
				$sql = "SELECT * FROM forums_read WHERE forum_id=".$row['forum_id']." AND member_id=".intval($_SESSION['member_id']);
				$result2 = mysql_query($sql, $db);
				$read = @mysql_num_rows($result2);
				
				if ($_SESSION['valid_user'] && $posts>$read) { 
					echo '<img src="images/email_red.png" alt="new messages" title="new messages" height="16" width="16" /> ';					
				} else {
					echo '<img src="images/email.png" alt="no new messages" title="no new messages" height="16" width="16" /> ';
				}

				echo "<span style='font-size: smaller;'> $posts posts in $topics topics</span>";
			echo '</div>';

		echo '</div>';

	} ?>
		<br style="clear:both" />
	</div>

<?php
} else {
	echo "No forums found.";
}

 require('include/footer.inc.php'); ?>

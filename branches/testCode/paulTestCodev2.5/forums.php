<?php 
define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');

/* Restrict to registered users only */
if (REG_USER_ONLY == 1){
   user_authenticate();
}

require(INCLUDE_PATH.'header.inc.php');

$sql = "SELECT * FROM forums WHERE 1 ORDER BY subject ASC";
$result = mysqli_query($db, $sql);
if (@mysqli_num_rows($result)) { 
	echo '<div id="block-container">';
	while ($row = mysqli_fetch_assoc($result)) {
		$title = get_title('forum', $row['forum_id']);
		?>

		<div class="cat">
			<div class="title-upper" onclick="location.href='forum_posts.php?f=<?php echo $row['forum_id']; ?>'">
				<div class="title-inner">
					<?php echo $title; ?>
				</div>
            <div class="title-goto-wrap">
               <a href="forum_posts.php?f=<?php echo $row['forum_id']; ?>"  class="goto">
                  <img src="images/hand.png" style="border:0px;padding:0px;" alt="click to view" />
               </a>
            </div>
			</div>
			
			<div class="cat-forum-lower">
			<?php
				//get post info
				$sql = "SELECT post_id FROM forums_posts WHERE forum_id=".$row['forum_id'];
				$result2 = mysqli_query($db, $sql);
				$posts = @mysqli_num_rows($result2);

				$sql = "SELECT * FROM forums_posts WHERE forum_id=".$row['forum_id']." AND parent_id=0";
				$result2 = mysqli_query($db, $sql);
				$topics = @mysqli_num_rows($result2);

				//check for new messages - get number of posts in the forums, get number of read forum posts in forum_read. if equal, no unread
				$sql = "SELECT * FROM forums_read WHERE forum_id=".$row['forum_id']." AND member_id=".intval($_SESSION['member_id']);
				$result2 = mysqli_query($db, $sql);
				$read = @mysqli_num_rows($result2);
				
				if ($_SESSION['valid_user'] && $posts>$read) { 
					echo '<img src="images/email_red.png" alt="new messages" title="new messages" height="16" width="16" style="border:none;" /> ';					
				} else {
					echo '<img src="images/email.png" alt="no new messages" title="no new messages" height="16" width="16" style="border:none;" /> ';
				}

				echo "<span class='forum-lower-info'> $posts posts in $topics topics</span>";
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

<?php 
define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');

require('include/header.inc.php'); 
?>

<div id="post-title">
	<h2><?php echo get_title('forum', intval($_GET['f']), 'small'); ?></h2>
	<ul id="submenu" style="margin-top:41px;">	
		<li><a href="forums.php?f=<?php echo intval($_GET['f']); ?>"><img src="images/arrow_left.png" alt="Back to forums" title="Back to forums" /></a></li>	
		<li><a href="forum_post_create.php?f=<?php echo intval($_GET['f']); ?>"><img src="images/user_comment.png" alt="Start a new topic" title="Start a new topic" /></a></li>			
	</ul>	
	<div style="clear:both" /></div>
</div>
<?php

$sql = "SELECT count(post_id) FROM forums_posts WHERE forum_id=".intval($_REQUEST['f'])." AND parent_id=0";
$result = mysql_query($sql, $db);
$total = mysql_fetch_assoc($result);

$perpage = 4;
if (isset($_GET['page'])) {
    $page = $_GET['page'];
} else {
	$page = 1;
}
$offset = ($page - 1) * $perpage;

$sql = "SELECT * FROM forums_posts WHERE forum_id=".intval($_REQUEST['f'])." AND parent_id=0 ORDER BY last_comment DESC LIMIT $offset, $perpage";
$result = mysql_query($sql, $db);
if (mysql_num_rows($result)) { 
	echo '<div>';
	
	for($page = 1; $page <= $maxPage; $page++) {
	   if ($page == $pageNum) {
		  $nav .= $page;
	   } else {
		  $nav .= '<a href="'.$_SERVER['PHP_SELF'].'page='.$page.'">'.$page.'</a>';
	   }
	}	
	
	while ($row = mysql_fetch_assoc($result)) {
		$title = get_title('post', $row['post_id']); 

		$sql2 = "SELECT views FROM forums_views WHERE post_id=".$row['post_id'];
		$result2 = mysql_query($sql2, $db);
		$views = mysql_fetch_assoc($result2);
		$views = intval($views['views']);
?>
		<div class="cat">
			
			<!-- div style="padding-right:2px;font-size:smaller;">
				<div style="float:left;">
					<?php echo date('g:ia, M j, y', strtotime($row['date'])); ?>
				</div>
				<div style="float:right;">
					<img src="images/user_female.png" style="margin-bottom:-5px;" /><?php echo $row['login']; ?>	
				</div>
			</div -->

			<div class="title">
				<div style="height:150px">
					<?php echo $title; ?>
				</div>							

				<a href="forum_post_view.php?f=<?php echo $row['forum_id']; ?>&p=<?php echo $row['post_id']; ?>" class="goto">
					<img src="images/hand.png" style="border:0px;padding:0px;" />
				</a>
			</div>

			<div>
				<div style="text-align:left;padding-right:2px; font-size:smaller;">
					<div style="float:left;">
						<?php //check for new messages - #comments vs number of read child posts in forum_read. if equal, no unread
						
						$sql = "SELECT * FROM forums_read WHERE (post_id=".$row['post_id']." OR parent_id=".$row['post_id'].") AND member_id=".intval($_SESSION['member_id']);
						$result2 = mysql_query($sql, $db);
						$read = @mysql_num_rows($result2);
												
						if ($_SESSION['valid_user'] && $row['num_comments']+1>$read) { 
							echo '<img src="images/email_red.png" alt="new messages" title="new messages" height="16" width="16" /> ';					
						} else {
							echo '<img src="images/email.png" alt="no new messages" title="no new messages" height="16" width="16" /> ';
						} ?>
					</div>
					<div style="float:right;">
						<img src="images/comments.png" style="margin-bottom:-5px;" alt="number of replies:" title="number of replies" /> <?php echo $row['num_comments']; ?>
						<img src="images/magnifier.png" style="margin-bottom:-5px;" alt="number of views:" title="number of views" /><?php echo $views; ?>
					</div>
					<div style="clear:both;">Last: <?php echo date('g:ia, M j, y', strtotime($row['last_comment'])); ?></div>
				</div>
			</div>
		</div>
<?php
	} ?>
		<br style="clear:both" />
		<div id="paging">
			
		</div>
	</div>
<?php
} else {
	echo "<p>No topics yet.</p>";
}
?>


<?php require('include/footer.inc.php'); ?>

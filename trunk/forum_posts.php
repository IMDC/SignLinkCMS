<?php 
define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');

require('include/header.inc.php'); 
?>

<div style="float:right">
	<a href="forum_post_create.php?f=<?php echo intval($_GET['f']); ?>"><img src="images/user_comment.png" alt="Start a new topic" title="Start a new topic" /></a>
</div>

<h2><?php echo get_title('forum', intval($_GET['f'])); ?></h2>

<?php
$sql = "SELECT * FROM forums_posts WHERE forum_id=".intval($_REQUEST['f'])." AND parent_id=0 ORDER BY last_comment DESC";
$result = mysql_query($sql, $db);
if (mysql_num_rows($result)) { 
	echo '<div>';
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
			</div-->

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
					<img src="images/comments.png" style="margin-bottom:-5px;" /> <?php echo $row['num_comments']; ?>
					<img src="images/magnifier.png" style="margin-bottom:-5px;" /><?php echo $views; ?>
					<br />
					Last: <?php echo date('g:ia, M j, y', strtotime($row['last_comment'])); ?>
				</div>
			</div>
		</div>
<?php
	} ?>
		<br style="clear:both" />
		<div id="paging">
			Page: 1, 2, 3...
		</div>
	</div>
<?php
} else {
	echo "<p>None found.</p>";
}
?>


<?php require('include/footer.inc.php'); ?>
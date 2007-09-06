<?php 
define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');

require('include/header.inc.php'); 
?>

<h2><?php echo get_title('forum', intval($_GET['f'])); ?></h2>

<a href="forum_post_create.php?f=<?php echo intval($_GET['f']); ?>">Start a new topic</a>

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
		$views = $views['views'];
?>
		<div class="cat" style="float:left; margin-bottom:50px; border:1px solid black;">
			
			<div style="float:left;font-size:smaller;">
				<img src="images/bullet_star.png" />
			</div>
			<div style="float:right;padding-right:2px;font-size:smaller;">
				Last post: <?php echo date('g:ia, M j, y', strtotime($row['last_comment'])); ?>
			</div>

			<a href="forum_post_view.php?f=<?php echo $row['forum_id']; ?>&parent=<?php echo $row['post_id']; ?>"><div style="background-color:white; height:150px; clear:both; padding:10px; margin-bottom:7px; border:1px solid #9A9A9A;"><?php echo $title; ?></div></a>

			<div>
				<div style="float:left;">
					<img src="images/user_female.png" style="margin-bottom:-5px;" /><?php echo $row['login']; ?>
				</div>
				<div style="float:right;padding-right:2px;">
					<img src="images/magnifier.png" /><?php echo $views; ?>
					<img src="images/email_go.png" /> <?php echo $row['num_comments']; ?>
				</div>
				<br style="clear:both;" />
			</div>
		</div>
<?php
	}
	echo '</div>';
} else {
	echo "<p>None found.</p>";
}
?>
	<!-- tr>
		<td style="width:20px;"><img src="images/email.png" alt="unread messages" /></td>
		<td><object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B"
			id="clip"
			width="150" height="113"
			codebase="http://www.apple.com/qtactivex/qtplugin.cab">
			<param name="src" value="videos/BJ_Welcome_movie.mp4"/>
			<param name="autoplay" value="false"/>
			<param name="controller" value="true"/>
			<param name="scale" value="tofit"/>
			<embed src="videos/BJ_Welcome_movie.mp4" width="150" height="113" name="clip"
			autoplay="false" controller="true" enablejavascript="true" scale="tofit"
			alt="Quicktime ASL video"
			pluginspage="http://www.apple.com/quicktime/download/"/>
		</object></td>
		<td><a href="">heidi</a></td>
		<td>5</td>
		<td>15</td>
		<td>Today at 16:56<br />
			by: RonnyK
		</td>
	</tr -->


<?php require('include/footer.inc.php'); ?>

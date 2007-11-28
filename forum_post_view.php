<?php 
define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');

require('include/header.inc.php'); 

$parent = intval($_REQUEST['parent']);

//if ($_SESSION['valid_user']) {
	//update the # thread views and the last accessed date
	$sql = "INSERT INTO forums_views VALUES ($parent, $_SESSION[member_id], NOW(), 0)";
	$result = mysql_query($sql, $db);
	if (!$result) {
		$sql = "UPDATE forums_views SET last_accessed=NOW(), views=views+1 WHERE post_id=$parent AND member_id=$_SESSION[member_id]";
		$result = mysql_query($sql, $db);
	}
//}

$msg = get_message('message', $parent);
//returns array of poster, date, message

?>

<h2 style="display:inline;"><a href="forum_posts.php?f=<?php echo intval($_GET['f']); ?>"><?php echo get_title('forum', intval($_GET['f'])); ?></a> > <?php echo get_title('post', $parent); ?></h2>

<div>		
		<div id="post">
			
			<div style="float:left;font-size:smaller;">
					<img src="images/user_female.png" style="margin-bottom:-5px;" /><?php echo $login; ?>
			</div>
			<div style="float:right;padding-right:2px;font-size:smaller;">
				<?php echo $date; ?>
			</div>

			<div style="clear:both; padding:10px; margin-bottom:7px;">
				<?php 
					echo $message;
				?>
			</div>


			<div style="float:right;padding-right:2px;">
				<a href="forum_post_create.php?f=<?php echo intval($_GET['f']); ?>&parent=<?php echo intval($_REQUEST['parent']); ?>">Reply</a> | <a href="">Edit</a>
			</div>
		</div>

	<br style="clear:both" />
</div>


<div id="replies">
	<h3>Responses</h3>

<?php
$sql = "SELECT * FROM forums_posts WHERE forum_id=".intval($_REQUEST['f'])." AND parent_id=".intval($_REQUEST['parent'])." ORDER BY last_comment DESC";
$result = mysql_query($sql, $db);
if (mysql_num_rows($result)) { 
	while ($row = mysql_fetch_assoc($result)) { ?>
		<div style="background-color:#efefef; margin-bottom:10px; padding:3px;">

		<div style="float:right;padding-right:2px;font-size:smaller;">
			<?php echo date('h:ia | M j, y', strtotime($row['last_comment'])); ?>
		</div>

		<?php
		if(!empty($row['msg_file'])) {
			$ext = explode('.',$row['msg_file']);
			$ext = $ext[1];
			
			switch ($ext) {
				case ('gif'||'png'||'jpeg'||'jpg' ):
					echo '<a href="forum_post_view.php?f=3&parent=13">image</a>';
					break;
				case ('mov' || $file_type=='mp4' || $file_type=='avi'):
					break;
			}
		} else {
			echo $row['msg'];
		}
		echo '</div>';
	}
} else {
	echo "<p>None found.</p>";
}
?>

</div>

<?php require('include/footer.inc.php'); ?>

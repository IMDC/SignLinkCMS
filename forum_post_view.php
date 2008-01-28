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

$msg = get_message($parent);  //returns array of poster, date, html-encoded message

?>

<h2><a href="forum_posts.php?f=<?php echo intval($_GET['f']); ?>"><?php echo get_title('forum', intval($_GET['f'])); ?></a></h2>

<div id="post">		
	<div id="post-info">
		<?php echo $msg[0]; ?><br />
		<img src="images/user_female.png" />
	</div>

	<div id="post-msg">
		<div style="float:right;">
			<a href="forum_post_create.php?f=<?php echo intval($_GET['f']); ?>&parent=<?php echo intval($_REQUEST['parent']); ?>">Reply</a> | <a href="">Edit</a>
		</div>

		<h3 style="margin:0px;"><?php echo get_title('post', $parent); ?></h3>
		<div style="clear:both; width:100%;">
		<small><?php echo $msg[1]; ?></small><br />
		<?php  echo $msg[2]; ?>
		</div>
	</div>
	<br style="clear:both" />
</div>


<div id="replies">
	<?php
	$sql = "SELECT * FROM forums_posts WHERE forum_id=".intval($_REQUEST['f'])." AND parent_id=".intval($_REQUEST['parent'])." ORDER BY last_comment DESC";
	$result = mysql_query($sql, $db);
	if (mysql_num_rows($result)) { ?>
		<table>
		<tr style="background-color:black; color:white;">
			<th>Reply</th>
			<th>Author</th>
			<th>Date</th>
		</tr>
		<?php 
			while ($row = mysql_fetch_assoc($result)) { ?>
			<tr>
				<?php print_reply_link($row['post_id']); ?>
				<td>
					<?php echo date('M j Y, h:ia', strtotime($row['last_comment'])); ?>
				</td>
			</tr>
		<?php
		}
	} else {
		echo "<p>No replies yet.</p>";
	}
	?>

</div>

<?php require('include/footer.inc.php'); ?>

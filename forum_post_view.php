<?php 
define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');

require('include/header.inc.php'); 

if ($_REQUEST['parent'] == 1) {
	$parent_id = intval($_REQUEST['p']);
}

$post_id = intval($_REQUEST['p']);
$forum_id = intval($_REQUEST['f']);

if ($_SESSION['valid_user']) {
	//update the # thread views and the last accessed date
	$sql = "INSERT INTO forums_views VALUES ($parent_id, $_SESSION[member_id], NOW(), 0)";
	$result = mysql_query($sql, $db);
	if (!$result) {
		$sql = "UPDATE forums_views SET last_accessed=NOW(), views=views+1 WHERE post_id=$parent_id AND member_id=$_SESSION[member_id]";
		$result = mysql_query($sql, $db);
	}
}

$msg = get_message($post_id);  //returns array of poster, date, html-encoded message

?>

<h2><a href="forum_posts.php?f=<?php echo $forum_id; ?>"><?php echo get_title('forum', $forum_id); ?></a></h2>

<div id="post">		
	<div id="post-info">
		<?php echo $msg[0]; ?><br />
		<img src="images/user_female.png" />
	</div>

	<div id="post-msg">
		<div style="float:right;">
			<?php 
			if (isset($parent_id)) { 
				echo "<a href='forum_post_create.php?f=$forum_id&p=$post_id'>Reply</a>&nbsp;";
			}
			if ($_SESSION['login'] == $msg[0]) {
				echo "<a href='forum_post_edit.php?f=$forum_id&p=$post_id&parent=$_GET[parent]'>Edit</a>";
			}
			?>
		</div>

		<h3 style="margin:0px;"><?php echo get_title('post', $post_id); ?></h3>
		<div style="clear:both; width:100%;">
		<small><?php echo $msg[1]; ?></small><br />
		<?php  echo $msg[2]; ?>
		</div>
	</div>
	<br style="clear:both" />
</div>

<?php
if (isset($parent_id)) { 

	$sql = "SELECT * FROM forums_posts WHERE forum_id=".$forum_id." AND parent_id=".$parent_id." ORDER BY last_comment DESC";
	$result = mysql_query($sql, $db);
	if (mysql_num_rows($result)) { ?>
		<table class="manage">
		<tr>
			<th style="width:50%">Reply</th>
			<th>Author</th>
			<th>Date</th>
		</tr>
		<?php 
			while ($row = mysql_fetch_assoc($result)) { ?>
			<tr>
				<?php print_reply_link($row['post_id']); ?>
				<td style="text-align:center">
					<?php echo date('M j Y, h:ia', strtotime($row['last_comment'])); ?>
				</td>
			</tr>
		<?php
		}
	} else {
		echo "<p>No replies yet.</p>";
	}
}
?>


<?php require('include/footer.inc.php'); ?>

<?php 
define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');

require('include/header.inc.php'); 

$parent_id = intval($_REQUEST['par']);
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
	
	//update that this member viewed this post
	$sql = "REPLACE INTO forums_read VALUES ($post_id, $_SESSION[member_id], $forum_id)";
	$result = mysql_query($sql, $db);	
}

$msg = get_message($post_id);  //returns array of poster, date, html-encoded message

?>

<div class="cat">
	<div class="title">
		<div style="height:150px">
			<?php echo get_title('forum', $forum_id); ?>
		</div>							

		<a href="forum_posts.php?f=<?php echo $forum_id; ?>" class="goto">
			<img src="images/hand.png" style="border:0px;padding:0px;" alt="View forum" title="View forum" />
		</a>
	</div>
</div>

<div style="float:left; margin-top:4em;font-size:medium;font-weight:bold;"> > 
	<?php 
	if ($parent_id) {
		echo "Re: "; 
	} 
	?>
</div>

<div class="cat">
	<div class="title">
		<div style="height:150px">
			<?php 
			if ($parent_id) {
				echo get_title('post', $parent_id); 
			} else {
				echo get_title('post', $post_id); 
			}
			?>
		</div>							

		<a href="forum_posts.php?f=<?php echo $forum_id; ?>" class="goto">
			<img src="images/hand.png" style="border:0px;padding:0px;" alt="View post" title="View post" />
		</a>
	</div>
</div>

<br style="clear:both" />
<div id="post">		
	<div id="post-info">
		<?php echo $msg[0]; ?><br />
		<img src="images/user_female.png" alt="avatar" />
	</div>

	<div id="post-msg">
		<div style="float:right;">
			<?php 
			if (!$parent_id) { 
				echo "<a href='forum_post_create.php?f=$forum_id&p=$post_id'><img src='images/comment_add.png' alt='Reply' title='Reply' /></a>&nbsp;";
			}
			if ($_SESSION['login'] == $msg[0]) {
				echo "<a href='forum_post_edit.php?f=$forum_id&p=$post_id&par=$parent_id'><img src='images/comment_edit.png' alt='Edit' title='Edit' /></a>";
			}
			?>
		</div>
		<div style="width:100%;">
			<small><?php echo $msg[1]; ?></small><br />
			<?php  echo $msg[2]; ?>
		</div>
	</div>
	<br style="clear:both" />
</div>
<?php
if (!$parent_id) { 

	$sql = "SELECT * FROM forums_posts WHERE forum_id=".$forum_id." AND parent_id=".$post_id." ORDER BY last_comment DESC";
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

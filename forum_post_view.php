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
	debug($sql);
	$result = mysql_query($sql, $db);	
}

$msg = get_message($post_id);  //returns array of poster, date, html-encoded message

?>

<div id="post-title">
	<div style="float:left;height:75px;"><?php echo get_title('forum', $forum_id, 'small'); ?></div>
	<div style="float:left;height:75px;">&nbsp; > &nbsp;<?php if ($parent_id) { echo "Re: "; } ?></div>
	
	<div style="float:left; vertical-align:middle; height:75px;">
	<?php 
	if ($parent_id) {
		echo get_title('post', $parent_id,'small'); 
	} else {
		echo get_title('post', $post_id, 'small'); 
	} ?>
	</div>

	<ul id="submenu" style="margin-top:41px;">	
		<li><a href="forums.php?f=<?php echo intval($_GET['f']); ?>"><img src="images/arrow_left.png" alt="Back to forums" title="Back to forums" /></a></li>	
		<?php 
		if (!$parent_id) { 
			echo "<li><a href='forum_post_create.php?f=$forum_id&p=$post_id'><img src='images/comment_add.png' alt='Reply' title='Reply' /></a></li>";
		}
		if ($_SESSION['login'] == $msg[0]) {
			echo "<li><a href='forum_post_edit.php?f=$forum_id&p=$post_id&par=$parent_id'><img src='images/comment_edit.png' alt='Edit' title='Edit' /></a></li>";
		}
		?>		
		
	</ul>	
	<div style="clear:both" /></div>
</div>

<div id="post">		
	<div id="post-info">
		<?php echo $msg[0]; ?><br />
		<img src="images/user_female.png" alt="avatar" />
	</div>

	<div id="post-msg">
		<div style="width:100%;">
			<small><?php echo $msg[1]; ?></small><br />
			<?php  echo $msg[2]; ?>
		</div>
	</div>
	<br style="clear:both" />

	<?php
	if (!$parent_id) { 
	
		$sql = "SELECT * FROM forums_posts WHERE forum_id=".$forum_id." AND parent_id=".$post_id." ORDER BY last_comment DESC";
		$result = mysql_query($sql, $db);
		if (mysql_num_rows($result)) { ?>
			<table class="manage">
			<tr>
				<th colspan="3">Replies</th>
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
</div>

<?php require('include/footer.inc.php'); ?>

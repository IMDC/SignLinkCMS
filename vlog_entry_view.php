<?php 
define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');

require(INCLUDE_PATH.'header.inc.php'); 
require(INCLUDE_PATH.'lib/vlogs.inc.php'); 

$entry_id = intval($_REQUEST['e']);
$vlog_id = intval($_REQUEST['v']);

$sql = "SELECT * FROM vlogs_entries WHERE vlog_id=".$vlog_id." AND entry_id=".$entry_id." ORDER BY date DESC";
$result = @mysql_query($sql, $db);
if (!$row = @mysql_fetch_assoc($result)) {
	echo "Entry not found.";
	require(INCLUDE_PATH.'footer.inc.php'); 
	exit;
}

?>

<div id="post-title">
	<div style="float:left;height:75px;"><?php echo get_title('vlog', $vlog_id, 'small'); ?> &nbsp;> &nbsp;</div>
	
	<div style="float:left; vertical-align:middle; height:75px;">
	<?php echo get_title('entry', $entry_id, 'small'); ?>
	</div>
	<div id="submenu" style="margin-top:41px;">
		<li><a href='vlog_entries.php?v=<?php echo $vlog_id; ?>'><img src='images/arrow_left.png' alt='Back to vlog entries' title='Back to vlog entries' class='buttonimage' /></a></li>	
	</div>	
	<div style="clear:both" /></div>
</div>

<div id="post">		
	<div style="padding:5px;">
		<div style="text-align:right">
		<?php
		if (get_vlog_owner($vlog_id) == $_SESSION['login']) {
			echo "<li style='display:inline;padding:8px;'><a href='vlog_entry_edit.php?v=$vlog_id&e=$entry_id'><img src='images/comment_edit.png' alt='Edit' title='Edit' /></a>";
		}		
		?>
		</div>
		<div id="post-msg-text">
			<?php  echo get_vlog_message($row['content'], $row['content_alt'], 'entries', $entry_id); ?>
		</div>
		<br style="clear:both" />
	</div>
	<br style="clear:both" />
</div>

<div style="padding-left:5px; padding-right:5px;">
	<a style="float:right;" href="vlog_comment.php?v=<?php echo $vlog_id.'&e='.$entry_id; ?>"><img src='images/comment_add.png' alt='Add comment' title='Add comment' /></a>
	<h3>Comments</h3>	
</div>
<div style="padding:5px;">	
	<?php
	/* comments */
	$sql = "SELECT * FROM vlogs_comments WHERE vlog_id=".$vlog_id." AND entry_id=".$entry_id." ORDER BY date DESC";
	$result = mysql_query($sql, $db);
	
	if (mysql_num_rows($result)) {
		while ($row = @mysql_fetch_assoc($result)) { 
			echo '<div style="background-color:#efefef; margin-bottom:10px; padding:10px;">';
			echo '<div style="float:left; text-align:center; width:100px;">';
			get_avatar($row['member_id']);
			echo '<div style="padding-bottom:10px;">'.get_login($row['member_id']).'</div>';
			echo '</div>';
			
			echo '<div style="margin-left:110px;">';				
			echo get_vlog_message($row['comment'], $row['comment_alt'], 'comments');
			echo '</div><br style="clear:both" />';		
			echo '</div>';
		}
	} else {
		echo "No comments.";
	}	
	?>
</div>

<?php require(INCLUDE_PATH.'footer.inc.php'); ?>

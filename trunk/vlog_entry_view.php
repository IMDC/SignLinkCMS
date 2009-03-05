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
<script type="text/javascript">
<!--
	function confirmDelete(entry_id) {
		confirm("Are you sure you want to delete this entry?");
	}	
	
//-->
</script>

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
	<div id="post-msg-text" style="padding-left:10px; padding-right:10px;">
		<div style="text-align:right;padding-right:10px;">
			<ul>
			<?php
			if ($_SESSION['valid_user'] && get_vlog_owner($vlog_id) == $_SESSION['member_id']) {
				echo "<li style='display:inline;padding:8px;'><a href='vlog_entry_edit.php?v=$vlog_id&e=$entry_id'><img src='images/comment_edit.png' alt='Edit' title='Edit' /></a></li>";
				echo "<li style='display:inline;padding:8px;'><a href='vlog_entry_delete.php?v=$vlog_id&e=$entry_id'><img src='images/delete.png' alt='Delete' title='Delete' onClick='javascript:confirmDelete()' /></a></li>";
			}						
			 ?>
			</ul>
		</div>
		<?php  echo get_vlog_message($row['content'], $row['content_alt'], 'entries', $entry_id); ?>
	</div>
	<br style="clear:both" />
	
	<?php	
	$sql = "SELECT * FROM vlogs_comments WHERE vlog_id=".$vlog_id." AND entry_id=".$entry_id." ORDER BY date DESC";
	$result = mysql_query($sql, $db);
	if (@mysql_num_rows($result)) { ?>
		<table class="manage">
		<tr>
			<th colspan="3"><a style="float:right" href="vlog_comment.php?v=<?php echo $vlog_id.'&e='.$entry_id; ?>"><img src='images/comment_add.png' alt='Add comment' title='Add comment' /></a> Comments</th>
		</tr>
		<?php 
			while ($row = mysql_fetch_assoc($result)) { 
				echo '<tr>';
				if (!empty($row['comment'])) {
					//the msg is plain text
					$link = substr($row['comment'],0,30).'...';
				} else {
					//the msg is a file
					$level = '';
					$depth = substr_count(INCLUDE_PATH, '/');
					for ($i=1; $i<$depth; $i++) {
						$level .= "../";
					}
					
					//get files
					$dir_files = @scandir($level.'uploads/comments/'.$id.'/');
		
					//pick out the "message" file and check its extension
					if (!empty($dir_files)) {
						foreach ($dir_files as $dir_file) {
							if (substr($dir_file,0, 7) == "message") {
								$msg_file = $dir_file;
								break;
							}
						}
						$ext = end(explode('.',$msg_file));
						if (in_array($ext, $filetypes_video)) {
							$link = '<img src="images/film.png" alt="movie content" style="border:0px;" />';
						} else if ($ext=="swf") {
							$link = '<img src="images/television.png" alt="signlink content" style="border:0px;" />';
						}
					}
				}
				echo '<td><a href="vlog_comment_view.php?v='.$vlog_id.'&e='.$entry_id.'&c='.$row['comment_id'].'">'.$link.'</a></td>';
				echo '<td style="text-align:center;">'.get_login($row['member_id']).'</td>'; 
				?>
				
				<td style="text-align:center">
					<?php echo date('M j Y, h:ia', strtotime($row['date'])); ?>
				</td>
			</tr>
		<?php
		}
	} else {
		echo "No comments.";
	}
	
	?>	
	
</div>


<?php require(INCLUDE_PATH.'footer.inc.php'); ?>

<?php 
define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');
require(INCLUDE_PATH.'lib/vlogs.inc.php'); 

$vlog_id = intval($_GET['v']);
$entry_id = intval($_GET['e']);

$comment_id = intval($_GET['c']);

//check if user owns this vlog
if ($_SESSION['member_id'] != get_vlog_owner($vlog_id)) {
	$_SESSION['errors'][] = "You don't have permission to delete entries from this vlog.";
	require(INCLUDE_PATH.'header.inc.php');
	require(INCLUDE_PATH.'footer.inc.php');
	exit;
}

$level = '';
$depth = substr_count(INCLUDE_PATH, '/');
for ($i=1; $i<$depth; $i++) {
	$level .= "../";
}

if ($comment_id) {
	//just deleting a comment
	$comment_path = $level.UPLOAD_DIR.'comments/'.$comment_id.'/';
	if (file_exists($comment_path)) {
		$dir_files = @scandir($comment_path);			
		foreach ($dir_files as $dir_file) {
			unlink($comment_path.$dir_file);
		}
		rmdir($comment_path);
	}		
	
	//delete entry comments
	$sql = "DELETE FROM vlogs_comments WHERE vlog_id=".$vlog_id." AND entry_id=".$entry_id." AND comment_id=".$comment_id;
	$result = mysql_query($sql, $db);		
		
	//adjust num comments
	$sql = "UPDATE vlogs_entries SET num_comments=num_comments-1 WHERE vlog_id=".$vlog_id." AND entry_id=".$entry_id;
	$result = mysql_query($sql, $db);			
		
	$_SESSION['feedback'][] = 'Vlog comment deleted.';
	//redirect
	header('Location: vlog_entry_view.php?v='.$vlog_id.'&e='.$entry_id);
	exit; 
	
} else if ($entry_id) {
	//delete vlog entry
	$sql = "DELETE FROM vlogs_entries WHERE vlog_id=".$vlog_id." AND entry_id=".$entry_id;
	$result = mysql_query($sql, $db);		
	
	$sql = "UPDATE vlogs SET num_entries=num_entries-1 WHERE vlog_id=".$vlog_id;
	$result = mysql_query($sql, $db);			
	
	//delete entry files		
	$entry_path = $level.UPLOAD_DIR.'entries/'.$entry_id.'/';
	if (file_exists($post_path)) {
		//delete files
		$dir_files = @scandir($entry_path);			
		foreach ($dir_files as $dir_file) {
			unlink($entry_path.$dir_file);
		}
		
		//delete directory
		rmdir($entry_path);
	}

	//get all the comment ids for this entry
	$sql = "SELECT comment_id FROM vlogs_comments WHERE vlog_id=".$vlog_id." AND entry_id=".$entry_id;
	$result = mysql_query($sql, $db);		
	
	while ($row = mysql_fetch_assoc($result)) {	
		//delete comment files		
		$comment_path = $level.UPLOAD_DIR.'comments/'.$row['comment_id'].'/';
		if (file_exists($comment_path)) {
			$dir_files = @scandir($comment_path);			
			foreach ($dir_files as $dir_file) {
				unlink($comment_path.$dir_file);
			}
			rmdir($comment_path);
		}	
	}
	
	//delete entry comments
	$sql = "DELETE FROM vlogs_comments WHERE vlog_id=".$vlog_id." AND entry_id=".$entry_id;
	$result = mysql_query($sql, $db);		
		
	$_SESSION['feedback'][] = 'Vlog entry deleted.';
	
} else {
	$_SESSION['errors'][] = 'Not found.';	
}

//redirect
header('Location: vlog_entries.php?v='.$vlog_id);
exit; 
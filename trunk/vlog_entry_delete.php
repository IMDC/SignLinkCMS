<?php 
define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');
require(INCLUDE_PATH.'lib/vlogs.inc.php'); 

$vlog_id = intval($_GET['v']);
$entry_id = intval($_GET['e']);

//check if user owns this vlog
if ($_SESSION['member_id'] != get_vlog_owner($vlog_id)) {
	$_SESSION['errors'][] = "You don't have permission to delete entries from this vlog.";
	require(INCLUDE_PATH.'header.inc.php');
	require(INCLUDE_PATH.'footer.inc.php');
	exit;
}

if ($entry_id) {
	//delete vlog entry
	$sql = "DELETE FROM vlogs_entries WHERE vlog_id=".$vlog_id." AND entry_id=".$entry_id;
	$result = mysql_query($sql, $db);		
	
	//delete entry files
	$level = '';
	$depth = substr_count(INCLUDE_PATH, '/');
	for ($i=1; $i<$depth; $i++) {
		$level .= "../";
	}		
	
	$entry_path = $level.UPLOAD_DIR.'entries/'.$entry_id.'/';
	if (file_exists($post_path)) {
		//delete files
		$dir_files = @scandir($entry_path);			
		foreach ($dir_files as $dir_file) {
			unset($dir_file);
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
				unset($dir_file);
			}
			rmdir($comment_path);
		}	
	}
	
	//delete entry comments
	$sql = "DELETE FROM vlogs_comments WHERE vlog_id=".$vlog_id." AND entry_id=".$entry_id;
	$result = mysql_query($sql, $db);		
		
	$_SESSION['feedback'][] = 'Vlog entry deleted.';
	
} else {
	$_SESSION['errors'][] = 'No such entry.';	
}

//redirect
header('Location: vlog_entries.php?v='.$vlog_id);
exit; 
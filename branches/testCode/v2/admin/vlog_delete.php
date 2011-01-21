<?php 
define('INCLUDE_PATH', '../include/');
require(INCLUDE_PATH.'vitals.inc.php');

admin_authenticate();

$vlog_id = intval($_GET['v']);

if ($vlog_id) {

	//delete all vlog entry files
	$sql = "SELECT * FROM vlogs_entries WHERE vlog_id=".$vlog_id;
	$result = mysql_query($sql, $db);
	if (@mysql_num_rows($result)) { 
		while ($row = mysql_fetch_assoc($result)) {
			$entry_path = '../'.UPLOAD_DIR.'entries/'.$row['entry_id'].'/';
			if (file_exists($entry_path)) {
				$dir_files = @scandir($entry_path);			
				foreach ($dir_files as $dir_file) {
					unlink($entry_path.$dir_file);
				}
				rmdir($entry_path);
			}	
		}
	}

	//delete all vlog entries
	$sql = "DELETE FROM vlogs_entries WHERE vlog_id=".$vlog_id;
	$result = mysql_query($sql, $db);

	//delete vlog files
	$vlog_path = '../'.UPLOAD_DIR.'vlogs/'.$vlog_id.'/';
	if (file_exists($vlog_path)) {
		$dir_files = @scandir($vlog_path);			
		foreach ($dir_files as $dir_file) {
			unlink($vlog_path.$dir_file);
		}
		rmdir($vlog_path);
	}		
	
	//delete vlog
	$sql = "DELETE FROM vlogs WHERE vlog_id=".$vlog_id;
	$result = mysql_query($sql, $db);
	$_SESSION['feedback'][] = 'Vlog deleted.';
}

//redirect
header('Location: vlog_manage.php');
exit; 
<?php 
define('INCLUDE_PATH', '../include/');
require(INCLUDE_PATH.'vitals.inc.php');


if (isset($_GET['c']) && !empty($_GET['c'])) {

	$page_id = intval($_GET['c']);

	//check if it has sub-pages. if so, can't delete
	$sql = "SELECT page_id FROM pages WHERE parent_id=".$page_id;
	$result = mysql_query($sql, $db);
	$numchildren = mysql_num_rows($result);	
	if ($numchildren) {
		$_SESSION['feedback'][] = 'Page cannot be deleted as it contains sub-pages.';
	} else {
		//delete page files & directory
		$level = '';
		$depth = substr_count(INCLUDE_PATH, '/');
		for ($i=1; $i<$depth; $i++) {
			$level .= "../";
		}		
		
		$page_path = $level.UPLOAD_DIR.'pages/'.$page_id.'/';
		if (file_exists($page_path)) {
			//delete files
			$dir_files = @scandir($page_path);			
			foreach ($dir_files as $dir_file) {
				unlink($dir_file);
			}
			
			//delete directory
			rmdir($page_path);
		}		
	
		//delete page
		$sql = "DELETE FROM pages WHERE page_id=".$page_id;
		$result = mysql_query($sql, $db);
		$_SESSION['feedback'][] = 'Page deleted successfully.';
	}
}

//redirect
header('Location: page_manage.php');
exit; 
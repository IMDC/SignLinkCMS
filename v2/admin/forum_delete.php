<?php 
define('INCLUDE_PATH', '../include/');
require(INCLUDE_PATH.'vitals.inc.php');

admin_authenticate();

$forum_id = intval($_GET['f']);

if (isset($_GET['t']) && !empty($_GET['t'])) {
	//delete title file
	$sql = "UPDATE forums SET title_file='' WHERE forum_id=".$forum_id;
	$result = mysql_query($sql, $db);
	$_SESSION['feedback'][] = 'Forum title deleted.';

} else {

	//delete all forum post files
	$sql = "SELECT * FROM forums_posts WHERE forum_id=".$forum_id;
	$result = mysql_query($sql, $db);
	if (@mysql_num_rows($result)) { 
		while ($row = mysql_fetch_assoc($result)) {
			$post_path = '../'.UPLOAD_DIR.'posts/'.$row['post_id'].'/';
			if (file_exists($post_path)) {
				$dir_files = @scandir($post_path);			
				foreach ($dir_files as $dir_file) {
					unlink($post_path.$dir_file);
				}
				rmdir($post_path);
			}	
		}
	}

	//delete threads
	$sql = "DELETE FROM forums_posts WHERE forum_id=".$forum_id;
	$result = mysql_query($sql, $db);
	
	//delete forum files
	$forum_path = '../'.UPLOAD_DIR.'forums/'.$forum_id.'/';
	if (file_exists($forum_path)) {
		$dir_files = @scandir($forum_path);			
		foreach ($dir_files as $dir_file) {
			unlink($forum_path.$dir_file);
		}
		rmdir($forum_path);
	}

	//delete forum
	$sql = "DELETE FROM forums WHERE forum_id=".$forum_id;
	$result = mysql_query($sql, $db);

	$_SESSION['feedback'][] = 'Forum deleted.';
}

//redirect
header('Location: forum_manage.php');
exit; 
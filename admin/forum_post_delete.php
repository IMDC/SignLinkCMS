<?php 
define('INCLUDE_PATH', '../include/');
require(INCLUDE_PATH.'vitals.inc.php');
admin_authenticate();

$forum_id = intval($_GET['f']);
$post_id = intval($_GET['p']);

if ($post_id) {
	//check if it has children
	$sql = "SELECT post_id FROM forums_posts WHERE parent_id=".$post_id." AND forum_id=".$forum_id;
	$result = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($result);
	if (!empty($row)) {
		$_SESSION['errors'][] = 'Cannot delete post as it has replies. You must delete the replies (child posts) first.';	
	} else {	
		//if it's a child, edit num_comments for parent.
		if ($row['parent_id']) {
			$sql = "UPDATE forums_posts SET num_comments=num_comments-1 WHERE post_id=".$post_id." AND forum_id=".$forum_id;
			$result = mysql_query($sql, $db);
		}
		
		//delete forum post
		$sql = "DELETE FROM forums_posts WHERE forum_id=".$forum_id." AND post_id=".$post_id;
		$result = mysql_query($sql, $db);

		//delete from forums_views & forums_read
		$sql = "DELETE FROM forums_views WHERE post_id=".$post_id;
		$result = mysql_query($sql, $db);		
		
		$sql = "DELETE FROM forums_read WHERE forum_id=".$forum_id." AND post_id=".$post_id;
		$result = mysql_query($sql, $db);			
		
		//delete post files
		$level = '';
		$depth = substr_count(INCLUDE_PATH, '/');
		for ($i=1; $i<$depth; $i++) {
			$level .= "../";
		}		
		
		$post_path = $level.UPLOAD_DIR.'posts/'.$post_id.'/';
		if (file_exists($post_path)) {
			//delete files
			$dir_files = @scandir($post_path);			
			foreach ($dir_files as $dir_file) {
				unset($dir_file);
			}
			
			//delete directory
			@rmdir($post_path);
		}		
		
		
		$_SESSION['feedback'][] = 'Forum post deleted.';
	}	
} else {
	$_SESSION['errors'][] = 'No such post.';	
}

//redirect
header('Location: forum_posts_manage.php?f='.$forum_id);
exit; 
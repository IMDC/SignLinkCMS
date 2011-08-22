<?php 
define('INCLUDE_PATH', '../include/');
require(INCLUDE_PATH.'vitals.inc.php');
admin_authenticate();

$forum_id = intval($_GET['f']);
$post_id = intval($_GET['p']);
$delete = intval($_GET['del']);

if ($post_id && $delete)
{
	//check if it has children
	$sql = "SELECT post_id FROM forums_posts WHERE parent_id=".$post_id." AND forum_id=".$forum_id;
         
        // if it's a parent, delete children
	if ($result = mysqli_query($db, $sql)) 
        {
                $sql = "DELETE FROM forums_posts WHERE parent_id=".$post_id." AND forum_id=".$forum_id;
                mysqli_query($db, $sql);
                
                while($row = mysqli_fetch_assoc($result)){
                    delete_folder('posts', $row['post_id']);
                }
	}
        //if it's a child, edit num_comments for parent.
        else
        {
                $sql_nested = "SELECT parent_id FROM forums_posts WHERE post_id=" .$post_id;
                $result_nested = mysqli_query($db, $sql_nested);
                $row_nested = mysqli_fetch_assoc($result_nested);
                $sql = "UPDATE forums_posts SET num_comments=num_comments-1 WHERE post_id =".intval($row_nested['parent_id'])." AND forum_id=".$forum_id;
                $result = mysqli_query($db, $sql);
        }

        //delete forum post
        $sql = "DELETE FROM forums_posts WHERE forum_id=".$forum_id." AND post_id=".$post_id;
        $result = mysqli_query($db, $sql);

        //delete from forums_views & forums_read
        $sql = "DELETE FROM forums_views WHERE post_id=".$post_id;
        $result = mysqli_query($db, $sql);		

        $sql = "DELETE FROM forums_read WHERE forum_id=".$forum_id." AND post_id=".$post_id;
        $result = mysqli_query($db, $sql);			

        //delete post files
        delete_folder('posts', $post_id);

        $_SESSION['feedback'][] = 'Forum post deleted.';
	
}
elseif ($post_id) 
{
        $sql = sprintf("UPDATE forums_posts set subject='%s', msg='%s', locked=1 WHERE forum_id='%s' AND post_id='%s'",
          mysqli_real_escape_string($db, '<span class="reply-deleted"><img src="images/post-deleted.png" alt="" />Post removed</span>'),
          mysqli_real_escape_string($db, '<span class="reply-deleted"><img src="images/post-deleted.png" alt="" />This post has been removed by the admin</span>'),
          mysqli_real_escape_string($db, $forum_id),
          mysqli_real_escape_string($db, $post_id));
        
        $result = mysqli_query($db, $sql);
        delete_folder('posts', $post_id);
        
        $_SESSION['feedback'][] = 'Forum post removed.';
        
} else {
	$_SESSION['errors'][] = 'No such post.';	
}

//redirect
header('Location: forum_posts_manage.php?f='.$forum_id);
exit; 

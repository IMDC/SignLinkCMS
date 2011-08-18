<?php 
define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');

$post_id = intval($_REQUEST['p']);
$forum_id = intval($_REQUEST['f']);
$parent = intval($_REQUEST['parent']);
$member = intval($_REQUEST['m']);

$msg = get_message($post_id);

if (isset($_POST['cancel'])) {
	header('Location: forum_post_view.php?f='.$forum_id.'&p='.$post_id.'&parent='.$parent);
	exit;
} 
else if ($_GET['processed']) {

	//check if there are any errors
	if(empty($_POST)) {
		$_SESSION['feedback'][] = "General error. Your URL is incorrect or you are trying to post a file that is too large for this installation.";
		require(INCLUDE_PATH.'header.inc.php');
		require(INCLUDE_PATH.'footer.inc.php');
		exit;
   }

	if (!isset($_SESSION['errors'])) {
  
      // Formulate Query
      // This is the best way to perform a SQL query
      // For more examples, see mysqli_real_escape_string()
//      $sql = sprintf("DELETE FROM forums_posts WHERE forum_id='%s' AND post_id='%s' AND parent_id='%s' AND member_id='%s'",
//          
//          mysqli_real_escape_string($db, $forum_id),
//          mysqli_real_escape_string($db, $post_id),
//          mysqli_real_escape_string($db, $parent),
//          mysqli_real_escape_string($db, $member));
//      //echo $sql;
         $sql = sprintf("UPDATE forums_posts set subject='%s', msg='%s', locked=1 WHERE forum_id='%s' AND post_id='%s' AND parent_id='%s' AND member_id='%s'",
          mysqli_real_escape_string($db, "<span class='reply-deleted'><img src='images/post-deleted.png' alt='' />This post has been removed by the user</span>"),
          mysqli_real_escape_string($db, "<span class='reply-deleted'><img src='images/post-deleted.png' alt='' />This post has been removed by the user</span>"),
          mysqli_real_escape_string($db, $forum_id),
          mysqli_real_escape_string($db, $post_id),
          mysqli_real_escape_string($db, $parent),
          mysqli_real_escape_string($db, $member));
      //echo $sql;
   }

   // Perform Query
   $result = mysqli_query($db, $sql);

   // Check result
   if (!$result) {
          //$message  = 'Invalid query: ' . mysqli_error() . "\n";
          //$message .= 'Whole query: ' . $query;
          $_SESSION['errors'][] = 'Database error.';
          //die($message);
   }
   else {
      delete_folder('posts', $post_id);
      $_SESSION['feedback'][] = 'Post deleted.';
      header('Location: forum_post_view.php?f='.$forum_id.'&p='.$parent);
   }

}


//check if user has logged in and can post
if (!$_SESSION['valid_user']) {
	$_SESSION['errors'][] = 'You must be logged in to post a message. Please <a href="login.php?f='.intval($_REQUEST['f']).'">login</a>.';
	require(INCLUDE_PATH.'header.inc.php');
	require(INCLUDE_PATH.'footer.inc.php');
	exit;
}

//check if user owns this post
if ($_SESSION['login'] != $msg[0]) {
	$_SESSION['errors'][] = "You don't have permission to delete this post.";
	require(INCLUDE_PATH.'header.inc.php');
	require(INCLUDE_PATH.'footer.inc.php');
	exit;
}

require(INCLUDE_PATH.'header.inc.php');

echo '<h3>Really delete Forum Post?</h3>';
echo '<div class="forum-post-playback">';

$the_post = get_message($post_id);
echo $the_post[2];
echo '</div>';

?>

		<form action="<?php echo htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES); ?>?processed=1" method="post" name="form_sub" id="form_sub" enctype="multipart/form-data" style="clear:both; padding-top:2px;">
			<input type="hidden" name="f" value="<?php echo $forum_id; ?>" />
			<input type="hidden" name="p" value="<?php echo $post_id; ?>" />
			<input type="hidden" name="parent" value="<?php echo $parent; ?>" />
         <input type="hidden" name="m" value="<?php echo $member; ?>" />

         <div class="submitrow">
            <input type="submit" class="submitBtn" name="submit" value="Delete" />
            <input type="submit" class="cancelBtn" name="cancel" value="Cancel" /> 
         </div>
      </form>

<?php require(INCLUDE_PATH.'footer.inc.php'); ?>

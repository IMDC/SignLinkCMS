<?php 
define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');

if (isset($_POST['parent'])) {
	$parent_id = intval($_POST['parent']);
} else {
	$parent_id = 0;
}

if (isset($_POST['cancel'])) {
	if ($parent_id) {
		header('Location: forum_post_view.php?f='.intval($_POST['f']).'&parent='.$parent_id);
	} else {
		header('Location: forum_posts.php?f='.intval($_POST['f']));
	}
	exit;

} else if (isset($_POST['submit'])) {

	//error check
	if (empty($_POST['subject']) && empty($_FILES['subject_file']['name'])) {
		$_SESSION['errors'][] = 'Please enter a subject.';
	} 
	if (empty($_POST['msg']) && empty($_POST['msg_file'])) {
		$_SESSION['errors'][] = 'Please enter a message.';
	}
	
	if (!isset($_SESSION['errors'])) {
		$subject = $addslashes($_POST['subject']);

		$msg = $addslashes($_POST['msg']);
		$msg_file = $addslashes($_POST['msg_file']);

		$forum_id = intval($_POST['f']);
	
		$now = date('Y-m-d G:i:s');
		$sql = "INSERT INTO forums_posts VALUES (NULL, '$parent_id', '$_SESSION[member_id]', '$forum_id', '$_SESSION[login]', '$now', 0, '$subject', '', '$msg', '$msg_file',NOW(),0, 0)";

		if (!$result = mysql_query($sql, $db)) {
			$_SESSION['errors'][] = 'Database error.';
		} else {
			//edit 'last comment' for parent
			if ($parent_id) {
				$sql = "UPDATE forums_posts SET last_comment='$now' WHERE post_id=$parent_id";
				$result = mysql_query($sql, $db);
			}

			//save files
			$post_id = mysql_insert_id();

			if (is_uploaded_file($_FILES['subject_file']['tmp_name'])) {
				save_title('post', $post_id);
			}

			//save_SLfile();

			//redirect
			if ($parent_id) {
				$_SESSION['feedback'][] = 'Replied successfully.';
				header('Location: forum_post_view.php?f='.intval($_POST['f']).'&parent='.$parent_id);
				exit;
			} else {
				$_SESSION['feedback'][] = 'Forum topic created successfully.';
				header('Location: forum_posts.php?f='.intval($_POST['f']));
				exit;
			}
		}
	}
} 

if (!$_SESSION['valid_user']) {
	$_SESSION['errors'][] = 'You must be logged in to post a message. Please <a href="login.php">login</a>.';
	require(INCLUDE_PATH.'header.inc.php');
	require(INCLUDE_PATH.'footer.inc.php');
	exit;
}

require(INCLUDE_PATH.'header.inc.php');

if(isset($_REQUEST['parent'])) {
	echo '<h2 style="display:inline;">Reply to '.get_title('post', $_REQUEST['parent']).'</h2>';
} else {
	echo '<h2>Post New Topic</h2>';
}
?>

<form action ="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form" enctype="multipart/form-data">
	<input type="hidden" name="f" value="<?php echo intval($_REQUEST['f']); ?>" />

	<?php if (isset($_REQUEST['parent'])) { ?>
		<input type="hidden" name="parent" value="<?php echo intval($_REQUEST['parent']); ?>" />
		<input type="hidden" name="subject" value="reply" />
	<?php //subject isn't anything when it's a reply (displays parent subject) but in the future, can add this option
	} ?>

	<?php require(INCLUDE_PATH.'forum_post.inc.php'); ?>
</form>

<?php require(INCLUDE_PATH.'footer.inc.php'); ?>
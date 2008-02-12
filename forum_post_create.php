<?php 
define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');

if (isset($_REQUEST['p'])) {
	$parent_id = intval($_REQUEST['p']);
} else {
	$parent_id = 0;
}

$forum_id = intval($_REQUEST['f']);

if (isset($_POST['cancel'])) {
	if ($parent_id) {
		header('Location: forum_post_view.php?f='.$forum_id.'&p='.$parent_id.'&parent=1');
	} else {
		header('Location: forum_posts.php?f='.$forum_id);
	}
	exit;

} else if ($_POST['f'] || $_GET['processed']) {

	//check if there are any upload errors
	if(empty($_POST)) {
		$_SESSION['errors'][] = 'File too large.';
	}
	check_uploads();

	if (!isset($_SESSION['errors'])) {	
	
		//now error check subject
		if (empty($_POST['subject']) || (empty($_FILES['isub-file']['tmp_name']) && empty($_FILES['vsub-file']['tmp_name']) && empty($_POST['sub-text'])) ) {
			$_SESSION['errors'][] = 'Subject empty.';
			
		} else if ($_POST['subject'] == "image") {
			$ext = explode('.', $_FILES['isub-file']['name']);
			$ext = $ext[1];
			if (!in_array($ext, $filetypes_image)) {
				$_SESSION['errors'][] = 'You have chosen to use an image file for your subject - invalid file format.'. $ext;
			}
			
		} else if ($_POST['subject'] == "video") {
			$ext = explode('.', $_FILES['vsub-file']['name']);
			$ext = $ext[1];
			if (!in_array($ext, $filetypes_video)) {
				$_SESSION['errors'][] = 'You have chosen a video file for your subject - invalid file format.';
			}
			
		} else if ( ($_POST['subject'] == "text") && empty($_POST['sub-text']) ) {
			$_SESSION['errors'][] = 'You have chosen text for your subject - message cannot be empty.';
		}	
		
		//error check message 
		if (empty($_POST['message']) || ( (empty($_FILES['sl1msg-file']['tmp_name']) && empty($_FILES['sl2msg-file']['tmp_name'])) && empty($_FILES['vmsg-file']['tmp_name']) && empty($_POST['msg-text'])) ) {
			$_SESSION['errors'][] = 'Message empty.';
		} else if ($_POST['message'] == "signlink" && ( empty($_FILES['sl1msg-file']['tmp_name']) || empty($_FILES['sl2msg-file']['tmp_name']) ) )  {
			$_SESSION['errors'][] = 'You have chosen to post a Signlink message - this requires that you submit two files: a flash file and a .flv file.';
			
		} else if ($_POST['message'] == "video") {
			$ext = end(explode('.', $_FILES['vmsg-file']['name']));
			if (!in_array($ext, $filetypes_video)) {
				$_SESSION['errors'][] = 'You have chosen to post a video message - invalid file format.';
			}
			
		} else if ( $_POST['message'] == "text" && empty($_POST['msg-text']) ) {
			$_SESSION['errors'][] = 'You have chosen to post a text message - message cannot be empty.';
		}		
	}	
	
	if (!isset($_SESSION['errors'])) {

		//prepare to insert into db
		switch ($_POST['subject']) {
			case 'image':
				$subject = '';
				$subject_alt = $addslashes(htmlspecialchars($_POST['isub-alt']));
				break;
			case 'video':
				$subject = '';
				$subject_alt = $addslashes(htmlspecialchars($_POST['vsub-alt']));
				break;
			case 'text':
				$subject = $addslashes(htmlspecialchars($_POST['sub-text']));
				$subject_alt = '';
				break;
		}

		switch ($_POST['message']) {
			case 'signlink':
				$message = '';
				$message_alt = '';
				break;
			case 'video':
				$message = '';
				$message_alt = $addslashes(htmlspecialchars($_POST['vmsg-alt']));
				break;
			case 'text':
				$message = $addslashes(htmlspecialchars($_POST['msg-text']));
				$message_alt = '';
				break;
		}

		$now = date('Y-m-d G:i:s');

		//insert into db
		$sql = "INSERT INTO forums_posts VALUES (NULL, '$parent_id', '$_SESSION[member_id]', '$forum_id', '$_SESSION[login]', '$now', 0, '$subject', '$subject_alt', '$message', '$message_alt', NOW(),0, 0)";

		if (!$result = mysql_query($sql, $db)) {
			$_SESSION['errors'][] = 'Database error.';
		} else {
			$post_id = mysql_insert_id();

			//edit 'last comment' field for parent to be now
			if ($parent_id) {
				$sql = "UPDATE forums_posts SET last_comment='$now', num_comments=num_comments+1 WHERE post_id=$parent_id";
				$result = mysql_query($sql, $db);
				$num_topics = '';
			} else {
				$num_topics = ", num_topics=num_topics+1";
			}
			
			//update info for forum (last post, num posts, num topics)
			$sql = "UPDATE forums SET last_post='$now', num_posts=num_posts+1 $num_topics WHERE forum_id=$forum_id";
			$result = mysql_query($sql, $db);

			//save files			
			switch ($_POST['subject']) {
				case 'image':
					if (is_uploaded_file($_FILES['isub-file']['tmp_name'])) {
						save_image('post', 'title', 'isub-file', $post_id);
					}
					break;
				case 'video':
					if (is_uploaded_file($_FILES['vsub-file']['tmp_name'])) {
						save_video('post', 'title', 'vsub-file', $post_id);
					}
					break;
			}

			switch ($_POST['message']) {
				case 'signlink':
					if (is_uploaded_file($_FILES['sl1msg-file']['tmp_name']) && is_uploaded_file($_FILES['sl2msg-file']['tmp_name'])) {
						save_signlink('post', 'message', 'sl1msg-file', $post_id);
						save_signlink('post', 'message2', 'sl2msg-file', $post_id);
					}
					break;
				case 'video':
					if (is_uploaded_file($_FILES['vmsg-file']['tmp_name'])) {
						save_video('post', 'message', 'vmsg-file', $post_id);
					}
					break;
			}

			//redirect
			if ($parent_id) {
				$_SESSION['feedback'][] = 'Replied successfully.';
				header('Location: forum_post_view.php?f='.intval($_POST['f']).'&p='.$parent_id.'&parent=1');
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
	$_SESSION['errors'][] = 'You must be logged in to post a message. Please <a href="login.php?f='.intval($_REQUEST['f']).'">login</a>.';
	require(INCLUDE_PATH.'header.inc.php');
	require(INCLUDE_PATH.'footer.inc.php');
	exit;
}

require(INCLUDE_PATH.'header.inc.php');

if ($parent_id) {
	echo '<h2>Reply to '.get_title('post', $parent_id).'</h2>';
} else {
	echo '<h2>Post New Topic</h2>';
}
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>?processed=1" method="post" name="form" enctype="multipart/form-data" style="clear:both; padding-top:2px;">
	<input type="hidden" name="f" value="<?php echo $forum_id; ?>" />
	<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAX_UPLOAD_SIZE; ?>" />

	<?php if ($parent_id) { ?>
		<input type="hidden" name="p" value="<?php echo $parent_id; ?>" />
		<input type="hidden" name="subject" value="text" />
		<input type="hidden" name="sub-text" value="Re: " />
	<?php 
	} ?>

	<?php require(INCLUDE_PATH.'forum_post.inc.php'); ?>
</form>

<?php require(INCLUDE_PATH.'footer.inc.php'); ?>
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

} else if ($_POST['f']) {

	//error check subject
	if (empty($_POST['subject']) || (empty($_FILES['isub-file']['tmp_name']) && empty($_FILES['vsub-file']['tmp_name']) && empty($_POST['sub-text'])) ) {
		$_SESSION['errors'][] = 'Subject empty.';
		
	} else if ($_POST['subject'] == "image") {
		$ext = explode('.', $_FILES['isub-file']['name']);
		$ext = $ext[1];
		if (!in_array($ext, $filetypes_image)) {
			$_SESSION['errors'][] = 'You have chosen to use an image file for your subject - invalid file format.'. $ext;
		}
		
	} else if ($_POST['message'] == "video") {
		$ext = explode('.', $_FILES['vsub-file']['name']);
		$ext = $ext[1];
		if (!in_array($ext, $filetypes_video)) {
			$_SESSION['errors'][] = 'You have chosen a video file for your subject - invalid file format.';
		}
		
	} else if ( ($_POST['message'] == "text") && empty($_POST['sub-text']) ) {
		$_SESSION['errors'][] = 'You have chosen text for your subject - message cannot be empty.';
	}	
	
	//error check message 
	if (empty($_POST['message']) || ( (empty($_FILES['sl1msg-file']['tmp_name']) && empty($_FILES['sl2msg-file']['tmp_name'])) && empty($_FILES['vmsg-file']['tmp_name']) && empty($_POST['msg-text'])) ) {
		$_SESSION['errors'][] = 'Message empty.';
	} else if ($_POST['message'] == "signlink" && ( empty($_FILES['sl1msg-file']['tmp_name']) || empty($_FILES['sl2msg-file']['tmp_name']) ) )  {
		$_SESSION['errors'][] = 'You have chosen to post a Signlink message - this requires that you submit two files: a flash file and a .flv file.';
		
	} else if ($_POST['message'] == "video") {
		$ext = explode('.', $_FILES['vmsg-file']['tmp_name']);
		$ext = $ext[1];
		if (!in_array($ext, $filetypes_video)) {
			$_SESSION['errors'][] = 'You have chosen to post a video message - invalid file format.';
		}
		
	} else if ( $_POST['message'] == "text" && empty($_POST['msg-text']) ) {
		$_SESSION['errors'][] = 'You have chosen to post a text message - message cannot be empty.';
	}		
	
	
	if (!isset($_SESSION['errors'])) {

		switch ($_POST['subject']) {
			case 'image':
				$subject = '';
				$subject_file = $addslashes($_FILES['isub-file']['name']);
				$subject_alt = $addslashes(htmlspecialchars($_POST['isub-alt']));
				break;
			case 'video':
				$subject = '';
				$subject_file = $addslashes($_FILES['vsub-file']['name']);
				$subject_alt = $addslashes(htmlspecialchars($_POST['vsub-alt']));

				$save_func = 'save_video';
				break;
			case 'text':
				$subject = $addslashes(htmlspecialchars($_POST['sub-text']));
				$subject_file = '';
				$subject_alt = '';
				break;
		}

		switch ($_POST['message']) {
			case 'signlink':
				$message = $addslashes($_FILES['sl1msg-file']['name']);
				$mesage_file = $addslashes($_FILES['sl2msg-file']['name']);
				$message_alt = '';
				break;
			case 'video':
				$message = '';
				$mesage_file = $addslashes($_FILES['vmsg-file']['name']);
				$message_alt = $addslashes(htmlspecialchars($_POST['vmsg-alt']));
				break;
			case 'text':
				$message = $addslashes(htmlspecialchars($_POST['msg-text']));
				$mesage_file = '';
				$message_alt = '';
				break;
		}

		$forum_id = intval($_POST['f']);
		$now = date('Y-m-d G:i:s');

		$sql = "INSERT INTO forums_posts VALUES (NULL, '$parent_id', '$_SESSION[member_id]', '$forum_id', '$_SESSION[login]', '$now', 0, '$subject', '$subject_file', '$subject_alt', '$message', '$message_file', '$message_alt', NOW(),0, 0)";

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

			switch ($_POST['subject']) {
				case 'image':
					$subject_alt = $addslashes(htmlspecialchars($_POST['isub-alt']));

					if (is_uploaded_file($_FILES['isub-file']['tmp_name'])) {
						$ext = end(explode('.',$_FILES['isub-file']['name']));
						save_title_image('post', $_FILES['isub-file']['tmp_name'], $ext, $post_id);
					}

					break;
				case 'video':
					$subject = '';
					$subject_file = $addslashes($_FILES['vsub-file']['name']);
					$subject_alt = $addslashes(htmlspecialchars($_POST['vsub-alt']));

					$save_func = 'save_video';
					break;
				case 'text':
					$subject = $addslashes(htmlspecialchars($_POST['sub-text']));
					$subject_file = '';
					$subject_alt = '';
					break;
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

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form" enctype="multipart/form-data">
	<input type="hidden" name="f" value="<?php echo intval($_REQUEST['f']); ?>" />

	<?php if (isset($_REQUEST['parent'])) { ?>
		<input type="hidden" name="parent" value="<?php echo intval($_REQUEST['parent']); ?>" />
		<input type="hidden" name="subject" value="Re:" />
	<?php 
	} ?>

	<?php require(INCLUDE_PATH.'forum_post.inc.php'); ?>
</form>

<?php require(INCLUDE_PATH.'footer.inc.php'); ?>
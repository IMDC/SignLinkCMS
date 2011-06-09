<?php 
define('INCLUDE_PATH', '../include/');
require(INCLUDE_PATH.'vitals.inc.php');

admin_authenticate();

if (isset($_POST['cancel'])) {
	header('Location: '.INCLUDE_PATH.'../admin/forum_manage.php');
	exit;
} else if (isset($_POST['submit']) || $_GET['processed']) {

	//check if there are any upload errors
	if(empty($_POST)) {
		$_SESSION['errors'][] = 'File too large.';
	}
	check_uploads();

	if (!isset($_SESSION['errors'])) {	
		//error check subject
		if (empty($_POST['subject']) || (empty($_FILES['isub-file']['tmp_name']) && empty($_FILES['vsub-file']['tmp_name']) && empty($_POST['sub-text'])) ) {
			$_SESSION['errors'][] = 'Subject empty.';
			
		} else if ($_POST['subject'] == "image") {
			$ext = explode('.', $_FILES['isub-file']['name']);
			$ext = strtolower($ext[1]);
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
	}
	if (!isset($_SESSION['errors'])) {
		//prepare to insert into db
		switch ($_POST['subject']) {
			case 'image':
				$subject = '';
				//$subject_alt = $addslashes(htmlspecialchars($_POST['isub-alt']));
				$subject_alt = mysqli_real_escape_string($db,htmlspecialchars($_POST['isub-alt']));
				break;
			case 'video':
				$subject = '';
				//$subject_alt = $addslashes(htmlspecialchars($_POST['vsub-alt']));
				$subject_alt = mysqli_real_escape_string($db, htmlspecialchars($_POST['vsub-alt']));
				break;
			case 'text':
				//$subject = $addslashes(htmlspecialchars($_POST['sub-text']));
				$subject = mysqli_real_escape_string($db, htmlspecialchars($_POST['sub-text']));
				$subject_alt = '';
				break;
		}

		$forum_id = intval($_POST['f']);
		$now = date('Y-m-d G:i:s');

		//insert into db
		$sql = "INSERT INTO forums ('forum_id', 'subject', 'subject_alt') VALUES (NULL, '$subject', '$subject_alt')";

		if (!$result = mysqli_query($db, $sql)) {
			$_SESSION['errors'][] = 'Database error.';
		} else {
			$fid = mysqli_insert_id($db);

			//save files			
			switch ($_POST['subject']) {
				case 'image':
					if (is_uploaded_file($_FILES['isub-file']['tmp_name'])) {
						save_image('forum', 'title', 'isub-file', $fid);
					}
					break;
				case 'video':
					if (is_uploaded_file($_FILES['vsub-file']['tmp_name'])) {
						save_video('forum', 'title', 'vsub-file', $fid);
					}
					break;
			}
		
			//redirect
			$_SESSION['feedback'][] = 'Forum created successfully.';
			header('Location: forum_manage.php');
			exit;
		}
	}
} 

require(INCLUDE_PATH.'admin_header.inc.php'); ?>

<h2>New Forum</h2>
<?php require(INCLUDE_PATH.'forum.inc.php'); ?>

<?php require('../include/footer.inc.php'); ?>

<?php 
define('INCLUDE_PATH', '../include/');
require(INCLUDE_PATH.'vitals.inc.php');
admin_authenticate();

$vlog_id = intval($_REQUEST['v']);

if (isset($_POST['cancel'])) {
	header('Location: '.INCLUDE_PATH.'../admin/vlog_manage.php');
	exit;
} else if (isset($_POST['submit']) || $_GET['processed'] && (isset($_POST['subject']) || $_POST['member'] != 0)) {
	
	if($_POST['member'] != 0) //if the user changed the dropdown then update the database
	{
		$sql = "UPDATE vlogs set member_id =" .$_POST['member']. " Where vlog_id = " . $vlog_id;	
	    $result =  mysqli_query($db, $sql);
	}
	
	if(isset($_POST['subject']))
	{
	   //this is here simply to make sure they dont have to update the title area
		if (!isset($_SESSION['errors'])) {	//error check the subject for the new title
	
		/*if (empty($_POST['member'])) {
			$_SESSION['errors'][] = 'Member not selected.';
		}
	    */
		//now error check subject
			if (empty($_POST['subject']) || (empty($_FILES['isub-file']['tmp_name']) && empty($_FILES['vsub-file']['tmp_name']) && empty($_POST['sub-text'])) ) {
				$_SESSION['errors'][] = 'Title empty.';
				
			} else if ($_POST['subject'] == "image") {
				$ext = explode('.', $_FILES['isub-file']['name']);
				$ext = $ext[1];
				if (!in_array($ext, $filetypes_image)) {
					$_SESSION['errors'][] = 'You have chosen to use an image file for your title - invalid file format.'. $ext;
				}
			
			} else if ($_POST['subject'] == "video") {
				$ext = explode('.', $_FILES['vsub-file']['name']);
				$ext = $ext[1];
				if (!in_array($ext, $filetypes_video)) {
					$_SESSION['errors'][] = 'You have chosen a video file for your title - invalid file format.';
				}
			
			} else if ( ($_POST['subject'] == "text") && empty($_POST['sub-text']) ) {
				$_SESSION['errors'][] = 'You have chosen text for your title - text cannot be empty.';
			}	
		}

        if (!isset($_SESSION['errors'])) {
		//prepare to insert into db
		//$member = intval($_POST['member']);
		
		    if(is_dir('../uploads/vlogs/'.$vlog_id))
			{
			   
			   $dir='../uploads/vlogs/'.$vlog_id.'/';
			   rrmdir($dir); //remove directory and other
			}
			
			switch ($_POST['subject']) {
			case 'image':
				$subject = '';
				//$subject_alt = $addslashes(htmlspecialchars($_POST['isub-alt']));
				$subject_alt = mysqli_real_escape_string($db,htmlspecialchars($_POST['isub-alt']));
				break;
			case 'video':
				$subject = '';
				//$subject_alt = $addslashes(htmlspecialchars($_POST['vsub-alt']));
				$subject_alt = mysqli_real_escape_string($db,htmlspecialchars($_POST['vsub-alt']));
				break;
			case 'text':
				//$subject = $addslashes(htmlspecialchars($_POST['sub-text']));
				$subject = mysqli_real_escape_string($db,htmlspecialchars($_POST['sub-text']));
				$subject_alt = '';
				break;
		     }
                
			//insert into db
			if($subject=="" && $subject_alt == "")
			{
				$sql = "UPDATE vlogs SET title=". '""' . ", title_alt=". '""' . " WHERE vlog_id=" . $vlog_id;
			}
			else if ($subject!="" && $subject_alt == ""){
			   $sql = "UPDATE vlogs SET title='" . $subject . "', title_alt=". '""' . " WHERE vlog_id=" . $vlog_id;	
			}
			else if ($subject=="" && $subject_alt != ""){
			   $sql = "UPDATE vlogs SET title=". '""' . ", title_alt='". $subject_alt ."' WHERE vlog_id=" . $vlog_id;	
			}
			else if ($subject !="" && $subject_alt != ""){
			   $sql = "UPDATE vlogs SET title='". $subject."', title_alt='". $subject_alt ."' WHERE vlog_id=" . $vlog_id;	
			}
			
			
			if (!$result = mysqli_query($db, $sql)) {
				$_SESSION['errors'][] = 'Database error.';
			} else {
				//save files			
				switch ($_POST['subject']) {
					case 'image':
						if (is_uploaded_file($_FILES['isub-file']['tmp_name'])) {
							save_image('vlog', 'title', 'isub-file', $vlog_id);
						}
						break;
					case 'video':
						if (is_uploaded_file($_FILES['vsub-file']['tmp_name'])) {
							save_video('vlog', 'title', 'vsub-file', $vlog_id);
						}
						break;
				 }
			
	         }
			
			$_SESSION['feedback'][] = 'Vlog edited successfully.';
				header('Location: vlog_manage.php');
			exit;
	     }
	   }



	//redirect
	/*$_SESSION['feedback'][] = 'Vlog edited successfully.';
	header('Location: vlog_manage.php');
	exit;*/
}


require(INCLUDE_PATH.'admin_header.inc.php'); 


?>

<H2>Edit Vlog</h2>

<?php

$sql = "SELECT * FROM vlogs WHERE vlog_id=". $vlog_id;
$result = mysqli_query($db, $sql);

while ($row = mysqli_fetch_assoc($result)) 
    {
         $title = get_title('vlog', $row['vlog_id'],'small');
         $title = adminMediaPathFix($title);

         echo $title; 
	}
?>
	
<script type="text/javascript" src="../jscripts/forum_post.js"></script>
<form action ="<?php echo htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES) . '?processed=1&v=' . $vlog_id; ?>" method="post" name="form" enctype="multipart/form-data">
		
	<div class="file-info">
		<span class="bold">Owner</span><br />
		<p>If you want to change the owner of this video, please choose a different member. If not, do not update the field below</p>
		<label>Member<br /> <?php print_members_dropdown();  ?><label>
	</div>													
	
	<div class="file-info">
		<span class="bold">Title</span><br />
			<p>If you want to change the title of the video, please do so below and fill out all of the required fields in the process.</p>

			<div class="choice">
				<label><input type="radio" name="subject" value="image" <?php if($_POST['subject'] == "image") { echo 'checked="checked"'; }?> />Image</label>

				<div class="choice-info" id="subject-image">
					<dl class="col-list">
						<dt>File</dt> <dd><input type="file" id="isub-file" name="isub-file" /></dd>
						<dt>Alt Text<dt> <dd><input type="text" id="isub-alt" name="isub-alt" size="80" value="<?php echo $_POST['isub-alt']; ?>" /></dd>
					</dl>
				</div><br />

				<label><input type="radio" name="subject" value="video" <?php if($_POST['subject'] == "video") { echo 'checked="checked"'; }?> /> Video</label>
				<div class="choice-info" id="subject-video">
					<dl class="col-list">
						<dt>File</dt> <dd><input type="file" id="vsub-file" name="vsub-file" /></dd>
						<dt>Alt Text<dt> <dd><input type="text" id="vsub-alt" name="vsub-alt" size="80" value="<?php echo $_POST['vsub-alt']; ?>" /></dd>
					</dl>
				</div><br />

				<label><input type="radio" name="subject" value="text" <?php if($_POST['subject'] == "text") { echo 'checked="checked"'; }?> /> Text</label>
				<div class="choice-info" id="subject-text">
					<input type="text" id="sub-text" name="sub-text" size="85" value="<?php echo $_POST['sub-text']; ?>" />
				</div>
			</div>
	</div>	  
	  
	<div class="row" style="text-align:right;margin-top:30px;">
		<input type="submit" name="submit" value="Submit" /> | <input type="submit" name="cancel" value="Cancel" /> 
	</div>
</form>




<?php require(INCLUDE_PATH.'footer.inc.php'); ?>

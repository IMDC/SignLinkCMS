<?php 
define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');

if (!$_SESSION['valid_user']) {
	$_SESSION['notices'][] = 'You must be logged in to manage your preferences. Please <a href="login.php">login</a>.';
	require(INCLUDE_PATH.'header.inc.php');
	require(INCLUDE_PATH.'footer.inc.php');
	exit;
} else if ($_POST['submit'] || $_GET['processed']) {
	//check if there are any upload errors
	if(empty($_POST)) {
		$_SESSION['errors'][] = "General error. Your URL is incorrect or you are trying to post a file that is too large for this installation.";
		require(INCLUDE_PATH.'header.inc.php');
		require(INCLUDE_PATH.'footer.inc.php');
		exit;
	}
	check_uploads();

  /***** PASSWORD STUFF ******/
  
	if ($_POST['oldpass'] != '') { 
		$_SESSION['errors'][] = 'Please enter a password.';
	}
  else {
		if ($_POST['newpass1'] != $_POST['newpass2']){
			$_SESSION['errors'][] = 'Passwords do not match.';
		}
		if (!preg_match('/^\S{8,}$/u', $_POST['newpass1'])) { 
			$_SESSION['errors'][] = 'Passwords must be greater than 8 characters.';
		} 
		if ((preg_match('/[a-z]+/i', $_POST['newpass1']) + preg_match('/[0-9]+/i', $_POST['password']) + preg_match('/[_\-\/+!@#%^$*&)(|.]+/i', $_POST['newpass1'])) < 2) {
			$_SESSION['errors'][] = 'Passwords must use a combination of letters, numbers and symbols.';
		}
	}

  /******* end password stuff *****/


  // ********** sanitize this data input ***********
  $member_id = mysqli_real_escape_string($db, $member_id);
  $sql = "select bl_pass from membersCopy where member_id=".$_SESSION['member_id']." and bl_pass = AES_ENCRYPT(concat('".$_SESSION['login'] . "','signlinkcms'), SHA1('".$_POST['oldpass'] . "'))";
  //echo '<pre>sql query is ' . $sql . '</pre>';
  $result = mysqli_query($db, $sql);

  if (mysqli_num_rows($result)==0) {
    $_SESSION['errors'][] = "The old password you entered does not match, use the <a href='password_reminder.php'>password reminder</a> feature if you forgot your password.";  
  }

	if (!isset($_SESSION['errors'])) {		
		$member_id = intval($_SESSION['member_id']);
		$name = $addslashes(htmlspecialchars($_POST['name']));
		$email = $addslashes(htmlspecialchars($_POST['email']));
		
		$sql = "UPDATE members SET name='$name', email='$email' WHERE member_id=$member_id";
		$result = mysqli_query($db, $sql);
		
		if (!empty($_FILES['avatar']['tmp_name'])) {
			save_avatar($member_id);
		}
		
		$_SESSION['feedback'][] = 'Preferences updated.';
		header('Location: preferences.php');
		exit;
	}
}


require(INCLUDE_PATH.'header.inc.php');

$sql = "SELECT login,name,email FROM membersCopy WHERE login='".$_SESSION['login']."' AND member_id=".intval($_SESSION['member_id']);
$result = mysqli_query($db, $sql);
if ($row = mysqli_fetch_assoc($result)) { 
  ?>

  <h2><img src="images/spanner.png" alt="Preferences" title="Preferences" style="padding:3px;" /></h2>

  <form action="<?php echo $_SERVER['PHP_SELF']; ?>?processed=1" method="post" name="form" class="pref_form" enctype="multipart/form-data">
    <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAX_UPLOAD_SIZE; ?>" />
    
    <label>Login</label>
    <div class="row textlogin">
      <?php echo $row['login'] ?><br /><br />
    </div>	
    
    <label>Name</label><br />
    <div class="row">
      <input type="text" name="name" value="<?php echo $row['name'] ?>" /><br /><br />
    </div>

    <label>Email</label><br />
    <div class="row">
      <input type="text" name="email" value="<?php echo $row['email'] ?>" /><br /><br />
    </div>

    <label>Change Password</label><br />
    <div class="row">
      <div class="subrow">
        <label>Enter your old password</label><br />
        <input type="password" name="oldpass" value="" /><br /><br />
        <label>Enter your new password</label><br />
        <input type="password" name="newpass1" value="" /><br /><br />
        <label>Enter your new password again</label><br />
        <input type="password" name="newpass2" value="" /><br /><br />
      </div>
    </div>

    <label>Avatar</label><br />
    <div class="row centeralign">
      <?php get_avatar($_SESSION['member_id']) ?><br />
      <input type="file" class="avatarSubmit" name="avatar" /><br /><br />
    </div>

    <div class="prefsubmit"> 
      <input type="submit" name="submit" value="Submit" />
    </div>
    
  </form>
  <div class="centerImage">
  <a href="index.php"><img src="images/refresh_48.png"/> Back to the main page</a>
  </div>
<?php
}
else {
	echo "Invalid member ID.";
}

 require('include/footer.inc.php'); ?>

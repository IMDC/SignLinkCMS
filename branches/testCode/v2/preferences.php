<?php 
define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');

// check if someone got here by mistake
if (!$_SESSION['valid_user']) {
	$_SESSION['notices'][] = 'You must be logged in to manage your preferences. Please <a href="login.php">login</a>.';
	require(INCLUDE_PATH.'header.inc.php');
	require(INCLUDE_PATH.'footer.inc.php');
	exit;
}
// user is a valid user
else if ($_POST['submit'] || $_GET['processed']) {
	//check if there are any upload errors
	if(empty($_POST)) {
		$_SESSION['errors'][] = "General error. Your URL is incorrect or you are trying to post a file that is too large for this installation.";
		require(INCLUDE_PATH.'header.inc.php');
		require(INCLUDE_PATH.'footer.inc.php');
		exit;
	}
	check_uploads();

   //$member_id = mysqli_real_escape_string($db, $_SESSION['member_id']);
   //$sql = "select bl_pass, name from membersCopy where member_id=".$_SESSION['member_id']." and bl_pass = AES_ENCRYPT(concat('".$_SESSION['login'] . "','signlinkcms'), SHA1('".$_POST['oldpass'] . "'))";
   $sql = "SELECT name,email FROM membersCopy WHERE member_id=".$_SESSION['member_id'];
   $result = mysqli_query($db, $sql);
   
   /*** Retrieve users name and password for future checks ***/
   if (mysqli_num_rows($result)==0) {   
      // if no results return from sql query, exit and display header/footer
      $_SESSION['errors'][] = 'Your account info could not be retrieved right now, please try again later<br />or contact your administrator';
      require(INCLUDE_PATH.'header.inc.php');
      require(INCLUDE_PATH.'footer.inc.php');
      exit;
   }
   else {
      // save results
      $row = mysqli_fetch_assoc($result);

      $name_change_requested = 0;
      $email_change_requested = 0;
      
      /***** Name Stuff ********/

      if ($_POST['name'] != $row['name']) {
         // user wants to change name
         // check name length make sure it's less than 40 chars
         if (strlen($_POST['name']) > 40) {
            $_SESSION['errors'][] = 'Name chosen is too long, please try again';
         }
         else {
            $name_change_requested = 1;
         }
         
      }
      /***** End NAME STUFF ******/

      /***** Email Stuff ********/

      if ($_POST['email'] != $row['email']) {
         // user wants to change email
         // check length make sure it is valid
         if (!eregi("^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,6}$", $_POST['email'])) {
            $_SESSION['errors'][] = 'Please enter a valid email.';
         }
         else {
            $email_change_requested = 1;
         }
      }
      /***** End Email STUFF ******/
   
   
      /***** PASSWORD CHECK STUFF ******/

      if ($_POST['oldpass'] == '') { 
         // skip this, they probably dont want to change their password
         //$_SESSION['errors'][] = 'Please enter a password.';
      }
      else {
         if ($_POST['newpass1'] != $_POST['newpass2']){
            $_SESSION['errors'][] = 'Passwords do not match.';
            break;
         }
         if (!preg_match('/^\S{8,}$/u', $_POST['newpass1'])) { 
            $_SESSION['errors'][] = 'Passwords must be greater than 8 characters.';
            break;
         } 
         if ((preg_match('/[a-z]+/i', $_POST['newpass1']) + preg_match('/[0-9]+/i', $_POST['password']) + preg_match('/[_\-\/+!@#%^$*&)(|.]+/i', $_POST['newpass1'])) < 2) {
            $_SESSION['errors'][] = 'Passwords must use a combination of letters, numbers and symbols.';
            break;
         }
         
         // check old password against db value
         $sql = "select bl_pass from membersCopy where member_id=".$_SESSION['member_id']." and bl_pass = AES_ENCRYPT(concat('".$_SESSION['login'] . "','signlinkcms'), SHA1('".$_POST['oldpass'] . "'))";
         //echo '<pre>sql query is ' . $sql . '</pre>';
         $result = mysqli_query($db, $sql);

         if (mysqli_num_rows($result)==0) {
            $_SESSION['errors'][] = "The old password you entered does not match, use the <a href='password_reminder.php'>password reminder</a> feature if you forgot your password.";  
            break;
         }
      }
      /******* end password check stuff *****/
   }

   // if there are no errors, go ahead and process the changes requested
	if (!isset($_SESSION['errors'])) {	
		$member_id = intval($_SESSION['member_id']);
      
      if ($name_change_requested || $email_change_requested) {
         $sql = "UPDATE membersCopy ";
         
         if ($name_change_requested) {
            $newname = mysqli_real_escape_string($db, htmlspecialchars($_POST['name']));
            $sql .= "SET name='$newname' ";
            $_SESSION['feedback'][] = 'Name has been changed';
         }
         else if ($email_change_requested) {
            $newemail = mysqli_real_escape_string($db, htmlspecialchars($_POST['email']));
            $sql .= "SET email='$newemail' ";
            $_SESSION['feedback'][] = 'Email has been changed';
         }
         
         $sql .= "WHERE member_id=$member_id";
         $_SESSION['feedback'][] =  $sql;
         $result = mysqli_query($db, $sql);
         if (mysqli_num_rows($result)==0) {
            // if no results return from sql query, exit and display header/footer
            $_SESSION['errors'][] = 'Your account info could not be updated right now, please try again later<br />or contact your administrator';
            require(INCLUDE_PATH.'header.inc.php');
            require(INCLUDE_PATH.'footer.inc.php');
            exit;
         }
      }
		
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
$row = mysqli_fetch_assoc($result);
if ($row) {
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

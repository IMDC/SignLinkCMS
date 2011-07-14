<?php 
define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');

// check if someone got here by mistake
if (!$_SESSION['valid_user']) 
{
	$_SESSION['notices'][] = 'You must be logged in to manage your preferences. Please <a href="login.php">login</a>.';
	require(INCLUDE_PATH.'header.inc.php');
	require(INCLUDE_PATH.'footer.inc.php');
	exit;
}
// user is a valid user
else if ($_POST['submit'] || $_GET['processed']) 
{
   //check if there are any upload errors
   if(empty($_POST)) {
      $_SESSION['errors'][] = "General error. Your URL is incorrect or you are trying to post a file that is too large or incorrectly formatted for this installation.";
      require(INCLUDE_PATH.'header.inc.php');
      require(INCLUDE_PATH.'footer.inc.php');
      exit;
   }
	
   check_uploads();

   // escape these values for later use in queries
   $prefMemberID = mysqli_real_escape_string($db, $_SESSION['member_id']);
   $prefMemberLogin = mysqli_real_escape_string($db, $_SESSION['login']);
   
   //$sql = "select bl_pass, name from membersCopy where member_id=".$_SESSION['member_id']." and bl_pass = AES_ENCRYPT(concat('".$_SESSION['login'] . "','signlinkcms'), SHA1('".$_POST['oldpass'] . "'))";
   $sql = "SELECT name,email FROM members WHERE member_id=".$prefMemberID;
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

      $name_change_requested = 0; $email_change_requested = 0; $password_change_requested = 0;

      /***** Name Stuff ********/

      if ($_POST['prefname'] != $row['name']) {
        // user wants to change name
        // check name length make sure it's less than 40 chars
        if (strlen($_POST['prefname']) > 40) {
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
         /*
         // check length make sure it is valid
         if (!eregi("^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,6}$", $_POST['email'])) {
            $_SESSION['errors'][] = 'Please enter a valid email.';
         }
         // check that email is unique in database
         $userEmail = mysqli_real_escape_string($db, $_POST['email']);
         $sql = "select * from members where email LIKE '%$userEmail%'";
         $result = mysqli_query($db, $sql);
         if (mysqli_num_rows($result) > 0) {
              $_SESSION['errors'][] = 'Email address is already in use, please enter a new email';
         }
         */
         if (validateEmail($_POST['email'], 1)) {
           // email passes checks, set change flag for later
           $email_change_requested = 1;
         }
         else {
           $_SESSION['errors'][] = 'Email address is already in use, please enter a new email';
         }
      }
      /***** End Email STUFF ******/


      /***** PASSWORD CHECK STUFF ******/

      if ($_POST['oldpass'] == '') {
         // skip this, they probably dont want to change their password
         //$_SESSION['errors'][] = 'Please enter a password.';
      }
      else {
         
         if ($_POST['newpass1'] != $_POST['newpass2']) {
            $_SESSION['errors'][] = 'Passwords do not match.';
         }
         if (!preg_match('/^\S{8,}$/u', $_POST['newpass1'])) { 
            $_SESSION['errors'][] = 'Passwords must be greater than 8 characters.';
         } 
         if ((preg_match('/[a-z]+/i', $_POST['newpass1']) + preg_match('/[0-9]+/i', $_POST['newpass1']) + preg_match('/[_\-\/+!@#%^$*&)(|.]+/i', $_POST['newpass1'])) < 2) {
            $_SESSION['errors'][] = 'Passwords must use a combination of letters, numbers and symbols.';
         }

         // check old password against db value
         $sql = "select bl_pass from members where member_id=".intval($_SESSION['member_id'])." and bl_pass = AES_ENCRYPT(concat('".mysqli_real_escape_string($db,$_SESSION['login']) . "','signlinkcms'), SHA1('".mysqli_real_escape_string($db,$_POST['oldpass']) . "'))";
         //echo '<pre>sql query is ' . $sql . '</pre>';
         $result = mysqli_query($db, $sql);

         if (mysqli_num_rows($result)==0) {
            $_SESSION['errors'][] = "The old password you entered does not match, use the <a href='password_reset_request.php'>password reset</a> feature if you forgot your password.";  
         }
         else {
            $password_change_requested = 1;
         }
      }
      /******* end password check stuff *****/

   }

   // if there are no errors, go ahead and process the changes requested
   if (!isset($_SESSION['errors'])) {	
   $member_id = intval($_SESSION['member_id']);

      if ($name_change_requested || $email_change_requested) {
         $sql = "UPDATE members SET ";

         if ($name_change_requested) {
            $newname = mysqli_real_escape_string($db, htmlspecialchars($_POST['prefname']));
            $sql .= "name='$newname' ";
            $_SESSION['feedback'][] = 'Name has been changed.';
         }
         if ($name_change_requested && $email_change_requested) {
                 $sql .= ", ";
         }
         if ($email_change_requested) {
                 $newemail = mysqli_real_escape_string($db, htmlspecialchars($_POST['email']));
                 $sql .= "email='$newemail' ";
                 $_SESSION['feedback'][] = 'Email has been changed.';
         }

         $sql .= "WHERE member_id=$prefMemberID";
         //$_SESSION['feedback'][] =  $sql;
         $result = mysqli_query($db, $sql);

         if (mysqli_affected_rows($db)<= 0) {
            // if no results return from sql query, exit and display header/footer
            $_SESSION['errors'][] = 'Your account info could not be updated right now, please try again later or contact your administrator. ';
            require(INCLUDE_PATH.'header.inc.php');
            require(INCLUDE_PATH.'footer.inc.php');
            exit;
         }
         
         // change password
         if ($password_change_requested) {
            $sql = "UPDATE members SET bl_pass = AES_ENCRYPT(concat(" . $prefMemberLogin . ",'signlinkcms'), SHA1('" . mysqli_real_escape_string($db, $_POST['newpass1']) . "')) where member_id=".intval($prefMemberID);
            $result = mysqli_query($db, $sql);
            $_SESSION['feedback'][] = $sql;
            if (mysqli_affected_rows($db) <= 0) {
               // if no results return from sql query, exit and display header/footer
               $_SESSION['errors'][] = 'Your account info could not be updated right now, please try again later or contact your administrator. ';
               trigger_error($sql, E_USER_WARNING);
               require(INCLUDE_PATH.'header.inc.php');
               require(INCLUDE_PATH.'footer.inc.php');
               exit;
            }
            else {
               $_SESSION['feedback'][] = 'Your password has been changed';
            }
         }
         
         
      }

      if (!empty($_FILES['avatar']['tmp_name'])) {
            save_avatar($prefMemberID);
      }

      //$_SESSION['feedback'][] = 'Preferences updated.';
      header('Location: preferences.php');
      exit;
   }
}

require(INCLUDE_PATH.'header.inc.php');

$sql = "SELECT login,name,email FROM members WHERE login='".$_SESSION['login']."' AND member_id=".intval($_SESSION['member_id']);
$result = mysqli_query($db, $sql);
$row = mysqli_fetch_assoc($result);

if ($row) 
{ ?>
   <script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.8.1/jquery.validate.min.js"></script>
   <script src="jscripts/prefs.js"></script>
  <h2><img src="images/spanner.png" alt="Preferences" title="Preferences" style="padding:3px;" /></h2>

  <form action="<?php echo htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES); ?>?processed=1" method="post" id="prefForm" name="form" class="pref_form" autocomplete="off" enctype="multipart/form-data">
    <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAX_UPLOAD_SIZE; ?>" />
    
    <label>Login</label>
    <div class="row textlogin">
      <?php echo $row['login'] ?><br /><br />
    </div>	
    
    <label>Name</label><br />
    <div class="row">
       <p><em>Note: This is not displayed in the forum. It is only used for password reset emails.</em></p>
      <input type="text" id="prefname" name="prefname" value="<?php echo $row['name'] ?>" />
    </div>

    <label>Email</label><br />
    <div class="row">
      <input type="text" id="prefemail" name="email" value="<?php echo $row['email'] ?>" />
    </div>

    <label>Change Password</label><br />
    <div class="row">
      <div class="subrow">
        <label id="loldpass" for="oldpass">Enter your old password</label><br />
        <input type="password" id="oldpass" name="oldpass" value="" maxlenth="100" /><br />
        <label id="lnewpass1" for="newpass1">Enter your new password</label><br />
        <input type="password" id="newpass1" name="newpass1" value="" maxlength="100" /><br />
        <label id="lnewpass2" for="newpass2">Enter your new password again</label><br />
        <input type="password" id="newpass2" name="newpass2" value="" maxlength="100" />
      </div>
    </div>

    <label>Avatar</label><br />
    <div class="row centeralign">
      <?php get_avatar($_SESSION['member_id']) ?><br />
      <input type="file" class="avatarSubmit" name="avatar" />
      <p><em>Images bigger than 120 pixels will be resized</em></p><br /><br />
    </div>

    <div class="prefsubmit">
<!--      <input type="submit" name="submit" value="Submit" class="submitBtn" /> -->
      <button type="button" id="testbutton" name="testbtn" value="Monkeys" class="submitIconBtn" style="margin-right:48px;padding:5px 10px;"> Submit <img src="images/yescheck.png" alt="" class="inlineVertMid" /></button>
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

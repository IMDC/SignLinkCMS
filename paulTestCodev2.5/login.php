<?php 
define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');

if (isset($_POST['loginSubmit']) || isset($_POST['password']) || isset($_POST['submitImg.x'])) {
	$this_login = $_POST['login'];
 
  /*
	if (strlen($_POST['password_hidden']) < 40) { // <noscript> on client end
		$this_password = sha1($_POST['password'] . $_SESSION['token']);
	} else { // sha1 ok
    $this_password = $_POST['password_hidden'];
	}
  */
   $this_password = mysqli_real_escape_string($db, $_POST['password']);
   $used_cookie	= false;
  // login/pass good to here
}

if (isset($this_login, $this_password)) {
	/*if (version_compare(PHP_VERSION, '5.1.0', '>=')) {
		session_regenerate_id(TRUE);
	}*/

  /*
	$this_login    = $addslashes($this_login);
	$this_password = $addslashes($this_password);
  */

  /* addslashes as of Jan 2011 is not working (after server migration)
  ** have replaced it with mysqli_real_escape_string for now **/
   $this_login    = mysqli_real_escape_string($db, $this_login);
   $this_password = mysqli_real_escape_string($db, $this_password);

   /*if ($used_cookie) {
      // check if that cookie is valid
      $sql = "SELECT member_id, login, SHA1(CONCAT(password, '-', '".DB_PASSWORD."')) AS pass FROM members WHERE login='$this_login' AND SHA1(CONCAT(password, '-', '".DB_PASSWORD."'))='$this_password'";

   } 
   else {*/
		//$sql = "SELECT member_id, login, SHA1(CONCAT(password, '-', '".DB_PASSWORD."')) AS pass FROM members WHERE login='$this_login' AND SHA1(CONCAT(password, '$_SESSION[token]'))='$this_password'";
    //$sql = "SELECT member_id, login, sh_pass from members where login='$this_login'";
    $sql = "SELECT member_id, login, name, status, last_login_ts FROM members where login = '$this_login' and bl_pass = AES_ENCRYPT(concat('$this_login','signlinkcms'), SHA1('$this_password'))";
    //print $sql;
   //}
   
   $result = mysqli_query($db, $sql);
  
   if (!$result) { 
      $_SESSION['errors'][] = 'Could not successfully run query($sql) from DB: ' . mysqli_error();
      require(INCLUDE_PATH.'header.inc.php');
      require(INCLUDE_PATH.'footer.inc.php');
      exit;
   }
   $row = mysqli_fetch_assoc($result);
   if ($row) {
       
        if (intval($row['status']) == 0)
        {
            $_SESSION['errors'][] = 'This account has not been activated. <br />You must activate your account before you are able to log in.';
            require(INCLUDE_PATH.'header.inc.php');
            require(INCLUDE_PATH.'footer.inc.php');
            exit;
        }
       
        $_SESSION['valid_user'] = true;
        $_SESSION['member_id']	= intval($row['member_id']);
        $_SESSION['login']		= $row['login'];
        $_SESSION['is_guest']	= 0;

                
		/*
      if ($auto_login == 1) {
			$parts = parse_url(htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES));
			// update the cookie.. increment to another 2 days
			$cookie_expire = time()+172800;
			setcookie('SLLogin', $this_login, $cookie_expire, $parts['path'], $parts['host'], 0);
			setcookie('SLPass',  $row['pass'],  $cookie_expire, $parts['path'], $parts['host'], 0);
		}*/

         //$sql = "UPDATE ".TABLE_PREFIX."members SET creation_date=creation_date, last_login=NOW() WHERE member_id=$_SESSION[member_id]";

         //$sql = "UPDATE membersCopy set last_login_ts = NOW() WHERE member_id = $_SESSION[member_id]";
         //$_SESSION['feedback'][] = mysqli_query($db, $sql);

         //update_member_last_login($_SESSION['member_id']);  //updates the last time the member logged in to right now for future logins

         $_SESSION['feedback'][] = 'Successfully logged in.';
         $_SESSION['feedback'][] = 'Welcome back ' . $row['name'] . '!';
         if (update_member_last_login($_SESSION['member_id'])) {
            // last user login successfully updated
            if ( substr($row['last_login_ts'], 0, 3) == "0000") {
               $_SESSION['feedback'][] = 'Welcome to our site! Why not <a href="preferences.php">make a custom avatar?</a>';
            }
            else {
               $_SESSION['feedback'][] = 'Your last login was: ' . $row['last_login_ts'];
            }
         }

         if (isset($_POST['f']) && !empty($_POST['f'])) {
            header('Location:forum_post_create.php?f='.$_POST['f'].'&p='.$_POST['p']);
         }
         else if (isset($_POST['v']) && !empty($_POST['v']) && isset($_POST['e']) && !empty($_POST['e'])) {
            header('Location:vlog_comment_create.php?v='.$_POST['v'].'&e='.$_POST['e']);
         }
         else {
            header('Location:index.php');
         }
         exit;
      }
      else {
           $_SESSION['errors'][] = 'Invalid login.';
      }

   }

$_SESSION['session_test'] = TRUE;

unset($_SESSION['login']);
unset($_SESSION['valid_user']);
unset($_SESSION['member_id']);
unset($_SESSION['is_admin']);
unset($_SESSION['is_super_admin']);


require(INCLUDE_PATH.'header.inc.php'); ?>

<script language="JavaScript" src="jscripts/sha-1factory.js" type="text/javascript"></script>
<script language="JavaScript" src="jscripts/login.js" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript">
//<!--
  function crypt_sha1() {
  	document.form.password_hidden.value = hex_sha1(document.form.password.value + "<?php echo $_SESSION['token']; ?>");
  	document.form.password.value = "";
  	return true;
  }
 //-->
</script>

<h2>Login</h2>

<p><a href="register.php"><img src="images/user_add.png" class="inlineVertMid" />Register</a> for a new account or use the <a href="password_reset.php"><img src="images/mail_key_small.png" class="inlineVertMid" />Password Reminder</a> if you have forgotten your login information.</p>

<form action="<?php echo htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES); ?>" method="post" name="form">
		<input type="hidden" name="login_action" value="true" />
		<input type="hidden" name="password_hidden" value="" />

		<input type="hidden" name="f" value="<?php echo intval($_REQUEST['f']); ?>" />
		<input type="hidden" name="p" value="<?php echo intval($_REQUEST['p']); ?>" />
		<input type="hidden" name="v" value="<?php echo intval($_REQUEST['v']); ?>" />
		<input type="hidden" name="e" value="<?php echo intval($_REQUEST['e']); ?>" />

	<dl class="col-list" style="width:33%; margin-left:auto; margin-right:auto;">
		<dt><label for="login"><img src="images/user_med.png" alt="login" title="login" />Login:</label></dt>
			<dd><input name="login" type="text" id="login" class="loginfocus" value="<?php echo $_SERVER['login']; ?>" /></dd>
		<dt><label for="pswd"><img src="images/key3.png" alt="password" title="password" />Password:</label></dt>
			<dd><input name="password" type="password" id="pswd" value="" /></dd>
	</dl>
	<div class="centeralign" style="width:33%;">
		<!-- label><input type="checkbox" name="autologin" value="1" /> keep me logged-in</label><br / --><br />
		<input type="submit" name="submit" value="Submit" class="submitBtn" />
<!--      <button type="button" id="testbutton" name="testbtn" value="Monkeys" class="submitIconBtn" style="margin-right:48px;padding:5px 10px;"> Submit <img src="images/yescheck.png" alt="" class="inlineVertMid" /></button>-->
   </div>
</form>

<?php require(INCLUDE_PATH.'footer.inc.php'); ?>

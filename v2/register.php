<?php 
define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');

//if (isset($_POST['submit'])) {
if ($_POST) {

	//$chk_email = $addslashes($_POST['email']);
	$chk_email = mysqli_real_escape_string($db, $_POST['email']);
	//$chk_login = $addslashes($_POST['login']);
	$chk_login= mysqli_real_escape_string($db, $_POST['login']);

	//error check
	if (empty($_POST['name'])) {
		$_SESSION['errors'][] = 'Please enter your name.';
	}
   
	if (empty($_POST['email'])) {
		$_SESSION['errors'][] = 'Please enter your email.';
	}
  else if (!eregi("^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,6}$", $_POST['email'])) {
		$_SESSION['errors'][] = 'Please enter a valid email.';
	} 
	
	$result = mysqli_query($db, "SELECT * FROM members WHERE email='$chk_email'");
	if (mysqli_num_rows($result) != 0) {
		$_SESSION['errors'][] = 'Email address already in use. Try the <a href="password_reset.php">Password Reset.</a>';
	}

	if (empty($_POST['login'])) {
		$_SESSION['errors'][] = 'Please enter a login name.';
	} 
  else {
		// check for special characters 
		if (!(eregi("^[a-zA-Z0-9_.-]([a-zA-Z0-9_.-])*$", $_POST['login']))) {
			$_SESSION['errors'][] = 'Login name taken. Please try another.';
		}
    else {
      $result = mysqli_query($db, "SELECT * FROM members WHERE login='$chk_login'");
      if (mysqli_num_rows($result) != 0) {
        $_SESSION['errors'][] = 'Login name already exists.';
      }
      /*else {
        $result = mysqli_query($db, "SELECT * FROM admins WHERE login='$chk_login'");
        if (mysqli_num_rows($result) != 0) {
          $msg->addError('LOGIN_EXISTS');
        }
      }*/
    }
	}	

	if ($_POST['password'] == '') { 
		$_SESSION['errors'][] = 'Please enter a password.';
	} else {
		if ($_POST['password'] != $_POST['password2']){
			$_SESSION['errors'][] = 'Passwords do not match.';
		}
		if (!preg_match('/^\S{8,}$/u', $_POST['password'])) { 
			$_SESSION['errors'][] = 'Passwords must be greater than 8 characters.';
		} 
		if ((preg_match('/[a-z]+/i', $_POST['password']) + preg_match('/[0-9]+/i', $_POST['password']) + preg_match('/[_\-\/+!@#%^$*&)(|.]+/i', $_POST['password'])) < 2) {
			$_SESSION['errors'][] = 'Passwords must use a combination of letters, numbers and symbols.';
		}
	}

	if (!isset($_SESSION['errors'])) {
		//$name       = $addslashes(trim($_POST['name']));
		//$email      = $addslashes(trim($_POST['email']));
		//$login      = $addslashes(trim($_POST['login']));

		$name       = mysqli_real_escape_string($db, trim($_POST['name']));
		$email      = mysqli_real_escape_string($db, trim($_POST['email']));
		$login      = mysqli_real_escape_string($db, trim($_POST['login']));

		$password   = mysqli_real_escape_string($db, trim($_POST['password']));

    $sql = "INSERT INTO members VALUES (NULL, '$login', '$password', '$name', '$email', AES_ENCRYPT(concat('$login','signlinkcms'), SHA1('$password')), DEFAULT, DEFAULT, 0)";
		$result = mysqli_query($db, $sql);

		if (!$result) {
      // enable the line below to get the sql error as feedback when you are unable to register
      //$_SESSION['errors'][] = mysqli_error($db);
			$_SESSION['errors'][] = 'Database error - user not added. Please register again.';
			exit;
		}

		//send email to registrant, not currently implemented

		$_SESSION['feedback'][] = 'Registration successful. Please login.';
		header('Location: login.php');
		exit;
	}
}

require(INCLUDE_PATH.'header.inc.php');
if (REGISTRATION_CLOSED) {
   require('registration_closed.php');
}

?>

<h2><img src="images/user_add_48.png" />Register</h2>
<p>Create a new account.</p>
<p><em>Contact information - this will be used to contact you and retrieve a forgotten password, never for spam</em></p>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="registerform" class="expose">
	<dl class="col-list">
		<dt><label for="name"><img src="images/user_small.png" class="inlineVertMid" />Name:</label></dt> 
			<dd><input name="name" type="text" id="name" value="<?php echo $_POST['name']; ?>" />&nbsp;&nbsp;<img src="images/example3.png" />"Carmen Smith"</dd><br />
		<dt><label for="email"><img src="images/email_small.png" class="inlineVertMid" />Email:</label></dt> 
			<dd><input name="email" type="text" id="email" value="<?php echo $_POST['email']; ?>" />&nbsp;&nbsp;<img src="images/example3.png" />"carmen_smith@example.com"<br /><br /></dd>
         <p><em>User information - To be used on this website</em></p>
		<dt><label for="login"><img src="images/user_small.png" class="inlineVertMid" />Login:</label></dt> 
			<dd><input name="login" type="text" id="login" value="<?php echo $_POST['login']; ?>" />&nbsp;&nbsp;<img src="images/example3.png" />"RockstarCarmen"</dd><br />
		<dt><label for="pswd"><img src="images/key_small.png" class="inlineVertMid" />Password:</label></dt> 
			<dd><input name="password" type="password" id="pswd" value="<?php echo $_POST['password']; ?>" />&nbsp;&nbsp;<img src="images/example3.png" />"9Xxitld4zG"&nbsp;- <em>8+ numbers and letters</em></dd><br />
		<dt><label for="pswd"><img src="images/key_small.png" class="inlineVertMid" />Password Again:</label></dt>
			<dd><input name="password2" type="password" id="pswd2" value="<?php echo $_POST['password2']; ?>" />&nbsp;&nbsp;<img src="images/example3.png" />"9Xxitld4zG"&nbsp;- <em>8+ numbers and letters</em></dd>
	</dl>
	<!-- div style="text-align:center"><label><input type="checkbox" name="autologin" value="1" /> keep me logged-in</label --><br /><br />
	<!-- <input type="image" name="submit" alt="Submit" src="images/registerbtn.png" class="inlineVertMid" style="margin-bottom: 2px;" /> | --> 
   &nbsp;&nbsp;&nbsp;<button type="button" name="cancelbtn" value="cancel" class="cancelIconBtn" onClick="javavscript:history.back(1);">Cancel <img src="images/cancelx.png" alt="" class="inlineVertMid" /></button> | <button type="button" id="testbutton" name="testbtn" value="Monkeys" class="submitIconBtn"> Submit <img src="images/yescheck.png" alt="" class="inlineVertMid" /></button></div>
</form>

<?php require(INCLUDE_PATH.'footer.inc.php'); ?>

<?php 
define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');

if (isset($_POST['submit'])) {

	$chk_email = $addslashes($_POST['email']);
	$chk_login = $addslashes($_POST['login']);

	//error check
	if (empty($_POST['name'])) {
		$_SESSION['errors'][] = 'Please enter your name.';
	} 
	if (empty($_POST['email'])) {
		$_SESSION['errors'][] = 'Please enter your email.';
	} else if (!eregi("^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,6}$", $_POST['email'])) {
		$_SESSION['errors'][] = 'Please enter a valid email.';
	} 
	
	$result = mysql_query("SELECT * FROM members WHERE email='$chk_email'",$db);
	if (mysql_num_rows($result) != 0) {
		$_SESSION['errors'][] = 'Email address already in use. Try the Password Reminder.';
	}

	if (empty($_POST['login'])) {
		$_SESSION['errors'][] = 'Please enter a login name.';
	} else {
		// check for special characters 
		if (!(eregi("^[a-zA-Z0-9_.-]([a-zA-Z0-9_.-])*$", $_POST['login']))) {
			$_SESSION['errors'][] = 'Login name taken. Please try another.';
		} else {
			$result = mysql_query("SELECT * FROM members WHERE login='$chk_login'",$db);
			if (mysql_num_rows($result) != 0) {
				$_SESSION['errors'][] = 'Login name already exists.';
			} /*else {
				$result = mysql_query("SELECT * FROM admins WHERE login='$chk_login'",$db);
				if (mysql_num_rows($result) != 0) {
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
		$name       = $addslashes(trim($_POST['name']));
		$email      = $addslashes(trim($_POST['email']));
		$login      = $addslashes(trim($_POST['login']));
		$password   = $addslashes(trim($_POST['password']));


		$sql = "INSERT INTO members VALUES (NULL, '$login', '$password', '$name', '$email')";
		$result = mysql_query($sql, $db);

		if (!$result) {
			$_SESSION['errors'][] = 'Database error - user not added.';
			exit;
		}

		//send email to registrant

		$_SESSION['feedback'][] = 'Registration successful. Please login.';
		header('Location: login.php');
		exit;
	}
}

require(INCLUDE_PATH.'header.inc.php'); ?>

<h2>Register</h2>
<p>Create a new account.</p>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
	<dl class="col-list">
		<dt><label for="name">Name:</label></dt> 
			<dd><input name="name" type="text" id="name" value="<?php echo $_POST['name']; ?>" /></dd>
		<dt><label for="email">Email:</label></dt> 
			<dd><input name="email" type="text" id="email" value="<?php echo $_POST['email']; ?>" /><br /><br /></dd>

		<dt><label for="login">Login:</label></dt> 
			<dd><input name="login" type="text" id="login" value="<?php echo $_POST['login']; ?>" /></dd>
		<dt><label for="pswd">Password:</label></dt> 
			<dd><input name="password" type="password" id="pswd" value="<?php echo $_POST['password']; ?>" /></dd>
		<dt><label for="pswd">Password Again:</label></dt> 
			<dd><input name="password2" type="password" id="pswd2" value="<?php echo $_POST['password2']; ?>" /></dd>
	</dl>
	<!-- div style="text-align:center"><label><input type="checkbox" name="autologin" value="1" /> keep me logged-in</label --><br /><br />
	<input type="submit" name="submit" value="Register" class="button" /></div>
</form>

<?php require(INCLUDE_PATH.'footer.inc.php'); ?>

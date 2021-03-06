<?php 
define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');

if (isset($_POST['submit'])) {
	$this_login		= $_POST['login'];

	if (strlen($_POST['password_hidden']) < 40) { // <noscript> on client end
		$this_password = sha1($_POST['password'] . $_SESSION['token']);
	} else { // sha1 ok
		$this_password = $_POST['password_hidden'];
	}
	$used_cookie	= false;
}

if (isset($this_login, $this_password)) {
	/*if (version_compare(PHP_VERSION, '5.1.0', '>=')) {
		session_regenerate_id(TRUE);
	}*/

	$this_login    = $addslashes($this_login);
	$this_password = $addslashes($this_password);

	if ($used_cookie) {
		// check if that cookie is valid
		$sql = "SELECT member_id, login, SHA1(CONCAT(password, '-', '".DB_PASSWORD."')) AS pass FROM members WHERE login='$this_login' AND SHA1(CONCAT(password, '-', '".DB_PASSWORD."'))='$this_password'";

	} else {
		$sql = "SELECT member_id, login, SHA1(CONCAT(password, '-', '".DB_PASSWORD."')) AS pass FROM members WHERE login='$this_login' AND SHA1(CONCAT(password, '$_SESSION[token]'))='$this_password'";
	}
	$result = mysql_query($sql, $db);

	if ($row = mysql_fetch_assoc($result)) {
		$_SESSION['valid_user'] = true;
		$_SESSION['member_id']	= intval($row['member_id']);
		$_SESSION['login']		= $row['login'];
		$_SESSION['is_guest']	= 0;

		if ($auto_login == 1) {
			$parts = parse_url($_SERVER['PHP_SELF']);
			// update the cookie.. increment to another 2 days
			$cookie_expire = time()+172800;
			setcookie('SLLogin', $this_login, $cookie_expire, $parts['path'], $parts['host'], 0);
			setcookie('SLPass',  $row['pass'],  $cookie_expire, $parts['path'], $parts['host'], 0);
		}

		//$sql = "UPDATE ".TABLE_PREFIX."members SET creation_date=creation_date, last_login=NOW() WHERE member_id=$_SESSION[member_id]";
		//mysql_query($sql, $db);

		$_SESSION['feedback'][] = 'Successfully logged in.';

		if (isset($_POST['f']) && !empty($_POST['f'])) {
			header('Location:forum_post_create.php?f='.$_POST['f'].'&p='.$_POST['p']);
		} else if (isset($_POST['v']) && !empty($_POST['v']) && isset($_POST['e']) && !empty($_POST['e'])) {
			header('Location:vlog_comment_create.php?v='.$_POST['v'].'&e='.$_POST['e']);
		} else {
			header('Location:index.php');
		}
		exit;	
	} else {
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

<p><a href="register.php">Register</a> for a new account or use the <a href="password_reminder.php">Password Reminder</a> if you have forgotten your login information.</p>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
		<input type="hidden" name="login_action" value="true" />
		<input type="hidden" name="password_hidden" value="" />

		<input type="hidden" name="f" value="<?php echo intval($_REQUEST['f']); ?>" />
		<input type="hidden" name="p" value="<?php echo intval($_REQUEST['p']); ?>" />
		<input type="hidden" name="v" value="<?php echo intval($_REQUEST['v']); ?>" />
		<input type="hidden" name="e" value="<?php echo intval($_REQUEST['e']); ?>" />

	<dl class="col-list" style="width:33%; margin-left:auto; margin-right:auto;">
		<dt><img src="images/user.png" alt="login" title="login" /> <label for="login">Login:</label></dt> 
			<dd><input name="login" type="text" id="login" value="<?php echo $_SERVER['login']; ?>" /></dd>
		<dt><img src="images/key.png" alt="password" title="password" /> <label for="pswd">Password:</label></dt> 
			<dd><input name="password" type="password" id="pswd" value="" /></dd>
	</dl>
	<div style="text-align:center">
		<!-- label><input type="checkbox" name="autologin" value="1" /> keep me logged-in</label><br / --><br />
		<input type="submit" name="submit" value="Submit" class="button" />
	</div>
</form>

<?php require(INCLUDE_PATH.'footer.inc.php'); ?>

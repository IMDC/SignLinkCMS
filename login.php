<?php 
define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');

// check if we have a cookie
if (isset($_COOKIE['SLLogin'])) {
	$cookie_login = $_COOKIE['SLLogin'];
}
if (isset($_COOKIE['SLPass'])) {
	$cookie_pass  = $_COOKIE['SLPass'];
}

if (isset($cookie_login, $cookie_pass) && !isset($_POST['submit'])) {
	/* auto login */
	$this_login		= $cookie_login;
	$this_password	= $cookie_pass;
	$auto_login		= 1;
	$used_cookie	= true;

} else if (isset($_POST['submit'])) {
	$this_login		= $_POST['login'];

	if (strlen($_POST['password_hidden']) < 40) { // <noscript> on client end
		$this_password = sha1($_POST['password'] . $_SESSION['token']);
	} else { // sha1 ok
		$this_password = $_POST['password_hidden'];
	}

	$auto_login		= isset($_POST['auto']) ? intval($_POST['auto']) : 0;
	$used_cookie	= false;
}

if (isset($this_login, $this_password) && !isset($_SESSION['session_test'])) {
	$_SESSION['errors'][] = 'Cookies not turned on.';
} else if (isset($this_login, $this_password)) {
	if (version_compare(PHP_VERSION, '5.1.0', '>=')) {
		session_regenerate_id(TRUE);
	}

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
		require(INCLUDE_PATH.'header.inc.php');
		require(INCLUDE_PATH.'footer.inc.php');
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


<script language="JavaScript" src="sha-1factory.js" type="text/javascript"></script>

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
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
		<input type="hidden" name="login_action" value="true" />
		<input type="hidden" name="password_hidden" value="" />

	<dl class="col-list">
		<dt><label for="login">Login:</label></dt> 
			<dd><input name="login" type="text" id="login" value="<?php echo $_SERVER['login']; ?>" /></dd>
		<dt><label for="pswd">Password:</label></dt> 
			<dd><input name="password" type="password" id="pswd" value="" /></dd>
	</dl>
	<div style="text-align:center"><label><input type="checkbox" name="autologin" value="1" /> keep me logged-in</label><br /><br />
	<input type="submit" name="submit" value="Submit" /></div>
</form>

<p style="text-align:center;">If you do not yet have a login, please <a href="register.php">register</a> an account.</p>

<?php require(INCLUDE_PATH.'footer.inc.php'); ?>

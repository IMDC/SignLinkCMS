<?php 
define('INCLUDE_PATH', '../include/');
require(INCLUDE_PATH.'vitals.inc.php');

if (isset($_POST['submit'])) {
	$this_login		= $addslashes($_POST['login']);
	//$this_password = $addslashes(sha1($_POST['password'] . $_SESSION['token']));
	$this_password = $addslashes($_POST['password']);
}

if (isset($this_login, $this_password) && !isset($_SESSION['session_test'])) {
	$_SESSION['errors'][] = 'Cookies not turned on.';
} else if (isset($this_login, $this_password)) {
	if (version_compare(PHP_VERSION, '5.1.0', '>=')) {
		session_regenerate_id(TRUE);
	}

	//$sql = "SELECT member_id, login, SHA1(CONCAT(password, '-', '".DB_PASSWORD."')) AS pass FROM members WHERE login='$this_login' AND SHA1(CONCAT(password, '$_SESSION[token]'))='$this_password'";	

	$sql = "SELECT member_id, login, password FROM members WHERE login='$this_login' AND password='$this_password'";	
	$result = mysql_query($sql, $db);

	if ($row = mysql_fetch_assoc($result)) {
		$_SESSION['valid_user'] = true;
		$_SESSION['member_id']	= intval($row['member_id']);
		$_SESSION['login']		= $row['login'];
		$_SESSION['is_admin'] = true;

		//$sql = "UPDATE ".TABLE_PREFIX."members SET creation_date=creation_date, last_login=NOW() WHERE member_id=$_SESSION[member_id]";
		//mysql_query($sql, $db);

		$_SESSION['feedback'][] = 'Successfully logged in.';
		header('Location:index.php');
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

require(INCLUDE_PATH.'admin_header.inc.php'); 
?>

<script language="JavaScript" src="../jscripts/sha-1factory.js" type="text/javascript"></script>
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

<p>Please login below.</p>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
	<input type="hidden" name="password_hidden" value="" />

	<dl class="col-list" style="width:33%; margin-left:auto; margin-right:auto;">
		<dt><label for="login">Login:</label></dt> 
			<dd><input name="login" type="text" id="login" value="<?php echo $_SERVER['login']; ?>" /></dd>
		<dt><label for="pswd">Password:</label></dt> 
			<dd><input name="password" type="password" id="pswd" value="" /></dd>
	</dl>
	<div style="text-align:center; padding-top:1em;">
		<input type="submit" name="submit" value="Submit" class="button" />
	</div>
</form>

<?php require('../include/footer.inc.php'); ?>

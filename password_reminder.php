<?php

define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');

require(INCLUDE_PATH.'header.inc.php');

if (isset($_POST['cancel'])) {
	header('Location: index.php');
	exit;

} else if ($_POST['email'] || $_GET['processed']) {

}

?>

<h3>Password Reminder</h3>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>?processed=1" method="post">

	<p>Enter your email address, and your login details will be mailed to you.</p>
	
	Email address <input type="text" name="email" /><br /><br />
	
	<input type="submit" name="submit" value="Submit" /> | <input type="button" onclick="javascript:history.back(1)" value="Cancel" />

</form>

<?php require(INCLUDE_PATH.'footer.inc.php'); ?>
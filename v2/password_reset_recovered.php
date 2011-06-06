<?php

define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');


if (isset($_POST['cancel'])) {
	header('Location: index.php');
	exit;

} else if ($_POST['submit'] || $_GET['processed']) {
		//$sql = "SELECT login, password FROM members WHERE email='".addslashes($_POST['email'])."'";
		$sql = "UPDATE members SET password = (SELECT SUBSTRING(md5(mt_rand()) FROM 1 TO 12) WHERE email='".mysqli_real_escape_string($db, $_POST['email'])."'";
		$result = mysql_query($sql, $db);		
		
		if ($row = @mysql_fetch_assoc($result)) {		
			$body = "SignlinkCMS\n\nYou have requested that we reset your password on " . $SITE_NAME;
			$body .= "The following are your login details for the Signlink forum website:"."\n\n";
			$body .= "Login: ". $row['login']."\n";
			$body .= "Password: ". $row['password']."\n";
			
			//send email
			require(INCLUDE_PATH . 'phpmailer/class.phpmailer.php');
			$mail = new PHPMailer();
			$mail->From     = 'noreply@signlinkstudio.ca';
			$mail->AddAddress($_POST['email']);
			$mail->Subject = "Signlink Password Reminder";
			$mail->Body    = $body;
	
			if(!$mail->Send()) {
			   $_SESSION['errors'][] = "Sending error.";
			} else {	
				$_SESSION['feedback'][] = "Your login details have been emailed.";
				unset($mail);
				require(INCLUDE_PATH.'header.inc.php');
				require(INCLUDE_PATH.'footer.inc.php');
				exit;
			}
		} else {
			$_SESSION['errors'][] = "Email not found.";
		}
}

require(INCLUDE_PATH.'header.inc.php');

?>

<h3>Password Reminder</h3>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>?processed=1" method="post">

	<p>Enter your email address, and your login details will be mailed to you.</p>

   <dt><label for="email"><img src="images/email_small.png" class="inlineVertMid" />Email:</label></dt> 
			<dd><input type="text" name="email" style="width:20em;" />&nbsp;&nbsp;<img src="images/example3.png" />"carmen_smith@example.com"<br /><br /></dd>

	<input type="submit" class="submitBtn" name="submit" value="Submit" /> | <input type="submit" name="cancel" value="Cancel" class="cancelBtn" />

</form>

<?php require(INCLUDE_PATH.'footer.inc.php'); ?>

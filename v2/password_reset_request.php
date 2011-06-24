<?php

define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');


if (isset($_POST['cancel'])) {
	header('Location: index.php');
	exit;

} else if ($_POST['email'] || $_GET['processed']) {
		$sql = "SELECT member_id, name, bl_pass FROM members WHERE email='".mysqli_real_escape_string($db, $_POST['email'])."'";
		$result = mysqli_query($db, $sql);
		
		if ($row = @mysqli_fetch_assoc($result))
                {
                    
                        //generate hash and url
                        do{
                            $hash = sha1($row['bl_pass'] . time());
                            $result = mysqli_query($db, "SELECT * FROM members WHERE passresethash=$hash");
                        }while(mysqli_num_rows($result) != 0);
                        
                        $url = str_replace("/password_reset_request.php?processed=1" , "/password_reset.php?".$hash , "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
                        
                        //add hash and time to database
                        $now = date('Y-m-d G:i:s', strtotime('+1 day'));
                        $sql = "UPDATE members SET passresethash='$hash', passresetexp_ts='$now' WHERE member_id='" . $row['member_id']."'";
                        $result = mysqli_query($db, $sql);

                        //email body
			$body  = "Hello ". $row['name'] .",\n\n";
                        $body .= "You have requested to reset your password for your SignLink account. \n";
                        $body .= "To confirm your request, please visit the following link: \n\n";
			$body .= $url. "\n\n";
                        $body .= "If you did not request a password reset, you do not need to take any action.\n";
                        $body .= "This link will expire within the next 24 hours.\n\n";
                        $body .= "Thank you,\n";
                        $body .= "SignLink Team";
			
			//send email
			require(INCLUDE_PATH . 'phpmailer/class.phpmailer.php');
			$mail = new PHPMailer();
			$mail->From     = 'noreply@signlinkstudio.ca';
			$mail->AddAddress($_POST['email']);
			$mail->Subject = "Signlink Password Reset Request";
			$mail->Body    = $body;
	
			if(!$mail->Send()) {
			   $_SESSION['errors'][] = "Sending error.";
			} else {	
				$_SESSION['feedback'][] = "Instructions on how to reset your password have been emailed.";
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

<h3>Password Reset Request</h3>

<form action="<?php echo htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES); ?>?processed=1" method="post">

	<p>Enter your email address, and password reset instructions will be mailed to you.</p>

   <dt><label for="email"><img src="images/email_small.png" class="inlineVertMid" />Email:</label></dt> 
			<dd><input type="text" name="email" style="width:20em;" />&nbsp;&nbsp;<img src="images/example3.png" />"carmen_smith@example.com"<br /><br /></dd>

	<input type="submit" class="submitBtn" name="submit" value="Submit" /> | <input type="submit" name="cancel" value="Cancel" class="cancelBtn" />

</form>

<?php require(INCLUDE_PATH.'footer.inc.php'); ?>

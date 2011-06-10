<?php

    define('INCLUDE_PATH', 'include/');
    require(INCLUDE_PATH.'vitals.inc.php');
    
    $hash = $_SERVER['QUERY_STRING'];
    
    $sql = "SELECT name, login, email, passresetexp_ts FROM members WHERE passresethash='$hash'";
    $result = mysqli_query($db, $sql);
    $row = @mysqli_fetch_assoc($result);
    
    if(mysqli_num_rows($result))
    {    
        if(strtotime($row['passresetexp_ts']) < strtotime("now"))
        {
            $_SESSION['errors'][] = "This link has expired.";
        }
        else
        { 
            $password = substr(md5($hash . time()), 0, 8);
            $result = mysqli_query($db, "UPDATE members SET bl_pass=AES_ENCRYPT(concat('".$row['login']."','signlinkcms'), SHA1('$password')) WHERE login='" . $row['login']."'");
            
            //email body
            $body  = "Hello ". $row['name'] .",\n\n";
            $body .= "You have successfully reset your password for your SignLink account. \n\n";
            $body .= "Your username is: " . $row['login'] ."\n";
            $body .= "Your password is: " . $password ."\n\n";
            $body .= "You can now go to login to your account using this password.\n";
            $body .= "You can also change the password as soon as you are logged in.\n\n";
            $body .= "Thank you,\n";
            $body .= "SignLink Team";
			
            //send email
            require(INCLUDE_PATH . 'phpmailer/class.phpmailer.php');
            $mail = new PHPMailer();
            $mail->From     = 'noreply@signlinkstudio.ca';
            $mail->AddAddress($row['email']);
            $mail->Subject = "Signlink Password Reset Confirmation";
            $mail->Body    = $body;
	
            if(!$mail->Send()){
                $_SESSION['errors'][] = "Sending error.";
            } else {	
                $_SESSION['feedback'][] = "You have successfully reset your password. \nThe new password has been emailed to you.";
                unset($mail);
            }
        }
        
        $sql = "UPDATE members SET bl_pass=AES_ENCRYPT(concat('".$row['login']."','signlinkcms'), SHA1('$password')), 
                passresethash='0', passresetexp_ts='0' WHERE login='" . $row['login']."'";
        $result = mysqli_query($db, $sql);
                        
    }
    else{
        $_SESSION['errors'][] = "Link does not exist.";
    }
    
    require(INCLUDE_PATH.'header.inc.php');
    require(INCLUDE_PATH.'footer.inc.php');
?>

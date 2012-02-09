<?php

    define('INCLUDE_PATH', 'include/');
    require(INCLUDE_PATH.'vitals.inc.php');
    
    $hash = mysqli_real_escape_string($db, $_SERVER['QUERY_STRING']);
    
    $sql = "SELECT member_id, name, login, email, passresetexp_ts FROM members WHERE passresethash='$hash' AND status=0";
    $result = mysqli_query($db, $sql);
    $row = @mysqli_fetch_assoc($result);
    
    if(mysqli_num_rows($result) && strtotime($row['passresetexp_ts']) > strtotime("now") && $hash != "0")
    {    
            $sql = "UPDATE members SET status='1', passresethash='0', passresetexp_ts='0' WHERE member_id='".$row['member_id']."'";
            $result = mysqli_query($db, $sql);
            
            if (mysqli_affected_rows($db)<= 0){           
                $_SESSION['errors'][] = "Account confirmation failed. Please try again later.";
            } 
            else {
                $_SESSION['feedback'][] = 'Your account has been confirmed. You may now log in.';
                header('Location: login.php');
                exit;
            }
    }
    else{
        $_SESSION['errors'][] = "This link is invalid or has expired.";
    }
     
    require(INCLUDE_PATH.'header.inc.php');
    require(INCLUDE_PATH.'footer.inc.php');
?>

<?php

    define('INCLUDE_PATH', 'include/');
    require(INCLUDE_PATH.'vitals.inc.php');
    
    $hash = mysqli_real_escape_string($db, $_SERVER['QUERY_STRING']);
    
    $sql = "SELECT member_id, name, login, email, status, passresetexp_ts FROM members WHERE passresethash='$hash'";
    $result = mysqli_query($db, $sql);
    $row = @mysqli_fetch_assoc($result);
    
    // attempt to reset the password for the user
    if(isset($_POST['login'], $_POST['newpw1']))
    {       
            if ($_POST['login'] != $row['login']){
                $_SESSION['errors'][] = 'Incorrect username.';
            }
            if ($_POST['newpw1'] != $_POST['newpw2']){
                $_SESSION['errors'][] = 'Passwords do not match.';
            }
            if (!preg_match('/^\S{8,}$/u', $_POST['newpw1'])) { 
                $_SESSION['errors'][] = 'Passwords must be greater than 8 characters.';
            } 
            if ((preg_match('/[a-z]+/i', $_POST['newpw1']) + preg_match('/[0-9]+/i', $_POST['newpw1']) + preg_match('/[_\-\/+!@#%^$*&)(|.]+/i', $_POST['newpw1'])) < 2) {
                $_SESSION['errors'][] = 'Passwords must use a combination of letters, numbers and symbols.';
            }

            if (!isset($_SESSION['errors']))  
            {
                $login = mysqli_real_escape_string($db, $_POST['login']);
                $password = mysqli_real_escape_string($db, $_POST['newpw1']);
                
                $sql = "UPDATE members SET bl_pass=AES_ENCRYPT(concat('".$login."','signlinkcms'), SHA1('".$password."')), 
                               passresethash='0', passresetexp_ts='0' WHERE member_id='".$row['member_id']."'";
                $result = mysqli_query($db, $sql);

                if (mysqli_affected_rows($db)<= 0){           
                    $_SESSION['errors'][] = "Password Reset Failed.";
                } 
                else {
                    $_SESSION['feedback'][] = 'Password Reset Successfull. <br />You can now <a href="login.php">login</a> using your new password.';
                    require(INCLUDE_PATH.'header.inc.php');
                    require(INCLUDE_PATH.'footer.inc.php');
                    exit;
                }
            }
    }
    
    // if hash is found and it is not expired
    if(mysqli_num_rows($result) && strtotime($row['passresetexp_ts']) > strtotime("now") && $hash != "0" && intval($row['status']) != 0)
    {    
            require(INCLUDE_PATH.'header.inc.php');
        
        ?>
            <h2>Password Reset</h2>
            
            <br /> <div class="centeralign"> Hello, <?php echo $row['name']; ?>. Please enter your username and a new password to use for your account. </div>

            <form action="<?php echo "password_reset.php?".$hash; ?>" method="POST" id="loginform" name="form">

                <dl class="col-list" style="width:33%; margin-left:auto; margin-right:auto;">
                    <br />
                    <dt><label for="login">Login:</label></dt>
                    <dt><label for="login"><img src="images/user_med.png" alt="login" title="login" class="inlineVertMid" /></label> <input name="login" type="text" id="login" value="<?php echo $_SERVER['login']; ?>" style="width:55%;font-size:1.3em;font-family:arial,verdana,sans-serif" /><br /></dt>
                    <br />
                    <dt><label for="newpass1">New Password:</label></dt>
                    <dt><label for="newpass1"><img src="images/key3.png" alt="password" title="password" class="inlineVertMid" /></label> <input name="newpw1" type="password" id="newpass1" value="" style="width:55%; font-size:smaller;" /><br /></dt>
                    <br />
                    <dt><label for="newpass2">New Password Again:</label></dt>
                    <dt><label for="newpass2"><img src="images/key3.png" alt="password" title="password" class="inlineVertMid" /></label> <input name="newpw2" type="password" id="newpass2" value="" style="width:55%; font-size:smaller;" /><br /></dt>
                    <br />
                </dl>

                <div class="centeralign" style="width:33%;">
                  <button type="submit" id="submitbutton" name="submit" class="submitIconBtn" style="margin-right:20px;padding:5px 10px;"> Reset Password <img src="images/yescheck.png" alt="" class="inlineVertMid" /></button>
                </div>

            </form>
  
        <?php
    }
    else{
        $_SESSION['errors'][] = "This link is invalid or has expired.";
         require(INCLUDE_PATH.'header.inc.php');
    }
     
    require(INCLUDE_PATH.'footer.inc.php');
?>
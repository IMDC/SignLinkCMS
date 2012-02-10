<?php 
define('INCLUDE_PATH', '../include/');
require(INCLUDE_PATH.'vitals.inc.php');

admin_authenticate();

require(INCLUDE_PATH.'admin_header.inc.php');

$memberSearch = $_GET["name"]; 
$memberID = $_GET["id"];
$memberEMail = $_GET["mail"];

//if (isset($_POST['submit'])) {
if ($_POST) {

	//$chk_email = $addslashes($_POST['email']);
	//$chk_login = $addslashes($_POST['login']);
	$chk_email = mysqli_real_escape_string($db,$_POST['email']);
	$chk_login = mysqli_real_escape_string($db,$_POST['login']);

	//error check
	if (empty($_POST['name'])) {
		$_SESSION['errors'][] = 'Please enter a name.';
	} 
	if (empty($_POST['email'])) {
		$_SESSION['errors'][] = 'Please enter an email.';
	} else if (!eregi("^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,6}$", $_POST['email'])) {
		$_SESSION['errors'][] = 'Please enter a valid email.';
	} 
	
	$result = mysqli_query($db, "SELECT * FROM members WHERE email='$chk_email'");
	if (mysqli_num_rows($result) != 0) {
		$_SESSION['errors'][] = 'Email address already in use. Try the Password Reminder.';
	}

	if (empty($_POST['login'])) {
		$_SESSION['errors'][] = 'Please enter a login name.';
	} else {
		// check for special characters 
		if (!(eregi("^[a-zA-Z0-9_.-]([a-zA-Z0-9_.-])*$", $_POST['login']))) {
			$_SESSION['errors'][] = 'Login name taken. Please try another.';
		} else {
			$result = mysqli_query($db, "SELECT * FROM members WHERE login='$chk_login'");
			if (mysqli_num_rows($result) != 0) {
				$_SESSION['errors'][] = 'Login name already exists.';
			} /*else {
				$result = mysqli_query($db, "SELECT * FROM admins WHERE login='$chk_login'");
				if (mysqli_num_rows($result) != 0) {
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
		//$name       = $addslashes(trim($_POST['name']));
		//$email      = $addslashes(trim($_POST['email']));
		//$login      = $addslashes(trim($_POST['login']));
		//$password   = $addslashes(trim($_POST['password']));
		$name       = mysqli_real_escape_string($db,trim($_POST['name']));
		$email      = mysqli_real_escape_string($db,trim($_POST['email']));
		$login      = mysqli_real_escape_string($db,trim($_POST['login']));
		$password   = mysqli_real_escape_string($db,trim($_POST['password']));

		/* MD5 encryption for the password for added security */
		//$password = md5($password);

                $sql = "INSERT INTO members (member_id, login, name, email, bl_pass, created_ts) VALUES (NULL, '$login', '$name', '$email', AES_ENCRYPT(concat('$login','signlinkcms'), SHA1('$password')), DEFAULT)";
		$result = mysqli_query($db, $sql);

		if (!$result) {
			$_SESSION['errors'][] = 'Database error - user not added.';
			exit;
		}

		//send email to registrant

		$_SESSION['feedback'][] = 'New member created successfully.';
	}
}
?>

<link rel="stylesheet" type="text/css" href="../css/admin_member.css" />

<script type="text/javascript">
   $(function() {
         <?php if (!isset($_SESSION['errors'])) {
         echo '$("#memberpane").toggle();';

         }?>

         $("#memberclick").click(function() {
           $("#memberpane").toggle(); 
         });
		 
		$(".registeredmembers a").click(function() {
             $("#searchcriteria").toggle(); 
        });
   });
   
   $(document).ready(function(){
      $("#searchcriteria").hide(); 
    });
</script>

<H2>members</h2>

<h3><a href="#" id="memberclick">Create new member</a></h3>
<div id="memberpane">
   <form action="<?php echo htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES); ?>" method="post" name="registerform" class="expose">
      <dl class="col-list">
         <dt><label for="name"><img src="../images/user_small.png" class="inlineVertMid" />Name:</label></dt> 
            <dd><input name="name" type="text" id="name" value="<?php echo $_POST['name']; ?>" />&nbsp;&nbsp;<img src="../images/example3.png" />"Carmen Smith"</dd><br />
         <dt><label for="email"><img src="../images/email_small.png" class="inlineVertMid" />Email:</label></dt> 
            <dd><input name="email" type="text" id="email" value="<?php echo $_POST['email']; ?>" />&nbsp;&nbsp;<img src="../images/example3.png" />"carmen_smith@example.com"<br /><br /></dd>
            <p><em>User information - To be used on this website</em></p>
         <dt><label for="login"><img src="../images/user_small.png" class="inlineVertMid" />Login:</label></dt> 
            <dd><input name="login" type="text" id="login" value="<?php echo $_POST['login']; ?>" />&nbsp;&nbsp;<img src="../images/example3.png" />"RockstarCarmen"</dd><br />
         <dt><label for="pswd"><img src="../images/key_small.png" class="inlineVertMid" />Password:</label></dt> 
            <dd><input name="password" type="password" id="pswd" value="<?php echo $_POST['password']; ?>" />&nbsp;&nbsp;<img src="../images/example3.png" />"9Xxitld4zG"&nbsp;- <em>8+ numbers and letters</em></dd><br />
         <dt><label for="pswd"><img src="../images/key_small.png" class="inlineVertMid" />Password Again:</label></dt>
            <dd><input name="password2" type="password" id="pswd2" value="<?php echo $_POST['password2']; ?>" />&nbsp;&nbsp;<img src="../images/example3.png" />"9Xxitld4zG"&nbsp;- <em>8+ numbers and letters</em></dd>
      </dl>
      <!-- div style="text-align:center"><label><input type="checkbox" name="autologin" value="1" /> keep me logged-in</label --><br /><br />
      <input type="image" name="submit" alt="Submit" src="../images/registerbtn.png" class="inlineVertMid" style="margin-bottom: 2px;" /> | 
      &nbsp;&nbsp;&nbsp;<button type="button" name="cancelbtn" value="cancel" class="cancelIconBtn" onClick="javavscript:history.back(1);">Cancel <img src="../images/cancelx.png" alt="" class="inlineVertMid" /></button>
      <!-- | <button type="button" id="testbutton" name="testbtn" value="Monkeys" class="submitIconBtn"> Submit <img src="../images/yescheck.png" alt="" class="inlineVertMid" /></button></div> -->
   </form>
</div>
<!--<h3><a href="#">Registered Members</a></h3>-->
<div>

<!-- new joe-->

<div class="registeredmembers">
	<span>The following members have registered:</span>
	<a href="#">Search</a>
</div>

<div id="searchBar">
    <div id="searchcriteria">
		<span>Search: </span> 
		<form action="#" >
			 ID:<input type="text" name="id" />
			 Login:<input type="text" name="name" id="auto" />
			 Email:<input type="text" name="mail" id ="auto-email"/>
			 Verified Members:<input type="checkbox" name="regmem" value="Yes" /> 
			 <input TYPE="image" SRC="../images/search_button_green_32.png" BORDER="0" ALT="Submit Form" placeholder="search" style="position:relative;top:10px">
		</form>
	</div>
</div>

<!--   -->

<?php
//get members

$sqlMI = "";
$sqlME = "";
$sqlMS = "";
$sqlreg = "";

if(!empty($memberID))
{
   $sqlMI = " AND member_id='$memberID'";
}
if (!empty($memberEMail)) 
{
   $sqlME = " AND email='$memberEMail'";
}
if( !empty($memberSearch))
{
    $sqlMS = " AND login='$memberSearch'";
}

if(isset($_GET['regmem']) &&
   $_GET['regmem'] == 'Yes')
{
   $sqlreg = " AND status = '1'";
}


	$sql = "SELECT * FROM members WHERE login!='admin'" . $sqlMS . $sqlME . $sqlMI . $sqlreg;

   
  

   
$result = mysqli_query($db, $sql);
$r = 1;
if (mysqli_num_rows($result)) { ?>
	<table class="manage">
	<tr>
		<th>ID</th>
		<th>Login</th>
		<th>Name</th>
		<th>Email</th>
		<th style="text-align:center;">Manage</th>
	</tr>
	<?php
	while ($row = mysqli_fetch_assoc($result)) {
		//print forum row info
		echo '<tr class="row'.$r.'">';
		echo '<td>'.$row['member_id'].'</td>'; 
		echo '<td><a href="member_view?name=' . $row['login'] . '&id=' . $row['member_id'] . '">' . $row['login'] . '</a></td>';
		echo '<td>'.$row['name'].'</td>';
		echo '<td>'.$row['email'].'</td>';		
		echo '<td style="text-align:center;"><a href="member_edit.php?m='.$row['member_id'].'">Edit</a>';
		echo ' | <a href="member_delete.php?m='.$row['member_id'].'" onclick="return confirm(\'Are you sure you want to delete this member?\')">Delete</a></td>';
		echo '</tr>';
		if ($r == 1) {
			$r = 2;
		} else {
			$r = 1;
		}
	}
	echo '</table>';
} else {
	echo "None found.";
}
?>
</div>

<?php require(INCLUDE_PATH.'footer.inc.php'); ?>
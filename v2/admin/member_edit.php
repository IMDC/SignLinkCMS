<?php 
define('INCLUDE_PATH', '../include/');
require(INCLUDE_PATH.'vitals.inc.php');
admin_authenticate();

$member_id = intval($_REQUEST['m']);

$sql = "SELECT * FROM members WHERE member_id=".$member_id;
$result = mysqli_query($db, $sql);

if (!$row = mysqli_fetch_assoc($result)) {
	$_SESSION['errors'][] = "Member not found";
	require(INCLUDE_PATH.'admin_header.inc.php');
	require(INCLUDE_PATH.'footer.inc.php');
	exit;
}

if (isset($_POST['cancel'])) {
	header('Location: member_manage.php');
	exit;
} else if (isset($_POST['submit']) || $_GET['processed']) {
	if ($_POST['password'] != $row['password']) {
		if ($_POST['password'] == '') { 
			$_SESSION['errors'][] = 'Password cannot be empty.';
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
			//prepare to insert into db
			$new_password = $addslashes(trim($_POST['password']));
			$sql = "UPDATE members SET password='$new_password' WHERE member_id=".intval($_POST['m']);
			$result = mysqli_query($db, $sql);
		}
	}
	if ($_POST['email'] != $row['email']) {

		if (empty($_POST['email'])) {
			$_SESSION['errors'][] = 'Email cannot be empty.';
		} else if (!eregi("^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,6}$", $_POST['email'])) {
			$_SESSION['errors'][] = 'Please enter a valid email.';
		} 
		
		$result = @mysqli_query($db, "SELECT * FROM members WHERE email=".addslashes($_POST['email']));
		if (@mysqli_num_rows($result) != 0) {
			$_SESSION['errors'][] = 'Email address already in use. Try the Password Reminder.';
		}
		if (!isset($_SESSION['errors'])) {
			//prepare to insert into db
			$new_email = $addslashes(trim($_POST['email']));
			$sql = "UPDATE members SET email='$new_email' WHERE member_id=".intval($_POST['m']);
			$result = mysqli_query($db, $sql);
		}		
	}	
	if (!isset($_SESSION['errors'])) {
		$_SESSION['feedback'][] = "Member details updated.";
		header('Location:member_manage.php');
		exit;
	}
}

require(INCLUDE_PATH.'admin_header.inc.php'); ?>

<h2>Member Edit</h2>


<form action="<?php echo htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES); ?>?processed=1" method="post" name="form" enctype="multipart/form-data">
	<input type="hidden" name="m" value="<?php echo $member_id; ?>" />
	<dl>
		<dt>Login
		<dd><?php echo $row['login']; ?>
	
		<dt>Password
		<dd><input type="password" name="password" value="<?php echo $row['password']; ?>" />
	
		<dt>Password Again
		<dd><input type="password" name="password2" value="<?php echo $row['password']; ?>" />	
	
		<dt>Name
		<dd><?php echo $row['name']; ?>
		
		<dt>Email
		<dd><input type="text" name="email" value="<?php echo $row['email']; ?>" />
		
	</dl>
	<div class="row" style="text-align:right;padding-top:5px;">
		<input type="submit" name="submit" value="Submit"> | <input type="submit" name="cancel" value="Cancel" /> 
	</div>	
</form>

<?php require(INCLUDE_PATH.'footer.inc.php'); ?>

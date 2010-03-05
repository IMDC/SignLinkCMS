<?php 

define('INCLUDE_PATH', '../include/');
require(INCLUDE_PATH.'vitals.inc.php');
admin_authenticate();

$sql = "SELECT * FROM settings WHERE 1";
$result = mysql_query($sql, $db);

while($row = mysql_fetch_assoc($result)) {
	$settings[$row['name']] = $row['value'];
}

$sql = "SELECT password FROM members WHERE login='admin'";
$result = mysql_query($sql, $db);
$row = mysql_fetch_assoc($result);
$password = $stripslashes($row['password']);

if (isset($_POST['cancel'])) {
	header('Location: settings.php');
	exit;
} else if (isset($_POST['submit']) || $_GET['processed']) {
	//check each
		
	if ($_POST['contact'] != $settings['contact']) {

		if (empty($_POST['contact'])) {
			$_SESSION['errors'][] = 'Email cannot be empty.';
		} else if (!eregi("^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,6}$", $_POST['contact'])) {
			$_SESSION['errors'][] = 'Please enter a valid email.';
		} 
		
		$result = @mysql_query("SELECT * FROM members WHERE email=".addslashes($_POST['email']), $db);
		if (@mysql_num_rows($result) != 0) {
			$_SESSION['errors'][] = 'Email address already in use.';
		}
		if (!isset($_SESSION['errors'])) {
			//prepare to insert into db
			$new_email = $addslashes(trim($_POST['contact']));
			$sql = "UPDATE settings SET value='$new_email' WHERE name='contact'";
			$result = mysql_query($sql, $db);
			$_SESSION['feedback'][] = "Contact email changed.";
		}		
	}	
	if ($_POST['password'] != $password) {

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
			$sql = "UPDATE members SET password='$new_password' WHERE login='admin'";
			$result = mysql_query($sql, $db);
			$_SESSION['feedback'][] = 'Passwords changed.';
		}		
	}		
	if ($_POST['site_name'] != $settings['site_name']) {
		if (empty($_POST['site_name'])) {
			$_SESSION['errors'][] = 'Site name cannot be empty.';
		}
		if (!isset($_SESSION['errors'])) {
			$new_site_name = $addslashes(trim($_POST['site_name']));
			$sql = "UPDATE settings SET value='$new_site_name' WHERE name='site_name'";
			$result = mysql_query($sql, $db);
			$_SESSION['feedback'][] = 'Site name changed.';
		}
	}
	if ($_POST['max_upload_size'] != $settings['max_upload_size']) {
		$max = intval($_POST['max_upload_size']);
		if (empty($max) || !is_numeric($_POST['max_upload_size'])) {
			$_SESSION['errors'][] = 'Upload size cannot be empty.';
		}
		if (!isset($_SESSION['errors'])) {
			$sql = "UPDATE settings SET value='$max' WHERE name='max_upload_size'";
			$result = mysql_query($sql, $db);
			$_SESSION['feedback'][] = 'Max upload size changed.';
		}
	}
	
	header('Location:settings.php');
	exit;
}

require(INCLUDE_PATH.'admin_header.inc.php'); ?>

<h2>Settings</h2>
<div class="file-info">
<script type="text/javascript" src="../jscripts/maxUploadMenu.js">
</script>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>?processed=1" method="post" name="form" enctype="multipart/form-data">

	<dl class="admin">
	
		<dt>Contact Email
		<dd><input type="text" name="contact" value="<?php echo $settings['contact']; ?>" />
	
		<dt>Admin Password
		<dd><input type="password" name="password" value="<?php echo $password; ?>" />
	
		<dt>Admin Password Again
		<dd><input type="password" name="password2" value="" />	
	
		<dt>Site Name
		<dd><input type="text" name="site_name" value="<?php echo $settings['site_name']; ?>" />
		
		<!-- dt>Banner Image
		<dd><input type="file" name="banner" value="<?php echo $settings['banner']; ?>" / -->
		
      <dt>Maximum file size (for uploads)
      <!--<dd><input type="text" name="max_upload_size" value="<?php echo $settings['max_upload_size']; ?>" /> (Default: 5Mb = 5242880b)-->
		<dd><select name="max_upload_size" size="7" multiple="no" onChange="menu_change(this)" >
			<option value="<?php echo $settings['max_upload_size']; ?>">Current Value: <?php echo $settings['max_upload_size']; ?></option>
			<option value="2097152">2 Mb</option>
         <option value="5242880">5 Mb</option>
			<option value="10485760">10 Mb</option>
			<option value="15728640">15 Mb</option>
			<option value="20971520">20 Mb</option>
			<option id="custom_name" value="0">Custom</option>
		</select>
		<div id="customSizeDiv" style="visibility: hidden;float: left;text-align: right;padding-top: 5px;">
			Enter the size in bits: <input id="custom_size" type="text" value="" onBlur="menu_setCustom(this)" />
		</div>
	</dl>
	<div class="row" style="text-align:right;padding-top:5px;">
		<input type="submit" name="submit" value="Submit"> | <input type="submit" name="cancel" value="Cancel" /> 
	</div>	
</form>
</div>

<?php require(INCLUDE_PATH.'footer.inc.php'); ?>

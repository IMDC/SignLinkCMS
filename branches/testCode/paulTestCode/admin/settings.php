<?php 

define('INCLUDE_PATH', '../include/');
require(INCLUDE_PATH.'vitals.inc.php');
admin_authenticate();

$sql = "SELECT * FROM settings WHERE 1";
$result = mysqli_query($db, $sql);

while($row = mysqli_fetch_assoc($result)) {
	$settings[$row['name']] = $row['value'];
}

/*
$sql = "SELECT password FROM members WHERE login='admin'";
$result = mysqli_query($db, $sql);
$row = mysqli_fetch_assoc($result);
$password = $stripslashes($row['password']);
*/

// *********** site status stuff ***********
$errspanintro = '<span class="status-error"><img src="../images/cancelx.png" class="inlineVertMid" alt="" /> ';
$sucspanintro = '<span class="status-success"><img src="../images/yescheck.png" class="inlineVertMid" alt="" /> ';
$spanout = '</span>';

// ffmpeg access

if (ffmpeg_access_enabled())
   $ffmpegstatus = $sucspanintro . "FFmpeg with x264 support appears to be working" . $spanout;
else
   $ffmpegstatus = $errspanintro . "FFmpeg error, check path in config file and executable permissions" . $spanout;

// GD image library present

if (gd_library_present())
   $gdsupport = $sucspanintro . 'GD image libary loaded successfully' . $spanout;
else
   $gdsupport = $errspanintro . 'GD image library not loaded, check that GD is installed with PHP' . $spanout;

// directory write access

if (directory_write_permission_enabled())
   $dirwriteaccess = $sucspanintro . "Directory write access by PHP username permitted" . $spanout;
else
   $dirwriteaccess = $errspanintro . "Uploads directory write access not permitted, check Uploads directory permissions" . $spanout;

// play button overlay image

if (playbutton_overlay_config_successful())
   $playoverlayimageaccess = $sucspanintro . 'Overlay image found and image libraries loaded successfully' . $spanout;
else
   $playoverlayimageaccess = $errspanintro . 'Overlay image not found or image libraries not loaded' . $spanout;

// ********** end site status stuff *********


if (isset($_POST['cancel'])) {
	header('Location: settings.php');
	exit;
}
else if (isset($_POST['submit']) || $_GET['processed']) {
	//check each
		
	if ($_POST['contact'] != $settings['contact']) {

		if (empty($_POST['contact'])) {
			$_SESSION['errors'][] = 'Email cannot be empty.';
		}
      else if (!eregi("^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,6}$", $_POST['contact'])) {
			$_SESSION['errors'][] = 'Please enter a valid email.';
		}
		
		//$result = @mysqli_query($db, "SELECT * FROM members WHERE email=".addslashes($_POST['email']));
      $result = @mysqli_query($db, "SELECT * FROM members WHERE email=".mysqli_real_escape_string($db,$_POST['email']));
		if (@mysqli_num_rows($result) != 0) {
			$_SESSION['errors'][] = 'Email address already in use.';
		}
		if (!isset($_SESSION['errors'])) {
			//prepare to insert into db
//			$new_email = $addslashes(trim($_POST['contact']));
			$new_email = mysqli_real_escape_string($db,trim($_POST['contact']));
			$sql = "UPDATE settings SET value='$new_email' WHERE name='contact'";
			$result = mysqli_query($db, $sql);
			$_SESSION['feedback'][] = "Contact email changed.";
		}		
	}	
	if ($_POST['password'] != $password) {

		if ($_POST['password'] == '') { 
			$_SESSION['errors'][] = 'Password cannot be empty.';
		} 
      else {
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
//			$new_password = $addslashes(trim($_POST['password']));
			$new_password = mysqli_real_escape_string($db,trim($_POST['password']));
			
      $sql = "UPDATE members SET bl_pass=AES_ENCRYPT('adminsignlinkcms', SHA1('$new_password')) WHERE login='admin' AND member_id=1";
			$result = mysqli_query($db, $sql);
			$_SESSION['feedback'][] = 'Passwords changed.';
		}		
	}		

	if ($_POST['site_name'] != $settings['site_name']) {
		if (empty($_POST['site_name'])) {
			$_SESSION['errors'][] = 'Site name cannot be empty.';
		}
		if (!isset($_SESSION['errors'])) {
			//$new_site_name = $addslashes(trim($_POST['site_name']));
			$new_site_name = mysqli_real_escape_string($db,trim($_POST['site_name']));
			$sql = "UPDATE settings SET value='$new_site_name' WHERE name='site_name'";
			$result = mysqli_query($db, $sql);
			$_SESSION['feedback'][] = 'Site name changed.';
		}
	}

	if ($_POST['max_upload_size'] != $settings['max_upload_size']) {
//      if (!preg_match('/[0-9]+/i', $_POST['max_upload_size'])) {
//         $_SESSION['errors'][] = 'Maximum upload size must be a positive number, please check your input';
//         header('Location: settings.php');
//      }
		$max = intval($_POST['max_upload_size']);
		if (empty($max) || !is_numeric($_POST['max_upload_size']) || ($max < 0) ) {
			$_SESSION['errors'][] = 'Upload size must be a positive number, please check your input.';
		}
		if (!isset($_SESSION['errors'])) {
			$sql = "UPDATE settings SET value='$max' WHERE name='max_upload_size'";
			$result = mysqli_query($db, $sql);
			$_SESSION['feedback'][] = 'Max upload size changed.';
		}
	}

   /* processing for members only selection */
	if ($_POST['mem_only'] != $settings['reg_user_only']) {
      $member_only_val = intval($_POST['mem_only']);
      if (!is_numeric($_POST['mem_only'])) {
         $_SESSION['errors'][] = 'Members only must be selected.';
      }
      if (!isset($_SESSION['errors'])) {
         $sql = "UPDATE settings SET value='$member_only_val' WHERE name='reg_user_only'";
         $result = mysqli_query($db, $sql);
         $_SESSION['feedback'][] = 'Members only selection changed.';
      }
   }

   /* processing for disable member registration selection */
	if ($_POST['disable_reg'] != $settings['registration_closed']) {
      $disable_reg_val = intval($_POST['disable_reg']);
      if (!is_numeric($_POST['disable_reg'])) {
         $_SESSION['errors'][] = 'Registration disabled must be selected.';
      }
      if (!isset($_SESSION['errors'])) {
         $sql = "UPDATE settings SET value='$disable_reg_val' WHERE name='registration_closed'";
         $result = mysqli_query($db, $sql);
         $_SESSION['feedback'][] = 'Registration disabled selection changed.';
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
<form action="<?php echo htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES); ?>?processed=1" method="post" name="form" enctype="multipart/form-data">

	<dl class="admin">

      <h3 style="padding:20px;">Admin password is required to make any changes on this page!</h3>
		<dt>Contact Email</dt>
		<dd><input type="text" name="contact" value="<?php echo $settings['contact']; ?>" /></dd>
	
      <dt>Admin Password</dt>
      <dd><input type="password" name="origpass" value="" /></dd>
      
		<dt>New Admin Password</dt>
		<dd><input type="password" name="password" value="" /></dd>
	
		<dt>New Admin Password Again</dt>
		<dd><input type="password" name="password2" value="" /></dd>	
	
		<dt>Site Name</dt>
		<dd><input type="text" name="site_name" value="<?php echo $settings['site_name']; ?>" /></dd>
		
		<!-- dt>Banner Image</dt>
		<dd><input type="file" name="banner" value="<?php echo $settings['banner']; ?>" / </dd>-->
		
      <dt>Maximum file size (for uploads)</dt>
      <!--<dd><input type="text" name="max_upload_size" value="<?php echo $settings['max_upload_size']; ?>" /> (Default: 5Mb = 5242880b)-->
		<dd>
         <div style="width:400px;">
            <div style="padding-top:40px;display:inline;float:left;width:200px;">
               <span style="font-size:1.4em;"><?php echo intval(floor($settings['max_upload_size']/1024/1024)) . 'Mb'; ?>/<?php echo get_maximum_php_installation_file_upload_size_mb() . 'Mb max*'; ?></span>
            </div>
            <span style="margin-left:15px;">Other sizes:</span>
            <select style="width:100px;" class="prefselect" name="max_upload_size" size="6" multiple="no" onChange="menu_change(this)" >
   <!--            <option selected value="<?php //echo $settings['max_upload_size']; ?>">Current Value: <?php //echo intval(floor($settings['max_upload_size']/1024/1024)) . 'Mb'; ?></option>-->
               <option value="2097152">2 Mb</option>
               <option value="5242880">5 Mb</option>
               <option value="10485760">10 Mb</option>
               <option value="15728640">15 Mb</option>
               <option value="20971520">20 Mb</option>
               <option id="custom_name" value="0">Custom</option>
            </select>
<!--            <div id="customSizeDiv" style="visibility:hidden;float:left;text-align:right;padding-top:5px;">-->
            <div id="customSizeDiv" style="visibility:hidden;position:relative;left:330px;top:-80px;">
               Enter the size in megabytes:<br /><input id="custom_size" type="text" value="" onBlur="menu_setCustom(this)" />
            </div>
            <span style="font-size:0.8em;position:relative;top:-30px;">*Note: maximum filesize determined by your PHP installation</span>
         </div>
      </dd>
      <div style="clear:both;"></div>

      <dt>Site restricted to members only</dt>
      <dd><input type="radio" name="mem_only" value="1" <?php if($settings['reg_user_only']==1)echo 'checked'?> /> Yes
         <br /> <input type="radio" name="mem_only" value="0" <?php if($settings['reg_user_only']==0)echo 'checked'?> /> No </dd>

      <dt>Disable member registration</dt>
      <dd><input type="radio" name="disable_reg" value="1" <?php if($settings['registration_closed']==1)echo 'checked'?> /> Yes
         <br /> <input type="radio" name="disable_reg" value="0" <?php if($settings['registration_closed']==0)echo 'checked'?> /> No </dd>
      <dt>Site status</dt>
      <ul>
         <li>FFmpeg access: <?php echo $ffmpegstatus ?> </li>
         <li>GD Image library present: <?php echo $gdsupport ?> </li>
         <li>Directory write access: <?php echo $dirwriteaccess ?> </li>
         <li>Play button overlay image: <?php echo $playoverlayimageaccess ?> </li>
      </ul>
      <dt>Video Thumbnails</dt>
      <dd>
         <p>There are approximately <?php echo count(searchUploadedFiles(array('mp4'))); ?> video thumbnails to re-generate<br />
            Generating new ones for every video could take some time<br />
            To re-generate thumbnails for specific videos/posts/comments/vlogs, please use the section specific menu choice at the top of the page<br />
            <a href="regenThumbs.php">Regenerate ALL thumbnails</a>
      </dd>
    
	</dl>
	<div class="submitrow row">
		<input type="submit" class="submitBtn" name="submit" value="Submit"> | <input type="submit" class="cancelBtn" name="cancel" value="Cancel" /> 
	</div>	
</form>
</div>

<?php require(INCLUDE_PATH.'footer.inc.php'); ?>

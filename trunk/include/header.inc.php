<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" />
<html>
<body>
<head>
	<title><?php echo SITE_NAME; ?></title>
	<link rel="stylesheet" href="asl.css" type="text/css" media="screen" />

<!--[if IE]>
	<link href="iehacks.css" rel="stylesheet" />
<![endif]-->

	<script type="text/javascript" src="jscripts/jquery-1.2.3.pack.js"></script>
    <!-- flowplayer is a free flash container used to hold and playback the .mp4 movies
    using it until a custom flash object that does the same thing can be built for me -->
   <script type="text/javascript" src="jscripts/flowplayer-3.1.1.min.js"></script>
   <!-- This file contains some jquery kung-fu to enable things like video/text/image entry
   highlighting on mouseover and navigation button animation on mouseover -->
   <script type="text/javascript" src="jscripts/tweaks.js"></script>          
 
</head>
			
<div id="container">
   <div id="topbackground"></div>
	<div id="login-area">
		<?php if(isset($_SESSION['member_id']) && $_SESSION['member_id'] && $_SESSION['valid_user']) { ?>
			<div style="float:left;width:60px; text-align:center;">
				<?php get_avatar($_SESSION['member_id']) ?><br />
				<?php echo $_SESSION['login']; ?>
			</div>
			<div style="float:right; padding-right:5px;">
				<a href="preferences.php"><img src="images/cog.png" alt="preferences" title="preferences" /></a>&nbsp;
				<a href="logout.php"><img src="images/door_out.png" alt="log out" title="log out" /></a>
			</div>

		<?php
		} else { ?>
		<form action="login.php" method="post" name="form">
			<!-- img src="images/door_in.png" alt="log out" title="log out" / -->

			<input type="hidden" name="f" value="<?php echo intval($_REQUEST['f']); ?>" />
			<input type="hidden" name="p" value="<?php echo intval($_REQUEST['p']); ?>" />
			
			<label for="login"><img src="images/user.png" alt="login" title="login" /></label> <input name="login" type="text" id="login" value="<?php echo $_SERVER['login']; ?>" style="width:55%;font-size:smaller;" /><br />
			<label for="pswd"><img src="images/key.png" alt="password" title="password" /></label> <input name="password" type="password" id="pswd" value="" style="width:55%; font-size:smaller;" /> &nbsp;<input type="submit" name="submit" value="Login" class="button" style="font-size:smaller;margin-top:5px;" />

			<br style="clear:both" /><p style="text-align:center"><a href="register.php">Register</a> | <a href="password_reminder.php">Password Reminder</a></p>
		</form>
		<?php 							
		} 
		?>
	</div>

	<div style="margin-top:5px;"><img src="images/signlink_banner.png" alt="<?php echo SITE_NAME; ?>" title="<?php echo SITE_NAME; ?>" /></div>

	<div id="menu" style="clear:both">
	<?php		
		$current_page = explode('/', $_SERVER['PHP_SELF']); 
		$current_page = $current_page[count($current_page) - 1];
		
	?>
		<ul>					
			<li id="menu-home"><a href="index.php"><img src="images/house.png" alt="home" title="home" <?php if($current_page == 'index.php') { echo 'class="menu-current"'; } ?> /></a></li>
			
			<li id="menu-content"><a href="content.php"><img src="images/picture.png" alt="pages" title="pages" <?php if(in_array($current_page, $content_pages)) { echo 'class="menu-current"'; } ?> /></a></li>
			
			<li id="menu-forum"><a href="forums.php"><img src="images/group.png" alt="forums" title="forums" <?php if(in_array($current_page, $forum_pages)) { echo 'class="menu-current"'; } ?> /></a></li>	
			
			<li id="menu-vlog"><a href="vlogs.php"><img src="images/cup_edit.png" alt="vlogs" title="vlogs" <?php if(in_array($current_page, $vlog_pages)) { echo 'class="menu-current"'; } ?> /></a></li>	
			
			<li id="menu-help"><a href="help.php"><img src="images/help.png" alt="help" title="help" <?php if($current_page == 'help.php') { echo 'class="menu-current"';} ?> /></a></li>	
		</ul>
	</div>
<div id="content">
	<?php 
	if (in_array($current_page, $content_pages)) {
		echo '<h2><a href="content.php"><img src="images/picture.png" alt="content pages" title="content pages" style="padding:3px;" /></a></h2>';
	} else if (in_array($current_page, $forum_pages)) {
		echo '<h2><a href="forums.php"><img src="images/group.png" alt="forums" title="forums" style="padding:3px;" /></a></h2>';
	} else if (in_array($current_page, $vlog_pages)) {
		echo '<h2><a href="vlogs.php"><img src="images/cup_edit.png" alt="vlogs" title="vlogs" style="padding:3px;" /></a></h2>';
	}	
	
	if (isset($_SESSION['errors'])) {
		echo '<div class="error"><strong>Error:</strong><br />';
		foreach ($_SESSION['errors'] as $errmsg) {
			echo $errmsg.'<br />';	
		}
		echo '</div>';
		unset($_SESSION['errors']);
	}
	if (isset($_SESSION['feedback'])) {
		echo '<div class="feedback"><strong>Feedback:</strong><br />';
		foreach ($_SESSION['feedback'] as $fbmsg) {
			echo $fbmsg.'<br />';	
		}
		echo '</div>';
		unset($_SESSION['feedback']);
	}
	if (isset($_SESSION['notices'])) {
		echo '<div class="notice"><strong>Notice:</strong><br />';
		foreach ($_SESSION['notices'] as $nmsg) {
			echo $nmsg.'<br />';	
		}
		echo '</div>';
		unset($_SESSION['notices']);
	}	
?>
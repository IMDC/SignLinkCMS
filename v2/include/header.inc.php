<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" />
<html>
<head>
	<title><?php echo SITE_NAME; ?></title>
	<link rel="stylesheet" href="asl.css" type="text/css" media="screen" />

<!--[if IE]>
	<link href="iehacks.css" rel="stylesheet" />
<![endif]-->

	<script type="text/javascript" src="jscripts/jquery/js/jquery-1.3.2.min.js"></script>
   <script type="text/javascript" src="jscripts/flowplayer-3.1.4.min.js"></script>
   <script src="jscripts/flowplayer_external_config.js"></script>
   <script type="text/javascript" src="jscripts/tweaks.js"></script>
   <script src="http://cdn.jquerytools.org/1.1.2/jquery.tools.min.js"></script>

</head>
<body>
		
<div id="container">

	<div id="topbackground"></div>
	<div id="login-area">
		<?php if(isset($_SESSION['member_id']) && $_SESSION['member_id'] && $_SESSION['valid_user']) { ?>
			<div style="float:left;width:60px; text-align:center;">
				<?php get_avatar($_SESSION['member_id']) ?><br />
				<?php echo $_SESSION['login']; ?>
			</div>
			<div style="float:right; padding-right:5px;">
				<a href="preferences.php"><img src="images/wrenchsmall.png" alt="preferences" title="preferences" /></a>&nbsp;
				<a href="logout.php"><img src="images/logout_24.png" alt="log out" title="log out" /></a>
			</div>

		<?php
		} else { ?>
		<form action="login.php" method="post" name="form">
			<!-- img src="images/door_in.png" alt="log out" title="log out" / -->

			<input type="hidden" name="f" value="<?php echo intval($_REQUEST['f']); ?>" />
			<input type="hidden" name="p" value="<?php echo intval($_REQUEST['p']); ?>" />
			
			<label for="login"><img src="images/user_small.png" alt="login" title="login" class="inlineVertMid" /></label> <input name="login" type="text" id="login" value="<?php echo $_SERVER['login']; ?>" style="width:55%;font-size:smaller;" /><br />
			<label for="pswd"><img src="images/key_small.png" alt="password" title="password" class="inlineVertMid" /></label> <input name="password" type="password" id="pswd" value="" style="width:55%; font-size:smaller;" /><br />
         <img src="images/login_24.png" name="submitImg" alt="" class="inlineVertMid" style="width:24px;margin-left:auto;margin-right:auto;margin-top:5px;" />
         <input type="submit" name="submit" id="submitLogin" value="Login" class="button" />

			<br style="clear:both" /><hr style="margin-top: 8px; width: 80%; height:2px; color: #0066FF;" /><p style="text-align:center"><a href="register.php"><img src="images/register_add.png" class="inlineVertMid" /></a><a href="register.php">Register</a> | <a href="password_reminder.php"><img src="images/mail_key_small.png" class="inlineVertMid" /></a><a href="password_reminder.php">Password Reminder</a></p>
		</form>
		<?php 							
		} 
		?>
	</div>

	<div style="margin-top:10px;"><img src="images/signlink_banner5.png" style="margin-top: 25px;margin-left:15px;" alt="<?php echo SITE_NAME; ?>" title="<?php echo SITE_NAME; ?>" /></div>

	<div id="menu" style="clear:both">
	<?php		
		$current_page = explode('/', $_SERVER['PHP_SELF']); 
		$current_page = $current_page[count($current_page) - 1];
		
	?>
		<ul>					
			<li id="menu-home"><a href="index.php"><img src="images/house_shadow.png" class="homenavicon" alt="home" title="home" <?php if($current_page == 'index.php') { echo 'class="menu-current"'; } ?> /></a></li>
			
			<li id="menu-content"><a href="content.php"><img src="images/content.png" class="pagesnavicon" alt="pages" title="pages" <?php if(in_array($current_page, $content_pages)) { echo 'class="menu-current"'; } ?> /></a></li>
			
			<li id="menu-forum"><a href="forums.php"><img src="images/group.png" class="forumnavicon" alt="forums" title="forums" <?php if(in_array($current_page, $forum_pages)) { echo 'class="menu-current"'; } ?> /></a></li>	
			
			<li id="menu-vlog"><a href="vlogs.php"><img src="images/vlog.png" class="vlognavicon" alt="vlogs" title="vlogs" <?php if(in_array($current_page, $vlog_pages)) { echo 'class="menu-current"'; } ?> /></a></li>
			
			<li id="menu-help"><a href="help.php"><img src="images/help3.png" class="helpnavicon" alt="help" title="help" <?php if($current_page == 'help.php') { echo 'class="menu-current"';} ?> /></a></li>	
		</ul>
	</div>
<div id="content">
	<?php 
	if (in_array($current_page, $content_pages)) {
		echo '<h2><a href="content.php"><img src="images/content.png" alt="content pages" title="content pages" style="padding:3px;" class="pagesnavicon" /></a></h2>';
	} else if (in_array($current_page, $forum_pages)) {
		echo '<h2><a href="forums.php"><img src="images/group.png" alt="forums" title="forums" style="padding:3px;" class="forumnavicon" /></a></h2>';
	} else if (in_array($current_page, $vlog_pages)) {
		echo '<h2><a href="vlogs.php"><img src="images/vlog.png" alt="vlogs" title="vlogs" style="padding:3px;" class="vlognavicon" /></a></h2>';
	}	
	
	if (isset($_SESSION['errors'])) {
		echo '<div class="error"><strong>Error:</strong><br />';
		foreach ($_SESSION['errors'] as $errmsg) {
         $invlogin = 'Invalid login.';

         echo $errmsg;	
			if (strcmp($errmsg, $invlogin) == 0) {
            echo '<img src="images/doorxshadowwhite.png" alt="" title="invalid login" class="inlineVertMid" style="margin:5 10;" />';
         }
         echo '<br />';
		}
		echo '</div>';
		unset($_SESSION['errors']);
	}
	if (isset($_SESSION['feedback'])) {
		echo '<div class="feedback"><strong>Feedback:</strong><br />';
		foreach ($_SESSION['feedback'] as $fbmsg) {
			$goodlogin = 'Successfully logged in.';
         
         echo $fbmsg;
         if (strcmp($fbmsg, $goodlogin) == 0) {
            echo '<img src="images/accepted_48.png" alt="" title="correct login" class="inlineVertMid" style="margin:5 10;" />';
         }

         echo '<br />';
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

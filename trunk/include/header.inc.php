<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" />
<html>
<body>
<head>
	<title>Signlink</title>
	<link rel="stylesheet" href="asl.css" type="text/css" media="screen" />

<!--[if IE]>
	<link href="iehacks.css" rel="stylesheet" />
<![endif]-->

	<script type="text/javascript" src="jscripts/jquery-1.2.3.pack.js"></script>          
 
</head>

<!-- ul id="menu-right">
	<li><a href="preferences.php"><img src="images/cog.png" alt="preferences" title="preferences" /></a></li>
	<li><a href="help.php"><img src="images/help.png" alt="help" title="help" /></a></li>
	<li>
	<?php 
	if ($_SESSION['valid_user']) {
		echo '<a href="logout.php"><img src="images/door_out.png" alt="logout" title="logout" /></a> '.$_SESSION['login']; 
	} else {
		echo '<a href="login.php"><img src="images/door_in.png" alt="login" title="login" /></a>'; 
	}		
	?>&nbsp;</li>
</ul -->
			
<div id="container">

	<div id="login-area">
		<?php if(isset($_SESSION['member_id']) && $_SESSION['member_id'] && $_SESSION['valid_user']) { ?>

			<div style="float:right">
				<img src="images/user.png" alt="" title="" /> <?php echo $_SESSION['login']; ?><br />
				<div style="vertical-align:bottom">
				<a href="preferences.php"><img src="images/cog.png" alt="preferences" title="preferences" /></a>&nbsp;
				<a href="logout.php"><img src="images/door_out.png" alt="log out" title="log out" /></a>
				</div>
			</div>
			<?php get_avatar($_SESSION['member_id']) ?>

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

	<div style="margin-top:5px;"><img src="images/fallback_forum_banner.png" alt="Signlink Studio Forum" /></div>

	

	<div id="menu" style="clear:both">
	<?php		
		$current_page = explode('/', $_SERVER['PHP_SELF']); 
		$current_page = $current_page[count($current_page) - 1];
	?>
		<ul>					
			<li><a href="index.php"><img src="images/house.png" alt="home" title="home" <?php if($current_page == 'index.php' || $current_page == 'page_view.php') { echo 'style="background-color: #cbdbef; border: 1px solid #7299C9;"'; } ?> /></a></li>
			<li><a href="content.php"><img src="images/picture.png" alt="pages" title="pages" /></a></li>
			<li><a href="forums.php"><img src="images/group.png" alt="forums" title="forums" <?php if(in_array($current_page, $forum_pages)) { echo 'style="background-color: #cbdbef; border: 1px solid #7299C9;"'; } ?> /></a></li>	
			<li><a href="vlogs.php"><img src="images/cup_edit.png" alt="vlogs" title="vlogs" <?php if($current_page == 'vlogs.php') { echo 'style="background-color: #cbdbef; border: 1px solid #7299C9;"'; } ?> /></a></li>	
			<li><a href="vlogs.php"><img src="images/help.png" alt="help" title="help" <?php if($current_page == 'help.php') { echo 'style="background-color: #cbdbef; border: 1px solid #7299C9;"'; } ?> /></a></li>	
		</ul>
	</div>


	<div id="content">
	<?php 
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
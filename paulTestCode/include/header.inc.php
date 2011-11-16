<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
  <title><?php echo SITE_NAME; ?></title>
  <link rel="stylesheet" href="css/asl.css" type="text/css" media="screen" />
  <link rel="stylesheet" href="jscripts/fancybox/jquery.fancybox-1.3.1.css" type="text/css" media="screen" />

  <!--[if IE]>
    <link href="css/iehacks.css" rel="stylesheet" />
  <![endif]-->

  <!--<script type="text/javascript" src="jscripts/jquery/js/jquery-1.3.2.min.js"></script>-->
  <script type="text/javascript" src="jscripts/jquery-1.4.4.min.js"></script> 
  <script type="text/javascript" src="jscripts/fancybox/jquery.fancybox-1.3.1.pack.js"></script>
  <script type="text/javascrtip" src="jscripts/fancybox/jquery.easing-1.3.pack.js"></script>
  <script type="text/javascript" src="jscripts/flowplayer-3.2.6.min.js"></script>
  <script type="text/javascript" src="jscripts/flowplayer_external_config.js"></script>
  <script type="text/javascript" src="jscripts/tweaks.js"></script>
  <!--<script type="text/javascript" src="http://cdn.jquerytools.org/1.1.2/jquery.tools.min.js"></script>-->

</head>
<body>
		
<div id="container">

	<div id="topbackground"></div>
	<div id="login-area">
		<?php if(isset($_SESSION['member_id']) && $_SESSION['member_id'] && $_SESSION['valid_user']) { ?>
			<div style="float:left;width:60px; text-align:center;">
				<?php get_avatar($_SESSION['member_id']) ?><br />
				<?php echo '<div id="memberid_login">' . $_SESSION['login'] . '</div>'; ?>
			</div>
			<div style="float:right; padding-right:5px;">
				<a href="preferences.php"><img src="images/spanner.png" alt="preferences" title="preferences" /></a>&nbsp;
				<a href="logout.php"><img src="images/door-logout2.png" style="background:none;" alt="log out" title="log out" /></a>
			</div>

		<?php
		} else { ?>
<!--      <a href="#data" id="inline"><img src="images/login_32.png" alt="" class="inlineVertMid" />Login</a>-->
      <a href="#" id="login-inline"><img src="images/login_32.png" alt="" class="inlineVertMid" /><br />Login</a>
      <script>
         $("#login-inline").click(function() {
            $("#login-div-content").toggle("slow");
         });
      </script>
      <div id="login-div-content" style="display:none;">
         <div id="data">
            <form action="login.php" method="POST" id="loginform" name="form">
               <input type="hidden" name="f" value="<?php echo intval($_REQUEST['f']); ?>" />
               <input type="hidden" name="p" value="<?php echo intval($_REQUEST['p']); ?>" />
               
               <label for="login"><img src="images/user_med.png" alt="login" title="login" class="inlineVertMid" /></label>
               <input name="login" type="text" id="login" value="<?php echo $_SERVER['login']; ?>" />
               <br />
               <label for="pswd"><img src="images/key3.png" alt="password" title="password" class="inlineVertMid" /></label>
               <input name="password" type="password" id="pswd" value="" />
               <br />
               <div id="login-button-container">
                  <input type="image" src="images/login_32.png" name="submitImg" alt="" class="inlineVertMid" style="margin-left:auto;margin-right:auto;margin-top:5px;" />
                  <input type="submit" name="loginSubmit" id="submitLogin" value="Login" class="button" />
               </div>

               <br style="clear:both" />
            <div id="login-footer-container">
              <ul>
                <li><a href="register.php"><img src="images/user_add_32.png" class="inlineVertMid" alt="" /></a><a href="register.php">Register</a></li>
                <!--<li><a href="password_reset_request.php"><img src="images/mail_key_32.png" class="inlineVertMid" alt="" /></a><a href="password_reset_request.php">Reset Password</a></li>-->
                <li><a href="password_reset_request.php"><img src="images/mail_key_32.png" class="inlineVertMid" alt="" />Reset Password</a></li>
              </ul>
            </div>
            </form>
         </div>
      </div>
		<?php 							
		} 
		?>
	</div>

	<!--<div style="margin-top:10px;"><a href="index.php"><img src="images/signlink_banner5.png" style="margin-top: 25px;margin-left:15px;" alt="<?php echo SITE_NAME; ?>" title="<?php echo SITE_NAME; ?>" /></a></div> -->
  <div id="new-logo-container">
    <img src="images/newlogoidea.png" alt="<?php echo SITE_NAME; ?>" title="<?php echo SITE_NAME; ?>" />
  </div>
  <div style="clear:both;"></div>

	<div id="menu" style="clear:both">
	<?php		
		$current_page = explode('/',htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES)); 
		$current_page = $current_page[count($current_page) - 1];
		
	?>
		<ul>					
			<li id="menu-home"><a href="index.php"><img src="images/house_shadow.png" class="homenavicon<?php if($current_page == 'index.php'){echo ' menu-current';}?>" alt="home" title="home"  /></a></li>
			
			<li id="menu-content"><a href="content.php"><img src="images/content.png" class="pagesnavicon<?php if(in_array($current_page, $content_pages)){echo ' menu-current';}?>" alt="pages" title="pages"  /></a></li>
			
			<li id="menu-forum"><a href="forums.php"><img src="images/group.png" class="forumnavicon<?php if(in_array($current_page, $forum_pages)){echo ' menu-current';}?>" alt="forums" title="forums"  /></a></li>	
			
			<li id="menu-vlog"><a href="vlogs.php"><img src="images/vlog.png" class="vlognavicon<?php if(in_array($current_page, $vlog_pages)) { echo ' menu-current';}?>" alt="vlogs" title="vlogs"  /></a></li>
			
			<li id="menu-help"><a href="help.php"><img src="images/help.png" class="helpnavicon<?php if($current_page == 'help.php'){echo ' menu-current';}?>" alt="help" title="help"  /></a></li>	
<!--         <li><a href="#"><img src="images/about-edit-2.png" alt="deleteme" /></a></li>-->
<!--         <li><a class="findmehere" href="images/default_movie_icon.png"><img src="images/lvlog2.png" alt="" /></a></li>-->
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
            echo '<img src="images/accepted_48.png" alt="" title="correct login" class="inlineVertMid" style="float:right;margin:5 10;" />';
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

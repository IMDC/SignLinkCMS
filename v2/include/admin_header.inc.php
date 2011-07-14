<html>
<body>
<head>
	<title><?php echo SITE_NAME; ?> - Administration</title>
	<link rel="stylesheet" href="../css/asl.css" type="text/css" media="screen" />

	<!--[if IE]>
		<link href="../css/iehacks.css" rel="stylesheet" />
	<![endif]-->
	<!--<script type="text/javascript" src="../jscripts/jquery/js/jquery-1.3.2.min.js"></script>--> 
	<!--<script type="text/javascript" src="../jscripts/jquery/js/jquery-ui-1.7.2.custom.min.js"></script>--> 
  <script type="text/javascript" src="../jscripts/jquery-1.4.4.min.js"></script>
  <script type="text/javascript" src="../jscripts/admintweaks.js"></script>
  <script type="text/javascript" src="../jscripts/fancybox/jquery.fancybox-1.3.1.pack.js"></script>
  <script type="text/javascript" src="../jscripts/flowplayer-3.2.6.min.js"></script>
  <script type="text/javascript" src="../jscripts/flowplayer_external_config.js"></script>
</head>

<div id="container">

	<div id="header">
		<h1><?php echo SITE_NAME; ?> - Administration</h1>
	</div>
	
	<?php 
	if ($_SESSION['is_admin']) {
		echo '<div style="float:right">'.$_SESSION['login'].' <a href="logout.php"><img src="../images/door-logout.png" alt="logout" title="logout" style="margin-bottom:-3px;" /></a></div>'; 
	} 		
	?>
	<?php if($_SESSION['is_admin']) { ?>
	<div id="menu">
		<ul>
			<li><a href="index.php"><img src="../images/house_shadow.png" class="homenavicon" alt="home" title="home" /></a> | </li>
			<li><a href="member_manage.php"><img src="../images/contacts.png" alt="members" /></a> | </li>
			<li><a href="page_manage.php"><img src="../images/content.png" class="pagesnavicon" alt="pages" /></a> | </li>
			<li><a href="forum_manage.php"><img src="../images/group.png" class="forumnavicon" alt="forums" /></a> | </li>
			<li><a href="vlog_manage.php"><img src="../images/vlog.png" class="vlognavicon" alt="vlogs" /></a> | </li>
			<li><a href="settings.php"><img src="../images/spanner.png" alt="settings" /></a> | </li>
			<li><a href="help.php"><img src="../images/help.png" class="helpnavicon" alt="help" /></a></li>
		</ul>
	</div>
	<?php } ?>
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
	?>

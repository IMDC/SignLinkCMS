<html>
<body>
<head>
	<title><?php echo SITE_NAME; ?> - Administration</title>
	<link rel="stylesheet" href="../asl.css" type="text/css" media="screen" />

	<!--[if IE]>
		<link href="../iehacks.css" rel="stylesheet" />
	<![endif]-->
	<script type="text/javascript" src="../jscripts/jquery-1.2.3.pack.js"></script>  
</head>

<div id="container">

	<div id="header">
		<h1><?php echo SITE_NAME; ?> - Administration</h1>
	</div>
	
	<?php 
	if ($_SESSION['is_admin']) {
		echo '<div style="float:right">'.$_SESSION['login'].' <a href="logout.php"><img src="../images/door_out.png" alt="logout" title="logout" style="margin-bottom:-3px;" /></a></div>'; 
	} 		
	?>
	<?php if($_SESSION['is_admin']) { ?>
	<div id="menu">
		<ul>
			<li><a href="index.php">home</a> | </li>
			<li><a href="member_manage.php">members</a> | </li>
			<li><a href="page_manage.php">pages</a> | </li>
			<li><a href="forum_manage.php">forums</a> | </li>
			<li><a href="vlog_manage.php">vlogs</a> | </li>
			<li><a href="settings.php">settings</a> | </li>
			<li><a href="">help</a></li>
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
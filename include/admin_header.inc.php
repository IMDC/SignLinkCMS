<html>
<body>
<head>
	<title>Admin</title>
	<link rel="stylesheet" href="../asl.css" type="text/css" media="screen" />

	<!--[if IE]>
		<link href="../iehacks.css" rel="stylesheet" />
	<![endif]-->
	<script type="text/javascript" src="../jscripts/jquery-1.2.3.pack.js"></script>          

</head>

<div id="container">

	<div id="header">
		<h1>SignLink Project - Administration</h1>
	</div>

	<div id="menu">
		<ul>
			<li><a href="index.php">main</a> | </li>
			<li><a href="page_manage.php">pages</a> | </li>
			<li><a href="forum_manage.php">forums</a> | </li>
			<li><a href="settings.php">settings</a> | </li>
			<li><a href="">help</a></li>
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
	?>
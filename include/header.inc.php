<html>
<body>
<head>
	<title>Forum</title>
	<link rel="stylesheet" href="asl.css" type="text/css" media="screen" />

	<script language="javascript" type="text/javascript" src="jscripts/tiny_mce/tiny_mce.js"></script>
	<script language="javascript" type="text/javascript">
		tinyMCE.init({
			mode : "textareas",
			theme : "simple"
		});
	</script>
</head>

<div id="container">

	<div id="header">
		<h1>SignLink Project</h1>
	</div>

	<div id="top">
		<div id="menu-left">
			<ul>
				<li><a href="index.php">main</a> | </li>
				<li><a href="forum_main.php">forums</a></li>
			</ul>
		</div>
		<div id="menu-right">
			<?php 
			if ($_SESSION['valid_user']) {
				echo '<strong>'.$_SESSION['login'].'</strong> - <a href="login.php">logout</a>'; 
			} else {
				echo '<a href="login.php">login</a>'; 
			}		
			?>
			
			| <a href="">help</a>
		</div>
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
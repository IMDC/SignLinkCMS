<html>
<body>
<head>
	<title>Forum</title>
	<link rel="stylesheet" href="asl.css" type="text/css" media="screen" />

<!--[if IE]>
	<link href="iehacks.css" rel="stylesheet" />
<![endif]-->

	<script type="text/javascript" src="jscripts/jquery-1.2.3.pack.js"></script>          
 
</head>

<div id="container">

	<div>
		<div style="float:right; margin-top:5px;"><img src="images/fake_logo.jpg" style="width:100px" alt="Signlink Studio" /></div>

		<div id="menu">
			<ul>						
				<li><a href="index.php"><img src="images/application_view_tile.png" alt="content" title="content" /></a></li>
				<li><a href="forum_main.php"><img src="images/group.png" alt="forums" title="forums" /></a></li>	
				<li><a href=""><img src="images/cup_edit.png" alt="vlogs" title="vlogs" /></a></li>				
				
				<li><a href=""><img src="images/help.png" alt="help" title="help" /></a></li>
				<li>
				<?php 
				if ($_SESSION['valid_user']) {
					echo '<a href="logout.php"><img src="images/door_out.png" alt="logout" title="logout" /></a> &nbsp;<strong>'.$_SESSION['login'].'</strong>'; 
				} else {
					echo '<a href="login.php"><img src="images/door_in.png" alt="login" title="login" /></a>'; 
				}		
				?>&nbsp;</li>
			</ul>
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
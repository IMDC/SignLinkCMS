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

<ul id="menu-right">
	<li><a href="preferences.php"><img src="images/cog.png" alt="preferences" title="preferences" /></a></li>
	<li><a href=""><img src="images/help.png" alt="help" title="help" /></a></li>
	<li>
	<?php 
	if ($_SESSION['valid_user']) {
		echo '<a href="logout.php"><img src="images/door_out.png" alt="logout" title="logout" /></a> '.$_SESSION['login']; 
	} else {
		echo '<a href="login.php"><img src="images/door_in.png" alt="login" title="login" /></a>'; 
	}		
	?>&nbsp;</li>
</ul>
			
<div id="container">

	<div>
		<div style="margin-top:5px;"><img src="images/fake_logo.jpg" style="width:75px" alt="Signlink Studio" /></div>

		<div id="menu">
		<?php		
			$current_page = explode('/', $_SERVER['PHP_SELF']); 
			$current_page = $current_page[count($current_page) - 1];
		?>
			<ul>					
				<li><a href="index.php"><img src="images/house.png" alt="home" title="home" <?php if($current_page == 'index.php' || $current_page == 'page_view.php') { echo 'style="background-color: #cbdbef; border: 1px solid #7299C9;"'; } ?> /></a></li>
				<li><a href="forums.php"><img src="images/group.png" alt="forums" title="forums" <?php if(in_array($current_page, $forum_pages)) { echo 'style="background-color: #cbdbef; border: 1px solid #7299C9;"'; } ?> /></a></li>	
				<li><a href=""><img src="images/cup_edit.png" alt="vlogs" title="vlogs" <?php if($current_page == 'vlogs.php') { echo 'style="background-color: #cbdbef; border: 1px solid #7299C9;"'; } ?> /></a></li>				
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
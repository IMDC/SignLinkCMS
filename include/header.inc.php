<html>
<body>
<head>
	<title>Forum</title>
	<link rel="stylesheet" href="asl.css" type="text/css" media="screen" />

	<script language="javascript" type="text/javascript" src="jscripts/tiny_mce/tiny_mce.js"></script>
	<script language="javascript" type="text/javascript">
		tinyMCE.init({
				theme : "advanced",
				mode : "textareas",
				plugins : "custom_media",
				theme_advanced_buttons1_add : "image, custom_media",
				theme_advanced_disable : "bold, italic, underline, strikethrough, justifyleft, justifycenter, justifyright, justifyfull, separator, formatselect, styleselect",
				theme_advanced_buttons2 : "",
				theme_advanced_buttons3 : "",
				theme_advanced_toolbar_location : "top",
				theme_advanced_toolbar_align : "right",
				file_browser_callback : 'fileBrowserCallBack'
		});

function fileBrowserCallBack(field_name, url, type, win) {
	var connector = "../../filemanager/browser.html?Connector=connectors/php/connector.php";
	var enableAutoTypeSelection = true;
	
	var cType;
	tinyfck_field = field_name;
	tinyfck = win;
	
	switch (type) {
		case "image":
			cType = "Image";
			break;
		case "flash":
			cType = "Flash";
			break;
		case "file":
			cType = "File";
			break;
	}
	
	if (enableAutoTypeSelection && cType) {
		connector += "&Type=" + cType;
	}
	
	window.open(connector, "tinyfck", "modal,width=600,height=400");
}
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
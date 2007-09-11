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
				file_browser_callback : 'myFileBrowser'
		});

  function myFileBrowser (field_name, url, type, win) {

    var fileBrowserWindow = new Array();
    fileBrowserWindow["file"] = "../../../../moduly/files/files_editor.php";
    fileBrowserWindow["title"] = "Browser";
    fileBrowserWindow["width"] = "830";
    fileBrowserWindow["height"] = "400";
    fileBrowserWindow["close_previous"] = "no";
    tinyMCE.openWindow(fileBrowserWindow, {
      window : win,
      input : field_name,
      resizable : "yes",
      inline : "yes",
      editor_id : tinyMCE.getWindowArg("editor_id")
    });
    
    return false;
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
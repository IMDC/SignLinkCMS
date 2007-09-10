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
				plugins : "media",
				theme_advanced_buttons1_add : "media",
				theme_advanced_disable : "bold, italic, underline, strikethrough, justifyleft, justifycenter, justifyright, justifyfull, separator, formatselect, styleselect",
				theme_advanced_buttons2 : "",
				theme_advanced_buttons3 : "",
				height : "20em",
				theme_advanced_toolbar_location : "top",
				theme_advanced_toolbar_align : "right",
				file_browser_callback : 'myFileBrowser'
		});

  function myFileBrowser (field_name, url, type, win) {

    // alert("Field_Name: " + field_name + "\nURL: " + url + "\nType: " + type + "\nWin: " + win); // debug/testing

    /* If you work with sessions in PHP and your client doesn't accept cookies you might need to carry
       the session name and session ID in the request string (can look like this: "?PHPSESSID=88p0n70s9dsknra96qhuk6etm5").
       These lines of code extract the necessary parameters and add them back to the filebrowser URL again. */

    var cmsURL = window.location.pathname;      // script URL
    var searchString = window.location.search;  // possible parameters
    if (searchString.length < 1) {
        // add "?" to the URL to include parameters (in other words: create a search string because there wasn't one before)
        searchString = "?";
    }

    // newer writing style of the TinyMCE developers for tinyMCE.openWindow

    tinyMCE.openWindow({
        file : cmsURL + searchString + "&type=" + type, // PHP session ID is now included if there is one at all
        title : "File Browser",
        width : 420,  // Your dimensions may differ - toy around with them!
        height : 400,
        close_previous : "no"
    }, {
        window : win,
        input : field_name,
        resizable : "yes",
        inline : "yes",  // This parameter only has an effect if you use the inlinepopups plugin!
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
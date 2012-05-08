<?php
   session_start();
   require('include/functions.inc.php');

   $introvid = "videos/admintoc.swf";
?>

  
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
  <title>Signlink Studio @ Nuit Blanche</title>
  <link rel="stylesheet" href="css/main.css" type="text/css" media="screen" />
  <link rel="stylesheet" href="jscripts/fancybox/jquery.fancybox-1.3.4.css" type="text/css" media="screen" />
  <script type="text/javascript" src="jscripts/jquery-1.4.3.min.js"></script> 
  <script type="text/javascript" src="jscripts/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
  <script type="text/javascript" src="jscripts/fancybox/jquery.easing-1.3.pack.js"></script>
  <script type="text/javascript" src="jscripts/flowplayer-3.2.6.min.js"></script>
  <script type="text/javascript" src="jscripts/scr.js"></script>
  <script>
  $(document).ready(function(){
		timedCount();
		//$("#replaceme").load("listVids.php");
		//$("#vid-list-container").load("listVids.php");
		$("#vid-list-container").load("listVids.php", function() {
			$("a.slolightbox").fancybox({
				'transitionIn'  :  'elastic',
				'transitionOut' :  'elastic',
				'speedIn'       :  200, 
				'speedOut'      :  200, 
				'cyclic'        :  'true',
				'overlayColor'  :  '#000',
				'overlayOpacity':  '0.4',
				'swf'           :  '{wmode:"opaque"}',
				'height'        :  430,
				'width'         :  570,
				'onStart'       : function() {
											$("#intro-container").hide();
											myInterval = setInterval('advanceVid()', 70000); // auto advance the gallery after x milliseconds eg) 3000 = 3 sec
										},
				'onClosed'      : function() {
											$("#intro-container").show();
											clearInterval(myInterval);
										}
			});
		});
	  });

	function timedCount() {
		t=setTimeout("timedCount()",1000);  // 1000 msec = 1 sec
		//$("#replaceme").load("listVids.php");
		$("#vid-list-container").load("listVids.php", function() {
			$("a.slolightbox").fancybox({
				'transitionIn'  :  'elastic',
				'transitionOut' :  'elastic',
				'speedIn'       :  200, 
				'speedOut'      :  200, 
				'cyclic'        :  'true',
				'overlayColor'  :  '#000',
				'overlayOpacity':  '0.4',
				'swf'           :  '{wmode:"opaque"}',
				'height'        :  430,
				'width'         :  570,
				'onStart'       : function() {
											$("#intro-container").hide();
											myInterval = setInterval('advanceVid()', 70000); // auto advance the gallery after x milliseconds eg) 3000 = 3 sec
										},
				'onClosed'      : function() {
											$("#intro-container").show();
											clearInterval(myInterval);
										}
			});
		});
	}
  </script>
</head>
<body>
<div id="top-pane">
   <img id="nuitlogo" src="images/nuitblanchelogo.jpg" alt="Nuit Blanche" width="100px;" height="70px;" />
   <div id="intro-container" class="centered"> 
      <img id="topbarbackground" class="centered" src="images/black_bar_background.gif" alt="black top bar" /> 

      <object width="565" height="455"
         classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"
         codebase="http://fpdownload.macromedia.com/pub/
         shockwave/cabs/flash/swflash.cab#version=8,0,0,0">
         <param name="movie" value="<?php echo $introvid; ?>"/>
         <param name="autoplay" value="false"/>
         <embed src="<?php echo $introvid; ?>" width="565" height="455"
         type="application/x-shockwave-flash" pluginspage=
         "http://www.macromedia.com/go/getflashplayer" />
      </object>
   </div>
</div>
<div id="textgreet" class="centered">
   <h1>Check out these Signlink Studio videos from Nuit Blanche visitors!</h1>
</div>
<div id="vid-list" class="centered">
   <div id="vid-list-container" class="centered">
		&nbsp;
   </div>
</div>
<div style="clear:both;background:#000;padding-bottom:60px;"></div>

<?php
   require('include/footer.inc.php');
?>

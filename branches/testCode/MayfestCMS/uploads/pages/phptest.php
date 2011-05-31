<html>
<head>
<style>
html, body {
  width: 800px;
  height: 100%;
  margin: 2px;
}
</style>
</head>
<body>
<?php //echo shell_exec("ffmpeg 2>&1"); 
?>
<?php echo shell_exec("ffmpeg -i 8/title.avi -ss 1 -f image2 -vframes 1 -s 144x112 8/thumb.jpg 2>&1"); ?>
<p>Text here</p>

<img src="8/thumb.jpg" />
</body>
</html>

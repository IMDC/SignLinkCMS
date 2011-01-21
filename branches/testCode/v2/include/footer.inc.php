	<div style="clear:both;"></div>

	</div>
	<div id="footer">
		&nbsp;
	</div>
</div>
<?php //debug($_SESSION)
  if (detectMobile()) {
    echo '<p><h1>mobile browser was detected in the footer</h1></p>';
  }
  else {
    echo '<p><h1>no mobile browser was detected in the footer</h1></p>';
  }
?>

</body>
</html>

<?php 
define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');

require(INCLUDE_PATH.'header.inc.php');

$sql = "SELECT * FROM forums WHERE 1 ORDER BY subject ASC";
$result = mysql_query($sql, $db);
if (@mysql_num_rows($result)) { ?>

<span class="bold"><label>Name</label></span><br />
<input type="text" value="" /><br /><br />

<span class="bold"><label>Email</label></span><br />
<input type="text" value="" /><br /><br />

<span class="bold"><label>Username</label></span><br />
<input type="text" value="" /><br /><br />

<span class="bold"><label>Avatar</label></span><br />
<input type="text" value="" /><br /><br />


<?php
} else {
	echo "Invalid member ID.";
}

 require('include/footer.inc.php'); ?>

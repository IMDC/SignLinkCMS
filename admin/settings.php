<?php 

define('INCLUDE_PATH', '../include/');
require(INCLUDE_PATH.'vitals.inc.php');
admin_authenticate();

require(INCLUDE_PATH.'admin_header.inc.php'); ?>


<ul class="admin-menu">
	<li><span class="bold">General</span>: 
		<ul>
			<li><a href="">header</li> 
			<li><a href="">admin email</li>
			<li><a href="">Users</a> - view and manage list (incl blacklisted)</li>
		</ul>
	</li>

</ul>

<?php require('../include/footer.inc.php'); ?>

<?php 

define('INCLUDE_PATH', '../include/');
require(INCLUDE_PATH.'vitals.inc.php');

require(INCLUDE_PATH.'admin_header.inc.php'); ?>


<ul class="admin-menu">
	<li><span class="bold">General</span>: 
		<ul>
			<li><a href="">Site Properties</a> - title, admin email</li> 
			<li><a href="">Modules</a> -  enable/disable vlog or forum</li>
			<li><a href="">Users</a> - view and manage list (incl blacklisted)</li>
		</ul>
	</li>
	<li><span class="bold">Forum</span>: 
		<ul>
			<li><a href="">Options</a> - title, who can post, max-upload size</li> 
		</ul>
	</li>
	<!-- li><span class="bold">VLog</span>: (if enabled) 
		<ul>
			<li><a href="">Options</a></li> 
			<li><a href="">Users</a></li>
		</ul>
	</li -->
</ul>

<?php require('../include/footer.inc.php'); ?>

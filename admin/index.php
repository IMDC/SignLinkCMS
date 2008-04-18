<?php 
define('INCLUDE_PATH', '../include/');
require(INCLUDE_PATH.'vitals.inc.php');
//admin_authenticate(AT_ADMIN_PRIV_USERS);

require(INCLUDE_PATH.'admin_header.inc.php'); 
?>

<!-- ul class="admin-menu">
	<li><span class="bold">Pages</span>:
		<ul>
			<li><a href="page_sign_create.php">New SignLink Page</a></li> 
			<li><a href="page_html_create.php">New html Page</a></li> 
			<li><a href="page_manage.php">Manage Pages</a></li>
		</ul>
	</li>
	<li><span class="bold">Forum</span>: 
		<ul>
			<li><a href="forum_create.php">Forum Categories</a></li> 
			<li><a href="forum_create.php">New Forum</a></li> 
			<li><a href="forum_manage.php">Manage Forums</a></li>
		</ul>
	</li>
	<li><span class="bold">VLog</span>: 
		<ul>
			<li><a href="">New Post</a></li> 
			<li><a href="">Manage Posts</a></li>
		</ul>
	</li>
</ul -->

<?php require('../include/footer.inc.php'); ?>

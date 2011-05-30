<?php 
define('INCLUDE_PATH', '../include/');
define('FROM_ADMIN', '1');

require_once(INCLUDE_PATH.'vitals.inc.php');

admin_authenticate();

require(INCLUDE_PATH.'admin_header.inc.php'); 
?>

<p>Use the above menu to manage each section of your website.</p>

<?php require('../include/footer.inc.php'); ?>

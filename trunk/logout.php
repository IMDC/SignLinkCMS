<?php

define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');

unset($_SESSION['login']);
unset($_SESSION['valid_user']);
unset($_SESSION['member_id']);
unset($_SESSION['is_admin']);
unset($_SESSION['errors']);
unset($_SESSION['notices']);

$_SESSION['feedback'][] = 'Successfully logged out.';

header('Location: index.php');
exit;

?>
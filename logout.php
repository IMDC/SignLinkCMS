﻿<?php

define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');

unset($_SESSION['login']);
unset($_SESSION['valid_user']);
unset($_SESSION['member_id']);
unset($_SESSION['is_admin']);

$_SESSION['feedback'][] = 'Successfully logged out.';

header('Location: login.php');
exit;

?>
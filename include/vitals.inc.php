<?php

if (!defined('INCLUDE_PATH')) { exit; }

define('DB_USER', 'root');
define('DB_PASSWORD', 'tydiutor');
define('DB_PORT', '3306');
define('DB_HOST', 'localhost');
define('DB_NAME', 'signlink');

define('UPLOAD_DIR', 'uploads/');

//define('AT_BASE_HREF', 'http://142.150.154.124/asl/');

session_start();

//if (INCLUDE_PATH !== 'NULL') {
	$db = @mysql_connect(DB_HOST . ':' . DB_PORT, DB_USER, DB_PASSWORD);
	if (!$db) {
		trigger_error('VITAL#Unable to connect to db.', E_USER_ERROR);
		exit;
	}
	if (!@mysql_select_db('signlink', $db)) {
		trigger_error('VITAL#DB connection established, but database "'.DB_HOST.'" cannot be selected.', E_USER_ERROR);
		exit;
	}
//}

function my_add_null_slashes( $string ) {
    return mysql_real_escape_string(stripslashes($string));
}
function my_null_slashes($string) {
	return $string;
}
if ( get_magic_quotes_gpc() == 1 ) {
	$addslashes   = 'my_add_null_slashes';
	$stripslashes = 'stripslashes';
} else {
	$addslashes   = 'mysql_real_escape_string';
	$stripslashes = 'my_null_slashes';
}

require('functions.inc.php');

?>
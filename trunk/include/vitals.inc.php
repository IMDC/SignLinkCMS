<?php
if (!defined('INCLUDE_PATH')) { exit; }

require('config.inc.php');
//require('constants.inc.php');

//define('AT_BASE_HREF', 'http://142.150.154.124/asl/');

session_start();

if (INCLUDE_PATH !== 'NULL') {
	$db = @mysql_connect(DB_HOST . ':' . DB_PORT, DB_USER, DB_PASSWORD);
	if (!$db) {
		die('Could not connect: ' . mysql_error());
	}
	if (!@mysql_select_db(DB_NAME, $db)) {
		echo 'DB connection established, but database "'.DB_HOST.'" cannot be selected.';
		exit;
	}
}



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

require('lib/functions.inc.php'); 
require('lib/forums.inc.php');
require('lib/pages.inc.php');

?>
<?php
if (!defined('INCLUDE_PATH')) { exit; }

require('config.inc.php');
require('constants.inc.php');

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

function admin_authenticate() {
	//check if logged in as admin
	if ($_SESSION['valid_user'] && $_SESSION['is_admin'] && !empty($_SESSION['member_id']) && $_SESSION['login']=="admin") {
		return true;
	} else {
		/*$_SESSION['errors'][] = 'You must <a href="login.php">login</a> as an administrator.';
		require(INCLUDE_PATH.'admin_header.inc.php');
		require(INCLUDE_PATH.'footer.inc.php');*/
		header('Location:login.php');
		exit;
	}
}


function user_authenticate() {
	//check if logged in as registered user 
	if ($_SESSION['valid_user'] && !empty($_SESSION['member_id'])) {
		return true;
	} else {
		$_SESSION['errors'][] = 'You must <a href="login.php">login</a> to use this site.';
		header( 'Location: login.php' );
      //require(INCLUDE_PATH.'footer.inc.php');
		exit;
	}
}


/* set administrator preferences for the site */
$result = @mysql_query( "SELECT * FROM settings WHERE 1", $db);
while ($row = @mysql_fetch_assoc($result)) {
	define(strtoupper($row['name']), $row['value']);
}

$_SESSION['token'] = 'signtokenlink09';

require(INCLUDE_PATH.'lib/functions.inc.php'); 
require(INCLUDE_PATH.'lib/forums.inc.php');
require(INCLUDE_PATH.'lib/pages.inc.php');


?>

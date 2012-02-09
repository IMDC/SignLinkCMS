<?php
$text = $_GET["term"];
require('../config.inc.php');
require('../constants.inc.php');

 $db = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);
  if (!$db) {
    //printf("Connect failed: %s\n", mysqli_connect_error());
    //exit();
    die('Could not connect: ' . mysqli_connect_error());
  }
  if (mysqli_connect_errno()) {
    die('Connect failed: ' . mysqli_connect_error());
  } 


$sql = "SELECT email FROM members Where login LIKE '%$text%' ORDER BY name ASC  "; 

$result =  mysqli_query($db, $sql);

if($result)
{
	
}
else {
	echo "no db";
}

$json = '[';
$first = true;

 while($row = mysqli_fetch_assoc($result)){
  
    if (!$first) { $json .=  ','; } else { $first = false; }
    $json .= '{"value":"'.$row['email'].'"}';
}
$json .= ']';
echo $json;
?>
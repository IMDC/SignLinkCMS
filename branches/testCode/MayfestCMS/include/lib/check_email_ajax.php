<?php 
define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');

$q=$_GET["q"];

$sql="SELECT * FROM members WHERE mail = '".$q."'";
      
$result = mysqli_query($db, $sql);
      
while($row = mysqli_fetch_array($result))
{
  <div>
    <p><h3> Your email has been found in our records </h3></p>
    <form action="../../password_reset.php" method="post" name="form">
      <input type="submit" class="submitBtn" name="submit" value="Reset Password" /> | <input type="submit" name="cancel" value="Cancel" class="cancelBtn" />
    </form>
  </div>
                            
}



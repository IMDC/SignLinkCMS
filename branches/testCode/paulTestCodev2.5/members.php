<?php 

define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');

require(INCLUDE_PATH.'header.inc.php');

?>
 
 <link rel="stylesheet" type="text/css" href="css/member.css" />
 
   <br / >
   <br />

 
 
 <!-- this will display the members header and the search bar to search a member -->
 
   <h2>
   
     <form class="search" action="searchmember.php" method="GET"> 
             <input  type="text" name="name" /> 
             <input class="submit-button" TYPE="image" SRC="images/search_button_green_32.png" BORDER="0" ALT="Submit Form" placeholder="search">
		</form> 
		
     <img  src="images/users.png">
	 Members
	  
	    
   </h2>
 
     
<?php

   //echo "<p> Check out our Newest Members!</p>";
 
  $sql = "SELECT * FROM members Where status = '1' Order BY created_ts DESC Limit 6"; 
 
  $result = mysqli_query($db, $sql);
 
 //display 6 pictures of verified members to user
  while($row = mysqli_fetch_assoc($result)){ ?>
		
		<div class="avatarContainer">
			
			<a href="searchmember.php?name=<?php echo $row['login']; ?>&id=<?php echo $row['member_id']; ?>" ><img src="uploads/members/<?php echo $row['member_id']; ?>/avatar.jpg" /></a> 
			
			<span><?php echo ucfirst($row['login']); ?></span>
	    </div>
		
  <?php
	
	}

    
  require('include/footer.inc.php');
  
?> 
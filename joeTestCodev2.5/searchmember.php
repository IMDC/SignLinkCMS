<?php 

define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');

require(INCLUDE_PATH.'header.inc.php');
  
$memberSearch = $_GET["name"]; 
$memberID = $_GET["id"];
?>

<link rel="stylesheet" type="text/css" href="css/member.css" />


</br>
</br>
<!-- Create the profile header -->
<h2>	
     <img  src="images/users.png">
	 User Profile	      
</h2>


<?php 
  
   
  $sql = "SELECT * FROM members WHERE login = '$memberSearch'"; 

  $result = mysqli_query($db, $sql);
  
  if(mysqli_num_rows($result)==0){
      echo "<br/> The user you searched for does not exist. ";
	  
      $partialString = substr($memberSearch,0,1);
	  $partialString = $partialString . '%';
	  
	  $sql = "SELECT * FROM members WHERE login Like '$partialString'"; 
	  $result = mysqli_query($db, $sql);
  
     if(mysqli_num_rows($result)){
	      echo "Did You mean: </br><hr/>";
			while($row = mysqli_fetch_assoc($result)){
	    
				$filename = "uploads/members/" . $row['member_id'] . "/avatar.jpg";	 ?>
		
				<div class="profileContent">
					<div class="profileImageContent">
			
						<?php if (file_exists($filename)) { ?>
							<img src="uploads/members/<?php echo $row['member_id']; ?>/avatar.jpg" />
						<?php }else{ ?>
							<img src="images/no_avatar.jpg" />
						<?php } ?>
					</div>
			
					<div class="profileBlock">
						<img class="profileBlockImage" src="images/user_med" />
					
						<span><?php echo "<br/>Username: " . $row["login"]; ?></span>
					</div>
			
					<div class="profileBlock">
						<img class="profileBlockImage" src="images/user_med" />
					
						<span><?php echo "<br/>Name: " . $row["name"]; ?></span>
					</div>
			
					<div class="profileBlock">
						<img class="profileBlockImage" src="images/forum_read_med" />
				
						<span><?php echo "<br/>Email: " . $row["email"];  ?></span>
					</div>
			
					<div class="profileBlock">
						<img class="profileBlockImage" src="images/user_med" />
					
						<span><?php echo "<br/>User Since: " . $row["created_ts"]; ?></span>
					</div>
		
       
			        <br /><a href="searchmember.php?name=<?php echo $row['login']; ?>&id=<?php echo $row['member_id']; ?>" >View User -></a> 
				</div>
		            
					
				<hr />
		
			<?php } 
		}
   }
  else{  
  // if there is a result for the query, then display it to the user 
     while($row = mysqli_fetch_assoc($result)){
	    
		$filename = "uploads/members/" . $row['member_id'] . "/avatar.jpg";	 ?>
		
		<div class="profileContent">
			<div class="profileImageContent">
			
				<?php if (file_exists($filename)) { ?>
					<img src="uploads/members/<?php echo $row['member_id']; ?>/avatar.jpg" />
				<?php }else{ ?>
					<img src="images/no_avatar.jpg" />
				<?php } ?>
		    </div>
			
			<div class="profileBlock">
				<img class="profileBlockImage" src="images/user_med" />
				
				<span><?php echo "<br/>Username: " . $row["login"]; ?></span>
			</div>
			
			<div class="profileBlock">
				<img class="profileBlockImage" src="images/user_med" />
				
				<span><?php echo "<br/>Name: " . $row["name"]; ?></span>
			</div>
			
			<div class="profileBlock">
				<img class="profileBlockImage" src="images/forum_read_med" />
				
				<span><?php echo "<br/>Email: " . $row["email"];  ?></span>
			</div>
			
			<div class="profileBlock">
				<img class="profileBlockImage" src="images/user_med" />
				
				<span><?php echo "<br/>User Since: " . $row["created_ts"]; ?></span>
			</div>
		
       
			
		</div>
		
	<?php /*this is a test
		$filename = "uploads/members/" . $row['member_id'] . "/avatar.jpg";	 
		
		<div class="profileContent">
			
			<?php if (file_exists($filename)) { ?>
				<img src="uploads/members/<?php echo $row['member_id']; ?>/avatar.jpg" />
			<?php }else{ ?>
			    <img src="images/no_avatar.jpg" />
			<?php } ?>
		
		    <span>
				<?php
					echo "<br/>Username: " . $row["login"];
					echo "<br/>Name: " . $row["name"]; 
					echo "<br/>Email: " . $row["email"];  
					echo "<br/>User Since: " . $row["created_ts"];
                ?>
			</span>
        <div>
		
		*/?>
		
		
 <?php
  
    }   
  
 ?>
 

<br/><div class="commentsHeader"><span>Forums</span></div>

<div class="commentContainer">
	<?php
  
	//forms created by the user/
	    
	    $counter = 0;
		$sql = "Select * from forums_posts WHERE  member_id='$memberID' AND parent_id = 0 ORDER BY last_comment DESC ";

		$result = mysqli_query($db, $sql);

	if (mysqli_num_rows($result)) { 
	
	
		echo '<span>Created</span> <br/>';
	
		while ($row = mysqli_fetch_assoc($result)) {
	        
			if($counter == 2)
			   {
			      break;
			   }
			   
			$counter++;
			
	        $title = get_title('post', $row['post_id']); 
	        
			$sql2 = "SELECT views FROM forums_views WHERE post_id=".$row['post_id'];
			$result2 = mysqli_query($db, $sql2);
			$views = mysqli_fetch_assoc($result2);
			$views = intval($views['views']);
	?>
		  <div class="infoContainer">
			
			<!-- div style="padding-right:2px;font-size:smaller;">
				<div style="float:left;">
						<?php echo date('g:ia, M j, y', strtotime($row['date'])); ?>
				</div>
				<div style="float:right;">
					<img src="images/user_female.png" style="margin-bottom:-5px;" /><?php echo $row['login']; ?>	
				</div>
			</div -->

				<div class="title" onclick="location.href='forum_post_view.php?f=<?php echo $row['forum_id']; ?>&p=<?php echo $row['post_id']; ?>'" style="cursor:pointer">
					<div style="height:150px">
						<?php echo $title; ?>
					</div>							

					<a href="forum_post_view.php?f=<?php echo $row['forum_id']; ?>&p=<?php echo $row['post_id']; ?>" class="goto">
						<img src="images/hand.png" style="border:0px;padding:0px;" alt="click to view" />
					</a>
				</div>

				<div>
					<div style="text-align:left;padding-right:2px; font-size:smaller;">
						<div style="float:left;">
							<?php //check for new messages - #comments vs number of read child posts in forum_read. if equal, no unread
						
							$sql = "SELECT * FROM forums_read WHERE (post_id=".$row['post_id']." OR parent_id=".$row['post_id'].") AND member_id=".intval($_SESSION['member_id']);
							$result2 = mysqli_query($db, $sql);
							$read = @mysqli_num_rows($result2);
													
							if ($_SESSION['valid_user'] && $row['num_comments']+1>$read) { 
								echo '<img src="images/email_red.png" alt="new messages" title="new messages" height="16" width="16" /> ';					
							} else {
								echo '<img src="images/email.png" alt="no new messages" title="no new messages" height="16" width="16" /> ';
							} ?>
						</div>
						<div style="float:right;">
							<img src="images/comments.png" style="margin-bottom:-5px;" alt="number of replies:" title="number of replies" /> <?php echo $row['num_comments']; ?>
							<img src="images/magnifier.png" style="margin-bottom:-5px;" alt="number of views:" title="number of views" /><?php echo $views; ?>
						</div>
						<div class="title-timestamp">Last: <?php echo date('M j y, h:ia', strtotime($row['last_comment']))?></div>
					</div>
				</div>
			
			</div>
			
			
	<?php 
		} ?>
			<!--<br style="clear:both" />-->
		
		<?php 
		
		    if (mysqli_num_rows($result) > 2) { ?>
		       <a href="more.php?name=<?php echo $memberSearch; ?>&id=<?php echo $memberID; ?>&type=<?php echo '1'; ?>">See More</a> <?php
		    }
		    
		?>
		
	<?php
	} else {
	
		echo '<span>Created</span> <br/>'; 
		echo "<p>The user has not created any forums.</p>";
	}
	?>
  
</div>

	
<div class="commentContainer">
		<?php  //so check if they have created forums and post 

//This will grab wat the user commented on and not what the user created/////////////////
        $counter = 0;
		$sql = "SELECT DISTINCT t1.post_id, t1.parent_id, t1.forum_id, t1.login, t1.date, t1.msg, t1.msg_alt, t1.subject, t1.subject_alt, t1.num_comments FROM forums_posts t1 inner join forums_posts t2 ON t1.post_id = t2.parent_id WHERE t2.parent_id != 0 AND t2.member_id='$memberID' ORDER BY t1.last_comment DESC ";
//AND NOT t1.parent_id = 0  to take out what they created
		$result = mysqli_query($db, $sql);
        $numRows = mysqli_num_rows($result);
		
		if (mysqli_num_rows($result)) { 
			
			echo '';
	
			echo '<span>Commented On</span> <br/>';
	
			while ($row = mysqli_fetch_assoc($result)) {
	          
	           if($counter == 2)
			   {
			      break;
			   }
			   
			   $counter++;
	           
		
				$title = get_title('post', $row['post_id']); 

				$sql2 = "SELECT views FROM forums_views WHERE post_id=".$row['post_id'];
				$result2 = mysqli_query($db, $sql2);
				$views = mysqli_fetch_assoc($result2);
				$views = intval($views['views']);
	?>
				<div class="infoContainer">
			
				<!-- div style="padding-right:2px;font-size:smaller;">
				<div style="float:left;">
						<?php echo date('g:ia, M j, y', strtotime($row['date'])); ?>
				</div>
				<div style="float:right;">
					<img src="images/user_female.png" style="margin-bottom:-5px;" /><?php echo $row['login']; ?>	
				</div>
			</div -->

					<div class="title" onclick="location.href='forum_post_view.php?f=<?php echo $row['forum_id']; ?>&p=<?php echo $row['post_id']; ?>'" style="cursor:pointer">
						<div style="height:150px">
							<?php echo $title; ?>
						</div>							

						<a href="forum_post_view.php?f=<?php echo $row['forum_id']; ?>&p=<?php echo $row['post_id']; ?>" class="goto">
							<img src="images/hand.png" style="border:0px;padding:0px;" alt="click to view" />
						</a>
					</div>

					<div>
						<div style="text-align:left;padding-right:2px; font-size:smaller;">
							<div style="float:left;">
								<?php //check for new messages - #comments vs number of read child posts in forum_read. if equal, no unread
						
								$sql = "SELECT * FROM forums_read WHERE (post_id=".$row['post_id']." OR parent_id=".$row['post_id'].") AND member_id=".intval($_SESSION['member_id']);
								$result2 = mysqli_query($db, $sql);
								$read = @mysqli_num_rows($result2);
												
								if ($_SESSION['valid_user'] && $row['num_comments']+1>$read) { 
									echo '<img src="images/email_red.png" alt="new messages" title="new messages" height="16" width="16" /> ';					
								} else {
									echo '<img src="images/email.png" alt="no new messages" title="no new messages" height="16" width="16" /> ';
								} ?>
							</div>
							<div style="float:right;">
								<img src="images/comments.png" style="margin-bottom:-5px;" alt="number of replies:" title="number of replies" /> <?php echo $row['num_comments']; ?>
								<img src="images/magnifier.png" style="margin-bottom:-5px;" alt="number of views:" title="number of views" /><?php echo $views; ?>
							</div>
							<div class="title-timestamp">Last: <?php echo date('M j y, h:ia', strtotime($row['last_comment']))?></div>
						</div>
					</div>
				</div>
		<?php
			} ?>
				<!--<br style="clear:both" />-->
			<?php 
			     if(mysqli_num_rows($result) > 2)
				 { ?>
				        <a href="more.php?name=<?php echo $memberSearch; ?>&id=<?php echo $memberID; ?>&type=<?php echo '2'; ?>">See More</a><?php
				 } ?>
			   
		<?php
		} else {
			echo '<span>Commented On</span> <br/>';
			echo "<p>The user has not commented on any Forums.</p>";
		}
		?>
</div>


 <div style="clear:both" ></div>
 <div class="commentsHeader"><span>vlogs</span></div>
 

<div class="commentContainer">
 <?php
	$counter = 0;
	$sql = "SELECT * FROM vlogs WHERE 1 AND member_id='$memberID' ORDER BY last_entry DESC";	
	$result = mysqli_query($db, $sql);

if (@mysqli_num_rows($result)) { 
	echo '<span>Created</span> <br/>';
	
	
	while ($row = mysqli_fetch_assoc($result)) {
		
		if($counter == 2)
		{
		    break;
		}
		$counter++;	 
		
		$title = get_title('vlog', $row['vlog_id']);
 ?>

		<div class="infoContainer"> <!--cat-->
			<div class="title" onclick="location.href='vlog_entries.php?v=<?php echo $row['vlog_id']; ?>'" style="cursor:pointer">
               <div style="height:150px;">
                  <?php echo $title; ?>
               </div>
               <a href="vlog_entries.php?v=<?php echo $row['vlog_id']; ?>" class="goto">
                  <img src="images/hand.png" style="width:20px;margin-top:2px;border:0px;padding:0px;" alt="click to view" />
               </a>
			</div>
			
			<div style="float:left;">
				<span style='font-size: smaller;'><img src="images/user.png" style="border:none;" /> <?php echo get_login($row['member_id']); ?></span>				
			</div>
			<span style='float:right; font-size: smaller;'> 
				<?php echo $row['num_entries']; 
				if ($row['num_entries']==1) { 
					echo ' entry';
				} else { 
					echo ' entries';
				} ?>
			</span>
		</div>
<?php
	} ?>
		<br style="clear:both" />
	

	 <?php if (mysqli_num_rows($result) > 2) { ?>
		       <a href="more.php?name=<?php echo $memberSearch; ?>&id=<?php echo $row['member_id']; ?>&type=<?php echo '3'; ?>">See More</a> <?php
	} ?>
<?php
} else {
	echo '<span>Created</span> <br/>';
	echo "<p>The user does not own any vlogs</p>";
}
?>
 
</div>
 
 
 <div class="commentContainer">
 <?php
		$counter = 0;
		$sql = "SELECT Distinct a.vlog_id, a.entry_id,  a.title, a.title_alt, a.content, a.date, a.content_alt FROM vlogs_entries a , vlogs_comments b Where b.member_id = '$memberID' AND b.vlog_id = b.vlog_id ORDER BY b.date DESC";
		$result = mysqli_query($db, $sql);

if (@mysqli_num_rows($result)) { 
	echo '<span>Commented On</span> <br/>';
	echo '<div>';
	
	while ($row = mysqli_fetch_assoc($result)) {
		
		if($counter == 2)
		{
		    break;
		}
		$counter++;	   
			
		
		$title = get_title('entry', $row['entry_id']); 
?>
		<div class="infoContainer">
			<div class="title" onclick="location.href='vlog_entry_view.php?v=<?php echo $row['vlog_id']; ?>&e=<?php echo $row['entry_id']; ?>'" style="cursor:pointer">
				<div style="height:150px">
					<?php echo $title; ?>
				</div>							

				<a href="vlog_entry_view.php?v=<?php echo $row['vlog_id']; ?>&e=<?php echo $row['entry_id']; ?>" class="goto">
					<img src="images/hand.png" style="border:0px;padding:0px;" alt="click to view" />
				</a>
			</div>

			<div>
				<div style="text-align:left;padding-right:2px; font-size:smaller;">
					<div style="float:right;">
						<img src="images/comments.png" style="margin-bottom:-5px;" alt="number of comments" title="number of comments" /> <?php echo $row['num_comments']; ?>
					</div>
					<?php echo date('M j y, h:ia', strtotime($row['date'])); ?>
				</div>
			</div>
		</div>
<?php
	} ?>
		<br style="clear:both" />
		
	</div>
	
	<?php if (mysqli_num_rows($result) > 2) { ?>
		       <a href="more.php?name=<?php echo $memberSearch; ?>&id=<?php echo $memberID; ?>&type=<?php echo '4'; ?>">See More</a> <?php 
	} ?>
	
<?php
} else {
	echo "<p>The user has not commented on any Vlogs.</p>";
}
?>
 
</div>
 
 <?php } ?>
 
 
 
 
<?php

 require('include/footer.inc.php');
?>

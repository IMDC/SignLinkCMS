<?php 

	define('INCLUDE_PATH', 'include/');
	require(INCLUDE_PATH.'vitals.inc.php');

	require(INCLUDE_PATH.'header.inc.php');
  
	$memberSearch = $_GET["name"]; 
	$memberID = $_GET["id"];
	$type = $_GET["type"];
	
?>

<link rel="stylesheet" type="text/css" href="css/member.css" />

<h2>	
     <img  src="images/users.png">
	 All	      
</h2>
<br/>
<div class="moreContainer">
<?php
if($type == 1){
			//forms created by user
			$sql = "Select * from forums_posts WHERE  member_id='$memberID' AND parent_id = 0 ORDER BY last_comment DESC ";
			$result = mysqli_query($db, $sql);
			
			while ($row = mysqli_fetch_assoc($result)) {
	          	
				$title = get_title('post', $row['post_id']); 

				$sql2 = "SELECT views FROM forums_views WHERE post_id=".$row['post_id'];
				$result2 = mysqli_query($db, $sql2);
				$views = mysqli_fetch_assoc($result2);
				$views = intval($views['views']);
	?>
				<div class="infoContainer">
			
				
						<?php //echo date('g:ia, M j, y', strtotime($row['date'])); ?>
			

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
			} 
			
			
}
elseif($type == 2){			
		
			//forms the user commented on
			$sql = "SELECT DISTINCT t1.post_id, t1.parent_id, t1.forum_id, t1.login, t1.date, t1.msg, t1.msg_alt, t1.subject, t1.subject_alt, t1.num_comments FROM forums_posts t1 inner join forums_posts t2 ON t1.post_id = t2.parent_id WHERE t2.parent_id != 0 AND t2.member_id='$memberID' ORDER BY t1.last_comment DESC ";
            $result = mysqli_query($db, $sql);
			
			while ($row = mysqli_fetch_assoc($result)) {
	          	
				$title = get_title('post', $row['post_id']); 

				$sql2 = "SELECT views FROM forums_views WHERE post_id=".$row['post_id'];
				$result2 = mysqli_query($db, $sql2);
				$views = mysqli_fetch_assoc($result2);
				$views = intval($views['views']);
	?>
				<div class="infoContainer">
			
				
						<?php //echo date('g:ia, M j, y', strtotime($row['date'])); ?>
			

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
			} 
			
			
			
}
elseif($type == 3){
			//vlogs created
			
			$sql = "SELECT * FROM vlogs WHERE 1 AND member_id='$memberID' ORDER BY last_entry DESC";
			$result = mysqli_query($db, $sql);
			
			while ($row = mysqli_fetch_assoc($result)) {
		
		
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
				} 
			
			
			
}
else{
			//vlogs commented
			$sql = "SELECT Distinct a.vlog_id, a.entry_id,  a.title, a.title_alt, a.content, a.date, a.content_alt FROM vlogs_entries a , vlogs_comments b Where b.member_id = '$memberID' AND b.vlog_id = b.vlog_id ORDER BY b.date DESC";
			$result = mysqli_query($db, $sql);
			while ($row = mysqli_fetch_assoc($result)) {

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
		<?php } 
}

?>	
</div>	

<?php

 require('include/footer.inc.php');
?>

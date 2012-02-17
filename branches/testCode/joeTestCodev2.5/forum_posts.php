<?php 
define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');

require('include/header.inc.php'); 

$forum_id = intval($_REQUEST['f']);
?>

<div id="post-title">
<!--	<h3>-->
      <?php echo get_title('forum', intval($_GET['f']), 'large'); ?>
<!--   </h3>-->
</div>
<div id="inner_nav_wrap">
	<ul id="submenu">	
		<li><a href="forums.php?f=<?php echo intval($_GET['f']); ?>"><img src="images/arrow_left_32.png" alt="Back to forums" title="Back to forums" /></a></li>	
		<li><a href="forum_post_create.php?f=<?php echo intval($_GET['f']); ?>"><img src="images/slscms-reply-icon-idea-small.png" alt="New post" title="New post" /></a></li>			
	</ul>	
	<div style="clear:both" /></div>
</div>
<?php

//paging
$perpage = 8;

$sql = "SELECT count(post_id) as numrows FROM forums_posts WHERE forum_id=".$forum_id." AND parent_id=0";
$result = mysqli_query($db, $sql);
$total = mysqli_fetch_assoc($result);
$total = $total['numrows'];

$numpages = ceil($total/$perpage);

if (isset($_GET['page'])) {
    $curpage = $_GET['page'];
} else {
	$curpage = 1;
}
$offset = ($curpage - 1) * $perpage;

//print page links
for ($page=1; $page<=$numpages; $page++) {
	if ($page == $curpage) {
	  $nav .= $page.'&nbsp;';
	} else {
	  $nav .= '<a href="'.htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES).'?f='.$forum_id.'&page='.$page.'">'.$page.'</a>&nbsp;';
	}
}	

if ($curpage > 1) {
   $page  = $curpage-1;
   $prev = '<a href="'.htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES).'?f='.$forum_id.'&page='.$page.'"><</a>&nbsp;';
} 

if ($curpage < $numpages) {
	$page = $curpage + 1;
	$next = '<a href="'.htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES).'?f='.$forum_id.'&page='.$page.'">></a>';
} else {
	$next = '&nbsp;&nbsp;';
}

if ($total>$perpage) {
	echo '<div style="text-align:right;clear:both;">'.$prev.$nav.$next.'</div>';
}

$sql = "SELECT * FROM forums_posts WHERE forum_id=".$forum_id." AND parent_id=0 ORDER BY last_comment DESC LIMIT $offset, $perpage";
$result = mysqli_query($db, $sql);
if (mysqli_num_rows($result)) { 
	echo '<div id="block-container">';
	
	while ($row = mysqli_fetch_assoc($result)) {
		$title = get_title('post', $row['post_id']); 

		$sql2 = "SELECT views FROM forums_views WHERE post_id=".$row['post_id'];
		$result2 = mysqli_query($db, $sql2);
		$views = mysqli_fetch_assoc($result2);
		$views = intval($views['views']);
?>
		<div class="cat">
			<div class="title-upper" onclick="location.href='forum_post_view.php?f=<?php echo $row['forum_id']; ?>&p=<?php echo $row['post_id']; ?>'">
				<div class="title-inner" style="background-image:url('images/hand.png');background-repeat:no-repeat;background-position:bottom center;background-position-x:75px;background-position-y:125px;">
					<?php echo $title; ?>
            </div>

            <div class="title-goto-wrap">
<!--               <a href="forum_post_view.php?f=<?php echo $row['forum_id']; ?>&p=<?php echo $row['post_id']; ?>" class="goto">
                  <img src="images/hand.png" style="border:0px;padding:0px;" alt="click to view" />
               </a>-->
            </div>
			</div>

			<div class="cat-info-wrap">
				<div class="cat-info-text">
<!--					<div style="float:left;">-->
						<?php //check for new messages - #comments vs number of read child posts in forum_read. if equal, no unread
						
//						$sql = "SELECT * FROM forums_read WHERE (post_id=".$row['post_id']." OR parent_id=".$row['post_id'].") AND member_id=".intval($_SESSION['member_id']);
//						$result2 = mysqli_query($db, $sql);
//						$read = @mysqli_num_rows($result2);
//												
//						if ($_SESSION['valid_user'] && $row['num_comments']+1>$read) { 
//							echo '<img src="images/email_red.png" alt="new messages" title="new messages" height="16" width="16" /> ';					
//						} else {
//							echo '<img src="images/email.png" alt="no new messages" title="no new messages" height="16" width="16" /> ';
//						} ?>
<!--					</div>-->
					<div class="cat-info-icons">
						<img src="images/comments.png" style="margin-bottom:-5px;" alt="number of replies:" title="number of replies" /> <?php echo $row['num_comments']; ?>
						<img src="images/magnifier.png" style="margin-bottom:-5px;" alt="number of views:" title="number of views" /><?php echo $views; ?>
					</div>
					<div class="title-timestamp">
                  Last: <?php echo date('M j y, h:ia', strtotime($row['last_comment']))?>
               </div>
				</div>
			</div>
		</div>
<?php
	} ?>
		<br style="clear:both" />
		<div id="paging">
			
		</div>
	</div>
<?php
} else {
	echo "<p>No posts yet.</p>";
}
?>


<?php require('include/footer.inc.php'); ?>

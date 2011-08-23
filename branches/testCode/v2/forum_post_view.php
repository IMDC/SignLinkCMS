<?php 
define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');

require('include/header.inc.php'); 

$parent_id = intval($_REQUEST['parent']);
$post_id = intval($_REQUEST['p']);
$forum_id = intval($_REQUEST['f']);

if ($_SESSION['valid_user']) {
	//update the # thread views and the last accessed date
	$sql = "INSERT INTO forums_views (post_id, member_id, last_accessed, views) VALUES ('$post_id', '".intval($_SESSION[member_id])."', NOW(), 0)";
	$result = mysqli_query($db, $sql);
	if (!$result) {
		$sql = "UPDATE forums_views SET last_accessed=NOW(), views=views+1 WHERE post_id=$post_id AND member_id=".intval($_SESSION[member_id]);
		$result = mysqli_query($db, $sql);
	}
	
	//update that this member viewed this post
	$sql = "REPLACE INTO forums_read VALUES ($post_id, ".intval($_SESSION[member_id]).", $forum_id, $parent_id)";
	$result = mysqli_query($db, $sql);	
}

$msg = get_message($post_id);  //returns array of poster, date, html-encoded message

?>


<!--<script type="text/javascript" src="jscripts/tiny_mce/jquery.tinymce.js"></script>-->

<div id="forum-title">
	<h3><div style="float:left;height:75px;"><?php echo get_title('forum', $forum_id, 'small'); ?></div></h3>
<!--	<h3><div style="float:left;height:75px;"><?php echo get_title('forum', $forum_id, 'small'); ?></div>-->
<!--	<div style="float:left;height:75px;">&nbsp; > &nbsp;<?php if ($parent_id) { echo "Re: &nbsp;"; } ?></div></h3>-->
	<div class="forum-post-breadcrumb-arrow">&nbsp; > &nbsp;<?php if ($parent_id) { echo "Re: &nbsp;"; } ?></div>
</div><div style="clear:both" /></div>
	
<div id="submenu">
   <?php 
   /*
      if (!$parent_id) { 
         echo "<li><a href='forum_posts.php?f=$forum_id'><img src='images/arrow_left_32.png' alt='Back to forum posts' title='Back to forum posts' class='buttonimage' /></a></li>";
      } 
      else {
         echo "<li><a href='forum_post_view.php?f=$forum_id&p=$parent_id'><img src='images/arrow_left_32.png' alt='Back to parent post' title='Back to parent post' class='buttonimage' /></a></li>";
      }
    */
   ?>	
</div>

<div style="clear:both" /></div>
</div>

<div id="post">
   <div class="forum-post-title centeralign">
   <?php 
      if ($parent_id) {
//         echo get_title('post', $parent_id,'small'); 
         echo get_title('post', $parent_id); 
      }
      else {
//         echo get_title('post', $post_id, 'small'); 
         echo get_title('post', $post_id); 
      }
   ?>
</div>		
	<div id="post-info">
		<div style="padding-bottom:5px;"><?php echo $msg[0]; ?></div>
		<div><?php get_avatar($msg[3]); ?></div>
    <?php echo '<small>' . $msg[1] . '</small>';?>
	</div>

	<div id="post-msg">
		<div style="text-align:right">
		<ul>
         <?php
            if ($_SESSION['login'] == $msg[0]) {
               echo "<li style='display:inline;padding:8px;'><a href='forum_post_edit.php?f=$forum_id&p=$post_id&parent=$parent_id'><img src='images/comment_edit.png' alt='Edit' title='Edit' /></a></li>";
               echo "<li style='display:inline;padding:8xp;'><a href='forum_post_delete.php?f=$forum_id&p=$post_id&parent=$parent_id&m=$_SESSION[member_id]'><img src='images/comment_delete.png' alt='Delete' title='Delete' /></a></li>";
            }
            
            if (!$parent_id) { 
//               echo "<li style='display:inline;padding:8px;'><a href='forum_post_create.php?f=$forum_id&p=$post_id'><img src='images/comment_rev.png' alt='Reply' title='Reply to this post!' /></a></li>";
                 echo "<li style='display:inline;padding:8px;'><a href='forum_post_create.php?f=$forum_id&p=$post_id'><img src='images/slscms-reply-icon-idea-small.png' alt='Reply' title='Reply to this post!' /></a></li>";
            } 
            
         ?>
		</ul>
		</div>
		<div id="post-msg-text">
      <?php //echo '<small>' . $msg[1] . '</small><br />';   

         if ($msg[4] == 1) {
            echo '<div style="height:100%;width=100%;overflow:auto;clear:right;">'.htmlspecialchars_decode($msg[2]).'</div>';
            //echo '<div style="height:100%;width=100%;overflow:auto;clear:right;">'.html_entity_decode($msg[2]).'</div>';
         }
         
         // otherwise the message is a video, signlink or old image type
         else {
            echo $msg[2];
         }
		?>
		</div>
		<br style="clear:both" />

	</div>
	<br style="clear:both" />
   <!--  ************************************* Replies ********************************* -->
   <a name="replytop" style="visibility:hidden;"></a>
   <div class="reply-table-container">
      
	<?php

   /************************************ Replies *****************************/

	if (!$parent_id) { 
	
		$sql = "SELECT * FROM forums_posts WHERE forum_id=".$forum_id." AND parent_id=".$post_id." ORDER BY last_comment ASC";
		$result = mysqli_query($db, $sql);
      if (@mysqli_num_rows($result)) {
         $numReplies = mysqli_num_rows($result);
         // seek to last comment to grab last reply timestamp
         mysqli_data_seek($result, mysqli_num_rows($result)-1);
         $row = mysqli_fetch_assoc($result);
         //print "<h3> Replies </h3>";
         print "<h3> Replies - last reply: " . date('M j Y, h:ia', strtotime($row['last_comment'])) . "</h3>&nbsp;&nbsp;<a href='#bottom'>Jump to most recent</a>";
         // reset mysqli data pointer to first reply
         mysqli_data_seek($result, 0);
         $row = mysqli_fetch_assoc($result);
         $postcounter = 1;
         //while ($row = mysqli_fetch_assoc($result)) {
         do {
            echo '<div class="reply-row">';
            echo "<div class='reply-mail'>";
//            //check for new messages
//            //$sql = "SELECT * FROM forums_read WHERE post_id=".$row['post_id']." AND member_id=".intval($_SESSION['member_id']);
//            $sql = "SELECT * FROM forums_read WHERE post_id=".$resultset['post_id']." AND member_id=".intval($_SESSION['member_id']);
//            $result2 = mysqli_query($db,$sql);
//            $read = @mysqli_num_rows($result2);
//
//            if ($_SESSION['valid_user'] && !$read) {
//               echo '<img src="images/forum_unread.png" alt="new message" title="new message" height="32" width="32" style="margin-top:50px;" /> ';					
//            }
//            else {
//               echo '<img src="images/forum_read.png" alt="no new messages" title="no new messages" height="32" width="32" style="margin-top:50px;" /> ';
//            }
//               
//            echo "</div>";
            if ($numReplies == $postcounter) {
               $lastreplylink = '<a name="bottom" style="visibility:hidden;"></a>';
            }
            $replynum = sprintf("<span class='reply-num' id='reply-%s'>%s</span>%s", $postcounter, $postcounter, $lastreplylink);
            echo $replynum;
            echo "</div>";
            //print_reply_link($row['post_id']);
            print_reply_link($row['post_id']);
            echo "</div>";
            $postcounter++;
			
			} while ($row = mysqli_fetch_assoc($result));
            echo '</div><p class="centeralign"><a href="#replytop">Back to top</a>';
		}
	}
	?>
</div>

<?php require('include/footer.inc.php'); ?>

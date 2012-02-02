<?php 
define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');

require(INCLUDE_PATH.'header.inc.php'); 
require(INCLUDE_PATH.'lib/vlogs.inc.php'); 

$entry_id = intval($_REQUEST['e']);
$vlog_id = intval($_REQUEST['v']);

// TODO: Limit this SQL query to the first 30-50 newest entries
// TODO: Create paging system to show older entries if desired
$sql = "SELECT * FROM vlogs_entries WHERE vlog_id=".$vlog_id." AND entry_id=".$entry_id." ORDER BY date DESC";
$result = @mysqli_query($db, $sql);
if (!$row = @mysqli_fetch_assoc($result)) {
	echo "Entry not found.";
	require(INCLUDE_PATH.'footer.inc.php'); 
	exit;
}

$msg = get_vlog_message_by_id($entry_id);

?>
<script type="text/javascript">
<!--
	function confirmDelete() {
		if (confirm("Are you sure you want to delete this entry?")) {
			return true;
		}
		return false;
	}	
	
	function confirmDeleteComment() {
		if (confirm("Are you sure you want to delete this comment?")) {
			return true;
		}
		return false;
	}		
	
//-->
</script>

<div class="vlog-title-top-wrap">
<!--<div id="post-title">-->
   <div class="vlog-title">
   <!--   <h3>-->
      <a href="vlog_entries.php?v=<?php echo $vlog_id; ?>"><?php echo get_title('vlog', $vlog_id, 'large'); ?></a>
   <!--   </h3>-->
   </div>  <!-- end of vlog-title div -->
</div>   <!-- end of vlog-title-top-wrap -->
<div style="clear:both" /></div>

<div id="submenu">
   <li>
      <a href='vlog_entries.php?v=<?php echo $vlog_id; ?>'><img src='images/arrow_left_32.png' alt='Back to vlog entries' title='Back to vlog entries' class='buttonimage' />
      </a>
   </li>	
</div>  <!-- end of submenu div -->
<div style="clear:both" /></div>
	
<!--<div class="vlog-post-title centeralign" style="float:left; vertical-align:middle; height:75px;">-->
<!--   VLOG OWNER AVATAR AND INFO GOES HERE -->

<div id="post">
   <div class="forum-post-title-wrap">
      <?php
         if ($_SESSION['valid_user'] && get_vlog_owner($vlog_id) == $_SESSION['member_id']) {
            echo "<div class='post-msg-tools'>";
               echo "<ul class='post-msg-tools-list'>";
                  echo "<li><a href='vlog_entry_edit.php?v=$vlog_id&e=$entry_id'><img src='images/comment_edit.png' alt='Edit' title='Edit' /></a></li>";
                  echo "<li><a href='vlog_entry_delete.php?v=$vlog_id&e=$entry_id' onClick='javascript: return confirmDelete()'><img src='images/delete.png' alt='Delete' title='Delete' /></a></li>";
               echo "</ul>";
            echo "</div>";
         }
      ?>

      <div class="forum-post-title centeralign">
         <?php 
            echo get_title('entry', $entry_id, 'reg'); 
         ?>
      </div>
   </div>

<div class="forum-post-info-wrap">
   <div id="post-info">
      <div class="forum-post-avatar-textlink">
         <?php
            echo '<a href="searchmember.php?name=' . $msg[0] . '&id=' . $msg[3] . '">' . $msg[0] . '</a>';
         ?>
      </div>
      <div class="forum-post-avatar-image-wrap">
         <a href="searchmember.php?name=<?php echo $msg[0] . '&id=' . $msg[3] ?>">
            <?php get_avatar($msg[3], $msg[0]); ?>
         </a>
      </div>
      <?php 
         echo '<small>Last Activity:<br />' . $msg[1] . '</small>';
      ?>
   </div>
   
   <div id="post-msg">
      <div id="post-msg-text">
         <?php
            if ($msg[4] == 1) {
               echo '<div style="height:100%;width=100%;overflow:auto;clear:right;">'.htmlspecialchars_decode($msg[2]).'</div>';
            }
            
            // else vlog message is a video, signlink, or old image type
            else {
               echo $msg[2];
            }
            ?>
      </div>
   </div>
</div>  <!-- end of forum-post-info-wrap   -->
</div>  <!-- end of div id=post -->
<br style="clear:both;" />

<!--		<div style="text-align:right;padding-right:10px;">
			<ul class="post-icon-list">
			<?php
//			if ($_SESSION['valid_user'] && get_vlog_owner($vlog_id) == $_SESSION['member_id']) {
//				echo "<li><a href='vlog_entry_edit.php?v=$vlog_id&e=$entry_id'><img src='images/comment_edit.png' alt='Edit' title='Edit' /></a></li>";
//				echo "<li><a href='vlog_entry_delete.php?v=$vlog_id&e=$entry_id' onClick='javascript: return confirmDelete()'><img src='images/delete.png' alt='Delete' title='Delete' /></a></li>";
//			}
            // display the reply icon regardless of user logged in or not.
            // if they are not logged in, the comment create page will prompt them to login
//            echo "<li><a href='vlog_comment_create.php?v=$vlog_id&e=$entry_id' ><img src='images/slscms-reply-icon-idea-small.png' alt='Reply' title='Reply to this vlog' /></a></li>";
			 ?>
			</ul>
		</div>
-->

		<?php
         // Call to get_vlog_message uses mysql result set from vlog_entries table above
         // so the content and entry_id is already known
//         echo get_vlog_message($row['content'], $row['content_alt'], 'entries', $entry_id); 
      ?>

   <!--  ************************************* Replies ********************************* -->

   <a name="replytop" style="visibility:hidden;"></a>
   <div class="reply-table-container">
   
   <?php
   
   /************************************ VLOG Replies start here *****************************/
   
	$sql = "SELECT * FROM vlogs_comments WHERE vlog_id=".$vlog_id." AND entry_id=".$entry_id." ORDER BY date DESC";
	$result = mysqli_query($db, $sql);
   
   // useful for repeating the vlog reply link later on
   $vlog_reply_link = "<a href='vlog_comment_create.php?v=$vlog_id&e=$entry_id' ><img class='shadow' src='images/reply-add-paul4.png' alt='Reply' title='Reply to this vlog' /><br />New Reply</a>";

   if (@mysqli_num_rows($result)) { 

      $numReplies = mysqli_num_rows($result);
      // seek to last comment to grab last reply timestamp
      mysqli_data_seek($result, mysqli_num_rows($result)-1);
      $row = mysqli_fetch_assoc($result);
      
      /********* BEGIN reply table tools  ***********/
      echo '<div class="reply-table-header-wrap">';
         print "<span class='reply-table-header-text'> Replies - last reply: " . date('M j Y, h:ia', strtotime($row['last_comment'])) . "</span>";
         print "<ul class='reply-table-header-menu'>";
            echo "<li>";
//               echo "<a href='forum_post_create.php?f=$forum_id&p=$post_id'><img class='shadow' src='images/reply-add-paul4.png' alt='Reply' title='Reply to this post!' /><br />New Reply</a>";
               echo $vlog_reply_link;
            echo "</li>";
            echo "<li>";
               echo "<a href='#bottom'><img class='shadow' src='images/arrow_down_48.png' /><br />Newest</a>";
            echo"</li>";
      echo '</div>';
      /********* END reply table tools  ***********/
      
//      print "<h3> Replies - last reply: " . date('M j Y, h:ia', strtotime($row['last_comment']));
      
      // reset mysqli data pointer to first retrieved reply
      mysqli_data_seek($result, 0);
      $row = mysqli_fetch_assoc($result);
      $postcounter = 1;

      do {
         if ($numReplies == $postcounter) {
            $lastreplylink = '<a name="bottom" style="visibility:hidden;"></a>';
         }
         else {
            $lastreplylink = '';
         }
         $replynum = sprintf("<span class='reply-num' id='reply-%s'>%s</span>%s", $postcounter, $postcounter, $lastreplylink);
         
         print_vlog_reply_row($row['comment_id'], $replynum);
         
         $postcounter++;
      } while ($row = mysqli_fetch_assoc($result));
      echo '</div><p class="centeralign"><a href="#replytop"><img src="images/arrow_up_48.png" /><br />First Reply</a>';
   }
   else {
      echo "<div class='shadow centeralign no-vlog-comments'>";
         echo "<p>No comments yet</p>";
         echo $vlog_reply_link;
      echo "</div>";
   }
   
//         echo '<div class="reply-row">';
//         echo "<div class='reply-mail'>";
//
//         if ($numReplies == $postcounter) {
//            $lastreplylink = '<a name="bottom" style="visibility:hidden;"></a>';
//         }
//         $replynum = sprintf("<span class='reply-num' id='reply-%s'>%s</span>%s", $postcounter, $postcounter, $lastreplylink);
//         echo $replynum;
//         echo "</div>";
//
//         get_vlog_message($row['comment'], $row['comment_alt'], 'comments', $comment_id);
//         echo "</div>";
//
//      } while ($row = mysqli_fetch_assoc($result));
//      echo '</div><p class="centeralign"><a href="replytop">Back to top</a>';
//
//      while ($row = mysqli_fetch_assoc($result)) { 
//         echo '<tr>';
//         if (!empty($row['comment'])) {
//            //the msg is plain text
//            $link = substr($row['comment'],0,30).'...';
//         } else {
//            //the msg is a file
//            $dir_files = @scandir('uploads/comments/'.$row['comment_id'].'/');
//
//            //pick out the "message" file and check its extension
//            if (!empty($dir_files)) {
//               foreach ($dir_files as $dir_file) {
//                  if (substr($dir_file,0, 7) == "message") {
//                     $msg_file = $dir_file;
//                     break;
//                  }
//               }
//               $ext = end(explode('.',$msg_file));
//               if (in_array($ext, $filetypes_video)) {
//                  $link = '<img src="images/film.png" alt="Video content" style="border:0px;" />';
//               } else if ($ext=="swf") {
//                  $link = '<img src="images/television.png" alt="Signlink content" style="border:0px;" />';
//               }
//            }
//         }
//         echo '<td><a href="vlog_comment_view.php?v='.$vlog_id.'&e='.$entry_id.'&c='.$row['comment_id'].'">'.$link.'</a></td>';
//         echo '<td style="text-align:center;">'.get_login($row['member_id']).'</td>'; 
        ?>

</div>


<?php require(INCLUDE_PATH.'footer.inc.php'); ?>

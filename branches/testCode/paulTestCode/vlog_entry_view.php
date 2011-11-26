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

<!--<div id="post-title">-->
<!--   VLOG OWNER AVATAR AND INFO GOES HERE -->
<?php
   $avatar_code = get_avatar(get_vlog_owner($vlog_id));
   $owner_div = "<div class='vlog-owner-div'>
                    <div class='vlog-avatar-wrap'>
                       $avatar_code
                    </div>
                 </div>";
   echo $owner_div;
?>
<div class="vlog-entry-title">
<!--   <h3>-->
      <div class="vlog-intro-title" style="">
         <a href="vlog_entries.php?v=<?php echo $vlog_id; ?>"><?php echo get_title('vlog', $vlog_id, 'large'); ?></a>
      </div>
<!--   </h3>-->
</div>

<div id="submenu">
   <li>
      <a href='vlog_entries.php?v=<?php echo $vlog_id; ?>'><img src='images/arrow_left_32.png' alt='Back to vlog entries' title='Back to vlog entries' class='buttonimage' />
      </a>
   </li>	
</div>
<div style="clear:both" /></div>
	
<!--<div clas="vlog-post-title centeralign" style="float:left; vertical-align:middle; height:75px;">-->
<div class="forum-post-title centeralign">
   <?php echo get_title('entry', $entry_id, 'big'); ?>
</div>

<div id="post">		
	<div id="post-msg-text" style="padding-left:10px; padding-right:10px;padding-bottom:20px;">
		<div style="text-align:right;padding-right:10px;">
			<ul class="post-icon-list">
			<?php
			if ($_SESSION['valid_user'] && get_vlog_owner($vlog_id) == $_SESSION['member_id']) {
				echo "<li><a href='vlog_entry_edit.php?v=$vlog_id&e=$entry_id'><img src='images/comment_edit.png' alt='Edit' title='Edit' /></a></li>";
				echo "<li><a href='vlog_entry_delete.php?v=$vlog_id&e=$entry_id' onClick='javascript: return confirmDelete()'><img src='images/delete.png' alt='Delete' title='Delete' /></a></li>";
			}
            // display the reply icon regardless of user logged in or not.
            // if they are not logged in, the comment create page will prompt them to login
            echo "<li><a href='vlog_comment_create.php?v=$vlog_id&e=$entry_id' ><img src='images/slscms-reply-icon-idea-small.png' alt='Reply' title='Reply to this vlog' /></a></li>";
			 ?>
			</ul>
		</div>
		<?php
         // Call to get_vlog_message uses mysql result set from vlog_entries table above
         // so the content and entry_id is already known
         echo get_vlog_message($row['content'], $row['content_alt'], 'entries', $entry_id); 
      ?>
		<br /><br style="clear:both" />
	</div>
	<br style="clear:both" />
	
   <a name="replytop" style="visibility:hidden;"></a>
   <div class="reply-table-container">
   <?php
   /*******    VLOG REPLIES START HERE ****************/
   
	$sql = "SELECT * FROM vlogs_comments WHERE vlog_id=".$vlog_id." AND entry_id=".$entry_id." ORDER BY date DESC";
	$result = mysqli_query($db, $sql); ?>
   <div class="reply-table-container">
   
<!--	<table class="manage">
	<tr>
		<th colspan="4"><a style="float:right;padding-right:10px;" href="vlog_comment_create.php?v=<?php echo $vlog_id.'&e='.$entry_id; ?>"><img src='images/comment_add.png' alt='Add comment' title='Add comment' /></a> Comments</th>
	</tr>	-->
      
	<?php 
      if (@mysqli_num_rows($result)) { 
         
         $numReplies = mysqli_num_rows($result);
         // seek to last comment to grab last reply timestamp
         mysqli_data_seek($result, mysqli_num_rows($result)-1);
         $row = mysqli_fetch_assoc($result);
         print "<h3> Replies - last reply: " . date('M j Y, h:ia', strtotime($row['last_comment']));
         // reset mysqli data pointer to first retrieved reply
         mysqli_data_seek($result, 0);
         $row = mysqli_fetch_assoc($result);
         $postcounter = 1;
         
         do {
            echo '<div class="reply-row">';
            echo "<div class='reply-mail'>";
            
            if ($numReplies == $postcounter) {
               $lastreplylink = '<a name="bottom" style="visibility:hidden;"></a>';
            }
            $replynum = sprintf("<span class='reply-num' id='reply-%s'>%s</span>%s", $postcounter, $postcounter, $lastreplylink);
            echo $replynum;
            echo "</div>";
            
            get_vlog_message($row['comment'], $row['comment_alt'], 'comments', $comment_id);
            echo "</div>";
            
         } while ($row = mysqli_fetch_assoc($result));
         echo '</div><p class="centeralign"><a href="replytop">Back to top</a>';
         
			while ($row = mysqli_fetch_assoc($result)) { 
				echo '<tr>';
				if (!empty($row['comment'])) {
					//the msg is plain text
					$link = substr($row['comment'],0,30).'...';
				} else {
					//the msg is a file
					$dir_files = @scandir('uploads/comments/'.$row['comment_id'].'/');
		
					//pick out the "message" file and check its extension
					if (!empty($dir_files)) {
						foreach ($dir_files as $dir_file) {
							if (substr($dir_file,0, 7) == "message") {
								$msg_file = $dir_file;
								break;
							}
						}
						$ext = end(explode('.',$msg_file));
						if (in_array($ext, $filetypes_video)) {
							$link = '<img src="images/film.png" alt="Video content" style="border:0px;" />';
						} else if ($ext=="swf") {
							$link = '<img src="images/television.png" alt="Signlink content" style="border:0px;" />';
						}
					}
				}
				echo '<td><a href="vlog_comment_view.php?v='.$vlog_id.'&e='.$entry_id.'&c='.$row['comment_id'].'">'.$link.'</a></td>';
				echo '<td style="text-align:center;">'.get_login($row['member_id']).'</td>'; 
				?>
				
				<td style="text-align:center">
					<?php echo date('M j Y, h:ia', strtotime($row['date'])); ?>
				</td>
				<?php if ($_SESSION['valid_user'] && get_vlog_owner($vlog_id) == $_SESSION['member_id']) { ?>
				<td style="text-align:center">
					<a href="vlog_entry_delete.php?v=<?php echo $vlog_id.'&e='.$entry_id.'&c='.$row['comment_id']; ?>" onclick="javascript: return confirmDeleteComment();"><img src="images/comment_delete.png" alt="Delete comment" title="Delete comment" style="border:0px;" /></a>
				</td>
				<?php } ?>
			</tr>
		<?php
		}
	} 
//   else {
//		echo "<tr colspan='4'><td>No comments.</td></tr>";
//	}
	
	?>	
	</table>
   </div>
	
</div>


<?php require(INCLUDE_PATH.'footer.inc.php'); ?>

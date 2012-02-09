<?php 
define('INCLUDE_PATH', '../include/');
require(INCLUDE_PATH.'vitals.inc.php');
admin_authenticate();

require(INCLUDE_PATH.'admin_header.inc.php'); 

$vlog_id = intval($_GET['v']);

?>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.14/jquery-ui.min.js"></script>
<link href="../css/admin.css" rel="stylesheet" type="text/css"/>
<style>
   .quickViewLink {
      left: -65px;
      top: -30px;
   }
</style>

<h2>Vlog Entries</h2>

<div style="padding-bottom:8px;">
<?php

$page_title = adminMediaPathFix($page_title);

echo '</div>';

//get vlog entries
$sql = "SELECT * FROM vlogs_entries WHERE vlog_id=".$vlog_id." ORDER BY date DESC";
$result = mysqli_query($db, $sql);

if (mysqli_num_rows($result)) 
{ ?>
    <div class="forum-posts-table">
        <div class="forum-posts-table-header forum-posts-table-elem centeralign">
            <div class="id-col sidebyside">      ID      </div>
            <div class="subject-col sidebyside"> Title   </div>
            <div class="date-col sidebyside">    Date    </div>
            <div class="manage-col sidebyside">  Manage  </div>
        </div>
        
        <?php
            while($row = mysqli_fetch_assoc($result))
            {
                $title = get_title('entry', $row['entry_id'], 'small');
                $title = adminMediaPathFix($title);
                
                $manage = '<a href="vlog_post_edit.php?v='.$vlog_id.'&e='.$row['entry_id'].'&c=0">Edit Title</a><br />
                           <a href="vlog_post_delete.php?v='.$vlog_id.'&e='.$row['entry_id'].'&c=0" onclick="return confirm(\'Are you sure you want to delete this vlog entry and all its comments?\')">Delete Entry</a>';
                
                echo '<div class="post-row forum-posts-table-elem">';
                echo '<div class="id-col sidebyside">     '. $row['entry_id'] .'</div>';
                echo '<div class="subject-col sidebyside">'. $title .'</div>';
                echo '<div class="date-col sidebyside">   '. $row['date'] .'</div>';
                echo '<div class="manage-col sidebyside">'. $manage .'</div>';
                echo '</div>';
                
                echo '<div id="post-pane" class="post-replies">';
                
                ?>
                <div id="post-main">
                    <div id="post-info">
                        <div style="padding:10px;">
                            <p><?php 
                            echo '<a href="vlog_post_edit.php?f='.$forum_id.'&p='.$replies['post_id'].'&thread=0">Edit Entry</a><br />';
                            echo '<a href="vlog_post_delete.php?f='.$forum_id.'&p='.$replies['post_id'].'&del=0" onclick="return confirm(\'Are you sure you want to remove the contents of this reply?\')">Remove Reply</a><br />';
                            ?></p>
                        </div>
                    </div>
                    
                    <div id="post-msg">
                        <div id="post-msg-text">

                        <?php
//                        echo get_vlog_message($row['content'], $row['content_alt'], 'entries', $row['entry_id']);
//                            if ($msg[4] == 1) { // MESSAGE IS TEXT
//                                echo '<div>'.htmlspecialchars_decode($msg[2]).'</div>';
//                            } 
//                            else { // otherwise the message is a video, signlink or old image type
//                                echo adminMediaPathFix($msg[2]);
//                            }
                        ?>
<br /><br style="clear:both" />
                        </div> <br style="clear:both" />
                    </div> <br style="clear:both" />
                </div>
                <?php
                
                $sql_replies = "SELECT post_id, parent_id FROM forums_posts WHERE forum_id=".$forum_id." AND (parent_id=".$row['post_id']." OR post_id=".$row['post_id'].") ORDER BY date ASC";
                $result_replies = mysqli_query($db, $sql_replies);

/*============================================================================*/
                
                while($replies = mysqli_fetch_assoc($result_replies))
                {
                    $msg = get_message($replies['entry_id']);  //returns array of poster, date, html-encoded message
                ?>
        

            <div id="post">
                <div id="post-info">
                    <div style="padding-bottom:5px;"><?php echo $msg[0]; ?></div>
                    <?php echo '<small>' . $msg[1] . '</small>';?>
                    <div style="padding:10px;">

                        <p><?php 
                        echo '<a href="forum_post_edit.php?f='.$forum_id.'&p='.$replies['post_id'].'&thread=0">Edit Reply</a><br />';
                        echo '<a href="forum_post_delete.php?f='.$forum_id.'&p='.$replies['post_id'].'&del=0" onclick="return confirm(\'Are you sure you want to remove the contents of this reply?\')">Remove Reply</a><br />';

                        if($replies['post_id'] != $row['post_id']){
                            echo '<a href="forum_post_delete.php?f='.$forum_id.'&p='.$replies['post_id'].'&del=1" onclick="return confirm(\'Are you sure you want to delete this reply?\')">Delete Reply</a>';
                        }
                        ?></p>

                    </div>
                </div>

                <div id="post-msg">
                    <div id="post-msg-text">
                
                    <?php
                        if ($msg[4] == 1) { // MESSAGE IS TEXT
                            echo '<div>'.htmlspecialchars_decode($msg[2]).'</div>';
                        } 
                        else { // otherwise the message is a video, signlink or old image type
                            echo adminMediaPathFix($msg[2]);
                        }
                    ?>
                        
                    </div> <br style="clear:both" />
                </div> <br style="clear:both" />
            </div>

        <?php } ?> <!-- while replies -->
        </div>
    <?php } ?> <!-- while posts -->
    </div>
    
<?php
} else {
    echo "<br />No posts found.";
}
?>
  
<script>
   
    $("div.post-row").click(function() {
        if ($(this).next().is(":hidden")) {
            $(this).next().slideDown();
        } else {
            $(this).next().slideUp();
        }
    });
    
    $("div.text_title").css('height', '67px');
    
</script>

<?php require(INCLUDE_PATH.'footer.inc.php'); ?>

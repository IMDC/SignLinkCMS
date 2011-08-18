<?php 
define('INCLUDE_PATH', '../include/');
require(INCLUDE_PATH.'vitals.inc.php');
admin_authenticate();

require(INCLUDE_PATH.'admin_header.inc.php'); 

$forum_id = intval($_GET['f']);
?>

<link href="../css/admin.css" rel="stylesheet" type="text/css"/>
<style>
   .quickViewLink {
      left: -65px;
      top: -30px;
   }
</style>

<h2>Forum Posts</h2>

<div style="padding-bottom:8px;">
<?php
// fix added to code returned from get_title call
// path to imagezoom image changed to reflect being in admin side
// path to flowplayer changed to reflect being in admin side
$page_title = get_title('forum', $forum_id, "small");
$page_title = preg_replace('/img class="quickView" src="images/', 'img class="quickView" src="../images', $page_title);
$page_title = preg_replace('/"flash\/flowplayer/', '"../flash/flowplayer', $title);
echo $page_title;
echo '</div>';

//get forum posts
$sql = "SELECT * FROM forums_posts WHERE forum_id=".$forum_id." AND parent_id='0' ORDER BY date DESC";
$result = mysqli_query($db, $sql);
$r = 1;

if (mysqli_num_rows($result)) 
{ ?>
    <div class="forum-posts-table">
        <div class="forum-posts-table-header forum-posts-table-elem">
            <div class="id-col sidebyside">      ID      </div>
            <div class="subject-col sidebyside"> Subject </div>
            <div class="author-col sidebyside">  Author  </div>
            <div class="date-col sidebyside">    Date    </div>
            <div class="manage-col sidebyside">  Manage  </div>
        </div>
        
        <?php
            while($row = mysqli_fetch_assoc($result))
            {
                $title = get_title('post', $row['post_id'], 'small');
                $title = preg_replace('/img class="quickView" src="images/', 'img class="quickView" src="../images', $title);
                $title = preg_replace('/"flash\/flowplayer/', '"../flash/flowplayer', $title);
                
                $manage = '<a href="forum_post_edit.php?f='.$forum_id.'&p='.$row['post_id'].'&thread=1">Edit Title</a><br />
                           <a href="forum_post_delete.php?f='.$forum_id.'&p='.$row['post_id'].'" onclick="return confirm(\'Are you sure you want to delete this post and all its contents?\')">Delete Post</a>';
                
                echo '<div class="post-row forum-posts-table-elem">';
                echo '<div class="id-col sidebyside">     '. $row['post_id'] .'</div>';
                echo '<div class="subject-col sidebyside">'. $title .'</div>';
                echo '<div class="author-col sidebyside"> '. $row['login'] .'</div>';
                echo '<div class="date-col sidebyside">   '. $row['date'] .'</div>';
                echo '<div class="manage-col sidebyside">'. $manage .'</div>';
                echo '</div>';
                
                echo '<div id="post-pane" class="post-replies">';

                $sql_replies = "SELECT post_id, parent_id FROM forums_posts WHERE forum_id=".$forum_id." AND (parent_id=".$row['post_id']." OR post_id=".$row['post_id'].") ORDER BY date ASC";
                $result_replies = mysqli_query($db, $sql_replies);

                while($replies = mysqli_fetch_assoc($result_replies))
                {
                    $msg = get_message($replies['post_id']);  //returns array of poster, date, html-encoded message
            ?>
            
        <?php if($replies['post_id'] == $row['post_id']){ ?>
            <div id="post-main">
        <?php } else { ?>
            <div id="post">
        <?php } ?>
            
            <div id="post-info">
                <div style="text-align:left;"><?php echo $replies['post_id']; ?></div>
                <div style="padding-bottom:5px;"><?php echo $msg[0]; ?></div>
                <?php echo '<small>' . $msg[1] . '</small>';?>
                <div style="padding:10px;">
                    <?php 
                    echo '<p><a href="forum_post_edit.php?f='.$forum_id.'&p='.$replies['post_id'].'&thread=0">Edit Reply</a><br />';
                    echo '<a href="" onclick="return confirm(\'Are you sure you want to remove the contents of this reply?\')">Remove Reply</a><br />';
                    
                    if($replies['post_id'] != $row['post_id']){
                            echo '<a href="forum_post_delete.php?f='.$forum_id.'&p='.$replies['post_id'].'" onclick="return confirm(\'Are you sure you want to delete this reply?\')">Delete Reply</a></p>';
                    } 
                    ?>
                </div>
            </div>

            <div id="post-msg">
                    <div id="post-msg-text">
                
                <?php
                     if ($msg[4] == 1) {
                        // MESSAGE IS TEXT
                        echo '<div>'.htmlspecialchars_decode($msg[2]).'</div>';
                        //echo '<div style="height:100%;width=100%;overflow:auto;clear:right;">'.html_entity_decode($msg[2]).'</div>';
                     }
                     // otherwise the message is a video, signlink or old image type
                     else {
                        // Fix for flowplayer embed code returned from call to get_message in forums.inc.php
                        // flowplayer doesn't like the id of an element to begin with "id='../uploads"
                        // so we change only the id of the anchor to get rid of the '../' and do not change the href absolute path to the video file
                        $fixedoutput = preg_replace('/id="..\/uploads\/posts/', 'id="uploads/posts', $msg[2]);
                        // fix for flowplayer embed code returned from call to get_message in forums.inc.php 
                        // this fix matches the one above it and makes sure the id of the element on the page that flowplayer replaces 
                        // matches with the previous change to the element id by removing the '../'
                        $fixedoutput = preg_replace('/flowplayer\("..\/uploads\/posts/', 'flowplayer("uploads/posts', $fixedoutput);
                        // fix for flowplayer embed code returned from call to get_message in forums.inc.php
                        // relative path to the flowplayer swf file needs to be changed because we are in the admin directory and not the site root
                        // TODO: change the signature of the get_message method to accept a parameter that indicates the folder level or path to the flowplayer include files
                        $fixedoutput = preg_replace('/flash\/flowplayer/', '../flash/flowplayer', $fixedoutput);
                        echo $fixedoutput;
                     }
                ?>
                    </div>
                    <br style="clear:both" />

            </div>
            <br style="clear:both" />
        </div>
              
            <?php
            
                } // while replies
                
                echo '</div>';
                
            } // while posts
        ?>
        
    </div>
    
<?php
} else {
	echo "<br />No posts found.";
}
?>

<!--<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>-->
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.14/jquery-ui.min.js"></script>
  
<script>
    
//    $("#post-pane").resizable();
    
    $("div.post-row").click(function() {
        if ($(this).next().is(":hidden")) {
            $(this).next().slideDown();
        } else {
            $(this).next().slideUp();
        }
    });
    
    $("div.text_title").css('height', '67px');
//    $("#post-msg-text").css('height', '100px');
//    $("#post-msg-text").css('width', 'auto');
//    $("#post-msg").css('padding', '5px 5px 5px 5px');
    
</script>

<?php require(INCLUDE_PATH.'footer.inc.php'); ?>

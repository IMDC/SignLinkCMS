<?php 
define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');
require(INCLUDE_PATH.'lib/vlogs.inc.php'); 

require('include/header.inc.php'); 

$vlog_id = intval($_REQUEST['v']);
?>
<div class="vlog-title-wrap">
   <div class="vlog-intro-title">
      <?php echo get_title('vlog', intval($_GET['v']), 'large'); ?>
   </div>
	<div style="clear:both" /></div>
</div>
<div id="inner_nav_wrap">
  <ul id="submenu">	
    <li>
       <a href="vlogs.php?v=<?php echo intval($_GET['v']); ?>"><img src="images/arrow_left_32.png" alt="Back to vlogs" title="Back to vlogs" /></a>
    </li>
    <?php if(get_vlog_owner($vlog_id) == $_SESSION['member_id']) { ?>
    <li>
       <a href="vlog_entry_create.php?v=<?php echo intval($_GET['v']); ?>"><img src="images/slscms-reply-icon-idea-small.png" alt="New entry" title="New entry" /></a>
    </li>
    <?php } ?>
  </ul>
</div>
<div style="clear:both" /></div>
<?php

//paging
$perpage = 8;

$sql = "SELECT count(entry_id) as numrows FROM vlogs_entries WHERE vlog_id=".$vlog_id;
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
	  $nav .= '<a href="'.htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES).'?v='.$vlog_id.'&page='.$page.'">'.$page.'</a>&nbsp;';
	}
}	

if ($curpage > 1) {
   $page  = $curpage-1;
   $prev = '<a href="'.htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES).'?v='.$vlog_id.'&page='.$page.'"><</a>&nbsp;';
} 

if ($curpage < $numpages) {
	$page = $curpage + 1;
	$next = '<a href="'.htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES).'?v='.$vlog_id.'&page='.$page.'">></a>';
} else {
	$next = '&nbsp;&nbsp;';
}

if ($total>$perpage) {
	echo '<div style="text-align:right;clear:both;">'.$prev.$nav.$next.'</div>';
}

$sql = "SELECT * FROM vlogs_entries WHERE vlog_id=".$vlog_id." ORDER BY date DESC LIMIT $offset, $perpage";
$result = mysqli_query($db, $sql);
if (@mysqli_num_rows($result)) { 
	echo '<div>';
	
	while ($row = mysqli_fetch_assoc($result)) {
		$title = get_title('entry', $row['entry_id']); 
?>
		<div class="cat">
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
		<div id="paging">
			
		</div>
	</div>
<?php
} else {
	echo "<p>No entries yet.</p>";
}
?>


<?php require('include/footer.inc.php'); ?>

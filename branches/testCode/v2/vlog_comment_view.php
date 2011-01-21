<?php 
define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');
require(INCLUDE_PATH.'lib/vlogs.inc.php');

require(INCLUDE_PATH.'header.inc.php'); 

$comment_id = intval($_REQUEST['c']);
$entry_id = intval($_REQUEST['e']);
$vlog_id = intval($_REQUEST['v']);

$sql = "SELECT * FROM vlogs_comments WHERE vlog_id=".$vlog_id." AND entry_id=".$entry_id." AND comment_id=".$comment_id." ORDER BY date DESC";
$result = mysqli_query($db, $sql);
?>

<div id="post-title">
	<h3><div style="float:left;height:75px;"><?php echo get_title('vlog', $vlog_id, 'small'); ?></div>
	<div style="float:left;height:75px;">&nbsp; > &nbsp;<?php echo "Re: &nbsp;"; ?></div></h3>
	
	<div style="float:left; vertical-align:middle; height:75px;">
		<?php echo get_title('entry', $entry_id, 'small'); ?>
	</div>
	<div id="submenu" style="margin-top:41px;">
		<?php echo "<li><a href='vlog_entry_view.php?v=$vlog_id&e=$entry_id'><img src='images/arrow_left_32.png' alt='Back to entry' title='Back to entry' class='buttonimage' /></a></li>"; ?>				
	</div>	
	<div style="clear:both" /></div>
</div>

<div id="post">	
	<?php if ($row = mysqli_fetch_assoc($result)) {  ?>

		<div id="post-info">
			<div style="padding-bottom:5px;"><?php echo get_login($row['member_id']); ?></div>
			<?php get_avatar($row['member_id']); ?>
		</div>
	
		<div id="post-msg">
			<div id="post-msg-text">
				<small><?php echo date('M j Y, h:ia', strtotime($row['date'])); ?></small><br />
				<?php  echo get_vlog_message($row['comment'], $row['comment_alt'], 'comments', $comment_id);  ?>
			</div>
			<br style="clear:both" />
		</div>
	<?php } else echo 'No comment found.'; ?>
	<br style="clear:both" />
</div>

<?php require('include/footer.inc.php'); ?>

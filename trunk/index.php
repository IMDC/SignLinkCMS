<?php 
define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');

require(INCLUDE_PATH.'header.inc.php'); 


/* top content pages */
$sql = "SELECT * FROM pages WHERE 1 ORDER BY last_modified DESC LIMIT 0, 2";
$result = mysql_query($sql, $db);
if (mysql_num_rows($result)) { 
	echo '<div class="col">';
	echo '<a href="content.php"><div style="text-align:left; background-color:#fffdce; padding:5px;"><img src="images/picture.png" alt="content pages" title="content pages" /></div></a>';
	
	while ($row = mysql_fetch_assoc($result)) { 
	$title = get_title('page', $row['page_id']);
?>
	<div class="cat">
		<div class="title">
			<div style="height:150px;">
				<?php echo $title; ?>
			</div>
						
			<a href="page_view.php?f=<?php echo $row['page_id']; ?>" class="goto">
				<img src="images/hand.png" style="border:0px;padding:0px;" />
			</a>
		</div>
	</div>
	<br style="clear:both" />
<?php
	}
	echo '</div>';
}

/* top forums */
$sql = "SELECT * FROM forums WHERE 1 ORDER BY last_post DESC LIMIT 0, 2";
$result = mysql_query($sql, $db);
if (mysql_num_rows($result)) { 
	echo '<div class="col">';
		echo '<a href="forums.php"><div style="text-align:left; background-color:#cbdbef; padding:5px;"><img src="images/group.png" alt="forums" title="forums" /></div></a>';
		
		while ($row = mysql_fetch_assoc($result)) { 
		$title = get_title('forum', $row['forum_id']);
		?>
		<div class="cat">
			<div class="title">
				<div style="height:150px;">
					<?php echo $title; ?>
				</div>
							
				<a href="forum_posts.php?f=<?php echo $row['forum_id']; ?>" class="goto">
					<img src="images/hand.png" style="border:0px;padding:0px;" />
				</a>
			</div>		
		</div>
		<br style="clear:both" />
		<?php
		}
	echo '</div>';
}



/* top vlogs pages */
$sql = "SELECT * FROM vlogs WHERE 1 ORDER BY last_entry DESC LIMIT 0, 2";
$result = mysql_query($sql, $db);
if (@mysql_num_rows($result)) { 
	echo '<div class="col">';
	echo '<a href="vlogs.php"><div style="text-align:left;background-color:#ffced6;padding:5px;"><img src="images/cup_edit.png" alt="vlogs" title="vlogs" /></div></a>';
	
	while ($row = mysql_fetch_assoc($result)) { 
	$title = get_title('vlog', $row['vlog_id']);
?>
		<div class="cat">
						<div class="title">
				<div style="height:150px;">
					<?php echo $title; ?>
				</div>
							
				<a href="vlog_entries.php?v=<?php echo $row['vlog_id']; ?>" class="goto">
					<img src="images/hand.png" style="border:0px;padding:0px;" />
				</a>
			</div>
		</div>
		<br style="clear:both" />
<?php
	}
	echo '</div>';
}

require(INCLUDE_PATH.'footer.inc.php'); 
?>
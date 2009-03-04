<?php 
define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');

require(INCLUDE_PATH.'header.inc.php'); 

/* top content pages */
$sql = "SELECT * FROM pages WHERE 1 ORDER BY last_modified DESC LIMIT 0, 4";
$result = mysql_query($sql, $db);
if (mysql_num_rows($result)) { 
	echo '<h2><a href="content.php"><img src="images/picture.png" alt="content pages" title="content pages" style="padding:3px;" /></a></h2>';

	echo '<div class="row">';	
	while ($row = mysql_fetch_assoc($result)) { 
	$title = get_title('page', $row['page_id']);
?>
	<div class="cat">
		<div class="title">
			<div style="height:150px;">
				<?php echo $title; ?>
			</div>
						
			<a href="page_view.php?c=<?php echo $row['page_id']; ?>" class="goto">
				<img src="images/hand.png" style="border:0px;padding:0px;" />
			</a>
		</div>
	</div>
<?php
	}
	echo '<br style="clear:both;" /></div>	';
}

/* top forums */
$sql = "SELECT * FROM forums WHERE 1 ORDER BY last_post DESC LIMIT 0, 4";
$result = mysql_query($sql, $db);
if (mysql_num_rows($result)) { 
	echo '<h2><a href="forums.php"><img src="images/group.png" alt="forums" title="forums" style="padding:3px;" /></a></h2>';
	echo '<div class="row">';		
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
		
		<?php
		}
	echo '<br style="clear:both;" /></div>';
}

/* top vlogs pages */
$sql = "SELECT * FROM vlogs WHERE 1 ORDER BY last_entry DESC LIMIT 0, 4";
$result = mysql_query($sql, $db);
if (@mysql_num_rows($result)) { 
	echo '<h2><a href="vlogs.php"><img src="images/cup_edit.png" alt="vlogs" title="vlogs" style="padding:3px;" /></a></h2>';

	echo '<div class="row">';	
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
		
<?php
	}
	echo '<br style="clear:both;" /></div>';
}

require(INCLUDE_PATH.'footer.inc.php'); 
?>
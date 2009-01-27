<?php 
define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');

require(INCLUDE_PATH.'header.inc.php'); 


/* top content pages */
$sql = "SELECT * FROM pages WHERE 1 ORDER BY last_modified DESC LIMIT 0, 2";
$result = mysql_query($sql, $db);
if (mysql_num_rows($result)) { 
	echo '<div class="col" style="background-color:#fffdce">';
	echo '<div style="text-align:left;"><img src="images/picture.png" alt="forums" title="forums" /></div>';
	
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
	echo '<div class="col" style="background-color:#cbdbef">';
		echo '<div style="text-align:left;"><img src="images/group.png" alt="forums" title="forums" /></div>';
		
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
$sql = "SELECT * FROM vlogs WHERE 1 ORDER BY last_modified DESC LIMIT 0, 2";
$result = mysql_query($sql, $db);
if (mysql_num_rows($result)) { 
	echo '<div class="col" style="background-color:#ffced6">';
	echo '<div style="text-align:left;"><img src="images/picture.png" alt="forums" title="forums" /></div>';
	
	while ($row = mysql_fetch_assoc($result)) { 
	$title = get_title('page', $row['page_id']);
?>
		<div class="title">
			<div style="height:150px;">
				<?php echo $title; ?>
			</div>
						
			<a href="page_view.php?f=<?php echo $row['page_id']; ?>" class="goto">
				<img src="images/hand.png" style="border:0px;padding:0px;" />
			</a>
		</div>
		<br style="clear:both" />
<?php
	}
	echo '</div>';
}

require(INCLUDE_PATH.'footer.inc.php'); 
?>
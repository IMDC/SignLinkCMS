<?php 
define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');

require(INCLUDE_PATH.'header.inc.php');

$sql = "SELECT * FROM vlogs WHERE 1 ORDER BY last_entry ASC";
$result = mysql_query($sql, $db);
if (@mysql_num_rows($result)) { 
	echo '<div id="block-container">';
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
			
			<div style="float:left;">
				<span style='font-size: smaller;'><img src="images/user.png" /> <?php echo get_login($row['member_id']); ?></span>				
			</div>
			<span style='float:right; font-size: smaller;'> 
				<?php echo $row['num_entries']; 
				if ($row['num_entries']==1) { 
					echo ' entry';
				} else { 
					echo ' entries';
				} ?>
			</span>
		</div>
<?php
	} ?>
		<br style="clear:both" />
	</div>

<?php
} else {
	echo "No vlogs found.";
}

 require('include/footer.inc.php'); ?>

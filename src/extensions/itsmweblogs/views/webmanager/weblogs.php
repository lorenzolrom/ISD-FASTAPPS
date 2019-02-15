<div class="tile-container">
	<?php
	$sites = array_slice(scandir($ITSM_WEBLOGS_PATHS), 2); // Ignore '.' and '..'
	
	foreach($sites as $site)
	{
		?>
		<div class="tile">
			<a href="<?=SITE_URI?>webmanager/weblogs/site?s=<?=$site?>"><?=$site?></a>
		</div>
		<?php
	}
	?>
</div>
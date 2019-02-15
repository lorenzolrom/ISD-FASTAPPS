<?php
	if(!isset($_GET['s']))
		throw new AppException("Site Not Defined", "P03");
	
	if(!is_dir($ITSM_WEBLOGS_PATHS . "/" . $_GET['s']))
		throw new AppException("Site Not Found", "P04");
	
	if(!isset($_GET['l']))
		throw new AppException("Log Not Defined", "P03");
	
	if(!is_file($ITSM_WEBLOGS_PATHS . "/" . $_GET['s'] . "/" . $_GET['l']))
		throw new AppException("Log Not Found", "P04");
?>
<h2><a href="<?=SITE_URI?>webmanager/weblogs/site?s=<?=$_GET['s']?>"><?=$_GET['s']?></a><?=" - " . $_GET['l']?></h2>
<textarea class="nc2-log-view" readonly>
	<?php
		$logfile = fopen($ITSM_WEBLOGS_PATHS . "/" . $_GET['s'] . "/" . $_GET['l'], 'r');
		while($line = fgets($logfile))
		{
			echo ($line . "\n");
		}
		fclose($logfile);
	?>
</textarea>
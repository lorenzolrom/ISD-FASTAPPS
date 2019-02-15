<?php
	if(!isset($_GET['s']))
		throw new AppException("Site Not Defined", "P03");
	
	if(!is_dir($ITSM_WEBLOGS_PATHS . "/" . $_GET['s']))
		throw new AppException("Site Not Found", "P04");
?>
<script>
	/**
	 * This function interactively searches the log list and only displays
	 * rows matching the filter
	 */
	function filter(input)
	{
		// Get the input from the filter field
		var filter = input.value.toUpperCase();
		
		// Get the parent display and all the rows inside that display
		var display = input.parentElement.getElementsByTagName("ul")[0];
		var rows = display.getElementsByTagName("li");
		
		for(var i = 0; i < rows.length; i++)
		{
			// get the A tag containing the log title
			this_link = rows[i].getElementsByTagName("a")[0];
			if(this_link)
			{
				// If the filter is present, show the element, if not
				// hide it
				if(this_link.innerHTML.toUpperCase().indexOf(filter) > -1)
				{
					rows[i].style.display = "";
				}
				else
				{
					rows[i].style.display = "none";
				}
			}
		}
	}
</script>
<div class="nc2-split-display">
	<h2>Viewing logs for <?=$_GET['s']?></h2>
	<div class="nc2-split-column">
		<h3>Access</h3>
		<input type="text" onkeyup="filter(this)" placeholder="Filter Access Logs">
		<ul>
			<?php
				$access_logs = scandir($ITSM_WEBLOGS_PATHS . "/" . $_GET['s']);
				foreach($access_logs as $access_log)
				{
					if(substr($access_log, 0, 6) == "access")
					{
						?>
						<li><a href="<?=SITE_URI?>webmanager/weblogs/log?s=<?=$_GET['s']?>&l=<?=$access_log?>"><?=substr($access_log, -14, 10)?></a></li>
						<?php
					}
				}
			?>
		</ul>
	</div>
	
	<div class="nc2-split-column">
		<h3>Error</h3>
		<input type="text" onkeyup="filter(this)" placeholder="Filter Error Logs">
		<ul>
			<?php
				$error_logs = scandir($ITSM_WEBLOGS_PATHS . "/" . $_GET['s']);
				foreach($error_logs as $error_log)
				{
					if(substr($error_log, 0, 5) == "error")
					{
						?>
						<li><a href="<?=SITE_URI?>webmanager/weblogs/log?s=<?=$_GET['s']?>&l=<?=$error_log?>"><?=substr($error_log, -14, 10)?></a></li>
						<?php
					}
				}
			?>
		</ul>
	</div>
</div>